<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJournalRequest;
use App\Http\Requests\UpdateJournalRequest;
use App\Models\Journal;
use App\Models\User;
use App\Services\JournalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class JournalsController extends Controller
{
    protected JournalService $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index(Request $request): Response
    {
        $filters = $request->only(['user_id', 'is_public', 'search']);

        $journals = Journal::query()
            ->with(['user'])
            ->when($filters['user_id'] ?? null, fn($q, $id) => $q->where('user_id', $id))
            ->when($filters['is_public'] ?? null, fn($q, $v) => $q->where('is_public', $v === 'true' || $v === true))
            ->when($filters['search'] ?? null, fn($q, $s) => $q->where('title', 'like', "%{$s}%")->orWhere('content', 'like', "%{$s}%"))
            ->latest('date')
            ->get()
            ->map(fn($j) => [
                'id' => $j->id,
                'user_id' => $j->user_id,
                'user_name' => $j->user?->name,
                'title' => $j->title,
                'content' => $j->content,
                'mood' => $j->mood,
                'weather' => $j->weather,
                'date' => $j->date?->format('Y-m-d'),
                'is_public' => $j->is_public,
                'likes_count' => $j->likes_count,
                'created_at' => $j->created_at?->format('Y-m-d'),
            ]);

        $users = User::all()->map(fn($u) => ['id' => $u->id, 'name' => $u->name]);

        return Inertia::render('admin/Journals', [
            'journals' => $journals,
            'users' => $users,
            'filters' => $filters,
        ]);
    }

    public function store(StoreJournalRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        $this->journalService->createJournal($data);

        return back()->with('success', '日记创建成功');
    }

    public function edit(Journal $journal): Response
    {
        $users = User::all()->map(fn($u) => ['id' => $u->id, 'name' => $u->name]);

        return Inertia::render('admin/Journals', [
            'journal' => [
                'id' => $journal->id,
                'user_id' => $journal->user_id,
                'title' => $journal->title ?? '',
                'content' => $journal->content,
                'mood' => $journal->mood ?? '',
                'weather' => $journal->weather ?? '',
                'date' => $journal->date?->format('Y-m-d'),
                'is_public' => $journal->is_public,
            ],
            'users' => $users,
        ]);
    }

    public function update(UpdateJournalRequest $request, Journal $journal): RedirectResponse
    {
        $data = $request->validated();
        $this->journalService->updateJournal($journal, $data);

        return back()->with('success', '日记更新成功');
    }

    public function destroy(Journal $journal): RedirectResponse
    {
        $this->journalService->deleteJournal($journal);

        return back()->with('success', '日记删除成功');
    }
}