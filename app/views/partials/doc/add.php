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
    <?php
    if ($show_header == true) {
        ?>
        <div class="bg-light p-3 mb-3">
            <div class="container">
                <div class="row ">
                    <div class="col ">
                        <h4 class="record-title">Add New Doctor</h4>
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
                        <form id="doc-add-form" role="form" novalidate enctype="multipart/form-data"
                            class="form page-form form-horizontal needs-validation"
                            action="<?php print_link("doc/add?csrf_token=$csrf_token") ?>" method="post">
                            <div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="full_names">Full Names <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-full_names"
                                                    value="<?php echo $this->set_field_value('full_names', ""); ?>"
                                                    type="text" placeholder="Enter Full Names" maxlength="200"
                                                    required="" name="full_names" class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="address">Address <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <textarea placeholder="Enter Address" id="ctrl-address" required=""
                                                    maxlength="255" rows="5" name="address"
                                                    class=" form-control"><?php echo $this->set_field_value('address', ""); ?></textarea>
                                                <!--<div class="invalid-feedback animated bounceIn text-center">Please enter text</div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="birthdate">Birthdate <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input id="ctrl-birthdate" class="form-control datepicker  datepicker"
                                                    required=""
                                                    value="<?php echo $this->set_field_value('birthdate', ""); ?>"
                                                    type="datetime" name="birthdate" placeholder="Enter Birthdate"
                                                    data-enable-time="true" data-min-date="" data-max-date=""
                                                    data-date-format="Y-m-d H:i:S" data-alt-format="F j, Y - H:i"
                                                    data-inline="false" data-no-calendar="false" data-mode="single" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="gender">Gender <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <?php
                                                $gender_options = Menu::$gender;
                                                if (!empty($gender_options)) {
                                                    foreach ($gender_options as $option) {
                                                        $value = $option['value'];
                                                        $label = $option['label'];
                                                        //check if current option is checked option
                                                        $checked = $this->set_field_checked('gender', $value, "");
                                                        ?>
                                                        <label class="custom-control custom-radio custom-control-inline">
                                                            <input id="ctrl-gender" class="custom-control-input" <?php echo $checked ?> value="<?php echo $value ?>" type="radio"
                                                                required="" name="gender" />
                                                            <span class="custom-control-label"><?php echo $label ?></span>
                                                        </label>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="dni">DNI<span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-dni"
                                                    value="<?php echo $this->set_field_value('dni', ""); ?>" type="text"
                                                    placeholder="Enter Doctor DNI" maxlength="200" required=""
                                                    name="dni" class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="Speciality">Speciality <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <textarea placeholder="Enter Speciality" id="ctrl-Speciality"
                                                    required="" maxlength="255" rows="5" name="Speciality"
                                                    class=" form-control"><?php echo $this->set_field_value('Speciality', ""); ?></textarea>
                                                <!--<div class="invalid-feedback animated bounceIn text-center">Please enter text</div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="license_number">License Number <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-license_number"
                                                    value="<?php echo $this->set_field_value('license_number', ""); ?>"
                                                    type="text" placeholder="Enter License Number" maxlength="200"
                                                    required="" name="license_number" class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="license_issuer">License Issuer <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-license_issuer"
                                                    value="<?php echo $this->set_field_value('license_issuer', ""); ?>"
                                                    type="text" placeholder="Enter License Issuer" maxlength="200"
                                                    required="" name="license_issuer" class="form-control " />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="license_issue_date">License Issue Date
                                                <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-license_issue_date"
                                                    value="<?php echo $this->set_field_value('license_issue_date', ""); ?>"
                                                    type="date" placeholder="Select License Issue Date" required=""
                                                    name="license_issue_date" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="license_expiry_date">License Expiry Date
                                                <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-license_expiry_date"
                                                    value="<?php echo $this->set_field_value('license_expiry_date', ""); ?>"
                                                    type="date" placeholder="Select License Expiry Date" required=""
                                                    name="license_expiry_date" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="university">University <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-university"
                                                    value="<?php echo $this->set_field_value('university', ""); ?>"
                                                    type="text" placeholder="Enter University" maxlength="200"
                                                    required="" name="university" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="years_experience">Years of Experience
                                                <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-years_experience"
                                                    value="<?php echo $this->set_field_value('years_experience', ""); ?>"
                                                    type="text" placeholder="Enter Years of Experience" maxlength="10"
                                                    required="" name="years_experience" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="office_phone">Office Phone <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-office_phone"
                                                    value="<?php echo $this->set_field_value('office_phone', ""); ?>"
                                                    type="text" placeholder="Enter Office Phone" maxlength="20"
                                                    required="" name="office_phone" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="work_email">Work Email <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-work_email"
                                                    value="<?php echo $this->set_field_value('work_email', ""); ?>"
                                                    type="email" placeholder="Enter Work Email" maxlength="200"
                                                    required="" name="work_email" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="working_hours">Working Hours <span
                                                    class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-working_hours"
                                                    value="<?php echo $this->set_field_value('working_hours', ""); ?>"
                                                    type="text" placeholder="Enter Working Hours" maxlength="100"
                                                    required="" name="working_hours" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (USER_ROLE === 'Admin') { ?>
                                    <div class="form-group ">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label class="control-label" for="status">Status <span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="">
                                                    <select required id="ctrl-status" name="status" class="custom-select">
                                                        <option value="">Seleccione una opción</option>
                                                        <?php
                                                        $status_options = $comp_model->doc_status_enum_options();
                                                        if (!empty($status_options)) {
                                                            foreach ($status_options as $opt) {
                                                                $value = $opt['value'];
                                                                $label = $opt['label'];
                                                                $selected = $this->set_field_selected('status', $value, "");
                                                                echo "<option value=\"$value\" $selected>$label</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <!-- Campo oculto para usuarios que no son admin -->
                                    <input type="hidden" name="status" value="Active">
                                <?php } ?>


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