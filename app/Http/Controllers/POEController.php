<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POEController extends Controller
{
    // Display a listing of the POEs
    public function MgtPoes()
    {
        $poes = DB::table('points_of_entry')->get();

        $data = [
            'Title' => 'POE List',
            'Desc' => 'Listing of all Points of Entry',
            'Page' => 'POE.MgtPoes',
            'poes' => $poes,
            'filters' => [], // Filters could be added if needed
        ];

        return view('scrn', $data);
    }

    // Show the form for creating a new POE
    public function create()
    {
        $data = [
            'Title' => 'Create POE',
            'Desc' => 'Form to create a new Point of Entry',
            'Page' => 'poereports.CreatePOE',
        ];

        return view('scrn', $data);
    }

    // Store a newly created POE in the database
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:airport,land_border,seaport',
            'location' => 'nullable|json',
            'status' => 'required|in:active,inactive,maintenance',
            'capacity' => 'nullable|integer',
        ]);

        DB::table('points_of_entry')->insert([
            'name' => $validatedData['name'],
            'type' => $validatedData['type'],
            'location' => $validatedData['location'],
            'status' => $validatedData['status'],
            'capacity' => $validatedData['capacity'],
            'createdAt' => now(),
            'updatedAt' => now(),
        ]);

        return redirect()->route('poes.index')->with('success', 'Point of Entry created successfully.');
    }

    // Show the form for editing the specified POE
    public function EditPOE($id)
    {
        $poe = DB::table('points_of_entry')->where('id', $id)->first();

        if (!$poe) {
            return redirect()->route('poes.index')->with('error', 'Point of Entry not found.');
        }

        $data = [
            'Title' => 'Edit POE',
            'Desc' => 'Edit details of the selected Point of Entry',
            'Page' => 'poereports.EditPOE',
            'poe' => $poe,
        ];

        return view('scrn', $data);
    }

    // Update the specified POE in the database
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:airport,land_border,seaport',
            'location' => 'nullable|json',
            'status' => 'required|in:active,inactive,maintenance',
            'capacity' => 'nullable|integer',
        ]);

        $poe = DB::table('points_of_entry')->where('id', $id)->first();
        if (!$poe) {
            return redirect()->route('poes.index')->with('error', 'Point of Entry not found.');
        }

        DB::table('points_of_entry')->where('id', $id)->update([
            'name' => $validatedData['name'],
            'type' => $validatedData['type'],
            'location' => $validatedData['location'],
            'status' => $validatedData['status'],
            'capacity' => $validatedData['capacity'],
            'updatedAt' => now(),
        ]);

        return redirect()->route('poes.index')->with('success', 'Point of Entry updated successfully.');
    }

    // Remove the specified POE from the database
    public function destroy($id)
    {
        $poe = DB::table('points_of_entry')->where('id', $id)->first();
        if (!$poe) {
            return redirect()->route('poes.index')->with('error', 'Point of Entry not found.');
        }

        DB::table('points_of_entry')->where('id', $id)->delete();

        return redirect()->route('poes.index')->with('success', 'Point of Entry deleted successfully.');
    }

    // Show details of a specific POE
    public function show($id)
    {
        $poe = DB::table('points_of_entry')->where('id', $id)->first();

        if (!$poe) {
            return redirect()->route('poes.index')->with('error', 'Point of Entry not found.');
        }

        $data = [
            'Title' => 'POE Details',
            'Desc' => 'Detailed view of the selected Point of Entry',
            'Page' => 'poereports.POEDetails',
            'poe' => $poe,
        ];

        return view('scrn', $data);
    }
}
