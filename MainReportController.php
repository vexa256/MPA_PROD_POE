<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use DB;

// use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

// use Carbon\Carbon;

class MainReportsController extends Controller
{
    // public function getScreeningSummary(Request $request)
    // {
    //     // Validate input parameters
    //     $request->validate([
    //         'year' => 'integer|nullable',
    //         'month' => 'integer|min:1|max:12|nullable',
    //         'date' => 'date|nullable',
    //         'poe_id' => 'integer|nullable',
    //     ]);

    //     // Get user data from AUTH_DATA header
    //     $authData = json_decode($request->header('AUTH_DATA'), true);
    //     $user = $authData['user'];

    //     // Build the base query
    //     $query = DB::table('ScreeningData')
    //         ->select(
    //             'poe_id',
    //             'poe_name',
    //             'poe_type',
    //             DB::raw('COUNT(*) as total_screenings'),
    //             DB::raw('SUM(CASE WHEN classification != "Non-Case" THEN 1 ELSE 0 END) as suspected_cases'),
    //             DB::raw('SUM(CASE WHEN classification = "Suspected VHF Case" THEN 1 ELSE 0 END) as suspected_vhf_cases'),
    //             DB::raw('SUM(CASE WHEN classification = "Contact" THEN 1 ELSE 0 END) as contacts'),
    //             DB::raw('SUM(CASE WHEN classification = "Non-Case" THEN 1 ELSE 0 END) as non_cases')
    //         )->where('poe_id', $user['poeId'])
    //         ->groupBy('poe_id', 'poe_name', 'poe_type');

    //     // Apply role-based access control and set data scope
    //     // $dataScope = '';
    //     // if ($user['role'] !== 'admin') {
    //     //     $query->where('poe_id', $user['poeId']);
    //     //     $dataScope = "Showing data for POE: " . $user['name'];
    //     // } else {
    //     //     $dataScope = "Showing data for all POEs";
    //     // }

    //     $dataScope = "Showing data for POE: " . $user['name'];

    //     // Apply filters if provided
    //     if ($request->filled('year')) {
    //         $query->whereYear('screening_timestamp', $request->year);
    //     }

    //     if ($request->filled('month')) {
    //         $query->whereMonth('screening_timestamp', $request->month);
    //     }

    //     if ($request->filled('date')) {
    //         $query->whereDate('screening_timestamp', $request->date);
    //     }

    //     if ($request->filled('poe_id')) {
    //         $query->where('poe_id', $request->poe_id);
    //         $dataScope = "Showing data for POE ID: " . $request->poe_id;
    //     }

    //     // Execute the query
    //     $results = $query->get();

    //     // Prepare the response
    //     $response = [
    //         'data_scope' => $dataScope,
    //         'summary' => $results,
    //         'total' => [
    //             'screenings' => $results->sum('total_screenings'),
    //             'suspected_cases' => $results->sum('suspected_cases'),
    //             'suspected_vhf_cases' => $results->sum('suspected_vhf_cases'),
    //             'contacts' => $results->sum('contacts'),
    //             'non_cases' => $results->sum('non_cases'),
    //         ],
    //     ];

    //     return response()->json($response);
    // }



