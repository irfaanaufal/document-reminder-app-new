@props(['reminders' => collect(), 'submitRoute'])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
    <div class="p-6 text-gray-900 space-y-6">
        @if (session('success'))
            <div class="rounded-md bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-md bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $submitRoute }}" enctype="multipart/form-data" class="grid gap-5">
            @csrf

            <!-- Row 1: Nama Dokumen -->
            <div>
                <label for="nama_dokumen" class="block text-sm font-medium text-gray-700">Nama Dokumen</label>
                <input id="nama_dokumen" name="nama_dokumen" type="text" value="{{ old('nama_dokumen') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>

            <!-- Row 2: Penerbit -->
            <div>
                <label for="penerbit_tujuan" class="block text-sm font-medium text-gray-700">Penerbit / Dituju</label>
                <input id="penerbit_tujuan" name="penerbit_tujuan" type="text" value="{{ old('penerbit_tujuan') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>

            <!-- Row 3: Tanggal Terbit & Expired Bersebelahan -->
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="tanggal_terbit" class="block text-sm font-medium text-gray-700">Tanggal Terbit</label>
                    <input id="tanggal_terbit" name="tanggal_terbit" type="date" value="{{ old('tanggal_terbit') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>

                <div>
                    <label for="tanggal_expired" class="block text-sm font-medium text-gray-700">Tanggal Expired</label>
                    <input id="tanggal_expired" name="tanggal_expired" type="date" value="{{ old('tanggal_expired') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                </div>
            </div>

            <!-- Row 4: PIC Internal -->
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="pic_nama" class="block text-sm font-medium text-gray-700">Nama PIC Internal <span class="text-xs text-gray-500">(opsional)</span></label>
                    <input id="pic_nama" name="pic_nama" type="text" value="{{ old('pic_nama') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="pic_telpon" class="block text-sm font-medium text-gray-700">No. Telpon PIC Internal <span class="text-xs text-gray-500">(opsional)</span></label>
                    <input id="pic_telpon" name="pic_telpon" type="text" value="{{ old('pic_telpon') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Row 5: PIC External -->
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="pic_external_nama" class="block text-sm font-medium text-gray-700">Nama PIC External <span class="text-xs text-gray-500">(opsional)</span></label>
                    <input id="pic_external_nama" name="pic_external_nama" type="text" value="{{ old('pic_external_nama') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="pic_external_telpon" class="block text-sm font-medium text-gray-700">No. Telpon PIC External <span class="text-xs text-gray-500">(opsional)</span></label>
                    <input id="pic_external_telpon" name="pic_external_telpon" type="text" value="{{ old('pic_external_telpon') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
            </div>

            <!-- Row 5: Reminder -->
            <div>
                <label for="reminder_bulan" class="block text-sm font-medium text-gray-700">Reminder</label>
                <select id="reminder_bulan" name="reminder_bulan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Pilih reminder</option>
                    <option value="1" @selected(old('reminder_bulan') == '1')>1 bulan</option>
                    <option value="3" @selected(old('reminder_bulan') == '3')>3 bulan</option>
                    <option value="6" @selected(old('reminder_bulan') == '6')>6 bulan</option>
                </select>
            </div>

            <!-- Row 6: File Lampiran -->
            <div>
                <label for="attachment" class="block text-sm font-medium text-gray-700">File Lampiran (PDF/JPG)</label>
                <input id="attachment" name="attachment" type="file" accept=".pdf,.jpg,.jpeg,application/pdf,image/jpeg" class="mt-1 block w-full text-sm text-gray-700" required>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-6 py-2 text-white text-sm font-semibold hover:bg-blue-700 transition-colors">
                    ✓ Simpan Data
                </button>
                <button type="reset" class="inline-flex items-center rounded-md bg-gray-400 px-6 py-2 text-white text-sm font-semibold hover:bg-gray-500 transition-colors">
                    Bersihkan
                </button>
            </div>
        </form>
    </div>
</div>
