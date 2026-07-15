<script>
  document.addEventListener('DOMContentLoaded', function () {
    try {
      if (typeof fechaFin !== 'undefined' && fechaFin instanceof Date) {
        const ahora = new Date();
        const diferencia = fechaFin - ahora;
        if (diferencia > 0) {
          const minutos = Math.floor(diferencia / 60000);
          const segundos = Math.floor((diferencia % 60000) / 1000);
          const mm = String(minutos).padStart(2, '0');
          const ss = String(segundos).padStart(2, '0');
          if (window.Swal) {
            Swal.fire({
              icon: 'info',
              title: 'Tiempo de reserva',
              html: `Te quedan <b>${mm}:${ss}</b> para terminar tu reserva.`,
              confirmButtonText: 'Entendido'
            });
          } else {
            alert(`Te quedan ${mm}:${ss} para terminar tu reserva.`);
          }
        }
      }
    } catch (e) { }
  });
</script>

<div class="rv-wrap container py-2 py-lg-3">
  <form action="/page/evento/seleccionarbeneficiarios" method="post" id="formCantidadPersonas" class="mb-3 mb-lg-5 card-principal">
    <!-- Campos ocultos necesarios -->
    <input type="hidden" name="reserva_id_evento" value="<?php echo $this->evento->evento_id ?>">
    <input type="hidden" name="evento_titulo" value="<?php echo $this->evento->evento_titulo ?>">
    <input type="hidden" name="evento_imagen_evento" value="<?php echo $this->evento->evento_imagen_evento ?>">
    <input type="hidden" name="evento_fecha_inicio" value="<?php echo $this->evento->evento_fecha_inicio ?>">
    <input type="hidden" name="evento_fecha_fin" value="<?php echo $this->evento->evento_fecha_fin ?>">

    <!-- ── Topbar: step + timer ── -->
    <div class="rv-topbar mb-3">

      <!-- Step badge (izquierda) -->
      <div class="step-indicator">
        <div class="step-badge">
          <i class="fa-solid fa-ticket me-2"></i>
          <span class="step-text">Paso 1 de 5</span>
          <span class="step-divider">•</span>
          <span class="step-name">Selección de boletas</span>
        </div>
      </div>

      <!-- Timer (derecha) -->
      <?php if ($this->sesionInfo && $this->sesionInfo->accion_sesion_sesion_activa == 1): ?>
        <div id="tiempo-restante-reserva" class="rv-timer">
          <i class="fa-solid fa-clock rv-timer-icon"></i>
          <div class="rv-timer-display">
            <span id="minutos">00</span><span class="rv-timer-colon">:</span><span id="segundos">00</span>
          </div>
          <span class="rv-timer-label">tiempo restante</span>
          <input type="hidden" id="minutosHidden" name="minutosHidden">
        </div>
      <?php endif; ?>

    </div>

    <!-- ── Contenido principal ── -->
    <div class="col-12">
      <div class="row rv-content-row">

        <!-- Panel lateral: reservas exitosas -->
        <?php if (is_countable($this->reservasExitosas) && count($this->reservasExitosas) > 0): ?>
          <div class="col-lg-4 order-2 order-lg-1 mb-3 mb-lg-0 caja-reservas-resumen">
            <div class="rv-panel">
              <div class="rv-panel-header">
                <span class="rv-panel-label">Tus reservas</span>
                <span class="rv-panel-count"><?= count($this->reservasExitosas) ?></span>
              </div>
              <div class="rv-panel-body">
                <?php foreach ($this->reservasExitosas as $reservaexitosa): ?>
                  <?php if ($reservaexitosa->estado == 4): ?>
                    <!-- Reserva con pago pendiente por confirmación: solo informativa, sin opciones -->
                    <div class="rv-reserva-row rv-reserva-row-info">
                      <div class="rv-reserva-title">
                        <span class="rv-reserva-id">#<?= $reservaexitosa->id ?> - <?= htmlspecialchars($reservaexitosa->nombre_ambiente) ?></span><br>
                        <span class="rv-reserva-personas"><?= $reservaexitosa->total_personas ?> persona<?= $reservaexitosa->total_personas != 1 ? 's' : '' ?></span>
                      </div>
                      <div class="rv-reserva-info-msg">
                        <i class="fa-solid fa-hourglass-half rv-reserva-info-icon"></i>
                        <span>Pendiente por confirmación de pago</span>
                      </div>
                    </div>
                  <?php else: ?>
                    <div class="rv-reserva-row">
                      <div class="rv-reserva-title">
                        <span class="rv-reserva-id">#<?= $reservaexitosa->id ?> - <?= htmlspecialchars($reservaexitosa->nombre_ambiente) ?></span><br>
                        <span class="rv-reserva-personas"><?= $reservaexitosa->total_personas ?> persona<?= $reservaexitosa->total_personas != 1 ? 's' : '' ?></span>
                      </div>
                      <div class="rv-reserva-status">
                        <span class="rv-reserva-badge rv-badge-<?= in_array($reservaexitosa->estado, [2, 3, 11]) ? 'ok' : 'pending' ?>">
                          <?= htmlspecialchars($reservaexitosa->estado_texto) ?>
                        </span>
                        <span class="rv-reserva-alerts">
                          <?php
                          $alertas = [];
                          if ($reservaexitosa->faltan_invitados)
                            $alertas[] = '<span class="rv-tag rv-tag-err"><i class="fas fa-exclamation-triangle"></i> Faltan datos</span>';
                          if (!$reservaexitosa->factura_completa)
                            $alertas[] = '<span class="rv-tag rv-tag-err"><i class="fas fa-file-invoice"></i> Falta factura</span>';
                          if ($reservaexitosa->qrs_generados > 0)
                            $alertas[] = '<span class="rv-tag rv-tag-ok"><i class="fas fa-check"></i> QR ' . $reservaexitosa->qrs_generados . '/' . $reservaexitosa->boletas_esperadas . '</span>';
                          elseif ($reservaexitosa->qr_anteriores > 0)
                            $alertas[] = '<span class="rv-tag rv-tag-warn"><i class="fas fa-exclamation-triangle"></i> QR en proceso</span>';
                          else
                            $alertas[] = '<span class="rv-tag rv-tag-err"><i class="fas fa-times"></i> Sin QR</span>';
                          echo implode('', $alertas);
                          ?>
                        </span>
                        <?php if ($reservaexitosa->cupos_pendiente_id): ?>
                          <span class="rv-tag rv-tag-warn">
                            <i class="fas fa-user-plus"></i> Cupos adicionales (+<?= $reservaexitosa->cupos_pendiente_cantidad ?>)
                          </span>
                        <?php endif; ?>
                      </div>
                      <div class="rv-reserva-actions">
                        <?php if ($reservaexitosa->cupos_pendiente_id): ?>
                          <a href="/page/evento/seleccionarbeneficiarios?cupos_id=<?= enc_id($reservaexitosa->cupos_pendiente_id) ?>" class="rv-action-btn rv-action-primary">
                            <i class="fas fa-credit-card"></i> Pagar cupos
                          </a>
                        <?php elseif ($reservaexitosa->id == 896): ?>
                          <button class="rv-action-btn rv-action-disabled" disabled>
                            <i class="fas fa-clock"></i> Procesando
                          </button>
                        <?php elseif ($reservaexitosa->puede_ver): ?>
                          <a href="/page/servicios/info?id=<?= enc_id($reservaexitosa->id) ?>" class="rv-action-btn rv-action-ok">
                            <i class="fas fa-eye"></i> Ver boletas
                          </a>
                        <?php elseif ($reservaexitosa->puede_gestionar): ?>
                          <a href="/page/guests?id=<?= enc_id($reservaexitosa->id) ?>" class="rv-action-btn rv-action-primary">
                            <i class="fas fa-edit"></i> Completar
                          </a>
                        <?php else: ?>
                          <button class="rv-action-btn rv-action-disabled" disabled>
                            <i class="fas fa-clock"></i> Procesando
                          </button>
                        <?php endif; ?>
                      </div>
                    </div>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Panel principal: selector de cantidad -->
        <div
          class="col-lg-<?= (is_countable($this->reservasExitosas) && count($this->reservasExitosas) > 0) ? '8' : '12' ?> order-1 order-lg-2">
          <div class="rv-main-card">

            <!-- Cupo info -->
            <?php
            $usadas = ($this->totalAceptadas + $this->totalPendientes);
            $restante = max(0, $this->UMBRAL_MAX_PERSONAS - $usadas);
            if ($usadas > 0):
              ?>
              <div class="rv-info-bar">
                <div class="rv-info-item">
                  <span class="rv-info-label">Aceptadas</span>
                  <span class="rv-info-val"><?= $this->totalAceptadas ?></span>
                </div>
                <div class="rv-info-sep"></div>
                <div class="rv-info-item">
                  <span class="rv-info-label">Pendientes</span>
                  <span class="rv-info-val"><?= $this->totalPendientes ?></span>
                </div>
                <div class="rv-info-sep"></div>
                <div class="rv-info-item">
                  <span class="rv-info-label">Total boletas</span>
                  <span class="rv-info-val rv-info-total"><?= $usadas ?></span>
                </div>
              </div>
            <?php endif; ?>

            <?php if ($restante === 0): ?>
              <div class="rv-alert rv-alert-warn">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Ya alcanzaste el cupo máximo permitido. No puedes agregar más personas.
              </div>
            <?php endif; ?>
            <?php if ($restante === 0 && $this->totalPendientes > 0): ?>
              <div class="rv-alert rv-alert-info">
                <i class="fas fa-clock me-2"></i>
                Estamos procesando su transacción. Por favor, espere unos minutos mientras confirmamos el pago y
                finalizamos su reserva.
              </div>
            <?php endif; ?>

            <!-- Título -->
            <div class="rv-section-title text-center">
              <h2>¿Cuántas boletas deseas adquirir?</h2>
              <p id="rvSubtitle"><?php echo ($modoInicial === 'silla') ? 'Indica cuántas sillas deseas comprar' : 'Selecciona el tamaño de mesa que necesitas'; ?></p>
            </div>

            <?php
            $mesasPorCapacidad = [];
            foreach (($this->mesasDisponibles ?? []) as $item) {
              $mesasPorCapacidad[(int) $item->mesa_capacidad] = (int) $item->cantidad_mesas;
            }
            $capacidadesDisponibles = array_keys($mesasPorCapacidad);
            sort($capacidadesDisponibles);

            $restante = max(0, $this->capacidadRestante ?? 0);
            $limiteEvento = (int) $this->maxInvitados;
            $limiteReal = min($restante, $limiteEvento);

            // Hay al menos una capacidad de mesa disponible dentro del cupo restante
            $hayMesas = false;
            foreach ($capacidadesDisponibles as $capacidad) {
              if ($capacidad <= $limiteReal) {
                $hayMesas = true;
                break;
              }
            }

            $haySillas = ((int) ($this->sillasDisponibles ?? 0)) > 0;
            // Si solo hay uno de los dos modos disponible, se arranca directo en ese modo
            // y no se muestra el selector (no aplica elegir entre una sola opción).
            $modoInicial = (!$hayMesas && $haySillas) ? 'silla' : 'mesa';
            ?>

            <?php if ($hayMesas && $haySillas): ?>
              <!-- Selector de modo: mesa completa vs sillas individuales -->
              <div class="rv-mode-toggle text-center">
                <button type="button" class="btn-modo-seleccion active" data-modo="mesa">
                  <i class="fa-solid fa-table-list"></i> Mesa completa
                </button>
                <button type="button" class="btn-modo-seleccion" data-modo="silla">
                  <i class="fa-solid fa-chair"></i> Sillas individuales
                </button>
              </div>
            <?php endif; ?>

            <!-- Panel: mesa completa -->
            <div id="panelMesas" <?php echo ($modoInicial === 'silla') ? 'style="display:none;"' : ''; ?>>
            <!-- Cards de capacidad -->
            <div id="cardsContainer" class="rv-tiles-grid">
              <?php
              $hayCards = false;

              foreach ($capacidadesDisponibles as $capacidad) {
                $hayCards = true;
                $cantidadMesas = $mesasPorCapacidad[$capacidad];
                // Agotado si no quedan mesas físicas de este tamaño O si excede el cupo
                // restante del evento/sesión. En ambos casos la tarjeta se muestra igual,
                // nunca se omite (antes desaparecía cuando $capacidad > $limiteReal).
                $excedeCupo = $capacidad > $limiteReal;
                $soldOut = $cantidadMesas <= 0 || $excedeCupo;
                $claseSoldOut = $soldOut ? ' rv-tile-soldout' : '';
                $cursor = $soldOut ? 'not-allowed' : 'pointer';
                $etiquetaMesas = $soldOut
                  ? "<span class='rv-tile-status rv-tile-badge rv-badge-soldout'><i class='fa-solid fa-ban'></i> Agotado</span>"
                  : "<span class='rv-tile-status rv-tile-badge rv-badge-mesas'><i class='fa-solid fa-chair'></i> $cantidadMesas mesa" . ($cantidadMesas != 1 ? 's' : '') . " disponible" . ($cantidadMesas != 1 ? 's' : '') . "</span>";

                // Semáforo de disponibilidad: rojo agotado, amarillo pocas mesas (1-2), verde buena disponibilidad (3+)
                if ($soldOut) {
                  $semaforoColor = 'red';
                  $semaforoTitulo = 'Agotado';
                } elseif ($cantidadMesas <= 2) {
                  $semaforoColor = 'yellow';
                  $semaforoTitulo = 'Pocas mesas disponibles';
                } else {
                  $semaforoColor = 'green';
                  $semaforoTitulo = 'Buena disponibilidad';
                }
                $semaforo = "<span class='rv-semaforo rv-semaforo-$semaforoColor' data-bs-toggle='tooltip' title='$semaforoTitulo'></span>";

                echo "
                  <div class='card card-capacidad rv-tile$claseSoldOut'
                      data-capacidad='$capacidad'
                      data-cantidad-mesas='$cantidadMesas'
                      data-soldout='" . ($soldOut ? '1' : '0') . "'
                      style='cursor: $cursor;'>
                    $semaforo
                    <div class='card-body rv-tile-body'>
                      <span class='rv-tile-num'>$capacidad</span>
                      <span class='rv-tile-label'>persona" . ($capacidad != 1 ? 's' : '') . "</span>
                      $etiquetaMesas
                    </div>
                  </div>
                ";
              }
              if (!$hayCards) {
                echo "<div class='rv-no-avail'>
                  <i class='fas fa-chair'></i>
                  <span>No hay mesas disponibles para reservar en este momento.</span>
                </div>";
              }
              ?>
            </div>
            </div><!-- /panelMesas -->

            <?php if ($haySillas): ?>
              <?php $maxSillas = min((int) $this->sillasDisponibles, (int) $limiteReal); ?>
              <!-- Panel: sillas individuales -->
              <div id="panelSillas" style="<?php echo ($modoInicial === 'silla') ? 'display:block;' : 'display:none;'; ?>">
                <div class="rv-sillas-box text-center">
                  <p class="rv-sillas-avail">
                    <i class="fa-solid fa-chair"></i>
                    <strong id="sillasDispLabel"><?php echo (int) $this->sillasDisponibles; ?></strong> sillas disponibles
                  </p>
                  <label for="cantidadSillas" class="rv-sillas-label">¿Cuántas sillas deseas?</label>
                  <input type="number" id="cantidadSillas" class="form-control rv-sillas-input"
                    min="1" max="<?php echo max(1, $maxSillas); ?>" value="1"
                    <?php echo $maxSillas < 1 ? 'disabled' : ''; ?>>
                  <div class="form-text rv-sillas-hint text-white">
                    Todas las sillas de la compra deben pertenecer al mismo ambiente. Máximo <?php echo max(0, $maxSillas); ?>.
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <input type="hidden" name="cantidad_personas" id="cantidadPersonas" required>
            <input type="hidden" name="mesasSeleccionadas" id="mesasSeleccionadasHidden">
            <input type="hidden" name="tipo_seleccion" id="tipoSeleccionHidden" value="<?php echo $modoInicial; ?>">

            <div class="form-text text-center mt-2 d-none">
              Cupo disponible: <strong><?php echo $restante; ?></strong> |
              Máximo: <strong><?php echo $this->maxInvitados; ?></strong>
            </div>

            <!-- Submit -->
            <div class="rv-submit-row">
              <button type="submit" class="btn-evento event-btn rv-submit-btn" id="btnSubmit">
                Siguiente &nbsp;<i class="fa-solid fa-angle-right"></i>
              </button>
              <div id="loadingSnippet" class="d-none rv-loading">
                <div class="spinner-border" role="status"
                  style="width:1.6rem;height:1.6rem;border-color:#111;border-right-color:transparent;">
                  <span class="visually-hidden">Cargando...</span>
                </div>
                <span>Verificando disponibilidad…</span>
              </div>
            </div>

          </div><!-- /rv-main-card -->
        </div>

      </div><!-- /row -->
    </div>

  </form>
