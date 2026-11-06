<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your MediSlot Verification Code</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" style="max-width: 520px; background-color: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);" cellspacing="0" cellpadding="0">
                    <!-- Brand Banner -->
                    <tr>
                        <td style="background-color: #0d4a46; padding: 28px 32px; text-align: left;">
                            <table role="presentation" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <div style="background-color: #14b8a6; color: #ffffff; width: 32px; height: 32px; border-radius: 8px; font-weight: 900; font-size: 18px; line-height: 32px; text-align: center;">+</div>
                                    </td>
                                    <td style="vertical-align: middle; padding-left: 12px;">
                                        <span style="color: #ffffff; font-size: 20px; font-weight: bold; letter-spacing: -0.5px;">MediSlot</span>
                                        <span style="display: block; color: #99f6e4; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">Hospital Onboarding</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Main Content -->
                    <tr>
                        <td style="padding: 36px 32px;">
                            <h1 style="margin: 0 0 12px 0; font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">
                                Verify your administrator email
                            </h1>
                            <p style="margin: 0 0 20px 0; font-size: 14px; line-height: 22px; color: #475569;">
                                Hello <strong>{{ $adminName }}</strong>,
                            </p>
                            <p style="margin: 0 0 28px 0; font-size: 14px; line-height: 22px; color: #475569;">
                                You are completing registration for <strong>{{ $hospitalName }}</strong> on the MediSlot healthcare network. Please use the verification code below to verify your email address and activate your account.
                            </p>

                            <!-- OTP Box -->
                            <div style="background-color: #f0fdfa; border: 2px dashed #0d9488; border-radius: 12px; padding: 24px; text-align: center; margin-bottom: 28px;">
                                <span style="display: block; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #0f766e; margin-bottom: 8px;">
                                    Your 6-Digit Verification Code
                                </span>
                                <div style="font-family: 'SF Mono', Monaco, Menlo, Consolas, monospace; font-size: 36px; font-weight: 800; letter-spacing: 10px; color: #0f172a; line-height: 1;">
                                    {{ $otp }}
                                </div>
                                <span style="display: block; font-size: 12px; color: #64748b; margin-top: 10px;">
                                    Expires in <strong>15 minutes</strong>
                                </span>
                            </div>

                            <p style="margin: 0 0 8px 0; font-size: 12px; line-height: 18px; color: #94a3b8;">
                                If you did not initiate this registration for {{ $hospitalName }}, you can safely ignore this email. No account will be activated without this code.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; border-top: 1px solid #f1f5f9; padding: 20px 32px; text-align: center;">
                            <p style="margin: 0; font-size: 11px; color: #94a3b8; line-height: 18px;">
                                &copy; {{ date('Y') }} MediSlot Healthcare Platform. All rights reserved.<br>
                                Automated Security &amp; Identity Verification
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
