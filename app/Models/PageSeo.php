<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PageSeo extends Model
{
    use LogsActivity;
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('seo');
    }
}
