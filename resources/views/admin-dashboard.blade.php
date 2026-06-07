<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-zinc-100">
            Dashboard
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="mb-6">

            <!-- KPI Cards -->
            <div class="flex w-full flex-col gap-4 overflow-x-auto pb-1 sm:flex-row sm:flex-nowrap">

                <!-- Card 1 -->
                <a href="{{ route('dokumen', ['jenis' => 'semua']) }}" title="Manajemen Dokumen" class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-6 shadow-sm flex items-start gap-4 sm:min-w-[260px] sm:flex-1 hover:shadow-md transition-shadow">
                    <div class="rounded-lg border border-indigo-200 bg-indigo-50 p-3 text-indigo-600 dark:border-indigo-800 dark:bg-indigo-950 dark:text-indigo-100">
                        <!-- Documents icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 dark:stroke-indigo-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <path d="M14 2v6h6"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold leading-tight text-gray-700 dark:text-zinc-200">Total Dokumen</h3>
                        <div class="mt-2 text-3xl font-bold leading-none text-gray-900 dark:text-zinc-100">{{ $totalDocuments ?? 0 }}</div>
                        <div class="mt-2 text-xs leading-tight text-gray-500 dark:text-zinc-400">Dokumen yang tersimpan</div>
                    </div>
                </a>

                <!-- Card 2 -->
                <a href="{{ route('dokumen', ['jenis' => 'sertifikat']) }}" title="Dokumen Sertifikat" class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-6 shadow-sm flex items-start gap-4 sm:min-w-[260px] sm:flex-1 hover:shadow-md transition-shadow">
                    <div class="p-3 rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-300">
                        <!-- Certificate icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2l2.09 4.26L18 7l-3 2.09L15.18 13 12 11.27 8.82 13 9 9.09 6 7l3.91-.74L12 2z"></path>
                            <path d="M21 15v4a1 1 0 0 1-1 1h-6v-5"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold leading-tight text-gray-700 dark:text-zinc-200">Total Sertifikat</h3>
                        <div class="mt-2 text-3xl font-bold leading-none text-gray-900 dark:text-zinc-100">{{ $totalSertifikat ?? 0 }}</div>
                        <div class="mt-2 text-xs leading-tight text-gray-500 dark:text-zinc-400">Dokumen bertipe Sertifikat</div>
                    </div>
                </a>

                <!-- Card 3 -->
                <a href="{{ route('dokumen', ['jenis' => 'spt']) }}" title="Wajib Lapor" class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-6 shadow-sm flex items-start gap-4 sm:min-w-[260px] sm:flex-1 hover:shadow-md transition-shadow">
                    <div class="p-3 rounded-lg bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-300">
                        <!-- Report icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15V6a2 2 0 0 0-2-2H7L3 6v11a2 2 0 0 0 2 2h12"></path>
                            <path d="M7 10h8M7 14h5"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-semibold leading-tight text-gray-700 dark:text-zinc-200">Total Wajib Lapor</h3>
                        <div class="mt-2 text-3xl font-bold leading-none text-gray-900 dark:text-zinc-100">{{ $totalWajibLapor ?? 0 }}</div>
                        <div class="mt-2 text-xs leading-tight text-gray-500 dark:text-zinc-400">Dokumen Wajib Lapor Tahunan</div>
                    </div>
                </a>

                <!-- Card 4 -->
                <a href="{{ route('dokumen', ['expired' => 1]) }}" title="Dokumen Expired" class="w-full bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-6 shadow-sm flex items-start gap-4 sm:min-w-[260px] sm:flex-1 hover:shadow-md transition-shadow">
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
                        <div class="mt-2 text-3xl font-bold leading-none text-gray-900 dark:text-zinc-100">{{ $totalExpired ?? 0 }}</div>
                        <div class="mt-2 text-xs leading-tight text-gray-500 dark:text-zinc-400">Dokumen yang Sudah Expired</div>
                    </div>
                </a>

            </div>
        </div>
    </div>

        <div
            class="grid grid-cols-1 gap-4"
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

                    if (state === 'green') {
                        return 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300';
                    }

                    return 'bg-gray-100 text-gray-500 dark:bg-zinc-800 dark:text-zinc-300';
                },
            }"
        >
            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 lg:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-zinc-100 lg:text-lg">Kalender Reminder</h3>
                        <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400 lg:text-sm">Klik tanggal untuk melihat dokumen yang expired pada hari tersebut.</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('dashboard', ['month' => $calendarPrev->format('Y-m')]) }}" class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.707 3.707a1 1 0 010 1.414L4.414 9H15a1 1 0 110 2H4.414l3.293 3.293a1 1 0 11-1.414 1.414l-5-5a1 1 0 010-1.414l5-5a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        </a>
                        <div class="rounded-lg bg-emerald-50 px-3 py-1.5 text-sm font-medium text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300">
                            {{ $calendarMonthLabel }}
                        </div>
                        <a href="{{ route('dashboard', ['month' => $calendarNext->format('Y-m')]) }}" class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 hover:bg-gray-100 dark:text-zinc-300 dark:hover:bg-zinc-800">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.293 16.293a1 1 0 010-1.414L15.586 11H5a1 1 0 110-2h10.586l-3.293-3.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        </a>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-7 gap-1 text-center text-[9px] sm:text-[10px] font-semibold uppercase tracking-wide text-gray-500 dark:text-zinc-400 lg:gap-2 lg:text-[11px]">
                    @foreach ($calendarWeekdays as $weekday)
                        <div class="py-1">{{ substr($weekday, 0, 3) }}</div>
                    @endforeach
                </div>

                <div class="mt-2 grid grid-cols-7 gap-1 lg:mt-3 lg:gap-2">
                    @foreach ($calendarDays as $day)
                        <button
                            type="button"
                            @click="selectDay(@js($day))"
                            class="relative flex min-h-[50px] sm:min-h-[60px] w-full flex-col rounded-lg border p-1.5 sm:p-2 text-left transition lg:min-h-[72px] lg:rounded-xl lg:p-3"
                            :class="selectedDate === '{{ $day['date_key'] }}'
                                ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-500/20 dark:border-blue-400 dark:bg-blue-900/20'
                                : '{{ $day['in_month']
                                    ? 'border-gray-200 bg-white hover:border-blue-300 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:border-blue-500'
                                    : 'border-gray-200 bg-gray-50 text-gray-400 dark:border-zinc-800 dark:bg-zinc-950/40 dark:text-zinc-600' }}'"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <span class="text-xs font-semibold lg:text-sm {{ $day['in_month'] ? 'text-gray-900 dark:text-zinc-100' : 'text-gray-400 dark:text-zinc-600' }}">{{ $day['day'] }}</span>
                                @if (! empty($day['documents']))
                                    <span class="inline-flex h-3 w-3 sm:h-3.5 sm:w-3.5 rounded-full @if($day['state'] === 'expired' || $day['state'] === 'red') bg-red-500 @elseif($day['state'] === 'yellow') bg-amber-400 @elseif($day['state'] === 'green') bg-emerald-500 @else bg-transparent @endif ring-1 ring-white/80 dark:ring-zinc-900/60"></span>
                                @endif
                            </div>

                            @if (! empty($day['documents']))
                                <span class="absolute bottom-1 right-1 sm:bottom-2 sm:right-2 inline-flex items-center rounded-full bg-gray-100 px-1.5 py-0.5 text-[9px] sm:text-[10px] font-medium text-gray-700 dark:bg-zinc-800 dark:text-zinc-200 lg:px-2 lg:py-1 lg:text-[11px] whitespace-nowrap">
                                    <span class="block sm:hidden">{{ count($day['documents']) }}</span>
                                    <span class="hidden sm:inline" aria-hidden="true">{{ count($day['documents']) }} dok.</span>
                                </span>
                            @endif
                        </button>
                    @endforeach
                </div>

                <div class="mt-4 rounded-xl border border-zinc-200 bg-zinc-50 p-3 dark:border-zinc-700 dark:bg-zinc-950/40 lg:mt-5 lg:p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-zinc-100" x-text="getSelectedDay() ? getSelectedDay().display_label : 'Pilih tanggal'"></p>
                            <div class="text-[11px] text-gray-500 dark:text-zinc-400 lg:text-xs">
                                <span class="block sm:hidden" x-text="selectedDocuments.length ? `${selectedDocuments.length} dok.` : 'Klik tanggal'"></span>
                                <span class="hidden sm:block" x-text="selectedDocuments.length ? `${selectedDocuments.length} dokumen terkait` : 'Klik tanggal yang memiliki indikator'"></span>
                            </div>
                        </div>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-semibold lg:px-3 lg:text-[11px]" :class="getSelectedDay() ? statusClass(getSelectedDay().state) : 'bg-gray-100 text-gray-500 dark:bg-zinc-800 dark:text-zinc-300'" x-text="getSelectedDay() ? (getSelectedDay().state === 'expired' ? 'Expired' : (getSelectedDay().state === 'red' || getSelectedDay().state === 'yellow' ? 'Mendekati expired' : (getSelectedDay().state === 'green' ? 'Reminder aktif' : 'Tidak ada indikator'))) : 'Belum dipilih'"></span>
                    </div>

                    <div class="mt-3 space-y-2 lg:mt-4 lg:space-y-3">
                        <template x-if="selectedDocuments.length">
                            <template x-for="document in selectedDocuments" :key="document.id">
                                <div class="rounded-lg border border-zinc-200 bg-white p-3 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 lg:p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-zinc-100 max-w-xs sm:max-w-full" x-text="document.name"></p>
                                            <p class="mt-1 text-[11px] text-gray-500 dark:text-zinc-400 lg:text-xs" x-text="document.type"></p>
                                        </div>
                                        <div class="flex flex-col items-end gap-2">
                                            <a :href="`/dokumen/${document.id}`" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-3 py-1 text-xs font-medium text-white hover:bg-blue-700 transition-colors">Detail</a>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[11px] text-gray-500 dark:text-zinc-400 lg:mt-3 lg:gap-3 lg:text-xs">
                                        <span>Expired: <span class="font-medium text-gray-700 dark:text-zinc-200" x-text="document.expired_at"></span></span>
                                        <span x-show="document.days_left >= 0">Sisa hari: <span class="font-medium text-gray-700 dark:text-zinc-200" x-text="document.days_left"></span></span>
                                    </div>
                                </div>
                            </template>
                        </template>

                        <div x-show="! selectedDocuments.length" class="rounded-lg border border-dashed border-zinc-300 bg-white px-4 py-4 text-center text-sm text-gray-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400 lg:py-6">
                            Tidak ada dokumen pada tanggal ini.
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @include('doc.partials.reminder-notice-modal')
</x-app-layout>