<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-800 dark:text-zinc-100 leading-tight">
            {{ __('Detail Dokumen') }}
        </h2>
    </x-slot>

    @php
        $today = \Carbon\Carbon::today();
        $expired = $reminder->tanggal_expired;
        $ext = strtolower(pathinfo($reminder->attachment_name ?? '', PATHINFO_EXTENSION));
        $pics = $reminder->internalPics;

        if (is_null($expired)) {
            $statusLabel = 'Aktif';
            $statusColor = 'bg-emerald-100 text-emerald-600';
            $sisaHariText = 'Seumur Hidup';
            $sisaHariColor = 'text-emerald-600';
        } else {
            $sisaHari = $today->diffInDays($expired, false);

            if ($sisaHari < 0) {
                $statusLabel = 'Expired';
                $statusColor = 'bg-red-100 text-red-600';
                $sisaHariText = 'Sudah lewat ' . abs($sisaHari) . ' hari';
                $sisaHariColor = 'text-red-600';
            } elseif ($sisaHari == 0) {
                $statusLabel = 'Expired Hari Ini';
                $statusColor = 'bg-red-100 text-red-500';
                $sisaHariText = 'Expired hari ini';
                $sisaHariColor = 'text-red-600';
            } elseif ($sisaHari <= 30) {
                $statusLabel = 'Segera Expired';
                $statusColor = 'bg-amber-100 text-amber-600';
                $sisaHariText = $sisaHari . ' hari lagi';
                $sisaHariColor = 'text-amber-600';
            } else {
                $statusLabel = 'Aktif';
                $statusColor = 'bg-green-100 text-green-600';
                $sisaHariText = $sisaHari . ' hari lagi';
                $sisaHariColor = 'text-gray-900 dark:text-zinc-100';
            }
        }
    @endphp

    {{-- Wrapper Halaman dengan latar belakang abu-abu terang sesuai mockup --}}
    <div class="p-2 sm:p-4 bg-[#f3f4f6] dark:bg-zinc-950 space-y-5 flex-1">

        {{-- ===== Header Card ===== --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                <h3 class="min-w-0 break-words text-xl font-bold text-gray-900 dark:text-zinc-100">{{ $reminder->nama_dokumen }}</h3>
                <span class="shrink-0 inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $statusColor }}">
                    {{ $statusLabel }}
                </span>
            </div>
            <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">{{ $reminder->penerbit_tujuan ?? 'Penerbit' }}</p>
        </div>

        {{-- ===== Informasi Dokumen + PIC (grid 2) ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-stretch">

            {{-- Kolom kiri: Informasi Dokumen --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-900 dark:text-zinc-100 mb-6">Informasi Dokumen</h3>
                <dl class="grid grid-cols-2 gap-x-6 gap-y-6">
                    <div>
                        <dt class="text-xs font-normal text-gray-500 dark:text-zinc-400">No Dokumen</dt>
                        <dd class="mt-1.5 text-xs font-bold text-gray-900 dark:text-zinc-100 leading-tight">{{ $reminder->no_dokumen }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-normal text-gray-500 dark:text-zinc-400">Jenis Dokumen</dt>
                        <dd class="mt-1.5 text-xs font-bold text-gray-900 dark:text-zinc-100 leading-tight">{{ $reminder->jenis_dokumen_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-normal text-gray-500 dark:text-zinc-400">Penerbit / Tujuan</dt>
                        <dd class="mt-1.5 text-xs font-bold text-gray-900 dark:text-zinc-100 leading-tight">{{ $reminder->penerbit_tujuan }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-normal text-gray-500 dark:text-zinc-400">Tanggal Terbit</dt>
                        <dd class="mt-1.5 text-xs font-bold text-gray-900 dark:text-zinc-100 leading-tight">{{ $reminder->tanggal_terbit?->translatedFormat('d F Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-normal text-gray-500 dark:text-zinc-400">Tanggal Expired</dt>
                        <dd class="mt-1.5 text-xs font-bold text-gray-900 dark:text-zinc-100 leading-tight">{{ $expired ? $expired->translatedFormat('d F Y') : 'Seumur Hidup' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-normal text-gray-500 dark:text-zinc-400">Sisa Waktu</dt>
                        <dd class="mt-1.5 text-xs font-bold {{ $sisaHariColor }} leading-tight">{{ $sisaHariText }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-normal text-gray-500 dark:text-zinc-400">Interval Reminder</dt>
                        <dd class="mt-1.5 text-xs font-bold text-gray-900 dark:text-zinc-100 leading-tight">{{ $reminder->reminder_bulan ? $reminder->reminder_bulan . ' bulan sebelum expired' : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-normal text-gray-500 dark:text-zinc-400">Dibuat Oleh</dt>
                        <dd class="mt-1.5 text-xs font-bold text-gray-900 dark:text-zinc-100 leading-tight">{{ $reminder->user?->nama ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-normal text-gray-500 dark:text-zinc-400">Tanggal Input</dt>
                        <dd class="mt-1.5 text-xs font-bold text-gray-900 dark:text-zinc-100 leading-tight">{{ $reminder->created_at->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-normal text-gray-500 dark:text-zinc-400">Terakhir Diupdate</dt>
                        <dd class="mt-1.5 text-xs font-bold text-gray-900 dark:text-zinc-100 leading-tight">{{ $reminder->updated_at->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Kolom kanan: PIC Internal & External (stack) --}}
            <div class="flex flex-col gap-5">

                {{-- Kartu: PIC Internal --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-base font-bold text-gray-900 dark:text-zinc-100 mb-4">PIC Internal</h3>
                    @if($pics->isNotEmpty())
                        <div class="space-y-2">
                            @foreach($pics as $pic)
                                <div class="flex items-center gap-3 p-3 rounded-2xl bg-[#eeeef0] dark:bg-zinc-800">
                                    @if(!empty($pic->avatar_url ?? $pic->profile_photo_url))
                                        <img src="{{ $pic->avatar_url ?? $pic->profile_photo_url }}"
                                             alt="{{ $pic->pivot->nama ?? $pic->nama }}"
                                             class="h-10 w-10 rounded-full object-cover shrink-0">
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#a38380] text-xs font-bold text-white shrink-0">
                                            {{ strtoupper(substr($pic->nama ?? $pic->pivot->nama ?? 'N', 0, 1)) }}
                                        </div>
                                    @endif

                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-gray-900 dark:text-zinc-100 truncate">{{ $pic->pivot->nama ?? $pic->nama }}</p>
                                        <p class="text-[11px] text-gray-500 dark:text-zinc-400 truncate mt-0.5">{{ $pic->pivot->email ?? $pic->email ?: '-' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">Tidak ada PIC Internal.</p>
                    @endif
                </div>

                {{-- Kartu: PIC External --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-base font-bold text-gray-900 dark:text-zinc-100 mb-4">PIC External</h3>
                    @if($reminder->pic_external_nama)
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-[#eeeef0] dark:bg-zinc-800">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#a38380] text-xs font-bold text-white shrink-0">
                                {{ strtoupper(substr($reminder->pic_external_nama, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-900 dark:text-zinc-100 truncate">{{ $reminder->pic_external_nama }}</p>
                                <p class="text-[11px] text-gray-500 dark:text-zinc-400 truncate mt-0.5">{{ $reminder->pic_external_telpon ?: '-' }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic">Tidak ada PIC External.</p>
                    @endif
                </div>

            </div>
        </div>

        {{-- ===== Lampiran Dokumen ===== --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl p-6 shadow-sm">
            <h3 class="text-base font-bold text-gray-900 dark:text-zinc-100 mb-6">Lampiran Dokumen</h3>
            @if($reminder->attachment_name)
                <div class="flex items-center justify-between p-3 rounded-2xl bg-[#eeeef0] dark:bg-zinc-800">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#f2e3e1] text-gray-700 dark:bg-zinc-700 dark:text-zinc-200 shrink-0">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 dark:text-zinc-100 truncate">{{ $reminder->attachment_name }}</p>
                            <p class="text-[10px] font-medium text-gray-500 uppercase mt-0.5">{{ $ext }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0 ml-2">
                        <a href="{{ route('doc.view', $reminder->id) }}" target="_blank" rel="noopener noreferrer" class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#2b6cb0] text-white hover:bg-blue-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('doc.download', $reminder->id) }}" class="flex h-8 w-8 items-center justify-center rounded-lg border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-900 text-gray-700 dark:text-zinc-300 hover:bg-gray-50 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-400 italic">Tidak ada lampiran.</p>
            @endif
        </div>

    </div>
</x-app-layout>
