<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $tags = Tag::all();

        $projects = [
            [
                'title' => 'Archyx Blog System',
                'description' => 'A minimalist, AI-driven blog engine built with Laravel and Vue.',
                'long_description' => 'Archyx is a modern blog system that combines minimalist design principles with AI-assisted development workflow. Built using Laravel 11 and Vue.js, it features a clean architecture and powerful content management capabilities.',
                'client' => 'Personal Project',
                'role' => 'Full Stack Developer',
                'year' => 2026,
                'image' => '/images/projects/archyx.png',
                'url' => 'https://archyx.com',
                'github_url' => 'https://github.com/archyx/archyx',
                'technologies' => json_encode(['Laravel', 'Vue.js', 'TailwindCSS']),
                'status' => 'in-progress',
                'sort_order' => 1,
                'views_count' => 2500,
                'likes_count' => 180,
            ],
            [
                'title' => 'Onyx Portfolio',
                'description' => 'A high-contrast dark theme portfolio for creative developers.',
                'long_description' => 'Onyx is a sleek, dark-themed portfolio template designed for creative developers who want to showcase their work with style. Features smooth animations and a modern aesthetic.',
                'client' => 'Freelance',
                'role' => 'Frontend Developer',
                'year' => 2025,
                'image' => '/images/projects/onyx.png',
                'url' => 'https://onyx-portfolio.dev',
                'github_url' => 'https://github.com/onyx/onyx-portfolio',
                'technologies' => json_encode(['Next.js', 'Framer Motion']),
                'status' => 'completed',
                'sort_order' => 2,
                'views_count' => 1800,
                'likes_count' => 120,
            ]
        ];

        foreach ($projects as $p) {
            $project = Project::create($p);
            $project->tags()->attach($tags->random(2)->pluck('id'));
        }
    }
}
