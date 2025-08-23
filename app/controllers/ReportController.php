<?php
class ReportController extends BaseController
{
    public function clinical_report(): ?string
    {
        $db = $this->GetModel();

        $sql = "
            SELECT 
                cp.id_patient,
                cp.full_names,
                cp.document_number,
                cp.birthdate,
                cp.gender,
                COALESCE(app.appointment_date, '') AS appointment_date,
                COALESCE(app.motive, '') AS motive,
                COALESCE(pr.description_prescription, '') AS description_prescription
            FROM clinic_patients cp
            LEFT JOIN appointment_new app ON cp.id_patient = app.id_patient
            LEFT JOIN clinic_prescription pr ON app.id_appointment = pr.id_appointment
            ORDER BY cp.full_names ASC
        ";

        $records = $db->rawQuery($sql);
        //var_dump($rows);
        //exit;
        // ✅ Pasamos rows directo a la vista
        return $this->render_view("../reports/clinical_report/view.php", [
			"records" => $records]);
    }


    // ✅ Alias para que no falle si alguien entra a /report/clinical_historial
    public function clinical_historial(): ?string
    {
        return $this->clinical_report();
    }
}

