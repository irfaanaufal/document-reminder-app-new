<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['nama', 'email', 'password', 'role', 'no_telpon', 'is_active', 'reset_otp', 'reset_otp_expires_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPER_ADMIN = 1;
    public const ROLE_ADMIN = 2;
    public const ROLE_USER = 3;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'integer',
            'is_active' => 'boolean',
            'reset_otp_expires_at' => 'datetime',
        ];
    }

    public static function roleOptions(): array
    {
        return [
            self::ROLE_SUPER_ADMIN => 'Super Admin',
            self::ROLE_ADMIN => 'Admin',
            self::ROLE_USER => 'User',
        ];
    }

    public function roleLabel(): string
    {
        return self::roleOptions()[$this->role] ?? 'Unknown';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN], true);
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    public function documentReminders(): HasMany
    {
        return $this->hasMany(DocumentReminder::class);
    }

    public function accessChangesMade(): HasMany
    {
        return $this->hasMany(UserAccessChangeLog::class, 'actor_user_id');
    }

    public function accessChangesReceived(): HasMany
    {
        return $this->hasMany(UserAccessChangeLog::class, 'target_user_id');
    }

    public function assignedReminders()
    {
        return $this->belongsToMany(DocumentReminder::class, 'document_reminder_user');
    }
}
