<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DiseasePrevalenceController extends Controller
{
    public function getDiseasePrevalenceReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $suspectedDiseaseFrequency = $this->getSuspectedDiseaseFrequency($startDate, $endDate);
        $geographicalDistribution = $this->getGeographicalDistribution($startDate, $endDate);
        $diseaseTrends = $this->getDiseaseTrends($startDate, $endDate);

        return response()->json([
            'suspected_disease_frequency' => $suspectedDiseaseFrequency,
            'geographical_distribution' => $geographicalDistribution,
            'disease_trends' => $diseaseTrends,
        ]);
    }

    private function getSuspectedDiseaseFrequency($startDate, $endDate)
    {
        return DB::table('screenings')
            ->select(
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease")) as disease'),
                DB::raw('COUNT(*) as frequency')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->whereRaw('JSON_LENGTH(suspectedDiseases) > 0')
            ->groupBy(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease"))'))
            ->orderBy('frequency', 'desc')
            ->get();
    }

    private function getGeographicalDistribution($startDate, $endDate)
    {
        return DB::table('screenings')
            ->select(
                'poe',
                'province',
                'district',
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease")) as disease'),
                DB::raw('COUNT(*) as case_count')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->whereRaw('JSON_LENGTH(suspectedDiseases) > 0')
            ->groupBy('poe', 'province', 'district', DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease"))'))
            ->orderBy('case_count', 'desc')
            ->get();
    }

    private function getDiseaseTrends($startDate, $endDate)
    {
        return DB::table('screenings')
            ->select(
                DB::raw('DATE(timestamp) as date'),
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease")) as disease'),
                DB::raw('COUNT(*) as daily_count')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->whereRaw('JSON_LENGTH(suspectedDiseases) > 0')
            ->groupBy(DB::raw('DATE(timestamp)'), DB::raw('JSON_UNQUOTE(JSON_EXTRACT(suspectedDiseases, "$[*].disease"))'))
            ->orderBy(DB::raw('DATE(timestamp)'))
            ->get();
    }
}