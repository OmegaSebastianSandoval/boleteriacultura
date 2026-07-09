<style>
  .btn-menu {
    display: none;
  }
</style>
<div class="container-fluid p-0">
  <!-- Header con navegación -->
  <div class="header-section">
    <div class="container">
      <div class="d-flex align-items-center justify-content-between py-3">
        <a href="/validacion/evento" class="btn-back">
          <i class="fas fa-arrow-left me-2"></i> Volver al inicio
        </a>
        <div class="status-badge d-none">
          <i class="fas fa-ticket-alt me-2"></i>
          Validación de boleta
        </div>
      </div>
    </div>
  </div>

  <div class="main-content">
    <div class="container">
      <div class="row g-4">
        <!-- Columna izquierda - Imagen del evento y detalles -->
        <div class="col-12 col-xl-5">
          <div class="event-card">
            <div class="event-image">
              <img src="/images/<?= $this->evento->evento_imagen_evento ?>" alt="<?= $this->evento->evento_titulo ?>"
                class="img-fluid" />
              <div class="event-overlay">
                <div class="event-status">
                  <?php if ($this->boletosTodos == $this->boletosValidados): ?>
                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Completamente Validado</span>
                  <?php else: ?>
                    <span class="badge bg-warning"><i class="fas fa-clock"></i> Pendiente de Validación</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="event-details">
              <h1 class="event-title"><?= $this->evento->evento_titulo ?></h1>
              <div class="event-info">
                <div class="info-item">
                  <i class="fas fa-calendar-alt"></i>
                  <span>Fecha del evento: <?= date('d/m/Y H:i', strtotime($this->evento->evento_fecha)) ?></span>
                </div>
                <div class="info-item">
                  <i class="fas fa-map-marker-alt"></i>
                  <span>Lugar: <?= $this->evento->evento_lugar ?? 'Por definir' ?></span>
                </div>
              </div>
            </div>
            <div class="info-card h-100">
              <div class="card-header-2">
                <h3><i class="fas fa-user me-2"></i>Información del invitado</h3>
              </div>
              <div class="card-body">
                <div class="info-group">
                  <label>Nombre completo</label>
                  <div class="info-value">
                    <?= ($this->boletaInformacion->invitadoReserva_nombre_invitado . ' ' . $this->boletaInformacion->invitadoReserva_apellido_invitado) ?? ($this->reserva->reserva_nombre_cliente . ' ' . $this->reserva->reserva_apellido_cliente) ?>
                  </div>
                </div>
                <div class="info-group">
                  <label>Documento</label>
                  <div class="info-value">
                    <?= $this->boletaInformacion->boleta_documento ?? $this->reserva->reserva_documento ?>
                  </div>
                </div>
                <div class="info-group d-none">
                  <label>Teléfono</label>
                  <div class="info-value">
                    <?= $this->boletaInformacion->invitadoReserva_telefono ?? 'No disponible' ?>
                  </div>
                </div>
                <div class="info-group d-none">
                  <label>Correo</label>
                  <div class="info-value">
                    <?= $this->boletaInformacion->invitadoReserva_correo_invitado ?? 'No disponible' ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Columna derecha - Información de la reserva y boleta -->
        <div class="col-12 col-xl-7">
          <!-- Fila con información del cliente y detalles de la reserva -->
          <div class="row g-4 mb-4">
            <!-- Card de información del cliente -->
            <div class="col-12 col-xl-6 d-none">
              <div class="info-card h-100">
                <div class="card-header">
                  <h3><i class="fas fa-user me-2"></i>Información del invitado</h3>
                </div>
                <div class="card-body">
                  <div class="info-group">
                    <label>Nombre completo</label>
                    <div class="info-value">
                      <?= $this->boletaInformacion->invitadoReserva_nombre_invitado . ' ' . $this->boletaInformacion->invitadoReserva_apellido_invitado ?? ($this->reserva->reserva_nombre_cliente . ' ' . $this->reserva->reserva_apellido_cliente) ?>
                    </div>
                  </div>
                  <div class="info-group">
                    <label>Documento</label>
                    <div class="info-value">
                      <?= $this->boletaInformacion->boleta_documento ?? $this->reserva->reserva_documento ?>
                    </div>
                  </div>
                  <div class="info-group d-none">
                    <label>Teléfono</label>
                    <div class="info-value">
                      <?= $this->boletaInformacion->invitadoReserva_telefono ?? 'No disponible' ?>
                    </div>
                  </div>
                  <div class="info-group d-none">
                    <label>Correo</label>
                    <div class="info-value">
                      <?= $this->boletaInformacion->invitadoReserva_correo_invitado ?? 'No disponible' ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Card de información de la reserva -->
            <div class="col-12 col-xl-12">
              <div class="info-card h-100">
                <div class="card-header">
                  <h3><i class="fas fa-clipboard-list me-2"></i>Detalles de la reserva</h3>
                </div>
                <div class="card-body">
                  <div class="row g-3 mb-3">
                    <div class="col-4">
                      <div class="stat-card-mini">
                        <div class="stat-icon-mini bg-primary">
                          <i class="fas fa-hashtag"></i>
                        </div>
                        <div class="stat-content-mini">
                          <div class="stat-label-mini">Id reserva</div>
                          <div class="stat-value-mini"><?= $this->reserva->id ?></div>
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="stat-card-mini">
                        <div class="stat-icon-mini bg-info">
                          <i class="fas fa-chair"></i>
                        </div>
                        <div class="stat-content-mini">
                          <div class="stat-label-mini"><?= count($this->mesasConsulta) > 1 ? 'Mesas' : 'Mesa' ?></div>
                          <div class="stat-value-mini d-flex justify-content-center">
                            <?php
                            $mesaCodigos = array_map(function ($mesa) {
                              return $mesa->mesa_codigo;
                            }, $this->mesasConsulta);
                            echo implode(', ', $mesaCodigos);
                            ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="stat-card-mini">
                        <div class="stat-icon-mini bg-success">
                          <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content-mini">
                          <div class="stat-label-mini">Personas</div>
                          <div class="stat-value-mini"><?= $this->reserva->reserva_total_personas ?></div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row g-3 mb-3">
                    <div class="col-12">
                      <div class="stat-card-mini">
                        <div class="stat-icon-mini bg-warning">
                          <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="stat-content-mini">
                          <div class="stat-label-mini">Ubicación en el ambiente</div>
                          <div class="stat-value-mini">
                            <button class="btn btn-sm btn-outline-primary btn-show-location"
                              data-mesa-id="<?= $this->mesaInfo->mesa_id ?>"
                              data-ambiente-id="<?= $this->mesaInfo->mesa_ambiente ?>">
                              <i class="fas fa-eye me-1"></i>Ver ubicación
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row g-2">
                    <div class="col-6">
                      <div class="info-group">
                        <label>Ambiente</label>
                        <div class="info-value"><?= $this->mesaInfo->ambiente_nombre ?? 'No disponible' ?></div>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="info-group">
                        <label>Piso</label>
                        <div class="info-value"><?= $this->mesaInfo->piso_nombre ?? 'No disponible' ?></div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="info-group">
                        <label>Mesa asignada</label>
                        <div class="info-value code-value fw-bold">
                          <?= $this->mesaInfo->mesa_nombre ?? 'No disponible' ?>
                        </div>
                      </div>
                    </div>
                    <div class="col-6 d-none">
                      <div class="info-group">
                        <label>Estado reserva</label>
                        <div class="info-value">
                          <span class="badge bg-success"><?= $this->reserva->reserva_estado_texto ?></span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Card de información de la boleta -->
          <div class="info-card mb-4">
            <div class="card-header">
              <h3><i class="fas fa-ticket-alt me-2"></i>Información de la boleta</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="info-group">
                    <label>Código de boleta</label>
                    <div class="info-value code-value">
                      <?= $this->boletaInfo->boleta_uid ?>
                      <button class="btn btn-sm btn-outline-secondary ms-2"
                        onclick="copyToClipboard('<?= $this->boletaInfo->boleta_uid ?>')">
                        <i class="fas fa-copy"></i>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info-group">
                    <label>Número de ticket</label>
                    <div class="info-value"><?= $this->boletaInfo->boleta_numero_ticket ?></div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info-group">
                    <label>Método de escaneo</label>
                    <div class="info-value">
                      <?php
                      $metodo = $this->metodo_escaneado ?? 'camera';
                      switch ($metodo) {
                        case 'camera':
                          echo '<i class="fas fa-camera text-primary"></i> Cámara';
                          break;
                        case 'usb_scanner':
                          echo '<i class="fas fa-qrcode text-info"></i> Escáner USB';
                          break;
                        case 'manual':
                          echo '<i class="fas fa-keyboard text-warning"></i> Entrada Manual';
                          break;
                        default:
                          echo '<i class="fas fa-question text-muted"></i> ' . ucfirst($metodo);
                      }
                      ?>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 d-none">
                  <div class="info-group">
                    <label>Tipo de boleta</label>
                    <div class="info-value">
                      <?php
                      $tipo = $this->boletaInfo->boleta_tipo_boleta == 'P' ? 'Presencial' : 'Virtual';
                      echo $tipo;
                      ?>
                    </div>
                  </div>
                </div>
                <div class="col-md-6 d-none">
                  <div class="info-group">
                    <label>Fecha de creación</label>
                    <div class="info-value">
                      <?= date('d/m/Y H:i', strtotime($this->boletaInfo->boleta_fecha_creacion)) ?>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Progreso de validación -->
              <div class="validation-progress mt-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="fw-bold">Progreso de validación</span>
                  <span class="text-muted"><?= $this->boletosValidados ?>/<?= $this->boletosTodos ?></span>
                </div>
                <div class="progress">
                  <div class="progress-bar bg-success" role="progressbar"
                    style="width: <?= ($this->boletosValidados / $this->boletosTodos) * 100 ?>%"
                    aria-valuenow="<?= $this->boletosValidados ?>" aria-valuemin="0"
                    aria-valuemax="<?= $this->boletosTodos ?>">
                  </div>
                </div>
                <div class="text-center mt-2">
                  <small class="text-muted">
                    <?= $this->boletosTodos - $this->boletosValidados ?> boleto(s) pendiente(s) de validar
                  </small>
                </div>
                <div class="text-center mt-2">
                  <!-- Lista simplificada de invitados -->
                  <div class="invitados-list-simple">
                    <h6 class="mb-3 text-start"><i class="fas fa-users me-2"></i>Lista de invitados</h6>
                    <?php foreach ($this->boletosInvitados as $key => $boleto) { ?>
                      <div
                        class="invitado-simple d-flex align-items-center justify-content-between mb-2 p-2 rounded bg-white border <?= $this->boletaInfo->boleta_id == $boleto->boleta_id ? 'boleta-actual' : '' ?>">
                        <div class="invitado-nombre">
                          <?= $boleto->invitado->invitadoReserva_nombre_invitado . ' ' . $boleto->invitado->invitadoReserva_apellido_invitado ?>

                        </div>
                        <div class="estado-validacion">
                          <?php if ($boleto->boleta_estado == 2): ?>
                            <span class="badge bg-success">
                              <i class="fas fa-check me-1"></i>Validado
                            </span>
                          <?php else: ?>
                            <span class="badge bg-warning text-dark">
                              <i class="fas fa-clock me-1"></i>Pendiente
                            </span>
                          <?php endif; ?>
                        </div>
                      </div>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Botón de validación -->
          <div class="validation-section">
            <?php
            // Debugging output to check the boletaInfo object
            // Uncomment the line below to see the structure of $this->boletaInfo
            // echo '<pre>'; print_r($this->boletaInfo); echo '</pre>';
            //print_r($this->boletaInfo);
            ?>
            <form action="/validacion/validar/registrar" class="w-100" id="formValidar">
              <input type="hidden" name="uid" value="<?= $this->boletaInfo->boleta_uid ?>">
              <input type="hidden" name="token" value="<?= $this->boletaInfo->boleta_token ?>">
              <input type="hidden" name="csrf" value="<?= $this->csrf ?>">
              <input type="hidden" name="csrf_section" value="<?= $this->csrf_section ?>">
              <input type="hidden" name="metodo_escaneado" value="<?= $this->metodo_escaneado ?? 'camera' ?>">
              <input type="hidden" name="documento_scan" value="<?= $this->documento_scan ?? null ?>">

              <button class="btn btn-validate w-100" type="submit" <?= $this->boletosTodos == $this->boletosValidados ? 'disabled' : '' ?>>
                <i class="fas fa-check-circle me-2"></i>
                <?= $this->boletosTodos == $this->boletosValidados ? 'Boleta ya validada' : 'Validar boleta' ?>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para mostrar el ambiente interactivo -->
<div class="modal fade" id="modalAmbienteInteractivo" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title p-3">
          <i class="fas fa-map-marker-alt me-2"></i>
          Ubicación de la mesa en el ambiente
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-3" style="height:80vh; position: relative;">
        <div id="loadingSpinnerAmbiente"
          style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 10; display: flex; justify-content: center; align-items: center;">
          <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Cargando ambiente...</span>
          </div>
        </div>
        <div id="ambienteContainer" style="width:100%; height:100%;">
          <!-- Aquí se cargará el ambiente interactivo -->
        </div>
      </div>
      <div class="modal-footer">
        <div class="d-flex justify-content-between w-100 align-items-center">
          <div class="text-muted">
            <i class="fas fa-info-circle me-1"></i>
            Mesa consultada resaltada en dorado, resto de elementos en gris
          </div>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalSubmit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog  modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <div class="modal-title-wrapper">
          <div class="modal-icon">
            <i class="fa-solid fa-check-to-slot"></i>
          </div>
          <div>
            <h5 class="modal-title mb-0" id="exampleModalLabel">Confirmar validación</h5>
            <p class="modal-subtitle mb-0">Validación de boleta de entrada</p>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center px-4 py-4">
        <div class="validation-info">
          <div class="ticket-preview">
            <div class="ticket-code">
              <i class="fas fa-ticket-alt me-2"></i>
              <span><?= $this->boletaInfo->boleta_uid ?></span>
            </div>
            <div class="ticket-details">
              <span class="detail-item">
                <i class="fas fa-user me-1"></i>
                <?= ($this->boletaInformacion->invitadoReserva_nombre_invitado ?? '') . ' ' . ($this->boletaInformacion->invitadoReserva_apellido_invitado ?? '') ?? ($this->reserva->reserva_nombre_cliente . ' ' . $this->reserva->reserva_apellido_cliente) ?>
              </span>
              <span class="detail-item">
                <i class="fas fa-calendar me-1"></i>
                <?= date('d/m/Y H:i', strtotime($this->evento->evento_fecha)) ?>
              </span>
            </div>
          </div>

          <div class="warning-section">
            <div class="warning-icon">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <p class="warning-text">
              <strong>¿Está seguro de validar esta boleta?</strong>
            </p>
            <p class="warning-description">
              Esta acción es <strong>irreversible</strong>. Una vez validada, la boleta no podrá ser utilizada
              nuevamente.
            </p>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 px-4 pb-4">
        <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i>Cancelar
        </button>
        <button type="submit" form="formValidar" <?= $this->boletosTodos == $this->boletosValidados ? 'disabled' : '' ?>
          class="btn btn-validate-confirm">
          <i class="fas fa-check-circle me-2"></i>Confirmar validación
        </button>
      </div>
    </div>
  </div>
</div>
<style>
  :root {
    --primary-color: #192a4b;
    --secondary-color: #f8f9fa;
    --accent-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --border-radius: 12px;
    --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
  }

  body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    height: auto;
  }

  /* Header Section */
  .header-section {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    /* position: sticky; */
    top: 0;
    z-index: 1000;
  }

  .btn-back {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
    box-shadow: var(--box-shadow);
  }

  .btn-back:hover {
    background: #1a365d;
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
  }

  .status-badge {
    background: var(--accent-color);
    color: white;
    padding: 8px 16px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 14px;
  }

  /* Main Content */
  .main-content {
    padding: 2rem 0;
  }

  /* Event Card */
  .event-card {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--box-shadow);

    width: calc((100vw - 30px) * 0.4167 - 15px);
    /* Aproximadamente col-lg-5 */
    max-width: 100%;
    transition: var(--transition);
    z-index: 100;
  }

  .event-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
  }

  .event-image {
    position: relative;
    overflow: hidden;
    height: 250px;
  }

  .event-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
  }

  .event-image:hover img {
    transform: scale(1.05);
  }

  .event-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, transparent 0%, rgba(0, 0, 0, 0.7) 100%);
    display: flex;
    align-items: flex-end;
    padding: 20px;
  }

  .event-status .badge {
    font-size: 12px;
    padding: 8px 12px;
    border-radius: 20px;
  }

  .event-details {
    padding: 1.5rem;
  }

  .event-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1rem;
    line-height: 1.2;
  }

  .event-info .info-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
    color: #666;
  }

  .event-info .info-item i {
    margin-right: 10px;
    color: var(--primary-color);
    width: 16px;
  }

  /* Info Cards */
  .info-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
    transition: var(--transition);
  }

  .info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
  }

  .info-card .card-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #2d3748 100%);
    color: white;
    padding: 1rem 1.5rem;
    border: none;
  }

  .info-card .card-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
  }

  .info-card .card-header-2 {
    /* background: linear-gradient(135deg, var(--primary-color) 0%, #2d3748 100%); */
    color: var(--primary-color);
    padding: 1rem 1.5rem;
    border: none;
  }

  .info-card .card-header-2 h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
  }

  .info-card .card-body {
    padding: 1rem;
  }

  /* Info Groups */
  .info-group {
    margin-bottom: 1rem;
  }

  .info-group label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #666;
    margin-bottom: 0.25rem;
    display: block;
    /* text-transform: uppercase; */
    letter-spacing: 0.5px;
  }

  .info-value {
    font-size: 1rem;
    font-weight: 500;
    color: var(--primary-color);
    background: #f8f9fa;
    padding: 8px 12px;
    border-radius: 6px;
    border-left: 3px solid var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .code-value {
    font-family: 'Monaco', 'Menlo', monospace;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-left: 3px solid #ffd700;
  }

  /* Stat Cards */
  .stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    transition: var(--transition);
    border: 1px solid #e2e8f0;
  }

  .stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
  }

  .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    color: white;
    font-size: 1.25rem;
  }

  .stat-label {
    font-size: 0.875rem;
    color: #666;
    font-weight: 500;
    margin-bottom: 0.25rem;
  }

  .stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
  }

  /* Stat Cards Mini */
  .stat-card-mini {
    background: white;
    border-radius: 8px;
    padding: 10px 5px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transition: var(--transition);
    border: 1px solid #e2e8f0;
    height: 100%;
  }

  .stat-card-mini:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .stat-icon-mini {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
    color: white;
    font-size: 0.875rem;
  }

  .stat-label-mini {
    font-size: 0.75rem;
    color: #666;
    font-weight: 500;
    margin-bottom: 0.25rem;
  }

  .stat-value-mini {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--primary-color);
  }

  /* Progress Bar */
  .validation-progress {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: var(--border-radius);
    border: 1px solid #e2e8f0;
  }

  .progress {
    height: 12px;
    border-radius: 10px;
    overflow: hidden;
    background: #e2e8f0;
  }

  .progress-bar {
    transition: width 0.6s ease;
    border-radius: 10px;
  }

  /* Validation Button */
  .validation-section {
    background: white;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    position: sticky;
    bottom: 20px;
  }

  .btn-validate {
    background: linear-gradient(135deg, var(--accent-color) 0%, #20c997 100%);
    border: none;
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
    padding: 15px 30px;
    border-radius: var(--border-radius);
    transition: var(--transition);
    box-shadow: var(--box-shadow);
  }

  .btn-validate:hover:not(:disabled) {
    background: linear-gradient(135deg, #20c997 0%, var(--accent-color) 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
  }

  .btn-validate:disabled {
    background: #6c757d;
    color: white;
    cursor: not-allowed;
    opacity: 0.6;
  }

  /* Modal Improvements */
  .modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    background: white;
  }

  .modal-dialog {
    /* max-width: 550px; */
  }

  .modal-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #2d3748 100%);
    color: white;
    border: none;
    padding: 0;
    position: relative;
    overflow: hidden;
  }

  .modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
    opacity: 0.1;
  }

  .modal-title-wrapper {
    display: flex;
    align-items: center;
    padding: 1.5rem 2rem;
    position: relative;
    z-index: 1;
  }

  .modal-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .modal-icon i {
    font-size: 24px;
    color: #fff;
  }

  .modal-title {
    font-weight: 700;
    font-size: 1.25rem;
    margin: 0;
    color: white;
  }

  .modal-subtitle {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    font-weight: 400;
  }

  .btn-close {
    filter: brightness(0) invert(1);
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 2;
    /* background: rgba(255, 255, 255, 0.1); */
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.8;
    transition: all 0.3s ease;
  }

  .btn-close:hover {
    opacity: 1;
    /* background: rgba(255, 255, 255, 0.2); */
    transform: scale(1.1);
  }

  .modal-body {
    padding: 2rem;
    background: #f8f9fa;
  }

  .validation-info {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  .ticket-preview {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    position: relative;
    overflow: hidden;
  }

  .ticket-preview::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
  }

  .ticket-code {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 10px;
    margin-bottom: 1rem;
    font-family: 'Monaco', 'Menlo', monospace;
    font-weight: 600;
    font-size: 1.1rem;
    letter-spacing: 1px;
  }

  .ticket-code i {
    margin-right: 10px;
    font-size: 1.2rem;
  }

  .ticket-details {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
  }

  .detail-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
    color: #4a5568;
    font-size: 0.95rem;
  }

  .detail-item i {
    margin-right: 12px;
    color: var(--primary-color);
    width: 16px;
    text-align: center;
  }

  .warning-section {
    background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
    border: 1px solid #feb2b2;
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
    position: relative;
  }

  .warning-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    color: white;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    box-shadow: 0 4px 15px rgba(245, 101, 101, 0.3);
  }

  .warning-icon i {
    font-size: 1.25rem;
  }

  .warning-text {
    font-size: 1.1rem;
    font-weight: 600;
    color: #742a2a;
    margin-bottom: 0.5rem;
  }

  .warning-description {
    font-size: 0.95rem;
    color: #975a5a;
    line-height: 1.5;
    margin: 0;
  }

  .modal-footer {
    background: white;
    border: none;
    padding: 1.5rem 2rem;
    display: flex;
    /* gap: 1rem; */
    justify-content: space-between;
  }

  .btn-secondary-custom {
    background: #e2e8f0;
    color: #4a5568;
    border: none;
    padding: 12px 0px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    flex: 1;
    /* max-width: 180px; */
  }

  .btn-secondary-custom:hover {
    background: #cbd5e0;
    color: #2d3748;
    transform: translateY(-1px);
  }

  .btn-validate-confirm {
    background: linear-gradient(135deg, var(--accent-color) 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    flex: 1;
    /* max-width: 220px; */
    position: relative;
    overflow: hidden;
  }

  .btn-validate-confirm:hover:not(:disabled) {
    background: linear-gradient(135deg, #20c997 0%, var(--accent-color) 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
  }

  .btn-validate-confirm:disabled {
    background: #9ca3af;
    color: white;
    cursor: not-allowed;
    opacity: 0.7;
  }

  .btn-validate-confirm::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
  }

  .btn-validate-confirm:hover:not(:disabled)::before {
    left: 100%;
  }

  /* Animaciones del modal */
  .modal.fade .modal-dialog {
    transform: scale(0.8) translateY(-50px);
    transition: all 0.3s ease;
  }

  .modal.show .modal-dialog {
    transform: scale(1) translateY(0);
  }

  /* Responsive para modal */
  @media (max-width: 576px) {
    .modal-dialog {
      margin: 1rem;
      max-width: calc(100vw - 2rem);
    }

    .modal-title-wrapper {
      padding: 1rem 1.5rem;
    }

    .modal-icon {
      width: 50px;
      height: 50px;
    }

    .modal-icon i {
      font-size: 20px;
    }

    .modal-body {
      padding: 1.5rem;
    }

    .modal-footer {
      padding: 1rem 1.5rem;
      flex-direction: column;
    }

    .btn-secondary-custom,
    .btn-validate-confirm {
      max-width: none;
      width: 100%;
    }
  }

  /* Badges */
  .badge {
    font-size: 0.875rem;
    padding: 6px 12px;
    border-radius: 20px;
  }

  /* Responsive Design */
  @media (max-width: 1200.98px) {
    .event-card {
      position: static;
      margin-bottom: 2rem;
      width: 100%;
      max-width: none;
    }

    .validation-section {
      position: fixed;
      bottom: 45px;
      left: 0;
      right: 0;
      margin: 0;
      border-radius: 0;
      z-index: 1000;
      padding: 15px;
    }

    .main-content {
      padding-bottom: 100px;
    }

    .stat-card,
    .stat-card-mini {
      margin-bottom: 1rem;
    }
  }

  @media (max-width: 767.98px) {
    .main-content {
      padding: 1rem 0;
    }

    .info-card .card-body {
      padding: 1rem;
    }

    .event-details {
      padding: 1rem;
    }

    .stat-icon,
    .stat-icon-mini {
      width: 40px;
      height: 40px;
      font-size: 1rem;
    }

    .stat-value {
      font-size: 1.25rem;
    }

    .stat-value-mini {
      font-size: 1rem;
    }

    body {
      padding-bottom: 100px;
    }

    footer {
      position: absolute;
    }
  }

  /* Animations */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .info-card,
  .event-card,
  .validation-section {
    animation: fadeInUp 0.6s ease-out;
  }

  /* Copy button styling */
  .btn-outline-secondary {
    border: 1px solid #dee2e6;
    color: #FFF;
    background: transparent;
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 0.75rem;
    transition: var(--transition);
  }

  .btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
  }

  /* Estilo para botón de ubicación */
  .btn-show-location {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
  }

  .btn-show-location:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
  }

  /* Estilos para la lista simplificada de invitados */
  .invitados-list-simple {
    max-width: 100%;
  }

  .invitado-simple {
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
    background: white !important;
  }

  .invitado-simple:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
    transform: translateY(-1px);
    border-color: #cbd5e0;
  }

  .invitado-nombre {
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--primary-color) !important;
  }

  .estado-validacion .badge {
    font-size: 0.8rem;
    padding: 4px 8px;
  }

  /* Estilo especial para boleta actual */
  .boleta-actual {
    border: 2px solid #007bff !important;
    background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%) !important;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2) !important;
    position: relative;
  }

  .boleta-actual::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #007bff, #0056b3);
    border-radius: 6px;
    z-index: -1;
    opacity: 0.1;
  }

  .boleta-actual:hover {
    box-shadow: 0 6px 20px rgba(0, 123, 255, 0.3) !important;
    transform: translateY(-2px);
  }

  /* Responsive para lista simplificada */
  @media (max-width: 767.98px) {
    .invitado-simple {
      flex-direction: column;
      text-align: center;
      gap: 0.5rem;
    }
  }
