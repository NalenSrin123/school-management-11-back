<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class UserController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseService $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    
    public function index(Request $request)
    {
        $users = User::all();

        return $this->apiResponse->success($users, 'Users retrieved successfully');
    }

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => [
                'required',
                Password::min(6)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'status'   => true,
            'role_id'  => 3,
        ]);

        return $this->apiResponse->success($user, 'User created successfully', 201);
    }

    
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->apiResponse->error('User not found', 404);
        }

        return $this->apiResponse->success($user, 'User retrieved successfully');
    }

    
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->apiResponse->error('User not found', 404);
        }

        $validated = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $id,
            'password' => [
                'sometimes',
                Password::min(6)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);

        return $this->apiResponse->success($user->fresh(), 'User updated successfully');
    }


    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->apiResponse->error('User not found', 404);
        }

        $user->delete();

        return $this->apiResponse->success(null, 'User deleted successfully');
    }
}