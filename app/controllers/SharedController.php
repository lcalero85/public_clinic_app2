<?php 

/**
 * SharedController Controller
 * @category  Controller / Model
 */
class SharedController extends BaseController{
	
	/**
     * users_user_name_value_exist Model Action
     * @return array
     */
	function users_user_name_value_exist($val){
		$db = $this->GetModel();
		$db->where("user_name", $val);
		$exist = $db->has("users");
		return $exist;
	}

	/**
     * users_email_value_exist Model Action
     * @return array
     */
	function users_email_value_exist($val){
		$db = $this->GetModel();
		$db->where("email", $val);
		$exist = $db->has("users");
		return $exist;
	}

	/**
     * clinic_patients_id_status_option_list Model Action
     * @return array
     */
	function clinic_patients_id_status_option_list(){
		$db = $this->GetModel();
		$sqltext = "SELECT  DISTINCT id AS value,status AS label FROM patients_status ORDER BY status ASC";
		$queryparams = null;
		$arr = $db->rawQuery($sqltext, $queryparams);
		return $arr;
	}

	/**
     * appointment_new_id_patient_option_list Model Action
     * @return array
     */
	function appointment_new_id_patient_option_list(){
		$db = $this->GetModel();
		$sqltext = "SELECT DISTINCT id_patient AS value , full_names AS label FROM clinic_patients ORDER BY label ASC";
		$queryparams = null;
		$arr = $db->rawQuery($sqltext, $queryparams);
		return $arr;
	}

	/**
     * appointment_new_id_doc_option_list Model Action
     * @return array
     */
	function appointment_new_id_doc_option_list(){
		$db = $this->GetModel();
		$sqltext = "SELECT DISTINCT id AS value , full_names AS label FROM doc ORDER BY label ASC";
		$queryparams = null;
		$arr = $db->rawQuery($sqltext, $queryparams);
		return $arr;
	}

	/**
     * clinic_prescription_additional_comments_option_list Model Action
     * @return array
     */
	function clinic_prescription_additional_comments_option_list(){
		$db = $this->GetModel();
		$sqltext = "SELECT DISTINCT id AS value , id AS label FROM patients_status ORDER BY label ASC";
		$queryparams = null;
		$arr = $db->rawQuery($sqltext, $queryparams);
		return $arr;
	}

	/**
     * clinic_prescription_id_patient_option_list Model Action
     * @return array
     */
	function clinic_prescription_id_patient_option_list(){
		$db = $this->GetModel();
		$sqltext = "SELECT DISTINCT id_patient AS value , full_names AS label FROM clinic_patients ORDER BY label ASC";
		$queryparams = null;
		$arr = $db->rawQuery($sqltext, $queryparams);
		return $arr;
	}

	/**
     * clinic_prescription_id_doctor_option_list Model Action
     * @return array
     */
	function clinic_prescription_id_doctor_option_list(){
		$db = $this->GetModel();
		$sqltext = "SELECT DISTINCT id AS value , full_names AS label FROM doc ORDER BY label ASC";
		$queryparams = null;
		$arr = $db->rawQuery($sqltext, $queryparams);
		return $arr;
	}

	/**
     * clinic_prescription_id_appointment_option_list Model Action
     * @return array
     */
	function clinic_prescription_id_appointment_option_list(){
		$db = $this->GetModel();
		$sqltext = "SELECT  DISTINCT id_appointment AS value,descritption AS label FROM appointment_new ORDER BY id_appointment ASC";
		$queryparams = null;
		$arr = $db->rawQuery($sqltext, $queryparams);
		return $arr;
	}

	/**
     * invoices_id_patient_option_list Model Action
     * @return array
     */
	function invoices_id_patient_option_list(){
		$db = $this->GetModel();
		$sqltext = "SELECT DISTINCT id_patient AS value , full_names AS label FROM clinic_patients ORDER BY label ASC";
		$queryparams = null;
		$arr = $db->rawQuery($sqltext, $queryparams);
		return $arr;
	}

	/**
     * invoices_id_concept_option_list Model Action
     * @return array
     */
	function invoices_id_concept_option_list(){
		$db = $this->GetModel();
		$sqltext = "SELECT  DISTINCT id AS value,concept AS label FROM invoices_concepts ORDER BY id ASC";
		$queryparams = null;
		$arr = $db->rawQuery($sqltext, $queryparams);
		return $arr;
	}

	/**
     * invoices_id_invoice_status_option_list Model Action
     * @return array
     */
	function invoices_id_invoice_status_option_list(){
		$db = $this->GetModel();
		$sqltext = "SELECT  DISTINCT id AS value,status AS label FROM invoice_status ORDER BY id ASC";
		$queryparams = null;
		$arr = $db->rawQuery($sqltext, $queryparams);
		return $arr;
	}

