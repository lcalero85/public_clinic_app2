<?php

/**
 * Users Page Controller
 * @category  Controller
 */
class UsersController extends SecureController
{
	function __construct()
	{
		parent::__construct();
		$this->tablename = "users";
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

    // Incluimos el role_name desde la tabla roles
    $fields = array(
        "users.id_user",
        "users.full_names",
        "roles.role_name", // <- nuevo campo
        "users.user_name",
        "users.email",
        "users.photo",
        "users.register_date",
        "users.update_date"
    );

    $pagination = $this->get_pagination(MAX_RECORD_COUNT);

    // Búsqueda
    if (!empty($request->search)) {
        $text = trim($request->search);
        $search_condition = "(
            users.id_user LIKE ? OR 
            users.full_names LIKE ? OR 
            roles.role_name LIKE ? OR 
            users.user_name LIKE ? OR 
            users.password LIKE ? OR 
            users.email LIKE ? OR 
            users.photo LIKE ? OR 
            users.register_date LIKE ? OR 
            users.update_date LIKE ? OR 
            users.cel LIKE ?
        )";
        $search_params = array(
            "%$text%",
            "%$text%",
            "%$text%",
            "%$text%",
            "%$text%",
            "%$text%",
            "%$text%",
            "%$text%",
            "%$text%",
            "%$text%"
        );
        $db->where($search_condition, $search_params);
        $this->view->search_template = "users/search.php";
    }

    if (!empty($request->orderby)) {
        $orderby = $request->orderby;
        $ordertype = (!empty($request->ordertype) ? $request->ordertype : ORDER_TYPE);
        $db->orderBy($orderby, $ordertype);
    } else {
        $db->orderBy("users.id_user", ORDER_TYPE);
    }

    if ($fieldname) {
        $db->where($fieldname, $fieldvalue);
    }

    // Hacemos el JOIN con roles
    $db->join("roles", "users.id_role = roles.id_role", "LEFT");

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

    $this->rec_id = array_column($records, "id_user");
    $this->write_to_log("list", "true");

    if ($db->getLastError()) {
        $this->set_page_error();
        $this->write_to_log("list", "false");
    }

    $page_title = $this->view->page_title = "Users";
    $this->view->report_filename = date('Y-m-d') . '-' . $page_title;
    $this->view->report_title = $page_title;
    $this->view->report_layout = "report_layout.php";
    $this->view->report_paper_size = "A4";
    $this->view->report_orientation = "portrait";

    $this->render_view("users/list.php", $data);
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

    // Hacemos el JOIN con roles
    $db->join("roles", "users.id_role = roles.id_role", "LEFT");

    $fields = array(
        "users.id_user",
        "users.full_names",
        "roles.role_name", // <- traemos el nombre del rol
        "users.user_name",
        "users.email",
        "users.register_date",
        "users.update_date"
    );

    if ($value) {
        $db->where($rec_id, urldecode($value)); // select record based on field name
    } else {
        $db->where("users.id_user", $rec_id); // select record based on primary key
    }

    $record = $db->getOne($tablename, $fields);

