<h1 class="titulo-principal"><i class="fas fa-stream"></i> <?php echo $this->titlesection; ?></h1>

<div class="container-fluid">
  <!-- Filtros -->
  <form action="<?php echo $this->route; ?>/sesiones" method="post">
    <div class="content-dashboard">
      <div class="row">
        <div class="col-md-2">
          <div class="form-group">
            <label for="fecha_desde">Fecha Desde:</label>
            <input type="date" class="form-control" name="fecha_desde" id="fecha_desde"
              value="<?php echo $this->fechaDesde; ?>">
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-group">
            <label for="fecha_hasta">Fecha Hasta:</label>
            <input type="date" class="form-control" name="fecha_hasta" id="fecha_hasta"
              value="<?php echo $this->fechaHasta; ?>">
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-group">
            <label for="usuario">Usuario:</label>
            <input type="text" class="form-control" name="usuario" id="usuario"
              value="<?php echo $this->usuarioFiltro; ?>" placeholder="Nombre de usuario">
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-group">
            <label for="metodo">Método:</label>
            <select class="form-control" name="metodo" id="metodo">
              <option value="">Todos los métodos</option>
              <option value="camera" <?php echo ($this->metodoFiltro == 'camera') ? 'selected' : ''; ?>>Cámara</option>
              <option value="usb_scanner" <?php echo ($this->metodoFiltro == 'usb_scanner') ? 'selected' : ''; ?>>Scanner
                USB</option>
              <option value="manual" <?php echo ($this->metodoFiltro == 'manual') ? 'selected' : ''; ?>>Manual</option>
            </select>
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-group">
            <label for="resultado">Estado:</label>
            <select class="form-control" name="resultado" id="resultado">
              <option value="">Todos los estados</option>
              <option value="exitoso" <?php echo ($this->resultadoFiltro == 'exitoso') ? 'selected' : ''; ?>>Completadas
              </option>
              <option value="fallido" <?php echo ($this->resultadoFiltro == 'fallido') ? 'selected' : ''; ?>>Fallidas
              </option>
            </select>
          </div>
        </div>

        <div class="col-md-2">
          <div class="form-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-primary btn-block">
              <i class="fas fa-filter"></i> Filtrar
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- Navegación entre vistas -->
  <div class="content-dashboard mt-3">
    <div class="row">
      <div class="col-12">
        <nav>
          <div class="nav nav-pills justify-content-center" role="tablist">
            <a class="nav-link" href="<?php echo $this->route; ?>/escaneologs">
              <i class="fas fa-list"></i> Vista de Logs
            </a>
            <a class="nav-link active" href="<?php echo $this->route; ?>/sesiones">
              <i class="fas fa-stream"></i> Vista de Sesiones
            </a>
          </div>
        </nav>
      </div>
    </div>
  </div>

  <!-- Estadísticas rápidas -->
  <?php if ($this->estadisticas && count($this->estadisticas) > 0): ?>
    <div class="content-dashboard mt-3">
      <div class="row">
        <?php foreach ($this->estadisticas as $stat): ?>
          <div class="col-md-3">
            <div class="card bg-light">
              <div class="card-body text-center">
                <h5 class="card-title">
                  <?php echo ucfirst($stat->auditoriaboleta_metodo_escaneado); ?>
                </h5>
                <div class="row">
                  <div class="col-6">
                    <h6 class="text-primary"><?php echo $stat->total_sesiones; ?></h6>
                    <small>Sesiones</small>
                  </div>
                  <div class="col-6">
                    <h6 class="text-success"><?php echo $stat->sesiones_exitosas; ?></h6>
                    <small>Exitosas</small>
                  </div>
                </div>
                <small class="text-muted">
                  Promedio: <?php echo round($stat->promedio_acciones_por_sesion, 1); ?> acciones/sesión
                </small>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Resultados - Timeline de Sesiones -->
  <div class="content-dashboard">
    <div class="franja-paginas">
      <div class="row g-0 justify-content-between">
        <div class="col-auto">
          <span class="contador-registros">
            Mostrando <?php echo count($this->sesiones); ?> sesiones
          </span>
        </div>
        <div class="col-auto">
          <span class="info-pagina">
            Página <?php echo $this->page; ?>
          </span>
        </div>
      </div>
    </div>

    <!-- Timeline de Sesiones -->
    <div class="sessions-timeline">
      <?php if ($this->sesiones && count($this->sesiones) > 0): ?>
        <?php foreach ($this->sesiones as $index => $sesion): ?>
          <div
            class="timeline-session <?php echo $sesion->validacion_completada ? 'session-success' : 'session-warning'; ?>">

            <!-- Marcador de timeline -->
            <div class="session-marker">
              <?php if ($sesion->validacion_completada): ?>
                <i class="fas fa-check-circle text-success"></i>
              <?php else: ?>
                <i class="fas fa-exclamation-triangle text-warning"></i>
              <?php endif; ?>
            </div>

            <!-- Contenido de la sesión -->
            <div class="session-content">
              <div class="session-header">
                <div class="session-title">
                  <h5 class="mb-1">
                    <i class="fas fa-fingerprint"></i>
                    Sesión <?php echo substr($sesion->auditoriaboleta_session_id, -8); ?>

                    <!-- Badge del método -->
                    <span class="badge method-badge method-<?php echo $sesion->auditoriaboleta_metodo_escaneado; ?>">
                      <?php
                      $metodos = [
                        'camera' => '<i class="fas fa-camera"></i> Cámara',
                        'usb_scanner' => '<i class="fas fa-usb"></i> Scanner USB',
                        'manual' => '<i class="fas fa-keyboard"></i> Manual'
                      ];
                      echo $metodos[$sesion->auditoriaboleta_metodo_escaneado] ?? $sesion->auditoriaboleta_metodo_escaneado;
                      ?>
                    </span>

                    <!-- Badge del resultado -->
                    <?php if ($sesion->validacion_completada): ?>
                      <span class="badge badge-success">Completada</span>
                    <?php else: ?>
                      <span class="badge badge-warning">Incompleta</span>
                    <?php endif; ?>
                  </h5>

                  <div class="session-metadata">
                    <small class="text-muted">
                      <i class="fas fa-user"></i> <?php echo $sesion->auditoriaboleta_usuario_validador_nombre; ?>
                      <i class="fas fa-clock ml-2"></i>
                      <?php echo date('d/m/Y H:i:s', strtotime($sesion->sesion_inicio)); ?>

                      <?php if ($sesion->auditoriaboleta_documento_escaneado): ?>
                        <i class="fas fa-id-card ml-2"></i> <?php echo $sesion->auditoriaboleta_documento_escaneado; ?>
                      <?php endif; ?>
                    </small>
                  </div>
                </div>
              </div>

              <!-- Detalles de la sesión -->
              <div class="session-details">
                <div class="row">
                  <div class="col-md-3">
                    <div class="stat-box">
                      <div class="stat-number"><?php echo $sesion->total_acciones; ?></div>
                      <div class="stat-label">Acciones Totales</div>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="stat-box">
                      <div class="stat-number text-success"><?php echo $sesion->acciones_exitosas; ?></div>
                      <div class="stat-label">Exitosas</div>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="stat-box">
                      <div class="stat-number text-danger"><?php echo $sesion->acciones_fallidas; ?></div>
                      <div class="stat-label">Fallidas</div>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="stat-box">
                      <?php
                      $duracion = strtotime($sesion->sesion_fin) - strtotime($sesion->sesion_inicio);
                      $duracionMin = floor($duracion / 60);
                      $duracionSeg = $duracion % 60;
                      ?>
                      <div class="stat-number"><?php echo $duracionMin; ?>:<?php echo sprintf('%02d', $duracionSeg); ?>
                      </div>
                      <div class="stat-label">Duración</div>
                    </div>
                  </div>
                </div>

                <!-- Secuencia de acciones -->
                <div class="actions-sequence mt-3">
                  <h6>Secuencia de Acciones:</h6>
                  <div class="action-tags">
                    <?php
                    $acciones = explode(',', $sesion->secuencia_acciones);
                    foreach ($acciones as $accion):
                      ?>
                      <span class="badge badge-info action-tag"><?php echo trim($accion); ?></span>
                    <?php endforeach; ?>
                  </div>
                </div>

                <!-- Botón para ver detalles completos -->
                <div class="session-actions mt-3">
                  <button class="btn btn-outline-primary btn-sm"
                    onclick="verDetallesSesion('<?php echo $sesion->auditoriaboleta_session_id; ?>')">
                    <i class="fas fa-search-plus"></i> Ver Detalles Completos
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="no-results">
          <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4>No se encontraron sesiones</h4>
            <p class="text-muted">Ajuste los filtros para ver más resultados</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Modal para detalles de sesión -->
