<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} — {{ $title }}</title>
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

                            <!-- Title -->
                            <h2 style="font-size:20px;font-weight:900;color:#000000;margin:0 0 16px 0;letter-spacing:-0.025em;">
                                {{ $title }}
                            </h2>

                            <!-- Message -->
                            <p style="font-size:15px;color:#333333;line-height:1.8;margin:0 0 24px 0;">
                                {!! $message !!}
                            </p>

                            @if(!empty($detail))
                            <!-- Detail Box -->
                            <div style="background-color:#F5F0EB;border-left:4px solid #EF4444;padding:16px 20px;margin-bottom:24px;">
                                <p style="font-size:14px;color:#555555;margin:0;line-height:1.6;font-family:monospace;">
                                    {{ $detail }}
                                </p>
                            </div>
                            @endif

                            @if(!empty($actionUrl))
                            <!-- Action Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                                <tr>
                                    <td align="center" style="background-color:#EF4444;border-radius:0;">
                                        <a href="{{ $actionUrl }}" target="_blank" style="display:inline-block;padding:14px 36px;font-size:13px;font-weight:700;color:#FFFFFF;text-decoration:none;text-transform:uppercase;letter-spacing:0.1em;">
                                            {{ $actionText ?? '查看详情' }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            @endif

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#000000;padding:20px 36px;text-align:center;">
                            <p style="font-size:11px;color:#888888;margin:0;letter-spacing:0.05em;">
                                &copy; {{ date('Y') }} {{ $brandName }}. All rights reserved.
                            </p>
                            <p style="font-size:10px;color:#666666;margin:6px 0 0 0;letter-spacing:0.05em;">
                                {{ $timestamp }}
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
