@echo off
setlocal enabledelayedexpansion

:: ========================================================
::  iRACK IT Solution — Database Disaster Recovery Restore
:: ========================================================

echo ========================================================
echo  iRACK IT Solution Database Restore Utility
echo ========================================================
echo.

set "BACKUP_DIR=%~dp0..\..\storage\backups"
set "DB_HOST=127.0.0.1"
set "DB_PORT=3306"
set "DB_USER=root"
set "DB_PASS="
set "DB_NAME=irack_db"

set "MYSQL_CLI=C:\xampp\mysql\bin\mysql.exe"
if not exist "%MYSQL_CLI%" (
    set "MYSQL_CLI=mysql"
)

if "%~1"=="" (
    echo Available backup files in %BACKUP_DIR%:
    echo --------------------------------------------------------
    dir /b /o-d "%BACKUP_DIR%\*.sql" 2>nul
    echo --------------------------------------------------------
    echo.
    set /p "TARGET_FILE=Enter SQL backup filename to restore: "
    set "RESTORE_PATH=%BACKUP_DIR%\!TARGET_FILE!"
) else (
    set "RESTORE_PATH=%~1"
)

if not exist "%RESTORE_PATH%" (
    echo [ERROR] Backup file not found: %RESTORE_PATH%
    exit /b 1
)

echo.
echo [WARNING] This will overwrite all tables in database '%DB_NAME%'!
set /p "CONFIRM=Are you sure you wish to continue? (yes/no): "
if /i not "%CONFIRM%"=="yes" (
    echo Restoration aborted by user.
    exit /b 0
)

echo.
echo [*] Restoring database from: %RESTORE_PATH%...

if "%DB_PASS%"=="" (
    "%MYSQL_CLI%" -h %DB_HOST% -P %DB_PORT% -u %DB_USER% %DB_NAME% < "%RESTORE_PATH%"
) else (
    "%MYSQL_CLI%" -h %DB_HOST% -P %DB_PORT% -u %DB_USER% -p%DB_PASS% %DB_NAME% < "%RESTORE_PATH%"
)

if %ERRORLEVEL% EQU 0 (
    echo [OK] Database restored successfully.
    echo [*] Clearing application cache...
    if exist "C:\xampp\php\php.exe" (
        "C:\xampp\php\php.exe" "%~dp0..\artisan" cache:clear
        "C:\xampp\php\php.exe" "%~dp0..\artisan" config:clear
    ) else (
        php "%~dp0..\artisan" cache:clear
        php "%~dp0..\artisan" config:clear
    )
    echo [OK] Application caches cleared. System restored to operational baseline.
) else (
    echo [ERROR] Restore operation failed!
    exit /b 1
)

echo.
pause
