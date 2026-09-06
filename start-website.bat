@echo off
title The Cochin - WordPress Local Server
echo ====================================================
echo Starting The Cochin WordPress Site...
echo ====================================================

:: Start MySQL if not running
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
    echo [OK] MySQL is already running.
) else (
    echo [..] Starting MySQL server...
    start /B "" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
    timeout /t 2 >nul
)

echo [OK] Launching WordPress at http://localhost:8000
start http://localhost:8000
echo.
echo Server is running. Press Ctrl+C to stop the server.
"C:\xampp\php\php.exe" -S localhost:8000 -t "%~dp0Web" "%~dp0Web\router.php"
