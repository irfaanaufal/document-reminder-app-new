# Sistem Terintegrasi — Arsitektur & Alur

## 1. Arsitektur Umum

```
┌─────────────────────────────────────────────────────────────────┐
│                      IT System (Pusat)                          │
│  - Mengelola user, role (superadmin/admin/user)                 │
│  - Mengelola aktivasi aplikasi (user_applications)              │
│  - Menerima & mengirim notifikasi antar sistem                 │
│  - Database: MySQL (main_db)                                   │
│  - Laravel 13 + Inertia React + Vite                           │
└─────────────────────────────────────────────────────────────────┘
         ▲                          │
         │ notifikasi               │ request akses
         │ aktivasi                 │
         ▼                          ▼
┌─────────────────────┐  ┌─────────────────────┐  ┌─────────────────────┐
│  Absensi Meeting    │  │     Reminder        │  │     Shortly         │
│  (Laravel + React)  │  │  (Laravel + React)  │  │  (Laravel + React)  │
└─────────────────────┘  └─────────────────────┘  └─────────────────────┘
```

**Pola:** Setiap sistem "child" hanya melayani request akses dari user. Aktivasi dilakukan **hanya** dari IT System oleh admin/superadmin.

---

## 2. Database & Model

### Tabel Shared (via API/Notifikasi, bukan join)

| Tabel di IT System | Kegunaan |
|---|---|
| `users` | User terdaftar, memiliki `fid`, `role_id` |
| `roles` | `superadmin`, `admin`, `user` |
| `karyawans` | Data karyawan dari admin, key = `fid` |
| `applications` | Daftar aplikasi terintegrasi |
| `user_applications` | Pivot aktivasi user ⇔ aplikasi |
| `log_notifikasi` | Notifikasi antar sistem |

### Struktur `user_applications`

```php
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->foreignId('application_id')->constrained()->cascadeOnDelete();
$table->boolean('is_active')->default(false);
$table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
$table->timestamp('approved_at')->nullable();
$table->timestamps();
```

### Struktur `log_notifikasi`

```php
$table->id();
$table->foreignId('user_id')->constrained()->cascadeOnDelete();
$table->foreignId('ticket_id')->nullable()->constrained()->nullOnDelete();
$table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
$table->string('actor_name')->nullable();
$table->string('recipient_type', 20);   // 'user' | 'admin'
$table->string('action', 40);           // 'new_access_request', dll
$table->string('title');
$table->text('message');
$table->string('status')->nullable();
$table->boolean('visible_in_bell')->default(true);
$table->timestamp('read_at')->nullable();
$table->timestamps();
```

---

## 3. Alur Registrasi User Baru

### Step 1: Input FID
```
User masukkan FID → GET /register/check-karyawan/{fid}
  ↓ sukses
Tampilkan data karyawan (nama, divisi)
  ↓
Step 2: Form registrasi (nama sudah terisi otomatis)
  ↓ submit
POST /register → RegisteredUserController@store
  ↓
User dibuat dengan role_id = 'user'
  ↓
Redirect ke login
```

### Endpoint Check Karyawan

**Route (auth.php):**
```php
Route::get('register/check-karyawan/{fid}', [RegisteredUserController::class, 'checkKaryawan'])
    ->name('register.check-karyawan');
```

**Controller:**
```php
public function checkKaryawan($fid): JsonResponse
{
    $karyawan = Karyawan::where('fid', $fid)->first();
    if (!$karyawan) return response()->json(['success' => false, 'message' => '...'], 404);

    $linked = User::where('fid', $fid)->exists();
    if ($linked) return response()->json(['success' => false, 'message' => '...'], 400);

    return response()->json([
        'success' => true,
        'karyawan' => [
            'fid' => $karyawan->fid,
            'nama_karyawan' => $karyawan->nama_karyawan,
            'divisi' => $karyawan->divisi ?? 'Umum',
        ]
    ]);
}
```

### Model Karyawan
```php
class Karyawan extends Model
{
    protected $table = 'karyawans';
    protected $primaryKey = 'fid';
    public $incrementing = false;
    protected $keyType = 'string';

    public function user(): HasOne { return $this->hasOne(User::class, 'fid', 'fid'); }
    public function tickets(): HasMany { ... }
}
```

