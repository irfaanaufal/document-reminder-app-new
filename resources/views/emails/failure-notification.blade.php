<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Gagal</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;">
        <tr>
            <td align="center" style="padding:20px 10px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color:#ffffff;padding:24px 30px;border-bottom:1px solid #e5e7eb;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="76" valign="middle" style="padding-right:16px;">
                                        <img src="{{ $message->embed(public_path('images/logo_email.png')) }}" width="60" height="60" alt="Logo" style="display:block;">
                                    </td>
                                    <td valign="middle">
                                        <div style="color:#4C1D95;font-size:17px;font-weight:bold;letter-spacing:0.3px;">PT SINDANGASIH MAKMUR</div>
                                        <div style="color:#6B7280;font-size:13px;margin-top:2px;">Sistem Reminder Dokumen</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Summary -->
                    <tr>
                        <td style="padding:24px 30px 0 30px;">
                            <p style="margin:0 0 8px 0;color:#374151;font-size:15px;line-height:1.6;">
                                Ringkasan Pengiriman Reminder
                            </p>
                            <p style="margin:0 0 20px 0;color:#6b7280;font-size:13px;">
                                Tanggal: {{ $date }}
                            </p>
                        </td>
                    </tr>

                    <!-- Stats -->
                    <tr>
                        <td style="padding:0 30px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="33%" style="padding:0 4px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;">
                                            <tr>
                                                <td style="padding:14px;text-align:center;">
                                                    <div style="color:#166534;font-size:24px;font-weight:bold;">{{ $successCount }}</div>
                                                    <div style="color:#166534;font-size:12px;margin-top:4px;">Berhasil</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="33%" style="padding:0 4px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#fef2f2;border:1px solid #fecaca;border-radius:6px;">
                                            <tr>
                                                <td style="padding:14px;text-align:center;">
                                                    <div style="color:#991B1B;font-size:24px;font-weight:bold;">{{ $total }}</div>
                                                    <div style="color:#991B1B;font-size:12px;margin-top:4px;">Gagal</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="33%" style="padding:0 4px;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;">
                                            <tr>
                                                <td style="padding:14px;text-align:center;">
                                                    <div style="color:#111827;font-size:24px;font-weight:bold;">{{ $total + $successCount }}</div>
                                                    <div style="color:#111827;font-size:12px;margin-top:4px;">Total</div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Failed Documents -->
                    <tr>
                        <td style="padding:24px 30px 0 30px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
                                <tr>
                                    <td style="background-color:#fef2f2;padding:12px 20px;border-bottom:1px solid #fecaca;">
                                        <strong style="color:#991B1B;font-size:14px;">DETAIL DOKUMEN GAGAL</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            @foreach($failedLogs as $index => $log)
                                                @php
                                                    $doc = $log->documentReminder;
                                                    $docNumber = $doc->no_dokumen ?? '-';
                                                    $docName = $doc->nama_dokumen ?? '-';
                                                    $recipientName = $log->recipient_name ?? '-';
                                                    $recipientEmail = $log->recipient_email ?? '-';
                                                    $rule = strtoupper($log->reminder_rule ?? '');
                                                    $response = $log->provider_response;
                                                    $errorMessage = is_array($response) ? ($response['message'] ?? json_encode($response)) : ($response ?? '-');
                                                @endphp
                                                <tr>
                                                    <td style="padding:{{ $loop->first ? '16' : '12' }}px 20px {{ $loop->last ? '16' : '8' }}px 20px;{{ !$loop->last ? 'border-bottom:1px solid #f3f4f6;' : '' }}">
                                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                                            <tr>
                                                                <td style="color:#111827;font-size:14px;font-weight:600;padding-bottom:6px;">
                                                                    {{ $index + 1 }}. {{ $docNumber }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="color:#6b7280;font-size:13px;padding-bottom:3px;">
                                                                    Dokumen: {{ $docName }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="color:#6b7280;font-size:13px;padding-bottom:3px;">
                                                                    PIC: {{ $recipientName }} ({{ $recipientEmail }})
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="color:#6b7280;font-size:13px;padding-bottom:3px;">
                                                                    Rule: {{ $rule }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="color:#DC2626;font-size:13px;font-weight:500;">
                                                                    Error: {{ $errorMessage }}
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Action -->
                    <tr>
                        <td style="padding:20px 30px 0 30px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;">
                                <tr>
                                    <td style="padding:14px 16px;color:#1e40af;font-size:14px;line-height:1.5;">
                                        💡 Silakan cek <strong>Logs Reminder</strong> untuk detail lebih lanjut. Retry dapat dilakukan melalui halaman Logs Reminder.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:30px 30px 24px 30px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding-top:20px;">
                                        <p style="margin:0 0 8px 0;color:#9ca3af;font-size:12px;line-height:1.5;">
                                            Email ini dikirim otomatis oleh sistem Reminder Dokumen PT Sindangasih Makmur.
                                        </p>
                                        <p style="margin:0;color:#9ca3af;font-size:12px;line-height:1.5;">
                                            Mohon tidak membalas email ini. Untuk pertanyaan, silakan hubungi bagian IT.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
