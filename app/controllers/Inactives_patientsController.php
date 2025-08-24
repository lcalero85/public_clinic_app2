<?php  
/**
 * Inactives_patients Page Controller
 * @category  Controller
 */
class Inactives_patientsController extends SecureController{
	function __construct(){
		parent::__construct();
		$this->tablename = "inactives_patients";
	}

	/**
     * List page records
     */
	function index($fieldname = null , $fieldvalue = null){
		$db = $this->GetModel();
		$tablename = $this->tablename;

		// Selección de campos con alias cp
		$fields = array(
			"cp.id_patient", 
			"cp.full_names", 
			"cp.gender", 
			"cp.birthdate", 
			"cp.register_date", 
			"patients_status.status AS patient_status",
			"users.full_names AS created_by",
			"CONCAT(cp.id_patient, DATE_FORMAT(cp.register_date, '%d%m%Y')) AS clinical_file"
		);

		// JOINs necesarios
		$db->join("patients_status", "cp.id_status = patients_status.id", "INNER");
		$db->join("users", "cp.id_user = users.id_user", "INNER");

		// Filtro si se pasa un campo específico
		if($fieldname){
			$db->where($fieldname , $fieldvalue);
		}

		// Orden por defecto
		$db->orderBy("cp.id_patient", "DESC");

		// Alias en la consulta
		$records = $db->get($tablename . " cp", null, $fields);

		// 🔹 Calcular edad
		foreach($records as &$rec){
			if(!empty($rec['birthdate'])){
				$dob = new DateTime($rec['birthdate']);
				$now = new DateTime();
				$rec['age'] = $dob->diff($now)->y;
			} else {
				$rec['age'] = "N/A";
			}
		}

		$data = new stdClass;
		$data->records = $records;

		$page_title = $this->view->page_title = "Inactive Patients Report";
		$this->view->report_filename = date('Y-m-d') . '-' . $page_title;
		$this->view->report_title = $page_title;
		$this->view->report_layout = "report_layout.php";
		$this->view->report_paper_size = "A4";
		$this->view->report_orientation = "portrait";

		$this->render_view("inactives_patients/list.php", $data); 
	}
}
