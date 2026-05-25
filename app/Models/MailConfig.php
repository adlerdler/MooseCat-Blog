<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailConfig extends Model
{
    protected $fillable = [
        'mailer',
        'host',
        'port',
        'username',
        'password',
        'encryption',
        'from_address',
        'from_name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'encrypted',
    ];

    /**
     * Get the active mail configuration.
     */
    public static function getActiveConfig()
    {
        return static::where('is_active', true)->first();
    }
}
