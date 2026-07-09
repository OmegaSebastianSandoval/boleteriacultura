<h1 class="titulo-principal"><i class="fas fa-shield-alt"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
  <!-- Filtros -->
  <form action="<?php echo $this->route; ?>/escaneologs" method="post">
    <div class="content-dashboard  d-none">
      <div class="row">
        <div class="col-2">
          <label>Fecha Desde</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-calendar"></i></span>
            </div>
            <input type="date" class="form-control" name="fecha_desde" value="<?php echo $this->fecha_desde; ?>">
          </label>
        </div>
        <div class="col-2">
          <label>Fecha Hasta</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-calendar"></i></span>
            </div>
            <input type="date" class="form-control" name="fecha_hasta" value="<?php echo $this->fecha_hasta; ?>">
          </label>
        </div>
        <div class="col-2">
          <label>Acción</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-cog"></i></span>
            </div>
            <select class="form-control" name="accion">
              <option value="">Todas las acciones</option>
              <?php foreach ($this->acciones as $accion): ?>
                <option value="<?php echo $accion; ?>" <?php echo ($this->accionFiltro == $accion) ? 'selected' : ''; ?>>
                  <?php echo $accion; ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>
        <div class="col-2">
          <label>Resultado</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
            </div>
            <select class="form-control" name="resultado">
              <option value="">Todos los resultados</option>
              <?php foreach ($this->resultados as $resultado): ?>
                <option value="<?php echo $resultado; ?>" <?php echo ($this->resultadoFiltro == $resultado) ? 'selected' : ''; ?>>
                  <?php echo ucfirst($resultado); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>
        <div class="col-2">
          <label>Método</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-qrcode"></i></span>
            </div>
            <select class="form-control" name="metodo">
              <option value="">Todos los métodos</option>
              <?php foreach ($this->metodos as $metodo): ?>
                <option value="<?php echo $metodo; ?>" <?php echo ($this->metodoFiltro == $metodo) ? 'selected' : ''; ?>>
                  <?php echo ucfirst(str_replace('_', ' ', $metodo)); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>
        <div class="col-2">
          <label>Documento</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-id-card"></i></span>
            </div>
            <input type="text" class="form-control" name="documento" value="<?php echo $this->documentoFiltro; ?>"
              placeholder="Buscar documento...">
          </label>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-2">
          <label>Usuario</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-user"></i></span>
            </div>
            <input type="text" class="form-control" name="usuario" value="<?php echo $this->usuarioFiltro; ?>"
              placeholder="Buscar usuario...">
          </label>
        </div>
        <div class="col-5">
          <button type="submit" class="btn btn-block btn-azul mt-4"> <i class="fas fa-filter"></i> Filtrar Logs</button>
        </div>
        <div class="col-5">
          <a class="btn btn-block btn-azul-claro mt-4" href="<?php echo $this->route; ?>/escaneologs"> <i
              class="fas fa-eraser"></i> Limpiar Filtros</a>
        </div>
      </div>
    </div>
  </form>



  <!-- Estadísticas rápidas -->
  <div class="content-dashboard mt-3">
    <div class="row">
      <div class="col-3">
        <div class="card bg-primary text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h4><?php echo $this->totalLogs; ?></h4>
                <p class="mb-0">Total de Registros</p>
              </div>
              <div>
                <i class="fas fa-list-alt fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-3">
        <div class="card bg-success text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h4>
                  <?php echo count(array_filter($this->logs, function ($log) {
                    return $log->auditoriaboleta_resultado == 'exitoso';
                  })); ?>
                </h4>
                <p class="mb-0">Exitosos (Página)</p>
              </div>
              <div>
                <i class="fas fa-check-circle fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-3">
        <div class="card bg-danger text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h4>
                  <?php echo count(array_filter($this->logs, function ($log) {
                    return $log->auditoriaboleta_resultado == 'fallido';
                  })); ?>
                </h4>
                <p class="mb-0">Fallidos (Página)</p>
              </div>
              <div>
                <i class="fas fa-times-circle fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-3">
        <div class="card bg-info text-white">
          <div class="card-body">
            <div class="d-flex justify-content-between">
              <div>
                <h4><?php echo count(array_unique(array_column($this->logs, 'auditoriaboleta_ip_address'))); ?></h4>
                <p class="mb-0">IPs Únicas (Página)</p>
              </div>
              <div>
                <i class="fas fa-network-wired fa-2x"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Paginación superior -->
  <div align="center">
    <ul class="pagination justify-content-center">
      <?php
      $url = $this->route . '/escaneologs';
      $params = array();
      if ($this->fecha_desde)
        $params[] = "fecha_desde=" . $this->fecha_desde;
      if ($this->fecha_hasta)
        $params[] = "fecha_hasta=" . $this->fecha_hasta;
      if ($this->accionFiltro)
        $params[] = "accion=" . $this->accionFiltro;
      if ($this->resultadoFiltro)
        $params[] = "resultado=" . $this->resultadoFiltro;
      if ($this->metodoFiltro)
        $params[] = "metodo=" . $this->metodoFiltro;
      if ($this->documentoFiltro)
        $params[] = "documento=" . $this->documentoFiltro;
      if ($this->usuarioFiltro)
        $params[] = "usuario=" . $this->usuarioFiltro;
      $paramString = ($params) ? '&' . implode('&', $params) : '';

      if ($this->totalpages > 1) {
        if ($this->page != 1)
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page - 1) . $paramString . '"> &laquo; Anterior </a></li>';

        $min = max(1, $this->page - 5);
        $max = min($this->totalpages, $this->page + 5);

        for ($i = $min; $i <= $max; $i++) {
          if ($this->page == $i)
            echo '<li class="active page-item"><a class="page-link">' . $this->page . '</a></li>';
          else
            echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . $paramString . '">' . $i . '</a></li>';
        }

        if ($this->page != $this->totalpages)
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page + 1) . $paramString . '">Siguiente &raquo;</a></li>';
      }
      ?>
    </ul>
  </div>

  <!-- Resultados -->
  <div class="content-dashboard">
    <div class="franja-paginas">
      <div class="row g-0 justify-content-between">
        <div class="col-8">
          <div class="titulo-registro">Se encontraron <?php echo $this->totalLogs; ?> registros de auditoría</div>
        </div>
        <div class="col-4">
          <div class="text-end">
            <span class="text-muted">Página <?php echo $this->page; ?> de <?php echo $this->totalpages; ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Timeline de auditoría -->
    <div class="audit-timeline">
      <?php
      // Función para obtener el icono según la acción
      function getActionIcon($accion)
      {
        switch ($accion) {
          case 'ACCESO_VALIDACION':
          case 'LOGIN':
            return 'fa-sign-in-alt';
          case 'VALIDACION_EXITOSA':
          case 'VALIDACION_DOCUMENTO_EXITOSA':
            return 'fa-check-circle';
          case 'VALIDACION_FALLIDA':
          case 'VALIDACION_DOCUMENTO_FALLIDA':
            return 'fa-times-circle';
          case 'CONSULTA_DOCUMENTO':
          case 'CONSULTA_BOLETA':
            return 'fa-search';
          case 'ESCANEO_QR':
          case 'ESCANEO_DOCUMENTO':
            return 'fa-qrcode';
          case 'LOGOUT':
            return 'fa-sign-out-alt';
          default:
            return 'fa-cog';
        }
      }

      // Función para obtener el color según el resultado
      function getResultColor($resultado)
      {
        switch ($resultado) {
          case 'exitoso':
            return '#28a745';
          case 'fallido':
            return '#dc3545';
          case 'pendiente':
            return '#ffc107';
          default:
            return '#6c757d';
        }
      }

      // Función para obtener descripción amigable del método de escaneo
      function getMetodoDescripcion($metodo, $log = null)
      {
        if (
          $log->auditoriaboleta_accion == 'ACCESO_VALIDACION' ||
          $log->auditoriaboleta_accion == 'INTENTO_LOGIN' ||
          $log->auditoriaboleta_accion == 'LOGIN_EXITOSO' ||
          $log->auditoriaboleta_accion == 'LOGOUT' ||
          $log->auditoriaboleta_accion == 'ACCESO_VALIDACION'
        ) {
          return false;
        }
        switch ($metodo) {
          case 'camera':
            return 'Cámara';
          case 'usb_scanner':
            return 'Escáner USB';
          case 'manual':
            return 'Entrada Manual';
          case 'automatico':
            return 'Automático';
          default:
            return ucfirst($metodo);
        }
      }

      // Agrupar logs por session_id
      $sessionGroups = [];
      $logsWithoutSession = [];

      foreach ($this->logs as $log) {
        if (!empty($log->auditoriaboleta_session_id)) {
          if (!isset($sessionGroups[$log->auditoriaboleta_session_id])) {
            $sessionGroups[$log->auditoriaboleta_session_id] = [];
          }
          $sessionGroups[$log->auditoriaboleta_session_id][] = $log;
        } else {
          $logsWithoutSession[] = $log;
        }
      }

      // Mostrar grupos de sesiones
      foreach ($sessionGroups as $sessionId => $sessionLogs) {
        // Calcular estadísticas de la sesión
        $exitosos = count(array_filter($sessionLogs, function ($log) {
          return $log->auditoriaboleta_resultado == 'exitoso';
        }));
        $fallidos = count(array_filter($sessionLogs, function ($log) {
          return $log->auditoriaboleta_resultado == 'fallido';
        }));
        $totalLogs = count($sessionLogs);

        // Obtener información principal de la sesión
        $firstLog = $sessionLogs[0];
        $lastLog = end($sessionLogs);

        // Determinar el estado general de la sesión
        $hasValidacionExitosa = false;
        foreach ($sessionLogs as $log) {
          if (in_array($log->auditoriaboleta_accion, ['VALIDACION_EXITOSA', 'VALIDACION_DOCUMENTO_EXITOSA'])) {
            $hasValidacionExitosa = true;
            break;
          }
        }

        $sessionClass = $hasValidacionExitosa ? 'session-success' : ($fallidos > $exitosos ? 'session-danger' : 'session-warning');
      ?>

        <!-- Grupo de Sesión -->
        <div class="session-group <?= $sessionClass; ?>">
          <div class="session-header-group">
            <div class="session-marker-group" onclick="toggleSession('<?= $sessionId; ?>')">
              <?php if ($hasValidacionExitosa): ?>
                <i class="fas fa-check-circle text-success"></i>
              <?php elseif ($fallidos > $exitosos): ?>
                <i class="fas fa-times-circle text-danger"></i>
              <?php else: ?>
                <i class="fas fa-exclamation-triangle text-warning"></i>
              <?php endif; ?>
            </div>

            <div class="session-content-group" onclick="toggleSession('<?= $sessionId; ?>')">
              <div class="session-title-group">
                <h5>
                  <i class="fas fa-layer-group"></i>
                  Sesión: <?= substr($sessionId, -12); ?>

                  <!-- Badge del estado -->
                  <?php if ($hasValidacionExitosa): ?>
                    <span class="badge text-bg-success">Validación Exitosa</span>
                  <?php elseif ($fallidos > $exitosos): ?>
                    <span class="badge text-bg-danger">Con Errores</span>
                  <?php else: ?>
                    <span class="badge text-bg-warning">Incompleta</span>
                  <?php endif; ?>

                  <!-- Badge del método -->
                  <span class="badge method-badge method-<?= $firstLog->auditoriaboleta_metodo_escaneado; ?>">
                    <?= getMetodoDescripcion($firstLog->auditoriaboleta_metodo_escaneado); ?>
                  </span>

                  <!-- Indicador de expandir/colapsar -->
                  <i class="fas fa-chevron-down session-toggle" id="toggle-<?= $sessionId; ?>"></i>
                </h5>

                <div class="session-summary">
                  <div class="row">
                    <div class="col-md-3">
                      <small><strong>Usuario:</strong> <?= $firstLog->auditoriaboleta_usuario_validador_nombre; ?></small>
                    </div>
                    <?php if ($firstLog->auditoriaboleta_documento_escaneado): ?>
                      <div class="col-md-3">
                        <small><strong>Documento:</strong>
                          <?= $firstLog->auditoriaboleta_documento_escaneado ?: 'N/A'; ?></small>
                      </div>
                    <?php endif; ?>
                    <div class="col-md-3">
                      <small><strong>Inicio:</strong>
                        <?= date('d/m/Y H:i:s', strtotime($firstLog->auditoriaboleta_fecha_hora)); ?></small>
                    </div>
                    <div class="col-md-3">
                      <small><strong>Logs:</strong> <?= $totalLogs; ?> (<?= $exitosos; ?> exitosos, <?= $fallidos; ?>
                        fallidos)</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Logs individuales de la sesión (colapsados por defecto) -->
          <div class="session-logs-container" id="session-<?= $sessionId; ?>" style="display: none;">
            <div class="session-timeline">
              <?php foreach ($sessionLogs as $key => $log):
                $actionIcon = getActionIcon($log->auditoriaboleta_accion);
                $resultColor = getResultColor($log->auditoriaboleta_resultado);

                // Determinar clase CSS para el timeline
                $timelineClass = '';
                switch ($log->auditoriaboleta_resultado) {
                  case 'exitoso':
                    $timelineClass = 'timeline-success';
                    break;
                  case 'fallido':
                    $timelineClass = 'timeline-danger';
                    break;
                  default:
                    $timelineClass = 'timeline-info';
                }
              ?>

                <div class="timeline-item-session <?= $timelineClass; ?>">
                  <div class="timeline-marker-session" style="background-color: <?= $resultColor; ?>">
                    <i class="fas <?= $actionIcon; ?>"></i>
                  </div>
                  <div class="timeline-content-session">
                    <div class="timeline-header-session">
                      <h6 class="timeline-title-session">
                        <?= $log->auditoriaboleta_accion; ?>
                        <span
                          class="badge text-bg-<?= ($log->auditoriaboleta_resultado == 'exitoso') ? 'success' : (($log->auditoriaboleta_resultado == 'fallido') ? 'danger' : 'secondary'); ?>">
                          <?= ucfirst($log->auditoriaboleta_resultado); ?>
                        </span>
                      </h6>
                      <span class="timeline-date-session">
                        <i class="fas fa-clock"></i>
                        <?= date('d/m/Y H:i:s', strtotime($log->auditoriaboleta_fecha_hora)); ?>
                      </span>
                    </div>

                    <div class="timeline-description-session">
                      <strong>Documento:</strong> <?= $log->auditoriaboleta_documento_escaneado ?: 'N/A'; ?><br>
                      <strong>Usuario:</strong> <?= $log->auditoriaboleta_usuario_validador_nombre ?: 'Sistema'; ?><br>
                      <?php if (getMetodoDescripcion($log->auditoriaboleta_metodo_escaneado, $log)): ?>
                        <strong>Método:</strong> <?= getMetodoDescripcion($log->auditoriaboleta_metodo_escaneado, $log); ?>
                      <?php endif; ?>

                      <?php if ($log->auditoriaboleta_motivo_fallo): ?>
                        <br><strong>Motivo del fallo:</strong> <span
                          class="text-danger"><?= $log->auditoriaboleta_motivo_fallo; ?></span>
                      <?php endif; ?>
                    </div>

                    <div class="timeline-details-session">
                      <div class="row">
                        <div class="col-6">
                          <small class="text-muted">
                            <?php if ($log->auditoriaboleta_boleta_uid): ?>

                              <i class="fas fa-ticket-alt"></i> <strong>Boleta:</strong>
                              <?= $log->auditoriaboleta_boleta_uid ?: 'N/A'; ?>
                              <br>
                            <?php endif; ?>

                            <?php if ($log->auditoriaboleta_boleta_numero_ticket): ?>
                              <i class="fas fa-hashtag"></i> <strong>Ticket:</strong>
                              <?= $log->auditoriaboleta_boleta_numero_ticket ?: 'N/A'; ?> <br>
                            <?php endif; ?>

                            <?php if ($log->auditoriaboleta_boleta_mesa): ?>
                              <i class="fas fa-table"></i> <strong>Mesa:</strong>
                              <?= $log->auditoriaboleta_boleta_mesa ?: 'N/A'; ?>
                            <?php endif; ?>
                          </small>
                        </div>
                        <div class="col-6">
                          <small class="text-muted">
                            <i class="fas fa-network-wired"></i> <strong>IP:</strong>

                            <?= $log->auditoriaboleta_ip_address; ?><br>
                            <?php if ($log->auditoriaboleta_numero_carnet): ?>

                              <i class="fas fa-id-card"></i> <strong>Carnet:</strong>
                              <?= $log->auditoriaboleta_numero_carnet ?: 'N/A'; ?><br>
                            <?php endif; ?>
                            <!-- <i class="fas fa-clock"></i> <strong>Unix:</strong>
                            <?= $log->auditoriaboleta_timestamp_unix; ?> -->
                          </small>
                        </div>
                      </div>
                    </div>

                    <?php if ($log->auditoriaboleta_user_agent): ?>
                      <div class="timeline-technical-session mt-2">
                        <small class="text-muted">
                          <i class="fas fa-desktop"></i> <strong>Dispositivo:</strong>
                          <?= substr($log->auditoriaboleta_user_agent, 0, 100) . (strlen($log->auditoriaboleta_user_agent) > 100 ? '...' : ''); ?>
                        </small>
                      </div>
                    <?php endif; ?>

                    <?php if ($log->auditoriaboleta_parametros_post || $log->auditoriaboleta_parametros_get): ?>
                      <div class="timeline-params-session mt-2">
                        <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse"
                          data-bs-target="#params<?= $log->auditoriaboleta_id; ?>">
                          <i class="fas fa-code"></i> Ver parámetros técnicos
                        </button>
                        <div class="collapse mt-2" id="params<?= $log->auditoriaboleta_id; ?>">
                          <div class="card card-body bg-light">
                            <?php if ($log->auditoriaboleta_parametros_post): ?>
                              <small><strong>POST:</strong><br><code><?= htmlspecialchars($log->auditoriaboleta_parametros_post); ?></code></small>
                            <?php endif; ?>
                            <?php if ($log->auditoriaboleta_parametros_get): ?>
                              <small><strong>GET:</strong><br><code><?= htmlspecialchars($log->auditoriaboleta_parametros_get); ?></code></small>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

      <?php } ?>

      <!-- Logs sin session_id (formato original) -->
      <?php if (!empty($logsWithoutSession)): ?>
        <div class="session-group session-legacy">
          <div class="session-header-group">
            <div class="session-marker-group" onclick="toggleSession('legacy')">
              <i class="fas fa-list text-secondary"></i>
            </div>
            <div class="session-content-group" onclick="toggleSession('legacy')">
              <div class="session-title-group">
                <h5>
                  <i class="fas fa-history"></i>
                  Logs Legacy (sin agrupación)
                  <span class="badge text-bg-secondary"><?= count($logsWithoutSession); ?> logs</span>
                  <i class="fas fa-chevron-down session-toggle" id="toggle-legacy"></i>
                </h5>
              </div>
            </div>
          </div>

          <div class="session-logs-container" id="session-legacy" style="display: none;">
            <?php foreach ($logsWithoutSession as $log):
              $actionIcon = getActionIcon($log->auditoriaboleta_accion);
              $resultColor = getResultColor($log->auditoriaboleta_resultado);

              $timelineClass = '';
              switch ($log->auditoriaboleta_resultado) {
                case 'exitoso':
                  $timelineClass = 'timeline-success';
                  break;
                case 'fallido':
                  $timelineClass = 'timeline-danger';
                  break;
                default:
                  $timelineClass = 'timeline-info';
              }
            ?>

              <div class="timeline-item <?= $timelineClass; ?>">
                <div class="timeline-marker" style="background-color: <?= $resultColor; ?>">
                  <i class="fas <?= $actionIcon; ?>"></i>
                </div>
                <div class="timeline-content">
                  <div class="timeline-header">
                    <h5 class="timeline-title">
                      <?= $log->auditoriaboleta_accion; ?>
                      <span
                        class="badge text-bg-<?= ($log->auditoriaboleta_resultado == 'exitoso') ? 'success' : (($log->auditoriaboleta_resultado == 'fallido') ? 'danger' : 'secondary'); ?>">
                        <?= ucfirst($log->auditoriaboleta_resultado); ?>
                      </span>
                    </h5>
                    <span class="timeline-date">
                      <i class="fas fa-clock"></i>
                      <?= date('d/m/Y H:i:s', strtotime($log->auditoriaboleta_fecha_hora)); ?>
                    </span>
                  </div>

                  <div class="timeline-description">
                    <strong>Documento:</strong> <?= $log->auditoriaboleta_documento_escaneado ?: 'N/A'; ?><br>
                    <strong>Usuario:</strong> <?= $log->auditoriaboleta_usuario_validador_nombre ?: 'Sistema'; ?><br>
                    <?php if (getMetodoDescripcion($log->auditoriaboleta_metodo_escaneado, $log)): ?>
                      <strong>Método:</strong> <?= getMetodoDescripcion($log->auditoriaboleta_metodo_escaneado, $log); ?>
                    <?php endif; ?>
                    <?php if ($log->auditoriaboleta_motivo_fallo): ?>
                      <br><strong>Motivo del fallo:</strong> <span
                        class="text-danger"><?= $log->auditoriaboleta_motivo_fallo; ?></span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>`
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>



      <?php if (!($this->logs)): ?>
        <div class="text-center py-5">
          <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
          <h5 class="text-muted">No se encontraron registros de auditoría</h5>
          <p class="text-muted">Intenta ajustar los filtros para ver más resultados.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Paginación inferior -->
  <div align="center">
    <ul class="pagination justify-content-center">
      <?php
      if ($this->totalpages > 1) {
        if ($this->page != 1)
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page - 1) . $paramString . '"> &laquo; Anterior </a></li>';

        $min = max(1, $this->page - 5);
        $max = min($this->totalpages, $this->page + 5);

        for ($i = $min; $i <= $max; $i++) {
          if ($this->page == $i)
            echo '<li class="active page-item"><a class="page-link">' . $this->page . '</a></li>';
          else
            echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . $paramString . '">' . $i . '</a></li>';
        }

        if ($this->page != $this->totalpages)
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page + 1) . $paramString . '">Siguiente &raquo;</a></li>';
      }
      ?>
    </ul>
  </div>
</div>

<style>
  /* Estilos para el timeline de auditoría */
  .audit-timeline {
    position: relative;
    padding: 20px 0;
  }

  /* 
  .audit-timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 30px;
    height: 100%;
    width: 3px;
    background: linear-gradient(to bottom, #007bff, #28a745, #ffc107, #dc3545);
  } */

  .timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 70px;
  }

  .timeline-marker {
    position: absolute;
    left: 18px;
    top: 5px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 11px;
    z-index: 1;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }

  .timeline-content {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s ease;
  }

  .timeline-content:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
  }

  .timeline-success .timeline-content {
    border-left: 4px solid #28a745;
  }

  .timeline-danger .timeline-content {
    border-left: 4px solid #dc3545;
  }

  .timeline-info .timeline-content {
    border-left: 4px solid #17a2b8;
  }

  .timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #f8f9fa;
    padding-bottom: 10px;
  }

  .timeline-title {
    margin: 0;
    color: #495057;
    font-weight: 600;
    font-size: 1.1em;
  }

  .timeline-date {
    color: #6c757d;
    font-size: 0.85em;
    white-space: nowrap;
  }

  .timeline-description {
    margin-bottom: 15px;
    color: #212529;
    line-height: 1.6;
  }

  .timeline-details {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
  }

  .timeline-technical {
    background: #e9ecef;
    border-radius: 6px;
    padding: 10px;
  }

  .timeline-params .card-body {
    padding: 10px;
    max-height: 200px;
    overflow-y: auto;
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

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .audit-timeline::before {
      left: 15px;
    }

    .timeline-item {
      padding-left: 50px;
    }

    .timeline-marker {
      left: 5px;
      width: 20px;
      height: 20px;
      font-size: 10px;
    }

    .timeline-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .timeline-date {
      margin-top: 5px;
    }

    .timeline-details .row>div {
      margin-bottom: 10px;
    }
  }

  /* Badge customizations */
  .badge {
    font-size: 0.75em;
    padding: 0.25em 0.5em;
  }

  /* Collapse button styling */
  .btn-outline-info {
    border-color: #17a2b8;
    color: #17a2b8;
  }

  .btn-outline-info:hover {
    background-color: #17a2b8;
    border-color: #17a2b8;
    color: white;
  }

  /* Code styling */
  code {
    background-color: #f8f9fa;
    color: #e83e8c;
    padding: 2px 4px;
    border-radius: 4px;
    word-break: break-all;
  }

  /* Estilos para agrupación de sesiones */
  .session-group {
    margin-bottom: 25px;
    border: 1px solid #dee2e6;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  }

  .session-header-group {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    padding: 15px 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
  }

  .session-header-group:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
  }

  .session-header-group.collapsed::after {
    content: '\f078';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%) rotate(180deg);
    transition: transform 0.3s ease;
  }

  /*.session-header-group:not(.collapsed)::after {*/
  /*  content: '\f078';*/
  /*  font-family: 'Font Awesome 5 Free';*/
  /*  font-weight: 900;*/
  /*  position: absolute;*/
  /*  right: 20px;*/
  /*  top: 50%;*/
  /*  transform: translateY(-50%);*/
  /*  transition: transform 0.3s ease;*/
  /*}*/

  .session-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
  }

  .session-title {
    font-size: 1.1em;
    font-weight: 600;
    color: #495057;
    margin: 0;
  }

  .session-stats {
    display: flex;
    gap: 15px;
    font-size: 0.85em;
    color: #6c757d;
  }

  .session-details {
    color: #6c757d;
    font-size: 0.9em;
    line-height: 1.4;
  }

  .session-content {
    padding: 0;
    background: #fff;
  }

  .timeline-item-small {
    position: relative;
    margin-bottom: 20px;
    padding: 15px 20px;
    border-bottom: 1px solid #f8f9fa;
  }

  .timeline-item-small:last-child {
    border-bottom: none;
  }

  .timeline-item-small::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 20px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px #dee2e6;
  }

  .timeline-item-small.timeline-success::before {
    background-color: #28a745;
  }

  .timeline-item-small.timeline-danger::before {
    background-color: #dc3545;
  }

  .timeline-item-small.timeline-info::before {
    background-color: #17a2b8;
  }

  .timeline-item-small .timeline-content {
    margin-left: 25px;
    padding: 12px;
    box-shadow: none;
    border: 1px solid #f8f9fa;
  }

  .session-indicator {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 8px;
  }

  .session-indicator.success {
    background-color: #28a745;
  }

  .session-indicator.warning {
    background-color: #ffc107;
  }

  .session-indicator.danger {
    background-color: #dc3545;
  }

  .legacy-logs-section {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px dashed #dee2e6;
  }

  .legacy-logs-title {
    color: #6c757d;
    font-size: 1.1em;
    font-weight: 600;
    margin-bottom: 20px;
    text-align: center;
  }

  /* Animaciones para el colapso */
  .session-content {
    transition: max-height 0.3s ease-out;
    overflow: hidden;
  }

  .session-group.collapsed .session-content {
    max-height: 0;
  }

  .session-group:not(.collapsed) .session-content {
    max-height: none;
  }

  /* Estilos adicionales para la nueva estructura */
  .session-marker-group {
    display: inline-block;
    width: 30px;
    text-align: center;
    vertical-align: top;
  }

  .session-content-group {
    display: inline-block;
    width: calc(100% - 30px);
    vertical-align: top;
  }

  .session-title-group h5 {
    margin: 0;
    font-size: 1.1em;
    font-weight: 600;
    color: #495057;
  }

  .session-summary {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e9ecef;
    color: #6c757d;
    font-size: 0.9em;
  }

  .session-toggle {
    float: right;
    transition: transform 0.3s ease;
    margin-left: 10px;
  }

  .session-logs-container {
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
  }

  /* Estilos para timeline small */
  .timeline-marker-small {
    position: absolute;
    left: 8px;
    top: 18px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 9px;
    border: 2px solid white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
  }

  .timeline-content-small {
    margin-left: 35px;
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 12px;
  }

  .timeline-header-small {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
  }

  .timeline-title-small {
    margin: 0;
    font-size: 0.95em;
    font-weight: 600;
    color: #495057;
  }

  .timeline-date-small {
    color: #6c757d;
    font-size: 0.8em;
    white-space: nowrap;
  }

  .timeline-description-small {
    margin-top: 8px;
    color: #495057;
    font-size: 0.85em;
    line-height: 1.4;
  }

  /* Badges para métodos */
  .method-badge {
    font-size: 0.7em !important;
    margin-left: 5px;
  }

  .method-camera {
    background-color: #17a2b8 !important;
  }

  .method-usb_scanner {
    background-color: #6f42c1 !important;
  }

  .method-manual {
    background-color: #fd7e14 !important;
  }

  .method-automatico {
    background-color: #20c997 !important;
  }

  /* Estilos para sesiones según estado */
  .session-success {
    border-left: 4px solid #28a745;
  }

  .session-danger {
    border-left: 4px solid #dc3545;
  }

  .session-warning {
    border-left: 4px solid #ffc107;
  }

  .session-legacy {
    border-left: 4px solid #6c757d;
  }

  /* Botones de control */
  .session-controls {
    text-align: right;
    margin-bottom: 15px;
  }

  .session-controls .btn {
    margin-left: 5px;
  }

  /* Estilos para timeline dentro de sesiones */
  .session-timeline {
    position: relative;
    padding: 15px 0;
  }

  .session-timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 25px;
    height: 100%;
    width: 2px;
    background: linear-gradient(to bottom, #007bff, #28a745, #ffc107, #dc3545);
  }

  .timeline-item-session {
    position: relative;
    margin-bottom: 25px;
    padding-left: 60px;
  }

  .timeline-marker-session {
    position: absolute;
    left: 16px;
    top: 5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 10px;
    z-index: 1;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
  }

  .timeline-content-session {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    transition: transform 0.2s ease;
  }

  .timeline-content-session:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
  }

  .timeline-item-session.timeline-success .timeline-content-session {
    border-left: 3px solid #28a745;
  }

  .timeline-item-session.timeline-danger .timeline-content-session {
    border-left: 3px solid #dc3545;
  }

  .timeline-item-session.timeline-info .timeline-content-session {
    border-left: 3px solid #17a2b8;
  }

  .timeline-header-session {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    border-bottom: 1px solid #f8f9fa;
    padding-bottom: 8px;
  }

  .timeline-title-session {
    margin: 0;
    color: #495057;
    font-weight: 600;
    font-size: 1em;
  }

  .timeline-date-session {
    color: #6c757d;
    font-size: 0.8em;
    white-space: nowrap;
  }

  .timeline-description-session {
    margin-bottom: 12px;
    color: #212529;
    line-height: 1.5;
    font-size: 0.9em;
  }

  .timeline-details-session {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 12px;
    margin-bottom: 8px;
    font-size: 0.85em;
  }

  .timeline-technical-session {
    background: #e9ecef;
    border-radius: 6px;
    padding: 8px;
    font-size: 0.8em;
  }

  .timeline-params-session .card-body {
    padding: 8px;
    max-height: 150px;
    overflow-y: auto;
    font-size: 0.8em;
  }

  /* Responsive para timeline de sesión */
  @media (max-width: 768px) {
    .session-timeline::before {
      left: 12px;
    }

    .timeline-item-session {
      padding-left: 40px;
    }

    .timeline-marker-session {
      left: 4px;
      width: 16px;
      height: 16px;
      font-size: 8px;
    }

    .timeline-header-session {
      flex-direction: column;
      align-items: flex-start;
    }

    .timeline-date-session {
      margin-top: 5px;
    }
  }
</style>

<script>
  // Función para actualizar las estadísticas en tiempo real
  function actualizarEstadisticas() {
    // Se puede implementar AJAX aquí si es necesario
  }

  // Auto-refresh opcional cada 30 segundos
  // setInterval(actualizarEstadisticas, 30000);

  // Función global para toggle de sesiones
  function toggleSession(sessionId) {
    const sessionContainer = document.getElementById('session-' + sessionId);
    const toggleIcon = document.getElementById('toggle-' + sessionId);

    if (sessionContainer) {
      if (sessionContainer.style.display === 'none' || sessionContainer.style.display === '') {
        // Mostrar sesión
        sessionContainer.style.display = 'block';
        if (toggleIcon) {
          toggleIcon.classList.remove('fa-chevron-down');
          toggleIcon.classList.add('fa-chevron-up');
        }
      } else {
        // Ocultar sesión
        sessionContainer.style.display = 'none';
        if (toggleIcon) {
          toggleIcon.classList.remove('fa-chevron-up');
          toggleIcon.classList.add('fa-chevron-down');
        }
      }
    }
  }

  // Funcionalidad de agrupación de sesiones
  document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad para expandir/colapsar todas las sesiones
    function expandirTodasSesiones() {
      const sessionContainers = document.querySelectorAll('.session-logs-container');
      const toggleIcons = document.querySelectorAll('.session-toggle');

      sessionContainers.forEach(container => {
        container.style.display = 'block';
      });

      toggleIcons.forEach(icon => {
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
      });
    }

    function colapsarTodasSesiones() {
      const sessionContainers = document.querySelectorAll('.session-logs-container');
      const toggleIcons = document.querySelectorAll('.session-toggle');

      sessionContainers.forEach(container => {
        container.style.display = 'none';
      });

      toggleIcons.forEach(icon => {
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
      });
    }

    // Agregar botones de control si no existen
    const auditTimeline = document.querySelector('.audit-timeline');
    if (auditTimeline && document.querySelectorAll('.session-group').length > 0) {
      // Verificar si los botones ya existen
      if (!document.querySelector('.session-controls')) {
        const controlsDiv = document.createElement('div');
        controlsDiv.className = 'session-controls mb-3 text-end';
        controlsDiv.innerHTML = `
          <button type="button" class="btn btn-sm btn-outline-primary mr-2" onclick="expandirTodasSesiones()">
            <i class="fas fa-expand-arrows-alt"></i> Expandir Todo
          </button>
          <button type="button" class="btn btn-sm btn-outline-secondary" onclick="colapsarTodasSesiones()">
            <i class="fas fa-compress-arrows-alt"></i> Colapsar Todo
          </button>
        `;
        auditTimeline.parentNode.insertBefore(controlsDiv, auditTimeline);
      }
    }

    // Hacer las funciones globales para los botones
    window.expandirTodasSesiones = expandirTodasSesiones;
    window.colapsarTodasSesiones = colapsarTodasSesiones;
    window.toggleSession = toggleSession;

    // Estado inicial: colapsar todas las sesiones por defecto
    setTimeout(() => {
      colapsarTodasSesiones();
    }, 100);
  });
</script>