</div>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    <?php if ($this->sin_disponibilidad): ?>
      Swal.fire({
        icon: 'warning',
        title: 'Sin disponibilidad',
        text: 'No hay mesas disponibles para la cantidad de personas seleccionada.',
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#3085d6',
      });
    <?php endif; ?>

    const haySillas = <?= (((int) ($this->sillasDisponibles ?? 0)) > 0) ? 'true' : 'false' ?>;
    const cards = document.querySelectorAll('.card-capacidad[data-soldout="0"]');
    if (cards.length === 0 && !haySillas) {
      document.getElementById('btnSubmit').disabled = true;
      Swal.fire({
        icon: 'warning',
        title: 'Sin disponibilidad',
        text: 'No hay mesas ni sillas disponibles.',
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#3085d6',
      });
    }
    const inputHidden = document.getElementById('cantidadPersonas');
    const tipoHidden = document.getElementById('tipoSeleccionHidden');
    const maxEvento = <?= $this->maxInvitados ?>;
    const totalAceptadas = <?= (int) $this->totalAceptadas ?>;
    const totalPendientes = <?= (int) $this->totalPendientes ?>;
    const maxGlobal = <?= (int) $this->UMBRAL_MAX_PERSONAS ?>;
    const restanteGlobal = Math.max(0, maxGlobal - (totalAceptadas + totalPendientes));
    const limiteReal = Math.min(restanteGlobal, maxEvento);
    const maxInvitados = limiteReal;
    // Con sillas se puede comprar 1 sola; solo se bloquea sin cupo alguno.
    if (maxInvitados < 1) {
      document.getElementById('btnSubmit').disabled = true;
    }

    let seleccionadas = [];
    let modoSeleccion = tipoHidden.value || 'mesa';

    function setCookie (nombre, valor, dias) {
      const d = new Date();
      d.setTime(d.getTime() + (dias * 24 * 60 * 60 * 1000));
      document.cookie = nombre + "=" + JSON.stringify(valor) + ";expires=" + d.toUTCString() + ";path=/";
    }
    function deleteCookie (nombre) {
      document.cookie = nombre + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }

    cards.forEach(card => {
      card.addEventListener('click', function () {
        if (this.classList.contains('rv-tile-soldout')) return;

        const capacidad = parseInt(this.dataset.capacidad, 10);

        if (this.classList.contains('selected')) {
          this.classList.remove('selected');
          this.style.border = '1px solid #e0e0e0';
          seleccionadas = [];
        } else {
          if (seleccionadas.length >= 1) {
            cards.forEach(c => {
              c.classList.remove('selected');
              c.style.border = '1px solid #e0e0e0';
            });
            seleccionadas = [];
          }
          this.classList.add('selected');
          this.style.border = '2px solid #111';
          seleccionadas.push(capacidad);
        }

        inputHidden.value = seleccionadas.reduce((acc, val) => acc + val, 0);
        setCookie("mesas_seleccionadas", seleccionadas, 1);
      });
    });

    // ---- Modo Sillas individuales ----
    const btnsModo = document.querySelectorAll('.btn-modo-seleccion');
    const panelMesas = document.getElementById('panelMesas');
    const panelSillas = document.getElementById('panelSillas');
    const inputSillas = document.getElementById('cantidadSillas');
    const subtitulo = document.getElementById('rvSubtitle');

    function limpiarSeleccionMesas() {
      cards.forEach(c => {
        c.classList.remove('selected');
        c.style.border = '1px solid #e0e0e0';
      });
    }

    function actualizarSillas() {
      if (!inputSillas) return;
      let n = parseInt(inputSillas.value, 10) || 0;
      const maxN = parseInt(inputSillas.getAttribute('max'), 10) || 0;
      if (n > maxN) { n = maxN; inputSillas.value = maxN; }
      if (n < 1) { n = 0; }
      seleccionadas = [];
      for (let i = 0; i < n; i++) seleccionadas.push(1);
      inputHidden.value = n; // total_personas = número de sillas
      setCookie("mesas_seleccionadas", seleccionadas, 1);
    }
    if (inputSillas) inputSillas.addEventListener('input', actualizarSillas);

    btnsModo.forEach(b => b.addEventListener('click', function () {
      btnsModo.forEach(x => x.classList.remove('active'));
      this.classList.add('active');
      modoSeleccion = this.dataset.modo;
      tipoHidden.value = modoSeleccion;
      setCookie("tipo_seleccion_mesa", modoSeleccion, 1);
      // Reiniciar selección al cambiar de modo
      seleccionadas = [];
      inputHidden.value = 0;
      limpiarSeleccionMesas();

      if (modoSeleccion === 'silla') {
        if (panelMesas) panelMesas.style.display = 'none';
        if (panelSillas) panelSillas.style.display = 'block';
        if (subtitulo) subtitulo.textContent = 'Indica cuántas sillas deseas comprar';
        actualizarSillas();
      } else {
        if (panelMesas) panelMesas.style.display = 'block';
        if (panelSillas) panelSillas.style.display = 'none';
        if (subtitulo) subtitulo.textContent = 'Selecciona el tamaño de mesa que necesitas';
      }
    }));

    // El modo inicial (mesa o silla) ya viene resuelto desde el servidor según
    // disponibilidad (ver $modoInicial en la vista); solo falta poblar el estado JS.
    if (modoSeleccion === 'silla') {
      actualizarSillas();
    }

    document.getElementById("formCantidadPersonas").addEventListener("submit", function (e) {
      e.preventDefault();

      const btn = document.getElementById("btnSubmit");
      const loading = document.getElementById("loadingSnippet");
      const cantidad = parseInt(inputHidden.value, 10) || 0;
      const form = this;

      if (cantidad === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Seleccione una opción',
          text: modoSeleccion === 'silla'
            ? 'Debe indicar al menos una silla antes de continuar.'
            : 'Debe elegir al menos una mesa antes de continuar.'
        });
        return;
      }

      btn.classList.add("d-none");
      loading.classList.remove("d-none");
      document.getElementById('mesasSeleccionadasHidden').value = JSON.stringify(seleccionadas);

      // Modo sillas: la cantidad ya está acotada por el máximo disponible.
      // La disponibilidad real por ambiente se valida en el servidor (paso 2 y 3).
      if (modoSeleccion === 'silla') {
        form.submit();
        return;
      }

      fetch("/page/evento/disponiblesmesas", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "cantidadPersonas=" + encodeURIComponent(cantidad) +
          "&mesasSeleccionadas=" + encodeURIComponent(JSON.stringify(seleccionadas))
      })
        .then(async response => {
          const text = await response.text();
          try {
            const data = JSON.parse(text);
            const todosDisponibles = Array.isArray(data.pisosDisponibles) && data.pisosDisponibles.length > 0 &&
              data.pisosDisponibles.every(item => Array.isArray(item.pisos) && item.pisos.length > 0);

            if (todosDisponibles) {
              form.submit();
            } else {
              Swal.fire({
                icon: 'warning',
                title: 'Sin disponibilidad',
                text: 'No hay mesas disponibles para esa cantidad de personas.'
              });
              btn.classList.remove("d-none");
              loading.classList.add("d-none");
            }
          } catch (err) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Respuesta inesperada del servidor.' });
            btn.classList.remove("d-none");
            loading.classList.add("d-none");
          }
        })
        .catch(error => {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Hubo un problema al verificar la disponibilidad.' });
          btn.classList.remove("d-none");
          loading.classList.add("d-none");
        });
    });

    // Capacidades expiradas por partido (se evalúa una vez al cargar; cambia máximo 1 vez al día)
    let _capacidadesExpiradas = [];
    function verificarCapacidadesExpiradas () {
      fetch('/page/evento/obtenerCapacidadesExpiradas', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      })
        .then(r => r.json())
        .then(data => {
          if (data && Array.isArray(data.capacidadesExpiradas)) {
            _capacidadesExpiradas = data.capacidadesExpiradas;
          }
          actualizarCardsCapacidadBloqueadas();
        })
        .catch(() => actualizarCardsCapacidadBloqueadas());
    }

    function actualizarCardsCapacidadBloqueadas () {
      fetch('/page/evento/obtenermesasbloqueadas', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      })
        .then(response => response.json())
        .then(data => {
          if (!data) return;
          const capacidadesBloqueadas = data
            .filter(b => b.mesa_bloqueo_estado == 1 && b.mesa_bloqueo_capacidad)
            .map(b => parseInt(b.mesa_bloqueo_capacidad, 10));

          const noDisponibles = [...new Set([...capacidadesBloqueadas, ..._capacidadesExpiradas])];

          document.querySelectorAll('.card-capacidad').forEach(card => {
            const capacidad = parseInt(card.dataset.capacidad, 10);
            const statusEl = card.querySelector('.rv-tile-status');
            const eraSoldOutOriginal = card.dataset.soldout === '1';
            const cantidadOriginal = parseInt(card.dataset.cantidadMesas, 10) || 0;
            const bloqueada = eraSoldOutOriginal || noDisponibles.includes(capacidad);

            const semaforoEl = card.querySelector('.rv-semaforo');

            if (bloqueada) {
              card.classList.add('rv-tile-soldout');
              card.style.cursor = 'not-allowed';
              if (statusEl) {
                statusEl.innerHTML = '<i class="fa-solid fa-ban"></i> Agotado';
                statusEl.classList.remove('rv-badge-mesas');
                statusEl.classList.add('rv-badge-soldout');
              }
              if (semaforoEl) {
                semaforoEl.className = 'rv-semaforo rv-semaforo-red';
                semaforoEl.title = 'Agotado';
              }
              if (card.classList.contains('selected')) {
                card.classList.remove('selected');
                card.style.border = '1px solid #e0e0e0';
                seleccionadas = seleccionadas.filter(c => c !== capacidad);
                inputHidden.value = seleccionadas.reduce((acc, val) => acc + val, 0);
                setCookie("mesas_seleccionadas", seleccionadas, 1);
              }
            } else {
              card.classList.remove('rv-tile-soldout');
              card.style.cursor = 'pointer';
              if (statusEl) {
                statusEl.innerHTML = '<i class="fa-solid fa-chair"></i> ' + cantidadOriginal + ' mesa' + (cantidadOriginal !== 1 ? 's' : '') + ' disponible' + (cantidadOriginal !== 1 ? 's' : '');
                statusEl.classList.remove('rv-badge-soldout');
                statusEl.classList.add('rv-badge-mesas');
              }
              if (semaforoEl) {
                if (cantidadOriginal <= 2) {
                  semaforoEl.className = 'rv-semaforo rv-semaforo-yellow';
                  semaforoEl.title = 'Pocas mesas disponibles';
                } else {
                  semaforoEl.className = 'rv-semaforo rv-semaforo-green';
                  semaforoEl.title = 'Buena disponibilidad';
                }
              }
            }
          });
        });
    }
    verificarCapacidadesExpiradas();      // carga inicial: expiraciones + bloqueadas
    setInterval(actualizarCardsCapacidadBloqueadas, 1000); // polling ligero: solo bloqueadas

    validarSesion();
    setInterval(validarSesion, 1000);
  });
