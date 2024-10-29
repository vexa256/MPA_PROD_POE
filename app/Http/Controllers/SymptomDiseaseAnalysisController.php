<?php
namespace App\Http\Controllers;

// namespace App\Http\Controllers;

use Carbon\Carbon;
// use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SymptomDiseaseAnalysisController extends Controller
{

    public function SymptomDiseaseAnalysis(Request $request)
    {
        // Capture filters or set default values
        $selectedYear = $request->input('year', Carbon::now()->year);
        $selectedMonth = $request->input('month', null);
        $selectedPOEId = $request->input('poeid', null);

        // Get list of Points of Entry
        $pointsOfEntry = DB::table('points_of_entry')->select('id', 'name')->get();

        // Main query with calculated disease and symptom fields
        $rawData = DB::table('secondary_screenings_data')
            ->join('points_of_entry', 'secondary_screenings_data.poeid', '=', 'points_of_entry.id')
            ->select(
                'points_of_entry.name as poe_name',
                'points_of_entry.id as poeid',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(suspected_diseases, '$[0].disease')) as disease"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(symptoms, '$[0]')) as symptom"),
                DB::raw('COUNT(*) as symptom_count')
            )
            ->when($selectedYear, function ($query) use ($selectedYear) {
                return $query->whereYear('secondary_screenings_data.created_at', $selectedYear);
            })
            ->when($selectedMonth, function ($query) use ($selectedMonth) {
                return $query->whereMonth('secondary_screenings_data.created_at', $selectedMonth);
            })
            ->when($selectedPOEId, function ($query) use ($selectedPOEId) {
                return $query->where('secondary_screenings_data.poeid', $selectedPOEId);
            })
            ->groupBy('poeid', 'disease', 'symptom')
            ->orderBy('symptom_count', 'desc')
            ->havingRaw("disease IS NOT NULL AND symptom IS NOT NULL")
            ->get();

        // Restructure data for chart compatibility
        $stackedBarData = [];
        foreach ($rawData as $record) {
            $poeName = $record->poe_name;
            $disease = $record->disease;
            $symptom = $record->symptom;

            if ($disease && $symptom) { // Double-checking for non-null values
                // Initialize POE and disease in the array if not already present
                if (!isset($stackedBarData[$poeName])) {
                    $stackedBarData[$poeName] = [];
                }
                if (!isset($stackedBarData[$poeName][$disease])) {
                    $stackedBarData[$poeName][$disease] = [];
                }

                // Add symptom and count
                $stackedBarData[$poeName][$disease][$symptom] = $record->symptom_count;
            }
        }

        // Prepare data for the view
        $data = [
            'Title' => 'Common Reported Symptoms by Disease and POE',
            'Desc' => 'Symptom analysis across Points of Entry by suspected diseases',
            'Page' => 'poereports.SymptomDiseaseAnalysis',
            'StackedBarData' => $stackedBarData,
            'pointsOfEntry' => $pointsOfEntry,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'selectedPOEId' => $selectedPOEId,
        ];

        return view('scrn', $data);
    }

}
