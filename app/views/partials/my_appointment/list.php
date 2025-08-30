<?php
$comp_model = new SharedController;
$page_element_id = "list-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
$view_data = $this->view_data;
$records = $view_data->records;
$record_count = $view_data->record_count;
$total_records = $view_data->total_records;
$field_name = $this->route->field_name;
$field_value = $this->route->field_value;
?>
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="list" data-display-type="table"
    data-page-url="<?php print_link($current_page); ?>">
    <div class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">My Appointments</h4>
                </div>
                <div class="col-sm-4 ">
                    <div class="">
                        <?php if (USER_ROLE_ID == 4): ?>
                            <a href="<?php print_link('appointment_new/request'); ?>" class="btn btn-sm btn-success">
                                <i class="fa fa-plus"></i> Request Appointment
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($_GET['today']) && $_GET['today'] == 1): ?>
                            <a href="<?php print_link('my_appointment'); ?>" class="btn btn-sm btn-secondary">
                                <i class="fa fa-list"></i> View All Appointments
                            </a>
                        <?php else: ?>
                            <a href="<?php print_link('my_appointment?today=1'); ?>" class="btn btn-sm btn-primary">
                                <i class="fa fa-calendar-check"></i> View Today's Appointments
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="row ">
            <div class="col-md-12 comp-grid">
                <?php $this::display_page_errors(); ?>
                <div class=" animated fadeIn page-content">
                    <div id="my_appointment-list-records">
                        <div id="page-report-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm text-left">
                                    <thead class="table-header bg-light">
                                        <tr>
                                            <th>#ID</th>
                                            <th>Patient Name</th>
                                            <th>Motive</th>
                                            <th>Description</th>
                                            <th>Comments</th>
                                            <th>Appointment Date</th>
                                            <th>Requested Date</th>
                                            <th>Register Date</th>
                                            <th>Doctor</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($records)) {
                                            $page_number = isset($this->route->page_number) ? $this->route->page_number : 1;
                                            $limit = isset($this->limit_count) ? $this->limit_count : count($records);
                                            $counter = ($page_number - 1) * $limit + 1;

                                            foreach ($records as $data) { ?>
                                                <tr>
                                                    <td data-label="#ID"><?php echo $counter++; ?></td>
                                                    <td data-label="Patient Name"><?php echo $data['full_names']; ?></td>
                                                    <td data-label="Motive"><?php echo $data['motive']; ?></td>
                                                    <td data-label="Description"><?php echo $data['description']; ?></td>
                                                    <td data-label="Comments"><?php echo $data['historial']; ?></td>
                                                    <td data-label="Appointment Date"><?php echo $data['appointment_date']; ?>
                                                    </td>
                                                    <td data-label="Requested Date">
                                                        <?php echo !empty($data['requested_date']) ? $data['requested_date'] : "Not provided"; ?>
                                                    </td>
                                                    <td data-label="Register Date"><?php echo $data['register_date']; ?></td>
                                                    <td data-label="Doctor"><?php echo $data['doctor_name']; ?></td>
                                                    <td data-label="Status"><?php echo $data['status']; ?></td>
                                                </tr>
                                        <?php }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class=" border-top mt-2">
                            <div class="row justify-content-center">
                                <div class="col-md-auto">
                                    <?php
                                    if (!empty($data->show_pagination) && $data->show_pagination == true) {
                                        $pager = new Pagination(total_records: $data->total_records, current_record_count: $data->record_count);
                                        $pager->route = $this->route;
                                        $pager->show_page_count = true;
                                        $pager->show_record_count = true;
                                        $pager->show_page_limit = true;
                                        $pager->limit_count = $this->limit_count;
                                        $pager->show_page_number_list = true;
                                        $pager->pager_link_range = 5;
                                        $pager->render();
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>