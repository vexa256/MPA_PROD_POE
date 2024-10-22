<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScreeningDataQualityController extends Controller
{
    public function getDataQualityReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        $totalScreenings = $this->getTotalScreenings($startDate, $endDate);
        $incompleteScreenings = $this->getIncompleteScreenings($startDate, $endDate);
        $dataCompleteness = $this->getDataCompleteness($startDate, $endDate);
        $dataEntryPatterns = $this->getDataEntryPatterns($startDate, $endDate);
        $dataConsistency = $this->getDataConsistency($startDate, $endDate);

        return response()->json([
            'total_screenings' => $totalScreenings,
            'incomplete_screenings' => $incompleteScreenings,
            'data_completeness' => $dataCompleteness,
            'data_entry_patterns' => $dataEntryPatterns,
            'data_consistency' => $dataConsistency,
        ]);
    }

    private function getTotalScreenings($startDate, $endDate)
    {
        return DB::table('screenings')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->count();
    }

    private function getIncompleteScreenings($startDate, $endDate)
    {
        return DB::table('screenings')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->where(function ($query) {
                $query->whereNull('travellerName')
                    ->orWhereNull('travellerAgeGroup')
                    ->orWhereNull('travellerGender')
                    ->orWhereNull('contactInfo')
                    ->orWhereNull('travelDestination')
                    ->orWhereNull('countryOfOrigin')
                    ->orWhereNull('hasSymptoms')
                    ->orWhereNull('classification')
                    ->orWhereNull('poe')
                    ->orWhereNull('province')
                    ->orWhereNull('district');
            })
            ->count();
    }

    private function getDataCompleteness($startDate, $endDate)
    {
        $fields = [
            'travellerName', 'travellerAgeGroup', 'travellerGender', 'contactInfo',
            'travelDestination', 'countryOfOrigin', 'hasSymptoms', 'classification',
            'poe', 'province', 'district'
        ];

        $completeness = [];

        foreach ($fields as $field) {
            $completeness[$field] = DB::table('screenings')
                ->whereBetween('timestamp', [$startDate, $endDate])
                ->whereNotNull($field)
                ->count();
        }

        return $completeness;
    }

    private function getDataEntryPatterns($startDate, $endDate)
    {
        return DB::table('screenings')
            ->select(DB::raw('HOUR(timestamp) as hour'), DB::raw('COUNT(*) as count'))
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->groupBy(DB::raw('HOUR(timestamp)'))
            ->orderBy(DB::raw('HOUR(timestamp)'))
            ->get();
    }

    private function getDataConsistency($startDate, $endDate)
    {
        $inconsistencies = [
            'age_gender_mismatch' => $this->getAgeGenderMismatch($startDate, $endDate),
            'symptoms_classification_mismatch' => $this->getSymptomsClassificationMismatch($startDate, $endDate),
            'invalid_dates' => $this->getInvalidDates($startDate, $endDate),
        ];

        return $inconsistencies;
    }

    private function getAgeGenderMismatch($startDate, $endDate)
    {
        return DB::table('screenings')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->whereRaw("(travellerAgeGroup = 'infant' AND travellerGender NOT IN ('male', 'female')) OR
                        (travellerAgeGroup = 'child' AND travellerGender NOT IN ('male', 'female'))")
            ->count();
    }

    private function getSymptomsClassificationMismatch($startDate, $endDate)
    {
        return DB::table('screenings')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->where(function ($query) {
                $query->where('hasSymptoms', true)
                    ->where('classification', 'non-case');
            })
            ->orWhere(function ($query) {
                $query->where('hasSymptoms', false)
                    ->where('classification', 'suspected-case');
            })
            ->count();
    }

    private function getInvalidDates($startDate, $endDate)
    {
        return DB::table('screenings')
            ->whereBetween('timestamp', [$startDate, $endDate])
            ->where('timestamp', '>', Carbon::now())
            ->count();
    }
}