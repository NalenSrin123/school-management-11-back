<?php

use App\Http\Controllers\BannerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Banner API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider and all of them
| will be assigned to the "api" middleware group.
|
*/

// Public routes
Route::get('/', [BannerController::class, 'index']);
Route::get('/active', [BannerController::class, 'activeBanners']);
Route::get('/{id}', [BannerController::class, 'show']);
Route::post('/', [BannerController::class, 'store']);
Route::put('/{id}', [BannerController::class, 'update']);
Route::delete('/{id}', [BannerController::class, 'destroy']);
