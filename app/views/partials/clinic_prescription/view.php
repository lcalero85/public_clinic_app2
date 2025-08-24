<?php 
//check if current user role is allowed access to the pages
$can_add = ACL::is_allowed("clinic_prescription/add");
$can_edit = ACL::is_allowed("clinic_prescription/edit");
$can_view = ACL::is_allowed("clinic_prescription/view");
$can_delete = ACL::is_allowed("clinic_prescription/delete");
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
<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="view"  data-display-type="table" data-page-url="<?php print_link($current_page); ?>">
    <?php
    if( $show_header == true ){
    ?>
    <div  class="bg-light p-3 mb-3">
        <div class="container">
            <div class="row ">
                <div class="col ">
                    <h4 class="record-title">View  Clinic Prescription</h4>
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
                <div class="col-md-12 comp-grid">
                    <?php $this :: display_page_errors(); ?>
                    <div  class="card animated fadeIn page-content">
                        <?php
                        $counter = 0;
                        if(!empty($data)){
                        $rec_id = (!empty($data['id_prescription']) ? urlencode($data['id_prescription']) : null);
                        $counter++;
                        ?>
                        <div id="page-report-body" class="">
                            <table class="table table-hover table-borderless table-striped">
                                <!-- Table Body Start -->
                                <tbody class="page-data" id="page-data-<?php echo $page_element_id; ?>">
                                    <tr  class="td-id_appointment">
                                        <th class="title"> Date Appointment: </th>
                                        <td class="value">
                                            <a size="sm" class="btn btn-sm btn-primary page-modal" href="<?php print_link("masterdetail/index/clinic_prescription/appointment_new/id_appointment/" . urlencode($data['id_appointment'])) ?>">
                                                <i class="fa fa-eye"></i> <?php echo $data['appointment_new_appointment_date'] ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr  class="td-id_patient">
                                        <th class="title"> Patient: </th>
                                        <td class="value">
                                            <a size="sm" class="btn btn-sm btn-primary page-modal" href="<?php print_link("masterdetail/index/clinic_prescription/clinic_patients/id_patient/" . urlencode($data['id_patient'])) ?>">
                                                <i class="fa fa-eye"></i> <?php echo $data['clinic_patients_full_names'] ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr  class="td-id_doctor">
                                        <th class="title"> Doctor: </th>
                                        <td class="value">
                                            <a size="sm" class="btn btn-sm btn-primary page-modal" href="<?php print_link("masterdetail/index/clinic_prescription/doc/id/" . urlencode($data['id_doctor'])) ?>">
                                                <i class="fa fa-eye"></i> <?php echo $data['doc_full_names'] ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr  class="td-description_prescription">
                                        <th class="title">  Prescription: </th>
                                        <td class="value">
                                            <span <?php if($can_edit){ ?> data-pk="<?php echo $data['id_prescription'] ?>" 
                                                data-url="<?php print_link("clinic_prescription/editfield/" . urlencode($data['id_prescription'])); ?>" 
                                                data-name="description_prescription" 
                                                data-title="Enter Description " 
                                                data-placement="left" 
                                                data-toggle="click" 
                                                data-type="textarea" 
                                                data-mode="popover" 
                                                data-showbuttons="left" 
                                                class="is-editable" <?php } ?>>
                                                <?php echo $data['description_prescription']; ?> 
                                            </span>
                                        </td>
                                    </tr>
                                    <tr  class="td-additional_comments">
                                        <th class="title"> Comments: </th>
                                        <td class="value">
                                            <span <?php if($can_edit){ ?> data-source='<?php print_link('api/json/clinic_prescription_additional_comments_option_list'); ?>' 
                                                data-pk="<?php echo $data['id_prescription'] ?>" 
                                                data-url="<?php print_link("clinic_prescription/editfield/" . urlencode($data['id_prescription'])); ?>" 
                                                data-name="additional_comments" 
                                                data-title="Enter Comments" 
                                                data-placement="left" 
                                                data-toggle="click" 
                                                data-type="textarea" 
                                                data-mode="popover" 
                                                data-showbuttons="left" 
                                                class="is-editable" <?php } ?>>
                                                <?php echo $data['additional_comments']; ?> 
                                            </span>
                                        </td>
                                    </tr>
                                    <tr  class="td-id_user">
                                        <th class="title"> Register User: </th>
                                        <td class="value">
                                            <a size="sm" class="btn btn-sm btn-primary page-modal" href="<?php print_link("masterdetail/index/clinic_prescription/users/id_user/" . urlencode($data['id_user'])) ?>">
                                                <i class="fa fa-eye"></i> <?php echo $data['users_full_names'] ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr  class="td-register_date">
                                        <th class="title"> Register Date: </th>
                                        <td class="value">
                                            <span <?php if($can_edit){ ?> data-value="<?php echo $data['register_date']; ?>" 
                                                data-pk="<?php echo $data['id_prescription'] ?>" 
                                                data-url="<?php print_link("clinic_prescription/editfield/" . urlencode($data['id_prescription'])); ?>" 
                                                data-name="register_date" 
                                                data-title="Enter Register Date" 
                                                data-placement="left" 
                                                data-toggle="click" 
                                                data-type="text" 
                                                data-mode="popover" 
                                                data-showbuttons="left" 
                                                class="is-editable" <?php } ?>>
                                                <?php echo $data['register_date']; ?> 
                                            </span>
                                        </td>
                                    </tr>
                                    <tr  class="td-update_date">
                                        <th class="title"> Update Date: </th>
                                        <td class="value">
                                            <span <?php if($can_edit){ ?> data-value="<?php echo $data['update_date']; ?>" 
                                                data-pk="<?php echo $data['id_prescription'] ?>" 
                                                data-url="<?php print_link("clinic_prescription/editfield/" . urlencode($data['id_prescription'])); ?>" 
                                                data-name="update_date" 
                                                data-title="Enter Update Date" 
                                                data-placement="left" 
                                                data-toggle="click" 
                                                data-type="text" 
                                                data-mode="popover" 
                                                data-showbuttons="left" 
                                                class="is-editable" <?php } ?>>
                                                <?php echo $data['update_date']; ?> 
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                                <!-- Table Body End -->
                            </table>
                        </div>
                        <div class="p-3 d-flex">
                            <div class="dropup export-btn-holder mx-1">
                                <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-save"></i> Export
                                </button>
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
                                            <?php if($can_edit){ ?>
                                            <a class="btn btn-sm btn-info"  href="<?php print_link("clinic_prescription/edit/$rec_id"); ?>">
                                                <i class="fa fa-edit"></i> Edit
                                            </a>
                                            <?php } ?>
                                            <?php if($can_delete){ ?>
                                            <a class="btn btn-sm btn-danger record-delete-btn mx-1"  href="<?php print_link("clinic_prescription/delete/$rec_id/?csrf_token=$csrf_token&redirect=$current_page"); ?>" data-prompt-msg="Are you sure you want to delete this record?" data-display-style="modal">
                                                <i class="fa fa-times"></i> Delete
                                            </a>
                                            <?php } ?>
                                        </div>
                                        <?php
                                        }
                                        else{
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
