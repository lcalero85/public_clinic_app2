<?php 
$page_id = null;
$comp_model = new SharedController;
$current_page = $this->set_current_page_link();
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<div>
    <div  class="bg-light p-3 mb-3">
        <div class="container">
            <h4>Welcome back,<?php echo USER_NAME ?> Here’s your summary for today.</h4>
            <hr class="my-4" style="border-top: 1px solid #e0e0e0;">
            <div class="row ">
                <div class="col-md-12 comp-grid">
                    <h6 ><?php
                        echo "User : ".USER_NAME."<BR>";
                            echo "Role : ".USER_ROLE ."<br>";
                            echo "Last access :".date_now();
                            ?></h6>
                            <a  class="btn btn-primary" href="<?php print_link("clinic_patients/list") ?>">
                                New Patient 
                            </a>
                            <a  class="btn btn-primary" href="<?php print_link("doc/list") ?>">
                                New Doctor 
                            </a>
                            <a  class="btn btn-primary" href="<?php print_link("appointment_new/list") ?>">
                                New  appointment 
                            </a>
                            <a  class="btn btn-primary" href="<?php print_link("clinic_prescription/list") ?>">
                                New Prescriptions 
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div  class="py-5">
                <div class="container">
                    <div class="page-header"><h4>General Dashboard</h4></div>
                    <div class="row ">
                        <div class="col-md-3 col-sm-4 comp-grid">
                            <?php $rec_count = $comp_model->getcount_patients();  ?>
                            <a class="animated zoomIn record-count card bg-light text-dark"  href="<?php print_link("clinic_patients/") ?>">
                                <div class="row">
                                    <div class="col-2">
                                        <i class="fa fa-user-plus "></i>
                                    </div>
                                    <div class="col-10">
                                        <div class="flex-column justify-content align-center">
                                            <div class="title">Patients</div>
                                            <small class=""></small>
                                        </div>
                                    </div>
                                    <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 comp-grid">
                            <?php $rec_count = $comp_model->getcount_inactivespatients();  ?>
                            <a class="animated zoomIn record-count card bg-light text-dark"  href="<?php print_link("inactives_patients/") ?>">
                                <div class="row">
                                    <div class="col-2">
                                        <i class="fa fa-user-times "></i>
                                    </div>
                                    <div class="col-10">
                                        <div class="flex-column justify-content align-center">
                                            <div class="title">Inactives Patients</div>
                                            <small class=""></small>
                                        </div>
                                    </div>
                                    <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 comp-grid">
                            <?php $rec_count = $comp_model->getcount_activespatients();  ?>
                            <a class="animated zoomIn record-count card bg-light text-dark"  href="<?php print_link("actives_patients/") ?>">
                                <div class="row">
                                    <div class="col-2">
                                        <i class="fa fa-users "></i>
                                    </div>
                                    <div class="col-10">
                                        <div class="flex-column justify-content align-center">
                                            <div class="title">Actives Patients</div>
                                            <small class=""></small>
                                        </div>
                                    </div>
                                    <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 comp-grid">
                            <?php $rec_count = $comp_model->getcount_appointments();  ?>
                            <a class="animated zoomIn record-count card bg-light text-dark"  href="<?php print_link("appointments/") ?>">
                                <div class="row">
                                    <div class="col-2">
                                        <i class="fa fa-calendar-check-o "></i>
                                    </div>
                                    <div class="col-10">
                                        <div class="flex-column justify-content align-center">
                                            <div class="title">Appointments</div>
                                            <small class=""></small>
                                        </div>
                                    </div>
                                    <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 comp-grid">
                            <?php $rec_count = $comp_model->getcount_invoices();  ?>
                            <a class="animated zoomIn record-count card bg-light text-dark"  href="<?php print_link("invoices/") ?>">
                                <div class="row">
                                    <div class="col-2">
                                        <i class="fa fa-calculator "></i>
                                    </div>
                                    <div class="col-10">
                                        <div class="flex-column justify-content align-center">
                                            <div class="title">Invoices</div>
                                            <small class=""></small>
                                        </div>
                                    </div>
                                    <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 comp-grid">
                            <?php $rec_count = $comp_model->getcount_doctors();  ?>
                            <a class="animated zoomIn record-count card bg-light text-dark"  href="<?php print_link("doc/") ?>">
                                <div class="row">
                                    <div class="col-2">
                                        <i class="fa fa-user-md "></i>
                                    </div>
                                    <div class="col-10">
                                        <div class="flex-column justify-content align-center">
                                            <div class="title">Doctors</div>
                                            <small class=""></small>
                                        </div>
                                    </div>
                                    <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-4 comp-grid">
                            <?php $rec_count = $comp_model->getcount_prescriptions();  ?>
                            <a class="animated zoomIn record-count card bg-light text-dark"  href="<?php print_link("clinic_prescription/") ?>">
                                <div class="row">
                                    <div class="col-2">
                                        <i class="fa fa-globe"></i>
                                    </div>
                                    <div class="col-10">
                                        <div class="flex-column justify-content align-center">
                                            <div class="title">Prescriptions</div>
                                            <small class=""></small>
                                        </div>
                                    </div>
                                    <h4 class="value"><strong><?php echo $rec_count; ?></strong></h4>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