    if ($record) {
        $this->write_to_log("view", "true");
        $page_title = $this->view->page_title = "View Users";
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

    return $this->render_view("users/view.php", $record);
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

        $fields = $this->fields = array(
            "full_names", "rol", "user_name", "password", "email", "photo", "register_date", "update_date","id_role"
        );

        $postdata = $this->format_request_data($formdata);
        $cpassword = trim($postdata['confirm_password']);
        $password = trim($postdata['password']);

        if ($cpassword !== $password) {
            $this->view->page_error[] = "Your password confirmation is not consistent";
        }

        $this->rules_array = array(
            'full_names' => 'required',
            'rol' => '',
			'id_role' => 'required',
            'user_name' => 'required',
            'password' => 'required',
            'email' => 'required|valid_email',
            'photo' => '',
        );

        $this->sanitize_array = array(
            'full_names' => 'sanitize_string',
            'rol' => '',
			'id_role' => 'sanitize_string',
            'user_name' => 'sanitize_string',
            'email' => 'sanitize_string',
            'photo' => '',
        );

        $this->filter_vals = true;
        $modeldata = $this->modeldata = $this->validate_form($postdata);

        // Hash de contraseña
        $password_text = $modeldata['password'];
        $modeldata['password'] = password_hash($password_text, PASSWORD_DEFAULT);
        $modeldata['register_date'] = datetime_now();
        $modeldata['update_date'] = datetime_now();
      

        // Validar duplicados en users
        $db->where("LOWER(user_name)", strtolower($modeldata['user_name']));
        if ($db->has($tablename)) {
            $this->view->page_error[] = $modeldata['user_name'] . " already exists!";
        }

        $db->where("LOWER(email)", strtolower($modeldata['email']));
        if ($db->has($tablename)) {
            $this->view->page_error[] = $modeldata['email'] . " already exists!";
        }

        // Verificar y asociar con doc o clinic_patients según el rol
        $rol = strtolower(trim($modeldata['rol']));
        $full_name = strtolower(trim($modeldata['full_names']));

        $new_doc_id = null;
        $new_patient_id = null;
        $doctor = null;
        $patient = null;

        if ($rol === 'doctor') {
            $db->where("LOWER(full_names)", $full_name);
            $doctor = $db->getOne('doc');
            if (!$doctor) {
                $new_doc_id = $db->insert('doc', [
                    'full_names' => ucwords($full_name),
                    'register_date' => datetime_now(),
                    'update_date' => datetime_now()
                ]);
            }
        }

        if ($rol === 'patients') {
            $db->where("LOWER(full_names)", $full_name);
            $patient = $db->getOne('clinic_patients');
            if (!$patient) {
                $new_patient_id = $db->insert('clinic_patients', [
                    'full_names' => ucwords($full_name),
                    'register_date' => datetime_now(),
                    'update_date' => datetime_now()
                ]);
            }
        }

        if ($this->validated()) {
            $rec_id = $this->rec_id = $db->insert($tablename, $modeldata);
            if ($rec_id) {
                // Asociar usuario en doc si corresponde
                if ($rol === 'doctor') {
                    if ($doctor) {
                        $db->where('id', $doctor['id'])->update('doc', ['id_user' => $rec_id]);
                    } elseif ($new_doc_id) {
                        $db->where('id', $new_doc_id)->update('doc', ['id_user' => $rec_id]);
                    }
                }

                // Asociar usuario en clinic_patients si corresponde
                if ($rol === 'patients') {
                    if ($patient) {
                        $db->where('id_patient', $patient['id_patient'])->update('clinic_patients', ['id_user' => $rec_id]);
                    } elseif ($new_patient_id) {
                        $db->where('id_patient', $new_patient_id)->update('clinic_patients', ['id_user' => $rec_id]);
                    }
                }

                $this->write_to_log("add", "true");
                $this->set_flash_msg("Record added successfully", "success");
                return $this->redirect("users");
            } else {
                $this->set_page_error();
                $this->write_to_log("add", "false");
            }
        }
    }

