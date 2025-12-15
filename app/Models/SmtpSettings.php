<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmtpSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'host',
        'port',
        'encryption',
        'username',
        'password',
        'from_address',
        'from_name',
        'notification_emails',
        'is_active',
        'last_tested_at',
        'test_successful',
        'test_error',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'test_successful' => 'boolean',
        'last_tested_at' => 'datetime',
        'notification_emails' => 'array',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Get the active SMTP settings
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Get decrypted password
     */
    public function getDecryptedPassword()
    {
        return decrypt($this->password);
    }

    /**
     * Set encrypted password
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = encrypt($value);
    }
}
