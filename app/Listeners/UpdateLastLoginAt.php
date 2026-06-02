<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class UpdateLastLoginAt
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        
        // 用 DB 直接更新，避免触发模型事件和更新时间戳
        DB::table('users')
            ->where('id', $user->id)
            ->update(['last_login_at' => now()]);
    }
}
