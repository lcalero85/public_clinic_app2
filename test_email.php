<?php
require_once __DIR__ . "/helpers/Mailer.php";
require_once __DIR__ ."/config.php";

$mailer = new Mailer();

// Activamos el debug SMTP dentro de PHPMailer
require_once(LIBS_DIR . 'PHPMailer/PHPMailerAutoload.php');
$mail = new PHPMailer();
$mail->isSMTP();
$mail->Host = SMTP_HOST;
$mail->SMTPAuth = true;
$mail->Username = SMTP_USERNAME;
$mail->Password = SMTP_PASSWORD;
$mail->SMTPSecure = SMTP_SECURE;
$mail->Port = SMTP_PORT;

// Debug verbose
$mail->SMTPDebug = 2; // Cambiar a 3 si quieres aún más detalle
$mail->Debugoutput = 'html';

// Datos de envío
$mail->setFrom(SMTP_USERNAME, 'Test Mailer');
$mail->addAddress("calflores45@gmail.com"); // Cambia por el destinatario real
$mail->Subject = "Prueba de correo con debug";
$mail->Body    = "Este es un correo de prueba con salida de debug SMTP.";

// Enviar y mostrar errores si ocurren
if(!$mail->send()){
    echo "<h3 style='color:red'>Error al enviar: " . $mail->ErrorInfo . "</h3>";
}else{
    echo "<h3 style='color:green'>Correo enviado correctamente</h3>";
}
?>


