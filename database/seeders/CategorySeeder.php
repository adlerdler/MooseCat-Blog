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
            [
                'name' => 'THEORY',
                'slug' => 'theory',
                'description' => '理论研究与探索',
                'sort_order' => 1,
                'status' => 'active',
            ],
            [
                'name' => 'DESIGN',
                'slug' => 'design',
                'description' => '设计与创意',
                'sort_order' => 2,
                'status' => 'active',
            ],
            [
                'name' => 'CULTURE',
                'slug' => 'culture',
                'description' => '文化与艺术',
                'sort_order' => 3,
                'status' => 'active',
            ],
            [
                'name' => 'SYSTEM-DESIGN',
                'slug' => 'system-design',
                'description' => '系统架构与设计',
                'sort_order' => 4,
                'status' => 'active',
            ],
            [
                'name' => 'ENGINEERING',
                'slug' => 'engineering',
                'description' => '工程与实践',
                'sort_order' => 5,
                'status' => 'active',
            ],
            [
                'name' => 'HISTORY',
                'slug' => 'history',
                'description' => '历史与发展',
                'sort_order' => 6,
                'status' => 'inactive',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }
    }
}
