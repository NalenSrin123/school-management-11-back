<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roleId = $request->user()->role_id;

        if ($roleId !== 1 && $roleId !== 2) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
