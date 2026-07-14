<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

<div class="rm-page container py-2 py-lg-3 mb-3 mb-lg-5">
  <form action="/page/evento/confirmarmesa" method="post" id="formSeleccionMesa" class="mb-3 mb-lg-5 card-principal">
    <input type="hidden" name="reserva_id" value="<?php echo enc_id($this->reserva->id); ?>">
    <input type="hidden" name="mesa_seleccionada" id="mesa_seleccionada" value="">

    <!-- ── Encabezado: regresar · step | timer ── -->
    <div class="rv-topbar mb-3">

      <!-- Izquierda: regresar + step + personas -->
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="/page/evento/reservar?boking=<?php echo enc_id($this->reserva->id); ?>" class="sb-back-btn">
          <i class="fa-solid fa-angle-left"></i>&nbsp; Regresar
        </a>
        <div class="step-indicator">
          <div class="step-badge">
            <i class="fa-solid fa-chair me-2"></i>
            <span class="step-text">Paso 3 de 5</span>
            <span class="step-divider">•</span>
            <span class="step-name"><?php echo (($this->tipoSeleccion ?? 'mesa') === 'silla') ? 'Selección de sillas' : 'Selección de mesa'; ?></span>
          </div>
        </div>
        <span class="rm-persons-tag">
          <i class="fa-solid fa-users me-1"></i>
          <?php echo $this->cantidadPersonas; ?> persona<?php echo $this->cantidadPersonas != 1 ? 's' : ''; ?>
        </span>
      </div>

      <!-- Derecha: timer -->
      <?php if ($this->reservaSesion && $this->reservaSesion->reserva_estado == 1) { ?>
        <div id="tiempo-restante-reserva" class="rv-timer">
          <i class="fa-solid fa-clock rv-timer-icon"></i>
          <div class="rv-timer-display">
            <span id="minutos">00</span><span class="rv-timer-colon">:</span><span id="segundos">00</span>
          </div>
          <span class="rv-timer-label">tiempo restante</span>
          <input type="hidden" id="minutosHidden" name="minutosHidden">
        </div>
      <?php } ?>

    </div>

    <!-- ── Contenedor principal oscuro (todo el contenido funcional) ── -->
    <div class="rm-main-card">

      <?php
      $mesasSeleccionadas = [];
      if (isset($_COOKIE['mesas_seleccionadas'])) {
        $mesasSeleccionadas = json_decode($_COOKIE['mesas_seleccionadas'], true);
      }
      $esSilla = (($this->tipoSeleccion ?? 'mesa') === 'silla');
      $totalSillas = $esSilla ? count($mesasSeleccionadas) : 0;
      // En modo silla se renderiza UN solo bloque (piso→ambiente→mapa) donde se seleccionan
      // varias sillas con clics directos sobre el mapa, en vez de una pestaña por silla.
      $stepsParaRenderizar = $esSilla ? [0 => ($mesasSeleccionadas[0] ?? 1)] : $mesasSeleccionadas;
      ?>

      <?php if ($esSilla): ?>
        <div class="alert alert-info rm-silla-hint" style="margin:0 0 14px;font-size:.92rem;">
          <i class="fa-solid fa-circle-info me-1"></i>
          Elige un piso y un ambiente, luego haz clic en las sillas que quieras dentro del mapa
          (<strong id="sillasContadorTop">0</strong> / <?= $totalSillas ?> seleccionadas).
        </div>
      <?php endif; ?>

      <!-- ─ Wizard tabs (solo modo mesa; en modo silla hay un único bloque) ── -->
      <?php if (!$esSilla && count($stepsParaRenderizar) > 1): ?>
        <div class="wizard-box mb-3">
          <div class="wizard-header">
            <?php foreach ($stepsParaRenderizar as $index => $capacidad): ?>
              <div class="wizard-step <?php echo $index === 0 ? 'active' : ''; ?>" data-step="<?php echo $index; ?>">
                <div class="circle"><?php echo $index + 1; ?></div>
                <span class="step-label"><?php echo htmlspecialchars($capacidad) . ' Personas'; ?></span>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <!--  Wizard contents ── -->
      <?php foreach ($stepsParaRenderizar as $index => $capacidad): ?>
        <div class="wizard-content <?php echo $index === 0 ? 'active' : ''; ?>" data-content="<?php echo $index; ?>">
          <div class="rm-layout">

            <!-- LEFT: selector panel -->
            <aside class="rm-selector-panel sticky-top">
              <div class="rm-panel-header">
                <span class="rm-panel-label"><?= $esSilla ? 'Selección de sillas' : 'Selección de mesa' ?></span>
                <small class="rm-panel-hint"><?= $esSilla ? 'Piso → Ambiente → Clic en las sillas' : 'Piso → Ambiente → Mesa' ?></small>
              </div>

              <div class="rm-steps-form">

                <!-- Step 1: Piso -->
                <div class="rm-step-item">
                  <div class="rm-step-num">1</div>
                  <div class="rm-step-body">
                    <label class="rm-step-label-text" for="selectPiso_<?= $index ?>">Seleccione el piso</label>
                    <select class="form-select form-select-lg rm-select" id="selectPiso_<?= $index ?>">
                      <option value="">— Elija un piso —</option>
                      <?php if (isset($this->pisosPorCapacidad[$index]) && count($this->pisosPorCapacidad[$index]) > 0): ?>
                        <?php foreach ($this->pisosPorCapacidad[$index] as $piso): ?>
                          <option value="<?php echo $piso->piso_id; ?>" data-color="<?php echo $piso->piso_color; ?>">
                            <?php echo htmlspecialchars(ucwords(strtolower($piso->piso_nombre))); ?>
                            (<?php echo $piso->total_mesas; ?> <?php echo $esSilla ? 'sillas' : 'mesas'; ?>)
                          </option>
                        <?php endforeach; ?>
                      <?php else: ?>
                        <option value="" disabled><?php echo $esSilla ? 'Sin pisos con sillas disponibles' : 'Sin pisos para ' . $mesasSeleccionadas[$index] . ' personas'; ?>
                        </option>
                      <?php endif; ?>
                    </select>
                    <div class="content-info-ubication">
                      <span id="infoPiso_<?= $index ?>" class="d-block"></span>
                    </div>
                  </div>
                </div>

                <!-- Step 2: Ambiente -->
                <div class="rm-step-item">
                  <div class="rm-step-num">2</div>
                  <div class="rm-step-body">
                    <label class="rm-step-label-text">Seleccione el ambiente</label>
                    <select class="d-none" id="selectAmbiente_<?= $index ?>" name="ambiente_id">
                      <option value="">— Seleccione un ambiente —</option>
                    </select>
                    <div id="ambienteCards_<?= $index ?>" class="ambiente-cards-container">
                      <p class="ambiente-placeholder">— Primero elija un piso —</p>
                    </div>
                    <div class="content-info-ubication mt-3">
                      <span id="infoAmbiente_<?= $index ?>" class="d-block"></span>
                    </div>
                  </div>
                </div>

                <!-- Step 3: Mesa / Sillas -->
                <div class="rm-step-item">
                  <div class="rm-step-num">3</div>
                  <div class="rm-step-body">
                    <?php if ($esSilla): ?>
                      <label class="rm-step-label-text">Haz clic en las sillas del mapa</label>
                      <div class="rm-sillas-progress-inline">
                        <i class="fa-solid fa-chair"></i>
                        <strong id="sillasContador_<?= $index ?>">0</strong> / <?= $totalSillas ?> sillas seleccionadas
                      </div>
                      <div id="sillasListaSeleccion_<?= $index ?>" class="rm-sillas-chips"></div>
                      <select class="d-none" id="selectMesa_<?= $index ?>" name="mesa_id_unused"></select>
                    <?php else: ?>
                      <label class="rm-step-label-text" for="selectMesa_<?= $index ?>">Seleccione su mesa</label>
                      <select class="form-select form-select-lg rm-select" id="selectMesa_<?= $index ?>" name="mesa_id"
                        disabled>
                        <option value="">— Primero elija un ambiente —</option>
                      </select>
                    <?php endif; ?>
                    <div class="content-info-ubication">
                      <span id="infoMesa_<?= $index ?>" class="d-block"></span>
                    </div>
                  </div>
                </div>
              </div>

            </aside>

            <!-- RIGHT: grid panel -->
            <div class="rm-grid-panel">

              <!-- Legend + ambiente title -->
              <div class="sticky-legend sticky-top">
                <div class="legend-title me-5">
                  <h4 style="color: #111;"><span id="legend-ambiente"></span></h4>
                  <hr class="m-0" style="color: #ebebeb;">
                </div>
                <div class="grid-legend my-2" id="gridLegend_<?= $index ?>">
                  <div class="legend-item">
                    <div class="legend-color" style="background-color: #4CAF50;"></div><span>Disponible</span>
                  </div>
                  <div class="legend-item">
                    <div class="legend-color" style="background-color: #ffe066;"></div><span>Mi selección</span>
                  </div>
                  <div class="legend-item">
                    <div class="legend-color" style="background-color: #F44336;"></div><span>Ocupada</span>
                  </div>
                  <div class="legend-item">
                    <div class="legend-color" style="background-color: blue;"></div><span>Ventana</span>
                  </div>
                  <div class="legend-item d-none">
                    <div class="legend-color" style="background-color: #2E7D32;"></div><span>Ingreso</span>
                  </div>
                  <div class="legend-item">
                    <div class="legend-color" style="background-color: #8B4513;"></div><span>Puerta</span>
                  </div>
                  <div class="legend-item">
                    <div class="legend-color" style="background-color: #795548;"></div><span>Barra</span>
                  </div>
                  <div class="legend-item">
                    <div class="legend-color" style="background-color: #9C27B0;"></div><span>Pista</span>
                  </div>
                  <div class="legend-item d-none">
                    <div class="legend-color" style="background-color: #E91E63;"></div><span>Escalera</span>
                  </div>
                  <div class="legend-item">
                    <div class="legend-color" style="background-color: #FF9800;"></div><span>Pantalla</span>
                  </div>
                  <div class="legend-item d-none">
                    <div class="legend-color" style="background-color: #ec94a2;"></div><span>Terraza</span>
                  </div>
                </div>
              </div>

              <!-- Scroll sync bar superior -->
              <div id="scroll-top-bar-grid_<?= $index ?>" class="scroll-top-bar"
                style="overflow-x:auto; overflow-y:hidden; width:100%; height:18px; margin-bottom:2px;"></div>

              <!-- Grid container -->
              <div class="grid-container" id="scroll-bottom-bar-grid_<?= $index ?>" style="overflow-x:auto;">
                <div id="grid_<?= $index ?>" class="grid d-none"></div>
              </div>

            </div><!-- /rm-grid-panel -->
          </div><!-- /rm-layout -->

          <script src="/components/interactjs/interact.min.js"></script>
        </div><!-- /wizard-content -->
      <?php endforeach; ?>

    </div><!-- /rm-main-card -->

    <!-- ── Footer fijo: info mesa seleccionada + confirmar ── -->
    <div class="rm-footer">

      <?php foreach ($stepsParaRenderizar as $index => $capacidad): ?>
        <div id="detallesMesa_<?= $index ?>" class="rm-footer-detail" style="display: none;">
          <i class="fa-solid fa-check-circle rm-fd-icon"></i>
          <div class="rm-fd-items">
            <span class="rm-fd-item">
              <span class="rm-fd-key"><?= $esSilla ? 'Sillas: ' : 'Mesa: ' ?></span>
              <span id="detalleMesaNombre_<?= $index ?>" class="rm-fd-val"></span>
            </span>
            <?php if (!$esSilla): ?>
              <span class="rm-fd-sep">•</span>
              <span class="rm-fd-item">
                <span class="rm-fd-key">Piso: </span>
                <span id="detallePiso_<?= $index ?>" class="rm-fd-val"></span>
              </span>
              <span class="rm-fd-sep">•</span>
              <span class="rm-fd-item">
                <span class="rm-fd-key">Ambiente: </span>
                <span id="detalleAmbiente_<?= $index ?>" class="rm-fd-val"></span>
              </span>
              <span class="rm-fd-sep">•</span>
              <span class="rm-fd-item">
                <span class="rm-fd-key">Capacidad: </span>
                <span id="detalleMesaCapacidad_<?= $index ?>" class="rm-fd-val"></span>
                <span class="rm-fd-unit">pers.</span>
              </span>
            <?php else: ?>
              <span style="display:none">
                <span id="detallePiso_<?= $index ?>"></span>
                <span id="detalleAmbiente_<?= $index ?>"></span>
                <span id="detalleMesaCapacidad_<?= $index ?>"></span>
              </span>
            <?php endif; ?>
            <span style="display:none">
              <span id="detalleMesaCodigo_<?= $index ?>"></span>
              <span id="detalleCategoria_<?= $index ?>"></span>
            </span>
          </div>
        </div>
      <?php endforeach; ?>

      <div class="rm-footer-actions">
        <button type="submit" class="btn btn-primary btn-lg event-btn rm-confirm-btn" id="btnConfirmarMesa" disabled>
          <i class="fas fa-check-circle me-2"></i><?= $esSilla ? 'Confirmar sillas' : 'Confirmar Mesa' ?>
        </button>
        <div id="loadingSnippet" class="d-none rm-footer-loading">
          <div class="spinner-border" role="status"
            style="width:1.6rem;height:1.6rem;border-color:#ffc107;border-right-color:transparent;">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <span class="rm-loading-text">Procesando...</span>
        </div>
      </div>

    </div><!-- /rm-footer -->

  </form>
