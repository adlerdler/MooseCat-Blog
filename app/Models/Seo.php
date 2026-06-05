<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Seo extends Model
{
    use LogsActivity;
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
        'rss_feed',
        'canonical_url',
        'og_image',
        'og_type',
        'twitter_card',
    ];

    protected $casts = [
        'sitemap' => 'boolean',
        'robots' => 'boolean',
        'llm_txt' => 'boolean',
        'rss_feed' => 'boolean',
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('seo');
    }
}
