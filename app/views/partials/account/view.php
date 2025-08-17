<?php
//check if current user role is allowed access to the pages
$can_add = ACL::is_allowed("users/add");
$can_edit = ACL::is_allowed("users/edit");
$can_view = ACL::is_allowed("users/view");
$can_delete = ACL::is_allowed("users/delete");
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
                        <h4 class="record-title">My User Account</h4>
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
                            $rec_id = (!empty($data['id_user']) ? urlencode($data['id_user']) : null);
                            $counter++;
                        ?>
                            <div class="profile-card m-2 mb-4">
                                <div class="profile text-center">
                                    <div class="avatar">
                                        <?php $userPhotoSrc = get_user_photo_src(USER_IMAGE); ?>
                                        <img class="user-photo"
                                            src="<?php echo $userPhotoSrc ? $userPhotoSrc : 'assets/images/no-image-available.png'; ?>"
                                            alt="User Photo"
                                            onerror="this.onerror=null; this.src='assets/images/no-image-available.png';" />
                                    </div>
                                    <h1 class="profile-name mt-3"><?php echo $data['user_name']; ?></h1>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mx-3 mb-3">
                                        <ul class="nav nav-pills flex-column text-left">
                                            <li class="nav-item">
                                                <a data-toggle="tab" href="#AccountPageView" class="nav-link active">
                                                    <i class="fa fa-user"></i> Account Detail
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a data-toggle="tab" href="#AccountPageEdit" class="nav-link">
                                                    <i class="fa fa-edit"></i> Edit Account
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a data-toggle="tab" href="#AccountPageChangeEmail" class="nav-link">
                                                    <i class="fa fa-envelope"></i> Change Email
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a data-toggle="tab" href="#AccountPageChangePassword" class="nav-link">
                                                    <i class="fa fa-key"></i> Reset Password
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-sm-9">
                                    <div class="mb-3">
                                        <div class="tab-content">
                                            <div class="tab-pane show active fade" id="AccountPageView" role="tabpanel">
                                                <table class="table table-hover table-borderless table-striped patient-view">
                                                    <tbody class="page-data" id="page-data-<?php echo $page_element_id; ?>">
                                                        <tr class="td-full_names">
                                                            <th class="title">Full Name</th>
                                                            <td class="value">
                                                                <?php echo htmlspecialchars($data['full_names']); ?>
                                                            </td>
                                                        </tr>
                                                        <tr class="td-rol">
                                                            <th class="title">Role</th>
                                                            <td class="value">
                                                                <?php echo USER_ROLE_NAME; ?>
                                                            </td>
                                                        </tr>
                                                        <tr class="td-user_name">
                                                            <th class="title">Username</th>
                                                            <td class="value">
                                                                <?php echo htmlspecialchars($data['user_name']); ?>
                                                            </td>
                                                        </tr>
                                                        <tr class="td-email">
                                                            <th class="title">Email Address</th>
                                                            <td class="value">
                                                                <?php echo htmlspecialchars($data['email']); ?>
                                                            </td>
                                                        </tr>

                                                        <tr class="td-register_date">
                                                            <th class="title">Registration Date</th>
                                                            <td class="value">
                                                                <?php echo date("F j, Y, g:i A", strtotime($data['register_date'])); ?>
                                                            </td>
                                                        </tr>

                                                        <tr class="td-update_date">
                                                            <th class="title">Last Update</th>
                                                            <td class="value">
                                                                <?php echo date("F j, Y, g:i A", strtotime($data['update_date'])); ?>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="tab-pane fade" id="AccountPageEdit" role="tabpanel">
                                                <div class=" reset-grids">
                                                    <?php $this->render_page("account/edit"); ?>
                                                </div>
                                            </div>
                                            <div class="tab-pane  fade" id="AccountPageChangeEmail" role="tabpanel">
                                                <div class=" reset-grids">
                                                    <?php $this->render_page("account/change_email"); ?>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="AccountPageChangePassword" role="tabpanel">
                                                <div class=" reset-grids">
                                                    <?php $this->render_page("passwordmanager"); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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