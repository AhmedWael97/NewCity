<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddCacheHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $maxAge = 3600): Response
    {
        $response = $next($request);

        // Only add cache headers for GET requests
        if ($request->isMethod('GET') && $response->getStatusCode() == 200) {
            $response->header('Cache-Control', "public, max-age={$maxAge}");
            $response->header('Expires', gmdate('D, d M Y H:i:s', time() + $maxAge) . ' GMT');
            
            // Add ETag for better caching
            $etag = md5($response->getContent());
            $response->header('ETag', '"' . $etag . '"');
            
            // Check if client has cached version
            if ($request->header('If-None-Match') === '"' . $etag . '"') {
                return response('', 304);
            }
        }

        return $response;
    }
}