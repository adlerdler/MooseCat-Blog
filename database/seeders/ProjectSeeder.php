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
                'year' => 2026,
                'status' => 'in-progress',
                'technologies' => ['Laravel', 'Vue.js', 'TailwindCSS'],
            ],
            [
                'title' => 'Onyx Portfolio',
                'description' => 'A high-contrast dark theme portfolio for creative developers.',
                'year' => 2025,
                'status' => 'completed',
                'technologies' => ['Next.js', 'Framer Motion'],
            ]
        ];

        foreach ($projects as $p) {
            $project = Project::create($p);
            $project->tags()->attach($tags->random(2)->pluck('id'));
        }
    }
}
