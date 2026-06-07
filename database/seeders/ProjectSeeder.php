<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $tags = Tag::all();
        $author = User::role('Administrator')->first();

        $projects = [
            [
                'title' => 'Archyx Blog System',
                'slug' => 'archyx-blog-system',
                'description' => 'A minimalist, AI-driven blog engine built with Laravel and Vue.',
                'long_description' => 'Archyx is a modern blog system that combines minimalist design principles with AI-assisted development workflow. Built using Laravel 11 and Vue.js, it features a clean architecture and powerful content management capabilities.',
                'client' => 'Personal Project',
                'role' => 'Full Stack Developer',
                'year' => 2026,
                'image' => '/images/projects/archyx.png',
                'url' => 'https://archyx.com',
                'github_url' => 'https://github.com/archyx/archyx',
                'technologies' => json_encode(['Laravel', 'Vue.js', 'TailwindCSS']),
                'meta_title' => 'Archyx Blog System',
                'meta_description' => 'A minimalist, AI-driven blog engine built with Laravel and Vue.',
                'meta_keywords' => 'blog,laravel,vue,open-source',
                'status' => 'in-progress',
                'sort_order' => 1,
                'views_count' => 2500,
                'likes_count' => 180,
                'author_id' => $author->id,
            ],
            [
                'title' => 'Onyx Portfolio',
                'slug' => 'onyx-portfolio',
                'description' => 'A high-contrast dark theme portfolio for creative developers.',
                'long_description' => 'Onyx is a sleek, dark-themed portfolio template designed for creative developers who want to showcase their work with style. Features smooth animations and a modern aesthetic.',
                'client' => 'Freelance',
                'role' => 'Frontend Developer',
                'year' => 2025,
                'image' => '/images/projects/onyx.png',
                'url' => 'https://onyx-portfolio.dev',
                'github_url' => 'https://github.com/onyx/onyx-portfolio',
                'technologies' => json_encode(['Next.js', 'Framer Motion']),
                'meta_title' => 'Onyx Portfolio',
                'meta_description' => 'A high-contrast dark theme portfolio for creative developers.',
                'meta_keywords' => 'portfolio,dark-theme,nextjs,creative',
                'status' => 'completed',
                'sort_order' => 2,
                'views_count' => 1800,
                'likes_count' => 120,
                'author_id' => $author->id,
            ]
        ];

        foreach ($projects as $p) {
            $project = Project::firstOrCreate(['slug' => $p['slug']], $p);
            if ($project->wasRecentlyCreated) {
                $project->tags()->attach($tags->random(2)->pluck('id'));
            }
        }
    }
}
