@echo off
setlocal EnableExtensions
REM Dipanggil Windows Task Scheduler: Senin-Sabtu, 08:30-10:00 WIB (tiap 1 menit).
REM Development: php artisan schedule:work (hentikan saat tidak dipakai).

cd /d "%~dp0.."

if exist "%~dp0php-path.txt" (
    set /p PHP_EXE=<"%~dp0php-path.txt"
) else (
    set "PHP_EXE=php"
)

"%PHP_EXE%" artisan schedule:run >> storage\logs\scheduler-cron.log 2>&1
