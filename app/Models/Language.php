<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag',
        'file_path',
        'direction',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the translations for the language.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class, 'locale', 'code');
    }

    /**
     * Scope for active languages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Get the default language.
     */
    public static function getDefaultLanguage()
    {
        return static::where('is_default', true)->first();
    }
}
