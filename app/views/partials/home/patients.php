<?php
$page_id = null;
$comp_model = new SharedController;
$current_page = $this->set_current_page_link();
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<div>
    <div class="bg-light py-4 mb-4 shadow-sm border-bottom">
        <div class="container">
               <div class="page-header mb-3">
                <h4 class="section-title">👋 Welcome back, <?php echo USER_NAME ?></h4>
                <div class="text-muted d-flex flex-wrap gap-3">
                    <small>Role: <?php echo USER_ROLE_NAME ?></small>
               &nbsp<small>Last access: <?php echo date_now(); ?></small>
                </div>
            </div>
            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0 section-title">⚡ Quick Actions</h5>
                </div>
                <div class="quick-actions-container">

                     <a href="<?php print_link('appointment_new/request'); ?>"
                        class="btn btn-success btn-sm">
                        <i class="fa fa-plus-circle"></i> Request Appointment
                    </a>
                    <a href="<?php print_link('my_appointment?today=1'); ?>"
                        class="btn btn-success btn-sm">
                        <i class="fa fa-clock"></i> View Today’s Appointments
                    </a>

                    <a href="<?php print_link('my_appointment?'); ?>"
                        class="btn btn-info btn-sm">
                        <i class="fa fa-calendar-alt"></i> View All Appointments
                    </a>

                    <a href="<?php print_link('report/clinical_historial'); ?>"
                        class="btn btn-danger btn-sm">
                        <i class="fa fa-notes-medical"></i> View My Clinical History
                    </a>
                </div>
                <p></p>
            </div>
            <style>
                /* Contenedor de las acciones rápidas */
                .quick-actions-container {
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: center;
                    gap: 15px;
                    /* separación horizontal en desktop */
                }

                /* Botones uniformes en desktop */
                .quick-actions-container .btn {
                    flex: 1 1 220px;
                    /* mismo ancho mínimo */
                    max-width: 240px;
                    /* límite para que no se estiren demasiado */
                    text-align: center;
                    padding: 12px;
                    font-weight: 600;
                }

                /* Vista móvil */
                @media (max-width: 768px) {
                    .quick-actions-container {
                        flex-direction: column;
                        /* se apilan */
                        gap: 12px;
                        /* separación vertical */
                    }

                    .quick-actions-container .btn {
                        flex: 1 1 100%;
                        max-width: 100%;
                        width: 100%;
                        margin-bottom: 0;
                        /* evita que se duplique con gap */
                    }
                }
            </style>

            <!-- Dashboard general -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0  section-title">📊 My General Dashboard</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <!-- Ver todas las citas -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 text-center shadow-sm border-0">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <i class="fa fa-calendar-alt fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="fw-bold">View All Appointments</h6>
                                    <p class="text-muted small">Check your complete appointment history</p>
                                    <a href="<?php print_link('my_appointment?'); ?>" class="btn btn-outline-primary btn-sm mt-2">
                                        View All Appointments
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Citas de hoy -->
                        <?php if (USER_ROLE_ID == 4): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 text-center shadow-sm border-0">
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <i class="fa fa-clock fa-2x text-success"></i>
                                        </div>
                                        <h6 class="fw-bold">Today's Appointments</h6>
                                        <p class="text-muted small">See all your appointments for today</p>
                                        <a href="<?php print_link('my_appointment?today=1'); ?>" class="btn btn-outline-success btn-sm mt-2">
                                            View My Appointments Today
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-4">
                            <div class="card h-100 text-center shadow-sm border-0">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <i class="fa fa-notes-medical fa-2x text-info"></i>
                                    </div>
                                    <h6 class="fw-bold">My Clinical History</h6>
                                    <p class="text-muted small">View your complete clinical history</p>
                                    <a href="<?php print_link('report/clinical_historial'); ?>"
                                        class="btn btn-outline-info btn-sm mt-2">
                                        View My History
                                    </a>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>