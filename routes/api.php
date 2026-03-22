<?php

use App\Http\Controllers\ConfirmPasswordController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\ResetOTPController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\resetPasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SchoolLogoController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LocatinControoler;
use App\Http\Controllers\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoadMapController;
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

    Route::post("/register", [AuthController::class, "register"]);
    Route::post("/login", [AuthController::class, "login"]);
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });

    Route::middleware('auth:sanctum')->post('/confirm-password', [ConfirmPasswordController::class, 'confirmNewPassword']);
    Route::post('/resetOtp', [ResetOTPController::class, "resetOtp"]);
    Route::post('/send_otp', [OtpController::class, 'sendOtp']);
    Route::post('/verify_otp', [OtpController::class, 'verifyOtp']);
    Route::post('/loginWithOtp', [OtpController::class, 'loginWithOtp']);
Route::apiResource('locations', LocatinControoler::class);
Route::post('/forgot-password', [resetPasswordController::class, 'forgotPassword']);
Route::post('/reset-password/{id}', [resetPasswordController::class, 'resetPassword']);

Route::get('/auth/google', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/roadmaps', [RoadMapController::class, 'index']);
    Route::post('/roadmaps', [RoadMapController::class, 'store']);
    Route::get('/roadmaps/{id}', [RoadMapController::class, 'show']);
    Route::put('/roadmaps/{id}', [RoadMapController::class, 'update']);
    Route::delete('/roadmaps/{id}', [RoadMapController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/feedback', [FeedbackController::class, 'index']);
Route::post('/feedback', [FeedbackController::class, 'store']);
Route::get('/feedback/{feedback}', [FeedbackController::class, 'show']);
Route::get('/logo', [SchoolLogoController::class, 'index']);
Route::post('/logo', [SchoolLogoController::class, 'store']);
Route::put('/logo/{id}', [SchoolLogoController::class, 'update']);
Route::delete('/logo/{id}', [SchoolLogoController::class, 'destroy']);
Route::get('/event', [EventController::class,'index']);
Route::post('/event', [EventController::class,'store']);
Route::get('/event', [EventController::class,'show']);
Route::patch('/event', [EventController::class,'update']);
Route::delete('/event', [EventController::class,'delete']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/courses', [CourseController::class, 'index']);
    Route::post('/courses', [CourseController::class, 'store']);
    Route::get('/courses/{id}', [CourseController::class, 'show']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
});
