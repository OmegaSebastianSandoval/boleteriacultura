<style>
  body {
    /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
    min-height: 100vh;
    /* font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; */
  }

  .waiting-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .position-number {
    font-size: 4rem;
    font-weight: 800;
    background: linear-gradient(45deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
  }

  .pulse-animation {
    animation: pulse 2s infinite;
  }

  @keyframes pulse {
    0% {
      transform: scale(1);
    }

    50% {
      transform: scale(1.05);
    }

    100% {
      transform: scale(1);
    }
  }

  .spinner-custom {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }

  @keyframes spin {
    0% {
      transform: rotate(0deg);
    }

    100% {
      transform: rotate(360deg);
    }
  }

  .status-indicator {
    width: 15px;
    height: 15px;
    background-color: #28a745;
    border-radius: 50%;
    animation: blink 1.5s infinite;
  }

  @keyframes blink {

    0%,
    50% {
      opacity: 1;
    }

    51%,
    100% {
      opacity: 0.3;
    }
  }

  .info-card {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 15px;
    backdrop-filter: blur(5px);
  }
</style>

<body>
  <div class="container vh-100 d-flex align-items-center justify-content-center">
    <div class="row w-100 justify-content-center">
      <div class="col-12 col-md-8 col-lg-6">
        <div class="waiting-card p-3 text-center">
          <!-- Logo/Header -->
          <div class="mb-3">
            <i class="fas fa-clock text-primary" style="font-size: 3rem;"></i>
          </div>

          <!-- Title -->
          <h1 class="mb-3 fw-bold text-dark">Sala de Espera</h1>

          <!-- Position Display -->
          <div class="pulse-animation mb-3">
            <div class="position-number" id="positionNumber"><?= $this->posicionEnCola ?></div>
            <h3 class="text-muted mb-0">Turnos pendientes</h3>
          </div>

          <!-- Status Indicator -->
          <div class="d-flex align-items-center justify-content-center mb-3">
            <div class="status-indicator me-2"></div>
            <span class="text-muted">Sistema activo</span>
          </div>

          <!-- Loading Spinner -->
          <div class="mb-3">
            <div class="spinner-custom mx-auto"></div>
          </div>

          <!-- Information Cards -->
          <div class="row g-3 mb-3">
            <div class="col-6">
              <div class="info-card p-3">
                <i class="fas fa-users mb-2 text-muted" style="font-size: 1.5rem;"></i>
                <div class="text-muted">
                  <div class="fw-bold" id="totalActive"><?= $this->totalActivos ?></div>
                  <small>Usuarios activos</small>
                </div>
              </div>
            </div>
            <div class="col-6">
              <div class="info-card p-3">
                <i class="fas fa-clock mb-2 text-muted" style="font-size: 1.5rem;"></i>
                <div class="text-muted">
                  <div class="fw-bold">15 min</div>
                  <small>Tiempo por turno</small>
                </div>
              </div>
            </div>
          </div>

          <!-- Message -->
          <div class="alert alert-info border-0" style="background: rgba(23, 162, 184, 0.1);">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Por favor espere</strong><br>
            Su turno será habilitado automáticamente cuando esté disponible
          </div>

          <!-- Last Updated -->
          <div class="text-muted small">
            <i class="fas fa-sync-alt me-1"></i>
            Actualizado hace <span id="lastUpdate">0</span> segundos
          </div>
        </div>
      </div>
    </div>
  </div>



  <script>
    let colaId = <?= $this->colaId ?>;
    let lastUpdateTime = 0;
    let updateInterval;

    function verificarEstado () {
      $.ajax({
        url: '/page/espera/verificarestado',
        method: 'POST',
        data: { colaId: colaId },
        dataType: 'json',
        success: function (response) {
          if (response.error) {
            console.error(response.error);
            return;
          }

          if (response.redirect) {
            // Es mi turno, redirigir
            window.location.href = response.redirect;
            return;
          }

          if (response.waiting) {
            // Actualizar información de la cola
            $('#positionNumber').text(Number(response.position) + 1);
            $('#totalActive').text(response.totalActive);

            // Agregar efectos visuales cuando cambia la posición
            $('#positionNumber').addClass('pulse-animation');
            setTimeout(() => {
              $('#positionNumber').removeClass('pulse-animation');
            }, 2000);
          }

          // Resetear contador de actualización
          lastUpdateTime = 0;
        },
        error: function (xhr, status, error) {
          console.error('Error verificando estado:', error);
        }
      });
    }

    function updateLastUpdateCounter () {
      lastUpdateTime++;
      $('#lastUpdate').text(lastUpdateTime);
    }

    // Verificar estado cada 10 segundos
    updateInterval = setInterval(verificarEstado, 10000);

    // Actualizar contador cada segundo
    setInterval(updateLastUpdateCounter, 1000);

    // Verificar inmediatamente al cargar la página
    $(document).ready(function () {
      verificarEstado();
    });

    // Limpiar intervalos al salir de la página
    $(window).on('beforeunload', function () {
      if (updateInterval) {
        clearInterval(updateInterval);
      }
    });

    // Advertir al usuario antes de recargar la página
    // window.addEventListener('beforeunload', function (e) {
    //   // Solo mostrar advertencia si estoy en cola de espera (no si es redirección automática)
    //   if (window.location.pathname.includes('/espera/')) {
    //     e.preventDefault();
    //     e.returnValue = '¿Está seguro de que desea recargar la página? Esto podría afectar su posición en la cola de espera.';
    //     return e.returnValue;
    //   }
    // });

    // Detectar si la página se está recargando y mostrar mensaje
    // if (performance.navigation.type == performance.navigation.TYPE_RELOAD) {
    //   // Mostrar notificación de que se mantuvo la posición
    //   setTimeout(function () {
    //     var alertDiv = $('<div class="alert alert-warning alert-dismissible fade show" role="alert">' +
    //       '<i class="fas fa-exclamation-triangle me-2"></i>' +
    //       '<strong>Página recargada:</strong> Se ha mantenido su posición en la cola.' +
    //       '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
    //       '</div>');
    //     $('.waiting-card .alert').first().before(alertDiv);

    //     // Auto-ocultar después de 5 segundos
    //     setTimeout(function () {
    //       alertDiv.fadeOut();
    //     }, 5000);
    //   }, 1000);
    // }
  </script>