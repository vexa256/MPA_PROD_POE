<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuspectedCaseController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'poe' => 'nullable|string',
        ]);

        $query = DB::table('screenings')
            ->whereDate('timestamp', $request->date)
            ->where('classification', 'Suspected Case');

        if ($request->poe) {
            $query->where('poe', $request->poe);
        }

        $suspectedCases = $query->get();

        return response()->json([
            'date' => $request->date,
            'poe' => $request->poe ?? 'All',
            'total_cases' => $suspectedCases->count(),
            'cases' => $suspectedCases->map(function ($case) {
                return [
                    'screening_id' => $case->screeningId,
                    'traveller_info' => json_decode($case->travellerInfo),
                    'screening_details' => json_decode($case->screeningDetails),
                    'symptoms' => json_decode($case->symptoms),
                    'risk_factors' => json_decode($case->riskFactors),
                    'suspected_diseases' => json_decode($case->suspectedDiseases),
                    'timestamp' => $case->timestamp,
                    'poe' => $case->poe,
                    'province' => $case->province,
                    'district' => $case->district,
                    'screening_officer' => $case->screeningOfficer,
                ];
            }),
        ]);
    }

    public function poesWithCases()
    {
        $poes = DB::table('screenings')
            ->where('classification', 'Suspected Case')
            ->distinct()
            ->pluck('poe');

        return response()->json($poes);
    }

    public function demographicAnalysis(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $poe = $request->input('poe');

        $query = DB::table('screenings')->where('classification', 'Suspected Case');

        if ($startDate && $endDate) {
            $query->whereBetween('timestamp', [$startDate, $endDate]);
        }

        if ($poe) {
            $query->where('poe', $poe);
        }

        $totalSuspectedCases = $query->count();

        $ageGroupDistribution = $query->selectRaw('
            CASE
                WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(travellerInfo, "$.travellerAgeGroup")) AS CHAR) = "0-4" THEN "0-4"
                WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(travellerInfo, "$.travellerAgeGroup")) AS CHAR) = "5-14" THEN "5-14"
                WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(travellerInfo, "$.travellerAgeGroup")) AS CHAR) = "15-24" THEN "15-24"
                WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(travellerInfo, "$.travellerAgeGroup")) AS CHAR) = "25-44" THEN "25-44"
                WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(travellerInfo, "$.travellerAgeGroup")) AS CHAR) = "45-64" THEN "45-64"
                WHEN CAST(JSON_UNQUOTE(JSON_EXTRACT(travellerInfo, "$.travellerAgeGroup")) AS CHAR) = "65+" THEN "65+"
                ELSE "Unknown"
            END as age_group,
            COUNT(*) as count,
            GROUP_CONCAT(DISTINCT JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease"))) as suspected_diseases
        ')
            ->groupBy('age_group')
            ->orderBy('age_group')
            ->get()
            ->map(function ($item) {
                $item->suspected_diseases = array_count_values(explode(',', $item->suspected_diseases));
                arsort($item->suspected_diseases);
                return $item;
            })
            ->keyBy('age_group');

        $genderDistribution = $query->selectRaw('
            JSON_UNQUOTE(JSON_EXTRACT(travellerInfo, "$.travellerGender")) as gender,
            COUNT(*) as count,
            GROUP_CONCAT(DISTINCT JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease"))) as suspected_diseases
        ')
            ->groupBy('gender')
            ->orderBy('count', 'desc')
            ->get()
            ->map(function ($item) {
                $item->suspected_diseases = array_count_values(explode(',', $item->suspected_diseases));
                arsort($item->suspected_diseases);
                return $item;
            })
            ->keyBy('gender');

        $nationalityDistribution = $query->selectRaw('
            JSON_UNQUOTE(JSON_EXTRACT(screeningDetails, "$.countryOfOrigin")) as nationality,
            COUNT(*) as count,
            GROUP_CONCAT(DISTINCT JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease"))) as suspected_diseases
        ')
            ->groupBy('nationality')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->suspected_diseases = array_count_values(explode(',', $item->suspected_diseases));
                arsort($item->suspected_diseases);
                return $item;
            })
            ->keyBy('nationality');

        $poeDistribution = $query->selectRaw('
            poe,
            COUNT(*) as count,
            GROUP_CONCAT(DISTINCT JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease"))) as suspected_diseases
        ')
            ->groupBy('poe')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $item->suspected_diseases = array_count_values(explode(',', $item->suspected_diseases));
                arsort($item->suspected_diseases);
                return $item;
            })
            ->keyBy('poe');

        $overallSuspectedDiseases = $query->selectRaw('
            GROUP_CONCAT(DISTINCT JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease"))) as suspected_diseases
        ')
            ->first();

        $overallSuspectedDiseases = array_count_values(explode(',', $overallSuspectedDiseases->suspected_diseases));
        arsort($overallSuspectedDiseases);

        return response()->json([
            'total_suspected_cases' => $totalSuspectedCases,
            'overall_suspected_diseases' => $overallSuspectedDiseases,
            'age_group_distribution' => $ageGroupDistribution,
            'gender_distribution' => $genderDistribution,
            'nationality_distribution' => $nationalityDistribution,
            'poe_distribution' => $poeDistribution,
        ]);
    }

    public function travelerOriginRiskAssessment(Request $request)
    {
        $query = DB::table('screenings')
            ->where('classification', 'Suspected Case');

        $riskAssessment = $query
            ->select(
                DB::raw('DATE(timestamp) as date'),
                'countryOfOrigin',
                DB::raw('COUNT(*) as case_count'),
                DB::raw('AVG(CAST(JSON_EXTRACT(screeningDetails, "$.accuracyProbability") AS DECIMAL(5,2))) as avg_accuracy'),
                DB::raw('GROUP_CONCAT(DISTINCT JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease"))) as top_diseases')
            )
            ->groupBy('date', 'countryOfOrigin')
            ->orderBy('date')
            ->orderByDesc('case_count')
            ->get()
            ->map(function ($item) {
                $item->top_diseases = array_slice(array_count_values(explode(',', $item->top_diseases)), 0, 3, true);
                return $item;
            });

        $startDate = $riskAssessment->min('date');
        $endDate = $riskAssessment->max('date');

        return response()->json([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'risk_assessment' => $riskAssessment,
        ]);
    }

    private function getPeriodColumn($period)
    {
        switch ($period) {
            case 'month':
                return "DATE_FORMAT(timestamp, '%Y-%m')";
            case 'quarter':
                return "CONCAT(YEAR(timestamp), '-Q', QUARTER(timestamp))";
            case 'year':
                return "YEAR(timestamp)";
            default:
                return "DATE(timestamp)";
        }
    }

    public function ageGenderDistribution(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'poe' => 'nullable|string',
            'country_of_origin' => 'nullable|string',
        ]);

        $query = DB::table('screenings');

        // Apply date range filter
        if ($request->has('start_date')) {
            $query->where('timestamp', '>=', $request->start_date);
        }
        if ($request->has('end_date')) {
            $query->where('timestamp', '<=', $request->end_date);
        }

        // Apply POE filter
        if ($request->has('poe')) {
            $query->where('poe', $request->poe);
        }

        // Apply country of origin filter
        if ($request->has('country_of_origin')) {
            $query->where('countryOfOrigin', $request->country_of_origin);
        }

        $distribution = $query->select('travellerAgeGroup', 'travellerGender', DB::raw('COUNT(*) as count'))
            ->groupBy('travellerAgeGroup', 'travellerGender')
            ->orderBy('travellerAgeGroup')
            ->orderBy('travellerGender')
            ->get();

        $totalCount = $distribution->sum('count');

        $formattedDistribution = $distribution->map(function ($item) use ($totalCount) {
            $item->percentage = round(($item->count / $totalCount) * 100, 2);
            return $item;
        });

        return response()->json([
            'total_count' => $totalCount,
            'distribution' => $formattedDistribution,
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'poe' => $request->poe,
                'country_of_origin' => $request->country_of_origin,
            ],
        ]);
    }

    public function screeningOfficerPerformance(Request $request)
    {
        $query = DB::table('screenings')
            ->select('screeningOfficer',
                DB::raw('COUNT(*) as total_screenings'),
                DB::raw('SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) as suspected_cases'),
                DB::raw('SUM(CASE WHEN classification = "Non-Case" THEN 1 ELSE 0 END) as non_cases')
            )
            ->groupBy('screeningOfficer');

        if ($request->has('search')) {
            $query->where('screeningOfficer', 'like', '%' . $request->search . '%');
        }

        if ($request->has('poe')) {
            $query->where('poe', $request->poe);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('timestamp', [$request->start_date, $request->end_date]);
        }

        $performance = $query->orderByDesc('total_screenings')->get();

        return response()->json($performance);
    }

}
