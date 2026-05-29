<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class MediaController extends Controller
{
    public function index(Request $request): Response
    {
        $media = Media::with('user')
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20);

        return Inertia::render('admin/Media', [
            'media' => $media,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'title' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
        ]);

        $media = Media::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'alt_text' => $request->alt_text,
            'tags' => $request->tags,
            'is_public' => true,
        ]);

        if ($request->hasFile('file')) {
            $media->addMedia($request->file('file'))
                ->toMediaCollection('default');
        }

        $media->load('media');

        $file = $media->media->first();

        return response()->json([
            'message' => '文件上传成功',
            'media' => [
                'id' => $media->id,
                'title' => $media->title,
                'name' => $file?->file_name ?? $request->title,
                'type' => $this->getFileType($file?->mime_type ?? ''),
                'size' => $this->formatSize($file?->size ?? 0),
                'url' => $file?->getUrl() ?? '',
                'thumb_url' => $file?->getUrl('thumb') ?? '',
                'date' => $media->created_at->format('Y-m-d'),
            ],
        ], 201);
    }

    private function getFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) return 'image';
        if (str_starts_with($mimeType, 'video/')) return 'video';
        if (in_array($mimeType, ['application/pdf', 'application/msword', 'text/plain'])) return 'document';
        return 'archive';
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        $k = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));
        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    public function update(Request $request, Media $media): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'alt_text' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'is_public' => 'boolean',
        ]);

        $media->update($request->only(['title', 'alt_text', 'tags', 'is_public']));

        return response()->json([
            'message' => '更新成功',
            'media' => $media->fresh('mediaFiles'),
        ]);
    }

    public function destroy(Media $media): JsonResponse
    {
        $media->mediaFiles()->each(fn ($file) => $file->delete());
        $media->delete();

        return response()->json([
            'message' => '删除成功',
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $media = Media::with('mediaFiles')
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return response()->json([
            'media' => $media,
        ]);
    }
}
