<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JournalsController extends Controller
{
    protected $mockDataService;

    public function __construct(MockDataService $mockDataService)
    {
        $this->mockDataService = $mockDataService;
    }

    public function index(): Response
    {
        $journals = $this->mockDataService->getJournals();
        
        return Inertia::render('admin/Journals', [
            'journals' => $journals,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/Journals');
    }

    public function store(Request $request)
    {
        // 日记创建逻辑
        return back()->with('success', '日记已创建');
    }

    public function edit(string $id): Response
    {
        $journals = $this->mockDataService->getJournals();
        $journal = collect($journals)->firstWhere('id', $id);
        
        return Inertia::render('admin/Journals', [
            'journal' => $journal,
        ]);
    }

    public function update(Request $request, string $id)
    {
        return back()->with('success', '日记已更新');
    }

    public function destroy(string $id)
    {
        return back()->with('success', '日记已删除');
    }
}