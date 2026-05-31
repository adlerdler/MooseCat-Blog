<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get all the posts that are assigned this tag.
     */
    public function posts(): MorphToMany
    {
        return $this->morphedByMany(Post::class, 'taggable');
    }

    /**
     * Get all the projects that are assigned this tag.
     */
    public function projects(): MorphToMany
    {
        return $this->morphedByMany(Project::class, 'taggable');
    }

    /**
     * Get all the resources that are assigned this tag.
     */
    public function resources(): MorphToMany
    {
        return $this->morphedByMany(Resource::class, 'taggable');
    }

    /**
     * Get all the videos that are assigned this tag.
     */
    public function videos(): MorphToMany
    {
        return $this->morphedByMany(Video::class, 'taggable');
    }
}
