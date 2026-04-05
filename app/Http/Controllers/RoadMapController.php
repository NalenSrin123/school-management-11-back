<?php

namespace App\Http\Controllers;

use App\Models\RoadMap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RoadMapController extends Controller
{
    // ✅ List all roadmaps
    public function index()
    {
        $roadmaps = RoadMap::with('creator')->get();
        return response()->json($roadmaps);
    }

    // ✅ Create a new roadmap
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // validate image
        ]);

        $imagePath = null;
        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('roadmaps'), $filename);
            $imagePath = asset('roadmaps/' . $filename);
        }
        // if ($request->hasFile('image_path')) {
        //     $file = $request->file('image_path');
        //     $imagePath = $file->store('roadmaps', 'public'); // saves in storage/app/public/roadmaps
        // }


        $roadmap = RoadMap::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status ?? 'active',
            'image_path' => $imagePath,
            'created_by' => Auth::id() ?? 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Roadmap created successfully',
            'data' => $roadmap
        ], 201);
    }
    // ✅ Show single roadmap
    public function show($id)
    {
        $roadmap = RoadMap::with('creator')->find($id);

        if (!$roadmap) {
            return response()->json(['message' => 'RoadMap not found'], 404);
        }

        return response()->json($roadmap);
    }

    // ✅ Update roadmap
    public function update(Request $request, $id)
    {
        $roadmap = RoadMap::find($id);

        if (!$roadmap) {
            return response()->json(['message' => 'RoadMap not found'], 404);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:active,inactive',
            'image_path' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image_path')) {
            $file = $request->file('image_path');

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $destination = public_path('roadmaps');
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            $file->move($destination, $filename);

            // Delete old image safely
            if ($roadmap->image_path) {
                $oldPath = public_path('roadmaps/' . basename($roadmap->image_path));
                if (is_file($oldPath)) {
                    unlink($oldPath);
                }
            }

            $roadmap->image_path = asset('roadmaps/' . $filename);
        }

        // ✅ Short and clean update
        $roadmap->update($request->only(['title', 'description', 'status']));
        $roadmap->updated_at = now();
        $roadmap->save();

        return response()->json([
            'status' => true,
            'message' => 'RoadMap updated successfully',
            'data' => $roadmap
        ]);
    }

    // ✅ Delete roadmap
    public function destroy($id)
    {
        $roadmap = RoadMap::find($id);

        if (!$roadmap) {
            return response()->json(['message' => 'RoadMap not found'], 404);
        }

        // Delete the uploaded image if it exists
        // if ($roadmap->image_path && Storage::disk('public')->exists($roadmap->image_path)) {
        //     Storage::disk('public')->delete($roadmap->image_path);
        // }

        if ($roadmap->image_path) {
            $path = public_path('roadmaps/' . basename($roadmap->image_path));
            if (is_file($path)) {
                unlink($path);
            }
        }

        $roadmap->delete();

        return response()->json([
            'status' => true,
            'message' => 'RoadMap and its image deleted successfully'
        ]);
    }
}
