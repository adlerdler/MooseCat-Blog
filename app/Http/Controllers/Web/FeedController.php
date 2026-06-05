<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Seo;

class FeedController extends Controller
{
    public function index()
    {
        $seo = Seo::getGlobalSeo();
        
        if (!$seo || !$seo->rss_feed) {
            abort(404);
        }

        $posts = Post::with(['category', 'author'])
            ->where('status', 'published')
            ->latest('published_at')
            ->limit(20)
            ->get();

        return response()->view('feed.rss', compact('posts', 'seo'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
