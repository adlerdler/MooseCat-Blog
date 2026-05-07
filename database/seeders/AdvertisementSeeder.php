<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use Illuminate\Database\Seeder;

class AdvertisementSeeder extends Seeder
{
    public function run(): void
    {
        Advertisement::create([
            'title' => 'Minimalist Hosting',
            'image_url' => 'https://via.placeholder.com/300x250',
            'link_url' => 'https://example.com/hosting',
            'position' => 'sidebar',
            'is_active' => true,
        ]);
    }
}
