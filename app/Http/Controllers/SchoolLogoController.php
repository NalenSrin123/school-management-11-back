<?php

namespace App\Http\Controllers;

use App\Models\SchoolLogo;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolLogoController extends Controller
{
    private $apiResponseService;

    public function __construct(ApiResponseService $apiResponseService)
    {
        $this->apiResponseService = $apiResponseService;
    }

    public function index()
    {
        try {
            $logos = SchoolLogo::all();


            return $this->apiResponseService->success($logos);
        } catch (\Exception $e) {
            return $this->apiResponseService->error($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            ]);

            $path = $request->file('logo')->store('logos', 'public');

            $logo = SchoolLogo::create([
                'logo' => $path,
            ]);

            return $this->apiResponseService->success($this->formatLogo($logo), 'Logo Created Succesfully', 201);
        } catch (\Exception $e) {
            return $this->apiResponseService->error($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $logo = SchoolLogo::findOrFail($id);

            return $this->apiResponseService->success($this->formatLogo($logo), 'Logo Reatived Successfully');
        } catch (\Exception $e) {
            return $this->apiResponseService->error($e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $logo = SchoolLogo::findOrFail($id);

            $request->validate([
                'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            ]);

            if ($request->hasFile('logo')) {
                if ($logo->logo && Storage::disk('public')->exists($logo->logo)) {
                    Storage::disk('public')->delete($logo->logo);
                }

                $logo->logo = $request->file('logo')->store('logos', 'public');
            }

            $logo->save();

            return $this->apiResponseService->success($this->formatLogo($logo), 'Logo Updated Succesfully', 200);
        } catch (\Exception $e) {
            return $this->apiResponseService->error($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $logo = SchoolLogo::findOrFail($id);

            if ($logo->logo && Storage::disk('public')->exists($logo->logo)) {
                Storage::disk('public')->delete($logo->logo);
            }

            $logo->delete();

            return $this->apiResponseService->success(null, 'Deleted successfully');
        } catch (\Exception $e) {
            return $this->apiResponseService->error($e->getMessage(), 500);
        }
    }

}
