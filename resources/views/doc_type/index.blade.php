<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-zinc-100">
                Jenis Dokumen
            </h2>
        </div>
    </x-slot>

    <div class="py-1">
        @php
            $canManageDocumentTypes = auth()->user()?->isAdmin() ?? false;
            $canCreateDocumentTypes = auth()->check();
        @endphp

        <div class="mb-4">
            <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
                <div class="w-full sm:max-w-xs">
                    <input
                        type="search"
                        data-datatable-search-input
                        placeholder="Search..."
                        class="w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100"
                    >
                </div>
                @if ($canCreateDocumentTypes)
                    <a href="{{ route('doc_type.create') }}" class="inline-flex w-full items-center justify-center rounded-md bg-green-600 px-4 py-2 text-white text-sm font-semibold hover:bg-green-700 transition-colors whitespace-nowrap sm:w-auto">
                        +
                    </a>
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 overflow-hidden shadow-sm" style="border-radius: 0 12px 12px 12px;">
            <div class="p-3 text-gray-900 dark:text-zinc-100">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-zinc-100 mb-4">Data Jenis Dokumen</h3>

                <div class="overflow-x-auto">
                    <table data-datatable class="min-w-[720px] w-full table-fixed divide-y divide-gray-200 dark:divide-zinc-800 text-sm">
                        <thead class="bg-gray-50 dark:bg-zinc-900">
                            <tr>
                                <th class="w-[30%] px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Nama Jenis</th>
                                <th class="w-[20%] px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Status</th>
                                <th class="w-[20%] px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Dibuat Oleh</th>
                                @if ($canManageDocumentTypes)
                                    <th class="w-[30%] px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-zinc-800">
                            @forelse ($documentTypes as $documentType)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900 dark:text-zinc-100">{{ $documentType->nama_jenis }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusClass = $documentType->status === 'active'
                                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300'
                                                : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300';
                                        @endphp
                                        <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                            {{ ucfirst($documentType->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-900 dark:text-zinc-100">
                                        {{ $documentType->creator?->name ?? '-' }}
                                    </td>
                                    @if ($canManageDocumentTypes)
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2 whitespace-nowrap">
                                                <a href="{{ route('doc_type.edit', $documentType) }}" class="inline-flex items-center text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors" title="Edit">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $canManageDocumentTypes ? 4 : 3 }}" class="px-4 py-6 text-center text-gray-500 dark:text-zinc-400">Belum ada jenis dokumen.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
