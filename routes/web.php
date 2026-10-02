<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DocumentReminderController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\ReminderLogController;
use App\Http\Controllers\DashboardController;
use App\Models\DocumentReminder;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'applications.access', 'throttle:30,1'])->name('dashboard');

Route::middleware(['auth', 'applications.access', 'role:1,2,3,4,7', 'throttle:30,1'])->group(function () {
    Route::get('/logs', [ReminderLogController::class, 'index'])->name('logs.index');
    Route::post('/logs/{log}/retry', [ReminderLogController::class, 'retry'])->name('logs.retry');
});

Route::middleware(['auth', 'applications.access', 'throttle:30,1'])->group(function () {
    Route::middleware('role:1,2,3,4,7')->group(function () {
        Route::get('/doc-type', [DocumentTypeController::class, 'index'])->name('doc_type.index');
        Route::get('/doc-type/create', [DocumentTypeController::class, 'create'])->name('doc_type.create');
        Route::post('/doc-type', [DocumentTypeController::class, 'store'])->name('doc_type.store');
        Route::get('/doc-type/{doc_type}/edit', [DocumentTypeController::class, 'edit'])->name('doc_type.edit');
        Route::patch('/doc-type/{doc_type}', [DocumentTypeController::class, 'update'])->name('doc_type.update');
        Route::delete('/doc-type/{doc_type}', [DocumentTypeController::class, 'destroy'])->name('doc_type.destroy');
    });

    Route::middleware('role:1,2,3,4,5,6,7,8,9')->group(function () {
        Route::get('/dokumen', function () {
            $today = now();
            $jenis = request()->string('jenis')->toString();
            $documentTypes = DocumentType::where('status', 'active')->orderBy('nama_jenis')->get();

            $remindersQuery = DocumentReminder::with(['documentType', 'internalPics']);

            if ($jenis !== '' && $jenis !== 'semua') {
                $remindersQuery->where(function ($query) use ($jenis) {
                    if (is_numeric($jenis)) {
                        $query->where('jenis_dokumen', $jenis);
                    } else {
                        $targetJenis = $jenis;
                        if ($jenis === 'spt') {
                            $targetJenis = 'wajib lapor tahunan';
                        }
                        $query->orWhereHas('documentType', function ($documentTypeQuery) use ($targetJenis) {
                            $documentTypeQuery->where('nama_jenis', $targetJenis);
                        });
                    }
                });
            }

            if (request()->boolean('expired')) {
                $remindersQuery->where('tanggal_expired', '<', $today->copy()->startOfDay());
            }

            // Banner global: selalu dihitung dari seluruh dokumen (di luar filter jenis/expired).
            $remindersToNotify = DashboardController::buildRemindersToNotify(
                DocumentReminder::query()
                    ->select(['id', 'nama_dokumen', 'penerbit_tujuan', 'tanggal_expired', 'reminder_bulan'])
                    ->get(),
                $today
            );

            $reminders = $remindersQuery->get()->sort(function ($a, $b) use ($today) {
            $aNull = $a->tanggal_expired === null;
            $bNull = $b->tanggal_expired === null;
            if ($aNull !== $bNull) {
                return $aNull <=> $bNull;
            }
            if (! $aNull) {
                $aExpired = $a->tanggal_expired->copy()->startOfDay();
                $bExpired = $b->tanggal_expired->copy()->startOfDay();
                $aPast = $aExpired->lt($today->copy()->startOfDay());
                $bPast = $bExpired->lt($today->copy()->startOfDay());
                if ($aPast !== $bPast) {
                    return $aPast <=> $bPast;
                }
                $months = max(1, (int) ($a->reminder_bulan ?? 3));
                $aDue = $today->copy()->startOfDay()->gte($aExpired->copy()->subMonthsNoOverflow($months));
                $bMonths = max(1, (int) ($b->reminder_bulan ?? 3));
                $bDue = $today->copy()->startOfDay()->gte($bExpired->copy()->subMonthsNoOverflow($bMonths));
                if ($aDue !== $bDue) {
                    return $aDue <=> $bDue;
                }
            }

            return ($a->tanggal_expired?->getTimestamp() ?? PHP_INT_MAX) <=> ($b->tanggal_expired?->getTimestamp() ?? PHP_INT_MAX);
        })->values();

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
});

Route::middleware(['auth', 'throttle:30,1'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/access', [ProfileController::class, 'toggleAccess'])->name('profile.access');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
});

require __DIR__.'/auth.php';
