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
        $editor = User::role('Editor')->first();
        $author = User::role('Author')->first();
        
        $categories = Category::all();
        $tags = Tag::all();

        $theoryCategory = Category::where('name', 'THEORY')->first();
        $designCategory = Category::where('name', 'DESIGN')->first();
        $cultureCategory = Category::where('name', 'CULTURE')->first();
        $systemDesignCategory = Category::where('name', 'SYSTEM-DESIGN')->first();
        $engineeringCategory = Category::where('name', 'ENGINEERING')->first();

        $posts = [
            [
                'title' => 'THE GEOMETRY OF PERCEPTION',
                'slug' => 'the-geometry-of-perception',
                'excerpt' => 'Exploring how structural design influences cognitive processes in modern interfaces.',
                'content' => "# The Geometry of Perception\n\nThe human brain is wired to find patterns in chaos. In this long-form exploration, we dive into the structural foundations of user interface design through the lens of Constructivist theory.\n\n## THE GRID AS A SKELETON\n\nEvery interface has a skeleton. Whether acknowledged or not, the underlying structure dictates the user journey.\n\n### Key Principles\n\nThe grid provides:\n- **Visual anchor points** - Consistent reference for alignment\n- **Predictable navigation patterns** - Users know where to look\n- **Hierarchical information organization** - Clear visual weight distribution\n- **Emotional comfort through familiarity** - Reduced cognitive load\n\n> \"Geometry is not just aesthetic; it is the grammar of visual communication.\"\n> — Constructivist Design Principles\n\n## MODULAR CONSTRUCTION\n\nModern interfaces are built from modular components. Each element should function independently while contributing to the greater whole.\n\n## THE TYPOGRAPHIC GRID\n\nTypography in Constructivist design follows strict geometric rules. The baseline grid ensures that text aligns across columns, creating visual rhythm.\n\n## COGNITIVE MAPPING\n\nWhen users encounter an interface, they create mental maps based on visual hierarchy. A well-structured grid reduces cognitive load by providing consistent anchor points.\n\n## CONCLUSION\n\nThe geometry of perception is not about rigid rules—it's about creating frameworks that guide users naturally through information architecture. The grid is neither inherently good nor bad—it's a tool whose ethics depend on how it's deployed.",
                'cover_image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800&h=600&fit=crop',
                'color' => 'red',
                'status' => 'published',
                'views_count' => 1250,
                'likes_count' => 45,
                'meta_title' => 'The Geometry of Perception',
                'meta_description' => 'Exploring how structural design influences cognitive processes in modern interfaces.',
                'meta_keywords' => 'design,constructivism,ui,ux',
                'author_id' => $admin->id,
                'category_id' => $theoryCategory->id,
                'published_at' => now()->subDays(28),
            ],
            [
                'title' => 'TYPOGRAPHY AS ARCHITECTURE',
                'slug' => 'typography-as-architecture',
                'excerpt' => 'Why fonts are the literal beams and columns of a digital experience.',
                'content' => "# Typography as Architecture\n\nTypography is the physical material of digital communication. If a layout is the blueprint, the typeface is the concrete and steel.\n\n## THE WEIGHT OF THE WORD\n\nBoldness is not just about emphasis; it is about hierarchy. When everything is loud, nothing is heard.\n\n### Structural Integrity\n\nDifferent font weights support the \"roof\" of the message:\n- **Black (900)** - Primary headings, maximum impact\n- **Bold (700)** - Secondary headings, strong emphasis\n- **Medium (500)** - Body text, optimal readability\n- **Regular (400)** - Captions, secondary information\n\n> \"A typeface should be like a building: solid, structured, and purposeful.\"\n> — Alexander Rodchenko\n\n## MONOSPACE AS TRUTH\n\nRodchenko famously used type as a weapon of clarity. Monospaced fonts are the most honest choices for a data-driven society.\n\n## TYPOGRAPHIC GRID\n\nCombining grid systems with typographic rhythm creates visual harmony.",
                'cover_image' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=800&h=600&fit=crop',
                'color' => 'red',
                'status' => 'published',
                'views_count' => 980,
                'likes_count' => 38,
                'meta_title' => 'Typography as Architecture',
                'meta_description' => 'Why fonts are the literal beams and columns of a digital experience.',
                'meta_keywords' => 'typography,fonts,design,architecture',
                'author_id' => $editor->id,
                'category_id' => $designCategory->id,
                'published_at' => now()->subDays(35),
            ],
            [
                'title' => 'MANIFESTO OF THE MACHINE',
                'slug' => 'manifesto-of-the-machine',
                'excerpt' => 'Redefining the relationship between human intuition and algorithmic precision.',
                'content' => "# Manifesto of the Machine\n\nWe live in an age of the hybrid. The machine provides the precision, the human provide the purpose.\n\n## ALGORITHMIC HONESTY\n\nWhen we hide complexity, we strip away user agency. \"Open-hood\" design reveals internal logic.\n\n## THE HYBRID FUTURE\n\nThis manifesto calls for a new digital culture that celebrates the mechanical rather than hiding it.\n\n### Design Principles\n\n- **Don't make it \"natural\"** - Make it readable\n- **Show your work** - Algorithms should be transparent\n- **Human in the loop** - Machines augment, don't replace\n- **Structure > Aesthetics** - Function defines form\n\n> \"We should not aim to make technology 'natural'—we should aim to make it readable.\"\n> — Adlerian Manifesto\n\nWe should not aim to make technology \"natural\"—we should aim to make it readable. The machine provides the precision, the human provides the purpose.",
                'cover_image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&h=600&fit=crop',
                'color' => 'black',
                'status' => 'published',
                'views_count' => 1560,
                'likes_count' => 62,
                'meta_title' => 'Manifesto of the Machine',
                'meta_description' => 'Redefining the relationship between human intuition and algorithmic precision.',
                'meta_keywords' => 'algorithm,machine,human,technology',
                'author_id' => $author->id,
                'category_id' => $cultureCategory->id,
                'published_at' => now()->subDays(40),
            ],
            [
                'title' => 'ARCHITECTURAL VECTORS',
                'slug' => 'architectural-vectors',
                'excerpt' => 'Analyzing the trajectory of lines in three-dimensional digital space.',
                'content' => "# Architectural Vectors\n\nVectors are the invisible strings that hold our digital world together. In this study, we bridge the gap between physical gravity and digital directional force.\n\n## VECTOR MATHEMATICS\n\nUnderstanding the math behind movement is fundamental.\n\n### Directional Force\n\nVectors define:\n- **Position** - Where something is\n- **Velocity** - Where it's going\n- **Acceleration** - How fast it's getting there\n- **Force** - What's pushing it\n\n## VISUAL COMPOSITION\n\nUsing vectors to create dynamic compositions.\n\n> \"A vector is not just a line—it's a story about movement.\"\n> — Vladimir Tatlin\n\nVectors bridge the gap between physical gravity and digital directional force, creating compositions that feel alive yet structured.",
                'cover_image' => 'https://images.unsplash.com/photo-1633356122102-3fe601e05bd2?w=800&h=600&fit=crop',
                'color' => 'red',
                'status' => 'published',
                'views_count' => 820,
                'likes_count' => 29,
                'meta_title' => 'Architectural Vectors',
                'meta_description' => 'Analyzing the trajectory of lines in three-dimensional digital space.',
                'meta_keywords' => 'vectors,architecture,3d,space',
                'author_id' => $admin->id,
                'category_id' => $systemDesignCategory->id,
                'published_at' => now()->subDays(43),
            ],
            [
                'title' => 'THE ETHICS OF THE GRID',
                'slug' => 'the-ethics-of-the-grid',
                'excerpt' => 'Is the grid a tool of liberation or a cage for creativity?',
                'content' => "# The Ethics of the Grid\n\nThe grid provides structure, but at what cost? We explore the philosophical implications of strict alignment in UX design.\n\n## LIBERATION VS CONSTRAINT\n\n### Philosophical Questions\n\n- **Does structure empower or limit?**\n- **Can creativity thrive within constraints?**\n- **Who benefits from the grid?**\n- **What gets excluded?**\n\n## DESIGN JUSTICE\n\n> \"A grid should be a framework for expression, not a prison for creativity.\"\n> — Design Justice Principles\n\nThe grid is neither inherently good nor bad—it's a tool whose ethics depend on how it's deployed.",
                'cover_image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&h=600&fit=crop',
                'color' => 'black',
                'status' => 'published',
                'views_count' => 1100,
                'likes_count' => 51,
                'meta_title' => 'The Ethics of the Grid',
                'meta_description' => 'Is the grid a tool of liberation or a cage for creativity?',
                'meta_keywords' => 'ethics,grid,ux,design',
                'author_id' => $admin->id,
                'category_id' => $engineeringCategory->id,
                'published_at' => now()->subDays(45),
            ],
        ];

        foreach ($posts as $postData) {
            $post = Post::create($postData);

            // 关联随机标签
            $post->tags()->attach($tags->random(rand(2, 4))->pluck('id'));
        }
    }
}