### Model User
```php
class User extends Authenticatable
{
    public function karyawan(): BelongsTo { return $this->belongsTo(Karyawan::class, 'fid', 'fid'); }
    public function role(): BelongsTo { return $this->belongsTo(Role::class); }
    public function isSuperAdmin(): bool { return $this->role?->name === 'superadmin'; }
    public function isAdmin(): bool { return in_array($this->role?->name, ['superadmin', 'admin']); }
    public function userApplications(): HasMany { return $this->hasMany(UserApplication::class); }
    public function applications(): BelongsToMany { ... }
    public function logNotifikasi(): HasMany { return $this->hasMany(LogNotifikasi::class); }
}
```

---

## 4. Alur Aktivasi Aplikasi

### Request Akses (dari sisi user di IT System)

> **OUT-OF-SCOPE di reminder-app:** halaman "Akses Saya" + `POST /applications/request` **dihapus**.
> User hubungi admin langsung; register → status "hubungi admin untuk aktivasi".
> Middleware force-logout + redirect login dengan Swal `activation_needed`.

```
(Register/login sudah menampilkan pesan aktivasi)
  ↓
Admin aktifkan via DB / ProfileController::toggleAccess → is_active = true
```

### Aktivasi oleh Admin (di IT System)
```
Admin aktifkan via DB / ProfileController::toggleAccess
  ↓
user_applications.is_active = true
```

### Middleware Check Akses

**Registrasi middleware di `bootstrap/app.php`:**
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'applications.access' => \App\Http\Middleware\CheckITWorkflowAccess::class,
    ]);
})
```

**File middleware:**
```php
class CheckITWorkflowAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) return $next($request);
        // Aturan seragam lintas aplikasi: TANPA bypass admin —
        // hanya baris aplikasi ini dengan is_active=true yang diizinkan.
        $appId = config('app.application_id');
        $hasAccess = $user->userApplications()
            ->where('application_id', $appId)
            ->where('is_active', true)
            ->exists();

        if (!$hasAccess) {
            // Force-logout + redirect login (hindari loop guest middleware)
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->withErrors([
                'activation_needed' => 'Akun belum diaktifkan. Hubungi tim IT',
            ]);
        }

        return $next($request);
    }
}
```

### Login Activation Check

**Di `AuthenticatedSessionController@store`:**
```php
// Aturan seragam: TANPA bypass admin — semua akun (termasuk admin)
// harus punya baris AKTIF; baris baru selalu dibuat inactive.
$app = Application::find(config('app.application_id')) ?? Application::firstOrCreate(['slug' => 'reminder'], [...]);
$userApp = UserApplication::where('user_id', $user->id)
    ->where('application_id', $app->id)->first();

if (!$userApp) {
    // Baris belum ada (akun lama) → buat inactive + notifikasi admin
    $userApp = UserApplication::create([... 'is_active' => false]);
    $user->sendAccessRequestNotifications();
}

if (!$userApp->is_active) {
    Auth::guard('web')->logout();
    // Redirect dengan error activation_needed (pesan seragam)
    return redirect()->route('login')->withErrors([
        'activation_needed' => 'Akun belum diaktifkan. Hubungi tim IT',
    ]);
}
```

---

## 5. Alur Notifikasi (Real-time + Bell)

> **OUT-OF-SCOPE** di reminder-app: endpoint `/api/notifications*`, `LogNotifikasiController`,
> dan writers `LogNotifikasi::create` pada request-access/login **dihapus sengaja**
> (keputusan produk). Model `LogNotifikasi` + migrasi tetap ada (shared `it-system`).
> Bagian di bawah = referensi desain untuk sistem child, bukan kode aktif di repo ini.

### Backend — Membuat Notifikasi

Gunakan `LogNotifikasi::create()` setiap kali ada event yang perlu notif:

```php
use App\Models\LogNotifikasi;
use App\Models\User;

