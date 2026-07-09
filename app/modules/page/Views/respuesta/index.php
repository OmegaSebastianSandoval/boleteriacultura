<?php if (isset($this->error)): ?>

  <div class="av-wrap container py-2 py-lg-3 mb-3 mb-lg-5">
    <div class="card-principal av-card">
      <div class="av-success-header text-center av-status-danger">
        <div class="av-success-icon"><i class="fas fa-exclamation-triangle"></i></div>
        <h1 class="av-title">Error</h1>
        <p class="av-subtitle">No pudimos procesar tu solicitud</p>
      </div>

      <div class="rs-card av-summary-card mb-3">
        <div class="rs-card-body text-center">
          <?php
          $errores = [
            'invalid_reference' => 'Referencia de reserva inválida',
            'reservation_not_found' => 'No se encontró la reserva',
            'connection_failed' => 'Error de conexión con el sistema de pagos',
            'no_reservation_id' => 'No se proporcionó ID de reserva'
          ];
          $mensajeError = $errores[$this->error] ?? 'Error desconocido';
          ?>
          <p class="av-desc mb-0"><?= htmlspecialchars($mensajeError) ?></p>
        </div>
      </div>

      <div class="av-actions">
        <a href="/page/evento/" class="av-btn-primary">
          <i class="fa-solid fa-arrow-left"></i> Volver a Eventos
        </a>
      </div>
    </div>
  </div>

