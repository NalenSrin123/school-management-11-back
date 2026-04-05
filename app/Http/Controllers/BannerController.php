<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Exception;

class BannerController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseService $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    public function index()
    {
        try {
            $banners = Banner::with('creator')->get();
            return $this->apiResponse->success($banners, 'Banners retrieved successfully');
        } catch (Exception $e) {
            return $this->apiResponse->error($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title'      => 'required|string|max:255',
                'image_path' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'status'     => 'required|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->error($validator->errors(), 422);
            }

            $data = $validator->validated();

            $imageUrl = null;

            if ($request->hasFile('image_path')) {
                $path     = $request->file('image_path')->store('banners', 'public');
                $imageUrl = Storage::disk('public')->url($path);
            }

            $banner = Banner::create([
                'title'        => $data['title'],
                'image_path'   => $imageUrl,
                'created_by'   => Auth::id(),
                'status'       => $data['status'],
                'created_date' => now(),
            ]);

            return $this->apiResponse->success($banner, 'Banner created successfully', 201);

        } catch (Exception $e) {
            return $this->apiResponse->error($e->getMessage(), 400);
        }
    }

    public function show($id)
    {
        try {
            $banner = Banner::with('creator')->findOrFail($id);
            return $this->apiResponse->success($banner, 'Banner retrieved successfully');
        } catch (Exception $e) {
            return $this->apiResponse->error('Banner not found', 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $banner = Banner::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'title'      => 'sometimes|string|max:255',
                'image_path' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'status'     => 'sometimes|in:active,inactive',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse->error($validator->errors(), 422);
            }

            $data = $validator->validated();

            if ($request->hasFile('image_path')) {

                if ($banner->image_path) {
                    $oldPath = str_replace(Storage::disk('public')->url(''), '', $banner->image_path);
                    Storage::disk('public')->delete($oldPath);
                }

                $path              = $request->file('image_path')->store('banners', 'public');
                $banner->image_path = Storage::disk('public')->url($path); // ✅ Full URL
            }

            if (isset($data['title']))  $banner->title  = $data['title'];
            if (isset($data['status'])) $banner->status = $data['status'];

            $banner->save();

            return $this->apiResponse->success($banner, 'Banner updated successfully');

        } catch (Exception $e) {
            return $this->apiResponse->error($e->getMessage(), 400);
        }
    }

    public function destroy($id)
    {
        try {
            $banner = Banner::findOrFail($id);

            //  Delete image from storage when banner is deleted
            if ($banner->image_path) {
                $oldPath = str_replace(Storage::disk('public')->url(''), '', $banner->image_path);
                Storage::disk('public')->delete($oldPath);
            }

            $banner->delete();

            return $this->apiResponse->success(null, 'Banner deleted successfully');

        } catch (Exception $e) {
            return $this->apiResponse->error($e->getMessage(), 400);
        }
    }
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
