<?php 
class ReportController extends BaseController
{
    public function clinical_report(): ?string
    {
        $db = $this->GetModel();

        // 🔹 Base query
        $sql = "
            SELECT 
                cp.id_patient,
                cp.full_names,
                cp.document_number,
                cp.birthdate,
                TIMESTAMPDIFF(YEAR, cp.birthdate, CURDATE()) AS age,
                CONCAT(cp.id_patient, DATE_FORMAT(cp.register_date, '%d%m%Y')) AS clinical_file,
                cp.gender,
                cp.photo,
                COALESCE(app.appointment_date, '') AS appointment_date,
                COALESCE(app.motive, '') AS motive,
                COALESCE(pr.description_prescription, '') AS description_prescription
            FROM clinic_patients cp
            LEFT JOIN appointment_new app ON cp.id_patient = app.id_patient
            LEFT JOIN clinic_prescription pr ON app.id_appointment = pr.id_appointment
        ";

        // 🔒 Si el usuario es paciente → filtrar solo su historial por id_user
        if (USER_ROLE_NAME == "Patients" || USER_ROLE_ID == 4) {
            $sql .= " WHERE cp.id_user = " . $db->escape(USER_ID);
        }

        $sql .= " ORDER BY cp.full_names DESC";

        $records = $db->rawQuery($sql);

        // 🔹 Convertimos foto binaria a Base64 si existe
        foreach ($records as &$rec) {
            if (!empty($rec['photo'])) {
                $rec['photo'] = "data:image/jpeg;base64," . base64_encode($rec['photo']);
            } else {
                $rec['photo'] = null;
            }
        }

        // ✅ Pasamos rows directo a la vista
        return $this->render_view("../reports/clinical_report/view.php", [
            "records" => $records
        ]);
    }

    // ✅ Alias para que no falle si alguien entra a /report/clinical_historial
    public function clinical_historial(): ?string
    {
        return $this->clinical_report();
    }
}

