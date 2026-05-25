<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSeo extends Model
{
    protected $fillable = [
        'route_name',
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
    ];

    /**
     * Get SEO data by route name.
     */
    public static function getByRoute(string $routeName)
    {
        return static::where('route_name', $routeName)->first();
    }
}
