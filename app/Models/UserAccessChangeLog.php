<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'actor_user_id',
    'target_user_id',
    'old_role',
    'new_role',
    'old_is_active',
    'new_is_active',
])]
class UserAccessChangeLog extends Model
{
    protected function casts(): array
    {
        return [
            'old_role' => 'integer',
            'new_role' => 'integer',
            'old_is_active' => 'boolean',
            'new_is_active' => 'boolean',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
