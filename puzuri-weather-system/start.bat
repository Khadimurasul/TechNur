@echo off
echo Starting Puzuri Farms Weather Forecast System...
echo Access the dashboard at http://localhost:8000
echo.
echo Please ensure PHP is in your system PATH.
echo.

:: Start the PHP built-in server in a new window
start "Puzuri Weather Server" php -S localhost:8000

:: Wait a moment for the server to initialize
timeout /t 3 /nobreak > nul

:: Open the default browser
start http://localhost:8000

echo Server is running. Close the other window to stop.
pause
