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

    // 🔹 Incluimos 'photo' en la consulta
    $user = $db->getOne($this->tablename, "*"); // o especificar campos: 'id_user, full_names, rol, id_role, user_name, email, photo'

    if(!empty($user)){
        $password_hash = $user['password'];
        $this->modeldata['password'] = $password_hash;

        if(password_verify($password_text,$password_hash)){
            unset($user['password']); // No guardar contraseña en sesión

            // Obtener el nombre del rol desde la tabla roles
            if (!empty($user['id_role'])) {
                $role = $db->where('id_role', $user['id_role'])->getOne('roles', ['role_name']);
                $user['role_name'] = $role ? $role['role_name'] : null;
            } else {
                $user['role_name'] = null;
            }

            // 🔹 Guardar foto como BLOB en la sesión
            if (!empty($user['photo'])) {
                // Lo guardamos tal cual (BLOB)
                $user['photo'] = $user['photo'];
            } else {
                $user['photo'] = null;
            }

            // Guardar todo en sesión
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

            $redirect_url = get_session("login_redirect_url");
            if(!empty($redirect_url)){
                clear_session("login_redirect_url");
                return $this->redirect($redirect_url);
            } else {
                return $this->redirect(HOME_PAGE);
            }
        } else {
            return $this->login_fail("Username or password not correct");
        }
    } else {
        return $this->login_fail("Username or password not correct");
    }
}

	/**
     * Display login page with custom message when login fails
     * @return BaseView
     */
	private function login_fail($page_error = null){
		$this->set_page_error($page_error);
		$this->write_to_log("userlogin", "false");
		$this->render_view("index/login.php");
	}
	/**
     * Login Action
     * If Not $_POST Request, Display Login Form View
     * @return View
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
     * Destroy All Sessions And Cookies
     * @return View
     */
	function logout($arg=null){
		Csrf::cross_check();
		$this->write_to_log("userlogout", "true");
		session_destroy();
		clear_cookie("login_session_key");
		$this->redirect("");
	}
}
