<?php

class AppointmentNotification
{

    // ✅ Obtiene todos los correos de administradores desde SharedController
    private function getAdminEmails()
    {
        $controller = new SharedController(); // reutilizamos tu controlador compartido
        $db = $controller->GetModel();        // aquí sí existe GetModel()

        $sql = "SELECT email FROM users WHERE id_role = 1 AND email IS NOT NULL";
        $result = $db->rawQuery($sql);

        return $result ?: [];
    }

    // ✅ Notificación al administrador
    public function notifyAdmin($appointmentData)
    {
        $adminEmails = $this->getAdminEmails();

        foreach ($adminEmails as $admin) {
            // Pasa variables al template
            $patient = $appointmentData['patient'] ?? null;
            $appointment = $appointmentData['appointment'] ?? null;

            ob_start();
            extract(compact('patient', 'appointment'));
            include APP_DIR . "views/emails/appointment_request_admin.php";
            $adminBody = ob_get_clean();

            $this->send([
                "to" => $admin['email'],
                "subject" => "New Appointment Request",
                "body" => $adminBody
            ]);
        }
    }

    // ✅ Notificación al paciente
    public function notifyPatient($patientEmail, $appointmentData)
    {
        $patient = $appointmentData['patient'] ?? [];
        $appointment = $appointmentData['appointment'] ?? [];

        ob_start();
        extract(compact('patient', 'appointment'));
        include APP_DIR . "views/emails/appointment_request_patient.php";
        $patientBody = ob_get_clean();
        $this->send([
            "to" => $patientEmail,
            "subject" => "Your Appointment Request",
            "body" => $patientBody
        ]);
    }


    // ✅ Función genérica de envío usando Mailer.php
    private function send($params)
    {
        require_once APP_DIR . "../helpers/Mailer.php";

        $mailer = new Mailer();

        return $mailer->send_mail(
            $params['to'],       // destinatario
            $params['subject'],  // asunto
            $params['body']      // mensaje en HTML
        );
    }

     public function notifyPatientDenied($patientEmail, $appointmentData)
    {
        $patient = $appointmentData['patient'] ?? [];
        $appointment = $appointmentData['appointment'] ?? [];
        $status = "Denied";
        $adminResponse = $appointmentData['admin_response'] ?? "The appointment has been denied by the administrator.";

        ob_start();
        extract(compact('patient', 'appointment', 'status', 'adminResponse'));
        include APP_DIR . "/views/emails/appointment_request_denied.php";; // 🔹 plantilla exclusiva
        $patientBody = ob_get_clean();

        return $this->send([
            "to" => $patientEmail,
            "subject" => "Your Appointment Request Has Been Denied",
            "body" => $patientBody
        ]);
    }

   // ✅ Notificación al paciente cuando la cita es APROBADA
public function notifyPatientApproved($patientEmail, $appointmentData): bool|string
{
    $patient = $appointmentData['patient'] ?? [];
    $appointment = $appointmentData['appointment'] ?? [];
    $doctor = $appointmentData['doctor'] ?? [];
    $status = "Approved";

    ob_start();
    extract(compact('patient', 'appointment', 'doctor', 'status'));
    include APP_DIR . "views/emails/appointment_request_approved.php";
    $patientBody = ob_get_clean();

    return $this->send([
        "to" => $patientEmail,
        "subject" => "Your Appointment Request Has Been Approved",
        "body" => $patientBody
    ]);
}

public function sendReminderToPatient($patientEmail, $appointmentData): bool|string
{
    $patient = $appointmentData['patient'] ?? [];
    $appointment = $appointmentData['appointment'] ?? [];
    $doctor = $appointmentData['doctor'] ?? [];
    $status = "Reminder";

    // Capturamos la vista como plantilla
    ob_start();
    extract(compact('patient', 'appointment', 'doctor', 'status'));
    include APP_DIR . "views/emails/appointment_reminder.php"; 
    $patientBody = ob_get_clean();

    // Usamos el método interno $this->send que ya existe
    return $this->send([
        "to"      => $patientEmail,
        "subject" => "Appointment Reminder - " . ($appointment['appointment_date'] ?? ''),
        "body"    => $patientBody
    ]);
}

public function sendExpiredAppointmentsReport($adminEmail, $data) {
    $appointments = $data['appointments'] ?? [];
    $reportDate = date("Y-m-d H:i:s");

    // Capturamos plantilla como en las demás notificaciones
    ob_start();
    extract(compact('appointments', 'reportDate'));
    include APP_DIR . "views/emails/expired_appointments_report.php"; 
    $body = ob_get_clean();

    // Usamos el método interno $this->send
    return $this->send([
        "to"      => $adminEmail,
        "subject" => "Expired Appointments Report - {$reportDate}",
        "body"    => $body
    ]);
}
public function sendPendingAppointmentsReport($adminEmail, $data) {
    $appointments = $data['appointments'] ?? [];
    $reportDate = date("Y-m-d H:i:s");

    // Capturamos la plantilla como en las demás notificaciones
    ob_start();
    extract(compact('appointments', 'reportDate'));
    include APP_DIR . "views/emails/pending_appointments_report.php"; 
    $body = ob_get_clean();

    // Usamos el método interno $this->send
    return $this->send([
        "to"      => $adminEmail,
        "subject" => "Pending Appointments Report - {$reportDate}",
        "body"    => $body
    ]);
}

public function notifyPatientCreated($patientEmail, $appointmentData): bool|string
{
    $patient = $appointmentData['patient'] ?? [];
    $appointment = $appointmentData['appointment'] ?? [];
    $doctor = $appointmentData['doctor'] ?? [];

    ob_start();
    extract(compact('patient', 'appointment', 'doctor'));
    include APP_DIR . "views/emails/appointment_created_patient.php";
    $body = ob_get_clean();

    return $this->send([
        "to" => $patientEmail,
        "subject" => "Your Appointment Has Been Created",
        "body" => $body
    ]);
}

public function notifyDoctorCreated($doctorEmail, $appointmentData): bool|string
{
    $patient = $appointmentData['patient'] ?? [];
    $appointment = $appointmentData['appointment'] ?? [];
    $doctor = $appointmentData['doctor'] ?? [];
    $status = $appointmentData['status'] ?? '';

    ob_start();
    extract(compact('patient', 'appointment', 'doctor', 'status'));
    include APP_DIR . "views/emails/appointment_created_doctor.php";
    $body = ob_get_clean();

    return $this->send([
        "to" => $doctorEmail,
        "subject" => "New Appointment Assigned to You",
        "body" => $body
    ]);
}

public function notifyDoctorPendingAppointments($doctorEmail, $appointments, $period = 'daily')
{
    ob_start();
    $title = ($period == 'monthly') ? "Monthly Pending Appointments" : "Daily Pending Appointments";
    $data = compact('appointments', 'title');
    extract($data);

    include APP_DIR . "views/emails/doctor_pending_appointments_report.php";
    $body = ob_get_clean();

    return $this->send([
        "to" => $doctorEmail,
        "subject" => $title,
        "body" => $body
    ]);
}


}
