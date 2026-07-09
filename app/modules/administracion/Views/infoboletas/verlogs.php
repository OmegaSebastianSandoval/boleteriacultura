<h1 class="titulo-principal"><i class="fas fa-list-alt"></i>Logs del Sistema de Boletas (Reenvío y Reenvío Simple)</h1>

<?php
// Mostrar mensajes flash de la sesión
$flashMessage = Session::getInstance()->get('flash_message');
$flashType = Session::getInstance()->get('flash_type');

if ($flashMessage) {
  $alertClass = $flashType === 'error' ? 'alert-danger' : 'alert-success';
  $icon = $flashType === 'error' ? 'fas fa-exclamation-triangle' : 'fas fa-check-circle';
  echo "<div class='alert {$alertClass} alert-dismissible fade show' role='alert'>
            <i class='{$icon}'></i> {$flashMessage}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";

  // Limpiar el mensaje de la sesión
  Session::getInstance()->set('flash_message', null);
  Session::getInstance()->set('flash_type', null);
}
?>

<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <!-- Filtros -->
      <div class="card mb-4">
        <div class="card-header">
          <i class="fas fa-filter"></i> Filtros de logs
        </div>
        <div class="card-body">
          <form method="GET" action="<?= $this->route ?>/verlogs" class="row g-3 align-items-center">
            <div class="col-md-3">
              <label for="tipo" class="form-label">Tipo de log</label>
              <select class="form-select" id="tipo" name="tipo">
                <option value="">Todos los tipos</option>
                <?php foreach ($this->tiposLogs as $tipo): ?>
                  <option value="<?= $tipo ?>" <?= ($this->tipoFiltro == $tipo) ? 'selected' : '' ?>>
                    <?= $tipo ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-3">
              <label for="fecha_desde" class="form-label">Fecha desde</label>
              <input type="date" class="form-control" id="fecha_desde" name="fecha_desde"
                value="<?= $this->fechaDesde ?>">
            </div>

            <div class="col-md-3">
              <label for="fecha_hasta" class="form-label">Fecha hasta</label>
              <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta"
                value="<?= $this->fechaHasta ?>">
            </div>
            <div class="col-md-3">

       
          <label for="reserva_id" class="form-label">ID Reserva:</label>
          <input type="text" class="form-control" id="reserva_id" name="reserva_id" 
                 value="<?php echo htmlspecialchars($reservaId); ?>" placeholder="Buscar por ID de reserva">
          </div>
                    <div class="col-md-6">

          <div class="checkbox">
            <label>
              <input type="checkbox" id="sesion_completa" name="sesion_completa" value="1" 
                     <?php echo ($sesionCompleta == '1') ? 'checked' : ''; ?>>
              Mostrar sesión completa (incluye logs relacionados por tiempo)
            </label>
          </div>
        </div>            
        <div class="col-md-3">
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-search"></i> Filtrar
                </button>
              </div>
            </div>
            
 
               <div class="col-md-3">
              <div class="d-grid">
                <a href="<?= $this->route ?>/verlogs" class="btn btn-secondary">
                  <i class="fas fa-search"></i> Limpiar
                </a>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Estadísticas de logs -->
      <div class="row mb-4 d-none">
        <div class="col-md-4">
          <div class="card text-white bg-info">
            <div class="card-header text-info">
              <i class="fas fa-list"></i> Total Logs
            </div>
            <div class="card-body">
              <h4 class="card-title"><?= number_format($this->totalLogs) ?></h4>
              <p class="card-text">Logs encontrados</p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card text-white bg-secondary">
            <div class="card-header text-secondary">
              <i class="fas fa-file-alt"></i> Página Actual
            </div>
            <div class="card-body">
              <h4 class="card-title"><?= $this->page ?> de <?= $this->pages ?></h4>
              <p class="card-text">Páginas totales</p>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card text-white bg-warning">
            <div class="card-header text-warning">
              <i class="fas fa-clock"></i> Última Actualización
            </div>
            <div class="card-body">
              <h4 class="card-title"><?= date('H:i:s') ?></h4>
              <p class="card-text">Hora actual</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de logs -->
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <span><i class="fas fa-history"></i> Historial de Logs</span>
          <a href="<?= $this->route ?>" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver al Index
          </a>
        </div>
        <div class="card-body">
          <?php if (!($this->logs)): ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i> No se encontraron logs con los filtros aplicados.
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-striped table-hover">
                <thead class="table-dark">
                  <tr>
                    <th><i class="fas fa-calendar"></i> Fecha/Hora</th>
                    <th><i class="fas fa-tag"></i> Tipo</th>
                    <th><i class="fas fa-file-alt"></i> Mensaje</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($this->logs as $log): ?>
                    <tr>
                      <td>
                        <small class="text-muted">
                          <?= date('d/m/Y H:i:s', strtotime($log->log_fecha)) ?>
                        </small>
                      </td>
                      <td>
                        <?php
                        $tipoClass = 'badge ';
                        switch ($log->log_tipo) {
                          case 'REENVIO BOLETERIA - INICIO':
                          case 'REENVIO SIMPLE - INICIO':
                            $tipoClass .= 'bg-primary';
                            break;
                          case 'REENVIO BOLETERIA - COMPLETADO':
                          case 'REENVIO SIMPLE - COMPLETADO':
                            $tipoClass .= 'bg-success';
                            break;
                          case 'REENVIO BOLETERIA - ERROR':
                          case 'REENVIO BOLETERIA - EMAIL FALLIDO':
                          case 'REENVIO BOLETERIA - EMAIL INVITADO ERROR':
                          case 'REENVIO BOLETERIA - EMAIL INVITADO EXCEPTION':
                          case 'REENVIO SIMPLE - ERROR':
                          case 'REENVIO SIMPLE - ERROR VALIDADA':
                          case 'REENVIO SIMPLE - ERROR EMAIL':
                            $tipoClass .= 'bg-danger';
                            break;
                          case 'REENVIO BOLETERIA - ADVERTENCIA':
                          case 'REENVIO BOLETERIA - EMAIL INVITADO SIN QR':
                            $tipoClass .= 'bg-warning text-dark';
                            break;
                          case 'REENVIO BOLETERIA - EMAIL INICIADO':
                          case 'REENVIO BOLETERIA - EMAIL PRINCIPAL':
                          case 'REENVIO BOLETERIA - EMAIL INVITADO':
                          case 'REENVIO SIMPLE - RESERVA':
                          case 'REENVIO SIMPLE - DATOS':
                          case 'REENVIO SIMPLE - ANALISIS':
                          case 'REENVIO SIMPLE - PREPARANDO ENVIO':
                            $tipoClass .= 'bg-info';
                            break;
                          case 'REENVIO BOLETERIA - EMAIL ENVIADO':
                          case 'REENVIO BOLETERIA - EMAIL INVITADO ENVIADO':
                          case 'REENVIO BOLETERIA - RESUMEN EMAILS':
                          case 'REENVIO SIMPLE - EMAIL ENVIADO':
                          case 'REENVIO SIMPLE - BOLETA CREADA':
                          case 'REENVIO SIMPLE - MARCADA REENVIO':
                            $tipoClass .= 'bg-success';
                            break;
                          case 'GENERACION QR - INICIO':
                          case 'GENERACION QR - PROCESANDO':
                          case 'GENERACION QR - COMPLETADO':
                          case 'REENVIO SIMPLE - CREANDO BOLETA':
                            $tipoClass .= 'bg-primary';
                            break;
                          case 'REENVIO SIMPLE - RESUMEN':
                          case 'REENVIO SIMPLE - INVALIDADA':
                          case 'REENVIO SIMPLE - BOLETA HUERFANA':
                          case 'REENVIO SIMPLE - ARCHIVOS FALTANTES':
                            $tipoClass .= 'bg-warning text-dark';
                            break;
                          default:
                            $tipoClass .= 'bg-secondary';
                        }
                        ?>
                        <span class="<?= $tipoClass ?>"><?= $log->log_tipo ?></span>
                      </td>
                      <td>
                        <div class="log-message">
                          <?= nl2br(htmlspecialchars($log->log_log)) ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <!-- Paginación -->
            <?php if ($this->pages > 1): ?>
              <nav aria-label="Navegación de logs" class="mt-4">
                <ul class="pagination justify-content-center">
                  <?php
                  $startPage = max(1, $this->page - 2);
                  $endPage = min($this->pages, $this->page + 2);

                  if ($startPage > 1): ?>
                    <li class="page-item">
                      <a class="page-link"
                        href="?page=1&tipo=<?= urlencode($this->tipoFiltro) ?>&fecha_desde=<?= $this->fechaDesde ?>&fecha_hasta=<?= $this->fechaHasta ?>&reserva_id=<?= $this->reservaId ?>&sesion_completa=<?= $this->sesionCompleta ?>">Primera</a>
                    </li>
                  <?php endif; ?>

                  <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= ($i == $this->page) ? 'active' : '' ?>">
                      <a class="page-link"
                        href="?page=<?= $i ?>&tipo=<?= urlencode($this->tipoFiltro) ?>&fecha_desde=<?= $this->fechaDesde ?>&fecha_hasta=<?= $this->fechaHasta ?>&reserva_id=<?= $this->reservaId ?>&sesion_completa=<?= $this->sesionCompleta ?>">
                        <?= $i ?>
                      </a>
                    </li>
                  <?php endfor; ?>

                  <?php if ($endPage < $this->pages): ?>
                    <li class="page-item">
                      <a class="page-link"
                        href="?page=<?= $this->pages ?>&tipo=<?= urlencode($this->tipoFiltro) ?>&fecha_desde=<?= $this->fechaDesde ?>&fecha_hasta=<?= $this->fechaHasta ?>&reserva_id=<?= $this->reservaId ?>">Última</a>
                    </li>
                  <?php endif; ?>
                </ul>
              </nav>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .log-message {
    max-width: 600px;
    word-wrap: break-word;
  }

  .table-responsive {
    /* max-height: 600px;
    overflow-y: auto; */
  }

  .badge {
    font-size: 0.75em;
  }
</style>

<script>
  // Auto-refresh cada 30 segundos
  setTimeout(function () {
    if (window.location.search.includes('auto_refresh=1')) {
      window.location.reload();
    }
  }, 30000);

  // Función para agregar auto-refresh
  function toggleAutoRefresh () {
    const url = new URL(window.location);
    if (url.searchParams.has('auto_refresh')) {
      url.searchParams.delete('auto_refresh');
    } else {
      url.searchParams.set('auto_refresh', '1');
    }
    window.location.href = url.toString();
  }
</script>