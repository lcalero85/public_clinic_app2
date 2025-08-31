<?php 
$page_element_id = "approve-appointment-" . random_str();
$comp_model = new SharedController;
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
$show_header = $this->show_header;
$view_title = $this->view_title;
$redirect_to = $this->redirect_to;

$data = $this->view_data['data'] ?? [];
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="edit" data-display-type=""
    data-page-url="<?php print_link($current_page); ?>">
    <?php if ($show_header == true) { ?>
        <div class="bg-light p-3 mb-3">
            <div class="container">
                <div class="row ">
                    <div class="col ">
                        <h4 class="record-title">Approve Appointment Request</h4>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="">
        <div class="container">
            <div class="row ">
                <div class="col-md-7 comp-grid">
                    <?php $this::display_page_errors(); ?>
                    <div class="bg-light p-3 animated fadeIn page-content">
                        <form id="appointment-approve-form" role="form" 
                            enctype="multipart/form-data"
                            class="form page-form form-horizontal needs-validation"
                            action="<?php print_link("appointment_new/save_approval/" . $data['id_appointment'] . "?csrf_token=$csrf_token"); ?>"
                            method="post" novalidate>
                            <div>

                                <!-- Assigned Doctor -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="id_doc">Assigned Doctor <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <select required id="ctrl-id_doc" name="id_doc" class="custom-select">
                                                    <option value="">Select a value ...</option>
                                                    <?php
                                                    $id_doc_options = $comp_model->doctor_list();
                                                    $field_value = $data['id_doc'] ?? null;
                                                    if (!empty($id_doc_options)) {
                                                        foreach ($id_doc_options as $option) {
                                                            $value = $option['value'] ?? null;
                                                            $label = $option['label'] ?? $value;
                                                            $selected = ($value == $field_value ? "selected" : "");
                                                            echo "<option value=\"$value\" $selected>$label</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback">Please select a doctor</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Appointment Date -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="appointment_date">
                                                Appointment Date <span class="text-danger">*</span>
                                            </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <?php
                                                $appointment_date = '';
                                                if (!empty($_GET['date']) && $_GET['date'] != 'Not scheduled' && $_GET['date'] != '0000-00-00') {
                                                    $appointment_date = date("Y-m-d\TH:i", strtotime($_GET['date']));
                                                } elseif (!empty($data['appointment_date'])) {
                                                    $appointment_date = date("Y-m-d\TH:i", strtotime($data['appointment_date']));
                                                } else {
                                                    $appointment_date = date("Y-m-d\TH:i");
                                                }
                                                ?>
                                                <input id="ctrl-appointment_date"
                                                    class="form-control"
                                                    value="<?php echo $appointment_date; ?>"
                                                    type="datetime-local"
                                                    readonly required />
                                                <input type="hidden"
                                                    name="appointment_date"
                                                    value="<?php echo $appointment_date; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Notes -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="notes">Notes <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <textarea id="ctrl-notes" name="notes" class="form-control" required><?php echo $data['notes'] ?? ''; ?></textarea>
                                                <div class="invalid-feedback">Please enter notes</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Submit Buttons -->
                            <div class="form-group form-submit-btn-holder text-center mt-3">
                                <div class="form-ajax-status"></div>
                                <button class="btn btn-success" type="submit" id="submitBtn">
                                    Confirm Approval <i class="fa fa-check"></i>
                                </button>
                                <a href="<?php print_link("appointment_new/request_manage") ?>"
                                    class="btn btn-secondary" id="cancelBtn">
                                    Cancel <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Overlay de carga -->
<div id="form-preloader" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    z-index:9999;
    text-align:center;
    padding-top:20%;
    color:#fff;
    font-family:Arial, sans-serif;
">
    <div class="spinner-border text-light" role="status" style="width:3rem; height:3rem;"></div>
    <h5 class="mt-3">Please wait, operation in progress...</h5>
</div>

<!-- Script de validación y overlay -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("appointment-approve-form");
    const submitBtn = document.getElementById("submitBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const overlay = document.getElementById("form-preloader");

    form.addEventListener("submit", function (e) {
        // Validar campos requeridos
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add("was-validated");
            return false;
        }

        // Mostrar overlay
        overlay.style.display = "block";

        // Bloquear botones
        submitBtn.disabled = true;
        cancelBtn.classList.add("disabled"); 
    }, false);
});
</script>

<style>
.was-validated .form-control:invalid,
.was-validated .custom-select:invalid {
    border-color: #dc3545;
}
.was-validated .form-control:invalid:focus,
.was-validated .custom-select:invalid:focus {
    box-shadow: 0 0 0 0.2rem rgba(220,53,69,.25);
}
</style>
