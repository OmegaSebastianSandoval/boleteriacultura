<?php if (
  $this->sesionInfo &&
  $this->sesionInfo->accion_sesion_sesion_activa == 1 &&
  (
    !$this->reservaSesion ||
    $this->reservaSesion->reserva_estado == 1
  )
): ?>
  <?php
  $fechaFin = $this->sesionInfo->accion_sesion_fecha_fin ?? $this->reservaSesion->reserva_fecha_cierre_reserva;
  if ($fechaFin instanceof DateTime) {
    $fechaFin = $fechaFin->format('Y-m-d H:i:s');
  }
  ?>
  <script>
    const fechaFinStr = "<?= $fechaFin ?>";
    const fechaFin = new Date(fechaFinStr.replace(' ', 'T'));
    const alertasMostradas = new Set();

    function actualizarTiempoRestante() {
      const ahora = new Date();
      const diferencia = fechaFin - ahora;
      if (diferencia <= 0) {
        window.location.href = "/page/login/logout?reserva_expirada=1&msm=1";
        return;
      }

      const minutosRestantes = Math.floor(diferencia / 60000);
      const segundosRestantes = Math.floor((diferencia % 60000) / 1000);

      if (segundosRestantes === 0 && [5, 3, 1].includes(minutosRestantes) && !alertasMostradas.has(minutosRestantes)) {
        alertasMostradas.add(minutosRestantes);
        const mensajes = {
          5: 'Tu reserva se cerrará en 5 minutos, continúa con el proceso de pago para no perderla.',
          3: 'Tu reserva se cerrará en 3 minutos, continúa con el proceso de pago para no perderla.',
          1: 'Tu reserva se cerrará en 1 minuto, finaliza tu pago para no perderla.'
        };
        Swal.fire({
          icon: 'warning',
          title: 'Reserva por expirar',
          text: mensajes[minutosRestantes],
          confirmButtonText: 'Entendido'
        });
      }

      const minEl = document.getElementById("minutos");
      const minElhide = document.getElementById("minutosHidden");
      const segEl = document.getElementById("segundos");

      if (minEl && segEl) {
        minEl.textContent = String(minutosRestantes).padStart(2, '0');
        if (minElhide) {
          minElhide.value = String(minutosRestantes).padStart(2, '0');
        }
        segEl.textContent = String(segundosRestantes).padStart(2, '0');

        const timerEl = document.getElementById("tiempo-restante-reserva");
        if (timerEl) {
          timerEl.classList.toggle("urgent", minutosRestantes < 3);
        }
      }
    }

    actualizarTiempoRestante();
    setInterval(actualizarTiempoRestante, 1000);
  </script>
<?php endif; ?>


<!-- ── Fixed header bar ── -->
<div class="hdr-bar">
  <div class="hdr-inner">

    <!-- Logo -->
    <a href="/" class="hdr-logo-link">
      <img src="/skins/page/images/logo.webp" alt="Club El Nogal" class="hdr-logo">
    </a>

    <!-- User info / brand spacer -->
    <?php if ($this->socio_header): ?>
      <div class="hdr-user-area">
        <div class="hdr-user-name">
          <span class="hdr-welcome-badge">
            <i class="fa-solid fa-circle-user"></i>
            Bienvenido
          </span>
          Sr(a). <?= htmlspecialchars($this->socio_header->sbe_nomb . ' ' . $this->socio_header->sbe_apel) ?>
        </div>
        <div class="hdr-user-meta">
          <span><b>Tel: <?= htmlspecialchars($this->socio_header->sbe_ncel) ?></b></span>
          <span class="hdr-dot">·</span>
          <span><b>Doc: <?= htmlspecialchars($this->socio_header->SBE_CODI) ?></b></span>
          <span class="hdr-dot">·</span>
          <span><b>Email: <?= htmlspecialchars($this->socio_header->sbe_mail) ?></b></span>
        </div>
      </div>
    <?php else: ?>
      <div class="hdr-brand-spacer"></div>
    <?php endif; ?>

    <!-- Actions -->
    <div class="hdr-actions">
      <?php if ($this->socio_header): ?>
        <a href="/page/login/logout" class="hdr-btn-exit" title="Cerrar sesión">
          <i class="fa-solid fa-right-from-bracket"></i>
          <span class="hdr-exit-text">Salir</span>
        </a>
      <?php endif; ?>
      <button class="hdr-btn-menu d-none" id="menuToggle" aria-label="Menú">
        <i class="fas fa-bars" id="menuIcon"></i>
      </button>
    </div>

  </div>
