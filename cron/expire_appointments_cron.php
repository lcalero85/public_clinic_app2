<?php 
// ================================
// CONFIGURACIÓN
// ================================
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../app/notifications/AppointmentNotification.php"; 

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    logMessage("❌ DB Connection failed: " . $e->getMessage());
    die();
}

// ================================
// FUNCION PARA LOG
// ================================
function logMessage($msg) {
    $logFile = __DIR__ . "/logs/appointment_expired.log";
    $datetime = date("Y-m-d H:i:s");
    file_put_contents($logFile, "[$datetime] $msg" . PHP_EOL, FILE_APPEND);
    echo "$msg\n"; // también muestra en consola
}

// ================================
// BUSCAR CITAS VENCIDAS
// ================================
$sql = "
    SELECT 
        app.id_appointment,
        app.appointment_date,
        cp.full_names AS patient_name,
        dc.full_names AS doctor_name
    FROM appointment_new AS app
    INNER JOIN clinic_patients AS cp ON cp.id_patient = app.id_patient
    INNER JOIN doc AS dc ON dc.id = app.id_doc
    WHERE DATE(app.appointment_date) < CURDATE()
      AND app.id_status_appointment = 1
";

$stmt = $pdo->query($sql);
$expiredAppointments = $stmt->fetchAll();

if ($expiredAppointments) {
    logMessage("🔎 Found " . count($expiredAppointments) . " expired appointments.");

    // ================================
    // OBTENER ADMIN
    // ================================
    $sqlAdmin = "SELECT email FROM users WHERE id_role = 1 LIMIT 1";
    $adminEmail = $pdo->query($sqlAdmin)->fetchColumn();

    if ($adminEmail) {
        logMessage("📧 Sending report to admin: {$adminEmail}");

        $notifier = new AppointmentNotification();
        $notifier->sendExpiredAppointmentsReport($adminEmail, [
            "appointments" => $expiredAppointments
        ]);

        logMessage("✔ Report sent successfully.");
    } else {
        logMessage("⚠ No admin email found, skipping notification.");
    }

    // ================================
    // ACTUALIZAR CITAS
    // ================================
    $ids = array_column($expiredAppointments, "id_appointment");
    $placeholders = rtrim(str_repeat("?,", count($ids)), ",");
    $updateSql = "UPDATE appointment_new SET id_status_appointment = 9 WHERE id_appointment IN ($placeholders)";
    $pdo->prepare($updateSql)->execute($ids);

    logMessage("✔ Updated " . count($ids) . " appointments to status 9 (incomplete).");
} else {
    logMessage("No expired appointments found.");
}

logMessage("✅ Execution finished.");