</div>

<script>
  // Modo de selección de este flujo: 'mesa' (una mesa) o 'silla' (varias sillas mismo ambiente)
  window.tipoSeleccion = <?= json_encode($this->tipoSeleccion ?? 'mesa'); ?>;

  // Mensajes de error devueltos por el servidor al confirmar
  <?php $errParam = isset($_GET['error']) ? $_GET['error'] : ''; ?>
  <?php if ($errParam === 'ambiente_mixto'): ?>
    document.addEventListener('DOMContentLoaded', function () {
      Swal.fire({ icon: 'warning', title: 'Mismo ambiente', text: 'Todas las sillas deben pertenecer al mismo ambiente. Por favor selecciónalas de nuevo.' });
    });
  <?php elseif ($errParam === 'mesa_no_disponible'): ?>
    document.addEventListener('DOMContentLoaded', function () {
      Swal.fire({ icon: 'warning', title: 'No disponible', text: 'Alguna de las opciones seleccionadas ya no está disponible. Intenta nuevamente.' });
    });
  <?php endif; ?>

  // Lógica para avanzar al siguiente paso (ya estaba)
  document.querySelectorAll('.btn-next').forEach(button => {
    button.addEventListener('click', () => {
      const currentStep = button.closest('.wizard-content');
      const stepIndex = parseInt(currentStep.dataset.content);
      const nextStep = stepIndex + 1;
      changeStep(nextStep);
    });
  });

  // NUEVO: Al hacer clic directamente en un paso
  document.querySelectorAll('.wizard-step').forEach(step => {
    step.addEventListener('click', () => {
      const targetStep = parseInt(step.dataset.step);
      changeStep(targetStep);
    });
  });

  // Funcin para cambiar de paso (centralizada)
  function changeStep (targetStep) {
    document.querySelectorAll('.wizard-content').forEach(content => {
      content.classList.remove('active');
    });
    document.querySelectorAll('.wizard-step').forEach(header => {
      header.classList.remove('active');
    });
    const nextContent = document.querySelector(`.wizard-content[data-content="${targetStep}"]`);
    const nextHeader = document.querySelector(`.wizard-step[data-step="${targetStep}"]`);
    if (nextContent) nextContent.classList.add('active');
    if (nextHeader) nextHeader.classList.add('active');

    // Mostrar solo el detalle del paso activo en el footer
    document.querySelectorAll('.rm-footer-detail').forEach((d, idx) => {
      const nombre = document.getElementById('detalleMesaNombre_' + idx);
      d.style.display = (idx === targetStep && nombre && nombre.textContent.trim()) ? 'flex' : 'none';
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    const totalMesas = <?= count($mesasSeleccionadas); ?>;
    const mesasSeleccionadas = <?= json_encode($mesasSeleccionadas); ?>;
    const tipoSeleccion = window.tipoSeleccion || 'mesa';
    const btnConfirmarMesa = document.getElementById('btnConfirmarMesa');
    let mesasSeleccionadasPorPaso = {};
    const mesaSeleccionadaInput = document.getElementById('mesa_seleccionada');

    function verificarSeleccionesCompletas () {
      let todasSeleccionadas = true;
      for (let i = 0; i < totalMesas; i++) {
        if (!mesasSeleccionadasPorPaso[i] || mesasSeleccionadasPorPaso[i] === '') {
          todasSeleccionadas = false;
          break;
        }
      }
      btnConfirmarMesa.disabled = !todasSeleccionadas;
    }

    function initializeSelections () {
      if (mesaSeleccionadaInput.value) {
        const selectedIds = mesaSeleccionadaInput.value.split(',');
        selectedIds.forEach((mesaId, index) => {
          if (index < totalMesas && mesaId && mesaId !== '') {
            mesasSeleccionadasPorPaso[index] = mesaId;
          }
        });
      }
      verificarSeleccionesCompletas();
    }

    function actualizarInputMesasSeleccionadas () {
      const selectedIds = Array(totalMesas).fill('');
      Object.keys(mesasSeleccionadasPorPaso).forEach(index => {
        if (index < totalMesas) {
          selectedIds[index] = mesasSeleccionadasPorPaso[index] || '';
        }
      });
      mesaSeleccionadaInput.value = selectedIds.join(',');
      verificarSeleccionesCompletas();
    }

    function sincronizarSelecciones (reservaMesaId) {
      mesasSeleccionadasPorPaso = {};
      if (reservaMesaId) {
        const selectedIds = reservaMesaId.split(',');
        selectedIds.forEach((mesaId, index) => {
          if (index < totalMesas && mesaId && mesaId !== '') {
            mesasSeleccionadasPorPaso[index] = mesaId;
          }
        });
      }
      actualizarInputMesasSeleccionadas();
    }

    initializeSelections();

    const MESES_ES = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    // Construye { date, label } a partir de ambiente_fecha_partido (fecha + hora de inicio del partido).
    // Las reservas cierran exactamente cuando arranca el partido. Si no hay fecha configurada, retorna null.
    function obtenerInfoPartido (ambiente) {
      if (!ambiente.ambiente_fecha_partido) return null;
      const date = new Date(ambiente.ambiente_fecha_partido.replace(' ', 'T'));
      const label = date.getDate() + ' de ' + MESES_ES[date.getMonth()];
      return { label: label, date: date };
    }

    function renderAmbienteCards (ambientes, stepIndex) {
      const container = document.getElementById('ambienteCards_' + stepIndex);
      const selectAmb = document.getElementById('selectAmbiente_' + stepIndex);
      if (!container) return;
      container.innerHTML = '';

      if (!window._ambienteIntervals) window._ambienteIntervals = {};
      if (window._ambienteIntervals[stepIndex]) {
        window._ambienteIntervals[stepIndex].forEach(function (id) { clearInterval(id); });
      }
      window._ambienteIntervals[stepIndex] = [];

      let visibles = 0;

      ambientes.forEach(function (ambiente, idx) {
        // Si el ambiente no tiene fecha de partido configurada en BD,
        // se muestra igual pero sin etiqueta de fecha ni cuenta regresiva de cierre.
        const info = obtenerInfoPartido(ambiente);

        const sinMesas = !ambiente.total_mesas || parseInt(ambiente.total_mesas) === 0;
        const esPasado = info ? info.date < new Date() : false;
        const deshabilitado = sinMesas || esPasado;

        visibles++;

        const card = document.createElement('label');
        card.className = 'ambiente-card' + (deshabilitado ? ' ambiente-card-disabled' : '');
        card.dataset.ambienteId = ambiente.ambiente_id;

        let estadoHtml;
        if (esPasado) {
          estadoHtml = '<div class="ambiente-countdown ambiente-sin-disp">Ambiente no disponible</div>';
        } else if (sinMesas) {
          estadoHtml = '<div class="ambiente-countdown ambiente-sin-disp">Sin disponibilidad</div>';
        } else if (info) {
          estadoHtml = '<div class="ambiente-countdown" id="pcd_' + stepIndex + '_' + idx + '">Disponible</div>';
        } else {
          estadoHtml = '<div class="ambiente-countdown">Disponible</div>';
        }

        const fechaTxt = ambiente.ambiente_nombre;

        card.innerHTML =
          (!deshabilitado ? '<input type="radio" name="ambiente_radio_' + stepIndex + '" value="' + ambiente.ambiente_id + '" class="d-none">' : '') +
          '<div class="ambiente-card-body">' +
            '<div class="ambiente-card-top">' +
              '<span class="ambiente-num">Ambiente ' + visibles + '</span>' +
              '<span class="ambiente-fecha">' + fechaTxt + '</span>' +
            '</div>' +
            estadoHtml +
          '</div>';

        if (!deshabilitado) {
          card.querySelector('input').addEventListener('change', function () {
            container.querySelectorAll('.ambiente-card').forEach(function (c) { c.classList.remove('activo'); });
            card.classList.add('activo');
            selectAmb.value = ambiente.ambiente_id;
            selectAmb.dispatchEvent(new Event('change', { bubbles: true }));
          });

          if (info) {
            const el = document.getElementById('pcd_' + stepIndex + '_' + idx);
            const tick = function () {
              const left = info.date - new Date();
              if (left <= 0) { if (el) el.textContent = 'Reservas cerradas'; return; }
              const d = Math.floor(left / 86400000);
              const h = Math.floor((left % 86400000) / 3600000);
              const m = Math.floor((left % 3600000) / 60000);
              const s = Math.floor((left % 60000) / 1000);
              if (el) el.textContent = 'Cierra en: ' + (d ? d + 'd ' : '') + String(h).padStart(2, '0') + 'h ' + String(m).padStart(2, '0') + 'm ' + String(s).padStart(2, '0') + 's';
            };
            tick();
            window._ambienteIntervals[stepIndex].push(setInterval(tick, 1000));
          }
        }

        container.appendChild(card);
      });

      if (visibles === 0) {
        container.innerHTML = '<p class="ambiente-placeholder">No hay ambientes disponibles</p>';
      }
    }

    // En modo silla solo hay UN bloque (piso→ambiente→mapa) donde se seleccionan varias
    // sillas con clics directos; en modo mesa se conserva la lógica original por paso.
    const totalSteps = tipoSeleccion === 'silla' ? 1 : mesasSeleccionadas.length;
    for (let i = 0; i < totalSteps; i++) {
      const capacidadRequerida = mesasSeleccionadas[i];

      const selectPiso = document.getElementById('selectPiso_' + i);
      const selectAmbiente = document.getElementById('selectAmbiente_' + i);
      const selectMesa = document.getElementById('selectMesa_' + i);
      const infoPiso = document.getElementById('infoPiso_' + i);
      const infoAmbiente = document.getElementById('infoAmbiente_' + i);
      const infoMesa = document.getElementById('infoMesa_' + i);
      const detallesMesa = document.getElementById('detallesMesa_' + i);
      const detalleMesaNombre = document.getElementById('detalleMesaNombre_' + i);
      const detalleMesaCapacidad = document.getElementById('detalleMesaCapacidad_' + i);
      const detalleMesaCodigo = document.getElementById('detalleMesaCodigo_' + i);
      const detallePiso = document.getElementById('detallePiso_' + i);
      const detalleAmbiente = document.getElementById('detalleAmbiente_' + i);
      const detalleCategoria = document.getElementById('detalleCategoria_' + i);

      if (tipoSeleccion === 'silla') {
        // ================================================================
        // MODO SILLAS: selección múltiple con clics directos en un mismo
        // mapa (piso + ambiente elegidos una sola vez). Nada de pestañas.
        // ================================================================
        const totalSillas = totalMesas;
        let slots = new Array(totalSillas).fill(null); // {id, nombre} | null por cada silla a elegir
        let detenerConsultaSillas = null;

        function sillasElegidas () {
          return slots.filter(function (s) { return s !== null; });
        }

        function actualizarProgresoSillas () {
          const elegidas = sillasElegidas();
          const contadorEl = document.getElementById('sillasContador_' + i);
          if (contadorEl) contadorEl.textContent = elegidas.length;
          const contadorTop = document.getElementById('sillasContadorTop');
          if (contadorTop) contadorTop.textContent = elegidas.length;

          const listaEl = document.getElementById('sillasListaSeleccion_' + i);
          if (listaEl) {
            listaEl.innerHTML = '';
            elegidas.forEach(function (s) {
              const chip = document.createElement('span');
              chip.className = 'rm-silla-chip';
              chip.innerHTML = (s.nombre || ('Silla ' + s.id)) + ' <i class="fa-solid fa-xmark"></i>';
              chip.querySelector('i').addEventListener('click', function (ev) {
                ev.stopPropagation();
                deseleccionarSilla(s.id);
              });
              listaEl.appendChild(chip);
            });
          }

          mesaSeleccionadaInput.value = elegidas.map(function (s) { return s.id; }).join(',');
          btnConfirmarMesa.disabled = elegidas.length !== totalSillas;

          if (elegidas.length > 0) {
            detallesMesa.style.display = window.innerWidth <= 767 ? 'block' : 'flex';
            detalleMesaNombre.textContent = elegidas.map(function (s) { return s.nombre || ('Silla ' + s.id); }).join(', ');
          } else {
            detallesMesa.style.display = 'none';
          }
        }

        function marcarClaseGrid (mesaId, clase) {
          const el = document.querySelector('#grid_' + i + " .elemento[data-id='" + mesaId + "']");
          if (el) {
            el.classList.remove('libre', 'ocupada', 'seleccionada');
            el.classList.add(clase);
            el.style.pointerEvents = (clase === 'ocupada') ? 'none' : '';
          }
        }

        function seleccionarSilla (mesaId, nombre) {
          const libre = slots.indexOf(null);
          if (libre === -1) {
            Swal.fire({ icon: 'warning', title: 'Máximo alcanzado', text: 'Ya seleccionaste tus ' + totalSillas + ' sillas. Quita una de la lista para cambiarla.' });
            return;
          }
          fetch('/page/evento/seleccionarmesa', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ mesa_id: mesaId, accion: 'seleccionar', stepIndex: libre })
          }).then(function (r) { return r.json(); }).then(function (data) {
            if (data.success) {
              slots[libre] = { id: String(mesaId), nombre: nombre };
              marcarClaseGrid(mesaId, 'seleccionada');
              actualizarProgresoSillas();
            } else {
              Swal.fire({ icon: 'warning', title: 'No disponible', text: data.message || 'Esta silla ya no está disponible.' });
              marcarClaseGrid(mesaId, 'ocupada');
            }
          }).catch(function () {
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo seleccionar la silla.' });
          });
        }

        function deseleccionarSilla (mesaId) {
          const idx = slots.findIndex(function (s) { return s && s.id === String(mesaId); });
          if (idx === -1) return;
          fetch('/page/evento/seleccionarmesa', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ mesa_id: mesaId, accion: 'deseleccionar', stepIndex: idx })
          }).catch(function () {}).finally(function () {
            slots[idx] = null;
            marcarClaseGrid(mesaId, 'libre');
            actualizarProgresoSillas();
          });
        }

        function limpiarTodasLasSillas () {
          slots.forEach(function (s, idx) {
            if (s) {
              fetch('/page/evento/seleccionarmesa', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mesa_id: s.id, accion: 'deseleccionar', stepIndex: idx })
              }).catch(function () {});
            }
          });
          slots = new Array(totalSillas).fill(null);
          actualizarProgresoSillas();
        }

        function crearElementoSillaEnGrid (mesa) {
          if (!mesa || typeof mesa !== 'object' || !mesa.mesa_id) return null;
          const ancho = parseInt(mesa.mesa_ancho) || 1;
          const alto = parseInt(mesa.mesa_alto) || 1;
          const posX = parseInt(mesa.mesa_posicion_x) || parseInt(mesa.mesa_pos_x) || 0;
          const posY = parseInt(mesa.mesa_posicion_y) || parseInt(mesa.mesa_pos_y) || 0;
          const rotacion = parseInt(mesa.mesa_rotacion) || 0;
          const isMobile = window.innerWidth <= 768;
          const isTablet = window.innerWidth > 768 && window.innerWidth <= 1024;
          const cellSize = isMobile ? 15 : isTablet ? 35 : 20;
          const gap = isMobile ? 1 : 2;

          const idx = slots.findIndex(function (s) { return s && s.id === String(mesa.mesa_id); });
          if (idx !== -1 && mesa.mesa_nombre) {
            slots[idx].nombre = mesa.mesa_nombre || mesa.mesa_codigo || slots[idx].nombre;
          }
          const yaEsMia = idx !== -1;
          const ocupada = (mesa.mesa_estado == 1 || mesa.mesa_estado === '1') && !yaEsMia && !mesa.es_mia;
          const estadoClase = (yaEsMia || mesa.es_mia) ? 'seleccionada' : (ocupada ? 'ocupada' : 'libre');

          const elemento = document.createElement('div');
          elemento.className = `elemento ${mesa.mesa_tipo || 'silla'} ${estadoClase}`;
          if (estadoClase === 'ocupada') elemento.style.pointerEvents = 'none';
          elemento.innerText = mesa.mesa_nombre || mesa.mesa_codigo || `Silla ${mesa.mesa_id}`;
          elemento.style.position = 'absolute';
          elemento.style.width = (cellSize * ancho + gap * (ancho - 1)) + 'px';
          elemento.style.height = (cellSize * alto + gap * (alto - 1)) + 'px';
          elemento.style.left = (posX * (cellSize + gap)) + 'px';
          elemento.style.top = (posY * (cellSize + gap)) + 'px';
          elemento.style.transform = `rotate(${rotacion}deg)`;
          elemento.style.fontSize = isMobile ? '9px' : isTablet ? '10px' : '12px';
          elemento.dataset.id = mesa.mesa_id;

          elemento.addEventListener('click', function () {
            if (elemento.classList.contains('ocupada')) {
              Swal.fire({ icon: 'warning', title: 'Silla no disponible', text: 'Esta silla ya fue tomada por otro usuario. Elige otra.' });
              return;
            }
            const idStr = String(mesa.mesa_id);
            const yaSeleccionada = slots.some(function (s) { return s && s.id === idStr; });
            if (yaSeleccionada) {
              deseleccionarSilla(idStr);
            } else {
              seleccionarSilla(idStr, mesa.mesa_nombre || mesa.mesa_codigo || ('Silla ' + mesa.mesa_id));
            }
          });
          return elemento;
        }

        function mostrarGridSilla (ambiente, sillas, todosElementos) {
          const grid = document.getElementById('grid_' + i);
          grid.classList.remove('d-none');
          grid.innerHTML = '';

          const isMobile = window.innerWidth <= 768;
          const isTablet = window.innerWidth > 768 && window.innerWidth <= 1024;
          const cellSize = isMobile ? 15 : isTablet ? 35 : 20;
          const gap = isMobile ? 1 : 2;

          for (let j = 0; j < Number(ambiente.ambiente_filas) * Number(ambiente.ambiente_columnas); j++) {
            const cell = document.createElement('div');
            cell.className = 'grid-cell';
            cell.style.width = cellSize + 'px';
            cell.style.height = cellSize + 'px';
            grid.appendChild(cell);
          }
          grid.style.gridTemplateColumns = `repeat(${Number(ambiente.ambiente_columnas)}, ${cellSize}px)`;
          grid.style.gridTemplateRows = `repeat(${Number(ambiente.ambiente_filas)}, ${cellSize}px)`;
          grid.style.gap = gap + 'px';

          (sillas || []).forEach(function (mesa) {
            const el = crearElementoSillaEnGrid(mesa);
            if (el) grid.appendChild(el);
          });

          (todosElementos || []).forEach(function (el) {
            if (!el || typeof el !== 'object' || !el.mesa_id) return;
            const ancho = parseInt(el.mesa_ancho) || 1;
            const alto = parseInt(el.mesa_alto) || 1;
            const posX = parseInt(el.mesa_pos_x) || 0;
            const posY = parseInt(el.mesa_pos_y) || 0;
            const rotacion = parseInt(el.mesa_rotacion) || 0;
            const div = document.createElement('div');
            div.className = `elemento ${el.mesa_tipo} elementonoseleccionable`;
            div.style.pointerEvents = 'none';
            div.innerText = el.mesa_nombre || el.mesa_tipo;
            div.style.position = 'absolute';
            div.style.width = (cellSize * ancho + gap * (ancho - 1)) + 'px';
            div.style.height = (cellSize * alto + gap * (alto - 1)) + 'px';
            div.style.left = (posX * (cellSize + gap)) + 'px';
            div.style.top = (posY * (cellSize + gap)) + 'px';
            div.style.transform = `rotate(${rotacion}deg)`;
            div.style.fontSize = isMobile ? '9px' : isTablet ? '10px' : '12px';
            div.dataset.id = el.mesa_id;
            grid.appendChild(div);
          });

          actualizarProgresoSillas();
        }

        function actualizarEstadoSillasEnGrid (sillas) {
          (sillas || []).forEach(function (mesa) {
            if (!mesa || typeof mesa !== 'object' || !mesa.mesa_id) return;
            const yaEsMia = slots.some(function (s) { return s && s.id === String(mesa.mesa_id); });
            if (yaEsMia) return; // el estado local manda para las que ya elegí
            const mesaDiv = document.querySelector('#grid_' + i + " .elemento[data-id='" + mesa.mesa_id + "']");
            if (!mesaDiv) return;
            const ocupada = (mesa.mesa_estado == 1 || mesa.mesa_estado === '1') && !mesa.es_mia;
            mesaDiv.classList.remove('libre', 'ocupada', 'seleccionada');
            mesaDiv.classList.add(ocupada ? 'ocupada' : 'libre');
            mesaDiv.style.pointerEvents = ocupada ? 'none' : '';
          });
        }

        selectPiso.addEventListener('change', function () {
          const pisoId = this.value;
          selectAmbiente.innerHTML = '<option value="">— Cargando ambientes... </option>';
          selectAmbiente.disabled = true;
          btnConfirmarMesa.disabled = true;
          const _cards = document.getElementById('ambienteCards_' + i);
          if (_cards) _cards.innerHTML = '<p class="ambiente-placeholder">Cargando ambientes...</p>';

          if (!pisoId) {
            infoPiso.classList.remove('activoinfo');
            infoAmbiente.classList.remove('activoinfo');
            selectAmbiente.innerHTML = '<option value="">— Primero elija un piso —</option>';
            infoPiso.textContent = '';
            $("#legend-ambiente").text('');
            if (_cards) _cards.innerHTML = '<p class="ambiente-placeholder">— Primero elija un piso —</p>';
            return;
          }

          const selectedOption = this.options[this.selectedIndex];
          infoPiso.classList.add('activoinfo');
          infoAmbiente.classList.add('activoinfo');
          infoPiso.innerHTML = `<b>Piso seleccionado:</b> ${selectedOption.textContent}`;
          let titulolimpio = selectedOption.textContent.replace(/\s*\([^)]*\)/g, '').trim();
          $("#legend-ambiente").text(titulolimpio);

          fetch(`/page/evento/getambientes?piso_id=${pisoId}&capacidad=${capacidadRequerida}&tipo=silla`)
            .then(function (response) { return response.json(); })
            .then(function (data) {
              if (data.success && data.data.length > 0) {
                selectAmbiente.innerHTML = '<option value="">— Seleccione un ambiente —</option>';
                data.data.forEach(function (ambiente) {
                  if (!ambiente.total_mesas || parseInt(ambiente.total_mesas) === 0) return;
                  const option = document.createElement('option');
                  option.value = ambiente.ambiente_id;
                  option.textContent = ambiente.ambiente_nombre;
                  selectAmbiente.appendChild(option);
                });
                selectAmbiente.disabled = data.count === 0;
                renderAmbienteCards(data.data, i);
                infoAmbiente.textContent = data.count > 0
                  ? `${data.count} ambiente${data.count !== 1 ? 's' : ''} disponible${data.count !== 1 ? 's' : ''}`
                  : 'Sin disponibilidad en este piso';
              } else {
                selectAmbiente.innerHTML = '<option value="" disabled>No hay ambientes disponibles</option>';
                infoAmbiente.textContent = 'No hay ambientes disponibles en este piso';
              }
            })
            .catch(function () {
              selectAmbiente.innerHTML = '<option value="" disabled>Error al cargar ambientes</option>';
              infoAmbiente.textContent = 'Error al cargar ambientes';
            });
        });

        selectAmbiente.addEventListener('change', function () {
          const ambienteId = this.value;

          if (detenerConsultaSillas) { detenerConsultaSillas(); detenerConsultaSillas = null; }

          const grid = document.getElementById('grid_' + i);

          if (slots.some(function (s) { return s !== null; })) {
            limpiarTodasLasSillas();
          }

          if (!ambienteId) {
            infoAmbiente.classList.remove('activoinfo');
            grid.classList.add('d-none');
            grid.innerHTML = '';
            const _ambCards = document.getElementById('ambienteCards_' + i);
            if (_ambCards) _ambCards.querySelectorAll('.ambiente-card').forEach(function (c) {
              c.classList.remove('activo');
              const inp = c.querySelector('input');
              if (inp) inp.checked = false;
            });
            const pisoTexto = $("#legend-ambiente").text().split(" - ")[0] || '';
            $("#legend-ambiente").text(pisoTexto);
            return;
          }

          const selectedOption = this.options[this.selectedIndex];
          infoAmbiente.classList.add('activoinfo');
          infoAmbiente.innerHTML = `<b>Ambiente:</b> ${selectedOption.textContent}`;
          const pisoTexto = $("#legend-ambiente").text().split(" - ")[0] || '';
          $("#legend-ambiente").text(`${pisoTexto} - ${selectedOption.textContent}`);

          fetch(`/page/evento/getmesas?ambiente_id=${ambienteId}&capacidad=${capacidadRequerida}&tipo=silla`)
            .then(function (response) { return response.json(); })
            .then(function (data) {
              if (data.success && data.ambiente) {
                mostrarGridSilla(data.ambiente, data.data || [], data.todos_elementos || []);
                detenerConsultaSillas = (function () {
                  let intervalo = setInterval(function () {
                    fetch(`/page/evento/culstamesasdisponibles?ambiente_id=${ambienteId}&capacidad=${capacidadRequerida}&tipo=silla`)
                      .then(function (res) { return res.json(); })
                      .then(function (d) {
                        if (d.success && Array.isArray(d.mesas)) actualizarEstadoSillasEnGrid(d.mesas);
                      })
                      .catch(function () {});
                  }, 3000);
                  return function () { clearInterval(intervalo); };
                })();
                if (window.innerWidth <= 767) {
                  setTimeout(function () {
                    const gridEl = document.getElementById('scroll-bottom-bar-grid_' + i);
                    if (gridEl) gridEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                  }, 150);
                }
              } else {
                grid.classList.add('d-none');
                grid.innerHTML = '';
                infoMesa.textContent = 'No hay sillas disponibles en este ambiente';
              }
            })
            .catch(function () {
              Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudieron cargar las sillas de este ambiente.' });
            });
        });

        // Restaurar (best-effort) una selección previa si el usuario vuelve a esta pantalla
        // con sillas ya elegidas (el nombre real se completa al cargar el grid del ambiente).
        if (mesaSeleccionadaInput.value) {
          const previas = mesaSeleccionadaInput.value.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
          previas.forEach(function (id, idx) {
            if (idx < totalSillas) slots[idx] = { id: id, nombre: 'Silla ' + id };
          });
          actualizarProgresoSillas();
        }

      } else {
      // ================================================================
      // MODO MESA: una sola mesa por compra (comportamiento original)
      // ================================================================
      function updateDetails (mesaData, index) {
        detalleMesaNombre.textContent = mesaData.mesa_nombre || 'N/A';
        detalleMesaCapacidad.textContent = mesaData.mesa_capacidad || 'N/A';
        detalleMesaCodigo.textContent = mesaData.mesa_codigo || 'N/A';
        detallePiso.textContent = mesaData.piso_nombre || 'N/A';
        detalleAmbiente.textContent = mesaData.ambiente_nombre || 'N/A';
        detalleCategoria.textContent = selectAmbiente.options[selectAmbiente.selectedIndex]?.dataset.categoria || 'N/A';
        detallesMesa.style.display = window.innerWidth <= 767 ? 'block' : 'flex';
        infoMesa.classList.add('activoinfo');
        infoMesa.innerHTML = `<b>Mesa seleccionada:</b> ${mesaData.mesa_nombre || 'N/A'}`;
      }

      function highlightTableInGrid (mesaId, index) {
        document.querySelectorAll(`#grid_${index} .elemento.seleccionada`).forEach(el => {
          el.classList.remove('seleccionada');
        });
        const mesaDiv = document.querySelector(`#grid_${index} .elemento[data-id="${mesaId}"]`);
        if (mesaDiv) mesaDiv.classList.add('seleccionada');
      }

      selectPiso.addEventListener('change', function () {
        const pisoId = this.value;
        selectAmbiente.innerHTML = '<option value="">— Cargando ambientes... </option>';
        selectAmbiente.disabled = true;
        selectMesa.innerHTML = '<option value=""> Primero elija un ambiente —</option>';
        selectMesa.disabled = true;
        btnConfirmarMesa.disabled = true;
        detallesMesa.style.display = 'none';
        const _cards = document.getElementById('ambienteCards_' + i);
        if (_cards) _cards.innerHTML = '<p class="ambiente-placeholder">Cargando ambientes...</p>';

        if (!pisoId) {
          infoPiso.classList.remove('activoinfo');
          infoAmbiente.classList.remove('activoinfo');
          selectAmbiente.innerHTML = '<option value="">— Primero elija un piso —</option>';
          infoPiso.textContent = '';
          $("#legend-ambiente").text('');
          if (_cards) _cards.innerHTML = '<p class="ambiente-placeholder">— Primero elija un piso —</p>';
          return;
        }

        const selectedOption = this.options[this.selectedIndex];
        infoPiso.classList.add('activoinfo');
        infoAmbiente.classList.add('activoinfo');
        infoPiso.innerHTML = `<b>Piso seleccionado:</b> ${selectedOption.textContent}`;

        let titulolimpio = selectedOption.textContent.replace(/\s*\([^)]*\)/g, '').trim();
        $("#legend-ambiente").text(titulolimpio);

        fetch(`/page/evento/getambientes?piso_id=${pisoId}&capacidad=${capacidadRequerida}&tipo=${tipoSeleccion}`)
          .then(response => response.json())
          .then(data => {
            if (data.success && data.data.length > 0) {
              selectAmbiente.innerHTML = '<option value="">— Seleccione un ambiente —</option>';
              // Solo agregar al select los partidos con mesas disponibles
              data.data.forEach(ambiente => {
                if (!ambiente.total_mesas || parseInt(ambiente.total_mesas) === 0) return;
                const option = document.createElement('option');
                option.value = ambiente.ambiente_id;
                option.textContent = ambiente.ambiente_nombre;
                option.dataset.categoria = ambiente.categoria_nombre || '';
                selectAmbiente.appendChild(option);
              });
              selectAmbiente.disabled = data.count === 0;
              renderAmbienteCards(data.data, i);
              if (data.count > 0) {
                infoAmbiente.textContent = `${data.count} ambiente${data.count !== 1 ? 's' : ''} disponible${data.count !== 1 ? 's' : ''}`;
              } else {
                infoAmbiente.textContent = 'Sin disponibilidad en este piso';
              }
            } else {
              selectAmbiente.innerHTML = '<option value="" disabled>No hay ambientes disponibles</option>';
              infoAmbiente.textContent = 'No hay ambientes disponibles en este piso';
            }
          })
          .catch(error => {
            console.error('Error:', error);
            selectAmbiente.innerHTML = '<option value="" disabled>Error al cargar ambientes</option>';
            infoAmbiente.textContent = 'Error al cargar ambientes';
          });
      });

      let detenerConsultaMesas = null;
      selectAmbiente.addEventListener('change', function () {
        const ambienteId = this.value;

        if (detenerConsultaMesas) {
          detenerConsultaMesas();
          detenerConsultaMesas = null;
        }

        selectMesa.innerHTML = '<option value="">— Cargando mesas... —</option>';
        selectMesa.disabled = true;
        btnConfirmarMesa.disabled = true;
        detallesMesa.style.display = 'none';

        if (!ambienteId) {
          infoAmbiente.classList.remove('activoinfo');
          infoMesa.classList.remove('activoinfo');
          selectMesa.innerHTML = '<option value="">— Primero elija un ambiente —</option>';
          selectMesa.value = '';
          infoAmbiente.textContent = '';
          infoMesa.textContent = '';
          const _ambCards = document.getElementById('ambienteCards_' + i);
          if (_ambCards) _ambCards.querySelectorAll('.ambiente-card').forEach(function (c) {
            c.classList.remove('activo');
            const inp = c.querySelector('input');
            if (inp) inp.checked = false;
          });
          const grid = document.getElementById('grid_' + i);
          grid.classList.add('d-none');
          grid.innerHTML = '';
          const pisoTexto = $("#legend-ambiente").text().split(" - ")[0] || '';
          $("#legend-ambiente").text(pisoTexto);
          return;
        }

        const selectedOption = this.options[this.selectedIndex];
        infoAmbiente.classList.add('activoinfo');
        infoMesa.classList.add('activoinfo');
        infoAmbiente.innerHTML = `<b>Ambiente:</b> ${selectedOption.textContent}`;

        const pisoTexto = $("#legend-ambiente").text().split(" - ")[0] || '';
        let textolimpio = selectedOption.textContent.replace(/\s*\([^)]*\)/g, '').trim();
        $("#legend-ambiente").text(`${pisoTexto} - ${selectedOption.textContent}`);

        fetch(`/page/evento/getmesas?ambiente_id=${ambienteId}&capacidad=${capacidadRequerida}&tipo=${tipoSeleccion}`)
          .then(response => response.json())
          .then(data => {
            if (data.success && data.data.length > 0) {
              selectMesa.innerHTML = '<option value="">— Seleccione una mesa —</option>';
              data.data.forEach(mesa => {
                const option = document.createElement('option');
                option.value = mesa.mesa_id;
                option.textContent = `${capitalizeFirstLetter(mesa.mesa_nombre)} (Cap: ${mesa.mesa_capacidad})`;
                option.dataset.mesa = JSON.stringify(mesa);
                selectMesa.appendChild(option);
              });
              selectMesa.disabled = false;
              infoMesa.textContent = `${data.count} mesas disponibles`;

              if (data.ambiente && Number(data.ambiente.ambiente_id) > 0) {
                mostrarGrid(data.ambiente, data.data, data.todos_elementos, i);
                detenerConsultaMesas = iniciarConsultaMesasDisponibles(data.ambiente.ambiente_id, capacidadRequerida, i);
                if (window.innerWidth <= 767) {
                  setTimeout(function () {
                    const gridEl = document.getElementById('scroll-bottom-bar-grid_' + i);
                    if (gridEl) gridEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                  }, 150);
                }
              }
            } else {
              selectMesa.innerHTML = '<option value="" disabled>No hay mesas disponibles</option>';
              infoMesa.textContent = 'No hay mesas disponibles en este ambiente';
            }
          })
          .catch(error => {
            console.error('Error:', error);
            selectMesa.innerHTML = '<option value="" disabled>Error al cargar mesas</option>';
            infoMesa.textContent = 'Error al cargar mesas';
          });
      });

      let origenClick = false;
      selectMesa.addEventListener('change', function () {
        const mesaId = this.value;
        const previousMesaId = mesasSeleccionadasPorPaso[i];

        if (previousMesaId && previousMesaId !== mesaId) {
          fetch('/page/evento/seleccionarmesa', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ mesa_id: previousMesaId, accion: 'deseleccionar', stepIndex: i })
          }).then(response => response.json())
            .then(data => {
              if (data.success) {
                console.log('Mesa anterior deseleccionada:', data.message);
                sincronizarSelecciones(data.reserva_mesa_id);
              } else {
                console.error('Error al deseleccionar mesa anterior:', data.message);
              }
            })
            .catch(error => { console.error('Error en solicitud de deselección:', error); });
        }

        document.querySelectorAll(`#grid_${i} .elemento.seleccionada`).forEach(el => {
          el.classList.remove('seleccionada');
        });

        if (!mesaId) {
          detallesMesa.style.display = 'none';
          delete mesasSeleccionadasPorPaso[i];
          actualizarInputMesasSeleccionadas();
          origenClick = false;
          return;
        }

        const mesaDiv = document.querySelector(`#grid_${i} .elemento[data-id="${mesaId}"]`);
        if (mesaDiv) mesaDiv.classList.add('seleccionada');

        mesasSeleccionadasPorPaso[i] = mesaId;
        actualizarInputMesasSeleccionadas();

        const selectedOption = this.options[this.selectedIndex];
        const mesaData = JSON.parse(selectedOption.dataset.mesa);
        updateDetails(mesaData, i);

        fetch('/page/evento/seleccionarmesa', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ mesa_id: mesaId, accion: 'seleccionar', stepIndex: i })
        }).then(response => response.json())
          .then(data => {
            if (data.success) {
              console.log('Mesa seleccionada correctamente:', data.message);
              sincronizarSelecciones(data.reserva_mesa_id);
            } else {
              console.error('Error al seleccionar mesa:', data.message);
              Swal.fire({ icon: 'error', title: 'Error', text: data.message });
            }
          })
          .catch(error => { console.error('Error en solicitud de selección:', error); });
        origenClick = false;
      });

      document.querySelectorAll('.wizard-step').forEach(step => {
        step.addEventListener('click', function () {
          const stepIndex = this.dataset.step;
          document.querySelectorAll('.wizard-step').forEach(s => s.classList.remove('active'));
          document.querySelectorAll('.wizard-content').forEach(c => c.classList.remove('active'));
          this.classList.add('active');
          document.querySelector(`.wizard-content[data-content="${stepIndex}"]`).classList.add('active');
        });
      });

      let resizeTimeout;
      window.addEventListener('resize', function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function () {
          const grid = document.getElementById('grid_' + i);
          if (!grid.classList.contains('d-none')) {
            const ambienteId = selectAmbiente.value;
            if (ambienteId) {
              fetch(`/page/evento/getmesas?ambiente_id=${ambienteId}&capacidad=${capacidadRequerida}&tipo=${tipoSeleccion}`)
                .then(response => response.json())
                .then(data => {
                  if (data.success && data.ambiente) {
                    mostrarGrid(data.ambiente, data.data, data.todos_elementos, i);
                  }
                })
                .catch(error => { console.error('Error al redimensionar grid:', error); });
            }
          }
        }, 250);
      });

      function obtenerClaseEstadoMesa (mesaEstado, esMia = false, index, mesaObj = null) {
        if (!mesaObj || typeof mesaObj !== 'object' || !mesaObj.mesa_id) return 'libre';
        if (mesaEstado === null || mesaEstado === '' || mesaEstado === '0' || mesaEstado === 0) return 'libre';
        else if (mesaEstado == 1 || mesaEstado === '1') return esMia || mesasSeleccionadasPorPaso[index] === mesaObj.mesa_id ? 'seleccionada' : 'ocupada';
        else if (mesaEstado == 2 || mesaEstado === '2') return 'pendiente';
        else if (mesaEstado == 3 || mesaEstado === '3') return 'vendida';
        return 'libre';
      }

      function obtenerClaseEstadoElemento (elementoEstado, esMia = false, tipoElemento) {
        if (tipoElemento === 'mesa') return 'mesanoseleccionable';
        return 'elementonoseleccionable';
      }

      function iniciarConsultaMesasDisponibles (ambienteId, capacidad, index) {
        let intervalo = setInterval(() => {
          fetch(`/page/evento/culstamesasdisponibles?ambiente_id=${ambienteId}&capacidad=${capacidad}&tipo=${tipoSeleccion}`)
            .then(res => res.json())
            .then(data => {
              if (data.success && Array.isArray(data.mesas)) {
                actualizarEstadoMesasEnGrid(data.mesas, capacidad, index);
              } else {
                console.warn('Respuesta inválida de culstamesasdisponibles:', data);
              }
            })
            .catch(err => { console.error('Error consultando disponibilidad de mesas:', err); });
        }, 3000);
        return () => clearInterval(intervalo);
      }

      function crearElementoMesaEnGrid (mesa, index) {
        if (!mesa || typeof mesa !== 'object' || !mesa.mesa_id) {
          console.warn('Mesa inválida en crearElementoMesaEnGrid:', mesa);
          return null;
        }
        const ancho = parseInt(mesa.mesa_ancho) || 1;
        const alto = parseInt(mesa.mesa_alto) || 1;
        const posX = parseInt(mesa.mesa_posicion_x) || parseInt(mesa.mesa_pos_x) || 0;
        const posY = parseInt(mesa.mesa_posicion_y) || parseInt(mesa.mesa_pos_y) || 0;
        const rotacion = parseInt(mesa.mesa_rotacion) || 0;
        const isMobile = window.innerWidth <= 768;
        const isTablet = window.innerWidth > 768 && window.innerWidth <= 1024;
        // Debe coincidir exactamente con mostrarGrid para que las posiciones sean correctas
        const cellSize = isMobile ? 15 : isTablet ? 35 : 20;
        const gap = isMobile ? 1 : 2;
        const estadoClase = obtenerClaseEstadoMesa(mesa.mesa_estado, mesa.es_mia, index, mesa);

        const elemento = document.createElement('div');
        elemento.className = `elemento ${mesa.mesa_tipo || 'mesa'} ${estadoClase}`;
        if (estadoClase === 'ocupada' || estadoClase === 'pendiente' || estadoClase === 'vendida') {
          elemento.style.pointerEvents = 'none';
        }
        elemento.innerText = mesa.mesa_nombre || mesa.mesa_codigo || `Mesa ${mesa.mesa_id}`;
        elemento.style.position = 'absolute';
        elemento.style.width = (cellSize * ancho + gap * (ancho - 1)) + 'px';
        elemento.style.height = (cellSize * alto + gap * (alto - 1)) + 'px';
        elemento.style.left = (posX * (cellSize + gap)) + 'px';
        elemento.style.top = (posY * (cellSize + gap)) + 'px';
        elemento.style.transform = `rotate(${rotacion}deg)`;
        elemento.style.fontSize = isMobile ? '9px' : isTablet ? '10px' : '12px';
        elemento.dataset.id = mesa.mesa_id;

        elemento.addEventListener('click', function () {
          // No permitir seleccionar una mesa que ya está ocupada/pendiente/vendida
          // (evita conflictos al confirmar), aunque el CSS pointer-events falle o no
          // se haya aplicado todavía.
          if (elemento.classList.contains('ocupada') || elemento.classList.contains('pendiente') || elemento.classList.contains('vendida')) {
            Swal.fire({ icon: 'warning', title: 'Mesa no disponible', text: 'Esta mesa ya fue tomada por otro usuario. Elige otra.' });
            return;
          }
          const option = Array.from(selectMesa.options).find(opt => opt.value === mesa.mesa_id);
          if (option) {
            origenClick = true;
            if (selectMesa.value === mesa.mesa_id) {
              selectMesa.value = '';
              selectMesa.dispatchEvent(new Event('change', { bubbles: true }));
            } else {
              selectMesa.value = mesa.mesa_id;
              selectMesa.dispatchEvent(new Event('change', { bubbles: true }));
            }
          } else {
            Swal.fire({ icon: 'warning', title: 'Mesa no disponible', text: 'Esta mesa no está disponible para selección.' });
          }
        });
        return elemento;
      }

      function actualizarEstadoMesasEnGrid (mesas, capacidadRequerida, index) {
        let mesaSeleccionadaEliminada = false;
        const mesaActualmenteSeleccionada = selectMesa.value;
        const grid = document.getElementById('grid_' + index);

        if (!mesas || !Array.isArray(mesas)) {
          console.warn('Mesas inválidas en actualizarEstadoMesasEnGrid:', mesas);
          return;
        }

        mesas.forEach(mesa => {
          if (!mesa || typeof mesa !== 'object' || !mesa.mesa_id || mesa.mesa_capacidad != capacidadRequerida) {
            console.warn('Mesa inválida o capacidad no coincide:', mesa);
            return;
          }

          let mesaDiv = document.querySelector(`#grid_${index} .elemento[data-id='${mesa.mesa_id}']`);
          if (!mesaDiv && grid && !grid.classList.contains('d-none')) {
            mesaDiv = crearElementoMesaEnGrid(mesa, index);
            if (mesaDiv) grid.appendChild(mesaDiv);
            else return;
          }

          mesaDiv.classList.remove('libre', 'ocupada', 'reservada', 'pendiente', 'vendida', 'seleccionada');
          mesaDiv.style.display = '';
          mesaDiv.style.pointerEvents = '';
          const estadoClase = obtenerClaseEstadoMesa(mesa.mesa_estado, mesa.es_mia, index, mesa);
          let seleccionable = true;

          if (estadoClase === 'libre') seleccionable = true;
          else if (estadoClase === 'seleccionada') seleccionable = true;
          else if (estadoClase === 'ocupada' || estadoClase === 'pendiente') seleccionable = false;
          else if (estadoClase === 'vendida') { mesaDiv.style.display = 'none'; seleccionable = false; }

          mesaDiv.classList.add(estadoClase);
          if (!seleccionable) mesaDiv.style.pointerEvents = 'none';

          const option = Array.from(selectMesa.options).find(opt => opt.value === mesa.mesa_id);
          if (option) {
            if (!seleccionable && !mesa.es_mia && mesa.mesa_id !== mesasSeleccionadasPorPaso[index]) {
              option.remove();
              if (mesaActualmenteSeleccionada === mesa.mesa_id) mesaSeleccionadaEliminada = true;
            } else if (mesa.es_mia || mesa.mesa_id === mesasSeleccionadasPorPaso[index]) {
              if (selectMesa.value !== mesa.mesa_id) selectMesa.value = mesa.mesa_id;
            }
          } else {
            if ((mesa.es_mia || mesa.mesa_id === mesasSeleccionadasPorPaso[index]) && seleccionable) {
              const mesaData = {
                mesa_id: mesa.mesa_id, mesa_numero: mesa.mesa_numero || mesa.mesa_id,
                mesa_capacidad: mesa.mesa_capacidad,
                mesa_nombre: mesa.mesa_nombre || `Mesa ${mesa.mesa_codigo || mesa.mesa_id}`,
                mesa_codigo: mesa.mesa_codigo || mesa.mesa_id, piso_nombre: 'N/A', ambiente_nombre: 'N/A'
              };
              const newOption = document.createElement('option');
              newOption.value = mesa.mesa_id;
              newOption.textContent = `${mesa.mesa_nombre || mesa.mesa_codigo || 'Mesa ' + mesa.mesa_id} (Mi selección)`;
              newOption.dataset.mesa = JSON.stringify(mesaData);
              selectMesa.appendChild(newOption);
              selectMesa.value = mesa.mesa_id;
            } else if (seleccionable && estadoClase === 'libre') {
              const mesaEnGrid = document.querySelector(`#grid_${index} .elemento[data-id='${mesa.mesa_id}']`);
              if (mesaEnGrid && mesa.mesa_capacidad == capacidadRequerida) {
                const mesaData = {
                  mesa_id: mesa.mesa_id, mesa_numero: mesa.mesa_numero || mesa.mesa_id,
                  mesa_capacidad: mesa.mesa_capacidad,
                  mesa_nombre: mesa.mesa_nombre || `Mesa ${mesa.mesa_codigo || mesa.mesa_id}`,
                  mesa_codigo: mesa.mesa_codigo || mesa.mesa_id, piso_nombre: 'N/A', ambiente_nombre: 'N/A'
                };
                const newOption = document.createElement('option');
                newOption.value = mesa.mesa_id;
                newOption.textContent = `${mesa.mesa_nombre || mesa.mesa_codigo || 'Mesa ' + mesa.mesa_id} (Cap: ${mesa.mesa_capacidad})`;
                newOption.dataset.mesa = JSON.stringify(mesaData);
                selectMesa.appendChild(newOption);
              }
            }
          }
        });

        if (mesaSeleccionadaEliminada) {
          selectMesa.value = '';
          selectMesa.dispatchEvent(new Event('change', { bubbles: true }));
        }

        const mesasDisponibles = Array.from(selectMesa.options).filter(opt => opt.value !== '').length;
        if (infoMesa) infoMesa.textContent = `${mesasDisponibles} mesas disponibles`;
      }

      function mostrarGrid (ambiente, mesas, todosElementos = [], index) {
        const grid = document.getElementById('grid_' + index);
        const legend = document.getElementById('gridLegend_' + index);
        grid.classList.remove('d-none');
        grid.innerHTML = '';

        const isMobile = window.innerWidth <= 768;
        const isTablet = window.innerWidth > 768 && window.innerWidth <= 1024;
        const cellSize = isMobile ? 15 : isTablet ? 35 : 20;
        console.log(cellSize);
        const gap = isMobile ? 1 : 2;

        for (let j = 0; j < Number(ambiente.ambiente_filas) * Number(ambiente.ambiente_columnas); j++) {
          const cell = document.createElement('div');
          cell.className = 'grid-cell';
          cell.style.width = cellSize + 'px';
          cell.style.height = cellSize + 'px';
          grid.appendChild(cell);
        }

        grid.style.gridTemplateColumns = `repeat(${Number(ambiente.ambiente_columnas)}, ${cellSize}px)`;
        grid.style.gridTemplateRows = `repeat(${Number(ambiente.ambiente_filas)}, ${cellSize}px)`;
        grid.style.gap = gap + 'px';

        mesas.forEach(mesa => {
          if (!mesa || typeof mesa !== 'object' || !mesa.mesa_id) {
            console.warn('Mesa inválida en mostrarGrid:', mesa);
            return;
          }
          const ancho = parseInt(mesa.mesa_ancho) || 1;
          const alto = parseInt(mesa.mesa_alto) || 1;
          const posX = parseInt(mesa.mesa_pos_x) || 0;
          const posY = parseInt(mesa.mesa_pos_y) || 0;
          const rotacion = parseInt(mesa.mesa_rotacion) || 0;
          const estadoClase = obtenerClaseEstadoMesa(mesa.mesa_estado, mesa.es_mia, index, mesa);

          const elemento = document.createElement('div');
          elemento.className = `elemento ${mesa.mesa_tipo} ${estadoClase}`;
          if (estadoClase === 'ocupada' || estadoClase === 'pendiente' || estadoClase === 'vendida') {
            elemento.style.pointerEvents = 'none';
          }
          elemento.innerText = mesa.mesa_nombre || mesa.mesa_tipo;
          elemento.style.position = 'absolute';
          elemento.style.width = (cellSize * ancho + gap * (ancho - 1)) + 'px';
          elemento.style.height = (cellSize * alto + gap * (alto - 1)) + 'px';
          elemento.style.left = (posX * (cellSize + gap)) + 'px';
          elemento.style.top = (posY * (cellSize + gap)) + 'px';
          elemento.style.transform = `rotate(${rotacion}deg)`;
          elemento.style.fontSize = isMobile ? '9px' : isTablet ? '10px' : '12px';
          elemento.dataset.id = mesa.mesa_id;

          elemento.addEventListener('click', function () {
            // No permitir seleccionar una mesa que ya está ocupada/pendiente/vendida
            // (evita conflictos al confirmar), aunque el CSS pointer-events falle o no
            // se haya aplicado todavía.
            if (elemento.classList.contains('ocupada') || elemento.classList.contains('pendiente') || elemento.classList.contains('vendida')) {
              Swal.fire({ icon: 'warning', title: 'Mesa no disponible', text: 'Esta mesa ya fue tomada por otro usuario. Elige otra.' });
              return;
            }
            const option = Array.from(selectMesa.options).find(opt => opt.value === mesa.mesa_id);
            if (option) {
              origenClick = true;
              if (selectMesa.value === mesa.mesa_id) {
                selectMesa.value = '';
                selectMesa.dispatchEvent(new Event('change', { bubbles: true }));
              } else {
                selectMesa.value = mesa.mesa_id;
                selectMesa.dispatchEvent(new Event('change', { bubbles: true }));
              }
            } else {
              Swal.fire({ icon: 'warning', title: 'Mesa no disponible', text: 'Esta mesa no está disponible para selección.' });
            }
          });
          grid.appendChild(elemento);
        });

        todosElementos.forEach(el => {
          if (!el || typeof el !== 'object' || !el.mesa_id) {
            console.warn('Elemento inválido en mostrarGrid:', el);
            return;
          }
          const ancho = parseInt(el.mesa_ancho) || 1;
          const alto = parseInt(el.mesa_alto) || 1;
          const posX = parseInt(el.mesa_pos_x) || 0;
          const posY = parseInt(el.mesa_pos_y) || 0;
          const rotacion = parseInt(el.mesa_rotacion) || 0;
          const estadoClase = obtenerClaseEstadoElemento(el.mesa_estado, el.es_mia, el.mesa_tipo);

          const elemento = document.createElement('div');
          elemento.className = `elemento ${el.mesa_tipo} ${estadoClase}`;
          elemento.innerText = el.mesa_nombre || el.mesa_tipo;
          elemento.style.position = 'absolute';
          elemento.style.width = (cellSize * ancho + gap * (ancho - 1)) + 'px';
          elemento.style.height = (cellSize * alto + gap * (alto - 1)) + 'px';
          elemento.style.left = (posX * (cellSize + gap)) + 'px';
          elemento.style.top = (posY * (cellSize + gap)) + 'px';
          elemento.style.transform = `rotate(${rotacion}deg)`;
          elemento.style.fontSize = isMobile ? '9px' : isTablet ? '10px' : '12px';
          elemento.dataset.id = el.mesa_id;
          grid.appendChild(elemento);
        });

        sincronizarScrollBars(index);
      }

      function sincronizarScrollBars (index) {
        const grid = document.getElementById('grid_' + index);
        const scrollTopBar = document.getElementById('scroll-top-bar-grid_' + index);
        const scrollBottomBar = document.getElementById('scroll-bottom-bar-grid_' + index);

        if (grid && scrollTopBar && scrollBottomBar) {
          scrollTopBar.innerHTML = '';
          const innerDiv = document.createElement('div');
          innerDiv.style.width = grid.offsetWidth + 'px';
          innerDiv.style.height = '1px';
          scrollTopBar.appendChild(innerDiv);

          scrollTopBar.addEventListener('scroll', function () { scrollBottomBar.scrollLeft = scrollTopBar.scrollLeft; });
          scrollBottomBar.addEventListener('scroll', function () { scrollTopBar.scrollLeft = scrollBottomBar.scrollLeft; });

          const resizeObserver = new ResizeObserver(function () { innerDiv.style.width = grid.offsetWidth + 'px'; });
          resizeObserver.observe(grid);
        }
      }

      function capitalizeFirstLetter (text) {
        if (typeof text !== 'string') return 'N/A';
        text = text.toLowerCase();
        return text.charAt(0).toUpperCase() + text.slice(1);
      }
      } // fin else (modo mesa)
    }

    for (let i = 0; i < totalSteps; i++) {
      const selectPiso = document.getElementById('selectPiso_' + i);
      if (selectPiso && selectPiso.value) {
        selectPiso.dispatchEvent(new Event('change', { bubbles: true }));
      }
    }
  });

  document.getElementById("formSeleccionMesa").addEventListener("submit", function (e) {
    const btn = document.getElementById("btnConfirmarMesa");
    const loading = document.getElementById("loadingSnippet");
    btn.classList.add("d-none");
    loading.classList.remove("d-none");
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    validarSesion();
    setInterval(validarSesion, 1000);
  })
