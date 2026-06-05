<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} — 内容摘要</title>
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

                            <!-- Greeting -->
                            <h2 style="font-size:20px;font-weight:900;color:#000000;margin:0 0 8px 0;letter-spacing:-0.025em;">
                                👋 {{ $greeting }}, {{ $userName }}!
                            </h2>
                            <p style="font-size:14px;color:#888888;margin:0 0 32px 0;">
                                这是你的 {{ $periodLabel }} 内容摘要 · {{ $dateRange }}
                            </p>

                            @if(count($newPosts) > 0)
                            <!-- Featured Posts Section -->
                            <div style="margin-bottom:32px;">
                                <h3 style="font-size:16px;font-weight:900;color:#000000;margin:0 0 16px 0;letter-spacing:-0.025em;border-left:4px solid #EF4444;padding-left:12px;">
                                    📝 你可能感兴趣的文章
                                </h3>

                                @foreach($newPosts as $post)
                                <div style="margin-bottom:20px;padding:20px;background-color:#F5F0EB;border-left:4px solid #000000;">
                                    @if(!empty($post['cover_image']))
                                    <img src="{{ $post['cover_image'] }}" alt="{{ $post['title'] }}" style="width:100%;max-height:200px;object-fit:cover;margin-bottom:12px;display:block;" />
                                    @endif
                                    <a href="{{ $post['url'] }}" style="font-size:16px;font-weight:700;color:#000000;text-decoration:none;display:block;margin-bottom:8px;">
                                        {{ $post['title'] }}
                                    </a>
                                    <p style="font-size:14px;color:#666666;margin:0 0 12px 0;line-height:1.6;">
                                        {{ $post['excerpt'] }}
                                    </p>
                                    <div style="font-size:12px;color:#888888;">
                                        {{ $post['author'] }} · {{ $post['category'] }} · {{ $post['published_at'] }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(count($newVideos) > 0)
                            <!-- New Videos Section -->
                            <div style="margin-bottom:32px;">
                                <h3 style="font-size:16px;font-weight:900;color:#000000;margin:0 0 16px 0;letter-spacing:-0.025em;border-left:4px solid #EF4444;padding-left:12px;">
                                    🎬 最新视频
                                </h3>

                                @foreach($newVideos as $video)
                                <div style="margin-bottom:16px;padding:16px;background-color:#F5F0EB;border-left:4px solid #000000;">
                                    <a href="{{ $video['url'] }}" style="font-size:15px;font-weight:700;color:#000000;text-decoration:none;display:block;margin-bottom:6px;">
                                        {{ $video['title'] }}
                                    </a>
                                    <p style="font-size:13px;color:#666666;margin:0;">
                                        {{ $video['description'] }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(count($newProjects) > 0)
                            <!-- New Projects Section -->
                            <div style="margin-bottom:32px;">
                                <h3 style="font-size:16px;font-weight:900;color:#000000;margin:0 0 16px 0;letter-spacing:-0.025em;border-left:4px solid #EF4444;padding-left:12px;">
                                    🚀 最新项目
                                </h3>

                                @foreach($newProjects as $project)
                                <div style="margin-bottom:16px;padding:16px;background-color:#F5F0EB;border-left:4px solid #000000;">
                                    <a href="{{ $project['url'] }}" style="font-size:15px;font-weight:700;color:#000000;text-decoration:none;display:block;margin-bottom:6px;">
                                        {{ $project['title'] }}
                                    </a>
                                    <p style="font-size:13px;color:#666666;margin:0;">
                                        {{ $project['description'] }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if(!empty($personalStats))
                            <!-- Personal Stats -->
                            <div style="margin-bottom:32px;padding:20px;background-color:#F5F0EB;border-left:4px solid #EF4444;">
                                <h3 style="font-size:16px;font-weight:900;color:#000000;margin:0 0 12px 0;letter-spacing:-0.025em;">
                                    📊 你的参与情况
                                </h3>
                                <div style="font-size:14px;color:#666666;line-height:1.8;">
                                    <div>评论数：{{ $personalStats['comments'] }}</div>
                                    <div>点赞数：{{ $personalStats['likes'] }}</div>
                                    <div>当前积分：{{ $personalStats['points'] }}</div>
                                </div>
                            </div>
                            @endif

                            <!-- Action Button -->
                            @if(!empty($siteUrl))
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                                <tr>
                                    <td align="center" style="background-color:#EF4444;border-radius:0;">
                                        <a href="{{ $siteUrl }}" target="_blank" style="display:inline-block;padding:14px 36px;font-size:13px;font-weight:700;color:#FFFFFF;text-decoration:none;text-transform:uppercase;letter-spacing:0.1em;">
                                            探索更多内容
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
                            <p style="font-size:10px;color:#666666;margin:6px 0 0 0;">
                                退订链接：<a href="{{ $unsubscribeUrl }}" style="color:#888888;">点击退订</a>
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
