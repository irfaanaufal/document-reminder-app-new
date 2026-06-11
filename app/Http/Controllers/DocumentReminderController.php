<?php

namespace App\Http\Controllers;

use App\Models\DocumentReminder;
use App\Models\DocumentType;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocumentReminderController extends Controller
{
    private function normalizePhoneNumber(?string $phone): string
    {
        $phone = trim((string) $phone);

        if ($phone === '') {
            return '';
        }

        $phone = preg_replace('/\D+/', '', $phone) ?? '';

        if ($phone === '') {
            return '';
        }

        if (str_starts_with($phone, '62')) {
            $phone = '0' . substr($phone, 2);
        }

        if (str_starts_with($phone, '8')) {
            $phone = '0' . $phone;
        }

        return $phone;
    }

    private function documentTypesForForm(?DocumentReminder $reminder = null)
    {
        return DocumentType::query()
            ->where('status', 'active')
            ->when($reminder !== null, function ($query) use ($reminder) {
                $query->orWhere('id', $reminder->jenis_dokumen)
                    ->orWhere('nama_jenis', $reminder->jenis_dokumen);
            })
            ->orderBy('nama_jenis')
            ->get();
    }

    private function documentTypeIdForForm(DocumentReminder $reminder): ?int
    {
        if (is_numeric($reminder->jenis_dokumen)) {
            return (int) $reminder->jenis_dokumen;
        }

        return DocumentType::query()
            ->where('nama_jenis', $reminder->jenis_dokumen)
            ->value('id');
    }

    private function redirectAfterSave(Request $request, string $fallbackMessage): RedirectResponse
    {
        $returnUrl = (string) $request->input('return_url', '');

        if ($returnUrl !== '' && str_starts_with($returnUrl, url('/'))) {
            return redirect()->to($returnUrl)->with('success', $fallbackMessage);
        }

        return redirect()
            ->route('dokumen', ['jenis' => 'semua'])
            ->with('success', $fallbackMessage);
    }

    /**
     * Helper privat untuk memproses dan menyinkronkan data PIC Internal
     */
    private function syncInternalPics(DocumentReminder $reminder, array $userIds): void
    {
        $syncData = [];
        $users = User::whereIn('id', $userIds)->get()->keyBy('id');
        
        foreach ($userIds as $userId) {
            if (isset($users[$userId])) {
                $syncData[$userId] = [
                    'nama' => $users[$userId]->nama,
                    'no_telpon' => $this->normalizePhoneNumber($users[$userId]->no_telpon)
                ];
            }
        }
        $reminder->internalPics()->sync($syncData);
    }

    /**
     * Helper privat untuk standarisasi format data PIC & Telepon
     */
    private function formatPicData(array $validated): array
    {
        $validated['pic_nama'] = $validated['pic_nama'] ?? '';
        $validated['pic_telpon'] = $this->normalizePhoneNumber($validated['pic_telpon'] ?? '');
        $validated['pic_external_nama'] = $validated['pic_external_nama'] ?? '';
        $validated['pic_external_telpon'] = $this->normalizePhoneNumber($validated['pic_external_telpon'] ?? '');

        return $validated;
    }

    /**
     * Rules validasi bersama untuk store dan update
     */
    private function validationRules(bool $isUpdate = false): array
    {
        return [
            'nama_dokumen' => ['required', 'string', 'max:255'],
            'no_dokumen' => ['required', 'string', 'max:255'],
            'jenis_dokumen' => ['required', 'integer', Rule::exists('document_types', 'id')],
            'pic_nama' => ['nullable', 'string', 'max:255'],
            'pic_telpon' => ['nullable', 'string', 'max:20'],
            'pic_external_nama' => ['nullable', 'string', 'max:255'],
            'pic_external_telpon' => ['nullable', 'string', 'max:15', 'regex:/^[0-9]+$/'],
            'penerbit_tujuan' => ['required', 'string', 'max:255'],
            'tanggal_terbit' => ['required', 'date'],
            'tanggal_expired' => ['required', 'date', 'after_or_equal:tanggal_terbit'],
            'reminder_bulan' => ['required', Rule::in([1, 3, 6, 9, 12])],
            'attachment' => [$isUpdate ? 'nullable' : 'required', 'file', 'mimes:pdf,png,jpg,jpeg', 'max:3072'],
        ];
    }

    public function create(): View
    {
        return view('doc.create', [
            'documentTypes' => $this->documentTypesForForm(),
            'users' => User::where('is_active', true)->orderBy('nama')->get(),
        ]);
    }

    public function show(DocumentReminder $reminder): View
    {
        $this->authorize('view', $reminder);
        $reminder->load(['internalPics', 'documentType', 'user']);
        return view('doc.show', ['reminder' => $reminder]);
    }

    public function edit(DocumentReminder $reminder): View
    {
        $this->authorize('update', $reminder);
        $reminder->load('internalPics');

        return view('doc.edit', [
            'reminder' => $reminder,
            'documentTypes' => $this->documentTypesForForm($reminder),
            'selectedDocumentTypeId' => $this->documentTypeIdForForm($reminder),
            'users' => User::where('is_active', true)->orderBy('nama')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->validationRules(false));

        $attachment = $request->file('attachment');
        $storedPath = $attachment->store('document-reminders', 'public');

        $validated = $this->formatPicData($validated);
        
        $reminder = DocumentReminder::create([
            'user_id' => $request->user()->id,
            'nama_dokumen' => $validated['nama_dokumen'],
            'no_dokumen' => $validated['no_dokumen'],
            'jenis_dokumen' => $validated['jenis_dokumen'],
            'pic_nama' => $validated['pic_nama'],
            'pic_telpon' => $validated['pic_telpon'],
            'pic_external_nama' => $validated['pic_external_nama'],
            'pic_external_telpon' => $validated['pic_external_telpon'],
            'penerbit_tujuan' => $validated['penerbit_tujuan'],
            'tanggal_terbit' => $validated['tanggal_terbit'],
            'tanggal_expired' => $validated['tanggal_expired'],
            'reminder_bulan' => $validated['reminder_bulan'],
            'attachment_path' => $storedPath,
            'attachment_name' => $attachment->getClientOriginalName(),
        ]);

        if ($request->has('pic_internal_user_ids')) {
            $this->syncInternalPics($reminder, $request->input('pic_internal_user_ids'));
        }

        return $this->redirectAfterSave($request, 'Data dokumen berhasil disimpan.');
    }

    public function update(Request $request, DocumentReminder $reminder): RedirectResponse
    {
        $this->authorize('update', $reminder);

        $validated = $request->validate($this->validationRules(true));

        if ($request->hasFile('attachment')) {
            if ($reminder->attachment_path && Storage::disk('public')->exists($reminder->attachment_path)) {
                Storage::disk('public')->delete($reminder->attachment_path);
            }
            $attachment = $request->file('attachment');
            $storedPath = $attachment->store('document-reminders', 'public');
            $validated['attachment_path'] = $storedPath;
            $validated['attachment_name'] = $attachment->getClientOriginalName();
        }

        $validated = $this->formatPicData($validated);

        $reminder->update($validated);

        if ($request->has('pic_internal_user_ids')) {
            $this->syncInternalPics($reminder, $request->input('pic_internal_user_ids'));
        }

        return $this->redirectAfterSave($request, 'Data dokumen berhasil diperbarui.');
    }

    public function destroy(DocumentReminder $reminder): RedirectResponse
    {
        $this->authorize('delete', $reminder);

        if ($reminder->attachment_path && Storage::disk('public')->exists($reminder->attachment_path)) {
            Storage::disk('public')->delete($reminder->attachment_path);
        }

        $reminder->delete();

        return redirect()
            ->route('dokumen', ['jenis' => 'semua'])
            ->with('success', 'Data dokumen berhasil dihapus.');
    }

    public function download(DocumentReminder $reminder)
    {
        $this->authorize('view', $reminder);

        if ($reminder->attachment_path && Storage::disk('public')->exists($reminder->attachment_path)) {
            return Storage::disk('public')->download($reminder->attachment_path, $reminder->attachment_name ?: null);
        }

        abort(404);
    }

    public function view(DocumentReminder $reminder)
    {
        $this->authorize('view', $reminder);

        if ($reminder->attachment_path && Storage::disk('public')->exists($reminder->attachment_path)) {
            $path = $reminder->attachment_path;
            $content = Storage::disk('public')->get($path);
            $mime = Storage::disk('public')->mimeType($path) ?? 'application/octet-stream';

            return response($content, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . ($reminder->attachment_name ?: basename($path)) . '"',
            ]);
        }

        abort(404);
    }
}