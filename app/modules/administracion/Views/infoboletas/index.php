<h1 class="titulo-principal"><i class="fas fa-ticket-alt"></i> Boletería</h1>

<div class="container-fluid">

  <?php
  $flashMessage = Session::getInstance()->get('flash_message');
  $flashType    = Session::getInstance()->get('flash_type');
  if ($flashMessage) {
    $alertClass = $flashType === 'error' ? 'alert-danger' : 'alert-success';
    $icon       = $flashType === 'error' ? 'fas fa-exclamation-triangle' : 'fas fa-check-circle';
    echo "<div class='alert {$alertClass} alert-dismissible fade show mb-3' role='alert'>
            <i class='{$icon}'></i> {$flashMessage}
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
    Session::getInstance()->set('flash_message', null);
    Session::getInstance()->set('flash_type', null);
  }
  ?>

  <!-- ══ ESTADÍSTICAS ══ -->
  <div class="row g-3 mb-3 mt-2">
    <div class="col-md-3 col-6">
      <div class="div-dashboard" style="margin-top:0;">
        <div style="padding:18px 20px;display:flex;align-items:center;gap:16px;">
          <div class="stat-icon" style="background:#3b82f620;color:#3b82f6;">
            <i class="fas fa-ticket-alt"></i>
          </div>
          <div>
            <div class="stat-number"><?= number_format($this->estadisticas->total_boletas) ?></div>
            <div class="stat-label">Total boletas</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="div-dashboard" style="margin-top:0;">
        <div style="padding:18px 20px;display:flex;align-items:center;gap:16px;">
          <div class="stat-icon" style="background:#22c55e20;color:#22c55e;">
            <i class="fas fa-check-circle"></i>
          </div>
          <div>
            <div class="stat-number"><?= number_format($this->estadisticas->boletas_validadas) ?></div>
            <div class="stat-label">Validadas</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="div-dashboard" style="margin-top:0;">
        <div style="padding:18px 20px;display:flex;align-items:center;gap:16px;">
          <div class="stat-icon" style="background:#f59e0b20;color:#f59e0b;">
            <i class="fas fa-clock"></i>
          </div>
          <div>
            <div class="stat-number"><?= number_format($this->estadisticas->boletas_sin_validar) ?></div>
            <div class="stat-label">Sin validar</div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-6">
      <div class="div-dashboard" style="margin-top:0;">
        <div style="padding:18px 20px;display:flex;align-items:center;gap:16px;">
          <div class="stat-icon" style="background:#8b5cf620;color:#8b5cf6;">
            <i class="fas fa-shopping-cart"></i>
          </div>
          <div>
            <div class="stat-number"><?= number_format($this->estadisticas->total_reservas) ?></div>
            <div class="stat-label">Total reservas</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ══ INCONSISTENCIAS ══ -->
  <?php if (is_countable($this->inconsistenciasDocumentosPorReserva) && count($this->inconsistenciasDocumentosPorReserva) > 0): ?>
  <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Inconsistencias de documentos detectadas</strong> —
    Se encontraron <?= count($this->inconsistenciasDocumentosPorReserva) ?> reserva(s) con inconsistencias entre los documentos de invitados y boletas:
    <ul class="mt-2 mb-0">
      <?php foreach ($this->inconsistenciasDocumentosPorReserva as $reservaId => $data): ?>
        <li>
          <strong>Reserva <?= $reservaId ?></strong> (<?= $data['reserva']->reserva_nombre_cliente ?> <?= $data['reserva']->reserva_apellido_cliente ?>):
          <ul>
            <?php foreach ($data['inconsistencias'] as $inc): ?>
              <li>
                <?php if ($inc['tipo'] == 'invitado_sin_boleta'): ?>
                  🚫 Invitado con documento <strong><?= $inc['documento'] ?></strong> no tiene boleta asignada
                <?php elseif ($inc['tipo'] == 'boleta_sin_invitado'): ?>
                  🎫 Boleta <strong><?= $inc['boleta_uid'] ?></strong> con documento <strong><?= $inc['documento'] ?></strong> no corresponde a ningún invitado
                  <?php if ($inc['boleta_estado'] != 3): ?>
                    <span class="badge bg-danger">Estado: <?= $inc['boleta_estado'] ?> (No anulada)</span>
                  <?php endif; ?>
                <?php endif; ?>
              </li>
            <?php endforeach; ?>
          </ul>
        </li>
      <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- ══ PROGRESO DE VALIDACIÓN ══ -->
  <div class="div-dashboard">
    <h2><i class="fas fa-chart-bar"></i> Progreso de validación</h2>
    <div class="pading-dashboard" style="height:auto;">
      <?php
      $porcentaje_validadas = $this->estadisticas->total_boletas > 0
        ? ($this->estadisticas->boletas_validadas / $this->estadisticas->total_boletas) * 100
        : 0;
      ?>
      <div class="progress mb-2" style="height:24px;border-radius:6px;">
        <div class="progress-bar bg-success" role="progressbar"
          style="width:<?= $porcentaje_validadas ?>%;"
          aria-valuenow="<?= $porcentaje_validadas ?>" aria-valuemin="0" aria-valuemax="100">
          <?= number_format($porcentaje_validadas, 1) ?>% Validadas
        </div>
      </div>
      <p style="font-size:.83rem;color:var(--text-muted);margin:0;">
        <strong><?= number_format($this->estadisticas->boletas_validadas) ?></strong> de
        <strong><?= number_format($this->estadisticas->total_boletas) ?></strong> boletas han sido validadas
      </p>
    </div>
  </div>

  <!-- ══ FILTROS ══ -->
  <form method="post" action="<?= $this->route ?>">
    <input type="hidden" name="csrf" value="<?= $this->csrf ?>">
    <div class="content-dashboard">
      <div class="row">
        <div class="col-1">
          <label>ID Reserva</label>
          <label class="input-group">
            <input type="text" class="form-control" name="id_reserva" placeholder="ID"
              value="<?= isset($this->filters->id_reserva) ? $this->filters->id_reserva : '' ?>">
          </label>
        </div>
        <div class="col-2">
          <label>Nombre o Apellido</label>
          <label class="input-group">
            <input type="text" class="form-control" name="nombre_apellido" placeholder="Nombre o apellido..."
              value="<?= isset($this->filters->nombre_apellido) ? $this->filters->nombre_apellido : '' ?>">
          </label>
        </div>
        <div class="col-2">
          <label>Documento</label>
          <label class="input-group">
            <input type="text" class="form-control" name="documento" placeholder="Número documento..."
              value="<?= isset($this->filters->documento) ? $this->filters->documento : '' ?>">
          </label>
        </div>
        <div class="col-2">
          <label>Carnet</label>
          <label class="input-group">
            <input type="text" class="form-control" name="carnet" placeholder="Número de carnet..."
              value="<?= isset($this->filters->carnet) ? $this->filters->carnet : '' ?>">
          </label>
        </div>
        <div class="col-2 d-none">
          <label>Estado de Envío</label>
          <label class="input-group">
            <select class="form-select" name="estado_envio">
              <option value="">Todos los estados</option>
              <option value="sin_enviar" <?= (isset($this->filters->estado_envio) && $this->filters->estado_envio == 'sin_enviar') ? 'selected' : '' ?>>Sin enviar boletería</option>
              <option value="enviado"    <?= (isset($this->filters->estado_envio) && $this->filters->estado_envio == 'enviado')    ? 'selected' : '' ?>>Boletas enviadas</option>
              <option value="completado" <?= (isset($this->filters->estado_envio) && $this->filters->estado_envio == 'completado') ? 'selected' : '' ?>>Completado</option>
              <option value="parcial"    <?= (isset($this->filters->estado_envio) && $this->filters->estado_envio == 'parcial')    ? 'selected' : '' ?>>Validación parcial</option>
            </select>
          </label>
        </div>
        <div class="col-2">
          <label>¿Listo para envío?</label>
          <label class="input-group">
            <select class="form-select" name="listo_envio">
              <option value="">Todos</option>
              <option value="1" <?= (isset($this->filters->listo_envio) && $this->filters->listo_envio == '1') ? 'selected' : '' ?>>Listo</option>
              <option value="0" <?= (isset($this->filters->listo_envio) && $this->filters->listo_envio == '0') ? 'selected' : '' ?>>No listo</option>
            </select>
          </label>
        </div>
        <div class="col-1">
          <label>&nbsp;</label>
          <button type="submit" class="btn btn-block btn-azul" title="Filtrar">
            <i class="fas fa-filter"></i>
          </button>
        </div>
        <div class="col-1">
          <label>&nbsp;</label>
          <a class="btn btn-block btn-azul-claro" href="<?= $this->route ?>?cleanfilter=1" title="Limpiar Filtro">
            <i class="fas fa-eraser"></i>
          </a>
        </div>
      </div>
    </div>
  </form>

  <!-- ══ PAGINACIÓN SUPERIOR ══ -->
  <div align="center">
    <ul class="pagination justify-content-center">
      <?php
      $url = $this->route;
      $min = $this->page - 10; if ($min < 0) $min = 1;
      $max = $this->page + 10;
      if ($this->totalpages > 1) {
        if ($this->page != 1)
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page - 1) . '"> &laquo; Anterior </a></li>';
        for ($i = 1; $i <= $this->totalpages; $i++) {
          if ($this->page == $i)
            echo '<li class="active page-item"><a class="page-link">' . $this->page . '</a></li>';
          elseif ($i <= $max && $i >= $min)
            echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
        }
        if ($this->page != $this->totalpages)
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page + 1) . '">Siguiente &raquo;</a></li>';
      }
      ?>
    </ul>
  </div>

  <!-- ══ TABLA ══ -->
  <div class="content-dashboard">

    <!-- Franja superior -->
    <div class="franja-paginas">
      <div class="row align-items-center">
        <div class="col-5">
          <div class="titulo-registro">Se encontraron <?= $this->register_number ?> Registros</div>
        </div>
        <div class="col-4 text-end">
          <div class="texto-paginas">Registros por Página:</div>
        </div>
        <div class="col-3 text-end d-flex align-items-center justify-content-end gap-2">
          <a href="<?= $this->route ?>/escaneologs" class="btn btn-sm btn-azul-claro">
            <i class="fas fa-list-alt"></i> Logs de validación
          </a>
        </div>
      </div>
    </div>

    <!-- Tabla -->
    <div class="content-table table-responsive">
      <?php if (count($this->lists) > 0): ?>
      <table class="table table-striped table-hover table-administrator text-start">
        <thead>
          <tr>
            <td width="40"><i class="fas fa-exclamation-triangle text-danger"></i></td>
            <td width="40"><i class="fas fa-user-check text-success"></i></td>
            <td width="60">ID</td>
            <td>Cliente</td>
            <td>Documento</td>
            <td>Carnet</td>
            <td width="70">Cantidad</td>
            <td width="60">¿Listo?</td>
            <td width="80">Generadas</td>
            <td width="80">Validadas</td>
            <td width="80" data-bs-toggle="tooltip" title="Invitados a los que aún les falta que se les genere/envíe la boleta">Sin boleta</td>
            <td>Estado envío</td>
            <td width="100">Acciones</td>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->lists as $item): ?>
            <tr>
              <td>
                <?php if (isset($this->inconsistenciasPorReserva[$item->id])): ?>
                  <i class="fas fa-exclamation-triangle text-danger"
                    data-bs-toggle="tooltip" data-bs-html="true"
                    data-bs-title="<?php
                      $inconsistencias = $this->inconsistenciasPorReserva[$item->id];
                      $tooltip = '<strong>Inconsistencias en boletas:</strong><br>';
                      foreach ($inconsistencias as $inc) {
                        $tooltip .= '• Boleta ' . $inc->boleta_uid . ': Doc boleta (' . $inc->documento_boleta . ') ≠ Doc invitado (' . $inc->documento_invitado . ')<br>';
                      }
                      echo htmlspecialchars($tooltip, ENT_QUOTES);
                    ?>" style="cursor:pointer;font-size:1.1em;"></i>
                <?php else: ?>
                  <i class="fas fa-check-circle text-success" data-bs-toggle="tooltip" title="Sin inconsistencias" style="opacity:.3;"></i>
                <?php endif; ?>
              </td>
              <td>
                <?php if (isset($this->inconsistenciasDocumentosPorReserva[$item->id])): ?>
                  <i class="fas fa-user-times text-warning"
                    data-bs-toggle="tooltip" data-bs-html="true"
                    data-bs-title="<?php
                      $inconsistenciasDoc = $this->inconsistenciasDocumentosPorReserva[$item->id]['inconsistencias'];
                      $tooltip = '<strong>Inconsistencias documentos:</strong><br>';
                      foreach ($inconsistenciasDoc as $inc) {
                        if ($inc['tipo'] == 'invitado_sin_boleta')
                          $tooltip .= '• 🚫 Invitado sin boleta: Doc. ' . $inc['documento'] . '<br>';
                        elseif ($inc['tipo'] == 'boleta_sin_invitado')
                          $tooltip .= '• 🎫 Boleta sin invitado: ' . $inc['boleta_uid'] . ' (Doc. ' . $inc['documento'] . ', Estado: ' . $inc['boleta_estado'] . ')<br>';
                      }
                      echo htmlspecialchars($tooltip, ENT_QUOTES);
                    ?>" style="cursor:pointer;font-size:1.1em;"></i>
                  <small class="d-block text-muted"><?= count($this->inconsistenciasDocumentosPorReserva[$item->id]['inconsistencias']) ?></small>
                <?php else: ?>
                  <i class="fas fa-user-check text-success" data-bs-toggle="tooltip" title="Documentos coinciden" style="opacity:.3;"></i>
                <?php endif; ?>
              </td>
              <td><strong><?= $item->id ?></strong></td>
              <td><?= $item->reserva_nombre_cliente ?> <?= $item->reserva_apellido_cliente ?></td>
              <td><?= $item->reserva_documento ?></td>
              <td><?= $item->reserva_numero_carnet ?></td>
              <td><?= $item->reserva_total_personas ?></td>
              <td><?= $item->listo_para_enviar == 1 ? '✔️' : '❌' ?></td>
              <td><span class="badge bg-primary rounded-pill"><?= $item->total_boletas ?? 0 ?></span></td>
              <td><span class="badge bg-success rounded-pill"><?= $item->boletas_validadas ?></span></td>
              <td><span class="badge bg-warning text-dark rounded-pill"><?= $item->invitados_sin_boleta ?></span></td>
              <td>
                <?php if ($item->boletas_validadas == $item->total_boletas && $item->total_boletas > 0): ?>
                  <span class="badge bg-primary">Completado</span>
                <?php elseif ($item->boletas_validadas > 0): ?>
                  <span class="badge bg-warning text-dark">Parcial</span>
                <?php elseif (!$item->reserva_boleteria_reenviada && $item->total_boletas > 0): ?>
                  <span class="badge bg-warning text-dark">Env. parcial</span>
                <?php elseif (!$item->reserva_boleteria_reenviada): ?>
                  <span class="badge bg-secondary">Sin enviar</span>
                <?php elseif ($item->reserva_boleteria_reenviada == 1): ?>
                  <span class="badge bg-success">Enviadas</span>
                <?php endif; ?>
              </td>
              <td>
                <div class="btn-group btn-group-sm">
                  <?php if ($item->total_boletas > 0 && $item->reserva_boleteria_reenviada): ?>
                    <button type="button" class="btn btn-azul" onclick="verDetalles(<?= $item->id ?>)" title="Ver detalles de boletas" data-bs-toggle="tooltip">
                      <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-reenviar-boletas" onclick="confirmarReenviarBoletas(<?= $item->id ?>)" title="Reenviar boletas" data-bs-toggle="tooltip">
                      <i class="fas fa-paper-plane"></i>
                    </button>
                  <?php endif; ?>
                  <?php if ((!$item->total_boletas || !$item->reserva_boleteria_reenviada) && $item->listo_para_enviar == 1): ?>
                    <button type="button" class="btn btn-success btn-generar-boletas" onclick="confirmarGenerarBoletas(<?= $item->id ?>)" title="Generar boletas" data-bs-toggle="tooltip">
                      <i class="fa-solid fa-share"></i>
                    </button>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php else: ?>
        <div class="alert alert-info text-center m-3">
          <i class="fas fa-info-circle"></i>
          No se encontraron reservas con boletas que coincidan con los filtros aplicados.
        </div>
      <?php endif; ?>
    </div>

    <!-- Franja inferior -->
    <div class="franja-paginas" style="border-top:1px solid #CCCCCC;margin-top:2%;">
      <div class="row" style="padding-top:1%;">
        <div class="col-5">
          <div class="titulo-registro">Se encontraron <?= $this->register_number ?> Registros</div>
        </div>
        <div class="col-7"></div>
      </div>
    </div>

  </div>

  <!-- ══ PAGINACIÓN INFERIOR ══ -->
  <div align="center">
    <ul class="pagination justify-content-center">
      <?php
      $url = $this->route;
      $min = $this->page - 10; if ($min < 0) $min = 1;
      $max = $this->page + 10;
      if ($this->totalpages > 1) {
        if ($this->page != 1)
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page - 1) . '"> &laquo; Anterior </a></li>';
        for ($i = 1; $i <= $this->totalpages; $i++) {
          if ($this->page == $i)
            echo '<li class="active page-item"><a class="page-link">' . $this->page . '</a></li>';
          elseif ($i <= $max && $i >= $min)
            echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
        }
        if ($this->page != $this->totalpages)
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page + 1) . '">Siguiente &raquo;</a></li>';
      }
      ?>
    </ul>
  </div>

