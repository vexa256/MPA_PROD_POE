<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class GenderAndRouteAnalysis extends Controller
{
    public function GenderAndRouteAnalysisDashboard(Request $request)
    {
        // **Step 1: Handle Filters**

        // Get filter inputs; if not set, default to null
        $poeFilter = $request->input('poe_id', null);
        $startDate = $request->input('start_date', null);
        $endDate = $request->input('end_date', null);

        // Get current month and year
        $currentMonth = date('m');
        $currentYear = date('Y');

        // **Step 2: Fetch Data from Tables**

        // Build the query for primary screenings
        $primaryQuery = DB::table('ScreeningData')
            ->select(
                'screening_id',
                'poe_id',
                'traveller_gender',
                'poe_province',
                'screening_timestamp'
            )
            ->where('classification', 'Non-Case');

        // Build the query for secondary screenings
        $secondaryQuery = DB::table('secondary_screenings')
            ->join('ScreeningData', 'secondary_screenings.screening_id', '=', 'ScreeningData.screening_id')
            ->select(
                'secondary_screenings.screening_id',
                'secondary_screenings.poe_id',
                'ScreeningData.traveller_gender',
                'secondary_screenings.poe_province',
                'secondary_screenings.created_at as screening_timestamp'
            );

        // **Apply Filters Only If They Are Used**

        if ($poeFilter) {
            $primaryQuery->where('poe_id', $poeFilter);
            $secondaryQuery->where('secondary_screenings.poe_id', $poeFilter);
        }

        if ($startDate && $endDate) {
            $primaryQuery->whereBetween('screening_timestamp', [$startDate, $endDate]);
            $secondaryQuery->whereBetween('secondary_screenings.created_at', [$startDate, $endDate]);
        } else {
            // If no date filters are applied, default to current month and year
            $primaryQuery->whereYear('screening_timestamp', $currentYear)
                ->whereMonth('screening_timestamp', $currentMonth);

            $secondaryQuery->whereYear('secondary_screenings.created_at', $currentYear)
                ->whereMonth('secondary_screenings.created_at', $currentMonth);
        }

        // Execute the queries
        $primaryScreenings = $primaryQuery->get();
        $secondaryScreenings = $secondaryQuery->get();

        // Combine primary and secondary screenings
        $combinedScreenings = $primaryScreenings->merge($secondaryScreenings);

        // **Step 3: Standardize and Clean Data**

        // Function to standardize gender
        $standardizeGender = function ($gender) {
            $gender = strtolower(trim($gender));
            if (in_array($gender, ['m', 'male'])) {
                return 'Male';
            } elseif (in_array($gender, ['f', 'female'])) {
                return 'Female';
            } elseif (in_array($gender, ['other', 'o'])) {
                return 'Other';
            } else {
                return 'Unknown';
            }
        };

        // Function to standardize province
        $standardizeProvince = function ($province) {
            $province = strtoupper(trim($province));
            $provinceMapping = [
                'KIGALI CITY' => 'KIGALI CITY',
                'EASTERN PROVINCE' => 'EASTERN PROVINCE',
                'NORTHERN PROVINCE' => 'NORTHERN PROVINCE',
                'WESTERN PROVINCE' => 'WESTERN PROVINCE',
                'SOUTHERN PROVINCE' => 'SOUTHERN PROVINCE',
                // Add any other variations or misspellings here
            ];

            return $provinceMapping[$province] ?? 'Unknown';
        };

        // Standardize and clean the data
        $cleanedData = $combinedScreenings->map(function ($item) use ($standardizeGender, $standardizeProvince) {
            // Standardize gender
            $item->traveller_gender = $standardizeGender($item->traveller_gender);
            // Standardize poe_province
            $item->poe_province = $standardizeProvince($item->poe_province);

            return $item;
        });

        // **Exclude entries where gender or province is empty or null**
        $cleanedData = $cleanedData->filter(function ($item) {
            return !empty($item->traveller_gender) && !empty($item->poe_province);
        });

        // **Step 4: Group and Aggregate Data**

        // Fetch POE names from points_of_entry table
        $poeData = DB::table('points_of_entry')
            ->select('id', 'name')
            ->get()
            ->keyBy('id');

        $poeNames = $poeData->map(function ($item) {
            return $item->name;
        });

        // **Total Screened**
        $totalScreened = $cleanedData->count();

        // **Gender Counts**
        $genderCounts = $cleanedData->groupBy('traveller_gender')->map(function ($group) {
            return $group->count();
        });

        // **Most Screened Gender**
        $mostScreenedGender = $genderCounts->sortDesc()->keys()->first();

        // **Genders Screened Per Province**

        $gendersScreenedPerProvince = [];

        foreach ($cleanedData->groupBy('poe_province') as $province => $groupByProvince) {
            $provinceGenderCounts = $groupByProvince->groupBy('traveller_gender')->map(function ($group) {
                return $group->count();
            });

            $gendersScreenedPerProvince[$province] = $provinceGenderCounts;
        }

        // **Genders Screened Per POE**

        $gendersPerPOE = [];

        foreach ($cleanedData->groupBy('poe_id') as $poe_id => $groupByPOE) {
            $poe_name = $poeNames->get($poe_id, 'Unknown POE');
            $poeGenderCounts = $groupByPOE->groupBy('traveller_gender')->map(function ($group) {
                return $group->count();
            });

            $gendersPerPOE[] = [
                'poe_id' => $poe_id,
                'poe_name' => $poe_name,
                'gender_counts' => $poeGenderCounts,
            ];
        }

        // **Step 5: Prepare Data for the View**

        // Fetch all Points of Entry for dropdown filters
        $pointsOfEntry = DB::table('points_of_entry')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        // Prepare data to be sent to the view
        $data = [
            'Title' => 'Gender and Route Analysis Dashboard',
            'Desc' => 'Comprehensive reports including both primary and secondary screenings.',
            'Page' => 'genderandroutes.GenderAndRoute',
            'GenderAndRouteKey' => 'true',
            'totalScreened' => $totalScreened,
            'genderCounts' => $genderCounts,
            'gendersScreenedPerProvince' => $gendersScreenedPerProvince,
            'gendersPerPOE' => $gendersPerPOE,
            'mostScreenedGender' => $mostScreenedGender,
            'pointsOfEntry' => $pointsOfEntry, // Points of Entry for dropdown
            'filters' => [
                'poe_id' => $poeFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ];

        return view('scrn', $data);
    }
}
