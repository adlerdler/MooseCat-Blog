<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * Mail Config Controller
 * 
 * Handles mail configuration management.
 * Provides functionality for viewing, updating, and testing email settings.
 */
class MailConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_mail_config');
    }

    /**
     * Display the mail configuration page
     * 
     * Loads the active mail config from database and renders the page.
     */
    public function index(): Response
    {
        $config = MailConfig::where('is_active', true)->first();

        return Inertia::render('admin/MailConfig', [
            'mailConfig' => $config ? $this->formatForFrontend($config) : [],
        ]);
    }

    /**
     * Update mail configuration
     * 
     * Creates or updates the active mail config record in the database.
     */
    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'host'         => 'required|string',
            'port'         => 'required|integer',
            'username'     => 'required|string',
            'encryption'   => 'nullable|string',
            'fromAddress'  => 'required|email',
            'fromName'     => 'required|string',
            'password'     => 'nullable|string',
            'driver'       => 'nullable|string',
        ]);

        $config = MailConfig::where('is_active', true)->first();

        if (! $config) {
            $config = new MailConfig();
        }

        $config->fill([
            'mailer'       => $validated['driver'] ?? 'smtp',
            'host'         => $validated['host'],
            'port'         => $validated['port'],
            'username'     => $validated['username'],
            'encryption'   => $validated['encryption'] ?? null,
            'from_address' => $validated['fromAddress'],
            'from_name'    => $validated['fromName'],
            'is_active'    => true,
        ]);

        // Only update password when explicitly provided (do not overwrite with empty)
        if (! empty($validated['password'])) {
            $config->password = $validated['password'];
        }

        $config->save();

        return back()->with('success', '邮件配置已更新');
    }

    /**
     * Test mail configuration
     * 
     * Sends a test email using the submitted SMTP settings
     * without saving to the database.
     */
    public function test(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'host'        => 'required|string',
            'port'        => 'required|integer',
            'username'    => 'required|string',
            'encryption'  => 'nullable|string',
            'fromAddress' => 'required|email',
            'fromName'    => 'required|string',
            'password'    => 'nullable|string',
        ]);

        try {
            // Build DSN string: smtp for TLS/none, smtps for SSL (port 465)
            $scheme = ($validated['encryption'] ?? '') === 'ssl' ? 'smtps' : 'smtp';

            $dsn = sprintf(
                '%s://%s:%s@%s:%d',
                $scheme,
                urlencode($validated['username']),
                urlencode($validated['password']),
                $validated['host'],
                $validated['port']
            );

            $transport = Transport::fromDsn($dsn);

            $email = (new Email())
                ->from(new Address($validated['fromAddress'], $validated['fromName']))
                ->to('adlerdecht@gmail.com')
                ->subject('Archyx SMTP 配置测试')
                ->text('这是一封测试邮件，如果您能收到此邮件，说明 SMTP 配置正确无误。');

            $mailer = new Mailer($transport);
            $mailer->send($email);

            return back()->with('success', '测试邮件已发送成功！');
        } catch (\Exception $e) {
            return back()->withErrors(['test' => '邮件发送失败：' . $e->getMessage()]);
        }
    }

    /**
     * Format a MailConfig model for the frontend (snake_case → camelCase).
     */
    private function formatForFrontend(MailConfig $config): array
    {
        return [
            'host'        => $config->host,
            'port'        => $config->port,
            'encryption'  => $config->encryption,
            'username'    => $config->username,
            'password'    => $config->password,
            'fromAddress' => $config->from_address,
            'fromName'    => $config->from_name,
            'driver'      => $config->mailer,
            'is_active'   => $config->is_active,
        ];
    }
}
