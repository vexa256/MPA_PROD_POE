<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Display a listing of the admin users.
     */
    public function index()
    {
        // Fetch only admin users
        $admins = User::where('role', 'admin')->get();

        $data = [
            'Title' => 'Admin Management',
            'Desc' => 'Manage system administrators | System Admins have access to this national dashboard',
            'Page' => 'UAC.MgtUsers',
            'admins' => $admins,
        ];

        return view('scrn', $data);
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        $data = [
            'Title' => 'Create New Admin',
            'Desc' => 'Add a new administrator to the system',
            'Page' => 'adminManagement.create',
        ];

        return view('scrn', $data);
    }

    /**
     * Store a newly created admin in the database.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users,username|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create new admin user
        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'passwordHash' => Hash::make($request->password),
            'role' => 'admin',
            'name' => $request->name,
        ]);

        return redirect()->route('admins.index')->with('success', 'Admin created successfully.');
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $data = [
            'Title' => 'Edit Admin',
            'Desc' => 'Modify administrator details',
            'Page' => 'adminManagement.edit',
            'admin' => $admin,
        ];

        return view('scrn', $data);
    }

    /**
     * Update the specified admin in the database.
     */
    public function update(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username,' . $admin->id,
            'email' => 'required|email|max:255|unique:users,email,' . $admin->id,
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $admin->username = $request->username;
        $admin->email = $request->email;
        $admin->name = $request->name;

        if ($request->filled('password')) {
            $admin->passwordHash = Hash::make($request->password);
        }

        $admin->save();

        return redirect()->route('admins.index')->with('success', 'Admin updated successfully.');
    }

    /**
     * Remove the specified admin from the database.
     */
    public function DeleteAdmin($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        // Prevent deleting the last remaining admin
        if (User::where('role', 'admin')->count() <= 1) {
            return redirect()->back()->with(['error_a' => 'Cannot delete the last admin user.']);
        }

        $admin->delete();

        return redirect()->route('index')->with('status', 'Admin deleted successfully.');
    }
}
