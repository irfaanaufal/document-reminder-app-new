<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-zinc-100">
            Dashboard
        </h2>
    </x-slot>

    <div class="grid gap-4 sm:gap-6 sm:grid-rows-[auto_1fr] sm:h-[calc(100vh-135px)]">
        <div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                @php $canDocs = auth()->user()?->canAccessDocuments() ?? false; @endphp

                <!-- Card 1 -->
                @if ($canDocs)
                <a href="{{ route('dokumen', ['jenis' => 'semua']) }}" title="Manajemen Dokumen" class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 sm:p-6 shadow-sm flex items-start gap-3 sm:gap-4 hover:shadow-md transition-shadow">
                @else
                <div title="Manajemen Dokumen" class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 sm:p-6 shadow-sm flex items-start gap-3 sm:gap-4">
                @endif
                    <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-3 text-indigo-600 dark:border-indigo-800 dark:bg-indigo-950 dark:text-indigo-100">
                        <!-- Documents icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 dark:stroke-indigo-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <path d="M14 2v6h6"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold leading-tight text-gray-700 dark:text-zinc-200">Total Dokumen</h3>
                        <div class="mt-2 text-2xl sm:text-3xl font-bold leading-none text-gray-900 dark:text-zinc-100">{{ $totalDocuments ?? 0 }}</div>
                        <div class="mt-2 text-xs leading-tight text-gray-500 dark:text-zinc-400">Dokumen yang tersimpan</div>
                    </div>
                @if ($canDocs)
                </a>
                @else
                </div>
                @endif

                <!-- Card 2 -->
                @if ($canDocs)
                <a href="{{ route('dokumen', ['jenis' => 'sertifikat']) }}" title="Dokumen Sertifikat" class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 sm:p-6 shadow-sm flex items-start gap-3 sm:gap-4 hover:shadow-md transition-shadow">
                @else
                <div title="Dokumen Sertifikat" class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 sm:p-6 shadow-sm flex items-start gap-3 sm:gap-4">
                @endif
                    <div class="p-3 rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-300">
                        <!-- Certificate icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l2.09 4.26L18 7l-3 2.09L15.18 13 12 11.27 8.82 13 9 9.09 6 7l3.91-.74L12 2z"></path>
                            <path d="M21 15v4a1 1 0 0 1-1 1h-6v-5"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold leading-tight text-gray-700 dark:text-zinc-200">Total Sertifikat</h3>
                        <div class="mt-2 text-2xl sm:text-3xl font-bold leading-none text-gray-900 dark:text-zinc-100">{{ $totalSertifikat ?? 0 }}</div>
                        <div class="mt-2 text-xs leading-tight text-gray-500 dark:text-zinc-400">Dokumen bertipe Sertifikat</div>
                    </div>
                @if ($canDocs)
                </a>
                @else
                </div>
                @endif

                <!-- Card 3 -->
                @if ($canDocs)
                <a href="{{ route('dokumen', ['jenis' => 'spt']) }}" title="Wajib Lapor" class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 sm:p-6 shadow-sm flex items-start gap-3 sm:gap-4 hover:shadow-md transition-shadow">
                @else
                <div title="Wajib Lapor" class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 sm:p-6 shadow-sm flex items-start gap-3 sm:gap-4">
                @endif
                    <div class="p-3 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-300">
                        <!-- Report icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15V6a2 2 0 0 0-2-2H7L3 6v11a2 2 0 0 0 2 2h12"></path>
                            <path d="M7 10h8M7 14h5"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold leading-tight text-gray-700 dark:text-zinc-200">Total Wajib Lapor</h3>
                        <div class="mt-2 text-2xl sm:text-3xl font-bold leading-none text-gray-900 dark:text-zinc-100">{{ $totalWajibLapor ?? 0 }}</div>
                        <div class="mt-2 text-xs leading-tight text-gray-500 dark:text-zinc-400">Dokumen Wajib Lapor Tahunan</div>
                    </div>
                @if ($canDocs)
                </a>
                @else
                </div>
                @endif

                <!-- Card 4 -->
                @if ($canDocs)
                <a href="{{ route('dokumen', ['expired' => 1]) }}" title="Dokumen Expired" class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 sm:p-6 shadow-sm flex items-start gap-3 sm:gap-4 hover:shadow-md transition-shadow">
                @else
                <div title="Dokumen Expired" class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-4 sm:p-6 shadow-sm flex items-start gap-3 sm:gap-4">
                @endif
                    <div class="p-3 rounded-lg bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-300">
                        <!-- Expired icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 8v5"></path>
                            <path d="M12 17h.01"></path>
                            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold leading-tight text-gray-700 dark:text-zinc-200">Total Dokumen Expired</h3>
                        <div class="mt-2 text-2xl sm:text-3xl font-bold leading-none text-gray-900 dark:text-zinc-100">{{ $totalExpired ?? 0 }}</div>
                        <div class="mt-2 text-xs leading-tight text-gray-500 dark:text-zinc-400">Dokumen yang Sudah Expired</div>
                    </div>
                @if ($canDocs)
                </a>
                @else
                </div>
                @endif

            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 min-h-0">

        <!-- Kalender Reminder (3/4) -->
        <div
            class="lg:col-span-2 min-h-0"
            x-data="{
                selectedDate: @js($selectedCalendarDate),
                selectedDocuments: @js($selectedCalendarDocuments),
                days: @js($calendarDays),
                selectDay(day) {
                    if (! day.in_month) {
                        return;
                    }

                    this.selectedDate = day.date_key;
                    this.selectedDocuments = day.documents;
                },
                getSelectedDay() {
                    return this.days.find((day) => day.date_key === this.selectedDate) || null;
                },
                statusClass(state) {
                    if (state === 'expired' || state === 'red') {
                        return 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300';
                    }

                    if (state === 'yellow') {
                        return 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300';
                    }

                    if (state === 'green' || state === 'lifetime') {
                        return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300';
                    }

                    return 'bg-gray-100 text-gray-500 dark:bg-zinc-800 dark:text-zinc-300';
                },
            }"
        >
            <div class="rounded-2xl border border-gray-200 bg-white p-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 lg:p-4 flex flex-col h-full min-h-0">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-zinc-100 lg:text-base">Kalender Reminder</h3>
                        <p class="mt-0.5 text-[11px] text-gray-500 dark:text-zinc-400 lg:text-xs">Klik tanggal untuk melihat dokumen yang expired.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <select id="cal-month" onchange="window.location='{{ route('dashboard') }}?month=' + document.getElementById('cal-year').value + '-' + this.value"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs sm:text-sm font-medium text-gray-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @foreach($monthNames as $num => $name)
                                <option value="{{ str_pad($num, 2, '0', STR_PAD_LEFT) }}" {{ $num == $calendarMonthNum ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <select id="cal-year" onchange="window.location='{{ route('dashboard') }}?month=' + this.value + '-' + document.getElementById('cal-month').value"
                            class="rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs sm:text-sm font-medium text-gray-700 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @foreach($calendarYears as $year)
                                <option value="{{ $year }}" {{ $year == $calendarMonthYear ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-3 grid grid-cols-7 gap-px text-center text-[10px] sm:text-[10px] font-semibold uppercase tracking-wide text-gray-500 dark:text-zinc-400 lg:mt-4 lg:gap-1 lg:text-[11px]">
                    @foreach ($calendarWeekdays as $weekday)
                        <div class="py-0.5">{{ substr($weekday, 0, 3) }}</div>
                    @endforeach
                </div>

                <div class="mt-2 grid grid-cols-7 gap-px lg:mt-3 lg:gap-1">
                    @foreach ($calendarDays as $day)
                        <button
                            type="button"
                            @click="selectDay(@js($day))"
                            class="relative flex min-h-[40px] sm:min-h-[38px] lg:min-h-[42px] w-full flex-col rounded p-1 sm:p-1 lg:p-1.5 text-left transition"
                            :class="selectedDate === '{{ $day['date_key'] }}'
                                ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500/20 dark:border-blue-400 dark:bg-blue-900/20'
                                : '{{ $day['in_month']
                                    ? 'border-gray-200 bg-white hover:border-blue-300 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-blue-500'
                                    : 'border-gray-200 bg-gray-50 text-gray-400 dark:border-zinc-800 dark:bg-zinc-950/40 dark:text-zinc-600' }}'"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs font-semibold lg:text-sm {{ $day['in_month'] ? 'text-gray-900 dark:text-zinc-100' : 'text-gray-400 dark:text-zinc-600' }}">{{ $day['day'] }}</span>
                                @if (! empty($day['documents']))
                                    <span class="inline-flex h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full @if($day['state'] === 'expired' || $day['state'] === 'red') bg-red-500 @elseif($day['state'] === 'yellow') bg-amber-400 @elseif($day['state'] === 'green') bg-emerald-500 @else bg-transparent @endif ring-1 ring-white/80 dark:ring-zinc-900/60"></span>
                                @endif
                            </div>

                            @if (! empty($day['documents']))
                                <span class="absolute bottom-0.5 right-0.5 sm:bottom-1 sm:right-1 inline-flex items-center rounded bg-gray-100 px-1 py-px text-[7px] sm:text-[9px] font-medium text-gray-700 dark:bg-zinc-800 dark:text-zinc-200 lg:px-1.5 lg:text-[10px] whitespace-nowrap">
                                    <span class="block sm:hidden">{{ count($day['documents']) }}</span>
                                    <span class="hidden sm:inline" aria-hidden="true">{{ count($day['documents']) }} dok.</span>
                                </span>
                            @endif
                        </button>
                    @endforeach
                </div>

                <div class="mt-3 rounded-xl border border-zinc-200 bg-zinc-50 p-2 dark:border-zinc-700 dark:bg-zinc-950/40 lg:mt-4 lg:p-3 flex-1 min-h-0 overflow-y-auto">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-zinc-100" x-text="getSelectedDay() ? getSelectedDay().display_label : 'Pilih tanggal'"></p>
                            <div class="text-[11px] text-gray-500 dark:text-zinc-400 lg:text-xs">
                                <span class="block sm:hidden" x-text="selectedDocuments.length ? `${selectedDocuments.length} dok.` : 'Klik tanggal'"></span>
                                <span class="hidden sm:block" x-text="selectedDocuments.length ? `${selectedDocuments.length} dokumen terkait` : 'Klik tanggal yang memiliki indikator'"></span>
                            </div>
                        </div>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold lg:px-3 lg:text-[11px]" :class="getSelectedDay() ? statusClass(getSelectedDay().state) : 'bg-gray-100 text-gray-500 dark:bg-zinc-800 dark:text-zinc-300'" x-text="getSelectedDay() ? (getSelectedDay().state === 'expired' ? 'Expired' : (getSelectedDay().state === 'lifetime' ? 'Seumur Hidup' : (getSelectedDay().state === 'red' || getSelectedDay().state === 'yellow' ? 'Mendekati expired' : (getSelectedDay().state === 'green' ? 'Reminder aktif' : 'Tidak ada indikator')))) : 'Belum dipilih'"></span>
                    </div>

                    <div class="mt-2 space-y-1.5 lg:mt-3 lg:space-y-2">
                        <template x-if="selectedDocuments.length">
                            <template x-for="document in selectedDocuments" :key="document.id">
                                <div class="rounded-lg border border-zinc-200 bg-white p-2 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 lg:p-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-zinc-100 max-w-xs sm:max-w-full" x-text="document.name"></p>
                                            <p class="mt-1 text-[11px] text-gray-500 dark:text-zinc-400 lg:text-xs" x-text="document.type"></p>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            @if ($canDocs)
                                            <a :href="`/dokumen/${document.id}`" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-1 text-xs font-medium text-white hover:bg-blue-700 transition-colors">Detail</a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400 lg:mt-3 lg:gap-3 lg:text-xs">
                                        <span>Expired: <span class="font-medium text-gray-700 dark:text-zinc-200" x-text="document.expired_at"></span></span>
                                        <span x-show="document.days_left !== null && document.days_left >= 0">Sisa hari: <span class="font-medium text-gray-700 dark:text-zinc-200" x-text="document.days_left"></span></span>
                                    </div>
                                </div>
                            </template>
                        </template>

                        <div x-show="! selectedDocuments.length" class="rounded-lg border border-dashed border-zinc-300 bg-white px-3 py-2 text-center text-xs text-gray-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400 lg:py-3">
                            Tidak ada dokumen pada tanggal ini.
                        </div>
                </div>
            </div>
        </div>
        </div>

        <!-- Dokumen Jatuh Tempo (1/4) -->
        @if($expiringSoon->isNotEmpty())
        <div class="lg:col-span-1 min-h-0">
            <div class="h-full flex flex-col bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm">
                <div class="px-3 py-3 border-b border-gray-100 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="rounded-lg bg-amber-50 p-1.5 text-amber-600 dark:bg-amber-900/20 dark:text-amber-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-zinc-100">Jatuh Tempo</h3>
                            <p class="text-[11px] text-gray-500 dark:text-zinc-400">Segera ditindak</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 flex flex-col gap-2 p-3 overflow-y-auto">
                    @foreach($expiringSoon as $i => $doc)
                        @if ($canDocs)
                        <a href="{{ route('doc.show', $doc['id']) }}" class="block rounded-lg bg-gray-50 dark:bg-zinc-800/50 p-3 hover:bg-gray-100 dark:hover:bg-zinc-800 transition {{ $i >= 5 ? 'hidden lg:block' : '' }}">
                        @else
                        <div class="block rounded-lg bg-gray-50 dark:bg-zinc-800/50 p-3 {{ $i >= 5 ? 'hidden lg:block' : '' }}">
                        @endif
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-900 dark:text-zinc-100 truncate">{{ $doc['nama_dokumen'] }}</p>
                                    <p class="text-[10px] text-gray-500 dark:text-zinc-400 mt-0.5">{{ $doc['jenis_label'] }} • {{ $doc['tanggal_expired'] }}</p>
                                </div>
                                @if($doc['days_left'] <= 7)
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-1.5 py-px text-[10px] font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-300">{{ $doc['days_left'] }}h</span>
                                @elseif($doc['days_left'] <= 30)
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-1.5 py-px text-[10px] font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">{{ $doc['days_left'] }}h</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-1.5 py-px text-[10px] font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">{{ $doc['days_left'] }}h</span>
                                @endif
                            </div>
                        @if ($canDocs)
                        </a>
                        @else
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        </div><!-- end grid -->
    </div>

    @if ($canDocs)
        @include('doc.partials.reminder-notice-banner')
    @endif
</x-app-layout>