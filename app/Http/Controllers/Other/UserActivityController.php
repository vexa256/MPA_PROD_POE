<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserActivityController extends Controller
{
    public function getUserActivityReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $userRoleAnalysis = $this->getUserRoleAnalysis($startDate, $endDate);
        $screeningVolumesByUser = $this->getScreeningVolumesByUser($startDate, $endDate);
        $systemAccessPatterns = $this->getSystemAccessPatterns($startDate, $endDate);

        return response()->json([
            'user_role_analysis' => $userRoleAnalysis,
            'screening_volumes_by_user' => $screeningVolumesByUser,
            'system_access_patterns' => $systemAccessPatterns,
        ]);
    }

    private function getUserRoleAnalysis($startDate, $endDate)
    {
        return DB::table('users')
            ->select('role', DB::raw('COUNT(*) as user_count'))
            ->whereDate('createdAt', '<=', $endDate)
            ->groupBy('role')
            ->get();
    }

    private function getScreeningVolumesByUser($startDate, $endDate)
    {
        return DB::table('screenings')
            ->join('users', 'screenings.screeningOfficer', '=', 'users.username')
            ->select(
                'users.username',
                'users.role',
                DB::raw('COUNT(*) as screening_count'),
                DB::raw('SUM(CASE WHEN screenings.classification = "suspected-case" THEN 1 ELSE 0 END) as suspected_cases')
            )
            ->whereBetween('screenings.timestamp', [$startDate, $endDate])
            ->groupBy('users.username', 'users.role')
            ->orderBy('screening_count', 'desc')
            ->get();
    }

    private function getSystemAccessPatterns($startDate, $endDate)
    {
        $loginPatterns = DB::table('audit_logs')
            ->select(
                DB::raw('DATE(actionTimestamp) as date'),
                DB::raw('HOUR(actionTimestamp) as hour'),
                DB::raw('COUNT(*) as login_count')
            )
            ->where('action', 'login')
            ->whereBetween('actionTimestamp', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(actionTimestamp)'), DB::raw('HOUR(actionTimestamp)'))
            ->orderBy('date')
            ->orderBy('hour')
            ->get();

        $potentialSecurityConcerns = DB::table('audit_logs')
            ->select(
                'performedBy',
                'action',
                'actionTimestamp',
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(newValue, "$.ip_address")) as ip_address')
            )
            ->where(function ($query) {
                $query->where('action', 'failed_login')
                    ->orWhere('action', 'password_reset')
                    ->orWhere('action', 'role_change');
            })
            ->whereBetween('actionTimestamp', [$startDate, $endDate])
            ->orderBy('actionTimestamp', 'desc')
            ->limit(100)
            ->get();

        return [
            'login_patterns' => $loginPatterns,
            'potential_security_concerns' => $potentialSecurityConcerns
        ];
    }
}