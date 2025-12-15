<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiAnalyticsController extends Controller
{
    /**
     * Display API analytics dashboard
     */
    public function index(Request $request)
    {
        $days = $request->input('days', 7);
        $startDate = Carbon::now()->subDays($days);

        // Overview statistics
        $totalRequests = ApiRequest::where('created_at', '>=', $startDate)->count();
        $successfulRequests = ApiRequest::where('created_at', '>=', $startDate)
            ->successful()
            ->count();
        $failedRequests = ApiRequest::where('created_at', '>=', $startDate)
            ->failed()
            ->count();
        $uniqueUsers = ApiRequest::where('created_at', '>=', $startDate)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');
        $avgResponseTime = ApiRequest::where('created_at', '>=', $startDate)
            ->avg('response_time');

        // Most used endpoints
        $topEndpoints = ApiRequest::select('endpoint', 'method', DB::raw('COUNT(*) as count'), DB::raw('AVG(response_time) as avg_time'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('endpoint', 'method')
            ->orderByDesc('count')
            ->limit(20)
            ->get();

        // Slowest endpoints
        $slowestEndpoints = ApiRequest::select('endpoint', 'method', DB::raw('AVG(response_time) as avg_time'), DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('endpoint', 'method')
            ->havingRaw('COUNT(*) >= 5') // Only endpoints with at least 5 requests
            ->orderByDesc('avg_time')
            ->limit(10)
            ->get();

        // Most failed endpoints
        $failedEndpoints = ApiRequest::select('endpoint', 'method', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->failed()
            ->groupBy('endpoint', 'method')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Requests by action type
        $actionTypes = ApiRequest::select('action_type', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('action_type')
            ->groupBy('action_type')
            ->orderByDesc('count')
            ->get();

        // Requests by resource type
        $resourceTypes = ApiRequest::select('resource_type', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('resource_type')
            ->groupBy('resource_type')
            ->orderByDesc('count')
            ->get();

        // Requests by device type
        $deviceTypes = ApiRequest::select('device_type', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('device_type')
            ->groupBy('device_type')
            ->get();

        // Requests by HTTP method
        $methods = ApiRequest::select('method', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->groupBy('method')
            ->get();

        // Requests over time (daily)
        $requestsOverTime = ApiRequest::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN response_status >= 200 AND response_status < 300 THEN 1 ELSE 0 END) as successful'),
                DB::raw('SUM(CASE WHEN response_status >= 400 THEN 1 ELSE 0 END) as failed')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top users by request count
        $topUsers = ApiRequest::select('user_id', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(10)
            ->with('user:id,name,email')
            ->get();

        // Most common error messages
        $commonErrors = ApiRequest::select('error_message', 'endpoint', DB::raw('COUNT(*) as count'))
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('error_message')
            ->groupBy('error_message', 'endpoint')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Response status distribution
        $statusCodes = ApiRequest::select(
                DB::raw('FLOOR(response_status / 100) * 100 as status_group'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('status_group')
            ->orderBy('status_group')
            ->get()
            ->mapWithKeys(function ($item) {
                $label = match((int)$item->status_group) {
                    200 => '2xx Success',
                    300 => '3xx Redirect',
                    400 => '4xx Client Error',
                    500 => '5xx Server Error',
                    default => $item->status_group
                };
                return [$label => $item->count];
            });

        return view('admin.api-analytics', compact(
            'totalRequests',
            'successfulRequests',
            'failedRequests',
            'uniqueUsers',
            'avgResponseTime',
            'topEndpoints',
            'slowestEndpoints',
            'failedEndpoints',
            'actionTypes',
            'resourceTypes',
            'deviceTypes',
            'methods',
            'requestsOverTime',
            'topUsers',
            'commonErrors',
            'statusCodes',
            'days'
        ));
    }

    /**
     * Get detailed request information
     */
    public function show($id)
    {
        $request = ApiRequest::with('user')->findOrFail($id);
        
        return view('admin.api-request-detail', compact('request'));
    }

    /**
     * Get recent requests (for real-time monitoring)
     */
    public function recent(Request $request)
    {
        $limit = $request->input('limit', 50);
        
        $requests = ApiRequest::with('user:id,name')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * Export API analytics data
     */
    public function export(Request $request)
    {
        $days = $request->input('days', 7);
        $startDate = Carbon::now()->subDays($days);

        $requests = ApiRequest::where('created_at', '>=', $startDate)
            ->orderByDesc('created_at')
            ->get();

        $filename = 'api_analytics_' . now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($requests) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Date Time',
                'User ID',
                'Endpoint',
                'Method',
                'Action Type',
                'Resource Type',
                'Resource ID',
                'Response Status',
                'Response Time (ms)',
                'Device Type',
                'IP Address',
                'Error Message'
            ]);

            // Add data rows
            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->id,
                    $request->created_at,
                    $request->user_id,
                    $request->endpoint,
                    $request->method,
                    $request->action_type,
                    $request->resource_type,
                    $request->resource_id,
                    $request->response_status,
                    $request->response_time,
                    $request->device_type,
                    $request->ip_address,
                    $request->error_message
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get API usage by endpoint
     */
    public function endpointStats(Request $request)
    {
        $endpoint = $request->input('endpoint');
        $days = $request->input('days', 7);
        $startDate = Carbon::now()->subDays($days);

        $stats = ApiRequest::where('endpoint', 'like', "%{$endpoint}%")
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('COUNT(*) as total_requests'),
                DB::raw('AVG(response_time) as avg_response_time'),
                DB::raw('MIN(response_time) as min_response_time'),
                DB::raw('MAX(response_time) as max_response_time'),
                DB::raw('SUM(CASE WHEN response_status >= 200 AND response_status < 300 THEN 1 ELSE 0 END) as successful'),
                DB::raw('SUM(CASE WHEN response_status >= 400 THEN 1 ELSE 0 END) as failed')
            )
            ->first();

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