<div class="modal fade" id="modalDetallesSesion" tabindex="-1" aria-labelledby="modalDetallesSesionLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalDetallesSesionLabel">
          <i class="fas fa-stream"></i> Detalles de Sesión
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="contenidoDetallesSesion">
          <div class="text-center p-3">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p>Cargando detalles...</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Timeline de Sesiones */
  .sessions-timeline {
    position: relative;
    padding: 20px 0;
  }

  .sessions-timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 30px;
    height: 100%;
    width: 3px;
    background: linear-gradient(to bottom, #007bff, #28a745, #ffc107);
  }

  .timeline-session {
    position: relative;
    margin-bottom: 30px;
    padding-left: 70px;
  }

  .session-marker {
    position: absolute;
    left: 18px;
    top: 5px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }

  .session-content {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
  }

  .session-content:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
  }

  .session-success .session-content {
    border-left: 4px solid #28a745;
  }

  .session-warning .session-content {
    border-left: 4px solid #ffc107;
  }

  .session-header {
    border-bottom: 1px solid #f8f9fa;
    padding-bottom: 15px;
    margin-bottom: 15px;
  }

  .session-title h5 {
    margin: 0;
    color: #495057;
    font-weight: 600;
  }

  .session-metadata {
    margin-top: 8px;
  }

  /* Method badges */
  .method-badge {
    font-size: 0.8em;
    padding: 4px 8px;
  }

  .method-camera {
    background-color: #007bff;
  }

  .method-usb_scanner {
    background-color: #6f42c1;
  }

  .method-manual {
    background-color: #fd7e14;
  }

  /* Stat boxes */
  .stat-box {
    text-align: center;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 10px;
  }

  .stat-number {
    font-size: 1.5em;
    font-weight: bold;
    color: #495057;
  }

  .stat-label {
    font-size: 0.85em;
    color: #6c757d;
    text-transform: uppercase;
  }

  /* Action tags */
  .actions-sequence {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
  }

  .action-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
  }

  .action-tag {
    font-size: 0.75em;
    padding: 3px 8px;
  }

  /* No results */
  .no-results {
    background: white;
    border-radius: 12px;
    padding: 40px;
    text-align: center;
    border: 1px solid #e9ecef;
  }

  /* Cards de estadísticas */
  .card {
    border-radius: 10px;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  /* Navigation pills */
  .nav-pills .nav-link {
    border-radius: 25px;
    margin: 0 10px;
    font-weight: 500;
  }

  .nav-pills .nav-link.active {
    background-color: #007bff;
  }

  .nav-pills .nav-link:not(.active) {
    color: #007bff;
    background-color: #f8f9fa;
  }

  .nav-pills .nav-link:not(.active):hover {
    background-color: #e9ecef;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .sessions-timeline::before {
      left: 15px;
    }

    .timeline-session {
      padding-left: 50px;
    }

    .session-marker {
      left: 5px;
    }

    .stat-box {
      margin-bottom: 15px;
    }
  }
