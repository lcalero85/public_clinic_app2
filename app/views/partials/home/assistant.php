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
                <h4 class="section-title">👋 Welcome back, <?php echo USER_NAME ?></h4>
                <div class="text-muted d-flex flex-wrap gap-3">
                    <small>Role: <?php echo USER_ROLE_NAME ?></small>
                   &nbsp<small>Last access: <?php echo date_now(); ?></small>
                </div>
            </div>
            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">⚡ Quick Actions</h5>
                </div>
                <div class="card-body text-center">
                    <div class="d-flex flex-wrap justify-content-center">
                        <a class="btn btn-primary btn-sm mx-2 my-1" href="<?php print_link("clinic_patients/list") ?>">
                            <i class="fa fa-user-plus"></i> New Patient
                        </a>
                        <a class="btn btn-info btn-sm mx-2 my-1" href="<?php print_link("doc/list") ?>">
                            <i class="fa fa-user-md"></i> New Doctor
                        </a>
                        <a class="btn btn-success btn-sm mx-2 my-1" href="<?php print_link("appointment_new/list") ?>">
                            <i class="fa fa-calendar-check"></i> New Appointment
                        </a>
                        <a class="btn btn-warning btn-sm mx-2 my-1"
                            href="<?php print_link("clinic_prescription/list") ?>">
                            <i class="fa fa-file-prescription"></i> New Prescriptions
                        </a>
                        <a class="btn btn-secondary btn-sm mx-2 my-1"
                            href="<?php echo print_link("report/clinical_historial"); ?>">
                            <i class="fa fa-notes-medical"></i> See Clinic Historial
                        </a>
                    </div>
                </div>
            </div>

            <!-- General Dashboard -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="fw-bold mb-0">📊 General Dashboard</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Patients -->
                        <div class="col-md-4 col-lg-3">
                            <?php $rec_count = $comp_model->getcount_patients(); ?>
                            <div class="card h-100 text-center shadow-sm border-0">
                                <div class="card-body">
                                    <i class="fa fa-user-plus fa-2x text-primary mb-2"></i>
                                    <h6 class="fw-bold">Patients</h6>
                                    <h4 class="fw-bold text-primary"><?php echo $rec_count; ?></h4>
                                    <a href="<?php print_link("clinic_patients/") ?>"
                                        class="btn btn-outline-primary btn-sm mt-2">View</a>
                                </div>
                            </div>
                        </div>

                        <!-- Inactive Patients -->
                        <div class="col-md-4 col-lg-3">
                            <?php $rec_count = $comp_model->getcount_inactivespatients(); ?>
                            <div class="card h-100 text-center shadow-sm border-0">
                                <div class="card-body">
                                    <i class="fa fa-user-times fa-2x text-danger mb-2"></i>
                                    <h6 class="fw-bold">Inactive Patients</h6>
                                    <h4 class="fw-bold text-danger"><?php echo $rec_count; ?></h4>
                                    <a href="<?php print_link("inactives_patients/") ?>"
                                        class="btn btn-outline-danger btn-sm mt-2">View</a>
                                </div>
                            </div>
                        </div>

                        <!-- Active Patients -->
                        <div class="col-md-4 col-lg-3">
                            <?php $rec_count = $comp_model->getcount_activespatients(); ?>
                            <div class="card h-100 text-center shadow-sm border-0">
                                <div class="card-body">
                                    <i class="fa fa-users fa-2x text-success mb-2"></i>
                                    <h6 class="fw-bold">Active Patients</h6>
                                    <h4 class="fw-bold text-success"><?php echo $rec_count; ?></h4>
                                    <a href="<?php print_link("actives_patients/") ?>"
                                        class="btn btn-outline-success btn-sm mt-2">View</a>
                                </div>
                            </div>
                        </div>

                        <!-- Appointments -->
                        <div class="col-md-4 col-lg-3">
                            <?php $rec_count = $comp_model->getcount_appointments(); ?>
                            <div class="card h-100 text-center shadow-sm border-0">
                                <div class="card-body">
                                    <i class="fa fa-calendar-check fa-2x text-info mb-2"></i>
                                    <h6 class="fw-bold">Appointments</h6>
                                    <h4 class="fw-bold text-info"><?php echo $rec_count; ?></h4>
                                    <a href="<?php print_link("appointments/") ?>"
                                        class="btn btn-outline-info btn-sm mt-2">View</a>
                                </div>
                            </div>
                        </div>

                        <!-- Invoices -->
                        <div class="col-md-4 col-lg-3">
                            <?php $rec_count = $comp_model->getcount_invoices(); ?>
                            <div class="card h-100 text-center shadow-sm border-0">
                                <div class="card-body">
                                    <i class="fa fa-calculator fa-2x text-secondary mb-2"></i>
                                    <h6 class="fw-bold">Invoices</h6>
                                    <h4 class="fw-bold text-secondary"><?php echo $rec_count; ?></h4>
                                    <a href="<?php print_link("invoices/") ?>"
                                        class="btn btn-outline-secondary btn-sm mt-2">View</a>
                                </div>
                            </div>
                        </div>

                        <!-- Doctors -->
                        <div class="col-md-4 col-lg-3">
                            <?php $rec_count = $comp_model->getcount_doctors(); ?>
                            <div class="card h-100 text-center shadow-sm border-0">
                                <div class="card-body">
                                    <i class="fa fa-user-md fa-2x text-dark mb-2"></i>
                                    <h6 class="fw-bold">Doctors</h6>
                                    <h4 class="fw-bold text-dark"><?php echo $rec_count; ?></h4>
                                    <a href="<?php print_link("doc/") ?>"
                                        class="btn btn-outline-dark btn-sm mt-2">View</a>
                                </div>
                            </div>
                        </div>

                        <!-- Prescriptions -->
                        <div class="col-md-4 col-lg-3">
                            <?php $rec_count = $comp_model->getcount_prescriptions(); ?>
                            <div class="card h-100 text-center shadow-sm border-0">
                                <div class="card-body">
                                    <i class="fa fa-file-prescription fa-2x text-warning mb-2"></i>
                                    <h6 class="fw-bold">Prescriptions</h6>
                                    <h4 class="fw-bold text-warning"><?php echo $rec_count; ?></h4>
                                    <a href="<?php print_link("clinic_prescription/") ?>"
                                        class="btn btn-outline-warning btn-sm mt-2">View</a>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>