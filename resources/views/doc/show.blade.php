<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-zinc-100 leading-tight">
            {{ __('Detail Dokumen') }}
        </h2>
    </x-slot>

    @php
        $today = \Carbon\Carbon::today();
        $expired = $reminder->tanggal_expired;
        $sisaHari = $today->diffInDays($expired, false);
        $ext = strtolower(pathinfo($reminder->attachment_name ?? '', PATHINFO_EXTENSION));
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

        if ($sisaHari < 0) {
            $statusLabel = 'Expired';
            $statusColor = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
            $sisaHariText = 'Sudah lewat ' . abs($sisaHari) . ' hari';
            $sisaHariColor = 'text-red-600 dark:text-red-400';
        } elseif ($sisaHari == 0) {
            $statusLabel = 'Expired Hari Ini';
            $statusColor = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
            $sisaHariText = 'Expired hari ini';
            $sisaHariColor = 'text-red-600 dark:text-red-400';
        } elseif ($sisaHari <= 30) {
            $statusLabel = 'Segera Expired';
            $statusColor = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400';
            $sisaHariText = $sisaHari . ' hari lagi';
            $sisaHariColor = 'text-amber-600 dark:text-amber-400';
        } elseif ($sisaHari <= 90) {
            $statusLabel = 'Perlu Perhatian';
            $statusColor = 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400';
            $sisaHariText = $sisaHari . ' hari lagi';
            $sisaHariColor = 'text-yellow-600 dark:text-yellow-400';
        } else {
            $statusLabel = 'Aktif';
            $statusColor = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
            $sisaHariText = $sisaHari . ' hari lagi';
            $sisaHariColor = 'text-green-600 dark:text-green-400';
        }
    @endphp

    <div class="py-6">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-6">

            {{-- Header Card --}}
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="px-6 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 flex-wrap">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-zinc-100 truncate">{{ $reminder->nama_dokumen }}</h3>
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $statusColor }}">{{ $statusLabel }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">{{ $reminder->penerbit_tujuan }}</p>
                        </div>
                        <a href="{{ url()->previous() ?: route('dokumen', ['jenis' => 'semua']) }}" class="inline-flex items-center gap-2 rounded-md bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>

            {{-- Sisa Hari Highlight --}}
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="px-6 py-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-zinc-400 uppercase tracking-wider">Sisa Waktu</p>
                            <p class="mt-1 text-3xl font-extrabold {{ $sisaHariColor }}">{{ $sisaHariText }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500 dark:text-zinc-400">Expired pada</p>
                            <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-zinc-100">{{ $expired->translatedFormat('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Informasi Dokumen --}}
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-zinc-100">Informasi Dokumen</h4>
                </div>
                <div class="px-6 py-5">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-5">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-zinc-400">No Dokumen</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-zinc-100">{{ $reminder->no_dokumen }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-zinc-400">Jenis Dokumen</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-zinc-100">{{ $reminder->jenis_dokumen_label }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-zinc-400">Penerbit / Tujuan</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-zinc-100">{{ $reminder->penerbit_tujuan }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-zinc-400">Tanggal Terbit</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-zinc-100">{{ $reminder->tanggal_terbit->translatedFormat('d F Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-zinc-400">Tanggal Expired</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-zinc-100">{{ $expired->translatedFormat('d F Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-zinc-400">Interval Reminder</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-zinc-100">{{ $reminder->reminder_bulan }} bulan sebelum expired</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-zinc-400">Dibuat Oleh</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-zinc-100">{{ $reminder->user->nama ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-zinc-400">Tanggal Input</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-zinc-100">{{ $reminder->created_at->translatedFormat('d F Y, H:i') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-zinc-400">Terakhir Diupdate</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-zinc-100">{{ $reminder->updated_at->translatedFormat('d F Y, H:i') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- PIC Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- PIC Internal --}}
                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4">
                        <h4 class="text-base font-semibold text-gray-900 dark:text-zinc-100">PIC Internal</h4>
                    </div>
                    <div class="px-6 py-5">
                        @if($reminder->internalPics->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($reminder->internalPics as $index => $pic)
                                    <div class="flex items-center gap-3 p-3 rounded-md border {{ $index === 0 ? 'bg-indigo-50 border-indigo-200 dark:bg-indigo-900/20 dark:border-indigo-800' : 'bg-gray-50 border-gray-200 dark:bg-zinc-800/50 dark:border-zinc-700' }}">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-sm font-bold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-400 shrink-0">
                                            {{ strtoupper(substr($pic->nama, 0, 1)) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-zinc-100 truncate">
                                                {{ $pic->pivot->nama ?? $pic->nama }}
                                                @if($index === 0)
                                                    <span class="ml-1 inline-flex rounded-full bg-indigo-600 px-1.5 py-0.5 text-[10px] font-bold uppercase text-white">Utama</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-zinc-400">{{ $pic->pivot->no_telpon ?? $pic->no_telpon ?: '-' }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-900 dark:text-zinc-100 font-semibold">{{ $reminder->pic_nama ?: '-' }}</p>
                            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">{{ $reminder->pic_telpon ?: '-' }}</p>
                        @endif
                    </div>
                </div>

                {{-- PIC External --}}
                <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4">
                        <h4 class="text-base font-semibold text-gray-900 dark:text-zinc-100">PIC External</h4>
                    </div>
                    <div class="px-6 py-5">
                        @if($reminder->pic_external_nama)
                            <div class="flex items-center gap-3 p-3 rounded-md border bg-gray-50 border-gray-200 dark:bg-zinc-800/50 dark:border-zinc-700">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400 shrink-0">
                                    {{ strtoupper(substr($reminder->pic_external_nama, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-zinc-100 truncate">{{ $reminder->pic_external_nama }}</p>
                                    <p class="text-xs text-gray-500 dark:text-zinc-400">{{ $reminder->pic_external_telpon ?: '-' }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-zinc-400 italic">Tidak ada PIC External.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Lampiran --}}
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="border-b border-gray-200 dark:border-zinc-800 px-6 py-4">
                    <h4 class="text-base font-semibold text-gray-900 dark:text-zinc-100">Lampiran Dokumen</h4>
                </div>
                <div class="px-6 py-5">
                    @if($reminder->attachment_name)
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-4 rounded-md border border-gray-200 bg-gray-50 dark:border-zinc-700 dark:bg-zinc-800/50">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $ext === 'pdf' ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' }} shrink-0">
                                    @if($ext === 'pdf')
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                    @else
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-zinc-100 truncate">{{ $reminder->attachment_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-zinc-400 uppercase">{{ $ext }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('doc.view', $reminder->id) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Lihat
                                </a>
                                <a href="{{ route('doc.download', $reminder->id) }}" class="inline-flex items-center gap-1.5 rounded-md border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-800 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-zinc-700 transition-colors">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    Download
                                </a>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 dark:text-zinc-400 italic">Tidak ada lampiran.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