<?php else: ?>

  <?php
  $tieneMesas = $this->mesaInfo && $this->categoria;
  $mostrarInfoPago = ($this->reserva->reserva_estado == '2' && $this->reserva->reserva_numero_cuotas)
    || ($this->reserva->reserva_estado == '3' && $this->reserva->reserva_franquicia);
  ?>

  <div class="rs-page container py-2 py-lg-3 mb-3 mb-lg-5">
    <div class="card-principal">

      <!-- ── Topbar: volver · paso a paso | timer ── -->
      <div class="rv-topbar mb-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <a href="/page/evento/" class="sb-back-btn">
            <i class="fa-solid fa-angle-left"></i>&nbsp; Volver
          </a>
          <div class="step-indicator">
            <div class="step-badge">
              <i class="fa-solid fa-receipt me-2"></i>
              <span class="step-text">Paso 5 de 5</span>
              <span class="step-divider">•</span>
              <span class="step-name">Confirmación de pago</span>
            </div>
          </div>
        </div>
        <div id="tiempo-restante-reserva" class="rv-timer">
          <i class="fa-solid fa-clock rv-timer-icon"></i>
          <div class="rv-timer-display">
            <span id="minutos">00</span><span class="rv-timer-colon">:</span><span id="segundos">00</span>
          </div>
          <span class="rv-timer-label">tiempo restante</span>
          <input type="hidden" id="minutosHidden" name="minutosHidden">
        </div>
      </div>

      <!-- ── Encabezado de estado ── -->
      <div class="av-success-header text-center av-status-<?= htmlspecialchars($this->estadoInfo['color']) ?>">
        <div class="av-success-icon"><i class="<?= htmlspecialchars($this->estadoInfo['icono']) ?>"></i></div>
        <h1 class="av-title"><?= htmlspecialchars($this->estadoInfo['titulo']) ?></h1>
        <p class="av-subtitle"><?= htmlspecialchars($this->estadoInfo['mensaje']) ?></p>
        <p class="av-desc"><?= htmlspecialchars($this->estadoInfo['descripcion']) ?></p>
      </div>

      <!-- ── Cuerpo scrollable ── -->
      <div class="rs-main-card">
        <div class="row g-3">

          <!-- LEFT: info pago + evento + mesas + importante -->
          <div class="col-lg-4">

            <?php if ($mostrarInfoPago): ?>
              <div class="rs-card mb-3">
                <div class="rs-card-header">
                  <i class="fa-solid fa-credit-card me-2"></i>
                  <h5>Información de pago</h5>
                </div>
                <div class="rs-card-body">
                  <?php if ($this->reserva->reserva_estado == '2'): ?>
                    <p class="rs-data-row">
                      <span class="rs-data-key">Método</span>
                      <span class="rs-data-val">Cargo a la acción</span>
                    </p>
                    <p class="rs-data-row rs-data-row-last">
                      <span class="rs-data-key">Cuotas</span>
                      <span class="rs-data-val"><?= (int) $this->reserva->reserva_numero_cuotas ?> cuota<?= $this->reserva->reserva_numero_cuotas > 1 ? 's' : '' ?></span>
                    </p>
                  <?php elseif ($this->reserva->reserva_estado == '3'): ?>
                    <p class="rs-data-row <?= $this->reserva->reserva_cus ? '' : 'rs-data-row-last' ?>">
                      <span class="rs-data-key">Método</span>
                      <span class="rs-data-val"><?= htmlspecialchars($this->reserva->reserva_franquicia) ?></span>
                    </p>
                    <?php if ($this->reserva->reserva_cus): ?>
                      <p class="rs-data-row rs-data-row-last">
                        <span class="rs-data-key">Autorización</span>
                        <span class="rs-data-val"><?= htmlspecialchars($this->reserva->reserva_cus) ?></span>
                      </p>
                    <?php endif; ?>
                  <?php endif; ?>
                </div>
              </div>
            <?php endif; ?>

            <!-- Información del Evento -->
            <div class="rs-card mb-3">
              <div class="rs-card-header">
                <i class="fa-solid fa-calendar-days me-2"></i>
                <h5>Información del evento</h5>
              </div>
              <div class="rs-card-body">
                <?php if ($this->evento): ?>
                  <p class="rs-data-row">
                    <span class="rs-data-key">Evento</span>
                    <span class="rs-data-val"><?= htmlspecialchars($this->evento->evento_titulo) ?></span>
                  </p>
                  <p class="rs-data-row">
                    <span class="rs-data-key">Fecha</span>
                    <span class="rs-data-val"><?= date('d/m/Y', strtotime($this->evento->evento_fecha)) ?></span>
                  </p>
                <?php endif; ?>
                <p class="rs-data-row">
                  <span class="rs-data-key">Reserva</span>
                  <span class="rs-data-val"><?= date('d/m/Y H:i', strtotime($this->reserva->reserva_fecha)) ?></span>
                </p>
                <p class="rs-data-row rs-data-row-last">
                  <span class="rs-data-key">Personas</span>
                  <span class="rs-data-val"><?= (int) $this->totalPersonas ?></span>
                </p>
              </div>
            </div>

            <!-- Información de Mesa y Categoría -->
            <?php if ($tieneMesas): ?>
              <div class="rs-card mb-3">
                <div class="rs-card-header">
                  <i class="fa-solid fa-table me-2"></i>
                  <h5>Mesa<?= count($this->mesaInfo) != 1 ? 's' : '' ?> reservada<?= count($this->mesaInfo) != 1 ? 's' : '' ?></h5>
                </div>
                <div class="rs-card-body p-0">
                  <?php foreach ($this->mesaInfo as $index => $mesa): ?>
                    <?php
                    $esZonaJuvenil = $mesa->piso_nombre == 'ZONA JUVENIL';
                    $pisoMostrar = $esZonaJuvenil ? $mesa->ambiente_nombre : $mesa->piso_nombre;
                    $ambienteMostrar = $esZonaJuvenil ? $mesa->piso_nombre : $mesa->ambiente_nombre;
                    ?>
                    <div class="rs-mesa-bloque">
                      <div class="rs-mesa-dato">
                        <span class="rs-mesa-key">Mesa</span>
                        <span class="rs-mesa-val"><?= htmlspecialchars($mesa->mesa_nombre) ?></span>
                      </div>
                      <div class="rs-mesa-dato">
                        <span class="rs-mesa-key">Código</span>
                        <span class="rs-mesa-val"><?= htmlspecialchars($mesa->mesa_codigo) ?></span>
                      </div>
                      <div class="rs-mesa-dato">
                        <span class="rs-mesa-key">Capacidad</span>
                        <span class="rs-mesa-val"><?= (int) $mesa->mesa_capacidad ?> personas</span>
                      </div>
                      <div class="rs-mesa-dato">
                        <span class="rs-mesa-key">Piso</span>
                        <span class="rs-mesa-val"><?= htmlspecialchars($pisoMostrar) ?></span>
                      </div>
                      <div class="rs-mesa-dato rs-mesa-dato-last" style="grid-column: 1 / -1;">
                        <span class="rs-mesa-key">Ambiente</span>
                        <span class="rs-mesa-val"><?= htmlspecialchars($ambienteMostrar) ?></span>
                      </div>
                    </div>
                    <?php if ($index < count($this->mesaInfo) - 1): ?>
                      <div class="rs-mesa-sep"></div>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>

            <!-- Información importante -->
            <div class="rs-card mb-3 mb-lg-0">
              <div class="rs-card-header d-flex justify-content-between align-items-center">
                <h5><i class="fa-solid fa-circle-info me-2"></i>Información importante</h5>
                <span class="rs-badge">#<?= (int) $this->reserva->id ?></span>
              </div>
              <div class="rs-card-body">
                <?php if ($this->reserva->reserva_fecha_pago): ?>
                  <p class="rs-data-row rs-data-row-last">
                    <span class="rs-data-key">Fecha de pago</span>
                    <span class="rs-data-val"><?= date('d/m/Y H:i', strtotime($this->reserva->reserva_fecha_pago)) ?></span>
                  </p>
                <?php endif; ?>
              </div>
            </div>

          </div>

          <!-- RIGHT: lista invitados -->
          <div class="col-lg-8">
            <div class="rs-card">
              <div class="rs-card-header d-flex justify-content-between align-items-center">
                <h5><i class="fa-solid fa-users me-2"></i>Resumen de invitados</h5>
                <span class="rs-badge"><?= (int) $this->totalPersonas ?> personas</span>
              </div>
              <div class="rs-card-body p-0">
                <div class="table-responsive">
                  <table class="rs-table">
                    <thead>
                      <tr>
                        <th width="30">No.</th>
                        <th>Nombre</th>
                        <th width="120">Tipo</th>
                        <th width="80" class="text-end">Precio</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $contador = 1; ?>
                      <?php foreach ($this->invitados as $invitado): ?>
                        <tr class="<?= $invitado->invitado_tipo == '1' ? 'rs-row-primary' : '' ?>">
                          <td>
                            <?php if ($invitado->es_socio_principal): ?>
                              <i class="fas fa-star rs-star" title="Socio principal"></i>
                            <?php else: ?>
                              <?= $contador ?>
                            <?php endif; ?>
                          </td>
                          <td>
                            <div>
                              <span class="rs-guest-name"><?= htmlspecialchars($invitado->invitadoReserva_nombre_invitado) ?> <?= htmlspecialchars($invitado->invitadoReserva_apellido_invitado) ?></span>
                              <?php if ($invitado->es_socio_principal): ?>
                                <small class="rs-small d-block">Socio actual</small>
                              <?php elseif (!empty($invitado->documento_invitado)): ?>
                                <small class="rs-small d-block">Doc: <?= htmlspecialchars($invitado->documento_invitado) ?></small>
                              <?php endif; ?>
                            </div>
                          </td>
                          <td>
                            <span class="rs-type-badge <?= $invitado->invitado_tipo == '1' ? 'rs-badge-success' : 'rs-badge-secondary' ?>">
                              <?= htmlspecialchars($invitado->tipo_participante) ?>
                            </span>
                          </td>
                          <td class="text-end rs-price">
                            $<?= number_format($invitado->precio_boleta, 0, ',', '.') ?>
                          </td>
                        </tr>
                        <?php $contador++; ?>
                      <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th colspan="3" class="text-end">Total pagado:</th>
                        <th class="text-end">
                          <span class="rs-total">$<?= number_format($this->totalGeneral, 0, ',', '.') ?></span>
                        </th>
                      </tr>
                      <?php if (($this->totalConDescuento) && ($this->totalConDescuento < $this->totalGeneral) && $this->descuento): ?>
                        <tr>
                          <th colspan="3" class="text-end">Total pagado con descuento (<?= $this->descuento ?>%):</th>
                          <th class="text-end">
                            <span class="rs-total rs-total-discount">$<?= number_format($this->totalConDescuento, 0, ',', '.') ?></span>
                          </th>
                        </tr>
                      <?php endif; ?>
                    </tfoot>
                  </table>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div><!-- /rs-main-card -->

      <!-- ── Acciones (solo pago aprobado) ── -->
      <?php if (in_array($this->reserva->reserva_estado, ['3'])): ?>
        <div class="av-note">
          <i class="fas fa-info-circle me-2"></i>
          <span>
            Le agradecemos diligenciar la información requerida para el registro de sus invitados, la factura electrónica y el menú gastronómico.
            En caso de no haber finalizado el proceso, podrá completarlo iniciando sesión nuevamente en la plataforma.
          </span>
        </div>

        <div class="av-actions">
          <a href="/page/login?id_res=<?= enc_id($this->id) ?>" class="av-btn-primary">
            <i class="fa-solid fa-clipboard-check"></i> Finalizar detalles de la reserva
          </a>
        </div>
      <?php endif; ?>

    </div>
  </div>

  <style>
    /* ============================================================
     RESPUESTA DE PAGO — Dark glass (misma cascada que reservar / resumen / aviso)
    ============================================================ */

    .contenedor-general {
      height: calc(100vh - 60px - 30px);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      padding-bottom: 0 !important;
    }

    .card-principal {
      background-color: rgba(21, 25, 29, 1);
      padding: 1.35rem 1.5rem;
      border-radius: 6px;
      flex: 1;
      min-height: 0;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      margin-bottom: 0 !important;
    }

    .rs-page {
      max-width: 100%;
      flex: 1;
      min-height: 0;
      display: flex;
      flex-direction: column;
      overflow: hidden;
      padding-top: 0.5rem;
      padding-bottom: 0;
    }

    /* ── Topbar: volver + paso a paso ── */
    .rv-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 0.75rem;
      flex-wrap: wrap;
      flex-shrink: 0;
    }

    .sb-back-btn {
      display: inline-flex;
      align-items: center;
      box-sizing: border-box;
      height: 34px;
      gap: 6px;
      font-size: 0.78rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      padding: 0 16px 0 12px;
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 20px;
      color: rgba(255, 255, 255, 0.8);
      text-decoration: none;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      transition: background 0.18s ease, color 0.18s ease;
    }

    .sb-back-btn:hover {
      background: rgba(255, 255, 255, 0.13);
      color: #ffffff;
    }

    .rv-topbar .step-badge {
      display: inline-flex;
      align-items: center;
      box-sizing: border-box;
      height: 34px;
      background: rgba(255, 255, 255, 0.07);
      color: #ffffff;
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 20px;
      padding: 0 16px 0 12px;
      font-size: 0.82rem;
      font-weight: 600;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
    }

    .rv-topbar .step-badge i { color: #ffc107; font-size: 0.78rem; }
    .rv-topbar .step-text { font-weight: 700; letter-spacing: 0.3px; }
    .rv-topbar .step-divider { opacity: 0.3; margin: 0 6px; font-size: 0.9rem; }
    .rv-topbar .step-name { color: white; font-weight: 500; font-size: 0.9rem; }

    /* ── Timer pill (igual que rv-timer en el resto del flujo) ── */
    .rv-timer {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      background: rgba(255, 255, 255, 0.07);
      border: 1px solid rgba(255, 255, 255, 0.12);
      border-radius: 20px;
      padding: 6px 16px 6px 12px;
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      transition: border-color 0.4s ease;
    }

    .rv-timer-icon { font-size: 0.78rem; color: #ffc107; flex-shrink: 0; }

    .rv-timer-display {
      font-size: 1.05rem;
      font-weight: 700;
      color: #ffffff;
      letter-spacing: 2px;
      line-height: 1;
    }

    .rv-timer-colon { animation: rv-blink 1s step-end infinite; opacity: 0.7; }

    .rv-timer-label {
      font-size: 0.9rem;
      text-transform: uppercase;
      letter-spacing: 1.8px;
      color: white;
      font-weight: 600;
    }

    .rv-timer.urgent {
      border-color: rgba(220, 53, 69, 0.55);
      background: rgba(220, 53, 69, 0.1);
    }

    .rv-timer.urgent .rv-timer-icon,
    .rv-timer.urgent .rv-timer-display { color: #ef9a9a; }

    @keyframes rv-blink { 50% { opacity: 0; } }

    /* ── Encabezado de estado ── */
    .av-success-header {
      margin-bottom: 0.85rem;
      flex-shrink: 0;
    }

    .av-success-icon {
      width: 60px;
      height: 60px;
      margin: 0 auto 0.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background: rgba(255, 193, 7, 0.12);
      border: 1px solid rgba(255, 193, 7, 0.35);
      color: #ffc107;
      font-size: 1.7rem;
      box-shadow: 0 0 0 6px rgba(255, 193, 7, 0.05);
    }

    .av-status-success .av-success-icon {
      background: rgba(40, 167, 69, 0.12);
      border-color: rgba(40, 167, 69, 0.35);
      color: #6fcf97;
      box-shadow: 0 0 0 6px rgba(40, 167, 69, 0.05);
    }

    .av-status-info .av-success-icon {
      background: rgba(74, 144, 226, 0.12);
      border-color: rgba(74, 144, 226, 0.35);
      color: #6fa8e2;
      box-shadow: 0 0 0 6px rgba(74, 144, 226, 0.05);
    }

    .av-status-danger .av-success-icon {
      background: rgba(220, 53, 69, 0.12);
      border-color: rgba(220, 53, 69, 0.35);
      color: #ef9a9a;
      box-shadow: 0 0 0 6px rgba(220, 53, 69, 0.05);
    }

    .av-status-secondary .av-success-icon {
      background: rgba(255, 255, 255, 0.07);
      border-color: rgba(255, 255, 255, 0.2);
      color: rgba(255, 255, 255, 0.7);
      box-shadow: 0 0 0 6px rgba(255, 255, 255, 0.03);
    }

    .av-title {
      font-size: 1.3rem;
      font-weight: 500;
      letter-spacing: 1px;
      color: #ffffff;
      margin-bottom: 0.2rem;
    }

    .av-subtitle {
      font-size: 0.68rem;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.5);
      margin: 0;
    }

    .av-desc {
      font-size: 0.8rem;
      color: rgba(255, 255, 255, 0.55);
      margin: 0.4rem 0 0;
    }

    /* ── Cuerpo scrollable ── */
    .rs-main-card {
      flex: 1;
      min-height: 0;
      overflow-y: auto;
      overflow-x: hidden;
      padding: 4px 2px 0;
    }

    .rs-main-card::-webkit-scrollbar { width: 5px; }
    .rs-main-card::-webkit-scrollbar-track { background: transparent; }
    .rs-main-card::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.12); border-radius: 4px; }

    .av-summary-card {
      flex-shrink: 1;
      min-height: 0;
      overflow-y: auto;
    }

    /* ── Cards ── */
    .rs-card {
      background: rgba(6, 6, 6, 0.55);
      backdrop-filter: blur(22px);
      -webkit-backdrop-filter: blur(22px);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 6px;
      overflow: hidden;
    }

    .rs-card-header {
      background: rgba(255, 255, 255, 0.04);
      border-bottom: 1px solid rgba(255, 255, 255, 0.07);
      padding: 8px 14px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .rs-card-header i { color: #ffc107; font-size: 0.9rem; }

    .rs-card-header h5 {
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: white;
      font-weight: 700;
      margin: 0;
    }

    .rs-card-body { padding: 10px 16px; }
    .rs-card-body.p-0 { padding: 0 !important; }

    .rs-badge {
      display: inline-block;
      padding: 3px 12px;
      border-radius: 20px;
      background: #ffc107;
      font-size: 0.68rem;
      font-weight: 700;
      letter-spacing: 0.5px;
      color: #111;
    }

    /* ── Data rows ── */
    .rs-data-row {
      display: flex;
      justify-content: space-between;
      align-items: baseline;
      gap: 8px;
      font-size: 0.8rem;
      padding: 6px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      margin-bottom: 0 !important;
    }

    .rs-data-row-last { border-bottom: none !important; }

    .rs-data-key {
      color: rgba(255, 255, 255, 0.55);
      font-weight: 600;
      white-space: nowrap;
    }

    .rs-data-val {
      color: #fff;
      font-weight: 600;
      text-align: right;
    }

    /* ── Mesas ── */
    .rs-mesa-bloque {
      display: grid;
      grid-template-columns: 1fr 1fr;
    }

    .rs-mesa-dato {
      padding: 6px 16px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .rs-mesa-dato:nth-child(odd) {
      border-right: 1px solid rgba(255, 255, 255, 0.06);
    }

    .rs-mesa-dato-last { border-bottom: none !important; }

    .rs-mesa-key {
      display: block;
      font-size: 0.66rem;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: rgba(255, 255, 255, 0.4);
      font-weight: 700;
      margin-bottom: 2px;
    }

    .rs-mesa-val {
      font-size: 0.85rem;
      color: rgba(255, 255, 255, 0.9);
      font-weight: 600;
    }

    .rs-mesa-sep {
      height: 1px;
      background: rgba(255, 193, 7, 0.15);
      margin: 0;
    }

    /* ── Tabla invitados ── */
    .rs-table { width: 100%; font-size: 0.83rem; border-collapse: collapse; }

    .rs-table thead th {
      font-size: 0.72rem;
      letter-spacing: 1.5px;
      text-transform: uppercase;
      color: rgba(255, 255, 255, 0.5);
      font-weight: 700;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      background: transparent;
      padding: 10px 12px;
    }

    .rs-table tbody tr td {
      padding: 9px 12px;
      vertical-align: middle;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      color: rgba(255, 255, 255, 0.82);
    }

    .rs-table tbody tr:last-child td { border-bottom: none; }
    .rs-table tbody tr.rs-row-primary td { background: rgba(255, 193, 7, 0.05); }

    .rs-table tfoot th {
      padding: 11px 12px;
      background: transparent;
      border-top: 1px solid rgba(255, 255, 255, 0.12);
      color: rgba(255, 255, 255, 0.4);
      font-size: 0.78rem;
    }

    .rs-guest-name { font-weight: 600; color: #fff; }
    .rs-small { color: rgba(255, 255, 255, 0.35); font-size: 0.72rem; }
    .rs-star { color: #ffc107; font-size: 0.8rem; }
    .rs-price { font-weight: 700; color: rgba(255, 255, 255, 0.9); }
    .rs-total { font-size: 1.1rem; font-weight: 800; color: #fff; }
    .rs-total-discount { color: #ffc107; }

    .rs-type-badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 4px;
      font-size: 0.67rem;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .rs-badge-success { background: rgba(40, 167, 69, 0.2); color: #6fcf97; border: 1px solid rgba(40, 167, 69, 0.3); }
    .rs-badge-secondary { background: rgba(255, 255, 255, 0.07); color: rgba(255, 255, 255, 0.5); border: 1px solid rgba(255, 255, 255, 0.1); }

    /* ── Nota ── */
    .av-note {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 0.85rem;
      margin: 0.75rem 0 0;
      font-size: 0.82rem;
      line-height: 1.3;
      color: rgba(255, 255, 255, 0.7);
      background: rgba(74, 144, 226, 0.08);
      border-left: 3px solid #4a90e2;
      border-radius: 4px;
      flex-shrink: 0;
    }

    .av-note i { flex-shrink: 0; }

    /* ── Acciones ── */
    .av-actions {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      align-items: center;
      gap: 0.65rem;
      padding-top: 0.75rem;
      flex-shrink: 0;
    }

    .av-btn-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      font-size: 0.82rem;
      font-weight: 700;
      letter-spacing: 0.4px;
      text-transform: uppercase;
      padding: 9px 26px;
      min-width: 200px;
      border-radius: 20px;
      text-decoration: none;
      background: #ffc107;
      border: 1px solid rgba(255, 255, 255, 0.15);
      color: #111 !important;
      transition: background 0.18s ease, transform 0.15s ease;
    }

    .av-btn-primary:hover {
      background: #ffd54f;
      transform: translateY(-1px);
      color: #111 !important;
    }

    /* ── Print ── */
    @media print {
      .av-actions, .av-note, .rv-topbar, nav, header, footer { display: none !important; }
      .rs-page, .card-principal, .rs-main-card { overflow: visible !important; height: auto !important; }
      .contenedor-general { height: auto !important; overflow: visible !important; }
    }

    /* ── Responsive ── */
    @media (max-width: 767px) {
      .contenedor-general { height: auto !important; min-height: calc(100vh - 60px - 30px); overflow: visible; }
      .card-principal { overflow: visible !important; flex: none !important; padding: 1.1rem 1rem !important; margin-bottom: 30px !important; }
      .rs-page { overflow: visible !important; flex: none !important; }
      .rs-main-card { overflow: visible !important; flex: none !important; }
      .rv-topbar > div:first-child { flex-wrap: nowrap !important; }
      .rv-topbar .step-badge { font-size: 0.72rem; padding: 5px 10px; white-space: nowrap; }
      .rv-topbar .step-name { display: none; }
      .rv-topbar .step-divider { display: none; }
      .sb-back-btn { font-size: 0.7rem; padding: 5px 10px 5px 8px; }
      .rv-timer { padding: 5px 10px; gap: 0.3rem; }
      .rv-timer-label { display: none; }
      .rv-timer-display { font-size: 0.88rem; letter-spacing: 1px; }
      .av-title { font-size: 1.35rem; }
      .rs-mesa-bloque { grid-template-columns: 1fr; }
      .rs-mesa-dato:nth-child(odd) { border-right: none; }
      .av-actions { flex-direction: column; align-items: stretch; }
      .av-btn-primary { width: 100%; }
      .rs-table thead th { font-size: 0.68rem; padding: 8px 8px; letter-spacing: 1px; }
      .rs-table tbody tr td { padding: 7px 8px; font-size: 0.78rem; }
      .rs-table tfoot th { padding: 8px 8px; font-size: 0.72rem; }
      .rs-guest-name { font-size: 0.78rem; }
      .rs-type-badge { font-size: 0.6rem; }
    }
  </style>

<?php endif; ?>
