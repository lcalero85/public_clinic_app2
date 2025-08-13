<?php
//check if current user role is allowed access to the pages
$can_add = ACL::is_allowed("clinic_patients/add");
$can_edit = ACL::is_allowed("clinic_patients/edit");
$can_view = ACL::is_allowed("clinic_patients/view");
$can_delete = ACL::is_allowed("clinic_patients/delete");
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
<!-- Import Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<!-- Link custom stylesheet and FontAwesome for icons -->
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
                        <h4 class="record-title">Clinic Patients</h4>
                    </div>
                    <div class="col-sm-3 ">
                        <?php if ($can_add) { ?>
                            <a class="btn btn-primary btn-add my-1" href="<?php print_link("clinic_patients/add") ?>">
                                Add New Patients
                            </a>
                        <?php } ?>
                    </div>
                    <div class="col-sm-4 ">
                        <form class="search clinic-search" action="<?php print_link('clinic_patients'); ?>" method="get">
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
                                                <a class="text-decoration-none" href="<?php print_link('clinic_patients'); ?>">
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
                                                <a class="text-decoration-none" href="<?php print_link('clinic_patients'); ?>">
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
                        <div id="clinic_patients-list-records">
                            <div id="page-report-body" class="table-responsive">
                                <table class="table  table-striped table-sm text-left">
                                    <thead class="table-header bg-light">
                                        <tr>
                                            <th class="td-sno">#</th>
                                            <th class="td-full_names">Names</th>
                                            <th class="td-address"> Address</th>
                                            <th class="td-gender"> Age</th>
                                            <th class="td-gender"> DNI</th>
                                            <th class="td-gender"> Gender</th>
                                            <th class="td-phone_patient"> Phone </th>
                                            <th class="td-register_date"> Register</th>
                                            <th class="td-email"> Emails</th>
                                            <th class="td-photo"> Actions</th>
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
                                                $rec_id = (!empty($data['id_patient']) ? urlencode($data['id_patient']) : null);
                                                $counter++;
                                                ?>
                                                <tr>
                                                    <th class="td-sno"><?php echo $counter; ?></th>

                                                    <td class="td-full_names">
                                                        <?php echo $data['full_names']; ?>
                                                    </td>

                                                    <td class="td-address">
                                                        <?php echo $data['address']; ?>
                                                    </td>


                                                    <td class="td-age">
                                                        <?php
                                                        if (!empty($data['birthdate'])) {
                                                            try {
                                                                $birth = new DateTime($data['birthdate']);
                                                                $today = new DateTime();
                                                                echo $birth->diff($today)->y; // Edad en años
                                                            } catch (Exception $e) {
                                                                echo '—';
                                                            }
                                                        } else {
                                                            echo '—';
                                                        }
                                                        ?>
                                                    </td>

                                                    <td class="td-document_number">
                                                        <?php echo $data['document_number']; ?>
                                                    </td>
                                                    <td class="td-gender">
                                                        <?php echo $data['gender']; ?>
                                                    </td>


                                                    <td class="td-phone_patient">
                                                        <?php echo $data['phone_patient']; ?>
                                                    </td>
                                                    <td class="td-register_date">
                                                        <?php echo $data['register_date']; ?>
                                                    </td>
                                                    <td class="td-email">
                                                        <?php echo $data['email']; ?>
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
                                                                        href="<?php print_link("clinic_patients/view/$rec_id"); ?>">
                                                                        <i class="fa fa-eye"></i> View
                                                                    </a>
                                                                <?php } ?>
                                                                <?php if ($can_edit) { ?>
                                                                    <a class="dropdown-item"
                                                                        href="<?php print_link("clinic_patients/edit/$rec_id"); ?>">
                                                                        <i class="fa fa-edit"></i> Edit
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