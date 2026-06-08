<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentReminderController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\ReminderLogController;
use App\Http\Controllers\AccessControlController;
use App\Http\Controllers\ChatbotController;
use App\Models\DocumentReminder;
use App\Models\DocumentType;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

// helper removed: compute reminders to notify inline in dashboard routes

Route::get('/', function () {
    return view('welcome');
});

Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');

Route::get('/dashboard', function () {
    $today = now();
    $monthNames = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    $reminders = DocumentReminder::with(['user', 'documentType'])->get();

    $buildRemindersToNotify = function ($reminders) use ($today) {
        return $reminders->filter(function ($reminder) use ($today) {
            $todayDate = $today->copy()->startOfDay();
            $expired = $reminder->tanggal_expired->copy()->startOfDay();
            $reminderMonths = (int) $reminder->reminder_bulan;
            $reminderStart = $expired->copy()->subMonths($reminderMonths);

            return $todayDate->betweenIncluded($reminderStart, $expired);
        })->sortBy('tanggal_expired')->values();
    };

    $resolveReminderState = function ($reminder) use ($today) {
        $daysLeft = $today->copy()->startOfDay()->diffInDays($reminder->tanggal_expired->copy()->startOfDay(), false);
        $reminderMonths = (int) $reminder->reminder_bulan;
        if ($daysLeft < 0) {
            return [
                'state' => 'expired',
                'label' => 'Expired',
                'days_left' => $daysLeft,
            ];
        }

        // Use the same thresholds as the Manajemen Dokumen view (doc.read)
        if ($reminderMonths === 1) {
            if ($daysLeft > 30) {
                $state = 'neutral';
                $label = 'Reminder aktif';
            } elseif ($daysLeft >= 15) {
                $state = 'green';
                $label = 'Reminder aktif';
            } elseif ($daysLeft >= 7) {
                $state = 'yellow';
                $label = 'Mendekati expired';
            } else {
                $state = 'red';
                $label = 'Mendekati expired';
            }
        } elseif ($reminderMonths === 3) {
            if ($daysLeft > 90) {
                $state = 'neutral';
                $label = 'Reminder aktif';
            } elseif ($daysLeft >= 45) {
                $state = 'green';
                $label = 'Reminder aktif';
            } elseif ($daysLeft >= 18) {
                $state = 'yellow';
                $label = 'Mendekati expired';
            } else {
                $state = 'red';
                $label = 'Mendekati expired';
            }
        } elseif ($reminderMonths === 6) {
            if ($daysLeft > 180) {
                $state = 'neutral';
                $label = 'Reminder aktif';
            } elseif ($daysLeft >= 90) {
                $state = 'green';
                $label = 'Reminder aktif';
            } elseif ($daysLeft >= 36) {
                $state = 'yellow';
                $label = 'Mendekati expired';
            } else {
                $state = 'red';
                $label = 'Mendekati expired';
            }
        } else {
            $totalDays = max(30, $reminderMonths * 30);
            $greenThreshold = (int) round($totalDays * 0.5);
            $yellowThreshold = (int) round($totalDays * 0.25);

            if ($daysLeft > $totalDays) {
                $state = 'neutral';
                $label = 'Reminder aktif';
            } elseif ($daysLeft >= $greenThreshold) {
                $state = 'green';
                $label = 'Reminder aktif';
            } elseif ($daysLeft >= $yellowThreshold) {
                $state = 'yellow';
                $label = 'Mendekati expired';
            } else {
                $state = 'red';
                $label = 'Mendekati expired';
            }
        }

        return [
            'state' => $state,
            'label' => $label,
            'days_left' => $daysLeft,
        ];
    };

    // Quick test: inject a dummy reminder when ?test_reminders=1 is present
    if (request()->has('test_reminders')) {
        $dummy = (object) [
            'nama_dokumen' => 'DUMMY Dokumen Demo',
            'penerbit_tujuan' => 'PT Demo',
            'tanggal_expired' => now()->addDays(30),
            'reminder_bulan' => 3,
            'attachment_path' => '',
            'attachment_name' => 'dummy.pdf',
        ];

        $remindersToNotify = collect([$dummy]);
    } else {
        $remindersToNotify = $buildRemindersToNotify($reminders);
    }

    $totalDocuments = $reminders->count();
    $totalSertifikat = $reminders->filter(function ($reminder) {
        return str_contains(strtolower((string) $reminder->jenis_dokumen), 'sertifikat')
            || str_contains(strtolower((string) $reminder->jenis_dokumen_label), 'sertifikat');
    })->count();
    $totalWajibLapor = $reminders->filter(function ($reminder) {
        $jenisDokumen = strtolower((string) $reminder->jenis_dokumen);
        $jenisLabel = strtolower((string) $reminder->jenis_dokumen_label);

        return str_contains($jenisDokumen, 'wajib')
            || str_contains($jenisDokumen, 'lapor')
            || str_contains($jenisDokumen, 'tahunan')
            || str_contains($jenisLabel, 'wajib lapor tahunan');
    })->count();
    $totalExpired = $reminders->filter(function ($reminder) use ($today) {
        return $reminder->tanggal_expired->lt($today->copy()->startOfDay());
    })->count();

    // allow viewing other months via ?month=YYYY-MM
    $requestedMonth = request()->string('month')->toString();
    if ($requestedMonth) {
        try {
            $calendarMonth = Carbon::createFromFormat('Y-m', $requestedMonth)->startOfMonth();
        } catch (Exception $e) {
            $calendarMonth = $today->copy()->startOfMonth();
        }
    } else {
        $calendarMonth = $today->copy()->startOfMonth();
    }
    $calendarStart = $calendarMonth->copy()->startOfWeek(Carbon::MONDAY);
    $calendarEnd = $calendarMonth->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

    $calendarDocuments = $reminders->map(function ($reminder) use ($resolveReminderState) {
        $state = $resolveReminderState($reminder);

        return [
            'id' => $reminder->id,
            'name' => $reminder->nama_dokumen,
            'type' => $reminder->jenis_dokumen_label,
            'expired_at' => $reminder->tanggal_expired->format('d-m-Y'),
            'date_key' => $reminder->tanggal_expired->toDateString(),
            'state' => $state['state'],
            'state_label' => $state['label'],
            'days_left' => $state['days_left'],
        ];
    })->groupBy('date_key')->map(function ($items) {
        return $items->values()->all();
    })->all();

    $calendarDays = [];
    foreach (CarbonPeriod::create($calendarStart, $calendarEnd) as $date) {
        $dateKey = $date->toDateString();
        $documentsForDate = $calendarDocuments[$dateKey] ?? [];

        // Determine day-level state by prioritizing the most severe document state
        $dayState = 'empty';
        if (! empty($documentsForDate)) {
            $docs = collect($documentsForDate);

            if ($docs->contains('state', 'expired')) {
                $dayState = 'expired';
            } elseif ($docs->contains('state', 'red')) {
                $dayState = 'red';
            } elseif ($docs->contains('state', 'yellow')) {
                $dayState = 'yellow';
            } elseif ($docs->contains('state', 'green')) {
                $dayState = 'green';
            } else {
                $dayState = 'neutral';
            }
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

    $selectedCalendarDate = $calendarMonth->toDateString();
    $todayCalendarDay = collect($calendarDays)->first(function ($day) use ($today) {
        return $day['date_key'] === $today->toDateString() && $day['in_month'];
    });

    if ($todayCalendarDay) {
        $selectedCalendarDate = $todayCalendarDay['date_key'];
    } else {
        $firstDocumentDay = collect($calendarDays)->first(function ($day) {
            return ! empty($day['documents']);
        });

        if ($firstDocumentDay) {
            $selectedCalendarDate = $firstDocumentDay['date_key'];
        }
    }

    $selectedCalendarDocuments = $calendarDocuments[$selectedCalendarDate] ?? [];
    $calendarMonthLabel = $monthNames[(int) $calendarMonth->month] . ' ' . $calendarMonth->year;
    $calendarPrev = $calendarMonth->copy()->subMonth();
    $calendarNext = $calendarMonth->copy()->addMonth();
    $calendarWeekdays = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

    return view('admin-dashboard', [
        'remindersToNotify' => $remindersToNotify,
        'totalDocuments' => $totalDocuments,
        'totalSertifikat' => $totalSertifikat,
        'totalWajibLapor' => $totalWajibLapor,
        'totalExpired' => $totalExpired,
        'calendarDays' => $calendarDays,
        'calendarMonthLabel' => $calendarMonthLabel,
        'calendarWeekdays' => $calendarWeekdays,
        'selectedCalendarDate' => $selectedCalendarDate,
        'selectedCalendarDocuments' => $selectedCalendarDocuments,
        'calendarPrev' => $calendarPrev,
        'calendarNext' => $calendarNext,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:1,2'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return redirect()->route('dashboard');
    })->name('admin.dashboard');

    Route::get('/logs', [ReminderLogController::class, 'index'])->name('logs.index');
    Route::post('/logs/{log}/retry', [ReminderLogController::class, 'retry'])->name('logs.retry');
});

Route::middleware(['auth', 'verified', 'role:1'])->group(function () {
    Route::get('/hak-akses', [AccessControlController::class, 'index'])->name('access-control.index');
    Route::patch('/hak-akses/{user}', [AccessControlController::class, 'update'])->name('access-control.update');
});

Route::middleware(['auth', 'verified', 'role:3'])->group(function () {
    Route::get('/user/dashboard', function () {
        return redirect()->route('dashboard');
    })->name('user.dashboard');
});

Route::middleware(['auth', 'verified', 'role:1,2,3'])->group(function () {
    Route::get('/doc-type', [DocumentTypeController::class, 'index'])->name('doc_type.index');
    Route::get('/doc-type/create', [DocumentTypeController::class, 'create'])->name('doc_type.create');
    Route::post('/doc-type', [DocumentTypeController::class, 'store'])->name('doc_type.store');
    Route::get('/doc-type/{doc_type}/edit', [DocumentTypeController::class, 'edit'])->name('doc_type.edit');
    Route::patch('/doc-type/{doc_type}', [DocumentTypeController::class, 'update'])->name('doc_type.update');
    Route::delete('/doc-type/{doc_type}', [DocumentTypeController::class, 'destroy'])->name('doc_type.destroy');

    Route::get('/dokumen', function () {
        $today = now();
        $jenis = request()->string('jenis')->toString();
        $documentTypes = DocumentType::where('status', 'active')->orderBy('nama_jenis')->get();

        $remindersQuery = DocumentReminder::with(['user', 'documentType', 'internalPics']);

        $buildRemindersToNotify = function ($reminders) use ($today) {
            return $reminders->filter(function ($reminder) use ($today) {
                $todayDate = $today->copy()->startOfDay();
                $expired = $reminder->tanggal_expired->copy()->startOfDay();
                $reminderMonths = (int) $reminder->reminder_bulan;
                $reminderStart = $expired->copy()->subMonths($reminderMonths);

                return $todayDate->betweenIncluded($reminderStart, $expired);
            })->sortBy('tanggal_expired')->values();
        };

        if (request()->has('test_reminders')) {
            $dummy = (object) [
                'nama_dokumen' => 'DUMMY Dokumen Demo',
                'penerbit_tujuan' => 'PT Demo',
                'tanggal_expired' => now()->addDays(30),
                'reminder_bulan' => 3,
                'attachment_path' => '',
                'attachment_name' => 'dummy.pdf',
            ];

            $remindersToNotify = collect([$dummy]);
        } else {
            $remindersToNotify = $buildRemindersToNotify($remindersQuery->get());
        }

        if ($jenis !== '' && $jenis !== 'semua') {
            $remindersQuery->where(function ($query) use ($jenis) {
                if (is_numeric($jenis)) {
                    $query->where('jenis_dokumen', $jenis);
                    $docType = DocumentType::find($jenis);
                    if ($docType) {
                        $query->orWhere('jenis_dokumen', $docType->nama_jenis);
                    }
                } else {
                    $targetJenis = $jenis;
                    if ($jenis === 'spt') {
                        $targetJenis = 'wajib lapor tahunan';
                    }
                    $query->where('jenis_dokumen', $targetJenis)
                        ->orWhereHas('documentType', function ($documentTypeQuery) use ($targetJenis) {
                            $documentTypeQuery->where('nama_jenis', $targetJenis);
                        });
                }
            });
        }

        // Support server-side filtering for expired documents via ?expired=1
        if (request()->boolean('expired')) {
            $remindersQuery->where('tanggal_expired', '<', $today->copy()->startOfDay());
        }

        $reminders = $remindersQuery
            ->orderByRaw('CASE WHEN tanggal_expired < CURDATE() THEN 1 ELSE 0 END ASC, CASE WHEN CURDATE() >= DATE_SUB(tanggal_expired, INTERVAL reminder_bulan MONTH) THEN 0 ELSE 1 END ASC, tanggal_expired ASC')
            ->get();

        return view('doc.read', [
            'reminders' => $reminders,
            'jenis' => $jenis,
            'remindersToNotify' => $remindersToNotify,
            'documentTypes' => $documentTypes,
        ]);
    })->name('dokumen');
    Route::get('/dokumen/create', [DocumentReminderController::class, 'create'])->name('doc.create');
    Route::get('/dokumen/{reminder}/edit', [DocumentReminderController::class, 'edit'])->name('doc.edit');
    Route::get('/dokumen/{reminder}', [DocumentReminderController::class, 'show'])->name('doc.show');
    Route::get('/dokumen/{reminder}/download', [DocumentReminderController::class, 'download'])->name('doc.download');
    Route::get('/dokumen/{reminder}/view', [DocumentReminderController::class, 'view'])->name('doc.view');

    Route::post('/document-reminders', [DocumentReminderController::class, 'store'])
        ->name('doc.store');
    Route::patch('/dokumen/{reminder}', [DocumentReminderController::class, 'update'])
        ->name('doc.update');
    Route::delete('/dokumen/{reminder}', [DocumentReminderController::class, 'destroy'])
        ->name('doc.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
