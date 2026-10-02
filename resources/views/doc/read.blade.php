@php
    $activeJenis = $jenis ?? request('jenis', 'semua');
    $filterableColumns = [
        'nomor' => 'No',
        'nama_dokumen' => 'Nama Dokumen',
        'no_dokumen' => 'No Dokumen',
        'jenis_dokumen' => 'Jenis Dokumen',
        'pic' => 'PIC',
        'penerbit' => 'Penerbit',
        'terbit' => 'Terbit',
        'expired' => 'Expired',
        'sisa_hari' => 'Sisa Hari',
        'aksi' => 'Aksi',
    ];

    $isDocTypeSelected = function($docType) use ($activeJenis) {
        if ($activeJenis == $docType->id) return true;
        if (strtolower($activeJenis) === strtolower($docType->nama_jenis)) return true;
        if ($activeJenis === 'spt' && strtolower($docType->nama_jenis) === 'wajib lapor tahunan') return true;
        return false;
    };
@endphp

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('docColumns', {
        nomor: true,
        nama_dokumen: true,
        no_dokumen: true,
        jenis_dokumen: true,
        pic: false,
        penerbit: true,
        terbit: false,
        expired: true,
        sisa_hari: true,
        aksi: true,
        reset() {
            this.nomor = true;
            this.nama_dokumen = true;
            this.no_dokumen = true;
            this.jenis_dokumen = true;
            this.pic = false;
            this.penerbit = true;
            this.terbit = false;
            this.expired = true;
            this.sisa_hari = true;
            this.aksi = true;
        }
    });
});
</script>

