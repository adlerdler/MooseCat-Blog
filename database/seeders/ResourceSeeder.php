<?php

namespace Database\Seeders;

use App\Models\Resource;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResourceSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        $author = User::role('Administrator')->first();

        $resources = [
            [
                'title' => 'Minimalist UI Kit',
                'description' => 'A clean and modern UI kit for Figma.',
                'format' => 'FIG',
                'file_size' => '12.5 MB',
                'image' => '/images/resources/ui-kit.png',
                'direct_link' => 'https://example.com/ui-kit',
                'drives' => json_encode([['name' => 'Google Drive', 'url' => 'https://drive.google.com/ui-kit']]),
                'downloads_count' => 1250,
                'likes_count' => 89,
            ],
            [
                'title' => 'Clean Code Cheat Sheet',
                'description' => 'Essential tips for writing readable code.',
                'format' => 'PDF',
                'file_size' => '1.2 MB',
                'image' => '/images/resources/clean-code.png',
                'direct_link' => 'https://example.com/clean-code',
                'drives' => json_encode([['name' => 'Baidu Pan', 'url' => 'https://pan.baidu.com/clean-code']]),
                'downloads_count' => 3420,
                'likes_count' => 156,
            ]
        ];

        foreach ($resources as $r) {
            Resource::create(array_merge($r, [
                'author_id' => $author->id,
                'category_id' => $categories->random()->id,
            ]));
        }
    }
}
