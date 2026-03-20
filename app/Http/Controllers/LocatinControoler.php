<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocatinControoler extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         return response()->json(Location::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $request->validate([
            'name' => 'required',
            'created_by' => 'required'
        ]);

        $location = Location::create([
            'name' => $request->name,
            'address' => $request->address,
            'link' => $request->link,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => $request->status ?? 'active',
            'created_by' => $request->created_by,
            'created_date' => now(),
        ]);

        return response()->json($location);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return response()->json(Location::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $location = Location::findOrFail($id);

        $location->update([
            'name' => $request->name,
            'address' => $request->address,
            'link' => $request->link,
            'phone' => $request->phone,
            'email' => $request->email,
            'status' => $request->status,
            'updated_date' => now(),
        ]);

        return response()->json($location);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Location::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
