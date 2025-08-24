<?php
/**
 * Menu Items
 * All Project Menu
 * @category  Menu List
 */
class Menu
{

    public static $navbarsideleft = array(
        array(
            'path' => 'home',
            'label' => 'Home',
            'icon' => '<i class="fa fa-home "></i>'
        ),

        array(
            'path' => 'clinic_patients',
            'label' => 'Patients',
            'icon' => '<i class="fa fa-user-plus "></i>'
        ),

        array(
            'path' => 'doc',
            'label' => 'Doctors',
            'icon' => '<i class="fa fa-heartbeat "></i>'
        ),

        array(
            'path' => 'appointment_new',
            'label' => 'Appointment ',
            'icon' => '<i class="fa fa-calendar "></i>'
        ),

        array(
            'path' => 'clinic_prescription',
            'label' => 'Prescriptions',
            'icon' => '<i class="fa fa-cubes "></i>'
        ),

        array(
            'path' => 'appointment_new/request_manage',
            'label' => 'Request Manager',
            'icon' => '<i class="fa fa-calendar "></i>'
        ),

        array(
            'path' => 'invoices',
            'label' => 'Invoices',
            'icon' => '<i class="fa fa-dollar "></i>',
            'submenu' => array(
                array(
                    'path' => 'invoices/Index',
                    'label' => 'Invoices',
                    'icon' => '<i class="fa fa-dollar "></i>'
                ),

                array(
                    'path' => 'invoices_concepts',
                    'label' => 'Concept',
                    'icon' => '<i class="fa fa-check-square-o "></i>'
                )
            )
        ),

        array(
            'path' => 'users',
            'label' => 'Reports',
            'icon' => '<i class="fa fa-pencil-square-o "></i>',
            'submenu' => array(
                array(
                    'path' => 'actives_patients',
                    'label' => 'Actives Patients',
                    'icon' => '<i class="fa fa-user-plus "></i>'
                ),

                array(
                    'path' => 'inactives_patients',
                    'label' => 'Inactives Patients',
                    'icon' => '<i class="fa fa-user-times "></i>'
                ),

                array(
                    'path' => 'invoice_cancelled',
                    'label' => 'Cancelled invoices',
                    'icon' => '<i class="bi bi-currency-dollar"></i>'
                ),

                array(
                    'path' => 'invoice_debt',
                    'label' => 'Debts Invoices',
                    'icon' => '<i class="fa fa-exclamation "></i>'
                ),

                array(
                    'path' => 'appointments',
                    'label' => 'Appointments',
                    'icon' => '<i class="fa fa-calendar "></i>'
                ),

                // 🔹 Clinical Historial (nuevo item)
                array(
                    'path' => 'report/clinical_historial',
                    'label' => 'Clinical Historial',
                    'icon' => '<i class="fa fa-file-medical"></i>'
                )
            )
        ),


        array(
            'path' => 'users',
            'label' => 'User Manager',
            'icon' => '<i class="fa fa-users "></i>',
            'submenu' => array(
                array(
                    'path' => 'users/Index',
                    'label' => 'User Manager',
                    'icon' => '<i class="fa fa-users "></i>'
                ),

                array(
                    'path' => 'users/Patients_Register_Page',
                    'label' => 'Patients Register Page',
                    'icon' => ''
                ),

                array(
                    'path' => 'users/Register',
                    'label' => 'Patients Register',
                    'icon' => '<i class="fa fa-hospital-o "></i>',
                    'submenu' => array(
                        array(
                            'path' => 'users/Patients_Register_Page',
                            'label' => 'Patients Register Page',
                            'icon' => ''
                        )
                    )
                )
            )
        ),

        array(
            'path' => 'My_Appointment',
            'label' => 'My Appointments',
            'icon' => '<i class="fa fa-folder "></i>',
            'submenu' => array(
                array(
                    'path' => 'My_Appointment',
                    'label' => 'Appointment Patients ',
                    'icon' => '<i class="fa fa-folder-open "></i>'
                )
            )
        ),


    );

    /**
     * Lista de roles ahora usando IDs numéricos
     * Esto debe coincidir con la tabla `roles` de tu base de datos
     */
    public static $rol = array(
        array(
            "value" => 1,
            "label" => "Admin",
        ),
        array(
            "value" => 2,
            "label" => "Doctor",
        ),
        array(
            "value" => 3,
            "label" => "Assistant",
        ),
        array(
            "value" => 4,
            "label" => "Patients",
        ),
    );

    public static $gender = array(
        array(
            "value" => "Male",
            "label" => "Male",
        ),
        array(
            "value" => "Female",
            "label" => "Female",
        ),
    );
}
