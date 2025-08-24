<?php 
/**
 * Actives_patients Page Controller
 * @category  Controller
 */
class Actives_patientsController extends SecureController{
	function __construct(){
		parent::__construct();
		$this->tablename = "actives_patients";
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
		$fields = array("actives_patients.id_patient", 
			"actives_patients.full_names", 
			"actives_patients.address", 
			"actives_patients.gender", 
			"actives_patients.age", 
			"actives_patients.birthdate", 
			"actives_patients.register_observations", 
			"actives_patients.referred", 
			"actives_patients.phone_patient", 
			"actives_patients.manager", 
			"actives_patients.diseases", 
			"actives_patients.register_date", 
			"actives_patients.id_status", 
			"patients_status.status AS patients_status_status", 
			"actives_patients.id_user", 
			"users.full_names AS users_full_names");
		$pagination = $this->get_pagination(MAX_RECORD_COUNT); // get current pagination e.g array(page_number, page_limit)
		//search table record
		if(!empty($request->search)){
			$text = trim($request->search); 
			$search_condition = "(
				actives_patients.id_patient LIKE ? OR 
				actives_patients.full_names LIKE ? OR 
				actives_patients.address LIKE ? OR 
				actives_patients.gender LIKE ? OR 
				actives_patients.age LIKE ? OR 
				actives_patients.birthdate LIKE ? OR 
				actives_patients.register_observations LIKE ? OR 
				actives_patients.referred LIKE ? OR 
				actives_patients.phone_patient LIKE ? OR 
				actives_patients.manager LIKE ? OR 
				actives_patients.diseases LIKE ? OR 
				actives_patients.register_date LIKE ? OR 
				actives_patients.update_date LIKE ? OR 
				actives_patients.id_status LIKE ? OR 
				actives_patients.id_user LIKE ?
			)";
			$search_params = array(
				"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
			);
			//setting search conditions
			$db->where($search_condition, $search_params);
			 //template to use when ajax search
			$this->view->search_template = "actives_patients/search.php";
		}
		$db->join("patients_status", "actives_patients.id_status = patients_status.id", "INNER");
		$db->join("users", "actives_patients.id_user = users.id_user", "INNER");
		if(!empty($request->orderby)){
			$orderby = $request->orderby;
			$ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
			$db->orderBy($orderby, $ordertype);
		}
		else{
			$db->orderBy("actives_patients.id_patient", ORDER_TYPE);
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
		$page_title = $this->view->page_title = "Actives Patients";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";
		$this->render_view("actives_patients/list.php", $data); //render the full page
	}

}
