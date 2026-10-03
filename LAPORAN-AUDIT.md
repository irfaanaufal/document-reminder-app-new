# LAPORAN AUDIT — reminder-app

**Tanggal:** 3 Oktober 2026
**Cakupan:** Full 6 dimensi — Keamanan, Bug & Edge-case, Kualitas & Test, Performa, Deploy & Config, Dokumentasi
**Metode:** Read-only (baca kode, grep, `route:list`, `pint --test`, `php artisan test`, 4 subagent audit paralel + verifikasi manual temuan prioritas). Nilai secret `.env`/`.env production` tidak pernah dibuka. Tidak ada file kode yang diubah selama audit.
**Baseline:** 61/61 test lulus · 43 route (40 app + 3 framework) · 179 file tracked · Laravel 13.11.2 · PHP 8.3.30 · `pint --test` gagal 41 file.

---

## RINGKASAN EKSEKUTIF

| Severity | Jumlah | Keterangan |
|---|---|---|
| 🔴 CRITICAL | **0** | Tidak ditemukan RCE/SQLi/unauth admin/kebocoran secret ke repo |
| 🟠 HIGH | **12** | 4 keamanan/bug + 2 kualitas/test + 2 dokumentasi + 4 performa/deploy |
| 🟡 MEDIUM | **31** | termasuk 14 route tanpa test, fitur verifikasi email setengah mati |
| 🔵 LOW | **32** | konsistensi, dead code, hardening |
| **Total** | **75** | (duplikasi sudah digabung) |

### Top 10 prioritas

| # | ID | Temuan | Dampak jika dibiarkan |
|---|---|---|---|
| 1 | SEC-01 | `trustProxies(at: '*')` | Seluruh rate-limit/lockout login bisa di-bypass via header `X-Forwarded-For` |
| 2 | SEC-02 | Lampiran dokumen & avatar di disk `public` | File perusahaan bisa diakses tanpa login via URL langsung |
| 3 | DEP-01 | Lampiran tidak pernah di-backup berkala | Kehilangan server = hilang dokumen asli (PDF) |
| 4 | DEP-02 | `deploy.sh` tanpa `trap` + tanpa rollback runbook | Deploy gagal setengah jalan = kode baru + skema lama |
| 5 | BUG-01 | `<script>` sebelum `<!DOCTYPE>` di `/dokumen` | Halaman paling sering dipakai = Quirks Mode |
| 6 | BUG-02 | Avatar lama dihapus sebelum file baru tersimpan | Gagal upload = foto profil hilang permanen |
| 7 | PRF-01 | `/dokumen` load seluruh tabel + render 2× | Ribuan dokumen = response MB, makin lambat tiap bulan |
| 8 | PRF-02 | Index `tanggal_expired` tidak ada | Full scan + filesort di 6+ titik query |
| 9 | SEC-03 | Grup `/profile` & auth tanpa `applications.access` | User nonaktif masih bisa ganti password & upload avatar |
| 10 | QLT-01 | 14 route tanpa test (view/download/destroy/avatar/retry/OTP) | Jalur file & gerbang OTP tanpa jaring pengaman |

---

## A. KEAMANAN (18 temuan)

### 🟠 HIGH

**SEC-01 · Spoofing IP via `trustProxies(at: '*')` mematikan seluruh rate limiting**
- Lokasi: `bootstrap/app.php:16` — `$middleware->trustProxies(at: '*');` (diverifikasi manual ✅)
- Bukti: semua IP dianggap proxy tepercaya → `X-Forwarded-For` klien dipercaya → `Request::ip()` bisa diubah seenaknya. `LoginRequest::throttleKey()` (`LoginRequest.php:63`) dan `ThrottleRequests` memakai IP.
- Dampak: brute-force password tanpa batas (lockout 5× Breeze tidak berlaku), brute-force OTP 6 digit (throttle 5/menit tidak berlaku), semua `throttle:*` jadi hiasan.
- Rekomendasi: `at: ['<IP-proxy-nyata>']` atau env `TRUSTED_PROXIES`; tambah throttle per-akun untuk OTP.

