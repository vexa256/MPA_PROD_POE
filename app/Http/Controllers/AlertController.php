<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlertController extends Controller
{
    public function AlertReport(Request $request)
    {
        // Set default date range to current month if not provided
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfMonth();

        // Build the query
        $query = DB::table('secondary_screenings_data')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNotIn('classification', ['non case', 'Person Under Surveillance (PUS)']);

        // Apply filters if provided
        if ($request->filled('poe')) {
            $query->where('poeid', $request->input('poe'));
        }
        if ($request->filled('province')) {
            $query->where('poe_province', $request->input('province'));
        }
        if ($request->filled('district')) {
            $query->where('poe_district', $request->input('district'));
        }

        // Fetch all cases
        $allCases = $query->get();

        // Fetch suspected cases and their diseases grouped by district
        $suspectedCasesByDistrict = $query->select('poe_district',
            DB::raw('JSON_EXTRACT(suspected_diseases, "$[0].disease") as disease'),
            DB::raw('count(*) as total'))
            ->where('classification', 'Suspected Case')
            ->groupBy('poe_district', DB::raw('JSON_EXTRACT(suspected_diseases, "$[0].disease")'))
            ->get();

        // Fetch suspected cases and their diseases grouped by province
        $suspectedCasesByProvince = $query->select('poe_province',
            DB::raw('JSON_EXTRACT(suspected_diseases, "$[0].disease") as disease'),
            DB::raw('count(*) as total'))
            ->where('classification', 'Suspected Case')
            ->groupBy('poe_province', DB::raw('JSON_EXTRACT(suspected_diseases, "$[0].disease")'))
            ->get();

        // Group suspected diseases and count
        $suspectedDiseasesCounts = $query->select(DB::raw('JSON_EXTRACT(suspected_diseases, "$[0].disease") as disease'),
            DB::raw('count(*) as total'))
            ->where('classification', 'Suspected Case')
            ->groupBy(DB::raw('JSON_EXTRACT(suspected_diseases, "$[0].disease")'))
            ->get();

        // Fetch all details for suspected cases
        $suspectedCasesDetails = $query->select('secondary_screenings_data.*', 'points_of_entry.name as poe_name', 'points_of_entry.type as poe_type')
            ->join('points_of_entry', 'secondary_screenings_data.poeid', '=', 'points_of_entry.id')
            ->where('secondary_screenings_data.classification', 'Suspected Case')
            ->get()
            ->map(function ($case) {
                $case->suspected_diseases = json_decode($case->suspected_diseases);
                $case->symptoms = json_decode($case->symptoms);
                $case->travel_exposures = json_decode($case->travel_exposures);
                $case->transit_countries = json_decode($case->transit_countries);
                return $case;
            });

        // Calculate monthly trend
        $monthlyTrend = $query->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total'))
            ->where('classification', 'Suspected Case')
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->orderBy('month', 'asc')
            ->get();

        // Fetch filter options for dropdowns
        $pointsOfEntry = DB::table('points_of_entry')->select('id', 'name')->get();
        $provinces = DB::table('points_of_entry')
            ->select(DB::raw('DISTINCT JSON_UNQUOTE(JSON_EXTRACT(location, "$.province")) as province'))
            ->pluck('province');
        $districts = DB::table('points_of_entry')
            ->select(DB::raw('DISTINCT JSON_UNQUOTE(JSON_EXTRACT(location, "$.district")) as district'))
            ->pluck('district');

        // Prepare data for the view
        $data = [
            'Title' => 'Monthly Suspected Disease Incidence Report',
            'Desc' => 'Analysis of suspected disease cases with filtering options.',
            'Page' => 'Alerts.AlertDashboard',
            'MonthlyIncidenceReportKey' => 'true',
            'suspectedCases' => $suspectedCasesDetails,
            'diseaseCounts' => $suspectedDiseasesCounts,
            'suspectedCasesByDistrict' => $suspectedCasesByDistrict,
            'suspectedCasesByProvince' => $suspectedCasesByProvince,
            'monthlyTrend' => $monthlyTrend,
            'totalCases' => $allCases->count(),
            'pointsOfEntry' => $pointsOfEntry,
            'provinces' => $provinces,
            'districts' => $districts,
            'filters' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'poe_id' => $request->input('poe_id'), // Changed from 'poe' to 'poe_id'
                'poe_province' => $request->input('poe_province'), // Changed from 'province' to 'poe_province'
                'poe_district' => $request->input('poe_district'), // Changed from 'district' to 'poe_district'
            ],
        ];

        return view('scrn', $data);

    }
}
