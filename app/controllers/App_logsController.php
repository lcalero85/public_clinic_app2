<?php 
/**
 * App_logs Page Controller
 * @category  Controller
 */
class App_logsController extends SecureController{
	function __construct(){
		parent::__construct();
		$this->tablename = "app_logs";
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
		$fields = array("app_logs.log_id", 
			"app_logs.Timestamp", 
			"app_logs.Action", 
			"app_logs.TableName", 
			"app_logs.RecordID", 
			"app_logs.SqlQuery", 
			"app_logs.UserID", 
			"users.full_names AS users_full_names", 
			"app_logs.ServerIP", 
			"app_logs.RequestUrl", 
			"app_logs.RequestData", 
			"app_logs.RequestCompleted", 
			"app_logs.RequestMsg");
		$pagination = $this->get_pagination(MAX_RECORD_COUNT); // get current pagination e.g array(page_number, page_limit)
		//search table record
		if(!empty($request->search)){
			$text = trim($request->search); 
			$search_condition = "(
				app_logs.log_id LIKE ? OR 
				app_logs.Timestamp LIKE ? OR 
				app_logs.Action LIKE ? OR 
				app_logs.TableName LIKE ? OR 
				app_logs.RecordID LIKE ? OR 
				app_logs.SqlQuery LIKE ? OR 
				app_logs.UserID LIKE ? OR 
				app_logs.ServerIP LIKE ? OR 
				app_logs.RequestUrl LIKE ? OR 
				app_logs.RequestData LIKE ? OR 
				app_logs.RequestCompleted LIKE ? OR 
				app_logs.RequestMsg LIKE ?
			)";
			$search_params = array(
				"%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%","%$text%"
			);
			//setting search conditions
			$db->where($search_condition, $search_params);
			 //template to use when ajax search
			$this->view->search_template = "app_logs/search.php";
		}
		$db->join("users", "app_logs.UserID = users.id_user", "INNER");
		if(!empty($request->orderby)){
			$orderby = $request->orderby;
			$ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
			$db->orderBy($orderby, $ordertype);
		}
		else{
			$db->orderBy("app_logs.log_id", ORDER_TYPE);
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
		$page_title = $this->view->page_title = "App Logs";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";
		$this->render_view("app_logs/list.php", $data); //render the full page
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
		$fields = array("app_logs.log_id", 
			"app_logs.Timestamp", 
			"app_logs.Action", 
			"app_logs.TableName", 
			"app_logs.RecordID", 
			"app_logs.SqlQuery", 
			"app_logs.UserID", 
			"users.full_names AS users_full_names", 
			"app_logs.ServerIP", 
			"app_logs.RequestUrl", 
			"app_logs.RequestData", 
			"app_logs.RequestCompleted", 
			"app_logs.RequestMsg");
		if($value){
			$db->where($rec_id, urldecode($value)); //select record based on field name
		}
		else{
			$db->where("app_logs.log_id", $rec_id);; //select record based on primary key
		}
		$db->join("users", "app_logs.UserID = users.id_user", "INNER");  
		$record = $db->getOne($tablename, $fields );
		if($record){
			$page_title = $this->view->page_title = "View  App Logs";
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
		}
		return $this->render_view("app_logs/view.php", $record);
	}
}
