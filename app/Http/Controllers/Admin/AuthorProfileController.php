<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthorProfileController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $profiles = $this->mockDataService->getAuthorProfiles();
        
        return Inertia::render('admin/AuthorProfiles', [
            'profiles' => $profiles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer',
            'slug' => 'required|string|unique:author_profiles,slug',
            'bio' => 'nullable|string',
            'role_label' => 'nullable|string|max:100',
            'role_title' => 'nullable|string|max:100',
        ]);

        return back()->with('success', '作者资料已创建');
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'slug' => 'required|string',
            'bio' => 'nullable|string',
            'role_label' => 'nullable|string|max:100',
            'role_title' => 'nullable|string|max:100',
        ]);

        return back()->with('success', '作者资料已更新');
    }

    public function destroy(string $id)
    {
        return back()->with('success', '作者资料已删除');
    }
}
