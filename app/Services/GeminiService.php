<?php

namespace App\Services;

use App\Models\DocumentReminder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class GeminiService
{
    protected $apiKey;
    protected $model;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model');
        $this->baseUrl = config('services.gemini.base_url');
    }

    public function buildGeneralPrompt(string $userMessage): string
    {
        return <<<PROMPT
Anda adalah asisten virtual yang ramah untuk aplikasi Reminder Dokumen.

Aplikasi Reminder Dokumen adalah sistem untuk mengelola dan mengingatkan tentang dokumen-dokumen penting yang memiliki tanggal jatuh tempo. Fitur utama aplikasi:
- Membuat dan mengelola data reminder dokumen
- Mengirim notifikasi pengingat via WhatsApp
- Mengelola jenis dokumen
- Hak akses berbasis role (Super Admin, Admin, User)

Aturan jawaban:
1. Jika user bertanya tentang data dokumen spesifik (contoh: jumlah dokumen, dokumen yang expired, dll.), jawab: "Silakan login terlebih dahulu untuk mengetahui data dokumen."
2. Untuk pertanyaan umum tentang aplikasi, jawab dengan informasi yang jelas dan ramah.
3. Gunakan bahasa Indonesia.

Pertanyaan user: {$userMessage}
PROMPT;
    }

    public function buildAuthenticatedPromptWithoutAccess(string $userMessage): string
    {
        return <<<PROMPT
Anda adalah asisten virtual yang ramah untuk aplikasi Reminder Dokumen.

Aplikasi Reminder Dokumen adalah sistem untuk mengelola dan mengingatkan tentang dokumen-dokumen penting yang memiliki tanggal jatuh tempo. Fitur utama aplikasi:
- Membuat dan mengelola data reminder dokumen
- Mengirim notifikasi pengingat via WhatsApp
- Mengelola jenis dokumen
- Hak akses berbasis role (Super Admin, Admin, User)

Aturan jawaban:
1. Jika user bertanya tentang data dokumen spesifik (contoh: jumlah dokumen, dokumen yang expired, dll.), jawab: "Anda tidak memiliki izin untuk mengakses data dokumen. Silakan hubungi Super Admin untuk meminta izin."
2. Untuk pertanyaan umum tentang aplikasi atau cara penggunaan, jawab dengan informasi yang jelas dan ramah.
3. Gunakan bahasa Indonesia.

Pertanyaan user: {$userMessage}
PROMPT;
    }

    public function buildAuthenticatedPromptWithAccess(string $userMessage): string
    {
        $reminders = DocumentReminder::with(['documentType', 'internalPics'])->get();
        $today = Carbon::now()->format('Y-m-d');
        
        if ($reminders->isEmpty()) {
            $formattedReminders = "(Tidak ada data reminder dokumen di database saat ini)";
        } else {
            $formattedReminders = $reminders->map(function ($reminder) use ($today) {
                $expiredDate = $reminder->tanggal_expired ? Carbon::parse($reminder->tanggal_expired)->format('Y-m-d') : '-';
                $status = '-';
                if ($expiredDate !== '-') {
                    if ($expiredDate < $today) {
                        $status = 'Expired';
                    } elseif ($expiredDate === $today) {
                        $status = 'Expired Hari Ini';
                    } else {
                        $status = 'Aktif';
                    }
                }
                
                $internalPics = $reminder->internalPics->map(function ($pic) {
                    return ($pic->pivot->nama ?? $pic->nama) . ' (' . ($pic->pivot->no_telpon ?? $pic->no_telpon ?? '-') . ')';
                })->implode(', ');
                
                if (empty($internalPics) && $reminder->pic_nama) {
                    $internalPics = $reminder->pic_nama . ($reminder->pic_telpon ? " ({$reminder->pic_telpon})" : "");
                }
                if (empty($internalPics)) {
                    $internalPics = '-';
                }

                $externalPic = $reminder->pic_external_nama
                    ? $reminder->pic_external_nama . ($reminder->pic_external_telpon ? " ({$reminder->pic_external_telpon})" : "")
                    : '-';
                
                return "- Nama Dokumen: {$reminder->nama_dokumen}\n" .
                       "  Nomor Dokumen: {$reminder->no_dokumen}\n" .
                       "  Jenis Dokumen: {$reminder->jenis_dokumen_label}\n" .
                       "  PIC Internal: {$internalPics}\n" .
                       "  PIC Eksternal: {$externalPic}\n" .
                       "  Penerbit/Tujuan: {$reminder->penerbit_tujuan}\n" .
                       "  Tanggal Terbit: " . ($reminder->tanggal_terbit ? Carbon::parse($reminder->tanggal_terbit)->format('Y-m-d') : '-') . "\n" .
                       "  Tanggal Expired: {$expiredDate}\n" .
                       "  Status: {$status}";
            })->implode("\n\n");
        }

        return <<<PROMPT
Anda adalah asisten virtual yang membantu pengguna tentang data reminder dokumen di aplikasi Reminder Dokumen.

Berikut adalah SEMUA data reminder dokumen yang ada di database saat ini:

{$formattedReminders}

Instruksi:
1. Jawab pertanyaan user berdasarkan data di atas secara akurat
2. Gunakan bahasa Indonesia yang ramah dan jelas
3. Jika pertanyaan tidak relevan dengan data, jawab secara umum tentang aplikasi
4. Jangan membuat informasi yang tidak ada di data

Pertanyaan user: {$userMessage}
PROMPT;
    }

    public function sendToGemini(string $prompt): string
    {
        if (empty($this->apiKey)) {
            return 'Maaf, konfigurasi API Key Gemini belum diatur.';
        }

        // Build the URL for Gemini API dynamically using configured baseUrl and model
        $baseUrl = rtrim($this->baseUrl ?: 'https://generativelanguage.googleapis.com/v1/models', '/');
        $model = $this->model ?: 'gemini-2.0-flash';
        $url = "{$baseUrl}/{$model}:generateContent?key={$this->apiKey}";

        try {
            $response = Http::timeout(30)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, tidak dapat menghasilkan jawaban.';
            }

            // Log the error for debugging
            $errorBody = $response->body();
            $status = $response->status();
            
            if ($status === 429) {
                return 'Maaf, kuota API Gemini Anda telah habis. Silakan tunggu beberapa menit atau buat API key baru di Google AI Studio.';
            }

            return 'Maaf, terjadi kesalahan saat menghubungi Gemini API. Status: ' . $status . ' - Error: ' . $errorBody;
        } catch (\Exception $e) {
            return 'Maaf, terjadi kesalahan: ' . $e->getMessage();
        }
    }
}
