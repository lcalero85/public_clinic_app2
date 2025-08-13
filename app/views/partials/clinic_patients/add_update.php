<?php
// Create an instance of the SharedController to access common functionality
$comp_model = new SharedController;

// Generate a unique element ID for the form section
$page_element_id = "add-page-" . random_str();

// Get the current page link for use in the form
$current_page = $this->set_current_page_link();

// CSRF token for form security
$csrf_token = Csrf::$token;

// Settings for page display (header, title, redirection, etc.)
$show_header = $this->show_header;
$view_title = $this->view_title;
$redirect_to = $this->redirect_to;
?>

<!-- Import Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<!-- Link custom stylesheet and FontAwesome for icons -->
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Main section of the form -->
<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="add" data-display-type="" data-page-url="<?php print_link($current_page); ?>">
    <?php if($show_header == true){ ?>
    <!-- Optional page header -->
    <div class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">Add New Clinic Patients</h4>
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

                    <!-- Main form container -->
                    <div class="bg-light p-3 animated fadeIn page-content">
                        <form id="clinic_patients-add-form" role="form" novalidate enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="<?php print_link("clinic_patients/add_update?csrf_token=$csrf_token") ?>" method="post">
                            <div>
                                <!-- Full Name field -->
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="full_names">Full Names <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-full_names" value="<?php echo $this->set_field_value('full_names',""); ?>" type="text" placeholder="Enter Full Names" maxlength="200" required name="full_names" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Address field -->
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="address">Full Address <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <textarea placeholder="Enter Address" id="ctrl-address" required maxlength="255" rows="5" name="address" class="form-control"><?php echo $this->set_field_value('address',""); ?></textarea>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gender radio buttons -->
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="gender">Gender <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <?php
                                            $gender_options = Menu::$gender;
                                            if(!empty($gender_options)){
                                                foreach($gender_options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    $checked = $this->set_field_checked('gender', $value, "");
                                            ?>
                                            <label class="custom-control custom-radio custom-control-inline">
                                                <input id="ctrl-gender" class="custom-control-input" <?php echo $checked ?> value="<?php echo $value ?>" type="radio" required name="gender" />
                                                <span class="custom-control-label"><?php echo $label ?></span>
                                            </label>
                                            <?php } } ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Birth date field -->
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="birthdate">Birth date <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input id="ctrl-birthdate" class="form-control datepicker" required value="<?php echo $this->set_field_value('birthdate',""); ?>" type="datetime" name="birthdate" placeholder="Enter Birth date" data-date-format="Y-m-d" data-alt-format="F j, Y" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Other fields... (Age, Observations, Referred, Diseases, etc.) -->
                                <!-- Repeating similar structure as above -->

                                <!-- Hidden status field -->
                                <input id="ctrl-id_status" value="<?php echo $this->set_field_value('id_status',"1"); ?>" type="hidden" name="id_status" class="form-control" />

                                <!-- Email field -->
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="email">Email <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input id="ctrl-email" value="<?php echo $this->set_field_value('email',""); ?>" type="email" placeholder="Enter Email" required name="email" class="form-control" />
                                        </div>
                                    </div>
                                </div>

                                <!-- Photo upload using Dropzone -->
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="photo">Photo <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="dropzone required" input="#ctrl-photo" fieldname="photo" data-multiple="false" dropmsg="Choose files or drag and drop" btntext="Browse" extensions=".jpg,.png,.gif,.jpeg" filesize="10" maximum="1">
                                                <input name="photo" id="ctrl-photo" required class="dropzone-input form-control" value="<?php echo $this->set_field_value('photo',""); ?>" type="text" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Submit button -->
                                <div class="form-group form-submit-btn-holder text-center mt-3">
                                    <div class="form-ajax-status"></div>
                                    <button class="btn btn-primary" type="submit">
                                        Submit <i class="fa fa-send"></i>
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
