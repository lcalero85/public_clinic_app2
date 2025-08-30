<?php
require_once __DIR__ . "/../config.php";
require_once __DIR__ . "/../helpers/logger.php";

// Conectar con PDO (desde config)
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
try {
    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// Obtener últimas 10 actividades (solo INFO)
$stmt = $pdo->prepare("SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 10");
$stmt->execute();
$activities = $stmt->fetchAll();

if ($activities) {
    foreach ($activities as $event) {
        $icon = "";
        switch ($event['type']) {
            case 'appointment': $icon = "<i class='fa fa-calendar text-danger'></i>"; break;
            case 'invoice': $icon = "<i class='fa fa-file-invoice text-success'></i>"; break;
            case 'user': $icon = "<i class='fa fa-user-plus text-dark'></i>"; break;
            case 'schedule': $icon = "<i class='fa fa-clock text-info'></i>"; break;
            case 'prescription': $icon = "<i class='fa fa-file-prescription text-primary'></i>"; break;
            default: $icon = "<i class='fa fa-info-circle text-secondary'></i>";
        }

        echo "<div class='activity-item mb-2'>
                {$icon} " . htmlspecialchars($event['action']) . "
                <small class='text-muted d-block'>" . date("d M Y H:i", strtotime($event['created_at'])) . "</small>
              </div>";
    }
} else {
    echo "<p class='text-muted'>No recent activity found</p>";
}

