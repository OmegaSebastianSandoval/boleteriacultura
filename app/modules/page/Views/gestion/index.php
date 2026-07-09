<div class="container">
  <a href="/page/login/logout" class="event-btn btn-red ">Salir</a>
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6">
      <!-- Header con gradiente -->
      <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-gradient rounded-circle mb-3" style="width: 80px; height: 80px;">
          <i class="fas fa-user-shield fa-2x text-white"></i>
        </div>
        <h2 class="fw-bold text-white mb-2">Gestión de boletería</h2>
      </div>

      <!-- Card principal -->
      <div class="card border-0 shadow-lg">
        <div class="card-body p-4 p-md-5">
          <?php if (isset($this->error) && $this->error): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
              <i class="fas fa-exclamation-circle me-2 fs-5"></i>
              <div class="flex-grow-1">
                <?php echo htmlspecialchars($this->error); ?>
              </div>
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          <?php endif; ?>

          <div id="mensajeResultado"></div>

          <form id="formBuscarSocio" class="p-2">
            <div class="mb-4">
              <label for="ncar" class="form-label fw-semibold text-dark">
                <i class="fas fa-id-card me-2 text-primary"></i>Número de Carnet
              </label>

              <input
                type="text"
                class="form-control"
                id="ncar"
                name="ncar"
                placeholder="Ej: 12345"
                required
                autofocus>

              <div class="form-text fs-6">
                <i class="fas fa-info-circle me-1"></i>
                Ingrese el número de carnet del socio
              </div>
            </div>

            <div class="d-grid gap-2 mb-4">
              <button type="submit" class="btn btn-primary btn-lg shadow-sm" id="btnBuscar">
                <i class="fas fa-right-to-bracket me-2"></i>
                Simular sesión de socio
              </button>
            </div>
          </form>

          <hr class="my-4">

          <!-- Información adicional -->
          <div class="bg-light bg-gradient rounded-3 p-3">
            <div class="d-flex align-items-start">
              <div class="flex-shrink-0">
                <i class="fas fa-shield-alt text-info fs-4 me-2"></i>
              </div>
              <div class="flex-grow-1">
                <h6 class="fw-semibold text-dark mb-2">
                  <i class="fas fa-lock me-1"></i>
                  Acceso Administrativo
                </h6>
                <p class="text-muted small mb-0">
                  Esta herramienta permite iniciar sesión como cualquier socio para gestionar reservas y compras.
                  La sesión se crea con las mismas credenciales y permisos que tendría el socio al iniciar sesión normalmente.
                </p>
              </div>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formBuscarSocio');
    const btnBuscar = document.getElementById('btnBuscar');
    const inputNcar = document.getElementById('ncar');

    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const ncar = inputNcar.value.trim();

      if (!ncar) {
        mostrarMensaje('Por favor ingrese un número de carnet', 'danger');
        return;
      }

      // Deshabilitar botón y mostrar loading
      const textoOriginal = btnBuscar.innerHTML;
      btnBuscar.disabled = true;
      btnBuscar.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Procesando...';

      // Enviar petición AJAX
      fetch('/page/gestion/buscar', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'ncar=' + encodeURIComponent(ncar)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            mostrarMensaje(data.message, 'success');

            // Animación de éxito
            btnBuscar.innerHTML = '<i class="fas fa-check-circle me-2"></i>¡Éxito! Redirigiendo...';
            btnBuscar.classList.remove('btn-primary');
            btnBuscar.classList.add('btn-success');

            setTimeout(function() {
              window.location.href = data.redirect;
            }, 1500);
          } else {
            mostrarMensaje(data.message, 'danger');
            btnBuscar.disabled = false;
            btnBuscar.innerHTML = textoOriginal;
          }
        })
        .catch(error => {
          console.error('Error:', error);
          mostrarMensaje('Error al procesar la solicitud. Por favor intente nuevamente.', 'danger');
          btnBuscar.disabled = false;
          btnBuscar.innerHTML = textoOriginal;
        });
    });

    function mostrarMensaje(mensaje, tipo) {
      const iconos = {
        'success': 'fa-check-circle',
        'danger': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
      };

      const icono = iconos[tipo] || 'fa-info-circle';

      const html = `
      <div class="alert alert-${tipo} alert-dismissible fade show d-flex align-items-center animate__animated animate__fadeInDown" role="alert">
        <i class="fas ${icono} me-2 fs-5"></i>
        <div class="flex-grow-1">${mensaje}</div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    `;

      document.getElementById('mensajeResultado').innerHTML = html;

      // Auto-cerrar después de 8 segundos si no es success
      if (tipo !== 'success') {
        setTimeout(function() {
          const alert = document.querySelector('#mensajeResultado .alert');
          if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
          }
        }, 8000);
      }
    }

    
  });
</script>

<style>
  :root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.15);
  }

  body {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
  }

  .card {
    border-radius: 20px;
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover) !important;
  }

  .shadow-lg {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
  }

  .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
  }

  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
  }

  .btn-primary:active {
    transform: translateY(0);
  }

  .input-group {
    border-radius: 12px;
    /* overflow: hidden; */
    transition: all 0.3s ease;
  }

  .input-group:focus-within {
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
  }

  .input-group-text {
    border: 1px solid #dee2e6;
  }

  .form-control {
    border: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
    font-size: 1.1rem;
  }

  .form-control:focus,
  .form-control:hover {
    border-color: #667eea;
    box-shadow: none;
  }

  .bg-gradient {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  }

  .rounded-circle {
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  }

  hr {
    opacity: 0.1;
  }

  /* Animaciones */
  @keyframes fadeInDown {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .animate__animated {
    animation-duration: 0.5s;
  }

  .animate__fadeInDown {
    animation-name: fadeInDown;
  }

  /* Spinner animation */
  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  .spinner-border {
    animation: spin 0.75s linear infinite;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .card-body {
      padding: 2rem 1.5rem !important;
    }

    .form-control {
      font-size: 1rem;
    }
  }

  /* Alert improvements */
  .alert {
    border-radius: 12px;
    border: none;
    font-weight: 500;
  }

  .alert-danger {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
  }

  .alert-success {
    background: linear-gradient(135deg, #51cf66 0%, #37b24d 100%);
    color: white;
  }

  .alert-info {
    background: linear-gradient(135deg, #74c0fc 0%, #339af0 100%);
    color: white;
  }

  .alert .btn-close {
    filter: brightness(0) invert(1);
  }

  /* Icon enhancements */
  .fas,
  .far {
    transition: all 0.3s ease;
  }

  .btn:hover .fas {
    transform: scale(1.1);
  }

  /* Input group hover effect */
  .input-group:hover .input-group-text {
    background-color: #e7f5ff;
    border-color: #667eea;
  }

  /* Features list animation */
  .row.g-3>div {
    animation: fadeInUp 0.6s ease forwards;
    opacity: 0;
  }

  .row.g-3>div:nth-child(1) {
    animation-delay: 0.1s;
  }

  .row.g-3>div:nth-child(2) {
    animation-delay: 0.2s;
  }

  .row.g-3>div:nth-child(3) {
    animation-delay: 0.3s;
  }

  .row.g-3>div:nth-child(4) {
    animation-delay: 0.4s;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(10px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>