<?php

namespace App\Http\Controllers\Admin;

use App\Events\SeoFilesNeedRegenerate;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

/**
 * Settings Controller
 * 
 * Handles system settings management.
 * Provides functionality for managing site configuration, themes, SEO, and internationalization.
 */
class SettingsController extends Controller
{
    protected $settingService;

    /**
     * Constructor
     * 
     * @param SettingService $settingService
     */
    public function __construct(SettingService $settingService)
    {
        $this->middleware('permission:manage_settings');
        $this->settingService = $settingService;
    }

    /**
     * Display the settings page
     * 
     * @return Response
     */
    public function index(): Response
    {
        $siteConfig = $this->settingService->getSiteConfig();
        $seoConfig = $this->settingService->getSeoConfig();
        $commentConfig = $this->settingService->getCommentConfig();
        $allSettings = $this->settingService->getAll();
        $user = Auth::user();
        
        $mediaItems = SpatieMedia::where('collection_name', 'default')
            ->latest()
            ->get()
            ->map(function (SpatieMedia $item) {
                $ext = pathinfo($item->file_name, PATHINFO_EXTENSION);
                return [
                    'id'   => $item->uuid,
                    'name' => $item->name,
                    'type' => str_starts_with($item->mime_type ?? '', 'image/') ? 'image' :
                             (str_starts_with($item->mime_type ?? '', 'video/') ? 'video' : 'document'),
                    'size' => $this->formatSize($item->size ?? 0),
                    'url'  => url("/media/{$item->uuid}" . ($ext ? ".{$ext}" : '')),
                    'date' => $item->created_at->format('Y-m-d'),
                ];
            });

        return Inertia::render('admin/Settings', [
            'siteConfig' => array_merge($siteConfig, [
                'site_url' => $allSettings['site_url'] ?? '',
                'copyright' => $allSettings['copyright'] ?? '',
                'timezone' => $allSettings['timezone'] ?? 'Asia/Shanghai',
                'maintenance' => (bool) ($allSettings['maintenance'] ?? false),
                'author_bio' => (bool) ($allSettings['author_bio'] ?? false),
                'comments' => (bool) ($allSettings['comments'] ?? true),
                'registration' => (bool) ($allSettings['registration'] ?? true),
                'comment_approval' => (bool) ($allSettings['comment_approval'] ?? false),
                'newsletter' => (bool) ($allSettings['newsletter'] ?? true),
                'social_login' => (bool) ($allSettings['social_login'] ?? false),
                'search' => (bool) ($allSettings['search'] ?? true),
                'cache' => (bool) ($allSettings['cache'] ?? true),
                'cache_duration' => (int) ($allSettings['cache_duration'] ?? 3600),
                'minification' => (bool) ($allSettings['minification'] ?? true),
                'lazy_load' => (bool) ($allSettings['lazy_load'] ?? true),
                'cdn' => (bool) ($allSettings['cdn'] ?? false),
                'cdn_url' => $allSettings['cdn_url'] ?? '',
                'max_upload_size' => (int) ($allSettings['max_upload_size'] ?? 10),
                'file_types' => $allSettings['file_types'] ?? ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx'],
            ]),
            'userNotifications' => $user ? [
                'email_notifications' => (bool) $user->notifications,
                'comment_approval_alert' => (bool) $user->comment_approval_alert,
                'new_user_alert' => (bool) $user->new_user_alert,
                'weekly_report' => (bool) $user->weekly_report,
                'digest_email' => (bool) $user->digest_email,
                'digest_frequency' => $user->digest_frequency ?? 'weekly',
            ] : [],
            'seoConfig' => $seoConfig,
            'commentConfig' => $commentConfig,
            'themes' => \App\Models\Theme::orderBy('sort_order')->get(),
            'media' => $mediaItems,
        ]);
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        $k = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Update system settings
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateSettingRequest $request)
    {
        $validated = $request->validated();

        $settingsToSave = [];
        $seoToSave = [];
        $booleanFields = [
            'maintenance', 'author_bio', 'comments', 'registration',
            'comment_approval', 'newsletter', 'social_login', 'search',
            'cache', 'minification', 'lazy_load', 'cdn',
        ];
        $seoFields = [
            'meta_title', 'meta_description', 'meta_keywords',
            'google_analytics', 'baidu_analytics', 'canonical_url',
            'og_image', 'og_type', 'twitter_card',
        ];
        $seoBooleanFields = ['sitemap', 'robots', 'llm_txt', 'rss_feed'];
        $userNotificationFields = [
            'email_notifications', 'comment_approval_alert', 'new_user_alert',
            'weekly_report', 'digest_email', 'digest_frequency'
        ];

        foreach ($validated as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            if (in_array($key, $seoFields)) {
                $seoToSave[$key] = $value;
            } elseif (in_array($key, $seoBooleanFields)) {
                $seoToSave[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            } elseif (in_array($key, $booleanFields)) {
                $settingsToSave[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            } elseif (is_array($value)) {
                $settingsToSave[$key] = json_encode($value);
            } else {
                $settingsToSave[$key] = $value;
            }
        }

        if (!empty($settingsToSave)) {
            $this->settingService->setMany($settingsToSave);
        }

        if (!empty($seoToSave)) {
            \App\Models\Seo::updateGlobalSeo($seoToSave);
        }

        // 触发 SEO 文件重新生成事件
        SeoFilesNeedRegenerate::dispatch();

        // 保存用户级通知设置
        $user = Auth::user();
        if ($user) {
            $userData = [];
            foreach ($userNotificationFields as $field) {
                if (isset($validated[$field])) {
                    if ($field === 'digest_frequency') {
                        $userData[$field] = $validated[$field];
                    } elseif ($field === 'email_notifications') {
                        $userData['notifications'] = filter_var($validated[$field], FILTER_VALIDATE_BOOLEAN);
                    } else {
                        $userData[$field] = filter_var($validated[$field], FILTER_VALIDATE_BOOLEAN);
                    }
                }
            }
            if (!empty($userData)) {
                $user->update($userData);
            }
        }

        return back()->with('success', '设置已更新');
    }

    public function storeTheme(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:themes,name',
            'label' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'preview_image' => 'nullable|string|max:500',
        ]);

        if ($validated['is_default']) {
            \App\Models\Theme::where('is_default', true)->update(['is_default' => false]);
        }

        $theme = \App\Models\Theme::create($validated);

        return back()->with('success', '主题已添加');
    }

    public function updateTheme(Request $request, $id)
    {
        $theme = \App\Models\Theme::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:themes,name,' . $id,
            'label' => 'required|string|max:255',
            'color' => 'required|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'preview_image' => 'nullable|string|max:500',
        ]);

        if ($validated['is_default']) {
            \App\Models\Theme::where('is_default', true)->where('id', '!=', $id)->update(['is_default' => false]);
        }

        $theme->update($validated);

        return back()->with('success', '主题已更新');
    }

    public function deleteTheme($id)
    {
        $theme = \App\Models\Theme::findOrFail($id);
        $theme->delete();

        return back()->with('success', '主题已删除');
    }
}