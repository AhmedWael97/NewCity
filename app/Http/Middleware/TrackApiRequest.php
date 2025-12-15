<?php

namespace App\Http\Middleware;

use App\Models\ApiRequest;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;

class TrackApiRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start timer
        $startTime = microtime(true);

        // Process the request
        $response = $next($request);

        // Calculate response time
        $responseTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds

        // Track the request asynchronously to not slow down the response
        $this->trackRequest($request, $response, $responseTime);

        return $response;
    }

    /**
     * Track the API request
     */
    protected function trackRequest(Request $request, Response $response, float $responseTime): void
    {
        try {
            // Get device type
            $agent = new Agent();
            $deviceType = 'unknown';
            if ($agent->isPhone()) {
                $deviceType = 'mobile';
            } elseif ($agent->isTablet()) {
                $deviceType = 'tablet';
            } elseif ($agent->isDesktop()) {
                $deviceType = 'desktop';
            }

            // Extract endpoint path (remove domain and query string)
            $endpoint = $request->path();

            // Detect action and resource type
            $actionType = ApiRequest::detectActionType($request->method(), $endpoint);
            $resourceType = ApiRequest::detectResourceType($endpoint);

            // Extract resource ID from route parameters
            $resourceId = $this->extractResourceId($request);

            // Get relevant headers (exclude sensitive data)
            $headers = [
                'accept' => $request->header('Accept'),
                'content-type' => $request->header('Content-Type'),
                'authorization' => $request->header('Authorization') ? 'Bearer ***' : null,
            ];

            // Get request data (exclude sensitive fields)
            $requestData = $this->sanitizeRequestData($request->all());

            // Create tracking record
            ApiRequest::create([
                'user_id' => Auth::id(),
                'endpoint' => $endpoint,
                'method' => $request->method(),
                'request_data' => $requestData,
                'query_params' => $request->query->all(),
                'headers' => array_filter($headers),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_type' => $deviceType,
                'response_status' => $response->getStatusCode(),
                'response_time' => round($responseTime, 3),
                'error_message' => $response->getStatusCode() >= 400 ? $this->extractErrorMessage($response) : null,
                'action_type' => $actionType,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'session_id' => session()->getId(),
            ]);
        } catch (\Exception $e) {
            // Log error but don't throw to prevent disrupting API responses
            Log::error('API tracking failed: ' . $e->getMessage(), [
                'endpoint' => $request->path(),
                'method' => $request->method(),
            ]);
        }
    }

    /**
     * Extract resource ID from route parameters
     */
    protected function extractResourceId(Request $request): ?string
    {
        $route = $request->route();
        if (!$route) {
            return null;
        }

        $parameters = $route->parameters();
        
        // Common parameter names for resources
        $resourceParams = ['id', 'shop', 'city', 'category', 'product', 'service', 'shopId', 'cityId'];
        
        foreach ($resourceParams as $param) {
            if (isset($parameters[$param])) {
                $value = $parameters[$param];
                // If it's a model, get its ID
                return is_object($value) && method_exists($value, 'getKey') 
                    ? (string) $value->getKey() 
                    : (string) $value;
            }
        }

        return null;
    }

    /**
     * Sanitize request data to remove sensitive information
     */
    protected function sanitizeRequestData(array $data): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'token',
            'api_key',
            'secret',
            'credit_card',
            'cvv',
            'ssn',
        ];

        foreach ($sensitiveFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = '***REDACTED***';
            }
        }

        return $data;
    }

    /**
     * Extract error message from response
     */
    protected function extractErrorMessage(Response $response): ?string
    {
        try {
            $content = $response->getContent();
            if (!$content) {
                return null;
            }

            $decoded = json_decode($content, true);
            
            // Try common error message fields
            if (isset($decoded['message'])) {
                return substr($decoded['message'], 0, 500);
            }
            
            if (isset($decoded['error'])) {
                return is_string($decoded['error']) 
                    ? substr($decoded['error'], 0, 500) 
                    : substr(json_encode($decoded['error']), 0, 500);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
