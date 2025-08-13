<?php 
/**
 * Clinic_prescription Page Controller
 * @category  Controller
 */
class Clinic_prescriptionController extends SecureController{
	function __construct(){
		parent::__construct();
		$this->tablename = "clinic_prescription";
	}
	/**
     * List page records
     * @param $fieldname (filter record by a field) 
     * @param $fieldvalue (filter field value)
     * @return BaseView
     */
	function index($fieldname = null , $fieldvalue = null){
		$request = $this->request;
		$db = $this->GetModel();
		$tablename = $this->tablename;
		$fields = array("clinic_prescription.id_prescription", 
			"clinic_prescription.id_appointment", 
			"appointment_new.appointment_date AS appointment_new_appointment_date", 
			"clinic_prescription.id_patient", 
			"clinic_patients.full_names AS clinic_patients_full_names", 
			"clinic_prescription.id_doctor", 
			"doc.full_names AS doc_full_names", 
			"clinic_prescription.description_prescription", 
			"clinic_prescription.additional_comments", 
			"clinic_prescription.id_user", 
			"users.full_names AS users_full_names", 
			"clinic_prescription.register_date");
		$pagination = $this->get_pagination(MAX_RECORD_COUNT); // get current pagination e.g array(page_number, page_limit)
		//search table record
		if(!empty($request->search)){
			$text = trim($request->search); 
			$search_condition = "(
				clinic_prescription.id_prescription LIKE ? OR 
				clinic_prescription.id_appointment LIKE ? OR 
				clinic_prescription.id_patient LIKE ? OR 
				clinic_prescription.id_doctor LIKE ? OR 
				clinic_prescription.description_prescription LIKE ? OR 
				clinic_prescription.additional_comments LIKE ? OR 
				clinic_prescription.id_user LIKE ? OR 
				clinic_prescription.register_date LIKE ? OR 
				clinic_prescription.update_date LIKE ?
			)";
			$search_params = array(
				"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
			);
			//setting search conditions
			$db->where($search_condition, $search_params);
			 //template to use when ajax search
			$this->view->search_template = "clinic_prescription/search.php";
		}
		$db->join("appointment_new", "clinic_prescription.id_appointment = appointment_new.id_appointment", "INNER");
		$db->join("clinic_patients", "clinic_prescription.id_patient = clinic_patients.id_patient", "INNER");
		$db->join("doc", "clinic_prescription.id_doctor = doc.id", "INNER");
		$db->join("users", "clinic_prescription.id_user = users.id_user", "INNER");
		if(!empty($request->orderby)){
			$orderby = $request->orderby;
			$ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
			$db->orderBy($orderby, $ordertype);
		}
		else{
			$db->orderBy("clinic_prescription.id_prescription", ORDER_TYPE);
		}
		if($fieldname){
			$db->where($fieldname , $fieldvalue); //filter by a single field name
		}
		$tc = $db->withTotalCount();
		$records = $db->get($tablename, $pagination, $fields);
		$records_count = count($records);
		$total_records = intval($tc->totalCount);
		$page_limit = $pagination[1];
		$total_pages = ceil($total_records / $page_limit);
		if(	!empty($records)){
			foreach($records as &$record){
				$record['register_date'] = human_date($record['register_date']);
			}
		}
		$data = new stdClass;
		$data->records = $records;
		$data->record_count = $records_count;
		$data->total_records = $total_records;
		$data->total_page = $total_pages;
		if($db->getLastError()){
			$this->set_page_error();
		}
		$page_title = $this->view->page_title = "Clinic Prescription";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";
		$this->render_view("clinic_prescription/list.php", $data); //render the full page
	}
	/**
     * View record detail 
	 * @param $rec_id (select record by table primary key) 
     * @param $value value (select record by value of field name(rec_id))
     * @return BaseView
     */
	function view($rec_id = null, $value = null){
		$request = $this->request;
		$db = $this->GetModel();
		$rec_id = $this->rec_id = urldecode($rec_id);
		$tablename = $this->tablename;
		$fields = array("clinic_prescription.id_prescription", 
			"clinic_prescription.id_appointment", 
			"appointment_new.appointment_date AS appointment_new_appointment_date", 
			"clinic_prescription.id_patient", 
			"clinic_patients.full_names AS clinic_patients_full_names", 
			"clinic_prescription.id_doctor", 
			"doc.full_names AS doc_full_names", 
			"clinic_prescription.description_prescription", 
			"clinic_prescription.additional_comments", 
			"clinic_prescription.id_user", 
			"users.full_names AS users_full_names", 
			"clinic_prescription.register_date", 
			"clinic_prescription.update_date");
		if($value){
			$db->where($rec_id, urldecode($value)); //select record based on field name
		}
		else{
			$db->where("clinic_prescription.id_prescription", $rec_id);; //select record based on primary key
		}
		$db->join("appointment_new", "clinic_prescription.id_appointment = appointment_new.id_appointment", "INNER");
		$db->join("clinic_patients", "clinic_prescription.id_patient = clinic_patients.id_patient", "INNER");
		$db->join("doc", "clinic_prescription.id_doctor = doc.id", "INNER");
		$db->join("users", "clinic_prescription.id_user = users.id_user", "INNER");  
		$record = $db->getOne($tablename, $fields );
		if($record){
			$this->write_to_log("view", "true");
			$record['register_date'] = human_date($record['register_date']);
$record['update_date'] = human_date($record['update_date']);
			$page_title = $this->view->page_title = "View  Clinic Prescription";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";
		}
		else{
			if($db->getLastError()){
				$this->set_page_error();
			}
			else{
				$this->set_page_error("No record found");
			}
			$this->write_to_log("view", "false");
		}
		return $this->render_view("clinic_prescription/view.php", $record);
	}
	/**
     * Insert new record to the database table
	 * @param $formdata array() from $_POST
     * @return BaseView
     */
	function add($formdata = null){
		if($formdata){
			$db = $this->GetModel();
			$tablename = $this->tablename;
			$request = $this->request;
			//fillable fields
			$fields = $this->fields = array("id_patient","id_doctor","id_appointment","description_prescription","additional_comments","register_date","update_date","id_user");
			$postdata = $this->format_request_data($formdata);
			$this->rules_array = array(
				'id_patient' => 'required',
				'id_doctor' => 'required',
				'id_appointment' => 'required',
				'description_prescription' => 'required',
				'additional_comments' => 'required',
			);
			$this->sanitize_array = array(
				'id_patient' => 'sanitize_string',
				'id_doctor' => 'sanitize_string',
				'id_appointment' => 'sanitize_string',
				'description_prescription' => 'sanitize_string',
				'additional_comments' => 'sanitize_string',
			);
			$this->filter_vals = true; //set whether to remove empty fields
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			$modeldata['register_date'] = datetime_now();
$modeldata['update_date'] = datetime_now();
$modeldata['id_user'] = USER_ID;
			if($this->validated()){
				$rec_id = $this->rec_id = $db->insert($tablename, $modeldata);
				if($rec_id){
					$this->write_to_log("add", "true");
					$this->set_flash_msg("Record added successfully", "success");
					return	$this->redirect("clinic_prescription");
				}
				else{
					$this->set_page_error();
					$this->write_to_log("add", "false");
				}
			}
		}
		$page_title = $this->view->page_title = "Add New Clinic Prescription";
		$this->render_view("clinic_prescription/add.php");
	}
	/**
     * Update table record with formdata
	 * @param $rec_id (select record by table primary key)
	 * @param $formdata array() from $_POST
     * @return array
     */
	function edit($rec_id = null, $formdata = null){
		$request = $this->request;
		$db = $this->GetModel();
		$this->rec_id = $rec_id;
		$tablename = $this->tablename;
		 //editable fields
		$fields = $this->fields = array("id_prescription","id_patient","id_doctor","id_appointment","description_prescription","additional_comments","register_date","update_date","id_user");
		if($formdata){
			$postdata = $this->format_request_data($formdata);
			$this->rules_array = array(
				'id_patient' => 'required',
				'id_doctor' => 'required',
				'id_appointment' => 'required',
				'description_prescription' => 'required',
				'additional_comments' => 'required',
			);
			$this->sanitize_array = array(
				'id_patient' => 'sanitize_string',
				'id_doctor' => 'sanitize_string',
				'id_appointment' => 'sanitize_string',
				'description_prescription' => 'sanitize_string',
				'additional_comments' => 'sanitize_string',
			);
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			$modeldata['register_date'] = datetime_now();
$modeldata['update_date'] = datetime_now();
$modeldata['id_user'] = USER_ID;
			if($this->validated()){
				$db->where("clinic_prescription.id_prescription", $rec_id);;
				$bool = $db->update($tablename, $modeldata);
				$numRows = $db->getRowCount(); //number of affected rows. 0 = no record field updated
				if($bool && $numRows){
					$this->write_to_log("edit", "true");
					$this->set_flash_msg("Record updated successfully", "success");
					return $this->redirect("clinic_prescription");
				}
				else{
					if($db->getLastError()){
						$this->set_page_error();
						$this->write_to_log("edit", "false");
					}
					elseif(!$numRows){
						//not an error, but no record was updated
						$page_error = "No record updated";
						$this->set_page_error($page_error);
						$this->set_flash_msg($page_error, "warning");
						$this->write_to_log("edit", "false");
						return	$this->redirect("clinic_prescription");
					}
				}
			}
		}
		$db->where("clinic_prescription.id_prescription", $rec_id);;
		$data = $db->getOne($tablename, $fields);
		$page_title = $this->view->page_title = "Edit  Clinic Prescription";
		if(!$data){
			$this->set_page_error();
		}
		return $this->render_view("clinic_prescription/edit.php", $data);
	}
	/**
     * Update single field
	 * @param $rec_id (select record by table primary key)
	 * @param $formdata array() from $_POST
     * @return array
     */
	function editfield($rec_id = null, $formdata = null){
		$db = $this->GetModel();
		$this->rec_id = $rec_id;
		$tablename = $this->tablename;
		//editable fields
		$fields = $this->fields = array("id_prescription","id_patient","id_doctor","id_appointment","description_prescription","additional_comments","register_date","update_date","id_user");
		$page_error = null;
		if($formdata){
			$postdata = array();
			$fieldname = $formdata['name'];
			$fieldvalue = $formdata['value'];
			$postdata[$fieldname] = $fieldvalue;
			$postdata = $this->format_request_data($postdata);
			$this->rules_array = array(
				'id_patient' => 'required',
				'id_doctor' => 'required',
				'id_appointment' => 'required',
				'description_prescription' => 'required',
				'additional_comments' => 'required',
			);
			$this->sanitize_array = array(
				'id_patient' => 'sanitize_string',
				'id_doctor' => 'sanitize_string',
				'id_appointment' => 'sanitize_string',
				'description_prescription' => 'sanitize_string',
				'additional_comments' => 'sanitize_string',
			);
			$this->filter_rules = true; //filter validation rules by excluding fields not in the formdata
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			if($this->validated()){
				$db->where("clinic_prescription.id_prescription", $rec_id);;
				$bool = $db->update($tablename, $modeldata);
				$numRows = $db->getRowCount();
				if($bool && $numRows){
					$this->write_to_log("edit", "true");
					return render_json(
						array(
							'num_rows' =>$numRows,
							'rec_id' =>$rec_id,
						)
					);
				}
				else{
					if($db->getLastError()){
						$page_error = $db->getLastError();
					}
					elseif(!$numRows){
						$page_error = "No record updated";
					}
					$this->write_to_log("edit", "false");
					render_error($page_error);
				}
			}
			else{
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
	function delete($rec_id = null){
		Csrf::cross_check();
		$request = $this->request;
		$db = $this->GetModel();
		$tablename = $this->tablename;
		$this->rec_id = $rec_id;
		//form multiple delete, split record id separated by comma into array
		$arr_rec_id = array_map('trim', explode(",", $rec_id));
		$db->where("clinic_prescription.id_prescription", $arr_rec_id, "in");
		$bool = $db->delete($tablename);
		if($bool){
			$this->write_to_log("delete", "true");
			$this->set_flash_msg("Record deleted successfully", "success");
		}
		elseif($db->getLastError()){
			$page_error = $db->getLastError();
			$this->set_flash_msg($page_error, "danger");
			$this->write_to_log("delete", "false");
		}
		return	$this->redirect("clinic_prescription");
	}
}
