<?php
$page_element_id = "deny-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
// ✅ Ahora ya no usamos $data[], sino variables directas
$id_appointment = isset($id_appointment) ? $id_appointment : null;
$admin_response = isset($admin_response) ? $admin_response : "";
?>
<section class="page" id="<?php echo $page_element_id; ?>">
    <div class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title text-danger">
                        <i class="fa fa-ban"></i> Deny Appointment
                    </h4>
                    <p class="text-muted">Provide a reason for denying this appointment</p>
                </div>
            </div>
        </div>
    </div>
    <?php $this::display_page_errors(); ?>
    <div class="container">
        <div class="card shadow-sm p-4">
            <form id="deny-form"
    action="<?php print_link("appointment_new/deny/" . $data['id_appointment'] . "?csrf_token=$csrf_token"); ?>"
    method="post"
    class="needs-validation"
    novalidate>

    <!-- Campo oculto para asegurar que el ID siempre viaje -->
    <input type="hidden" name="id_appointment" value="<?php echo $data['id_appointment']; ?>">

    <div class="form-group">
        <label for="admin_response">Admin Response</label>
        <textarea name="admin_response" id="admin_response" class="form-control" required></textarea>
    </div>

    <div class="form-group text-center mt-3">
        <button type="submit" class="btn btn-danger">
            <i class="fa fa-times"></i> Deny Appointment
        </button>
    </div>
</form>
        </div>
    </div>
</section>

<!-- ✅ Bootstrap Validation Script -->
<script>
    (function() {
        'use strict';
        var form = document.getElementById('deny-form');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    })();
</script>