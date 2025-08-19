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

<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="edit" data-display-type="" data-page-url="<?php print_link($current_page); ?>">
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
                        <form id="appointment-approve-form" role="form" novalidate enctype="multipart/form-data" 
                              class="form page-form form-horizontal needs-validation" 
                              action="<?php print_link("appointment_new/save_approval/" . $data['id_appointment']); ?>" 
                              method="post">
                            <div>

                                <!-- Assigned Doctor -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="id_doc">Assigned Doctor <span class="text-danger">*</span></label>
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
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Appointment Date -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="appointment_date">Appointment Date <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input id="ctrl-appointment_date" class="form-control datepicker" 
                                                       required 
                                                       value="<?php echo $data['appointment_date'] ?? ''; ?>" 
                                                       type="datetime" 
                                                       name="appointment_date" 
                                                       placeholder="Enter Appointment Date" 
                                                       data-enable-time="true" 
                                                       data-date-format="Y-m-d H:i:S" 
                                                       data-alt-format="F j, Y - H:i" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="notes">Notes</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <textarea id="ctrl-notes" name="notes" class="form-control"><?php echo $data['notes'] ?? ''; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Submit Buttons -->
                            <div class="form-group form-submit-btn-holder text-center mt-3">
                                <div class="form-ajax-status"></div>
                                <button class="btn btn-success" type="submit">
                                    Confirm Approval <i class="fa fa-check"></i>
                                </button>
                                <a href="<?php print_link("appointment_new/request_manage") ?>" class="btn btn-secondary">
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


