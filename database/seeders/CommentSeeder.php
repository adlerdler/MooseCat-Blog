<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $posts = Post::all();
        $user = User::where('role', 'user')->first();

        foreach ($posts as $post) {
            Comment::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
                'body' => '这是一篇非常有见地的文章，深受启发！',
                'is_approved' => true,
            ]);

            Comment::create([
                'post_id' => $post->id,
                'name' => 'Guest User',
                'email' => 'guest@example.com',
                'body' => '非常喜欢这种极简的设计风格。',
                'is_approved' => true,
            ]);
        }
    }
}
