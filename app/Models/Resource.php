<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Resource extends Model
{
    use LogsActivity;
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'format',
        'file_size',
        'image',
        'direct_link',
        'drives',
        'downloads_count',
        'likes_count',
    ];

    protected $casts = [
        'drives' => 'array',
    ];

    /**
     * Get the category of the resource.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the tags for the resource.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('resources');
    }
}
