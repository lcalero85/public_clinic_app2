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
            <!-- Encabezado -->
            <div class="page-header mb-3">
                <h4 class="fw-bold">
                    👋 Welcome back, <span class="text-primary"><?php echo USER_NAME ?></span>
                </h4>
                <small class="text-muted">
                    Role: <?php echo USER_ROLE_NAME ?> | Last access: <?php echo date_now(); ?>
                </small>
            </div>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0 section-title">⚡ Quick Actions</h5>
                </div>
                <div class="card-body quick-actions">
                    <div class="row g-2 text-center">
                        <div class="col-12 col-sm-6 col-md-3">
                            <a href="<?php print_link('my_appointment'); ?>" class="btn btn-success w-100">
                                <i class="fa fa-calendar-check"></i> My Appointments
                            </a>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <a href="<?php print_link('clinic_prescription/add'); ?>" class="btn btn-success w-100">
                                <i class="fa fa-file-prescription"></i> Add Prescription
                            </a>
                        </div>
                        <div class="col-12 col-sm-6 col-md-3">
                            <a href="<?php print_link('report/clinical_historial'); ?>" class="btn btn-info w-100">
                                <i class="fa fa-notes-medical"></i> Clinical Historial
                            </a>
                        </div>
                        <?php if (USER_ROLE_ID == 3): ?>
                            <div class="col-12 col-sm-6 col-md-3">
                                <a href="<?php print_link('my_appointment?today=1'); ?>" class="btn btn-warning w-100">
                                    <i class="fa fa-clock"></i> Today’s Appointments
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- General Dashboard -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0 section-title">📊 General Dashboard</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">

                        <!-- Appointments -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 text-center shadow-sm border-0">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <i class="fa fa-calendar-check fa-2x text-primary"></i>
                                    </div>
                                    <h6 class="fw-bold">Appointments</h6>
                                    <p class="text-muted small">Manage your scheduled appointments</p>
                                    <a href="<?php print_link('my_appointment'); ?>" class="btn btn-outline-primary btn-sm mt-2">
                                        View My Appointments
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Prescriptions -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 text-center shadow-sm border-0">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <i class="fa fa-file-prescription fa-2x text-success"></i>
                                    </div>
                                    <h6 class="fw-bold">Prescriptions</h6>
                                    <p class="text-muted small">Create and manage prescriptions</p>
                                    <a href="<?php print_link('clinic_prescription/add'); ?>" class="btn btn-outline-success btn-sm mt-2">
                                        Add New Prescription
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Clinical Historial -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card h-100 text-center shadow-sm border-0">
                                <div class="card-body">
                                    <div class="mb-2">
                                        <i class="fa fa-notes-medical fa-2x text-info"></i>
                                    </div>
                                    <h6 class="fw-bold">Clinical Historial</h6>
                                    <p class="text-muted small">Review patient medical history</p>
                                    <a href="<?php print_link('report/clinical_historial'); ?>" class="btn btn-outline-info btn-sm mt-2">
                                        See Historial
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Today's Appointments -->
                        <?php if (USER_ROLE_ID == 3): ?>
                            <div class="col-md-6 col-lg-3">
                                <div class="card h-100 text-center shadow-sm border-0">
                                    <div class="card-body">
                                        <div class="mb-2">
                                            <i class="fa fa-clock fa-2x text-warning"></i>
                                        </div>
                                        <h6 class="fw-bold">Today's Appointments</h6>
                                        <p class="text-muted small">Check all your appointments today</p>
                                        <a href="<?php print_link('my_appointment?today=1'); ?>" class="btn btn-outline-warning btn-sm mt-2">
                                            View Today’s Appointments
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>