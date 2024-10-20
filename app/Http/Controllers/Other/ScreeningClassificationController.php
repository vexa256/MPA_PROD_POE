<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScreeningClassificationController extends Controller
{
    public function getScreeningClassificationReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $classificationDistribution = $this->getClassificationDistribution($startDate, $endDate);
        $suspectedCaseFollowUp = $this->getSuspectedCaseFollowUp($startDate, $endDate);
        $classificationAccuracy = $this->getClassificationAccuracy($startDate, $endDate);

        return response()->json([
            'classification_distribution' => $classificationDistribution,
            'suspected_case_follow_up' => $suspectedCaseFollowUp,
            'classification_accuracy' => $classificationAccuracy,
        ]);
    }

    private function getClassificationDistribution($startDate, $endDate)
    {
        return DB::table('screenings')
            ->select('classification', DB::raw('COUNT(*) as count'))
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy('classification')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getSuspectedCaseFollowUp($startDate, $endDate)
    {
        // Assuming we have a follow_up_results table linked to screenings
        return DB::table('screenings')
            ->leftJoin('follow_up_results', 'screenings.screeningId', '=', 'follow_up_results.screeningId')
            ->select(
                DB::raw('COUNT(screenings.id) as total_suspected_cases'),
                DB::raw('COUNT(follow_up_results.id) as followed_up_cases'),
                DB::raw('SUM(CASE WHEN follow_up_results.final_result = "confirmed" THEN 1 ELSE 0 END) as confirmed_cases'),
                DB::raw('SUM(CASE WHEN follow_up_results.final_result = "cleared" THEN 1 ELSE 0 END) as cleared_cases'),
                DB::raw('SUM(CASE WHEN follow_up_results.final_result IS NULL THEN 1 ELSE 0 END) as pending_cases')
            )
            ->where('screenings.classification', 'suspected-case')
            ->whereBetween('screenings.timestamp', [$startDate, $endDate])
            ->first();
    }

    private function getClassificationAccuracy($startDate, $endDate)
    {
        // Assuming we have a follow_up_results table linked to screenings
        $results = DB::table('screenings')
            ->leftJoin('follow_up_results', 'screenings.screeningId', '=', 'follow_up_results.screeningId')
            ->select(
                'screenings.classification',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN follow_up_results.final_result = "confirmed" THEN 1 ELSE 0 END) as confirmed'),
                DB::raw('SUM(CASE WHEN follow_up_results.final_result = "cleared" THEN 1 ELSE 0 END) as cleared')
            )
            ->whereBetween('screenings.timestamp', [$startDate, $endDate])
            ->groupBy('screenings.classification')
            ->get();

        $accuracy = $results->map(function ($item) {
            $accuracy = $item->total > 0 ? 
                ($item->classification === 'suspected-case' ? $item->confirmed : $item->cleared) / $item->total : 0;
            
            return [
                'classification' => $item->classification,
                'total' => $item->total,
                'correct' => $item->classification === 'suspected-case' ? $item->confirmed : $item->cleared,
                'accuracy' => $accuracy,
            ];
        });

        return $accuracy;
    }
}