    $page_title = $this->view->page_title = "Add New Users";
    $this->render_view("users/add.php");
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
		$fields = $this->fields = array("id_user", "full_names", "rol", "user_name", "photo", "register_date", "update_date");
		if ($formdata) {
			$postdata = $this->format_request_data($formdata);
			$this->rules_array = array(
				'full_names' => 'required',
				'rol' => 'required',
				'user_name' => 'required',
				'photo' => 'required',
			);
			$this->sanitize_array = array(
				'full_names' => 'sanitize_string',
				'rol' => 'sanitize_string',
				'user_name' => 'sanitize_string',
				'photo' => 'sanitize_string',
			);
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			$modeldata['register_date'] = datetime_now();
			$modeldata['update_date'] = datetime_now();
			//Check if Duplicate Record Already Exit In The Database
			if (isset($modeldata['user_name'])) {
				$db->where("user_name", $modeldata['user_name'])->where("id_user", $rec_id, "!=");
				if ($db->has($tablename)) {
					$this->view->page_error[] = $modeldata['user_name'] . " Already exist!";
				}
			}
			if ($this->validated()) {
				$db->where("users.id_user", $rec_id);;
				$bool = $db->update($tablename, $modeldata);
				$numRows = $db->getRowCount(); //number of affected rows. 0 = no record field updated
				if ($bool && $numRows) {
					$this->write_to_log("edit", "true");
					$this->set_flash_msg("Record updated successfully", "success");
					return $this->redirect("users");
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
						return	$this->redirect("users");
					}
				}
			}
		}
		$db->where("users.id_user", $rec_id);;
		$data = $db->getOne($tablename, $fields);
		$page_title = $this->view->page_title = "Edit  Users";
		if (!$data) {
			$this->set_page_error();
		}
		return $this->render_view("users/edit.php", $data);
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
		$db->where("users.id_user", $arr_rec_id, "in");
		$bool = $db->delete($tablename);
		if ($bool) {
			$this->write_to_log("delete", "true");
			$this->set_flash_msg("Record deleted successfully", "success");
		} elseif ($db->getLastError()) {
			$page_error = $db->getLastError();
			$this->set_flash_msg($page_error, "danger");
			$this->write_to_log("delete", "false");
		}
		return	$this->redirect("users");
	}
	/**
	 * Insert new record to the database table
	 * @param $formdata array() from $_POST
	 * @return BaseView
	 */
	function register($formdata = null)
	{
		if ($formdata) {
			$db = $this->GetModel();
			$tablename = $this->tablename;
			$request = $this->request;
			//fillable fields
			$fields = $this->fields = array(
				"full_names",
				"user_name",
				"password",
				"email",
				"register_date",
				"update_date",
				"cel",
				"rol",
			);
			$postdata = $this->format_request_data($formdata);
			$cpassword = $postdata['confirm_password'];
			$password = $postdata['password'];
			if ($cpassword != $password) {
				$this->view->page_error[] = "Your password confirmation is not consistent";
			}
			$this->rules_array = array(
				'full_names' => 'required',
				'user_name' => 'required',
				'password' => 'required',
				'email' => 'required|valid_email',
				'register_date' => 'required',
				'update_date' => 'required',
				'cel' => 'required',
				'rol' => '',

			);
			$this->sanitize_array = array(
				'full_names' => 'sanitize_string',
				'user_name' => 'sanitize_string',
				'email' => 'sanitize_string',
				'register_date' => 'sanitize_string',
				'update_date' => 'sanitize_string',
				'cel' => 'sanitize_string',
				'rol'=> '',
			);
			$this->filter_vals = true; //set whether to remove empty fields
			$modeldata = $this->modeldata = $this->validate_form($postdata);
			$modeldata['rol'] = 'Patients';
			$password_text = $modeldata['password'];

			//update modeldata with the password hash
			$modeldata['password'] = $this->modeldata['password'] = password_hash($password_text, PASSWORD_DEFAULT);
			//Check if Duplicate Record Already Exit In The Database
			$db->where("user_name", $modeldata['user_name']);
			if ($db->has($tablename)) {
				$this->view->page_error[] = $modeldata['user_name'] . " Already exist!";
			}
			//Check if Duplicate Record Already Exit In The Database
			$db->where("email", $modeldata['email']);
			if ($db->has($tablename)) {
				$this->view->page_error[] = $modeldata['email'] . " Already exist!";
			}
			if ($this->validated()) {
				$rec_id = $this->rec_id = $db->insert($tablename, $modeldata);
				if ($rec_id) {
					$this->write_to_log("add", "true");
					# Statement to execute after adding record
					// For each register of patients rol 
					$table_data = array(
						"id_user" => $rec_id,
						"full_names" => $modeldata['full_names'],
						"email" => $modeldata['email'],
						"phone_patient" => $modeldata['cel'],
						"id_status" => 1
					);
					$db->insert("clinic_patients", $table_data);
					# End of after add statement
					$this->set_flash_msg("Grabar agregado exitosamente", "success");
					return	$this->redirect("home");
				} else {
					$this->set_page_error();
					$this->write_to_log("add", "false");
				}
			}
		}
		$page_title = $this->view->page_title = "Add New Patient";
		$this->render_view("users/register.php");
	}
}
