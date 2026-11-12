#!/usr/bin/env bash
# ========================================================
#  iRACK IT Solution — Linux Disaster Recovery Restore
# ========================================================
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BASE_DIR="${SCRIPT_DIR}/.."
BACKUP_DIR="${BASE_DIR}/storage/backups"

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-techmart}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"

RESTORE_FILE="${1:-}"

if [ -z "${RESTORE_FILE}" ]; then
    echo "Available SQL backups in ${BACKUP_DIR}:"
    ls -lh "${BACKUP_DIR}"/*.sql.gz 2>/dev/null || ls -lh "${BACKUP_DIR}"/*.sql 2>/dev/null || echo "No backups found."
    echo ""
    read -rp "Enter exact path to SQL or SQL.GZ backup: " RESTORE_FILE
fi

if [ ! -f "${RESTORE_FILE}" ]; then
    echo "[ERROR] File not found: ${RESTORE_FILE}"
    exit 1
fi

echo "=========================================================="
echo "WARNING: This will overwrite database '${DB_DATABASE}'!"
echo "Target host: ${DB_HOST}:${DB_PORT}"
echo "Restore file: ${RESTORE_FILE}"
echo "=========================================================="
read -rp "Type 'CONFIRM' to proceed with database restoration: " CONFIRM

if [ "${CONFIRM}" != "CONFIRM" ]; then
    echo "Restoration aborted by operator."
    exit 0
fi

echo "[*] Restoring database..."
if [[ "${RESTORE_FILE}" == *.gz ]]; then
    if [ -z "${DB_PASSWORD}" ]; then
        gunzip -c "${RESTORE_FILE}" | mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USERNAME}" "${DB_DATABASE}"
    else
        MYSQL_PWD="${DB_PASSWORD}" gunzip -c "${RESTORE_FILE}" | mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USERNAME}" "${DB_DATABASE}"
    fi
else
    if [ -z "${DB_PASSWORD}" ]; then
        mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USERNAME}" "${DB_DATABASE}" < "${RESTORE_FILE}"
    else
        MYSQL_PWD="${DB_PASSWORD}" mysql -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USERNAME}" "${DB_DATABASE}" < "${RESTORE_FILE}"
    fi
fi

echo "[OK] Database restored successfully."
echo "[*] Clearing application caches..."
php "${BASE_DIR}/artisan" cache:clear
php "${BASE_DIR}/artisan" config:clear
php "${BASE_DIR}/artisan" route:clear
echo "[OK] Restoration complete. System operational."