</div>

<!-- ══ MODAL: Detalles de boletas ══ -->
<div class="modal fade" id="modalDetalles" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl" style="max-width:92%;width:92%;">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--surface);border-bottom:1px solid var(--border);padding:14px 20px;">
        <h4 class="modal-title" style="font-size:.95rem;font-weight:600;display:flex;align-items:center;gap:8px;margin:0;">
          <i class="fas fa-ticket-alt" style="color:var(--brand-green);"></i>
          Detalles de Boletas
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding:16px;background:var(--bg);" id="modalDetallesContent">
        <!-- Contenido cargado dinámicamente -->
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--border);padding:10px 16px;">
        <button type="button" class="btn btn-azul btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ══ MODAL: QR ══ -->
<div class="modal fade" id="modalQR" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--surface);border-bottom:1px solid var(--border);padding:14px 20px;">
        <h4 class="modal-title" style="font-size:.95rem;font-weight:600;display:flex;align-items:center;gap:8px;margin:0;">
          <i class="fas fa-qrcode" style="color:var(--brand-green);"></i>
          Código QR — Boleta <span id="numeroBoleta"></span>
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center" style="padding:24px;background:var(--bg);">
        <div id="qrcode"></div>
        <p class="mt-3" style="font-size:.83rem;color:var(--text-muted);">UID: <span id="uidBoleta"></span></p>
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--border);padding:10px 16px;">
        <button type="button" class="btn btn-azul btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ══ MODAL: Confirmar generación ══ -->
