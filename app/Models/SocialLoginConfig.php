<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLoginConfig extends Model
{
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
}
