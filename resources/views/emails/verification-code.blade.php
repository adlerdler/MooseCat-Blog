<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} — Verification</title>
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

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:28px;">
                                <tr>
                                    <td>
                                        <span style="display:inline-block;background-color:#000000;color:#FFFFFF;padding:4px 12px;font-size:9px;font-weight:800;letter-spacing:0.35em;text-transform:uppercase;">
                                            CONSTRUCT // VERIFICATION
                                        </span>
                                        <span style="display:block;margin-top:8px;font-size:9px;font-weight:700;letter-spacing:0.25em;color:#999999;text-transform:uppercase;">
                                            AUTH_02
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Main Card -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;border:3px solid #000000;background-color:#FAFAFA;">
                                <tr>
                                    <td style="padding:36px;">

                                        <h2 style="margin:0 0 8px;font-size:22px;font-weight:900;color:#000000;line-height:1.2;text-transform:uppercase;">
                                            Verification Code
                                        </h2>
                                        <p style="margin:0 0 24px;font-size:15px;color:#666666;line-height:1.6;">
                                            Use the code below to verify your email address and complete registration.
                                        </p>

                                        <!-- Code Display -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:24px;">
                                            <tr>
                                                <td style="padding:24px;background-color:#FFFFFF;border:3px solid #000000;text-align:center;">
                                                    <span style="font-size:40px;font-weight:900;color:#000000;letter-spacing:12px;font-family:'Courier New',Courier,monospace;">
                                                        {{ $code }}
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Expiry Notice -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:24px;">
                                            <tr>
                                                <td style="padding:16px 20px;background-color:#FFF7ED;border-left:4px solid #F97316;">
                                                    <span style="font-size:13px;font-weight:700;color:#9A3412;">Expires in 5 minutes</span>
                                                    <span style="font-size:12px;color:#C2410C;display:block;margin-top:2px;">If you didn't request this code, please ignore this email.</span>
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
