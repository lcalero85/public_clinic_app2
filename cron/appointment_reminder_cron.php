<?php
// Cargar configuración y clases
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../app/notifications/AppointmentNotification.php"; // ajusta ruta según tu estructura

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

// Buscar citas a 5 días
$sql = "
    SELECT 
        app.id_appointment,
        app.appointment_date,
        cp.full_names AS patient_name,
        cp.email AS patient_email,
        dc.full_names AS doctor_name
    FROM appointment_new AS app
    INNER JOIN clinic_patients AS cp ON cp.id_patient = app.id_patient
    INNER JOIN doc AS dc ON dc.id = app.id_doc
    WHERE DATE(app.appointment_date) = DATE_ADD(CURDATE(), INTERVAL 5 DAY)
      AND app.id_status_appointment = 1
";

$stmt = $pdo->query($sql);
$appointments = $stmt->fetchAll();

if ($appointments) {
    $notifier = new AppointmentNotification();

    foreach ($appointments as $appointment) {
        $data = [
            'patient' => [
                'full_names' => $appointment['patient_name'],
            ],
            'appointment' => [
                'appointment_date' => $appointment['appointment_date'],
            ],
            'doctor' => [
                'full_names' => $appointment['doctor_name'],
            ]
        ];

        // Enviar recordatorio
        $notifier->sendReminderToPatient($appointment['patient_email'], $data);

        echo "Reminder sent to {$appointment['patient_email']} for appointment {$appointment['appointment_date']}\n";
    }
} else {
    echo "No appointments found for reminders.\n";
}
