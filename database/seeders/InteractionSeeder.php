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
            ]);

            $post->increment('likes_count');
        }
    }
}
