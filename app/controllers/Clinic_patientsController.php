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
	function index($fieldname = null, $fieldvalue = null)
	{
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
		$pagination = $this->get_pagination(MAX_RECORD_COUNT); // get current pagination e.g array(page_number, page_limit)
		//search table record
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
			$search_params = array(
				"%$text%",
				"%$text%",
				"%$text%",
				"%$text%",
				"%$text%",
				"%$text%",
				"%$text%",
				"%$text%",
				"%$text%",
				"%$text%",
				"%$text%",
				"%$text%"
			);
			//setting search conditions
			$db->where($search_condition, $search_params);
			//template to use when ajax search
			$this->view->search_template = "clinic_patients/search.php";
		}
		$db->join("users", "clinic_patients.id_user = users.id_user", "INNER");
		$db->join("patients_status", "clinic_patients.id_status = patients_status.id", "INNER");
		if (!empty($request->orderby)) {
			$orderby = $request->orderby;
			$ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
			$db->orderBy($orderby, $ordertype);
		} else {
			$db->orderBy("clinic_patients.id_patient", ORDER_TYPE);
		}
		if ($fieldname) {
			$db->where($fieldname, $fieldvalue); //filter by a single field name
		}
		$tc = $db->withTotalCount();
		$records = $db->get($tablename, $pagination, $fields);
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
		$this->render_view("clinic_patients/list.php", $data); //render the full page
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
				// 1) Imagen desde el selector de archivos
				$photoData = file_get_contents($_FILES['photo_file']['tmp_name']);
			} elseif (!empty($_POST['photo_webcam'])) {
				// 2) Imagen tomada con webcam (dataURL base64)
				$base64 = $_POST['photo_webcam'];
				$photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
			}

			// Asignar al campo real de la tabla
			$modeldata['photo'] = $photoData ?: null;

			if ($this->validated()) {
				$rec_id = $this->rec_id = $db->insert($tablename, $modeldata);
				if ($rec_id) {
					$this->write_to_log("add", "true");
					$this->set_flash_msg("Record added successfully", "success");
					return $this->redirect("clinic_patients");
				} else {
					$this->set_page_error();
					$this->write_to_log("add", "false");
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
    //editable fields
    $fields = $this->fields = array(
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
        "id_user",
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
            "workplace" => 'required',
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
            "workplace" => 'sanitize_string',
            'id_document_type' => 'sanitize_string',
            'id_marital_status' => 'sanitize_string',
            'document_number' => 'sanitize_string',
        );

        $modeldata = $this->modeldata = $this->validate_form($postdata);
        $modeldata['register_date'] = date_now();
        $modeldata['update_date'] = date_now();
        $modeldata['id_user'] = USER_ID;

        // --- Foto: archivo O webcam O nada (NO modificar si no se envía) ---
        $photoData = null;

        if (!empty($_FILES['photo_file']['tmp_name'])) {
            // 1) Imagen desde el selector de archivos
            $photoData = file_get_contents($_FILES['photo_file']['tmp_name']);
        } elseif (!empty($_POST['photo_webcam'])) {
            // 2) Imagen tomada con webcam (dataURL base64)
            $base64 = $_POST['photo_webcam'];
            $photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        }

        if ($photoData !== null) {
            $modeldata['photo'] = $photoData;
        } else {
            // 🚀 Si no hay foto nueva, no actualizar ese campo
            unset($modeldata['photo']);
        }

        if ($this->validated()) {
            $db->where("clinic_patients.id_patient", $rec_id);
            $bool = $db->update($tablename, $modeldata);
            $numRows = $db->getRowCount(); //number of affected rows. 0 = no record field updated
            if ($bool && $numRows) {
                $this->write_to_log("edit", "true");
                $this->set_flash_msg("Record updated successfully", "success");
                return $this->redirect("clinic_patients");
            } else {
                if ($db->getLastError()) {
                    $this->set_page_error();
                    $this->write_to_log("edit", "false");
                } elseif (!$numRows) {
                    //not an error, but no record was updated
                    $page_error = "No record updated";
                    $this->set_page_error($page_error);
                    $this->set_flash_msg($page_error, "warning");
                    $this->write_to_log("edit", "false");
                    return $this->redirect("clinic_patients");
                }
            }
        }
    }

    $db->where("clinic_patients.id_patient", $rec_id);
    $data = $db->getOne($tablename, $fields);
    $page_title = $this->view->page_title = "Edit  Clinic Patients";
    if (!$data) {
        $this->set_page_error();
    }
    return $this->render_view("clinic_patients/edit.php", $data);
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
		$fields = $this->fields = array("id_patient", "full_names", "address", "gender", "birthdate", "age", "register_observations", "referred", "diseases", "phone_patient", "manager", "register_date", "update_date", "id_user", "id_status", "email", "document_number");
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
				'gender' => 'required',
				'birthdate' => 'required',
				'age' => 'required|max_len,10|min_len,2',
				'register_observations' => 'required',
				'referred' => 'required|max_len,100',
				'diseases' => 'required',
				'phone_patient' => 'required|max_len,20',
				'manager' => 'required|max_len,100',
				'id_status' => 'required',
				'email' => 'required|valid_email',
				'document_number' => 'required',
			);
			$this->sanitize_array = array(
				'full_names' => 'sanitize_string',
				'address' => 'sanitize_string',
				'gender' => 'sanitize_string',
				'birthdate' => 'sanitize_string',
				'age' => 'sanitize_string',
				'register_observations' => 'sanitize_string',
				'referred' => 'sanitize_string',
				'diseases' => 'sanitize_string',
				'phone_patient' => 'sanitize_string',
				'manager' => 'sanitize_string',
				'id_status' => 'sanitize_string',
				'email' => 'sanitize_string',
				'document_number' => 'sanitize_string',
			);
			$this->filter_rules = true; //filter validation rules by excluding fields not in the formdata
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			if ($this->validated()) {
				$db->where("clinic_patients.id_patient", $rec_id);;
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
	function delete($rec_id = null)
	{
		Csrf::cross_check();
		$request = $this->request;
		$db = $this->GetModel();
		$tablename = $this->tablename;
		$this->rec_id = $rec_id;
		//form multiple delete, split record id separated by comma into array
		$arr_rec_id = array_map('trim', explode(",", $rec_id));
		$db->where("clinic_patients.id_patient", $arr_rec_id, "in");
		$bool = $db->delete($tablename);
		if ($bool) {
			$this->write_to_log("delete", "true");
			$this->set_flash_msg("Record deleted successfully", "success");
		} elseif ($db->getLastError()) {
			$page_error = $db->getLastError();
			$this->set_flash_msg($page_error, "danger");
			$this->write_to_log("delete", "false");
		}
		return $this->redirect("clinic_patients");
	}
}
