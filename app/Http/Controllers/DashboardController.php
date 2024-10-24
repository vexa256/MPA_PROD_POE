<?php

namespace App\Http\Controllers;

use DB;

class DashboardController extends Controller
{

    public function MainDashboard()
    {
        // Get the distribution of POEs by type
        $poeDistribution = DB::table('points_of_entry')
            ->select('type', DB::raw('COUNT(*) as total'))
            ->groupBy('type')
            ->get();

        // Extract totals for each type for the summary stats
        $totalAirports = $poeDistribution->firstWhere('type', 'airport')->total ?? 0;
        $totalLandBorders = $poeDistribution->firstWhere('type', 'land_border')->total ?? 0;
        $totalSeaports = $poeDistribution->firstWhere('type', 'seaport')->total ?? 0;

        // Query to get the User Role Distribution (focus on 'admin' and 'screener')
        $userRolesByPOE = DB::table('users')
            ->join('points_of_entry', 'users.poeId', '=', 'points_of_entry.id')
            ->select(
                'points_of_entry.name as poeName',
                'points_of_entry.type as poeType',
                'users.role',
                DB::raw('COUNT(users.id) as total'),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.district')) as district"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.province')) as province")
            )
            ->whereIn('users.role', ['admin', 'screener', 'supervisor'])
            ->groupBy('points_of_entry.name', 'points_of_entry.type', 'users.role', 'district', 'province')
            ->get();

        // Get the geographical distribution of POEs by province
        $provinceDistribution = DB::table('points_of_entry')
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.province')) as province"), DB::raw('COUNT(*) as total'))
            ->groupBy('province')
            ->get();

        // Get the total number of users by district
        $districtDistribution = DB::table('users')
            ->join('points_of_entry', 'users.poeId', '=', 'points_of_entry.id')
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.district')) as district"), DB::raw('COUNT(users.id) as total'))
            ->groupBy('district')
            ->get();

        // Get the total number of users by province
        $totalUsersByProvince = DB::table('users')
            ->join('points_of_entry', 'users.poeId', '=', 'points_of_entry.id')
            ->select(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(location, '$.province')) as province"), DB::raw('COUNT(users.id) as total'))
            ->groupBy('province')
            ->get();

        // Prepare data for Chart.js and summary
        $data = [
            'Title' => 'Rwanda POE Screening Dashboard',
            'Desc' => 'Main Dashboard Landing Page',
            'poeDistribution' => $poeDistribution,
            'totalAirports' => $totalAirports,
            'totalLandBorders' => $totalLandBorders,
            'totalSeaports' => $totalSeaports,
            'provinceDistribution' => $provinceDistribution, // Make sure this line is correct
            'userRolesByPOE' => $userRolesByPOE, // Pass role distribution data to view
            'districtDistribution' => $districtDistribution, // Pass district user distribution data to view
            'totalUsersByProvince' => $totalUsersByProvince, // Pass province user distribution data to view for chart
            'Page' => 'maindash.PoeDashboard',
            'rw_dash' => 'rw_dash',
        ];

        return view('scrn', $data);
    }

}