**SEC-02 · Lampiran dokumen & avatar disimpan di disk `public` → dijangkau tanpa autentikasi**
- Lokasi: `DocumentReminderController.php:168,211,257,268` (`store(..., 'public')` — diverifikasi manual ✅), `ProfileController.php:90`, `config/filesystems.php:41-48`, `deployment/deploy.sh:116` (`storage:link --force`)
- Dampak: setelah symlink, `https://…/storage/document-reminders/<hash>.pdf` diservis web server langsung — **lewati** auth, middleware akses, policy, rate-limit. URL yang bocor valid selamanya; menonaktifkan user tidak mencabut akses filenya. (Mitigasi parsial: nama file = hash 40 karakter acak; validasi `mimes` berbasis sniffing konten.)
- Rekomendasi: pindahkan lampiran ke disk `private`, sajikan hanya via `doc.view`/`doc.download`; avatar boleh tetap `public`.

### 🟡 MEDIUM

**SEC-03 · Grup `/profile` dan seluruh route `auth` tanpa `applications.access`**
- Lokasi: `routes/web.php:96-100` (hanya `auth,throttle:30,1`), `routes/auth.php:53-74`
- Dampak: user yang dicabut aksesnya (force-logout di route lain) **masih bisa** ganti nama/email, ganti password, upload avatar, via sesi lama.
- Rekomendasi: tambahkan `applications.access` ke kedua grup.

**SEC-04 · Route `GET|PUT storage/{path}` terdaftar tanpa middleware apa pun**
- Lokasi: `config/filesystems.php:36` (`'serve' => true`), sumber: `FilesystemServiceProvider.php:111-125`
- Catatan mitigasi: `deploy.sh:114` menjalankan `route:cache` → di produksi kedua route ini **tidak terdaftar**; risiko nyata di dev/localhost dan bila `route:cache` di-clear. Penjaga: HMAC signature (butuh `APP_KEY`), tapi signature tidak mengikat isi body.
- Rekomendasi: `'serve' => false` pada disk `local` (tidak pernah dipakai aplikasi).

**SEC-05 · Seeder berisi 2 akun level IT dengan password `"password"` (file ter-track)**
- Lokasi: `database/seeders/DatabaseSeeder.php:34-52`
- Dampak: `db:seed --force` di server = kredensial admin yang diketahui publik. (Mitigasi: `deploy.sh` hanya `migrate --force`.)
- Rekomendasi: guard `app()->isProduction()` atau akun acak.

**SEC-06 · Reset/ganti password tidak mencabut sesi aktif lain**
- Lokasi: `NewPasswordController.php:60-63`, `PasswordController.php:23-25`; driver sesi = database (`config/session.php:21`)
- Dampak: korban yang mereset password karena akses mencurigai — sesi penyerang tetap hidup.
- Rekomendasi: hapus baris `sessions` milik user saat password berubah.

**SEC-07 · OTP reset password plaintext + tanpa lockout per-akun**
- Lokasi: `PasswordResetLinkController.php:36-42` (simpan), `:93` (verifikasi, tanpa counter gagal)
- Dampak: pembaca DB tahu OTP; brute force hanya terbentur 15 menit + throttle IP (yang bisa dibypass — SEC-01).
- Rekomendasi: hash OTP di DB, tambah lockout 5 gagal per akun.

**SEC-08 · `POST /login` tanpa middleware `throttle` route**
- Lokasi: `routes/auth.php:29`
- Dampak: password *spraying* lintas akun dari 1 IP tidak dibatasi (per-username 5× saja).
- Rekomendasi: `->middleware('throttle:10,1')`.

### 🔵 LOW

| ID | Temuan | Lokasi | Rekomendasi |
|---|---|---|---|
| SEC-09 | Policy `view` selalu `true` → semua user aktif boleh lihat/unduh SEMUA dokumen (IDOR by design) | `DocumentReminderPolicy.php:13-16` | Konfirmasi kebutuhan bisnis; jika bukan, scoping per-departemen |
| SEC-10 | `check-karyawan/{fid}` publik → enumerasi data karyawan (beda pesan "ada"/"sudah terdaftar") | `routes/auth.php:15-17` | Seragamkan pesan |
| SEC-11 | Tanpa Content-Security-Policy | `SecurityHeaders.php:15-20` | Tambah CSP minimalis |
| SEC-12 | `SESSION_SECURE_COOKIE` tidak diset | `config/session.php:172`, `.env.example` | Set `true` di produksi |
| SEC-13 | `attachment_name` mentah dari `getClientOriginalName()` → `download()` bisa 500 pada nama aneh | `DocumentReminderController.php:187,214,258` | Sanitasi saat upload |
| SEC-14 | Pencarian LIKE tanpa escape `%_` (bukan injeksi SQL — bound parameter) | `ReminderLogController.php:45-50` | `addcslashes($search, '%_')` |
| SEC-15 | `redirect()->back()` pasca-POST (open redirect terbatas, butuh CSRF) | `ReminderLogController.php:115,206,209,218` | `redirect()->route('logs.index')` |
| SEC-16 | `pendingOtpsCount` tampil ke semua user | `DashboardController.php:113`, `layouts/app.blade.php:74,109` | Batasi level admin |
| SEC-17 | PII nyata (email/telepon) di seeder; `storage/app/public` tanpa `.htaccess` anti-eksekusi | `DatabaseSeeder.php:38-51` | Data dummy + `php_flag engine off` |
| SEC-18 | Log tumbuh tanpa rotasi (`LOG_STACK=single`) | `.env.example:19`, `config/logging.php:57` | Pindah ke `daily` + `LOG_DAILY_DAYS` |

