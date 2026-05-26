<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * I18n Controller
 * 
 * Handles internationalization management.
 * Provides functionality for managing language settings and translations.
 */
class I18nController extends Controller
{
    protected $mockDataService;

    /**
     * Constructor
     * 
     * @param MockDataService $mockDataService
     */
    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    /**
     * Display the internationalization settings page
     * 
     * @return Response
     */
    public function index(): Response
    {
        $i18nConfig = $this->mockDataService->getI18nConfig();

        return Inertia::render('admin/I18nManager', [
            'i18nConfig' => ['languages' => $i18nConfig],
        ]);
    }

    /**
     * Update internationalization configuration
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        return back()->with('success', '国际化配置已更新');
    }
}