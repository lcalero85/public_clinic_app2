<?php
class My_appointmentController extends SecureController
{
    function __construct()
    {
        parent::__construct();
        $this->tablename = "My_Appointment";
    }

    /**
     * Custom list page
     * @return BaseView
     */
   function index()
{
    $request = $this->request;
    $db = $this->GetModel();
    $pagination = null;

    // Detectar rol numérico
    $userRoleId = USER_ROLE_ID; // 1=admin, 2=assistant, 3=doctor, 4=Patients

    if ($userRoleId == 3) {
        // DOCTOR
        $sqltext = "SELECT SQL_CALC_FOUND_ROWS  
            cp.full_names AS full_names,
            app.motive,
            app.descritption,
            app.historial,
            app.appointment_date,
            app.register_date,
            dc.full_names AS doctor_name,
            apps.status
        FROM appointment_new AS app
        INNER JOIN clinic_patients AS cp ON app.id_patient = cp.id_patient
        INNER JOIN doc AS dc ON app.id_doc = dc.id
        INNER JOIN appointment_status AS apps ON apps.id = app.id_status_appointment
        WHERE dc.id_user = " . USER_ID;

        // 🔹 DOCTOR → filtrar por citas de HOY si corresponde
        if (!empty($_GET['today']) && $_GET['today'] == 1) {
            $sqltext .= " AND DATE(app.appointment_date) = CURDATE()";
        }
    } elseif ($userRoleId == 4) {
        // PACIENTE
        $sqltext = "SELECT SQL_CALC_FOUND_ROWS  
            cp.full_names AS full_names,
            app.motive,
            app.descritption,
            app.historial,
            app.appointment_date,
            app.register_date,
            dc.full_names AS doctor_name,
            apps.status
        FROM appointment_new AS app
        INNER JOIN clinic_patients AS cp ON app.id_patient = cp.id_patient
        INNER JOIN doc AS dc ON app.id_doc = dc.id
        INNER JOIN appointment_status AS apps ON apps.id = app.id_status_appointment
        WHERE cp.id_user = " . USER_ID;

        // 🔹 PACIENTE → también puede ver solo citas de HOY
        if (!empty($_GET['today']) && $_GET['today'] == 1) {
            $sqltext .= " AND DATE(app.appointment_date) = CURDATE()";
        }
    } else {
        // ADMIN o ASSISTANT
        $sqltext = "SELECT SQL_CALC_FOUND_ROWS  
            cp.full_names AS full_names,
            app.motive,
            app.descritption,
            app.historial,
            app.appointment_date,
            app.register_date,
            dc.full_names AS doctor_name,
            apps.status
        FROM appointment_new AS app
        INNER JOIN clinic_patients AS cp ON app.id_patient = cp.id_patient
        INNER JOIN doc AS dc ON app.id_doc = dc.id
        INNER JOIN appointment_status AS apps ON apps.id = app.id_status_appointment
        WHERE 1=1";
    }

    $queryparams = null;

    // Ordenamiento
    if (!empty($request->orderby)) {
        $orderby = $request->orderby;
        $ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
        $db->orderBy($orderby, $ordertype);
    } else {
        $db->orderBy("app.appointment_date", ORDER_TYPE);
    }

    // Paginación
    $pagination = $this->get_pagination(MAX_RECORD_COUNT);

    // 🔹 Ejecutar consulta con paginación
    $records = $db->rawQuery($sqltext . " LIMIT {$pagination[0]}, {$pagination[1]}", $queryparams);

    // Obtener total de registros
    $tc = $db->rawQueryOne("SELECT FOUND_ROWS() AS totalCount");
    $total_records = intval($tc['totalCount']);

    $records_count = count($records);
    $page_limit = (!empty($pagination) ? $pagination[1] : 1);
    $total_pages = ($page_limit > 0 ? ceil($total_records / $page_limit) : 1);

    // Datos para la vista
    $data = new stdClass;
    $data->records = $records;
    $data->record_count = $records_count;
    $data->total_records = $total_records;
    $data->total_page = $total_pages;
    $data->show_pagination = true;

    if ($db->getLastError()) {
        $this->set_page_error();
    }

    // Configuración de la vista
    $page_title = $this->view->page_title = "My Appointment";
    $this->view->report_filename = date('Y-m-d') . '-' . $page_title;
    $this->view->report_title = $page_title;
    $this->view->report_layout = "report_layout.php";
    $this->view->report_paper_size = "A4";
    $this->view->report_orientation = "portrait";

    $this->render_view("my_appointment/list.php", $data);
}
}