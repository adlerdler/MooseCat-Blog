<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class UserLevel extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name',
        'level',
        'min_points',
        'max_points',
        'discount',
        'color',
        'icon',
        'description',
        'benefits',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'level' => 'integer',
        'min_points' => 'integer',
        'max_points' => 'integer',
        'discount' => 'integer',
        'sort_order' => 'integer',
        'benefits' => 'array',
    ];

    /**
     * Get the users for the level.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'level_id');
    }

    /**
     * Get the level for given points.
     */
    public static function getLevelForPoints(int $points)
    {
        return static::where('min_points', '<=', $points)
            ->where('max_points', '>=', $points)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Scope for active levels.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('user-levels');
    }
}
