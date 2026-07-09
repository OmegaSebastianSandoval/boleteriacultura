<div class="container py-4">
    <div class="mb-4">

    <a href="/page/guests" class="event-btn">Volver</a>
  </div>

  <!-- Debug Info (remover después) -->
  <?php if (isset($this->debugInfo)): ?>
    <div class="alert alert-info">
      <strong>Debug:</strong>
      ID: <?= $this->debugInfo['id_enviado'] ?> |
      Reserva: <?= $this->debugInfo['reserva_encontrada'] ?> |
      Tiene boletas: <?= $this->debugInfo['tiene_boletas'] ?>
    </div>
  <?php endif; ?>
  <?php if (!$this->tieneBoletas): ?>
    <!-- Mensaje cuando no hay boletas -->
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-body text-center py-5">
            <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Sin información disponible</h4>
            <p class="text-muted"><?= htmlspecialchars($this->mensaje) ?></p>
            <a href="/page/guests" class="btn btn-primary">
              <i class="fas fa-calendar-alt me-2"></i>Ver mis reservas
            </a>
          </div>
        </div>
      </div>
    </div>
  <?php else: ?>
    <!-- Header con información del evento -->
    <div class="row mb-4">
      <div class="col-12">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <div class="row align-items-center">
              <div class="col">
                <h4 class="mb-0">
                  <i class="fas fa-ticket-alt me-2"></i>Mis Boletas
                </h4>
              </div>
              <div class="col-auto">
                <button type="button" class="btn btn-light btn-sm d-none" onclick="reenviarBoletas()">
                  <i class="fas fa-envelope me-1"></i>Reenviar por Email
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-8">
                <h5 class="text-primary"><?= htmlspecialchars($this->evento->evento_titulo) ?></h5>
                <div class="row">
                  <div class="col-md-6">
                    <p class="mb-1"><strong>Fecha del evento:</strong>
                      <?= date('d/m/Y', strtotime($this->evento->evento_fecha)) ?>
                    </p>
                    <p class="mb-1"><strong>Hora:</strong>
                      <?= date('H:i', strtotime($this->evento->evento_fecha_inicio)) ?>
                    </p>
                    <?php if ($this->mesaInfo): ?>
                      <?php foreach ($this->mesaInfo as $mesa): ?>
                        <p class="mb-1"><strong>Mesa:</strong>
                          <?= htmlspecialchars($mesa->mesa_nombre) ?>
                        </p>
                        <p class="mb-1"><strong>Ubicación:</strong>
                          <?= htmlspecialchars($mesa->piso_nombre) ?> -
                          <?= htmlspecialchars($mesa->ambiente_nombre) ?>
                        </p>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                  <div class="col-md-6">
                    <p class="mb-1"><strong>Reserva #:</strong> <?= $this->reserva->id ?></p>
                    <p class="mb-1"><strong>Estado:</strong>
                      <span class="badge bg-success">Confirmada</span>
                    </p>
                    <p class="mb-1"><strong>Total personas:</strong> <?= $this->reserva->reserva_total_personas ?></p>
                  </div>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="text-center">
                  <div class="d-flex justify-content-center gap-3">
                    <div class="text-center">
                      <h3 class="text-primary mb-0"><?= $this->totalBoletas ?></h3>
                      <small class="text-muted">Total Boletas</small>
                    </div>
                    <div class="text-center">
                      <h3 class="text-success mb-0"><?= $this->boletasValidadas ?></h3>
                      <small class="text-muted">Validadas</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lista de boletas -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">
              <i class="fas fa-list me-2"></i>Lista de Boletas
            </h5>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0 responsive-table">
                <thead class="table-light">
                  <tr>
                    <th width="80">Boleta #</th>
                    <th>Participante</th>
                    <th width="120">Documento</th>
                    <th width="100">Tipo</th>
                    <th width="120">Validación</th>
                    <th width="120" class="text-center">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($this->boletas as $boleta):
                    // echo '<pre>';
                    // print_r($boleta);
                    // echo '</pre>';
                    ?>

                    <tr>
                      <td data-label="Boleta #">
                        <strong>#<?= str_pad($boleta->boleta_id, 4, '0', STR_PAD_LEFT) ?></strong>
                      </td>
                      <td data-label="Participante">
                        <div>
                          <strong><?= htmlspecialchars($boleta->invitado_nombre ?? 'N/A') ?></strong>
                          <?php if ($boleta->es_socio_principal): ?>
                            <span class="badge bg-warning text-dark ms-1">
                              <i class="fas fa-star"></i> Socio principal de la reservar
                            </span>
                          <?php endif; ?>
                          <?php if ($boleta->invitado_correo): ?>
                            <br><small class="text-muted"><?= htmlspecialchars($boleta->invitado_correo) ?></small>
                          <?php endif; ?>
                        </div>
                      </td>
                      <td data-label="Documento">
                        <?= htmlspecialchars($boleta->invitado_documento ?? 'N/A') ?>
                      </td>
                      <td data-label="Tipo">
                        <span
                          class="badge <?= $boleta->invitadoReserva_estado_invitado == 'A' || $boleta->invitadoReserva_estado_invitado == 'S' ? 'bg-success' : 'bg-secondary' ?>">
                          <?= $boleta->invitadoReserva_estado_invitado == 'A' ? 'Socio' : ($boleta->invitadoReserva_estado_invitado == 'S' ? 'Cosocio' : 'Invitado') ?>
                        </span>
                      </td>
                      <td data-label="Estado">
                        <?php
                        $estadoClass = '';
                        $estadoTexto = '';
                        switch ($boleta->boleta_estado) {
                          case '1':
                            $estadoClass = 'bg-warning';
                            $estadoTexto = 'Pendiente';
                            break;
                          case '2':
                            $estadoClass = 'bg-success';
                            $estadoTexto = 'Validada';
                            break;
                          case '3':
                            $estadoClass = 'bg-danger';
                            $estadoTexto = 'Usada';
                            break;
                          default:
                            $estadoClass = 'bg-secondary';
                            $estadoTexto = 'Sin estado';
                        }
                        ?>
                        <span class="badge <?= $estadoClass ?>"><?= $estadoTexto ?></span>
                      </td>
                      <td data-label="Acciones" class="text-center">
                        <?php if ($boleta->boleta_uid && file_exists(PDFS_PATH_NEWS . "boleta_cena_{$boleta->boleta_uid}.pdf")): ?>
                          <div class="btn-group btn-group-sm">
                            <a type="button" class="btn btn-primary p-2" download data-bs-toggle="tooltip"
                              data-bs-placement="top" data-bs-title="Descargar PDF"
                              href="/pdfs_news/boleta_cena_<?= $boleta->boleta_uid ?>.pdf">
                              <i class="fas fa-download"></i>
                            </a>
                            <a type="button" class="btn btn-info p-2" href="/pdfs_news/boleta_cena_<?= $boleta->boleta_uid ?>.pdf"
                              data-bs-placement="top" target="_blank" data-bs-toggle="tooltip" data-bs-title="Ver Detalle">
                              <i class="fas fa-eye"></i>
                            </a>
                          <?php endif; ?>

                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Información adicional -->
    <div class="row mt-4">
      <div class="col-md-6">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">
              <i class="fas fa-info-circle me-2"></i>Información Importante
            </h6>
          </div>
          <div class="card-body">
            <ul class="list-unstyled mb-0">
              <li class="mb-2">
                <i class="fas fa-check text-success me-2"></i>
                Presenta tu boleta digital.
              </li>
              <li class="mb-2">
                <i class="fas fa-id-card text-info me-2"></i>
                Lleva tu documento de identidad original
              </li>
              <li class="mb-2">
                <i class="fas fa-clock text-warning me-2"></i>
                Llega 60 minutos antes del evento
              </li>
              <li class="mb-0">
                <i class="fas fa-envelope text-primary me-2"></i>
                Revisa tu correo electrónico para actualizaciones
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="col-md-6 d-none">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0">
              <i class="fas fa-question-circle me-2"></i>¿Necesitas Ayuda?
            </h6>
          </div>
          <div class="card-body">
            <p class="mb-3">Si tienes algún problema con tus boletas, contáctanos:</p>
            <div class="d-grid gap-2">
              <a href="tel:+57015551234" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-phone me-2"></i>Llamar al Club
              </a>
              <a href="mailto:eventos@clubelnogal.com" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-envelope me-2"></i>Enviar Email
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Modal para detalles de boleta -->
<div class="modal fade" id="modalDetalleBoleta" tabindex="-1" aria-labelledby="modalDetalleBoletaLabel"
  aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetalleBoletaLabel">Detalle de Boleta</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalDetalleBoletaContent">
        <!-- Contenido cargado dinámicamente -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script>


  function reenviarBoletas () {
    Swal.fire({
      title: '¿Reenviar boletas?',
      text: 'Se enviarán todas tus boletas a tu correo electrónico registrado.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Continuar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#222220',
      cancelButtonColor: '#6c757d'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('/page/servicios/reenviarboletas')
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: data.message,
                confirmButtonText: 'Continuar',
                confirmButtonColor: '#222220'
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message,
                confirmButtonText: 'Continuar',
                confirmButtonColor: '#222220'
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Ocurrió un error al reenviar las boletas',
              confirmButtonText: 'Continuar',
              confirmButtonColor: '#222220'
            });
          });
      }
    });
  }

  // Auto-refresh cada 30 segundos para actualizar estados
  // setInterval(function () {
  //   // Solo si estamos en la página y hay boletas
  //   if (document.querySelector('.table tbody tr')) {
  //     location.reload();
  //   }
  // }, 30000);
