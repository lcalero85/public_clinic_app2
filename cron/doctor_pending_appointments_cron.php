<?php 
// ================================
// pending_appointments_cron.php
// ================================

require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../app/notifications/AppointmentNotification.php"; 

// ================================
// CONEXIÓN DB CON PDO
// ================================
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    echo "❌ DB Connection failed: " . $e->getMessage() . PHP_EOL;
    die();
}

$notification = new AppointmentNotification();

function logMessage($msg) {
    $logFile = __DIR__ . "/logs/doctor_pending_appointments.log";
    $datetime = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$datetime] $msg" . PHP_EOL, FILE_APPEND);
    echo "$msg\n"; // también muestra en consola
}


// ================================
// PERIODO (daily | monthly)
// ================================
$period = $argv[1] ?? 'daily'; 

if ($period == 'daily') {
    // 🔹 Citas pendientes del día actual
    $sql = "
        SELECT 
            app.id_appointment,
            cp.full_names AS patient,
            app.motive,
            app.appointment_date,
            apps.status,
            dc.work_email AS doctor_email,
            dc.full_names AS doctor_name
        FROM appointment_new AS app
        INNER JOIN clinic_patients AS cp ON app.id_patient = cp.id_patient
        INNER JOIN doc AS dc ON app.id_doc = dc.id
        INNER JOIN appointment_status AS apps ON apps.id = app.id_status_appointment
        WHERE DATE(app.appointment_date) = CURDATE()
          AND apps.id = 1
        ORDER BY app.appointment_date ASC
    ";
    $stmt = $pdo->query($sql);
    $appointments = $stmt->fetchAll();
} elseif ($period == 'monthly') {
    // 🔹 Citas pendientes del mes actual
    $sql = "
        SELECT 
            app.id_appointment,
            cp.full_names AS patient,
            app.motive,
            app.appointment_date,
            apps.status,
            dc.work_email AS doctor_email,
            dc.full_names AS doctor_name
        FROM appointment_new AS app
        INNER JOIN clinic_patients AS cp ON app.id_patient = cp.id_patient
        INNER JOIN doc AS dc ON app.id_doc = dc.id
        INNER JOIN appointment_status AS apps ON apps.id = app.id_status_appointment
        WHERE MONTH(app.appointment_date) = MONTH(CURDATE())
          AND YEAR(app.appointment_date) = YEAR(CURDATE())
          AND apps.id = 1
        ORDER BY app.appointment_date ASC
    ";
    $stmt = $pdo->query($sql);
    $appointments = $stmt->fetchAll();
} else {
    echo "❌ Invalid period. Use 'daily' or 'monthly'." . PHP_EOL;
    exit;
}

// ================================
// AGRUPAR CITAS POR DOCTOR
// ================================
$doctorsAppointments = [];
foreach ($appointments as $appt) {
    $doctorEmail = $appt['doctor_email'];
    if (!isset($doctorsAppointments[$doctorEmail])) {
        $doctorsAppointments[$doctorEmail] = [];
    }
    $doctorsAppointments[$doctorEmail][] = $appt;
}

// ================================
// ENVIAR CORREOS
// ================================
foreach ($doctorsAppointments as $doctorEmail => $doctorAppointments) {
    if (!empty($doctorEmail)) {
        $notification->notifyDoctorPendingAppointments(
            $doctorEmail, 
            $doctorAppointments, 
            $period
        );
        echo "📧 Notification sent to {$doctorEmail} (" . count($doctorAppointments) . " appointments)" . PHP_EOL;
    }
}

echo "✅ Pending appointments cron ({$period}) completed." . PHP_EOL;
