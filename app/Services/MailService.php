<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MailConfig;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * MailService - 统一邮件发送服务
 *
 * 所有邮件均通过数据库 MailConfig 中的 SMTP 配置使用 Symfony Mailer 直连发送，
 * 不依赖 Laravel Mail facade 或 .env 中的 MAIL_* 环境变量。
 *
 * 使用方式：
 *   app(MailService::class)->send('user@example.com', '主题', '<p>HTML内容</p>');
 *   app(MailService::class)->send(['a@x.com', 'b@x.com'], '主题', '<p>HTML</p>');
 */
class MailService
{
    /**
     * 发送一封 HTML 邮件
     *
     * @param string|string[] $to       收件人邮箱（单个字符串或字符串数组）
     * @param string          $subject  邮件主题
     * @param string          $htmlBody HTML 正文
     * @param string|null     $fromAddress 发件人地址（null 则使用数据库配置）
     * @param string|null     $fromName    发件人名称（null 则使用数据库配置）
     * @return bool 发送成功返回 true，失败返回 false
     */
    public function send(
        string|array $to,
        string $subject,
        string $htmlBody,
        ?string $fromAddress = null,
        ?string $fromName = null,
    ): bool {
        try {
            $mailConfig = MailConfig::getActiveConfig();
            if (! $mailConfig) {
                Log::warning('MailService: No active mail config found, email skipped', [
                    'subject' => $subject,
                ]);
                return false;
            }

            $scheme = ($mailConfig->encryption === 'ssl') ? 'smtps' : 'smtp';
            $dsn = sprintf(
                '%s://%s:%s@%s:%d',
                $scheme,
                urlencode($mailConfig->username),
                urlencode($mailConfig->password),
                $mailConfig->host,
                $mailConfig->port
            );

            $transport = Transport::fromDsn($dsn);
            $mailer = new Mailer($transport);

            $fromAddr = new Address(
                $fromAddress ?? $mailConfig->from_address,
                $fromName ?? $mailConfig->from_name
            );

            $email = (new Email())
                ->from($fromAddr)
                ->subject($subject)
                ->html($htmlBody);

            // 支持单个或多个收件人
            if (is_array($to)) {
                $email->to(...$to);
            } else {
                $email->to($to);
            }

            $mailer->send($email);

            Log::info('MailService: Email sent successfully', [
                'to' => is_array($to) ? implode(',', $to) : $to,
                'subject' => $subject,
            ]);

            return true;
        } catch (TransportExceptionInterface $e) {
            Log::error('MailService: Transport error', [
                'to' => is_array($to) ? implode(',', $to) : $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('MailService: Unexpected error', [
                'to' => is_array($to) ? implode(',', $to) : $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * 获取发件人名称（来自活跃 MailConfig）
     */
    public function getFromName(): string
    {
        $config = MailConfig::getActiveConfig();
        return $config?->from_name ?? config('app.name', 'Archyx');
    }

    /**
     * 获取发件人地址（来自活跃 MailConfig）
     */
    public function getFromAddress(): string
    {
        $config = MailConfig::getActiveConfig();
        return $config?->from_address ?? '';
    }
}
