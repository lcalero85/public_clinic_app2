<?php
$comp_model = new SharedController;
$page_element_id = "request-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
?>
<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="add" data-display-type="" data-page-url="<?php print_link($current_page); ?>">
    <div class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">Request Appointment</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row ">
            <div class="col-md-7 comp-grid">
                <?php $this :: display_page_errors(); ?>
                <div class="bg-light p-3 animated fadeIn page-content">
                    <form id="appointment-request-form" role="form" novalidate enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="<?php print_link("appointment_new/request_submit?csrf_token=$csrf_token") ?>" method="post">
                        <div class="form-group">
                            <label class="control-label">Motive</label>
                            <input type="text" name="motive" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label class="control-label">Description</label>
                            <textarea name="descritption" class="form-control"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Requested Date</label>
                            <input class="form-control datepicker" name="requested_date" required type="datetime" data-enable-time="true" data-date-format="Y-m-d H:i:S" />
                        </div>
                        <div class="form-group text-center">
                            <button class="btn btn-primary" type="submit">
                                <i class="fa fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
