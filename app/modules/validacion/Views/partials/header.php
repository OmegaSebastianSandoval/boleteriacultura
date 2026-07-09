<div class="container mb-4">
  <div class="row mt-lg-4 mt-2 mb-0 mb-lg-2">
    <div class="col-lg-12 p-0">
      <div class="d-flex justify-content-between align-items-center">
        <!-- <div class="text-end">
          <a href="#" class="btn-menu" id="menuToggle">
            <i class="fas fa-bars" id="menuIcon"></i>
          </a>
        </div> -->
        <img src="/skins/administracion/images/logo-new.png" class="logo-blanco" style="height: 60px;">

        <a href="/validacion/index/logout" class="btn btn-danger"><i
            class="fa-solid fa-right-from-bracket me-2"></i>Salir</a>
      </div>
    </div>
  </div>
</div>


<div id="menuContent" class="menu-slide shadow">
  <ul class="list-unstyled mb-0 menu-items">


    <li><a href="#" class="nav-link"><i class="fa-solid fa-envelope me-2"></i>Contacto</a></li>
    <li><a href="/validacion/index/logout" class="nav-link"><i class="fa-solid fa-right-from-bracket me-2"></i>Salir</a>
    </li>
  </ul>
</div>

<script>
  $(document).ready(function() {
    $('#menuToggle').on('click', function() {
      $('#menuContent').toggleClass('active');

      const icon = $('#menuIcon');
      icon.toggleClass('fa-bars fa-times');
    });
  });
</script>


<style>
  .bx-user3 {
    display: none;
  }
</style>