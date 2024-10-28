<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertVolumeByMonthController extends Controller
{
    public function AlertVolumeByMonth(Request $request)
    {
        // Capture optional filters with defaults for unfiltered data
        $selectedPOEId = $request->input('poe_id', null);
        $selectedClassification = $request->input('classification', null);
        $selectedMonth = $request->input('month', null);
        $selectedYear = $request->input('year', date('Y'));

        // Retrieve unique classifications dynamically from the database
        $classifications = DB::table('secondary_screenings_data')
            ->where('classification', '!=', 'Non-Case')
            ->distinct()
            ->pluck('classification');

        // Build query for alert volume, excluding records classified as 'Non-Case'
        $alertQuery = DB::table('secondary_screenings_data')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as alert_volume')
            )
            ->where('classification', '!=', 'Non-Case')
            ->when($selectedPOEId, function ($query) use ($selectedPOEId) {
                return $query->where('poeid', $selectedPOEId);
            })
            ->when($selectedClassification, function ($query) use ($selectedClassification) {
                return $query->where('classification', $selectedClassification);
            })
            ->when($selectedMonth, function ($query) use ($selectedMonth) {
                return $query->whereMonth('created_at', $selectedMonth);
            })
            ->whereYear('created_at', $selectedYear)
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        // Extract data for points of entry for filter options
        $pointsOfEntry = DB::table('points_of_entry')
            ->select('id', 'name')
            ->get();

        // Prepare data for the view in the specified structure
        $data = [
            'Title' => 'Monthly Alerts Volume by POE',
            'Desc' => 'Aggregated POE alert volume by month, with applied filters.',
            'Page' => 'poereports.AlertsByMonth',
            'MonthlyAlertsVolumeKey' => 'true',
            'alertData' => $alertQuery,
            'pointsOfEntry' => $pointsOfEntry,
            'selectedYear' => $selectedYear,
            'selectedPOEId' => $selectedPOEId,
            'selectedClassification' => $selectedClassification,
            'selectedMonth' => $selectedMonth,
            'classifications' => $classifications, // Pass classifications to the view
        ];

        return view('scrn', $data);
    }

    public function HighRiskAlertByMonth(Request $request)
    {
        // Capture optional filters with defaults for unfiltered data
        $selectedPOEId = $request->input('poe_id', null);
        $selectedDisease = $request->input('disease', null); // New disease filter
        $selectedMonth = $request->input('month', null);
        $selectedYear = $request->input('year', date('Y'));

        // Retrieve unique suspected diseases dynamically from the database
        $diseases = DB::table('secondary_screenings_data')
            ->where('classification', '!=', 'Non-Case')
            ->whereNotNull('suspected_diseases')
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(suspected_diseases, '$[*].disease')) as disease"))
            ->distinct()
            ->pluck('disease');

        // Build query for high-risk alert volume, focusing on the high-risk cases and selected disease
        $alertQuery = DB::table('secondary_screenings_data')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as high_risk_alert_count')
            )
            ->where('classification', '!=', 'Non-Case')
            ->where('high_risk_alert', 1) // Focus on high-risk alerts only
            ->when($selectedPOEId, function ($query) use ($selectedPOEId) {
                return $query->where('poeid', $selectedPOEId);
            })
            ->when($selectedDisease, function ($query) use ($selectedDisease) {
                return $query->whereJsonContains('suspected_diseases', [['disease' => $selectedDisease]]);
            })
            ->when($selectedMonth, function ($query) use ($selectedMonth) {
                return $query->whereMonth('created_at', $selectedMonth);
            })
            ->whereYear('created_at', $selectedYear)
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get();

        // Extract data for points of entry for filter options
        $pointsOfEntry = DB::table('points_of_entry')
            ->select('id', 'name')
            ->get();

        // Prepare data for the view in the specified structure
        $data = [
            'Title' => 'Monthly High-Risk Alerts Volume by Suspected Disease',
            'Desc' => 'Aggregated high-risk POE alert volume by month, filtered by suspected disease.',
            'Page' => 'poereports.MonthlyHighRiskAlertsVolume',
            'MonthlyHighRiskAlertsVolumeKey' => 'true',
            'alertData' => $alertQuery,
            'pointsOfEntry' => $pointsOfEntry,
            'selectedYear' => $selectedYear,
            'selectedPOEId' => $selectedPOEId,
            'selectedDisease' => $selectedDisease,
            'selectedMonth' => $selectedMonth,
            'diseases' => $diseases, // Pass suspected diseases to the view
        ];

        return view('scrn', $data);
    }
}
