<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller
{
    // GET all menus
    public function index()
    {
        try {
            $menus = Menu::orderBy('order')->get();
            return response()->json($menus, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch menus',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // GET single menu
    public function show($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            return response()->json($menu, 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Menu not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to fetch menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // POST create menu
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'route' => 'required|string|max:255',
                'icon' => 'nullable|string|max:50',
                'order' => 'nullable|integer'
            ]);

            $menu = Menu::create($validated);
            return response()->json($menu, 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // PUT update menu
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'route' => 'sometimes|required|string|max:255',
                'icon' => 'nullable|string|max:50',
                'order' => 'nullable|integer'
            ]);

            $menu = Menu::findOrFail($id);
            $menu->update($validated);

            return response()->json($menu, 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Menu not found'
            ], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE menu
    public function destroy($id)
    {
        try {
            $menu = Menu::findOrFail($id);
            $menu->delete();

            return response()->json([
                'message' => 'Menu deleted successfully'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Menu not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete menu',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}