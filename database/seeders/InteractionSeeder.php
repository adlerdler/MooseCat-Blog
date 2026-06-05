<?php

namespace Database\Seeders;

use App\Models\Interaction;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class InteractionSeeder extends Seeder
{
    public function run(): void
    {
        $posts = Post::all();
        $user = User::role('Subscriber')->first();

        foreach ($posts as $post) {
            Interaction::create([
                'user_id' => $user->id,
                'interactable_id' => $post->id,
                'interactable_type' => Post::class,
                'type' => 'like',
                'visitor_id' => md5('user-' . $user->id),
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Seeder/1.0',
            ]);

            $post->increment('likes_count');
        }
    }
}
