<?php if ($this->reserva): ?>
  <div class="mb-3">
    <h6><i class="fas fa-user"></i> Información de la Reserva</h6>
    <div class="row">
      <div class="col-md-6">
        <p><strong>ID Reserva:</strong> <?= $this->reserva->id ?></p>
        <p><strong>Cliente:</strong> <?= $this->reserva->reserva_nombre_cliente ?>
          <?= $this->reserva->reserva_apellido_cliente ?>
        </p>
        <p><strong>Email:</strong> <?= $this->reserva->reserva_correo ?></p>
      </div>
      <div class="col-md-6">
        <p><strong>Documento:</strong> <?= $this->reserva->reserva_documento ?></p>
        <p><strong>Teléfono:</strong> <?= $this->reserva->reserva_telefono ?></p>
        <p><strong>Fecha Compra:</strong> <?= date('d/m/Y H:i', strtotime($this->reserva->reserva_fecha)) ?></p>
      </div>
    </div>
  </div>

  <hr>

  <h6><i class="fas fa-ticket-alt"></i> Boletas de la Reserva (<?= count($this->boletas) ?> boletas)</h6>

  <?php if (count($this->boletas) > 0): ?>
    <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead class="thead-light">
          <tr>
            <th>Número boleta</th>
            <th>Nombre</th>
            <th>Documento</th>
            <!-- <th>Evento</th> -->
            <th>Estado</th>
            <th>Fecha creación</th>
            <th>Fecha validación</th>
            <th>Usuario que valid&oacute;</th>
            <th>QR/PDF</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($this->boletas as $boleta): ?>
            <?php if ($boleta->boleta_estado == 3) continue; ?>
            <tr>
              <td>
                <strong><?= $boleta->boleta_numero_ticket ?></strong>
              </td>
              <td>
                <?= $boleta->invitadoInfo->invitadoReserva_nombre_invitado . ' ' . $boleta->invitadoInfo->invitadoReserva_apellido_invitado ?>
              </td>
              <td>
                <?= $boleta->boleta_documento ?>
              </td>
              <!-- <td>
                <small>
                  <?= $boleta->evento_titulo ?><br>
                  <span class="text-muted"><?= date('d/m/Y', strtotime($boleta->evento_fecha)) ?></span><br>
                  <span class="text-muted"><?= $boleta->evento_lugar ?></span>
                </small>
              </td> -->
              <td>
                <?php if ($boleta->boleta_estado == 2): ?>
                  <span class="badge text-bg-success">
                    <i class="fas fa-check"></i> <?= $boleta->estado_texto ?>
                  </span>
                <?php elseif ($boleta->boleta_estado == 1): ?>
                  <span class="badge text-bg-warning">
                    <i class="fas fa-clock"></i> <?= $boleta->estado_texto ?>
                  </span>
                <?php else: ?>
                  <span class="badge text-bg-secondary">
                    <i class="fas fa-question"></i> <?= $boleta->estado_texto ?>
                  </span>
                <?php endif; ?>
              </td>
              <td>
                <small><?= date('d/m/Y H:i', strtotime($boleta->boleta_fecha_creacion)) ?></small>
              </td>
              <td>
                <?php if ($boleta->boleta_fecha_validacion): ?>
                  <small><?= date('d/m/Y H:i', strtotime($boleta->boleta_fecha_validacion)) ?></small>
                  <br><small class="text-muted">Método: <?= $boleta->boleta_metodo_validacion ?></small>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($boleta->usuarioValidador->user_user): ?>
                  <?= $boleta->usuarioValidador->user_user ?>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <!-- <button type="button" class="btn btn-sm btn-outline-primary"
                  onclick="mostrarQR('<?= $boleta->boleta_uid ?>', '<?= $boleta->boleta_numero_ticket ?>')"
                  title="Ver código QR">
                  <i class="fas fa-qrcode"></i>
                </button> -->
                <?php if (file_exists(PDFS_PATH_NEWS . "boleta_cena_{$boleta->boleta_uid}.pdf")): ?>
                  <a type="button" class="btn btn-sm btn-outline-danger" target="_blank"
                    href="/pdfs_news/boleta_cena_<?= $boleta->boleta_uid ?>.pdf" title="Ver PDF">
                    <i class="fas fa-file-pdf"></i>
                  </a>

                  <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">
                    UID: <?= ($boleta->boleta_uid) ?>
                  </small>
                <?php else: ?>
                  <span class="text-muted">PDF no disponible</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Resumen de estados -->
    <div class="row mt-3">
      <div class="col-12">
        <div class="alert alert-info">
          <h6><i class="fas fa-info-circle"></i> Resumen</h6>
          <?php
          $validadas = count(array_filter($this->boletas, function ($b) {
            return $b->boleta_estado == 2;
          }));
          $vendidas = count(array_filter($this->boletas, function ($b) {
            return $b->boleta_estado == 1;
          }));
          $canceladas = count(array_filter($this->boletas, function ($b) {
            return $b->boleta_estado == 3;
          }));
          $total = count($this->boletas);
          ?>
          <div class="row">
            <div class="col-md-3">
              <span class="badge text-bg-success"><?= $validadas ?> Validadas</span>
            </div>
            <div class="col-md-3">
              <span class="badge text-bg-warning"><?= $vendidas ?> Sin validar</span>
            </div>
            <div class="col-md-3">
              <span class="badge text-bg-secondary"><?= $canceladas ?> Canceladas</span>
            </div>
            <div class="col-md-3">
              <span class="badge text-bg-primary"><?= $total ?> Total</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Botones de acción -->
    <div class="row mt-3 d-none">
      <div class="col-12">
        <button type="button" class="btn btn-primary btn-sm" onclick="descargarPDF(<?= $b->boleta_uid ?>)">
          <i class="fas fa-file-pdf"></i> Descargar PDF
        </button>
        <button type="button" class="btn btn-info btn-sm" onclick="enviarPorEmailDetalle(<?= $this->reservaId ?>)">
          <i class="fas fa-envelope"></i> Enviar por Email
        </button>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-warning">
      <i class="fas fa-exclamation-triangle"></i>
      No se encontraron boletas para esta reserva.
    </div>
  <?php endif; ?>

<?php else: ?>
  <div class="alert alert-danger">
    <i class="fas fa-times-circle"></i>
    No se encontró información de la reserva.
  </div>
<?php endif; ?>

<style>
  .table-sm td,
  .table-sm th {
    padding: 0.3rem;
    font-size: 0.875rem;
  }
</style>