### ✅ Terverifikasi AMAN (jangan diulang pemeriksaan)
- **XSS**: 0 output `{!! !!}` di seluruh `resources/` — semua `{{ }}` + `escapeHtml()`/`@json()`.
- **SQL injection**: semua `*Raw` memakai konstan/binding; tanpa `DB::select`/`eval`/`unserialize`.
- **CSRF**: 22 form — semua POST/PATCH punya `@csrf`; avatar pakai `X-CSRF-TOKEN` meta.
- **Upload**: `store()` hash name (tanpa path traversal), `mimes` sniffing konten, tanpa SVG, ukuran dibatasi.
- **Mass assignment**: tanpa `$request->all()`; `$fillable` sempit; `validate()` hanya key ber-rule.
- **Secret hygiene**: `.env`/`.env production` tidak ter-track; `.env.example` semua nilai kosong; tak ada hardcoded secret di file ter-track.
- **Session**: regenerate saat login, invalidate saat logout, `same_site=lax`, `http_only`.

---

## B. BUG & EDGE-CASE (17 temuan)

### 🟠 HIGH

**BUG-01 · `/dokumen` dirender dalam Quirks Mode (`<script>` sebelum `<!DOCTYPE>`)**
- Lokasi: `resources/views/doc/read.blade.php:24-51` (blok `<script>` alpine:init) berada **sebelum** `<x-app-layout>` di baris 173 — diverifikasi manual ✅ (struktur file: `@php` → `<script>` → … → `<x-app-layout>`).
- Dampak: content non-whitespace sebelum DOCTYPE = Quirks Mode → perhitungan height/box-model beda antar-browser; layout `md:h-[calc(100vh-135px)]` rapuh; halaman paling sering dipakai tidak valid.
- Rekomendasi: pindahkan blok script ke dalam layout, atau daftarkan `Alpine.store('docColumns')` di `resources/js/app.js` (saat ini tidak mendaftarkan store apa pun).

**BUG-02 · Avatar lama dihapus SEBELUM file baru tersimpan & DB disimpan**
- Lokasi: `ProfileController.php:86-93` — diverifikasi manual ✅ (`delete()` → `store()` → `save()` tanpa guard).
- Dampak: `store()` gagal (disk penuh) atau `save()` gagal (DB down) → avatar lama hilang, path lama tetap di DB = broken image permanen.
- Rekomendasi: simpan file baru → `save()` sukses → baru hapus lama; cek `store() === false`.

### 🟡 MEDIUM

