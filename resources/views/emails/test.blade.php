<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ARCHYX — SMTP Test</title>
</head>
<body style="margin:0;padding:0;background-color:#F5F0EB;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
        <tr>
            <td align="center" style="padding:48px 16px;">

                <!-- Container -->
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;border-collapse:collapse;">

                    <!-- Header Bar -->
                    <tr>
                        <td style="background-color:#000000;padding:24px 32px;border-bottom:4px solid #EF4444;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                <tr>
                                    <td style="vertical-align:middle;">
                                        @if($logo)
                                        <img src="{{ $logo }}" alt="ARCHYX" style="display:block;width:36px;height:36px;object-fit:contain;border:0;" />
                                        @endif
                                    </td>
                                    <td style="vertical-align:middle;padding-left:16px;">
                                        <span style="font-size:22px;font-weight:900;letter-spacing:-0.025em;color:#FFFFFF;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            {{ $brandName }}
                                        </span>
                                    </td>
                                    <td align="right" style="vertical-align:middle;">
                                        <span style="font-size:9px;font-weight:700;letter-spacing:0.3em;color:#EF4444;text-transform:uppercase;">
                                            SYS_01
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Red Accent Strip -->
                    <tr>
                        <td style="height:6px;background-color:#EF4444;font-size:0;line-height:0;">&nbsp;</td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background-color:#FFFFFF;padding:40px 32px;border-left:4px solid #000000;border-right:4px solid #000000;">

                            <!-- Label -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:28px;">
                                <tr>
                                    <td>
                                        <span style="display:inline-block;background-color:#000000;color:#FFFFFF;padding:4px 12px;font-size:9px;font-weight:800;letter-spacing:0.35em;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            CONSTRUCT // SMTP_TEST
                                        </span>
                                        <span style="display:block;margin-top:8px;font-size:9px;font-weight:700;letter-spacing:0.25em;color:#999999;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            TRANSMISSION PROTOCOL VERIFICATION
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Main Card -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;border:3px solid #000000;background-color:#FAFAFA;position:relative;">
                                <!-- Corner accent -->
                                <tr>
                                    <td style="padding:0;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                            <tr>
                                                <td style="width:16px;height:16px;background-color:#EF4444;font-size:0;">&nbsp;</td>
                                                <td>&nbsp;</td>
                                                <td style="width:16px;height:16px;background-color:#000000;font-size:0;">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:32px 28px 28px;">

                                        <!-- Greeting -->
                                        <p style="margin:0 0 12px;font-size:11px;font-weight:700;letter-spacing:0.3em;color:#EF4444;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            GREETINGS, {{ $penName }}
                                        </p>

                                        <!-- Main Message -->
                                        <p style="margin:0 0 20px;font-size:22px;font-weight:900;line-height:1.2;letter-spacing:-0.02em;color:#000000;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            SMTP 配置<br>测试成功。
                                        </p>

                                        <p style="margin:0 0 28px;font-size:14px;line-height:1.7;color:#555555;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            此邮件确认您的邮箱配置已正确生效。系统邮件通知、订阅推送等自动化传输已就绪。
                                        </p>

                                        <!-- Status Grid -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;border-top:2px solid #EEEEEE;margin-bottom:20px;">
                                            <tr>
                                                <td style="padding:16px 0;border-bottom:2px solid #EEEEEE;width:50%;">
                                                    <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">SYSTEM STATUS</span>
                                                    <span style="font-size:14px;font-weight:900;color:#22C55E;text-transform:uppercase;letter-spacing:0.05em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">● OPERATIONAL</span>
                                                </td>
                                                <td style="padding:16px 0 16px 20px;border-bottom:2px solid #EEEEEE;width:50%;border-left:2px solid #EEEEEE;">
                                                    <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">TRANSMISSION</span>
                                                    <span style="font-size:14px;font-weight:900;color:#000000;text-transform:uppercase;letter-spacing:0.05em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">VERIFIED ✓</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:16px 0;width:50%;">
                                                    <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">TIMESTAMP</span>
                                                    <span style="font-size:14px;font-weight:900;color:#000000;text-transform:uppercase;letter-spacing:0.05em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">{{ $timestamp }}</span>
                                                </td>
                                                <td style="padding:16px 0 16px 20px;width:50%;border-left:2px solid #EEEEEE;">
                                                    <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">HOST</span>
                                                    <span style="font-size:14px;font-weight:900;color:#000000;text-transform:uppercase;letter-spacing:0.05em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">{{ $smtpHost }}</span>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                                <!-- Bottom accent line -->
                                <tr>
                                    <td style="height:4px;background-color:#EF4444;font-size:0;">&nbsp;</td>
                                </tr>
                            </table>

                            <!-- Divider -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:32px 0;border-collapse:collapse;">
                                <tr>
                                    <td style="border-top:3px solid #000000;font-size:0;">&nbsp;</td>
                                </tr>
                            </table>

                            <!-- Info Box -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                <tr>
                                    <td style="padding:20px;background-color:#F5F0EB;border-left:4px solid #EF4444;">
                                        <p style="margin:0;font-size:11px;line-height:1.6;color:#666666;font-weight:500;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            <strong style="color:#000000;text-transform:uppercase;letter-spacing:0.15em;">INFO //</strong> 此邮件由 ARCHYX 邮件配置测试功能自动发送。如果您未执行此测试，请忽略此邮件并检查您的 SMTP 配置安全性。
                                        </p>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#000000;padding:28px 32px;border-top:4px solid #EF4444;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                <tr>
                                    <td style="vertical-align:middle;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                                            <tr>
                                                <td style="width:8px;height:8px;background-color:#EF4444;font-size:0;">&nbsp;</td>
                                                <td style="width:8px;">&nbsp;</td>
                                                <td>
                                                    <span style="font-size:13px;font-weight:900;letter-spacing:-0.02em;color:#FFFFFF;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                                        {{ $brandName }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                        <p style="margin:8px 0 0;font-size:8px;font-weight:700;letter-spacing:0.3em;color:#666666;text-transform:uppercase;">
                                            VOL.2026 // BUILDING SYSTEM // MINIMALISM
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

                <!-- Footnote -->
                <p style="margin:24px 0 0;font-size:9px;color:#AAAAAA;text-align:center;letter-spacing:0.15em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                    ARCHYX MAIL SYSTEM — TRANSMISSION PROTOCOL TEST
                </p>

            </td>
        </tr>
    </table>
</body>
</html>
