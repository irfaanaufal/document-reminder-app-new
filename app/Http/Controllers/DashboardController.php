<?php

namespace App\Http\Controllers;

use App\Models\DocumentReminder;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $today = now();
        $monthNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $startOfToday = $today->copy()->startOfDay();

        $reminders = DocumentReminder::query()
            ->select(['id', 'nama_dokumen', 'jenis_dokumen', 'tanggal_expired', 'reminder_bulan', 'penerbit_tujuan'])
            ->with('documentType:id,nama_jenis')
            ->get();

        $remindersToNotify = $this->buildRemindersToNotify($reminders, $today);

        $totalDocuments = DocumentReminder::query()->count();
        $totalExpired = DocumentReminder::query()
            ->whereNotNull('tanggal_expired')
            ->where('tanggal_expired', '<', $startOfToday->toDateString())
            ->count();
        $totalSertifikat = $this->countByJenisLabel('sertifikat');
        $totalWajibLapor = $this->countByJenisLabel('wajib', 'lapor', 'tahunan');

        $expiringSoon = DocumentReminder::query()
            ->leftJoin('document_types', 'document_types.id', '=', 'document_reminders.jenis_dokumen')
            ->whereNotNull('document_reminders.tanggal_expired')
            ->where('document_reminders.tanggal_expired', '>=', $startOfToday->toDateString())
            ->orderBy('document_reminders.tanggal_expired')
            ->limit(9)
            ->get([
                'document_reminders.id',
                'document_reminders.nama_dokumen',
                'document_reminders.tanggal_expired',
                DB::raw('COALESCE(document_types.nama_jenis, CAST(document_reminders.jenis_dokumen AS CHAR)) as jenis_label'),
            ])
            ->map(function ($r) use ($startOfToday) {
                $daysLeft = (int) $startOfToday->diffInDays($r->tanggal_expired->copy()->startOfDay(), false);
                return [
                    'id' => $r->id,
                    'nama_dokumen' => $r->nama_dokumen,
                    'jenis_label' => (string) $r->jenis_label,
                    'tanggal_expired' => $r->tanggal_expired->format('d-m-Y'),
                    'days_left' => $daysLeft,
                ];
            })
            ->values();

        $requestedMonth = $request->string('month')->toString();
        if ($requestedMonth) {
            try {
                $calendarMonth = Carbon::createFromFormat('Y-m', $requestedMonth)->startOfMonth();
            } catch (\Exception) {
                $calendarMonth = $today->copy()->startOfMonth();
            }
        } else {
            $calendarMonth = $today->copy()->startOfMonth();
        }

        $calendarStart = $calendarMonth->copy()->startOfWeek(Carbon::MONDAY);
        $calendarEnd = $calendarMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $calendarDocuments = $reminders->map(function ($reminder) {
            $state = $this->resolveReminderState($reminder);
            return [
                'id' => $reminder->id,
                'name' => $reminder->nama_dokumen,
                'type' => $reminder->jenis_dokumen_label,
                'expired_at' => $reminder->tanggal_expired ? $reminder->tanggal_expired->format('d-m-Y') : 'Seumur Hidup',
                'date_key' => $reminder->tanggal_expired ? $reminder->tanggal_expired->toDateString() : 'lifetime',
                'state' => $state['state'],
                'state_label' => $state['label'],
                'days_left' => $state['days_left'],
            ];
        })->groupBy('date_key')->map(fn ($items) => $items->values()->all())->all();

        $calendarDays = $this->buildCalendarDays($calendarDocuments, $calendarStart, $calendarEnd, $calendarMonth, $today, $monthNames);

        [$selectedCalendarDate, $selectedCalendarDocuments] = $this->resolveSelectedDate($calendarDays, $calendarDocuments, $calendarMonth, $today);

        return view('admin-dashboard', [
            'remindersToNotify' => $remindersToNotify,
            'totalDocuments' => $totalDocuments,
            'totalSertifikat' => $totalSertifikat,
            'totalWajibLapor' => $totalWajibLapor,
            'totalExpired' => $totalExpired,
            'expiringSoon' => $expiringSoon,
            'calendarDays' => $calendarDays,
            'calendarMonthLabel' => $monthNames[(int) $calendarMonth->month] . ' ' . $calendarMonth->year,
            'calendarMonthNum' => (int) $calendarMonth->month,
            'calendarMonthYear' => (int) $calendarMonth->year,
            'calendarYears' => range(now()->year - 5, now()->year + 5),
            'monthNames' => $monthNames,
            'calendarWeekdays' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            'selectedCalendarDate' => $selectedCalendarDate,
            'selectedCalendarDocuments' => $selectedCalendarDocuments,
            'pendingOtpsCount' => User::whereNotNull('reset_otp')->where('reset_otp_expires_at', '>', now())->count(),
        ]);
    }

    /**
     * Hitung dokumen dengan label jenis (nama_jenis, fallback cast id) mengandung salah satu needle.
     * Ekuivalen PHP: str_contains(strtolower($r->jenis_dokumen_label), $needle) — termasuk fallback type hilang.
     */
    private function countByJenisLabel(string ...$needles): int
    {
        $labelSql = 'LOWER(COALESCE(document_types.nama_jenis, CAST(document_reminders.jenis_dokumen AS CHAR)))';

        return DocumentReminder::query()
            ->leftJoin('document_types', 'document_types.id', '=', 'document_reminders.jenis_dokumen')
            ->where(function ($query) use ($needles, $labelSql) {
                foreach ($needles as $i => $needle) {
                    $clause = $labelSql . ' LIKE ?';
                    if ($i === 0) {
                        $query->whereRaw($clause, ['%' . $needle . '%']);
                    } else {
                        $query->orWhereRaw($clause, ['%' . $needle . '%']);
                    }
                }
            })
            ->count('document_reminders.id');
    }

    public static function buildRemindersToNotify($reminders, ?Carbon $today = null): \Illuminate\Support\Collection
    {
        $today ??= now();
        return $reminders->filter(function ($reminder) use ($today) {
            if (is_null($reminder->tanggal_expired)) {
                return false;
            }
            $todayDate = $today->copy()->startOfDay();
            $expired = $reminder->tanggal_expired->copy()->startOfDay();
            $reminderMonths = (int) $reminder->reminder_bulan;
            $reminderStart = $expired->copy()->subMonthsNoOverflow($reminderMonths);
            return $todayDate->betweenIncluded($reminderStart, $expired);
        })->sortBy('tanggal_expired')->values();
    }

    private function resolveReminderState(DocumentReminder $reminder): array
    {
        $today = now();
        if (is_null($reminder->tanggal_expired)) {
            return ['state' => 'lifetime', 'label' => 'Seumur Hidup', 'days_left' => null];
        }
        $daysLeft = $today->copy()->startOfDay()->diffInDays($reminder->tanggal_expired->copy()->startOfDay(), false);
        if ($daysLeft !== null) {
            $daysLeft = (int) $daysLeft;
        }
        $reminderMonths = (int) $reminder->reminder_bulan;

        if ($daysLeft < 0) {
            return ['state' => 'expired', 'label' => 'Expired', 'days_left' => $daysLeft];
        }

        $thresholds = match ($reminderMonths) {
            1 => [30, 15, 7],
            3 => [90, 45, 18],
            6 => [180, 90, 36],
            default => null,
        };

        if ($thresholds) {
            [$totalDays, $greenThreshold, $yellowThreshold] = $thresholds;
        } else {
            $totalDays = max(30, $reminderMonths * 30);
            $greenThreshold = (int) round($totalDays * 0.5);
            $yellowThreshold = (int) round($totalDays * 0.25);
        }

        if ($daysLeft > $totalDays) {
            $state = 'neutral'; $label = 'Reminder aktif';
        } elseif ($daysLeft >= $greenThreshold) {
            $state = 'green'; $label = 'Reminder aktif';
        } elseif ($daysLeft >= $yellowThreshold) {
            $state = 'yellow'; $label = 'Mendekati expired';
        } else {
            $state = 'red'; $label = 'Mendekati expired';
        }

        return ['state' => $state, 'label' => $label, 'days_left' => $daysLeft];
    }

    private function buildCalendarDays(array $calendarDocuments, Carbon $calendarStart, Carbon $calendarEnd, Carbon $calendarMonth, Carbon $today, array $monthNames): array
    {
        $calendarDays = [];
        foreach (CarbonPeriod::create($calendarStart, $calendarEnd) as $date) {
            $dateKey = $date->toDateString();
            $documentsForDate = $calendarDocuments[$dateKey] ?? [];
            $dayState = 'empty';
            if (!empty($documentsForDate)) {
                $docs = collect($documentsForDate);
                $dayState = match (true) {
                    $docs->contains('state', 'expired') => 'expired',
                    $docs->contains('state', 'red') => 'red',
                    $docs->contains('state', 'yellow') => 'yellow',
                    $docs->contains('state', 'green') => 'green',
                    default => 'neutral',
                };
            }
            $indicatorClass = match ($dayState) {
                'expired', 'red' => 'bg-red-500',
                'yellow' => 'bg-amber-400',
                'green' => 'bg-emerald-500',
                default => 'bg-transparent',
            };
            $calendarDays[] = [
                'date_key' => $dateKey,
                'day' => (int) $date->format('j'),
                'month' => (int) $date->month,
                'in_month' => $date->month === $calendarMonth->month,
                'is_today' => $date->isSameDay($today),
                'display_label' => $monthNames[(int) $date->month] . ' ' . $date->format('Y'),
                'indicator_class' => $indicatorClass,
                'state' => $dayState,
                'documents' => $documentsForDate,
            ];
        }
        return $calendarDays;
    }

    private function resolveSelectedDate(array $calendarDays, array $calendarDocuments, Carbon $calendarMonth, Carbon $today): array
    {
        $selectedCalendarDate = $calendarMonth->toDateString();
        $todayCalendarDay = collect($calendarDays)->first(fn ($day) => $day['date_key'] === $today->toDateString() && $day['in_month']);
        if ($todayCalendarDay) {
            $selectedCalendarDate = $todayCalendarDay['date_key'];
        } else {
            $firstDocumentDay = collect($calendarDays)->first(fn ($day) => !empty($day['documents']));
            if ($firstDocumentDay) {
                $selectedCalendarDate = $firstDocumentDay['date_key'];
            }
        }
        return [$selectedCalendarDate, $calendarDocuments[$selectedCalendarDate] ?? []];
    }
}
