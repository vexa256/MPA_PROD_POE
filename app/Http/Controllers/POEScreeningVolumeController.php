<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POEScreeningVolumeController extends Controller
{
    public function monthlyScreeningVolumeByPOE(Request $request)
    {
        // Set the selected year to current year if none provided
        $selectedYear = $request->input('year', date('Y'));
        $selectedPOEId = $request->input('poeid');

        // Primary query for ScreeningData with year and POE filtering applied before the union
        $screeningDataQuery = DB::table('ScreeningData')
            ->select(
                DB::raw('MONTH(screening_timestamp) as month'),
                DB::raw('COUNT(*) as total_screenings'),
                'poe_id'
            )
            ->where('classification', 'Non-Case')
            ->whereYear('screening_timestamp', $selectedYear);

        // Apply POE filter if selected
        if ($selectedPOEId) {
            $screeningDataQuery->where('poe_id', $selectedPOEId);
        }

        // Secondary query for secondary_screenings_data with year and POE filtering applied before the union
        $secondaryScreeningDataQuery = DB::table('secondary_screenings_data')
            ->select(
                DB::raw('MONTH(arrival_date) as month'),
                DB::raw('COUNT(*) as total_screenings'),
                'poeid as poe_id'
            )
            ->whereYear('arrival_date', $selectedYear);

        // Apply POE filter if selected
        if ($selectedPOEId) {
            $secondaryScreeningDataQuery->where('poeid', $selectedPOEId);
        }

        // Union the primary and secondary queries with both year and POE filters applied in each
        $combinedScreeningData = DB::table(DB::raw("({$screeningDataQuery->unionAll($secondaryScreeningDataQuery)->toSql()}) as combined"))
            ->mergeBindings($screeningDataQuery)
            ->select(
                'month',
                'poe_id',
                DB::raw('SUM(total_screenings) as total_screenings')
            )
            ->groupBy('month', 'poe_id')
            ->orderBy('month');

        // Execute the query to get the results
        $screeningData = $combinedScreeningData->get();

        // Fetch Points of Entry for the POE dropdown in the view
        $pointsOfEntry = DB::table('points_of_entry')
            ->select('id', 'name')
            ->get();

        // Prepare data for the view
        $data = [
            'Title' => 'Monthly Screening Volume by POE',
            'Desc' => 'Aggregated POE screening volume  by month, with applied filters.',
            'Page' => 'poereports.MonthlyScreeningVolume',
            'MonthlyScreeningVolumeKey' => 'true',
            'screeningData' => $screeningData,
            'pointsOfEntry' => $pointsOfEntry,
            'selectedYear' => $selectedYear,
            'selectedPOEId' => $selectedPOEId,
        ];

        return view('scrn', $data);
    }
}
