<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\EmailDigestService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendDigestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:digest
                            {frequency=daily : 发送频率 (daily|weekly|monthly)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成并发送内容摘要邮件给订阅用户';

    /**
     * Execute the console command.
     */
    public function handle(EmailDigestService $digestService): int
    {
        $frequency = $this->argument('frequency');

        // 验证频率参数
        if (!in_array($frequency, ['daily', 'weekly', 'monthly'])) {
            $this->error('无效的频率参数。请使用 daily、weekly 或 monthly。');
            return Command::FAILURE;
        }

        $frequencyLabels = [
            'daily'   => '每日',
            'weekly'  => '每周',
            'monthly' => '每月',
        ];

        $this->info("开始生成 {$frequencyLabels[$frequency]} 摘要...");

        try {
            $count = $digestService->sendDigestEmail($frequency);

            $this->info("摘要邮件已发送给 {$count} 位用户。");
            Log::info('Digest email command executed', [
                'frequency' => $frequency,
                'recipients' => $count,
            ]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('生成摘要邮件失败：' . $e->getMessage());

            Log::error('Digest email command failed', [
                'frequency' => $frequency,
                'error'     => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
