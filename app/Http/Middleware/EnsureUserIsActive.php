<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->is_active) {
            // Revoke all tokens for deactivated users
            $request->user()->tokens()->delete();
            
            return response()->json([
                'success' => false,
                'message' => 'Your account has been deactivated. Please reactivate it to continue.'
            ], 403);
        }

        return $next($request);
    }
}
