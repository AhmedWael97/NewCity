<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'city_id',
        'shop_id',
        'assigned_admin_id',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'attachments',
        'resolved_at',
        'closed_at',
        'resolution_notes',
        'satisfaction_rating',
        'satisfaction_feedback'
    ];

    protected $casts = [
        'attachments' => 'array',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    public function publicReplies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id')->where('is_internal_note', false);
    }

    public function internalNotes(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id')->where('is_internal_note', true);
    }

    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $lastTicket = self::whereYear('created_at', $year)->latest()->first();
        $number = $lastTicket ? intval(substr($lastTicket->ticket_number, -3)) + 1 : 1;
        return 'TICK-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'open' => '<span class="badge bg-primary">مفتوح</span>',
            'in_progress' => '<span class="badge bg-warning">قيد المعالجة</span>',
            'waiting_user' => '<span class="badge bg-info">في انتظار المستخدم</span>',
            'resolved' => '<span class="badge bg-success">تم الحل</span>',
            'closed' => '<span class="badge bg-secondary">مغلق</span>',
            default => '<span class="badge bg-light">غير معروف</span>'
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'low' => '<span class="badge bg-success">منخفض</span>',
            'medium' => '<span class="badge bg-warning">متوسط</span>',
            'high' => '<span class="badge bg-orange">عالي</span>',
            'urgent' => '<span class="badge bg-danger">عاجل</span>',
            default => '<span class="badge bg-light">غير محدد</span>'
        };
    }

    public function getCategoryNameAttribute(): string
    {
        return match($this->category) {
            'technical_issue' => 'مشكلة تقنية',
            'shop_complaint' => 'شكوى متجر',
            'payment_issue' => 'مشكلة دفع',
            'account_problem' => 'مشكلة حساب',
            'feature_request' => 'طلب ميزة',
            'bug_report' => 'بلاغ خطأ',
            'content_issue' => 'مشكلة محتوى',
            'other' => 'أخرى',
            default => 'غير محدد'
        };
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_admin_id');
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
        });
    }
}