| ID | Temuan | Lokasi | Rekomendasi |
|---|---|---|---|
| BUG-03 | Nama bulan `translatedFormat('d F Y')` tampil **Inggris** — `Carbon::setLocale('id')` tidak pernah dipanggil (`config/app.php` locale tidak berdampak ke Carbon) | `AppServiceProvider.php`, `doc/show.blade.php:81,85,101,105` | `Carbon::setLocale(config('app.locale'))` di boot |
| BUG-04 | Tanpa folder `lang/` → semua pesan validasi bahasa Inggris di UI Indonesia | (tidak ada `lang/`) | Buat `lang/id/validation.php` dll. |
| BUG-05 | `verify-email.blade.php` & `confirm-password.blade.php` masih teks Inggris | `auth/verify-email.blade.php:3,8,18,27`, `confirm-password.blade.php:3,11,23` | Terjemahkan |
| BUG-06 | `store()`: lampiran di-`store()` **di luar** transaksi → file yatim bila transaksi gagal (berbeda `update()` yang sudah ada cleanup) | `DocumentReminderController.php:167-172` | `try/catch` + cleanup seperti `update()` |
| BUG-07 | Batas avatar JS `3*1024*1024` vs server `max:3000` → 3050 KB lolos JS, ditolak server | `profile/edit.blade.php:186` vs `ProfileController.php:80` | Samakan 3072 |
| BUG-08 | `destroy()`: file lampiran dihapus SEBELUM baris DB → gagal DB = data ada, file hilang, view/download 404 | `DocumentReminderController.php:242-246` | Hapus DB dulu / `DB::afterCommit` |
| BUG-09 | Fitur re-verifikasi email **setengah mati**: reset `email_verified_at` saat ganti email, tapi guard `instanceof MustVerifyEmail` selalu false (`User.php:5` import dikomentari — diverifikasi ✅), middleware `verified` 0 pemasangan → UI mati, user tak terverifikasi tetap akses penuh | `ProfileController.php:50`, `update-profile-information-form.blade.php:25` (diverifikasi ✅) | **Pilih: A) aktifkan penuh (kontrak + middleware) atau B) hapus fitur** |

### 🔵 LOW

| ID | Temuan | Lokasi |
|---|---|---|
| BUG-10 | `update()`: `store()` gagal → `false` tersimpan & file lama ikut terhapus | `DocumentReminderController.php:211-233` |
| BUG-11 | `view()` memakai sanitasi ASCII nama file (beda perilaku dengan `download()` yang pakai `fallbackName`) | `DocumentReminderController.php:273` |
| BUG-12 | Dokumen tanpa PIC internal **tidak pernah** dapat reminder (diam-diam `continue`) | `QueueDocumentReminders.php:62-65` |
| BUG-13 | OTP tidak dibersihkan saat kedaluwarsa/hanya diset saat sukses | `PasswordResetLinkController.php:39-42,99-102` |
| BUG-14 | Pivot `document_reminder_user` tanpa unique index (saat ini aman karena `sync()`) | `migrations/2026_06_05_102913:14-19` |
| BUG-15 | Variabel mati `const tipe` di handler `@change` (valid secara Alpine, hanya sampah) | `doc/create.blade.php:31-34`, `doc/edit.blade.php:32` |
| BUG-16 | `show.blade.php` memakai `avatar_url ?? profile_photo_url` yang tidak pernah ada sebagai accessor User → cabang avatar PIC selalu fallback inisial | `doc/show.blade.php:120-121` (dari audit QLT-M-07) |
| BUG-17 | `QueueDocumentReminders` tanpa `try/catch` → satu exception DB membatalkan run harian; `Carbon::parse` baris legacy bisa melempar | `QueueDocumentReminders.php:16-100,111` |

### ✅ Terverifikasi AMAN
File lampiran hilang → `abort(404)` bukan 500 · header download terjaga regex-nya · XHR avatar response cocok dengan JS · tanggal null tertangani di semua view · `internalPics` kosong tertangani · dedupe notifikasi via unique index cocok · **0 `dd/dump/TODO/FIXME`** di app/routes/resources/database.

---

## C. KUALITAS KODE & TEST (19 temuan)

### 🟠 HIGH

**QLT-01 · 14 route aplikasi TANPA test — termasuk seluruh jalur file & gerbang OTP**
- Tanpa test: `doc.view`, `doc.download`, `doc.destroy` (hapus DB + file), `doc_type.create|store|edit|update|destroy`, `profile.avatar`, `logs.retry` (kirim email nyata), `password.otp.show|verify`, `register.check-karyawan`, `verification.send`.
- Coverage: **26/40 route app (65%)** punya test.
- Rekomendasi prioritas: view/download happy+404, destroy (DB+file), avatar valid/invalid, `logs.retry` dengan `Mail::fake()`, OTP salah/kedaluwarsa/benar/tanpa-sesi.

**QLT-02 · Test yang bisa gagal justru lolos: `not->toContain()` multi-needle di Pest**
- Lokasi: `tests/Feature/DashboardStatsTest.php:144` — `->not->toContain('Expired Baru', 'Lifetime X')`
- Bukti: Pest loop per-needle; OppositeExpectation berhenti di needle pertama yang gagal → regresi filter exclude-expired tak terdeteksi.
- Rekomendasi: pecah jadi dua ekspektasi 1-needle.

