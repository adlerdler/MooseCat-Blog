<?php

namespace Database\Seeders;

use App\Models\Resource;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();

        $resources = [
            [
                'title' => 'Minimalist UI Kit',
                'description' => 'A clean and modern UI kit for Figma.',
                'format' => 'FIG',
                'file_size' => '12.5 MB',
                'direct_link' => 'https://example.com/ui-kit',
            ],
            [
                'title' => 'Clean Code Cheat Sheet',
                'description' => 'Essential tips for writing readable code.',
                'format' => 'PDF',
                'file_size' => '1.2 MB',
                'direct_link' => 'https://example.com/clean-code',
            ]
        ];

        foreach ($resources as $r) {
            Resource::create(array_merge($r, [
                'category_id' => $categories->random()->id,
            ]));
        }
    }
}
