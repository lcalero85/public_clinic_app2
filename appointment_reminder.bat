@echo off
REM === Configuración de rutas ===
set PHP_PATH=C:\x\php\php.exe

REM === Cronjob de recordatorio de citas ===
set SCRIPT_REMINDER=C:\x\htdocs\public_clinic_app\cron\appointment_reminder_cron.php
set LOG_REMINDER=C:\x\htdocs\public_clinic_app\cron\logs\appointment_reminder.log

REM === Cronjob de citas vencidas ===
set SCRIPT_EXPIRE=C:\x\htdocs\public_clinic_app\cron\expire_appointments_cron.php
set LOG_EXPIRE=C:\x\htdocs\public_clinic_app\cron\logs\expire_appointments.log

REM === Cronjob de citas pendientes ===
set SCRIPT_PENDING=C:\x\htdocs\public_clinic_app\cron\pending_appointments_cron.php
set LOG_PENDING=C:\x\htdocs\public_clinic_app\cron\logs\pending_appointments.log


REM === Cronjob de citas pendientes doctor===
set SCRIPT_PENDING=C:\x\htdocs\public_clinic_app\cron\doctor_pending_appointments_cron.php
set DOCTOR_LOG_PENDING=C:\x\htdocs\public_clinic_app\cron\logs\doctor_pending_appointments_cron.log


echo ================================================== >> "%LOG_REMINDER%"
echo Fecha y hora de ejecución (REMINDER): %date% %time% >> "%LOG_REMINDER%"
"%PHP_PATH%" "%SCRIPT_REMINDER%" >> "%LOG_REMINDER%" 2>&1
echo Ejecución finalizada (REMINDER). >> "%LOG_REMINDER%"
echo. >> "%LOG_REMINDER%"

echo ================================================== >> "%LOG_EXPIRE%"
echo Fecha y hora de ejecución (EXPIRE): %date% %time% >> "%LOG_EXPIRE%"
"%PHP_PATH%" "%SCRIPT_EXPIRE%" >> "%LOG_EXPIRE%" 2>&1
echo Ejecución finalizada (EXPIRE). >> "%LOG_EXPIRE%"
echo. >> "%LOG_EXPIRE%"

echo ================================================== >> "%LOG_PENDING%"
echo Fecha y hora de ejecución (PENDING): %date% %time% >> "%LOG_PENDING%"
"%PHP_PATH%" "%SCRIPT_PENDING%" >> "%LOG_PENDING%" 2>&1
echo Ejecución finalizada (PENDING). >> "%LOG_PENDING%"
echo. >> "%LOG_PENDING%"

echo ================================================== >> "%DOCTOR_LOG_PENDING%"
echo Fecha y hora de ejecución (PENDING): %date% %time% >> "%DOCTOR_LOG_PENDING%"
"%PHP_PATH%" "%SCRIPT_PENDING%" >> "%DOCTOR_LOG_PENDING%" 2>&1
echo Ejecución finalizada (PENDING). >> "%DOCTOR_LOG_PENDING%"
echo. >> "%DOCTOR_LOG_PENDING%"

pause

