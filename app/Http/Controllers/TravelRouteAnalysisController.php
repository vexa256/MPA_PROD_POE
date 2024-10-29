<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TravelRouteAnalysisController extends Controller
{
    public function TravelRouteAnalysis(Request $request)
    {
        // Optional filters for date range, POE, and disease
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $poeId = $request->input('poe_id');
        $disease = $request->input('disease');

        // Base query for retrieving suspected cases with transit and destination data
        $query = DB::table('secondary_screenings_data')
            ->select('transit_countries', 'travel_destination')
            ->whereNotNull('transit_countries')
            ->whereNotNull('travel_destination')
            ->where('classification', 'LIKE', '%Suspected%');

        // Apply optional filters
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }
        if ($poeId) {
            $query->where('poeid', $poeId);
        }
        if ($disease) {
            $query->whereJsonContains('suspected_diseases', [['disease' => $disease]]);
        }

        // Execute the query and get the results
        $results = $query->get();

        // Process transit country data to find most common transit countries
        $transitCountryCounts = [];
        foreach ($results as $result) {
            $transitCountries = json_decode($result->transit_countries, true);
            foreach ($transitCountries as $country) {
                if (isset($transitCountryCounts[$country])) {
                    $transitCountryCounts[$country]++;
                } else {
                    $transitCountryCounts[$country] = 1;
                }
            }
        }

        // Sort transit countries by count (most common first)
        arsort($transitCountryCounts);

        // Process destination data to find the most common destinations
        $destinationCounts = [];
        foreach ($results as $result) {
            $destination = $result->travel_destination;
            if (isset($destinationCounts[$destination])) {
                $destinationCounts[$destination]++;
            } else {
                $destinationCounts[$destination] = 1;
            }
        }

        // Retrieve Points of Entry for the filter dropdown
        $pointsOfEntry = DB::table('points_of_entry')->select('id', 'name')->get()->toArray();

        // Retrieve unique diseases for the disease dropdown filter
        $diseases = DB::table('secondary_screenings_data')
            ->select(DB::raw('DISTINCT JSON_UNQUOTE(JSON_EXTRACT(suspected_diseases, "$[0].disease")) AS disease'))
            ->whereNotNull('suspected_diseases')
            ->pluck('disease')
            ->filter()
            ->unique()
            ->toArray();

        // Sort destinations by count (most common first)
        arsort($destinationCounts);

        // Prepare data for the view
        $data = [
            'Title' => 'Travel Route Analysis for Suspected Cases',
            'Desc' => 'Tracks transit routes and destination patterns for travelers flagged with suspected diseases.',
            'Page' => 'poereports.Travel',
            'pointsOfEntry' => $pointsOfEntry,
            'transitCountryCounts' => $transitCountryCounts,
            'destinationCounts' => $destinationCounts,
            'diseases' => $diseases,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'poe_id' => $poeId,
                'disease' => $disease,
            ],
        ];

        return view('scrn', $data);
    }
}
