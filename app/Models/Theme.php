<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Theme extends Model
{
    use LogsActivity;
    protected $fillable = [
        'name',
        'label',
        'color',
        'sort_order',
        'is_active',
        'is_default',
        'preview_image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the active theme.
     */
    public static function getActiveTheme()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Get the default theme.
     */
    public static function getDefaultTheme()
    {
        return static::where('is_default', true)->first();
    }

    /**
     * Scope for active themes.
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
            ->useLogName('themes');
    }
}
