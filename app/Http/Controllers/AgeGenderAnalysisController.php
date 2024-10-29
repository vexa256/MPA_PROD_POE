<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgeGenderAnalysisController extends Controller
{
    public function AgeGenderAnalysis(Request $request)
    {
        // Get optional filters for month, year, and POE, defaulting to all data if filters aren't provided
        $month = $request->input('month');
        $year = $request->input('year');
        $poeId = $request->input('poe_id');

        // Base query for fetching gender distribution by suspected disease with optional filters
        $query = DB::table('secondary_screenings_data')
            ->select(DB::raw('json_unquote(json_extract(suspected_diseases, "$[0].disease")) as disease'), 'gender')
            ->when($month, function ($q) use ($month) {
                return $q->whereMonth('created_at', $month);
            })
            ->when($year, function ($q) use ($year) {
                return $q->whereYear('created_at', $year);
            })
            ->when($poeId, function ($q) use ($poeId) {
                return $q->where('poeid', $poeId);
            })
            ->get();

        // Process gender distribution by disease and handle missing data
        $genderDistribution = $query->groupBy('disease')->map(function ($diseaseGroup) {
            return [
                'male' => $diseaseGroup->where('gender', 'Male')->count() ?? 0,
                'female' => $diseaseGroup->where('gender', 'Female')->count() ?? 0,
                'other' => $diseaseGroup->where('gender', 'Other')->count() ?? 0,
            ];
        })->toArray();

        // Fetch all Points of Entry (POEs) for the dropdown filter
        $pointsOfEntry = DB::table('points_of_entry')->select('id', 'name')->get()->toArray();

        // Prepare data for the view
        $data = [
            'title' => 'Gender Distribution by Suspected Disease',
            'description' => 'Distribution of suspected disease cases by gender.',
            'Page' => 'poereports.AgeAndGender',
            'genderData' => $genderDistribution,
            'pointsOfEntry' => $pointsOfEntry,
            'filters' => [
                'month' => $month,
                'year' => $year,
                'poe_id' => $poeId,
            ],
        ];

        return view('scrn', $data);
    }

    public function ageDistribution(Request $request)
    {
        // Optional filters for month, year, and POE
        $month = $request->input('month');
        $year = $request->input('year');
        $poeId = $request->input('poe_id');

        // Base query to get age data grouped by suspected disease
        $query = DB::table('secondary_screenings_data')
            ->select(DB::raw('json_unquote(json_extract(suspected_diseases, "$[0].disease")) as disease'), 'age')
            ->whereNotNull('age');

        // Apply optional filters
        if ($month) {
            $query->whereMonth('created_at', $month);
        }
        if ($year) {
            $query->whereYear('created_at', $year);
        }
        if ($poeId) {
            $query->where('poeid', $poeId);
        }

        $ageData = $query->get()->groupBy('disease')->map(function ($ages) {
            return $ages->pluck('age')->sort()->values()->all();
        })->filter(function ($ages, $disease) {
            return $disease && count($ages) > 0;
        })->toArray();

        // Fetch all Points of Entry (POEs) for the dropdown filter
        $pointsOfEntry = DB::table('points_of_entry')->select('id', 'name')->get()->toArray();

        // Prepare data for the view
        $data = [
            'title' => 'Age Distribution by Suspected Disease',
            'description' => 'Age distribution analysis across suspected diseases.',
            'Page' => "poereports.Age",
            'ageData' => $ageData,
            'pointsOfEntry' => $pointsOfEntry,
            'filters' => [
                'month' => $month,
                'year' => $year,
                'poe_id' => $poeId,
            ],
        ];

        return view('scrn', $data);
    }
}
