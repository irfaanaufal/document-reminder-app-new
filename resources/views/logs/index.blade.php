@php
    $hasActiveFilters = !empty($filters['q']) || !empty($filters['status']) || !empty($filters['rule']) || !empty($filters['date_from']) || !empty($filters['date_to']);
@endphp

@section('header-actions')
<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" type="button"
        class="relative p-2 text-gray-500 hover:text-gray-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-gray-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
        </svg>
        @if($hasActiveFilters)
            <span class="absolute top-1 right-1 flex h-2 w-2">
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak
        @click.outside="open = false"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="fixed left-4 right-4 top-20 z-50 mx-auto max-w-sm md:absolute md:left-auto md:right-0 md:top-auto md:mt-2 md:mx-0 md:max-w-[calc(100vw-2rem)] overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-black/40">
        <form method="GET" action="{{ route('logs.index') }}" class="p-4">
            <div class="space-y-3">
                <div>
                    <label for="q-combined" class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-zinc-300">Cari</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 dark:text-zinc-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        <input id="q-combined" name="q" value="{{ $filters['q'] }}" type="text" placeholder="No dokumen / nama / email"
                            class="w-full rounded-lg border border-zinc-300 bg-white py-2.5 pl-9 pr-3 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100"
                            x-ref="searchInput" x-init="$watch('open', v => { if(v) setTimeout(() => $refs.searchInput.focus(), 50) })">
                    </div>
                </div>

                <div class="border-t border-zinc-100 dark:border-zinc-800 pt-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="status-combined" class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-zinc-300">Status</label>
                            <select id="status-combined" name="status" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                <option value="">Semua</option>
                                @foreach ($statusOptions as $option)
                                    <option value="{{ $option }}" @selected($filters['status'] === $option)>{{ strtoupper($option) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="rule-combined" class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-zinc-300">Rule</label>
                            <select id="rule-combined" name="rule" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                <option value="">Semua</option>
                                @foreach ($ruleOptions as $option)
                                    <option value="{{ $option }}" @selected($filters['rule'] === $option)>{{ strtoupper($option) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="date_from-combined" class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-zinc-300">Dari Tanggal</label>
                        <input id="date_from-combined" name="date_from" value="{{ $filters['date_from'] }}" type="date"
                            class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    </div>
                    <div>
                        <label for="date_to-combined" class="mb-1.5 block text-xs font-medium text-gray-600 dark:text-zinc-300">Sampai Tanggal</label>
                        <input id="date_to-combined" name="date_to" value="{{ $filters['date_to'] }}" type="date"
                            class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                    </div>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-end gap-2 border-t border-zinc-100 pt-3 dark:border-zinc-800">
                <a href="{{ route('logs.index') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-zinc-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 transition-colors hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">
                    Reset
                </a>
                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-green-700">
                    Filter
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-zinc-100 leading-tight">
            Logs Reminder
        </h2>
    </x-slot>

    <div class="py-1">
        @if (session('success'))
            <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-300">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 grid grid-cols-6 gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <div class="col-span-3 sm:col-span-1 rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-zinc-400">Total Notifikasi</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $summary['total'] }}</p>
            </div>
            <div class="col-span-3 sm:col-span-1 rounded-xl border border-cyan-200 bg-cyan-50 p-4 shadow-sm dark:border-cyan-900/50 dark:bg-cyan-900/20">
                <p class="text-xs font-medium uppercase tracking-wide text-cyan-700 dark:text-cyan-300">Reminder Aktif</p>
                <p class="mt-1 text-2xl font-bold text-cyan-700 dark:text-cyan-300">{{ $summary['due_reminders'] }}</p>
            </div>
            <div class="col-span-2 sm:col-span-1 rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm dark:border-emerald-900/50 dark:bg-emerald-900/20">
                <p class="text-xs font-medium uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Sent</p>
                <p class="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $summary['sent'] }}</p>
            </div>
            <div class="col-span-2 sm:col-span-1 rounded-xl border border-rose-200 bg-rose-50 p-4 shadow-sm dark:border-rose-900/50 dark:bg-rose-900/20">
                <p class="text-xs font-medium uppercase tracking-wide text-rose-700 dark:text-rose-300">Failed</p>
                <p class="mt-1 text-2xl font-bold text-rose-700 dark:text-rose-300">{{ $summary['failed'] }}</p>
            </div>
            <div class="col-span-2 sm:col-span-1 rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm dark:border-amber-900/50 dark:bg-amber-900/20">
                <p class="text-xs font-medium uppercase tracking-wide text-amber-700 dark:text-amber-300">Pending</p>
                <p class="mt-1 text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $summary['pending'] }}</p>
            </div>
        </div>

        {{-- Mobile Card Layout --}}
        <div class="block md:hidden space-y-3">
            @forelse ($logs as $log)
                @php
                    $statusClass = match ($log->status) {
                        'sent' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
                        'failed' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
                        'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                        default => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300',
                    };
                    $responseBody = $log->provider_response;
                    if (is_array($responseBody)) {
                        $responseBody = json_encode($responseBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                    }
                    $document = $log->documentReminder;
                @endphp
                <div x-data="{ open: false }" class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex rounded-md px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ strtoupper((string) $log->status) }}</span>
                            <span class="inline-flex rounded-md bg-zinc-100 px-2 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">{{ strtoupper((string) $log->reminder_rule) }}</span>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-zinc-500">#{{ $logs->firstItem() + $loop->index }}</span>
                    </div>

                    <div class="mt-3 min-w-0">
                        <div class="font-medium text-gray-900 dark:text-zinc-100 truncate">{{ $document?->no_dokumen ?? '-' }}</div>
                        <div class="text-sm text-gray-500 dark:text-zinc-400 truncate">{{ $document?->nama_dokumen ?? '-' }}</div>
                    </div>

                    <div class="mt-2 flex items-center gap-1.5 text-sm text-gray-600 dark:text-zinc-400 min-w-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 dark:text-zinc-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                        </svg>
                        <span class="truncate">{{ $log->recipient_name ?: ($document?->pic_nama ?: '-') }}</span>
                    </div>
                    <div class="mt-1 flex items-center gap-1.5 text-xs text-gray-400 dark:text-zinc-500 min-w-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 dark:text-zinc-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                        <span class="break-all">{{ $log->recipient_email ?: '-' }}</span>
                    </div>

                    <div class="mt-2 flex flex-wrap items-center gap-1.5 text-xs text-gray-400 dark:text-zinc-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-gray-400 dark:text-zinc-500 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ optional($log->sent_at)->format('d-m-Y H:i') ?? 'Belum terkirim' }}</span>
                        <span class="text-gray-300 dark:text-zinc-600">•</span>
                        <span>Attempt: {{ $log->attempt_count }}</span>
                    </div>

                    <div class="mt-3 flex items-center gap-2 border-t border-zinc-100 pt-3 dark:border-zinc-800">
                        @if ($log->status === 'failed')
                            <form method="POST" action="{{ route('logs.retry', $log->id) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-amber-500 px-3 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-amber-600">
                                    Retry
                                </button>
                            </form>
                        @endif

                        <button @click="open = !open" type="button" class="inline-flex items-center justify-center rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-1 h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" :class="{ 'rotate-180': open }">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Response
                        </button>
                    </div>

                    <div x-show="open" x-cloak x-collapse class="mt-3">
                        <pre class="max-w-full overflow-auto rounded-lg bg-zinc-100 p-2.5 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">{{ $responseBody ?: '-' }}</pre>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-zinc-200 bg-white p-8 text-center shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-12 w-12 text-gray-300 dark:text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="mt-3 text-sm text-gray-500 dark:text-zinc-400">Belum ada data log dengan filter saat ini.</p>
                </div>
            @endforelse

            @if ($logs->hasPages())
                <div class="rounded-xl border border-zinc-200 bg-white px-4 py-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

        {{-- Desktop Table Layout --}}
        <div class="hidden md:block overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-[800px] w-full divide-y divide-gray-200 dark:divide-zinc-800 text-sm">
                    <thead class="bg-gray-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-300">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-300">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-300">Dokumen</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-300">PIC</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-300">Tujuan</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-300">Rule</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-300">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-300">Attempt</th>
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-600 dark:text-zinc-300">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-zinc-800">
                        @forelse ($logs as $log)
                            @php
                                $statusClass = match ($log->status) {
                                    'sent' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300',
                                    'failed' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/20 dark:text-rose-300',
                                    'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300',
                                    default => 'bg-sky-50 text-sky-700 dark:bg-sky-900/20 dark:text-sky-300',
                                };
                                $responseBody = $log->provider_response;
                                if (is_array($responseBody)) {
                                    $responseBody = json_encode($responseBody, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                                }
                                $document = $log->documentReminder;
                            @endphp
                            <tr>
                                <td class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100 font-medium whitespace-nowrap">{{ $logs->firstItem() + $loop->index }}</td>
                                <td class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100 whitespace-nowrap">
                                    <div class="font-medium">{{ optional($log->scheduled_for)->format('d-m-Y') ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-zinc-400">{{ optional($log->sent_at)->format('d-m-Y H:i') ?? 'Belum terkirim' }}</div>
                                </td>
                                <td class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100">
                                    <div class="font-medium">{{ $document?->no_dokumen ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $document?->nama_dokumen ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100">
                                    <div>{{ $log->recipient_name ?: ($document?->pic_nama ?: '-') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $log->recipient_email ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100 whitespace-nowrap">{{ $log->recipient_email }}</td>
                                <td class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100 whitespace-nowrap">{{ strtoupper((string) $log->reminder_rule) }}</td>
                                <td class="px-4 py-3 align-top">
                                    <span class="inline-flex rounded-md px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ strtoupper((string) $log->status) }}</span>
                                </td>
                                <td class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100">{{ $log->attempt_count }}</td>
                                <td class="px-4 py-3 align-top">
                                    @if ($log->status === 'failed')
                                        <form method="POST" action="{{ route('logs.retry', $log->id) }}" class="mb-2">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-amber-500 px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-amber-600">
                                                Retry
                                            </button>
                                        </form>
                                    @endif

                                    <details class="group">
                                        <summary class="cursor-pointer text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Lihat response</summary>
                                        <pre class="mt-2 max-w-[420px] overflow-auto rounded-md bg-zinc-100 p-2 text-xs text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">{{ $responseBody ?: '-' }}</pre>
                                    </details>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-zinc-400">Belum ada data log dengan filter saat ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-700">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
