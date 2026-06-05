<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SocialLoginConfig extends Model
{
    use LogsActivity;
    protected $fillable = [
        'provider',
        'name',
        'client_id',
        'client_secret',
        'redirect_uri',
        'enabled',
        'extra_config',
    ];

    protected $casts = [
        'enabled'       => 'boolean',
        'extra_config'  => 'array',
        'client_secret' => 'encrypted',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('social-login')
            ->logExcept(['client_secret']);
    }
}
