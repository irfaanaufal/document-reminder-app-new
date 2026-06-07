<?php

namespace App\Http\Controllers;

use App\Models\DocumentReminder;
use App\Models\ReminderNotificationLog;
use App\Services\FonnteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReminderLogController extends Controller
{
    public function index(Request $request): View
    {
        $today = Carbon::today()->toDateString();
        $dueRemindersCollection = DocumentReminder::all()
            ->filter(function (DocumentReminder $reminder) use ($today) {
                $reminderStartDate = $reminder->tanggal_expired
                    ->copy()
                    ->subMonths((int) $reminder->reminder_bulan)
                    ->toDateString();
                $expiredDate = $reminder->tanggal_expired->toDateString();

                return $reminderStartDate <= $today && $today <= $expiredDate;
            })
            ->map(function (DocumentReminder $r) {
                return [
                    'id' => $r->id,
                    'no_dokumen' => $r->no_dokumen,
                    'nama_dokumen' => $r->nama_dokumen,
                    'pic_nama' => $r->pic_nama,
                    'pic_telpon' => $r->pic_telpon,
                    'tanggal_expired' => optional($r->tanggal_expired)->toDateString(),
                ];
            })
            ->values();

        $dueReminderCount = $dueRemindersCollection->count();

        $logsQuery = ReminderNotificationLog::query()
            ->with(['documentReminder:id,no_dokumen,nama_dokumen,pic_nama,pic_telpon']);

        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $rule = strtolower(trim((string) $request->query('rule', '')));
        $dateFrom = trim((string) $request->query('date_from', ''));
        $dateTo = trim((string) $request->query('date_to', ''));

        if ($search !== '') {
            $logsQuery->where(function ($query) use ($search) {
                $query->where('recipient_phone', 'like', '%' . $search . '%')
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

        $summary = [
            'total' => (clone $summarySource)->count(),
            'sent' => (clone $summarySource)->where('status', 'sent')->count(),
            'failed' => (clone $summarySource)->where('status', 'failed')->count(),
            'pending' => (clone $summarySource)
                ->where('status', 'pending')
                ->whereDate('scheduled_for', '<=', $today)
                ->count(),
            'dry_run' => (clone $summarySource)->where('status', 'dry_run')->count(),
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
            'dueReminders' => $dueRemindersCollection,
        ]);
    }

    public function retry(ReminderNotificationLog $log, FonnteService $fonnteService): RedirectResponse
    {
        if ($log->status === 'sent') {
            return back()->with('error', 'Log dengan status sent tidak perlu di-retry.');
        }

        $document = DocumentReminder::find($log->document_reminder_id);

        if (! $document) {
            return back()->with('error', 'Dokumen terkait log tidak ditemukan.');
        }

        $phone = $fonnteService->normalizePhoneForWhatsapp($log->recipient_phone);

        if ($phone === '') {
            return back()->with('error', 'Nomor tujuan tidak valid untuk retry.');
        }

        $message = $fonnteService->buildReminderMessage([
            'pic_nama' => $document->pic_nama,
            'nama_dokumen' => $document->nama_dokumen,
            'no_dokumen' => $document->no_dokumen,
            'penerbit_tujuan' => $document->penerbit_tujuan,
            'tanggal_expired' => optional($document->tanggal_expired)->format('d-m-Y'),
            'reminder_rule' => (string) $log->reminder_rule,
            'sisa_hari' => Carbon::today()->diffInDays($document->tanggal_expired, false),
        ]);
        $recipientName = trim((string) $document->pic_nama);

        $log->increment('attempt_count');

        try {
            $response = $fonnteService->sendMessage($phone, $message);

            $log->update([
                'status' => $response['ok'] ? 'sent' : 'failed',
                'sent_at' => $response['ok'] ? now() : null,
                'provider_response' => $response,
                'recipient_phone' => $phone,
                'recipient_name' => $recipientName,
            ]);

            if ($response['ok']) {
                return back()->with('success', 'Retry berhasil dikirim ke ' . $phone . '.');
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
