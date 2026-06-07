<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class FonnteService
{
    public function buildReminderMessage(array $data): string
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

        $closingLine = 'Mohon segera dicek dan diselesaikan. Jangan sampai melewati jatuh tempo.';

        if ($daysLeftValue !== null) {
            if ($daysLeftValue === 0) {
                $closingLine = 'Wajib diproses hari ini juga agar tidak melewati batas waktu.';
            } elseif ($daysLeftValue < 0) {
                $closingLine = 'Dokumen ini sudah lewat jatuh tempo. Mohon segera ditangani tanpa penundaan.';
            }
        }

        $intro = 'Mohon perhatian terhadap dokumen berikut yang memerlukan peninjauan:';

        if ($reminderRule === 'h-7') {
            $intro = 'Dokumen berikut akan segera mencapai tanggal jatuh tempo dan memerlukan tindak lanjut:';
        } elseif ($reminderRule === 'h-0') {
            $intro = 'Dokumen berikut mencapai tanggal jatuh tempo hari ini dan memerlukan tindakan segera:';
        } elseif ($reminderRule === 'h-14') {
            $intro = 'Dokumen berikut telah memasuki periode pemantauan menjelang tanggal jatuh tempo:';
        }

        // Format sisa waktu dengan tampilan yang lebih ramah
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

    public function normalizePhoneForWhatsapp(?string $phone): string
    {
        $phone = trim((string) $phone);

        if ($phone === '') {
            return '';
        }

        $phone = preg_replace('/\D+/', '', $phone) ?? '';

        if ($phone === '') {
            return '';
        }

        if (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '0' . $phone;
        } elseif (! str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }

        return $phone;
    }

    public function sendMessage(string $targetPhone, string $message): array
    {
        $token = (string) config('services.fonnte.token');
        $baseUrl = (string) config('services.fonnte.base_url', 'https://api.fonnte.com/send');
        $sender = (string) config('services.fonnte.sender');

        if ($token === '') {
            throw new InvalidArgumentException('Fonnte token belum diatur di .env.');
        }

        $payload = [
            'target' => $targetPhone,
            'message' => $message,
            'countryCode' => '62',
        ];

        if ($sender !== '') {
            $payload['sender'] = $sender;
        }

        $response = Http::withHeaders([
                'Authorization' => $token,
            ])
            ->acceptJson()
            ->post($baseUrl, $payload);

        return [
            'ok' => $response->successful(),
            'status' => $response->status(),
            'body' => $response->json() ?? $response->body(),
        ];
    }
}
