<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    // protected function redirectTo(Request $request): ?string
    // {
    //     // For API requests, return null to trigger 401 JSON response
    //     return $request->expectsJson() ? null : route('login');
    // }

    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            abort(response()->json([
                'success' => false,
                'message' => 'You must be logged in to access this resource.'
            ], 401));
        }

        parent::unauthenticated($request, $guards);
    }
}