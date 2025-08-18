<?php

/**
 * Appointment_new Page Controller
 * @category  Controller
 */
class Appointment_newController extends SecureController
{
	function __construct()
	{
		parent::__construct();
		$this->tablename = "appointment_new";
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
			"appointment_new.id_appointment",
			"appointment_new.id_patient",
			"clinic_patients.full_names AS clinic_patients_full_names",
			"appointment_new.id_doc",
			"doc.full_names AS doc_full_names",
			"CONCAT(appointment_new.motive,' - ',IFNULL(appointment_new.descritption,'')) AS motive_summary",
			"appointment_new.historial",
			"appointment_new.appointment_date",
			"appointment_new.register_date",
			"appointment_new.nex_appointment_date",
			"appointment_new.id_status_appointment",
			"appointment_new.id_appointment_type",
			"appointment_status.status AS status",
			"appointment_new.priority",
			"appointment_new.id_user",
			"users.full_names AS users_full_names",
		);

		$pagination = $this->get_pagination(MAX_RECORD_COUNT); // get current pagination e.g array(page_number, page_limit)
		//search table record
		if (!empty($request->search)) {
			$text = trim($request->search);

			$search_condition = "
      (
        appointment_new.id_appointment LIKE ? OR
        appointment_new.id_patient LIKE ? OR
        appointment_new.id_doc LIKE ? OR
        CONCAT(appointment_new.motive,' ',IFNULL(appointment_new.descritption,'')) LIKE ? OR
        appointment_new.historial LIKE ? OR
        appointment_new.appointment_date LIKE ? OR
        appointment_new.register_date LIKE ? OR
        appointment_new.nex_appointment_date LIKE ? OR
        appointment_new.id_status_appointment LIKE ? OR
		appointment_status.status LIKE ? OR
		appointment_new.priority LIKE ? OR
		appointment_new.id_appointment_type LIKE ? OR
        appointment_new.id_user LIKE ? OR
        clinic_patients.full_names LIKE ? OR
        doc.full_names LIKE ? OR
        users.full_names LIKE ?
      )
    ";

			// Genera la cantidad exacta de parámetros según los "?" del string
			$placeholders = substr_count($search_condition, '?');
			$search_params = array_fill(0, $placeholders, "%{$text}%");
			//setting search conditions
			$db->where($search_condition, $search_params);
			//template to use when ajax search
			$this->view->search_template = "appointment_new/search.php";
		}
		$db->join("clinic_patients", "appointment_new.id_patient = clinic_patients.id_patient", "INNER");
		$db->join("doc", "appointment_new.id_doc = doc.id", "INNER");
		$db->join("appointment_status", "appointment_new.id_status_appointment = appointment_status.id", "INNER");
		$db->join("users", "appointment_new.id_user = users.id_user", "INNER");
		if (!empty($request->orderby)) {
			$orderby = $request->orderby;
			$ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
			$db->orderBy($orderby, $ordertype);
		} else {
			$db->orderBy("appointment_new.id_appointment", ORDER_TYPE);
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
		if (!empty($records)) {
			foreach ($records as &$record) {
				$record['register_date'] = human_date($record['register_date']);
			}
		}
		$data = new stdClass;
		$data->records = $records;
		$data->record_count = $records_count;
		$data->total_records = $total_records;
		$data->total_page = $total_pages;
		if ($db->getLastError()) {
			$this->set_page_error();
		}
		$page_title = $this->view->page_title = "Appointment ";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";
		$this->render_view("appointment_new/list.php", $data); //render the full page
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
			"appointment_new.id_appointment",
			"appointment_new.id_patient",
			"clinic_patients.full_names AS clinic_patients_full_names",
			"appointment_new.motive",
			"appointment_new.descritption",
			"appointment_new.historial",
			"appointment_new.appointment_date",
			"appointment_new.register_date",
			"appointment_new.nex_appointment_date",
			"appointment_new.id_user",
			"users.full_names AS users_full_names",
			"appointment_new.id_doc",
			"doc.full_names AS doc_full_names",
			"doc.Speciality AS Speciality",
			"appointment_new.priority",
			"reminder_preference",
			"follow_up_required",
			"appointment_new.id_status_appointment",
			"appointment_status.status AS appointment_status_status"
		);
		if ($value) {
			$db->where($rec_id, urldecode($value)); //select record based on field name
		} else {
			$db->where("appointment_new.id_appointment", $rec_id);; //select record based on primary key
		}
		$db->join("clinic_patients", "appointment_new.id_patient = clinic_patients.id_patient", "INNER");
		$db->join("users", "appointment_new.id_user = users.id_user", "INNER");
		$db->join("doc", "appointment_new.id_doc = doc.id", "INNER");
		$db->join("appointment_status", "appointment_new.id_status_appointment = appointment_status.id", "INNER");
		$record = $db->getOne($tablename, $fields);
		if ($record) {
			$this->write_to_log("view", "true");
			$record['register_date'] = human_date($record['register_date']);
			$page_title = $this->view->page_title = "View  Appointment New";
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
		return $this->render_view("appointment_new/view.php", $record);
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
				"id_patient",
				"id_doc",
				"motive",
				"descritption",
				"appointment_date",
				"register_date",
				"id_user",
				"id_appointment_type",
				"id_status_appointment",
				"priority",
				"reminder_preference",
				"follow_up_required",
			);
			$postdata = $this->format_request_data($formdata);
			$this->rules_array = array(
				'id_patient' => 'required',
				'id_doc' => 'required',
				'motive' => 'required',
				'descritption' => 'required',
				'appointment_date' => 'required',
				'id_status_appointment' => '',
				'id_appointment_type' => 'required',
				'priority' => 'required',
				'reminder_preference' => 'required',
				'follow_up_required' => 'required',

			);
			$this->sanitize_array = array(
				'id_patient' => 'sanitize_string',
				'id_doc' => 'sanitize_string',
				'motive' => 'sanitize_string',
				'descritption' => 'sanitize_string',
				'appointment_date' => 'sanitize_string',
				'id_status_appointment' => 'sanitize_string',
				'id_appointment_type' => 'sanitize_string',
				'priority' => 'sanitize_string',
				'reminder_preference' => 'sanitize_string',
				'follow_up_required' => 'sanitize_string',

			);
			$this->filter_vals = true; //set whether to remove empty fields
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			$modeldata['register_date'] = datetime_now();
			$modeldata['id_user'] = USER_ID;
			$modeldata['id_status_appointment'] = "1";
			if ($this->validated()) {
				$rec_id = $this->rec_id = $db->insert($tablename, $modeldata);
				if ($rec_id) {
					$this->write_to_log("add", "true");
					$this->set_flash_msg("Record added successfully", "success");
					return	$this->redirect("appointment_new");
				} else {
					$this->set_page_error();
					$this->write_to_log("add", "false");
				}
			}
		}
		$page_title = $this->view->page_title = "Add New Appointment ";
		$this->render_view("appointment_new/add.php");
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
		$fields = $this->fields = array("id_appointment", "id_patient", "id_doc", "motive", "descritption", "historial", "nex_appointment_date", "register_date", "update_date", "id_user", "id_status_appointment");
		if ($formdata) {
			$postdata = $this->format_request_data($formdata);
			$this->rules_array = array(
				'id_patient' => 'required',
				'id_doc' => 'required',
				'motive' => 'required',
				'nex_appointment_date' => 'required',
				'id_status_appointment' => 'required',

			);
			$this->sanitize_array = array(
				'id_patient' => 'sanitize_string',
				'id_doc' => 'sanitize_string',
				'motive' => 'sanitize_string',
				'nex_appointment_date' => 'sanitize_string',
				'id_status_appointment' => 'sanitize_string',
			);
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			$modeldata['register_date'] = datetime_now();
			$modeldata['update_date'] = datetime_now();
			$modeldata['id_user'] = USER_ID;

			if ($this->validated()) {
				$db->where("appointment_new.id_appointment", $rec_id);;
				$bool = $db->update($tablename, $modeldata);
				$numRows = $db->getRowCount(); //number of affected rows. 0 = no record field updated
				if ($bool && $numRows) {
					$this->write_to_log("edit", "true");
					$this->set_flash_msg("Record updated successfully", "success");
					return $this->redirect("appointment_new");
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
						return	$this->redirect("appointment_new");
					}
				}
			}
		}
		$db->where("appointment_new.id_appointment", $rec_id);;
		$data = $db->getOne($tablename, $fields);
		$page_title = $this->view->page_title = "Edit  Appointment New";
		if (!$data) {
			$this->set_page_error();
		}
		return $this->render_view("appointment_new/edit.php", $data);
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
		$fields = $this->fields = array("id_appointment", "id_patient", "id_doc", "motive", "descritption", "historial", "appointment_date", "nex_appointment_date", "register_date", "update_date", "id_user", "id_status_appointment");
		$page_error = null;
		if ($formdata) {
			$postdata = array();
			$fieldname = $formdata['name'];
			$fieldvalue = $formdata['value'];
			$postdata[$fieldname] = $fieldvalue;
			$postdata = $this->format_request_data($postdata);
			$this->rules_array = array(
				'id_patient' => 'required',
				'id_doc' => 'required',
				'motive' => 'required',
				'descritption' => 'required',
				'historial' => 'required',
				'appointment_date' => 'required',
				'nex_appointment_date' => 'required',
			);
			$this->sanitize_array = array(
				'id_patient' => 'sanitize_string',
				'id_doc' => 'sanitize_string',
				'motive' => 'sanitize_string',
				'descritption' => 'sanitize_string',
				'historial' => 'sanitize_string',
				'appointment_date' => 'sanitize_string',
				'nex_appointment_date' => 'sanitize_string',
			);
			$this->filter_rules = true; //filter validation rules by excluding fields not in the formdata
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			if ($this->validated()) {
				$db->where("appointment_new.id_appointment", $rec_id);;
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
		$db->where("appointment_new.id_appointment", $arr_rec_id, "in");
		$bool = $db->delete($tablename);
		if ($bool) {
			$this->write_to_log("delete", "true");
			$this->set_flash_msg("Record deleted successfully", "success");
		} elseif ($db->getLastError()) {
			$page_error = $db->getLastError();
			$this->set_flash_msg($page_error, "danger");
			$this->write_to_log("delete", "false");
		}
		return	$this->redirect("appointment_new");
	}

	public function request($formdata = null)
	{
		$this->view->page_title = "Request Appointment";
		return $this->render_view("my_appointment/request.php");
	}

	public function request_submit($formdata = null): bool
	{
		if ($formdata) {
			$db = $this->GetModel();
			$tablename = "appointment_new";

			$postdata = $this->format_request_data($formdata);

			// 🔹 Buscar el id_patient real a partir del usuario logueado
			$patient = $db->rawQueryOne("SELECT id_patient FROM clinic_patients WHERE id_user = ?", array(USER_ID));

			if (!$patient) {
				$this->set_flash_msg("No patient record found for this user", "danger");
				return false;
			}

			$modeldata = array();
			$modeldata['id_patient'] = $patient['id_patient']; // ✅ Guardar id_patient correcto
			$modeldata['motive'] = $postdata['motive'];
			$modeldata['descritption'] = $postdata['descritption'];
			$modeldata['requested_date'] = $postdata['requested_date'];
			$modeldata['register_date'] = date("Y-m-d");
			$modeldata['update_date'] = date("Y-m-d");
			$modeldata['id_status_appointment'] = 2; // Pending Confirmation
			$modeldata['created_by'] = USER_ID;

			$rec_id = $db->insert($tablename, $modeldata);

			if ($rec_id) {
				$this->set_flash_msg("Appointment request submitted successfully", "success");
				return $this->redirect("my_appointment");
			} else {
				$this->set_flash_msg("Error saving request", "danger");
			}
		}
		return false;
	}


	/**
	 * Mostrar solicitudes pendientes (solo admin)
	 */
	public function request_manage(): ?string
	{
		$db = $this->GetModel();

		// Consulta mejorada con JOINs y orden
		$sql = "SELECT 
                app.id_appointment,
                cp.full_names AS patient_name,
                app.motive,
                app.descritption,
                app.appointment_date,
                app.register_date,
                st.status AS appointment_status
            FROM appointment_new AS app
            INNER JOIN clinic_patients AS cp 
                ON app.id_patient = cp.id_patient
            INNER JOIN appointment_status AS st
                ON app.id_status_appointment = st.id
            WHERE app.id_status_appointment = 2
            ORDER BY app.register_date DESC";

		$records = $db->rawQuery($sql);

		$this->view->page_title = "Pending Appointment Requests";
		return $this->render_view("appointment_new/request_manage.php", ["data" => $records]);
	}


	/**
	 * Aprobar cita (fecha solicitada = fecha aprobada)
	 */
	public function approve($id = null)
	{
		$db = $this->GetModel();
		$tablename = "appointment_new";

		$update = array(
			"approved_date" => $db->now(),
			"id_status_appointment" => 1, // Scheduled
			"updated_by" => USER_ID
		);

		$db->where("id_appointment", $id);
		$result = $db->update($tablename, $update);

		if ($result) {
			$this->set_flash_msg("Appointment approved successfully", "success");
		} else {
			$this->set_flash_msg("Error approving appointment", "danger");
		}
		return $this->redirect("appointment_new/request_manage");
	}

	/**
	 * Denegar cita con comentario
	 */
	public function deny($id = null, $formdata = null)
	{
		$db = $this->GetModel();
		$tablename = "appointment_new";

		if ($formdata) {
			$postdata = $this->format_request_data($formdata);
			$update = array(
				"id_status_appointment" => 7, // Cancelled
				"admin_response" => $postdata['admin_response'],
				"updated_by" => USER_ID
			);

			$db->where("id_appointment", $id);
			$db->update($tablename, $update);

			$this->set_flash_msg("Appointment denied", "danger");
			return $this->redirect("appointment_new/request_manage");
		}

		// Renderizar formulario simple de rechazo
		$this->view->page_title = "Deny Appointment";
		return $this->render_view("appointment_new/deny.php", array("id" => $id));
	}

	/**
	 * Reprogramar cita con nueva fecha
	 */
	public function reschedule($id = null, $formdata = null)
	{
		$db = $this->GetModel();
		$tablename = "appointment_new";

		if ($formdata) {
			$postdata = $this->format_request_data($formdata);

			$update = array(
				"approved_date" => $postdata['approved_date'],
				"id_status_appointment" => 5, // Rescheduled
				"admin_response" => $postdata['admin_response'],
				"updated_by" => USER_ID
			);

			$db->where("id_appointment", $id);
			$db->update($tablename, $update);

			$this->set_flash_msg("Appointment rescheduled successfully", "success");
			return $this->redirect("appointment_new/request_manage");
		}

		// Renderizar formulario de reprogramación
		$this->view->page_title = "Reschedule Appointment";
		return $this->render_view("appointment_new/reschedule.php", array("id" => $id));
	}
}
