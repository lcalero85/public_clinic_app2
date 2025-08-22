<?php
class ReportController extends BaseController
{
    public function show($report = null): mixed
    {
        if ($report == "historial_clinico") {
            $db = $this->GetModel();
            $patient_id = $_GET['patient_id'] ?? null;

            if ($patient_id) {
                // 🔹 Modo DETALLE
                $sql = "SELECT cp.id_patient, cp.full_names, cp.document_number, cp.birthdate, cp.gender,
                               app.motive, app.appointment_date, pr.description_prescription
                        FROM clinic_patients cp
                        LEFT JOIN appointment_new app ON cp.id_patient = app.id_patient
                        LEFT JOIN clinic_prescription pr ON app.id_appointment = pr.id_appointment
                        WHERE cp.id_patient = ?";
                $rows = $db->rawQuery($sql, [$patient_id]);

                $view_data = [
                    "mode" => "single",
                    "patient" => $rows[0] ?? null,
                    "appointments" => array_map(fn($r) => [
                        "motive" => $r["motive"] ?? "",
                        "appointment_date" => $r["appointment_date"] ?? ""
                    ], $rows),
                    "prescriptions" => array_map(fn($r) => $r["description_prescription"] ?? "", $rows)
                ];
            } else {
                // 🔹 Modo LISTADO
                $sql = "SELECT cp.id_patient, cp.full_names, cp.document_number, cp.birthdate, cp.gender,
                               app.appointment_date, app.motive, pr.description_prescription
                        FROM clinic_patients cp
                        LEFT JOIN appointment_new app ON cp.id_patient = app.id_patient
                        LEFT JOIN clinic_prescription pr ON app.id_appointment = pr.id_appointment
                        ORDER BY cp.full_names ASC";
                $rows = $db->rawQuery($sql);

                $view_data = [
                    "mode" => "list",
                    "rows" => $rows ?? []   // 👈 aseguro que se manda $rows
                ];
            }

            // 🔹 Exportación
            $export = $_GET['export'] ?? null;
            if ($export == "pdf") {
                $html = $this->render_view("../reports/historial_clinico/view.php", $view_data, true);
                ReportExporter::toPDF($html, "HistorialClinico.pdf");
                exit;
            }
            if ($export == "excel") {
                ReportExporter::toExcel($view_data["rows"] ?? [], "HistorialClinico.xlsx");
                exit;
            }
            if ($export == "csv") {
                ReportExporter::toCSV($view_data["rows"] ?? [], "HistorialClinico.csv");
                exit;
            }

            return $this->render_view("../reports/historial_clinico/view.php", $view_data);
        }
    }

    public function clinical_historial()
    {
        return $this->show("historial_clinico");
    }
}



