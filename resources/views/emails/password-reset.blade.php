<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} — 密码重置</title>
</head>
<body style="margin:0;padding:0;background-color:#F5F0EB;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
        <tr>
            <td align="center" style="padding:48px 24px;">

                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;border-collapse:collapse;">

                    <!-- Header Bar -->
                    <tr>
                        <td style="background-color:#000000;padding:28px 36px;border-bottom:4px solid #EF4444;">
                            <span style="font-size:22px;font-weight:900;letter-spacing:-0.025em;color:#FFFFFF;text-transform:uppercase;">
                                {{ $brandName }}
                            </span>
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
                                        <span style="display:inline-block;background-color:#000000;color:#FFFFFF;padding:4px 12px;font-size:9px;font-weight:800;letter-spacing:0.35em;text-transform:uppercase;">
                                            CONSTRUCT // PASSWORD_RESET
                                        </span>
                                        <span style="display:block;margin-top:8px;font-size:9px;font-weight:700;letter-spacing:0.25em;color:#999999;text-transform:uppercase;">
                                            SECURITY_OPERATION_02
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Main Card -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;border:3px solid #000000;background-color:#FAFAFA;">
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
                                        <p style="margin:0 0 8px;font-size:11px;font-weight:700;letter-spacing:0.3em;color:#EF4444;text-transform:uppercase;">
                                            GREETINGS, {{ $userName }}
                                        </p>

                                        <!-- Main Message -->
                                        <p style="margin:0 0 8px;font-size:20px;font-weight:900;line-height:1.3;letter-spacing:-0.02em;color:#000000;">
                                            您已请求重置<br>账户密码。
                                        </p>

                                        <p style="margin:0 0 24px;font-size:13px;line-height:1.7;color:#555555;">
                                            我们收到了来自您的密码重置请求。点击下方按钮设置新密码。<br>
                                            此链接将在 <strong style="color:#000000;">{{ $expireMinutes }} 分钟</strong> 后过期。
                                        </p>

                                        <!-- Divider -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:0 0 20px;border-collapse:collapse;">
                                            <tr>
                                                <td style="border-top:1px solid #E5E5E5;font-size:0;">&nbsp;</td>
                                            </tr>
                                        </table>

                                        <!-- Security Notice -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:24px;">
                                            <tr>
                                                <td style="padding:16px 20px;background-color:#FFF5F5;border-left:4px solid #EF4444;border-top:1px solid #FECACA;border-right:1px solid #FECACA;border-bottom:1px solid #FECACA;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                                        <tr>
                                                            <td style="width:20px;vertical-align:top;padding-top:2px;">
                                                                <span style="display:inline-block;width:12px;height:12px;background-color:#EF4444;font-size:0;">&nbsp;</span>
                                                            </td>
                                                            <td style="padding-left:10px;">
                                                                <p style="margin:0;font-size:11px;font-weight:800;letter-spacing:0.15em;color:#EF4444;text-transform:uppercase;">SECURITY NOTICE</p>
                                                                <p style="margin:6px 0 0;font-size:12px;line-height:1.5;color:#991B1B;">
                                                                    如果您没有请求密码重置，请忽略此邮件。<br>
                                                                    您的密码在点击链接之前不会被修改。
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- CTA Button -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ $resetUrl }}"
                                                        style="display:inline-block;background-color:#000000;color:#FFFFFF;padding:14px 40px;font-size:12px;font-weight:800;letter-spacing:0.2em;text-transform:uppercase;text-decoration:none;border-bottom:3px solid #EF4444;"
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                    >
                                                        // RESET PASSWORD //
                                                    </a>

                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
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
                                        <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">ACCOUNT</span>
                                        <span style="font-size:14px;font-weight:900;color:#000000;text-transform:uppercase;letter-spacing:0.05em;">{{ $userName }}</span>
                                    </td>
                                    <td style="padding:18px 24px;border-bottom:2px solid #E5E5E5;width:50%;border-left:2px solid #E5E5E5;">
                                        <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">REQUEST STATUS</span>
                                        <span style="font-size:14px;font-weight:900;color:#EF4444;text-transform:uppercase;letter-spacing:0.05em;">● PENDING</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:18px 24px;width:50%;">
                                        <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">EXPIRES IN</span>
                                        <span style="font-size:14px;font-weight:900;color:#000000;text-transform:uppercase;letter-spacing:0.05em;">{{ $expireMinutes }} MIN</span>
                                    </td>
                                    <td style="padding:18px 24px;width:50%;border-left:2px solid #E5E5E5;">
                                        <span style="display:block;font-size:8px;font-weight:800;letter-spacing:0.3em;color:#999999;text-transform:uppercase;margin-bottom:4px;">TIMESTAMP</span>
                                        <span style="font-size:14px;font-weight:900;color:#000000;text-transform:uppercase;letter-spacing:0.05em;">{{ $timestamp }}</span>
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
                                                    <span style="font-size:13px;font-weight:900;letter-spacing:-0.02em;color:#FFFFFF;text-transform:uppercase;">
                                                        {{ $brandName }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                        <p style="margin:8px 0 0;font-size:8px;font-weight:700;letter-spacing:0.3em;color:#666666;text-transform:uppercase;">
                                            VOL.2026 // PASSWORD RESET SYSTEM // CONSTRUCTIVIST
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>

                <!-- Footnote -->
                <p style="margin:24px 0 0;font-size:9px;color:#AAAAAA;text-align:center;letter-spacing:0.15em;">
                    {{ $brandName }} — 密码重置请求 // 此邮件由系统自动发送，请勿回复
                </p>

            </td>
        </tr>
    </table>
</body>
</html>
