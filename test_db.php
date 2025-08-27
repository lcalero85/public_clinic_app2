<?php
// Incluir config.php donde están definidas las constantes
require_once __DIR__ . "/config.php";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USERNAME,
        DB_PASSWORD
    );
    echo "✅ Conexión exitosa a la base: " . DB_NAME;
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage();
}
?>

