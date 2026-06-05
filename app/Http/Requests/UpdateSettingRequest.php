<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => ['nullable', 'string', 'max:255'],
            'title'           => ['nullable', 'string', 'max:255'],
            'description'     => ['nullable', 'string', 'max:500'],
            'keywords'        => ['nullable', 'string', 'max:500'],
            'logo'            => ['nullable', 'string', 'max:500'],
            'favicon'         => ['nullable', 'string', 'max:500'],
            'site_url'        => ['nullable', 'string', 'max:500'],
            'copyright'       => ['nullable', 'string', 'max:500'],
            'timezone'        => ['nullable', 'string', 'max:100'],
            'maintenance'     => ['nullable'],
            'author_bio'      => ['nullable'],
            'comments'        => ['nullable'],
            'registration'    => ['nullable'],
            'comment_approval'=> ['nullable'],
            'newsletter'      => ['nullable'],
            'social_login'    => ['nullable'],
            'search'          => ['nullable'],
            'cache'           => ['nullable'],
            'cache_duration'  => ['nullable', 'integer'],
            'minification'    => ['nullable'],
            'lazy_load'       => ['nullable'],
            'cdn'             => ['nullable'],
            'cdn_url'         => ['nullable', 'string', 'max:500'],
            'max_upload_size' => ['nullable', 'integer'],
            'file_types'      => ['nullable'],
            'meta_title'      => ['nullable', 'string', 'max:255'],
            'meta_description'=> ['nullable', 'string', 'max:500'],
            'meta_keywords'   => ['nullable', 'string', 'max:500'],
            'google_analytics'=> ['nullable', 'string', 'max:500'],
            'baidu_analytics' => ['nullable', 'string', 'max:500'],
            'canonical_url'   => ['nullable', 'string', 'max:500'],
            'og_image'        => ['nullable', 'string', 'max:500'],
            'og_type'         => ['nullable', 'string', 'max:50'],
            'twitter_card'    => ['nullable', 'string', 'max:50'],
            'sitemap'         => ['nullable'],
            'robots'          => ['nullable'],
            'llm_txt'         => ['nullable'],
            'rss_feed'        => ['nullable'],
            // 用户通知设置
            'email_notifications'     => ['nullable', 'boolean'],
            'comment_approval_alert'  => ['nullable', 'boolean'],
            'new_user_alert'          => ['nullable', 'boolean'],
            'weekly_report'           => ['nullable', 'boolean'],
            'digest_email'            => ['nullable', 'boolean'],
            'digest_frequency'        => ['nullable', 'string', 'in:daily,weekly,monthly'],
        ];
    }
}
