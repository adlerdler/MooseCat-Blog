<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        // 基本信息
        'name',
        'description',
        'site_url',
        'copyright',
        'logo',
        'favicon',
        'timezone',
        
        // 功能开关
        'maintenance',
        'author_bio',
        'comments',
        'registration',
        'comment_approval',
        'newsletter',
        'social_login',
        'search',
        
        // 性能优化
        'cache',
        'cache_duration',
        'minification',
        'lazy_load',
        'cdn',
        'cdn_url',
        
        // 文件上传
        'max_upload_size',
        'file_types',
    ];

    protected $casts = [
        // 布尔值
        'maintenance' => 'boolean',
        'author_bio' => 'boolean',
        'comments' => 'boolean',
        'registration' => 'boolean',
        'comment_approval' => 'boolean',
        'newsletter' => 'boolean',
        'social_login' => 'boolean',
        'search' => 'boolean',
        'cache' => 'boolean',
        'minification' => 'boolean',
        'lazy_load' => 'boolean',
        'cdn' => 'boolean',
        
        // 整数
        'cache_duration' => 'integer',
        'max_upload_size' => 'integer',
        
        // JSON
        'file_types' => 'array',
    ];

    /**
     * Get the site settings.
     */
    public static function getSettings()
    {
        return static::first();
    }

    /**
     * Update or create site settings.
     */
    public static function updateSettings(array $data): self
    {
        $settings = static::first();
        if ($settings) {
            $settings->update($data);
        } else {
            $settings = static::create($data);
        }
        return $settings;
    }
}
