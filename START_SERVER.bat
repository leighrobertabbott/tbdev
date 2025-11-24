@echo off
echo Starting TorrentBits Server...
echo.
echo Server will be available at: http://localhost:8000
echo Press Ctrl+C to stop the server
echo.
cd /d "%~dp0"
C:\xampp\php\php.exe -S localhost:8000 -t public
pause

