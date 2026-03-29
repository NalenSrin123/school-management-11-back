<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolLogo;

class SchoolLogoController extends Controller
{
    public function index()
    {
        try {
            $logos = SchoolLogo::all();

            return response()->json([
                'success' => true,
                'message' => 'Logos fetched successfully',
                'data' => $logos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch logos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048'
            ]);

            $file = $request->file('logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();

            $file->move(public_path('logos'), $filename);

            $logo = SchoolLogo::create([
                'logo' => $filename
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logo uploaded successfully',
                'data' => $logo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $logo = SchoolLogo::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Logo found',
                'data' => $logo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logo not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $logo = SchoolLogo::findOrFail($id);

            if ($request->hasFile('logo')) {

                if ($logo->logo && file_exists(public_path('logos/' . $logo->logo))) {
                    unlink(public_path('logos/' . $logo->logo));
                }

                $file = $request->file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();

                $file->move(public_path('logos'), $filename);

                $logo->logo = $filename;
            }

            $logo->save();

            return response()->json([
                'success' => true,
                'message' => 'Logo updated successfully',
                'data' => $logo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $logo = SchoolLogo::findOrFail($id);

            if ($logo->logo && file_exists(public_path('logos/' . $logo->logo))) {
                unlink(public_path('logos/' . $logo->logo));
            }

            $logo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Delete failed...!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}