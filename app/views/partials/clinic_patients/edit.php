<?php
$comp_model = new SharedController;
$page_element_id = "edit-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
$data = $this->view_data;
//$rec_id = $data['__tableprimarykey'];
$page_id = $this->route->page_id;
$show_header = $this->show_header;
$view_title = $this->view_title;
$redirect_to = $this->redirect_to;
?>
<!-- Import Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<!-- Link custom stylesheet and FontAwesome for icons -->
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="edit"  data-display-type="" data-page-url="<?php print_link($current_page); ?>">
    <?php
    if( $show_header == true ){
    ?>
    <div  class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">Edit  Clinic Patients</h4>
                </div>
            </div>
        </div>
    </div>
    <?php
    }
    ?>
    <div  class="">
        <div class="container">
            <div class="row ">
                <div class="col-md-7 comp-grid">
                    <?php $this :: display_page_errors(); ?>
                    <div  class="bg-light p-3 animated fadeIn page-content form-card">
                        <form novalidate  id="" role="form" enctype="multipart/form-data"  class="form page-form form-horizontal needs-validation clinic-form" action="<?php print_link("clinic_patients/edit/$page_id/?csrf_token=$csrf_token"); ?>" method="post">
                            <div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="full_names">Full Names <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-full_names"  value="<?php  echo $data['full_names']; ?>" type="text" placeholder="Enter Full Names" maxlength="200"  required="" name="full_names"  class="form-control " />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label class="control-label" for="address">Address <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="">
                                                    <textarea placeholder="Enter Address" id="ctrl-address"  required="" maxlength="255" rows="5" name="address" class=" form-control"><?php  echo $data['address']; ?></textarea>
                                                    <!--<div class="invalid-feedback animated bounceIn text-center">Please enter text</div>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label class="control-label" for="gender">Gender <span class="text-danger">*</span></label>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="">
                                                    <?php
                                                    $gender_options = Menu :: $gender;
                                                    $field_value = $data['gender'];
                                                    if(!empty($gender_options)){
                                                    foreach($gender_options as $option){
                                                    $value = $option['value'];
                                                    $label = $option['label'];
                                                    //check if value is among checked options
                                                    $checked = $this->check_form_field_checked($field_value, $value);
                                                    ?>
                                                    <label class="custom-control custom-radio custom-control-inline">
                                                        <input id="ctrl-gender" class="custom-control-input" <?php echo $checked ?>  value="<?php echo $value ?>" type="radio" required=""   name="gender" />
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
                                                    <label class="control-label" for="birthdate">Birth date <span class="text-danger">*</span></label>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <input id="ctrl-birthdate" class="form-control datepicker  datepicker"  required="" value="<?php  echo $data['birthdate']; ?>" type="datetime" name="birthdate" placeholder="Enter Birth date" data-enable-time="false" data-min-date="" data-max-date="" data-date-format="Y-m-d" data-alt-format="F j, Y" data-inline="false" data-no-calendar="false" data-mode="single" />
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
                                            <label class="control-label" for="phone_patient">Phone Patients <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-phone_patient"  value="<?php  echo $data['phone_patient']; ?>" type="text" placeholder="Enter Phone Patients" maxlength="200"  required="" name="phone_patient"  class="form-control " />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="email">Email Patients <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-email"  value="<?php  echo $data['email']; ?>" type="text" placeholder="Enter Email" maxlength="200"  required="" name="email"  class="form-control " />
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                     <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="manager">Emergency Contact Name <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-manager"  value="<?php  echo $data['manager']; ?>" type="text" placeholder="Emergency Contact Name " maxlength="200"  required="" name="manager"  class="form-control " />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                     <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="manager">Emergency Contact Phone <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-manager"  value="<?php  echo $data['emergency_contact_phone']; ?>" type="text" placeholder="Enter Emergency Contact Phone " maxlength="200"  required="" name="emergency_contact_phone"  class="form-control " />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                     <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="occupation">Occupation <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <input id="ctrl-occupation"  value="<?php  echo $data['occupation']; ?>" type="text" placeholder="Enter Occupation " maxlength="200"  required="" name="occupation"  class="form-control " />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group ">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label class="control-label" for="referred">Medic Referred /insurance <span class="text-danger">*</span></label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <div class="">
                                                                <input id="ctrl-referred"  value="<?php  echo $data['referred']; ?>" type="text" placeholder="Enter Referred" maxlength="100"  required="" name="referred"  class="form-control " />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>      
                                          
                                          
                                                <div class="form-group ">
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <label class="control-label" for="register_observations">Register Observations <span class="text-danger">*</span></label>
                                                        </div>
                                                        <div class="col-sm-8">
                                                            <div class="">
                                                                <textarea placeholder="Enter Register Observations" id="ctrl-register_observations"  required="" maxlength="255" rows="5" name="register_observations" class=" form-control"><?php  echo $data['register_observations']; ?></textarea>
                                                                <!--<div class="invalid-feedback animated bounceIn text-center">Please enter text</div>-->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                    <div class="form-group ">
                                                        <div class="row">
                                                            <div class="col-sm-4">
                                                                <label class="control-label" for="diseases">Comments/Diseases <span class="text-danger">*</span></label>
                                                            </div>
                                                            <div class="col-sm-8">
                                                                <div class="">
                                                                    <textarea placeholder="Enter Diseases" id="ctrl-diseases"  required="" maxlength="255" rows="5" name="diseases" class=" form-control"><?php  echo $data['diseases']; ?></textarea>
                                                                    <!--<div class="invalid-feedback animated bounceIn text-center">Please enter text</div>-->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                       <?php if (USER_ROLE == 'Admin') { // si el rol del usuario es Admin ?>
                                                           <div class="form-group">
        <div class="row">
            <div class="col-sm-4">
                <label class="control-label" for="id_status">Patients Status</label>
            </div>
            <div class="col-sm-8">
                <select id="ctrl-id_status" name="id_status" class="custom-select" required>
                    <?php 
                    $id_status_options = $comp_model->clinic_patients_id_status_option_list();
                    if(!empty($id_status_options)){
                        foreach($id_status_options as $option){
                            $value = (!empty($option['value']) ? $option['value'] : null);
                            $label = (!empty($option['label']) ? $option['label'] : $value);
                            $selected = ($value == $data['id_status']) ?  : '';
                            echo "<option value=\"$value\">$label</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
<?php } else { ?>
    <input id="ctrl-id_status"  
           value="<?php echo $data['id_status']; ?>" 
           type="hidden"  
           name="id_status"  
           required 
           class="form-control" />
<?php } ?>

                                                        
                                                            
                                                               
                                                                    <div class="form-ajax-status"></div>
                                                                    <div class="form-group text-center">
                                                                        <button class="btn btn-primary" type="submit">
                                                                            Update
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
