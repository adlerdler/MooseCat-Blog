<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['type' => 'front', 'label_key' => 'menu.home', 'icon_name' => 'home', 'path' => '/', 'sort_order' => 1, 'is_active' => true, 'parent_id' => null],
            ['type' => 'front', 'label_key' => 'menu.posts', 'icon_name' => 'article', 'path' => '/posts', 'sort_order' => 2, 'is_active' => true, 'parent_id' => null],
            ['type' => 'front', 'label_key' => 'menu.projects', 'icon_name' => 'project', 'path' => '/projects', 'sort_order' => 3, 'is_active' => true, 'parent_id' => null],
            ['type' => 'front', 'label_key' => 'menu.resources', 'icon_name' => 'resource', 'path' => '/resources', 'sort_order' => 4, 'is_active' => true, 'parent_id' => null],
            ['type' => 'front', 'label_key' => 'menu.videos', 'icon_name' => 'video', 'path' => '/videos', 'sort_order' => 5, 'is_active' => true, 'parent_id' => null],
            ['type' => 'front', 'label_key' => 'menu.about', 'icon_name' => 'about', 'path' => '/about', 'sort_order' => 6, 'is_active' => true, 'parent_id' => null],
            ['type' => 'front', 'label_key' => 'menu.tech_blog', 'icon_name' => null, 'path' => '/posts?category=tech', 'sort_order' => 1, 'is_active' => true, 'parent_id' => 2],
            ['type' => 'front', 'label_key' => 'menu.life_essay', 'icon_name' => null, 'path' => '/posts?category=life', 'sort_order' => 2, 'is_active' => true, 'parent_id' => 2],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
