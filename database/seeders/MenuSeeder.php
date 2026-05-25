<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            ['type' => 'front', 'label_key' => 'menu.home', 'icon_name' => 'home', 'path' => '/', 'component_name' => 'Home', 'sort_order' => 1, 'is_active' => true, 'parent_id' => null],
            ['type' => 'front', 'label_key' => 'menu.posts', 'icon_name' => 'article', 'path' => '/posts', 'component_name' => 'Posts', 'sort_order' => 2, 'is_active' => true, 'parent_id' => null],
            ['type' => 'front', 'label_key' => 'menu.projects', 'icon_name' => 'project', 'path' => '/projects', 'component_name' => 'Projects', 'sort_order' => 3, 'is_active' => true, 'parent_id' => null],
            ['type' => 'front', 'label_key' => 'menu.resources', 'icon_name' => 'resource', 'path' => '/resources', 'component_name' => 'Resources', 'sort_order' => 4, 'is_active' => true, 'parent_id' => null],
            ['type' => 'front', 'label_key' => 'menu.videos', 'icon_name' => 'video', 'path' => '/videos', 'component_name' => 'Videos', 'sort_order' => 5, 'is_active' => true, 'parent_id' => null],
            ['type' => 'front', 'label_key' => 'menu.about', 'icon_name' => 'about', 'path' => '/about', 'component_name' => 'About', 'sort_order' => 6, 'is_active' => true, 'parent_id' => null],
            ['type' => 'front', 'label_key' => 'menu.tech_blog', 'icon_name' => null, 'path' => '/posts?category=tech', 'component_name' => null, 'sort_order' => 1, 'is_active' => true, 'parent_id' => 2],
            ['type' => 'front', 'label_key' => 'menu.life_essay', 'icon_name' => null, 'path' => '/posts?category=life', 'component_name' => null, 'sort_order' => 2, 'is_active' => true, 'parent_id' => 2],
        ];

        foreach ($menus as $menu) {
            Menu::create($menu);
        }
    }
}
