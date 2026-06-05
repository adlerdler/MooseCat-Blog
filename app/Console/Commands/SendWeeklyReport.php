<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\EmailDigestService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWeeklyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:weekly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成并发送本周数据报告给订阅用户';

    /**
     * Execute the console command.
     */
    public function handle(EmailDigestService $digestService): int
    {
        $this->info('开始生成周报...');

        try {
            $count = $digestService->sendWeeklyReport();

            $this->info("周报已发送给 {$count} 位用户。");
            Log::info('Weekly report command executed', ['recipients' => $count]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('生成周报失败：' . $e->getMessage());

            Log::error('Weekly report command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
