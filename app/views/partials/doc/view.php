<?php
//check if current user role is allowed access to the pages
$can_add = ACL::is_allowed("doc/add");
$can_edit = ACL::is_allowed("doc/edit");
$can_view = ACL::is_allowed("doc/view");
$can_delete = ACL::is_allowed("doc/delete");
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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="view" data-display-type="table"
    data-page-url="<?php print_link($current_page); ?>">
    <?php
    if ($show_header == true) {
        ?>
        <div class="bg-light p-3 mb-3">
            <div class="container">
                <div class="row ">
                    <div class="col ">
                        <h4 class="record-title">View Doctors</h4>
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
                            $rec_id = (!empty($data['id']) ? urlencode($data['id']) : null);
                            $counter++;
                            ?>
                            <div id="page-report-body" class="">
                                <table class="table table-hover table-borderless table-striped patient-view">
                                    <!-- Table Body Start -->

                                    <tbody class="page-data" id="page-data-<?php echo $page_element_id; ?>">
                                        <tr class="td-full_names">
                                            <th class="title"> Full Names: </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['full_names']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="td-full_names">
                                            <th class="title"> Speciality: </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['Speciality']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="td-age">
                                            <th class="title"> Age: </th>
                                            <td class="value">
                                                <span>
                                                    <?php
                                                    if (!empty($data['birthdate'])) {
                                                        $birthDate = new DateTime($data['birthdate']);
                                                        $today = new DateTime();
                                                        $age = $today->diff($birthDate)->y;
                                                        echo $age . " years";
                                                    } else {
                                                        echo "N/A";
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>

                                        </tr>
                                        <tr class="td-full_names">
                                            <th class="title"> Address: </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['address']; ?>
                                                </span>
                                            </td>
                                        </tr>

                                        </tr>
                                        <tr class="td-full_names">
                                            <th class="title"> # Licenser Number: </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['license_number']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <tr class="td-status">
                                            <th class="title"> Doctors Status: </th>
                                            <td class="value">
                                                <span><?php echo !empty($data['status']) ? $data['status'] : 'N/A'; ?></span>
                                            </td>
                                        </tr>
                                        <tr class="td-university">
                                            <th class="title"> University: </th>
                                            <td class="value">
                                                <span><?php echo !empty($data['university']) ? $data['university'] : 'N/A'; ?></span>
                                            </td>
                                        </tr>

                                        <tr class="td-office_phone">
                                            <th class="title"> Office Phone: </th>
                                            <td class="value">
                                                <span>
                                                    <?php
                                                    if (!empty($data['office_phone'])) {
                                                        $telHref = preg_replace('/\D+/', '', $data['office_phone']); // sólo dígitos para el link
                                                        echo '<a href="tel:' . $telHref . '">' . $data['office_phone'] . '</a>';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>

                                        <tr class="td-work_email">
                                            <th class="title"> Work Email: </th>
                                            <td class="value">
                                                <span>
                                                    <?php
                                                    if (!empty($data['work_email'])) {
                                                        echo '<a href="mailto:' . $data['work_email'] . '">' . $data['work_email'] . '</a>';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>

                                        <tr class="td-years_experience">
                                            <th class="title"> Years Experience: </th>
                                            <td class="value">
                                                <span>
                                                    <?php
                                                    if ($data['years_experience'] !== '' && $data['years_experience'] !== null) {
                                                        echo (int) $data['years_experience'] . ' years';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>

                                        <tr class="td-dni">
                                            <th class="title"> DNI: </th>
                                            <td class="value">
                                                <span><?php echo !empty($data['dni']) ? $data['dni'] : 'N/A'; ?></span>
                                            </td>
                                        </tr>

                                        </tr>
                                        <tr class="td-full_names">
                                            <th class="title"> Register Date: </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['register_date']; ?>
                                                </span>
                                            </td>
                                        </tr>

                                        </tr>
                                        <tr class="td-full_names">
                                            <th class="title"> Last Update Date: </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['update_date']; ?>
                                                </span>
                                            </td>
                                        </tr>

                                        </tr>
                                        <tr class="td-full_names">
                                            <th class="title"> Register By : </th>
                                            <td class="value">
                                                <span>
                                                    <?php echo $data['users_full_names']; ?>
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
                                        <a class="dropdown-item export-link-btn" data-format="print"
                                            href="<?php print_link($export_print_link); ?>" target="_blank">
                                            <img src="<?php print_link('assets/images/print.png') ?>" class="mr-2" /> PRINT
                                        </a>
                                        <?php $export_pdf_link = $this->set_current_page_link(array('format' => 'pdf')); ?>
                                        <a class="dropdown-item export-link-btn" data-format="pdf"
                                            href="<?php print_link($export_pdf_link); ?>" target="_blank">
                                            <img src="<?php print_link('assets/images/pdf.png') ?>" class="mr-2" /> PDF
                                        </a>
                                        <?php $export_csv_link = $this->set_current_page_link(array('format' => 'csv')); ?>
                                        <a class="dropdown-item export-link-btn" data-format="csv"
                                            href="<?php print_link($export_csv_link); ?>" target="_blank">
                                            <img src="<?php print_link('assets/images/csv.png') ?>" class="mr-2" /> CSV
                                        </a>
                                        <?php $export_excel_link = $this->set_current_page_link(array('format' => 'excel')); ?>
                                        <a class="dropdown-item export-link-btn" data-format="excel"
                                            href="<?php print_link($export_excel_link); ?>" target="_blank">
                                            <img src="<?php print_link('assets/images/xsl.png') ?>" class="mr-2" /> EXCEL
                                        </a>
                                    </div>
                                </div>
                                <?php if ($can_edit) { ?>
                                    <a class="btn btn-sm btn-info" href="<?php print_link("doc/edit/$rec_id"); ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                <?php } ?>
                                <?php if ($can_delete) { ?>
                                    <a class="btn btn-sm btn-danger record-delete-btn mx-1"
                                        href="<?php print_link("doc/delete/$rec_id/?csrf_token=$csrf_token&redirect=$current_page"); ?>"
                                        data-prompt-msg="Are you sure you want to delete this record?"
                                        data-display-style="modal">
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