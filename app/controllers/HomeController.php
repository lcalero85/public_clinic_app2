<?php 

/**
 * Home Page Controller
 * @category  Controller
 */
class HomeController extends SecureController{
    /**
     * Acción principal (Index)
     * @return View
     */
    function index(){
        // Obtenemos el nombre del rol desde la constante que creamos en login_user()
        // Se convierte a minúsculas para evitar problemas con mayúsculas/minúsculas
        $role_name = strtolower(USER_ROLE_NAME);

        // Definimos un mapeo de roles a las vistas que debe cargar cada uno
        $role_views = [
            'admin'     => 'home/admin.php',     // Vista para Administrador
            'doctor'    => 'home/doctor.php',    // Vista para Doctor
            'assistant' => 'home/assistant.php', // Vista para Asistente
            'patients'  => 'home/patients.php'   // Vista para Pacientes
        ];

        // Si el rol existe en el mapeo, asignamos la vista correspondiente
        // Si no existe, asignamos la vista por defecto "home/index.php"
        $view = isset($role_views[$role_name]) ? $role_views[$role_name] : 'home/index.php';

        // Renderizamos la vista elegida usando el layout principal
        $this->render_view($view, null, "main_layout.php");
    }
}
