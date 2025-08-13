<?php 
/**
 * My_appointment Page Controller
 * @category  Controller
 */
class My_appointmentController extends SecureController{
	function __construct(){
		parent::__construct();
		$this->tablename = "My_Appointment";
	}
	/**
     * Custom list page
     * @return BaseView
     */
	function index(){
		$request = $this->request;
		$db = $this->GetModel();
		$pagination = null;
		$sqltext = "SELECT SQL_CALC_FOUND_ROWS  cp.full_names, app.motive ,app.descritption,app.historial ,app.appointment_date,app.register_date,dc.full_names as Name,apps.status FROM appointment_new as app INNER JOIN clinic_patients as cp on app.id_patient = cp.id_patient INNER JOIN users as us on cp.id_user = us.id_user INNER JOIN doc as dc on app.id_doc = dc.id INNER JOIN appointment_status as apps on apps.id = app.id_status_appointment WHERE cp.id_user = ".USER_ID."";
		$queryparams = null;
		if(!empty($request->orderby)){
			$orderby = $request->orderby;
			$ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
			$db->orderBy($orderby, $ordertype);
		}
		else{
			$db->orderBy("full_names", ORDER_TYPE);
		}
		$pagination = $this->get_pagination(MAX_RECORD_COUNT); //Get sql limit from url if not set on the sql command text
		$tc = $db->withTotalCount();
		$records = $db->query($sqltext, $pagination, $queryparams);
		$records_count = count($records);
		$total_records = intval($tc->totalCount);
		$page_limit = (!empty($pagination) ? $pagination[1] : 1);
		$total_pages = ceil($total_records / $page_limit);
		$data = new stdClass;
		$data->records = $records;
		$data->record_count = $records_count;
		$data->total_records = $total_records;
		$data->total_page = $total_pages;
		if($db->getLastError()){
			$this->set_page_error();
		}
		$page_title = $this->view->page_title = "My Appointment";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";
		$this->render_view("my_appointment/list.php", $data); //render the full page
	}
}
