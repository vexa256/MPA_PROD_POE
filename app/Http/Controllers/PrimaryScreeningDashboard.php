<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class PrimaryScreeningDashboard extends Controller
{
    public function ScreeningVolumebyPOE(Request $request)
    {
        // **Step 1: Filter Initialization**

        $currentMonth = date('m');
        $currentYear = date('Y');

        $poeFilter = $request->input('poe_id', null);
        $startDate = $request->input('start_date', null);
        $endDate = $request->input('end_date', null);
        $month = $request->input('month', $currentMonth);
        $year = $request->input('year', $currentYear);

        // **Step 2: Fetch Points of Entry**

        $pointsOfEntry = DB::table('points_of_entry')
            ->select('id', 'name', 'type')
            ->where('status', 'active')
            ->get();

        // **Step 3: Fetch Primary Screening Data**

        $primaryScreeningQuery = DB::table('ScreeningData')
            ->select(
                'poe_id',
                'poe_name',
                DB::raw('COUNT(screening_id) as total_screenings'),
                DB::raw('MAX(poe_province) as province'),
                DB::raw('MAX(poe_district) as district')
            )
            ->where('classification', 'Non-Case')
            ->when($poeFilter, function ($query, $poeFilter) {
                return $query->where('poe_id', $poeFilter);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('screening_timestamp', [$startDate, $endDate]);
            }, function ($query) use ($month, $year) {
                return $query->whereMonth('screening_timestamp', $month)
                    ->whereYear('screening_timestamp', $year);
            })
            ->groupBy('poe_id', 'poe_name');

        $primaryData = $primaryScreeningQuery->get();

        // **Step 4: Fetch Secondary Screening Data**

        $secondaryScreeningQuery = DB::table('secondary_screenings')
            ->select(
                'poe_id',
                'poe_name',
                DB::raw('COUNT(id) as total_screenings'),
                DB::raw('MAX(poe_province) as province'),
                DB::raw('MAX(poe_district) as district')
            )
            ->when($poeFilter, function ($query, $poeFilter) {
                return $query->where('poe_id', $poeFilter);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            }, function ($query) use ($month, $year) {
                return $query->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
            })
            ->groupBy('poe_id', 'poe_name');

        $secondaryData = $secondaryScreeningQuery->get();

        // **Step 5: Merge and Aggregate Data**

        $mergedData = $primaryData->merge($secondaryData);

        $combinedScreeningData = $mergedData->groupBy(function ($item) {
            return $item->poe_id;
        })->map(function ($group) {
            $first = $group->first();
            return (object) [
                'poe_id' => $first->poe_id,
                'poe_name' => $first->poe_name,
                'total_screenings' => $group->sum('total_screenings'),
                'province' => $first->province,
                'district' => $first->district,
            ];
        })->sortByDesc('total_screenings')->values();

        // **Step 6: Fetch Province Distribution Data**

        $provinceData = DB::table('ScreeningData')
            ->select(
                'poe_province as province',
                DB::raw('COUNT(screening_id) as total_screenings')
            )
            ->where('classification', 'Non-Case')
            ->when($poeFilter, function ($query, $poeFilter) {
                return $query->where('poe_id', $poeFilter);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('screening_timestamp', [$startDate, $endDate]);
            }, function ($query) use ($month, $year) {
                return $query->whereMonth('screening_timestamp', $month)
                    ->whereYear('screening_timestamp', $year);
            })
            ->groupBy('poe_province')
            ->get();

        // **Step 7: Prepare Data for the View**

        $data = [
            'Title' => 'National Screening Volume Analysis',
            'Desc' => 'Current Month and Year data shown by default unless filtered',
            'Page' => 'primaryscreening.ScrByVolume',
            'scr_vol' => $combinedScreeningData, // Combined data for the table
            'provinceDistribution' => $provinceData, // Data for the chart
            'pointsOfEntry' => $pointsOfEntry, // Pass POE data for the dropdown
            'filters' => [
                'poe_id' => $poeFilter,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'month' => $month,
                'year' => $year,
            ],
        ];

        return view('scrn', $data);
    }
}
