<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ReminderMailService
{
    public function buildReminderBody(array $data): string
    {
        $picName = trim((string) ($data['pic_nama'] ?? ''));
        $documentName = trim((string) ($data['nama_dokumen'] ?? '-'));
        $documentNumber = trim((string) ($data['no_dokumen'] ?? '-'));
        $publisher = trim((string) ($data['penerbit_tujuan'] ?? '-'));
        $expiredDate = trim((string) ($data['tanggal_expired'] ?? '-'));
        $daysLeftRaw = $data['sisa_hari'] ?? null;
        $daysLeft = is_null($daysLeftRaw) ? '-' : (string) $daysLeftRaw;
        $reminderRule = strtolower(trim((string) ($data['reminder_rule'] ?? '')));
        $daysLeftValue = is_numeric($daysLeft) ? (int) $daysLeft : null;

        $closingLine = 'Demikian informasi ini kami sampaikan. Atas perhatian dan tindak lanjutnya, kami ucapkan terima kasih.';

        if ($daysLeftValue !== null) {
            if ($daysLeftValue === 0) {
                $closingLine = 'Berkaitan dengan batas waktu yang berakhir hari ini, mohon dokumen ini menjadi prioritas utama untuk segera diproses. Terima kasih atas perhatian dan kerjasamanya.';
            } elseif ($daysLeftValue < 0) {
                $closingLine = 'Dokumen ini telah melewati batas waktu. Mohon segera dilakukan evaluasi dan penanganan untuk menghindari konsekuensi lebih lanjut. Terima kasih.';
            }
        }

        $intro = 'Kami informasikan bahwa dokumen berikut telah memasuki masa pemantauan dan memerlukan persiapan penanganan:';

        if ($reminderRule === 'h-7') {
            $intro = 'Peringatan: Dokumen berikut akan jatuh tempo dalam 7 hari. Mohon segera dilakukan pengecekan dan persiapan perpanjangan atau pemrosesan:';
        } elseif ($reminderRule === 'h-0') {
            $intro = 'URGENT: Dokumen berikut mencapai batas waktu hari ini. Mohon segera diproses untuk menghindari risiko kedaluwarsa:';
        } elseif ($reminderRule === 'h-14') {
            $intro = 'Dokumen berikut akan mencapai tanggal jatuh tempo dalam 14 hari ke depan. Mohon dapat dipersiapkan tindak lanjut yang diperlukan:';
        }

        $sisaWaktuDisplay = '-';
        if (is_numeric($daysLeft)) {
            $v = (int) $daysLeft;
            if ($v === 0) {
                $sisaWaktuDisplay = 'Hari ini';
            } elseif ($v > 0) {
                $sisaWaktuDisplay = $v . ' hari';
            } else {
                $sisaWaktuDisplay = 'LEWAT ' . abs($v) . ' hari';
            }
        }

        $lines = [
            'REMINDER DOKUMEN',
            '',
            $picName !== '' ? 'Halo ' . $picName . ',' : 'Halo,',
            '',
            $intro,
            '',
            'Nama Dokumen: ' . $documentName,
            'Nomor Dokumen: ' . $documentNumber,
            'Penerbit: ' . ($publisher !== '' ? $publisher : '-'),
            'Tanggal Jatuh Tempo: ' . ($expiredDate !== '' ? $expiredDate : '-'),
            'Sisa Waktu: ' . $sisaWaktuDisplay,
            '',
            $closingLine,
            '',
            'Makasih ya.',
        ];

        return implode("\n", array_filter($lines, fn ($line) => $line !== ''));
    }

    public function buildSubject(array $data): string
    {
        $documentName = trim((string) ($data['nama_dokumen'] ?? '-'));
        $reminderRule = strtolower(trim((string) ($data['reminder_rule'] ?? '')));
        $daysLeftRaw = $data['sisa_hari'] ?? null;
        $daysLeft = is_numeric($daysLeftRaw) ? (int) $daysLeftRaw : null;

        $prefix = '[Reminder]';

        if ($reminderRule === 'h-0') {
            $prefix = '[REMINDER - JATUH TEMPO HARI INI]';
        } elseif ($reminderRule === 'h-7') {
            $prefix = '[REMINDER - 7 HARI LAGI]';
        } elseif ($reminderRule === 'h-14') {
            $prefix = '[REMINDER - 14 HARI LAGI]';
        } elseif ($reminderRule === 'monthly') {
            $prefix = '[REMINDER - BULANAN]';
        }

        if ($daysLeft !== null && $daysLeft < 0) {
            $prefix = '[REMINDER - MELEWATI JATUH TEMPO]';
        }

        return $prefix . ' ' . $documentName;
    }

    public function sendHtmlEmail(
        string $toEmail,
        string $subject,
        string $viewName,
        array $viewData,
        ?string $attachmentPath = null,
        ?string $attachmentName = null
    ): array {
        try {
            Mail::send($viewName, $viewData, function ($message) use ($toEmail, $subject, $attachmentPath, $attachmentName) {
                $message->to($toEmail)
                    ->subject($subject)
                    ->from(
                        config('mail.from.address'),
                        config('mail.from.name')
                    );

                if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                    $fullPath = Storage::disk('public')->path($attachmentPath);
                    $message->attach($fullPath, [
                        'as' => $attachmentName ?? basename($attachmentPath),
                    ]);
                }
            });

            return [
                'ok' => true,
                'message' => 'Email berhasil dikirim ke ' . $toEmail,
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function sendEmail(
        string $toEmail,
        string $subject,
        string $body,
        ?string $attachmentPath = null,
        ?string $attachmentName = null
    ): array {
        try {
            $isHtml = str_starts_with(trim($body), '<!DOCTYPE') || str_starts_with(trim($body), '<html');

            $mailFn = function ($message) use ($toEmail, $subject, $attachmentPath, $attachmentName, $isHtml) {
                $message->to($toEmail)
                    ->subject($subject)
                    ->from(
                        config('mail.from.address'),
                        config('mail.from.name')
                    );

                if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                    $fullPath = Storage::disk('public')->path($attachmentPath);
                    $message->attach($fullPath, [
                        'as' => $attachmentName ?? basename($attachmentPath),
                    ]);
                }
            };

            if ($isHtml) {
                Mail::html($body, $mailFn);
            } else {
                Mail::raw($body, $mailFn);
            }

            return [
                'ok' => true,
                'message' => 'Email berhasil dikirim ke ' . $toEmail,
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function sendFailureNotification(Collection $failedLogs, int $successCount = 0): void
    {
        $emails = array_filter(explode(',', (string) config('services.failure_notify_email')));
        $emails = array_map('trim', $emails);
        $emails = array_filter($emails);

        if (empty($emails)) {
            return;
        }

        $total = $failedLogs->count();

        $subject = '[WARNING] Reminder Gagal - ' . $total . ' dokumen gagal terkirim';

        $viewData = [
            'failedLogs' => $failedLogs,
            'total' => $total,
            'successCount' => $successCount,
            'date' => now()->format('d-m-Y H:i'),
        ];

        foreach ($emails as $email) {
            try {
                Mail::send('emails.failure-notification', $viewData, function ($message) use ($email, $subject) {
                    $message->to($email)
                        ->subject($subject)
                        ->from(config('mail.from.address'), config('mail.from.name'));
                });
            } catch (Throwable $e) {
                report($e);
            }
        }
    }

    private function buildFailureBody(Collection $failedLogs): string
    {
        $today = now()->format('d-m-Y H:i');
        $total = $failedLogs->count();

        $lines = [
            'WARNING: REMINDER GAGAL',
            '',
            'Ringkasan Pengiriman Reminder',
            'Tanggal: ' . $today,
            'Jumlah Gagal: ' . $total,
            '',
            'Detail Gagal:',
            str_repeat('─', 40),
        ];

        $index = 1;
        foreach ($failedLogs as $log) {
            $document = $log->documentReminder;
            $docName = $document?->nama_dokumen ?? '-';
            $docNumber = $document?->no_dokumen ?? '-';
            $recipient = $log->recipient_email ?? '-';
            $recipientName = $log->recipient_name ?? '-';
            $rule = strtoupper((string) $log->reminder_rule);
            $response = $log->provider_response;
            $errorMessage = is_array($response) ? ($response['message'] ?? json_encode($response)) : ($response ?? '-');

            $lines[] = "{$index}. {$docNumber}";
            $lines[] = "   Dokumen: {$docName}";
            $lines[] = "   PIC: {$recipientName}";
            $lines[] = "   Email: {$recipient}";
            $lines[] = "   Rule: {$rule}";
            $lines[] = "   Error: {$errorMessage}";
            $lines[] = '';

            $index++;
        }

        $lines[] = str_repeat('─', 40);
        $lines[] = '';
        $lines[] = 'Silakan cek Logs Reminder untuk detail lebih lanjut.';
        $lines[] = 'Retry dapat dilakukan melalui halaman Logs Reminder.';

        return implode("\n", $lines);
    }
}
