<?php

namespace App\Http\Controllers;

use App\Mail\OtpMail;
use App\Models\OtpCode;
use App\Models\User;
use App\Services\ApiResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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

        $user = User::create([
            "name" => $validated["name"],
            "email" => $validated["email"],
            "password" => Hash::make($validated["password"]),
            "status" => true,
            "role_id" => 3,
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
            "message" => "User registered successfully. An OTP has been sent to your email to verify your login.",
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

        // Generate a 6-digit random OTP and set expiration (10 minutes)
        $otp = rand(100000, 999999);
        $expire_at = now()->addMinutes(10);

        // Store or update the OTP in the database
        OtpCode::updateOrCreate(
            ['email' => $user->email],
            ['code' => $otp, 'expire_at' => $expire_at]
        );

        // Dispatch the email using the SMTP configuration
        Mail::to($user->email)->send(new OtpMail($otp));

        return response()->json([
            'success' => true,
            'message' => 'Login successful. An OTP has been sent to your email. Please verify it to complete your login.',
            'user' => $user->only(['id', 'name', 'email'])
        ], 200);
    }
}
