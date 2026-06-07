# Hapus task scheduler Reminder App dari Windows Task Scheduler.

$TaskName = "ReminderApp-LaravelScheduler"

schtasks /Delete /TN $TaskName /F 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "Task '$TaskName' berhasil dihapus." -ForegroundColor Green
} else {
    Write-Host "Task '$TaskName' tidak ditemukan atau gagal dihapus." -ForegroundColor Yellow
}
