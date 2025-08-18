<?php
$page_element_id = "reschedule-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
?>
<section class="page" id="<?php echo $page_element_id; ?>">
    <div class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">Reschedule Appointment</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="card p-3">
            <form action="<?php print_link("appointment_new/reschedule/$id?csrf_token=$csrf_token"); ?>" method="post">
                <div class="form-group">
                    <label for="approved_date">New Date</label>
                    <input class="form-control datepicker" type="datetime" name="approved_date" required
                           data-enable-time="true" data-date-format="Y-m-d H:i:S" />
                </div>
                <div class="form-group">
                    <label for="admin_response">Comment</label>
                    <textarea name="admin_response" class="form-control"></textarea>
                </div>
                <div class="text-center">
                    <button class="btn btn-warning" type="submit">
                        <i class="fa fa-calendar"></i> Reschedule
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
