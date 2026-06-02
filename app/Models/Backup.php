<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    protected $fillable = [
        'filename',
        'size',
        'status',
        'type',
        'disk',
        'note',
        'is_scheduled',
        'schedule_time',
        'started_at',
        'completed_at',
        'error_message',
    ];

    protected $casts = [
        'is_scheduled' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'schedule_time' => 'datetime',
    ];

    /**
     * Scope for successful backups.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }
}
