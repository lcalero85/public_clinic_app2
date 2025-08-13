<?php 
/**
 * Invoice_cancelled Page Controller
 * @category  Controller
 */
class Invoice_cancelledController extends SecureController{
	function __construct(){
		parent::__construct();
		$this->tablename = "invoice_cancelled";
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
		$fields = array("invoice_cancelled.id_invoice", 
			"invoice_cancelled.invoice_num", 
			"invoice_cancelled.comments", 
			"invoice_cancelled.id_concept", 
			"invoices_concepts.concept AS invoices_concepts_concept", 
			"invoice_cancelled.id_patient", 
			"clinic_patients.full_names AS clinic_patients_full_names", 
			"invoice_cancelled.quantity", 
			"invoice_cancelled.price", 
			"invoice_cancelled.total_invoice", 
			"invoice_cancelled.date_invoice", 
			"invoice_cancelled.id_invoice_status", 
			"invoice_status.status AS invoice_status_status", 
			"invoice_cancelled.register_date", 
			"invoice_cancelled.id_user", 
			"users.full_names AS users_full_names");
		$pagination = $this->get_pagination(MAX_RECORD_COUNT); // get current pagination e.g array(page_number, page_limit)
		//search table record
		if(!empty($request->search)){
			$text = trim($request->search); 
			$search_condition = "(
				invoice_cancelled.id_invoice LIKE ? OR 
				invoice_cancelled.invoice_num LIKE ? OR 
				invoice_cancelled.comments LIKE ? OR 
				invoice_cancelled.id_concept LIKE ? OR 
				invoice_cancelled.id_patient LIKE ? OR 
				invoice_cancelled.quantity LIKE ? OR 
				invoice_cancelled.price LIKE ? OR 
				invoice_cancelled.total_invoice LIKE ? OR 
				invoice_cancelled.date_invoice LIKE ? OR 
				invoice_cancelled.id_invoice_status LIKE ? OR 
				invoice_cancelled.register_date LIKE ? OR 
				invoice_cancelled.update_date LIKE ? OR 
				invoice_cancelled.id_user LIKE ?
			)";
			$search_params = array(
				"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
			);
			//setting search conditions
			$db->where($search_condition, $search_params);
			 //template to use when ajax search
			$this->view->search_template = "invoice_cancelled/search.php";
		}
		$db->join("invoices_concepts", "invoice_cancelled.id_concept = invoices_concepts.id", "INNER");
		$db->join("clinic_patients", "invoice_cancelled.id_patient = clinic_patients.id_patient", "INNER");
		$db->join("invoice_status", "invoice_cancelled.id_invoice_status = invoice_status.id", "INNER");
		$db->join("users", "invoice_cancelled.id_user = users.id_user", "INNER");
		if(!empty($request->orderby)){
			$orderby = $request->orderby;
			$ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
			$db->orderBy($orderby, $ordertype);
		}
		else{
			$db->orderBy("invoice_cancelled.id_invoice", ORDER_TYPE);
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
		$page_title = $this->view->page_title = "Invoice Cancelled";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";
		$this->render_view("invoice_cancelled/list.php", $data); //render the full page
	}
// No View Function Generated Because No Field is Defined as the Primary Key on the Database Table
}
