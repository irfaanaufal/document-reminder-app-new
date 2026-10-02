@php
    $canManageDocumentTypes = auth()->user()?->canManageDocumentTypes() ?? false;
    $canCreateDocumentTypes = $canManageDocumentTypes;
@endphp

@section('header-actions')
<div x-data="{ searchOpen: false }" class="flex items-center gap-2">
    @if ($canCreateDocumentTypes)
        <a href="{{ route('doc_type.create') }}" class="hidden md:inline-flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg bg-black text-lg font-bold text-white hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200 transition-colors" title="Tambah Jenis Dokumen">
            +
        </a>
        <a href="{{ route('doc_type.create') }}" class="md:hidden flex fixed bottom-5 right-5 z-30 h-12 w-12 items-center justify-center rounded-full bg-black text-2xl font-bold leading-none text-white shadow-lg transition-colors hover:bg-gray-800 dark:bg-white dark:text-black dark:hover:bg-gray-200" title="Tambah Jenis Dokumen" aria-label="Tambah Jenis Dokumen">
            +
        </a>
    @endif

    <button @click="searchOpen = !searchOpen" type="button"
        class="p-2 rounded-lg transition-colors sm:!flex shrink-0"
        :class="searchOpen
            ? 'text-indigo-600 dark:text-indigo-400 sm:scale-110'
            : 'text-gray-500 hover:text-gray-900 dark:text-zinc-400 dark:hover:text-zinc-100 hover:bg-gray-100 dark:hover:bg-zinc-800'"
        x-show="!searchOpen">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
        </svg>
    </button>

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
</div>
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-zinc-100 leading-tight">
            {{ __('Jenis Dokumen') }}
        </h2>
    </x-slot>

    <div class="">
        <div class="flex flex-col md:h-[calc(100vh-135px)] md:overflow-hidden md:rounded-xl md:border md:border-zinc-200 md:bg-white md:shadow-sm md:dark:border-zinc-700 md:dark:bg-zinc-900">
            <div class="flex-1 min-h-0 overflow-hidden text-gray-900 dark:text-zinc-100">
                @if ($documentTypes->isNotEmpty())
                    {{-- Mobile Card Layout --}}
                    <div class="block md:hidden p-3 pb-6 space-y-3">
                        @foreach ($documentTypes as $documentType)
                            @php
                                $statusClass = $documentType->status === 'active'
                                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300'
                                    : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300';
                                $searchBlob = strtolower(implode(' ', array_filter([
                                    $documentType->nama_jenis,
                                    str_replace('_', ' ', $documentType->tipe_form ?? 'default'),
                                    $documentType->status,
                                    $documentType->creator?->nama,
                                ])));
                            @endphp
                            <div data-doc-search="{{ e($searchBlob) }}" class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <div class="text-xs font-medium text-gray-400 dark:text-zinc-500">#{{ $loop->iteration }}</div>
                                        <div class="mt-0.5 text-sm font-semibold text-gray-900 dark:text-zinc-100 break-words" title="{{ $documentType->nama_jenis }}">{{ $documentType->nama_jenis }}</div>
                                        <div class="mt-1">
                                            <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-[11px] font-medium text-blue-600 dark:bg-blue-900/20 dark:text-blue-300 capitalize">
                                                {{ str_replace('_', ' ', $documentType->tipe_form ?? 'default') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="shrink-0">
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-medium {{ $statusClass }}">
                                            {{ ucfirst($documentType->status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-2 text-sm text-gray-600 dark:text-zinc-400">
                                    <span class="text-gray-400 dark:text-zinc-500">Dibuat oleh</span>
                                    <span class="ml-1 text-gray-900 dark:text-zinc-100">{{ $documentType->creator?->nama ?? '-' }}</span>
                                </div>

                                @if ($canManageDocumentTypes)
                                    <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-zinc-100 pt-3 dark:border-zinc-800">
                                        <a href="{{ route('doc_type.edit', $documentType) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-zinc-200 bg-white px-3 py-1.5 text-xs font-medium text-blue-600 transition-colors hover:bg-blue-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-blue-400 dark:hover:bg-blue-900/20" title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                            Edit
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Desktop Table Layout --}}
                    <div class="hidden md:block h-full overflow-hidden">
                    <table data-datatable class="w-full table-auto text-sm">
                        <thead class="bg-gray-50 dark:bg-zinc-800/50 border-b border-gray-200 dark:border-zinc-700">
                            <tr>
                                <th class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">No</th>
                                <th class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Nama Jenis</th>
                                <th class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Tipe Form</th>
                                <th class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Status</th>
                                <th class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Dibuat Oleh</th>
                                @if ($canManageDocumentTypes)
                                    <th class="text-left text-xs font-extrabold uppercase tracking-wider text-gray-500 dark:text-zinc-400">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-zinc-800">
                            @foreach ($documentTypes as $documentType)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-900/40 transition-colors">
                                    <td class="whitespace-nowrap text-sm text-gray-900 dark:text-zinc-100">{{ $loop->iteration }}</td>
                                    <td class="text-sm text-gray-900 dark:text-zinc-100 font-medium">{{ $documentType->nama_jenis }}</td>
                                    <td class="text-sm">
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-0.5 text-[11px] font-medium text-blue-600 dark:bg-blue-900/20 dark:text-blue-300 capitalize">
                                            {{ str_replace('_', ' ', $documentType->tipe_form ?? 'default') }}
                                        </span>
                                    </td>
                                    <td class="text-sm">
                                        @php
                                            $statusClass = $documentType->status === 'active'
                                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300'
                                                : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300';
                                        @endphp
                                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[11px] font-medium {{ $statusClass }}">
                                            {{ ucfirst($documentType->status) }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-gray-900 dark:text-zinc-100">
                                        {{ $documentType->creator?->nama ?? '-' }}
                                    </td>
                                    @if ($canManageDocumentTypes)
                                        <td class="min-w-[80px]">
                                            <div class="flex items-center gap-1 whitespace-nowrap justify-start">
                                                <a href="{{ route('doc_type.edit', $documentType) }}" class="inline-flex items-center justify-center p-1.5 text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 rounded-md hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                @else
                    <div class="flex items-center justify-center text-gray-500 dark:text-zinc-400 py-8">
                        <p class="text-sm">Belum ada jenis dokumen. Klik tombol di atas untuk menambahkan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
