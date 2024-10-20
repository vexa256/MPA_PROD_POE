<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dailyScreeningSummary(Request $request)
    {
        $query = DB::table('screenings')
            ->select(
                DB::raw('DATE(timestamp) as screening_date'),
                'poe',
                DB::raw('COUNT(*) as total_screenings'),
                DB::raw('SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) as suspected_cases'),
                DB::raw('SUM(CASE WHEN classification = "Non-Case" THEN 1 ELSE 0 END) as non_cases')
            )
            ->groupBy('screening_date', 'poe');

        if ($request->filled('start_date')) {
            $query->where('timestamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('timestamp', '<=', $request->end_date);
        }

        if ($request->filled('poe')) {
            $query->where('poe', $request->poe);
        }

        $screenings = $query->orderBy('screening_date', 'desc')->get();

        $results = $screenings->groupBy('screening_date')->map(function ($dayScreenings) {
            $poeBreakdown = $dayScreenings->mapWithKeys(function ($screening) {
                $suspectedDiseases = DB::table('screenings')
                    ->whereDate('timestamp', $screening->screening_date)
                    ->where('poe', $screening->poe)
                    ->where('classification', 'Suspected Case')
                    ->whereNotNull('suspectedDiseases')
                    ->get()
                    ->flatMap(function ($s) {
                        return json_decode($s->suspectedDiseases, true);
                    })
                    ->groupBy('disease')
                    ->map(function ($group) {
                        return [
                            'name' => $group[0]['disease'],
                            'count' => count($group),
                        ];
                    })
                    ->values();

                return [$screening->poe => [
                    'total_screenings' => $screening->total_screenings,
                    'suspected_cases' => $screening->suspected_cases,
                    'non_cases' => $screening->non_cases,
                    'suspected_diseases' => $suspectedDiseases,
                ]];
            });

            return [
                'screening_date' => $dayScreenings[0]->screening_date,
                'total_screenings' => $dayScreenings->sum('total_screenings'),
                'suspected_cases' => $dayScreenings->sum('suspected_cases'),
                'non_cases' => $dayScreenings->sum('non_cases'),
                'poe_breakdown' => $poeBreakdown,
            ];
        })->values();

        return $results;
    }

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

    public function topSuspectedDiseases(Request $request)
    {
        $query = DB::table('screenings')
            ->select(
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease")) as diseases'),
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].accuracy")) as accuracies')
            );

        if ($request->filled('start_date')) {
            $query->where('timestamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('timestamp', '<=', $request->end_date);
        }

        if ($request->filled('poe')) {
            $query->where('poe', $request->poe);
        }

        $results = $query->get();

        $diseaseCount = [];
        $diseaseAccuracy = [];

        foreach ($results as $result) {
            $diseases = json_decode($result->diseases);
            $accuracies = json_decode($result->accuracies);

            for ($i = 0; $i < count($diseases); $i++) {
                $disease = $diseases[$i];
                $accuracy = $accuracies[$i];

                if (!isset($diseaseCount[$disease])) {
                    $diseaseCount[$disease] = 0;
                    $diseaseAccuracy[$disease] = 0;
                }

                $diseaseCount[$disease]++;
                $diseaseAccuracy[$disease] += $accuracy;
            }
        }

        $topDiseases = [];
        foreach ($diseaseCount as $disease => $count) {
            $topDiseases[] = [
                'disease' => $disease,
                'occurrence_count' => $count,
                'avg_accuracy' => $diseaseAccuracy[$disease] / $count,
            ];
        }

        usort($topDiseases, function ($a, $b) {
            return $b['occurrence_count'] - $a['occurrence_count'];
        });

        return array_slice($topDiseases, 0, 10);
    }

    public function symptomFrequencyAnalysis(Request $request)
    {
        $query = DB::table('screenings')
            ->select('symptoms')
            ->where('hasSymptoms', 1);

        if ($request->filled('start_date')) {
            $query->where('timestamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('timestamp', '<=', $request->end_date);
        }

        if ($request->filled('poe')) {
            $query->where('poe', $request->poe);
        }

        $results = $query->get();

        $symptomCount = [];
        $totalScreenings = count($results);

        foreach ($results as $result) {
            $symptoms = json_decode($result->symptoms);
            foreach ($symptoms as $symptom) {
                if (!isset($symptomCount[$symptom])) {
                    $symptomCount[$symptom] = 0;
                }
                $symptomCount[$symptom]++;
            }
        }

        $symptomFrequency = [];
        foreach ($symptomCount as $symptom => $count) {
            $symptomFrequency[] = [
                'symptom' => $symptom,
                'occurrence_count' => $count,
                'percentage' => ($count / $totalScreenings) * 100,
            ];
        }

        usort($symptomFrequency, function ($a, $b) {
            return $b['occurrence_count'] - $a['occurrence_count'];
        });

        return $symptomFrequency;
    }

    public function screeningOfficerPerformance(Request $request)
    {
        $query = DB::table('screenings')
            ->select(
                'screeningOfficer',
                DB::raw('COUNT(*) as total_screenings'),
                DB::raw('AVG(CASE WHEN classification = "Suspected Case" THEN accuracyProbability ELSE NULL END) as avg_accuracy_suspected_cases'),
                DB::raw('SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) as suspected_cases_count')
            )
            ->groupBy('screeningOfficer');

        if ($request->filled('start_date')) {
            $query->where('timestamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('timestamp', '<=', $request->end_date);
        }

        if ($request->filled('poe')) {
            $query->where('poe', $request->poe);
        }

        return $query->orderBy('total_screenings', 'desc')->get();
    }

    public function travelRouteAnalysis(Request $request)
    {
        $query = DB::table('screenings')
            ->select(
                'countryOfOrigin',
                'travelDestination',
                DB::raw('COUNT(*) as route_frequency'),
                DB::raw('SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) as suspected_cases_count')
            )
            ->groupBy('countryOfOrigin', 'travelDestination');

        if ($request->filled('start_date')) {
            $query->where('timestamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('timestamp', '<=', $request->end_date);
        }

        if ($request->filled('poe')) {
            $query->where('poe', $request->poe);
        }

        return $query->orderBy('suspected_cases_count', 'desc')
            ->orderBy('route_frequency', 'desc')
            ->limit(20)
            ->get();
    }

    public function ageGroupRiskAnalysis(Request $request)
    {
        $query = DB::table('screenings')
            ->select(
                'travellerAgeGroup',
                DB::raw('COUNT(*) as total_screenings'),
                DB::raw('SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) as suspected_cases'),
                DB::raw('(SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as suspected_case_percentage')
            )
            ->groupBy('travellerAgeGroup');

        if ($request->filled('start_date')) {
            $query->where('timestamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('timestamp', '<=', $request->end_date);
        }

        if ($request->filled('poe')) {
            $query->where('poe', $request->poe);
        }

        return $query->orderBy('suspected_case_percentage', 'desc')->get();
    }

    public function poeWorkloadAnalysis(Request $request)
    {
        $query = DB::table('screenings')
            ->select(
                'poe',
                DB::raw('COUNT(*) as total_screenings'),
                DB::raw('AVG(TIMESTAMPDIFF(MINUTE, createdAt, updatedAt)) as avg_screening_duration_minutes'),
                DB::raw('SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) as suspected_cases_count')
            )
            ->groupBy('poe');

        if ($request->filled('start_date')) {
            $query->where('timestamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('timestamp', '<=', $request->end_date);
        }

        return $query->orderBy('total_screenings', 'desc')->get();
    }

    public function riskFactorAnalysis(Request $request)
    {
        $query = DB::table('screenings')
            ->select('riskFactors');

        if ($request->filled('start_date')) {
            $query->where('timestamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('timestamp', '<=', $request->end_date);
        }

        if ($request->filled('poe')) {
            $query->where('poe', $request->poe);
        }

        $results = $query->get();

        $riskFactorCount = [];
        $totalScreenings = count($results);

        foreach ($results as $result) {
            $riskFactors = json_decode($result->riskFactors);
            foreach ($riskFactors as $factor) {
                if (!isset($riskFactorCount[$factor])) {
                    $riskFactorCount[$factor] = 0;
                }
                $riskFactorCount[$factor]++;
            }
        }

        $riskFactorAnalysis = [];
        foreach ($riskFactorCount as $factor => $count) {
            $riskFactorAnalysis[] = [
                'risk_factor' => $factor,
                'occurrence_count' => $count,
                'percentage' => ($count / $totalScreenings) * 100,
            ];
        }

        usort($riskFactorAnalysis, function ($a, $b) {
            return $b['occurrence_count'] - $a['occurrence_count'];
        });

        return $riskFactorAnalysis;
    }

    public function genderAnalysis(Request $request)
    {
        $query = DB::table('screenings')
            ->select(
                'travellerGender',
                DB::raw('COUNT(*) as total_screenings'),
                DB::raw('SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) as suspected_cases'),
                DB::raw('(SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as suspected_case_percentage')
            )
            ->groupBy('travellerGender');

        if ($request->filled('start_date')) {
            $query->where('timestamp', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('timestamp', '<=', $request->end_date);
        }

        if ($request->filled('poe')) {
            $query->where('poe', $request->poe);
        }

        return $query->orderBy('suspected_case_percentage', 'desc')->get();
    }
}
