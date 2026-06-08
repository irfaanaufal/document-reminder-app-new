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

        @if (session('error'))
            <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-900/50 dark:bg-rose-900/20 dark:text-rose-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-4 rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <form method="GET" action="{{ route('logs.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label for="q" class="mb-1 block text-xs font-medium text-gray-600 dark:text-zinc-300">Cari</label>
                    <input id="q" name="q" value="{{ $filters['q'] }}" type="text" placeholder="No dokumen / nama / nomor" class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                </div>

                <div>
                    <label for="status" class="mb-1 block text-xs font-medium text-gray-600 dark:text-zinc-300">Status</label>
                    <select id="status" name="status" class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                        <option value="">Semua</option>
                        @foreach ($statusOptions as $option)
                            <option value="{{ $option }}" @selected($filters['status'] === $option)>{{ strtoupper($option) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="rule" class="mb-1 block text-xs font-medium text-gray-600 dark:text-zinc-300">Rule</label>
                    <select id="rule" name="rule" class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                        <option value="">Semua</option>
                        @foreach ($ruleOptions as $option)
                            <option value="{{ $option }}" @selected($filters['rule'] === $option)>{{ strtoupper($option) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date_from" class="mb-1 block text-xs font-medium text-gray-600 dark:text-zinc-300">Dari Tanggal</label>
                    <input id="date_from" name="date_from" value="{{ $filters['date_from'] }}" type="date" class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                </div>

                <div>
                    <label for="date_to" class="mb-1 block text-xs font-medium text-gray-600 dark:text-zinc-300">Sampai Tanggal</label>
                    <input id="date_to" name="date_to" value="{{ $filters['date_to'] }}" type="date" class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                </div>

                <div class="sm:col-span-2 lg:col-span-6 flex items-end justify-end gap-2">
                    <a href="{{ route('logs.index') }}" class="inline-flex items-center justify-center rounded-md border border-zinc-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700">Reset</a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-green-700">Filter</button>
                </div>
            </form>
        </div>

        <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
            <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-zinc-400">Total Notifikasi</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $summary['total'] }}</p>
            </div>
            <div class="rounded-xl border border-cyan-200 bg-cyan-50 p-4 shadow-sm dark:border-cyan-900/50 dark:bg-cyan-900/20">
                <p class="text-xs font-medium uppercase tracking-wide text-cyan-700 dark:text-cyan-300">Reminder Aktif</p>
                <p class="mt-1 text-2xl font-bold text-cyan-700 dark:text-cyan-300">{{ $summary['due_reminders'] }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 shadow-sm dark:border-emerald-900/50 dark:bg-emerald-900/20">
                <p class="text-xs font-medium uppercase tracking-wide text-emerald-700 dark:text-emerald-300">Sent</p>
                <p class="mt-1 text-2xl font-bold text-emerald-700 dark:text-emerald-300">{{ $summary['sent'] }}</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 shadow-sm dark:border-rose-900/50 dark:bg-rose-900/20">
                <p class="text-xs font-medium uppercase tracking-wide text-rose-700 dark:text-rose-300">Failed</p>
                <p class="mt-1 text-2xl font-bold text-rose-700 dark:text-rose-300">{{ $summary['failed'] }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 shadow-sm dark:border-amber-900/50 dark:bg-amber-900/20">
                <p class="text-xs font-medium uppercase tracking-wide text-amber-700 dark:text-amber-300">Pending</p>
                <p class="mt-1 text-2xl font-bold text-amber-700 dark:text-amber-300">{{ $summary['pending'] }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="overflow-x-auto">
                <table class="min-w-[980px] w-full divide-y divide-gray-200 dark:divide-zinc-800 text-sm">
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
                                    <div class="text-xs text-gray-500 dark:text-zinc-400">{{ $document?->pic_telpon ?: '-' }}</div>
                                </td>
                                <td class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100 whitespace-nowrap">{{ $log->recipient_phone }}</td>
                                <td class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100 whitespace-nowrap">{{ strtoupper((string) $log->reminder_rule) }}</td>
                                <td class="px-4 py-3 align-top">
                                    <span class="inline-flex rounded-md px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ strtoupper((string) $log->status) }}</span>
                                </td>
                                <td class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100">{{ $log->attempt_count }}</td>
                                <td class="px-4 py-3 align-top">
                                    @if ($log->status === 'failed')
                                        <form method="POST" action="{{ route('logs.retry', $log->id) }}" class="mb-2">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center justify-center rounded-md bg-amber-500 px-2.5 py-1.5 text-xs font-semibold text-white transition-colors hover:bg-amber-600">
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
