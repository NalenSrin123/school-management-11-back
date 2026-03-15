<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => [
                "required",
                Password::min(6)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "Validation failed!",
                "errors" => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $user = User::create([
            "name" => $validated["name"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"]),
            "status" => true,
            "role_id" => 1,
        ]);

        return response()->json([
            "message" => "User registered successfully",
            "data" => [
                "user" => $user->only(["id", "name", "email"]),
            ],
        ], 201);
    }
}