**QLT-03 · Route `doc_type.destroy` mati ganda: tanpa UI + tanpa test** — diverifikasi ✅ (0 referensi `route('doc_type.destroy')` di semua view; `doc_type/index` hanya tombol Edit).
- Rekomendasi: tambah tombol hapus ter-guard **atau** hapus route.

**QLT-04 · AGENTS.md menyangkal notifikasi yang justru dijaga test** — `AGENTS.md:501,241-243` bilang "tanpa `LogNotifikasi::create`", padahal `User.php:160` memanggilnya dan `RegistrationTest.php:55-60` `assertDatabaseHas('log_notifikasi')`. Risiko: developer menghapus fitur agar cocok dokumen.

### 🟡 MEDIUM

| ID | Temuan | Lokasi |
|---|---|---|
| QLT-05 | AGENTS.md mendeskripsikan middleware alias `admin.it`/`superadmin` yang tidak ada (`CheckAdminIT`/`CheckSuperAdmin` tak wujud) | `AGENTS.md:403-404,426-429` vs `bootstrap/app.php:22-25` |
| QLT-06 | Gerbang OTP reset-password tanpa tes negatif (salah/kedaluwarsa/tanpa sesi); test `PasswordResetTest.php:25-38` pakai token literal `otp` | `tests/Feature/Auth/PasswordResetTest.php` |
| QLT-07 | 3 test "validation passes" hanya `assertSessionHasNoErrors` — tetap lolos saat `store()` crash | `DocumentReminderValidationTest.php:125,209,224` |
| QLT-08 | Duplikasi `doc/create` ↔ `doc/edit` = **89,6%** (LCS 412 baris; script ~170 baris identik); drift label nyata: "Interval Reminder" vs "Pilih Reminder" | `doc/create.blade.php`, `doc/edit.blade.php` |
| QLT-09 | Duplikasi struktural: `navigation` 42,5% (render menu 2×), `logs/index` 31,8%, `doc_type/index` 31,6%, `read` 26,2% | masing-masing file |
| QLT-10 | Dead view-data: `userApp` (profile), `calendarMonthLabel` (dashboard) | `ProfileController.php:26`, `DashboardController.php:105` |
| QLT-11 | Otorisasi dobel & 4 mekanisme campur (`@can` / `authorize()` / middleware `can:` / helper model) + `role:`+`can:` list identik | `routes/web.php:25,34`, `DocumentTypeController.php:15` |
| QLT-12 | 12/61 test smoke-only (hanya assertOk); 2 test rapuh terikat teks JS (`assertSee('let selectedUsers =', false)`) | `DocFormPagesTest.php:62,76` dll. |
| QLT-13 | Inventaris dead code 20+ item: `User::isAdmin()` (0 pakai, definisi `[1,2,3,4]` vs `canManageAllDocuments` `[1,2,3,4,7]`), relasi `User` 7× tak terpakai, model `UserAccessChangeLog`, `ReminderMailService` 3 metode, `tests/Pest.php` helper | detail di seksi kode |
| QLT-14 | Fitur verifikasi email setengah mati (**= BUG-09**, digabung) | — |

### 🔵 LOW

| ID | Temuan | Lokasi |
|---|---|---|
| QLT-15 | `pint --test` gagal 41 file; tanpa `pint.json`; `composer test` tidak memanggil Pint → tak ada gerbang gaya | project-wide |
| QLT-16 | Konsistensi: `auth()` 2× vs `Auth::` 19×; radius `rounded-xl` vs `rounded-2xl` kartu; 4 varian border netral; dark mode hilang di login/register; `@method` case campur; `__()` 55× tanpa `lang/` | berbagai view |
| QLT-17 | Logika 45 baris closure route (filter jenis/alias `spt`/`expired` tanpa test, tak bisa unit-test) | `routes/web.php:35-80` |
| QLT-18 | `public/storage` tidak ada lokal (symlink belum dibuat) → avatar/aset rusak lokal | `Test-Path public\storage` = False |
| QLT-19 | Statistik profil = data global (tanpa filter `user_id`) — pastikan disengaja | `ProfileController.php:29-36` |

---

## D. PERFORMA (9 temuan)

### 🟠 HIGH

**PRF-01 · `/dokumen` memuat & merender SELURUH tabel, 2× render, tanpa pagination**
- Lokasi: `routes/web.php:70-72` (`->get()`), banner query kedua `:63-68`; render ganda `read.blade.php:243-334` (kartu mobile) + `:357-426` (tabel desktop); DataTable `perPage:14` → >98% data tidak pernah tampil.
- Dampak: 1.000 dokumen ≈ 1,5–2 MB response + komputasi Carbon 2N per request.
- Rekomendasi: `paginate()` + `withQueryString()`; render satu skeleton dengan toggle CSS.

