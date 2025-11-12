<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'event_type',
        'event_category',
        'event_action',
        'event_label',
        'event_data',
        'page_url',
        'page_title',
        'referrer',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'ip_address',
        'city_id',
        'shop_id',
        'category_id',
        'time_on_page',
        'scroll_depth',
    ];

    protected $casts = [
        'event_data' => 'array',
        'time_on_page' => 'integer',
        'scroll_depth' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
