<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Dokumen</title>
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f3f4f6;">
        <tr>
            <td align="center" style="padding:20px 10px;">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,0.1);">

                    {{-- Header --}}
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

                    {{-- Status Badge --}}
                    @php
                        $daysLeft = isset($sisa_hari) ? (int) $sisa_hari : null;
                        if ($daysLeft === 0) {
                            $badgeColor = '#DC2626';
                            $badgeText = 'JATUH TEMPO HARI INI';
                        } elseif ($daysLeft !== null && $daysLeft < 0) {
                            $badgeColor = '#991B1B';
                            $badgeText = 'LEWAT ' . abs($daysLeft) . ' HARI';
                        } elseif ($daysLeft !== null && $daysLeft <= 7) {
                            $badgeColor = '#D97706';
                            $badgeText = $daysLeft . ' HARI LAGI';
                        } elseif ($daysLeft !== null && $daysLeft <= 14) {
                            $badgeColor = '#2563EB';
                            $badgeText = $daysLeft . ' HARI LAGI';
                        } else {
                            $badgeColor = '#6B7280';
                            $badgeText = $sisa_waktu ?? '-';
                        }
                    @endphp
                    <tr>
                        <td style="padding:24px 30px 0 30px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="background-color:{{ $badgeColor }};color:#ffffff;font-size:15px;font-weight:bold;padding:14px 20px;border-radius:6px;text-align:center;letter-spacing:0.5px;">
                                        {{ $badgeText }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Content --}}
                    <tr>
                        <td style="padding:24px 30px 0 30px;">
                            <p style="margin:0 0 16px 0;color:#374151;font-size:15px;line-height:1.6;">
                                Yth. Bapak/Ibu <strong>{{ $pic_nama ?: 'PIC' }}</strong>,
                            </p>
                            <p style="margin:0 0 20px 0;color:#374151;font-size:15px;line-height:1.6;">
                                {{ $intro }}
                            </p>
                        </td>
                    </tr>

                    {{-- Document Details --}}
                    <tr>
                        <td style="padding:0 30px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
                                <tr>
                                    <td style="background-color:#f9fafb;padding:12px 20px;border-bottom:1px solid #e5e7eb;">
                                        <strong style="color:#111827;font-size:14px;">DETAIL DOKUMEN</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="padding:12px 20px;border-bottom:1px solid #f3f4f6;width:170px;color:#6b7280;font-size:14px;">Nama Dokumen</td>
                                                <td style="padding:12px 20px;border-bottom:1px solid #f3f4f6;color:#111827;font-size:14px;font-weight:600;">{{ $nama_dokumen }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:12px 20px;border-bottom:1px solid #f3f4f6;color:#6b7280;font-size:14px;">Nomor Dokumen</td>
                                                <td style="padding:12px 20px;border-bottom:1px solid #f3f4f6;color:#111827;font-size:14px;font-weight:600;">{{ $no_dokumen }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:12px 20px;border-bottom:1px solid #f3f4f6;color:#6b7280;font-size:14px;">Penerbit</td>
                                                <td style="padding:12px 20px;border-bottom:1px solid #f3f4f6;color:#111827;font-size:14px;font-weight:600;">{{ $penerbit_tujuan ?: '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:12px 20px;border-bottom:1px solid #f3f4f6;color:#6b7280;font-size:14px;">Tanggal Jatuh Tempo</td>
                                                <td style="padding:12px 20px;border-bottom:1px solid #f3f4f6;color:#111827;font-size:14px;font-weight:600;">{{ $tanggal_expired }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:12px 20px;border-bottom:1px solid #f3f4f6;color:#6b7280;font-size:14px;">Sisa Waktu</td>
                                                <td style="padding:12px 20px;border-bottom:1px solid #f3f4f6;color:{{ $badgeColor }};font-size:14px;font-weight:700;">{{ $sisa_waktu }}</td>
                                            </tr>
                                            @if(!empty($attachment_name))
                                            <tr>
                                                <td style="padding:12px 20px;color:#6b7280;font-size:14px;">Lampiran</td>
                                                <td style="padding:12px 20px;color:#111827;font-size:14px;font-weight:600;">{{ $attachment_name }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:30px 30px 24px 30px;">
                            <p style="margin:0;color:#9ca3af;font-size:12px;line-height:1.6;text-align:center;">
                                Email ini dikirim otomatis oleh sistem Reminder Dokumen PT Sindangasih Makmur.<br>
                                Mohon tidak membalas email ini. Untuk pertanyaan, silakan hubungi bagian IT.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
