<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role_id',
        'no_telpon', 'reset_otp', 'reset_otp_expires_at',
        'can_use_chatbot', 'fid', 'avatar_path',
    ];

    public const ROLE_SUPER_ADMIN = 1;
    public const ROLE_ADMIN = 2;
    public const ROLE_USER = 3;

    public function getNamaAttribute(): string
    {
        return $this->name;
    }

    public function setNamaAttribute(string $value): void
    {
        $this->name = $value;
    }

    public function getRoleAttribute(): int
    {
        return $this->role_id;
    }

    public function setRoleAttribute(int $value): void
    {
        $this->role_id = $value;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role_id' => 'integer',
            'reset_otp_expires_at' => 'datetime',
            'can_use_chatbot' => 'boolean',
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
        return self::roleOptions()[$this->role_id] ?? 'Unknown';
    }

    public function isSuperAdmin(): bool
    {
        return $this->role_id === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role_id, [self::ROLE_SUPER_ADMIN, self::ROLE_ADMIN], true);
    }

    public function isUser(): bool
    {
        return $this->role_id === self::ROLE_USER;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'fid', 'fid');
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

    public function assignedReminders(): BelongsToMany
    {
        return $this->belongsToMany(DocumentReminder::class, 'document_reminder_user');
    }

    public function canUseChatbot(): bool
    {
        return (bool) $this->can_use_chatbot;
    }

    public function chatSessions(): HasMany
    {
        return $this->hasMany(ChatSession::class);
    }

    public function userApplications(): HasMany
    {
        return $this->hasMany(UserApplication::class);
    }

    public function applications(): BelongsToMany
    {
        return $this->belongsToMany(Application::class, 'user_applications')
            ->withPivot('is_active', 'approved_by', 'approved_at')
            ->withTimestamps();
    }

    public function logNotifikasi(): HasMany
    {
        return $this->hasMany(LogNotifikasi::class);
    }
}
