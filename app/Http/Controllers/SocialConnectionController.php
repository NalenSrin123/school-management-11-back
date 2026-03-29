<?php

namespace App\Http\Controllers;

use App\Models\SocialConnection;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SocialConnectionController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseService $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    /**
     * Display a listing of social connections.
     */
    public function index()
    {
        try {
            $connections = SocialConnection::latest()->get();
            return $this->apiResponse->success($connections, 'Social connections retrieved successfully');
        } catch (\Exception $e) {
            Log::error('SocialConnection index error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to retrieve social connections', 500);
        }
    }

    /**
     * Store a newly created social connection.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
            'status' => 'required|string|in:active,inactive',
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->error('Validation failed', 422, $validator->errors());
        }

        try {
            $data = $request->only(['url', 'status']);

            if ($request->hasFile('icon_image')) {
                $image = $request->file('icon_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/social_connections'), $imageName);
                $data['icon_image'] = 'uploads/social_connections/' . $imageName;
            }

            $data['created_by'] = Auth::id();
            $connection = SocialConnection::create($data);

            return $this->apiResponse->success($connection, 'Social connection created successfully', 201);
        } catch (\Exception $e) {
            Log::error('SocialConnection store error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to create social connection', 500);
        }
    }

    /**
     * Display the specified social connection.
     */
    public function show($id)
    {
        try {
            $socialConnection = SocialConnection::find($id);
            if (!$socialConnection) {
                return $this->apiResponse->error('Social connection not found', 404);
            }
            return $this->apiResponse->success($socialConnection, 'Social connection retrieved successfully');
        } catch (\Exception $e) {
            Log::error('SocialConnection show error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to retrieve social connection', 500);
        }
    }

    /**
     * Update the specified social connection.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'sometimes|required|url',
            'status' => 'sometimes|required|string|in:active,inactive',
            'icon_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse->error('Validation failed', 422, $validator->errors());
        }

        try {
            $socialConnection = SocialConnection::find($id);
            if (!$socialConnection) {
                return $this->apiResponse->error('Social connection not found', 404);
            }

            $data = $request->only(['url', 'status']);

            if ($request->hasFile('icon_image')) {
                // Delete old image
                if ($socialConnection->getRawOriginal('icon_image') && File::exists(public_path($socialConnection->getRawOriginal('icon_image')))) {
                    File::delete(public_path($socialConnection->getRawOriginal('icon_image')));
                }

                $image = $request->file('icon_image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/social_connections'), $imageName);
                $data['icon_image'] = 'uploads/social_connections/' . $imageName;
            }

            $data['updated_by'] = Auth::id();
            $socialConnection->update($data);

            return $this->apiResponse->success($socialConnection, 'Social connection updated successfully');
        } catch (\Exception $e) {
            Log::error('SocialConnection update error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to update social connection', 500);
        }
    }

    /**
     * Remove the specified social connection.
     */
    public function destroy($id)
    {
        try {
            $socialConnection = SocialConnection::find($id);
            if (!$socialConnection) {
                return $this->apiResponse->error('Social connection not found', 404);
            }

            // Delete image if it exists
            if ($socialConnection->getRawOriginal('icon_image') && File::exists(public_path($socialConnection->getRawOriginal('icon_image')))) {
                File::delete(public_path($socialConnection->getRawOriginal('icon_image')));
            }

            $socialConnection->delete();
            return $this->apiResponse->success(null, 'Social connection deleted successfully');
        } catch (\Exception $e) {
            Log::error('SocialConnection destroy error: ' . $e->getMessage());
            return $this->apiResponse->error('Failed to delete social connection', 500);
        }
    }
}
