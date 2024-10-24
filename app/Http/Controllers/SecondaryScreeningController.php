<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SecondaryScreeningController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'screening_id' => 'required|string|max:20',
                'traveller_name' => 'required|string|max:100',
                'symptoms' => 'required|string|max:255',
                'status' => 'required|string|in:pending,completed',
                'referred_by_id' => 'required|integer',
                'referred_by_username' => 'required|string|max:50',
                'referred_by_email' => 'required|email|max:100',
                'referred_by_role' => 'required|string|max:20',
                'poe_id' => 'required|integer',
                'poe_name' => 'required|string|max:100',
                'poe_type' => 'required|string|max:20',
                'poe_country' => 'required|string|max:100',
                'poe_district' => 'required|string|max:100',
                'poe_province' => 'required|string|max:100',
                'poe_status' => 'required|string',
                'poe_capacity' => 'required|integer',
                'screened_by_id' => 'required|integer',
                'screened_by_username' => 'required|string|max:50',
                'screened_by_email' => 'required|email|max:100',
                'screened_by_role' => 'required|string|max:20',

                'created_at' => 'required|date',
                'updated_at' => 'required|date',

            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // Get validated data
            $data = $validator->validated();

            // Insert the referral into the database
            $id = DB::table('secondary_screenings')->insertGetId($data);

            // Fetch the inserted record
            $referral = DB::table('secondary_screenings')->where('id', $id)->first();

            // Return a success response
            return response()->json([
                'message' => 'Secondary screening referral created successfully',
                'data' => $referral,
            ], 201);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in SecondaryScreeningController@store: ' . $e->getMessage());

            // Return an error response
            return response()->json([
                'message' => 'An error occurred while creating the secondary screening referral',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getReferrals(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'poeId' => 'required|integer',
                'page' => 'integer|min:1',
                'limit' => 'integer|min:1|max:100',
                'sort' => 'string|in:created_at,traveller_name,status',
                'order' => 'string|in:asc,desc',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid parameters provided',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $poeId = $request->input('poeId');
            $page = $request->input('page', 1);
            $limit = $request->input('limit', 20);
            $sort = $request->input('sort', 'created_at');
            $order = $request->input('order', 'desc');

            $query = DB::table('secondary_screenings')
                ->where('poe_id', $poeId)
                ->where('status', '!=', 'cancelled')
                ->where('status', 'pending')
                ->orderBy($sort, $order);

            $total = $query->count();
            $referrals = $query->forPage($page, $limit)->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Referrals retrieved successfully',
                'data' => $referrals,
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'last_page' => ceil($total / $limit),
                ],
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Error in SecondaryScreeningController@getReferrals: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching referrals',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function cancelReferral(Request $request)
    {
        try {
            // Validate the incoming request data
            $validator = Validator::make($request->all(), [
                'referralId' => 'required|integer|exists:secondary_screenings,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid referral ID provided',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $referralId = $request->input('referralId');

            // Check if the referral is in a state that can be cancelled
            $referral = DB::table('secondary_screenings')
                ->where('id', $referralId)
                ->first();

            if (!$referral) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Referral not found',
                ], 404);
            }

            if ($referral->status !== 'pending') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Only pending referrals can be cancelled',
                ], 400);
            }

            // Update the referral status to 'cancelled'
            $updated = DB::table('secondary_screenings')
                ->where('id', $referralId)
                ->update([
                    'status' => 'cancelled',
                    'updated_at' => now(),
                ]);

            if ($updated) {
                // Fetch the updated referral
                $updatedReferral = DB::table('secondary_screenings')
                    ->where('id', $referralId)
                    ->first();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Referral cancelled successfully',
                    'data' => $updatedReferral,
                ], 200);
            } else {
                throw new \Exception('Failed to update referral status');
            }
        } catch (\Exception $e) {
            \Log::error('Error in SecondaryScreeningController@cancelReferral: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while cancelling the referral',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function recordNewScreening(Request $request)
    {
        $validator = Validator::make($request->all(), rules: [
            'screening_id' => 'required|string|max:20|unique:secondary_screenings_data',
            'traveller_name' => 'required|string|max:100',
            'age' => 'nullable|integer',
            'gender' => 'required|in:Male,Female,Other',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'departure_country' => 'required|string|max:100',
            'travel_destination' => 'required|string|max:100',
            'arrival_date' => 'required|date',
            'transit_countries' => 'nullable|json',
            'poe_name' => 'required|string|max:100',
            'poe_type' => 'required|string|max:20',
            'poe_district' => 'required|string|max:100',
            'poe_province' => 'required|string|max:100',
            'poeid' => 'required|string|max:50',
            'screener_id' => 'required|string|max:50',
            'screener_username' => 'required|string|max:50',
            'symptoms' => 'required|json',
            'travel_exposures' => 'required|json',
            'classification' => 'required|string|max:50',
            'confidence_level' => 'nullable|string|max:20',
            'recommended_action' => 'required|string',
            'suspected_diseases' => 'nullable|json',
            'endemic_warning' => 'nullable|string|max:255',
            'high_risk_alert' => 'required|boolean',
            'referral_status' => 'required|in:Not Referred,Referred',
            'referral_province' => 'nullable|string|max:100',
            'referral_district' => 'nullable|string|max:100',
            'referral_hospital' => 'nullable|string|max:100',
            'sync_status' => 'required|in:Pending,Synchronized',
            'data_version' => 'required|string|max:10',
            'additional_notes' => 'nullable|string|max:65535',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $screening = DB::table('secondary_screenings_data')->insert($validator->validated());

            DB::commit();

            $updated = DB::table('secondary_screenings')
                ->where('screening_id', $request->screening_id)
                ->update([
                    'status' => 'completed',
                    // 'updated_at' => now(),
                ]);

            return response()->json(['message' => 'Screening data saved successfully',
                'data' => $screening], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving screening data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while saving the screening data'], 500);
        }
    }

    public function fetchAllScreenings(Request $request)
    {
        try {
            $poeid = $request->input('poeid');

            if (!$poeid) {
                return response()->json(['error' => 'POEID is required'], 400);
            }

            $screenings = DB::table('secondary_screenings_data')
                ->where('poeid', $poeid)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json($screenings);
        } catch (\Exception $e) {
            \Log::error('Error fetching screenings: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching screenings'], 500);
        }
    }

    public function retrieveScreeningDetails(Request $request, $id)
    {
        try {
            $poeid = $request->input('poeid');

            if (!$poeid) {
                return response()->json(['error' => 'POEID is required'], 400);
            }

            $screening = DB::table('secondary_screenings_data')
                ->where('id', $id)
                ->where('poeid', $poeid)
                ->first();

            if (!$screening) {
                return response()->json(['error' => 'Screening not found'], 404);
            }

            return response()->json($screening);
        } catch (\Exception $e) {
            \Log::error('Error fetching screening: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while fetching the screening'], 500);
        }
    }

    public function modifyExistingScreening(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'traveller_name' => 'sometimes|required|string|max:100',
            'age' => 'nullable|integer',
            'gender' => 'sometimes|required|in:Male,Female,Other',
            'address' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:50',
            'emergency_contact_name' => 'nullable|string|max:100',
            'emergency_contact_phone' => 'nullable|string|max:50',
            'departure_country' => 'sometimes|required|string|max:100',
            'travel_destination' => 'sometimes|required|string|max:100',
            'arrival_date' => 'sometimes|required|date',
            'transit_countries' => 'nullable|json',
            'poe_name' => 'sometimes|required|string|max:100',
            'poe_type' => 'sometimes|required|string|max:20',
            'poe_district' => 'sometimes|required|string|max:100',
            'poe_province' => 'sometimes|required|string|max:100',
            'poeid' => 'required|string|max:50',
            'symptoms' => 'sometimes|required|json',
            'travel_exposures' => 'sometimes|required|json',
            'classification' => 'sometimes|required|string|max:50',
            'confidence_level' => 'nullable|string|max:20',
            'recommended_action' => 'sometimes|required|string',
            'suspected_diseases' => 'nullable|json',
            'endemic_warning' => 'nullable|string|max:255',
            'high_risk_alert' => 'sometimes|required|boolean',
            'referral_status' => 'sometimes|required|in:Not Referred,Referred',
            'referral_province' => 'nullable|string|max:100',
            'referral_district' => 'nullable|string|max:100',
            'referral_hospital' => 'nullable|string|max:100',
            'sync_status' => 'sometimes|required|in:Pending,Synchronized',
            'additional_notes' => 'nullable|string|max:65535',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $affected = DB::table('secondary_screenings_data')
                ->where('id', $id)
                ->where('poeid', $request->input('poeid'))
                ->update($validator->validated());

            if ($affected === 0) {
                DB::rollBack();
                return response()->json(['error' => 'Screening not found or no changes made'], 404);
            }

            DB::commit();

            $updatedScreening = DB::table('secondary_screenings_data')
                ->where('id', $id)
                ->first();

            return response()->json(['message' => 'Screening data updated successfully', 'data' => $updatedScreening]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating screening data: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while updating the screening data'], 500);
        }
    }

    public function removeScreeningRecord(Request $request, $id)
    {
        try {
            $poeid = $request->input('poeid');

            if (!$poeid) {
                return response()->json(['error' => 'POEID is required'], 400);
            }

            $affected = DB::table('secondary_screenings_data')
                ->where('id', $id)
                ->where('poeid', $poeid)
                ->delete();

            if ($affected === 0) {
                return response()->json(['error' => 'Screening not found'], 404);
            }

            return response()->json(['message' => 'Screening deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Error deleting screening: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while deleting the screening'], 500);
        }
    }

}