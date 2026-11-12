@echo off
echo ========================================================
echo  iRACK IT Solution — Initial Project Setup
echo ========================================================
echo.

REM 1. Setup Backend
echo [1/3] Installing Laravel backend dependencies...
cd /d "%~dp0backend"
if exist "C:\ProgramData\ComposerSetup\bin\composer.bat" (
    call "C:\ProgramData\ComposerSetup\bin\composer.bat" install
) else (
    composer install
)

if not exist ".env" (
    copy .env.example .env
)

if exist "C:\xampp\php\php.exe" (
    "C:\xampp\php\php.exe" artisan key:generate
) else (
    php artisan key:generate
)

REM 2. Setup Frontend
echo.
echo [2/3] Installing Vue 3 + Vite frontend dependencies...
cd /d "%~dp0frontend"
if exist "C:\nvm4w\nodejs\npm.cmd" (
    call "C:\nvm4w\nodejs\npm.cmd" install
) else (
    call npm install
)

echo.
echo [3/3] Setup complete!
echo You can now run 'start-backend.bat' and 'start-frontend.bat'.
pause
