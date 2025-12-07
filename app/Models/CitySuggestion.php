<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CitySuggestion extends Model
{
    protected $fillable = [
        'city_name',
        'phone',
        'group_url',
        'ip_address',
        'user_agent',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for pending suggestions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved suggestions
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected suggestions
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
