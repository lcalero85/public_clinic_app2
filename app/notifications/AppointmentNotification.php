<?php

/**
 * Clase AppointmentNotification
 * Se encarga de manejar todas las notificaciones (por correo electrónico)
 * relacionadas con citas médicas: creación, aprobación, recordatorios,
 * reportes, etc.
 */
class AppointmentNotification
{

    /**
     * 🔹 Obtiene todos los correos electrónicos de administradores
     * Usa el controlador compartido para acceder al modelo y consultar la DB.
     */
    private function getAdminEmails()
    {
        $controller = new SharedController(); // Instancia del controlador compartido
        $db = $controller->GetModel();        // Obtiene el modelo de base de datos

        // Consulta: selecciona correos de usuarios con rol administrador (id_role = 1)
        $sql = "SELECT email FROM users WHERE id_role = 1 AND email IS NOT NULL";
        $result = $db->rawQuery($sql);

        return $result ?: []; // Devuelve array vacío si no hay resultados
    }

    /**
     * 🔹 Envía notificación al administrador cuando se crea una cita
     */
    public function notifyAdmin($appointmentData)
    {
        $adminEmails = $this->getAdminEmails(); // Correos de administradores

        foreach ($adminEmails as $admin) {
            // Extrae datos de paciente y cita
            $patient = $appointmentData['patient'] ?? null;
            $appointment = $appointmentData['appointment'] ?? null;

            // Renderiza la plantilla de correo para administradores
            ob_start();
            extract(compact('patient', 'appointment'));
            include APP_DIR . "views/emails/appointment_request_admin.php";
            $adminBody = ob_get_clean();

            // Envío de correo
            $this->send([
                "to" => $admin['email'],
                "subject" => "New Appointment Request",
                "body" => $adminBody
            ]);
        }
    }

    /**
     * 🔹 Envía notificación al paciente cuando solicita una cita
     */
    public function notifyPatient($patientEmail, $appointmentData)
    {
        $patient = $appointmentData['patient'] ?? [];
        $appointment = $appointmentData['appointment'] ?? [];

        // Renderiza plantilla de correo para pacientes
        ob_start();
        extract(compact('patient', 'appointment'));
        include APP_DIR . "views/emails/appointment_request_patient.php";
        $patientBody = ob_get_clean();

        // Envío de correo
        $this->send([
            "to" => $patientEmail,
            "subject" => "Your Appointment Request",
            "body" => $patientBody
        ]);
    }

    /**
     * 🔹 Método genérico de envío de correos
     * Usa la clase Mailer ubicada en helpers/Mailer.php
     */
    private function send($params)
    {
        require_once APP_DIR . "../helpers/Mailer.php"; // Incluye clase Mailer

        $mailer = new Mailer();

        // Envía el correo con parámetros
        return $mailer->send_mail(
            $params['to'],       // destinatario
            $params['subject'],  // asunto
            $params['body']      // cuerpo HTML
        );
    }

    /**
     * 🔹 Notificación al paciente cuando su cita fue DENEGADA
     */
    public function notifyPatientDenied($patientEmail, $appointmentData)
    {
        $patient = $appointmentData['patient'] ?? [];
        $appointment = $appointmentData['appointment'] ?? [];
        $status = "Denied"; // Estado de la cita
        $adminResponse = $appointmentData['admin_response'] ?? "The appointment has been denied by the administrator.";

        // Renderiza plantilla para denegación
        ob_start();
        extract(compact('patient', 'appointment', 'status', 'adminResponse'));
        include APP_DIR . "/views/emails/appointment_request_denied.php"; 
        $patientBody = ob_get_clean();

        return $this->send([
            "to" => $patientEmail,
            "subject" => "Your Appointment Request Has Been Denied",
            "body" => $patientBody
        ]);
    }

    /**
     * 🔹 Notificación al paciente cuando su cita fue APROBADA
     */
    public function notifyPatientApproved($patientEmail, $appointmentData): bool|string
    {
        $patient = $appointmentData['patient'] ?? [];
        $appointment = $appointmentData['appointment'] ?? [];
        $doctor = $appointmentData['doctor'] ?? [];
        $status = "Approved"; // Estado aprobado

        // Renderiza plantilla de aprobación
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

    /**
     * 🔹 Envía recordatorio al paciente sobre su cita
     */
    public function sendReminderToPatient($patientEmail, $appointmentData): bool|string
    {
        $patient = $appointmentData['patient'] ?? [];
        $appointment = $appointmentData['appointment'] ?? [];
        $doctor = $appointmentData['doctor'] ?? [];
        $status = "Reminder"; // Recordatorio

        // Renderiza plantilla de recordatorio
        ob_start();
        extract(compact('patient', 'appointment', 'doctor', 'status'));
        include APP_DIR . "views/emails/appointment_reminder.php"; 
        $patientBody = ob_get_clean();

        return $this->send([
            "to"      => $patientEmail,
            "subject" => "Appointment Reminder - " . ($appointment['appointment_date'] ?? ''),
            "body"    => $patientBody
        ]);
    }

    /**
     * 🔹 Envía reporte de citas expiradas a un administrador
     */
    public function sendExpiredAppointmentsReport($adminEmail, $data)
    {
        $appointments = $data['appointments'] ?? [];
        $reportDate = date("Y-m-d H:i:s"); // Fecha actual

        ob_start();
        extract(compact('appointments', 'reportDate'));
        include APP_DIR . "views/emails/expired_appointments_report.php"; 
        $body = ob_get_clean();

        return $this->send([
            "to"      => $adminEmail,
            "subject" => "Expired Appointments Report - {$reportDate}",
            "body"    => $body
        ]);
    }

    /**
     * 🔹 Envía reporte de citas pendientes a un administrador
     */
    public function sendPendingAppointmentsReport($adminEmail, $data)
    {
        $appointments = $data['appointments'] ?? [];
        $reportDate = date("Y-m-d H:i:s");

        ob_start();
        extract(compact('appointments', 'reportDate'));
        include APP_DIR . "views/emails/pending_appointments_report.php"; 
        $body = ob_get_clean();

        return $this->send([
            "to"      => $adminEmail,
            "subject" => "Pending Appointments Report - {$reportDate}",
            "body"    => $body
        ]);
    }

    /**
     * 🔹 Notificación al paciente cuando se crea una cita
     */
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

    /**
     * 🔹 Notificación al doctor cuando se le asigna una nueva cita
     */
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

    /**
     * 🔹 Reporte de citas pendientes para un doctor
     * Puede ser diario o mensual según parámetro $period
     */
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

