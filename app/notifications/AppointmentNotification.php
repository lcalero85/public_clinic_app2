<?php

class AppointmentNotification {

    // ✅ Obtiene todos los correos de administradores desde SharedController
    private function getAdminEmails() {
        $controller = new SharedController(); // reutilizamos tu controlador compartido
        $db = $controller->GetModel();        // aquí sí existe GetModel()
        
        $sql = "SELECT email FROM users WHERE id_role = 1 AND email IS NOT NULL";
        $result = $db->rawQuery($sql);

        return $result ?: [];
    }

    // ✅ Notificación al administrador
    public function notifyAdmin($appointmentData) {
        $adminEmails = $this->getAdminEmails();

        foreach ($adminEmails as $admin) {
            ob_start();
            include APP_DIR . "/views/emails/appointment_request_admin.php";
            $adminBody = ob_get_clean();

            $this->send([
                "to" => $admin['email'],
                "subject" => "New Appointment Request",
                "body" => $adminBody
            ]);
        }
    }

    // ✅ Notificación al paciente
    public function notifyPatient($patientEmail, $appointmentData) {
        ob_start();
        include APP_DIR . "/views/emails/appointment_request_patient.php";
        $patientBody = ob_get_clean();

        $this->send([
            "to" => $patientEmail,
            "subject" => "Your Appointment Request",
            "body" => $patientBody
        ]);
    }

    // ✅ Función genérica de envío (adáptala a PHPMailer, SwiftMailer o mail())
    private function send($params) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: Clinic System <" . SMTP_USERNAME . ">" . "\r\n";

        mail($params['to'], $params['subject'], $params['body'], $headers);
    }
}
