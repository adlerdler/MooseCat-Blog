<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} — 评论回复通知</title>
</head>
<body style="margin:0;padding:0;background-color:#F5F0EB;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
        <tr>
            <td align="center" style="padding:48px 24px;">

                <!-- Container — Full Width -->
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:720px;border-collapse:collapse;">

                    <!-- Header Bar -->
                    <tr>
                        <td style="background-color:#000000;padding:28px 36px;border-bottom:4px solid #EF4444;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                <tr>
                                    <td style="vertical-align:middle;">
                                        @if($logo)
                                        <img src="{{ $logo }}" alt="{{ $brandName }}" style="display:block;width:36px;height:36px;object-fit:contain;border:0;" />
                                        @endif
                                    </td>
                                    <td style="vertical-align:middle;padding-left:16px;">
                                        <span style="font-size:22px;font-weight:900;letter-spacing:-0.025em;color:#FFFFFF;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            {{ $brandName }}
                                        </span>
                                    </td>
                                    <td align="right" style="vertical-align:middle;">
                                        <span style="font-size:9px;font-weight:700;letter-spacing:0.3em;color:#EF4444;text-transform:uppercase;">
                                            REPLY_01
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
                                            CONSTRUCT // REPLY_NOTIFICATION
                                        </span>
                                        <span style="display:block;margin-top:8px;font-size:9px;font-weight:700;letter-spacing:0.25em;color:#999999;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            AUTHOR RESPONSE DETECTED
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
                                    <td style="padding:32px 28px 20px;">

                                        <!-- Greeting -->
                                        <p style="margin:0 0 8px;font-size:11px;font-weight:700;letter-spacing:0.3em;color:#EF4444;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            GREETINGS, {{ $commenterName }}
                                        </p>

                                        <!-- Main Message -->
                                        <p style="margin:0 0 8px;font-size:20px;font-weight:900;line-height:1.3;letter-spacing:-0.02em;color:#000000;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            {{ $penName }} 已回复<br>了你的评论。
                                        </p>

                                        <p style="margin:0 0 24px;font-size:13px;line-height:1.7;color:#555555;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                            你在文章 <strong style="color:#000000;">《{{ $postTitle }}》</strong> 下的评论收到了作者的回复，详情如下。
                                        </p>

                                        <!-- Divider -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:0 0 20px;border-collapse:collapse;">
                                            <tr>
                                                <td style="border-top:1px solid #E5E5E5;font-size:0;">&nbsp;</td>
                                            </tr>
                                        </table>

                                        <!-- Your Comment -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:24px;">
                                            <tr>
                                                <td style="padding-bottom:6px;">
                                                    <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.35em;color:#999999;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                                        YOUR TRANSMISSION
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:16px 20px;background-color:#FFFFFF;border-left:4px solid #000000;border-top:1px solid #E5E5E5;border-right:1px solid #E5E5E5;border-bottom:1px solid #E5E5E5;">
                                                    <p style="margin:0;font-size:13px;line-height:1.6;color:#555555;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                                        {{ $commentBody }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Author Reply -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:24px;">
                                            <tr>
                                                <td style="padding-bottom:6px;">
                                                    <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.35em;color:#EF4444;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                                        ● AUTHOR REPLY
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:16px 20px;background-color:#FFF5F5;border-left:4px solid #EF4444;border-top:1px solid #FECACA;border-right:1px solid #FECACA;border-bottom:1px solid #FECACA;">
                                                    <p style="margin:0 0 8px;font-size:11px;font-weight:700;letter-spacing:0.15em;color:#EF4444;text-transform:uppercase;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                                        {{ $penName }}
                                                        <span style="display:inline-block;background-color:#EF4444;color:#FFFFFF;padding:1px 6px;font-size:8px;font-weight:800;letter-spacing:0.25em;margin-left:6px;vertical-align:middle;">作者</span>
                                                    </p>
                                                    <p style="margin:0;font-size:14px;line-height:1.7;color:#1A1A1A;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                                        {{ $replyBody }}
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- CTA Button -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ $postUrl }}#comments"
                                                        style="display:inline-block;background-color:#000000;color:#FFFFFF;padding:14px 40px;font-size:12px;font-weight:800;letter-spacing:0.2em;text-transform:uppercase;text-decoration:none;border-bottom:3px solid #EF4444;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                    >
                                                        // VIEW CONVERSATION //
                                                    </a>
                                                    <p style="margin:12px 0 0;font-size:10px;color:#AAAAAA;letter-spacing:0.1em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                                                        {{ $postUrl }}#comments
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

                            <!-- Divider -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:32px 0;border-collapse:collapse;">
                                <tr>
                                    <td style="border-top:3px solid #000000;font-size:0;">&nbsp;</td>
                                </tr>
                            </table>

                            <!-- Status Grid -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;border:2px solid #000000;background-color:#FAFAFA;">
                                <tr>
                                    <td style="padding:18px 24px;border-bottom:2px solid #E5E5E5;width:50%;">
                                        <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">ARTICLE</span>
                                        <span style="font-size:14px;font-weight:900;color:#000000;text-transform:uppercase;letter-spacing:0.05em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">{{ $postTitle }}</span>
                                    </td>
                                    <td style="padding:18px 24px;border-bottom:2px solid #E5E5E5;width:50%;border-left:2px solid #E5E5E5;">
                                        <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">REPLY STATUS</span>
                                        <span style="font-size:14px;font-weight:900;color:#22C55E;text-transform:uppercase;letter-spacing:0.05em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">● DELIVERED</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:18px 24px;width:50%;">
                                        <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">TIMESTAMP</span>
                                        <span style="font-size:14px;font-weight:900;color:#000000;text-transform:uppercase;letter-spacing:0.05em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">{{ $timestamp }}</span>
                                    </td>
                                    <td style="padding:18px 24px;width:50%;border-left:2px solid #E5E5E5;">
                                        <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">AUTHOR</span>
                                        <span style="font-size:14px;font-weight:900;color:#EF4444;text-transform:uppercase;letter-spacing:0.05em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">{{ $penName }}</span>
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
                                            VOL.2026 // COMMUNITY REPLY SYSTEM // CONSTRUCTIVIST
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

                <!-- Footnote -->
                <p style="margin:24px 0 0;font-size:9px;color:#AAAAAA;text-align:center;letter-spacing:0.15em;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                    {{ $brandName }} — 评论回复通知 // 此邮件由系统自动发送
                </p>
                <p style="margin:8px 0 0;font-size:8px;color:#CCCCCC;text-align:center;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
                    如不希望接收此类通知，请前往 <a href="{{ $siteUrl }}" style="color:#999999;">站点设置</a> 取消订阅。
                </p>

            </td>
        </tr>
    </table>
</body>
</html>
