<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
// use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// use Carbon\Carbon;

class ReportsController extends Controller
{

    public function generateScreeningReport(Request $request, $report_type)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'report_type' => 'required|in:province,district,poe,screener',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
        ]);

        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');

        switch ($report_type) {
            case 'province':
                $groupBy = 'poe_province';
                $select = ['poe_province as Location'];
                break;
            case 'district':
                $groupBy = ['poe_district', 'poe_province'];
                $select = ['poe_district as Location', 'poe_province as Province'];
                break;
            case 'poe':
                $groupBy = ['poe_name', 'poe_type'];
                $select = ['poe_name as Location', 'poe_type as POEType'];
                break;
            case 'screener':
                $groupBy = ['screener_username', 'screener_role'];
                $select = ['screener_username as Location', 'screener_role as Role'];
                break;
        }

        $query = DB::table('ScreeningData')
            ->select($select)
            ->selectRaw('COUNT(*) as TotalScreenings')
            ->selectRaw('SUM(CASE WHEN classification = "Contact" THEN 1 ELSE 0 END) as Contacts')
            ->selectRaw('SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) as SuspectedCases')
            ->selectRaw('SUM(CASE WHEN classification = "Suspected VHF Case" THEN 1 ELSE 0 END) as SuspectedVHFCases')
            ->selectRaw('SUM(CASE WHEN high_risk_alert = TRUE THEN 1 ELSE 0 END) as HighRiskAlerts')
            ->selectRaw('SUM(CASE WHEN has_symptoms = TRUE THEN 1 ELSE 0 END) as SymptomaticCases')
            ->selectRaw('COUNT(DISTINCT CASE WHEN classification != "Non-Case" THEN traveller_name END) as UniqueIndividuals')
            ->selectRaw('AVG(accuracy_probability) as AverageAccuracy')
            ->whereBetween('screening_timestamp', [$startDate, $endDate]);

        if ($search) {
            $query->where(function ($q) use ($search, $report_type) {
                $q->where($this->getSearchColumn($report_type), 'LIKE', "%{$search}%");
            });
        }

        $total = $query->count();

        $report = $query->groupBy($groupBy)
            ->orderByDesc('TotalScreenings')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        $report = $report->map(function ($item) {
            $item->ContactPercentage = $this->calculatePercentage($item->Contacts, $item->TotalScreenings);
            $item->SuspectedCasePercentage = $this->calculatePercentage($item->SuspectedCases, $item->TotalScreenings);
            $item->SuspectedVHFCasePercentage = $this->calculatePercentage($item->SuspectedVHFCases, $item->TotalScreenings);
            $item->HighRiskAlertPercentage = $this->calculatePercentage($item->HighRiskAlerts, $item->TotalScreenings);
            $item->SymptomaticCasePercentage = $this->calculatePercentage($item->SymptomaticCases, $item->TotalScreenings);
            return $item;
        });

        $totals = $this->calculateTotals($report);

        $response = [
            'report' => $report->values(),
            'totals' => $totals,
            'metadata' => [
                'report_type' => $report_type,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'generated_at' => now()->toDateTimeString(),
                'total_items' => $total,
                'current_page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage),
            ],
        ];

        return response()->json($response);
    }

    private function calculatePercentage($value, $total)
    {
        return $total > 0 ? round(($value / $total) * 100, 2) : 0;
    }

    private function calculateTotals($report)
    {
        $totals = [
            'TotalScreenings' => $report->sum('TotalScreenings'),
            'Contacts' => $report->sum('Contacts'),
            'SuspectedCases' => $report->sum('SuspectedCases'),
            'SuspectedVHFCases' => $report->sum('SuspectedVHFCases'),
            'HighRiskAlerts' => $report->sum('HighRiskAlerts'),
            'SymptomaticCases' => $report->sum('SymptomaticCases'),
            'UniqueIndividuals' => $report->sum('UniqueIndividuals'),
            'AverageAccuracy' => $report->avg('AverageAccuracy'),
        ];

        $totals['ContactPercentage'] = $this->calculatePercentage($totals['Contacts'], $totals['TotalScreenings']);
        $totals['SuspectedCasePercentage'] = $this->calculatePercentage($totals['SuspectedCases'], $totals['TotalScreenings']);
        $totals['SuspectedVHFCasePercentage'] = $this->calculatePercentage($totals['SuspectedVHFCases'], $totals['TotalScreenings']);
        $totals['HighRiskAlertPercentage'] = $this->calculatePercentage($totals['HighRiskAlerts'], $totals['TotalScreenings']);
        $totals['SymptomaticCasePercentage'] = $this->calculatePercentage($totals['SymptomaticCases'], $totals['TotalScreenings']);

        return $totals;
    }

    private function getSearchColumn($report_type)
    {
        switch ($report_type) {
            case 'province':
                return 'poe_province';
            case 'district':
                return 'poe_district';
            case 'poe':
                return 'poe_name';
            case 'screener':
                return 'screener_username';
            default:
                return 'poe_province';
        }
    }

    public function reportByProvince(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Parse the dates
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Query the database
        $report = ScreeningData::select('poe_province as Province')
            ->selectRaw('COUNT(*) as TotalScreenings')
            ->selectRaw('SUM(CASE WHEN classification = "Contact" THEN 1 ELSE 0 END) as Contacts')
            ->selectRaw('SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) as SuspectedCases')
            ->selectRaw('SUM(CASE WHEN classification = "Suspected VHF Case" THEN 1 ELSE 0 END) as SuspectedVHFCases')
            ->selectRaw('SUM(CASE WHEN high_risk_alert = TRUE THEN 1 ELSE 0 END) as HighRiskAlerts')
            ->where('classification', '!=', 'Non-Case')
            ->whereBetween('screening_timestamp', [$startDate, $endDate])
            ->groupBy('poe_province')
            ->orderByDesc('TotalScreenings')
            ->get();

        // Calculate totals
        $totals = [
            'TotalScreenings' => $report->sum('TotalScreenings'),
            'Contacts' => $report->sum('Contacts'),
            'SuspectedCases' => $report->sum('SuspectedCases'),
            'SuspectedVHFCases' => $report->sum('SuspectedVHFCases'),
            'HighRiskAlerts' => $report->sum('HighRiskAlerts'),
        ];

        // Prepare the response
        $response = [
            'report' => $report,
            'totals' => $totals,
            'metadata' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'generated_at' => now()->toDateTimeString(),
            ],
        ];

        return response()->json($response);
    }

    public function reportByDistrict(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'province' => 'nullable|string',
        ]);

        // Parse the dates
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Start building the query
        $query = DB::table('ScreeningData')
            ->select('poe_district as District', 'poe_province as Province')
            ->selectRaw('COUNT(*) as TotalScreenings')
            ->selectRaw('SUM(CASE WHEN classification = "Contact" THEN 1 ELSE 0 END) as Contacts')
            ->selectRaw('SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) as SuspectedCases')
            ->selectRaw('SUM(CASE WHEN classification = "Suspected VHF Case" THEN 1 ELSE 0 END) as SuspectedVHFCases')
            ->selectRaw('SUM(CASE WHEN high_risk_alert = TRUE THEN 1 ELSE 0 END) as HighRiskAlerts')
            ->where('classification', '!=', 'Non-Case')
            ->whereBetween('screening_timestamp', [$startDate, $endDate]);

        // Apply province filter if provided
        if ($request->has('province')) {
            $query->where('poe_province', $request->province);
        }

        // Execute the query
        $report = $query->groupBy('poe_district', 'poe_province')
            ->orderBy('poe_province')
            ->orderByDesc('TotalScreenings')
            ->get();

        // Calculate totals
        $totals = [
            'TotalScreenings' => $report->sum('TotalScreenings'),
            'Contacts' => $report->sum('Contacts'),
            'SuspectedCases' => $report->sum('SuspectedCases'),
            'SuspectedVHFCases' => $report->sum('SuspectedVHFCases'),
            'HighRiskAlerts' => $report->sum('HighRiskAlerts'),
        ];

        // Get unique provinces for metadata
        $provinces = $report->pluck('Province')->unique()->values();

        // Prepare the response
        $response = [
            'report' => $report,
            'totals' => $totals,
            'metadata' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'provinces' => $provinces,
                'generated_at' => now()->toDateTimeString(),
            ],
        ];

        return response()->json($response);
    }

    public function reportByScreener(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'poe' => 'nullable|string',
            'role' => 'nullable|string',
        ]);

        // Parse the dates
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Start building the query
        $query = DB::table('ScreeningData')
            ->select('screener_username as Screener', 'screener_role as Role', 'poe_name as PointOfEntry')
            ->selectRaw('COUNT(*) as TotalScreenings')
            ->selectRaw('SUM(CASE WHEN classification = "Contact" THEN 1 ELSE 0 END) as Contacts')
            ->selectRaw('SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) as SuspectedCases')
            ->selectRaw('SUM(CASE WHEN classification = "Suspected VHF Case" THEN 1 ELSE 0 END) as SuspectedVHFCases')
            ->selectRaw('SUM(CASE WHEN high_risk_alert = TRUE THEN 1 ELSE 0 END) as HighRiskAlerts')
            ->where('classification', '!=', 'Non-Case')
            ->whereBetween('screening_timestamp', [$startDate, $endDate]);

        // Apply POE filter if provided
        if ($request->has('poe')) {
            $query->where('poe_name', $request->poe);
        }

        // Apply role filter if provided
        if ($request->has('role')) {
            $query->where('screener_role', $request->role);
        }

        // Execute the query
        $report = $query->groupBy('screener_username', 'screener_role', 'poe_name')
            ->orderBy('poe_name')
            ->orderByDesc('TotalScreenings')
            ->get();

        // Calculate totals
        $totals = [
            'TotalScreenings' => $report->sum('TotalScreenings'),
            'Contacts' => $report->sum('Contacts'),
            'SuspectedCases' => $report->sum('SuspectedCases'),
            'SuspectedVHFCases' => $report->sum('SuspectedVHFCases'),
            'HighRiskAlerts' => $report->sum('HighRiskAlerts'),
        ];

        // Get unique POEs and roles for metadata
        $poes = $report->pluck('PointOfEntry')->unique()->values();
        $roles = $report->pluck('Role')->unique()->values();

        // Calculate average screenings per screener
        $averageScreenings = $report->count() > 0 ? $totals['TotalScreenings'] / $report->count() : 0;

        // Prepare the response
        $response = [
            'report' => $report,
            'totals' => $totals,
            'metadata' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'points_of_entry' => $poes,
                'roles' => $roles,
                'average_screenings_per_screener' => round($averageScreenings, 2),
                'generated_at' => now()->toDateTimeString(),
            ],
        ];

        return response()->json($response);

    }

    public function nationalDashboard(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'interval' => 'required|in:daily,weekly,monthly',
        ]);

        // Parse the dates
        $startDate = Carbon::parse($request->start_date)->startOfDay();
        $endDate = Carbon::parse($request->end_date)->endOfDay();

        // Determine the grouping format based on the interval
        $groupFormat = $request->interval === 'daily' ? 'Y-m-d' : ($request->interval === 'weekly' ? 'Y-W' : 'Y-m');

        // Base query for all metrics
        $baseQuery = DB::table('ScreeningData')
            ->whereBetween('screening_timestamp', [$startDate, $endDate]);

        // Fetch time series data
        $timeSeriesData = $baseQuery->clone()
            ->select(DB::raw("DATE_FORMAT(screening_timestamp, '{$groupFormat}') as date"))
            ->selectRaw('COUNT(*) as total_screenings')
            ->selectRaw('SUM(CASE WHEN classification != "Non-Case" THEN 1 ELSE 0 END) as cases')
            ->selectRaw('SUM(CASE WHEN high_risk_alert = TRUE THEN 1 ELSE 0 END) as high_risk_alerts')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Fetch top 10 POEs by screening volume
        $topPOEs = $baseQuery->clone()
            ->select('poe_name', DB::raw('COUNT(*) as screening_count'))
            ->groupBy('poe_name')
            ->orderByDesc('screening_count')
            ->limit(10)
            ->get();

        // Fetch classification distribution
        $classificationDistribution = $baseQuery->clone()
            ->select('classification', DB::raw('COUNT(*) as count'))
            ->groupBy('classification')
            ->get();

        // Fetch suspected diseases distribution
        $suspectedDiseasesDistribution = $baseQuery->clone()
            ->select('suspected_diseases')
            ->whereNotNull('suspected_diseases')
            ->get()
            ->flatMap(function ($item) {
                return json_decode($item->suspected_diseases, true);
            })
            ->groupBy('disease')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc()
            ->take(10);

        // Fetch geographic distribution
        $geographicDistribution = $baseQuery->clone()
            ->select('poe_province', 'poe_district', DB::raw('COUNT(*) as screening_count'))
            ->groupBy('poe_province', 'poe_district')
            ->orderByDesc('screening_count')
            ->get()
            ->groupBy('poe_province');

        // Calculate key performance indicators (KPIs)
        $kpis = [
            'total_screenings' => $baseQuery->clone()->count(),
            'total_cases' => $baseQuery->clone()->where('classification', '!=', 'Non-Case')->count(),
            'high_risk_alerts' => $baseQuery->clone()->where('high_risk_alert', true)->count(),
            'average_screenings_per_day' => $baseQuery->clone()->count() / $startDate->diffInDays($endDate),
        ];

        // Prepare the response
        $response = [
            'kpis' => $kpis,
            'time_series_data' => $timeSeriesData,
            'top_poes' => $topPOEs,
            'classification_distribution' => $classificationDistribution,
            'suspected_diseases_distribution' => $suspectedDiseasesDistribution,
            'geographic_distribution' => $geographicDistribution,
            'metadata' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'interval' => $request->interval,
                'generated_at' => now()->toDateTimeString(),
            ],
        ];

        return response()->json($response);
    }
}
