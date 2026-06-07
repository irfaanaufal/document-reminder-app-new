@if (! empty($remindersToNotify) && $remindersToNotify->isNotEmpty())
    <x-modal name="reminder-notice" :show="true" maxWidth="md">
        <div class="relative rounded-2xl bg-white px-6 pt-6 pb-4 shadow-lg border-2 border-zinc-200 dark:bg-zinc-900 dark:border-zinc-700">

            <div class="flex flex-col items-center text-center">
                <div class="flex h-24 w-24 items-center justify-center rounded-full border-8 border-red-600 bg-red-50 text-red-600 shadow-md dark:bg-red-900/30 dark:text-red-300">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="h-14 w-14 fill-current">
                        <path d="M32 6a26 26 0 1 0 26 26A26.03 26.03 0 0 0 32 6Zm0 40a4 4 0 1 1 4-4 4 4 0 0 1-4 4Zm4-10h-8L26 18h12Z" />
                    </svg>
                </div>

                <h2 class="mt-4 text-2xl font-bold text-zinc-900 dark:text-zinc-100">Reminder Dokumen</h2>
            </div>

            <div class="mt-4 border-t border-zinc-200 dark:border-zinc-700"></div>

            <div class="mt-3 px-1">
                @php
                    $nearestReminders = $remindersToNotify->sortBy('tanggal_expired')->take(3);
                @endphp
                <div class="">
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
                                    $badgeClass = 'inline-flex rounded-md bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/20 dark:text-red-300';
                                } elseif ($status === 'yellow') {
                                    $badgeClass = 'inline-flex rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/20 dark:text-amber-300';
                                } elseif ($status === 'green') {
                                    $badgeClass = 'inline-flex rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300';
                                } else {
                                    $badgeClass = 'inline-flex rounded-md px-2.5 py-1 text-xs font-medium text-gray-500 dark:text-zinc-400';
                                }

                                $badgeText = $isToday
                                    ? 'Hari Ini'
                                    : ($daysLeft > 0 ? ($daysLeft . ' hari lagi') : 'Kadaluarsa');
                        @endphp
                        <div class="py-3 border-b border-zinc-200 last:border-b-0 dark:border-zinc-700 flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <p class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">{{ $reminder->nama_dokumen }}</p>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $reminder->penerbit_tujuan }}</p>
                            </div>

                            <div class="flex-shrink-0 text-right">
                                    @if ($status !== 'neutral')
                                        <span class="{{ $badgeClass }}">{{ $badgeText }}</span>
                                    @else
                                        <span class="inline-flex rounded-md px-2.5 py-1 text-xs font-medium text-gray-500 dark:text-zinc-400">{{ $badgeText }}</span>
                                    @endif
                                    <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $expired->format('d-m-Y') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 flex justify-end px-2">
                <a href="{{ route('dokumen', ['jenis' => 'semua']) }}" class="inline-flex items-center rounded-md border border-zinc-300 bg-white px-3 py-1 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800">Lihat Semua</a>
            </div>

        </div>
    </x-modal>
@endif