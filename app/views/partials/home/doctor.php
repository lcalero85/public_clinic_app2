<?php
$page_id = null;
$comp_model = new SharedController;
$current_page = $this->set_current_page_link();
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<div>
    <div class="bg-light p-3 mb-3">
        <div class="container">
            <h4>Welcome back,<?php echo USER_NAME ?> Here’s your summary for today.</h4>
            <hr class="my-4" style="border-top: 1px solid #e0e0e0;">
            <div class="row ">
                <div class="col-md-12 comp-grid">
                    <h6><?php
                        echo "User : " . USER_NAME . "<BR>";
                        echo "Role : " . USER_ROLE_NAME . "<br>";
                        echo "Last access :" . date_now();
                        ?></h6>
                </div>
            </div>
        </div>
    </div>
    <div class="py-5">
        <div class="container">
            <div class="page-header">
                <h4>General Dashboard</h4>
            </div>
            <div class="row ">
                <div class="card text-center shadow-sm m-2">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fa fa-calendar-check fa-2x text-primary"></i>
                        </div>
                        <h5 class="card-title">Appointments</h5>
                        <a href="<?php print_link('my_appointment'); ?>" class="btn btn-primary btn-sm mt-2">
                            View My Appointments
                        </a>
                    </div>
                </div>
                <div class="card text-center shadow-sm m-2">
                    <div class="card-body">
                        <div class="mb-2">
                            <i class="fa fa-file-prescription fa-2x text-success"></i>
                        </div>
                        <h5 class="card-title">Prescriptions</h5>
                        <a href="<?php print_link('clinic_prescription\add'); ?>" class="btn btn-success btn-sm mt-2">
                            Add new Prescriptions
                        </a>
                    </div>
                </div>

                <?php if (USER_ROLE_ID == 3): ?>
                    <div class="card text-center shadow-sm m-2">
                    <div class="card-body">
                        <div class="mb-2">
                             <i class="fa fa-calendar-check fa-2x text-primary"></i>
                        </div>
                        <h5 class="card-title">Today's Appointments</h5>
                        <a href="<?php print_link('my_appointment'); ?>" class="btn btn-primary btn-sm mt-2">
                            View My Appointments today
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>