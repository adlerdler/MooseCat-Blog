<?php

namespace Database\Seeders;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::role('Administrator')->first();

        $journals = [
            [
                'title' => '探索建筑与技术的交汇点',
                'content' => '今天在设计新项目的过程中，深刻感受到了建筑设计与现代技术融合的魅力。通过参数化设计工具，我能够将复杂的建筑形态转化为可计算的模型，这不仅提高了设计效率，还开拓了新的创意可能性。

下午参加了技术分享会，讨论了WebGL在建筑可视化中的应用前景。这种实时渲染技术能够让客户在设计方案阶段就体验到沉浸式的空间感受，极大地改善了沟通效果。',
                'mood' => 'excited',
                'weather' => 'sunny',
                'date' => now()->subDays(10),
                'is_public' => true,
                'likes_count' => 24,
            ],
            [
                'title' => '关于可持续设计的思考',
                'content' => '最近一直在研究绿色建筑的设计理念。可持续设计不仅仅是使用环保材料，更重要的是从整体系统角度考虑建筑的生命周期。

今天在阅读Adlerian心理学相关文献时，突然意识到建筑设计与心理学有着深刻的联系。空间如何影响人的情绪和行为，这正是我们作为设计师需要深入思考的问题。',
                'mood' => 'thoughtful',
                'weather' => 'cloudy',
                'date' => now()->subDays(7),
                'is_public' => true,
                'likes_count' => 18,
            ],
            [
                'title' => '代码与建筑的奇妙结合',
                'content' => '作为一个既热爱编程又热爱建筑的人，我一直在寻找两者的结合点。今天终于开始了一个个人项目——用Three.js创建一个交互式的建筑模型展示平台。

这个项目的目标是让非专业人士也能轻松地浏览和理解复杂的建筑设计。通过简单的鼠标操作，用户可以360度查看建筑模型，甚至可以进行简单的材质和颜色替换。',
                'mood' => 'happy',
                'weather' => 'sunny',
                'date' => now()->subDays(3),
                'is_public' => false,
                'likes_count' => 12,
            ],
            [
                'title' => '雨天随笔',
                'content' => '窗外下着雨，泡了一杯咖啡，坐在电脑前整理最近的设计笔记。

建筑是一门关于空间的艺术，而编程是一门关于逻辑的艺术。两者看似截然不同，但在深层次上却有着惊人的相似性——都是在创造一种结构，一种能够承载人类活动和情感的结构。',
                'mood' => 'calm',
                'weather' => 'rainy',
                'date' => now()->subDays(1),
                'is_public' => true,
                'likes_count' => 8,
            ],
            [
                'title' => '新项目启动',
                'content' => '今天正式启动了新的建筑设计项目。这是一个混合用途的建筑综合体，包含商业、办公和住宅功能。

项目的挑战在于如何在有限的用地面积内创造出丰富的空间体验。我们团队提出了"垂直村落"的概念，试图在现代高层建筑中重现传统村落的社区感和人情味。

接下来的几周将是非常忙碌的，但我很期待这个项目的发展。',
                'mood' => 'motivated',
                'weather' => 'clear',
                'date' => now(),
                'is_public' => true,
                'likes_count' => 5,
            ],
        ];

        foreach ($journals as $j) {
            Journal::create([
                'user_id' => $admin->id,
                'title' => $j['title'],
                'content' => $j['content'],
                'mood' => $j['mood'],
                'weather' => $j['weather'],
                'date' => $j['date'],
                'is_public' => $j['is_public'],
                'likes_count' => $j['likes_count'],
            ]);
        }
    }
}
