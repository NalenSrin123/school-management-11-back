<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;

class LocatinControoler extends Controller
{
        protected $apiResponse;
    
    public function __construct(ApiResponseService $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function index()
    {
        $locations = Location::all();
        return $this->apiResponse->success($locations, "Locations fetched successfully");
    }

    public function store(Request $request)
    {
        try {
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

            return $this->apiResponse->success($location, "Location created successfully", 201);

        } catch (\Exception $e) {
            return $this->apiResponse->error("Failed to create location", 500, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $location = Location::findOrFail($id);
            return $this->apiResponse->success($location, "Location found");

        } catch (\Exception $e) {
            return $this->apiResponse->error("Location not found", 404);
        }
    }

     public function update(Request $request, $id)
    {
        try {
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

            return $this->apiResponse->success($location, "Location updated successfully");

        } catch (\Exception $e) {
            return $this->apiResponse->error("Failed to update location", 500);
        }
    }

    public function destroy($id)
    {
        try {
            $location = Location::findOrFail($id);
            $location->delete();

            return $this->apiResponse->success(null, "Deleted successfully");

        } catch (\Exception $e) {
            return $this->apiResponse->error("Failed to delete location", 500);
        }
    }
}
