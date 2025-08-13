<?php 
/**
 * Invoices Page Controller
 * @category  Controller
 */
class InvoicesController extends SecureController{
	function __construct(){
		parent::__construct();
		$this->tablename = "invoices";
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
		$fields = array("invoices.id_invoice", 
			"invoices.invoice_num", 
			"invoices.id_concept", 
			"invoices_concepts.concept AS invoices_concepts_concept", 
			"invoices.id_patient", 
			"clinic_patients.full_names AS clinic_patients_full_names", 
			"invoices.quantity", 
			"invoices.price", 
			"invoices.total_invoice", 
			"invoices.date_invoice", 
			"invoices.id_invoice_status", 
			"invoice_status.status AS invoice_status_status", 
			"invoices.register_date", 
			"invoices.id_user", 
			"users.full_names AS users_full_names");
		$pagination = $this->get_pagination(MAX_RECORD_COUNT); // get current pagination e.g array(page_number, page_limit)
		//search table record
		if(!empty($request->search)){
			$text = trim($request->search); 
			$search_condition = "(
				invoices.id_invoice LIKE ? OR 
				invoices.invoice_num LIKE ? OR 
				invoices.id_concept LIKE ? OR 
				invoices.id_patient LIKE ? OR 
				invoices.comments LIKE ? OR 
				invoices.quantity LIKE ? OR 
				invoices.price LIKE ? OR 
				invoices.total_invoice LIKE ? OR 
				invoices.date_invoice LIKE ? OR 
				invoices.id_invoice_status LIKE ? OR 
				invoices.register_date LIKE ? OR 
				invoices.update_date LIKE ? OR 
				invoices.id_user LIKE ?
			)";
			$search_params = array(
				"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
			);
			//setting search conditions
			$db->where($search_condition, $search_params);
			 //template to use when ajax search
			$this->view->search_template = "invoices/search.php";
		}
		$db->join("invoices_concepts", "invoices.id_concept = invoices_concepts.id", "INNER");
		$db->join("clinic_patients", "invoices.id_patient = clinic_patients.id_patient", "INNER");
		$db->join("invoice_status", "invoices.id_invoice_status = invoice_status.id", "INNER");
		$db->join("users", "invoices.id_user = users.id_user", "INNER");
		if(!empty($request->orderby)){
			$orderby = $request->orderby;
			$ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
			$db->orderBy($orderby, $ordertype);
		}
		else{
			$db->orderBy("invoices.id_invoice", ORDER_TYPE);
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
		$data = new stdClass;
		$data->records = $records;
		$data->record_count = $records_count;
		$data->total_records = $total_records;
		$data->total_page = $total_pages;
		if($db->getLastError()){
			$this->set_page_error();
		}
		$page_title = $this->view->page_title = "Invoices";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";
		$this->view->report_hidden_fields = array('id_invoice', 'comments', 'id_invoice_status', 'register_date', 'update_date', 'id_user');
		$this->render_view("invoices/list.php", $data); //render the full page
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
		$fields = array("invoices.id_invoice", 
			"invoices.invoice_num", 
			"invoices.id_concept", 
			"invoices_concepts.concept AS invoices_concepts_concept", 
			"invoices.id_patient", 
			"clinic_patients.full_names AS clinic_patients_full_names", 
			"invoices.quantity", 
			"invoices.price", 
			"invoices.total_invoice", 
			"invoices.date_invoice", 
			"invoices.id_invoice_status", 
			"invoice_status.status AS invoice_status_status", 
			"invoices.register_date", 
			"invoices.id_user", 
			"users.full_names AS users_full_names");
		if($value){
			$db->where($rec_id, urldecode($value)); //select record based on field name
		}
		else{
			$db->where("invoices.id_invoice", $rec_id);; //select record based on primary key
		}
		$db->join("invoices_concepts", "invoices.id_concept = invoices_concepts.id", "INNER");
		$db->join("clinic_patients", "invoices.id_patient = clinic_patients.id_patient", "INNER");
		$db->join("invoice_status", "invoices.id_invoice_status = invoice_status.id", "INNER");
		$db->join("users", "invoices.id_user = users.id_user", "INNER");  
		$record = $db->getOne($tablename, $fields );
		if($record){
			$this->write_to_log("view", "true");
			$page_title = $this->view->page_title = "View  Invoices";
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
		return $this->render_view("invoices/view.php", $record);
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
			$fields = $this->fields = array("id_patient","id_concept","comments","quantity","price","total_invoice","date_invoice","id_invoice_status","register_date","invoice_num","id_user");
			$postdata = $this->format_request_data($formdata);
			$this->rules_array = array(
				'id_patient' => 'required',
				'id_concept' => 'required',
				'comments' => 'required',
				'quantity' => 'required|numeric|max_numeric,100',
				'price' => 'required|numeric|max_numeric,100',
				'total_invoice' => 'required|numeric|max_numeric,100',
				'date_invoice' => 'required',
				'id_invoice_status' => 'required',
			);
			$this->sanitize_array = array(
				'id_patient' => 'sanitize_string',
				'id_concept' => 'sanitize_string',
				'comments' => 'sanitize_string',
				'quantity' => 'sanitize_string',
				'price' => 'sanitize_string',
				'total_invoice' => 'sanitize_string',
				'date_invoice' => 'sanitize_string',
				'id_invoice_status' => 'sanitize_string',
			);
			$this->filter_vals = true; //set whether to remove empty fields
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			$modeldata['register_date'] = datetime_now();
$modeldata['invoice_num'] = "INV00".time();
$modeldata['id_user'] = USER_ID;
			if($this->validated()){
				$rec_id = $this->rec_id = $db->insert($tablename, $modeldata);
				if($rec_id){
					$this->write_to_log("add", "true");
					$this->set_flash_msg("Record added successfully", "success");
					return	$this->redirect("invoices");
				}
				else{
					$this->set_page_error();
					$this->write_to_log("add", "false");
				}
			}
		}
		$page_title = $this->view->page_title = "Add New Invoices";
		$this->render_view("invoices/add.php");
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
		$fields = $this->fields = array("id_invoice","id_patient","id_concept","comments","quantity","price","total_invoice","date_invoice","id_invoice_status","register_date","invoice_num","update_date","id_user");
		if($formdata){
			$postdata = $this->format_request_data($formdata);
			$this->rules_array = array(
				'id_patient' => 'required',
				'id_concept' => 'required',
				'comments' => 'required',
				'quantity' => 'required|numeric|max_numeric,100',
				'price' => 'required|numeric|max_numeric,100',
				'total_invoice' => 'required|numeric|max_numeric,100',
				'date_invoice' => 'required',
				'id_invoice_status' => 'required',
			);
			$this->sanitize_array = array(
				'id_patient' => 'sanitize_string',
				'id_concept' => 'sanitize_string',
				'comments' => 'sanitize_string',
				'quantity' => 'sanitize_string',
				'price' => 'sanitize_string',
				'total_invoice' => 'sanitize_string',
				'date_invoice' => 'sanitize_string',
				'id_invoice_status' => 'sanitize_string',
			);
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			$modeldata['register_date'] = datetime_now();
$modeldata['invoice_num'] = "INV00".time();
$modeldata['update_date'] = datetime_now();
$modeldata['id_user'] = USER_ID;
			if($this->validated()){
				$db->where("invoices.id_invoice", $rec_id);;
				$bool = $db->update($tablename, $modeldata);
				$numRows = $db->getRowCount(); //number of affected rows. 0 = no record field updated
				if($bool && $numRows){
					$this->write_to_log("edit", "true");
					$this->set_flash_msg("Record updated successfully", "success");
					return $this->redirect("invoices");
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
						return	$this->redirect("invoices");
					}
				}
			}
		}
		$db->where("invoices.id_invoice", $rec_id);;
		$data = $db->getOne($tablename, $fields);
		$page_title = $this->view->page_title = "Edit  Invoices";
		if(!$data){
			$this->set_page_error();
		}
		return $this->render_view("invoices/edit.php", $data);
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
		$fields = $this->fields = array("id_invoice","id_patient","id_concept","comments","quantity","price","total_invoice","date_invoice","id_invoice_status","register_date","invoice_num","update_date","id_user");
		$page_error = null;
		if($formdata){
			$postdata = array();
			$fieldname = $formdata['name'];
			$fieldvalue = $formdata['value'];
			$postdata[$fieldname] = $fieldvalue;
			$postdata = $this->format_request_data($postdata);
			$this->rules_array = array(
				'id_patient' => 'required',
				'id_concept' => 'required',
				'comments' => 'required',
				'quantity' => 'required|numeric|max_numeric,100',
				'price' => 'required|numeric|max_numeric,100',
				'total_invoice' => 'required|numeric|max_numeric,100',
				'date_invoice' => 'required',
				'id_invoice_status' => 'required',
			);
			$this->sanitize_array = array(
				'id_patient' => 'sanitize_string',
				'id_concept' => 'sanitize_string',
				'comments' => 'sanitize_string',
				'quantity' => 'sanitize_string',
				'price' => 'sanitize_string',
				'total_invoice' => 'sanitize_string',
				'date_invoice' => 'sanitize_string',
				'id_invoice_status' => 'sanitize_string',
			);
			$this->filter_rules = true; //filter validation rules by excluding fields not in the formdata
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			if($this->validated()){
				$db->where("invoices.id_invoice", $rec_id);;
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
		$db->where("invoices.id_invoice", $arr_rec_id, "in");
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
		return	$this->redirect("invoices");
	}
}