<div class="modal fade" id="modalConfirmarBoletas" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--surface);border-bottom:1px solid var(--border);padding:14px 20px;">
        <h4 class="modal-title" style="font-size:.95rem;font-weight:600;display:flex;align-items:center;gap:8px;margin:0;">
          <i class="fas fa-exclamation-triangle" style="color:#f59e0b;"></i>
          Confirmar Generación de Boletas
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding:16px;background:var(--bg);">
        <p style="font-size:.88rem;">¿Está seguro que desea generar las boletas para la reserva <strong id="reservaIdConfirm"></strong>?</p>
        <div class="alert alert-warning" style="font-size:.83rem;">
          <i class="fas fa-exclamation-triangle"></i>
          <strong>Atención:</strong> Esta acción generará las boletas y las enviará por email al cliente. Una vez realizada no se puede deshacer.
        </div>
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--border);padding:10px 16px;gap:8px;">
        <button type="button" class="btn btn-azul-claro btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times"></i> Cancelar
        </button>
        <button type="button" class="btn btn-success btn-sm" id="btnConfirmarGenerar">
          <i class="fa-solid fa-share"></i> Sí, Generar Boletas
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ══ MODAL: Confirmar reenvío ══ -->
<div class="modal fade" id="modalConfirmarReenvio" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header" style="background:var(--surface);border-bottom:1px solid var(--border);padding:14px 20px;">
        <h4 class="modal-title" style="font-size:.95rem;font-weight:600;display:flex;align-items:center;gap:8px;margin:0;">
          <i class="fas fa-paper-plane" style="color:#f59e0b;"></i>
          Confirmar Reenvío de Boletas
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" style="padding:16px;background:var(--bg);">
        <p style="font-size:.88rem;">¿Está seguro que desea reenviar la boletería para la reserva <strong id="reservaIdConfirmReenvio"></strong>?</p>
        <div class="alert alert-info" style="font-size:.83rem;">
          <i class="fas fa-lightbulb"></i> <strong>Esta acción realizará:</strong>
          <ul class="mb-0 mt-1">
            <li>✅ Incluirá todas las boletas existentes válidas</li>
            <li>✅ Creará nuevas boletas para invitados que no las tengan</li>
            <li>✅ Regenerará archivos QR/PDF faltantes automáticamente</li>
            <li>📧 Enviará el correo con todas las boletas actualizadas</li>
          </ul>
        </div>
        <div class="alert alert-warning" style="font-size:.83rem;">
          <i class="fas fa-exclamation-triangle"></i>
          <strong>Nota:</strong> Ideal para casos donde han cambiado invitados en la reserva.
        </div>
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--border);padding:10px 16px;gap:8px;">
        <button type="button" class="btn btn-azul-claro btn-sm" data-bs-dismiss="modal">
          <i class="fas fa-times"></i> Cancelar
        </button>
        <button type="button" class="btn btn-warning btn-sm" id="btnConfirmarReenvio">
          <i class="fas fa-paper-plane"></i> Sí, Reenviar Boletas
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ══ LOADING OVERLAY ══ -->
<div id="loading-overlay">
  <div class="spinner-border text-light" role="status" style="width:3rem;height:3rem;">
    <span class="visually-hidden">Cargando...</span>
  </div>