</style>
<script>
  $(document).ready(function() {
    // Animación de entrada para las tarjetas
    $('.info-card, .event-card').each(function(index) {
      $(this).css('animation-delay', (index * 0.1) + 's');
    });

    // Manejo del formulario de validación
    $("#formValidar").on("submit", function(e) {
      console.log("Formulario enviado");
      e.preventDefault();
      $(".loader-bx").addClass("show");

      let data = $(this).serialize();
      $.ajax({
        url: $(this).attr("action"),
        type: $(this).attr("method"),
        data: data,
        dataType: "json",
        success: function(response) {
          switch (response.status) {
            case "success":
              Swal.fire({
                icon: "success",
                title: "¡Perfecto!",
                text: response.message,
                confirmButtonColor: "#28a745",
                confirmButtonText: "Continuar",
                showClass: {
                  popup: 'animate__animated animate__fadeInUp'
                },
                hideClass: {
                  popup: 'animate__animated animate__fadeOutDown'
                }
              }).then(function() {
                window.location.href = response.redirect;
              });
              break;
            case "error":
              Swal.fire({
                icon: "error",
                title: "¡Ups! Algo salió mal",
                text: response.message,
                confirmButtonColor: "#dc3545",
                confirmButtonText: "Entendido",
                showClass: {
                  popup: 'animate__animated animate__shakeX'
                }
              }).then(function() {
                window.location.href = response.redirect;
              });
              break;
          }
        },
        error: function(xhr, status, error) {
          console.error("Error en la solicitud:", error);
          Swal.fire({
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor. Por favor, verifique su conexión e intente nuevamente.",
            confirmButtonColor: "#dc3545",
            confirmButtonText: "Reintentar"
          });
        },
        complete: function() {
          $(".loader-bx").removeClass("show");
        },
      });
    });

    // Efecto hover para las tarjetas estadísticas
    $('.stat-card, .stat-card-mini').hover(
      function() {
        $(this).find('.stat-icon, .stat-icon-mini').addClass('animate__animated animate__pulse');
      },
      function() {
        $(this).find('.stat-icon, .stat-icon-mini').removeClass('animate__animated animate__pulse');
      }
    );

    // Animación de la barra de progreso
    setTimeout(function() {
      $('.progress-bar').addClass('animate__animated animate__fadeInLeft');
    }, 500);
  });

  // Función para copiar al portapapeles
  function copyToClipboard(text) {
    if (navigator.clipboard && window.isSecureContext) {
      // Método moderno para navegadores compatibles
      navigator.clipboard.writeText(text).then(function() {
        showCopyNotification('¡Código copiado al portapapeles!');
      }).catch(function(err) {
        console.error('Error al copiar: ', err);
        fallbackCopyTextToClipboard(text);
      });
    } else {
      // Método de respaldo para navegadores más antiguos
      fallbackCopyTextToClipboard(text);
    }
  }

  // Método de respaldo para copiar texto
  function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
      const successful = document.execCommand('copy');
      if (successful) {
        showCopyNotification('¡Código copiado al portapapeles!');
      } else {
        showCopyNotification('No se pudo copiar el código', 'error');
      }
    } catch (err) {
      console.error('Error al copiar: ', err);
      showCopyNotification('Error al copiar el código', 'error');
    }

    document.body.removeChild(textArea);
  }

  // Función para mostrar notificación de copiado
  function showCopyNotification(message, type = 'success') {
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 2000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    });

    Toast.fire({
      icon: type,
      title: message
    });
  }

  // Efecto de parallax suave en scroll
  // $(window).scroll(function () {
  //   const scrolled = $(this).scrollTop();
  //   const parallax = $('.event-image img');
  //   const speed = 0.5;

  //   parallax.css('transform', 'translateY(' + (scrolled * speed) + 'px)');
  // });

  // Contador animado para las estadísticas
  // function animateCounters () {
  //   $('.stat-value, .stat-value-mini').each(function () {
  //     const $this = $(this);
  //     const text = $this.text();
  //     const number = parseInt(text.replace(/\D/g, ''));

  //     if (!isNaN(number) && number > 0) {
  //       $this.prop('Counter', 0).animate({
  //         Counter: number
  //       }, {
  //         duration: 1500,
  //         easing: 'swing',
  //         step: function (now) {
  //           if (text.includes('$')) {
  //             $this.text('$' + Math.ceil(now).toLocaleString());
  //           } else {
  //             $this.text(Math.ceil(now));
  //           }
  //         }
  //       });
  //     }
  //   });
  // }

  // Inicializar contadores cuando las tarjetas son visibles
  // const observer = new IntersectionObserver((entries) => {
  //   entries.forEach(entry => {
  //     if (entry.isIntersecting) {
  //       animateCounters();
  //       observer.unobserve(entry.target);
  //     }
  //   });
  // });

  // $('.stat-card, .stat-card-mini').each(function () {
  //   observer.observe(this);
  // });

  // Manejar clic en botón "Ver ubicación"
  $('.btn-show-location').on('click', function() {
    const mesaId = $(this).data('mesa-id');
    const ambienteId = $(this).data('ambiente-id');

    if (!mesaId || !ambienteId) {
      showCopyNotification('Error: No se pudo obtener la información de la mesa', 'error');
      return;
    }

    mostrarAmbienteInteractivo(mesaId, ambienteId);
  });

  function mostrarAmbienteInteractivo(mesaId, ambienteId) {
    const modal = new bootstrap.Modal(document.getElementById('modalAmbienteInteractivo'));
    const spinner = document.getElementById('loadingSpinnerAmbiente');
    const container = document.getElementById('ambienteContainer');

    // Mostrar el spinner
    spinner.style.display = 'flex';
    container.innerHTML = '';

    // Construir la URL para cargar el ambiente con la mesa resaltada
    const url = `/administracion/ambientes/manage?id=${ambienteId}&display=1&destacar_mesa=${mesaId}&modo=validacion`;

    // Crear iframe para cargar el ambiente
    const iframe = document.createElement('iframe');
    iframe.src = url;
    iframe.style.width = '100%';
    iframe.style.height = '100%';
    iframe.style.border = 'none';
    iframe.style.borderRadius = '8px';

    // Manejar carga del iframe
    iframe.onload = function() {
      spinner.style.display = 'none';

      // Agregar estilos personalizados al iframe para resaltar la mesa
      try {
        const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        if (iframeDoc) {
          // Crear estilo para el modo validación con todos los elementos en gris
          const style = iframeDoc.createElement('style');
          style.textContent = `
            /* Todos los elementos en gris por defecto */
            .elemento {
              background: #9e9e9e !important;
              color: #666 !important;
              border: 1px solid #bdbdbd !important;
              opacity: 0.6 !important;
              pointer-events: none !important;
              cursor: default !important;
            }
            
            /* Solo la mesa consultada en color y resaltada */
            .elemento[data-mesa-id="${mesaId}"] {
              background: linear-gradient(45deg, #FFD700, #FFA500) !important;
              border: 3px solid #FF6B35 !important;
              box-shadow: 0 0 20px rgba(255, 215, 0, 0.8) !important;
              animation: pulseHighlight 2s infinite !important;
              z-index: 1000 !important;
              color: #333 !important;
              font-weight: bold !important;
              opacity: 1 !important;
            }
            
            @keyframes pulseHighlight {
              0%, 100% { 
                box-shadow: 0 0 20px rgba(255, 215, 0, 0.8);
                transform: scale(1);
              }
              50% { 
                box-shadow: 0 0 30px rgba(255, 215, 0, 1);
                transform: scale(1.05);
              }
            }
            
            .grid-cell {
              pointer-events: none !important;
            }
            
            .modal-mesa-bg,
            .btn-warning,
            .btn-primary {
              display: none !important;
            }
          `;
          iframeDoc.head.appendChild(style);
        }
      } catch (e) {
        console.log('No se pudo acceder al contenido del iframe para aplicar estilos personalizados');
      }
    };

    iframe.onerror = function() {
      spinner.style.display = 'none';
      container.innerHTML = '<div class="text-center p-4"><div class="alert alert-danger">Error al cargar el ambiente interactivo.</div></div>';
    };

    container.appendChild(iframe);

    // Mostrar el modal
    modal.show();
  }

  // Limpiar el iframe al cerrar el modal
  document.getElementById('modalAmbienteInteractivo').addEventListener('hidden.bs.modal', function() {
    const container = document.getElementById('ambienteContainer');
    const spinner = document.getElementById('loadingSpinnerAmbiente');
    container.innerHTML = '';
    spinner.style.display = 'none';
  });
</script>