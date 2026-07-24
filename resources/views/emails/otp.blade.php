<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kode OTP</title>
</head>
<body style="font-family: 'Roboto', Arial, sans-serif; background-color: #e8f0fe; padding: 40px 0; margin: 0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table role="presentation" width="450" cellpadding="0" cellspacing="0"
                    style="background:#ffffff; border-radius: 8px; padding: 32px; box-shadow: 0 1px 3px rgba(60,64,67,.3);">
                    <tr>
                        <td align="center" style="padding-bottom: 16px;">
                            <h2 style="color:#202124; margin: 0;">Reset Password ATK App</h2>
                        </td>
                    </tr>
                    <tr>
                        <td style="color:#5f6368; font-size: 14px; line-height: 1.6;">
                            <p>Kami menerima permintaan untuk mereset password akun Anda. Gunakan kode OTP di bawah ini untuk melanjutkan:</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 24px 0;">
                            <span style="display:inline-block; font-size: 32px; font-weight: 700; letter-spacing: 8px; color:#1a73e8; background:#e8f0fe; padding: 12px 24px; border-radius: 8px;">
                                {{ $otp }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="color:#5f6368; font-size: 13px; line-height: 1.6;">
                            <p>Kode ini berlaku selama <strong>10 menit</strong>. Jangan bagikan kode ini kepada siapa pun, termasuk pihak yang mengatasnamakan admin ATK App.</p>
                            <p>Jika Anda tidak merasa meminta reset password, abaikan email ini.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>