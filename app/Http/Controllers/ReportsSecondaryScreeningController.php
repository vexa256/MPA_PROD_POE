<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportsSecondaryScreeningController extends Controller
{

    public function getDashboardData(Request $request)
    {
        $poeId = $request->input('poeId');
        $year = $request->input('year');
        $month = $request->input('month');

        // Input validation
        if (!$poeId || !$year || !$month) {
            return response()->json(['error' => 'Invalid input parameters'], 400);
        }

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Use caching to improve performance
        $cacheKey = "dashboard_data_{$poeId}_{$year}_{$month}";
        $cacheDuration = 60 * 60; // Cache for 1 hour

        return Cache::remember($cacheKey, $cacheDuration, function () use ($poeId, $startDate, $endDate) {
            $baseQuery = DB::table('secondary_screenings_data')
                ->whereBetween('arrival_date', [$startDate, $endDate])
                ->where('poeid', $poeId);

            $totalScreenings = (clone $baseQuery)->count();
            $suspectedCases = (clone $baseQuery)->where('classification', 'Suspected Case')->count();
            $highRiskAlerts = (clone $baseQuery)->where('high_risk_alert', 1)->count();
            $referrals = (clone $baseQuery)->where('referral_status', 'Referred')->count();

            $screeningsByClassification = $this->getScreeningsByClassification($baseQuery);
            $screeningsByGender = $this->getScreeningsByGender($baseQuery);
            $topDepartureCountries = $this->getTopDepartureCountries($baseQuery);
            $topSuspectedDiseases = $this->getTopSuspectedDiseases($baseQuery);
            $dailyScreeningCounts = $this->getDailyScreeningCounts($baseQuery);
            $recentScreenings = $this->getRecentScreenings($baseQuery);
            $ageDistribution = $this->getAgeDistribution($baseQuery);

            return response()->json([
                'totalScreenings' => $totalScreenings,
                'suspectedCases' => $suspectedCases,
                'highRiskAlerts' => $highRiskAlerts,
                'referrals' => $referrals,
                'screeningsByClassification' => $screeningsByClassification,
                'screeningsByGender' => $screeningsByGender,
                'topDepartureCountries' => $topDepartureCountries,
                'topSuspectedDiseases' => $topSuspectedDiseases,
                'dailyScreeningCounts' => $dailyScreeningCounts,
                'recentScreenings' => $recentScreenings,
                'ageDistribution' => $ageDistribution,
            ]);
        });
    }

    private function getScreeningsByClassification($query)
    {
        return (clone $query)->groupBy('classification')
            ->select('classification', DB::raw('count(*) as count'))
            ->pluck('count', 'classification')
            ->toArray();
    }

    private function getScreeningsByGender($query)
    {
        return (clone $query)->groupBy('gender')
            ->select('gender', DB::raw('count(*) as count'))
            ->pluck('count', 'gender')
            ->toArray();
    }

    private function getTopDepartureCountries($query)
    {
        return (clone $query)->groupBy('departure_country')
            ->select('departure_country', DB::raw('count(*) as count'))
            ->orderByDesc('count')
            ->limit(5)
            ->pluck('count', 'departure_country')
            ->toArray();
    }

    private function getTopSuspectedDiseases($query)
    {
        $screenings = (clone $query)->whereNotNull('suspected_diseases')
            ->get(['suspected_diseases']);

        $diseases = collect();
        foreach ($screenings as $screening) {
            try {
                $suspectedDiseases = json_decode($screening->suspected_diseases, true);
                if (is_array($suspectedDiseases)) {
                    foreach ($suspectedDiseases as $disease) {
                        if (isset($disease['disease']) && isset($disease['score'])) {
                            $diseases->push($disease);
                        }
                    }
                }
            } catch (\JsonException $e) {
                // Log the error or handle it as needed
                continue;
            }
        }

        return $diseases->groupBy('disease')
            ->map(function ($group) {
                return [
                    'disease' => $group->first()['disease'] ?? 'Unknown',
                    'count' => $group->count(),
                    'averageScore' => $group->avg('score'),
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values()
            ->toArray();
    }

    private function getAgeDistribution($query)
    {
        return (clone $query)->select(DB::raw('
            CASE
                WHEN age < 18 THEN "Under 18"
                WHEN age BETWEEN 18 AND 30 THEN "18-30"
                WHEN age BETWEEN 31 AND 50 THEN "31-50"
                WHEN age BETWEEN 51 AND 70 THEN "51-70"
                ELSE "Over 70"
            END as age_group
        '))
            ->groupBy(DB::raw('
            CASE
                WHEN age < 18 THEN "Under 18"
                WHEN age BETWEEN 18 AND 30 THEN "18-30"
                WHEN age BETWEEN 31 AND 50 THEN "31-50"
                WHEN age BETWEEN 51 AND 70 THEN "51-70"
                ELSE "Over 70"
            END
        '))
            ->select(DB::raw('
            CASE
                WHEN age < 18 THEN "Under 18"
                WHEN age BETWEEN 18 AND 30 THEN "18-30"
                WHEN age BETWEEN 31 AND 50 THEN "31-50"
                WHEN age BETWEEN 51 AND 70 THEN "51-70"
                ELSE "Over 70"
            END as age_group
        '), DB::raw('count(*) as count'))
            ->pluck('count', 'age_group')
            ->toArray();
    }

    private function getDailyScreeningCounts($query)
    {
        return (clone $query)->groupBy(DB::raw('DATE(arrival_date)'))
            ->select(DB::raw('DATE(arrival_date) as date'), DB::raw('count(*) as count'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    private function getRecentScreenings($query)
    {
        return (clone $query)->orderByDesc('arrival_date')
            ->get()
            ->map(function ($screening) {
                try {
                    $screening->transit_countries = json_decode($screening->transit_countries, true) ?? [];
                    $screening->suspected_diseases = json_decode($screening->suspected_diseases, true) ?? [];
                } catch (\JsonException $e) {
                    $screening->transit_countries = [];
                    $screening->suspected_diseases = [];
                }
                return $screening;
            })
            ->toArray();
    }
}
