<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-zinc-100">
            Hak Akses
        </h2>
    </x-slot>

    <div class="py-1">
        @php
            $totalUsers = $users->count();
            $activeUsers = $users->where('is_active', true)->count();
            $inactiveUsers = $users->where('is_active', false)->count();
            $adminUsers = $users->filter(fn ($item) => in_array((int) $item->role, [1, 2], true))->count();
        @endphp

        @if (session('error'))
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-xs font-semibold tracking-wide text-gray-500 dark:text-zinc-400">Total User</p>
                <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-zinc-100">{{ $totalUsers }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-xs font-semibold tracking-wide text-gray-500 dark:text-zinc-400">User Aktif</p>
                <p class="mt-2 text-2xl font-bold text-emerald-600 dark:text-emerald-300">{{ $activeUsers }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-xs font-semibold tracking-wide text-gray-500 dark:text-zinc-400">User Nonaktif</p>
                <p class="mt-2 text-2xl font-bold text-red-600 dark:text-red-300">{{ $inactiveUsers }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-4 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <p class="text-xs font-semibold tracking-wide text-gray-500 dark:text-zinc-400">Super Admin + Admin</p>
                <p class="mt-2 text-2xl font-bold text-blue-600 dark:text-blue-300">{{ $adminUsers }}</p>
            </div>
        </div>

        <div class="overflow-hidden border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900" style="border-radius: 0 12px 12px 12px;">
            <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-800">
                <h3 class="text-base font-semibold text-gray-800 dark:text-zinc-100">Kelola Role dan Status User</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">User baru default menjadi User. Super admin dapat mengubah role dan status aktif dari sini.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[1000px] w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                    <thead class="bg-gray-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">No</th>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Nama</th>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Email</th>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Role</th>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Status</th>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Akses AI</th>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @foreach ($users as $account)
                            @php
                                $formId = 'access-form-'.$account->id;
                            @endphp
                             <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/30">
                                <td class="px-4 py-3 text-gray-900 dark:text-zinc-100 font-medium">
                                    {{ $users->firstItem() + $loop->index }}
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-zinc-100">
                                    <div class="font-medium">{{ $account->nama }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-zinc-300">{{ $account->email }}</td>
                                <td class="px-4 py-3">
                                    <select form="{{ $formId }}" name="role" class="w-full rounded-md border-zinc-300 bg-white text-sm focus:border-green-500 focus:ring-green-500 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-100">
                                        @foreach ($roleOptions as $value => $label)
                                            <option value="{{ $value }}" @selected((int) $account->role === (int) $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-zinc-200">
                                            <input
                                                form="{{ $formId }}"
                                                type="checkbox"
                                                class="rounded border-zinc-300 text-green-600 focus:ring-green-500 dark:border-zinc-700 dark:bg-zinc-800"
                                                @checked($account->is_active)
                                                onchange="this.form.is_active.value = this.checked ? 1 : 0"
                                            >
                                            <span>{{ $account->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                        </label>
                                        <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold {{ $account->is_active ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' : 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300' }}">
                                            {{ $account->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <label class="inline-flex items-center gap-2 text-sm text-gray-700 dark:text-zinc-200">
                                            <!-- Hidden input to send value when checkbox is not checked -->
                                            <input form="{{ $formId }}" type="hidden" name="can_use_chatbot" value="0">
                                            <input
                                                form="{{ $formId }}"
                                                type="checkbox"
                                                name="can_use_chatbot"
                                                value="1"
                                                class="rounded border-zinc-300 text-blue-600 focus:ring-blue-500 dark:border-zinc-700 dark:bg-zinc-800"
                                                @checked($account->can_use_chatbot)
                                            >
                                            <span>{{ $account->can_use_chatbot ? 'Diizinkan' : 'Tidak Diizinkan' }}</span>
                                        </label>
                                        <span class="inline-flex items-center rounded-md px-2.5 py-1 text-xs font-semibold {{ $account->can_use_chatbot ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300' : 'bg-gray-50 text-gray-600 dark:bg-gray-800/50 dark:text-gray-400' }}">
                                            {{ $account->can_use_chatbot ? 'Yes' : 'No' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <form id="{{ $formId }}" method="POST" action="{{ route('access-control.update', $account) }}" class="inline-flex">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $account->is_active ? 1 : 0 }}">
                                        <button type="submit" class="inline-flex items-center rounded-md bg-green-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-green-700">
                                            Simpan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 px-4 py-3 dark:border-zinc-800">
                {{ $users->links() }}
            </div>
        </div>

        <div class="mt-5 overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-800">
                <h3 class="text-base font-semibold text-gray-800 dark:text-zinc-100">Riwayat Perubahan Hak Akses</h3>
                <p class="mt-1 text-xs text-gray-500 dark:text-zinc-400">20 perubahan terakhir role/status user.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[900px] w-full divide-y divide-zinc-200 text-sm dark:divide-zinc-800">
                    <thead class="bg-gray-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">No</th>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Waktu</th>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Diubah Oleh</th>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Target User</th>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Role</th>
                            <th class="px-4 py-3 text-left text-[13px] font-bold tracking-wide text-gray-700 dark:text-zinc-200">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse ($recentLogs as $log)
                            <tr class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/30">
                                <td class="px-4 py-3 text-gray-900 dark:text-zinc-100 font-medium">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-gray-900 dark:text-zinc-100">{{ $log->created_at?->format('d-m-Y H:i:s') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-zinc-300">{{ $log->actor?->nama ?? 'Sistem' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-zinc-300">{{ $log->target?->nama ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-700 dark:text-zinc-300">
                                    {{ $roleOptions[$log->old_role] ?? 'Unknown' }}
                                    <span class="mx-1 text-gray-400">-></span>
                                    {{ $roleOptions[$log->new_role] ?? 'Unknown' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 dark:text-zinc-300">
                                    {{ $log->old_is_active ? 'Aktif' : 'Nonaktif' }}
                                    <span class="mx-1 text-gray-400">-></span>
                                    {{ $log->new_is_active ? 'Aktif' : 'Nonaktif' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-zinc-400">Belum ada riwayat perubahan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
