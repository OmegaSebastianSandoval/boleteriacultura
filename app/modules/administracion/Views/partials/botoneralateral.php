<ul>

  <?php if (Session::getInstance()->get('kt_login_level') == '1') { ?>
    <li <?php if ($this->botonpanel == 9) { ?>class="activo" <?php } ?>><a href="/administracion/dashboard"><i
          class="fas fa-tachometer-alt"></i>Dashboard</a></li>
    <li <?php if ($this->botonpanel == 2) { ?>class="activo" <?php } ?>>
      <a href="/administracion/publicidad">
        <i class="far fa-images"></i>
        Administrar publicidad
      </a>
    </li>
    <li class="dropdown">
      <a href="#" onclick="toggleSubmenu(this)">
        <i class="fas fa-cogs"></i>Configuración evento
        <i class="fas fa-chevron-right dropdown-arrow"></i>
      </a>
      <ul class="submenu" style="display: none;">
        <li <?php if ($this->botonpanel == 25) { ?>class="activo" <?php } ?>><a href="/administracion/boletasestilos"><i class="fa-solid fa-object-group"></i>Administrar dise&ntilde;os</a></li>
        <li <?php if ($this->botonpanel == 1) { ?>class="activo" <?php } ?>><a href="/administracion/informacion"><i class="fa-solid fa-envelope"></i>Administrar envío de correos</a></li>
        <li <?php if ($this->botonpanel == 10) { ?>class="activo" <?php } ?>><a href="/administracion/eventos/manage?id=1"><i
              class="fas fa-calendar-plus"></i>Administrar evento</a></li>
        <li <?php if ($this->botonpanel == 26) { ?>class="activo" <?php } ?>><a href="/administracion/terminos"> <i class="fas fa-file-alt"></i>Administrar términos y condiciones</a></li>
      </ul>
    </li>

    <li class="dropdown">
      <a href="#" onclick="toggleSubmenu(this)">
        <i class="fas fa-building"></i>Estructura
        <i class="fas fa-chevron-right dropdown-arrow"></i>
      </a>
      <ul class="submenu" style="display: none;">
        <li <?php if ($this->botonpanel == 6) { ?>class="activo" <?php } ?>><a href="/administracion/pisos"><i
              class="fas fa-building"></i>Administrar pisos</a></li>
        <li <?php if ($this->botonpanel == 7) { ?>class="activo" <?php } ?>><a href="/administracion/ambientes"><i
              class="fas fa-door-open"></i>Administrar ambientes</a></li>
        <li <?php if ($this->botonpanel == 8) { ?>class="activo" <?php } ?>><a href="/administracion/mesas"><i
              class="fas fa-chair"></i>Administrar mesas</a></li>
        <li <?php if ($this->botonpanel == 5) { ?>class="activo" <?php } ?>><a href="/administracion/categorias"><i
              class="fas fa-tags"></i>Administrar precios</a></li>
      </ul>
    </li>

    <li class="dropdown">
      <a href="#" onclick="toggleSubmenu(this)">
        <i class="fas fa-calendar-check"></i>Reservas
        <i class="fas fa-chevron-right dropdown-arrow"></i>
      </a>
      <ul class="submenu" style="display: none;">
        <li <?php if ($this->botonpanel == 12) { ?>class="activo" <?php } ?>><a href="/administracion/reservas"><i
              class="fas fa-calendar-check"></i>Visualizar reserva</a></li>
        <li <?php if ($this->botonpanel == 13) { ?>class="activo" <?php } ?>><a href="/administracion/infodocumento"><i
              class="fas fa-search"></i>Verificador de reserva</a></li>
        <li <?php if ($this->botonpanel == 21) { ?>class="activo" <?php } ?>><a
            href="/administracion/reservas/listadofacturacion"><i class="fas fa-calendar-check"></i>Listado para
            facturaci&oacute;n</a></li>
        <li <?php if ($this->botonpanel == 24) { ?>class="activo" <?php } ?>><a
            href="/administracion/reservas/informes"><i class="fa-regular fa-file-excel"></i>Informes</a></li>
      </ul>
    </li>

    <li <?php if ($this->botonpanel == 4) { ?>class="activo" <?php } ?>><a href="/administracion/usuario"><i
          class="fas fa-users"></i>Administrar usuarios</a></li>
    <?php if (Session::getInstance()->get('kt_login_id') == '1') { ?>

      <li <?php if ($this->botonpanel == 16) { ?>class="activo" <?php } ?>><a href="/administracion/accionsesiones"><i
            class="fas fa-user-clock"></i>Administrar sesiones</a></li>
    <?php } ?>
    <li <?php if ($this->botonpanel == 18) { ?>class="activo" <?php } ?>><a href="/administracion/reservasauditoria"> <i
          class="fa-solid fa-newspaper"></i>Auditoria</a></li>
    <li <?php if ($this->botonpanel == 14) { ?>class="activo" <?php } ?>>
      <a href="/administracion/infoboletas">
        <i class="fas fa-ticket-alt"></i>
        Administrar información de boletas
      </a>
    </li>
    <li <?php if ($this->botonpanel == 15) { ?>class="activo" <?php } ?>>
      <a href="/administracion/newinvitados">
        <i class="fas fa-ticket-alt"></i>
        Administrar invitados reserva
      </a>
    </li>
  <?php } ?>
  <?php if (Session::getInstance()->get('kt_login_level') == '2') { ?>
    <?php if ($_SESSION['kt_login_user'] != "consulta") { ?>
      <li <?php if ($this->botonpanel == 9) { ?>class="activo" <?php } ?>><a href="/administracion/dashboard"><i
            class="fas fa-tachometer-alt"></i>Dashboard</a></li>
    <?php } ?>
    <li <?php if ($this->botonpanel == 7) { ?>class="activo" <?php } ?>><a href="/administracion/ambientes"><i
          class="fas fa-door-open"></i>Administrar Ambientes</a></li>
    <li <?php if ($this->botonpanel == 12) { ?>class="activo" <?php } ?>><a href="/administracion/reservas"><i
          class="fas fa-calendar-check"></i>Visualizar Reserva</a></li>
    <li <?php if ($this->botonpanel == 17) { ?>class="activo" <?php } ?>><a href="/administracion/infodocumento"><i
          class="fas fa-search"></i>Verificador de reserva</a></li>
    <li <?php if ($this->botonpanel == 15) { ?>class="activo" <?php } ?>>
      <a href="/administracion/newinvitados">
        <i class="fas fa-ticket-alt"></i>
        Administrar invitados reserva
      </a>
    </li>
  <?php } ?>

</ul>

<script>
  function toggleSubmenu(element) {
    var submenu = element.nextElementSibling;
    var arrow = element.querySelector('.dropdown-arrow');
    // var allSubmenus = document.querySelectorAll('.submenu');
    // allSubmenus.forEach(function(sm) {
    //   if (sm !== submenu) {
    //     sm.style.display = 'none';
    //   }
    // });
    if (submenu.style.display === "none" || submenu.style.display === "") {
      submenu.style.display = "flex";
      if (arrow) arrow.classList.add('open');
    } else {
      submenu.style.display = "none";
      if (arrow) arrow.classList.remove('open');
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
    var submenus = document.querySelectorAll('.submenu');
    submenus.forEach(function(submenu) {
      var activeItems = submenu.querySelectorAll('li.activo');
      if (activeItems.length > 0) {
        submenu.style.display = 'flex';
        var parentLink = submenu.previousElementSibling;
        var arrow = parentLink.querySelector('.dropdown-arrow');
        if (arrow) arrow.classList.add('open');
      }
    });
  });
</script>