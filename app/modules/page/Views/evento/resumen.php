<?php
$cuposId = $this->cuposId;
$esCuposMode = ($cuposId !== null);
$total_personas = count($this->invitados);
$cuotas = $this->evento->evento_max_cuotas;
$ambienteNombre = '';
if ($this->mesaInfo && $this->categoria) {
  foreach ($this->mesaInfo as $_m) {
    $ambienteNombre = htmlspecialchars($_m->ambiente_nombre);
  }
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

<!-- Formulario oculto para proceder al pago -->
<form id="formPago" action="/page/evento/pagar" method="post" style="display: none;">
  <input type="hidden" name="reserva_id" value="<?php echo enc_id($this->reserva->id) ?>">
  <input type="hidden" name="total_pagar" value="<?php echo $this->totalPagar ?>">
  <input type="hidden" name="metodo_pago" id="metodo_pago_hidden">
  <input type="hidden" name="numero_cuotas" id="numero_cuotas_hidden">
  <input type="hidden" name="total_personas" value="<?php echo $total_personas ?>">
  <input type="hidden" name="evento_id" value="<?php echo $this->evento->evento_id ?>">
  <?php if ($esCuposMode): ?>
    <input type="hidden" name="cupos_id" value="<?= enc_id($cuposId) ?>">
  <?php endif; ?>
</form>

<div class="rs-page container py-2 py-lg-3 mb-3 mb-lg-5">
  <div class="card-principal">

    <!-- ── Encabezado: regresar · step | timer ── -->
    <div class="rv-topbar mb-3">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="/page/evento/reservar?boking=<?php echo enc_id($this->reserva->id); ?>" class="sb-back-btn">
          <i class="fa-solid fa-angle-left"></i>&nbsp; Regresar
        </a>
        <div class="step-indicator">
          <div class="step-badge">
            <i class="fa-solid fa-credit-card me-2"></i>
            <span class="step-text">Paso 4 de 5</span>
            <span class="step-divider">•</span>
            <span class="step-name">Resumen de pedido</span>
          </div>
        </div>
        <?php if ($esCuposMode): ?>
          <div class="step-badge cupos-badge">
            <i class="fas fa-user-plus"></i>
            <span class="step-text">+<?= $total_personas ?> cupo<?= $total_personas != 1 ? 's' : '' ?> adicional<?= $total_personas != 1 ? 'es' : '' ?></span>
            <span class="step-divider">•</span>
            <span class="step-name">Reserva #<?= $this->reserva->id ?></span>
          </div>
        <?php endif; ?>
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

    <!-- ── Cuerpo scrollable ── -->
    <div class="rs-main-card">
      <div class="row g-3">

        <!-- LEFT: info evento + mesas -->
        <div class="col-lg-4">

          <!-- Información del Evento -->
          <div class="rs-card mb-3">
            <div class="rs-card-header">
              <i class="fa-solid fa-calendar-days me-2"></i>
              <h5>Información del evento</h5>
            </div>
            <div class="rs-card-body">
              <p class="rs-data-row">
                <span class="rs-data-key">Nombre del evento</span>
                <span class="rs-data-val"><?php echo htmlspecialchars($this->evento->evento_titulo) ?></span>
              </p>
              <p class="rs-data-row d-none">
                <span class="rs-data-key">Evento</span>
                <span class="rs-data-val"><?= $ambienteNombre ?></span>
              </p>
              <?php if ($this->evento->evento_fecha != $this->evento->evento_fecha_fin): ?>
                <p class="rs-data-row d-none">
                  <span class="rs-data-key">Fecha fin</span>
                  <span class="rs-data-val"><?php echo date('d/m/Y', strtotime($this->evento->evento_fecha_fin)) ?></span>
                </p>
              <?php endif; ?>
              <p class="rs-data-row">
                <span class="rs-data-key">Fecha de reserva</span>
                <span class="rs-data-val"><?php echo date('d/m/Y', strtotime($this->reserva->reserva_fecha)) ?></span>
              </p>
              <p class="rs-data-row rs-data-row-last">
                <span class="rs-data-key">Número de personas</span>
                <span class="rs-data-val"><?php echo $total_personas ?></span>
              </p>
            </div>
          </div>

          <!-- Información de Mesa/Silla y Categoría -->
          <?php $esSillaResumen = ($this->esModoSilla); ?>
          <?php if ($this->mesaInfo && ($this->categoria || $esSillaResumen)): ?>
            <div class="rs-card mb-3">
              <div class="rs-card-header">
                <i class="fa-solid <?= $esSillaResumen ? 'fa-chair' : 'fa-table' ?> me-2"></i>
                <h5><?= $esSillaResumen ? 'Información de las sillas' : 'Información de las mesas' ?></h5>
              </div>
              <div class="rs-card-body p-0">
                <?php foreach ($this->mesaInfo as $index => $mesa): ?>
                  <div class="rs-mesa-bloque">
                    <div class="rs-mesa-dato">
                      <span class="rs-mesa-key">Piso</span>
                      <span class="rs-mesa-val"><?= htmlspecialchars($mesa->piso_nombre) ?></span>
                    </div>
                    <div class="rs-mesa-dato rs-mesa-dato-last">
                      <span class="rs-mesa-key">Ambiente</span>
                      <span class="rs-mesa-val"><?= htmlspecialchars($mesa->ambiente_nombre) ?></span>
                    </div>
                    <div class="rs-mesa-dato">
                      <span class="rs-mesa-key"><?= $esSillaResumen ? 'Silla' : 'Mesa' ?></span>
                      <span class="rs-mesa-val"><?= htmlspecialchars($mesa->mesa_nombre) ?></span>
                    </div>
                    <div class="rs-mesa-dato">
                      <span class="rs-mesa-key">Código</span>
                      <span class="rs-mesa-val"><?= htmlspecialchars($mesa->mesa_codigo) ?></span>
                    </div>
                    <?php if (!$esSillaResumen): ?>
                    <div class="rs-mesa-dato">
                      <span class="rs-mesa-key">Capacidad</span>
                      <span class="rs-mesa-val"><?= $mesa->mesa_capacidad ?> personas</span>
                    </div>
                    <?php endif; ?>
                    <div class="rs-mesa-dato d-none">
                      <span class="rs-mesa-key">Categoría</span>
                      <span class="rs-mesa-val"><?= htmlspecialchars($this->categoria->categoria_nombre ?? '') ?></span>
                    </div>
                  </div>
                  <?php if ($index < count($this->mesaInfo) - 1): ?>
                    <div class="rs-mesa-sep"></div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- RIGHT: lista invitados + términos -->
        <div class="col-lg-8">

          <!-- Lista de Invitados -->
          <div class="rs-card <?= (is_countable($this->terminos) && count($this->terminos) > 0) ? 'mb-3' : '' ?>">
            <div class="rs-card-header d-flex justify-content-between align-items-center">
              <h5><i class="fa-solid fa-users me-2"></i>Lista de invitados</h5>
              <span class="rs-badge"><?php echo $total_personas ?> personas</span>
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
                      <tr class="<?php echo $invitado->invitado_tipo == '1' ? 'rs-row-primary' : '' ?>">
                        <td>
                          <?php if ($invitado->es_socio_principal): ?>
                            <i class="fas fa-star rs-star"></i>
                          <?php else: ?>
                            <?php echo $contador; ?>
                          <?php endif; ?>
                        </td>
                        <td>
                          <div>
                            <span class="rs-guest-name"><?php echo htmlspecialchars($invitado->invitadoReserva_nombre_invitado) ?> <?php echo htmlspecialchars($invitado->invitadoReserva_apellido_invitado) ?></span>
                            <?php if ($invitado->es_socio_principal): ?>
                              <small class="rs-small d-block">Socio actual</small>
                            <?php elseif (($invitado->documento_invitado)): ?>
                              <small class="rs-small d-block">Doc: <?php echo $invitado->documento_invitado ?></small>
                            <?php endif; ?>
                          </div>
                        </td>
                        <td>
                          <span class="rs-type-badge <?php echo $invitado->invitado_tipo == '1' ? 'rs-badge-success' : 'rs-badge-secondary' ?>">
                            <?php echo $invitado->tipo_participante ?>
                          </span>
                        </td>
                        <td class="text-end rs-price">
                          $<?php echo number_format($invitado->precio_boleta, 0, ',', '.') ?>
                        </td>
                      </tr>
                      <?php $contador++; ?>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr>
                      <th colspan="3" class="text-end">Total a pagar:</th>
                      <th class="text-end">
                        <span class="rs-total">$<?php echo number_format($this->totalGeneral, 0, ',', '.') ?></span>
                      </th>
                    </tr>
                    <?php if (($this->totalConDescuento) && ($this->totalConDescuento < $this->totalGeneral) && $this->descuento): ?>
                      <tr>
                        <th colspan="3" class="text-end">Total con descuento (<?php echo $this->descuento ?>%):</th>
                        <th class="text-end">
                          <span class="rs-total">$<?php echo number_format($this->totalConDescuento, 0, ',', '.') ?></span>
                        </th>
                      </tr>
                    <?php endif; ?>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

          <!-- Términos y condiciones -->
          <?php if (is_countable($this->terminos) && count($this->terminos) > 0): ?>
            <div class="rs-card mb-3">
              <div class="rs-card-header">
                <i class="fa-solid fa-file-contract me-2"></i>
                <h5>Términos y condiciones</h5>
              </div>
              <div class="rs-card-body">
                <?php foreach ($this->terminos as $index => $termino): ?>
                  <div class="rs-form-check mb-2">
                    <input class="form-check-input termino-checkbox" type="checkbox"
                      id="termino_<?php echo $termino->termino_id; ?>" required>
                    <label class="form-check-label" for="termino_<?php echo $termino->termino_id; ?>">
                      Acepto
                      <?php if (($termino->termino_enlace)): ?>
                        <a href="<?php echo htmlspecialchars($termino->termino_enlace); ?>" target="_blank" class="rs-link">
                          <?php echo htmlspecialchars($termino->termino_titulo); ?>
                        </a>
                      <?php elseif (($termino->termino_texto)): ?>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalTermino_<?php echo $termino->termino_id; ?>" class="rs-link">
                          <?php echo htmlspecialchars($termino->termino_titulo); ?>
                        </a>
                      <?php else: ?>
                        <?php echo htmlspecialchars($termino->termino_titulo); ?>
                      <?php endif; ?>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- realizar pago -->
          <div class="rs-card mb-3">
            <div class="rs-card-header d-flex align-items-center gap-2">
              <i class="fa-solid fa-credit-card"></i>
              <h5>Realizar pago</h5>
            </div>
            <div class="rs-card-body">

              <!-- Totales -->
              <div class="rs-pay-totals mb-3">
                <div class="rs-pay-total-item">
                  <span class="rs-pay-total-label">Total a pagar</span>
                  <span class="rs-pay-total-val">$<?= number_format($this->totalGeneral, 0, ',', '.') ?></span>
                </div>
                <?php if (($this->totalConDescuento) && ($this->totalConDescuento < $this->totalGeneral) && $this->descuento): ?>
                  <div class="rs-pay-sep"></div>
                  <div class="rs-pay-total-item">
                    <span class="rs-pay-total-label">Con descuento (<?= $this->descuento ?>%)</span>
                    <span class="rs-pay-total-val rs-pay-total-discount">$<?= number_format($this->totalConDescuento, 0, ',', '.') ?></span>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Selectores -->
              <div class="rs-pay-fields">
                <div class="rs-pay-field">
                  <label class="rs-field-label" for="metodo">Método de pago</label>
                  <select name="metodo" id="metodo" class="rs-select w-100">
                    <option value="" selected disabled>Seleccione...</option>
                    <?php if (!$this->gestor && !$esCuposMode): ?>
                      <option value="linea">Pago en línea</option>
                    <?php endif; ?>
                    <?php if ($this->hayCupo): ?>
                      <option value="cargo">Cargo a la acción</option>
                    <?php endif; ?>
                    <?php if ($this->gestor && $this->evento->evento_datafono): ?>
                      <option value="datafono">Pago con Datafono</option>
                    <?php endif; ?>
                  </select>
                </div>

                <?php if ($this->evento->evento_cuotas == 1): ?>
                  <div class="rs-pay-field" id="cuotasFieldWrapper" style="display: none;">
                    <label class="rs-field-label" for="cuotas">Número de cuotas</label>
                    <select name="cuotas" id="cuotas" class="rs-select w-100">
                      <option value="" selected disabled>Seleccione...</option>
                      <?php foreach (range(1, $cuotas) as $cuota): ?>
                        <option value="<?php echo $cuota ?>"><?php echo $cuota ?> cuota<?= $cuota > 1 ? 's' : '' ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                <?php else: ?>
                  <input type="hidden" name="cuotas" id="cuotas" value="1">
                <?php endif; ?>
              </div>

            </div>
          </div>

        </div>
      </div>
    </div><!-- /rs-main-card -->

    <!-- ── Footer fijo: total + botón ── -->
    <div class="rs-footer">
      <div class="rs-footer-summary">
        <span class="rs-footer-summary-label">Total</span>
        <span class="rs-footer-summary-val">$<?= number_format($this->totalGeneral, 0, ',', '.') ?></span>
        <?php if (($this->totalConDescuento) && ($this->totalConDescuento < $this->totalGeneral) && $this->descuento): ?>
          <span class="rs-footer-summary-discount">con <?= $this->descuento ?>% desc. $<?= number_format($this->totalConDescuento, 0, ',', '.') ?></span>
        <?php endif; ?>
      </div>
      <div class="rs-footer-actions">
        <a href="/page/evento/reservar?boking=<?php echo enc_id($this->reserva->id); ?>" class="sb-back-btn">
          <i class="fa-solid fa-angle-left"></i>&nbsp; Regresar
        </a>
        <button type="button" id="btnProcederPago" class="rs-pay-btn">
          <span id="btnTexto">Proceder al Pago &nbsp;<i class="fa-solid fa-angle-right"></i></span>
          <span id="btnSpinner" style="display: none;">
            <i class="fas fa-spinner fa-spin"></i> Procesando...
          </span>
        </button>
      </div>
    </div><!-- /rs-footer -->

  </div>
</div>

<!-- ── Modales de términos con texto ── -->
<?php if (($this->terminos)): ?>
  <?php foreach ($this->terminos as $termino): ?>
    <?php if (($termino->termino_texto)): ?>
      <div class="modal fade" id="modalTermino_<?php echo $termino->termino_id; ?>" tabindex="-1"
        aria-labelledby="modalTermino_<?php echo $termino->termino_id; ?>Label" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalTermino_<?php echo $termino->termino_id; ?>Label">
                <?php echo htmlspecialchars($termino->termino_titulo); ?>
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <?php echo $termino->termino_texto; ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
              <button type="button" class="btn btn-primary" data-bs-dismiss="modal"
                onclick="aceptarTermino(<?php echo $termino->termino_id; ?>)">
                Acepto los Términos
              </button>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>

<!-- Modal de Términos y Condiciones del Evento -->
<div class="modal fade" id="modalTerminosEvento" tabindex="-1" aria-labelledby="modalTerminosEventoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTerminosEventoLabel">Términos y Condiciones del Evento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-12">
            <h6>1. Condiciones de la Reserva</h6>
            <ul>
              <li>La reserva es intransferible y debe ser confirmada por el socio titular</li>
              <li>El pago debe realizarse dentro del tiempo establecido para mantener la reserva</li>
              <li><?= $esSillaResumen ? 'Las sillas asignadas se mantendrán durante todo el evento' : 'La mesa asignada se mantendrá durante todo el evento' ?></li>
              <?php if (!$esSillaResumen): ?>
              <li>El número de invitados no puede exceder la capacidad de la mesa reservada</li>
              <?php endif; ?>
            </ul>
            <h6>2. Políticas del Evento</h6>
            <ul>
              <li>Se requiere vestimenta apropiada según el código de vestimenta del club</li>
              <li>No se permite el ingreso de menores de edad sin autorización previa</li>
              <li>Los alimentos y bebidas son exclusivamente los ofrecidos por el club</li>
              <li>El horario del evento debe respetarse estrictamente</li>
            </ul>
            <h6>3. Cancelaciones y Reembolsos</h6>
            <ul>
              <li>Las cancelaciones deben realizarse con al menos 48 horas de anticipación</li>
              <li>Los reembolsos están sujetos a las políticas del club</li>
              <li>En caso de cancelación del evento por fuerza mayor, se reembolsará el 100% del pago</li>
            </ul>
            <h6>4. Responsabilidades</h6>
            <ul>
              <li>El socio es responsable del comportamiento de todos sus invitados</li>
              <li>Cualquier daño causado por el grupo será facturado al socio titular</li>
              <li>El incumplimiento de las normas puede resultar en la suspensión del evento</li>
            </ul>
            <h6>5. Aceptación</h6>
            <p>Al marcar esta casilla, confirmo que he leído, entendido y acepto todos los términos y condiciones mencionados anteriormente.</p>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="aceptarTerminosEvento()">Acepto los Términos</button>
      </div>
    </div>
  </div>
</div>

<style>
  

  .contenedor-general {
    height: calc(100vh - 60px - 30px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding-bottom: 0 !important;
  }

  .card-principal {
    background-color: rgba(21, 25, 29, 1);
    padding: 15px 15px;
    border-radius: 6px;
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    margin-bottom: 0 !important;
  }

  /* ── VIEWPORT LAYOUT ── */
  .rs-page {
    max-width: 100%;
    flex: 1; min-height: 0;
    display: flex; flex-direction: column;
    overflow: hidden;
    padding-top: 0.5rem; padding-bottom: 0;
  }

  /* ── TOPBAR (shared classes) ── */
  .rv-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
  }

  /* ── Back button ── */
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

  /* ── Step badge (scoped a rv-topbar) ── */
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
    transition: border-color 0.3s ease;
  }
  .rv-topbar .step-badge i { color: #ffc107; font-size: 0.78rem; }
  .rv-topbar .step-text { font-weight: 700; letter-spacing: 0.3px; }
  .rv-topbar .step-divider { opacity: 0.3; margin: 0 6px; font-size: 0.9rem; }
  .rv-topbar .step-name { color: white; font-weight: 500; font-size: 0.9rem; }
  .rv-topbar .cupos-badge {
    background: rgba(139, 92, 246, 0.15);
    border-color: rgba(139, 92, 246, 0.4);
  }
  .rv-topbar .cupos-badge i { color: #a78bfa; }
  .rv-topbar .cupos-badge .step-text { color: #c4b5fd; }

  /* ── Timer pill (igual que rv-timer en seleccionarbeneficiarios) ── */
  .rv-timer {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    background: #ffc107;
    border: 1px solid #e6ac00;
    border-radius: 20px;
    padding: 6px 16px 6px 12px;
    box-shadow: 0 2px 10px rgba(255, 193, 7, 0.25);
    transition: border-color 0.4s ease, background 0.4s ease;
  }
  .rv-timer-icon { font-size: 0.78rem; color: #111; flex-shrink: 0; }
  .rv-timer-display {
    font-size: 1.05rem;
    font-weight: 700;
    color: #111;
    letter-spacing: 2px;
    line-height: 1;
  }
  .rv-timer-colon { animation: rv-blink 1s step-end infinite; opacity: 0.7; }
  .rv-timer-label {
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1.8px;
    color: #111;
    font-weight: 600;
  }
  /* Urgency state (applied via JS): pasa de amarillo a rojo cuando quedan < 3 min */
  .rv-timer.urgent {
    background: #dc3545;
    border-color: #b02a37;
  }
  .rv-timer.urgent .rv-timer-icon,
  .rv-timer.urgent .rv-timer-display,
  .rv-timer.urgent .rv-timer-label { color: #ffffff; }
  @keyframes rv-blink { 50% { opacity: 0; } }

  /* ── SCROLLABLE BODY ── */
  .rs-main-card {
    flex: 1; min-height: 0;
    overflow-y: auto; overflow-x: hidden;
    padding: 4px 2px 0px;
  }
  .rs-main-card::-webkit-scrollbar { width: 5px; }
  .rs-main-card::-webkit-scrollbar-track { background: transparent; }
  .rs-main-card::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.12); border-radius: 4px; }

  /* ── CARDS ── */
  .rs-card {
    background: rgba(6, 6, 6, 0.55);
    backdrop-filter: blur(22px); -webkit-backdrop-filter: blur(22px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 6px; overflow: hidden;
  }
  .rs-card-header {
    background: rgba(255, 255, 255, 0.04);
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    padding: 10px 14px;
    display: flex; align-items: center; gap: 8px;
  }
  .rs-card-header i { color: #fff; font-size: 0.9rem; }
  .rs-card-header h5 {
    font-size: 1rem; text-transform: uppercase; letter-spacing: 2.5px;
    color: white; font-weight: 700; margin: 0;
  }
  .rs-card-body { padding: 14px; }
  .rs-card-body.p-0 { padding: 0 !important; }

  /* ── DATA ROWS ── */
  .rs-data-row {
    display: flex; justify-content: space-between; align-items: baseline;
    gap: 8px; font-size: 0.83rem;
    padding: 7px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    margin-bottom: 0 !important;
  }
  .rs-data-row-last { border-bottom: none !important; }
  .rs-data-key { color: white; font-size: 0.9rem; font-weight: 600; white-space: nowrap; }
  .rs-data-val { color: #fff; font-weight: 500; text-align: right; }

  /* ── MESA INFO ── */
  .rs-mesa-bloque { display: grid; grid-template-columns: 1fr 1fr; }
  .rs-mesa-dato {
    padding: 9px 14px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  }
  .rs-mesa-dato:nth-child(odd) { border-right: 1px solid rgba(255, 255, 255, 0.06); }
  .rs-mesa-dato-last { border-bottom: none !important; }
  .rs-mesa-key {
    display: block; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;
    color: white; font-weight: 700; margin-bottom: 2px;
  }
  .rs-mesa-val { font-size: 0.82rem; color: rgba(255, 255, 255, 0.85); font-weight: 500; }
  .rs-mesa-sep { height: 1px; background: rgba(255, 193, 7, 0.15); margin: 0; }

  /* ── TABLE ── */
  .rs-table { width: 100%; font-size: 0.83rem; border-collapse: collapse; }
  .rs-table thead th {
    font-size: 0.8rem; letter-spacing: 2px; text-transform: uppercase;
    color: white; font-weight: 700;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    background: transparent; padding: 10px 12px;
  }
  .rs-table tbody tr td {
    padding: 9px 12px; vertical-align: middle;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.82);
  }
  .rs-table tbody tr:last-child td { border-bottom: none; }
  .rs-table tbody tr.rs-row-primary td { background: rgba(255, 193, 7, 0.05); }
  .rs-table tfoot th {
    padding: 11px 12px; background: transparent;
    border-top: 1px solid rgba(255, 255, 255, 0.12);
    color: rgba(255, 255, 255, 0.4); font-size: 0.78rem;
  }

  .rs-guest-name { font-weight: 600; color: #fff; }
  .rs-small { color: rgba(255, 255, 255, 0.35); font-size: 0.72rem; }
  .rs-star { color: #ffc107; font-size: 0.8rem; }
  .rs-price { font-weight: 700; color: rgba(255, 255, 255, 0.9); }
  .rs-total { font-size: 1.1rem; font-weight: 800; color: #fff; }

  .rs-badge {
    display: inline-block; padding: 3px 10px; border-radius: 20px;
    background: #ffc107; border: 1px solid rgba(255, 255, 255, 0.15);
    font-size: 0.7rem; font-weight: 600; letter-spacing: 1px; color: black;
  }
  .rs-type-badge {
    display: inline-block; padding: 2px 8px; border-radius: 4px;
    font-size: 0.67rem; font-weight: 600; letter-spacing: 0.5px;
  }
  .rs-badge-success { background: rgba(40, 167, 69, 0.2); color: #6fcf97; border: 1px solid rgba(40,167,69,0.3); }
  .rs-badge-secondary { background: rgba(255,255,255,0.07); color: rgba(255,255,255,0.5); border: 1px solid rgba(255,255,255,0.1); }

  /* ── TÉRMINOS ── */
  .rs-section-title {
    font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px;
    color: rgba(255,255,255,0.4); font-weight: 700; margin-bottom: 12px;
  }
  .rs-form-check .form-check-label { font-size: 0.82rem; color: rgba(255,255,255,0.7); }
  .rs-form-check .form-check-input {
    border: 1.5px solid rgba(255,255,255,0.25) !important;
    border-radius: 3px !important;
    background: transparent !important;
  }
  .rs-form-check .form-check-input:checked {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
  }
  .rs-link { color: #ffc107; text-decoration: none; }
  .rs-link:hover { text-decoration: underline; color: #ffd454; }

  /* ── CARD PAGO ── */
  .rs-pay-totals {
    display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
    padding: 10px 14px;
    background: rgba(255, 193, 7, 0.06);
    border: 1px solid rgba(255, 193, 7, 0.15);
    border-radius: 6px;
  }
  .rs-pay-sep { width: 1px; height: 28px; background: rgba(255,255,255,0.1); }
  .rs-pay-total-item { display: flex; flex-direction: column; gap: 2px; }
  .rs-pay-total-label { font-size: 0.67rem; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.35); font-weight: 600; }
  .rs-pay-total-val { font-size: 1.1rem; font-weight: 800; color: #fff; letter-spacing: 0.3px; }
  .rs-pay-total-discount { color: #ffc107; }

  .rs-pay-fields { display: flex; gap: 0.75rem; flex-wrap: wrap; }
  .rs-pay-field { display: flex; flex-direction: column; gap: 5px; flex: 1; min-width: 160px; }
  .rs-field-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4); font-weight: 600; }
  .rs-select.w-100 { width: 100%; min-width: unset; }

  /* ── FOOTER FIJO ── */
  .rs-footer {
    display: flex; align-items: center;
    justify-content: space-between; gap: 1rem;
    padding: 0.65rem 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(6, 6, 6, 0.75);
    backdrop-filter: blur(22px); -webkit-backdrop-filter: blur(22px);
    flex-shrink: 0; flex-wrap: wrap;
  }
  .rs-footer-summary {
    display: flex; align-items: baseline; gap: 0.5rem; flex-wrap: wrap;
  }
  .rs-footer-summary-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.35); font-weight: 600; }
  .rs-footer-summary-val { font-size: 1.15rem; font-weight: 800; color: #fff; }
  .rs-footer-summary-discount { font-size: 0.72rem; color: #ffc107; font-weight: 600; }
  .rs-footer-actions { display: flex; align-items: center; gap: 0.6rem; flex-shrink: 0; margin-left: auto; }

  .rs-select {
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.18) !important;
    border-radius: 8px; color: #fff !important;
    font-size: 0.82rem; padding: 9px 14px;
    min-width: 160px;
    appearance: auto;
  }
  .rs-select:focus { box-shadow: none !important; border-color: #ffc107 !important; outline: none; }
  .rs-select option { background: #1a1a1a; color: #fff; }

  .rs-pay-btn {
    display: inline-flex; align-items: center; justify-content: center; gap: 6px;
    box-sizing: border-box;
    background: #ffc107; color: #111 !important;
    border: none; border-radius: 20px;
    font-size: 0.78rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
    padding: 0 22px; height: 34px; cursor: pointer;
    transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
    box-shadow: 0 3px 12px rgba(255, 193, 7, 0.3);
  }
  .rs-pay-btn:hover:not(:disabled) {
    background: #e6ac00;
    transform: translateY(-1px);
    box-shadow: 0 5px 18px rgba(255, 193, 7, 0.45);
  }
  .rs-pay-btn.ready {
    background: #4CAF50;
    color: #fff !important;
    box-shadow: 0 3px 12px rgba(76, 175, 80, 0.35);
  }
  .rs-pay-btn.ready:hover:not(:disabled) {
    background: #43a047;
    box-shadow: 0 5px 18px rgba(76, 175, 80, 0.5);
  }
  .rs-pay-btn:disabled {
    background: rgba(255, 255, 255, 0.07) !important;
    color: rgba(255, 255, 255, 0.2) !important;
    cursor: not-allowed; box-shadow: none; transform: none;
  }

  /* ── RESPONSIVE ── */
  @media (max-width: 767px) {
    /* Romper cadena fixed-height/overflow para scroll natural en móvil */
    .contenedor-general { height: auto !important; overflow: visible !important; }
    .card-principal { overflow: visible !important; flex: none !important; padding: 12px 10px !important; margin-bottom: 30px !important; }
    .rs-page { overflow: visible !important; flex: none !important; }
    .rs-main-card { overflow: visible !important; flex: none !important; }

    /* Topbar: sin saltos de línea en el lado izquierdo */
    .rv-topbar > div:first-child { flex-wrap: nowrap !important; }
    .rv-topbar .step-badge { font-size: 0.72rem; padding: 5px 10px; white-space: nowrap; }
    .rv-topbar .step-name { display: none; }
    .rv-topbar .step-divider { display: none; }
    .sb-back-btn { font-size: 0.7rem; padding: 5px 10px 5px 8px; }

    /* Timer: ocultar label para ahorrar espacio */
    .rv-timer { padding: 5px 10px; gap: 0.3rem; }
    .rv-timer-label { display: none; }
    .rv-timer-display { font-size: 0.88rem; letter-spacing: 1px; }

    /* Footer */
    .rs-footer { flex-direction: column; align-items: stretch; gap: 0.5rem; }
    .rs-footer-actions { margin-left: 0; width: 100%; }
    .rs-footer-actions .sb-back-btn { flex-shrink: 0; }
    .rs-pay-btn { width: 100%; justify-content: center; }

    /* Campos de pago */
    .rs-select { min-width: 100%; }
    .rs-pay-fields { flex-direction: column; }
    .rs-pay-field { min-width: 100%; }

    /* Mesa info: columna única */
    .rs-mesa-bloque { grid-template-columns: 1fr; }
    .rs-mesa-dato:nth-child(odd) { border-right: none; }

    /* Tabla invitados: fuentes más compactas */
    .rs-table thead th { font-size: 0.7rem; padding: 8px 8px; letter-spacing: 1px; }
    .rs-table tbody tr td { padding: 7px 8px; font-size: 0.78rem; }
    .rs-table tfoot th { padding: 8px 8px; font-size: 0.72rem; }
    .rs-guest-name { font-size: 0.78rem; }
    .rs-type-badge { font-size: 0.6rem; }
  }
</style>

<?php if ($this->error && $this->errorMessage != ''): ?>
  <script>
    Swal.fire({
      icon: 'warning',
      title: 'Error',
      text: '<?php echo $this->errorMessage ?>',
      confirmButtonText: 'Aceptar',
      confirmButtonColor: '#3085d6',
    });
  </script>
<?php endif ?>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const totalPagar = <?php echo $this->totalGeneral ?>;
    const selectMetodo = document.getElementById('metodo');
    const selectCuotas = document.getElementById('cuotas');
    const btnProcederPago = document.getElementById('btnProcederPago');
    const btnTexto = document.getElementById('btnTexto');
    const btnSpinner = document.getElementById('btnSpinner');
    const formPago = document.getElementById('formPago');
    const metodoPagoHidden = document.getElementById('metodo_pago_hidden');
    const numeroCuotasHidden = document.getElementById('numero_cuotas_hidden');

    const checkboxesTerminos = document.querySelectorAll('.termino-checkbox');

    const cuotasWrapper = document.getElementById('cuotasFieldWrapper');

    // Refleja en el botón (amarillo -> verde) si ya están dadas todas las
    // condiciones para pagar: términos aceptados, método elegido y, si aplica, cuotas.
    function actualizarEstadoBotonPago () {
      const todosAceptados = checkboxesTerminos.length === 0 || Array.from(checkboxesTerminos).every(checkbox => checkbox.checked);
      const metodoPago = selectMetodo.value;
      let cuotasOk = true;
      if (metodoPago === 'cargo' && selectCuotas && selectCuotas.tagName === 'SELECT') {
        cuotasOk = !!selectCuotas.value;
      }
      const listo = todosAceptados && !!metodoPago && cuotasOk;
      btnProcederPago.classList.toggle('ready', listo);
    }

    checkboxesTerminos.forEach(function (checkbox) {
      checkbox.addEventListener('change', actualizarEstadoBotonPago);
    });

    selectMetodo.addEventListener('change', function () {
      const metodoPago = this.value;
      if (selectCuotas && selectCuotas.tagName === 'SELECT') {
        const mostrar = metodoPago === 'cargo';
        if (cuotasWrapper) cuotasWrapper.style.display = mostrar ? 'flex' : 'none';
        selectCuotas.selectedIndex = 0;
      }
      metodoPagoHidden.value = metodoPago;
      numeroCuotasHidden.value = '';
      actualizarEstadoBotonPago();
    });

    if (selectCuotas && selectCuotas.tagName === 'SELECT') {
      selectCuotas.addEventListener('change', function () {
        numeroCuotasHidden.value = this.value;
        actualizarEstadoBotonPago();
      });
    }

    actualizarEstadoBotonPago();

    // Valida el formulario al momento del click e indica puntualmente qué falta
    function validarAntesDePagar () {
      if (totalPagar <= 0) {
        Swal.fire({ icon: 'warning', title: 'Nada por pagar', text: 'El total a pagar no es válido.', confirmButtonText: 'Aceptar', confirmButtonColor: '#3085d6' });
        return false;
      }

      const todosAceptados = Array.from(checkboxesTerminos).every(checkbox => checkbox.checked);
      if (checkboxesTerminos.length > 0 && !todosAceptados) {
        Swal.fire({ icon: 'warning', title: 'Términos y condiciones', text: 'Debes aceptar los términos y condiciones para continuar.', confirmButtonText: 'Entendido', confirmButtonColor: '#3085d6' });
        return false;
      }

      const metodoPago = selectMetodo.value;
      if (!metodoPago) {
        Swal.fire({ icon: 'warning', title: 'Método de pago', text: 'Debes seleccionar un método de pago para continuar.', confirmButtonText: 'Entendido', confirmButtonColor: '#3085d6' });
        return false;
      }

      if (metodoPago === 'cargo' && selectCuotas && selectCuotas.tagName === 'SELECT' && !selectCuotas.value) {
        Swal.fire({ icon: 'warning', title: 'Número de cuotas', text: 'Debes seleccionar el número de cuotas para continuar.', confirmButtonText: 'Entendido', confirmButtonColor: '#3085d6' });
        return false;
      }

      metodoPagoHidden.value = metodoPago;
      numeroCuotasHidden.value = selectCuotas ? selectCuotas.value : '1';
      return true;
    }

    btnProcederPago.addEventListener('click', function () {
      if (this.disabled) return;
      if (!validarAntesDePagar()) return;

      this.disabled = true;

      Swal.fire({
        title: 'Confirmar pago',
        html: `¿Confirmas que deseas proceder con el pago de <b>$${totalPagar.toLocaleString('es-CO')}</b>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, pagar',
        cancelButtonText: 'Cancelar',
        customClass: { actions: 'my-swal-actions', confirmButton: 'me-3' }
      }).then((result) => {
        if (result.isConfirmed) {
          btnTexto.style.display = 'none';
          btnSpinner.style.display = 'inline-block';
          formPago.submit();
        } else {
          btnProcederPago.disabled = false;
        }
      });
    });
  });

  function aceptarTermino (terminoId) {
    const checkbox = document.getElementById('termino_' + terminoId);
    if (checkbox) {
      checkbox.checked = true;
      checkbox.dispatchEvent(new Event('change'));
    }
  }

  function aceptarTerminosEvento () {
    const primerCheckbox = document.querySelector('.termino-checkbox');
    if (primerCheckbox) {
      primerCheckbox.checked = true;
      primerCheckbox.dispatchEvent(new Event('change'));
    }
  }
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    validarSesion();
    setInterval(validarSesion, 1000);
  })
</script>
