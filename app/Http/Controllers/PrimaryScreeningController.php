<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrimaryScreeningController extends Controller
{

    public function getScreenings(Request $request)
    {
        $request->validate([
            'poeId' => 'required|integer',
            'filterType' => 'required|in:date,month,year',
            'classification' => 'required|in:Non-Case',
            'date' => 'required_if:filterType,date|date',
            'month' => 'required_if:filterType,month|date_format:Y-m',
            'year' => 'required_if:filterType,year|integer|min:2000|max:' . date('Y'),
        ]);

        $poeId = $request->input('poeId');
        $filterType = $request->input('filterType');
        $classification = $request->input('classification');

        $baseQuery = DB::table('ScreeningData')
            ->where('poe_id', $poeId)
            ->where('classification', $classification);

        switch ($filterType) {
            case 'date':
                $date = Carbon::parse($request->input('date'))->toDateString();
                $baseQuery->whereDate('screening_timestamp', $date);
                break;
            case 'month':
                $date = Carbon::parse($request->input('month'));
                $baseQuery->whereYear('screening_timestamp', $date->year)
                    ->whereMonth('screening_timestamp', $date->month);
                break;
            case 'year':
                $year = $request->input('year');
                $baseQuery->whereYear('screening_timestamp', $year);
                break;
        }

        $totalScreenings = $baseQuery->count();

        $genderCounts = $baseQuery->select('traveller_gender', DB::raw('COUNT(*) as count'))
            ->groupBy('traveller_gender')
            ->get()
            ->pluck('count', 'traveller_gender')
            ->toArray();

        $dailyScreenings = $baseQuery->select(
            DB::raw('DATE(screening_timestamp) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->groupBy(DB::raw('DATE(screening_timestamp)'))
            ->orderBy(DB::raw('DATE(screening_timestamp)'))
            ->get();

        // Fix for the ONLY_FULL_GROUP_BY issue
        $recentScreenings = $baseQuery->select('screening_id', 'screening_timestamp', 'traveller_gender')
            ->orderBy('screening_timestamp', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'totalScreenings' => $totalScreenings,
            'maleCount' => $genderCounts['Male'] ?? 0,
            'femaleCount' => $genderCounts['Female'] ?? 0,
            'otherCount' => $genderCounts['Other'] ?? 0,
            'dailyLabels' => $dailyScreenings->pluck('date'),
            'dailyCounts' => $dailyScreenings->pluck('count'),
            'recentScreenings' => $recentScreenings,
        ]);
    }
}
