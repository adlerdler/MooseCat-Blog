<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Architecture', 'slug' => 'architecture'],
            ['name' => 'Design', 'slug' => 'design'],
            ['name' => 'Technology', 'slug' => 'technology'],
            ['name' => 'Philosophy', 'slug' => 'philosophy'],
            ['name' => 'Research', 'slug' => 'research'],
            ['name' => 'Tutorial', 'slug' => 'tutorial'],
            ['name' => 'Case Study', 'slug' => 'case-study'],
            ['name' => 'Algorithm', 'slug' => 'algorithm'],
            ['name' => 'Parametric', 'slug' => 'parametric'],
            ['name' => 'Computational', 'slug' => 'computational'],
            ['name' => 'Digital Fabrication', 'slug' => 'digital-fabrication'],
            ['name' => 'BIM', 'slug' => 'bim'],
            ['name' => 'Sustainability', 'slug' => 'sustainability'],
            ['name' => 'Urban Design', 'slug' => 'urban-design'],
            ['name' => 'Generative', 'slug' => 'generative'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['slug' => $tag['slug']], $tag);
        }
    }
}
