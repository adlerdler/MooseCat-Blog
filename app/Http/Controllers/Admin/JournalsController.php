<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MockDataService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Journals Controller
 * 
 * Handles journal management operations.
 * Provides CRUD functionality for user journals/diaries.
 */
class JournalsController extends Controller
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
     * Display the journal list
     * 
     * @return Response
     */
    public function index(): Response
    {
        $journals = $this->mockDataService->getJournals();
        $users = $this->mockDataService->getUsers();

        return Inertia::render('admin/Journals', [
            'journals' => $journals,
            'users' => $users,
        ]);
    }

    /**
     * Display the create journal form
     * 
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('admin/Journals');
    }

    /**
     * Store a newly created journal
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        return back()->with('success', '日记已创建');
    }

    /**
     * Display the edit journal form
     * 
     * @param string $id
     * @return Response
     */
    public function edit(string $id): Response
    {
        $journals = $this->mockDataService->getJournals();
        $journal = collect($journals)->firstWhere('id', $id);
        
        return Inertia::render('admin/Journals', [
            'journal' => $journal,
        ]);
    }

    /**
     * Update the specified journal
     * 
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, string $id)
    {
        return back()->with('success', '日记已更新');
    }

    /**
     * Remove the specified journal
     * 
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        return back()->with('success', '日记已删除');
    }
}