**PRF-02 · Index `tanggal_expired` TIDAK ADA — dipakai di 6+ titik WHERE/ORDER BY**
- Lokasi: `migrations/2026_05_25_000004:19` (tanpa index); query di `routes/web.php:59,71`, `DashboardController.php:34-46`, `ReminderLogController.php:20-32`, `ProfileController.php:30-36`.
- Dampak: full table scan + filesort tiap request dokumen/dashboard/logs/profile.
- Rekomendasi: `index(['tanggal_expired','id'])`.

### 🟡 MEDIUM

| ID | Temuan | Lokasi |
|---|---|---|
| PRF-03 | `logs`: `dueReminderCount` `->get()` lalu filter Carbon di memori (bukan `count()` SQL) | `ReminderLogController.php:20-32` |
| PRF-04 | Dashboard 6 query terpisah; 2 full-scan non-sargable (`LOWER(COALESCE(...)) LIKE`); kalender load seluruh tabel | `DashboardController.php:26-46,121-137` |
| PRF-05 | Profile 4× COUNT terpisah (3 tanpa index) | `ProfileController.php:29-37` |
| PRF-06 | `reminder_notification_logs`: ORDER BY `scheduled_for` tanpa index pendukung (index `(status,…)` tak kepakai); `whereDate` non-sargable; tabel tak pernah dibersihkan | `ReminderLogController.php:64-94` |
| PRF-07 | Tanpa pruning: `sessions` (driver database), `cache`, `reminder_notification_logs` tumbuh tanpa batas | `routes/console.php` |
| PRF-08 | N+1 email gagal: `whereIn(...)->get()` tanpa `with('documentReminder')` — justru saat SMTP tertekan | `SendDocumentReminders.php:187` |
| PRF-09 | Payload **semua user** (`id,nama,email`) di-embed ke form create/edit — PII ke client + payload linear | `doc/create.blade.php:367`, `doc/edit.blade.php:373` |

### 🔵 LOW
- **PRF-10**: Banner jalankan query tabel kedua (`routes/web.php:63-68`).
- **PRF-11**: Aksesori `jenis_dokumen_label` bisa N+1 diam-diam bila relasi belum di-load (`DocumentReminder.php:39-40`).

### ✅ Eager load yang sudah benar
`routes/web.php:40`, `DocumentReminderController.php:131`, `ReminderLogController.php:35`, `DashboardController.php:28`, `QueueDocumentReminders.php:23` — loop view aman; `@can` di loop hanya +1 query (cached guard).

---

## E. DEPLOY & CONFIG (11 temuan)

### 🟠 HIGH

**DEP-01 · Lampiran dokumen TIDAK pernah di-backup berkala**
- Lokasi: `deployment/deploy.sh:48-52` — `document-reminders` hanya disalin **sekali** saat deploy pertama; berikutnya tidak ada backup file sama sekali (hanya DB di `:73-86`).
- Dampak: kehilangan server = kehilangan PDF asli perusahaan (tidak ada di git, tidak ada di dump SQL).
- Rekomendasi: sync berkala offsite (rsync/S3) + retensi; masukkan checklist deploy.

**DEP-02 · Tanpa rollback runbook + `set -euo pipefail` tanpa `trap`**
- Lokasi: `deployment/deploy.sh:7-10,13` — rollback hanya di komentar; `trap` tidak ada → kegagalan di `npm ci` (`:100`) / `view:cache` (`:115`) / `migrate --force` (`:121`) menghentikan script setelah `.env` disalin & cache dibakar = kondisi campuran. Ada migrasi destruktif (`drop`, `renameColumn`, `dropIfExists`).
- Rekomendasi: `trap ERR` → rollback; `php artisan down`/`up`; dokumentasi restore SQL + folder.

### 🟡 MEDIUM

