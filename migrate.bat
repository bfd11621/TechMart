@echo off
echo ========================================================
echo  Running iRACK IT Solution Database Migrations and Seeders
echo ========================================================
echo.
cd /d "%~dp0backend"

if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" create-database.php
    "C:\xampp\php\php.exe" artisan migrate:fresh --seed
) else (
    php create-database.php
    php artisan migrate:fresh --seed
)
pause
