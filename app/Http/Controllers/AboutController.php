<?php

namespace App\Http\Controllers;

use App\Models\About;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AboutController extends Controller
{
    private $apiResponseService;

    public function __construct(ApiResponseService $apiResponseService)
    {
        $this->apiResponseService = $apiResponseService;
    }

    public function index()
    {
        try {
            $about = About::orderBy("created_at", "desc")->paginate(10);
            return $this->apiResponseService->success([
                "data" => $about,
                "message" => "About Retrieved Successfully"
            ]);
        } catch (\Exception $e) {
            return $this->apiResponseService->error([
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                "title" => "required|string|max:255",
                "subtitle" => "required|string|max:255",
                "mission" => "required|string|max:255",
                "vision" => "required|string|max:255",
                "image" => "nullable|image|max:2048",
                "status" => "nullable|in:active,inactive"
            ]);

            if ($validate->fails()) {
                return $this->apiResponseService->error($validate->errors());
            }


            $about = new About();
            $about->title = $request->title;
            $about->subtitle = $request->subtitle;
            $about->mission = $request->mission;
            $about->vision = $request->vision;
            $about->image = $request->image;
            $about->status = $request->status ?? 'active';
            $about->created_by = Auth::id();
            $about->save();

            return $this->apiResponseService->success([
                "data" => $about,
                "message" => "About Created Successfully"
            ], 201);
        } catch (\Exception $e) {
            return $this->apiResponseService->error([
                "message" => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $about = About::findOrFail($id);
            $validate = Validator::make($request->all(), [
                "title" => "sometimes|string|max:255",
                "subtitle" => "sometimes|string|max:255",
                "mission" => "sometimes|string|max:255",
                "vision" => "sometimes|string|max:255",
                "image" => "nullable|image|max:2048",
                "status" => "nullable|in:active,inactive"
            ]);

            if ($validate->fails()) {
                return $this->apiResponseService->error($validate->errors());
            }
            $about = new About();
            $about->title = $request->title;
            $about->description = $request->description;
            $about->image = $request->image;
            $about->status = $request->status ?? "active";
            $about->created_by = Auth::id();
            $about->updated_by = Auth::id();
            $about->save();

            return $this->apiResponseService->success([
                "data" => $about,
                "message" => "About Updated Successfully"
            ], 200);
        } catch (\Exception $e) {
            return $this->apiResponseService->error([
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $about = About::findOrFail($id);
            $about->delete();

            return $this->apiResponseService->success([
                "message" => "About Deleted Successfully"
            ], 200);
        } catch (\Exception $e) {
            return $this->apiResponseService->error([
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
