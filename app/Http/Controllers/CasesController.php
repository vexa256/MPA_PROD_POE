<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CasesController extends Controller
{
    public function CasesReport(Request $request)
    {
        $query = DB::table('secondary_screenings_data')
            ->join('points_of_entry', 'secondary_screenings_data.poeid', '=', 'points_of_entry.id')
            ->select(
                'secondary_screenings_data.*',
                'points_of_entry.name as poe_name',
                'points_of_entry.type as poe_type',
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(points_of_entry.location, "$.province")) as province'),
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(points_of_entry.location, "$.district")) as district')
            );

        // Apply filters
        $filters = [
            'month' => $request->input('month', date('m')),
            'year' => $request->input('year', date('Y')),
            'province' => $request->input('province'),
            'district' => $request->input('district'),
            'poe_id' => $request->input('poe_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        if ($filters['start_date'] && $filters['end_date']) {
            $query->whereBetween('secondary_screenings_data.created_at', [$filters['start_date'], $filters['end_date']]);
        } elseif ($filters['month'] && $filters['year']) {
            $query->whereYear('secondary_screenings_data.created_at', $filters['year'])
                ->whereMonth('secondary_screenings_data.created_at', $filters['month']);
        }

        if ($filters['province']) {
            $query->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(points_of_entry.location, "$.province")) = ?', [$filters['province']]);
        }

        if ($filters['district']) {
            $query->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(points_of_entry.location, "$.district")) = ?', [$filters['district']]);
        }

        if ($filters['poe_id']) {
            $query->where('secondary_screenings_data.poeid', $filters['poe_id']);
        }

        $suspectedCases = $query->get();

        // Process data for suspected disease counts and trend analysis
        $diseaseCounts = [];
        $monthlyTrend = [];

        foreach ($suspectedCases as $case) {
            $suspectedDiseases = json_decode($case->suspected_diseases, true);
            $caseDate = Carbon::parse($case->created_at);
            $monthYear = $caseDate->format('Y-m');

            foreach ($suspectedDiseases as $disease) {
                $diseaseName = $disease['disease'];
                if (!isset($diseaseCounts[$diseaseName])) {
                    $diseaseCounts[$diseaseName] = 0;
                }
                $diseaseCounts[$diseaseName]++;

                if (!isset($monthlyTrend[$monthYear])) {
                    $monthlyTrend[$monthYear] = [];
                }
                if (!isset($monthlyTrend[$monthYear][$diseaseName])) {
                    $monthlyTrend[$monthYear][$diseaseName] = 0;
                }
                $monthlyTrend[$monthYear][$diseaseName]++;
            }
        }

        // Sort monthly trend by date
        ksort($monthlyTrend);

        // Ensure all diseases are represented in each month
        $allDiseases = array_keys($diseaseCounts);
        foreach ($monthlyTrend as &$month) {
            foreach ($allDiseases as $disease) {
                if (!isset($month[$disease])) {
                    $month[$disease] = 0;
                }
            }
        }

        // Prepare data for the view
        $data = [
            'Title' => 'Monthly Suspected Disease Incidence Report',
            'Desc' => 'Analysis of suspected disease cases with filtering options.',
            'Page' => 'cases.MonthlyIncidenceReport',
            'MonthlyIncidenceReportKey' => 'true',
            'suspectedCases' => $suspectedCases,
            'diseaseCounts' => $diseaseCounts,
            'monthlyTrend' => $monthlyTrend,
            'totalCases' => count($suspectedCases),
            'pointsOfEntry' => DB::table('points_of_entry')->select('id', 'name')->get(),
            'provinces' => DB::table('points_of_entry')
                ->select(DB::raw('DISTINCT JSON_UNQUOTE(JSON_EXTRACT(location, "$.province")) as province'))
                ->pluck('province'),
            'districts' => DB::table('points_of_entry')
                ->select(DB::raw('DISTINCT JSON_UNQUOTE(JSON_EXTRACT(location, "$.district")) as district'))
                ->pluck('district'),
            'filters' => $filters,
        ];

        return view('scrn', $data);
    }
}
