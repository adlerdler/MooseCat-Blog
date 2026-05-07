<?php

namespace Database\Seeders;

use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $journals = [
            ['content' => '今天成功将项目更名为 Archyx，感觉这个名字更有设计感。', 'mood' => 'Happy', 'weather' => 'Sunny'],
            ['content' => '在调试 Vue 局部增强模式，虽然遇到了一点白屏问题，但最终通过 Alias 解决了。', 'mood' => 'Focused', 'weather' => 'Cloudy'],
            ['content' => '准备开始编写数据库填充类，为前端展示提供真实数据支持。', 'mood' => 'Productive', 'weather' => 'Rainy'],
        ];

        foreach ($journals as $j) {
            Journal::create([
                'user_id' => $admin->id,
                'content' => $j['content'],
                'mood' => $j['mood'],
                'weather' => $j['weather'],
                'is_public' => true,
            ]);
        }
    }
}
