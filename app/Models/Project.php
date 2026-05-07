<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Project extends Model
{
    protected $fillable = [
        'title',
        'description',
        'long_description',
        'client',
        'role',
        'year',
        'image',
        'url',
        'github_url',
        'technologies',
        'status',
        'sort_order',
        'views_count',
        'likes_count',
    ];

    protected $casts = [
        'technologies' => 'array',
    ];

    /**
     * Get the tags for the project.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }
}
