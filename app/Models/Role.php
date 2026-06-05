<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use LogsActivity;
    protected $fillable = [
        'name',
        'value',
        'guard_name',
        'label',
        'color',
        'description',
    ];

    /**
     * Get role by value.
     */
    public static function getByValue(string $value)
    {
        return static::where('value', $value)->first();
    }

    /**
     * Scope for web guard.
     */
    public function scopeWeb($query)
    {
        return $query->where('guard_name', 'web');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('roles');
    }
}
