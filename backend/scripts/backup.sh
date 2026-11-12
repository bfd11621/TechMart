#!/usr/bin/env bash
# ========================================================
#  iRACK IT Solution — Linux Production Automated Backup
# ========================================================
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BASE_DIR="${SCRIPT_DIR}/.."
BACKUP_DIR="${BASE_DIR}/storage/backups"
TIMESTAMP="$(date +'%Y%m%d_%H%M%S')"

mkdir -p "${BACKUP_DIR}"

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-techmart}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"

BACKUP_FILE="${BACKUP_DIR}/irack_db_${TIMESTAMP}.sql.gz"
ENV_BACKUP="${BACKUP_DIR}/env_${TIMESTAMP}.env"
UPLOADS_BACKUP="${BACKUP_DIR}/uploads_${TIMESTAMP}.tar.gz"

echo "=== Starting iRACK Backup: ${TIMESTAMP} ==="

# 1. MySQL Dump with Gzip Compression
if [ -z "${DB_PASSWORD}" ]; then
    mysqldump -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USERNAME}" \
        --single-transaction --routines --triggers "${DB_DATABASE}" | gzip > "${BACKUP_FILE}"
else
    MYSQL_PWD="${DB_PASSWORD}" mysqldump -h "${DB_HOST}" -P "${DB_PORT}" -u "${DB_USERNAME}" \
        --single-transaction --routines --triggers "${DB_DATABASE}" | gzip > "${BACKUP_FILE}"
fi
echo "[OK] Database backup created: ${BACKUP_FILE}"

# 2. Environment Configuration Backup
if [ -f "${BASE_DIR}/.env" ]; then
    cp "${BASE_DIR}/.env" "${ENV_BACKUP}"
    chmod 600 "${ENV_BACKUP}"
    echo "[OK] Environment file backed up: ${ENV_BACKUP}"
fi

# 3. Application Public Uploads & Datasheets Archive
if [ -d "${BASE_DIR}/storage/app/public" ]; then
    tar -czf "${UPLOADS_BACKUP}" -C "${BASE_DIR}/storage/app" public
    echo "[OK] Media and datasheets archived: ${UPLOADS_BACKUP}"
fi

# 4. Retention Management (Purge backups older than 30 days)
find "${BACKUP_DIR}" -type f -name "*.sql.gz" -mtime +30 -delete
find "${BACKUP_DIR}" -type f -name "*.env" -mtime +30 -delete
find "${BACKUP_DIR}" -type f -name "*.tar.gz" -mtime +30 -delete
echo "[OK] Backup retention policy enforced (30-day window maintained)."

echo "=== Backup Completed Successfully ==="
