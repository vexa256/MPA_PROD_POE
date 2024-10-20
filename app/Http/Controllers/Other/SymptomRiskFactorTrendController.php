<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SymptomRiskFactorTrendController extends Controller
{
    public function getSymptomRiskFactorTrendReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $symptomFrequency = $this->getSymptomFrequency($startDate, $endDate);
        $riskFactorFrequency = $this->getRiskFactorFrequency($startDate, $endDate);
        $symptomDiseaseCorrelation = $this->getSymptomDiseaseCorrelation($startDate, $endDate);
        $riskFactorDiseaseCorrelation = $this->getRiskFactorDiseaseCorrelation($startDate, $endDate);
        $emergingSymptomPatterns = $this->getEmergingSymptomPatterns($startDate, $endDate);

        return response()->json([
            'symptom_frequency' => $symptomFrequency,
            'risk_factor_frequency' => $riskFactorFrequency,
            'symptom_disease_correlation' => $symptomDiseaseCorrelation,
            'risk_factor_disease_correlation' => $riskFactorDiseaseCorrelation,
            'emerging_symptom_patterns' => $emergingSymptomPatterns,
        ]);
    }

    private function getSymptomFrequency($startDate, $endDate)
    {
        return DB::table('screenings')
            ->select(
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(symptoms, "$[*]")) as symptom'),
                DB::raw('COUNT(*) as frequency')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->whereRaw('JSON_LENGTH(symptoms) > 0')
            ->groupBy(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(symptoms, "$[*]"))'))
            ->orderBy('frequency', 'desc')
            ->get();
    }

    private function getRiskFactorFrequency($startDate, $endDate)
    {
        return DB::table('screenings')
            ->select(
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(riskFactors, "$[*]")) as risk_factor'),
                DB::raw('COUNT(*) as frequency')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->whereRaw('JSON_LENGTH(riskFactors) > 0')
            ->groupBy(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(riskFactors, "$[*]"))'))
            ->orderBy('frequency', 'desc')
            ->get();
    }

    private function getSymptomDiseaseCorrelation($startDate, $endDate)
    {
        return DB::table('screenings')
            ->select(
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(symptoms, "$[*]")) as symptom'),
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease")) as disease'),
                DB::raw('COUNT(*) as correlation_count')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->whereRaw('JSON_LENGTH(symptoms) > 0')
            ->whereRaw('JSON_LENGTH(suspectedDiseases) > 0')
            ->groupBy(
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(symptoms, "$[*]"))'),
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease"))')
            )
            ->orderBy('correlation_count', 'desc')
            ->get();
    }

    private function getRiskFactorDiseaseCorrelation($startDate, $endDate)
    {
        return DB::table('screenings')
            ->select(
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(riskFactors, "$[*]")) as risk_factor'),
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease")) as disease'),
                DB::raw('COUNT(*) as correlation_count')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->whereRaw('JSON_LENGTH(riskFactors) > 0')
            ->whereRaw('JSON_LENGTH(suspectedDiseases) > 0')
            ->groupBy(
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(riskFactors, "$[*]"))'),
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease"))')
            )
            ->orderBy('correlation_count', 'desc')
            ->get();
    }

    private function getEmergingSymptomPatterns($startDate, $endDate)
    {
        $interval = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
        $midpoint = Carbon::parse($startDate)->addDays($interval / 2);

        $firstHalf = $this->getSymptomFrequency($startDate, $midpoint->toDateString());
        $secondHalf = $this->getSymptomFrequency($midpoint->addDay()->toDateString(), $endDate);

        $emergingPatterns = [];
        foreach ($secondHalf as $symptom) {
            $firstHalfFrequency = $firstHalf->firstWhere('symptom', $symptom->symptom)->frequency ?? 0;
            if ($symptom->frequency > $firstHalfFrequency * 1.5) {  // 50% increase threshold
                $emergingPatterns[] = [
                    'symptom' => $symptom->symptom,
                    'first_half_frequency' => $firstHalfFrequency,
                    'second_half_frequency' => $symptom->frequency,
                    'increase_percentage' => (($symptom->frequency - $firstHalfFrequency) / $firstHalfFrequency) * 100
                ];
            }
        }

        return $emergingPatterns;
    }
}