$adminUsers = User::whereHas('role', fn($q) => $q->whereIn('name', ['superadmin', 'admin']))->get();
$adminUsers->each(function ($admin) use ($user, $app) {
    LogNotifikasi::create([
        'user_id' => $admin->id,
        'ticket_id' => null,              // null jika bukan dari ticket
        'actor_user_id' => $user->id,
        'actor_name' => $user->name,
        'recipient_type' => 'admin',
        'action' => 'new_access_request',  // identifier unik
        'title' => 'Permintaan akses baru',
        'message' => $user->name . ' mengajukan akses ke "' . $app->name . '".',
        'status' => null,
        'visible_in_bell' => true,
    ]);
});
```

### Frontend — Fetch & Tampilkan

```javascript
// Di AuthenticatedLayout.jsx
const fetchNotifications = () => {
    axios.get('/api/notifications')
        .then(res => setNotifications(res.data.map(n => ({
            ...n,
            msg: n.message ?? n.msg,
            time: new Date(n.created_at ?? n.time),
            read: n.read === true,
        }))))
        .catch(console.error);
};
```

### Frontend — Click Handler

```javascript
const handleNotifClick = (n) => {
    if (!n.read) markOneRead(n.id);
    setNotifOpen(false);
    setIsMobileNotifModalOpen(false);

    let path;
    if (n.recipient_type === 'admin') {
        if (n.action === 'new_ticket') path = '/admin/inbox';
        else if (n.action === 'new_access_request') path = '/admin/applications/requests';
        else path = n.ticket_id ? `/admin/tickets/${n.ticket_id}` : '/admin/inbox';
    } else {
        switch (n.action) {
            case 'created': path = '/my-requests'; break;
            case 'ticket_taken':
            case 'entered_review':
            case 'entered_to_do':
            case 'entered_in_progress': path = '/global-monitor'; break;
            case 'entered_testing':
            case 'revision_requested': path = '/my-requests'; break;
            case 'approved':
            case 'uat_approved': path = `/tickets/${n.ticket_id}`; break;
            default: path = n.ticket_id ? `/tickets/${n.ticket_id}` : '/global-monitor';
        }
    }
    router.visit(path);
};
```

### API Routes Notifikasi

> **OUT-OF-SCOPE** — contoh referensi, tidak ada di routes aktif.

```php
Route::get('/api/notifications', [LogNotifikasiController::class, 'index']);
Route::patch('/api/notifications/read-all', [LogNotifikasiController::class, 'markAllRead']);
```

### Mark One Read (Backend)
```php
public function markAllRead(Request $request): JsonResponse
{
    $query = LogNotifikasi::query()
        ->where('user_id', $request->user()->id)
        ->where('visible_in_bell', true)
        ->whereNull('read_at');

    if ($request->filled('ids')) {
        $query->whereIn('id', (array) $request->input('ids'));
    }

    $query->update(['read_at' => now()]);
    return response()->json(['message' => '...']);
}
```

---

## 6. Alur untuk Sistem Child (Absensi Meeting, Reminder, Shortly)

### Yang Perlu Dibuat di Setiap Sistem Child:

| Komponen | File |
|---|---|
| **Database** | Migration `user_applications` (sama struktur) |
| **Model** | `UserApplication`, `Application`, `LogNotifikasi` |
| **Registrasi** | 2-step FID check + form (sama persis) |
| **Login** | Check activation via `user_applications` |
| **Middleware** | Check akses aplikasi spesifik (slug masing-masing) |
| **SweetAlert** | Activation error sekali per session (`sessionStorage`) |
| **Halaman Request** | **OUT-OF-SCOPE** di reminder-app (halaman Akses Saya dihapus) |
| **Notifikasi** | OUT-OF-SCOPE di reminder-app (lihat §5) — target desain sistem child: notif ke admin IT System |

### Flow Registrasi di Sistem Child:

```
1. User buka sistem child (e.g., Absensi Meeting)
2. Register → input FID → GET /register/check-karyawan/{fid} (panggil API IT System)
   → Tapi lebih baik: setiap sistem child punya DB karyawans sendiri
     (sinkron dari admin tiap malam via cron/SQL)
3. Registrasi selesai → user terdaftar di sistem child
4. Login → cek user_applications untuk slug sistem child
   → if not active → SweetAlert "Hubungi tim IT"
5. User hubungi admin (halaman Akses Saya = out-of-scope di reminder-app)
6. Admin aktifkan via DB → is_active = true
7. User bisa login
```

### Integrasi Notifikasi Antar Sistem

**Cara 1: Satu Database bersama** (disarankan)
- Semua sistem pakai 1 database MySQL (`main_db`)
- Tabel `log_notifikasi`, `users`, `user_applications`, `applications` dibagikan
- Setiap sistem hanya punya tabel spesifiknya sendiri (e.g., `meetings`, `reminders`, `short_urls`)

**Cara 2: API Gateway**
- Sistem child panggil API IT System untuk buat notifikasi:
```
POST /api/external/notifications
  Body: { user_id, action, title, message, recipient_type }
  Auth: API Token bersama
