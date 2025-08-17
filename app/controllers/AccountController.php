<?php

/**
 * Account Page Controller
 * @category  Controller
 */
class AccountController extends SecureController
{
	function __construct()
	{
		parent::__construct();
		$this->tablename = "users";
	}
	/**
	 * Index Action
	 * @return null
	 */
	function index()
	{
		$db = $this->GetModel();
		$rec_id = $this->rec_id = USER_ID; //get current user id from session
		$db->where("id_user", $rec_id);
		$tablename = $this->tablename;
		$fields = array(
			"id_user",
			"full_names",
			"rol",
			"user_name",
			"email",
			"register_date",
			"update_date"
		);
		$user = $db->getOne($tablename, $fields);
		if (!empty($user)) {
			$page_title = $this->view->page_title = "My Account";
			$this->render_view("account/view.php", $user);
		} else {
			$this->set_page_error();
			$this->render_view("account/view.php");
		}
	}
	/**
	 * Update user account record with formdata
	 * @param $formdata array() from $_POST
	 * @return array
	 */
	function edit($formdata = null)
{
    $request = $this->request;
    $db = $this->GetModel();
    $rec_id = $this->rec_id = USER_ID;
    $tablename = $this->tablename;
    //editable fields
    $fields = $this->fields = array(
        "id_user",
        "full_names",
        "photo",
        "register_date",
        "update_date"
    );

    if ($formdata) {
        $postdata = $this->format_request_data($formdata);
        $this->rules_array = array(
            'full_names' => 'required',
            'photo' => '',
        );
        $this->sanitize_array = array(
            'full_names' => 'sanitize_string',
            'photo' => '',
        );

        $modeldata = $this->modeldata = $this->validate_form($postdata);
        $modeldata['register_date'] = datetime_now();
        $modeldata['update_date'] = datetime_now();

        // --- Foto: archivo O webcam O nada (NO modificar si no se envía) ---
        $photoData = null;

        if (!empty($_FILES['photo_file']['tmp_name'])) {
            // 1) Imagen desde el selector de archivos
            $photoData = file_get_contents($_FILES['photo_file']['tmp_name']);
        } elseif (!empty($_POST['photo_webcam'])) {
            // 2) Imagen tomada con webcam (dataURL base64)
            $base64 = $_POST['photo_webcam'];
            $photoData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        }

        if ($photoData !== null) {
            $modeldata['photo'] = $photoData;
        } else {
            // 🚀 Si no hay nueva foto, no actualizar ese campo
            unset($modeldata['photo']);
        }

        //Check if Duplicate Record Already Exists In The Database
        if (isset($modeldata['user_name'])) {
            $db->where("user_name", $modeldata['user_name'])->where("id_user", $rec_id, "!=");
            if ($db->has($tablename)) {
                $this->view->page_error[] = $modeldata['user_name'] . " Already exist!";
            }
        }

        if ($this->validated()) {
            $db->where("users.id_user", $rec_id);
            $bool = $db->update($tablename, $modeldata);
            $numRows = $db->getRowCount(); // number of affected rows. 0 = no record field updated
            if ($bool && $numRows) {
                $this->write_to_log("edit", "true");
                $this->set_flash_msg("Record updated successfully", "success");

                // 🔄 refrescar sesión con los nuevos datos
                $db->where("id_user", $rec_id);
                $user = $db->getOne($tablename, "*");
                set_session("user_data", $user);

                return $this->redirect("account");
            } else {
                if ($db->getLastError()) {
                    $this->set_page_error();
                    $this->write_to_log("edit", "false");
                } elseif (!$numRows) {
                    $this->set_flash_msg("No record updated", "warning");
                    return $this->redirect("account");
                }
            }
        }
    }

    $db->where("users.id_user", $rec_id);
    $data = $db->getOne($tablename, $fields);
    $page_title = $this->view->page_title = "My Account";
    if (!$data) {
        $this->set_page_error();
    }
    return $this->render_view("account/edit.php", $data);
}

	/**
	 * Change account email
	 * @return BaseView
	 */
	function change_email($formdata = null)
	{
		if ($formdata) {
			$email = trim($formdata['email']);
			$db = $this->GetModel();
			$rec_id = $this->rec_id = USER_ID; //get current user id from session
			$tablename = $this->tablename;
			$db->where("id_user", $rec_id);
			$result = $db->update($tablename, array('email' => $email));
			if ($result) {
				$this->write_to_log("emailchange", "true");
			} else {
				$this->set_page_error("Email not changed");
				$this->write_to_log("emailchange", "false");
			}
		}
		return $this->render_view("account/change_email.php");
	}
}
