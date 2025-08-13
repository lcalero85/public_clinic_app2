<?php
//check if current user role is allowed access to the pages
$can_add = ACL::is_allowed("doc/add");
$can_edit = ACL::is_allowed("doc/edit");
$can_view = ACL::is_allowed("doc/view");
$can_delete = ACL::is_allowed("doc/delete");
?>
<?php
$comp_model = new SharedController;
$page_element_id = "list-page-" . random_str();
$current_page = $this->set_current_page_link();
$csrf_token = Csrf::$token;
//Page Data From Controller
$view_data = $this->view_data;
$records = $view_data->records;
$record_count = $view_data->record_count;
$total_records = $view_data->total_records;
$field_name = $this->route->field_name;
$field_value = $this->route->field_value;
$view_title = $this->view_title;
$show_header = $this->show_header;
$show_footer = $this->show_footer;
$show_pagination = $this->show_pagination;
?>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo SITE_ADDR; ?>/assets/css/custom.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<section class="page" id="<?php echo $page_element_id; ?>" data-page-type="list" data-display-type="table"
    data-page-url="<?php print_link($current_page); ?>">
    <?php
    if ($show_header == true) {
        ?>
        <div class="bg-light p-3 mb-3">
            <div class="container-fluid">
                <div class="row ">
                    <div class="col ">
                        <h4 class="record-title">Doctors</h4>
                    </div>
                    <div class="col-sm-3 ">
                        <?php if ($can_add) { ?>
                            <a class="btn btn-primary btn-add my-1" href="<?php print_link("doc/add") ?>">
                                <i class="fa fa-plus"></i>
                                Add New Doctor
                            </a>
                        <?php } ?>
                    </div>
                    <div class="col-sm-4 ">
                        <form class="search clinic-search" action="<?php print_link('doc'); ?>" method="get">
                            <div class="input-group">
                                <input value="<?php echo get_value('search'); ?>" class="form-control" type="text"
                                    name="search" placeholder="Search" />
                                <div class="input-group-append">
                                    <button class="btn btn-primary"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-12 comp-grid">
                        <div class="">
                            <!-- Page bread crumbs components-->
                            <?php
                            if (!empty($field_name) || !empty($_GET['search'])) {
                                ?>
                                <hr class="sm d-block d-sm-none" />
                                <nav class="page-header-breadcrumbs mt-2" aria-label="breadcrumb">
                                    <ul class="breadcrumb m-0 p-1">
                                        <?php
                                        if (!empty($field_name)) {
                                            ?>
                                            <li class="breadcrumb-item">
                                                <a class="text-decoration-none" href="<?php print_link('doc'); ?>">
                                                    <i class="fa fa-angle-left"></i>
                                                </a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <?php echo (get_value("tag") ? get_value("tag") : make_readable($field_name)); ?>
                                            </li>
                                            <li class="breadcrumb-item active text-capitalize font-weight-bold">
                                                <?php echo (get_value("label") ? get_value("label") : make_readable(urldecode($field_value))); ?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if (get_value("search")) {
                                            ?>
                                            <li class="breadcrumb-item">
                                                <a class="text-decoration-none" href="<?php print_link('doc'); ?>">
                                                    <i class="fa fa-angle-left"></i>
                                                </a>
                                            </li>
                                            <li class="breadcrumb-item text-capitalize">
                                                Search
                                            </li>
                                            <li class="breadcrumb-item active text-capitalize font-weight-bold">
                                                <?php echo get_value("search"); ?>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                    </ul>
                                </nav>
                                <!--End of Page bread crumbs components-->
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    <div class="">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-md-12 comp-grid">
                    <?php $this::display_page_errors(); ?>
                    <div class=" animated fadeIn page-content">
                        <div id="doc-list-records">
                            <div id="page-report-body" class="table-responsive">
                                <table class="table  table-striped table-sm text-left">
                                    <thead class="table-header bg-light">
                                        <tr>
                                            <?php if ($can_delete) { ?>
                                                <th class="td-checkbox">
                                                    <label class="custom-control custom-checkbox custom-control-inline">
                                                        <input class="toggle-check-all custom-control-input"
                                                            type="checkbox" />
                                                        <span class="custom-control-label"></span>
                                                    </label>
                                                </th>
                                            <?php } ?>
                                            <th class="td-sno">#</th>
                                            <th class="td-full_names"> Names</th>
                                            <th class="td-address"> Address</th>
                                            <th class="td-register_date"># DNI Number</th>
                                            <th class="td-register_date"># License Number</th>
                                            <th class="td-birthdate"> Age</th>
                                            <th class="td-Speciality"> Speciality</th>
                                            <th class="td-register_date"># Contact Number</th>
                                            <th class="td-register_date">Status</th>
                                            <th class="td-btn"></th>
                                        </tr>
                                    </thead>
                                    <?php
                                    if (!empty($records)) {
                                        ?>
                                        <tbody class="page-data" id="page-data-<?php echo $page_element_id; ?>">
                                            <!--record-->
                                            <?php
                                            $counter = 0;
                                            foreach ($records as $data) {
                                                $rec_id = (!empty($data['id']) ? urlencode($data['id']) : null);
                                                $counter++;
                                                ?>
                                                <tr>
                                                    <?php if ($can_delete) { ?>
                                                        <th class=" td-checkbox">
                                                            <label class="custom-control custom-checkbox custom-control-inline">
                                                                <input class="optioncheck custom-control-input" name="optioncheck[]"
                                                                    value="<?php echo $data['id'] ?>" type="checkbox" />
                                                                <span class="custom-control-label"></span>
                                                            </label>
                                                        </th>
                                                    <?php } ?>
                                                    <th class="td-sno"><?php echo $counter; ?></th>

                                                    <td class="td-full_names">
                                                        <span>
                                                            <?php echo $data['full_names']; ?>
                                                        </span>
                                                    </td>
                                                    <td class="td-full_names">
                                                        <span>
                                                            <?php echo $data['address']; ?>
                                                        </span>
                                                    </td>
                                                     <td class="td-full_names">
                                                        <span>
                                                            <?php echo $data['dni']; ?>
                                                        </span>
                                                    </td>

                                                     <td class="td-full_names">
                                                        <span>
                                                            <?php echo $data['license_number']; ?>
                                                        </span>
                                                    </td>

                                                    <td class="td-full_names">
                                                        <span>
                                                            <?php
                                                            if (!empty($data['birthdate']) && $data['birthdate'] != '0000-00-00') {
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
                                                      <td class="td-full_names">
                                                        <span>
                                                            <?php echo $data['Speciality']; ?>
                                                        </span>
                                                    </td>
                                                     </td>
                                                      <td class="td-full_names">
                                                        <span>
                                                            <?php echo $data['office_phone']; ?>
                                                        </span>
                                                    </td>
                                                     <td class="td-status">
                                                        <span>
                                                            <?php echo $data['status']; ?>
                                                        </span>
                                                    </td>

                                                    <td class="page-list-action td-btn">
                                                        <div class="dropdown">
                                                            <button data-toggle="dropdown"
                                                                class="dropdown-toggle btn btn-primary btn-sm">
                                                                <i class="fa fa-bars"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <?php if ($can_view) { ?>
                                                                    <a class="dropdown-item"
                                                                        href="<?php print_link("doc/view/$rec_id"); ?>">
                                                                        <i class="fa fa-eye"></i> View
                                                                    </a>
                                                                <?php } ?>
                                                                <?php if ($can_edit) { ?>
                                                                    <a class="dropdown-item"
                                                                        href="<?php print_link("doc/edit/$rec_id"); ?>">
                                                                        <i class="fa fa-edit"></i> Edit
                                                                    </a>
                                                                <?php } ?>
                                                                <?php if ($can_delete) { ?>
                                                                    <a class="dropdown-item record-delete-btn"
                                                                        href="<?php print_link("doc/delete/$rec_id/?csrf_token=$csrf_token&redirect=$current_page"); ?>"
                                                                        data-prompt-msg="Are you sure you want to delete this record?"
                                                                        data-display-style="modal">
                                                                        <i class="fa fa-times"></i> Delete
                                                                    </a>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <!--endrecord-->
                                        </tbody>
                                        <tbody class="search-data" id="search-data-<?php echo $page_element_id; ?>"></tbody>
                                        <?php
                                    }
                                    ?>
                                </table>
                                <?php
                                if (empty($records)) {
                                    ?>
                                    <h4 class="bg-light text-center border-top text-muted animated bounce  p-3">
                                        <i class="fa fa-ban"></i> No record found
                                    </h4>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                            if ($show_footer && !empty($records)) {
                                ?>
                                <div class=" border-top mt-2">
                                    <div class="row justify-content-center">
                                        <div class="col-md-auto justify-content-center">
                                            <div class="p-3 d-flex justify-content-between">
                                                <?php if ($can_delete) { ?>
                                                    <button data-prompt-msg="Are you sure you want to delete these records?"
                                                        data-display-style="modal"
                                                        data-url="<?php print_link("doc/delete/{sel_ids}/?csrf_token=$csrf_token&redirect=$current_page"); ?>"
                                                        class="btn btn-sm btn-danger btn-delete-selected d-none">
                                                        <i class="fa fa-times"></i> Delete Selected
                                                    </button>
                                                <?php } ?>
                                                <div class="dropup export-btn-holder mx-1">
                                                    
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <?php $export_print_link = $this->set_current_page_link(array('format' => 'print')); ?>
                                                        <a class="dropdown-item export-link-btn" data-format="print"
                                                            href="<?php print_link($export_print_link); ?>" target="_blank">
                                                            <img src="<?php print_link('assets/images/print.png') ?>"
                                                                class="mr-2" /> PRINT
                                                        </a>
                                                        <?php $export_pdf_link = $this->set_current_page_link(array('format' => 'pdf')); ?>
                                                        <a class="dropdown-item export-link-btn" data-format="pdf"
                                                            href="<?php print_link($export_pdf_link); ?>" target="_blank">
                                                            <img src="<?php print_link('assets/images/pdf.png') ?>"
                                                                class="mr-2" /> PDF
                                                        </a>
                                                        <?php $export_csv_link = $this->set_current_page_link(array('format' => 'csv')); ?>
                                                        <a class="dropdown-item export-link-btn" data-format="csv"
                                                            href="<?php print_link($export_csv_link); ?>" target="_blank">
                                                            <img src="<?php print_link('assets/images/csv.png') ?>"
                                                                class="mr-2" /> CSV
                                                        </a>
                                                        <?php $export_excel_link = $this->set_current_page_link(array('format' => 'excel')); ?>
                                                        <a class="dropdown-item export-link-btn" data-format="excel"
                                                            href="<?php print_link($export_excel_link); ?>" target="_blank">
                                                            <img src="<?php print_link('assets/images/xsl.png') ?>"
                                                                class="mr-2" /> EXCEL
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <?php
                                            if ($show_pagination == true) {
                                                $pager = new Pagination($total_records, $record_count);
                                                $pager->route = $this->route;
                                                $pager->show_page_count = true;
                                                $pager->show_record_count = true;
                                                $pager->show_page_limit = true;
                                                $pager->limit_count = $this->limit_count;
                                                $pager->show_page_number_list = true;
                                                $pager->pager_link_range = 5;
                                                $pager->render();
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>