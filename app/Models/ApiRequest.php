<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'endpoint',
        'method',
        'request_data',
        'query_params',
        'headers',
        'ip_address',
        'user_agent',
        'device_type',
        'response_status',
        'response_time',
        'error_message',
        'action_type',
        'resource_type',
        'resource_id',
        'session_id',
    ];

    protected $casts = [
        'request_data' => 'array',
        'query_params' => 'array',
        'headers' => 'array',
        'response_time' => 'float',
        'response_status' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes for analytics
    public function scopeSuccessful($query)
    {
        return $query->whereBetween('response_status', [200, 299]);
    }

    public function scopeFailed($query)
    {
        return $query->where('response_status', '>=', 400);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeLastDays($query, int $days)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByEndpoint($query, string $endpoint)
    {
        return $query->where('endpoint', 'like', "%{$endpoint}%");
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('method', strtoupper($method));
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper to determine action type from endpoint
    public static function detectActionType(string $method, string $endpoint): string
    {
        $method = strtoupper($method);
        
        // Pattern matching for common actions
        if (str_contains($endpoint, '/search')) {
            return 'search';
        } elseif (str_contains($endpoint, '/login') || str_contains($endpoint, '/register')) {
            return 'authentication';
        } elseif (str_contains($endpoint, '/favorite')) {
            return 'favorite';
        } elseif (str_contains($endpoint, '/rating') || str_contains($endpoint, '/review')) {
            return 'review';
        } elseif (str_contains($endpoint, '/contact')) {
            return 'contact';
        } elseif ($method === 'GET' && preg_match('/\/(\d+)$/', $endpoint)) {
            return 'view';
        } elseif ($method === 'GET') {
            return 'list';
        } elseif ($method === 'POST') {
            return 'create';
        } elseif ($method === 'PUT' || $method === 'PATCH') {
            return 'update';
        } elseif ($method === 'DELETE') {
            return 'delete';
        }
        
        return 'unknown';
    }

    // Helper to extract resource type from endpoint
    public static function detectResourceType(string $endpoint): ?string
    {
        if (str_contains($endpoint, '/shops')) {
            return 'shop';
        } elseif (str_contains($endpoint, '/cities')) {
            return 'city';
        } elseif (str_contains($endpoint, '/categories')) {
            return 'category';
        } elseif (str_contains($endpoint, '/products')) {
            return 'product';
        } elseif (str_contains($endpoint, '/services')) {
            return 'service';
        } elseif (str_contains($endpoint, '/marketplace')) {
            return 'marketplace';
        } elseif (str_contains($endpoint, '/news')) {
            return 'news';
        } elseif (str_contains($endpoint, '/forums')) {
            return 'forum';
        } elseif (str_contains($endpoint, '/user')) {
            return 'user';
        }
        
        return null;
    }
}
