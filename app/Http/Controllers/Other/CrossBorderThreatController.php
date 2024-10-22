<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CrossBorderThreatController extends Controller
{
    public function getCrossBorderThreatReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $suspectedCasesByOrigin = $this->getSuspectedCasesByOrigin($startDate, $endDate);
        $highRiskTravelRoutes = $this->getHighRiskTravelRoutes($startDate, $endDate);
        $internationalHealthTrends = $this->getInternationalHealthTrends($startDate, $endDate);

        return response()->json([
            'suspected_cases_by_origin' => $suspectedCasesByOrigin,
            'high_risk_travel_routes' => $highRiskTravelRoutes,
            'international_health_trends' => $internationalHealthTrends,
        ]);
    }

    private function getSuspectedCasesByOrigin($startDate, $endDate)
    {
        return DB::table('screenings')
            ->select(
                'countryOfOrigin',
                DB::raw('COUNT(*) as total_travelers'),
                DB::raw('SUM(CASE WHEN classification = "suspected-case" THEN 1 ELSE 0 END) as suspected_cases'),
                DB::raw('SUM(CASE WHEN classification = "suspected-case" THEN 1 ELSE 0 END) / COUNT(*) as risk_ratio')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy('countryOfOrigin')
            ->orderBy('suspected_cases', 'desc')
            ->get();
    }

    private function getHighRiskTravelRoutes($startDate, $endDate)
    {
        return DB::table('screenings')
            ->select(
                'countryOfOrigin',
                'travelDestination',
                DB::raw('COUNT(*) as total_travelers'),
                DB::raw('SUM(CASE WHEN classification = "suspected-case" THEN 1 ELSE 0 END) as suspected_cases'),
                DB::raw('SUM(CASE WHEN classification = "suspected-case" THEN 1 ELSE 0 END) / COUNT(*) as risk_ratio')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy('countryOfOrigin', 'travelDestination')
            ->havingRaw('risk_ratio > ?', [0.1])  // Adjust the threshold as needed
            ->orderBy('risk_ratio', 'desc')
            ->limit(10)
            ->get();
    }

    private function getInternationalHealthTrends($startDate, $endDate)
    {
        $intervalDays = 7; // Weekly trends
        $trends = DB::table('screenings')
            ->select(
                DB::raw('DATE(timestamp) as date'),
                'countryOfOrigin',
                DB::raw('COUNT(*) as total_travelers'),
                DB::raw('SUM(CASE WHEN classification = "suspected-case" THEN 1 ELSE 0 END) as suspected_cases')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(timestamp)'), 'countryOfOrigin')
            ->orderBy(DB::raw('DATE(timestamp)'))
            ->get();

        $groupedTrends = $trends->groupBy('countryOfOrigin')->map(function ($countryData) use ($intervalDays) {
            return $countryData->chunk($intervalDays)->map(function ($chunk) {
                return [
                    'start_date' => $chunk->first()->date,
                    'end_date' => $chunk->last()->date,
                    'total_travelers' => $chunk->sum('total_travelers'),
                    'suspected_cases' => $chunk->sum('suspected_cases'),
                    'risk_ratio' => $chunk->sum('suspected_cases') / $chunk->sum('total_travelers'),
                ];
            });
        });

        return $groupedTrends;
    }
}