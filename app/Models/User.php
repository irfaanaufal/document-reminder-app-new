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

#[Hidden(['password', 'remember_token', 'reset_otp'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const LEVELS_MANAGE_ALL = [1, 2, 3, 4, 7];

    public const LEVELS_DOC_TYPE = [1, 2, 3, 4, 7];

    public const LEVELS_DOC_ACCESS = [1, 2, 3, 4, 5, 6, 7, 8, 9];

    public const LEVEL_NEW_USER = 10;

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role_id',
        'no_telpon', 'fid', 'avatar_path',
    ];

    public function getNamaAttribute(): string
    {
        return $this->name;
    }

    public function setNamaAttribute(string $value): void
    {
        $this->name = $value;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role_id' => 'integer',
            'reset_otp_expires_at' => 'datetime',
        ];
    }

    public function roleLabel(): string
    {
        return $this->role?->name ?? 'Unknown';
    }

    public function level(): ?int
    {
        return $this->role?->level;
    }

    public function isAdmin(): bool
    {
        return in_array($this->level(), [1, 2, 3, 4], true);
    }

    public function isNewUserLevel(): bool
    {
        return (int) $this->level() === self::LEVEL_NEW_USER;
    }

    public function canManageAllDocuments(): bool
    {
        return in_array((int) $this->level(), self::LEVELS_MANAGE_ALL, true);
    }

    public function canAccessLogs(): bool
    {
        return $this->canManageAllDocuments();
    }

    public function canSeePasswordResetBell(): bool
    {
        return $this->canManageAllDocuments();
    }

    public function canManageDocumentTypes(): bool
    {
        return in_array((int) $this->level(), self::LEVELS_DOC_TYPE, true);
    }

    public function canAccessDocuments(): bool
    {
        return in_array((int) $this->level(), self::LEVELS_DOC_ACCESS, true);
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

    /**
     * Beri tahu semua pemegang hak akses Kelola Permintaan (level 1,2,3,4,7)
     * bahwa ada permintaan akses baru. Aturan seragam lintas aplikasi.
     */
    public function sendAccessRequestNotifications(): void
    {
        $app = Application::find(config('app.application_id'))
            ?? Application::where('slug', 'reminder')->first();

        $adminLevels = config('permissions.it_levels', [1, 2, 3, 4, 7]);

        User::whereHas('role', fn ($q) => $q->whereIn('level', $adminLevels))
            ->get()
            ->each(function ($admin) use ($app) {
                LogNotifikasi::create([
                    'user_id' => $admin->id,
                    'ticket_id' => null,
                    'actor_user_id' => $this->id,
                    'actor_name' => $this->name,
                    'recipient_type' => 'admin',
                    'action' => 'new_access_request',
                    'title' => 'Permintaan akses baru',
                    'message' => $this->name . ' (' . $this->username . ') mengajukan akses ke "' . ($app?->name ?? 'Reminder') . '".',
                    'status' => null,
                    'visible_in_bell' => true,
                ]);
            });
    }
}
