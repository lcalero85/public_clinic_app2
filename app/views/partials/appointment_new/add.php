<?php
$comp_model = new SharedController;
$page_element_id = "add-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
$show_header = $this->show_header;
$view_title = $this->view_title;
$redirect_to = $this->redirect_to;
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="add" data-display-type="" data-page-url="<?php print_link($current_page); ?>">
    <?php
    if ($show_header == true) {
    ?>
        <div class="bg-light p-3 mb-3">
            <div class="container">
                <div class="row ">
                    <div class="col ">
                        <h4 class="record-title">Add New Appointment New</h4>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="">
        <div class="container">
            <div class="row ">
                <div class="col-md-7 comp-grid">
                    <?php $this::display_page_errors(); ?>
                    <div class="bg-light p-3 animated fadeIn page-content">
                        <form id="appointment_new-add-form" role="form" novalidate enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="<?php print_link("appointment_new/add?csrf_token=$csrf_token") ?>" method="post">
                            <div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="id_patient">Patient <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <select required="" id="ctrl-id_patient" name="id_patient" placeholder="Select a value ..." class="custom-select">
                                                    <option value="">Select a value ...</option>
                                                    <?php
                                                    $id_patient_options = $comp_model->appointment_new_id_patient_option_list();
                                                    if (!empty($id_patient_options)) {
                                                        foreach ($id_patient_options as $option) {
                                                            $value = (!empty($option['value']) ? $option['value'] : null);
                                                            $label = (!empty($option['label']) ? $option['label'] : $value);
                                                            $selected = $this->set_field_selected('id_patient', $value, "");
                                                    ?>
                                                            <option <?php echo $selected; ?> value="<?php echo $value; ?>">
                                                                <?php echo $label; ?>
                                                            </option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="id_doc">Doctor <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <select required="" id="ctrl-id_doc" name="id_doc" placeholder="Select a value ..." class="custom-select">
                                                    <option value="">Select a value ...</option>
                                                    <?php
                                                    $id_doc_options = $comp_model->appointment_new_id_doc_option_list();
                                                    if (!empty($id_doc_options)) {
                                                        foreach ($id_doc_options as $option) {
                                                            $value = (!empty($option['value']) ? $option['value'] : null);
                                                            $label = (!empty($option['label']) ? $option['label'] : $value);
                                                            $selected = $this->set_field_selected('id_doc', $value, "");
                                                    ?>
                                                            <option <?php echo $selected; ?> value="<?php echo $value; ?>">
                                                                <?php echo $label; ?>
                                                            </option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="motive">Motive <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <textarea placeholder="Enter Motive" id="ctrl-motive" required="" maxlength="255" rows="5" name="motive" class=" form-control"><?php echo $this->set_field_value('motive', ""); ?></textarea>
                                                <!--<div class="invalid-feedback animated bounceIn text-center">Please enter text</div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="descritption">Symptoms Descritptions <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <textarea placeholder="Enter Descritption" id="ctrl-descritption" required="" maxlength="255" rows="5" name="descritption" class=" form-control"><?php echo $this->set_field_value('descritption', ""); ?></textarea>
                                                <!--<div class="invalid-feedback animated bounceIn text-center">Please enter text</div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="id_appointment_type">Appointment Type <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <select required id="ctrl-id_appointment_type" name="id_appointment_type" placeholder="Select a type ..." class="custom-select">
                                                    <option value="">Select a type ...</option>
                                                    <?php
                                                    $appointment_type_options = $comp_model->appointment_new_id_appointment_type_option_list();
                                                    if (!empty($appointment_type_options)) {
                                                        foreach ($appointment_type_options as $option) {
                                                            $value = (!empty($option['value']) ? $option['value'] : null);
                                                            $label = (!empty($option['label']) ? $option['label'] : $value);
                                                            $selected = $this->set_field_selected('id_appointment_type', $value, "");
                                                    ?>
                                                            <option <?php echo $selected; ?> value="<?php echo $value; ?>">
                                                                <?php echo $label; ?>
                                                            </option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="priority">Priority <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <select required id="ctrl-priority" name="priority" placeholder="Select a value ..." class="custom-select">
                                                    <option value="">Select a value ...</option>
                                                    <?php
                                                    $priority_options = $comp_model->appointment_new_priority_option_list();
                                                    if (!empty($priority_options)) {
                                                        foreach ($priority_options as $option) {
                                                            $value = (!empty($option['value']) ? $option['value'] : null);
                                                            $label = (!empty($option['label']) ? $option['label'] : $value);
                                                            $selected = $this->set_field_selected('priority', $value, "");
                                                    ?>
                                                            <option <?php echo $selected; ?> value="<?php echo $value; ?>">
                                                                <?php echo $label; ?>
                                                            </option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="reminder_preference">Reminder Preference <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <select required id="ctrl-reminder_preference" name="reminder_preference" placeholder="Select a value ..." class="custom-select">
                                                    <option value="">Select a value ...</option>
                                                    <?php
                                                    $reminder_options = $comp_model->appointment_new_reminder_preference_option_list();
                                                    if (!empty($reminder_options)) {
                                                        foreach ($reminder_options as $option) {
                                                            $value = (!empty($option['value']) ? $option['value'] : null);
                                                            $label = (!empty($option['label']) ? $option['label'] : $value);
                                                            $selected = $this->set_field_selected('reminder_preference', $value, "");
                                                    ?>
                                                            <option <?php echo $selected; ?> value="<?php echo $value; ?>">
                                                                <?php echo $label; ?>
                                                            </option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="follow_up_required">Follow Up Required <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <?php
                                                $options = [
                                                    ["value" => 1, "label" => "Yes"],
                                                    ["value" => 0, "label" => "No"]
                                                ];
                                                foreach ($options as $option) {
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $checked = $this->set_field_checked('follow_up_required', $value, "");
                                                ?>
                                                    <label class="custom-control custom-radio custom-control-inline">
                                                        <input class="custom-control-input" type="radio"
                                                            id="ctrl-follow_up_required-<?php echo $value; ?>"
                                                            name="follow_up_required"
                                                            value="<?php echo $value; ?>"
                                                            <?php echo $checked; ?> required>
                                                        <span class="custom-control-label"><?php echo $label; ?></span>
                                                    </label>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="appointment_date">Appointment Date <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input id="ctrl-appointment_date" class="form-control datepicker  datepicker" required="" value="<?php echo $this->set_field_value('appointment_date', ""); ?>" type="datetime" name="appointment_date" placeholder="Enter Appointment Date" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group form-submit-btn-holder text-center mt-3">
                                <div class="form-ajax-status"></div>
                                <button class="btn btn-primary" type="submit">
                                    Submit
                                    <i class="fa fa-send"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>