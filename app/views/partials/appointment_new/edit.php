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
<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="edit" data-display-type="" data-page-url="<?php print_link($current_page); ?>">
    <?php
    if ($show_header == true) {
    ?>
        <div class="bg-light p-3 mb-3">
            <div class="container">
                <div class="row ">
                    <div class="col ">
                        <h4 class="record-title">Edit Appointment New</h4>
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
                        <form novalidate id="" role="form" enctype="multipart/form-data" class="form page-form form-horizontal needs-validation" action="<?php print_link("appointment_new/edit/$page_id/?csrf_token=$csrf_token"); ?>" method="post">
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
                                                    $rec = $data['id_patient'];
                                                    $id_patient_options = $comp_model->appointment_new_id_patient_option_list();
                                                    if (!empty($id_patient_options)) {
                                                        foreach ($id_patient_options as $option) {
                                                            $value = (!empty($option['value']) ? $option['value'] : null);
                                                            $label = (!empty($option['label']) ? $option['label'] : $value);
                                                            $selected = ($value == $rec ? 'selected' : null);
                                                    ?>
                                                            <option
                                                                <?php echo $selected; ?> value="<?php echo $value; ?>"><?php echo $label; ?>
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
                                                    $rec = $data['id_doc'];
                                                    $id_doc_options = $comp_model->appointment_new_id_doc_option_list();
                                                    if (!empty($id_doc_options)) {
                                                        foreach ($id_doc_options as $option) {
                                                            $value = (!empty($option['value']) ? $option['value'] : null);
                                                            $label = (!empty($option['label']) ? $option['label'] : $value);
                                                            $selected = ($value == $rec ? 'selected' : null);
                                                    ?>
                                                            <option
                                                                <?php echo $selected; ?> value="<?php echo $value; ?>"><?php echo $label; ?>
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
                                            <label class="control-label" for="motive">Reason <span class="text-danger">*</span></label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="">
                                                <textarea placeholder="Enter Motive" id="ctrl-motive" required="" maxlength="255" rows="5" name="motive" class=" form-control"><?php echo $data['motive']; ?></textarea>
                                                <!--<div class="invalid-feedback animated bounceIn text-center">Please enter text</div>-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <label class="control-label" for="nex_appointment_date">
                                                Next Appointment Date <span class="text-danger">*</span>
                                            </label>
                                        </div>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <?php
                                                // Si la fecha es inválida o vacía, no mostrar valor
                                                $nex_date_value = !empty($data['nex_appointment_date']) && $data['nex_appointment_date'] != '0000-00-00'
                                                    ? $data['nex_appointment_date']
                                                    : '';
                                                ?>
                                                <input id="ctrl-nex_appointment_date"
                                                    class="form-control datepicker"
                                                    required
                                                    value="<?php echo $nex_date_value; ?>"
                                                    type="text"
                                                    name="nex_appointment_date"
                                                    placeholder="Enter Next Appointment Date"
                                                    data-enable-time="false"
                                                    data-min-date=""
                                                    data-max-date=""
                                                    data-date-format="Y-m-d"
                                                    data-alt-format="F j, Y"
                                                    data-inline="false"
                                                    data-no-calendar="false"
                                                    data-mode="single" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (USER_ROLE == 'Admin') { ?>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <label class="control-label" for="id_status_appointment">
                                                    Appointment Status <span class="text-danger">*</span>
                                                </label>
                                            </div>
                                            <div class="col-sm-8">
                                                <select id="ctrl-id_status_appointment"
                                                    name="id_status_appointment"
                                                    class="form-control"
                                                    style="width:100%;height:100%" required>
                                                    <option value="">-- Select Status --</option>
                                                    <?php
                                                    $comp_model = new SharedController;
                                                    $status_options = $comp_model->appointment_new_status_option_list();
                                                    $current_value = $data['id_status_appointment'] ?? '';

                                                    if (!empty($status_options)) {
                                                        foreach ($status_options as $option) {
                                                            $value = $option['value'];
                                                            $label = $option['label'];
                                                            $selected = ($value == $current_value) ? 'selected' : '';
                                                            echo "<option value=\"$value\" $selected>$label</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
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