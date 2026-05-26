<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Media Controller
 * 
 * Handles media file management.
 * Provides functionality for uploading, viewing, and deleting media files.
 */
class MediaController extends Controller
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
     * Display the media library
     * 
     * @return Response
     */
    public function index(): Response
    {
        $media = $this->mockDataService->getMedia();
        
        return Inertia::render('admin/Media', [
            'media' => $media,
        ]);
    }

    /**
     * Store a newly uploaded media file
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'file' => 'required|file',
        ]);

        return back()->with('success', '文件已上传');
    }

    /**
     * Remove the specified media file
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        return back()->with('success', '文件已删除');
    }
}