```

---

## 7. Routing Structure (Referensi)

> Catatan: blok API notifications di bawah **OUT-OF-SCOPE** di reminder-app (sudah dikomentari).

### Middleware Stack
```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin.it' => \App\Http\Middleware\CheckAdminIT::class,
        'superadmin' => \App\Http\Middleware\CheckSuperAdmin::class,
        'applications.access' => \App\Http\Middleware\CheckITWorkflowAccess::class,
    ]);
})
```

### Route Groups
```php
// Public
Route::get('register/check-karyawan/{fid}', ...);  // tanpa middleware

// Guest
Route::middleware('guest')->group(function () {
    Route::get('register', ...);
    Route::post('register', ...);
    Route::get('login', ...);
    Route::post('login', ...);
});

// Auth + verified (tanpa check aplikasi) — contoh referensi
Route::middleware(['auth', 'verified'])->group(function () {
    // GET /applications + POST /applications/request → OUT-OF-SCOPE di reminder-app (dihapus)
    Route::get('/admin/applications/requests', [ApplicationController::class, 'requests']);
    Route::patch('/applications/toggle', [ApplicationController::class, 'toggleAccess']);
    Route::get('/admin/applications', [ApplicationController::class, 'manage']);
    Route::post('/admin/applications', [ApplicationController::class, 'store']);
});

// Auth + verified + aplikasi aktif
Route::middleware(['auth', 'applications.access'])->group(function () {
    // Semua halaman internal (dashboard, tickets, history, dll)
    Route::get('/dashboard', ...)->name('dashboard');
    Route::get('/my-requests', ...)->name('my-requests');
    Route::get('/global-monitor', ...)->name('global-monitor');

    // Admin only
    Route::middleware('admin.it')->group(function () {
        Route::get('/admin/inbox', ...);
        Route::get('/admin/kanban', ...);
        Route::get('/admin/tickets/{id}', ...);
    });

    // Superadmin only
    Route::middleware('superadmin')->group(function () {
        Route::get('/admin/systems', ...);
        Route::get('/admin/roles-permissions', ...);
    });

    // API — OUT-OF-SCOPE di reminder-app (dihapus sengaja; lihat §5)
    // Route::prefix('api')->group(function () {
    //     Route::get('/notifications', [LogNotifikasiController::class, 'index']);
    //     Route::patch('/notifications/read-all', [LogNotifikasiController::class, 'markAllRead']);
    // });
});
```

---

## 8. SweetAlert Activation (Login Page)

```javascript
// Login.jsx
import Swal from 'sweetalert2';

const submit = (e) => {
    e.preventDefault();
    post(route('login'), {
        onError: (errs) => {
            if (errs.activation_needed) {
                const alreadyShown = sessionStorage.getItem('activation_alert_shown');
                if (!alreadyShown) {
                    sessionStorage.setItem('activation_alert_shown', '1');
                    Swal.fire({
                        icon: 'warning',
                        title: 'Akun Belum Diaktifkan',
                        text: errs.activation_needed,
                        confirmButtonText: 'Hubungi Tim IT',
                        confirmButtonColor: '#6366f1',
                    });
                }
            }
            reset('password');
        },
    });
};
```

---

## 9. Summary Checklist untuk Sistem Baru

- [x] Migration `user_applications` & `applications`
- [x] Model `UserApplication`, `Application`, `LogNotifikasi`
- [x] FID check endpoint + 2-step register
- [x] Login activation check (bypass admin)
- [x] Middleware check akses aplikasi
- [x] Halaman "Akses Saya" dengan request access → **out-of-scope** (halaman dihapus; middleware force-logout + Swal login)
- [x] Notifikasi ke admin saat request access → **out-of-scope** (request dicatat, tanpa `LogNotifikasi::create`)
- [x] SweetAlert activation — perilaku aktual: Swal **setiap kali** login gagal; sessionStorage 1×/session **belum** diimplementasi
- [x] API notifikasi (index, read-all) → **out-of-scope** (endpoint dihapus sengaja)
- [x] Role-based redirect setelah login → **out-of-scope** (redirect universal ke `dashboard`; gating via `role:` middleware)

### Catatan cleanup terakhir
- Dead code dihapus: view modal/bulan partial, 6 komponen Breeze, `Console/Kernel`, route stub `admin.dashboard`/`user.dashboard`, `DocumentReminderIntervals`, `chart.js`, nav link "Akses Saya", halaman `applications` + `ApplicationController`
- Dashboard: hanya konservatif (buang `$user` tak terpakai + merge query) — **full SQL aggregate M2 belum** (risiko beda angka KPI)
