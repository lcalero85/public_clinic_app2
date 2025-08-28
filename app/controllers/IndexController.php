<?php  
/**
 * Index Page Controller
 * @category  Controller
 */
class IndexController extends BaseController{
	function __construct(){
		parent::__construct(); 
		$this->tablename = "users";
	}

	/**
     * Index Action 
     * @return null
     */
	function index(){
		if(user_login_status() == true){
			// Usuario ya logueado → lo mandamos directo al Home
			$this->redirect(HOME_PAGE);
		}
		else{
			$this->render_view("index/index.php");
		}
	}

	private function login_user($username , $password_text, $rememberme = false){
		$db = $this->GetModel();
		$username = htmlspecialchars(strip_tags($username), ENT_QUOTES, 'UTF-8');
		$db->where("user_name", $username)->orWhere("email", $username);

		$user = $db->getOne($this->tablename, "*");

		if(!empty($user)){
			$password_hash = $user['password'];
			$this->modeldata['password'] = $password_hash;

			if(password_verify($password_text,$password_hash)){
				unset($user['password']); 

				// Obtener nombre del rol
				if (!empty($user['id_role'])) {
					$role = $db->where('id_role', $user['id_role'])->getOne('roles', ['role_name']);
					$user['role_name'] = $role ? $role['role_name'] : null;
				} else {
					$user['role_name'] = null;
				}

				// Foto en sesión
				$user['photo'] = !empty($user['photo']) ? $user['photo'] : null;

				// Guardamos usuario en sesión
				set_session("user_data", $user);

				$this->write_to_log("userlogin", "true");

				// Remember me
				if($rememberme == true){
					$sessionkey = time().random_str(20);
					$db->where("id_user", $user['id_user']);
					$res = $db->update($this->tablename, array("login_session_key" => hash_value($sessionkey)));
					if(!empty($res)){
						set_cookie("login_session_key", $sessionkey);
					}
				} else {
					clear_cookie("login_session_key");
				}

				// 🔹 Siempre redirigir al Home (HomeController se encarga del rol)
				return $this->redirect(HOME_PAGE);

			} else {
				return $this->login_fail("Username or password not correct");
			}
		} else {
			return $this->login_fail("Username or password not correct");
		}
	}

	/**
     * Login fail → mostrar error
     */
	private function login_fail($page_error = null){
		$this->set_page_error($page_error);
		$this->write_to_log("userlogin", "false");
		$this->render_view("index/login.php");
	}

	/**
     * Login Action
     */
	function login($formdata = null){
		if($formdata){
			$modeldata = $this->modeldata = $formdata;
			$username = trim($modeldata['username']);
			$password = $modeldata['password'];
			$rememberme = (!empty($modeldata['rememberme']) ? $modeldata['rememberme'] : false);
			$this->login_user($username, $password, $rememberme);
		}
		else{
			$this->set_page_error("Invalid request");
			$this->render_view("index/login.php");
		}
	}

	/**
     * Logout Action
     */
	function logout($arg=null){
		Csrf::cross_check();
		$this->write_to_log("userlogout", "true");
		session_destroy();
		clear_cookie("login_session_key");
		$this->redirect("");
	}
}

