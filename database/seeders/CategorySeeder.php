<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tech', 'description' => 'Technology and Programming'],
            ['name' => 'Life', 'description' => 'Personal life and thoughts'],
            ['name' => 'Design', 'description' => 'UI/UX and Minimalist design'],
            ['name' => 'AI', 'description' => 'Artificial Intelligence and Agents'],
        ];

        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'description' => $cat['description'],
                'sort_order' => rand(1, 10),
            ]);
        }
    }
}
