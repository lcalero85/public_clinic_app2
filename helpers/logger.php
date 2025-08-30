<?php
function app_logger($level, $type, $action, $user_id = null) {
    $logDir = __DIR__ . "/../logs";
    $logFile = $logDir . "/app_logs.txt";

    // Crear carpeta si no existe
    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    // Formato de log
    $timestamp = date("Y-m-d H:i:s");
    $user = $user_id ? "UserID:$user_id" : "System";

    $entry = "[$timestamp] [$level] [$type] $user - $action" . PHP_EOL;

    // Escribir siempre en el archivo
    file_put_contents($logFile, $entry, FILE_APPEND);

    // Insertar también en la tabla activity_log
    try {
        $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USERNAME, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

        $stmt = $db->prepare("INSERT INTO activity_log (user_id, level, type, action) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $level, $type, $action]);

    } catch (Exception $e) {
        // Si falla la BD, al menos queda el archivo
        file_put_contents($logFile, "[$timestamp] [ERROR] DB Insert Failed - " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
}