</script>

<style>
  /* ============================================================
   RESERVAR — Step 1  ·  Dark glass theme
  ============================================================ */

  /* Toggle de modo: mesa completa vs sillas individuales */
  .rv-mode-toggle {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin: 0 0 20px;
    flex-wrap: wrap;
  }
  .btn-modo-seleccion {
    background: rgba(255, 255, 255, 0.06);
    color: #eee;
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 999px;
    padding: 9px 20px;
    font-size: 0.92rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .18s ease;
  }
  .btn-modo-seleccion:hover {
    border-color: #fff;
  }
  .btn-modo-seleccion.active {
    background: #fff;
    color: #111;
    border-color: #fff;
  }
  .rv-sillas-box {
    padding: 24px 16px;
  }
  .rv-sillas-avail {
    font-size: 1rem;
    margin-bottom: 14px;
    color: #eee;
  }
  .rv-sillas-label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: #eee;
  }
  .rv-sillas-input {
    max-width: 160px;
    margin: 0 auto;
    text-align: center;
    font-size: 1.3rem;
    font-weight: 700;
  }
  .rv-sillas-hint {
    margin-top: 10px;
    opacity: .8;
  }

  /* Fill the viewport between fixed header (60px) and fixed footer (30px) */
  .contenedor-general {
    min-height: calc(100vh - 60px - 30px);
    display: flex;
    flex-direction: column;
    padding-bottom: 0 !important;
  }

  .rv-wrap {
    max-width: 100%;
    flex: 1;
    display: flex;
    flex-direction: column;
  }

  .card-principal {
    background-color: rgba(21, 25, 29, 1);
    padding: 15px 15px;
    border-radius: 6px;
    flex: 1;
  }

  /* ── Main card ── */
  .rv-main-card {
    background: rgba(6, 6, 6, 0.65);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    padding: 2.5rem;
    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.5);
  }

  @media (max-width: 576px) {
    .rv-main-card {
      padding: 1.5rem 1.25rem;
    }
  }

  /* ── Cupo info bar ── */
  .rv-info-bar {
    display: flex;
    align-items: center;
    gap: 0;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 4px;
    margin-bottom: 1.75rem;
    overflow: hidden;
  }

  .rv-info-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1rem 0.5rem;
    gap: 4px;
  }

  .rv-info-sep {
    width: 1px;
    height: 2.5rem;
    background: rgba(255, 255, 255, 0.1);
    flex-shrink: 0;
  }

  .rv-info-label {
    font-size: 0.58rem;
    text-transform: uppercase;
    letter-spacing: 1.8px;
    color: rgba(255, 255, 255, 0.45);
    font-weight: 600;
  }

  .rv-info-val {
    font-size: 1.6rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1;
  }

  .rv-info-total {
    color: #ffc107;
  }

  /* ── Alerts ── */
  .rv-alert {
    display: flex;
    align-items: flex-start;
    gap: 0.6rem;
    padding: 0.85rem 1.1rem;
    font-size: 0.83rem;
    margin-bottom: 1.25rem;
    border-radius: 4px;
    border-left: 3px solid;
    backdrop-filter: blur(10px);
  }

  .rv-alert-warn {
    background: rgba(255, 193, 7, 0.1);
    border-left-color: #ffc107;
    color: #ffe082;
  }

  .rv-alert-info {
    background: rgba(74, 144, 226, 0.1);
    border-left-color: #4a90e2;
    color: #90caf9;
  }

  .rv-alert-danger {
    background: rgba(220, 53, 69, 0.12);
    border-left-color: #dc3545;
    color: #ef9a9a;
  }

  /* ── Section title ── */
  .rv-section-title {
    margin-bottom: 2rem;
  }

  .rv-section-title h2 {
    font-size: 2rem;
    font-weight: 500;
    color: #ffffff;
    margin-bottom: 0.35rem;
    letter-spacing: 1.5px;
  }

  .rv-section-title p {
    font-size: 0.68rem;
    color: white;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin: 0;
  }

  /* ── Capacity tiles ── */
  .rv-tiles-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    justify-content: center;
    margin-bottom: 2.25rem;
  }

  .rv-tile.card-capacidad {
    position: relative !important;
    width: 110px !important;
    height: 110px !important;
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-radius: 6px !important;
    box-shadow: none !important;
    transition: transform 0.22s ease, background 0.22s ease, box-shadow 0.22s ease !important;
    margin: 0;
    cursor: pointer;
  }

  .rv-tile.card-capacidad:hover {
    background: rgba(255, 193, 7, 0.1) !important;
    border-color: rgba(255, 193, 7, 0.45) !important;
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(255, 193, 7, 0.1) !important;
  }

  .rv-tile.card-capacidad.selected {
    background: rgba(255, 193, 7, 0.16) !important;
    border: 2px solid #ffc107 !important;
    transform: translateY(-3px);
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.18), 0 10px 28px rgba(255, 193, 7, 0.18) !important;
  }

  .rv-tile-body {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    height: 100% !important;
    padding: 0 !important;
    gap: 5px;
  }

  .rv-tile-num {
    font-size: 2.4rem;
    font-weight: 700;
    line-height: 1;
    color: #ffffff;
    transition: color 0.22s ease;
  }

  .rv-tile.card-capacidad.selected .rv-tile-num {
    color: #ffc107;
  }

  .rv-tile-label {
    font-size: 0.76rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: white;
    font-weight: 600;
    transition: color 0.22s ease;
  }

  .rv-tile.card-capacidad.selected .rv-tile-label {
    color: rgba(255, 193, 7, 0.75);
  }

  .rv-tile-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    padding: 3px 10px;
    border-radius: 20px;
    line-height: 1.5;
    white-space: nowrap;
  }

  .rv-tile-badge i {
    font-size: 0.62rem;
  }

  .rv-badge-mesas {
    background: rgba(76, 175, 80, 0.12);
    color: #66bb6a;
    border: 1px solid rgba(76, 175, 80, 0.32);
    /* El texto "x mesas disponibles" es largo: permitir 2 líneas dentro de la tarjeta */
    display: inline-block;
    white-space: normal;
    max-width: 100px;
    padding: 3px 8px;
    line-height: 1.3;
    text-align: center;
    vertical-align: middle;
  }

  .rv-badge-mesas i {
    margin-right: 3px;
  }

  .rv-badge-soldout {
    background: rgba(220, 53, 69, 0.16);
    color: #ef9a9a;
    border: 1px solid rgba(220, 53, 69, 0.4);
  }

  .rv-tile.card-capacidad.rv-tile-soldout {
    opacity: 0.55;
    pointer-events: none;
    background: rgba(255, 255, 255, 0.03) !important;
  }

  /* ── Semáforo de disponibilidad (esquina superior derecha de cada tarjeta) ── */
  .rv-semaforo {
    position: absolute;
    top: 8px;
    right: 8px;
    width: 11px;
    height: 11px;
    border-radius: 50%;
    z-index: 2;
    border: 1.5px solid rgba(0, 0, 0, 0.25);
  }

  .rv-semaforo-green {
    background: #2ecc71;
    box-shadow: 0 0 7px rgba(46, 204, 113, 0.85);
    animation: rv-semaforo-pulse-green 2s ease-in-out infinite;
  }

  .rv-semaforo-yellow {
    background: #ffc107;
    box-shadow: 0 0 7px rgba(255, 193, 7, 0.85);
    animation: rv-semaforo-pulse-yellow 1.4s ease-in-out infinite;
  }

  .rv-semaforo-red {
    background: #dc3545;
    box-shadow: 0 0 7px rgba(220, 53, 69, 0.85);
  }

  @keyframes rv-semaforo-pulse-green {
    0%, 100% { box-shadow: 0 0 7px rgba(46, 204, 113, 0.85); }
    50% { box-shadow: 0 0 3px rgba(46, 204, 113, 0.4); }
  }

  @keyframes rv-semaforo-pulse-yellow {
    0%, 100% { box-shadow: 0 0 7px rgba(255, 193, 7, 0.85); }
    50% { box-shadow: 0 0 3px rgba(255, 193, 7, 0.4); }
  }

  .rv-tile.card-capacidad.rv-tile-soldout .rv-tile-num,
  .rv-tile.card-capacidad.rv-tile-soldout .rv-tile-label {
    color: rgba(255, 255, 255, 0.4);
  }

  .linea_users_selected {
    display: none !important;
  }

  /* ── No availability ── */
  .rv-no-avail {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.9rem;
    padding: 3rem 1rem;
    color: rgba(255, 255, 255, 0.35);
    font-size: 0.88rem;
    text-align: center;
    width: 100%;
  }

  .rv-no-avail i {
    font-size: 2.8rem;
    color: rgba(255, 255, 255, 0.15);
  }

  /* ── Submit area ── */
  .rv-submit-row {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.85rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
  }

  .rv-submit-btn.event-btn {
    min-width: 220px;
    background: linear-gradient(135deg, #ffc107, #ffb300);
    border-color: #ffc107;
    color: #111;
    border-radius: 4px;
  }

  .rv-submit-btn.event-btn:hover {
    background: linear-gradient(135deg, #ffd740, #ffc107);
    border-color: #ffd740;
    color: #000;
  }

  .rv-loading {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.82rem;
    color: rgba(255, 255, 255, 0.55);
  }

  /* ── Side panel ── */
  .caja-reservas-resumen {
    max-height: 70vh;
    overflow-y: auto;
  }

  .rv-panel {
    background: rgba(6, 6, 6, 0.6);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    height: 100%;
    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.45);
  }

  .rv-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  }

  .rv-panel-label {
    font-size: 1rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 700;
    color: white;
  }

  .rv-panel-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    background: #ffc107;
    color: #111;
    border-radius: 50%;
    font-size: 0.7rem;
    font-weight: 700;
  }

  .rv-panel-body {
    padding: 0;
  }

  .rv-reserva-row {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    display: flex;
    flex-direction: column;
    gap: 0.45rem;
  }

  .rv-reserva-row:last-child {
    border-bottom: none;
  }

  .rv-reserva-title {
    align-items: baseline;
    gap: 0.5rem;
  }

  .rv-reserva-id {
    font-size: 1rem;
    font-weight: 700;
    color: white;
  }

  .rv-reserva-status {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.4rem;
  }

  .rv-reserva-badge {
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    padding: 2px 8px;
    border-radius: 20px;
  }

  .rv-badge-ok {
    background: rgba(29, 106, 63, 0.3);
    color: #81e6a8;
    border: 1px solid rgba(129, 230, 168, 0.25);
  }

  .rv-badge-pending {
    background: rgba(255, 193, 7, 0.14);
    color: #ffc107;
    border: 1px solid rgba(255, 193, 7, 0.28);
  }

  .rv-reserva-personas {
    color: white;
    font-size: 0.9rem;
  }

  .rv-reserva-alerts {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
  }

  .rv-tag {
    font-size: 0.73rem;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
  }

  .rv-tag-ok {
    background: rgba(29, 106, 63, 0.28);
    color: #81e6a8;
  }

  .rv-tag-warn {
    background: rgba(255, 193, 7, 0.14);
    color: #ffc107;
  }

  .rv-tag-err {
    background: rgba(220, 53, 69, 0.18);
    color: #ef9a9a;
  }

  .rv-reserva-actions {
    margin-top: 0.3rem;
  }

  /* ── Reserva informativa (pendiente por confirmación, sin opciones) ── */
  .rv-reserva-row-info {
    opacity: 0.85;
  }

  .rv-reserva-info-msg {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #ffc107;
  }

  .rv-reserva-info-icon {
    font-size: 0.78rem;
    color: #ffc107;
    flex-shrink: 0;
  }

  .rv-action-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.4px;
    padding: 5px 12px;
    border: 1px solid;
    border-radius: 3px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.18s ease;
  }

  .rv-action-ok {
    background: rgba(29, 106, 63, 0.18);
    border-color: rgba(129, 230, 168, 0.3);
    color: #81e6a8;
  }

  .rv-action-ok:hover {
    background: rgba(29, 106, 63, 0.35);
    color: #a7f3c0;
  }

  .rv-action-primary {
    background: rgba(255, 193, 7, 0.14);
    border-color: rgba(255, 193, 7, 0.45);
    color: #ffc107;
  }

  .rv-action-primary:hover {
    background: rgba(255, 193, 7, 0.28);
    color: #ffd54f;
  }

  .rv-action-disabled {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.2);
    cursor: not-allowed;
  }

  /* ── Content row ── */
  .rv-content-row {
    --bs-gutter-x: 1.25rem;
  }

  /* ── Step badge override: glass pill igual que el timer ── */
  .rv-topbar .step-badge {
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

  .rv-topbar .step-badge i {
    color: #ffc107;
    font-size: 0.78rem;
  }

  .rv-topbar .step-text {
    font-weight: 700;
    letter-spacing: 0.3px;
  }

  .rv-topbar .step-divider {
    opacity: 0.3;
    margin: 0 6px;
    font-size: 0.9rem;
  }

  .rv-topbar .step-name {
    color: white;
    font-weight: 500;
    font-size: 0.9rem;
  }

  /* ── Topbar row ── */
  .rv-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
  }

  /* ── Inline timer pill ── */
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

  .rv-timer-icon {
    font-size: 0.78rem;
    color: #111;
    flex-shrink: 0;
  }

  .rv-timer-display {
    font-size: 1.05rem;
    font-weight: 700;
    color: #111;
    letter-spacing: 2px;
    line-height: 1;
  }

  .rv-timer-colon {
    animation: rv-blink 1s step-end infinite;
    opacity: 0.7;
  }

  @keyframes rv-blink {
    50% { opacity: 0.1; }
  }

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
  .rv-timer.urgent .rv-timer-label {
    color: #ffffff;
  }
</style>