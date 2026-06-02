<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;

class MediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_media');
    }

    /**
     * 媒体库列表
     */
    public function index(Request $request): Response
    {
        $query = SpatieMedia::where('collection_name', 'default');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $media = $query->latest()->paginate(20);

        $media->getCollection()->transform(function (SpatieMedia $item) {
            // 确保旧记录有 UUID（兜底：自动补填）
            $uuid = $item->uuid ?? $this->ensureUuid($item);
            return [
                'id'   => $uuid,
                'name' => $item->name,
                'type' => $this->getFileType($item->mime_type ?? ''),
                'size' => $this->formatSize($item->size ?? 0),
                'url'  => $this->buildUuidUrl($item),
                'date' => $item->created_at->format('Y-m-d'),
            ];
        });

        return Inertia::render('admin/Media', [
            'media' => $media,
        ]);
    }

    /**
     * 上传文件（挂载到当前用户下，Spatie 原生处理）
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:102400',
        ]);

        $user = auth()->user();

        $addedMedia = $user->addMedia($request->file('file'))
            ->toMediaCollection('default', 'public');

        return response()->json([
            'message' => '文件上传成功',
            'media'   => [
                'id'   => $addedMedia->uuid,
                'name' => $addedMedia->name,
                'type' => $this->getFileType($addedMedia->mime_type ?? ''),
                'size' => $this->formatSize($addedMedia->size ?? 0),
                'url'  => $this->buildUuidUrl($addedMedia),
                'date' => $addedMedia->created_at->format('Y-m-d'),
            ],
        ], 201);
    }

    /**
     * 删除媒体（UUID 路由绑定）
     */
    public function destroy(SpatieMedia $media): JsonResponse
    {
        $media->delete();

        return response()->json([
            'message' => '删除成功',
        ]);
    }

    // ─── helpers ───────────────────────────────────────────────

    private function getFileType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) return 'image';
        if (str_starts_with($mimeType, 'video/')) return 'video';
        if (str_starts_with($mimeType, 'audio/')) return 'audio';
        if (str_starts_with($mimeType, 'text/')) return 'document';

        // Office Open XML + PDF + 旧版 Office 格式
        if (in_array($mimeType, [
            'application/pdf',
            'application/msword',                                                         // .doc
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',     // .docx
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',           // .xlsx
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',   // .pptx
            'application/vnd.ms-excel',                                                    // .xls
            'application/vnd.ms-powerpoint',                                               // .ppt
        ])) {
            return 'document';
        }

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

    /**
     * 构建 UUID 访问 URL：/media/{uuid}.{ext}
     */
    private function buildUuidUrl(SpatieMedia $media): string
    {
        $ext = pathinfo($media->file_name, PATHINFO_EXTENSION);
        return url("/media/{$media->uuid}" . ($ext ? ".{$ext}" : ''));
    }

    /**
     * 为旧记录兜底补充 UUID（旧数据可能没有 uuid 字段）
     */
    private function ensureUuid(SpatieMedia $media): string
    {
        $uuid = (string) Str::uuid();
        $media->forceFill(['uuid' => $uuid])->saveQuietly();
        return $uuid;
    }
}
