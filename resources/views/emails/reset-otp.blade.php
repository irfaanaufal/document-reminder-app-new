<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Reset Password</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.06);">
                    <tr>
                        <td style="padding:28px 32px 8px 32px;">
                            <p style="margin:0;font-size:14px;color:#64748b;">{{ $app_name ?? 'Reminder App' }}</p>
                            <h1 style="margin:8px 0 0 0;font-size:20px;color:#0f172a;">Kode OTP Reset Password</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 32px;">
                            <p style="margin:0 0 16px 0;font-size:14px;color:#334155;line-height:1.6;">
                                Kami menerima permintaan untuk mengatur ulang kata sandi akun Anda.
                                Gunakan kode di bawah ini untuk melanjutkan proses.
                            </p>
                            <div style="text-align:center;background-color:#f1f5f9;border-radius:12px;padding:20px 12px;margin:8px 0 20px 0;">
                                <span style="display:inline-block;font-size:32px;font-weight:bold;letter-spacing:10px;color:#0f172a;">{{ $otp }}</span>
                            </div>
                            <p style="margin:0 0 8px 0;font-size:13px;color:#64748b;line-height:1.5;">
                                Kode berlaku selama <strong>15 menit</strong>. Jika Anda tidak meminta reset ini,
                                abaikan email ini — kata sandi Anda tidak akan berubah.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 32px 28px 32px;">
                            <p style="margin:0;font-size:12px;color:#94a3b8;">
                                Email ini dikirim otomatis oleh sistem {{ $app_name ?? 'Reminder App' }}. Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
