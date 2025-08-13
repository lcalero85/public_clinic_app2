<?php 

/**
 * Home Page Controller
 * @category  Controller
 */
class HomeController extends SecureController{
	/**
     * Index Action
     * @return View
     */
	function index(){
		if(strtolower(USER_ROLE) == 'admin'){
			$this->render_view("home/admin.php" , null , "main_layout.php");
		}
		elseif(strtolower(USER_ROLE) == 'doctor'){
			$this->render_view("home/doctor.php" , null , "main_layout.php");
		}
		elseif(strtolower(USER_ROLE) == 'assistant'){
			$this->render_view("home/assistant.php" , null , "main_layout.php");
		}
		elseif(strtolower(USER_ROLE) == 'patients'){
			$this->render_view("home/patients.php" , null , "main_layout.php");
		}
		else{
			$this->render_view("home/index.php" , null , "main_layout.php");
		}
	}
}
