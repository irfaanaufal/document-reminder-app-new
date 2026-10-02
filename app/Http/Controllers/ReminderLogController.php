<?php

namespace App\Http\Controllers;

use App\Models\DocumentReminder;
use App\Models\ReminderNotificationLog;
use App\Services\ReminderMailService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReminderLogController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today()->toDateString();
        // NOTE: logika due-reminder di sini sengaja divergen dari
        // DashboardController::buildRemindersToNotify (exclude reminder_bulan null).
        $dueReminderCount = DocumentReminder::query()
            ->whereNotNull('tanggal_expired')
            ->whereNotNull('reminder_bulan')
            ->where('tanggal_expired', '>=', $today)
            ->get()
            ->filter(function (DocumentReminder $r) use ($today) {
                $reminderStart = \Carbon\Carbon::parse($r->tanggal_expired)
                    ->startOfDay()
                    ->subMonthsNoOverflow((int) $r->reminder_bulan);

                return \Carbon\Carbon::parse($today)->gte($reminderStart);
            })
            ->count();

        $logsQuery = ReminderNotificationLog::query()
            ->with(['documentReminder:id,no_dokumen,nama_dokumen,pic_nama,pic_email']);

        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $rule = strtolower(trim((string) $request->query('rule', '')));
        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));

        if ($search !== '') {
            $logsQuery->where(function ($query) use ($search) {
                $query->where('recipient_email', 'like', '%' . $search . '%')
                    ->orWhere('recipient_name', 'like', '%' . $search . '%')
                    ->orWhereHas('documentReminder', function ($docQuery) use ($search) {
                        $docQuery->where('no_dokumen', 'like', '%' . $search . '%')
                            ->orWhere('nama_dokumen', 'like', '%' . $search . '%')
                            ->orWhere('pic_nama', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($status !== '') {
            $logsQuery->where('status', $status);
        }

        if ($rule !== '') {
            $logsQuery->where('reminder_rule', $rule);
        }

        if ($dateFrom !== '') {
            $logsQuery->whereDate('scheduled_for', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $logsQuery->whereDate('scheduled_for', '<=', $dateTo);
        }

        $summarySource = clone $logsQuery;

        $summaryRow = (clone $summarySource)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = 'pending' AND DATE(scheduled_for) <= ? THEN 1 ELSE 0 END) as pending
            ")
            ->addBinding($today, 'select')
            ->first();

        $summary = [
            'total' => (int) ($summaryRow->total ?? 0),
            'sent' => (int) ($summaryRow->sent ?? 0),
            'failed' => (int) ($summaryRow->failed ?? 0),
            'pending' => (int) ($summaryRow->pending ?? 0),
            'due_reminders' => $dueReminderCount,
        ];

        $logs = $logsQuery
            ->orderByDesc('scheduled_for')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('logs.index', [
            'logs' => $logs,
            'summary' => $summary,
            'filters' => [
                'q' => $search,
                'status' => $status,
                'rule' => $rule,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'statusOptions' => ['sent', 'failed', 'pending', 'dry_run'],
            'ruleOptions' => ['monthly', 'h-14', 'h-7', 'h-0'],
        ]);
    }

    public function retry(ReminderNotificationLog $log, ReminderMailService $mailService): RedirectResponse
    {
        if ($log->status === 'sent') {
            return back()->with('error', 'Log dengan status sent tidak perlu di-retry.');
        }

        $document = DocumentReminder::find($log->document_reminder_id);

        if (! $document) {
            return back()->with('error', 'Dokumen terkait log tidak ditemukan.');
        }

        $email = $log->recipient_email;

        if (empty($email)) {
            return back()->with('error', 'Email tujuan tidak valid untuk retry.');
        }

        $recipientName = trim((string) ($log->recipient_name ?: $document->pic_nama));

        $daysLeft = $document->tanggal_expired ? Carbon::today()->diffInDays($document->tanggal_expired, false) : null;
        if ($daysLeft !== null) {
            $daysLeft = (int) $daysLeft;
        }

        $reminderRule = strtolower(trim((string) ($log->reminder_rule ?? '')));

        $closingLine = 'Demikian informasi ini kami sampaikan. Atas perhatian dan tindak lanjutnya, kami ucapkan terima kasih.';
        if ($daysLeft !== null && $daysLeft === 0) {
            $closingLine = 'Berkaitan dengan batas waktu yang berakhir hari ini, mohon dokumen ini menjadi prioritas utama untuk segera diproses. Terima kasih atas perhatian dan kerjasamanya.';
        } elseif ($daysLeft !== null && $daysLeft < 0) {
            $closingLine = 'Dokumen ini telah melewati batas waktu. Mohon segera dilakukan evaluasi dan penanganan untuk menghindari konsekuensi lebih lanjut. Terima kasih.';
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
        if ($daysLeft !== null) {
            if ($daysLeft === 0) {
                $sisaWaktuDisplay = 'Hari ini';
            } elseif ($daysLeft > 0) {
                $sisaWaktuDisplay = $daysLeft . ' hari';
            } else {
                $sisaWaktuDisplay = 'LEWAT ' . abs($daysLeft) . ' hari';
            }
        }

        $viewData = [
            'pic_nama' => $recipientName,
            'nama_dokumen' => $document->nama_dokumen,
            'no_dokumen' => $document->no_dokumen,
            'penerbit_tujuan' => $document->penerbit_tujuan,
            'tanggal_expired' => optional($document->tanggal_expired)->format('d-m-Y'),
            'sisa_waktu' => $sisaWaktuDisplay,
            'sisa_hari' => $daysLeft,
            'intro' => $intro,
            'closing' => $closingLine,
            'attachment_name' => $document->attachment_name,
        ];

        $subject = $mailService->buildSubject([
            'nama_dokumen' => $document->nama_dokumen,
            'reminder_rule' => (string) $log->reminder_rule,
            'sisa_hari' => $daysLeft,
        ]);

        $log->increment('attempt_count');

        try {
            $response = $mailService->sendHtmlEmail(
                $email,
                $subject,
                'emails.reminder',
                $viewData,
                $document->attachment_path,
                $document->attachment_name
            );

            $log->update([
                'status' => $response['ok'] ? 'sent' : 'failed',
                'sent_at' => $response['ok'] ? now() : null,
                'provider_response' => $response,
                'recipient_email' => $email,
                'recipient_name' => $recipientName,
            ]);

            if ($response['ok']) {
                return back()->with('success', 'Retry berhasil dikirim ke ' . $email . '.');
            }

            return back()->with('error', 'Retry gagal. Cek provider response pada detail log.');
        } catch (\Throwable $throwable) {
            $log->update([
                'status' => 'failed',
                'provider_response' => [
                    'message' => $throwable->getMessage(),
                ],
            ]);

            return back()->with('error', 'Retry gagal: ' . $throwable->getMessage());
        }
    }
}
