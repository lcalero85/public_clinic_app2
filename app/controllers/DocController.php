<?php

/**
 * Doc Page Controller
 * @category  Controller
 */
class DocController extends SecureController
{
	function __construct()
	{
		parent::__construct();
		$this->tablename = "doc";
	}
	/**
	 * List page records
	 * @param $fieldname (filter record by a field) 
	 * @param $fieldvalue (filter field value)
	 * @return BaseView
	 */
	function index($fieldname = null, $fieldvalue = null)
{
    $request = $this->request;
    $db = $this->GetModel();
    $tablename = $this->tablename;
    $fields = array(
        "doc.id",
        "doc.full_names",
        "doc.address",
        "doc.birthdate",
        "doc.gender",
        "doc.Speciality",
        "doc.register_date",
        "doc.id_user",
        "users.full_names AS users_full_names",
        "doc.office_phone",
        "doc.dni",
        "doc.license_number",
        "doc.status"
    );

    // paginación (page_number, page_limit)
    $pagination = $this->get_pagination(MAX_RECORD_COUNT);

    // búsqueda
    if (!empty($request->search)) {
        $text = trim($request->search);
        $search_condition = "(
            doc.id LIKE ? OR 
            doc.full_names LIKE ? OR 
            doc.address LIKE ? OR 
            doc.birthdate LIKE ? OR 
            doc.gender LIKE ? OR 
            doc.Speciality LIKE ? OR 
            doc.register_date LIKE ? OR 
            doc.update_date LIKE ? OR 
            doc.office_phone LIKE ? OR 
            doc.license_number LIKE ? OR 
            doc.dni LIKE ? OR 
            doc.status LIKE ? OR 
            doc.id_user LIKE ? 
        )";
        $search_params = array_fill(0, 13, "%$text%");
        $db->where($search_condition, $search_params);
        $this->view->search_template = "doc/search.php";
    }

    // joins
    $db->join("users", "doc.id_user = users.id_user", "INNER");

    // ordenar
    if (!empty($request->orderby)) {
        $orderby = $request->orderby;
        $ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
        $db->orderBy($orderby, $ordertype);
    } else {
        $db->orderBy("doc.id", ORDER_TYPE);
    }

    // filtro por campo si aplica
    if ($fieldname) {
        $db->where($fieldname, $fieldvalue);
    }

    // 🔹 forzar que no haya duplicados por el join
    $db->groupBy("doc.id");

    // consulta principal
    $tc = $db->withTotalCount();
    $records = $db->get($tablename, $pagination, $fields);
    $records_count = count($records);
    $total_records = intval($tc->totalCount);
    $page_limit = $pagination[1];
    $total_pages = ceil($total_records / $page_limit);

    // preparar datos para la vista
    $data = new stdClass;
    $data->records = $records;
    $data->record_count = $records_count;
    $data->total_records = $total_records;
    $data->total_page = $total_pages;

    if ($db->getLastError()) {
        $this->set_page_error();
    }

    $page_title = $this->view->page_title = "Doctors";
    $this->view->report_filename = date('Y-m-d') . '-' . $page_title;
    $this->view->report_title = $page_title;
    $this->view->report_layout = "report_layout.php";
    $this->view->report_paper_size = "A4";
    $this->view->report_orientation = "portrait";