@section('header-actions')
<div x-data="{ searchOpen: false, filterOpen: false }" class="flex items-center gap-2">
    <a href="{{ route('doc.create') }}" class="hidden md:inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-black text-lg font-bold text-white hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200 transition-colors" title="Tambah Dokumen">
        +
    </a>
    <a href="{{ route('doc.create') }}" class="md:hidden flex fixed bottom-5 right-5 z-30 h-12 w-12 items-center justify-center rounded-full bg-black text-2xl font-bold leading-none text-white shadow-lg transition-colors hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200" title="Tambah Dokumen" aria-label="Tambah Dokumen">
        +
    </a>

    {{-- Search icon: toggle open --}}
    <button @click="searchOpen = !searchOpen; if(searchOpen) filterOpen = false" type="button"
        class="p-2 rounded-lg transition-colors sm:!flex shrink-0"
        :class="searchOpen
            ? 'text-indigo-600 dark:text-indigo-400 sm:scale-110'
            : 'text-gray-500 hover:text-gray-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-gray-100 dark:hover:bg-zinc-800'"
        x-show="!searchOpen">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
        </svg>
    </button>

    {{-- Search input inline --}}
    <div x-show="searchOpen" x-cloak x-data="{ query: '' }" @click.outside="searchOpen = false" @keydown.escape.window="searchOpen = false" class="flex items-center gap-2 flex-1 sm:flex-none animate-fadeIn">
        <div class="relative flex-1 sm:flex-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input id="header-search" type="search" data-datatable-search-input placeholder="Cari..."
                x-ref="headerSearchInput"
                x-model="query"
                x-init="$watch('searchOpen', v => { if(v) { query = ''; setTimeout(() => $refs.headerSearchInput.focus(), 50) } })"
                class="w-full sm:w-64 h-9 pl-9 pr-8 text-xs bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-full text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-zinc-500 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-400 outline-none transition shadow-xs">
            <button x-show="query.length > 0" @click="query = ''; $refs.headerSearchInput.dispatchEvent(new Event('input'))" type="button"
                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        {{-- Close search: X on the right of the field --}}
        <button @click="searchOpen = false" type="button"
            class="p-1.5 rounded-full text-gray-400 hover:text-gray-600 dark:text-zinc-500 dark:hover:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-800 cursor-pointer shrink-0"
            title="Tutup pencarian">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Filter icon --}}
    <div class="relative">
        <button @click="filterOpen = !filterOpen; if(filterOpen) searchOpen = false" type="button"
            class="relative p-2 text-gray-500 hover:text-gray-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
            @if ($activeJenis !== 'semua' && !empty($activeJenis))
                <span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-green-500"></span>
            @endif
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M3.25 4.5A1.25 1.25 0 014.5 3.25h11A1.25 1.25 0 0116.75 4.5v1.039c0 .332-.132.65-.366.884L12.75 10.107v5.143a.75.75 0 01-.27.578l-2 1.75a.75.75 0 01-1.23-.578v-6.893L3.616 6.423A1.25 1.25 0 013.25 5.54V4.5z" />
            </svg>
        </button>

        <div x-show="filterOpen" x-cloak
            @click.outside="filterOpen = false"
            @keydown.escape.window="filterOpen = false"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed left-4 right-4 top-20 z-50 mx-auto max-w-sm md:absolute md:left-auto md:right-0 md:top-auto md:mt-2 md:mx-0 md:w-[448px] md:max-w-md max-h-[75vh] overflow-y-auto overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-black/40">
            <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm font-semibold text-gray-800 dark:text-zinc-100">Filter</p>
            </div>

            <div class="p-3 space-y-3">
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-zinc-300">Jenis Dokumen</label>
                    <select onchange="window.location.href = this.value"
                        class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                        <option value="{{ route('dokumen', ['jenis' => 'semua']) }}" @selected($activeJenis === 'semua' || empty($activeJenis))>Semua Jenis Dokumen</option>
                        @foreach ($documentTypes as $docType)
                            <option value="{{ route('dokumen', ['jenis' => $docType->id]) }}" @selected($isDocTypeSelected($docType))>
                                {{ $docType->nama_jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-zinc-300">Kolom</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                        @foreach ($filterableColumns as $key => $label)
                            <button type="button"
                                @click="$store.docColumns.{{ $key }} = !$store.docColumns.{{ $key }}"
                                class="flex w-full items-center justify-between rounded-md border px-3 py-2 text-sm transition-colors"
                                :class="$store.docColumns.{{ $key }} ? 'border-green-500 bg-green-50 text-green-700 dark:border-green-500 dark:bg-green-900/25 dark:text-green-300' : 'border-zinc-300 bg-white text-gray-700 hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700'">
                                <span class="whitespace-nowrap">{{ $label }}</span>
                                <svg x-show="$store.docColumns.{{ $key }}" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-none text-green-600 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.415l-7.25 7.25a1 1 0 01-1.415 0l-3.25-3.25a1 1 0 111.415-1.415l2.542 2.543 6.543-6.543a1 1 0 011.415 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 border-t border-zinc-200 px-4 py-3 text-xs dark:border-zinc-700 dark:bg-zinc-900">
                <button type="button" @click="$store.docColumns.reset()" class="font-medium text-green-600 transition-colors hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                    Reset semua
                </button>
                <button type="button" @click="filterOpen = false" class="font-medium text-gray-500 transition-colors hover:text-gray-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-zinc-100 leading-tight">
            {{ __('Manajemen Dokumen') }}
        </h2>
    </x-slot>

    <div class="">
        @include('doc.partials.reminder-notice-banner')

        {{-- Panel Konten --}}
        <div class="flex flex-col md:h-[calc(100vh-135px)] md:overflow-hidden md:rounded-xl md:border md:border-zinc-200 md:bg-white md:shadow-sm md:dark:border-zinc-700 md:dark:bg-zinc-900">
            <div class="flex-1 min-h-0 overflow-hidden text-gray-900 dark:text-zinc-100">
                @if ($reminders->isNotEmpty())
                    @php
                        $hasJenis = $activeJenis === 'semua';
                        $today = now();
                        $calcRow = function ($reminder) use ($today) {
                            $isToday = $reminder->tanggal_expired?->isSameDay($today) ?? false;
                            $isExpired = $reminder->tanggal_expired && $reminder->tanggal_expired->lt($today->copy()->startOfDay()) && !$isToday;
                            $reminderMonths = (int) $reminder->reminder_bulan;
                            $daysLeft = $reminder->tanggal_expired
                                ? $today->copy()->startOfDay()->diffInDays($reminder->tanggal_expired->copy()->startOfDay(), false)
                                : null;

                            $status = match(true) {
                                is_null($daysLeft) => 'lifetime',
                                $daysLeft < 0 => 'expired',
                                default => 'active',
                            };

                            if ($status === 'active') {
                                $thresholds = match($reminderMonths) {
                                    1 => [30 => 'neutral', 15 => 'green', 7 => 'yellow', 0 => 'red'],
                                    3 => [90 => 'neutral', 45 => 'green', 18 => 'yellow', 0 => 'red'],
                                    6 => [180 => 'neutral', 90 => 'green', 36 => 'yellow', 0 => 'red'],
                                    default => tap([], function (&$t) use ($reminderMonths) {
                                        $totalDays = max(30, $reminderMonths * 30);
                                        $t[$totalDays] = 'neutral';
                                        $t[(int) round($totalDays * 0.5)] = 'green';
                                        $t[(int) round($totalDays * 0.25)] = 'yellow';
                                        $t[0] = 'red';
                                    }),
                                };
                                $status = collect($thresholds)->first(fn($label, $threshold) => $daysLeft >= $threshold);
                            }

                            $badgeMap = [
                                'lifetime' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300',
                                'expired' => 'bg-gray-200 text-gray-700 dark:bg-zinc-700 dark:text-zinc-300',
                                'red' => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300',
                                'yellow' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300',
                                'green' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300',
                                'neutral' => '',
                            ];
                            $badgeClass = 'inline-flex min-w-20 justify-center rounded-md px-2.5 py-1 text-xs font-semibold ' . ($badgeMap[$status] ?? '');
                            $showBadge = $status !== 'neutral';
                            $badgeText = match(true) {
                                $status === 'lifetime' => 'Seumur Hidup',
                                $isToday => 'Hari ini',
                                $daysLeft > 0 => $daysLeft . ' hari lagi',
                                default => 'Kadaluarsa',
                            };

                            return compact('isToday', 'isExpired', 'daysLeft', 'status', 'badgeClass', 'showBadge', 'badgeText');
                        };
                    @endphp

                    {{-- Mobile Card Layout --}}
                    <div class="block md:hidden p-3 pb-6 space-y-3">
                        @foreach ($reminders as $reminder)
                            @php
                                extract($calcRow($reminder));
                                $searchBlob = strtolower(implode(' ', array_filter([
                                    $reminder->nama_dokumen,
                                    $reminder->no_dokumen,
                                    $reminder->jenis_dokumen_label,
                                    $reminder->penerbit_tujuan,
                                    $reminder->tanggal_expired?->format('d-m-Y'),
                                    $badgeText,
                                ])));
                            @endphp
                            <div data-doc-search="{{ e($searchBlob) }}" class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <div x-show="$store.docColumns.nomor" class="text-xs font-medium text-gray-400 dark:text-zinc-500">#{{ $loop->iteration }}</div>
                                        <div x-show="$store.docColumns.nama_dokumen" class="mt-0.5 text-sm font-semibold text-gray-900 dark:text-zinc-100 break-words" title="{{ $reminder->nama_dokumen }}">{{ $reminder->nama_dokumen }}</div>
                                    </div>
                                    <div x-show="$store.docColumns.sisa_hari" class="shrink-0">
                                        @if ($showBadge)
                                            <span class="{{ $badgeClass }}">{{ $badgeText }}</span>
                                        @else
                                            <span class="inline-flex min-w-20 justify-center px-2.5 py-1 text-[11px] font-medium text-gray-500 dark:text-zinc-400">{{ $badgeText }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-2 space-y-1.5 text-sm text-gray-600 dark:text-zinc-400">
                                    <div x-show="$store.docColumns.no_dokumen" class="flex flex-wrap gap-x-2 gap-y-0.5">
                                        <span class="text-gray-400 dark:text-zinc-500">No Dok.</span>
                                        <span class="text-gray-900 dark:text-zinc-100 break-all">{{ $reminder->no_dokumen }}</span>
                                    </div>
                                    @if($hasJenis)
                                        <div x-show="$store.docColumns.jenis_dokumen">
                                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $reminder->jenis_dokumen_label }}</span>
                                        </div>
                                    @endif
                                    <div x-show="$store.docColumns.penerbit" class="flex flex-wrap gap-x-2 gap-y-0.5">
                                        <span class="text-gray-400 dark:text-zinc-500">Penerbit</span>
                                        <span class="text-gray-900 dark:text-zinc-100 break-words">{{ $reminder->penerbit_tujuan }}</span>
                                    </div>
                                    <div x-show="$store.docColumns.terbit" class="flex flex-wrap gap-x-2 gap-y-0.5">
                                        <span class="text-gray-400 dark:text-zinc-500">Terbit</span>
                                        <span class="text-gray-900 dark:text-zinc-100">{{ $reminder->tanggal_terbit?->format('d-m-Y') ?? '-' }}</span>
                                    </div>
                                    <div x-show="$store.docColumns.expired" class="flex flex-wrap gap-x-2 gap-y-0.5">
                                        <span class="text-gray-400 dark:text-zinc-500">Expired</span>
                                        <span class="text-gray-900 dark:text-zinc-100">{{ $reminder->tanggal_expired ? $reminder->tanggal_expired->format('d-m-Y') : 'Seumur Hidup' }}</span>
                                    </div>
                                    <div x-show="$store.docColumns.pic" class="flex flex-wrap gap-x-2 gap-y-0.5">
                                        <span class="text-gray-400 dark:text-zinc-500">PIC</span>
                                        <span class="min-w-0 break-words text-gray-900 dark:text-zinc-100">
                                            @if($reminder->internalPics->isNotEmpty())
                                                {{ $reminder->internalPics->first()->pivot->nama ?? $reminder->internalPics->first()->nama }}
                                                @if($reminder->internalPics->count() > 1)
                                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">(+{{ $reminder->internalPics->count() - 1 }})</span>
                                                @endif
                                                <span class="block text-xs text-gray-500 dark:text-zinc-400 break-all">{{ $reminder->internalPics->first()->pivot->email ?? $reminder->internalPics->first()->email ?: '-' }}</span>
                                            @else
                                                {{ $reminder->pic_nama ?: '-' }}
                                                <span class="block text-xs text-gray-500 dark:text-zinc-400 break-all">{{ $reminder->pic_email ?: '-' }}</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div x-show="$store.docColumns.aksi" class="mt-3 flex flex-wrap items-center gap-2 border-t border-zinc-100 pt-3 dark:border-zinc-800">
                                    @can('update', $reminder)
                                        <a href="{{ route('doc.edit', $reminder->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-blue-600 transition-colors hover:bg-blue-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-blue-400 dark:hover:bg-blue-900/20" title="Edit{{ $isExpired ? ' / Perpanjang' : '' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                            Edit
                                        </a>
                                    @endcan
                                    @can('delete', $reminder)
                                        <form method="POST" action="{{ route('doc.destroy', $reminder->id) }}" class="contents">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-red-500 transition-colors hover:bg-red-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-red-400 dark:hover:bg-red-900/20" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                Hapus
                                            </button>
                                        </form>
                                    @endcan
                                    @can('view', $reminder)
                                        <a href="{{ route('doc.show', $reminder->id) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-emerald-600 transition-colors hover:bg-emerald-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-emerald-400 dark:hover:bg-emerald-900/20" title="Lihat Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3.75c-3.69 0-6.86 2.29-8.28 5.54a1 1 0 000 .92C3.14 13.46 6.31 15.75 10 15.75s6.86-2.29 8.28-5.54a1 1 0 000-.92C16.86 6.04 13.69 3.75 10 3.75zm0 8.5A2.25 2.25 0 1 1 10 7.5a2.25 2.25 0 0 1 0 4.75z" /></svg>
                                            Detail
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop Table Layout --}}
                    <div class="hidden md:block h-full overflow-hidden">
                    <table data-datatable class="w-full table-auto text-sm">
                            <thead class="bg-gray-50 dark:bg-zinc-800/50 border-b border-gray-200 dark:border-zinc-700">
                                <tr>
                                    <th x-cloak x-show="$store.docColumns.nomor" class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">No</th>
                                    <th x-cloak x-show="$store.docColumns.nama_dokumen" class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Dokumen</th>
                                    <th x-cloak x-show="$store.docColumns.no_dokumen" class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">No Dok.</th>
                                    @if($hasJenis)
                                        <th x-cloak x-show="$store.docColumns.jenis_dokumen" class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Jenis</th>
                                    @endif
                                    <th x-cloak x-show="$store.docColumns.pic" class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">PIC</th>
                                    <th x-cloak x-show="$store.docColumns.penerbit" class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Penerbit</th>
                                    <th x-cloak x-show="$store.docColumns.terbit" class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Terbit</th>
                                    <th x-cloak x-show="$store.docColumns.expired" class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Expired</th>
                                    <th x-cloak x-show="$store.docColumns.sisa_hari" class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Sisa Hari</th>
                                    <th x-cloak x-show="$store.docColumns.aksi" class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-zinc-800">
                                @foreach ($reminders as $reminder)
                                    @php extract($calcRow($reminder)); @endphp
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-900/40 transition-colors">
                                        <td x-cloak x-show="$store.docColumns.nomor" class="whitespace-nowrap text-sm text-gray-900 dark:text-zinc-100">{{ $loop->iteration }}</td>
                                        <td x-cloak x-show="$store.docColumns.nama_dokumen" class="text-sm text-gray-900 dark:text-zinc-100 max-w-[200px]">
                                            <span class="block truncate leading-tight" title="{{ $reminder->nama_dokumen }}">{{ $reminder->nama_dokumen }}</span>
                                        </td>
                                        <td x-cloak x-show="$store.docColumns.no_dokumen" class="whitespace-nowrap text-sm text-gray-900 dark:text-zinc-100">
                                            <span class="truncate" title="{{ $reminder->no_dokumen }}">{{ $reminder->no_dokumen }}</span>
                                        </td>
                                        @if($activeJenis === 'semua')
                                            <td x-cloak x-show="$store.docColumns.jenis_dokumen" class="text-sm text-gray-900 dark:text-zinc-100">
                                                <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-[11px] font-medium text-gray-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $reminder->jenis_dokumen_label }}</span>
                                            </td>
                                        @endif
                                        <td x-cloak x-show="$store.docColumns.pic" class="text-sm break-words">
                                            @if($reminder->internalPics->isNotEmpty())
                                                <div class="text-sm leading-tight text-gray-900 dark:text-zinc-100">
                                                    {{ $reminder->internalPics->first()->pivot->nama ?? $reminder->internalPics->first()->nama }}
                                                    @if($reminder->internalPics->count() > 1)
                                                        <span class="text-xs text-indigo-600 dark:text-indigo-400 font-bold">(+{{ $reminder->internalPics->count() - 1 }})</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs leading-tight text-gray-500 dark:text-zinc-400">
                                                    {{ $reminder->internalPics->first()->pivot->email ?? $reminder->internalPics->first()->email ?: '-' }}
                                                </div>
                                            @else
                                                <div class="text-sm leading-tight text-gray-900 dark:text-zinc-100">{{ $reminder->pic_nama ?: '-' }}</div>
                                                <div class="text-xs leading-tight text-gray-500 dark:text-zinc-400">{{ $reminder->pic_email ?: '-' }}</div>
                                            @endif
                                        </td>
                                        <td x-cloak x-show="$store.docColumns.penerbit" class="text-sm text-gray-900 dark:text-zinc-100 max-w-[180px]">
                                            <span class="block truncate leading-tight" title="{{ $reminder->penerbit_tujuan }}">{{ $reminder->penerbit_tujuan }}</span>
                                        </td>
                                        <td x-cloak x-show="$store.docColumns.terbit" class="whitespace-nowrap text-sm text-gray-900 dark:text-zinc-100">{{ $reminder->tanggal_terbit?->format('d-m-Y') ?? '-' }}</td>
                                        <td x-cloak x-show="$store.docColumns.expired" class="whitespace-nowrap text-sm text-gray-900 dark:text-zinc-100">
                                            {{ $reminder->tanggal_expired ? $reminder->tanggal_expired->format('d-m-Y') : 'Seumur Hidup' }}
                                        </td>
                                        <td x-cloak x-show="$store.docColumns.sisa_hari" class="whitespace-nowrap text-sm text-gray-900 dark:text-zinc-100">
                                            @if ($showBadge)
                                                <span class="{{ $badgeClass }}">{{ $badgeText }}</span>
                                            @else
                                                <span class="inline-flex min-w-20 justify-center px-2.5 py-1 text-[11px] font-medium text-gray-500 dark:text-zinc-400">{{ $badgeText }}</span>
                                            @endif
                                        </td>
                                        <td x-cloak x-show="$store.docColumns.aksi" class="min-w-[100px]">
                                            <div class="flex items-center gap-1 whitespace-nowrap justify-start">
                                                @can('update', $reminder)
                                                    <a href="{{ route('doc.edit', $reminder->id) }}" class="inline-flex items-center justify-center p-1.5 text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Edit{{ $isExpired ? ' / Perpanjang' : '' }}">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                                    </a>
                                                @endcan
                                                @can('delete', $reminder)
                                                    <form method="POST" action="{{ route('doc.destroy', $reminder->id) }}" class="contents">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="inline-flex items-center justify-center p-1.5 text-red-400 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" title="Hapus">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @can('view', $reminder)
                                                    <a href="{{ route('doc.show', $reminder->id) }}" class="inline-flex items-center justify-center p-1.5 text-emerald-500 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 rounded-md hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors" title="Lihat Detail">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3.75c-3.69 0-6.86 2.29-8.28 5.54a1 1 0 000 .92C3.14 13.46 6.31 15.75 10 15.75s6.86-2.29 8.28-5.54a1 1 0 000-.92C16.86 6.04 13.69 3.75 10 3.75zm0 8.5A2.25 2.25 0 1 1 10 7.5a2.25 2.25 0 0 1 0 4.75z" /></svg>
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                @else
                    <div class="flex items-center justify-center text-gray-500 dark:text-zinc-400 py-8">
                        <p class="text-sm">Belum ada data dokumen. Klik tombol di atas untuk menambahkan dokumen baru.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