| ID | Temuan | Lokasi |
|---|---|---|
| DEP-03 | Backup DB: tanpa `--single-transaction`, tanpa rotasi, tanpa kompresi, tanpa tes restore | `deploy.sh:84-86` |
| DEP-04 | Cron scheduler jalan sebagai **root** (web = `www`) → log scheduler bentrok ownership; output dibuang `>/dev/null` → kegagalan tak terlihat; `deployment/cron-reminder` duplikat | `deploy.sh:127-133` |
| DEP-05 | `.env.example` kurang: **`APP_APPLICATION_ID`** (dipakai 5 titik!), **`SESSION_SECURE_COOKIE`**, `DB_*` dikomentari (default sqlite → `deploy.sh:78` FATAL bila kosong) | `.env.example` vs `config/*.php` |
| DEP-06 | Mail reminder **SYNC tanpa timeout** (`'timeout' => null`); infra queue (`jobs`, `QUEUE_CONNECTION=database`) dead code — tanpa retry otomatis; `withoutOverlapping` lock 24 jam bisa menahan run | `ReminderMailService.php:112`, `config/mail.php:47` |
| DEP-07 | `config:cache`/`route:cache` ada di deploy ✓; tapi tanpa `php artisan down`, tanpa health-check otomatis, edit `.env` setelah script = cache basi | `deploy.sh:113-121,142` |
| DEP-08 | `trustProxies` hardcoded tak terkontrol env (**= SEC-01**, digabung) | `bootstrap/app.php:16` |

### 🔵 LOW
- **DEP-09**: Git hygiene **BERSIH** ✓ (179 file, tanpa log/cache/build ter-track; 4 file jadwal = changeset ditunda, **bukan temuan** — server yang deploy dari `dfc72a1` masih jadwal 08:30/08:35 tanpa filter hari).
- **DEP-10**: `deployment/.env.production` template yatim tak direferensikan; `scripts/php-path.txt` ter-track (noise); `vite.config.js:16` hardcode IP lokal; login bergantung **SweetAlert2 CDN** (`login.blade.php:115`).
- **DEP-11**: Local status (audit `php artisan about`): Config NOT cached, Routes NOT cached (dev wajar); bundle sehat (JS 124,5 KB, CSS 72,6 KB, tanpa source map).

---

## F. DOKUMENTASI — AGENTS.md (17 klaim tidak akurat)

**Ringkas** (detail per baris di laporan kerja):
1. `:12,:19-21` — klaim "Inertia React" padahal **Blade + Alpine + Tailwind** (`resources/js/app.js`).
2. `:35,:85` — roles string `superadmin/admin/user` padahal **level numerik** (`role_id=10` untuk user baru).
3. `:129` — `Karyawan::tickets()` **tidak ada**.
4. `:139-140` — `isSuperAdmin()` tidak ada; `isAdmin()` ada tapi **0 pemakaian** dan beda definisi dari `canManageAllDocuments()`.
5. `:172-179,:403-404` — alias middleware deskripsi vs nyata (`role` + `applications.access` saja).
6. `:413` — "check-karyawan tanpa middleware" padahal `guest` + `throttle:10,1`.
7. `:426-449` — route fiktif (`/admin/applications`, `/my-requests`, `/global-monitor`, dll.) + `ApplicationController` tak wujud.
8. `:465-489` — `Login.jsx` tidak ada.
9. `:498` — "bypass admin" **kontradiksi internal** dengan `:216` dan kode (`AuthenticatedSessionController.php:37-47`).
10. `:501,:241-243` — **membantah notifikasi yang dijaga test** (**QLT-04**).
11. `:508` — "SQL aggregate belum" padahal sudah (`DashboardController.php:42,126,37,137`).
12. `:77-88` — "Step 2 form terpisah" padahal di dalam halaman login.
13. `:160,:165` — sudah diperbaiki sesi ini (toggleAccess ✓).
14. **0 penyebutan** `test`/`Pest`/`pint`/`route:list` padahal ada 61 test.

---

## G. CHECKLIST SERVER — blok SSH read-only

> **Aturan:** semua perintah hanya membaca. Jangan pernah tampilkan isi `.env` (tanpa `cat`/`grep` nilai). Jangan `rm`/`migrate`/`config:cache`. Tempel hasilnya ke saya.

