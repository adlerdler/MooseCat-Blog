<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    private MockDataService $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(Request $request): Response
    {
        // 使用模拟数据 - 先对接前端
        $media = $this->mockDataService->getMedia();

        return Inertia::render('admin/Media', [
            'media' => $media,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        // TODO: 先使用模拟逻辑，后续对接真实功能
        return response()->json([
            'message' => '文件上传成功',
        ], 201);
    }

    public function destroy(string $id): JsonResponse
    {
        // TODO: 先使用模拟逻辑，后续对接真实功能
        return response()->json([
            'message' => '文件删除成功',
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        // 使用模拟数据 - 先对接前端
        $media = $this->mockDataService->getMedia();

        return response()->json([
            'media' => $media,
        ]);
    }
}
