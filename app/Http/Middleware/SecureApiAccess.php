<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureApiAccess
{
    /**
     * Handle an incoming request.
     * This middleware ensures only authorized apps can access the API
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get API key from header
        $apiKey = $request->header('X-API-Key');

        // Get expected API key from environment
        $expectedApiKey = env('MOBILE_API_KEY');

        // If API key is configured, verify it
        if ($expectedApiKey && $apiKey !== $expectedApiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 401);
        }

        // Verify app signature if provided (additional security layer)
        $appSignature = $request->header('X-App-Signature');
        if ($appSignature) {
            // You can implement signature verification here
            // For example, verifying HMAC of request body with a secret key
            $secret = env('MOBILE_APP_SECRET');
            if ($secret) {
                $expectedSignature = hash_hmac('sha256', $request->getContent(), $secret);
                if (!hash_equals($expectedSignature, $appSignature)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid app signature',
                    ], 401);
                }
            }
        }

        return $next($request);
    }
}