    public function getScreeningData(Request $request)
    {
        try {
            // Validate input parameters
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
                'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
                'month' => 'nullable|integer|min:1|max:12',
                'per_page' => 'nullable|integer|min:10|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Get user data from AUTH_DATA header
            $authData = json_decode($request->header('AUTH_DATA'), true);
            if (!$authData || !isset($authData['user']['poeId'])) {
                return response()->json(['error' => 'Invalid or missing AUTH_DATA'], 401);
            }

            $poeId = $authData['user']['poeId'];

            // Generate cache key
            $cacheKey = "high_risk_screening_data_{$poeId}_" . md5(json_encode($request->all()));

            // Try to get data from cache
            return Cache::remember($cacheKey, 300, function () use ($request, $poeId) {
                // Build the base query
                $query = DB::table('ScreeningData')
                    ->where('poe_id', $poeId)
                    ->where('high_risk_alert', 1);

                // Apply filters
                $this->applyFilters($query, $request);

                // Execute the query with pagination
                $perPage = $request->input('per_page', 50);
                $screeningData = $query->paginate($perPage);

                // Process the data (decode JSON fields)
                $screeningData->getCollection()->transform(function ($item) {
                    $item->symptoms = json_decode($item->symptoms);
                    $item->risk_factors = json_decode($item->risk_factors);
                    $item->suspected_diseases = json_decode($item->suspected_diseases);
                    return $item;
                });

                // Fetch filter options
                $filterOptions = $this->getFilterOptions($poeId);

                return response()->json([
                    'data' => $screeningData,
                    'filter_options' => $filterOptions,
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error fetching high-risk screening data: ' . $e->getMessage(), [
                'user_id' => $authData['user']['id'] ?? 'unknown',
                'poe_id' => $poeId ?? 'unknown',
                'request' => $request->all(),
            ]);
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    private function applyFilters($query, Request $request)
    {
        // Date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('screening_timestamp', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('year') && $request->filled('month')) {
            $query->whereYear('screening_timestamp', $request->year)
                ->whereMonth('screening_timestamp', $request->month);
        } elseif ($request->filled('year')) {
            $query->whereYear('screening_timestamp', $request->year);
        } else {
            // Default to today's data
            $today = Carbon::today()->toDateString();
            $query->whereDate('screening_timestamp', $today);
        }
    }

    private function getFilterOptions($poeId)
    {
        $cacheKey = "high_risk_filter_options_{$poeId}";

        return Cache::remember($cacheKey, 3600, function () use ($poeId) {
            $data = DB::table('ScreeningData')
                ->where('poe_id', $poeId)
                ->where('high_risk_alert', 1)
                ->select(
                    DB::raw('MIN(screening_timestamp) as min_date'),
                    DB::raw('MAX(screening_timestamp) as max_date')
                )
                ->first();

            $years = DB::table('ScreeningData')
                ->where('poe_id', $poeId)
                ->where('high_risk_alert', 1)
                ->selectRaw('DISTINCT YEAR(screening_timestamp) as year')
                ->orderBy('year', 'desc')
                ->pluck('year');

            $months = [
                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
            ];

            return [
                'date_range' => [
                    'min' => $data->min_date,
                    'max' => $data->max_date,
                ],
                'years' => $years,
                'months' => $months,
            ];
        });
    }

    public function getAllCases(Request $request)
    {
        try {
            // Validate input parameters
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
                'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
                'month' => 'nullable|integer|min:1|max:12',
                'per_page' => 'nullable|integer|min:10|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Get user data from AUTH_DATA header
            $authData = json_decode($request->header('AUTH_DATA'), true);
            if (!$authData || !isset($authData['user']['poeId'])) {
                return response()->json(['error' => 'Invalid or missing AUTH_DATA'], 401);
            }

            $poeId = $authData['user']['poeId'];

            // Generate cache key
            $cacheKey = "712high_risk_screening_data_{$poeId}_" . md5(json_encode($request->all()));

            // Try to get data from cache
            return Cache::remember($cacheKey, 300, function () use ($request, $poeId) {
                // Build the base query
                $query = DB::table('ScreeningData')
                    ->where('poe_id', $poeId)
                    ->where('classification', '!=', "Non-Case");

                // Apply filters
                $this->applyFilters($query, $request);

                // Execute the query with pagination
                $perPage = $request->input('per_page', 50);
                $screeningData = $query->paginate($perPage);

                // Process the data (decode JSON fields)
                $screeningData->getCollection()->transform(function ($item) {
                    $item->symptoms = json_decode($item->symptoms);
                    $item->risk_factors = json_decode($item->risk_factors);
                    $item->suspected_diseases = json_decode($item->suspected_diseases);
                    return $item;
                });

                // Fetch filter options
                $filterOptions = $this->getFilterOptions($poeId);

                return response()->json([
                    'data' => $screeningData,
                    'filter_options' => $filterOptions,
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error fetching high-risk screening data: ' . $e->getMessage(), [
                'user_id' => $authData['user']['id'] ?? 'unknown',
                'poe_id' => $poeId ?? 'unknown',
                'request' => $request->all(),
            ]);
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    public function getAllContacts(Request $request)
    {
        try {
            // Validate input parameters
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
                'year' => 'nullable|integer|min:2000|max:' . (date('Y') + 1),
                'month' => 'nullable|integer|min:1|max:12',
                'per_page' => 'nullable|integer|min:10|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            // Get user data from AUTH_DATA header
            $authData = json_decode($request->header('AUTH_DATA'), true);
            if (!$authData || !isset($authData['user']['poeId'])) {
                return response()->json(['error' => 'Invalid or missing AUTH_DATA'], 401);
            }

            $poeId = $authData['user']['poeId'];

            // Generate cache key
            $cacheKey = "contacts2_screening_data_{$poeId}_" . md5(json_encode($request->all()));

            // Try to get data from cache
            return Cache::remember($cacheKey, 300, function () use ($request, $poeId) {
                // Build the base query
                $query = DB::table('ScreeningData')
                    ->where('poe_id', $poeId)
                    ->where('classification', "Contact");

                // Apply filters
                $this->applyFilters($query, $request);

                // Execute the query with pagination
                $perPage = $request->input('per_page', 50);
                $screeningData = $query->paginate($perPage);

                // Process the data (decode JSON fields)
                $screeningData->getCollection()->transform(function ($item) {
                    $item->symptoms = json_decode($item->symptoms);
                    $item->risk_factors = json_decode($item->risk_factors);
                    $item->suspected_diseases = json_decode($item->suspected_diseases);
                    return $item;
                });

                // Fetch filter options
                $filterOptions = $this->getFilterOptions($poeId);

                return response()->json([
                    'data' => $screeningData,
                    'filter_options' => $filterOptions,
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Error fetching high-risk screening data: ' . $e->getMessage(), [
                'user_id' => $authData['user']['id'] ?? 'unknown',
                'poe_id' => $poeId ?? 'unknown',
                'request' => $request->all(),
            ]);
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    public function TravellerRoutes(Request $request)
    {
        try {
            $query = DB::table('ScreeningData')
                ->select(
                    'origin_country',
                    'poe_id',
                    'poe_name',
                    'poe_type',
                    DB::raw('COUNT(*) AS total_screenings'),
                    DB::raw('SUM(CASE WHEN classification = "Suspected Case" THEN 1 ELSE 0 END) AS suspected_cases_count'),
                    DB::raw('SUM(CASE WHEN classification = "Suspected VHF Case" THEN 1 ELSE 0 END) AS suspected_vhf_cases_count'),
                    DB::raw('SUM(CASE WHEN classification = "Contact" THEN 1 ELSE 0 END) AS contacts_count'),
                    DB::raw('SUM(CASE WHEN high_risk_alert = 1 THEN 1 ELSE 0 END) AS high_risk_cases_count')
                )
                ->where('classification', '!=', 'Non-Case')
                ->groupBy('origin_country', 'poe_id', 'poe_name', 'poe_type');

            // Apply POE ID filter
            $poeId = $request->header('AUTH_DATA') ? json_decode($request->header('AUTH_DATA'))->user->poeId : null;
            if ($poeId) {
                $query->where('poe_id', $poeId);
            }

            // Apply date filters
            if ($request->has('start_date') && $request->start_date) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $query->where('screening_timestamp', '>=', $startDate);
            }
            if ($request->has('end_date') && $request->end_date) {
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->where('screening_timestamp', '<=', $endDate);
            }

            // Apply year filter
            if ($request->has('year') && $request->year) {
                $query->whereYear('screening_timestamp', $request->year);
            }

            // Apply month filter
            if ($request->has('month') && $request->month) {
                $query->whereMonth('screening_timestamp', $request->month);
            }

            // Paginate the results
            $perPage = $request->input('per_page', 50);
            $page = $request->input('page', 1);
            $total = $query->count();
            $screeningData = $query->offset(($page - 1) * $perPage)->limit($perPage)->get();

            // Prepare filter options
            $filterOptions = [
                'date_range' => [
                    'min' => DB::table('ScreeningData')->min('screening_timestamp'),
                    'max' => DB::table('ScreeningData')->max('screening_timestamp'),
                ],
                'years' => DB::table('ScreeningData')
                    ->selectRaw('DISTINCT YEAR(screening_timestamp) as year')
                    ->pluck('year')
                    ->sort()
                    ->values(),
                'months' => [
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                ],
            ];

            return response()->json([
                'data' => [
                    'current_page' => $page,
                    'data' => $screeningData,
                    'first_page_url' => url("/api/TravellerRoutes?page=1"),
                    'from' => ($page - 1) * $perPage + 1,
                    'last_page' => ceil($total / $perPage),
                    'last_page_url' => url("/api/TravellerRoutes?page=" . ceil($total / $perPage)),
                    'next_page_url' => $page < ceil($total / $perPage) ? url("/api/TravellerRoutes?page=" . ($page + 1)) : null,
                    'path' => url("/api/TravellerRoutes"),
                    'per_page' => $perPage,
                    'prev_page_url' => $page > 1 ? url("/api/TravellerRoutes?page=" . ($page - 1)) : null,
                    'to' => min($page * $perPage, $total),
                    'total' => $total,
                ],
                'filter_options' => $filterOptions,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching data: ' . $e->getMessage()], 500);
        }
    }

}
