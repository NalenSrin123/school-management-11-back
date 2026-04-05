<?php
namespace App\Http\Controllers;

use App\Models\About;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
           $about = About::all()->map(function ($item) {
            $data          = $item->toArray();
            $data['image'] = $item->image
                ? Storage::disk('public')->url($item->image)
                : null;
            return $data;
        });

        return $this->apiResponseService->success(
             $about,
            "About Retrieved Successfully",
            200

        );
        } catch (\Exception $e) {
            return $this->apiResponseService->error([
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                "title"    => "required|string|max:255",
                "subtitle" => "required|string|max:255",
                "mission"  => "required|string|max:255",
                "vision"   => "required|string|max:255",
                "image"    => "nullable|image|max:2048",
                "status"   => "nullable|in:active,inactive",
            ]);

            if ($validate->fails()) {
                return $this->apiResponseService->error($validate->errors());
            }

            $imagePath = $request->hasFile('image')
                ? $request->file('image')->store('abouts', 'public') : null;

            $about             = new About();
            $about->title      = $request->title;
            $about->subtitle   = $request->subtitle;
            $about->mission    = $request->mission;
            $about->vision     = $request->vision;
            $about->image      = $imagePath;
            $about->status     = $request->status ?? 'active';
            $about->created_by = Auth::id() ?? 1;
            $about->save();

            $responseData          = $about->toArray();
            $responseData['image'] = $imagePath
                ? Storage::disk('public')->url($imagePath)
                : null;

            return $this->apiResponseService->success($responseData,"About Created Successfully",201);
        } catch (\Exception $e) {
            return $this->apiResponseService->error($e->getMessage(),500);
        }
    }
    public function show($id)
    {
        try {
            $about = About::find($id);
            $about = About::all()->map(function ($item) {
            $data          = $item->toArray();
            $data['image'] = $item->image
                ? Storage::disk('public')->url($item->image)
                : null;
            return $data;
        });
            if (! $about) {
                return $this->apiResponseService->error("About not found", 404);
            }

            return $this->apiResponseService->success($about, "About Retrieved Successfully");
        } catch (\Exception $e) {
            return $this->apiResponseService->error([
                "message" => $e->getMessage(),
            ], 500);
        }
    }
 public function update(Request $request, $id)
{
    try {
        $about = About::find($id);
        if (!$about) {
            return $this->apiResponseService->error("About not found", 404);
        }

        $validate = Validator::make($request->all(), [
            "title"    => "sometimes|string|max:255",
            "subtitle" => "sometimes|string|max:255",
            "mission"  => "sometimes|string|max:255",
            "vision"   => "sometimes|string|max:255",
            "image"    => "sometime|image|max:2048",
            "status"   => "sometime|in:active,inactive",
        ]);

        if ($validate->fails()) {
            return $this->apiResponseService->error($validate->errors());
        }

        // Only fill fields that were actually sent in the request
        $about->fill($request->only([
            'title', 'subtitle', 'mission', 'vision', 'status'
        ]));

        if ($request->hasFile("image")) {
            if ($about->image && Storage::disk("public")->exists($about->image)) {
                Storage::disk("public")->delete($about->image);
            }
            $about->image = $request->file("image")->store("abouts", "public");
        }

        $about->updated_by = Auth::id() ?? 1;
        $about->save();

        // Build full response with complete image URL
        $responseData          = $about->toArray();
        $responseData['image'] = $about->image
            ? Storage::disk('public')->url($about->image)
            : null;

        return $this->apiResponseService->success($responseData, "About Updated Successfully", 200);
    } catch (\Exception $e) {
        return $this->apiResponseService->error($e->getMessage(), 500);
    }
}

    public function destroy($id)
    {
        try {
            $about = About::findOrFail($id);
            $about->delete();

            return $this->apiResponseService->success(
                "About Deleted Successfully",
             200);
        } catch (\Exception $e) {
            return $this->apiResponseService->error(
              $e->getMessage(),
             500);
        }
    }
}
