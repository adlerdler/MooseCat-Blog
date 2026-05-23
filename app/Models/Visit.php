<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    const UPDATED_AT = null;
    
    protected $fillable = [
        'visitable_id',
        'visitable_type',
        'ip_address',
        'user_agent',
        'referrer',
    ];
}
