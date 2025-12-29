<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class AddApiTokenToInertia
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            // Delete existing tokens to avoid accumulation
            $request->user()->tokens()->delete();

            // Create a new API token
            $token = $request->user()->createToken('web-api-token')->plainTextToken;

            // Share the token with Inertia
            Inertia::share('apiToken', $token);
        }

        return $next($request);
    }
}
