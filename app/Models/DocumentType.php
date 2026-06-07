<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['nama_jenis', 'status', 'created_by', 'tipe_form'])]
class DocumentType extends Model
{
	public function creator(): BelongsTo
	{
		return $this->belongsTo(User::class, 'created_by');
	}
}