<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} — 周报</title>
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
                            <h2 style="font-size:20px;font-weight:900;color:#000000;margin:0 0 8px 0;letter-spacing:-0.025em;">
                                📊 {{ $periodLabel }} 数据报告
                            </h2>
                            <p style="font-size:14px;color:#888888;margin:0 0 32px 0;">
                                {{ $dateRange }}
                            </p>

                            <!-- Stats Grid -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:32px;">
                                <tr>
                                    @foreach($stats as $stat)
                                    <td width="25%" style="text-align:center;padding:20px 8px;border-right:1px solid #EEEEEE;">
                                        <div style="font-size:32px;font-weight:900;color:#EF4444;line-height:1;margin-bottom:8px;">
                                            {{ $stat['value'] }}
                                        </div>
                                        <div style="font-size:12px;color:#888888;text-transform:uppercase;letter-spacing:0.1em;">
                                            {{ $stat['label'] }}
                                        </div>
                                    </td>
                                    @endforeach
                                </tr>
                            </table>

                            @if(count($newPosts) > 0)
                            <!-- New Posts Section -->
                            <div style="margin-bottom:32px;">
                                <h3 style="font-size:16px;font-weight:900;color:#000000;margin:0 0 16px 0;letter-spacing:-0.025em;border-left:4px solid #EF4444;padding-left:12px;">
                                    📝 新发布的文章
                                </h3>

                                @foreach($newPosts as $post)
                                <div style="margin-bottom:16px;padding:16px;background-color:#F5F0EB;border-left:4px solid #000000;">
                                    <a href="{{ $post['url'] }}" style="font-size:15px;font-weight:700;color:#000000;text-decoration:none;display:block;margin-bottom:6px;">
                                        {{ $post['title'] }}
                                    </a>
                                    <p style="font-size:13px;color:#666666;margin:0;line-height:1.6;">
                                        {{ $post['excerpt'] }}
                                    </p>
                                    <div style="margin-top:8px;font-size:12px;color:#888888;">
                                        {{ $post['author'] }} · {{ $post['published_at'] }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(count($topComments) > 0)
                            <!-- Top Comments Section -->
                            <div style="margin-bottom:32px;">
                                <h3 style="font-size:16px;font-weight:900;color:#000000;margin:0 0 16px 0;letter-spacing:-0.025em;border-left:4px solid #EF4444;padding-left:12px;">
                                    💬 热门评论
                                </h3>

                                @foreach($topComments as $comment)
                                <div style="margin-bottom:12px;padding:12px 16px;background-color:#F5F0EB;border-left:4px solid #000000;">
                                    <p style="font-size:14px;color:#333333;margin:0 0 8px 0;line-height:1.6;">
                                        "{{ $comment['body'] }}"
                                    </p>
                                    <div style="font-size:12px;color:#888888;">
                                        — {{ $comment['author'] }} 在《{{ $comment['post_title'] }}》
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(count($newSubscribers) > 0)
                            <!-- New Subscribers Section -->
                            <div style="margin-bottom:32px;">
                                <h3 style="font-size:16px;font-weight:900;color:#000000;margin:0 0 16px 0;letter-spacing:-0.025em;border-left:4px solid #EF4444;padding-left:12px;">
                                    👥 新订阅者
                                </h3>
                                <p style="font-size:14px;color:#666666;margin:0;">
                                    本周新增 {{ count($newSubscribers) }} 位订阅者 🎉
                                </p>
                            </div>
                            @endif

                            <!-- Action Button -->
                            @if(!empty($dashboardUrl))
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                                <tr>
                                    <td align="center" style="background-color:#EF4444;border-radius:0;">
                                        <a href="{{ $dashboardUrl }}" target="_blank" style="display:inline-block;padding:14px 36px;font-size:13px;font-weight:700;color:#FFFFFF;text-decoration:none;text-transform:uppercase;letter-spacing:0.1em;">
                                            查看完整数据
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
