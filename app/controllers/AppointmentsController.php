<?php 
/**
 * Appointments Page Controller
 * @category  Controller
 */
class AppointmentsController extends SecureController{
	function __construct(){
		parent::__construct();
		$this->tablename = "appointments";
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
		$fields = array("appointments.id_appointment", 
			"appointments.id_patient", 
			"clinic_patients.full_names AS clinic_patients_full_names", 
			"appointments.id_doc", 
			"doc.full_names AS doc_full_names", 
			"appointments.motive", 
			"appointments.descritption", 
			"appointments.historial", 
			"appointments.register_date", 
			"appointments.id_user", 
			"users.full_names AS users_full_names", 
			"appointments.id_status_appointment", 
			"appointment_status.status AS appointment_status_status");
		$pagination = $this->get_pagination(MAX_RECORD_COUNT); // get current pagination e.g array(page_number, page_limit)
		//search table record
		if(!empty($request->search)){
			$text = trim($request->search); 
			$search_condition = "(
				appointments.id_appointment LIKE ? OR 
				appointments.id_patient LIKE ? OR 
				appointments.id_doc LIKE ? OR 
				appointments.motive LIKE ? OR 
				appointments.descritption LIKE ? OR 
				appointments.historial LIKE ? OR 
				appointments.appointment_date LIKE ? OR 
				appointments.nex_appointment_date LIKE ? OR 
				appointments.register_date LIKE ? OR 
				appointments.update_date LIKE ? OR 
				appointments.id_user LIKE ? OR 
				appointments.id_status_appointment LIKE ?
			)";
			$search_params = array(
				"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
			);
			//setting search conditions
			$db->where($search_condition, $search_params);
			 //template to use when ajax search
			$this->view->search_template = "appointments/search.php";
		}
		$db->join("clinic_patients", "appointments.id_patient = clinic_patients.id_patient", "INNER");
		$db->join("doc", "appointments.id_doc = doc.id", "INNER");
		$db->join("users", "appointments.id_user = users.id_user", "INNER");
		$db->join("appointment_status", "appointments.id_status_appointment = appointment_status.id", "INNER");
		if(!empty($request->orderby)){
			$orderby = $request->orderby;
			$ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
			$db->orderBy($orderby, $ordertype);
		}
		else{
			$db->orderBy("appointments.id_appointment", ORDER_TYPE);
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
		$page_title = $this->view->page_title = "Appointments";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";
		$this->render_view("appointments/list.php", $data); //render the full page
	}
// No View Function Generated Because No Field is Defined as the Primary Key on the Database Table
}
