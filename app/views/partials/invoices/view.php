<?php 
//check if current user role is allowed access to the pages
$can_add = ACL::is_allowed("invoices/add");
$can_edit = ACL::is_allowed("invoices/edit");
$can_view = ACL::is_allowed("invoices/view");
$can_delete = ACL::is_allowed("invoices/delete");
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
                    <h4 class="record-title">View  Invoices</h4>
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
                        $rec_id = (!empty($data['id_invoice']) ? urlencode($data['id_invoice']) : null);
                        $counter++;
                        ?>
                        <div id="page-report-body" class="">
                            <table class="table table-hover table-borderless table-striped">
                                <!-- Table Body Start -->
                                <tbody class="page-data" id="page-data-<?php echo $page_element_id; ?>">
                                    <tr  class="td-invoice_num">
                                        <th class="title"> Invoice Number: </th>
                                        <td class="value">
                                            <span <?php if($can_edit){ ?> data-value="<?php echo $data['invoice_num']; ?>" 
                                                data-pk="<?php echo $data['id_invoice'] ?>" 
                                                data-url="<?php print_link("invoices/editfield/" . urlencode($data['id_invoice'])); ?>" 
                                                data-name="invoice_num" 
                                                data-title="Enter Invoice Num" 
                                                data-placement="left" 
                                                data-toggle="click" 
                                                data-type="text" 
                                                data-mode="popover" 
                                                data-showbuttons="left" 
                                                class="is-editable" <?php } ?>>
                                                <?php echo $data['invoice_num']; ?> 
                                            </span>
                                        </td>
                                    </tr>
                                    <tr  class="td-id_concept">
                                        <th class="title"> Concept: </th>
                                        <td class="value">
                                            <a size="sm" class="btn btn-sm btn-primary page-modal" href="<?php print_link("masterdetail/index/invoices/invoices_concepts/id/" . urlencode($data['id_concept'])) ?>">
                                                <i class="fa fa-eye"></i> <?php echo $data['invoices_concepts_concept'] ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr  class="td-id_patient">
                                        <th class="title">  Patient: </th>
                                        <td class="value">
                                            <a size="sm" class="btn btn-sm btn-primary page-modal" href="<?php print_link("masterdetail/index/invoices/clinic_patients/id_patient/" . urlencode($data['id_patient'])) ?>">
                                                <i class="fa fa-eye"></i> <?php echo $data['clinic_patients_full_names'] ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr  class="td-quantity">
                                        <th class="title"> Quantity: </th>
                                        <td class="value">
                                            <span <?php if($can_edit){ ?> data-max="100" 
                                                data-value="<?php echo $data['quantity']; ?>" 
                                                data-pk="<?php echo $data['id_invoice'] ?>" 
                                                data-url="<?php print_link("invoices/editfield/" . urlencode($data['id_invoice'])); ?>" 
                                                data-name="quantity" 
                                                data-title="Enter Quantity" 
                                                data-placement="left" 
                                                data-toggle="click" 
                                                data-type="number" 
                                                data-mode="popover" 
                                                data-showbuttons="left" 
                                                class="is-editable" <?php } ?>>
                                                <?php echo $data['quantity']; ?> 
                                            </span>
                                        </td>
                                    </tr>
                                    <tr  class="td-price">
                                        <th class="title"> Price: </th>
                                        <td class="value">
                                            <span <?php if($can_edit){ ?> data-max="100" 
                                                data-step="0.1" 
                                                data-value="<?php echo $data['price']; ?>" 
                                                data-pk="<?php echo $data['id_invoice'] ?>" 
                                                data-url="<?php print_link("invoices/editfield/" . urlencode($data['id_invoice'])); ?>" 
                                                data-name="price" 
                                                data-title="Enter Price" 
                                                data-placement="left" 
                                                data-toggle="click" 
                                                data-type="number" 
                                                data-mode="popover" 
                                                data-showbuttons="left" 
                                                class="is-editable" <?php } ?>>
                                                <?php echo $data['price']; ?> 
                                            </span>
                                        </td>
                                    </tr>
                                    <tr  class="td-total_invoice">
                                        <th class="title"> Total : </th>
                                        <td class="value">
                                            <span <?php if($can_edit){ ?> data-max="100" 
                                                data-step="0.1" 
                                                data-value="<?php echo $data['total_invoice']; ?>" 
                                                data-pk="<?php echo $data['id_invoice'] ?>" 
                                                data-url="<?php print_link("invoices/editfield/" . urlencode($data['id_invoice'])); ?>" 
                                                data-name="total_invoice" 
                                                data-title="Enter Total " 
                                                data-placement="left" 
                                                data-toggle="click" 
                                                data-type="number" 
                                                data-mode="popover" 
                                                data-showbuttons="left" 
                                                class="is-editable" <?php } ?>>
                                                <?php echo $data['total_invoice']; ?> 
                                            </span>
                                        </td>
                                    </tr>
                                    <tr  class="td-date_invoice">
                                        <th class="title"> Date : </th>
                                        <td class="value">
                                            <span <?php if($can_edit){ ?> data-flatpickr="{altFormat: 'Y-m-d', enableTime: false, minDate: '', maxDate: ''}" 
                                                data-value="<?php echo $data['date_invoice']; ?>" 
                                                data-pk="<?php echo $data['id_invoice'] ?>" 
                                                data-url="<?php print_link("invoices/editfield/" . urlencode($data['id_invoice'])); ?>" 
                                                data-name="date_invoice" 
                                                data-title="Enter Date " 
                                                data-placement="left" 
                                                data-toggle="click" 
                                                data-type="flatdatetimepicker" 
                                                data-mode="popover" 
                                                data-showbuttons="left" 
                                                class="is-editable" <?php } ?>>
                                                <?php echo $data['date_invoice']; ?> 
                                            </span>
                                        </td>
                                    </tr>
                                    <tr  class="td-id_invoice_status">
                                        <th class="title"> Status: </th>
                                        <td class="value">
                                            <a size="sm" class="btn btn-sm btn-primary page-modal" href="<?php print_link("invoice_status/view/" . urlencode($data['id_invoice_status'])) ?>">
                                                <i class="fa fa-eye"></i> <?php echo $data['invoice_status_status'] ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr  class="td-register_date">
                                        <th class="title"> Register Date: </th>
                                        <td class="value">
                                            <span <?php if($can_edit){ ?> data-value="<?php echo $data['register_date']; ?>" 
                                                data-pk="<?php echo $data['id_invoice'] ?>" 
                                                data-url="<?php print_link("invoices/editfield/" . urlencode($data['id_invoice'])); ?>" 
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
                                            <a class="btn btn-sm btn-info"  href="<?php print_link("invoices/edit/$rec_id"); ?>">
                                                <i class="fa fa-edit"></i> Edit
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
