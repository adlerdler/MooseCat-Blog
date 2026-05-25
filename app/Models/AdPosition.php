<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdPosition extends Model
{
    protected $fillable = [
        'name',
        'label_key',
        'description',
        'default_width',
        'default_height',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_width' => 'integer',
        'default_height' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the advertisements for the position.
     */
    public function advertisements(): HasMany
    {
        return $this->hasMany(Advertisement::class, 'position_id');
    }

    /**
     * Scope for active positions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
