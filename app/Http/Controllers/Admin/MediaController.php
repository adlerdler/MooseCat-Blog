<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadMediaRequest;
use App\Models\Medium;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\Support\MediaLibrary;

class MediaController extends Controller
{
    public function index(Request $request): Response
    {
        $media = Medium::latest()
            ->paginate(20)
            ->through(fn ($medium) => [
                'id' => $medium->id,
                'name' => $medium->name,
                'file_name' => $medium->file_name,
                'mime_type' => $medium->mime_type,
                'size' => $medium->size,
                'url' => $medium->getUrl(),
                'thumbnail_url' => $medium->getUrl('thumb'),
                'preview_url' => $medium->getUrl('preview'),
                'created_at' => $medium->created_at,
            ]);

        return Inertia::render('admin/Media', [
            'media' => $media,
        ]);
    }

    public function store(UploadMediaRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $file = $validated['file'];
        $name = $validated['name'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $collection = $validated['collection'] ?? 'default';

        $medium = new Medium();
        $medium->name = $name;
        $medium->collection_name = $collection;
        $medium->save();

        $medium->addMedia($file)
            ->usingName($name)
            ->toMediaCollection($collection);

        $media = $medium->media->first();

        return response()->json([
            'message' => '文件上传成功',
            'media' => [
                'id' => $medium->id,
                'name' => $medium->name,
                'file_name' => $media->file_name,
                'mime_type' => $media->mime_type,
                'size' => $media->size,
                'url' => $media->getUrl(),
                'thumbnail_url' => $media->getUrl('thumb'),
                'preview_url' => $media->getUrl('preview'),
            ],
        ], 201);
    }

    public function destroy(string $id): JsonResponse
    {
        $medium = Medium::findOrFail($id);
        $medium->clearMediaCollection();
        $medium->delete();

        return response()->json([
            'message' => '文件删除成功',
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $collection = $request->get('collection', 'default');

        $media = Medium::where('collection_name', $collection)
            ->latest()
            ->get()
            ->map(fn ($medium) => [
                'id' => $medium->id,
                'name' => $medium->name,
                'file_name' => $medium->file_name,
                'mime_type' => $medium->mime_type,
                'size' => $medium->size,
                'url' => $medium->getUrl(),
                'thumbnail_url' => $medium->getUrl('thumb'),
                'preview_url' => $medium->getUrl('preview'),
                'created_at' => $medium->created_at,
            ]);

        return response()->json([
            'media' => $media,
        ]);
    }
}
