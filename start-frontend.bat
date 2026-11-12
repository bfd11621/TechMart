@echo off
echo ========================================================
echo  Starting iRACK IT Solution Vue 3 + Vite Frontend (Port 5173)
echo ========================================================
echo.
cd /d "%~dp0frontend"

if exist "C:\nvm4w\nodejs\npm.cmd" (
    call "C:\nvm4w\nodejs\npm.cmd" run dev
) else (
    call npm run dev
)
pause
