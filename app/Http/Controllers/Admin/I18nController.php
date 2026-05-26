<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class I18nController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $i18nConfig = $this->mockDataService->getI18nConfig();
        
        return Inertia::render('admin/I18nManager', [
            'i18nConfig' => $i18nConfig,
        ]);
    }

    public function update(Request $request)
    {
        return back()->with('success', '国际化配置已更新');
    }
}