<?php  
/**
 * Appointments Page Controller
 * @category  Controller
 */
class AppointmentsController extends SecureController{
    function __construct(){
        parent::__construct();
        $this->tablename = "appointment_new"; // 🔹 Cambiado a la vista real
    }

    /**
     * List page records
     */
    function index($fieldname = null , $fieldvalue = null){
        $db = $this->GetModel();
        $tablename = $this->tablename;

        // Selección de campos
        $fields = array(
            "app.id_appointment",
            "cp.full_names AS patient_name",
            "cp.gender",
            "cp.birthdate",
            "dc.full_names AS doctor_name",
            "app.motive",
            "app.appointment_date",
            "app.register_date",
            "apps.status AS appointment_status",
            "users.full_names AS created_by",
            "CONCAT(cp.id_patient, DATE_FORMAT(app.register_date, '%d%m%Y')) AS clinical_file"
        );

        // JOINs necesarios (LEFT para no perder registros si faltan relaciones)
        $db->join("clinic_patients cp", "app.id_patient = cp.id_patient", "LEFT");
        $db->join("doc dc", "app.id_doc = dc.id", "LEFT");
        $db->join("appointment_status apps", "app.id_status_appointment = apps.id", "LEFT");
        $db->join("users", "app.id_user = users.id_user", "LEFT");

        // Orden por defecto
        $db->orderBy("app.id_appointment", "DESC");

        // Alias de la tabla principal
        $records = $db->get($tablename . " app", null, $fields);

        // 🔹 Calcular edad + ID incremental
        $i = 1;
        foreach($records as &$rec){
            if(!empty($rec['birthdate'])){
                $dob = new DateTime($rec['birthdate']);
                $now = new DateTime();
                $rec['age'] = $dob->diff($now)->y;
            } else {
                $rec['age'] = "N/A";
            }
            $rec['id_incremental'] = $i++;
        }

        // Preparar datos para la vista
        $data = new stdClass;
        $data->records = $records;

        $page_title = $this->view->page_title = "Appointments Report";
        $this->view->report_filename = date('Y-m-d') . '-' . $page_title;
        $this->view->report_title = $page_title;
        $this->view->report_layout = "report_layout.php";
        $this->view->report_paper_size = "A4";
        $this->view->report_orientation = "portrait";

        $this->render_view("appointments/list.php", $data); 
    }
}
