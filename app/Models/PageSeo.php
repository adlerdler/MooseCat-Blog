<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSeo extends Model
{
    protected $table = 'page_seo';

    protected $fillable = [
        'page_key',
        'title',
        'description',
        'keywords',
        'og_image',
    ];

    /**
     * Get SEO data by page key.
     */
    public static function getByPageKey(string $pageKey)
    {
        return static::where('page_key', $pageKey)->first();
    }
}
