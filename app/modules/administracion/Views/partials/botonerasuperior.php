<!-- <div class="infousuario">
	<span ><i class="fas fa-user-tie" aria-hidden="true"></i> Bienvenido(a): <?php echo $_SESSION['kt_login_name']; ?></span>
	<a href="/administracion/loginuser/logout" class="enlace-salir">Salir <i class="fas fa-sign-out-alt"></i></a></i>
</div> -->

<div class="header-bx">
  <div class="header-bx-left">
    <!-- <div class="nav-brand">
    </div> -->
    <div class="menu-toggler">
      <i class="fas fa-bars"></i>
    </div>
  </div>
  <div class="header-bx-center">
    <img src="/skins/administracion/images/logo-new.png" alt="">
  </div>
  <div class="header-bx-right">
    <div class="user-info-wrapper">
      <i class="fa-solid fa-user"></i>
      <strong>Bienvenido:</strong>
      <span><?php echo $_SESSION['kt_login_name']; ?></span>
    </div>
    <div class="logout-wrapper">
      <a href="/administracion/loginuser/logout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Salir</span>
      </a>
    </div>
  </div>
</div>