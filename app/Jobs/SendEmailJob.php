<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\MailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SendEmailJob — 异步邮件发送任务
 *
 * 配合 QUEUE_CONNECTION 一行切换模式：
 *   sync  → afterResponse 延迟执行（零 Worker / 零 Redis / 廉价 VPS）
 *   redis → 推入 Redis 队列，Worker 消费（可靠重试 + 失败归档）
 */
class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        public string|array $to,
        public string $subject,
        public string $htmlBody,
        public ?string $fromAddress = null,
        public ?string $fromName = null,
    ) {}

    public function handle(MailService $mailService): void
    {
        $success = $mailService->send(
            $this->to,
            $this->subject,
            $this->htmlBody,
            $this->fromAddress,
            $this->fromName,
        );

        if (! $success) {
            Log::warning('SendEmailJob: MailService returned false', [
                'to'      => is_array($this->to) ? implode(',', $this->to) : $this->to,
                'subject' => $this->subject,
                'attempt' => $this->attempts(),
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendEmailJob: Final failure', [
            'to'      => is_array($this->to) ? implode(',', $this->to) : $this->to,
            'subject' => $this->subject,
            'error'   => $exception->getMessage(),
        ]);
    }
}