```bash
# 1) Commit ter-deploy (harus: dfc72a1 atau lebih baru)
cd /path/ke/reminder-app && git log --oneline -3 && git status --short | head -20

# 2) Scheduler & cron
crontab -l 2>/dev/null | grep -i artisan; php artisan schedule:list

# 3) Kesehatan app (read-only)
php artisan about --only=environment,cache 2>/dev/null | head -30; php artisan migrate:status | tail -15

# 4) Kehadiran file — HANYA daftar nama, JANGAN cat isinya
ls -la .env* 2>/dev/null; ls -la public/storage 2>/dev/null; ls -la public/build/manifest.json 2>/dev/null

# 5) Sampah deploy & backup
ls -d ../*.bak-* ../*.replaced-* ../*.backup* 2>/dev/null; ls -lht /root/backup_*.sql 2>/dev/null | head -5

# 6) Lampiran & log (ukuran/jumlah saja)
du -sh storage/app/public/document-reminders 2>/dev/null; ls -la storage/logs/ | head -10
grep -c '\[ERROR\]' storage/logs/laravel.log 2>/dev/null || echo "log kosong/rotasi"

# 7) Ruang disk
df -h / | tail -1
```

**Yang DICARI:**
- `git log` = `dfc72a1`+ (bukan `60f9243` lama) · cron `schedule:run` ada & menit-an
- `.env` production ada; **tidak ada** `.env production`/`*.bak-*` menyusutkan folder
- `public/storage` symlink ada; `public/build/manifest.json` ada (npm build jalan)
- backup DB ada & < 7 hari; lampiran terukur (untuk REK baseline backup)
- `laravel.log` ERROR count wajar; disk lega

---

## H. KEPUTUSAN YANG MENUNGGU ANDA

| # | Keputusan | Opsi |
|---|---|---|
| 1 | **BUG-09 / fitur verifikasi email** | **A)** Aktifkan penuh (implement `MustVerifyEmail` + middleware `verified`) · **B)** Hapus fiturnya (reset email + guard + form + 4 route + 3 test) |
| 2 | **14 route tanpa test** | Perbaiki sekarang (batch test) · Jadwalkan (minimal 6 kritis: view/download/destroy/avatar/retry/OTP) |
| 3 | **pint --fix** | Jalankan sekali di commit khusus "style: apply pint" agar diff fix ke depan bersih |
| 4 | **Batch perbaikan** | Susun batch (lihat I) — setiap batch dieksekusi setelah persetujuan terpisah |

## I. USULAN BATCH PERBAIKAN (menunggu persetujuan per batch)

| Batch | Isi | Severity terproteksi | Risiko perubahan |
|---|---|---|---|
| **B1 — Keamanan inti** | SEC-01 trustProxies, SEC-03 `applications.access` di profile+auth, SEC-08 throttle login, SEC-06 cabut sesi saat ganti password | 2 HIGH + 3 MEDIUM | Rendah-menengah (perlu tes login/profil) |
| **B2 — File & data safety** | SEC-02 lampiran ke disk private, BUG-02 urutan avatar, BUG-06/08 urutan simpan/hapus, DEP-01 skrip backup lampiran | 3 HIGH + 3 MEDIUM | Menengah (butuh migrasi path lama + storage:link) |
| **B3 — Cepat & murah** | PRF-02 index `tanggal_expired`, BUG-03 Carbon locale, BUG-07 batas avatar, BUG-14 unique index, SEC-13 sanitasi nama file | 2 HIGH (PRF) + MEDIUM | Rendah |
| **B4 — Halaman dokumen** | BUG-01 script ke app.js, PRF-01 pagination | 2 HIGH | Menengah (butuh tes sort/filter) |
| **B5 — Test & gate** | QLT-01 6 test kritis, QLT-02 pecah toContain, QLT-07 perkuat 3 test, pint --fix + gerbang | 3 HIGH + MEDIUM | Rendah (kode test) |
| **B6 — Deploy hardening** | DEP-02 trap+runbook, DEP-03 backup DB proper, DEP-04 cron user www, DEP-05 .env.example | 2 HIGH + MEDIUM | Menengah (di server — perlu izin eksekusi server) |
| **B7 — Keputusan & kebersihan** | BUG-09 (opsi A/B), QLT-03 doc_type.destroy, dead code QLT-13, AGENTS.md F, QLT-08 partial form | MEDIUM | Keputusan produk dulu |
| **B8 — Performa lanjutan** | PRF-03..09 agregat SQL, pruning, queue mail, PII user payload | MEDIUM | Menengah-tinggi |

---

*Laporan disusun oleh audit otomatis 4 dimensi paralel + verifikasi manual 6 temuan prioritas (SEC-01, SEC-02, BUG-01, BUG-02, BUG-09-guard, orphan-route). Tidak ada kode yang diubah selama audit. File ini adalah satu-satunya artefak yang ditulis.*
