<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class resetPasswordController extends Controller
{
   // Forgot Password
    public function forgotPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email not found'
            ], 404);
        }

        // Generate token
        $token = Str::random(60);

        // Insert token into password_resets table
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        // Create reset link
        $link = url('/reset-password/'.$user->id.'/'.$token);

        // Send email
        Mail::raw("Reset password: ".$link, function($message) use ($request){
            $message->to($request->email)
                    ->subject('Reset Password');
        });

        // Return token explicitly in response
        return response()->json([
            'message' => 'Email sent',
            'reset_link' => $link,
            'token' => $token  
        ]);
    }

    // Reset Password
    public function resetPassword(Request $request, $id)
{
    // Get token from Bearer Authorization header
    $authHeader = $request->header('Authorization'); // "Bearer TOKEN"
    $token = str_replace('Bearer ', '', $authHeader);

    $reset = DB::table('password_resets')->where('token', $token)->first();

    if (!$reset) {
        return response()->json(['message' => 'Invalid token'], 400);
    }

    $user = User::find($id);
    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $user->password = Hash::make($request->password);
    $user->save();

    DB::table('password_resets')->where('token', $token)->delete();

    return response()->json(['message' => 'Password successfully reset']);
}


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
