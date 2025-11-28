<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OptimizeResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Add performance headers
        if ($response instanceof Response) {
            // Enable browser caching
            if ($this->shouldCache($request)) {
                $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            }

            // Add security headers
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
            $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

            // Add performance headers
            $response->headers->set('X-DNS-Prefetch-Control', 'on');

            // Compress response if applicable
            if ($this->shouldCompress($request, $response)) {
                $this->compressResponse($response);
            }

            // Remove unnecessary headers
            $response->headers->remove('X-Powered-By');
        }

        return $response;
    }

    /**
     * Check if request should be cached
     */
    private function shouldCache(Request $request): bool
    {
        $path = $request->path();
        
        // Cache static assets
        return preg_match('/\.(css|js|jpg|jpeg|png|gif|ico|woff|woff2|ttf|svg|webp)$/', $path);
    }

    /**
     * Check if response should be compressed
     */
    private function shouldCompress(Request $request, Response $response): bool
    {
        // Check if client supports compression
        $acceptEncoding = $request->header('Accept-Encoding', '');
        
        if (strpos($acceptEncoding, 'gzip') === false) {
            return false;
        }

        // Only compress text-based responses
        $contentType = $response->headers->get('Content-Type', '');
        
        return strpos($contentType, 'text/') === 0 
            || strpos($contentType, 'application/json') !== false
            || strpos($contentType, 'application/javascript') !== false;
    }

    /**
     * Compress the response
     */
    private function compressResponse(Response $response): void
    {
        $content = $response->getContent();
        
        if ($content && function_exists('gzencode')) {
            $compressed = gzencode($content, 9);
            
            if ($compressed !== false) {
                $response->setContent($compressed);
                $response->headers->set('Content-Encoding', 'gzip');
                $response->headers->set('Content-Length', strlen($compressed));
            }
        }
    }
}
