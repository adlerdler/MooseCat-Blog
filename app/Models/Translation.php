<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = [
        'group',
        'key',
        'text',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'text' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope for active translations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Scope for a specific group.
     */
    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    /**
     * Get translation by key and locale.
     */
    public static function getTranslation(string $key, string $locale, string $default = '')
    {
        $translation = static::where('key', $key)
            ->where('is_active', true)
            ->first();
        
        if ($translation && isset($translation->text[$locale])) {
            return $translation->text[$locale];
        }
        
        return $default;
    }
}