	/**
     * getcount_users Model Action
     * @return Value
     */
	function getcount_users(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM users";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_patients Model Action
     * @return Value
     */
	function getcount_patients(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM clinic_patients";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_inactivespatients Model Action
     * @return Value
     */
	function getcount_inactivespatients(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM inactives_patients";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_activespatients Model Action
     * @return Value
     */
	function getcount_activespatients(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM actives_patients";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_appointments Model Action
     * @return Value
     */
	function getcount_appointments(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM appointments";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_invoices Model Action
     * @return Value
     */
	function getcount_invoices(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM invoices";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_doctors Model Action
     * @return Value
     */
	function getcount_doctors(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM doc";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	/**
     * getcount_prescriptions Model Action
     * @return Value
     */
	function getcount_prescriptions(){
		$db = $this->GetModel();
		$sqltext = "SELECT COUNT(*) AS num FROM clinic_prescription";
		$queryparams = null;
		$val = $db->rawQueryValue($sqltext, $queryparams);
		
		if(is_array($val)){
			return $val[0];
		}
		return $val;
	}

	public function clinic_patients_id_document_type_option_list() {
    $db = $this->GetModel();
    $sqltext = "SELECT id AS value, type AS label FROM document_type_catalog ORDER BY type ASC";
    $options = $db->rawQuery($sqltext);
    return $options;
}

public function clinic_patients_id_blood_type_option_list(){
    $db = $this->GetModel();
    $sqltext = "SELECT id AS value, type AS label FROM blood_type_catalog ORDER BY type ASC";
    $options = $db->rawQuery($sqltext);
    return $options;
}

public function doc_status_enum_options(){
    $db = $this->GetModel();
    // Trae la definición de la columna ENUM
    $row = $db->rawQueryOne("SHOW COLUMNS FROM `doc` LIKE 'status'");
    $options = [];

    if ($row && isset($row['Type'])) {
        // Type viene como: enum('Activo','Inactivo','Vacaciones','Retirado',...)
        if (preg_match("/^enum\((.*)\)$/i", $row['Type'], $m)) {
            // Convierte la lista en array respetando comillas
            $values = str_getcsv($m[1], ',', "'");
            foreach ($values as $v) {
                $options[] = ['value' => $v, 'label' => $v];
            }
        }
    }

    // Fallback por si algo falla
    if (empty($options)) {
        $fallback = ['Activo','Inactivo','Vacaciones','Retirado'];
        foreach ($fallback as $v) {
            $options[] = ['value' => $v, 'label' => $v];
        }
    }

    return $options;
}


public function appointment_new_priority_option_list(){
    $db = $this->GetModel();
    $enum_values = array();
    $query = "SHOW COLUMNS FROM appointment_new LIKE 'priority'";
    $result = $db->rawQueryOne($query);

    if($result && isset($result['Type'])){
        // Extraer los valores del enum
        preg_match("/^enum\(\'(.*)\'\)$/", $result['Type'], $matches);
        if(!empty($matches[1])){
            $vals = explode("','", $matches[1]);
            foreach($vals as $val){
                $enum_values[] = array(
                    'value' => $val,
                    'label' => ucfirst($val) // capitaliza la primera letra
                );
            }
        }
    }
    return $enum_values;
}

public function appointment_new_reminder_preference_option_list(){
    $db = $this->GetModel();
    $enum_values = array();
    $query = "SHOW COLUMNS FROM appointment_new LIKE 'reminder_preference'";
    $result = $db->rawQueryOne($query);

    if($result && isset($result['Type'])){
        // Extraer valores del enum
        preg_match("/^enum\(\'(.*)\'\)$/", $result['Type'], $matches);
        if(!empty($matches[1])){
            $vals = explode("','", $matches[1]);
            foreach($vals as $val){
                $enum_values[] = array(
                    'value' => $val,
                    'label' => ucfirst($val)
                );
            }
        }
    }
    return $enum_values;
}
public function appointment_new_id_appointment_type_option_list(){
    $db = $this->GetModel();
    $sql = "SELECT id_appointment_type AS value, name AS label 
            FROM appointment_types 
            WHERE status = 1 
            ORDER BY name ASC";
    $result = $db->rawQuery($sql);
    return $result;
}

public function appointment_new_status_option_list(){
    $db = $this->GetModel();
    $sql = "SELECT id AS value, status AS label 
            FROM appointment_status 
            ORDER BY status ASC";
    return $db->rawQuery($sql);
}

public function marital_status_options(){
    $db = $this->GetModel();
    $sql = "SELECT id AS value, status AS label FROM marital_status_catalog ORDER BY status ASC";
    $result = $db->rawQuery($sql);
    return $result;
}

public function document_type_options(){
    $db = $this->GetModel();
    $sql = "SELECT id AS value, type AS label FROM document_type_catalog ORDER BY type ASC";
    return $db->rawQuery($sql);
}


}
