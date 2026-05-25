<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    protected $table = 'seo';

    protected $fillable = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'google_analytics',
        'baidu_analytics',
        'sitemap',
        'robots',
        'llm_txt',
        'canonical_url',
        'og_image',
        'og_type',
        'twitter_card',
    ];

    protected $casts = [
        'sitemap' => 'boolean',
        'robots' => 'boolean',
        'llm_txt' => 'boolean',
    ];

    /**
     * Get the global SEO configuration.
     */
    public static function getGlobalSeo()
    {
        return static::first();
    }

    /**
     * Update or create global SEO configuration.
     */
    public static function updateGlobalSeo(array $data): self
    {
        $seo = static::first();
        if ($seo) {
            $seo->update($data);
        } else {
            $seo = static::create($data);
        }
        return $seo;
    }
}
