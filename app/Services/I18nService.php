<?php

namespace App\Services;

use App\Models\Language;
use Illuminate\Support\Facades\File;

class I18nService
{
    protected string $localesPath;

    public function __construct()
    {
        $this->localesPath = public_path('locales');
        File::ensureDirectoryExists($this->localesPath);
    }

    // ─── Languages (DB) ─────────────────────────────────────────

    /**
     * 获取所有语言（含活跃状态）
     */
    public function getLanguages(): array
    {
        return Language::orderBy('sort_order')->get()->map(function ($lang) {
            $count = $this->getTranslationCount($lang->code);
            return [
                'code'        => $lang->code,
                'name'        => $lang->name,
                'native_name' => $lang->native_name,
                'flag'        => $lang->flag,
                'file_path'   => $lang->file_path ?? "/locales/{$lang->code}.json",
                'direction'   => $lang->direction,
                'is_default'  => (bool) $lang->is_default,
                'is_active'   => (bool) $lang->is_active,
                'sort_order'  => (int) $lang->sort_order,
                'translation_count' => $count,
            ];
        })->toArray();
    }

    /**
     * 获取单个语言
     */
    public function getLanguage(string $code): ?Language
    {
        return Language::where('code', $code)->first();
    }

    /**
     * 添加语言
     */
    public function addLanguage(array $data): Language
    {
        $code = $data['code'];

        // 如果设为默认，先取消其他默认
        if (!empty($data['is_default'])) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $language = Language::create([
            'code'        => $code,
            'name'        => $data['name'],
            'native_name' => $data['native_name'] ?? $data['name'],
            'flag'        => $data['flag'] ?? '🌐',
            'file_path'   => $data['file_path'] ?? "/locales/{$code}.json",
            'direction'   => $data['direction'] ?? 'ltr',
            'is_default'  => (bool) ($data['is_default'] ?? false),
            'is_active'   => (bool) ($data['is_active'] ?? true),
            'sort_order'  => (int) ($data['sort_order'] ?? 0),
        ]);

        // 如果 JSON 文件不存在则创建空文件（上传时已存在的文件不覆盖）
        $jsonPath = $this->getJsonPath($code);
        if (!File::exists($jsonPath)) {
            $this->saveTranslations($code, []);
        }

        return $language;
    }

    /**
     * 更新语言元数据
     */
    public function updateLanguage(string $code, array $data): ?Language
    {
        $language = Language::where('code', $code)->first();
        if (!$language) return null;

        // 如果设为默认，先取消其他默认
        if (!empty($data['is_default']) && !$language->is_default) {
            Language::where('is_default', true)->update(['is_default' => false]);
        }

        $language->update([
            'name'        => $data['name'] ?? $language->name,
            'native_name' => $data['native_name'] ?? $language->native_name,
            'flag'        => $data['flag'] ?? $language->flag,
            'file_path'   => $data['file_path'] ?? $language->file_path,
            'direction'   => $data['direction'] ?? $language->direction,
            'is_default'  => array_key_exists('is_default', $data) ? (bool) $data['is_default'] : $language->is_default,
            'is_active'   => array_key_exists('is_active', $data) ? (bool) $data['is_active'] : $language->is_active,
            'sort_order'  => $data['sort_order'] ?? $language->sort_order,
        ]);

        return $language->fresh();
    }

    /**
     * 删除语言
     */
    public function deleteLanguage(string $code): bool
    {
        $language = Language::where('code', $code)->first();
        if (!$language) return false;

        // 不允许删除默认语言
        if ($language->is_default) return false;

        // 删除 JSON 文件
        $filePath = $this->localesPath . "/{$code}.json";
        if (File::exists($filePath)) {
            File::delete($filePath);
        }

        $language->delete();
        return true;
    }

    // ─── Translations (JSON files) ──────────────────────────────

    /**
     * 加载指定语言的翻译 JSON
     */
    public function loadTranslations(string $code): array
    {
        $filePath = $this->getJsonPath($code);
        if (!File::exists($filePath)) return [];

        $content = File::get($filePath);
        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * 保存翻译到 JSON 文件
     */
    public function saveTranslations(string $code, array $data): void
    {
        $filePath = $this->getJsonPath($code);
        File::put(
            $filePath,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * 获取翻译条目数
     */
    public function getTranslationCount(string $code): int
    {
        return count($this->loadTranslations($code));
    }

    /**
     * 获取 JSON 文件路径
     */
    protected function getJsonPath(string $code): string
    {
        return $this->localesPath . "/{$code}.json";
    }
}
