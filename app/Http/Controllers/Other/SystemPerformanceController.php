<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SystemPerformanceController extends Controller
{
    public function getSystemPerformanceReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $responseTimesAnalysis = $this->getResponseTimesAnalysis($startDate, $endDate);
        $errorRatesAnalysis = $this->getErrorRatesAnalysis($startDate, $endDate);
        $systemLoadAnalysis = $this->getSystemLoadAnalysis($startDate, $endDate);
        $dataProcessingMetrics = $this->getDataProcessingMetrics($startDate, $endDate);

        return response()->json([
            'response_times_analysis' => $responseTimesAnalysis,
            'error_rates_analysis' => $errorRatesAnalysis,
            'system_load_analysis' => $systemLoadAnalysis,
            'data_processing_metrics' => $dataProcessingMetrics,
        ]);
    }

    private function getResponseTimesAnalysis($startDate, $endDate)
    {
        return DB::table('system_logs')
            ->select(
                DB::raw('DATE(timestamp) as date'),
                DB::raw('AVG(JSON_EXTRACT(message, "$.response_time")) as avg_response_time'),
                DB::raw('MAX(JSON_EXTRACT(message, "$.response_time")) as max_response_time')
            )
            ->where('logLevel', 'info')
            ->where('source', 'api_request')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(timestamp)'))
            ->orderBy('date')
            ->get();
    }

    private function getErrorRatesAnalysis($startDate, $endDate)
    {
        $totalRequests = DB::table('system_logs')
            ->where('source', 'api_request')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->count();

        $errorCounts = DB::table('system_logs')
            ->select(
                DB::raw('DATE(timestamp) as date'),
                DB::raw('COUNT(*) as error_count')
            )
            ->where('logLevel', 'error')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(timestamp)'))
            ->orderBy('date')
            ->get();

        return [
            'total_requests' => $totalRequests,
            'error_counts' => $errorCounts,
        ];
    }

    private function getSystemLoadAnalysis($startDate, $endDate)
    {
        return DB::table('system_logs')
            ->select(
                DB::raw('DATE(timestamp) as date'),
                DB::raw('HOUR(timestamp) as hour'),
                DB::raw('AVG(JSON_EXTRACT(message, "$.cpu_usage")) as avg_cpu_usage'),
                DB::raw('AVG(JSON_EXTRACT(message, "$.memory_usage")) as avg_memory_usage')
            )
            ->where('source', 'system_metrics')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(timestamp)'), DB::raw('HOUR(timestamp)'))
            ->orderBy('date')
            ->orderBy('hour')
            ->get();
    }

    private function getDataProcessingMetrics($startDate, $endDate)
    {
        $screeningsProcessed = DB::table('screenings')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->count();

        $avgProcessingTime = DB::table('screenings')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->avg(DB::raw('JSON_EXTRACT(screeningDetails, "$.processingTime")'));

        $syncStatus = DB::table('sync_status')
            ->select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('createdAt', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        return [
            'screenings_processed' => $screeningsProcessed,
            'avg_processing_time' => $avgProcessingTime,
            'sync_status' => $syncStatus,
        ];
    }
}