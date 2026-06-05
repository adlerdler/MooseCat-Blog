<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} — 新文章通知</title>
</head>
<body style="margin:0;padding:0;background-color:#F5F0EB;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
        <tr>
            <td align="center" style="padding:48px 24px;">

                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;border-collapse:collapse;">

                    <!-- Header Bar -->
                    <tr>
                        <td style="background-color:#000000;padding:28px 36px;border-bottom:4px solid #EF4444;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                <tr>
                                    <td style="vertical-align:middle;">
                                        <span style="font-size:22px;font-weight:900;letter-spacing:-0.025em;color:#FFFFFF;text-transform:uppercase;">
                                            {{ $brandName }}
                                        </span>
                                    </td>
                                    <td align="right" style="vertical-align:middle;">
                                        <span style="font-size:9px;font-weight:700;letter-spacing:0.3em;color:#EF4444;text-transform:uppercase;">
                                            POST_01
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
                        <td style="background-color:#FFFFFF;padding:40px 36px;border-left:4px solid #000000;border-right:4px solid #000000;">

                            <!-- Label -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:28px;">
                                <tr>
                                    <td>
                                        <span style="display:inline-block;background-color:#000000;color:#FFFFFF;padding:4px 12px;font-size:9px;font-weight:800;letter-spacing:0.35em;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            CONSTRUCT // NEW_POST
                                        </span>
                                        <span style="display:block;margin-top:8px;font-size:9px;font-weight:700;letter-spacing:0.25em;color:#999999;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            SUBSCRIBER BROADCAST
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Main Card -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;border:3px solid #000000;background-color:#FAFAFA;">
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
                                    <td style="padding:32px 28px 20px;">

                                        <!-- Greeting -->
                                        <p style="margin:0 0 8px;font-size:11px;font-weight:700;letter-spacing:0.3em;color:#EF4444;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            GREETINGS, SUBSCRIBER
                                        </p>

                                        <!-- Title -->
                                        <h2 style="margin:0 0 12px;font-size:24px;font-weight:900;line-height:1.25;letter-spacing:-0.02em;color:#000000;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            {{ $title }}
                                        </h2>

                                        <!-- Excerpt -->
                                        <p style="margin:0 0 24px;font-size:14px;line-height:1.8;color:#555555;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            {{ $excerpt }}
                                        </p>

                                        <!-- Divider -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:0 0 20px;border-collapse:collapse;">
                                            <tr>
                                                <td style="border-top:1px solid #E5E5E5;font-size:0;">&nbsp;</td>
                                            </tr>
                                        </table>

                                        <!-- CTA Button -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ $postUrl }}"
                                                        style="display:inline-block;background-color:#000000;color:#FFFFFF;padding:14px 40px;font-size:12px;font-weight:800;letter-spacing:0.2em;text-transform:uppercase;text-decoration:none;border-bottom:3px solid #EF4444;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                    >
                                                        {{ $actionText ?? '阅读全文' }}
                                                    </a>
                                                    <p style="margin:12px 0 0;font-size:10px;color:#AAAAAA;letter-spacing:0.1em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                                        {{ $postUrl }}
                                                    </p>
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

                            <!-- Thick Divider -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:32px 0;border-collapse:collapse;">
                                <tr>
                                    <td style="border-top:3px solid #000000;font-size:0;">&nbsp;</td>
                                </tr>
                            </table>

                            <!-- Info Grid -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;border:2px solid #000000;background-color:#FAFAFA;">
                                <tr>
                                    <td style="padding:18px 24px;border-bottom:2px solid #E5E5E5;width:50%;">
                                        <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">PLATFORM</span>
                                        <span style="font-size:14px;font-weight:900;color:#000000;text-transform:uppercase;letter-spacing:0.05em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">{{ $brandName }}</span>
                                    </td>
                                    <td style="padding:18px 24px;border-bottom:2px solid #E5E5E5;width:50%;border-left:2px solid #E5E5E5;">
                                        <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">STATUS</span>
                                        <span style="font-size:14px;font-weight:900;color:#22C55E;text-transform:uppercase;letter-spacing:0.05em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">● PUBLISHED</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:18px 24px;width:50%;">
                                        <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">TIMESTAMP</span>
                                        <span style="font-size:14px;font-weight:900;color:#000000;text-transform:uppercase;letter-spacing:0.05em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">{{ $timestamp }}</span>
                                    </td>
                                    <td style="padding:18px 24px;width:50%;border-left:2px solid #E5E5E5;">
                                        <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">CATEGORY</span>
                                        <span style="font-size:14px;font-weight:900;color:#EF4444;text-transform:uppercase;letter-spacing:0.05em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">{{ $category ?? 'GENERAL' }}</span>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#000000;padding:28px 36px;border-top:4px solid #EF4444;">
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
                                            VOL.2026 // SUBSCRIBER NOTIFICATION // CONSTRUCTIVIST
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

                <!-- Footnote -->
                <p style="margin:24px 0 0;font-size:9px;color:#AAAAAA;text-align:center;letter-spacing:0.15em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                    {{ $brandName }} — 文章更新通知 // 此邮件由系统自动发送
                </p>
                <p style="margin:8px 0 0;font-size:8px;color:#CCCCCC;text-align:center;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                    你收到此邮件是因为你订阅了 {{ $brandName }} 的文章更新。<br>如不想再收到此类邮件，请访问站点取消订阅。
                </p>

            </td>
        </tr>
    </table>
</body>
</html>
