@echo off
echo ========================================================
echo  Starting iRACK IT Solution Laravel Backend API (Port 8000)
echo ========================================================
echo.
cd /d "%~dp0backend"

if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" artisan serve --port=8000
) else (
    php artisan serve --port=8000
)
pause
