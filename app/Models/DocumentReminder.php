<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'nama_dokumen', 'no_dokumen', 'jenis_dokumen','pic_nama', 'pic_telpon', 'pic_external_nama', 'pic_external_telpon', 'penerbit_tujuan', 'tanggal_terbit', 'tanggal_expired', 'reminder_bulan', 'attachment_path', 'attachment_name'])]

class DocumentReminder extends Model
{
    protected function casts(): array
    {
        return [
            'tanggal_terbit' => 'date',
            'tanggal_expired' => 'date',
        ];
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'jenis_dokumen');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getJenisDokumenLabelAttribute(): string
    {
        if ($this->relationLoaded('documentType') && $this->documentType) {
            return $this->documentType->nama_jenis;
        }

        if (is_numeric($this->jenis_dokumen)) {
            $documentType = DocumentType::find($this->jenis_dokumen);

            return $documentType?->nama_jenis ?? (string) $this->jenis_dokumen;
        }

        $legacyDocumentType = DocumentType::query()
            ->where('nama_jenis', $this->jenis_dokumen)
            ->first();

        if ($legacyDocumentType) {
            return $legacyDocumentType->nama_jenis;
        }

        return $this->jenis_dokumen ? Str::headline((string) $this->jenis_dokumen) : '-';
    }

    public function internalPics()
    {
        return $this->belongsToMany(User::class, 'document_reminder_user')
                    ->withPivot(['nama', 'no_telpon'])
                    ->withTimestamps();
    }
}
