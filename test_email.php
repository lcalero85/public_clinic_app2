<?php
// Incluye PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/libs/PHPMailer/src/Exception.php';
require __DIR__ . '/libs/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/libs/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    // Configuración del servidor SMTP (Gmail)
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'katalontest258@gmail.com'; 
    $mail->Password   = 'dohpqvtovfyiqyaf'; // no la contraseña normal
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Remitente y destinatario
    $mail->setFrom('katalontest258@gmail.com', 'Mi Sistema');
    $mail->addAddress('calflores45@gmail.com');

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = 'Prueba de correo con Gmail + PHPMailer';
    $mail->Body    = '<h1>Funciona!</h1><p>Este es un test enviado con <b>PHPMailer</b>.</p>';

    $mail->send();
    echo '✅ Correo enviado correctamente';
} catch (Exception $e) {
    echo "❌ Error al enviar el correo: {$mail->ErrorInfo}";
}
