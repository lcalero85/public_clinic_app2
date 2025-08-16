<?php
/**
 * Page Access Control
 * @category  RBAC Helper
 */
defined('ROOT') or exit('No direct script access allowed');

class ACL
{
    /**
     * Array of user roles (ID) and page access 
     * Use "*" to grant all access rights to particular user role
     * @var array
     */
    public static $role_pages = array(
        1 => // Admin
            array(
                'users' => array('list','view','add','edit', 'editfield','delete','import_data','accountedit','accountview'),
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

        3 => // Doctor
            array(
                'users' => array('register'),
                'clinic_prescription' => array('list','view','add','edit', 'editfield','delete'),
                'my_appointment' => array('list','view')
            ),

        2=> // Assistant
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
							'appointments' => array('list','view')
							
						),

        4 => // Patients
            array(
                'my_appointment' => array('list')
            )
    );

    /**
     * Current user role ID
     * @var int|null
     */
    public static $user_role = null;

    /**
     * Pages to exclude from access validation check
     * @var array
     */
    public static $exclude_page_check = array("", "index", "home", "account", "info", "masterdetail");

    /**
     * Init page properties
     */
    public function __construct()
    {   
        if (!empty(USER_ROLE_ID)) {
            self::$user_role = USER_ROLE_ID; // Guardamos el ID del rol actual
        }
    }

    /**
     * Check page path against user role permissions
     * if user has access return AUTHORIZED
     * if user has NO access return FORBIDDEN
     * if user has NO role return NOROLE
     * @return string
     */
    public static function GetPageAccess($path)
    {
        $rp = self::$role_pages;

        if ($rp == "*") {
            return AUTHORIZED; // Acceso total a todos los roles
        } else {
            $path = strtolower(trim($path, '/'));
            $arr_path = explode("/", $path);
            $page = strtolower($arr_path[0]);

            // Si la página está excluida de control de acceso
            if (in_array($page, self::$exclude_page_check)) {
                return AUTHORIZED;
            }

            $user_role = USER_ROLE_ID; // Usamos el ID numérico del rol

            if (array_key_exists($user_role, $rp)) {
                $action = (!empty($arr_path[1]) ? $arr_path[1] : "list");
                if ($action == "index") {
                    $action = "list";
                }

                // Validación de permisos
                if ($rp[$user_role] == "*" || (!empty($rp[$user_role][$page]) && $rp[$user_role][$page] == "*")) {
                    return AUTHORIZED;
                } else {
                    if (!empty($rp[$user_role][$page]) && in_array($action, $rp[$user_role][$page])) {
                        return AUTHORIZED;
                    }
                }
                return FORBIDDEN;
            } else {
                // El usuario no tiene un rol asignado válido
                return NOROLE;
            }
        }
    }

    /**
     * Check if user role has access to a page
     * @return bool
     */
    public static function is_allowed($path)
    {
        return (self::GetPageAccess($path) == AUTHORIZED);
    }
}
