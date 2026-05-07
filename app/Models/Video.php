<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title',
        'description',
        'video_id',
        'platform',
        'thumbnail',
        'duration',
        'views_count',
        'likes_count',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
