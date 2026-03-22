<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ConfirmPasswordController extends Controller
{
     // Confirm New Password
    public function confirmNewPassword(Request $request)
    {
        $request->validate([
            'email'=>'required|email|exists:users,email',
            'password'=>'required|min:6|confirmed'
        ]);

        $user = User::where('email',$request->email)->first();

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            "message"=>"Password confirm successfully"
        ]);
    }
}
