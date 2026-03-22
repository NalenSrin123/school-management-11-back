<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;

class BannerController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseService $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    /**
     * 🔐 LOGIN (Sanctum)
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials'
            ], 401);
        }

        $user = Auth::user();

        // create token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $user
        ]);
    }

    /**
     * 📄 GET all banners
     */
    public function index()
    {
        try {
            $banners = Banner::with('creator')->get();
            return $this->apiResponse->success($banners, 'Banners retrieved successfully');
        } catch (Exception $e) {
            return $this->apiResponse->error($e->getMessage(), 500);
        }
    }

    /**
     * ➕ STORE banner
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            if (empty($data)) {
                $data = json_decode($request->getContent(), true);
            }

            $validator = Validator::make($data ?? [], [
                'title' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Image is optional
            $path = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = $file->store('banners', 'public');
            }

            $banner = Banner::create([
                'title' => $data['title'] ?? null,
                'image_path' => $path,
                'created_by' => Auth::id() ?? 1,
                'status' => 'active',
                'created_date' => now()
            ]);

            return $this->apiResponse->success($banner, 'Banner created successfully', 201);

        } catch (Exception $e) {
            return $this->apiResponse->error($e->getMessage(), 400);
        }
    }

    /**
     * 🔍 SHOW banner
     */
    public function show($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            return $this->apiResponse->success($banner, 'Banner retrieved successfully');
        } catch (Exception $e) {
            return $this->apiResponse->error('Banner not found', 404);
        }
    }

    /**
     * ✏️ UPDATE banner
     */
    public function update(Request $request, $id)
    {
        try {
            // Accept JSON or form-data
            $data = $request->all();
            if (empty($data)) {
                $data = json_decode($request->getContent(), true);
            }

            $banner = Banner::findOrFail($id);

            $validator = Validator::make($data ?? [], [
                'title' => 'sometimes|string|max:255',
                'image' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif,svg|max:2048',
                'status' => 'sometimes|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // update image if exists
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = $file->store('banners', 'public');
                $banner->image_path = $path;
            }

            // Update title if provided
            if (isset($data['title'])) {
                $banner->title = $data['title'];
            }
            // Update status if provided
            if (isset($data['status'])) {
                $banner->status = $data['status'];
            }
            $banner->save();

            return $this->apiResponse->success($banner, 'Banner updated successfully');

        } catch (Exception $e) {
            return $this->apiResponse->error($e->getMessage(), 400);
        }
    }

    /**
     * ❌ DELETE banner
     */
    public function destroy($id)
    {
        try {
            $banner = Banner::findOrFail($id);
            $banner->delete();

            return $this->apiResponse->success(null, 'Banner deleted successfully');

        } catch (Exception $e) {
            return $this->apiResponse->error($e->getMessage(), 400);
        }
    }

    /**
     * ✅ GET active banners
     */
    public function activeBanners()
    {
        try {
            $banners = Banner::with('creator')
                ->where('status', 'active')
                ->get();

            return $this->apiResponse->success($banners, 'Active banners retrieved successfully');

        } catch (Exception $e) {
            return $this->apiResponse->error($e->getMessage(), 500);
        }
    }

    /**
     * 🔄 TOGGLE status
     */
    public function toggleStatus($id)
    {
        try {
            $banner = Banner::findOrFail($id);

            $banner->status = $banner->status === 'active' ? 'inactive' : 'active';
            $banner->save();

            return $this->apiResponse->success($banner, 'Status updated successfully');

        } catch (Exception $e) {
            return $this->apiResponse->error($e->getMessage(), 400);
        }
    }
}
