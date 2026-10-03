#!/bin/bash
# ============================================================
# REMINDER-APP DEPLOYMENT — aaPanel (sindangasih-makmur.com)
# Jalankan di server SEBAGAI ROOT, SETELAH project di-upload ke
# /www/wwwroot/sindangasih-makmur.com/reminder-app
#
# Prasyarat manual (lihat runbook FASE 3):
#   1. mv reminder-app reminder-app-backup   (folder lama = backup)
#   2. upload project lokal -> reminder-app  (KECUALI .env & node_modules)
#   3. bash -n deployment/deploy.sh && bash deployment/deploy.sh
# ============================================================

set -euo pipefail

ROOT=/www/wwwroot/sindangasih-makmur.com
APP_DIR="$ROOT/reminder-app"
BACKUP_DIR="$ROOT/reminder-app-backup"
PHP83=/www/server/php/83/bin/php
WEB_USER=www
EXPECTED_URL=http://sindangasih-makmur.com/reminder-app

echo "============================================"
echo "  REMINDER-APP DEPLOYMENT (aaPanel)"
echo "============================================"

# ---- 0. PREFLIGHT ------------------------------------------------------------
echo ""
echo "[0/8] Preflight..."
[ -d "$APP_DIR" ] || { echo "FATAL: $APP_DIR tidak ada (project belum diupload?)"; exit 1; }
[ -x "$PHP83" ]  || { echo "FATAL: $PHP83 tidak ada"; exit 1; }
"$PHP83" -v | head -1
command -v node  >/dev/null 2>&1 && node -v  || echo "CATATAN: node tidak ada -> build harus sudah diupload (public/build)"
command -v composer >/dev/null 2>&1 && composer -V | head -1 || echo "CATATAN: composer tidak ada -> vendor harus sudah diupload"

# ---- 1. BAWA DATA PENTING DARI BACKUP (jika belum ada) -----------------------
echo ""
echo "[1/8] Carry-over dari folder backup..."
if [ ! -f "$APP_DIR/.env" ]; then
  if [ -f "$BACKUP_DIR/.env" ]; then
    cp -a "$BACKUP_DIR/.env" "$APP_DIR/.env"
    echo "  + .env disalin dari backup (LALU edit APP_URL & APP_DEBUG — lihat langkah 2)"
  else
    echo "  FATAL: .env tidak ada di project maupun backup"; exit 1
  fi
else
  echo "  = .env sudah ada di project, dipertahankan"
fi
if [ ! -d "$APP_DIR/storage/app/public/document-reminders" ] && [ -d "$BACKUP_DIR/storage/app/public/document-reminders" ]; then
  mkdir -p "$APP_DIR/storage/app/public"
  cp -a "$BACKUP_DIR/storage/app/public/document-reminders" "$APP_DIR/storage/app/public/"
  echo "  + document-reminders (lampiran asli) disalin dari backup"
fi
[ -d "$APP_DIR/storage/app/public/document-reminders" ] \
  && echo "  = document-reminders: $(find "$APP_DIR/storage/app/public/document-reminders" -type f | wc -l) file" \
  || echo "  PERINGATAN: document-reminders tidak ditemukan (folder kosong?)"

# ---- 2. VERIFIKASI .env (sebelum config:cache membakar nilainya) --------------
echo ""
echo "[2/8] Verifikasi .env..."
env_val() { sed -n "s/^$1=//p" "$APP_DIR/.env" | head -1 | tr -d "\"'" ; }
[ -n "$(env_val APP_KEY)" ] || { echo "FATAL: APP_KEY kosong"; exit 1; }
URL_NOW="$(env_val APP_URL)"
if [ "$URL_NOW" != "$EXPECTED_URL" ]; then
  echo "  PERINGATAN: APP_URL='$URL_NOW' seharusnya '$EXPECTED_URL'"
  echo "  -> edit manual: sed -i 's|^APP_URL=.*|APP_URL=$EXPECTED_URL|' $APP_DIR/.env"