</div>

<style>
  /* ── Stat cards ── */
  .stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
  }
  .stat-number {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text-primary, #2d3748);
    line-height: 1.1;
  }
  .stat-label {
    font-size: .75rem;
    color: var(--text-muted, #94a3b8);
    margin-top: 2px;
  }

  /* ── Botón reenviar ── */
  .btn-reenviar-boletas {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
  }
  .btn-reenviar-boletas:hover {
    background-color: #ffca2c;
    border-color: #ffc720;
    color: #212529;
  }
  .btn-reenviar-boletas:disabled { opacity: .5; cursor: not-allowed; }

  /* ── Icono pulsante ── */
  .fas.fa-exclamation-triangle { animation: pulse 2s infinite; }
  @keyframes pulse {
    0%   { opacity: 1; }
    50%  { opacity: .6; }
    100% { opacity: 1; }
  }

  /* ── Loading overlay ── */
  #loading-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,.5);
    z-index: 9999;
    justify-content: center;
    align-items: center;
  }

  /* ── Tooltips ── */
  .tooltip { font-size: 14px !important; }
  .tooltip-inner {
    max-width: 350px !important;
    padding: 12px 15px !important;
    font-size: 13px !important;
    line-height: 1.4 !important;
    text-align: left !important;
  }
</style>

