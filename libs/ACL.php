<?php
/**
 * Page Access Control
 * @category  RBAC Helper
 */
defined('ROOT') or exit('No direct script access allowed');
class ACL
{
	

	/**
	 * Array of user roles and page access 
	 * Use "*" to grant all access right to particular user role
	 * @var array
	 */
	public static $role_pages = array(
			'admin' =>
						array(
							'users' => array('list','view','add','edit', 'editfield','delete','import_data','accountedit','accountview','register'),
							'clinic_patients' => array('list','view','add','edit', 'editfield','delete'),
							'appointment_new' => array('list','view','add','edit', 'editfield','delete'),
							'doc' => array('list','view','add','edit', 'editfield','delete'),
							'clinic_prescription' => array('list','view','add','edit', 'editfield','delete'),
							'patients_status' => array('list','view','add','edit', 'editfield','delete'),
							'actives_patients' => array('list','view'),
							'app_logs' => array('list','view'),
							'invoice_status' => array('list','view','add','edit', 'editfield','delete'),
							'invoices_concepts' => array('list','view','add','edit', 'editfield','delete'),
							'invoices' => array('list','view','add','edit', 'editfield','delete'),
							'invoice_cancelled' => array('list','view'),
							'invoice_debt' => array('list','view'),
							'inactives_patients' => array('list','view'),
							'appointment_status' => array('list','view','add','edit', 'editfield','delete'),
							'appointments' => array('list','view')
						),
		
			'doctor' =>
						array(
							'users' => array('register'),
							'clinic_patients' => array('list','view'),
							'appointment_new' => array('list','view','add'),
							'doc' => array('list'),
							'clinic_prescription' => array('list','view','add','edit', 'editfield','delete'),
							'actives_patients' => array('list','view'),
							'my_appointment' => array('list')
						),
		
			'assistant' =>
						array(
							'users' => array('view','register'),
							'clinic_patients' => array('list','view','add','edit', 'editfield','delete'),
							'appointment_new' => array('list','view','add','edit', 'editfield','delete'),
							'doc' => array('list','view','add','edit', 'editfield','delete'),
							'patients_status' => array('list','view','add','edit', 'editfield','delete'),
							'actives_patients' => array('list','view'),
							'invoices' => array('list','view','add','edit', 'editfield','delete'),
							'invoice_cancelled' => array('list','view'),
							'invoice_debt' => array('list','view'),
							'inactives_patients' => array('list','view'),
							'appointments' => array('list','view'),
							'my_appointment' => array('list')
						),
		
			'patients' =>
						array(
							'my_appointment' => array('list')
						)
		);

	/**
	 * Current user role name
	 * @var string
	 */
	public static $user_role = null;

	/**
	 * pages to Exclude From Access Validation Check
	 * @var array
	 */
	public static $exclude_page_check = array("", "index", "home", "account", "info", "masterdetail");

	/**
	 * Init page properties
	 */
	public function __construct()
	{	
		if(!empty(USER_ROLE)){
			self::$user_role = USER_ROLE;
		}
	}

	/**
	 * Check page path against user role permissions
	 * if user has access return AUTHORIZED
	 * if user has NO access return UNAUTHORIZED
	 * if user has NO role return NO_ROLE
	 * @return string
	 */
	public static function GetPageAccess($path)
	{
		$rp = self::$role_pages;
		if ($rp == "*") {
			return AUTHORIZED; // Grant access to any user
		} else {
			$path = strtolower(trim($path, '/'));

			$arr_path = explode("/", $path);
			$page = strtolower($arr_path[0]);

			//If user is accessing excluded access contrl pages
			if (in_array($page, self::$exclude_page_check)) {
				return AUTHORIZED;
			}

			$user_role = strtolower(USER_ROLE); // Get user defined role from session value
			if (array_key_exists($user_role, $rp)) {
				$action = (!empty($arr_path[1]) ? $arr_path[1] : "list");
				if ($action == "index") {
					$action = "list";
				}
				//Check if user have access to all pages or user have access to all page actions
				if ($rp[$user_role] == "*" || (!empty($rp[$user_role][$page]) && $rp[$user_role][$page] == "*")) {
					return AUTHORIZED;
				} else {
					if (!empty($rp[$user_role][$page]) && in_array($action, $rp[$user_role][$page])) {
						return AUTHORIZED;
					}
				}
				return FORBIDDEN;
			} else {
				//User does not have any role.
				return NOROLE;
			}
		}
	}

	/**
	 * Check if user role has access to a page
	 * @return Bool
	 */
	public static function is_allowed($path)
	{
		return (self::GetPageAccess($path) == AUTHORIZED);
	}

}
