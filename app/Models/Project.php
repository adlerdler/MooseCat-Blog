<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use LogsActivity;
    protected $fillable = [
        'title',
        'slug',
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

    /**
     * Get the visit records for the project.
     */
    public function visits(): MorphMany
    {
        return $this->morphMany(Visit::class, 'visitable');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('projects');
    }
}
