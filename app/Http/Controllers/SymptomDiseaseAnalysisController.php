<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SymptomDiseaseAnalysisController extends Controller
{
    public function SymptomDiseaseAnalysis(Request $request)
    {
        // Get filter inputs
        $selectedYear = $request->input('year', date('Y'));
        $selectedMonth = $request->input('month', null);
        $selectedPOEId = $request->input('poeid', null);

        // Primary query for secondary_screenings_data
        $query = DB::table('secondary_screenings_data')
            ->join('points_of_entry', 'secondary_screenings_data.poeid', '=', 'points_of_entry.id')
            ->select(
                'secondary_screenings_data.poeid',
                'points_of_entry.name as poe_name',
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(symptoms, "$[*]")) as symptom'),
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspected_diseases, "$[*].disease")) as disease'),
                DB::raw('COUNT(*) as frequency')
            )
            ->whereYear('arrival_date', $selectedYear);

        // Apply month filter if selected
        if ($selectedMonth) {
            $query->whereMonth('arrival_date', $selectedMonth);
        }

        // Apply POE filter if selected
        if ($selectedPOEId) {
            $query->where('secondary_screenings_data.poeid', $selectedPOEId);
        }

        // Group by POE, Disease, and Symptom for Heatmap and Treemap data
        $query->groupBy('secondary_screenings_data.poeid', 'disease', 'symptom');

        // Execute the query and retrieve data for charts
        $data = $query->get();

        // Format data for the required charts
        $heatmapData = [];
        $treemapData = [];
        $stackedBarData = [];

        foreach ($data as $entry) {
            // Heatmap data structure
            $heatmapData[] = [
                'poe_id' => $entry->poeid,
                'disease' => $entry->disease,
                'symptom' => $entry->symptom,
                'frequency' => $entry->frequency,
            ];

            // Treemap data structure
            $treemapData[] = [
                'disease' => $entry->disease,
                'symptom' => $entry->symptom,
                'count' => $entry->frequency,
            ];

            // Stacked Bar data structure
            $stackedBarData[] = [
                'poe_name' => $entry->poe_name,
                'disease' => $entry->disease,
                'symptom_count' => $entry->frequency,
            ];
        }

        // Fetch available years and months for filters
        $years = DB::table('secondary_screenings_data')
            ->selectRaw('DISTINCT YEAR(arrival_date) as year')
            ->pluck('year');
        $months = range(1, 12); // All months for filter options
        $pointsOfEntry = DB::table('points_of_entry')
            ->select('id', 'name')
            ->get();

        // Prepare data for the view
        return view('scrn', [
            'Title' => 'Symptom-Disease Analysis',
            'Desc' => 'Analysis of reported symptoms and suspected diseases across points of entry',
            'Page' => 'poereports.SymptomDiseaseAnalysis',
            'HeatmapData' => $heatmapData,
            'TreemapData' => $treemapData,
            'StackedBarData' => $stackedBarData,
            'years' => $years,
            'months' => $months,
            'pointsOfEntry' => $pointsOfEntry,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'selectedPOEId' => $selectedPOEId,
        ]);
    }
}
