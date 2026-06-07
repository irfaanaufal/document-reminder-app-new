<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'document_reminder_id',
    'recipient_phone',
    'recipient_name',
    'scheduled_for',
    'reminder_rule',
    'status',
    'attempt_count',
    'sent_at',
    'provider_response',
])]
class ReminderNotificationLog extends Model
{
    protected function casts(): array
    {
        return [
            'scheduled_for' => 'date',
            'sent_at' => 'datetime',
            'provider_response' => 'array',
        ];
    }

    public function documentReminder(): BelongsTo
    {
        return $this->belongsTo(DocumentReminder::class);
    }
}