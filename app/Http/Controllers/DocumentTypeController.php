<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocumentTypeController extends Controller
{
    public function index(): View
    {
        $documentTypes = DocumentType::query()
            ->with('creator')
            ->orderBy('nama_jenis')
            ->get();

        return view('doc_type.index', compact('documentTypes'));
    }

    public function create(): View
    {
        return view('doc_type.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_jenis' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9][a-z0-9\s_-]*$/i',
                Rule::unique('document_types', 'nama_jenis'),
            ],
            'status' => ['required', Rule::in(['active', 'deactive'])],
        ]);

        DocumentType::create($validated + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('doc_type.index')
            ->with('success', 'Jenis dokumen berhasil ditambahkan.');
    }

    public function edit(DocumentType $doc_type): View
    {
        return view('doc_type.edit', ['documentType' => $doc_type]);
    }

    public function update(Request $request, DocumentType $doc_type): RedirectResponse
    {
        $validated = $request->validate([
            'nama_jenis' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9][a-z0-9\s_-]*$/i',
                Rule::unique('document_types', 'nama_jenis')->ignore($doc_type->id),
            ],
            'status' => ['required', Rule::in(['active', 'deactive'])],
        ]);

        $doc_type->update($validated);

        return redirect()
            ->route('doc_type.index')
            ->with('success', 'Jenis dokumen berhasil diperbarui.');
    }

    public function destroy(DocumentType $doc_type): RedirectResponse
    {
        $doc_type->delete();

        return redirect()
            ->route('doc_type.index')
            ->with('success', 'Jenis dokumen berhasil dihapus.');
    }
}