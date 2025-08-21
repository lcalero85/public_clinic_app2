<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once LIBS_DIR . 'PHPMailer/src/Exception.php';
require_once LIBS_DIR . 'PHPMailer/src/PHPMailer.php';
require_once LIBS_DIR . 'PHPMailer/src/SMTP.php';

class Mailer
{
    protected $smtp_username = SMTP_USERNAME;
    protected $smtp_password = SMTP_PASSWORD;
    protected $smtp_host     = SMTP_HOST;
    protected $smtp_port     = SMTP_PORT;
    protected $smtp_secure   = SMTP_SECURE;  // ahora toma de config.php

    protected $sender_email  = DEFAULT_EMAIL;
    protected $sender_name   = DEFAULT_EMAIL_ACCOUNT_NAME;

    public function __construct()
    {
        if (empty($this->smtp_port)) {
            $this->smtp_port = 587; // Gmail puerto TLS por defecto
        }
    }

    public function send_mail($recipient_emails, $subject, $msg)
    {
        $mail = new PHPMailer(true);

        try {
            if (USE_SMTP == true) {
                //$mail->SMTPDebug = 2; // Activa logs de conexión si necesitas probar
                $mail->isSMTP();
                $mail->Host       = $this->smtp_host;
                $mail->SMTPAuth   = true;
                $mail->Username   = $this->smtp_username;
                $mail->Password   = $this->smtp_password;
                $mail->SMTPSecure = $this->smtp_secure ?: PHPMailer::ENCRYPTION_STARTTLS; 
                $mail->Port       = $this->smtp_port;
            }

            // Remitente
            $mail->setFrom($this->sender_email, $this->sender_name);

            // Destinatarios
            if (is_array($recipient_emails)) {
                foreach ($recipient_emails as $email) {
                    $mail->addAddress($email);
                }
            } else {
                $mail->addAddress($recipient_emails);
            }

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $msg;
            $mail->AltBody = strip_tags($msg);

            $mail->send();
            return true;
        } catch (Exception $e) {
            return "Mailer Error: {$mail->ErrorInfo}";
        }
    }
}
