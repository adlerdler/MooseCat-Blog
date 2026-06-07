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
        // 动态获取文章（通过 slug 而非硬编码 ID）
        $post1 = Post::where('slug', 'the-geometry-of-perception')->first();
        $post2 = Post::where('slug', 'typography-as-architecture')->first();
        $post3 = Post::where('slug', 'manifesto-of-the-machine')->first();

        if (!$post1 || !$post2 || !$post3) {
            return;
        }

        $comments = [
            // 第一篇文章的评论
            [
                'post_id' => $post1->id,
                'parent_id' => null,
                'user_id' => null,
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'body' => 'Great article! Very insightful analysis of constructivist principles.',
                'is_approved' => true,
                'is_admin' => false,
                'ip_address' => '192.168.1.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ],
            [
                'post_id' => $post1->id,
                'parent_id' => null, // 先创建父评论
                'user_id' => null,
                'name' => 'Alice Chen',
                'email' => 'alice@example.com',
                'body' => 'Can you elaborate on the sustainability aspect?',
                'is_approved' => false,
                'is_admin' => false,
                'ip_address' => '192.168.1.4',
                'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)',
            ],
            
            // 第二篇文章的评论
            [
                'post_id' => $post2->id,
                'parent_id' => null,
                'user_id' => null,
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'body' => 'Would love to see more examples of parametric design.',
                'is_approved' => false,
                'is_admin' => false,
                'ip_address' => '192.168.1.2',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            ],
            [
                'post_id' => $post2->id,
                'parent_id' => null, // 先创建父评论
                'user_id' => null,
                'name' => 'Architect_X',
                'email' => 'arch@example.com',
                'body' => 'Great insights! Would love to see more about the intersection of traditional architecture principles with modern computational methods.',
                'is_approved' => true,
                'is_admin' => false,
                'ip_address' => '192.168.1.10',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            ],
            
            // 第三篇文章的评论
            [
                'post_id' => $post3->id,
                'parent_id' => null,
                'user_id' => null,
                'name' => 'Bob Wilson',
                'email' => 'bob@example.com',
                'body' => 'The code examples are very helpful. Thanks!',
                'is_approved' => true,
                'is_admin' => false,
                'ip_address' => '192.168.1.3',
                'user_agent' => 'Mozilla/5.0 (Linux; Android 10)',
            ],
            [
                'post_id' => $post3->id,
                'parent_id' => null,
                'user_id' => null,
                'name' => 'Visitor',
                'email' => 'visitor@example.com',
                'body' => 'Interesting perspective on cognitive architecture! The grid analogy really resonates.',
                'is_approved' => true,
                'is_admin' => false,
                'ip_address' => '192.168.1.9',
                'user_agent' => 'Mozilla/5.0 (Linux; Android 12)',
            ],
        ];

        foreach ($comments as $commentData) {
            Comment::firstOrCreate([
                'post_id' => $commentData['post_id'],
                'body' => $commentData['body'],
                'name' => $commentData['name'],
            ], $commentData);
        }

        // 所有评论创建完成后，再设置子评论的 parent_id
        $aliceComment = Comment::where('name', 'Alice Chen')->first();
        $johnComment = Comment::where('name', 'John Doe')->first();
        if ($aliceComment && $johnComment) {
            $aliceComment->update(['parent_id' => $johnComment->id]);
        }

        $architectComment = Comment::where('name', 'Architect_X')->first();
        $janeComment = Comment::where('name', 'Jane Smith')->first();
        if ($architectComment && $janeComment) {
            $architectComment->update(['parent_id' => $janeComment->id]);
        }
    }
}
