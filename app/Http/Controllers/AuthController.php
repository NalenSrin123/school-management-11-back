<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\role;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $apiResponse;

    public function __construct(ApiResponseService $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "email" => "required|string|email|max:255|unique:users",
            "password" => [
                "required",
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "Validation failed!",
                "errors" => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $role = role::firstOrCreate([
            'name' => 'user',
        ]);

        $user = User::create([
            "name" => $validated["name"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"]),
            "status" => true,
            "role_id" => $role->id,
        ]);

        // Generate a 6-digit random OTP and set expiration (10 minutes)
        // $otp = rand(100000, 999999);
        // $expire_at = now()->addMinutes(10);

        // Store or update the OTP in the database
        // \App\Models\OtpCode::updateOrCreate(
        //     ['email' => $user->email],
        //     ['code' => $otp, 'expire_at' => $expire_at]
        // );

        // Dispatch the email using the SMTP configuration
        // \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpMail($otp));

        return response()->json([
            "message" => "User registered successfully.",
            "data" => [
                "user" => $user->only(["id", "name", "email"]),
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|string|max:255',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "message" => "Validation failed!",
                "errors" => $validator->errors(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('login_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->only(['id', 'name', 'email'])
        ], 200);
    }
}