<script>
  function verDetalles(reservaId) {
    fetch(`/administracion/infoboletas/getdetallesboletas?id=${reservaId}`)
      .then(response => response.text())
      .then(data => {
        document.getElementById('modalDetallesContent').innerHTML = data;
        new bootstrap.Modal(document.getElementById('modalDetalles')).show();
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error al cargar los detalles');
      });
  }

  function mostrarQR(uid, numeroBoleta) {
    document.getElementById('numeroBoleta').textContent = numeroBoleta;
    document.getElementById('uidBoleta').textContent = uid;
    document.getElementById('qrcode').innerHTML =
      '<div class="border p-4"><h4>' + uid + '</h4><p>Código QR aquí</p><img src="/images_sales/qrs_news/' + uid + '.png" alt="Código QR" /></div>';
    new bootstrap.Modal(document.getElementById('modalQR')).show();
  }

  function descargarPDF(uid) {
    window.open('/pdfs/' + reservaId + '.pdf', '_blank');
  }

  function enviarPorEmailDetalle(reservaId) {
    if (confirm('¿Enviar las boletas por email al cliente?')) {
      enviarBoletas(reservaId);
    }
  }

  function enviarBoletas(reservaId) {
    if (confirm('¿Está seguro de reenviar las boletas por email?')) {
      fetch(`/administracion/infoboletas/reenviarBoletas?id=${reservaId}`, { method: 'POST' })
        .then(response => response.json())
        .then(data => {
          if (data.success) alert('Boletas reenviadas correctamente');
          else alert('Error al reenviar las boletas: ' + data.message);
        })
        .catch(error => { console.error('Error:', error); alert('Error al procesar la solicitud'); });
    }
  }

  function confirmarReenviarBoletas(reservaId) {
    document.getElementById('reservaIdConfirmReenvio').textContent = reservaId;
    const btnConfirmar = document.getElementById('btnConfirmarReenvio');
    btnConfirmar.onclick = function () {
      bootstrap.Modal.getInstance(document.getElementById('modalConfirmarReenvio')).hide();
      reenviarBoletas(reservaId);
    };
    new bootstrap.Modal(document.getElementById('modalConfirmarReenvio')).show();
  }

  function reenviarBoletas(reservaId) {
    document.getElementById('loading-overlay').style.display = 'flex';
    document.querySelectorAll('.btn-reenviar-boletas').forEach(btn => { btn.disabled = true; btn.style.opacity = '0.5'; });
    setTimeout(() => {
      window.location.href = `/administracion/infoboletas/reenviarboletassimple?id_reserva=${reservaId}&forzar_reenvio=1`;
    }, 500);
  }

  function exportarExcel() {
    window.location.href = '/administracion/infoboletas/exportarExcel';
  }

  function generarBoletas(reservaId) {
    document.getElementById('loading-overlay').style.display = 'flex';
    document.querySelectorAll('.btn-generar-boletas').forEach(btn => { btn.disabled = true; btn.style.opacity = '0.5'; });
    setTimeout(() => {
      window.location.href = `/administracion/infoboletas/generarboletas?id_reserva=${reservaId}`;
    }, 500);
  }

  function confirmarGenerarBoletas(reservaId) {
    document.getElementById('reservaIdConfirm').textContent = reservaId;
    const btnConfirmar = document.getElementById('btnConfirmarGenerar');
    btnConfirmar.onclick = function () {
      bootstrap.Modal.getInstance(document.getElementById('modalConfirmarBoletas')).hide();
      generarBoletas(reservaId);
    };
    new bootstrap.Modal(document.getElementById('modalConfirmarBoletas')).show();
  }

  document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) {
      return new bootstrap.Tooltip(el, { html: true, placement: 'auto' });
    });
  });
</script>
