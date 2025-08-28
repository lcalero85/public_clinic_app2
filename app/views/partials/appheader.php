<?php
require_once __DIR__ . "/../../../config.php";

function get_user_photo_src($photoBlob)
{
    if (!empty($photoBlob)) {
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
            <div class="navbar-collapse collapse navbar-responsive-collapse">
            <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
            <button type="button" id="sidebarCollapse" class="btn btn-info">
            <span class="navbar-toggler-icon"></span>
            </button>
            </button>
                <ul class="navbar-nav ml-auto align-items-center">
                    <!-- 🔔 Notificaciones -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="#" data-toggle="modal" data-target="#notificationsModal">
                            <i class="fa fa-bell fa-lg text-white"></i>
                            <span id="notif-count"
                                class="badge position-absolute badge-secondary"
                                style="top:0; right:0; font-size:0.7rem;">
                                0
                            </span>
                        </a>
                    </li>
                    <!-- 👤 Menú Usuario -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                            <?php $userPhotoSrc = get_user_photo_src(USER_IMAGE); ?>
                            <img class="user-photo rounded-circle"
                                src="<?php echo $userPhotoSrc ? $userPhotoSrc : 'assets/images/no-image-available.png'; ?>"
                                alt="User Photo"
                                style="width:40px; height:40px; object-fit:cover; border-radius:50%;" />
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
        <div class="modal-content border-0 shadow-lg" style="border-radius:15px; overflow:hidden;">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title font-weight-bold" id="notificationsLabel">
                    <i class="fa fa-bell"></i> Notifications
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background:#e6f7fa;">
                <ul class="list-group list-group-flush" id="notificationsList">
                    <li class="list-group-item text-center text-muted">Loading...</li>
                </ul>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-info btn-sm px-4" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- 🎨 Estilos -->
<style>
    .list-group-item {
        font-size: 0.9rem;
        border: none;
        border-bottom: 1px solid #e0e0e0;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    .list-group-item strong {
        color: #006680;
    }

    .list-group-item .text-muted {
        font-size: 0.75rem;
    }
</style>
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
<?php if(user_login_status()): ?>
<script>
$(document).ready(function(){

    // 🔄 Función para refrescar contador de notificaciones
    function refreshNotificationCount() {
        $.ajax({
            url: "<?php echo SITE_ADDR; ?>notifications/unread_count",
            method: "GET",
            dataType: "json",
            success: function(data){
                let count = data.count || 0;

                // Actualizar badge
                $("#notif-count").text(count);

                if(count > 0){
                    $("#notif-count")
                        .removeClass("badge-secondary")
                        .addClass("badge-danger");
                } else {
                    $("#notif-count")
                        .removeClass("badge-danger")
                        .addClass("badge-secondary");
                }
            },
            error: function(xhr){
                console.error("⚠️ Error cargando contador:", xhr.responseText);
            }
        });
    }

    // ▶️ Al abrir el modal de notificaciones
    $('#notificationsModal').on('shown.bs.modal', function () {
        $.post('<?php echo SITE_ADDR; ?>notifications/mark_all', {
            csrf_token: "<?php echo Csrf::$token; ?>"
        }, function(){
            // Resetear badge a 0
            $("#notif-count").text("0")
                .removeClass("badge-danger")
                .addClass("badge-secondary");

            // 🔄 Recargar listado de notificaciones (👉 usar get_all)
            $.get('<?php echo SITE_ADDR; ?>notifications/get_all', function(data){
                let html = "";

                if (data.length > 0) {
                    data.forEach(function(n){
                        html += `
                            <li class="list-group-item d-flex flex-column" style="font-size:0.85rem;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>
                                        <i class="fa ${n.is_read == 1 ? 'fa-envelope-open text-secondary' : 'fa-envelope text-info'} mr-2"></i>
                                        <strong>${n.title}</strong>
                                    </span>
                                    <small class="text-muted">${n.created_at}</small>
                                </div>
                                <div class="mt-1 text-muted">${n.message}</div>
                            </li>
                        `;
                    });
                } else {
                    html = `<li class="list-group-item text-center text-muted">No notifications yet</li>`;
                }

                $("#notificationsList").html(html);
            }, 'json');
        }, 'json');
    });

    // ▶️ Llamada inicial al cargar página
    refreshNotificationCount();

    // ⏱ Refrescar cada 30 segundos
    setInterval(refreshNotificationCount, 30000);
});
</script>
<?php endif; ?>

