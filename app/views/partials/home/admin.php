<?php
$page_id = null;
$comp_model = new SharedController;
$current_page = $this->set_current_page_link();
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<div>
    <div class="bg-light py-4 mb-4">
        <div class="container">

           <!-- Encabezado de bienvenida -->
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
        <h5 class="mb-0 text-primary"><i class="fa fa-bolt me-2"></i> Quick Actions</h5>
    </div>
    <div class="card-body">
        <div class="row g-2 justify-content-center"> 
            <div class="col-12 col-sm-6 col-md-auto">
                <a class="btn btn-primary w-100" href="<?php print_link("clinic_patients/list") ?>">
                    <i class="fa fa-user-plus me-2"></i> New Patient
                </a>
            </div>
            <div class="col-12 col-sm-6 col-md-auto">
                <a class="btn btn-primary w-100" href="<?php print_link("doc/list") ?>">
                    <i class="fa fa-user-md me-2"></i> New Doctor
                </a>
            </div>
            <div class="col-12 col-sm-6 col-md-auto">
                <a class="btn btn-primary w-100" href="<?php print_link("appointment_new/list") ?>">
                    <i class="fa fa-calendar-plus-o me-2"></i> New Appointment
                </a>
            </div>
            <div class="col-12 col-sm-6 col-md-auto">
                <a class="btn btn-primary w-100" href="<?php print_link("clinic_prescription/list") ?>">
                    <i class="fa fa-file-medical me-2"></i> New Prescriptions
                </a>
            </div>
            <div class="col-12 col-sm-6 col-md-auto">
                <a class="btn btn-primary w-100" href="<?php echo print_link("report/clinical_historial"); ?>">
                    <i class="fa fa-notes-medical me-2"></i> See Historical Clinic
                </a>
            </div>
        </div>
    </div>
</div>


            <!-- General Dashboard -->
<div class="py-4"> 
    <div class="container">
        <div class="page-header mb-3">
            <h4 class="section-title">📊 General Dashboard</h4>
        </div>

        <!-- Pacientes -->
        <div class="mb-4">
            <h5 class="section-title">👥 Patients</h5>
            <div class="row">
                <div class="col-md-3 col-sm-4 comp-grid">
                    <?php $rec_count = $comp_model->getcount_patients();  ?>
                    <a class="animated zoomIn record-count card bg-light text-dark" href="<?php print_link("clinic_patients/") ?>">
                        <div class="row">
                            <div class="col-2"><i class="fa fa-user-plus"></i></div>
                            <div class="col-10"><div class="title">Patients</div><small>Total registered</small></div>
                            <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-4 comp-grid">
                    <?php $rec_count = $comp_model->getcount_activespatients();  ?>
                    <a class="animated zoomIn record-count card bg-light text-dark" href="<?php print_link("actives_patients/") ?>">
                        <div class="row">
                            <div class="col-2"><i class="fa fa-users"></i></div>
                            <div class="col-10"><div class="title">Active Patients</div><small>Currently active</small></div>
                            <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-4 comp-grid">
                    <?php $rec_count = $comp_model->getcount_inactivespatients();  ?>
                    <a class="animated zoomIn record-count card bg-light text-dark" href="<?php print_link("inactives_patients/") ?>">
                        <div class="row">
                            <div class="col-2"><i class="fa fa-user-times"></i></div>
                            <div class="col-10"><div class="title">Inactive Patients</div><small>Currently inactive</small></div>
                            <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Citas -->
        <div class="mb-4">
            <h5 class="section-title">📅 Appointments</h5>
            <div class="row">
                <div class="col-md-3 col-sm-4 comp-grid">
                    <?php $rec_count = $comp_model->getcount_appointments();  ?>
                    <a class="animated zoomIn record-count card bg-light text-dark" href="<?php print_link("appointments/") ?>">
                        <div class="row">
                            <div class="col-2"><i class="fa fa-calendar-check-o"></i></div>
                            <div class="col-10"><div class="title">Appointments</div><small>Total scheduled</small></div>
                            <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-sm-4 comp-grid">
                    <?php $pending_count = $comp_model->getcount_pending_appointments2(); ?>
                    <a class="animated zoomIn record-count card bg-light text-dark" href="<?php print_link("appointment_new/request_manage") ?>">
                        <div class="row">
                            <div class="col-2"><i class="fa fa-calendar-plus-o"></i></div>
                            <div class="col-10"><div class="title">Pending Requests</div><small>Awaiting approval</small></div>
                            <h4 class="value"><strong><?php echo $pending_count; ?></strong></h4>
                        </div>
                    </a>
                </div>
            </div>
        </div>


        <!-- Doctores -->
        <div class="mb-4">
            <h5 class="section-title">🧑‍⚕️ Doctors</h5>
            <div class="row">
                <div class="col-md-3 col-sm-4 comp-grid">
                    <?php $rec_count = $comp_model->getcount_doctors();  ?>
                    <a class="animated zoomIn record-count card bg-light text-dark" href="<?php print_link("doc/") ?>">
                        <div class="row">
                            <div class="col-2"><i class="fa fa-user-md"></i></div>
                            <div class="col-10"><div class="title">Doctors</div><small>Registered</small></div>
                            <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Usuarios -->
        <div class="mb-4">
            <h5 class="section-title">👤 Users</h5>
            <div class="row">
                <div class="col-md-3 col-sm-4 comp-grid">
                    <?php $rec_count = $comp_model->getcount_users();  ?>
                    <a class="animated zoomIn record-count card bg-light text-dark" href="<?php print_link("users/") ?>">
                        <div class="row">
                            <div class="col-2"><i class="fa fa-users"></i></div>
                            <div class="col-10"><div class="title">Users</div><small>Total registered</small></div>
                            <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mb-4">
            <h5 class="section-title">🔔 Recent Activity</h5>
            <div class="row">
                <div class="col-md-6 col-sm-12 comp-grid">
                    <div class="card bg-light text-dark shadow-sm h-100">
                        <div class="card-body" id="recent-activity" style="max-height: 300px; overflow-y: auto;">
                            <p class="text-muted text-center">Loading activity...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
function loadActivity(){
    $("#recent-activity").load("api/activity_feed.php");
}
loadActivity();          
setInterval(loadActivity, 30000); 
</script>
