<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassificationsController extends Controller
{
    public function SelectClassification()
    {
        // Fetch unique classifications from the database for the dropdown
        $classifications = DB::table('secondary_screenings_data')
            ->distinct()
            ->pluck('classification');

        $data = [
            'Title' => 'Select Classification for Alerts',
            'Desc' => 'Select a classification',
            'Page' => 'poereports.SelectClassification',
            'classifications' => $classifications,
        ];

        return view('scrn', $data);
    }

    public function GetClassificationData(Request $request)
    {
        $classification = $request->input('classification');

        // Check if classification is supplied
        if (!$classification) {
            return redirect()->back()->withErrors(['error_a' => 'Please select a classification.']);
        }

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $poeName = $request->input('poe_name');
        $province = $request->input('province');
        $district = $request->input('district');
        $month = $request->input('month');

        $query = DB::table('secondary_screenings_data')
            ->join('points_of_entry', 'secondary_screenings_data.poeid', '=', 'points_of_entry.id')
            ->select(
                'secondary_screenings_data.id',
                'secondary_screenings_data.traveller_name',
                'secondary_screenings_data.age',
                'secondary_screenings_data.gender',
                'secondary_screenings_data.address',
                'secondary_screenings_data.phone_number',
                'secondary_screenings_data.id_number',
                'secondary_screenings_data.emergency_contact_name',
                'secondary_screenings_data.emergency_contact_phone',
                'secondary_screenings_data.departure_country',
                'secondary_screenings_data.travel_destination',
                'secondary_screenings_data.arrival_date',
                'secondary_screenings_data.transit_countries',
                'secondary_screenings_data.poe_name',
                'secondary_screenings_data.poe_type',
                'secondary_screenings_data.poe_district',
                'secondary_screenings_data.poe_province',
                'secondary_screenings_data.poeid',
                'secondary_screenings_data.screener_id',
                'secondary_screenings_data.screener_username',
                'secondary_screenings_data.symptoms',
                'secondary_screenings_data.travel_exposures',
                'secondary_screenings_data.classification',
                'secondary_screenings_data.confidence_level',
                'secondary_screenings_data.recommended_action',
                'secondary_screenings_data.suspected_diseases',
                'secondary_screenings_data.endemic_warning',
                'secondary_screenings_data.high_risk_alert',
                'secondary_screenings_data.referral_status',
                'secondary_screenings_data.referral_province',
                'secondary_screenings_data.referral_district',
                'secondary_screenings_data.referral_hospital',
                'secondary_screenings_data.sync_status',
                'secondary_screenings_data.data_version',
                'secondary_screenings_data.additional_notes',
                'points_of_entry.name as point_of_entry_name',
                'points_of_entry.type as point_of_entry_type',
                'points_of_entry.location as point_of_entry_location',
                'points_of_entry.status as point_of_entry_status',
                'points_of_entry.capacity as point_of_entry_capacity'
            )
            ->where('secondary_screenings_data.classification', '=', $classification);

        // Apply filters if provided
        if ($startDate && $endDate) {
            $query->whereBetween('arrival_date', [$startDate, $endDate]);
        }

        if ($poeName) {
            $query->where('poe_name', $poeName);
        }

        if ($province) {
            $query->where('poe_province', $province);
        }

        if ($district) {
            $query->where('poe_district', $district);
        }

        if ($month) {
            $query->whereMonth('arrival_date', $month);
        }

        $suspectedCases = $query->get();

        // If no results found, redirect back with error
        if ($suspectedCases->isEmpty()) {
            return redirect()->back()->withErrors(['error_a' => 'No cases found for the selected classification.']);
        }

        $data = [
            'Title' => 'Detailed Classification Report',
            'Desc' => 'Report of cases for the selected classification: ' . $classification,
            'Page' => 'poereports.ClassificationReport',
            'selectedClassification' => $classification,
            'classification' => $classification,
            'suspectedCases' => $suspectedCases,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'poe_name' => $poeName,
                'province' => $province,
                'district' => $district,
                'month' => $month,
            ],
        ];

        return view('scrn', $data);
    }
}
