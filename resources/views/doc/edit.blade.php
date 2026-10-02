<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-zinc-100 leading-tight">
            {{ __('Edit Dokumen') }}
        </h2>
    </x-slot>

    <div x-data="formState()">

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('doc.update', $reminder->id) }}" enctype="multipart/form-data" id="documentForm">
            @csrf
            @method('PATCH')
            <input type="hidden" name="tidak_ada_expired" :value="tidakAdaExpired ? '1' : '0'">

            <div class="columns-1 md:columns-2 gap-6">

                {{-- ===== Kartu 1: Pilih Jenis Dokumen ===== --}}
                <div class="break-inside-avoid mb-6 bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm p-4 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-zinc-100 mb-4">Pilih Jenis Dokumen</h3>
                    <select id="jenis_dokumen" name="jenis_dokumen"
                        x-model="jenisDokumen"
                        @change="
                            showRelevantForm();
                            const tipe = $el.options[$el.selectedIndex]?.getAttribute('data-tipe-form') || 'default';
                        "
                        class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required>
                        <option value="">Pilih jenis dokumen</option>
                        @foreach ($documentTypes as $documentType)
                            <option value="{{ $documentType->id }}" data-tipe-form="{{ $documentType->tipe_form }}" @selected(old('jenis_dokumen', $selectedDocumentTypeId) == $documentType->id)>{{ $documentType->nama_jenis }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- ===== Kartu 2: PIC Internal ===== --}}
                <div class="break-inside-avoid mb-6 bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm p-4 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-zinc-100 mb-4">PIC Internal</h3>

                    <div x-data="picSearch()" class="mb-4 relative">
                        <div class="flex items-center rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500">
                            <div class="pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" x-model="search" @input="open = true" @click="open = true" @keydown.escape="open = false" placeholder="Ketik nama untuk mencari user..."
                                class="flex-1 min-w-0 pl-4 pr-2 py-2 rounded-lg border-0 bg-transparent text-sm text-gray-900 dark:text-zinc-100 placeholder-gray-400 focus:ring-0 focus:outline-none" autocomplete="off">
                        </div>
                        <div x-show="open" x-cloak @click.outside="open = false" x-transition class="absolute z-50 mt-1 w-full max-h-60 overflow-auto rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-lg">
                            <template x-for="user in filteredUsers" :key="user.id">
                                <button type="button" @click="selectUser(user)" class="w-full text-left px-4 py-2.5 text-sm hover:bg-indigo-50 dark:hover:bg-zinc-800 transition-colors flex items-center justify-between group">
                                    <span class="text-gray-700 dark:text-zinc-200" x-text="user.nama + ' - ' + user.email"></span>
                                    <span class="text-xs text-indigo-600 dark:text-indigo-400 opacity-0 group-hover:opacity-100 transition-opacity">Pilih</span>
                                </button>
                            </template>
                        </div>
                        <div x-show="open && filteredUsers.length === 0" x-cloak class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 shadow-lg px-4 py-3 text-sm text-gray-500 dark:text-zinc-400">
                            User tidak ditemukan.
                        </div>
                    </div>

                    <div id="recipient_list_container" class="mb-4">
                        <div id="recipient_list" class="flex flex-wrap gap-2"></div>
                        <p class="text-sm text-gray-500 dark:text-zinc-400 italic hidden" id="no_recipients_msg">Belum ada PIC yang dipilih.</p>
                    </div>

                    <input type="hidden" name="pic_nama" id="primary_pic_nama" value="{{ old('pic_nama', $reminder->pic_nama) }}">
                    <input type="hidden" name="pic_email" id="primary_pic_email" value="{{ old('pic_email', $reminder->pic_email) }}">
                    <div id="hidden_user_ids"></div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Nama PIC External</label>
                            <input name="pic_external_nama" type="text" value="{{ old('pic_external_nama', $reminder->pic_external_nama) }}" placeholder="Masukan Nama PIC External"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">No Telp PIC External</label>
                            <input id="pic_external_telpon" name="pic_external_telpon" type="tel" value="{{ old('pic_external_telpon', $reminder->pic_external_telpon) }}" placeholder="Masukan No Telp PIC External" maxlength="15"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>
                </div>

                {{-- ===== Kartu 3: Interval Reminder ===== --}}
                <div class="break-inside-avoid mb-6 bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm p-4 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-zinc-100 mb-4">Pilih Reminder <span class="text-red-500">*</span></h3>
                <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1 mb-4">Wajib diisi jika dokumen memiliki tanggal expired.</p>
                    <select id="reminder_bulan_global" name="reminder_bulan"
                        class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <option value="">Pilih reminder</option>
                        <option value="1" @selected(old('reminder_bulan', $reminder->reminder_bulan) == '1')>1 bulan sebelum expired</option>
                        <option value="3" @selected(old('reminder_bulan', $reminder->reminder_bulan) == '3')>3 bulan sebelum expired</option>
                        <option value="6" @selected(old('reminder_bulan', $reminder->reminder_bulan) == '6')>6 bulan sebelum expired</option>
                        <option value="9" @selected(old('reminder_bulan', $reminder->reminder_bulan) == '9')>9 bulan sebelum expired</option>
                        <option value="12" @selected(old('reminder_bulan', $reminder->reminder_bulan) == '12')>12 bulan sebelum expired</option>
                    </select>
                </div>

                {{-- ===== Kartu 4: Detail Dokumen ===== --}}
                <div class="break-inside-avoid mb-6 bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm p-4 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-zinc-100 mb-4">Detail Dokumen</h3>

                    {{-- Form Default --}}
                    <div id="form-default" class="space-y-4 hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Nama Dokumen</label>
                            <input id="nama_default" name="nama_dokumen" type="text" value="{{ old('nama_dokumen', $reminder->nama_dokumen) }}" placeholder="Masukan Nama Dokumen"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">No Dokumen</label>
                            <input id="no_default" name="no_dokumen" type="text" value="{{ old('no_dokumen', $reminder->no_dokumen) }}" placeholder="Masukan No Dokumen"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Penerbit Dokumen</label>
                            <input id="penerbit_tujuan_default" name="penerbit_tujuan" type="text" value="{{ old('penerbit_tujuan', $reminder->penerbit_tujuan) }}" placeholder="Masukan Nama Penerbit"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Tanggal Terbit</label>
                                <input id="tanggal_terbit_default" name="tanggal_terbit" type="date" value="{{ old('tanggal_terbit', $reminder->tanggal_terbit ? $reminder->tanggal_terbit->format('Y-m-d') : '') }}"
                                    class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Tanggal Expired</label>
                                <input id="tanggal_expired_default" name="tanggal_expired" type="date" value="{{ old('tanggal_expired', $reminder->tanggal_expired ? $reminder->tanggal_expired->format('Y-m-d') : '') }}"
                                    :class="tidakAdaExpired ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="tidakAdaExpired"
                                class="rounded border-gray-300 dark:border-zinc-600 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-600 dark:text-zinc-400">Tidak Ada Expired</span>
                        </label>
                        <p x-show="tidakAdaExpired" class="text-sm text-emerald-600 dark:text-emerald-400 mt-1">Seumur Hidup (tanpa expired)</p>
                    </div>

                    {{-- Form Sertifikat --}}
                    <div id="form-sertifikat" class="space-y-4 hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Nama Sertifikat</label>
                            <input id="nama_dokumen" name="nama_dokumen" type="text" value="{{ old('nama_dokumen', $reminder->nama_dokumen) }}" placeholder="Masukan Nama Sertifikat"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">No Sertifikat</label>
                            <input id="no_dokumen" name="no_dokumen" type="text" value="{{ old('no_dokumen', $reminder->no_dokumen) }}" placeholder="Masukan No Sertifikat"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Penerbit Sertifikat</label>
                            <input id="penerbit_tujuan" name="penerbit_tujuan" type="text" value="{{ old('penerbit_tujuan', $reminder->penerbit_tujuan) }}" placeholder="Masukan Nama Penerbit"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Tanggal Terbit</label>
                                <input id="tanggal_terbit" name="tanggal_terbit" type="date" value="{{ old('tanggal_terbit', $reminder->tanggal_terbit ? $reminder->tanggal_terbit->format('Y-m-d') : '') }}"
                                    class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Tanggal Expired</label>
                                <input id="tanggal_expired" name="tanggal_expired" type="date" value="{{ old('tanggal_expired', $reminder->tanggal_expired ? $reminder->tanggal_expired->format('Y-m-d') : '') }}"
                                    :class="tidakAdaExpired ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="tidakAdaExpired"
                                class="rounded border-gray-300 dark:border-zinc-600 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-600 dark:text-zinc-400">Tidak Ada Expired</span>
                        </label>
                        <p x-show="tidakAdaExpired" class="text-sm text-emerald-600 dark:text-emerald-400 mt-1">Seumur Hidup (tanpa expired)</p>
                    </div>

                    {{-- Form Wajib Lapor Tahunan --}}
                    <div id="form-wajib-lapor-tahunan" class="space-y-4 hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Nama Wajib Lapor</label>
                            <input id="nama_dokumen_wlt" name="nama_dokumen" type="text" value="{{ old('nama_dokumen', $reminder->nama_dokumen) }}" placeholder="Masukan Wajib Lapor Tahunan"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">No Pelaporan</label>
                            <input id="tahun_laporan" name="no_dokumen" type="text" value="{{ old('no_dokumen', $reminder->no_dokumen) }}" placeholder="Masukan No Pelaporan"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Instansi Tujuan</label>
                            <input id="instansi_tujuan" name="penerbit_tujuan" type="text" value="{{ old('penerbit_tujuan', $reminder->penerbit_tujuan) }}" placeholder="Masukan Nama Instansi"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Tanggal Terbit</label>
                                <input id="tanggal_terbit_wlt" name="tanggal_terbit" type="date" value="{{ old('tanggal_terbit', $reminder->tanggal_terbit ? $reminder->tanggal_terbit->format('Y-m-d') : '') }}"
                                    class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Tanggal Expired</label>
                                <input id="batas_pengiriman" name="tanggal_expired" type="date" value="{{ old('tanggal_expired', $reminder->tanggal_expired ? $reminder->tanggal_expired->format('Y-m-d') : '') }}"
                                    :class="tidakAdaExpired ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="tidakAdaExpired"
                                class="rounded border-gray-300 dark:border-zinc-600 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-600 dark:text-zinc-400">Tidak Ada Expired</span>
                        </label>
                        <p x-show="tidakAdaExpired" class="text-sm text-emerald-600 dark:text-emerald-400 mt-1">Seumur Hidup (tanpa expired)</p>
                    </div>

                    {{-- Form SLO --}}
                    <div id="form-slo" class="space-y-4 hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Nama SLO</label>
                            <input id="nama_slo" name="nama_dokumen" type="text" value="{{ old('nama_dokumen', $reminder->nama_dokumen) }}" placeholder="Masukan Nama SLO"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">No SLO</label>
                            <input id="tahun_slo" name="no_dokumen" type="text" value="{{ old('no_dokumen', $reminder->no_dokumen) }}" placeholder="Masukan No SLO"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Penerbit SLO</label>
                            <input id="instansi_slo" name="penerbit_tujuan" type="text" value="{{ old('penerbit_tujuan', $reminder->penerbit_tujuan) }}" placeholder="Masukan Nama Penerbit SLO"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Tanggal Terbit</label>
                                <input id="tanggal_terbit_slo" name="tanggal_terbit" type="date" value="{{ old('tanggal_terbit', $reminder->tanggal_terbit ? $reminder->tanggal_terbit->format('Y-m-d') : '') }}"
                                    class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Tanggal Expired</label>
                                <input id="batas_pengiriman_slo" name="tanggal_expired" type="date" value="{{ old('tanggal_expired', $reminder->tanggal_expired ? $reminder->tanggal_expired->format('Y-m-d') : '') }}"
                                    :class="tidakAdaExpired ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="tidakAdaExpired"
                                class="rounded border-gray-300 dark:border-zinc-600 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-600 dark:text-zinc-400">Tidak Ada Expired</span>
                        </label>
                        <p x-show="tidakAdaExpired" class="text-sm text-emerald-600 dark:text-emerald-400 mt-1">Seumur Hidup (tanpa expired)</p>
                    </div>

                    {{-- Form Legalitas --}}
                    <div id="form-legalitas" class="space-y-4 hidden">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Nama Legalitas</label>
                            <input id="nama_legalitas" name="nama_dokumen" type="text" value="{{ old('nama_dokumen', $reminder->nama_dokumen) }}" placeholder="Masukan Nama Legalitas"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">No Legalitas</label>
                            <input id="no_legalitas" name="no_dokumen" type="text" value="{{ old('no_dokumen', $reminder->no_dokumen) }}" placeholder="Masukan No Legalitas"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Penerbit Legalitas</label>
                            <input id="instansi_legalitas" name="penerbit_tujuan" type="text" value="{{ old('penerbit_tujuan', $reminder->penerbit_tujuan) }}" placeholder="Masukan Nama Penerbit Legalitas"
                                class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Tanggal Terbit</label>
                                <input id="tanggal_terbit_legalitas" name="tanggal_terbit" type="date" value="{{ old('tanggal_terbit', $reminder->tanggal_terbit ? $reminder->tanggal_terbit->format('Y-m-d') : '') }}"
                                    class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Tanggal Expired</label>
                                <input id="batas_pengiriman_legalitas" name="tanggal_expired" type="date" value="{{ old('tanggal_expired', $reminder->tanggal_expired ? $reminder->tanggal_expired->format('Y-m-d') : '') }}"
                                    :class="tidakAdaExpired ? 'opacity-50 cursor-not-allowed' : ''"
                                    class="block w-full rounded-lg border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="tidakAdaExpired"
                                class="rounded border-gray-300 dark:border-zinc-600 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-sm text-gray-600 dark:text-zinc-400">Tidak Ada Expired</span>
                        </label>
                        <p x-show="tidakAdaExpired" class="text-sm text-emerald-600 dark:text-emerald-400 mt-1">Seumur Hidup (tanpa expired)</p>
                    </div>
                </div>

                {{-- ===== Kartu 5: Lampiran Dokumen ===== --}}
                <div class="break-inside-avoid mb-6 bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm p-4 sm:p-6">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-zinc-100 mb-4">Lampiran Dokumen</h3>
                    @if($reminder->attachment_path)
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mb-1.5">File saat ini: <a href="{{ route('doc.view', $reminder->id) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $reminder->attachment_name }}</a></p>
                    @endif
                    <input id="attachment" name="attachment" type="file"
                        accept=".pdf,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg"
                        class="block w-full text-sm text-gray-500 dark:text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50 transition-colors">
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-zinc-400">Format: PDF, PNG, JPG, JPEG. Maks 3MB. Kosongkan jika tidak ingin mengganti lampiran.</p>
                </div>

            </div>

            {{-- ===== Footer Buttons ===== --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end mt-6 pt-4 border-t border-gray-200 dark:border-zinc-700">
                <a href="{{ url()->previous() }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-zinc-400 dark:hover:text-zinc-200 transition-colors text-center py-2.5 rounded-lg border border-zinc-300 dark:border-zinc-600 sm:border-0 sm:py-0">
                    Batal
                </a>
                <button type="submit"
                    class="inline-flex items-center justify-center w-full sm:w-auto px-5 py-2.5 sm:py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>

    </div>

    <script>
        function formState() {
            return {
                jenisDokumen: '{{ old('jenis_dokumen', $selectedDocumentTypeId) }}',
                tidakAdaExpired: {{ old('tidak_ada_expired') !== null
                    ? (old('tidak_ada_expired') == '1' ? 'true' : 'false')
                    : (is_null($reminder->tanggal_expired) ? 'true' : 'false') }},

                init() {
                    if (document.getElementById('jenis_dokumen').value) {
                        this.jenisDokumen = document.getElementById('jenis_dokumen').value;
                    }
                    if (this.tidakAdaExpired) {
                        document.querySelectorAll('div[id^="form-"]:not(.hidden) input[name="tanggal_expired"]')
                            .forEach(input => { input.value = ''; });
                        const reminderSelect = document.getElementById('reminder_bulan_global');
                        if (reminderSelect) { reminderSelect.value = ''; }
                    }
                    this.$watch('tidakAdaExpired', (checked) => {
                        if (checked) {
                            document.querySelectorAll('div[id^="form-"]:not(.hidden) input[name="tanggal_expired"]')
                                .forEach(input => { input.value = ''; });
                            const reminderSelect = document.getElementById('reminder_bulan_global');
                            if (reminderSelect) { reminderSelect.value = ''; }
                        }
                    });
                    document.addEventListener('change', (e) => {
                        if (e.target.matches('div[id^="form-"]:not(.hidden) input[name="tanggal_expired"]')) {
                            const r = document.getElementById('reminder_bulan_global');
                            if (e.target.value && r && !r.value) { r.value = '1'; }
                        }
                    });
                }
            };
        }

        function picSearch() {
            return {
                search: '',
                open: false,
                users: @json($users->map(fn($u) => ['id' => $u->id, 'nama' => $u->nama, 'email' => $u->email])),
                get filteredUsers() {
                    if (!this.search) return this.users.filter(u => !selectedUsers.some(s => s.id === u.id));
                    return this.users.filter(u =>
                        !selectedUsers.some(s => s.id === u.id) &&
                        (u.nama.toLowerCase().includes(this.search.toLowerCase()) ||
                        (u.email && u.email.toLowerCase().includes(this.search.toLowerCase())))
                    );
                },
                selectUser(user) {
                    if (selectedUsers.some(u => u.id === user.id)) {
                        alert('User ini sudah ada dalam daftar.');
                        this.search = '';
                        this.open = false;
                        return;
                    }
                    selectedUsers.push({ id: user.id, name: user.nama, email: user.email });
                    updateRecipientUI();
                    this.search = '';
                    this.open = false;
                }
            };
        }

        let selectedUsers = @json($selectedPics);

        function showRelevantForm() {
            const selectElement = document.getElementById('jenis_dokumen');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const tipeForm = selectedOption ? selectedOption.getAttribute('data-tipe-form') : null;

            const formDefault = document.getElementById('form-default');
            const formSertifikat = document.getElementById('form-sertifikat');
            const formWajibLapor = document.getElementById('form-wajib-lapor-tahunan');
            const formSLO = document.getElementById('form-slo');
            const formLegalitas = document.getElementById('form-legalitas');

            [formDefault, formSertifikat, formWajibLapor, formSLO, formLegalitas].forEach(form => {
                if (form) {
                    form.classList.add('hidden');
                    form.querySelectorAll('input, select').forEach(el => el.disabled = true);
                }
            });

            if (tipeForm === 'sertifikat') {
                formSertifikat.classList.remove('hidden');
                formSertifikat.querySelectorAll('input, select').forEach(el => el.disabled = false);
            } else if (tipeForm === 'wajib_lapor_tahunan') {
                formWajibLapor.classList.remove('hidden');
                formWajibLapor.querySelectorAll('input, select').forEach(el => el.disabled = false);
            } else if (tipeForm === 'slo') {
                formSLO.classList.remove('hidden');
                formSLO.querySelectorAll('input, select').forEach(el => el.disabled = false);
            } else if (tipeForm === 'legalitas') {
                formLegalitas.classList.remove('hidden');
                formLegalitas.querySelectorAll('input, select').forEach(el => el.disabled = false);
            } else {
                if (formDefault) {
                    formDefault.classList.remove('hidden');
                    formDefault.querySelectorAll('input, select').forEach(el => el.disabled = false);
                }
            }

            const visibleForm = document.querySelector('div[id^="form-"]:not(.hidden)');
            if (visibleForm) {
                const cb = visibleForm.querySelector('input[x-model="tidakAdaExpired"]');
                if (cb && cb.checked) {
                    const dateInput = visibleForm.querySelector('input[name="tanggal_expired"]');
                    if (dateInput) { dateInput.value = ''; }
                }
            }
        }

        function escapeHtml(str) {
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function removePicRecipient(userId) {
            selectedUsers = selectedUsers.filter(u => u.id != userId);
            updateRecipientUI();
        }

        function updateRecipientUI() {
            const container = document.getElementById('recipient_list');
            const msg = document.getElementById('no_recipients_msg');
            const hiddenContainer = document.getElementById('hidden_user_ids');

            container.innerHTML = '';
            hiddenContainer.innerHTML = '';

            if (selectedUsers.length === 0) {
                msg.classList.remove('hidden');
            } else {
                msg.classList.add('hidden');

                selectedUsers.forEach((user, index) => {
                    const isPrimary = index === 0;

                    const chip = document.createElement('div');
                    chip.className = `inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border text-sm font-medium ${isPrimary ? 'bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-900/20 dark:border-indigo-800 dark:text-indigo-300' : 'bg-gray-50 border-gray-200 text-gray-700 dark:bg-zinc-800/50 dark:border-zinc-700 dark:text-zinc-300'}`;
                    chip.innerHTML = `
                        <span>${escapeHtml(user.name)}${isPrimary ? ' (Utama)' : ''}</span>
                        <button type="button" onclick="removePicRecipient('${user.id}')" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    container.appendChild(chip);

                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'pic_internal_user_ids[]';
                    hidden.value = user.id;
                    hiddenContainer.appendChild(hidden);

                    if (isPrimary) {
                        document.getElementById('primary_pic_nama').value = user.name;
                        document.getElementById('primary_pic_email').value = user.email || '';
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            showRelevantForm();
            updateRecipientUI();
        });
    </script>
</x-app-layout>
