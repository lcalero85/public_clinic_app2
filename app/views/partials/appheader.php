<?php
function get_user_photo_src($photoBlob)
{
    if (!empty($photoBlob)) {
        // Ya viene con el encabezado listo para usar
        return $photoBlob;
    }
    return null;
}
?>

<!-- Forzar estado del sidebar por defecto = COLAPSADO -->
<script>
    (function() {
        const KEY = 'sidebar_state';
        const COLLAPSED_CLASS = 'nav-sm';
        const EXPANDED_CLASS = 'nav-md';
        let state = localStorage.getItem(KEY) || 'collapsed';
        const root = document.body;
        root.classList.remove(COLLAPSED_CLASS, EXPANDED_CLASS);
        root.classList.add(state === 'collapsed' ? COLLAPSED_CLASS : EXPANDED_CLASS);

        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.querySelector('#menu-toggle, .nav-toggle, [data-nav-toggle]');
            if (!toggle) return;
            toggle.addEventListener('click', function() {
                const isCollapsed = root.classList.contains(COLLAPSED_CLASS);
                root.classList.toggle(COLLAPSED_CLASS, !isCollapsed);
                root.classList.toggle(EXPANDED_CLASS, isCollapsed);
                localStorage.setItem(KEY, !isCollapsed ? 'collapsed' : 'expanded');
            });
        });
    })();
</script>

<div id="topbar" class="navbar navbar-expand-sm fixed-top navbar-light bg-info"> 
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php print_link(HOME_PAGE) ?>">
            <img class="img-responsive" src="<?php print_link(SITE_LOGO); ?>" /> <?php echo SITE_NAME ?>
        </a>

        <?php if (user_login_status() == true) { ?>
            <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                <button type="button" id="sidebarCollapse" class="btn btn-info">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </button>

            <div class="navbar-collapse collapse navbar-responsive-collapse">
                <ul class="navbar-nav ml-auto align-items-center">

                    <!-- 🔔 Badge de Notificaciones -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="#" data-toggle="modal" data-target="#notificationsModal">
                            <i class="fa fa-bell fa-lg text-white"></i>
                            <span id="notif-count" class="badge badge-danger position-absolute" style="top:0; right:0; font-size:0.7rem;">
                                3
                            </span>
                        </a>
                    </li>

                    <!-- 👤 Menú usuario -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                            <?php
                            $userPhotoSrc = get_user_photo_src(USER_IMAGE);
                            ?>
                            <img class="user-photo rounded-circle"
                                src="<?php echo $userPhotoSrc ? $userPhotoSrc : 'assets/images/no-image-available.png'; ?>"
                                alt="User Photo"
                                style="width:40px; height:40px; object-fit:cover; border-radius:50%;"
                                onerror="this.onerror=null; this.src='assets/images/no-image-available.png';" />
                            <span class="text-white">Hi <?php echo ucwords(USER_NAME); ?> !</span>
                        </a>
                        <ul class="dropdown-menu custom-dropdown shadow-lg">
                            <a class="dropdown-item" href="<?php print_link('account') ?>">
                                <i class="fa fa-user-circle"></i> My Account
                            </a>
                            <a class="dropdown-item text-danger" href="<?php print_link('index/logout?csrf_token=' . Csrf::$token) ?>">
                                <i class="fa fa-power-off"></i> Logout
                            </a>
                        </ul>
                    </li>
                </ul>
            </div>
        <?php } ?>
    </div>
</div>

<!-- 📌 Modal de Notificaciones -->
<div class="modal fade" id="notificationsModal" tabindex="-1" role="dialog" aria-labelledby="notificationsLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-scrollable modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="notificationsLabel"><i class="fa fa-bell"></i> Notifications</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Ejemplo de notificaciones -->
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><i class="fa fa-user-plus text-info"></i> New patient registered</li>
          <li class="list-group-item"><i class="fa fa-calendar text-success"></i> Appointment scheduled</li>
          <li class="list-group-item"><i class="fa fa-file-invoice text-warning"></i> New invoice created</li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-info btn-sm" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php if (user_login_status() == true) { ?>
    <nav id="sidebar" class="navbar-light bg-info">
        <ul class="nav navbar-nav w-100 flex-column align-self-start">
            <li class="menu-profile text-center nav-item">
                <a class="avatar" href="<?php print_link('account') ?>">
                    <?php $userPhotoSrc = get_user_photo_src(USER_IMAGE); ?>
                    <?php //var_dump(USER_IMAGE)
                    ?>
                    <img class="user-photo rounded-circle"
                        src="<?php echo $userPhotoSrc ? $userPhotoSrc : 'assets/images/no-image-available.png'; ?>"
                        alt="User Photo"
                        style="width:80px; height:80px; object-fit:cover; border-radius:50%;"
                        onerror="this.onerror=null; this.src='assets/images/no-image-available.png';" />
                </a>
                <h5 class="user-name">Hi <?php echo ucwords(USER_NAME); ?>
                    <small class="text-muted"><?php echo USER_ROLE_NAME; ?></small>
                </h5>
                <div class="dropdown menu-dropdown">
                    <button class="btn btn-primary dropdown-toggle btn-sm" type="button" data-toggle="dropdown">
                        <i class="fa fa-user"></i>
                    </button>
                    <ul class="dropdown-menu custom-dropdown shadow-lg">
                        <a class="dropdown-item" href="<?php print_link('account') ?>">
                            <i class="fa fa-user-circle"></i> My Account
                        </a>
                        <a class="dropdown-item text-danger" href="<?php print_link('index/logout?csrf_token=' . Csrf::$token) ?>">
                            <i class="fa fa-power-off"></i> Logout
                        </a>
                    </ul>
                </div>
            </li>
        </ul>
        <?php Html::render_menu(Menu::$navbarsideleft, "nav navbar-nav w-100 flex-column align-self-start", "accordion"); ?>
    </nav>
<?php } ?>

<style>
    /* Estilo general del menú */
    .custom-dropdown {
        background: #ffffff;
        border-radius: 12px;
        padding: 8px 0;
        border: none;
        min-width: 180px;
    }

    /* Estilo de los items */
    .custom-dropdown .dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        font-size: 14px;
        color: #333;
        padding: 10px 16px;
        border-radius: 8px;
        transition: all 0.25s ease-in-out;
    }

    /* Hover con efecto */
    .custom-dropdown .dropdown-item:hover {
        background: #006680;
        color: #fff;
        transform: translateX(4px);
    }

    /* Iconos */
    .custom-dropdown .dropdown-item i {
        font-size: 16px;
        color: #006680;
        transition: all 0.25s ease;
    }

    /* Cambiar color de icono al hover */
    .custom-dropdown .dropdown-item:hover i {
        color: #fff;
    }

    /* Botón de logout más destacado */
    .custom-dropdown .dropdown-item.text-danger {
        color: #3a0107ff;
    }

    .custom-dropdown .dropdown-item.text-danger:hover {
        background: #c82333;
        color: #030303ff;
    }
</style>