<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $users = DB::table('users')
                ->leftJoin('points_of_entry', 'users.poeId', '=', 'points_of_entry.id')
                ->select('users.*', 'points_of_entry.name as poeName')
                ->get();

            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Failed to fetch users: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch users' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,screener,supervisor,province,district,national',
            'poeId' => 'nullable|exists:points_of_entry,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $userId = DB::table('users')->insertGetId([
                'username' => $request->username,
                'email' => $request->email,
                'passwordHash' => Hash::make($request->password),
                'role' => $request->role,
                'poeId' => $request->poeId,
                'createdAt' => now(),
                'updatedAt' => now(),
            ]);

            $user = DB::table('users')->find($userId);
            return response()->json($user, 201);
        } catch (\Exception $e) {
            Log::error('Failed to create user: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create user' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $user = DB::table('users')
                ->leftJoin('points_of_entry', 'users.poeId', '=', 'points_of_entry.id')
                ->select('users.*', 'points_of_entry.name as poeName')
                ->where('users.id', $id)
                ->first();

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json($user);
        } catch (\Exception $e) {
            Log::error('Failed to fetch user: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch user' . $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $id,
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'role' => 'sometimes|required|in:admin,screener,supervisor,province,district,national',
            'poeId' => 'nullable|exists:points_of_entry,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $updateData = [
                'username' => $request->username,
                'email' => $request->email,
                'role' => $request->role,
                'poeId' => $request->poeId,
                'updatedAt' => now(),
            ];

            if ($request->has('password')) {
                $updateData['passwordHash'] = Hash::make($request->password);
            }

            $updated = DB::table('users')
                ->where('id', $id)
                ->update($updateData);

            if (!$updated) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $user = DB::table('users')->find($id);
            return response()->json($user);
        } catch (\Exception $e) {
            Log::error('Failed to update user: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update user' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $deleted = DB::table('users')->where('id', $id)->delete();

            if (!$deleted) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete user'], 500);
        }
    }

    /**
     * Update the last login timestamp for a user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateLastLogin($id)
    {
        try {
            $updated = DB::table('users')
                ->where('id', $id)
                ->update(['lastLogin' => now()]);

            if (!$updated) {
                return response()->json(['message' => 'User not found'], 404);
            }

            return response()->json(['message' => 'Last login updated successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to update last login: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update last login'], 500);
        }
    }

    /**
     * Get users associated with a specific POE.
     *
     * @param  int  $poeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersByPOE($poeId)
    {
        try {
            $users = DB::table('users')
                ->where('poeId', $poeId)
                ->get();

            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Failed to fetch users by POE: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch users by POE'], 500);
        }
    }

    public function appLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $user = DB::table('users')
                ->where('username', $request->username)
                ->first();

            if (!$user || !Hash::check($request->password, $user->passwordHash)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }

            // Fetch user details with associated POE information
            $userDetails = DB::table('users')
                ->leftJoin('points_of_entry', 'users.poeId', '=', 'points_of_entry.id')
                ->select('users.*', 'points_of_entry.*', 'points_of_entry.name AS POE*')
                ->where('users.id', $user->id)
                ->first();

            // Update last login timestamp
            DB::table('users')
                ->where('id', $user->id)
                ->update(['lastLogin' => now()]);

            // Prepare the response
            $response = [
                'user' => $userDetails,
                'message' => 'Login successful. Welcome, ' . $user->username . '!',
            ];

            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Login failed: ' . $e->getMessage());
            return response()->json(['message' => 'Login failed. Please try again.'], 500);
        }
    }

}
