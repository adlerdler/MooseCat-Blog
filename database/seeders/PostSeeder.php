<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('Administrator')->first();
        $categories = Category::all();
        $tags = Tag::all();

        $posts = [
            [
                'title' => 'The Art of Minimalism in Web Design',
                'excerpt' => 'How less can actually be more when building modern interfaces.',
                'content' => "## Minimalism\nMinimalism is not just a visual style; it's a philosophy of functionality. By removing the unnecessary, we highlight what truly matters.\n\n### Why it matters\n- Faster loading times\n- Better user focus\n- Elegant aesthetics",
            ],
            [
                'title' => 'Building with Archyx: An AI-First Approach',
                'excerpt' => 'Exploring the development workflow of our new blog system.',
                'content' => "## AI-First Development\nIn this post, we dive into how Trae and Claude collaborate to build the Archyx system.\n\n```php\n// Example of a clean service\npublic function publish(Post \$post) {\n    return \$post->update(['status' => 'published']);\n}\n```",
            ],
            [
                'title' => 'Laravel 11: The Leanest Version Yet',
                'excerpt' => 'A look at the streamlined structure of the latest Laravel release.',
                'content' => "Laravel 11 has removed much of the boilerplate, making it perfect for minimalist projects like Archyx. No more cluttered providers or complex config files by default.",
            ]
        ];

        foreach ($posts as $postData) {
            $post = Post::create([
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'status' => 'published',
                'author_id' => $admin->id,
                'category_id' => $categories->random()->id,
                'published_at' => now(),
            ]);

            // 关联随机标签
            $post->tags()->attach($tags->random(rand(1, 3))->pluck('id'));
        }
    }
}