</style>

<script>
  // Función para ver detalles completos de una sesión
  function verDetallesSesion (sessionId) {
    // Mostrar modal
    $('#modalDetallesSesion').modal('show');

    // Cargar contenido vía AJAX
    $.ajax({
      url: '<?php echo $this->route; ?>/detallesesion',
      method: 'POST',
      data: {
        session_id: sessionId
      },
      success: function (response) {
        if (response.success) {
          let html = `
          <div class="session-detail-header mb-4">
            <h6><i class="fas fa-fingerprint"></i> Session ID: <code>${response.session_id}</code></h6>
            <p class="text-muted">Total de ${response.total_logs} eventos registrados</p>
          </div>
          
          <div class="timeline-detail">
        `;

          response.logs.forEach((log, index) => {
            const badgeClass = log.resultado === 'exitoso' ? 'success' :
              log.resultado === 'fallido' ? 'danger' : 'warning';

            html += `
            <div class="timeline-detail-item">
              <div class="timeline-detail-marker">
                <span class="badge badge-${badgeClass}">${index + 1}</span>
              </div>
              <div class="timeline-detail-content">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <h6 class="mb-0">${log.accion}</h6>
                  <small class="text-muted">${log.fecha_hora}</small>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <small><strong>Resultado:</strong> 
                      <span class="badge badge-${badgeClass}">${log.resultado}</span>
                    </small>
                  </div>
                  <div class="col-md-6">
                    <small><strong>Método:</strong> ${log.metodo || 'N/A'}</small>
                  </div>
                </div>
                ${log.documento ? `<small><strong>Documento:</strong> ${log.documento}</small><br>` : ''}
                ${log.motivo_fallo ? `<small><strong>Motivo:</strong> ${log.motivo_fallo}</small><br>` : ''}
                ${log.observaciones ? `<small><strong>Observaciones:</strong> ${log.observaciones}</small><br>` : ''}
                ${log.url ? `<small><strong>URL:</strong> <code>${log.url}</code></small><br>` : ''}
              </div>
            </div>
          `;
          });

          html += '</div>';

          $('#contenidoDetallesSesion').html(html);
        } else {
          $('#contenidoDetallesSesion').html(`
          <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i> Error al cargar los detalles: ${response.error}
          </div>
        `);
        }
      },
      error: function () {
        $('#contenidoDetallesSesion').html(`
        <div class="alert alert-danger">
          <i class="fas fa-exclamation-triangle"></i> Error de conexión al cargar los detalles
        </div>
      `);
      }
    });
  }
</script>

<style>
  /* Estilos para el timeline de detalles en el modal */
  .timeline-detail {
    position: relative;
    padding-left: 40px;
  }

  .timeline-detail::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    height: 100%;
    width: 2px;
    background-color: #dee2e6;
  }

  .timeline-detail-item {
    position: relative;
    margin-bottom: 25px;
  }

  .timeline-detail-marker {
    position: absolute;
    left: -30px;
    top: 5px;
    z-index: 1;
  }

  .timeline-detail-marker .badge {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7em;
  }

  .timeline-detail-content {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 3px solid #dee2e6;
  }

  .session-detail-header {
    background: #e9ecef;
    border-radius: 8px;
    padding: 15px;
  }
</style>