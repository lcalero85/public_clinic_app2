<?php

/**
 * Clinic_patients Page Controller
 * @category  Controller
 */
class Clinic_patientsController extends SecureController
{
	
	function __construct()
	{
		parent::__construct();
		$this->tablename = "clinic_patients";
	}
	/**
	 * List page records
	 * @param $fieldname (filter record by a field) 
	 * @param $fieldvalue (filter field value)
	 * @return BaseView
	 */
	public function index($fieldname = null, $fieldvalue = null)
{
    require_once __DIR__ . "/../../helpers/logger.php";
    $request = $this->request;
    $db = $this->GetModel();
    $tablename = $this->tablename;
    $fields = array(
        "clinic_patients.id_patient",
        "clinic_patients.full_names",
        "clinic_patients.address",
        "clinic_patients.gender",
        "clinic_patients.phone_patient",
        "clinic_patients.register_date",
        "clinic_patients.id_user",
        "clinic_patients.id_status",
        "clinic_patients.email",
        "clinic_patients.document_number",
        "clinic_patients.age",
        "clinic_patients.birthdate",
    );

    // paginación (page_number, page_limit)
    $pagination = $this->get_pagination(MAX_RECORD_COUNT);

    // búsqueda
    if (!empty($request->search)) {
        $text = trim($request->search);
        $search_condition = "(
            clinic_patients.id_patient LIKE ? OR 
            clinic_patients.full_names LIKE ? OR 
            clinic_patients.address LIKE ? OR 
            clinic_patients.gender LIKE ? OR 
            clinic_patients.age LIKE ? OR 
            clinic_patients.phone_patient LIKE ? OR 
            clinic_patients.register_date LIKE ? OR 
            clinic_patients.id_user LIKE ? OR 
            clinic_patients.id_status LIKE ? OR 
            clinic_patients.email LIKE ? OR 
            clinic_patients.document_number LIKE ? OR  
            clinic_patients.birthdate LIKE ? 
        )";
        $search_params = array_fill(0, 12, "%$text%");
        $db->where($search_condition, $search_params);
        $this->view->search_template = "clinic_patients/search.php";
    }

    // joins
    $db->join("users", "clinic_patients.id_user = users.id_user", "INNER");
    $db->join("patients_status", "clinic_patients.id_status = patients_status.id", "INNER");
    // ordenar
    if (!empty($request->orderby)) {
        $orderby = $request->orderby;
        $ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
        $db->orderBy($orderby, $ordertype);
    } else {
        $db->orderBy("clinic_patients.id_patient", ORDER_TYPE);
    }

    // filtro por campo si aplica
    if ($fieldname) {
        $db->where($fieldname, $fieldvalue);
    }
    // evitar duplicados

    $db->groupBy("clinic_patients.id_patient");

    // consulta principal
    $tc = $db->withTotalCount();
    $records = $db->get($tablename, $pagination, $fields);

    // datos de la vista
    $records_count = count($records);
    $total_records = intval($tc->totalCount);
    $page_limit = $pagination[1];
    $total_pages = ceil($total_records / $page_limit);

    $data = new stdClass;
    $data->records = $records;
    $data->record_count = $records_count;
    $data->total_records = $total_records;
    $data->total_page = $total_pages;
    $data->show_pagination = true;

    if ($db->getLastError()) {
        $this->set_page_error();
    }

    $page_title = $this->view->page_title = "Patients";
    $this->view->report_filename = date('Y-m-d') . '-' . $page_title;
    $this->view->report_title = $page_title;
    $this->view->report_layout = "report_layout.php";
    $this->view->report_paper_size = "A4";
    $this->view->report_orientation = "portrait";

