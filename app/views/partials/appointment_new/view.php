<?php
//check if current user role is allowed access to the pages
$can_add = ACL::is_allowed("appointment_new/add");
$can_edit = ACL::is_allowed("appointment_new/edit");
$can_view = ACL::is_allowed("appointment_new/view");
$can_delete = ACL::is_allowed("appointment_new/delete");
?>
<?php
$comp_model = new SharedController;
$page_element_id = "view-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
//Page Data Information from Controller
$data = $this->view_data;
//$rec_id = $data['__tableprimarykey'];
$page_id = $this->route->page_id; //Page id from url
$view_title = $this->view_title;
$show_header = $this->show_header;
$show_edit_btn = $this->show_edit_btn;
$show_delete_btn = $this->show_delete_btn;
$show_export_btn = $this->show_export_btn;
?>
<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="view" data-display-type="table" data-page-url="<?php print_link($current_page); ?>">
    <?php
    if ($show_header == true) {
    ?>
        <div class="bg-light p-3 mb-3">
            <div class="container">
                <div class="row ">
                    <div class="col ">
                        <h4 class="record-title">View Appointment New</h4>
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
                <div class="col-md-12 comp-grid">
                    <?php $this::display_page_errors(); ?>
                    <div class="card animated fadeIn page-content">
                        <?php
                        $counter = 0;
                        if (!empty($data)) {
                            $rec_id = (!empty($data['id_appointment']) ? urlencode($data['id_appointment']) : null);
                            $counter++;
                        ?>
                            <div id="page-report-body" class="">
                                <table class="table table-hover table-borderless table-striped">
                                    <!-- Table Body Start -->
                                    <tbody class="page-data" id="page-data-<?php echo $page_element_id; ?>">

                                        <tr class="td-full_names">
                                            <th class="title"> Patient: </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['clinic_patients_full_names']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="td-full_names">
                                            <th class="title"> Motive: </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['motive']; ?>
                                                </span>
                                            </td>
                                        </tr>

                                        <tr class="td-full_names">
                                            <th class="title"> Appointment Date:: </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['appointment_date']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="td-full_names">
                                            <th class="title"> Doctor: </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['doc_full_names']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="td-full_names">
                                            <th class="title"> Appointment Type: </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['Speciality']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="td-full_names">
                                            <th class="title"> Appointment Priority: </th>
                                            <td class="value">
                                                <span>
                                                    <?php
                                                    $comp_model = new SharedController;
                                                    $priority_options = $comp_model->appointment_new_priority_option_list();
                                                    $priority_label = $data['priority']; // valor por defecto si no hay coincidencia

                                                    if (!empty($priority_options)) {
                                                        foreach ($priority_options as $option) {
                                                            if ($option['value'] == $data['priority']) {
                                                                $priority_label = $option['label'];
                                                                break;
                                                            }
                                                        }
                                                    }

                                                    echo $priority_label;
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="td-reminder_preference">
                                            <th class="title"> Reminder Preference: </th>
                                            <td class="value">
                                                <span>
                                                    <?php
                                                    $comp_model = new SharedController;
                                                    $reminder_options = $comp_model->appointment_new_reminder_preference_option_list();
                                                    $reminder_label = $data['reminder_preference']; // valor por defecto si no hay coincidencia

                                                    if (!empty($reminder_options)) {
                                                        foreach ($reminder_options as $option) {
                                                            if ($option['value'] == $data['reminder_preference']) {
                                                                $reminder_label = $option['label'];
                                                                break;
                                                            }
                                                        }
                                                    }

                                                    echo $reminder_label ?: "Not specified";
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="td-full_names">
                                            <th class="title"> Follow Up Required: </th>
                                            <td class="value">
                                                <span>
                                                    <?php
                                                    $follow_up_text = "Not specified"; // Valor por defecto

                                                    if ($data['follow_up_required'] == 1) {
                                                        $follow_up_text = "Yes";
                                                    } elseif ($data['follow_up_required'] == 2) {
                                                        $follow_up_text = "Not Required";
                                                    }

                                                    echo $follow_up_text;
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>

                                        <tr class="td-full_names">
                                            <th class="title"> Status Appointment: </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['appointment_status_status']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <!-- Table Body End -->
                                </table>
                            </div>
                            <div class="p-3 d-flex">
                                <div class="dropup export-btn-holder mx-1">
                                    
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <?php $export_print_link = $this->set_current_page_link(array('format' => 'print')); ?>
                                        <a class="dropdown-item export-link-btn" data-format="print" href="<?php print_link($export_print_link); ?>" target="_blank">
                                            <img src="<?php print_link('assets/images/print.png') ?>" class="mr-2" /> PRINT
                                        </a>
                                        <?php $export_pdf_link = $this->set_current_page_link(array('format' => 'pdf')); ?>
                                        <a class="dropdown-item export-link-btn" data-format="pdf" href="<?php print_link($export_pdf_link); ?>" target="_blank">
                                            <img src="<?php print_link('assets/images/pdf.png') ?>" class="mr-2" /> PDF
                                        </a>
                                        <?php $export_csv_link = $this->set_current_page_link(array('format' => 'csv')); ?>
                                        <a class="dropdown-item export-link-btn" data-format="csv" href="<?php print_link($export_csv_link); ?>" target="_blank">
                                            <img src="<?php print_link('assets/images/csv.png') ?>" class="mr-2" /> CSV
                                        </a>
                                        <?php $export_excel_link = $this->set_current_page_link(array('format' => 'excel')); ?>
                                        <a class="dropdown-item export-link-btn" data-format="excel" href="<?php print_link($export_excel_link); ?>" target="_blank">
                                            <img src="<?php print_link('assets/images/xsl.png') ?>" class="mr-2" /> EXCEL
                                        </a>
                                    </div>
                                </div>
                                <?php if ($can_edit) { ?>
                                    <a class="btn btn-sm btn-info" href="<?php print_link("appointment_new/edit/$rec_id"); ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                <?php } ?>
                                <?php if ($can_delete) { ?>
                                    <a class="btn btn-sm btn-danger record-delete-btn mx-1" href="<?php print_link("appointment_new/delete/$rec_id/?csrf_token=$csrf_token&redirect=$current_page"); ?>" data-prompt-msg="Are you sure you want to delete this record?" data-display-style="modal">
                                        <i class="fa fa-times"></i> Delete
                                    </a>
                                <?php } ?>
                            </div>
                        <?php
                        } else {
                        ?>
                            <!-- Empty Record Message -->
                            <div class="text-muted p-3">
                                <i class="fa fa-ban"></i> No Record Found
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>