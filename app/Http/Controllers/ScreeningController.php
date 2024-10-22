<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ScreeningController extends Controller
{

    public function storedata(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'screeningId' => 'required|string|max:20|unique:ScreeningData,screening_id',
            'travellerInfo' => 'required|json',
            'screeningDetails' => 'required|json',
            'symptoms' => 'required|json',
            'riskFactors' => 'required|json',
            'suspectedDiseases' => 'required|json',
            'metadata' => 'required|json',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Parse JSON data
        $travellerInfo = json_decode($request->travellerInfo, true);
        $screeningDetails = json_decode($request->screeningDetails, true);
        $metadata = json_decode($request->metadata, true);

        try {
            // Insert data into the ScreeningData table
            $screeningId = DB::table('ScreeningData')->insertGetId([
                'screening_id' => $request->screeningId,
                'screening_timestamp' => Carbon::now(),

                // POE Information
                'poe_id' => $metadata['poeId'],
                'poe_name' => $metadata['poe'],
                'poe_type' => $metadata['poeType'],
                'poe_country' => 'Unknown', // Add logic to determine country based on POE
                'poe_province' => $metadata['province'],
                'poe_district' => $metadata['district'],
                'poe_status' => $metadata['poeStatus'],
                'poe_capacity' => $metadata['poeCapacity'],

                // Screener Information
                'screener_id' => $metadata['userId'],
                'screener_username' => $metadata['screeningOfficer'],
                'screener_email' => 'unknown@example.com', // Add logic to fetch email
                'screener_role' => $metadata['userRole'],

                // Traveler Information
                'traveller_name' => $travellerInfo['travellerName'],
                'traveller_age_group' => $travellerInfo['travellerAgeGroup'],
                'traveller_gender' => $travellerInfo['travellerGender'],
                'traveller_contact_info' => $travellerInfo['contactInfo'],
                'traveller_nationality' => 'Unknown', // Add logic to determine nationality

                // Travel Information
                'origin_country' => $screeningDetails['countryOfOrigin'],
                'destination_country' => $screeningDetails['travelDestination'],
                'recent_travel_history' => $screeningDetails['recentTravelHistory'],

                // Screening Results
                'has_symptoms' => $screeningDetails['hasSymptoms'],
                'symptoms' => $request->symptoms,
                'risk_factors' => $request->riskFactors,

                // Disease Analysis
                'suspected_diseases' => $request->suspectedDiseases,
                'classification' => $screeningDetails['classification'],
                'accuracy_probability' => $screeningDetails['accuracyProbability'],

                // Actions and Alerts
                'recommended_action' => $screeningDetails['recommendedAction'],
                'endemic_warning' => $screeningDetails['endemicWarning'],
                'high_risk_alert' => $screeningDetails['highRiskAlert'],

                // Additional Data
                'body_temperature' => null, // Add logic if body temperature is collected
                'additional_notes' => null, // Add logic if additional notes are collected

                // Metadata
                'data_version' => '1.0', // Adjust as needed
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            return response()->json(['message' => 'Screening data saved successfully', 'id' => $screeningId], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save screening data: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'screeningId' => 'required|string|unique:screenings,screeningId',
                'travellerInfo' => 'required|json',
                'screeningDetails' => 'required|json',
                'symptoms' => 'required|json',
                'riskFactors' => 'required|json',
                'suspectedDiseases' => 'required|json',
                'timestamp' => 'required|date',
                'metadata' => 'required|json',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $screeningId = DB::table('screenings')->insertGetId([
                'screeningId' => $request->screeningId,
                'travellerInfo' => $request->travellerInfo,
                'screeningDetails' => $request->screeningDetails,
                'symptoms' => $request->symptoms,
                'riskFactors' => $request->riskFactors,
                'suspectedDiseases' => $request->suspectedDiseases,
                'timestamp' => Carbon::parse($request->timestamp)->format('Y-m-d H:i:s'),
                'metadata' => $request->metadata,
                'createdAt' => now(),
                'updatedAt' => now(),
            ]);

            return response()->json(['message' => 'Screening saved successfully', 'id' => $screeningId], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to save screening', 'message' => $e->getMessage()], 500);
        }
    }

    public function index()
    {
        try {
            $screenings = DB::table('screenings')->get();
            return response()->json($screenings);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve screenings', 'message' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $screening = DB::table('screenings')->where('id', $id)->first();
            if (!$screening) {
                return response()->json(['error' => 'Screening not found'], 404);
            }
            return response()->json($screening);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve screening', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'travellerInfo' => 'sometimes|required|json',
                'screeningDetails' => 'sometimes|required|json',
                'symptoms' => 'sometimes|required|json',
                'riskFactors' => 'sometimes|required|json',
                'suspectedDiseases' => 'sometimes|required|json',
                'timestamp' => 'sometimes|required|date',
                'metadata' => 'sometimes|required|json',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $updated = DB::table('screenings')
                ->where('id', $id)
                ->update(array_merge($request->only([
                    'travellerInfo',
                    'screeningDetails',
                    'symptoms',
                    'riskFactors',
                    'suspectedDiseases',
                    'metadata',
                ]), [
                    'timestamp' => $request->has('timestamp') ? Carbon::parse($request->timestamp)->format('Y-m-d H:i:s') : null,
                    'updatedAt' => now(),
                ]));

            if (!$updated) {
                return response()->json(['error' => 'Screening not found'], 404);
            }

            return response()->json(['message' => 'Screening updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update screening', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $deleted = DB::table('screenings')->where('id', $id)->delete();
            if (!$deleted) {
                return response()->json(['error' => 'Screening not found'], 404);
            }
            return response()->json(['message' => 'Screening deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete screening', 'message' => $e->getMessage()], 500);
        }
    }

    public function getByPOE($poe)
    {
        try {
            $screenings = DB::table('screenings')
                ->whereRaw("JSON_EXTRACT(metadata, '$.poe') = ?", [$poe])
                ->get();
            return response()->json($screenings);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve screenings by POE', 'message' => $e->getMessage()], 500);
        }
    }

    public function getByDateRange(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $screenings = DB::table('screenings')
                ->whereBetween('timestamp', [
                    Carbon::parse($request->start_date)->startOfDay(),
                    Carbon::parse($request->end_date)->endOfDay(),
                ])
                ->get();
            return response()->json($screenings);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve screenings by date range', 'message' => $e->getMessage()], 500);
        }
    }
}
