# iRACK IT Solution — Backup and Disaster Recovery Guide

## 1. Overview & Recovery Objectives
This document establishes the official Backup, Retention, and Disaster Recovery Procedures for iRACK IT Solution to ensure business continuity in the event of hardware failure, catastrophic data loss, or server migration.

- **Recovery Point Objective (RPO)**: Maximum 24 hours (nightly automated dumps) or 1 hour with binary logging enabled.
- **Recovery Time Objective (RTO)**: Less than 15 minutes to full service restoration.

---

## 2. Backup Strategy & Asset Scope

| Asset | Scope | Method | Schedule | Retention |
| :--- | :--- | :--- | :--- | :--- |
| **Relational Database** | Schema, tables, triggers, stored procedures, routines | `mysqldump` single-transaction | Nightly at 02:30 AM | 30 Days rolling |
| **Environment Config** | Laravel `.env` (keys, secrets, SMTP) | Copy & secure chmod 600 | Nightly with DB dump | 30 Days rolling |
| **Public Media & Files**| Uploaded OEM PDF datasheets and product images | `tar -czf` / file archive | Weekly Sunday 03:00 AM| 60 Days rolling |

---

## 3. Automated Backup Execution

### 3.1 Windows Environment
Execute the provided batch script located in the repository:
```cmd
backend\scripts\backup-database.bat
```
This utility:
1. Connects to MySQL using configured port and host.
2. Dumps database `techmart` with `--single-transaction --routines --triggers`.
3. Creates a timestamped SQL file in `storage\backups\irack_db_backup_YYYYMMDD_HHMMSS.sql`.
4. Copies `.env` to `storage\backups\env_backup_YYYYMMDD_HHMMSS.env`.
5. Automatically deletes SQL and ENV files older than 30 days to protect disk space.

### 3.2 Linux / Unix Production Environment
Automated via cron. The script is located at:
```bash
backend/scripts/backup.sh
```
Crontab entry (`/etc/cron.d/irack-backup`):
```cron
30 2 * * * /var/www/irack/backend/scripts/backup.sh >> /var/log/irack_backup.log 2>&1
```

---

## 4. Disaster Recovery & Restoration Procedures

### 4.1 Restoring on Windows
```cmd
backend\scripts\restore-database.bat
```
1. The script lists all available SQL backups in `storage\backups`.
2. Type or select the filename to restore.
3. Confirm with `yes`.
4. The script imports the SQL dump into MySQL, clears Laravel configuration and cache files, and restores the system to the backup baseline.

### 4.2 Restoring on Linux / Production Server
```bash
# Execute recovery script
sudo /var/www/irack/backend/scripts/restore.sh /var/www/irack/backend/storage/backups/irack_db_20260107_023000.sql.gz

# Confirm restoration by typing CONFIRM when prompted
```

### 4.3 Restoring Public Uploads & Datasheets
```bash
# Extract media archive into storage
tar -xzf /var/www/irack/backend/storage/backups/uploads_20260107_023000.tar.gz -C /var/www/irack/backend/storage/app/

# Re-link public storage if necessary
php /var/www/irack/backend/artisan storage:link
```
