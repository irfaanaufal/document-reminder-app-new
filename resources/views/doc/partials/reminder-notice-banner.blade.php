@if (! empty($remindersToNotify) && $remindersToNotify->isNotEmpty())
    @php
        $nearestReminders = $remindersToNotify->sortBy('tanggal_expired')->take(5);
    @endphp

    <x-modal name="reminder-notice" :show="true" maxWidth="md">
        <div class="relative bg-white dark:bg-zinc-900 px-6 pt-6 pb-5">

            {{-- Close button --}}
            <button x-on:click="$dispatch('close-modal', 'reminder-notice')" type="button"
                class="absolute top-4 right-4 text-zinc-400 hover:text-zinc-600 dark:text-zinc-500 dark:hover:text-zinc-300 transition-colors rounded-lg p-1 hover:bg-zinc-100 dark:hover:bg-zinc-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                </svg>
            </button>

            {{-- Header --}}
            <div class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6 text-amber-600 dark:text-amber-400">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 6a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 6zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h2 class="mt-3 text-lg font-bold text-gray-900 dark:text-zinc-100">Dokumen Perlu Perhatian</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">{{ $remindersToNotify->count() }} dokumen mendekati masa expired.</p>
            </div>

            {{-- Document List --}}
            <div class="mt-5 space-y-2">
                @foreach ($nearestReminders as $reminder)
                    @php
                        $expired = ($reminder->tanggal_expired instanceof \Illuminate\Support\Carbon)
                            ? $reminder->tanggal_expired
                            : \Illuminate\Support\Carbon::parse($reminder->tanggal_expired);
                        $today = now();
                        $daysLeft = (int) $today->copy()->startOfDay()->diffInDays($expired->copy()->startOfDay(), false);

                        if ($daysLeft < 0) {
                            $dotClass = 'bg-red-500';
                            $badgeClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
                            $badgeText = 'Kadaluarsa';
                        } elseif ($daysLeft === 0) {
                            $dotClass = 'bg-red-500';
                            $badgeClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
                            $badgeText = 'Hari Ini';
                        } elseif ($daysLeft <= 7) {
                            $dotClass = 'bg-red-500';
                            $badgeClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300';
                            $badgeText = $daysLeft . ' hari lagi';
                        } elseif ($daysLeft <= 14) {
                            $dotClass = 'bg-amber-500';
                            $badgeClass = 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300';
                            $badgeText = $daysLeft . ' hari lagi';
                        } else {
                            $dotClass = 'bg-emerald-500';
                            $badgeClass = 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300';
                            $badgeText = $daysLeft . ' hari lagi';
                        }
                    @endphp

                    <div class="flex items-center justify-between rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-800/50">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="h-2 w-2 flex-shrink-0 rounded-full {{ $dotClass }}"></span>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-zinc-100 truncate">{{ $reminder->nama_dokumen }}</p>
                                <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">{{ $reminder->penerbit_tujuan }}</p>
                            </div>
                        </div>
                        <span class="ml-3 flex-shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-semibold {{ $badgeClass }}">
                            {{ $badgeText }}
                        </span>
                    </div>
                @endforeach
            </div>

            {{-- Footer --}}
            <div class="mt-5 flex items-center justify-end gap-3 border-t border-zinc-100 dark:border-zinc-800 pt-4">
                <button x-on:click="$dispatch('close-modal', 'reminder-notice')" type="button"
                    class="rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                    Tutup
                </button>
                <a href="{{ route('dokumen', ['jenis' => 'semua']) }}"
                    class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 transition-colors">
                    Lihat Semua
                </a>
            </div>

        </div>
    </x-modal>
@endif