</script>

<style>
  /* ============================================================
     RESERVAR MESA — Paso 3 — Dark glass (cascada seleccionarbeneficiarios)
  ============================================================ */

  /* Modo sillas: progreso + chips de seleccionadas */
  .rm-sillas-progress-inline {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
    color: #eee;
    margin: 6px 0 10px;
  }
  .rm-sillas-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
  }
  .rm-silla-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    border-radius: 999px;
    padding: 4px 10px;
    font-size: 0.82rem;
  }
  .rm-silla-chip i {
    cursor: pointer;
    opacity: .7;
  }
  .rm-silla-chip i:hover {
    opacity: 1;
    color: #ff6b6b;
  }

  .contenedor-general {
    height: calc(100vh - 60px);
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

  body { font-size: 14px; }

  /* ── Viewport layout — card fijo, scroll interior ── */
  .rm-page {
    max-width: 100%;
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding-top: 0.5rem;
    padding-bottom: 0;
  }

  /* ── Topbar row (igual que rv-topbar en seleccionarbeneficiarios) ── */
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
    gap: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    padding: 6px 16px 6px 12px;
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

  /* ─ Step badge (scoped a rv-topbar) ── */
  .rv-topbar .step-badge {
    display: inline-flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.07);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 20px;
    padding: 6px 16px 6px 12px;
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

  /* ── Timer pill (igual que rv-timer en seleccionarbeneficiarios) ── */
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
  @keyframes rv-blink { 50% { opacity: 0.1; } }

  /* ── Persons tag ── */
  .rm-persons-tag {
    display: inline-flex;
    align-items: center;
    background: rgba(255, 193, 7, 0.9);
    color: #111;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
  }

  /* ── Main card — dark glass + scroll interior ── */
  .rm-main-card {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
    background: rgba(6, 6, 6, 0.6);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.45);
  }

  /* ── Two-column layout ── */
  .rm-layout {
    display: grid;
    grid-template-columns: 350px 1fr;
    gap: 14px;
    align-items: start;
  }
  @media (max-width: 991px) { .rm-layout { grid-template-columns: 1fr; } }

  /* ── LEFT PANEL ── */
  .rm-selector-panel {
    background: rgba(6, 6, 6, 0.55);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    overflow: hidden;
  }
  .rm-panel-header {
    background: rgba(255, 193, 7, 0.08);
    border-bottom: 1px solid rgba(255, 193, 7, 0.25);
    padding: 10px 14px;
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  .rm-panel-label {
    color: #ffc107;
    font-weight: 700;
    font-size: 0.78rem;
    letter-spacing: 1px;
    text-transform: uppercase;
  }
  .rm-panel-hint { color: rgba(255, 255, 255, 0.35); font-size: 0.7rem; }

  /* Steps form */
  .rm-steps-form { padding: 14px 14px 4px; display: flex; flex-direction: column; }
  .rm-step-item {
    display: flex;
    gap: 10px;
    align-items: flex-start;
    position: relative;
    padding-bottom: 16px;
  }
  .rm-step-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 12px; top: 28px; bottom: 0;
    width: 1px;
    background: linear-gradient(to bottom, rgba(255, 193, 7, 0.5), rgba(255, 255, 255, 0.04));
  }
  .rm-step-num {
    flex-shrink: 0;
    width: 24px; height: 24px;
    border-radius: 50%;
    background: rgba(255, 193, 7, 0.15);
    border: 1.5px solid rgba(255, 193, 7, 0.6);
    color: #ffc107;
    font-size: 0.7rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    margin-top: 2px; position: relative; z-index: 1;
  }
  .rm-step-body { flex: 1; min-width: 0; }
  .rm-step-label-text {
    display: block;
    color: white;
    font-size: 1rem; font-weight: 600;
    letter-spacing: 0.3px; margin-bottom: 5px;
  }

  /* Selects dark */
  .rm-select {
    background-color: rgba(255, 255, 255, 0.06) !important;
    border: 1.5px solid rgba(255, 193, 7, 0.4) !important;
    color: #ffffff !important;
    border-radius: 5px !important;
    font-size: 0.8rem !important;
    padding: 7px 10px !important;
    transition: border-color 0.2s ease;
  }
  .rm-select:focus {
    border-color: rgba(255, 193, 7, 0.8) !important;
    background-color: rgba(255, 255, 255, 0.09) !important;
    box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.15) !important;
    outline: none; color: #ffffff !important;
  }
  .rm-select:disabled {
    background-color: rgba(255, 255, 255, 0.03) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    color: rgba(255, 255, 255, 0.2) !important;
    cursor: not-allowed;
  }
  .rm-select option { background: #1a1a1a; color: #fff; }

  /* Info ubication dark */
  .rm-selector-panel .content-info-ubication span {
    background: rgba(255, 193, 7, 0.08);
    border-left: 2px solid rgba(255, 193, 7, 0.55);
    color: rgba(255, 255, 255, 0.6);
    font-size: 1rem; padding: 0;
    border-radius: 0 3px 3px 0;
  }
  .rm-selector-panel .content-info-ubication .activoinfo { padding: 5px 8px !important; }

  /* Mesa detail dark */
  .rm-mesa-detail {
    margin: 0 14px 14px;
    border-radius: 6px; overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  .rm-mesa-detail-header {
    background: rgba(255, 193, 7, 0.12);
    color: #ffc107;
    font-size: 0.72rem; font-weight: 700;
    letter-spacing: 0.6px; text-transform: uppercase;
    padding: 7px 12px; display: flex; align-items: center;
  }
  .rm-mesa-detail-body {
    background: rgba(255, 255, 255, 0.04);
    padding: 10px 12px; display: flex; flex-direction: column; gap: 5px;
  }
  .rm-detail-row { display: flex; align-items: baseline; gap: 6px; font-size: 0.77rem; }
  .rm-detail-key {
    color: rgba(255, 255, 255, 0.35); font-weight: 600;
    min-width: 64px; font-size: 0.68rem;
    text-transform: uppercase; letter-spacing: 0.5px; flex-shrink: 0;
  }
  .rm-detail-val { color: #ffffff; font-weight: 500; }
  .rm-detail-unit { color: rgba(255, 255, 255, 0.35); font-size: 0.7rem; }
  .rm-detail-divider { height: 1px; background: rgba(255, 255, 255, 0.08); margin: 3px 0; }

  /* ── RIGHT PANEL ── */
  .rm-grid-panel { display: flex; flex-direction: column; gap: 8px; min-width: 0; }

  /* Legend dark */
  .sticky-legend {
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    background: rgba(6, 6, 6, 0.55);
    padding: 10px 14px;
    border-radius: 6px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: none;
  }
  .sticky-legend .legend-title h4 {
    color: #fff !important;
    font-size: 0.95rem; font-weight: 600;
    text-shadow: none; letter-spacing: -0.2px;
  }
  .sticky-legend .legend-title hr {
    border-color: rgba(255, 255, 255, 0.1) !important;
    opacity: 1; margin-top: 6px !important;
  }
  .grid-legend {
    display: flex; flex-wrap: wrap;
    gap: 5px 12px; margin-top: 8px;
    background: transparent; padding: 0; border-radius: 0;
  }
  .legend-item { display: flex; align-items: center; gap: 5px; }
  .legend-color { width: 12px; height: 12px; border-radius: 2px; flex-shrink: 0; }
  .sticky-legend .legend-item span {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.7rem; font-weight: 500; text-shadow: none;
  }

  /* Scroll bars */
  .scroll-top-bar {
    overflow-x: auto; overflow-y: hidden;
    width: 100%; height: 14px; margin-bottom: 2px;
    background: transparent; border-radius: 4px;
  }
  .scroll-top-bar::-webkit-scrollbar,
  .grid-container::-webkit-scrollbar { width: 4px; height: 4px; }
  .scroll-top-bar::-webkit-scrollbar-track,
  .grid-container::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.04); border-radius: 4px; }
  .scroll-top-bar::-webkit-scrollbar-thumb,
  .grid-container::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.18); border-radius: 4px; }
  .scroll-top-bar::-webkit-scrollbar-thumb:hover,
  .grid-container::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.3); }

  .grid-container { overflow-x: auto; }
  .grid {
    position: relative; display: grid;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(255, 255, 255, 0.04);
    width: max-content; margin: 0 auto; border-radius: 4px;
  }
  .grid-cell { border: 1px solid rgba(255, 255, 255, 0.04); }

  /* ── MESA ELEMENTS — colores funcionales intactos ── */
  .elemento {
    position: absolute; border-radius: 4px; font-weight: 700; color: #fff;
    display: flex; align-items: center; justify-content: center;
    box-sizing: border-box; cursor: pointer; z-index: 2;
    transform-origin: center center; transition: all 0.2s ease;
    word-wrap: break-word; text-align: center; line-height: 1.2;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
  }
  @media (max-width: 767px) { .elemento { touch-action: manipulation; } }
  .mesa { background: #4CAF50; }
  .barra { background: #795548; }
  .puerta { background: #8B4513; }
  .pista { background: #9C27B0; }
  .tarima { background: #3F51B5; }
  .bano { background: #FF5722; }
  .escalera { background: #E91E63; }
  .ascensor { background: #00BCD4; }
  .pantalla { background: #FF9800; }
  .terraza { background: #ec94a2; }
  .ventana { background: blue; }
  .libre { background-color: #4CAF50; cursor: pointer; }
  .mesanoseleccionable { background-color: #bbb; cursor: not-allowed; }
  .elementonoseleccionable { cursor: not-allowed; }
  .elementonoseleccionable.mesa { background-color: #bbb; }
  .ocupada { background-color: #F44336; z-index: 5; cursor: not-allowed; }
  .reservada { background-color: #FF9800; z-index: 5; }
  .pendiente { background-color: #FFC107; z-index: 5; cursor: not-allowed; }
  .seleccionada {
    background-color: #ffe066 !important;
    box-shadow: 0 0 0 3px rgba(255, 214, 0, 0.5), 0 2px 8px rgba(0, 0, 0, 0.6);
    z-index: 10; color: #1a1a00; text-shadow: none; transform: scale(1.1);
  }
  .celda-hover { background: #b3e5fc !important; border: 2px solid #0288d1 !important; z-index: 1; }

  /* ── FOOTER FIJO ── */
  .rm-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 0.65rem 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(6, 6, 6, 0.75);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    flex-shrink: 0;
    flex-wrap: wrap;
  }

  .rm-footer-detail {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex: 1;
    min-width: 0;
    flex-wrap: wrap;
    background: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.22);
    border-radius: 8px;
    padding: 0.45rem 0.8rem;
    color: black;
    border-radius: 20px;
    min-height: 48px;
  }

  .rm-fd-icon { color: black; font-size: 0.9rem; flex-shrink: 0; }

  .rm-fd-items {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    flex-wrap: wrap;
  }

  .rm-fd-item { display: flex; align-items: baseline; gap: 3px; }

  .rm-fd-key {
    color: black;
    font-size: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.4px;
  }

  .rm-fd-val { color: black; font-size: 1rem; font-weight: 600; }

  .rm-fd-unit { color: black; font-size: 0.8rem; }

  .rm-fd-sep { color: black; font-size: 0.8rem; }

  .rm-footer-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
  }

  .rm-footer-loading {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    color: rgba(255, 255, 255, 0.45);
    font-size: 0.8rem;
  }

  .rm-confirm-btn {
    background: #ffc107 !important;
    border: none !important; color: #111 !important;
    font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;
    font-size: 0.82rem; padding: 13px 36px;
    border-radius: 20px; min-height: 48px;
    transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
    box-shadow: 0 3px 12px rgba(255, 193, 7, 0.3);
  }
  .rm-confirm-btn:hover:not(:disabled) {
    background: #e6ac00 !important;
    transform: translateY(-1px);
    box-shadow: 0 5px 18px rgba(255, 193, 7, 0.45);
    color: #111 !important;
  }
  .rm-confirm-btn:disabled {
    background: rgba(255, 255, 255, 0.07) !important;
    color: rgba(255, 255, 255, 0.2) !important;
    cursor: not-allowed; box-shadow: none; transform: none;
  }
  .rm-loading-text { color: rgba(255, 255, 255, 0.4); font-size: 0.8rem; letter-spacing: 0.5px; }

  /* ── WIZARD TABS dark ── */
  .wizard-box {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 7px; padding: 8px 10px;
  }
  .wizard-header { display: flex; justify-content: center; gap: 8px; flex-wrap: wrap; }
  .wizard-step {
    display: flex; align-items: center; padding: 5px 14px;
    background-color: rgba(255, 255, 255, 0.06);
    border-radius: 30px; cursor: pointer;
    transition: background-color 0.2s ease;
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.1);
  }
  .wizard-step .circle {
    background-color: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.6);
    border-radius: 50%; width: 24px; height: 24px;
    font-weight: bold; display: flex; align-items: center;
    justify-content: center; margin-right: 8px; font-size: 12px;
  }
  .wizard-step .step-label { font-weight: 500; font-size: 0.78rem; white-space: nowrap; }
  .wizard-step.active { background-color: #ffc107; border-color: #ffc107; color: #111; }
  .wizard-step.active .circle { background-color: rgba(0, 0, 0, 0.12); color: #111; }
  .wizard-step:hover:not(.active) { background-color: rgba(255, 255, 255, 0.1); color: #fff; }
  .wizard-content { display: none; }
  .wizard-content.active { display: block; }

  /* ── PARTIDO CARDS dark ── */
  .rm-select-readonly {
    pointer-events: none;
    background-color: rgba(255, 255, 255, 0.03) !important;
    color: rgba(255, 255, 255, 0.2) !important;
    cursor: default !important;
  }
  .ambiente-cards-container { display: flex; flex-direction: column; gap: 7px; margin-top: 4px; }
  .ambiente-placeholder { color: rgba(255, 255, 255, 0.25); font-size: 0.72rem; margin: 0; padding: 5px 0; }
  .ambiente-card {
    display: block;
    border: 1.5px solid rgba(255, 255, 255, 0.1);
    border-radius: 7px; padding: 10px 12px;
    cursor: pointer; background: rgba(255, 255, 255, 0.05);
    user-select: none;
    transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
    margin: 0;
  }
  .ambiente-card:hover { border-color: rgba(255, 193, 7, 0.65); background: rgba(255, 193, 7, 0.06); }
  .ambiente-card.activo {
    border-color: #ffc107;
    background: rgba(255, 193, 7, 0.07);
    box-shadow: 0 0 0 2.5px rgba(255, 193, 7, 0.22);
  }
  .ambiente-card-body { display: flex; flex-direction: column; gap: 5px; }
  .ambiente-card-top { display: flex; align-items: center; justify-content: space-between; gap: 8px; }
  .ambiente-num {
    font-size: 0.68rem; font-weight: 700; color: #ffc107;
    text-transform: uppercase; letter-spacing: 0.6px;
    background: rgba(255, 193, 7, 0.15); padding: 2px 7px; border-radius: 20px;
  }
  .ambiente-fecha { font-size: 0.88rem; font-weight: 600; color: #fff; }
  .ambiente-countdown { font-size: 0.69rem; color: rgba(255, 255, 255, 0.4); font-variant-numeric: tabular-nums; letter-spacing: 0.2px; }
  .ambiente-card.activo .ambiente-num { background: rgba(255, 193, 7, 0.35); }
  .ambiente-card.activo .ambiente-countdown { color: #ffc107; }
  .ambiente-card-disabled { cursor: not-allowed; opacity: 0.4; background: rgba(255, 255, 255, 0.02); pointer-events: none; }
  .ambiente-sin-disp { font-size: 0.69rem; font-weight: 600; color: #ef9a9a; letter-spacing: 0.2px; }

  /* ── RESPONSIVE ── */
  @media (max-width: 767px) {
    /* CRÍTICO: romper la cadena fixed-height/overflow para scroll natural en móvil */
    .contenedor-general { height: auto !important; overflow: visible !important; }
    .card-principal { overflow: visible !important; flex: none !important; padding: 12px 10px !important; margin-bottom: 30px !important; }
    .rm-page { overflow: visible !important; flex: none !important; }
    .rm-main-card { overflow: visible !important; flex: none !important; padding: 12px 10px; }

    body { overflow-x: hidden; }
    .container { max-width: 99%; }

    /* Topbar: sin saltos de línea en el lado izquierdo */
    .rv-topbar > div:first-child { flex-wrap: nowrap !important; }
    .rv-topbar .step-badge { font-size: 0.7rem; padding: 5px 9px; white-space: nowrap; }
    .rv-topbar .step-name { display: none; }
    .rv-topbar .step-divider { display: none; }
    .sb-back-btn { font-size: 0.7rem; padding: 5px 10px 5px 8px; }
    .rm-persons-tag { font-size: 0.68rem; padding: 3px 8px; white-space: nowrap; }

    /* Timer: sin label para ahorrar ancho */
    .rv-timer { padding: 5px 10px; gap: 0.3rem; }
    .rv-timer-label { display: none; }
    .rv-timer-display { font-size: 0.85rem; letter-spacing: 1px; }

    /* CORRECCIÓN sticky: el panel y la leyenda son ellos mismos sticky-top,
       hay que apuntarlos directamente con !important para vencer a Bootstrap */
    .rm-selector-panel { position: relative !important; top: auto !important; }
    .sticky-legend { position: relative !important; top: auto !important; }
    .legend-item { justify-content: start; }

    /* Layout y fuentes del formulario selector */
    .rm-layout { gap: 12px; }
    .rm-step-label-text { font-size: 0.82rem; }
    .rm-select { font-size: 0.75rem !important; padding: 6px 8px !important; }
    .rm-selector-panel .content-info-ubication span { font-size: 0.72rem; }

    /* Grid: scroll horizontal, altura limitada */
    .grid-container { max-height: 55vh; overflow-x: auto; }

    /* Footer: columna, info fluye como texto puro — corta donde sea necesario */
    .rm-footer { flex-direction: column; align-items: stretch; gap: 0.4rem; padding: 0.5rem 0.75rem; }
    .rm-footer-detail { display: block; min-height: auto; padding: 0.4rem 0.75rem; line-height: 0.9; }
    .rm-fd-icon { display: inline !important; font-size: 0.65rem; margin-right: 3px; }
    .rm-fd-items { display: inline; }
    .rm-fd-item { display: inline; }
    .rm-fd-key { display: inline; font-size: 0.62rem; }
    .rm-fd-val { display: inline; font-size: 0.65rem; word-break: break-all; }
    .rm-fd-sep { display: inline; font-size: 0.6rem; margin: 0 2px; }
    .rm-fd-unit { display: inline; font-size: 0.62rem; }
    .rm-footer-actions { width: 100%; justify-content: stretch; }
    .rm-confirm-btn { width: 100%; box-sizing: border-box; font-size: 0.78rem; padding: 9px 16px; min-height: 40px; }
  }

  @media (min-width: 768px) and (max-width: 1024px) {
    .rm-layout { grid-template-columns: 250px 1fr; }
    .grid-container { max-height: 60vh; overflow-x: auto; }
  }
  @media (max-width: 767px) and (orientation: landscape) { .grid-container { max-height: 50vh; } }
  @media (hover: none) and (pointer: coarse) { button, .btn, select, .form-select { min-height: 44px; } }
  @media (prefers-reduced-motion: reduce) {
    .elemento, .rm-confirm-btn { transition: none; }
    .seleccionada { transform: none; }
  }
</style>