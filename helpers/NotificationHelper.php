<?php
/**
 * Notification Helper
 * Centralized function to trigger notifications via Stored Procedure
 */
class NotificationHelper
{
    /**
     * Send a notification event
     * @param string $eventName = Name of the event (e.g., "patient_registered_self")
     * @param array $data = key-value placeholders (patient_name, doctor_name, etc.)
     */
    public static function sendNotification($eventName, $data = [])
    {
        // 🔹 Crear conexión PDO usando config.php
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            // Convertir datos a JSON
            $jsonData = json_encode($data);

            // Ejecutar el stored procedure
            $stmt = $pdo->prepare("CALL notify_event(:event_name, :json_data)");
            $stmt->execute([
                ":event_name" => $eventName,
                ":json_data"  => $jsonData
            ]);

        } catch (Exception $e) {
            error_log("Notification error: " . $e->getMessage());
        }
    }
}


