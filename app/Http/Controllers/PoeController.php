<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PoeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $poes = DB::table('points_of_entry')->get();
        return response()->json($poes);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|in:airport,land_border,seaport',
            'location' => 'required|json',
            'status' => 'required|in:active,inactive,maintenance',
            'capacity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $id = DB::table('points_of_entry')->insertGetId([
                'name' => $request->name,
                'type' => $request->type,
                'location' => $request->location,
                'status' => $request->status,
                'capacity' => $request->capacity,
                'createdAt' => now(),
                'updatedAt' => now(),
            ]);

            $poe = DB::table('points_of_entry')->find($id);
            return response()->json($poe, 201);
        } catch (\Exception $e) {
            Log::error('Failed to create POE: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create POE'.$e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $poe = DB::table('points_of_entry')->find($id);

        if (!$poe) {
            return response()->json(['message' => 'POE not found'], 404);
        }

        return response()->json($poe);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:airport,land_border,seaport',
            'location' => 'sometimes|required|json',
            'status' => 'sometimes|required|in:active,inactive,maintenance',
            'capacity' => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $updated = DB::table('points_of_entry')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'type' => $request->type,
                    'location' => $request->location,
                    'status' => $request->status,
                    'capacity' => $request->capacity,
                    'updated_at' => now(),
                ]);

            if (!$updated) {
                return response()->json(['message' => 'POE not found'], 404);
            }

            $poe = DB::table('points_of_entry')->find($id);
            return response()->json($poe);
        } catch (\Exception $e) {
            Log::error('Failed to update POE: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update POE'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $deleted = DB::table('points_of_entry')->where('id', $id)->delete();

            if (!$deleted) {
                return response()->json(['message' => 'POE not found'], 404);
            }

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Failed to delete POE: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete POE'], 500);
        }
    }
}