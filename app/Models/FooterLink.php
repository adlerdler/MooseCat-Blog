<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FooterLink extends Model
{
    protected $fillable = [
        'type',
        'platform',
        'icon_name',
        'label',
        'url',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Scope for active footer links.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Scope for social links.
     */
    public function scopeSocialLinks($query)
    {
        return $query->where('type', 'social_link');
    }

    /**
     * Scope for navigation links.
     */
    public function scopeNavLinks($query)
    {
        return $query->where('type', 'nav_link');
    }

    /**
     * Scope for category navigation links.
     */
    public function scopeCategoryLinks($query)
    {
        return $query->where('type', 'nav_link')->where('platform', 'categories');
    }

    /**
     * Scope for data links.
     */
    public function scopeDataLinks($query)
    {
        return $query->where('type', 'nav_link')->where('platform', 'data');
    }
}
