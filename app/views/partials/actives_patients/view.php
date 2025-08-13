<?php 
//check if current user role is allowed access to the pages
$can_add = ACL::is_allowed("actives_patients/add");
$can_edit = ACL::is_allowed("actives_patients/edit");
$can_view = ACL::is_allowed("actives_patients/view");
$can_delete = ACL::is_allowed("actives_patients/delete");
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
                    <h4 class="record-title">View  Actives Patients</h4>
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
                        $rec_id = (!empty($data['']) ? urlencode($data['']) : null);
                        $counter++;
                        ?>
                        <div id="page-report-body" class="">
                            <table class="table table-hover table-borderless table-striped">
                                <!-- Table Body Start -->
                                <tbody class="page-data" id="page-data-<?php echo $page_element_id; ?>">
                                    <tr  class="td-id_patient">
                                        <th class="title"> Id: </th>
                                        <td class="value"> <?php echo $data['id_patient']; ?></td>
                                    </tr>
                                    <tr  class="td-full_names">
                                        <th class="title"> Names: </th>
                                        <td class="value">
                                            <a size="sm" class="btn btn-sm btn-primary page-modal" href="<?php print_link("doc/view/" . urlencode($data['id_doc'])) ?>">
                                                <i class="fa fa-eye"></i> <?php echo $data['doc_full_names'] ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr  class="td-address">
                                        <th class="title"> Address: </th>
                                        <td class="value"> <?php echo $data['address']; ?></td>
                                    </tr>
                                    <tr  class="td-gender">
                                        <th class="title"> Gender: </th>
                                        <td class="value"> <?php echo $data['gender']; ?></td>
                                    </tr>
                                    <tr  class="td-age">
                                        <th class="title"> Age: </th>
                                        <td class="value"> <?php echo $data['age']; ?></td>
                                    </tr>
                                    <tr  class="td-birthdate">
                                        <th class="title"> Birthdate: </th>
                                        <td class="value"> <?php echo $data['birthdate']; ?></td>
                                    </tr>
                                    <tr  class="td-register_observations">
                                        <th class="title"> Observations: </th>
                                        <td class="value"> <?php echo $data['register_observations']; ?></td>
                                    </tr>
                                    <tr  class="td-referred">
                                        <th class="title"> Referred: </th>
                                        <td class="value"> <?php echo $data['referred']; ?></td>
                                    </tr>
                                    <tr  class="td-phone_patient">
                                        <th class="title"> Phone : </th>
                                        <td class="value"> <?php echo $data['phone_patient']; ?></td>
                                    </tr>
                                    <tr  class="td-manager">
                                        <th class="title"> Manager: </th>
                                        <td class="value"> <?php echo $data['manager']; ?></td>
                                    </tr>
                                    <tr  class="td-diseases">
                                        <th class="title"> Diseases: </th>
                                        <td class="value"> <?php echo $data['diseases']; ?></td>
                                    </tr>
                                    <tr  class="td-register_date">
                                        <th class="title"> Register : </th>
                                        <td class="value"> <?php echo $data['register_date']; ?></td>
                                    </tr>
                                    <tr  class="td-id_user">
                                        <th class="title"> Register User: </th>
                                        <td class="value">
                                            <a size="sm" class="btn btn-sm btn-primary page-modal" href="<?php print_link("masterdetail/index/actives_patients/users/id_user/" . urlencode($data['id_user'])) ?>">
                                                <i class="fa fa-eye"></i> <?php echo $data['users_full_names'] ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr  class="td-id_status">
                                        <th class="title"> Status: </th>
                                        <td class="value">
                                            <a size="sm" class="btn btn-sm btn-primary page-modal" href="<?php print_link("masterdetail/index/actives_patients/patients_status/id/" . urlencode($data['id_status'])) ?>">
                                                <i class="fa fa-eye"></i> <?php echo $data['patients_status_status'] ?>
                                            </a>
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
                                            <?php $export_word_link = $this->set_current_page_link(array('format' => 'word')); ?>
                                            <a class="dropdown-item export-link-btn" data-format="word" href="<?php print_link($export_word_link); ?>" target="_blank">
                                                <img src="<?php print_link('assets/images/doc.png') ?>" class="mr-2" /> WORD
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
