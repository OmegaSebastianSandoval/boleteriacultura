<style>
  .btn-menu {
    display: none;
  }
</style>
<?php
$meses = [
  'January' => 'enero',
  'February' => 'febrero',
  'March' => 'marzo',
  'April' => 'abril',
  'May' => 'mayo',
  'June' => 'junio',
  'July' => 'julio',
  'August' => 'agosto',
  'September' => 'septiembre',
  'October' => 'octubre',
  'November' => 'noviembre',
  'December' => 'diciembre'
];

// Formatear fecha si existe
$fechaFormateada = '';
if (($this->reserva) && ($this->reserva->boleta_compra_fecha)) {
  $fecha = new DateTime($this->reserva->boleta_compra_fecha);
  $dia = $fecha->format('d');
  $mes = $meses[$fecha->format('F')];
  $anio = $fecha->format('Y');
  $fechaFormateada = "$dia de $mes de $anio";
}
?>

<div class="container-fluid p-0">
  <!-- Header con navegación -->
  <div class="header-section">
    <div class="container">
      <div class="d-flex justify-content-between align-items-center py-3">
        <a href="/validacion/evento" class="btn-back">
          <i class="fas fa-arrow-left me-2"></i>Volver al inicio
        </a>
        <div class="status-badge bg-danger d-none">
          <i class="fas fa-exclamation-triangle me-1"></i>
          Error de validación
        </div>
      </div>
    </div>
  </div>

  <div class="main-content">
    <div class="container">
      <div class="row g-4 justify-content-center">
        <?php switch ($this->tipoError) {
          case '1': ?>
            <!-- Error: Boleta no encontrada -->
            <div class="col-12 col-lg-8">
              <div class="error-card">
                <div class="error-header">
                  <div class="error-icon bg-danger">
                    <i class="fas fa-search"></i>
                  </div>
                  <div>
                    <h1 class="error-title">Boleta no encontrada</h1>
                    <p class="error-subtitle">La boleta no se encuentra en la base de datos</p>
                  </div>
                </div>

                <div class="error-body">
                  <div class="alert alert-danger">
                    <h4><i class="fas fa-info-circle me-2"></i>¿Qué significa este error?</h4>
                    <p class="mb-2">La boleta que está intentando validar no existe en nuestro sistema.</p>
                    <p class="mb-0">Esto puede ocurrir por:</p>
                    <ul class="mt-2 mb-0">
                      <li>Código de boleta incorrecto</li>
                      <li>Boleta de otro evento</li>
                      <li>Error en el escaneo del código QR</li>
                    </ul>
                  </div>

                  <div class="action-section">
                    <h5><i class="fas fa-lightbulb me-2"></i>Soluciones sugeridas:</h5>
                    <div class="row g-3">
                      <div class="col-md-6">
                        <div class="suggestion-card">
                          <i class="fas fa-qrcode"></i>
                          <h6>Verificar código QR</h6>
                          <p>Escanee nuevamente el código QR de la boleta</p>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="suggestion-card">
                          <i class="fas fa-keyboard"></i>
                          <h6>Ingresar manualmente</h6>
                          <p>Digite el código de la boleta cuidadosamente</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php break; ?>

          <?php case '2': ?>
            <!-- Error: Boleta ya validada -->
            <div class="col-12 col-lg-10">
              <div class="error-card">
                <div class="error-header">
                  <div class="error-icon bg-primary">
                    <i class="fas fa-check-circle"></i>
                  </div>
                  <div>
                    <h1 class="error-title">Boleta ya validada</h1>
                    <p class="error-subtitle">Esta boleta fue validada anteriormente</p>
                  </div>
                </div>

                <div class="error-body">
                  <!-- Información del evento -->
                  <?php if (($this->evento)): ?>
                    <div class="info-card mb-4 d-none">
                      <div class="card-header bg-primary">
                        <h3><i class="fas fa-calendar-alt me-2"></i>Información del evento</h3>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-4">
                            <?php if ($this->evento->evento_imagen_evento): ?>
                              <div class="event-image">
                                <img src="/images/<?= $this->evento->evento_imagen_evento ?>" alt="Evento" class="img-fluid">
                              </div>
                            <?php endif; ?>
                          </div>
                          <div class="col-md-8">
                            <h4 class="event-title"><?= $this->evento->evento_titulo ?></h4>
                            <div class="info-grid">
                              <div class="info-group">
                                <label>Fecha del evento</label>
                                <div class="info-value">
                                  <?= date('d/m/Y H:i', strtotime($this->evento->evento_fecha)) ?>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>

                  <!-- Estado de validación -->
                  <div class="alert alert-primary">
                    <div class="row align-items-center">
                      <div class="col-md-12">
                        <h4 class="mb-2">
                          <i class="fas fa-check-circle text-primary me-2"></i>
                          Estado: <span class="text-primary fw-bold"><?= $this->estadoBoleto ?></span>
                        </h4>
                        <p class="mb-2">
                          <i class="fas fa-calendar me-2"></i>
                          <strong>Fecha de validación:</strong>
                          <?= date('d/m/Y H:i', strtotime($this->boletaInfo->boleta_fecha_validacion)) ?>
                        </p>
                        <p class="mb-2">
                          <i class="fas fa-user me-2"></i>
                          <strong>Usuario validador:</strong>
                          <?= $this->usuarioValidador->user_user ?>
                        </p>
                        <p class="mb-2">
                          <i class="fas fa-laptop me-2"></i>
                          <strong>Dispositivo:</strong>
                          <?= $this->boletaInfo->boleta_dispositivo_validacion ?>
                        </p>
                        <?php if ($this->invitado->documento_invitado) { ?>
                          <hr>

                          <p class="mb-2">
                            <i class="fas fa-user me-2"></i>
                            <strong>Invitado:</strong>
                            <?= $this->invitado->invitadoReserva_nombre_invitado . ' ' . $this->invitado->invitadoReserva_apellido_invitado ?>
                          </p>
                        <?php } ?>
                        <?php if ($this->invitado->documento_invitado) { ?>
                          <p class="mb-2">
                            <i class="fas fa-id-card me-2"></i>
                            <strong>Documento:</strong>
                            <?= $this->invitado->documento_invitado ?>
                          </p>
                          <hr>

                        <?php } ?>
                        <p class="mb-0">
                          <i class="fas fa-info-circle me-2"></i>
                          Esta boleta ya fue validada anteriormente y no puede ser validada nuevamente.
                        </p>
                      </div>
                      <!-- <div class="col-md-4 text-center">
                        <div class="validation-status">
                          <i class="fas fa-shield-check text-primary"></i>
                          <div class="status-text">Validación completada</div>
                        </div>
                      </div> -->
                    </div>
                  </div>
                  <!-- Información de la reserva -->
                  <?php if (($this->reserva)): ?>
                    <div class="info-card mb-4">
                      <div class="card-header bg-info">
                        <h3><i class="fas fa-clipboard-list me-2"></i>Detalles de la reserva</h3>
                      </div>
                      <div class="card-body">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <div class="info-group">
                              <label>ID de reserva</label>
                              <div class="info-value code-value"><?= $this->reserva->id ?></div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="info-group">
                              <label>Código de boleta</label>
                              <div class="info-value code-value"><?= $this->boletaInfo->boleta_uid ?></div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php endif; ?>
                  <!-- Botón para ver ubicación -->
                  <?php if (($this->mesaInfo) && $this->mesaInfo->mesa_id && $this->mesaInfo->mesa_ambiente): ?>
                    <div class="row g-3 mb-4">
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
                  <?php endif; ?>
                  <!-- Información de ubicación -->
                  <div class="row g-3 mb-4">
                    <div class="col-4">
                      <div class="stat-card-mini">
                        <div class="stat-icon-mini bg-primary">
                          <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-content-mini">
                          <div class="stat-label-mini">Ambiente</div>
                          <div class="stat-value-mini">
                            <?= ($this->mesaInfo->ambiente_nombre) ? $this->mesaInfo->ambiente_nombre : 'No disponible' ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="stat-card-mini">
                        <div class="stat-icon-mini bg-info">
                          <i class="fas fa-chair"></i>
                        </div>
                        <div class="stat-content-mini">
                          <div class="stat-label-mini"><?= (($this->mesaInfo->mesa_tipo) && $this->mesaInfo->mesa_tipo === 'silla') ? 'Silla asignada' : 'Mesa asignada' ?></div>
                          <div class="stat-value-mini">
                            <?= ($this->mesaInfo->mesa_nombre) ? $this->mesaInfo->mesa_nombre : 'No disponible' ?>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-4">
                      <div class="stat-card-mini">
                        <div class="stat-icon-mini bg-success">
                          <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="stat-content-mini">
                          <div class="stat-label-mini">Piso</div>
                          <div class="stat-value-mini">
                            <?= ($this->mesaInfo->piso_nombre) ? $this->mesaInfo->piso_nombre : 'No disponible' ?>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>


                </div>

              </div>
            </div>
          </div>
          <?php break; ?>

        <?php case '3': ?>
          <!-- Error: No asociada a reserva -->
          <div class="col-12 col-lg-8">
            <div class="error-card">
              <div class="error-header">
                <div class="error-icon bg-danger">
                  <i class="fas fa-unlink"></i>
                </div>
                <div>
                  <h1 class="error-title">Error de asociación</h1>
                  <p class="error-subtitle">La boleta no está asociada a una reserva válida</p>
                </div>
              </div>

              <div class="error-body">
                <div class="alert alert-danger">
                  <h4><i class="fas fa-exclamation-triangle me-2"></i>Error de sistema</h4>
                  <p class="mb-2">La boleta existe pero no se encuentra asociada a una reserva válida en el sistema.</p>
                  <p class="mb-0">Este es un error de configuración que requiere asistencia técnica.</p>
                </div>

                <div class="action-section">
                  <h5><i class="fas fa-phone me-2"></i>Contactar soporte:</h5>
                  <div class="support-info">
                    <div class="support-card">
                      <i class="fas fa-headset"></i>
                      <div>
                        <h6>Asistencia técnica</h6>
                        <p>Contacte al administrador del sistema para resolver este problema</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php break; ?>

        <?php default: ?>
          <!-- Error genérico -->
          <div class="col-12 col-lg-8">
            <div class="error-card">
              <div class="error-header">
                <div class="error-icon bg-danger">
                  <i class="fas fa-bug"></i>
                </div>
                <div>
                  <h1 class="error-title">Error inesperado</h1>
                  <p class="error-subtitle">Ha ocurrido un error inesperado durante la validación</p>
                </div>
              </div>

              <div class="error-body">
                <div class="alert alert-danger">
                  <h4><i class="fas fa-exclamation-circle me-2"></i>Error desconocido</h4>
                  <p class="mb-2">Se ha producido un error inesperado al procesar la validación de la boleta.</p>
                  <p class="mb-0">Por favor, intente nuevamente o contacte al soporte técnico.</p>
                </div>

                <div class="action-section">
                  <h5><i class="fas fa-redo me-2"></i>Intentar nuevamente:</h5>
                  <div class="row g-3">
                    <div class="col-md-6">
                      <div class="suggestion-card">
                        <i class="fas fa-refresh"></i>
                        <h6>Recargar página</h6>
                        <p>Actualice la página e intente nuevamente</p>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="suggestion-card">
                        <i class="fas fa-qrcode"></i>
                        <h6>Escanear de nuevo</h6>
                        <p>Vuelva a escanear el código QR de la boleta</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php break; ?>
      <?php } ?>
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
    /* min-height: 100dvh; */
    height: auto;
    display: block;
  }

  .main-content {
    min-height: calc(100dvh - 196.5px);
  }

  /* Header Section */
  .header-section {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
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

  /* Error Card */
  .error-card {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
  }

  .error-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
  }

  .error-header {
    background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
    color: white;
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }

  .error-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
    background-color: var(--primary-color) !important;
  }

  .error-title {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
  }

  .error-subtitle {
    font-size: 1rem;
    margin: 0.5rem 0 0 0;
    opacity: 0.9;
  }

  .error-body {
    padding: 2rem;
  }

  /* Info Cards */
  .info-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: var(--transition);
  }

  .info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
  }

  .info-card .card-header {
    color: white;
    padding: 1rem 1.5rem;
    border: none;
    background-color: var(--primary-color) !important;
  }

  .info-card .card-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
  }

  .info-card .card-body {
    padding: 1.5rem;
  }

  /* Event Image */
  .event-image {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  }

  .event-image img {
    width: 100%;
    height: auto;
    object-fit: cover;
  }

  .event-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 1rem;
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
  }

  .code-value {
    font-family: 'Monaco', 'Menlo', monospace;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-left: 3px solid #ffd700;
  }

  /* Action Section */
  .action-section {
    margin-top: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
  }

  .action-section h5 {
    color: var(--primary-color);
    font-weight: 600;
    margin-bottom: 1rem;
  }

  /* Suggestion Cards */
  .suggestion-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: var(--transition);
    height: 100%;
  }

  .suggestion-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
  }

  .suggestion-card i {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 1rem;
  }

  .suggestion-card h6 {
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
  }

  .suggestion-card p {
    color: #666;
    font-size: 0.9rem;
    margin: 0;
  }

  /* Support Info */
  .support-info {
    margin-top: 1rem;
  }

  .support-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  .support-card i {
    font-size: 2rem;
    color: var(--danger-color);
    flex-shrink: 0;
  }

  .support-card h6 {
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 0.25rem;
  }

  .support-card p {
    color: #666;
    margin: 0;
  }

  /* Validation Status */
  .validation-status {
    text-align: center;
  }

  .validation-status i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
  }

  .status-text {
    font-weight: 600;
    color: var(--primary-color);
  }

  /* Alert Improvements */
  .alert {
    border: none;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  .alert h4 {
    font-weight: 600;
    margin-bottom: 1rem;
  }

  .alert-danger {
    background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%);
    color: #c62828;
    border-left: 4px solid var(--danger-color);
  }

  .alert-primary {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    color: var(--primary-color);
    border-left: 4px solid var(--primary-color);
  }

  .alert-warning {
    background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
    color: #f57c00;
    border-left: 4px solid var(--warning-color);
  }

  /* Stat Cards Mini */
  .stat-card-mini {
    background: white;
    border-radius: 8px;
    padding: 10px 5px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
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

  /* Info Grid */
  .info-grid {
    display: grid;
    gap: 1rem;
  }

  /* Responsive Design */
  @media (max-width: 767.98px) {
    .main-content {
      padding: 1rem 0;
    }

    .error-header {
      padding: 1.5rem;
      flex-direction: column;
      text-align: center;
      gap: 1rem;
    }

    .error-body {
      padding: 1.5rem;
    }

    .error-title {
      font-size: 1.5rem;
    }

    .suggestion-card,
    .support-card {
      margin-bottom: 1rem;
    }

    .support-card {
      flex-direction: column;
      text-align: center;
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

  .error-card,
  .info-card {
    animation: fadeInUp 0.6s ease-out;
  }
</style>

<script>
  $(document).ready(function () {
    // Manejar clic en botón "Ver ubicación"
    $('.btn-show-location').on('click', function () {
      const mesaId = $(this).data('mesa-id');
      const ambienteId = $(this).data('ambiente-id');

      if (!mesaId || !ambienteId) {
        showCopyNotification('Error: No se pudo obtener la información de la mesa', 'error');
        return;
      }

      mostrarAmbienteInteractivo(mesaId, ambienteId);
    });
  });

  function mostrarAmbienteInteractivo (mesaId, ambienteId) {
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
    iframe.onload = function () {
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

    iframe.onerror = function () {
      spinner.style.display = 'none';
      container.innerHTML = '<div class="text-center p-4"><div class="alert alert-danger">Error al cargar el ambiente interactivo.</div></div>';
    };

    container.appendChild(iframe);

    // Mostrar el modal
    modal.show();
  }

  // Limpiar el iframe al cerrar el modal
  document.getElementById('modalAmbienteInteractivo').addEventListener('hidden.bs.modal', function () {
    const container = document.getElementById('ambienteContainer');
    const spinner = document.getElementById('loadingSpinnerAmbiente');
    container.innerHTML = '';
    spinner.style.display = 'none';
  });

  // Función para mostrar notificación (si no existe)
  function showCopyNotification (message, type = 'success') {
    // Crear el elemento de notificación
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : 'success'} position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    notification.innerHTML = `
      <div class="d-flex align-items-center">
        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
      </div>
    `;

    // Agregar al DOM
    document.body.appendChild(notification);

    // Auto-remover después de 3 segundos
    setTimeout(() => {
      if (notification && notification.parentNode) {
        notification.remove();
      }
    }, 3000);
  }
</script>