<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class TagService
{
    public function getTags(): Collection
    {
        return Tag::query()
            ->withCount(['posts', 'videos'])
            ->orderBy('name', 'asc')
            ->get();
    }

    public function getPopularTags(int $limit = 20): Collection
    {
        return Tag::query()
            ->withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTagBySlug(string $slug): ?Tag
    {
        return Tag::where('slug', $slug)->first();
    }

    public function createTag(array $data): Tag
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
            return Tag::create($data);
        });
    }

    public function updateTag(Tag $tag, array $data): Tag
    {
        return DB::transaction(function () use ($tag, $data) {
            if (isset($data['name']) && !isset($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }
            $tag->update($data);
            return $tag;
        });
    }

    public function deleteTag(Tag $tag): bool
    {
        return DB::transaction(function () use ($tag) {
            $tag->posts()->detach();
            $tag->videos()->detach();
            $tag->projects()->detach();
            return $tag->delete();
        });
    }

    public function findOrCreate(string $name): Tag
    {
        return Tag::firstOrCreate(
            ['name' => $name],
            ['slug' => Str::slug($name)]
        );
    }

    public function createTags(array $names): SupportCollection
    {
        $tags = [];
        foreach ($names as $name) {
            $tags[] = $this->findOrCreate(trim($name));
        }
        return new SupportCollection($tags);
    }
}
