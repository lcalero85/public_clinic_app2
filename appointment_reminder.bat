@echo off
REM === Configuración de rutas ===
set PHP_PATH=C:\x\php\php.exe
set TODAY=%date:~-4%-%date:~3,2%-%date:~0,2%_%time:~0,2%-%time:~3,2%-%time:~6,2%
set TODAY=%TODAY: =0%

REM === Directorio de logs ===
set LOG_DIR=C:\x\htdocs\public_clinic_app\cron\logs

REM === Cronjob de recordatorio de citas ===
set SCRIPT_REMINDER=C:\x\htdocs\public_clinic_app\cron\appointment_reminder_cron.php
set LOG_REMINDER=%LOG_DIR%\appointment_reminder_%TODAY%.log

REM === Cronjob de citas vencidas ===
set SCRIPT_EXPIRE=C:\x\htdocs\public_clinic_app\cron\expire_appointments_cron.php
set LOG_EXPIRE=%LOG_DIR%\expire_appointments_%TODAY%.log

REM === Cronjob de citas pendientes ===
set SCRIPT_PENDING=C:\x\htdocs\public_clinic_app\cron\pending_appointments_cron.php
set LOG_PENDING=%LOG_DIR%\pending_appointments_%TODAY%.log

REM === Cronjob de citas pendientes doctor ===
set SCRIPT_DOCTOR_PENDING=C:\x\htdocs\public_clinic_app\cron\doctor_pending_appointments_cron.php
set LOG_DOCTOR_PENDING=%LOG_DIR%\doctor_pending_appointments_%TODAY%.log


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

echo ================================================== >> "%LOG_DOCTOR_PENDING%"
echo Fecha y hora de ejecución (DOCTOR_PENDING): %date% %time% >> "%LOG_DOCTOR_PENDING%"
"%PHP_PATH%" "%SCRIPT_DOCTOR_PENDING%" >> "%LOG_DOCTOR_PENDING%" 2>&1
echo Ejecución finalizada (DOCTOR_PENDING). >> "%LOG_DOCTOR_PENDING%"
echo. >> "%LOG_DOCTOR_PENDING%"

pause
