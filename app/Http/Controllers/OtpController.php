<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\OtpCode;
use App\Mail\OtpMail;

class OtpController extends Controller
{
    /**
     * Send OTP to the user's email.
     */
    public function sendOtp(Request $request)
    {
        // Validate the incoming email address
        $request->validate(['email' => 'required|email']);

        // Generate a 6-digit random code and set expiration (10 minutes)
        $otp = rand(100000, 999999);
        $expire_at = now()->addMinutes(10);

        // Store or update the OTP in the database
        OtpCode::updateOrCreate(
            ['email' => $request->email],
            ['code' => $otp, 'expire_at' => $expire_at]
        );

        // Dispatch the email using the SMTP configuration
        Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json([
            'message' => 'OTP has been sent successfully to ' . $request->email
        ]);
    }

    /**
     * Verify the provided OTP.
     */
    public function verifyOtp(Request $request)
    {
        // Validate input format
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:6'
        ]);

        // Find the record matching email and code
        $data = OtpCode::where('email', $request->email)
            ->where('code', $request->otp)
            ->first();

        // Check if code exists and is not expired
        if (!$data || now()->isAfter($data->expire_at)) {
            return response()->json([
                'message' => 'Invalid or expired OTP code.'
            ], 400);
        }

        // Delete the code after successful verification for security
        $data->delete();

        return response()->json([
            'message' => 'OTP verification successful!'
        ]);
    }
}
