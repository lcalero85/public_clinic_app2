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
    function index(): void {
        // Obtenemos el rol desde la constante definida en login_user()
        $role_name = strtolower(USER_ROLE_NAME ?? '');

        // Mapeo de roles a vistas específicas
        $role_views = [
            'admin'     => 'home/admin.php',     // Vista para Administrador
            'doctor'    => 'home/doctor.php',    // Vista para Doctor
            'assistant' => 'home/assistant.php', // Vista para Asistente
            'patients'  => 'home/patients.php',  // Vista para Pacientes
        ];

        // Seleccionar la vista según el rol, o una vista por defecto
        if(isset($role_views[$role_name])){
            $view = $role_views[$role_name];
        } else {
            // Si el rol no existe o está vacío, carga la vista genérica
            $view = 'home/index.php';
        }

        // Renderizar la vista usando el layout principal
        $this->render_view($view, null, "main_layout.php");
    }
}

