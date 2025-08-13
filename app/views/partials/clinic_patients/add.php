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

<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="add" data-display-type=""
    data-page-url="<?php print_link($current_page); ?>">
    <?php if ($show_header == true) { ?>
        <div class="bg-light p-3 mb-3">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <h4 class="record-title">Add New Clinic Patients</h4>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-7 comp-grid">
                    <?php $this::display_page_errors(); ?>
                    <div class="bg-light p-3 animated fadeIn page-content">
                        <form id="clinic_patients-add-form" role="form" novalidate enctype="multipart/form-data"
                            class="form page-form form-horizontal needs-validation"
                            action="<?php print_link("clinic_patients/add?csrf_token=$csrf_token") ?>" method="post">
                            <div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="full_names">Full Names <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-full_names"
                                                value="<?php echo $this->set_field_value('full_names', ""); ?>"
                                                type="text" placeholder="Enter Full Names" maxlength="200" required
                                                name="full_names" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="address">Full Address <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <textarea placeholder="Enter Address" id="ctrl-address" required
                                                maxlength="255" rows="5" name="address"
                                                class="form-control"><?php echo $this->set_field_value('address', ""); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="gender">Gender <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?php
                                            $gender_options = Menu::$gender;
                                            if (!empty($gender_options)) {
                                                foreach ($gender_options as $option) {
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $checked = $this->set_field_checked('gender', $value, "");
                                                    ?>
                                                    <label class="custom-control custom-radio custom-control-inline">
                                                        <input id="ctrl-gender" class="custom-control-input" <?php echo $checked ?> value="<?php echo $value ?>" type="radio" required
                                                            name="gender" />
                                                        <span class="custom-control-label"><?php echo $label ?></span>
                                                    </label>
                                                <?php }
                                            } ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="birthdate">Birth date <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input id="ctrl-birthdate" class="form-control datepicker" required
                                                    value="<?php echo $this->set_field_value('birthdate', ""); ?>"
                                                    type="datetime" name="birthdate" placeholder="Enter Birth date"
                                                    data-enable-time="false" data-date-format="Y-m-d"
                                                    data-alt-format="F j, Y" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                               

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="register_observations">Register
                                                Observations <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <textarea placeholder="Enter Register Observations"
                                                id="ctrl-register_observations" required maxlength="255" rows="5"
                                                name="register_observations"
                                                class="form-control"><?php echo $this->set_field_value('register_observations', ""); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="referred">Medic Referred /insurance <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-referred"
                                                value="<?php echo $this->set_field_value('referred', ""); ?>"
                                                type="text" placeholder="Enter Referred" maxlength="100" required
                                                name="referred" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="diseases">Comments/Diseases <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <textarea placeholder="Enter Diseases" id="ctrl-diseases" required
                                                maxlength="255" rows="5" name="diseases"
                                                class="form-control"><?php echo $this->set_field_value('diseases', ""); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="id_blood_type">ID Blood Type <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select required="" id="ctrl-id_blood_type" name="id_blood_type"
                                                class="custom-select">
                                                <option value="">Select a value...</option>
                                                <?php
                                                $options = $comp_model->clinic_patients_id_blood_type_option_list();
                                                if (!empty($options)) {
                                                    foreach ($options as $option) {
                                                        $value = $option['value'];
                                                        $label = $option['label'];
                                                        $selected = $this->set_field_selected('id', $value, "");
                                                        echo "<option $selected value=\"$value\">$label</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="phone_patient">Phone Patient <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-phone_patient"
                                                value="<?php echo $this->set_field_value('phone_patient', ""); ?>"
                                                type="text" placeholder="Enter Phone Patient" maxlength="20" required
                                                name="phone_patient" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="manager">Emergency Contact Name<span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-manager"
                                                value="<?php echo $this->set_field_value('manager', ""); ?>" type="text"
                                                placeholder="Enter Emergency Contact Name" maxlength="100" required name="manager"
                                                class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                 <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="phone_patient">Emergency Contact Phone <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-emergency_contact_phone"
                                                value="<?php echo $this->set_field_value('emergency_contact_phone', ""); ?>"
                                                type="text" placeholder="Enter Emergency Contact Phone" maxlength="20" required
                                                name="emergency_contact_phone" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="email">Email Patient <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-email"
                                                    value="<?php echo $this->set_field_value('email', ""); ?>"
                                                    type="email" placeholder="Enter Email" required="" name="email"
                                                    class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="id_document_type">Document Type <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select required="" id="ctrl-id_document_type" name="id_document_type"
                                                class="custom-select">
                                                <option value="">Select a value...</option>
                                                <?php
                                                $options = $comp_model->clinic_patients_id_document_type_option_list();
                                                if (!empty($options)) {
                                                    foreach ($options as $option) {
                                                        $value = $option['value'];
                                                        $label = $option['label'];
                                                        $selected = $this->set_field_selected('id', $value, "");
                                                        echo "<option $selected value=\"$value\">$label</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="document_number">Document Number <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-document_number"
                                                    value="<?php echo $this->set_field_value('document_number', ""); ?>"
                                                    type="text" placeholder="Enter Document Number" required=""
                                                    name="document_number" class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="occupation">Occupation <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-occupation"
                                                    value="<?php echo $this->set_field_value('occupation', ""); ?>"
                                                    type="text" placeholder="Enter occupation" required=""
                                                    name="occupation" class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                  <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="allergies">Allergies
                                                Observations <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <textarea placeholder="Enter Allergies"
                                                id="ctrl-allergies" required maxlength="255" rows="5"
                                                name="allergies"
                                                class="form-control"><?php echo $this->set_field_value('allergies', ""); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <input id="ctrl-id_status"
                                    value="<?php echo $this->set_field_value('id_status', "1"); ?>" type="hidden"
                                    name="id_status" class="form-control" list="id_status_list" required />
                                <datalist id="id_status_list">
                                    <?php
                                    $id_status_options = $comp_model->clinic_patients_id_status_option_list();
                                    if (!empty($id_status_options)) {
                                        foreach ($id_status_options as $option) {
                                            $value = (!empty($option['value']) ? $option['value'] : null);
                                            $label = (!empty($option['label']) ? $option['label'] : $value);
                                            echo "<option value=\"$value\">$label</option>";
                                        }
                                    }
                                    ?>
                                </datalist>
                                <div class="form-group form-submit-btn-holder text-center mt-3">
                                    <div class="form-ajax-status"></div>
                                    <button class="btn btn-primary" type="submit">
                                        Submit
                                        <i class="fa fa-send"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>