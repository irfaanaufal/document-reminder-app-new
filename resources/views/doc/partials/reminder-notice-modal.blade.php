@if (! empty($remindersToNotify) && $remindersToNotify->isNotEmpty())
    <x-modal name="reminder-notice" :show="true" maxWidth="md">
        <div class="relative rounded-2xl bg-white dark:bg-zinc-900 overflow-hidden shadow-2xl border border-zinc-200 dark:border-zinc-800 px-6 pt-8 pb-5">

            <!-- Close button -->
            <button x-on:click="show = false" type="button" class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-500 dark:text-zinc-500 dark:hover:text-zinc-400 focus:outline-none transition-colors duration-200" title="Tutup">
                <span class="sr-only">Tutup</span>
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Header Content -->
            <div class="flex flex-col items-center text-center">
                <!-- Glowing bell icon container -->
                <div class="relative flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-tr from-red-500/10 via-amber-500/10 to-emerald-500/10 p-4 border border-zinc-200/50 dark:border-zinc-700/50">
                    <div class="absolute inset-0 rounded-full animate-pulse bg-red-500/10 opacity-75"></div>
                    <div class="relative flex h-14 w-14 items-center justify-center rounded-full bg-white dark:bg-zinc-800 shadow-md border border-zinc-100 dark:border-zinc-700">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-7 w-7 text-red-500 dark:text-red-400 animate-bounce">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </div>
                </div>

                <h2 class="mt-4 text-xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-100">Reminder Dokumen</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Ada beberapa dokumen yang mendekati masa expired.</p>
            </div>

            <!-- List Content -->
            <div class="mt-5 px-1 max-h-[300px] overflow-y-auto space-y-3">
                @php
                    $nearestReminders = $remindersToNotify->sortBy('tanggal_expired')->take(3);
                @endphp
                
                @foreach ($nearestReminders as $reminder)
                    @php
                        $expired = ($reminder->tanggal_expired instanceof \Illuminate\Support\Carbon)
                            ? $reminder->tanggal_expired
                            : \Illuminate\Support\Carbon::parse($reminder->tanggal_expired);
                        $today = now();
                        $isToday = $expired->isSameDay($today);
                        $isExpired = $expired->lt($today->copy()->startOfDay()) && ! $isToday;
                        $reminderMonths = (int) $reminder->reminder_bulan;
                        $daysLeft = $today->copy()->startOfDay()->diffInDays($expired->copy()->startOfDay(), false);

                        if ($daysLeft < 0) {
                            $status = 'expired';
                        } else {
                            if ($reminderMonths === 1) {
                                if ($daysLeft > 30) {
                                    $status = 'neutral';
                                } elseif ($daysLeft >= 15) {
                                    $status = 'green';
                                } elseif ($daysLeft >= 7) {
                                    $status = 'yellow';
                                } else {
                                    $status = 'red';
                                }
                            } elseif ($reminderMonths === 3) {
                                if ($daysLeft > 90) {
                                    $status = 'neutral';
                                } elseif ($daysLeft >= 45) {
                                    $status = 'green';
                                } elseif ($daysLeft >= 18) {
                                    $status = 'yellow';
                                } else {
                                    $status = 'red';
                                }
                            } elseif ($reminderMonths === 6) {
                                if ($daysLeft > 180) {
                                    $status = 'neutral';
                                } elseif ($daysLeft >= 90) {
                                    $status = 'green';
                                } elseif ($daysLeft >= 36) {
                                    $status = 'yellow';
                                } else {
                                    $status = 'red';
                                }
                            } else {
                                $totalDays = max(30, $reminderMonths * 30);
                                $greenThreshold = (int) round($totalDays * 0.5);
                                $yellowThreshold = (int) round($totalDays * 0.25);
                                if ($daysLeft > $totalDays) {
                                    $status = 'neutral';
                                } elseif ($daysLeft >= $greenThreshold) {
                                    $status = 'green';
                                } elseif ($daysLeft >= $yellowThreshold) {
                                    $status = 'yellow';
                                } else {
                                    $status = 'red';
                                }
                            }
                        }

                        if ($status === 'red' || $status === 'expired') {
                            $cardBgClass = 'bg-red-50/20 dark:bg-red-950/10 hover:bg-red-50/40 dark:hover:bg-red-950/20';
                            $borderLeftClass = 'border-l-4 border-l-red-500';
                            $badgeClass = 'inline-flex items-center rounded-full bg-red-100 dark:bg-red-900/30 px-2.5 py-0.5 text-[11px] font-bold text-red-800 dark:text-red-300';
                        } elseif ($status === 'yellow') {
                            $cardBgClass = 'bg-amber-50/20 dark:bg-amber-950/10 hover:bg-amber-50/40 dark:hover:bg-amber-950/20';
                            $borderLeftClass = 'border-l-4 border-l-amber-500';
                            $badgeClass = 'inline-flex items-center rounded-full bg-amber-100 dark:bg-amber-900/30 px-2.5 py-0.5 text-[11px] font-bold text-amber-800 dark:text-amber-300';
                        } elseif ($status === 'green') {
                            $cardBgClass = 'bg-emerald-50/20 dark:bg-emerald-950/10 hover:bg-emerald-50/40 dark:hover:bg-emerald-950/20';
                            $borderLeftClass = 'border-l-4 border-l-emerald-500';
                            $badgeClass = 'inline-flex items-center rounded-full bg-emerald-100 dark:bg-emerald-900/30 px-2.5 py-0.5 text-[11px] font-bold text-emerald-800 dark:text-emerald-300';
                        } else {
                            $cardBgClass = 'bg-zinc-50/50 dark:bg-zinc-800/30 hover:bg-zinc-100/50 dark:hover:bg-zinc-800/50';
                            $borderLeftClass = 'border-l-4 border-l-zinc-400';
                            $badgeClass = 'inline-flex items-center rounded-full bg-zinc-100 dark:bg-zinc-700/50 px-2.5 py-0.5 text-[11px] font-medium text-zinc-600 dark:text-zinc-400';
                        }

                        $badgeText = $isToday
                            ? 'Hari Ini'
                            : ($daysLeft > 0 ? ($daysLeft . ' hari lagi') : 'Kadaluarsa');
                    @endphp

                    <div class="relative flex items-center justify-between p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 transition-all duration-200 {{ $cardBgClass }} {{ $borderLeftClass }} shadow-sm hover:shadow-md">
                        <div class="flex-1 min-w-0 pr-4">
                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-zinc-100 truncate" title="{{ $reminder->nama_dokumen }}">
                                {{ $reminder->nama_dokumen }}
                            </h4>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 truncate" title="{{ $reminder->penerbit_tujuan }}">
                                {{ $reminder->penerbit_tujuan }}
                            </p>
                        </div>
                        <div class="flex flex-col items-end flex-shrink-0">
                            @if ($status !== 'neutral')
                                <span class="{{ $badgeClass }}">{{ $badgeText }}</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-zinc-100 dark:bg-zinc-700/50 px-2.5 py-0.5 text-[11px] font-medium text-zinc-600 dark:text-zinc-400">{{ $badgeText }}</span>
                            @endif
                            <span class="text-[10px] font-medium text-zinc-400 dark:text-zinc-500 mt-1.5">
                                {{ $expired->format('d-m-Y') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Footer Buttons -->
            <div class="mt-6 flex items-center justify-end gap-3 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                <button x-on:click="show = false" type="button" class="inline-flex items-center justify-center rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-4 py-2 text-sm font-semibold text-zinc-700 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-zinc-500/20">
                    Tutup
                </button>
                <a href="{{ route('dokumen', ['jenis' => 'semua']) }}" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 dark:bg-emerald-500 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700 dark:hover:bg-emerald-600 shadow-sm shadow-emerald-500/10 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    Lihat Semua
                </a>
            </div>

        </div>
    </x-modal>
@endif