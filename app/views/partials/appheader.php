<!-- Forzar estado del sidebar por defecto = COLAPSADO -->
<script>
(function () {
  // clave única para recordar el estado
  const KEY = 'sidebar_state';            // 'collapsed' | 'expanded'
  // clases que emplea tu plantilla para el body
  const COLLAPSED_CLASS = 'nav-sm';
  const EXPANDED_CLASS  = 'nav-md';

  // 1) Lee el estado guardado o usa 'collapsed' como default
  let state = localStorage.getItem(KEY) || 'collapsed';

  // 2) Aplica inmediatamente (sin esperar DOMContentLoaded)
  const root = document.body; // o document.documentElement si tu tema lo usa
  root.classList.remove(COLLAPSED_CLASS, EXPANDED_CLASS);
  if (state === 'collapsed') {
    root.classList.add(COLLAPSED_CLASS);
  } else {
    root.classList.add(EXPANDED_CLASS);
  }

  // 3) Si tienes un botón para abrir/cerrar, sincroniza y guarda el estado
  document.addEventListener('DOMContentLoaded', function () {
    // Ajusta el selector al botón real de tu header
    const toggle = document.querySelector('#menu-toggle, .nav-toggle, [data-nav-toggle]');
    if (!toggle) return;

    toggle.addEventListener('click', function () {
      const isCollapsed = root.classList.contains(COLLAPSED_CLASS);
      // alterna clases
      root.classList.toggle(COLLAPSED_CLASS, !isCollapsed);
      root.classList.toggle(EXPANDED_CLASS, isCollapsed);
      // guarda
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
            <?php 
            if(user_login_status() == true ){ 
            ?>
            <button type="button" class="navbar-toggler dropdown-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
            <button type="button" id="sidebarCollapse" class="btn btn-info">
            <span class="navbar-toggler-icon"></span>
            </button>
            </button>
           
            <div class="navbar-collapse collapse navbar-responsive-collapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                         
                           <?php if (!empty(USER_PHOTO)) { ?>
                               <img class="img-fluid rounded-circle" style="height:30px; width:30px;"
                               src="<?php echo SITE_ADDR . 'uploads/files/' . USER_PHOTO; ?>" />
                                 <?php } else { ?>
                                 <span class="avatar-icon"><i class="fa fa-user"></i></span>
                                 <?php } ?>
                                 <span>Hi <?php echo ucwords(USER_NAME); ?> !</span>
                                
                            <ul class="dropdown-menu">
                                <a class="dropdown-item" href="<?php print_link('account') ?>"><i class="fa fa-user"></i> My Account</a>
                                <a class="dropdown-item" href="<?php print_link('index/logout?csrf_token=' . Csrf::$token) ?>"><i class="fa fa-sign-out"></i> Logout</a>
                            </ul>
                        </li>
                    </ul>
                </div>
                <?php 
                } 
                ?>
            </div>
        </div>
        <?php 
        if(user_login_status() == true ){ 
        ?>
        <nav id="sidebar" class="navbar-light bg-info">
            <ul class="nav navbar-nav w-100 flex-column align-self-start">
                <li class="menu-profile text-center nav-item">
                    <a class="avatar" href="<?php print_link('account') ?>">
                        <?php 
                        if(!empty(USER_PHOTO)){
                        ?>
                        <img class="img-fluid" src="<?php print_link(set_img_src(USER_PHOTO,260,200)); ?>" />
                            <?php
                            }
                            else{
                            ?>
                            <span class="avatar-icon"><i class="fa fa-user"></i></span>
                            <?php
                            }
                            ?>
                        </a>
                        <h5 class="user-name">Hi 
                            <?php echo ucwords(USER_NAME); ?>
                            <small class="text-muted"><?php echo ACL::$user_role; ?> </small>
                        </h5>
                        <div class="dropdown menu-dropdown">
                            <button class="btn btn-primary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-user"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <a class="dropdown-item" href="<?php print_link('account') ?>"><i class="fa fa-user"></i> My Account</a>
                                <a class="dropdown-item" href="<?php print_link('index/logout?csrf_token=' . Csrf::$token) ?>"><i class="fa fa-sign-out"></i> Logout</a>
                            </ul>
                        </div>
                    </li>
                </ul>
                <?php Html :: render_menu(Menu :: $navbarsideleft  , "nav navbar-nav w-100 flex-column align-self-start"  , "accordion"); ?>
            </nav>
            <?php 
            } 
            ?>
