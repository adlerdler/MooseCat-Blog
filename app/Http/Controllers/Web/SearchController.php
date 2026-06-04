<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Project;
use App\Models\Video;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * 全局搜索 API（前台）
     * 搜索范围：Posts、Videos、Projects、Resources
     * 按相关性排序，最多返回 10 条
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['data' => []]);
        }

        $results = [];

        // 搜索文章（status=published）
        $posts = Post::where('status', 'published')
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('excerpt', 'like', "%{$q}%");
            })
            ->latest('published_at')
            ->limit(5)
            ->get();

        foreach ($posts as $post) {
            $results[] = [
                'id'       => $post->id,
                'type'     => 'post',
                'title'    => $post->title,
                'excerpt'  => $post->excerpt,
                'route'    => "/blog/{$post->slug}",
                'category' => $post->category?->name ?? 'UNCATEGORIZED',
            ];
        }

        // 搜索视频（status=published）
        $videos = Video::where('status', 'published')
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->latest('published_at')
            ->limit(5)
            ->get();

        foreach ($videos as $video) {
            $results[] = [
                'id'       => $video->id,
                'type'     => 'video',
                'title'    => $video->title,
                'excerpt'  => $video->description,
                'route'    => "/videos/{$video->slug}",
                'category' => $video->category?->name ?? 'VIDEO',
            ];
        }

        // 搜索项目（status=completed）
        $projects = Project::where('status', 'completed')
            ->where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->orderBy('sort_order', 'asc')
            ->limit(5)
            ->get();

        foreach ($projects as $project) {
            $results[] = [
                'id'       => $project->id,
                'type'     => 'project',
                'title'    => $project->title,
                'excerpt'  => $project->description,
                'route'    => "/projects/{$project->slug}",
                'category' => 'PROJECT',
            ];
        }

        // 搜索资源
        $resources = Resource::where(function ($query) use ($q) {
                $query->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->latest()
            ->limit(5)
            ->get();

        foreach ($resources as $resource) {
            $results[] = [
                'id'       => $resource->id,
                'type'     => 'resource',
                'title'    => $resource->title,
                'excerpt'  => $resource->description,
                'route'    => '/resources',
                'category' => $resource->category?->name ?? 'RESOURCE',
            ];
        }

        // 按标题匹配优先排序：标题含关键词的排在前面
        $qLower = mb_strtolower($q);
        usort($results, function ($a, $b) use ($qLower) {
            $aTitle = mb_stripos($a['title'], $qLower) !== false ? 1 : 0;
            $bTitle = mb_stripos($b['title'], $qLower) !== false ? 1 : 0;
            return $bTitle - $aTitle;
        });

        // 最多返回 10 条
        $results = array_slice($results, 0, 10);

        return response()->json(['data' => $results]);
    }
}
