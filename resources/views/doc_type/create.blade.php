<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-zinc-100">
            Tambah Jenis Dokumen
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                @if ($errors->any())
                    <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-900 dark:bg-red-900/30 dark:text-red-300">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('doc_type.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="nama_jenis" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Nama Jenis</label>
                        <input id="nama_jenis" name="nama_jenis" type="text" value="{{ old('nama_jenis') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" required>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100" required>
                            <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                            <option value="deactive" @selected(old('status') === 'deactive')>Deactive</option>
                        </select>
                    </div>

                    <div>
                        <label for="tipe_form" class="block text-sm font-medium text-gray-700 dark:text-zinc-300">Tipe Form Input</label>
                        <select id="tipe_form" name="tipe_form" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100">
                            <option value="">Pilih Tipe Form (Default)</option>
                            <option value="default" @selected(old('tipe_form') === 'default')>Default</option>
                            <option value="sertifikat" @selected(old('tipe_form') === 'sertifikat')>Sertifikat</option>
                            <option value="wajib_lapor_tahunan" @selected(old('tipe_form') === 'wajib_lapor_tahunan')>Wajib Lapor Tahunan</option>
                            <option value="slo" @selected(old('tipe_form') === 'slo')>SLO</option>
                            <option value="legalitas" @selected(old('tipe_form') === 'legalitas')>Legalitas</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-3 border-t border-gray-200 pt-4 dark:border-zinc-800">
                        <button type="submit" class="inline-flex items-center rounded-md bg-green-600 px-6 py-2 text-sm font-semibold text-white hover:bg-green-700 transition-colors">Simpan</button>
                        <a href="{{ route('doc_type.index') }}" class="inline-flex items-center rounded-md bg-red-600 px-6 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>