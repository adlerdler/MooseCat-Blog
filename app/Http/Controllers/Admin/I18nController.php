<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\I18nService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;

class I18nController extends Controller
{
    protected I18nService $i18nService;

    public function __construct(I18nService $i18nService)
    {
        $this->middleware('permission:manage_i18n');
        $this->i18nService = $i18nService;
    }

    /**
     * 显示 i18n 管理页面
     */
    public function index(): Response
    {
        $languages = $this->i18nService->getLanguages();

        // 构建 translations 数据 (格式: { en: {...}, zh: {...} })
        $translations = [];
        foreach ($languages as $lang) {
            $translations[$lang['code']] = $this->i18nService->loadTranslations($lang['code']);
        }

        return Inertia::render('admin/I18nManager', [
            'i18nConfig' => [
                'languages'    => $languages,
                'translations' => $translations,
            ],
        ]);
    }

    // ─── Translations ───────────────────────────────────────────

    /**
     * 保存翻译 (全量覆盖)
     */
    public function saveTranslations(Request $request): JsonResponse
    {
        $request->validate([
            'translations' => 'required|array',
        ]);

        foreach ($request->input('translations', []) as $code => $data) {
            // 验证语言存在
            if (!$this->i18nService->getLanguage($code)) continue;

            $this->i18nService->saveTranslations($code, (array) $data);
        }

        return response()->json(['message' => '翻译已保存']);
    }

    // ─── Languages ──────────────────────────────────────────────

    /**
     * 上传语言 JSON 文件到 /locales/
     */
    public function uploadLocale(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string|max:10',
            'file' => 'required|file|mimes:json|max:2048',
        ]);

        $code = $request->input('code');
        $file = $request->file('file');

        // 确保 locales 目录存在
        $localesPath = public_path('locales');
        File::ensureDirectoryExists($localesPath);

        // 保存到 public/locales/{code}.json
        $file->move($localesPath, $code . '.json');

        return response()->json([
            'message'   => '文件已上传',
            'file_path' => '/locales/' . $code . '.json',
        ]);
    }

    /**
     * 添加语言
     */
    public function addLanguage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code'        => 'required|string|max:10|unique:languages,code',
            'name'        => 'required|string|max:100',
            'native_name' => 'nullable|string|max:100',
            'flag'        => 'nullable|string|max:20',
            'file_path'   => 'nullable|string|max:200',
            'direction'   => 'nullable|string|max:3',
            'is_default'  => 'boolean',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
        ]);

        $language = $this->i18nService->addLanguage($validated);

        return response()->json([
            'message'  => '语言已添加',
            'language' => [
                'code'        => $language->code,
                'name'        => $language->name,
                'native_name' => $language->native_name,
                'flag'        => $language->flag,
                'file_path'   => $language->file_path,
                'is_default'  => (bool) $language->is_default,
                'is_active'   => (bool) $language->is_active,
                'sort_order'  => (int) $language->sort_order,
            ],
        ], 201);
    }

    /**
     * 更新语言
     */
    public function updateLanguage(Request $request, string $code): JsonResponse
    {
        $validated = $request->validate([
            'name'        => 'sometimes|string|max:100',
            'native_name' => 'nullable|string|max:100',
            'flag'        => 'nullable|string|max:20',
            'file_path'   => 'nullable|string|max:200',
            'direction'   => 'nullable|string|max:3',
            'is_default'  => 'boolean',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
        ]);

        $language = $this->i18nService->updateLanguage($code, $validated);

        if (!$language) {
            return response()->json(['message' => '语言不存在'], 404);
        }

        return response()->json(['message' => '语言已更新']);
    }

    /**
     * 删除语言
     */
    public function deleteLanguage(string $code): JsonResponse
    {
        $success = $this->i18nService->deleteLanguage($code);

        if (!$success) {
            return response()->json(['message' => '删除失败（语言不存在或为默认语言）'], 400);
        }

        return response()->json(['message' => '语言已删除']);
    }
}
