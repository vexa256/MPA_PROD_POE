<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PointOfEntryPerformanceController extends Controller
{
    public function getScreeningEffectivenessReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $screeningCounts = $this->getScreeningCounts($startDate, $endDate);
        $symptomsRiskFactorsPercentage = $this->getSymptomsRiskFactorsPercentage($startDate, $endDate);
        $screeningTrends = $this->getScreeningTrends($startDate, $endDate);

        return response()->json([
            'screening_counts' => $screeningCounts,
            'symptoms_risk_factors_percentage' => $symptomsRiskFactorsPercentage,
            'screening_trends' => $screeningTrends,
        ]);
    }

    public function getCapacityAndPerformanceReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $screeningVsCapacity = $this->getScreeningVsCapacity($startDate, $endDate);
        $averageProcessingTimes = $this->getAverageProcessingTimes($startDate, $endDate);
        $detectionRates = $this->getDetectionRates($startDate, $endDate);

        return response()->json([
            'screening_vs_capacity' => $screeningVsCapacity,
            'average_processing_times' => $averageProcessingTimes,
            'detection_rates' => $detectionRates,
        ]);
    }

    private function getScreeningCounts($startDate, $endDate)
    {
        return DB::table('screenings')
            ->join('points_of_entry', 'screenings.poe', '=', 'points_of_entry.name')
            ->select('points_of_entry.name as poe', DB::raw('COUNT(*) as screening_count'))
            ->whereBetween('screenings.timestamp', [$startDate, $endDate])
            ->groupBy('points_of_entry.name')
            ->orderBy('screening_count', 'desc')
            ->get();
    }

    private function getSymptomsRiskFactorsPercentage($startDate, $endDate)
    {
        $totalScreenings = DB::table('screenings')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->count();

        $symptomsRiskFactorsCount = DB::table('screenings')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->where(function ($query) {
                $query->where('hasSymptoms', true)
                    ->orWhereRaw('JSON_LENGTH(riskFactors) > 0');
            })
            ->count();

        return [
            'total_screenings' => $totalScreenings,
            'with_symptoms_or_risk_factors' => $symptomsRiskFactorsCount,
            'percentage' => ($totalScreenings > 0) ? ($symptomsRiskFactorsCount / $totalScreenings) * 100 : 0,
        ];
    }

    private function getScreeningTrends($startDate, $endDate)
    {
        return DB::table('screenings')
            ->join('points_of_entry', 'screenings.poe', '=', 'points_of_entry.name')
            ->select(
                'points_of_entry.name as poe',
                DB::raw('DATE(screenings.timestamp) as date'),
                DB::raw('COUNT(*) as screening_count'),
                DB::raw('SUM(CASE WHEN screenings.hasSymptoms = true OR JSON_LENGTH(screenings.riskFactors) > 0 THEN 1 ELSE 0 END) as with_symptoms_or_risk_factors')
            )
            ->whereBetween('screenings.timestamp', [$startDate, $endDate])
            ->groupBy('points_of_entry.name', DB::raw('DATE(screenings.timestamp)'))
            ->orderBy(DB::raw('DATE(screenings.timestamp)'))
            ->get();
    }

    private function getScreeningVsCapacity($startDate, $endDate)
    {
        return DB::table('screenings')
            ->join('points_of_entry', 'screenings.poe', '=', 'points_of_entry.name')
            ->select(
                'points_of_entry.name as poe',
                'points_of_entry.capacity as daily_capacity',
                DB::raw('COUNT(*) as screening_count'),
                DB::raw('COUNT(*) / points_of_entry.capacity as utilization_rate')
            )
            ->whereBetween('screenings.timestamp', [$startDate, $endDate])
            ->groupBy('points_of_entry.name', 'points_of_entry.capacity')
            ->orderBy('utilization_rate', 'desc')
            ->get();
    }

    private function getAverageProcessingTimes($startDate, $endDate)
    {
        return DB::table('screenings')
            ->join('points_of_entry', 'screenings.poe', '=', 'points_of_entry.name')
            ->select(
                'points_of_entry.name as poe',
                DB::raw('AVG(JSON_EXTRACT(screenings.screeningDetails, "$.processingTime")) as avg_processing_time')
            )
            ->whereBetween('screenings.timestamp', [$startDate, $endDate])
            ->groupBy('points_of_entry.name')
            ->orderBy('avg_processing_time', 'desc')
            ->get();
    }

    private function getDetectionRates($startDate, $endDate)
    {
        return DB::table('screenings')
            ->join('points_of_entry', 'screenings.poe', '=', 'points_of_entry.name')
            ->select(
                'points_of_entry.name as poe',
                DB::raw('COUNT(*) as total_screenings'),
                DB::raw('SUM(CASE WHEN screenings.classification = "suspected-case" THEN 1 ELSE 0 END) as suspected_cases'),
                DB::raw('SUM(CASE WHEN screenings.classification = "suspected-case" THEN 1 ELSE 0 END) / COUNT(*) as detection_rate')
            )
            ->whereBetween('screenings.timestamp', [$startDate, $endDate])
            ->groupBy('points_of_entry.name')
            ->orderBy('detection_rate', 'desc')
            ->get();
    }
}