<?php

use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ResetOTPController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\resetPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/resetOtp',[ResetOTPController::class,"resetOtp"]);
Route::post('/send_otp', [OtpController::class, 'sendOtp']);
Route::post('/verify_otp', [OtpController::class, 'verifyOtp']);

Route::post('/forgot-password', [resetPasswordController::class, 'forgotPassword']);
Route::post('/reset-password/{id}',[resetPasswordController::class,'resetPassword']);

Route::get('/auth/google', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