</div>


<!-- ── Slide menu ── -->
<div id="menuContent" class="menu-slide shadow">
  <ul class="list-unstyled mb-0 menu-items">
    <li>
      <a href="https://publicidad.clubelnogal.com/Cena_Gala_2025/Parrilla_Musical.html" target="_blank" class="nav-link">
        <i class="fa-solid fa-music me-2"></i>Programación
      </a>
    </li>
    <?php if ($this->verBoletas): ?>
      <li>
        <a href="/page/guests" class="nav-link">
          <i class="fa-solid fa-ticket me-2"></i>Ver mis reservas
        </a>
      </li>
    <?php endif; ?>
    <?php if ($this->verComprar): ?>
      <li>
        <a href="/page/espera/" class="nav-link">
          <i class="fa-solid fa-cart-shopping me-2"></i>Comprar boletas
        </a>
      </li>
    <?php endif; ?>
    <li class="d-none">
      <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#contactoModal">
        <i class="fa-solid fa-envelope me-2"></i>Contacto
      </a>
    </li>
  </ul>
</div>


<!-- ── Contact modal ── -->
<div class="modal fade" id="contactoModal" tabindex="-1" aria-labelledby="contactoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-warning bg-gradient text-dark border-0">
        <h5 class="modal-title fw-bold" id="contactoModalLabel">
          <i class="fa-solid fa-envelope me-2"></i>Contacto
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body py-4">
        <div class="mb-3 d-flex align-items-center gap-2">
          <span class="fs-4 text-warning"><i class="fa-solid fa-at"></i></span>
          <div>
            <strong class="d-block">E-mail</strong>
            <a href="mailto:info@clubelnogal.com" target="_blank" class="contact-link">info@clubelnogal.com</a>
          </div>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="fs-4 text-warning"><i class="fa-solid fa-phone"></i></span>
          <div>
            <strong class="d-block">Teléfono</strong>
            <a href="tel:6013267700" target="_blank" class="contact-link">601 326 7700</a>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<script>
  $(document).ready(function () {
    let menuTimer;
    $('#menuToggle').on('click', function () {
      const menuContent = $('#menuContent');
      const icon = $('#menuIcon');
      menuContent.toggleClass('active');
      icon.toggleClass('fa-bars fa-times');
      if (menuContent.hasClass('active')) {
        if (menuTimer) clearTimeout(menuTimer);
        menuTimer = setTimeout(function () {
          menuContent.removeClass('active');
          icon.removeClass('fa-times').addClass('fa-bars');
        }, 2000);
      } else {
        if (menuTimer) { clearTimeout(menuTimer); menuTimer = null; }
      }
    });
  });
</script>

<script>
  function validarSesion() {
    fetch('/page/index/validarsesion', { method: 'POST' })
      .then(response => response.json())
      .then(data => {
        if (!data) return;
        if (data.success && data.sesion_activa === 'salir') {
          window.location.href = '/page/login/logout';
        }
      });
  }
</script>


