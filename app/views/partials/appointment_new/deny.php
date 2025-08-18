<?php
$page_element_id = "deny-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
?>
<section class="page" id="<?php echo $page_element_id; ?>">
    <div class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">Deny Appointment</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="card p-3">
            <form action="<?php print_link("appointment_new/deny/$id?csrf_token=$csrf_token"); ?>" method="post">
                <div class="form-group">
                    <label for="admin_response">Reason for denial</label>
                    <textarea name="admin_response" class="form-control" required></textarea>
                </div>
                <div class="text-center">
                    <button class="btn btn-danger" type="submit">
                        <i class="fa fa-times"></i> Deny Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
