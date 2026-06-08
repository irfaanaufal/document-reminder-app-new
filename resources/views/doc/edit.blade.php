<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-zinc-100 leading-tight">
            {{ __('Edit Dokumen') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 overflow-hidden shadow-sm sm:rounded-lg border border-gray-200 dark:border-zinc-800">
                <div class="p-6 text-gray-900 dark:text-zinc-100">
                    @if ($errors->any())
                        <div class="mb-6 rounded-md bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-900 px-4 py-3 text-sm text-red-700 dark:text-red-300">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('doc.update', $reminder->id) }}" enctype="multipart/form-data" id="documentForm" class="space-y-6">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="return_url" value="{{ url()->previous() }}">
                        
                        <!-- Jenis Dokumen Selector -->
                        <div class="max-w-full">
                            <x-input-label for="jenis_dokumen" :value="__('Pilih Jenis Dokumen')" />
                            <select id="jenis_dokumen" name="jenis_dokumen" class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required onchange="showRelevantForm()">
                                <option value="">Pilih jenis dokumen</option>
                                @foreach ($documentTypes as $documentType)
                                    <option value="{{ $documentType->id }}" data-tipe-form="{{ $documentType->tipe_form }}" @selected(old('jenis_dokumen', $selectedDocumentTypeId) == $documentType->id)>{{ $documentType->nama_jenis }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Form Default -->
                        <div id="form-default" class="space-y-6 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="nama_default" :value="__('Nama Dokumen')" />
                                    <x-text-input id="nama_default" name="nama_dokumen" type="text" class="mt-1 block w-full" :value="old('nama_dokumen', $reminder->nama_dokumen)" placeholder="Masukkan nama dokumen" />
                                </div>
                                <div>
                                    <x-input-label for="no_default" :value="__('No Dokumen')" />
                                    <x-text-input id="no_default" name="no_dokumen" type="text" class="mt-1 block w-full" :value="old('no_dokumen', $reminder->no_dokumen)" placeholder="Masukkan nomor dokumen" />
                                </div>
                                <div>
                                    <x-input-label for="penerbit_tujuan_default" :value="__('Penerbit Dokumen')" />
                                    <x-text-input id="penerbit_tujuan_default" name="penerbit_tujuan" type="text" class="mt-1 block w-full" :value="old('penerbit_tujuan', $reminder->penerbit_tujuan)" placeholder="Masukkan nama penerbit" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="tanggal_terbit_default" :value="__('Tanggal Terbit')" />
                                    <x-text-input id="tanggal_terbit_default" name="tanggal_terbit" type="date" class="mt-1 block w-full" :value="old('tanggal_terbit', $reminder->tanggal_terbit ? $reminder->tanggal_terbit->format('Y-m-d') : '')" />
                                </div>
                                <div>
                                    <x-input-label for="tanggal_expired_default" :value="__('Tanggal Expired')" />
                                    <x-text-input id="tanggal_expired_default" name="tanggal_expired" type="date" class="mt-1 block w-full" :value="old('tanggal_expired', $reminder->tanggal_expired ? $reminder->tanggal_expired->format('Y-m-d') : '')" />
                                </div>
                            </div>
                        </div>

                        <!-- Form Sertifikat -->
                        <div id="form-sertifikat" class="space-y-6 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="nama_dokumen" :value="__('Nama Sertifikat')" />
                                    <x-text-input id="nama_dokumen" name="nama_dokumen" type="text" class="mt-1 block w-full" :value="old('nama_dokumen', $reminder->nama_dokumen)" placeholder="Masukkan nama sertifikat" />
                                </div>
                                <div>
                                    <x-input-label for="no_dokumen" :value="__('No Sertifikat')" />
                                    <x-text-input id="no_dokumen" name="no_dokumen" type="text" class="mt-1 block w-full" :value="old('no_dokumen', $reminder->no_dokumen)" placeholder="Masukkan nomor dokumen" />
                                </div>
                                <div>
                                    <x-input-label for="penerbit_tujuan" :value="__('Penerbit')" />
                                    <x-text-input id="penerbit_tujuan" name="penerbit_tujuan" type="text" class="mt-1 block w-full" :value="old('penerbit_tujuan', $reminder->penerbit_tujuan)" placeholder="Contoh: BPN Pontianak" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="tanggal_terbit" :value="__('Tanggal Terbit')" />
                                    <x-text-input id="tanggal_terbit" name="tanggal_terbit" type="date" class="mt-1 block w-full" :value="old('tanggal_terbit', $reminder->tanggal_terbit->format('Y-m-d'))" />
                                </div>
                                <div>
                                    <x-input-label for="tanggal_expired" :value="__('Tanggal Expired')" />
                                    <x-text-input id="tanggal_expired" name="tanggal_expired" type="date" class="mt-1 block w-full" :value="old('tanggal_expired', $reminder->tanggal_expired->format('Y-m-d'))" />
                                </div>
                            </div>
                        </div>

                        <!-- Form Wajib Lapor Tahunan -->
                        <div id="form-wajib-lapor-tahunan" class="space-y-6 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="nama_dokumen_wlt" :value="__('Nama Wajib Lapor')" />
                                    <x-text-input id="nama_dokumen_wlt" name="nama_dokumen" type="text" class="mt-1 block w-full" :value="old('nama_dokumen', $reminder->nama_dokumen)" placeholder="Contoh: WLK Tahunan 2024" />
                                </div>
                                <div>
                                    <x-input-label for="tahun_laporan" :value="__('No Pelaporan')" />
                                    <x-text-input id="tahun_laporan" name="no_dokumen" type="text" class="mt-1 block w-full" :value="old('no_dokumen', $reminder->no_dokumen)" placeholder="Masukkan nomor pelaporan" />
                                </div>
                                <div>
                                    <x-input-label for="instansi_tujuan" :value="__('Instansi Tujuan')" />
                                    <x-text-input id="instansi_tujuan" name="penerbit_tujuan" type="text" class="mt-1 block w-full" :value="old('penerbit_tujuan', $reminder->penerbit_tujuan)" placeholder="Contoh: Dinas Tenaga Kerja" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="tanggal_terbit_wlt" :value="__('Tanggal Terbit')" />
                                    <x-text-input id="tanggal_terbit_wlt" name="tanggal_terbit" type="date" class="mt-1 block w-full" :value="old('tanggal_terbit', $reminder->tanggal_terbit->format('Y-m-d'))" />
                                </div>
                                <div>
                                    <x-input-label for="batas_pengiriman" :value="__('Tanggal Expired')" />
                                    <x-text-input id="batas_pengiriman" name="tanggal_expired" type="date" class="mt-1 block w-full" :value="old('tanggal_expired', $reminder->tanggal_expired->format('Y-m-d'))" />
                                </div>
                            </div>
                        </div>

                        <!-- Form SLO -->
                        <div id="form-slo" class="space-y-6 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="nama_slo" :value="__('Nama SLO')" />
                                    <x-text-input id="nama_slo" name="nama_dokumen" type="text" class="mt-1 block w-full" :value="old('nama_dokumen', $reminder->nama_dokumen)" placeholder="Masukkan nama SLO" />
                                </div>
                                <div>
                                    <x-input-label for="tahun_slo" :value="__('No SLO')" />
                                    <x-text-input id="tahun_slo" name="no_dokumen" type="text" class="mt-1 block w-full" :value="old('no_dokumen', $reminder->no_dokumen)" placeholder="Masukkan nomor SLO" />
                                </div>
                                <div>
                                    <x-input-label for="instansi_slo" :value="__('Penerbit SLO')" />
                                    <x-text-input id="instansi_slo" name="penerbit_tujuan" type="text" class="mt-1 block w-full" :value="old('penerbit_tujuan', $reminder->penerbit_tujuan)" placeholder="Masukkan nama penerbit SLO" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="tanggal_terbit_slo" :value="__('Tanggal Terbit')" />
                                    <x-text-input id="tanggal_terbit_slo" name="tanggal_terbit" type="date" class="mt-1 block w-full" :value="old('tanggal_terbit', $reminder->tanggal_expired->format('Y-m-d'))" />
                                </div>
                                <div>
                                    <x-input-label for="batas_pengiriman_slo" :value="__('Tanggal Expired')" />
                                    <x-text-input id="batas_pengiriman_slo" name="tanggal_expired" type="date" class="mt-1 block w-full" :value="old('tanggal_expired', $reminder->tanggal_expired->format('Y-m-d'))" />
                                </div>
                            </div>
                        </div>

                        <!-- Form Legalitas -->
                        <div id="form-legalitas" class="space-y-6 hidden">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="nama_legalitas" :value="__('Nama Legalitas')" />
                                    <x-text-input id="nama_legalitas" name="nama_dokumen" type="text" class="mt-1 block w-full" :value="old('nama_dokumen', $reminder->nama_dokumen)" placeholder="Masukkan nama legalitas" />
                                </div>
                                <div>
                                    <x-input-label for="no_legalitas" :value="__('No Legalitas')" />
                                    <x-text-input id="no_legalitas" name="no_dokumen" type="text" class="mt-1 block w-full" :value="old('no_dokumen', $reminder->no_dokumen)" placeholder="Masukkan nomor legalitas" />
                                </div>
                                <div>
                                    <x-input-label for="instansi_legalitas" :value="__('Penerbit Legalitas')" />
                                    <x-text-input id="instansi_legalitas" name="penerbit_tujuan" type="text" class="mt-1 block w-full" :value="old('penerbit_tujuan', $reminder->penerbit_tujuan)" placeholder="Masukkan nama penerbit legalitas" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="tanggal_terbit_legalitas" :value="__('Tanggal Terbit')" />
                                    <x-text-input id="tanggal_terbit_legalitas" name="tanggal_terbit" type="date" class="mt-1 block w-full" :value="old('tanggal_terbit', $reminder->tanggal_terbit->format('Y-m-d'))" />
                                </div>
                                <div>
                                    <x-input-label for="batas_pengiriman_legalitas" :value="__('Tanggal Expired')" />
                                    <x-text-input id="batas_pengiriman_legalitas" name="tanggal_expired" type="date" class="mt-1 block w-full" :value="old('tanggal_expired', $reminder->tanggal_expired->format('Y-m-d'))" />
                                </div>
                            </div>
                        </div>

                        <!-- Section: PIC & Penerima WhatsApp -->
                        <div class="bg-gray-50 dark:bg-zinc-800/50 p-6 rounded-lg border border-gray-200 dark:border-zinc-700 space-y-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-zinc-100">Person In Charge (PIC)</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                <div class="md:col-span-2">
                                    <h3 class="text-md font-medium text-gray-900 dark:text-zinc-100 mb-4 text-sm uppercase">PIC Internal</h3>
                                    <x-input-label for="user_selector"/>
                                    <select id="user_selector" class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Pilih User Yang Akan Menerima Notifikasi</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" data-nama="{{ $user->nama }}" data-telp="{{ $user->no_telpon }}">
                                                {{ $user->nama }} - {{ $user->no_telpon ?? 'No Telp Belum Ada' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-primary-button type="button" onclick="addPicRecipient()" class="justify-center h-10 bg-blue-600 hover:bg-blue-700 focus:ring-blue-500">
                                    {{ __('Tambah PIC') }}
                                </x-primary-button>
                            </div>

                            <div id="recipient_list_container" class="space-y-3">
                                <p class="text-sm text-gray-500 dark:text-zinc-400 italic" id="no_recipients_msg">Belum ada PIC yang dipilih. PIC pertama akan dijadikan PIC utama.</p>
                                <div id="recipient_list" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- List items injected here by JS -->
                                </div>
                            </div>

                            <!-- Hidden inputs for legacy fields -->
                            <input type="hidden" name="pic_nama" id="primary_pic_nama" value="{{ old('pic_nama', $reminder->pic_nama) }}">
                            <input type="hidden" name="pic_telpon" id="primary_pic_telpon" value="{{ old('pic_telpon', $reminder->pic_telpon) }}">
                            <div id="hidden_user_ids"></div>

                            <div class="pt-6 border-t border-gray-200 dark:border-zinc-700">
                                <h3 class="text-md font-medium text-gray-900 dark:text-zinc-100 mb-4 text-sm uppercase">PIC External (Opsional)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="pic_external_nama" :value="__('Nama PIC External')" />
                                        <x-text-input name="pic_external_nama" type="text" class="mt-1 block w-full" :value="old('pic_external_nama', $reminder->pic_external_nama)" />
                                    </div>
                                    <div>
                                        <x-input-label for="pic_external_telpon" :value="__('No Telp PIC External')" />
                                        <x-text-input name="pic_external_telpon" type="tel" class="mt-1 block w-full" :value="old('pic_external_telpon', $reminder->pic_external_telpon)" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Reminder & Attachment -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                            <div>
                                <x-input-label for="reminder_bulan_global" :value="__('Interval Reminder')" />
                                <select id="reminder_bulan_global" name="reminder_bulan" class="mt-1 block w-full rounded-md border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih reminder</option>
                                    <option value="1" @selected(old('reminder_bulan', $reminder->reminder_bulan) == '1')>1 bulan sebelum expired</option>
                                    <option value="3" @selected(old('reminder_bulan', $reminder->reminder_bulan) == '3')>3 bulan sebelum expired</option>
                                    <option value="6" @selected(old('reminder_bulan', $reminder->reminder_bulan) == '6')>6 bulan sebelum expired</option>
                                    <option value="9" @selected(old('reminder_bulan', $reminder->reminder_bulan) == '9')>9 bulan sebelum expired</option>
                                    <option value="12" @selected(old('reminder_bulan', $reminder->reminder_bulan) == '12')>12 bulan sebelum expired</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="attachment" :value="__('Lampiran Dokumen (PDF/PNG/JPG/JPEG, Maks 3MB)')" />
                                @if($reminder->attachment_path)
                                    <p class="text-xs text-gray-500 dark:text-zinc-400 mb-2">File saat ini: <a href="{{ route('doc.view', $reminder->id) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ $reminder->attachment_name }}</a></p>
                                @endif
                                <input id="attachment" name="attachment" type="file" accept=".pdf,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg" class="mt-1 block w-full text-sm text-gray-500 dark:text-zinc-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 dark:file:bg-zinc-800 file:text-zinc-700 dark:file:text-zinc-300 hover:file:bg-zinc-200 dark:hover:file:bg-zinc-700 transition-colors">
                                <p class="mt-2 text-xs text-gray-500 dark:text-zinc-400 italic">Kosongkan jika tidak ingin mengganti lampiran.</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-100 dark:border-zinc-800">
                            <a href="{{ url()->previous() }}" class="inline-flex items-center px-8 py-3 bg-red-600 rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 transition ease-in-out duration-150">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button class="inline-flex items-center px-8 py-3 bg-green-600 rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 transition ease-in-out duration-150">
                                {{ __('Simpan Perubahan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let selectedUsers = @json($reminder->internalPics->map(fn($u) => ['id' => $u->id, 'name' => $u->nama, 'telp' => $u->no_telpon]));

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
        }

        function addPicRecipient() {
            const selector = document.getElementById('user_selector');
            const userId = selector.value;
            if (!userId) return;

            if (selectedUsers.some(u => u.id == userId)) {
                alert('User ini sudah ada dalam daftar.');
                return;
            }

            const option = selector.options[selector.selectedIndex];
            const name = option.getAttribute('data-nama');
            const telp = option.getAttribute('data-telp');

            selectedUsers.push({ id: userId, name: name, telp: telp });
            updateRecipientUI();
            selector.value = '';
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
                    
                    const div = document.createElement('div');
                    div.className = `flex items-center justify-between p-3 rounded-md border ${isPrimary ? 'bg-indigo-50 border-indigo-200 dark:bg-indigo-900/20 dark:border-indigo-800' : 'bg-white border-gray-200 dark:bg-zinc-900 dark:border-zinc-700'}`;
                    div.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-zinc-300">
                                ${user.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <p class="text-sm font-semibold dark:text-zinc-100">${user.name} ${isPrimary ? '<span class="text-[10px] bg-indigo-600 text-white px-1.5 py-0.5 rounded ml-1 uppercase">Utama</span>' : ''}</p>
                                <p class="text-xs text-gray-500 dark:text-zinc-400">${user.telp || 'No Telp -'}</p>
                            </div>
                        </div>
                        <button type="button" onclick="removePicRecipient('${user.id}')" class="text-gray-400 hover:text-red-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    `;
                    container.appendChild(div);

                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'pic_internal_user_ids[]';
                    hidden.value = user.id;
                    hiddenContainer.appendChild(hidden);

                    if (isPrimary) {
                        document.getElementById('primary_pic_nama').value = user.name;
                        document.getElementById('primary_pic_telpon').value = user.telp || '';
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
