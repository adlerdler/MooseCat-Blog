<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $media = $this->mockDataService->getMedia();
        
        return Inertia::render('admin/Media', [
            'media' => $media,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file',
        ]);

        return back()->with('success', '文件已上传');
    }

    public function destroy(string $id)
    {
        return back()->with('success', '文件已删除');
    }
}