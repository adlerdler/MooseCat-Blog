<?php

namespace App\Services;

use App\Models\Journal;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class JournalService
{
    public function getPaginatedJournals(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return Journal::query()
            ->with(['user'])
            ->when($filters['user_id'] ?? null, fn($q, $id) => $q->where('user_id', $id))
            ->when($filters['is_public'] ?? null, fn($q, $v) => $q->where('is_public', $v))
            ->latest('date')
            ->paginate($perPage);
    }

    public function getJournals(array $filters = []): Collection
    {
        return Journal::query()
            ->with(['user'])
            ->when($filters['is_public'] ?? null, fn($q, $v) => $q->where('is_public', $v))
            ->latest('date')
            ->get();
    }

    public function createJournal(array $data): Journal
    {
        return Journal::create($data);
    }

    public function updateJournal(Journal $journal, array $data): Journal
    {
        $journal->update($data);
        return $journal;
    }

    public function deleteJournal(Journal $journal): bool
    {
        return $journal->delete();
    }
}