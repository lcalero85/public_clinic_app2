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
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                            <?php

                            $userPhotoSrc = get_user_photo_src(USER_IMAGE);
                            ?>
                            <img class="user-photo rounded-circle"
                                src="<?php echo $userPhotoSrc ? $userPhotoSrc : 'assets/images/no-image-available.png'; ?>"
                                alt="User Photo"
                                style="width:80px; height:80px; object-fit:cover; border-radius:50%;"
                                onerror="this.onerror=null; this.src='assets/images/no-image-available.png';" />
                            <?php //var_dump(USER_IMAGE)
                            ?>
                            <span>Hi <?php echo ucwords(USER_NAME); ?> !</span>
                        </a>
                        <ul class="dropdown-menu">
                            <a class="dropdown-item" href="<?php print_link('account') ?>"><i class="fa fa-user"></i> My Account</a>
                            <a class="dropdown-item" href="<?php print_link('index/logout?csrf_token=' . Csrf::$token) ?>"><i class="fa fa-sign-out"></i> Logout</a>
                        </ul>
                    </li>
                </ul>
            </div>
        <?php } ?>
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
                    <ul class="dropdown-menu">
                        <a class="dropdown-item" href="<?php print_link('account') ?>"><i class="fa fa-user"></i> My Account</a>
                        <a class="dropdown-item" href="<?php print_link('index/logout?csrf_token=' . Csrf::$token) ?>"><i class="fa fa-sign-out"></i> Logout</a>
                    </ul>
                </div>
            </li>
        </ul>
        <?php Html::render_menu(Menu::$navbarsideleft, "nav navbar-nav w-100 flex-column align-self-start", "accordion"); ?>
    </nav>
<?php } ?>