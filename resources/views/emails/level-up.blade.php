<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} — Level Up!</title>
</head>
<body style="margin:0;padding:0;background-color:#F5F0EB;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
        <tr>
            <td align="center" style="padding:48px 24px;">

                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;border-collapse:collapse;">

                    <!-- Header Bar -->
                    <tr>
                        <td style="background-color:#000000;padding:28px 36px;border-bottom:4px solid:#EF4444;">
                            <span style="font-size:22px;font-weight:900;letter-spacing:-0.025em;color:#FFFFFF;text-transform:uppercase;">
                                {{ $brandName }}
                            </span>
                        </td>
                    </tr>

                    <!-- Level Indicator Bar -->
                    <tr>
                        <td style="height:6px;background-color:{{ $levelColor }};font-size:0;line-height:0;">&nbsp;</td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="background-color:#FFFFFF;padding:40px 36px;border-left:4px solid #000000;border-right:4px solid #000000;">

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:28px;">
                                <tr>
                                    <td>
                                        <span style="display:inline-block;background-color:#000000;color:#FFFFFF;padding:4px 12px;font-size:9px;font-weight:800;letter-spacing:0.35em;text-transform:uppercase;">
                                            LEVEL UP // NOTIFICATION
                                        </span>
                                        <span style="display:block;margin-top:8px;font-size:9px;font-weight:700;letter-spacing:0.25em;color:#999999;text-transform:uppercase;">
                                            RANK_{{ strtoupper($levelName) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Main Card -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;border:3px solid #000000;background-color:#FAFAFA;">
                                <tr>
                                    <td style="padding:36px;">

                                        <!-- Level Badge -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:24px;">
                                            <tr>
                                                <td align="center">
                                                    <div style="display:inline-block;width:80px;height:80px;border-radius:50%;background-color:{{ $levelColor }};margin-bottom:12px;line-height:80px;text-align:center;">
                                                        <span style="font-size:36px;color:#FFFFFF;">{{ $levelIcon }}</span>
                                                    </div>
                                                    <h2 style="margin:0;font-size:28px;font-weight:900;color:{{ $levelColor }};line-height:1.2;">
                                                        {{ $levelName }}
                                                    </h2>
                                                </td>
                                            </tr>
                                        </table>

                                        <h3 style="margin:0 0 16px;font-size:18px;font-weight:700;color:#000000;line-height:1.4;text-align:center;">
                                            🎉 Congratulations, {{ $userName }}!
                                        </h3>
                                        <p style="margin:0 0 24px;font-size:15px;color:#333333;line-height:1.8;text-align:center;">
                                            You've reached <strong style="color:{{ $levelColor }};">{{ $levelName }}</strong> with <strong>{{ $totalPoints }} points</strong>.
                                        </p>

                                        @if($levelDescription)
                                        <p style="margin:0 0 20px;font-size:14px;color:#666666;line-height:1.6;text-align:center;">
                                            {{ $levelDescription }}
                                        </p>
                                        @endif

                                        <!-- Benefits -->
                                        @if(!empty($benefits))
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:24px;">
                                            <tr>
                                                <td style="padding:16px 20px;background-color:#F0FDF4;border-left:4px solid #22C55E;">
                                                    <span style="font-size:13px;font-weight:700;color:#166534;">Unlocked Benefits</span>
                                                    @foreach($benefits as $benefit)
                                                    <span style="font-size:12px;color:#4D7C0F;display:block;margin-top:4px;">✓ {{ $benefit }}</span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        </table>
                                        @endif

                                        <!-- Next Level Hint -->
                                        @if($nextLevelName)
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:24px;">
                                            <tr>
                                                <td style="padding:16px 20px;background-color:#FFF7ED;border-left:4px solid #F97316;">
                                                    <span style="font-size:13px;font-weight:700;color:#9A3412;">Next: {{ $nextLevelName }}</span>
                                                    <span style="font-size:12px;color:#C2410C;display:block;margin-top:2px;">{{ $pointsToNext }} more points to unlock</span>
                                                </td>
                                            </tr>
                                        </table>
                                        @endif

                                        <!-- CTA -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ $siteUrl }}" style="display:inline-block;background-color:{{ $levelColor }};color:#FFFFFF;padding:14px 40px;font-size:14px;font-weight:800;text-decoration:none;letter-spacing:0.05em;text-transform:uppercase;border:2px solid {{ $levelColor }};">
                                                        Keep Exploring
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color:#000000;padding:24px 36px;border-top:4px solid #EF4444;">
                            <p style="margin:0;font-size:10px;color:#888888;letter-spacing:0.15em;text-align:center;text-transform:uppercase;">
                                {{ $brandName }} &middot; {{ date('Y') }} &middot; {{ $timestamp }}
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