    $this->render_view("clinic_patients/list.php", $data);
}


	/**
	 * View record detail 
	 * @param $rec_id (select record by table primary key) 
	 * @param $value value (select record by value of field name(rec_id))
	 * @return BaseView
	 */
	function view($rec_id = null, $value = null)
	{
		$request = $this->request;
		$db = $this->GetModel();
		$rec_id = $this->rec_id = urldecode($rec_id);
		$tablename = $this->tablename;
		$fields = array(
			"clinic_patients.id_patient",
			"clinic_patients.full_names",
			"clinic_patients.address",
			"clinic_patients.gender",
			"clinic_patients.birthdate",
			"clinic_patients.register_observations",
			"clinic_patients.referred",
			"clinic_patients.phone_patient",
			"clinic_patients.manager",
			"clinic_patients.diseases",
			"clinic_patients.register_date",
			"clinic_patients.update_date",
			"clinic_patients.id_user",
			"users.full_names AS users_full_names",
			"clinic_patients.id_status",
			"patients_status.status AS patients_status_status",
			"clinic_patients.email",
			"document_number",
			"age",
			"allergies",
			"emergency_contact_phone",
			"document_type_catalog.type AS document_type_name",
			"blood_type_catalog.type AS blood_type_name",
			"clinic_patients.photo",
			"marital_status_catalog.status AS status",
			"workplace"
		);
		if ($value) {
			$db->where($rec_id, urldecode($value)); //select record based on field name
		} else {
			$db->where("clinic_patients.id_patient", $rec_id);; //select record based on primary key
		}
		$db->join("users", "clinic_patients.id_user = users.id_user", "INNER");
		$db->join("patients_status", "clinic_patients.id_status = patients_status.id", "INNER");
		$db->join("document_type_catalog", "clinic_patients.id_document_type = document_type_catalog.id", "INNER");
		$db->join("blood_type_catalog", "clinic_patients.id_blood_type = blood_type_catalog.id", "INNER");
		$db->join("marital_status_catalog", "clinic_patients.id_marital_status = marital_status_catalog.id", "INNER");

		$record = $db->getOne($tablename, $fields);
		if ($record) {
			$this->write_to_log("view", "true");
			$page_title = $this->view->page_title = "View  Clinic Patients";
			$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
			$this->view->report_title = $page_title;
			$this->view->report_layout = "report_layout.php";
			$this->view->report_paper_size = "A4";
			$this->view->report_orientation = "portrait";
		} else {
			if ($db->getLastError()) {
				$this->set_page_error();
			} else {
				$this->set_page_error("No record found");
			}
			$this->write_to_log("view", "false");
		}
		return $this->render_view("clinic_patients/view.php", $record);
	}
	/**
	 * Insert new record to the database table
	 * @param $formdata array() from $_POST
	 * @return BaseView
	 */
	function add($formdata = null) 
{
    if ($formdata) {
        $db = $this->GetModel();
        $tablename = $this->tablename;
        $request = $this->request;

        //fillable fields
        $fields = $this->fields = array(
            "full_names",
            "address",
            "gender",
            "birthdate",
            "register_observations",
            "referred",
            "diseases",
            "phone_patient",
            "manager",
            "register_date",
            "update_date",
            "id_user",
            "id_status",
            "email",
            "id_document_type",
            "document_number",
            "occupation",
            "allergies",
            "emergency_contact_phone",
            "id_blood_type",
            'photo',
            "id_marital_status",
            "workplace"
        );

        $postdata = $this->format_request_data($formdata);

        $this->rules_array = array(
            'full_names' => 'required|max_len,200',
            'address' => 'required',
            'gender' => 'required',
            'birthdate' => 'required',
            'register_observations' => 'required',
            'referred' => 'required|max_len,100',
            'diseases' => 'required',
            'phone_patient' => 'required|max_len,20',
            'manager' => 'required|max_len,100',
            'id_status' => 'required',
            'email' => 'required|valid_email',
            'id_document_type' => 'required',
            'document_number' => 'required',
            'occupation' => 'required',
            'allergies' => 'required',
            'emergency_contact_phone' => 'required',
            'id_blood_type' => 'required',
            'photo' => '',
            'id_marital_status' => 'required',
            'workplace' => 'required',
        );

        $this->sanitize_array = array(
            'full_names' => 'sanitize_string',
            'address' => 'sanitize_string',
            'gender' => 'sanitize_string',
            'birthdate' => 'sanitize_string',
            'register_observations' => 'sanitize_string',
            'referred' => 'sanitize_string',
            'diseases' => 'sanitize_string',
            'phone_patient' => 'sanitize_string',
            'manager' => 'sanitize_string',
            'id_status' => 'sanitize_string',
            'email' => 'sanitize_string',
            'id_document_type' => 'sanitize_string',
            'document_number' => 'sanitize_string',
            'occupation' => 'sanitize_string',
            'allergies' => 'sanitize_string',
            'emergency_contact_phone' => 'sanitize_string',
            'id_blood_type' => 'sanitize_string',
            'photo' => '',
            'id_marital_status' => 'sanitize_string',
            'workplace' => 'sanitize_string',
        );

        $this->filter_vals = true; //set whether to remove empty fields
        $modeldata = $this->modeldata = $this->validate_form($postdata);

        $modeldata['register_date'] = date_now();
        $modeldata['update_date'] = date_now();
        $modeldata['id_user'] = USER_ID;

        // --- Foto: archivo O webcam O nada (NULL) ---
        $photoData = null;
        if (!empty($_FILES['photo_file']['tmp_name'])) {
            $photoData = file_get_contents($_FILES['photo_file']['tmp_name']);
        } elseif (!empty($_POST['photo_webcam'])) {
            $base64 = $_POST['photo_webcam'];
            $photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        }
        $modeldata['photo'] = $photoData ?: null;

        if ($this->validated()) {
            $rec_id = $this->rec_id = $db->insert($tablename, $modeldata);

            if ($rec_id) {
                // 🔹 Log normal del sistema
                $this->write_to_log("add", "true");

                // 🔹 Registrar en activity_log (solo nivel INFO para dashboard)
                $db->insert("activity_log", [
                    "user_id" => USER_ID,
                    "type"    => "patient",
                    "action"  => "New patient registered: " . $modeldata['full_names'],
                    "level"   => "info"
                ]);

                $this->set_flash_msg("Record added successfully", "success");
                return $this->redirect("clinic_patients");
            } else {
                $this->set_page_error();
                $this->write_to_log("add", "false");

                // 🔹 También podemos guardar un error en el log de auditoría
                $db->insert("activity_log", [
                    "user_id" => USER_ID,
                    "type"    => "patient",
                    "action"  => "Error registering new patient",
                    "level"   => "error"
                ]);
            }
        }
    }

    $page_title = $this->view->page_title = "Add New Clinic Patients";
    $this->render_view("clinic_patients/add.php");
}

	/**
	 * Update table record with formdata
	 * @param $rec_id (select record by table primary key)
	 * @param $formdata array() from $_POST
	 * @return array
	 */
	function edit($rec_id = null, $formdata = null)
{
    $request = $this->request;
    $db = $this->GetModel();
    $this->rec_id = $rec_id;
    $tablename = $this->tablename;

    // ✅ Cargar el logger correcto
    require_once __DIR__ . "/../../helpers/logger.php";

    // Verificar si este paciente está asociado a un user creado por admin
    $allow_edit_id_user = false;
    $db->where("id_patient", $rec_id);
    $id_user = $db->getValue("clinic_patients", "id_user");

    if ($id_user) {
        $db->where("id_user", $id_user);
        $created_by = $db->getValue("users", "created_by");
        if (!empty($created_by)) {
            $allow_edit_id_user = true; 
        }
    }

    // editable fields
    $fields = array(
        "id_patient",
        "full_names",
        "address",
        "gender",
        "birthdate",
        "register_observations",
        "referred",
        "diseases",
        "phone_patient",
        "manager",
        "register_date",
        "update_date",
        "id_status",
        "email",
        "emergency_contact_phone",
        "occupation",
        "photo",
        "workplace",
        "id_document_type",
        "id_marital_status",
        "document_number"
    );

    if ($allow_edit_id_user) {
        $fields[] = "id_user";
    }

    $this->fields = $fields;

    if ($formdata) {
        $postdata = $this->format_request_data($formdata);

        $this->rules_array = array(
            'full_names' => 'required|max_len,200',
            'address' => 'required',
            'gender' => 'required',
            'birthdate' => 'required',
            'register_observations' => 'required',
            'referred' => 'required|max_len,100',
            'diseases' => 'required',
            'phone_patient' => 'required|max_len,20',
            'manager' => 'required|max_len,100',
            'id_status' => 'required',
            'email' => 'required|valid_email',
            'emergency_contact_phone' => 'required|max_len,100',
            'occupation' => 'required|max_len,100',
            'photo' => '',
            'workplace' => 'required',
            'id_document_type' => 'required',
            'id_marital_status' => 'required',
            'document_number' => 'required',
        );

        $this->sanitize_array = array(
            'full_names' => 'sanitize_string',
            'address' => 'sanitize_string',
            'gender' => 'sanitize_string',
            'birthdate' => 'sanitize_string',
            'register_observations' => 'sanitize_string',
            'referred' => 'sanitize_string',
            'diseases' => 'sanitize_string',
            'phone_patient' => 'sanitize_string',
            'manager' => 'sanitize_string',
            'id_status' => 'sanitize_string',
            'email' => 'sanitize_string',
            'emergency_contact_phone' => 'sanitize_string',
            'occupation' => 'sanitize_string',
            'photo' => '',
            'workplace' => 'sanitize_string',
            'id_document_type' => 'sanitize_string',
            'id_marital_status' => 'sanitize_string',
            'document_number' => 'sanitize_string',
        );

        $modeldata = $this->modeldata = $this->validate_form($postdata);
        $modeldata['register_date'] = date_now();
        $modeldata['update_date'] = date_now();

        if (!$allow_edit_id_user) {
            unset($modeldata['id_user']);
        }

        // --- Foto ---
        $photoData = null;
        if (!empty($_FILES['photo_file']['tmp_name'])) {
            $photoData = file_get_contents($_FILES['photo_file']['tmp_name']);
        } elseif (!empty($_POST['photo_webcam'])) {
            $base64 = $_POST['photo_webcam'];
            $photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        }

        if ($photoData !== null) {
            $modeldata['photo'] = $photoData;
        } else {
            unset($modeldata['photo']);
        }

        if ($this->validated()) {
            $db->where("clinic_patients.id_patient", $rec_id);
            $bool = $db->update($tablename, $modeldata);
            $numRows = $db->getRowCount();

            if ($bool && $numRows) {
                $this->write_to_log("edit", "true");
                $this->set_flash_msg("Record updated successfully", "success");

                // 📌 Registrar en activity_log y logs/app_logs.txt
                app_logger("info", "patient", "Patient updated: " . $modeldata['full_names'], USER_ID);

                return $this->redirect("clinic_patients");
            } else {
                if ($db->getLastError()) {
                    $this->set_page_error();
                    $this->write_to_log("edit", "false");

                    app_logger("error", "patient", "DB error updating patient ID: $rec_id (" . $db->getLastError() . ")", USER_ID);

                } elseif (!$numRows) {
                    $page_error = "No record updated";
                    $this->set_page_error($page_error);
                    $this->set_flash_msg($page_error, "warning");
                    $this->write_to_log("edit", "false");

                    app_logger("warning", "patient", "No changes detected for patient ID: $rec_id", USER_ID);

                    return $this->redirect("clinic_patients");
                }
            }
        }
    }

    $db->where("clinic_patients.id_patient", $rec_id);
    $data = $db->getOne($tablename, $fields);
    $page_title = $this->view->page_title = "Edit Clinic Patients";
    if (!$data) {
        $this->set_page_error();
    }
    return $this->render_view("clinic_patients/edit.php", $data);
}

	/**
	 * Delete record from the database
	 * Support multi delete by separating record id by comma.
	 * @return BaseView
	 */
	public function delete($rec_id = null): mixed
{
    require_once __DIR__ . "/../../helpers/logger.php"; // incluir logger correctamente

    Csrf::cross_check();
    $request = $this->request;
    $db = $this->GetModel();
    $tablename = $this->tablename;
    $this->rec_id = $rec_id;

    // múltiples ids separados por coma
    $arr_rec_id = array_map('trim', explode(",", $rec_id));

    // Recuperar nombres de los pacientes antes de marcar como eliminados
    $db->where("id_patient", $arr_rec_id, "in");
    $patients = $db->get("clinic_patients", null, ["id_patient", "full_names"]);

    // Actualizar id_status = 3 (Deleted)
    $db->where("clinic_patients.id_patient", $arr_rec_id, "in");
    $data = [
        "id_status"  => 3,
        "update_date" => date_now()
    ];
    $bool = $db->update($tablename, $data);

    if ($bool) {
        $this->write_to_log("delete", "true");
        $this->set_flash_msg("Record(s) marked as deleted successfully", "success");

        foreach ($patients as $patient) {
            app_logger("warning", "patient", "Patient deleted: " . $patient['full_names'], USER_ID);
        }

    } elseif ($db->getLastError()) {
        $page_error = $db->getLastError();
        $this->set_flash_msg($page_error, "danger");
        $this->write_to_log("delete", "false");

        app_logger("error", "patient", "Error deleting patients ($rec_id): $page_error", USER_ID);
    }

    return $this->redirect("clinic_patients");
}


}



