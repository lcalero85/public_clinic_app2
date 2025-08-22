<?php
// ================================
// Pending Appointments Cron Job
// ================================

// Cargar configuración y clases
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../app/notifications/AppointmentNotification.php";

// Conectar a la base
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

echo "🔍 Checking pending appointments...\n";

// ================================
// Buscar citas pendientes de confirmación (status = 2)
// ================================
$sql = $sql = "SELECT
    an.id_appointment,
    cp.full_names AS patient_name,
    CASE 
        WHEN an.requested_date IS NULL OR an.requested_date = '0000-00-00 00:00:00' 
        THEN 'Not provided'
        ELSE an.requested_date
    END AS requested_date
FROM appointment_new AS an
INNER JOIN clinic_patients AS cp ON an.id_patient = cp.id_patient
INNER JOIN appointment_status AS apps ON apps.id = an.id_status_appointment
WHERE an.id_status_appointment = 2
ORDER BY an.register_date DESC";

$stmt = $pdo->query($sql);
$pendingAppointments = $stmt->fetchAll();

if ($pendingAppointments) {
    echo "📌 Found " . count($pendingAppointments) . " pending appointments.\n";

    // ================================
    // Obtener correo del Admin
    // ================================
    $sqlAdmin = "SELECT email FROM users WHERE id_role = 1 LIMIT 1";
    $adminEmail = $pdo->query($sqlAdmin)->fetchColumn();

    if ($adminEmail) {
        // ================================
        // Enviar notificación con plantilla
        // ================================
        $notifier = new AppointmentNotification();
        $notifier->sendPendingAppointmentsReport($adminEmail, [
            "appointments" => $pendingAppointments
        ]);

        echo "📧 Report sent to admin ({$adminEmail}).\n";
    } else {
        echo "⚠ No admin email found.\n";
    }
} else {
    echo "No pending appointments found.\n";
}

echo "✅ Execution finished (PENDING).\n";