    $this->render_view("doc/list.php", $data);
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
			"doc.id",
			"doc.full_names",
			"doc.address",
			"doc.birthdate",
			"doc.gender",
			"doc.Speciality",
			"doc.register_date",
			"doc.update_date",
			"doc.id_user",
			"users.full_names AS users_full_names",
			"doc.license_number",
			"doc.university",
			"doc.office_phone",
			"doc.work_email",
			"doc.years_experience",
			"doc.dni",
			"doc.status",
			"doc.photo",



		);
		if ($value) {
			$db->where($rec_id, urldecode($value)); //select record based on field name
		} else {
			$db->where("doc.id", $rec_id);; //select record based on primary key
		}
		$db->join("users", "doc.id_user = users.id_user", "INNER");
		$record = $db->getOne($tablename, $fields);
		if ($record) {
			$this->write_to_log("view", "true");
			$record['update_date'] = human_date($record['update_date']);
			$page_title = $this->view->page_title = "View  Doctors";
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
		return $this->render_view("doc/view.php", $record);
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

        // Campos permitidos
        $fields = $this->fields = array(
            "full_names",
            "address",
            "birthdate",
            "gender",
            "Speciality",
            "register_date",
            "update_date",
            "id_user",
            "license_number",
            "license_issuer",
            "license_issue_date",
            "license_expiry_date",
            "university",
            "years_experience",
            "office_phone",
            "work_email",
            "working_hours",
            "status",
            "dni",
            "photo",
        );

        $postdata = $this->format_request_data($formdata);

        // Reglas de validación
        $this->rules_array = array(
            'full_names' => 'required|max_len,200',
            'address' => 'required',
            'birthdate' => 'required',
            'gender' => 'required',
            'Speciality' => 'required',
            'license_number' => 'required',
            'license_issuer' => 'required',
            'license_issue_date' => 'required',
            'license_expiry_date' => 'required',
            'university' => 'required',
            'years_experience' => 'required',
            'office_phone' => 'required',
            'work_email' => 'required',
            'working_hours' => 'required',
            'status' => 'required',
            'dni' => 'required',
            'photo' => '',
        );

        // Sanitización
        $this->sanitize_array = array(
            'full_names' => 'sanitize_string',
            'address' => 'sanitize_string',
            'birthdate' => 'sanitize_string',
            'gender' => 'sanitize_string',
            'Speciality' => 'sanitize_string',
            'license_number' => 'sanitize_string',
            'license_issuer' => 'sanitize_string',
            'license_issue_date' => 'sanitize_string',
            'license_expiry_date' => 'sanitize_string',
            'university' => 'sanitize_string',
            'years_experience' => 'sanitize_string',
            'office_phone' => 'sanitize_string',
            'work_email' => 'sanitize_string',
            'working_hours' => 'sanitize_string',
            'status' => 'sanitize_string',
            'dni' => 'sanitize_string',
            'photo' => '',
        );

        $this->filter_vals = true;
        $modeldata = $this->modeldata = $this->validate_form($postdata);

        // Asignaciones automáticas
        $modeldata['register_date'] = datetime_now();
        $modeldata['update_date']   = datetime_now();
        $modeldata['id_user']       = USER_ID;

        // Foto (archivo o webcam)
        $photoData = null;
        if (!empty($_FILES['photo_file']['tmp_name'])) {
            $photoData = file_get_contents($_FILES['photo_file']['tmp_name']);
        } elseif (!empty($_POST['photo_webcam'])) {
            $base64    = $_POST['photo_webcam'];
            $photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        }
        $modeldata['photo'] = $photoData ?: null;

        if ($this->validated()) {
            $rec_id = $this->rec_id = $db->insert($tablename, $modeldata);

            if ($rec_id) {
                // --- LOG NORMAL ---
                $this->write_to_log("add", "true");

                // --- ACTIVITY LOG ---
                $db->insert("activity_log", [
                    "user_id" => USER_ID,
                    "type"    => "doctor",
                    "action"  => "New doctor registered: " . $modeldata['full_names'],
                    "level"   => "info",
                ]);

                // Aquí si envías correos o notificaciones internas, 
                // también registra en activity_log:
                /*
                $db->insert("activity_log", [
                    "user_id" => USER_ID,
                    "type"    => "notification",
                    "action"  => "Notification sent for doctor " . $modeldata['full_names'],
                    "level"   => "info",
                ]);
                */

                $this->set_flash_msg("Doctor added successfully", "success");
                return $this->redirect("doc");
            } else {
                $this->set_page_error();
                $this->write_to_log("add", "false");

                // --- ACTIVITY LOG (Error) ---
                $db->insert("activity_log", [
                    "user_id" => USER_ID,
                    "type"    => "doctor",
                    "action"  => "Error registering doctor",
                    "level"   => "error",
                ]);
            }
        }
    }

    $page_title = $this->view->page_title = "Add New Doctor";
    $this->render_view("doc/add.php");
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

    // editable fields (SIN id_user)
    $fields = $this->fields = array(
        "id",
        "full_names",
        "address",
        "birthdate",
        "gender",
        "Speciality",
        "register_date",
        "update_date",
        "dni",
        "office_phone",
        "work_email",
        "status",
        "photo"
    );

    if ($formdata) {
        $postdata = $this->format_request_data($formdata);

        $this->rules_array = array(
            'full_names' => 'required|max_len,200',
            'address' => 'required',
            'birthdate' => 'required',
            'gender' => 'required',
            'Speciality' => 'required',
            'dni' => 'required',
            'office_phone' => 'required',
            'work_email' => 'required',
            'status' => 'required',
            'photo' => '',
        );

        $this->sanitize_array = array(
            'full_names' => 'sanitize_string',
            'address' => 'sanitize_string',
            'birthdate' => 'sanitize_string',
            'gender' => 'sanitize_string',
            'Speciality' => 'sanitize_string',
            'dni' => 'sanitize_string',
            'office_phone' => 'sanitize_string',
            'work_email' => 'sanitize_string',
            'status' => 'sanitize_string',
            'photo' => '',
        );

        $modeldata = $this->modeldata = $this->validate_form($postdata);
        $modeldata['update_date'] = datetime_now();

        // --- Foto: archivo O webcam O nada ---
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
            unset($modeldata['photo']); // 🚀 no modificar si no se envía
        }

        if ($this->validated()) {
            $db->where("doc.id", $rec_id);
            $bool = $db->update($tablename, $modeldata);
            $numRows = $db->getRowCount();

            if ($bool && $numRows) {
                // ✅ Log normal
                $this->write_to_log("edit", "true");

                // ✅ Activity log
                $db->insert("activity_log", [
                    "user_id" => USER_ID,
                    "type"    => "doctor",
                    "action"  => "Doctor updated: " . $modeldata['full_names'],
                    "level"   => "info",
                ]);

                $this->set_flash_msg("Doctor updated successfully", "success");
                return $this->redirect("doc");
            } else {
                if ($db->getLastError()) {
                    $this->set_page_error();
                    $this->write_to_log("edit", "false");

                    // ❌ Activity log error
                    $db->insert("activity_log", [
                        "user_id" => USER_ID,
                        "type"    => "doctor",
                        "action"  => "Error updating doctor (ID: $rec_id)",
                        "level"   => "error",
                    ]);
                } elseif (!$numRows) {
                    $page_error = "No record updated";
                    $this->set_page_error($page_error);
                    $this->set_flash_msg($page_error, "warning");
                    $this->write_to_log("edit", "false");

                    // ⚠️ Activity log advertencia
                    $db->insert("activity_log", [
                        "user_id" => USER_ID,
                        "type"    => "doctor",
                        "action"  => "No changes detected for doctor (ID: $rec_id)",
                        "level"   => "warning",
                    ]);

                    return $this->redirect("doc");
                }
            }
        }
    }

    $db->where("doc.id", $rec_id);
    $data = $db->getOne($tablename, $fields);
    $page_title = $this->view->page_title = "Edit Doctor";

    if (!$data) {
        $this->set_page_error();
    }

    return $this->render_view("doc/edit.php", $data);
}


	/**
	 * Update single field
	 * @param $rec_id (select record by table primary key)
	 * @param $formdata array() from $_POST
	 * @return array
	 */
	function editfield($rec_id = null, $formdata = null)
	{
		$db = $this->GetModel();
		$this->rec_id = $rec_id;
		$tablename = $this->tablename;
		//editable fields
		$fields = $this->fields = array("id", "full_names", "address", "birthdate", "gender", "age", "Speciality", "register_date", "update_date", "id_user", "photo");
		$page_error = null;
		if ($formdata) {
			$postdata = array();
			$fieldname = $formdata['name'];
			$fieldvalue = $formdata['value'];
			$postdata[$fieldname] = $fieldvalue;
			$postdata = $this->format_request_data($postdata);
			$this->rules_array = array(
				'full_names' => 'required|max_len,200',
				'address' => 'required',
				'birthdate' => 'required',
				'gender' => 'required',
				'age' => 'required|max_len,25|min_len,2',
				'Speciality' => 'required',
				'photo' => 'required',
			);
			$this->sanitize_array = array(
				'full_names' => 'sanitize_string',
				'address' => 'sanitize_string',
				'birthdate' => 'sanitize_string',
				'gender' => 'sanitize_string',
				'age' => 'sanitize_string',
				'Speciality' => 'sanitize_string',
				'photo' => 'sanitize_string',
			);
			$this->filter_rules = true; //filter validation rules by excluding fields not in the formdata
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			if ($this->validated()) {
				$db->where("doc.id", $rec_id);;
				$bool = $db->update($tablename, $modeldata);
				$numRows = $db->getRowCount();
				if ($bool && $numRows) {
					$this->write_to_log("edit", "true");
					return render_json(
						array(
							'num_rows' => $numRows,
							'rec_id' => $rec_id,
						)
					);
				} else {
					if ($db->getLastError()) {
						$page_error = $db->getLastError();
					} elseif (!$numRows) {
						$page_error = "No record updated";
					}
					$this->write_to_log("edit", "false");
					render_error($page_error);
				}
			} else {
				render_error($this->view->page_error);
			}
		}
		return null;
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
    $request   = $this->request;
    $db        = $this->GetModel();
    $tablename = $this->tablename;
    $this->rec_id = $rec_id;

    // múltiples ids separados por coma
    $arr_rec_id = array_map('trim', explode(",", $rec_id));

    // Recuperar nombres de los doctores antes de marcar como eliminados
    $db->where("id", $arr_rec_id, "in");
    $doctors = $db->get("doc", null, ["id", "full_names"]);

    // Actualizar status = Inactive (borrado lógico)
    $db->where("doc.id", $arr_rec_id, "in");
    $data = [
        "status"      => "Inactive",
        "update_date" => date_now()
    ];
    $bool = $db->update($tablename, $data);

    if ($bool) {
        $this->write_to_log("delete", "true");
        $this->set_flash_msg("Doctor(s) set to Inactive successfully", "success");

        foreach ($doctors as $doctor) {
            $doctorName = !empty($doctor['full_names']) ? $doctor['full_names'] : "(No name)";

            // Guardar en activity_log
            $db->insert("activity_log", [
                "user_id" => USER_ID,
                "type"    => "doctor",
                "action"  => "Doctor set to Inactive (logical delete): " . $doctorName . " (ID: " . $doctor['id'] . ")",
                "level"   => "info"
            ]);

            // Logger helper (archivo / registro extendido)
            app_logger(
                "warning",
                "doctor",
                "Doctor set to Inactive (logical delete): " . $doctorName . " (ID: " . $doctor['id'] . ")",
                USER_ID
            );
        }
    } elseif ($db->getLastError()) {
        $page_error = $db->getLastError();
        $this->set_flash_msg($page_error, "danger");
        $this->write_to_log("delete", "false");

        app_logger("error", "doctor", "Error setting doctor(s) Inactive ($rec_id): $page_error", USER_ID);
    }

    return $this->redirect("doc");
}




}