<style>
  /* ── Fixed header bar ── */
  .hdr-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    height: 60px;
    background: rgba(21, 25, 29, 1);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border-bottom: 1px solid rgba(255, 193, 7, 0.2);
    z-index: 1000;
    box-shadow: 0 2px 24px rgba(0, 0, 0, 0.45);
  }

  .hdr-inner {
    display: flex;
    align-items: center;
    height: 100%;
    padding: 0 1.25rem;
    gap: 0.75rem;
  }

  /* Logo */
  .hdr-logo-link {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    text-decoration: none;
    gap: 0;
  }

  .hdr-logo {
    height: 34px;
    width: auto;
    object-fit: contain;
    transition: opacity 0.2s ease;
  }

  .hdr-logo-link:hover .hdr-logo {
    opacity: 0.8;
  }

  /* Gold divider after logo */
  .hdr-logo-link::after {
    content: '';
    display: block;
    width: 1px;
    height: 26px;
    background: rgba(255, 193, 7, 0.28);
    margin-left: 0.9rem;
    flex-shrink: 0;
  }

  /* User area */
  .hdr-user-area {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 2px;
    min-width: 0;
    overflow: hidden;
  }

  .hdr-brand-spacer {
    flex: 1;
  }

  .hdr-user-name {
    font-size: 1rem;
    font-weight: 700;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: 0.1px;
    display: flex;
    align-items: center;
    gap: 0.3rem;
  }

  .hdr-welcome-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    background: #ffc107;
    color: #111111;
    padding: 3px 11px;
    border-radius: 20px;
    font-size: 1rem;
    font-weight: 700;
    white-space: nowrap;
    flex-shrink: 0;
    letter-spacing: 0.2px;
  }

  .hdr-welcome-badge i {
    font-size: 1rem;
  }

  .hdr-user-meta {
    display: none;
  }

  .hdr-user-meta b {
    font-weight: 400;
    color: white;
  }

  .hdr-dot {
    opacity: 0.3;
  }

  /* Actions */
  .hdr-actions {
    display: flex;
    align-items: center;
    gap: 0.45rem;
    flex-shrink: 0;
  }

  .hdr-btn-exit {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.66rem;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    background: rgba(220, 53, 69, 0.14);
    border-color: rgba(220, 53, 69, 0.4);
    color: #ef9a9a;
    text-decoration: none;
    padding: 6px 11px;
    border-radius: 4px;
    transition: all 0.18s ease;
    white-space: nowrap;
    height: 36px;
  }

  .hdr-btn-exit:hover {
    background: rgba(220, 53, 69, 0.14);
    border-color: rgba(220, 53, 69, 0.4);
    color: #ef9a9a;
  }

  .hdr-btn-menu {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.13);
    border-radius: 4px;
    color: #ffffff;
    cursor: pointer;
    transition: all 0.18s ease;
    padding: 0;
  }

  .hdr-btn-menu:hover {
    background: rgba(255, 193, 7, 0.12);
    border-color: rgba(255, 193, 7, 0.32);
  }

  .hdr-btn-menu i {
    font-size: 0.9rem;
    color: #ffffff !important;
  }

  /* Push content below fixed header */
  body {
    padding-top: 60px !important;
  }

  /* Menu slide: open just below the new header */
  .menu-slide {
    top: 60px !important;
  }

  /* Collapse header flow height (bar is fixed, out of flow) */
  header {
    height: 0 !important;
    overflow: visible !important;
    min-height: 0 !important;
  }

  /* ── Responsive ── */
  @media (max-width: 767px) {
    .hdr-inner { padding: 0 0.75rem; gap: 0.5rem; }
    .hdr-logo  { height: 28px; }
  }

  @media (max-width: 575px) {
    /* Badge "Bienvenido": sólo icono, sin texto */
    .hdr-welcome-badge { padding: 3px 7px; }
    .hdr-welcome-badge::after { content: ''; }

    /* Botón salir: sólo icono */
    .hdr-exit-text { display: none; }
    .hdr-btn-exit  { width: 36px; height: 36px; padding: 0; justify-content: center; }
  }

  @media (max-width: 380px) {
    .hdr-inner { padding: 0 0.5rem; gap: 0.35rem; }
    .hdr-logo  { height: 24px; }
    .hdr-logo-link::after { margin-left: 0.5rem; }
    /* Ocultar también el badge completo en pantallas muy pequeñas */
    .hdr-welcome-badge { display: none; }
    .hdr-user-name { font-size: 0.75rem; }
  }

  /* ── Modal de contacto (scoped para no pisar otros estilos) ── */
  #contactoModal .modal-content {
    border-radius: 1rem;
  }
  #contactoModal .modal-header.bg-warning {
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
  }
  #contactoModal .modal-body strong { color: #212529; }

  .contact-link {
    color: gray; font-weight: 500;
    text-decoration: none; transition: color 0.2s;
  }
  .contact-link:hover { color: #ffc107; }

  .bx-user3 { display: none; }
</style>