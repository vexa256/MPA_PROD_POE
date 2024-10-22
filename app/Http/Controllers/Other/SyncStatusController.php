<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SymptomTrendController extends Controller
{
    public function getSymptomTrendReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $symptomFrequency = $this->getSymptomFrequency($startDate, $endDate);
        $symptomTrends = $this->getSymptomTrends($startDate, $endDate);
        $emergingSymptoms = $this->getEmergingSymptoms($startDate, $endDate);

        return response()->json([
            'symptom_frequency' => $symptomFrequency,
            'symptom_trends' => $symptomTrends,
            'emerging_symptoms' => $emergingSymptoms,
        ]);
    }

    private function getSymptomFrequency($startDate, $endDate)
    {
        return DB::table('screenings')
            ->select(
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(symptoms, "$[*]")) as symptom'),
                DB::raw('COUNT(*) as frequency')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->whereRaw('JSON_LENGTH(symptoms) > 0')
            ->groupBy(DB::raw('JSON_UNQUOTE(JSON_EXTRACT(symptoms, "$[*]"))'))
            ->orderBy('frequency', 'desc')
            ->get();
    }

    private function getSymptomTrends($startDate, $endDate)
    {
        $intervalDays = 7; // Weekly trends
        $trends = DB::table('screenings')
            ->select(
                DB::raw('DATE(timestamp) as date'),
                DB::raw('JSON_UNQUOTE(JSON_EXTRACT(symptoms, "$[*]")) as symptom')
            )
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->whereRaw('JSON_LENGTH(symptoms) > 0')
            ->get();

        $groupedTrends = $trends->groupBy('symptom')->map(function ($symptomData) use ($intervalDays, $startDate, $endDate) {
            $startDateTime = Carbon::parse($startDate);
            $endDateTime = Carbon::parse($endDate);
            $intervals = [];

            while ($startDateTime->lte($endDateTime)) {
                $intervalEnd = (clone $startDateTime)->addDays($intervalDays - 1)->min($endDateTime);
                $count = $symptomData->whereBetween('date', [$startDateTime->toDateString(), $intervalEnd->toDateString()])->count();
                
                $intervals[] = [
                    'start_date' => $startDateTime->toDateString(),
                    'end_date' => $intervalEnd->toDateString(),
                    'count' => $count,
                ];

                $startDateTime->addDays($intervalDays);
            }

            return $intervals;
        });

        return $groupedTrends;
    }

    private function getEmergingSymptoms($startDate, $endDate)
    {
        $midpoint = Carbon::parse($startDate)->average(Carbon::parse($endDate));

        $firstHalf = $this->getSymptomFrequency($startDate, $midpoint->toDateString());
        $secondHalf = $this->getSymptomFrequency($midpoint->addDay()->toDateString(), $endDate);

        $emergingSymptoms = [];
        foreach ($secondHalf as $symptom) {
            $firstHalfFrequency = $firstHalf->firstWhere('symptom', $symptom->symptom)->frequency ?? 0;
            if ($symptom->frequency > $firstHalfFrequency * 1.5) {  // 50% increase threshold
                $emergingSymptoms[] = [
                    'symptom' => $symptom->symptom,
                    'first_half_frequency' => $firstHalfFrequency,
                    'second_half_frequency' => $symptom->frequency,
                    'increase_percentage' => $firstHalfFrequency > 0 ? 
                        round((($symptom->frequency - $firstHalfFrequency) / $firstHalfFrequency) * 100, 2) : 
                        100
                ];
            }
        }

        return $emergingSymptoms;
    }
}