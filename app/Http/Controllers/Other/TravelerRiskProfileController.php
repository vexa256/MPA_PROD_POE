<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TravelerRiskProfileController extends Controller
{
    public function getTravelerRiskProfileReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $demographicDistribution = $this->getDemographicDistribution($startDate, $endDate);
        $highRiskTravelRoutes = $this->getHighRiskTravelRoutes($startDate, $endDate);
        $travelerCharacteristicsCorrelation = $this->getTravelerCharacteristicsCorrelation($startDate, $endDate);

        return response()->json([
            'demographic_distribution' => $demographicDistribution,
            'high_risk_travel_routes' => $highRiskTravelRoutes,
            'traveler_characteristics_correlation' => $travelerCharacteristicsCorrelation,
        ]);
    }

    private function getDemographicDistribution($startDate, $endDate)
    {
        $ageGroupDistribution = DB::table('screenings')
            ->select('travellerAgeGroup', DB::raw('COUNT(*) as count'))
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy('travellerAgeGroup')
            ->get();

        $genderDistribution = DB::table('screenings')
            ->select('travellerGender', DB::raw('COUNT(*) as count'))
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy('travellerGender')
            ->get();

        return [
            'age_group_distribution' => $ageGroupDistribution,
            'gender_distribution' => $genderDistribution,
        ];
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
            ->havingRaw('risk_ratio > 0')
            ->orderBy('risk_ratio', 'desc')
            ->limit(10)
            ->get();
    }

    private function getTravelerCharacteristicsCorrelation($startDate, $endDate)
    {
        $ageGroupCorrelation = DB::table('screenings')
            ->select(
                'travellerAgeGroup',
                DB::raw('COUNT(*) as total_travelers'),
                DB::raw('SUM(CASE WHEN classification = "suspected-case" THEN 1 ELSE 0 END) as suspected_cases'),
                DB::raw('SUM(CASE WHEN classification = "suspected-case" THEN 1 ELSE 0 END) / COUNT(*) as risk_ratio')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy('travellerAgeGroup')
            ->orderBy('risk_ratio', 'desc')
            ->get();

        $genderCorrelation = DB::table('screenings')
            ->select(
                'travellerGender',
                DB::raw('COUNT(*) as total_travelers'),
                DB::raw('SUM(CASE WHEN classification = "suspected-case" THEN 1 ELSE 0 END) as suspected_cases'),
                DB::raw('SUM(CASE WHEN classification = "suspected-case" THEN 1 ELSE 0 END) / COUNT(*) as risk_ratio')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy('travellerGender')
            ->orderBy('risk_ratio', 'desc')
            ->get();

        $travelPurposeCorrelation = DB::table('screenings')
            ->select(
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(screeningDetails, "$.travelPurpose")) as travel_purpose'),
                DB::raw('COUNT(*) as total_travelers'),
                DB::raw('SUM(CASE WHEN classification = "suspected-case" THEN 1 ELSE 0 END) as suspected_cases'),
                DB::raw('SUM(CASE WHEN classification = "suspected-case" THEN 1 ELSE 0 END) / COUNT(*) as risk_ratio')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(screeningDetails, "$.travelPurpose"))'))
            ->orderBy('risk_ratio', 'desc')
            ->get();

        return [
            'age_group_correlation' => $ageGroupCorrelation,
            'gender_correlation' => $genderCorrelation,
            'travel_purpose_correlation' => $travelPurposeCorrelation,
        ];
    }
}