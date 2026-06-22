<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-zinc-100 leading-tight">
            {{ __('Manajemen Dokumen') }}
        </h2>
    </x-slot>

    <div class="py-1" x-data="{
        columns: {
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
        },
        resetColumns() {
            this.columns = {
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
            };
        },
    }">
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

        <div class="mb-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <!-- Left side: Dropdown Filter Jenis Dokumen & Show Entries -->
                <div class="flex items-center gap-3 w-full lg:w-auto">
                    <div class="flex-1 lg:flex-initial">
                        <select id="filter_jenis" onchange="window.location.href = this.value" class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 font-medium lg:min-w-[200px]">
                            <option value="{{ route('dokumen', ['jenis' => 'semua']) }}" @selected($activeJenis === 'semua' || empty($activeJenis))>Semua Jenis Dokumen</option>
                            @foreach ($documentTypes as $docType)
                                <option value="{{ route('dokumen', ['jenis' => $docType->id]) }}" @selected($isDocTypeSelected($docType))>
                                    {{ $docType->nama_jenis }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <select
                        data-datatable-perpage
                        class="flex-shrink-0 rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                    >
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>

                <!-- Right side: Search, Filter, Create -->
                <div class="flex items-center gap-3 w-full lg:w-auto lg:ml-auto">
                    <div class="flex-1 lg:flex-initial lg:w-64">
                        <input
                            type="search"
                            data-datatable-search-input
                            placeholder="Search..."
                            class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                        >
                    </div>

                    <div class="relative flex-shrink-0" x-data="{ open: false }">
                        <button
                            type="button"
                            @click="open = !open"
                            aria-label="Filter Kolom"
                            title="Filter Kolom"
                            class="inline-flex h-[38px] w-[38px] items-center justify-center rounded-md bg-blue-700 text-white hover:bg-blue-800 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3.25 4.5A1.25 1.25 0 014.5 3.25h11A1.25 1.25 0 0116.75 4.5v1.039c0 .332-.132.65-.366.884L12.75 10.107v5.143a.75.75 0 01-.27.578l-2 1.75a.75.75 0 01-1.23-.578v-6.893L3.616 6.423A1.25 1.25 0 013.25 5.54V4.5z" />
                            </svg>
                        </button>

                        <div
                            x-cloak
                            x-show="open"
                            @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-[-50px] lg:right-0 z-50 mt-2 w-80 max-w-[calc(100vw-2rem)] overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-black/40"
                        >
                            <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-900">
                                <p class="text-sm font-semibold text-gray-800 dark:text-zinc-100">Filter Kolom</p>
                                <p class="text-xs text-gray-500 dark:text-zinc-400">Klik untuk tampil atau sembunyikan kolom.</p>
                            </div>

                            <div class="space-y-2 p-3">
                                @foreach ($filterableColumns as $key => $label)
                                    <button
                                        type="button"
                                        @click="columns.{{ $key }} = !columns.{{ $key }}"
                                        class="flex w-full items-center justify-between rounded-md border px-3 py-2 text-sm transition-colors"
                                        :class="columns.{{ $key }} ? 'border-green-500 bg-green-50 text-green-700 dark:border-green-500 dark:bg-green-900/25 dark:text-green-300' : 'border-zinc-300 bg-white text-gray-700 hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 dark:hover:bg-zinc-700'"
                                    >
                                        <span class="whitespace-nowrap">{{ $label }}</span>
                                        <svg x-show="columns.{{ $key }}" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-none text-green-600 dark:text-green-300" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.415l-7.25 7.25a1 1 0 01-1.415 0l-3.25-3.25a1 1 0 111.415-1.415l2.542 2.543 6.543-6.543a1 1 0 011.415 0z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                @endforeach
                            </div>

                            <div class="flex items-center justify-between gap-3 border-t border-zinc-200 px-4 py-3 text-xs dark:border-zinc-700 dark:bg-zinc-900">
                                <button type="button" @click="resetColumns()" class="font-medium text-green-600 transition-colors hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                                    Reset semua
                                </button>
                                <button type="button" @click="open = false" class="font-medium text-gray-500 transition-colors hover:text-gray-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('doc.create') }}" class="inline-flex h-[38px] w-[38px] flex-shrink-0 items-center justify-center rounded-md bg-green-600 text-white font-semibold hover:bg-green-700 transition-colors" title="Tambah Dokumen">
                        +
                    </a>
                </div>
            </div>

            {{-- Panel Konten --}}
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm rounded-xl mt-4">
                <div class="p-3 text-gray-900 dark:text-zinc-100">
                    @if ($reminders->isNotEmpty())
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-zinc-100 mb-4">Data Tersimpan</h3>
                        <div class="overflow-x-auto">
                            @php
                                $hasJenis = $activeJenis === 'semua';
                                $wNomor = 'w-[5%]';
                                $wDokumen = $hasJenis ? 'w-[17%]' : 'w-[23%]';
                                $wNo = 'w-[10%]';
                                $wJenis = 'w-[12%]';
                                $wPIC = $hasJenis ? 'w-[13%]' : 'w-[17%]';
                                $wPenerbit = $hasJenis ? 'w-[15%]' : 'w-[20%]';
                                $wTerbit = 'w-[9%]';
                                $wExpired = 'w-[10%]';
                                $wRem = 'w-[8%]';
                                $wAksi = 'w-[7%]';
                            @endphp
                            <table data-datatable class="min-w-[1080px] w-full table-fixed divide-y divide-gray-200 dark:divide-zinc-800 text-sm">
                                <thead class="bg-gray-50 dark:bg-zinc-900">
                                    <tr>
                                        <th x-cloak x-show="columns.nomor" class="{{ $wNomor }} px-4 py-3 text-left text-sm font-bold tracking-wide text-gray-700 dark:text-zinc-200">No</th>
                                        <th x-cloak x-show="columns.nama_dokumen" class="{{ $wDokumen }} px-4 py-3 text-left text-sm font-bold tracking-wide text-gray-700 dark:text-zinc-200">Dokumen</th>
                                        <th x-cloak x-show="columns.no_dokumen" class="{{ $wNo }} px-4 py-3 text-left text-sm font-bold tracking-wide text-gray-700 dark:text-zinc-200">No Dok.</th>
                                        @if($hasJenis)
                                            <th x-cloak x-show="columns.jenis_dokumen" class="{{ $wJenis }} px-4 py-3 text-left text-sm font-bold tracking-wide text-gray-700 dark:text-zinc-200">Jenis Dok</th>
                                        @endif
                                        <th x-cloak x-show="columns.pic" class="{{ $wPIC }} px-4 py-3 text-left text-sm font-bold tracking-wide text-gray-700 dark:text-zinc-200">PIC</th>
                                        <th x-cloak x-show="columns.penerbit" class="{{ $wPenerbit }} px-4 py-3 text-left text-sm font-bold tracking-wide text-gray-700 dark:text-zinc-200">Penerbit</th>
                                        <th x-cloak x-show="columns.terbit" class="{{ $wTerbit }} px-4 py-3 text-left text-sm font-bold tracking-wide text-gray-700 dark:text-zinc-200">Terbit</th>
                                        <th x-cloak x-show="columns.expired" class="{{ $wExpired }} px-4 py-3 text-left text-sm font-bold tracking-wide text-gray-700 dark:text-zinc-200">Expired</th>
                                        <th x-cloak x-show="columns.sisa_hari" class="{{ $wRem }} px-4 py-3 text-left text-sm font-bold tracking-wide text-gray-700 dark:text-zinc-200">Sisa Hari</th>
                                        <th x-cloak x-show="columns.aksi" class="{{ $wAksi }} px-4 py-3 text-left text-sm font-bold tracking-wide text-gray-700 dark:text-zinc-200">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-zinc-800">
                                    @php
                                        $today = now();
                                    @endphp
                                    @foreach ($reminders as $reminder)
                                        @php
                                            $isToday = $reminder->tanggal_expired ? $reminder->tanggal_expired->isSameDay($today) : false;
                                            $isExpired = $reminder->tanggal_expired ? ($reminder->tanggal_expired->lt($today->copy()->startOfDay()) && ! $isToday) : false;
                                            $reminderMonths = (int) $reminder->reminder_bulan;
                                            $daysLeft = $reminder->tanggal_expired ? $today->copy()->startOfDay()->diffInDays($reminder->tanggal_expired->copy()->startOfDay(), false) : null;

                                            if (is_null($daysLeft)) {
                                                $status = 'lifetime';
                                            } elseif ($daysLeft < 0) {
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

                                            if ($status === 'lifetime') {
                                                $badgeClass = 'inline-flex min-w-20 justify-center rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300';
                                            } elseif ($status === 'expired') {
                                                $badgeClass = 'inline-flex min-w-20 justify-center rounded-md bg-gray-400 px-2.5 py-1 text-xs font-semibold text-gray-900 dark:bg-zinc-800 dark:text-zinc-100';
                                            } elseif ($status === 'red') {
                                                $badgeClass = 'inline-flex min-w-20 justify-center rounded-md bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/20 dark:text-red-300';
                                            } elseif ($status === 'yellow') {
                                                $badgeClass = 'inline-flex min-w-20 justify-center rounded-md bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/20 dark:text-amber-300';
                                            } elseif ($status === 'green') {
                                                $badgeClass = 'inline-flex min-w-20 justify-center rounded-md bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300';
                                            } else {
                                                $badgeClass = 'inline-flex min-w-20 justify-center rounded-md px-2.5 py-1 text-xs font-medium text-gray-500 dark:text-zinc-400';
                                            }

                                            $showBadge = $status !== 'neutral';
                                            $badgeText = $status === 'lifetime'
                                                ? 'Seumur Hidup'
                                                : ($isToday ? 'Hari ini' : ($daysLeft > 0 ? ($daysLeft . ' hari lagi') : 'Kadaluarsa'));
                                        @endphp
                                        <tr>
                                            <td x-cloak x-show="columns.nomor" class="px-4 py-3 align-top whitespace-nowrap text-gray-900 dark:text-zinc-100">{{ $loop->iteration }}</td>
                                            <td x-cloak x-show="columns.nama_dokumen" class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100">
                                                <span class="block overflow-hidden text-ellipsis break-words leading-tight" title="{{ $reminder->nama_dokumen }}" style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; max-height: 2.5rem;">
                                                    {{ $reminder->nama_dokumen }}
                                                </span>
                                            </td>
                                            <td x-cloak x-show="columns.no_dokumen" class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100">
                                                <span class="block overflow-hidden text-ellipsis break-words leading-tight" title="{{ $reminder->no_dokumen }}" style="display: -webkit-box; -webkit-box-orient: vertical; -webkit-line-clamp: 2; max-height: 2.5rem;">
                                                    {{ $reminder->no_dokumen }}
                                                </span>
                                            </td>
                                            @if($activeJenis === 'semua')
                                                <td x-cloak x-show="columns.jenis_dokumen" class="px-4 py-3 align-top whitespace-normal text-gray-900 dark:text-zinc-100">
                                                    @php
                                                        $rawJenis = (string) $reminder->jenis_dokumen;
                                                        $displayJenis = $reminder->jenis_dokumen_label;
                                                    @endphp
                                                    <span title="{{ $rawJenis }}" class="inline-flex max-w-full items-center justify-center rounded-md bg-gray-100 px-2.5 py-1 text-[11px] font-medium leading-tight text-center text-gray-700 dark:bg-zinc-800 dark:text-zinc-200">
                                                        {{ $displayJenis }}
                                                    </span>
                                                </td>
                                            @endif
                                            <td x-cloak x-show="columns.pic" class="px-4 py-3 align-top break-words">
                                                @if($reminder->internalPics->isNotEmpty())
                                                    <div class="text-sm leading-tight text-gray-900 dark:text-zinc-100">
                                                        {{ $reminder->internalPics->first()->pivot->nama ?? $reminder->internalPics->first()->nama }}
                                                        @if($reminder->internalPics->count() > 1)
                                                            <span class="text-[10px] text-indigo-600 dark:text-indigo-400 font-bold">(+{{ $reminder->internalPics->count() - 1 }})</span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs leading-tight text-gray-500 dark:text-zinc-400">
                                                        {{ $reminder->internalPics->first()->pivot->no_telpon ?? $reminder->internalPics->first()->no_telpon ?: '-' }}
                                                    </div>
                                                @else
                                                    <div class="text-sm leading-tight text-gray-900 dark:text-zinc-100">{{ $reminder->pic_nama ?: '-' }}</div>
                                                    <div class="text-xs leading-tight text-gray-500 dark:text-zinc-400">{{ $reminder->pic_telpon ?: '-' }}</div>
                                                @endif
                                            </td>
                                            <td x-cloak x-show="columns.penerbit" class="px-4 py-3 align-top text-gray-900 dark:text-zinc-100">
                                                <span class="block whitespace-normal break-words leading-tight" title="{{ $reminder->penerbit_tujuan }}">{{ $reminder->penerbit_tujuan }}</span>
                                            </td>
                                            <td x-cloak x-show="columns.terbit" class="px-4 py-3 align-top whitespace-nowrap text-gray-900 dark:text-zinc-100">{{ $reminder->tanggal_terbit->format('d-m-Y') }}</td>
                                            <td x-cloak x-show="columns.expired" class="px-4 py-3 align-top">
                                                <div class="inline-flex items-center rounded-md px-2.5 py-1 whitespace-nowrap text-sm text-gray-900 dark:text-zinc-100">{{ $reminder->tanggal_expired ? $reminder->tanggal_expired->format('d-m-Y') : 'Seumur Hidup' }}</div>
                                            </td>
                                            <td x-cloak x-show="columns.sisa_hari" class="px-4 py-3 align-top whitespace-nowrap text-gray-900 dark:text-zinc-100">
                                                @if ($showBadge)
                                                    <span class="{{ $badgeClass }}">{{ $badgeText }}</span>
                                                @else
                                                    <span class="inline-flex min-w-20 justify-center px-2.5 py-1 text-xs font-medium text-gray-500 dark:text-zinc-400">{{ $badgeText }}</span>
                                                @endif
                                            </td>
                                            <td x-cloak x-show="columns.aksi" class="px-4 py-3 align-top">
                                                <div class="flex items-center gap-2 whitespace-nowrap">
                                                    @can('update', $reminder)
                                                        <a href="{{ route('doc.edit', $reminder->id) }}" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors" title="Edit{{ $isExpired ? ' / Perpanjang' : '' }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $reminder)
                                                        <form method="POST" action="{{ route('doc.destroy', $reminder->id) }}" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('Yakin ingin menghapus?')" class="text-red-400 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 transition-colors" title="Hapus">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    <a href="{{ route('doc.show', $reminder->id) }}" class="text-emerald-500 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors" title="Lihat Detail">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M10 3.75c-3.69 0-6.86 2.29-8.28 5.54a1 1 0 000 .92C3.14 13.46 6.31 15.75 10 15.75s6.86-2.29 8.28-5.54a1 1 0 000-.92C16.86 6.04 13.69 3.75 10 3.75zm0 8.5A2.25 2.25 0 1 1 10 7.5a2.25 2.25 0 0 1 0 4.75z" /></svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        

                    @else
                        <div class="text-center text-gray-500 dark:text-zinc-400 py-8">
                            <p>Belum ada data dokumen. Klik tombol di atas untuk menambahkan dokumen baru.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

