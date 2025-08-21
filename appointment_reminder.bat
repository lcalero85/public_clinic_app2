@echo off
REM === Configuración de rutas ===
set PHP_PATH=C:\x\php\php.exe
set SCRIPT_PATH=C:\x\htdocs\public_clinic_app\cron\appointment_reminder_cron.php
set LOG_PATH=C:\x\htdocs\public_clinic_app\cron\logs\appointment_reminder.log

echo ================================================== >> "%LOG_PATH%"
echo Fecha y hora de ejecución: %date% %time% >> "%LOG_PATH%"
"%PHP_PATH%" "%SCRIPT_PATH%" >> "%LOG_PATH%" 2>&1
echo Ejecución finalizada. >> "%LOG_PATH%"
echo. >> "%LOG_PATH%"
pause
