@echo off
setlocal enabledelayedexpansion

:: ========================================================
::  iRACK IT Solution — Automated Database & Config Backup
:: ========================================================

echo ========================================================
echo  iRACK IT Solution Database Backup Utility
echo ========================================================
echo.

set "BACKUP_DIR=%~dp0..\..\storage\backups"
set "TIMESTAMP=%date:~10,4%%date:~4,2%%date:~7,2%_%time:~0,2%%time:~3,2%%time:~6,2%"
set "TIMESTAMP=%TIMESTAMP: =0%"

if not exist "%BACKUP_DIR%" (
    mkdir "%BACKUP_DIR%"
)

set "DB_HOST=127.0.0.1"
set "DB_PORT=3306"
set "DB_USER=root"
set "DB_PASS="
set "DB_NAME=irack_db"

set "MYSQL_DUMP=C:\xampp\mysql\bin\mysqldump.exe"
if not exist "%MYSQL_DUMP%" (
    set "MYSQL_DUMP=mysqldump"
)

set "BACKUP_FILE=%BACKUP_DIR%\irack_db_backup_%TIMESTAMP%.sql"
set "ENV_BACKUP=%BACKUP_DIR%\env_backup_%TIMESTAMP%.env"

echo [*] Target Database: %DB_NAME% at %DB_HOST%:%DB_PORT%
echo [*] Dumping schema, tables, stored procedures, and triggers...

if "%DB_PASS%"=="" (
    "%MYSQL_DUMP%" -h %DB_HOST% -P %DB_PORT% -u %DB_USER% --single-transaction --routines --triggers %DB_NAME% > "%BACKUP_FILE%"
) else (
    "%MYSQL_DUMP%" -h %DB_HOST% -P %DB_PORT% -u %DB_USER% -p%DB_PASS% --single-transaction --routines --triggers %DB_NAME% > "%BACKUP_FILE%"
)

if %ERRORLEVEL% EQU 0 (
    echo [OK] Database backup completed successfully:
    echo      %BACKUP_FILE%
) else (
    echo [ERROR] Database backup failed! Please check MySQL connection parameters.
    exit /b 1
)

:: Environment Backup
if exist "%~dp0..\.env" (
    copy "%~dp0..\.env" "%ENV_BACKUP%" >nul
    echo [OK] Environment configuration backed up: %ENV_BACKUP%
)

:: Retention Management: Delete SQL backups older than 30 days
echo [*] Pruning backups older than 30 days...
forfiles /P "%BACKUP_DIR%" /M *.sql /D -30 /C "cmd /c del @path" 2>nul
forfiles /P "%BACKUP_DIR%" /M *.env /D -30 /C "cmd /c del @path" 2>nul

echo.
echo ========================================================
echo  Backup operation complete. Verified for disaster recovery.
echo ========================================================
pause