</script>

<style>
  .card {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: none;
  }

  .card-header {
    border-bottom: 2px solid #e9ecef;
  }

  .table th {
    font-weight: 600;
    font-size: 0.875rem;
  }

  .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
  }

  .badge {
    font-size: 0.75rem;
  }

  /* Responsive Table Styles */
  .responsive-table {
    border: 1px solid #dee2e6;
    border-collapse: collapse;
    margin: 0;
    padding: 0;
    width: 100%;
    table-layout: fixed;
  }

  .responsive-table tr {
    background-color: #fff;
    border: 1px solid #dee2e6;
    padding: 0.35em;
  }

  .responsive-table th,
  .responsive-table td {
    padding: 0.75rem;
    text-align: left;
  }

  .responsive-table th {
    font-size: 0.875rem;
    font-weight: 600;
    letter-spacing: 0.05em;
    background-color: #f8f9fa;
  }

  /* Mobile responsive styles */
  @media screen and (max-width: 800px) {
    .table-responsive {
      border: 0;
      overflow: visible;
    }

    .responsive-table {
      border: 0;
      table-layout: auto;
    }

    .responsive-table thead {
      border: none;
      clip: rect(0 0 0 0);
      height: 1px;
      margin: -1px;
      overflow: hidden;
      padding: 0;
      position: absolute;
      width: 1px;
    }

    .responsive-table tr {
      border: 1px solid #dee2e6;
      border-radius: 8px;
      display: block;
      margin-bottom: 1rem;
      padding: 1rem;
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .responsive-table td {
      border-bottom: 1px solid #eee;
      display: flex;
      font-size: 0.9rem;
      text-align: left;
      padding: 0.5rem 0;
      position: relative;
      justify-content: space-between;
    }

    .responsive-table td::before {
      content: attr(data-label) ": ";
      font-weight: bold;
      color: #495057;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.05em;
      margin-right: 0.5rem;
    }

    .responsive-table td:last-child {
      border-bottom: 0;
    }

    /* Estilos específicos para móvil */
    .responsive-table td[data-label="Acciones"] {
      text-align: center;
      padding-top: 1rem;
      margin-top: 0.5rem;
      border-top: 1px solid #eee;
    }

    .responsive-table td[data-label="Acciones"]::before {
      display: block;
      text-align: start;
      margin-bottom: 0.5rem;
    }

    .responsive-table .btn-group {
      display: flex;
      justify-content: start;
      gap: 0.5rem;
    }

    .responsive-table .btn-group .btn {
      flex: 0 0 auto;
      min-width: 40px;
    }

    /* Mejorar badges en móvil */
    .responsive-table .badge {
      font-size: 0.8rem;
      padding: 0.375rem 0.75rem;
    }

    /* Mejorar visualización del participante en móvil */
    .responsive-table td[data-label="Participante"] {
      background-color: #f8f9fa;
      border-radius: 4px;
      margin-bottom: 0.5rem;
      padding: 0.75rem;
    }

    .responsive-table td[data-label="Participante"]::before {
      color: #007bff;
      font-weight: 700;
    }

    .table-hover>tbody>tr:hover>* {

      --bs-table-bg-state: none;
    }
  }

  /* Tablet responsive styles */
  @media screen and (min-width: 801px) and (max-width: 1024px) {

    .responsive-table th,
    .responsive-table td {
      padding: 0.5rem;
      font-size: 0.875rem;
    }

    .btn-group-sm .btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.8rem;
    }
  }

  /* Estilos adicionales para mejorar UX */
  @media screen and (max-width: 800px) {
    .card-header h5 {
      font-size: 1.1rem;
    }



    /* Hacer el header del evento más compacto en móvil */
    .card-body .row .col-lg-8 {
      margin-bottom: 1rem;
    }

    .card-body .row .col-lg-4 {
      text-align: center;
    }
  }
</style>