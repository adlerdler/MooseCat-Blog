<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $brandName }} — Welcome</title>
    <style type="text/css">
        @media only screen and (max-width: 620px) {
            .email-container {
                width: 100% !important;
                max-width: 100% !important;
            }
            .mobile-container-padding {
                padding: 0 !important;
            }
            .mobile-padding {
                padding: 24px 20px !important;
            }
            .mobile-header-padding {
                padding: 20px 24px !important;
            }
            .mobile-footer-padding {
                padding: 18px 24px !important;
            }
            .mobile-card-padding {
                padding: 24px !important;
            }
            .mobile-cta-button {
                width: 100% !important;
                padding: 16px 24px !important;
                display: block !important;
                box-sizing: border-box !important;
                text-align: center !important;
            }
            h2 {
                font-size: 22px !important;
            }
            h3 {
                font-size: 15px !important;
            }
            p {
                font-size: 14px !important;
            }
        }
        @media only screen and (max-width: 480px) {
            .mobile-padding {
                padding: 16px 12px !important;
            }
            .mobile-header-padding {
                padding: 16px 16px !important;
            }
            .mobile-footer-padding {
                padding: 14px 16px !important;
            }
            .mobile-card-padding {
                padding: 20px !important;
            }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#F5F0EB;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;">
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
        <tr>
            <td class="mobile-container-padding" align="center" style="padding:32px 16px;">

                <table role="presentation" class="email-container" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width:600px;border-collapse:collapse;">

                    <!-- Header Bar -->
                    <tr>
                        <td class="mobile-header-padding" style="background-color:#000000;padding:28px 36px;border-bottom:4px solid #EF4444;">
                            <span style="font-size:20px;font-weight:900;letter-spacing:-0.025em;color:#FFFFFF;text-transform:uppercase;">
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
                        <td class="mobile-padding" style="background-color:#FFFFFF;padding:40px 36px;border-left:4px solid #000000;border-right:4px solid #000000;">

                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:24px;">
                                <tr>
                                    <td>
                                        <span style="display:inline-block;background-color:#000000;color:#FFFFFF;padding:4px 12px;font-size:9px;font-weight:800;letter-spacing:0.35em;text-transform:uppercase;">
                                            CONSTRUCT // WELCOME
                                        </span>
                                        <span style="display:block;margin-top:8px;font-size:9px;font-weight:700;letter-spacing:0.25em;color:#999999;text-transform:uppercase;">
                                            ONBOARDING_01
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Main Card -->
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;border:3px solid #000000;background-color:#FAFAFA;">
                                <tr>
                                    <td class="mobile-card-padding" style="padding:36px;">

                                        <h2 style="margin:0 0 8px;font-size:26px;font-weight:900;color:#000000;line-height:1.2;">
                                            Welcome to {{ $brandName }}
                                        </h2>
                                        <h3 style="margin:0 0 24px;font-size:16px;font-weight:400;color:#666666;line-height:1.4;">
                                            Hi {{ $userName }}, glad to have you here.
                                        </h3>

                                        <p style="margin:0 0 16px;font-size:15px;color:#333333;line-height:1.8;">
                                            Your account has been created successfully. Start exploring everything {{ $brandName }} has to offer.
                                        </p>

                                        @if($provider)
                                        <p style="margin:0 0 24px;font-size:14px;color:#666666;line-height:1.6;">
                                            You signed up via <strong>{{ ucfirst($provider) }}</strong> — no password needed.
                                        </p>
                                        @endif

                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;margin-bottom:24px;">
                                            <tr>
                                                <td style="padding:14px 18px;background-color:#F0FDF4;border-left:4px solid #22C55E;">
                                                    <span style="font-size:14px;font-weight:700;color:#166534;">+40 Points</span>
                                                    <span style="font-size:13px;color:#4D7C0F;display:block;margin-top:2px;">Registration bonus credited to your account</span>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- CTA -->
                                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="border-collapse:collapse;">
                                            <tr>
                                                <td align="center">
                                                    <a href="{{ $siteUrl }}" class="mobile-cta-button" style="display:inline-block;background-color:#000000;color:#FFFFFF;padding:14px 40px;font-size:14px;font-weight:800;text-decoration:none;letter-spacing:0.05em;text-transform:uppercase;border:2px solid #000000;">
                                                        Start Exploring
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
                        <td class="mobile-footer-padding" style="background-color:#000000;padding:24px 36px;border-top:4px solid #EF4444;">
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
