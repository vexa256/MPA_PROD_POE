<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CasesByProvince extends Controller
{
    public function getSuspectedCasesByProvince(Request $request)
    {
        // Set default values for filters if not provided
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->startOfMonth();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfMonth();
        $provinceFilter = $request->input('province');
        $monthFilter = $request->input('month');

        // Start building the query
        $query = DB::table('secondary_screenings_data')
            ->select(
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.province')) as province"),
                DB::raw('JSON_UNQUOTE(suspected_diseases->"$[0].disease") as suspected_disease'),
                DB::raw('COUNT(secondary_screenings_data.id) as suspected_count')
            )
            ->join('points_of_entry', 'secondary_screenings_data.poeid', '=', 'points_of_entry.id')
            ->where('secondary_screenings_data.classification', 'Suspected Case')
            ->groupBy('province', 'suspected_disease');

        // Apply filters if provided
        if ($provinceFilter) {
            $query->where(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.province'))"), $provinceFilter);
        }

        if ($monthFilter) {
            $query->whereMonth('secondary_screenings_data.created_at', '=', $monthFilter);
        } else {
            // Default to current month
            $query->whereMonth('secondary_screenings_data.created_at', '=', Carbon::now()->month);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('secondary_screenings_data.created_at', [$startDate, $endDate]);
        }

        // Execute the query
        $suspectedCasesByProvince = $query->get();

        // Query to get suspected cases for specific diseases (VHF, Marburg, Mpox, Cholera)
        $diseases = ['VHF', 'Marburg', 'Mpox', 'Cholera'];
        $diseaseCounts = DB::table('secondary_screenings_data')
            ->select(DB::raw('JSON_UNQUOTE(suspected_diseases->"$[0].disease") as suspected_disease'), DB::raw('COUNT(*) as total_count'))
            ->whereIn(DB::raw('JSON_UNQUOTE(suspected_diseases->"$[0].disease")'), $diseases)
            ->groupBy('suspected_disease')
            ->get();

        // Prepare data for chart plotting
        $diseaseNames = [];
        $diseaseTotals = [];
        foreach ($diseaseCounts as $disease) {
            $diseaseNames[] = $disease->suspected_disease;
            $diseaseTotals[] = $disease->total_count;
        }

        // Prepare data for the view
        $data = [
            'Title' => 'Monthly Suspected Disease Incidence Report',
            'Desc' => 'Analysis of suspected disease cases with filtering options.',
            'Page' => 'cases.CasesByProvince',
            'MonthlyIncidenceReportKey' => 'true',
            'suspectedCasesByProvince' => $suspectedCasesByProvince,
            'diseaseNames' => json_encode($diseaseNames),
            'diseaseTotals' => json_encode($diseaseTotals),
            'filters' => [
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'province' => $provinceFilter,
                'month' => $monthFilter,
            ],
        ];

        return view('scrn', $data);
    }
}
