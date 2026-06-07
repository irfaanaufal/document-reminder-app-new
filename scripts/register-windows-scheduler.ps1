# Daftarkan Laravel scheduler ke Windows Task Scheduler.
# Aktif: Senin–Sabtu, 08:30–10:00 WIB (schedule:run tiap 1 menit dalam window itu).
#
# Buka PowerShell sebagai Administrator, lalu:
#   Set-ExecutionPolicy -Scope Process Bypass -Force
#   cd D:\PETER\KERJAAN\reminder-app\scripts
#   .\register-windows-scheduler.ps1

$ErrorActionPreference = "Stop"

$ProjectRoot = Resolve-Path (Join-Path $PSScriptRoot "..")
$BatPath = (Resolve-Path (Join-Path $PSScriptRoot "schedule-run.bat")).Path
$PhpPathFile = Join-Path $PSScriptRoot "php-path.txt"
$TaskName = "ReminderApp-LaravelScheduler"
$LogDir = Join-Path $ProjectRoot "storage\logs"
$StartTime = "08:30"
$RepeatMinutes = 1
$Duration = "01:30"

$php = Get-Command php -ErrorAction SilentlyContinue
if (-not $php) {
    Write-Host "ERROR: php tidak ditemukan di PATH." -ForegroundColor Red
    Write-Host "Pastikan Laragon/XAMPP aktif, atau isi manual file: $PhpPathFile"
    exit 1
}

$phpExe = $php.Source
Set-Content -Path $PhpPathFile -Value $phpExe -NoNewline
Write-Host "PHP: $phpExe"

if (-not (Test-Path $LogDir)) {
    New-Item -ItemType Directory -Path $LogDir -Force | Out-Null
}

cmd /c "schtasks /Query /TN `"$TaskName`" >nul 2>nul"
if ($LASTEXITCODE -eq 0) {
    Write-Host "Task '$TaskName' sudah ada. Menghapus dulu..."
    cmd /c "schtasks /Delete /TN `"$TaskName`" /F >nul"
}

cmd /c "schtasks /Create /TN `"$TaskName`" /TR `"`"$BatPath`"`" /SC WEEKLY /D MON,TUE,WED,THU,FRI,SAT /ST $StartTime /RI $RepeatMinutes /DU $Duration /F"

if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Gagal membuat scheduled task. Jalankan PowerShell sebagai Administrator." -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Berhasil! Task '$TaskName' aktif." -ForegroundColor Green
Write-Host "Window Windows Task Scheduler:"
Write-Host "  Hari   : Senin - Sabtu"
Write-Host "  Jam    : $StartTime - 10:00 WIB (ulang tiap $RepeatMinutes menit)"
Write-Host ""
Write-Host "Jadwal reminder di aplikasi (routes/console.php):"
Write-Host "  08:30 WIB -> reminders:queue  (Sen-Sab)"
Write-Host "  08:35 WIB -> reminders:send   (Sen-Sab)"
Write-Host ""
Write-Host "Cek log: $LogDir\scheduler.log dan scheduler-cron.log"
Write-Host "Tes manual: cd $ProjectRoot; php artisan schedule:list"
Write-Host "Hapus task: schtasks /Delete /TN $TaskName /F"