fi
[ "$(env_val APP_DEBUG)" = "false" ] || echo "  PERINGATAN: APP_DEBUG bukan false (set APP_DEBUG=false untuk production)"
[ -n "$(env_val FAILURE_NOTIFY_EMAIL)" ] || echo "  PERINGATAN: FAILURE_NOTIFY_EMAIL kosong -> notifikasi kegagalan email reminder tak terkirim siapa pun"
echo "  APP_URL  : $URL_NOW"
echo "  APP_ENV  : $(env_val APP_ENV)"
echo "  DB       : $(env_val DB_DATABASE) @ $(env_val DB_HOST)"

# ---- 3. BACKUP DATABASE (sebelum migrate) ------------------------------------
echo ""
echo "[3/8] Backup database..."
DB_HOST="$(env_val DB_HOST)"; DB_PORT="$(env_val DB_PORT)"; DB_NAME="$(env_val DB_DATABASE)"
DB_USER="$(env_val DB_USERNAME)"; DB_PASS="$(env_val DB_PASSWORD)"
[ -n "$DB_NAME" ] || { echo "FATAL: DB_DATABASE kosong di .env"; exit 1; }
DUMP=$(command -v mysqldump || true)
if [ -z "$DUMP" ] && [ -x /www/server/mysql/bin/mysqldump ]; then
  DUMP=/www/server/mysql/bin/mysqldump
fi
[ -n "$DUMP" ] || { echo "FATAL: mysqldump tidak ditemukan"; exit 1; }
BACKUP_FILE="backup_${DB_NAME}_$(date +%Y%m%d_%H%M%S).sql"
MYSQL_PWD="$DB_PASS" "$DUMP" -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USER" "$DB_NAME" > "/root/$BACKUP_FILE"
echo "  + Backup: /root/$BACKUP_FILE ($(wc -c < "/root/$BACKUP_FILE") bytes)"

# ---- 4. DEPENDENCIES & BUILD --------------------------------------------------
echo ""
echo "[4/8] Dependencies & build..."
cd "$APP_DIR"
if command -v composer >/dev/null 2>&1; then
  composer install --no-dev --optimize-autoloader
elif [ ! -d vendor ]; then
  echo "FATAL: tanpa composer, vendor/ wajib sudah ikut terupload"; exit 1
else
  echo "  = vendor ikut terupload, composer dilewati"
fi
if command -v node >/dev/null 2>&1; then
  npm ci
  npm run build
elif [ -f public/build/manifest.json ]; then
  echo "  = public/build ikut terupload, build dilewati"
else
  echo "FATAL: tanpa node dan public/build tidak ada"; exit 1
fi

# ---- 5. PERMISSIONS & SYMLINK -------------------------------------------------
echo ""
echo "[5/8] Permissions, cache, storage:link..."
chown -R "$WEB_USER:$WEB_USER" "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache"
"$PHP83" artisan config:cache
"$PHP83" artisan route:cache
"$PHP83" artisan view:cache
"$PHP83" artisan storage:link --force

# ---- 6. MIGRATE (DINONAKTIFKAN — skema server dibangun via dump) --------------
echo ""
echo "[6/8] Migrate DILEWATI (sengaja — 27 migrasi tak tercatat, jalankan manual bila ada migrasi baru):"
echo "       1) backup DB   2) $PHP83 artisan migrate --force --path=database/migrations/<file_baru>.php"

# ---- 7. CRON SCHEDULER ---------------------------------------------------------
echo ""
echo "[7/8] Cron scheduler..."
CRON_TMP=$(mktemp)
crontab -l 2>/dev/null | grep -v "reminder-app" > "$CRON_TMP" || true
echo "* * * * * cd $APP_DIR && $PHP83 artisan schedule:run >> /dev/null 2>&1" >> "$CRON_TMP"
crontab "$CRON_TMP"
rm -f "$CRON_TMP"
echo "  + schedule:run tiap menit via $PHP83"
crontab -l | grep reminder-app || true

# ---- 8. RINGKASAN ---------------------------------------------------------------
echo ""
echo "============================================"
echo "  DEPLOYMENT SELESAI!"
echo "============================================"
echo "Akses      : $EXPECTED_URL"
echo "Verifikasi :"
echo "  curl -s -o /dev/null -w '%{http_code}\n' $EXPECTED_URL/login"
echo "  crontab -l | grep reminder"
echo "  tail -f $APP_DIR/storage/logs/laravel.log"
echo "Lanjut     : FASE 4 — migrate-shared.sh reminder-app (shared avatar)"
echo "============================================"
