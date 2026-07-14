<div class="sb-wrap container py-2 py-lg-3 mb-3 mb-lg-5">
  <?php echo $this->puestos; ?>
  <form action="/page/evento/registrarreserva" method="post" id="formSeleccionBeneficiarios" class="mb-3 mb-lg-5 card-principal">
    <!-- Campos ocultos necesarios -->
    <input type="hidden" name="reserva_id_evento" value="<?php echo $this->evento->evento_id ?>">
    <input type="hidden" name="reserva_numero_carnet" value="<?php echo Session::getInstance()->get('ncar') ?>">
    <input type="hidden" name="reserva_nombre_cliente" value="<?php echo $this->socio->sbe_nomb ?>">
    <input type="hidden" name="reserva_apellido_cliente" value="<?php echo $this->socio->sbe_apel ?>">
    <input type="hidden" name="reserva_telefono" value="<?php echo $this->socio->sbe_ncel ?>">
    <input type="hidden" name="reserva_correo" value="<?php echo $this->socio->sbe_mail ?>">
    <input type="hidden" name="reserva_documento" value="<?php echo $this->socio->SBE_CODI ?>">

    <!-- ── Encabezado: regresar · step | timer ── -->
    <div class="rv-topbar mb-3">

      <!-- Izquierda: regresar + step -->
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <a href="/page/evento/reservar" class="sb-back-btn">
          <i class="fa-solid fa-angle-left"></i>&nbsp; Regresar
        </a>
        <div class="step-indicator">
          <div class="step-badge">
            <i class="fas fa-users me-2"></i>
            <span class="step-text">Paso 2 de 5</span>
            <span class="step-divider">•</span>
            <span class="step-name">Selección de Beneficiarios (Opcional)</span>
          </div>
        </div>
      </div>

      <!-- Derecha: timer -->
      <?php if ($this->sesionInfo && $this->sesionInfo->accion_sesion_sesion_activa == 1) { ?>
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

    <!-- ── Cuerpo ─ -->
    <div class="col-12 sb-body">

      <!-- ── Resumen de reserva + contador ─ -->
      <div class="sb-summary-bar mb-3">
        <div class="sb-summary-item">
          <span class="sb-summary-label">Cantidad seleccionada</span>
          <span class="sb-summary-val"><?php echo $this->cantidadSeleccionada ?>
            persona<?= $this->cantidadSeleccionada != 1 ? 's' : '' ?></span>
        </div>
        <div class="sb-summary-sep"></div>
        <div class="sb-summary-item">
          <span class="sb-summary-label">Beneficiarios asignados</span>
          <span class="sb-summary-val" id="contadorBeneficiarios">0</span>
        </div>
        <div class="sb-summary-sep"></div>
        <div class="sb-summary-item">
          <span class="sb-summary-label">Invitados restantes</span>
          <span class="sb-summary-val sb-counter-restantes"
            id="contadorRestantes"><?php echo $this->cantidadSeleccionada ?></span>
        </div>
      </div>

      <!-- ── Beneficiarios ─ -->
      <div class="sb-info-note mb-1">
        <span class="sb-beneficiario-title">
          <i class="fas fa-users me-2"></i>
          Beneficiarios de la acción
        </span>
        <br>
        <span class="sb-card-subtitle">Elige quiénes asistirán al evento</span>
      </div>
      <div class="sb-card mb-3">
        <div class="sb-card-body lista-beneficiarios">
          <?php if (is_countable($this->beneficiarios) && !$this->beneficiarios || count($this->beneficiarios) == 0): ?>
            <div class="sb-empty-state">
              <i class="fas fa-users"></i>
              <p>No hay beneficiarios registrados. Todas las boletas serán para invitados no asociados.</p>
            </div>
          <?php else: ?>
            <div class="sb-beneficiarios-list">
              <?php foreach ($this->beneficiariosStats['listado'] as $index => $row): ?>
                <div class="beneficiario-card sb-beneficiario-row <?= $row['socio_principal'] ? 'sb-row-principal' : '' ?>"
                  data-beneficiario-id="<?php echo $row['beneficiario']->SBE_CODI ?>">
                  <div class="sb-bene-info">
                    <div class="sb-bene-name">
                      <i class="fas fa-user-circle me-2"></i>
                      <?php echo htmlspecialchars($row['beneficiario']->SBE_NOMB) ?>
                      <?php echo htmlspecialchars($row['beneficiario']->SBE_APEL) ?>
                    </div>
                    <div class="sb-bene-tags">
                      <?php if ($row['socio_principal']): ?>
                        <span class="sb-tag sb-tag-principal">Titular</span>
                      <?php endif; ?>
                      <?php if ($row['edad'] && $row['menor25']): ?>
                        <span class="sb-tag sb-tag-age">&lt; 25</span>
                      <?php endif; ?>
                      <?php if ($row['hijo']): ?>
                        <span class="sb-tag sb-tag-hijo">Hijo/a</span>
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="sb-bene-action">
                    <?php
                    $mostrarBoton = empty($row['bloqueado_reserva']);
                    if ($mostrarBoton):
                      ?>
                      <button type="button" class="btn btn-outline-primary btn-sm btn-agregar sb-btn-toggle"
                        data-beneficiario-id="<?php echo $row['beneficiario']->SBE_CODI ?>"
                        data-accion="<?php echo $row['beneficiario']->MAC_NUME ?>">
                        <i class="fas fa-plus"></i> Agregar
                      </button>
                    <?php else: ?>
                      <button type="button" class="btn btn-secondary btn-sm sb-btn-toggle" disabled>
                        <i class="fas fa-lock"></i> Bloqueado
                      </button>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ─ Invitar cosocio ── -->
      <div id="alertaInvitarCosocio" class="sb-cosocio-alert mb-3" role="alert">
        <div class="sb-cosocio-text">
          <i class="fas fa-user-friends me-2"></i>
          ¿Desea invitar a un socio de otra acción?
        </div>
        <div class="d-flex gap-2" id="divMostrarBtnInvitar">
          <button type="button" class="sb-btn-sm sb-btn-primary" id="btnMostrarPanelInvitar">Sí, invitar</button>
          <button type="button" class="sb-btn-sm sb-btn-ghost" id="btnOcultarPanelInvitar">No</button>
        </div>
      </div>

      <!-- ── Panel búsqueda cosocio ── -->
      <div class="sb-card mb-3" id="panelInvitarPorAccion" style="display: none;">
        <div class="sb-card-header">
          <span class="sb-card-title"><i class="fas fa-search me-2"></i>Buscar socio por número de acción</span>
          <span class="sb-card-subtitle">Ingresa el número de acción para encontrar socios de otras acciones</span>
        </div>
        <div class="sb-card-body">
          <div class="sb-search-row mt-2 mb-2">
            <div class="sb-search-input-wrap">
              <label for="numeroAccion" class="sb-field-label">Número de acción</label>
              <input type="text" class="form-control sb-input" id="numeroAccion" placeholder="ingrese el número de acción" min="100"
                max="9999">
              <small class="text-muted d-none">Ingrese un número de acción válido (mínimo 3 dígitos)</small>
            </div>
            <div class="sb-search-btn-wrap">
              <button type="button" class="sb-btn-search" id="btnConsultarAccion">
                <i class="fas fa-search me-1"></i> Consultar
              </button>
              <div id="loadingConsulta" class="d-none sb-search-spinner">
                <div class="spinner-border spinner-border-sm" role="status">
                  <span class="visually-hidden">Consultando...</span>
                </div>
              </div>
            </div>
          </div>

          <div id="resultadosAccion" class="mt-3 rv-topbar" style="display: none;">
            <div class="step-indicator">
              <div class="step-badge">
                <i class="fas fa-search me-2"></i>
                <p class="sb-results-label">Socios encontrados del número de acción: <span id="numeroAccionResult"></span></p>
              </div>
            </div>
            <div id="listaSociosAccion" class="row g-2 mt-2"></div>
          </div>
        </div>
      </div>

    </div><!-- /sb-body -->

    <!-- ── Botón continuar (fijo al fondo del card) ── -->
    <div class="sb-footer">
      <!-- ── Nota invitados ── -->
      <div class="alert alert-info py-2 px-3 mb-2" style="font-size: 1rem;" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <?php if ($this->invitadosPermitidos): ?>
          Las boletas no asignadas a beneficiarios serán para <strong>invitados</strong>.
          Quedan <b id="contadorRestantesAlert"><?= $this->cantidadSeleccionada ?></b>
          boleta<?= $this->cantidadSeleccionada != 1 ? 's' : '' ?> por asignar.
        <?php else: ?>
          Este evento <strong>no permite invitados no asociados</strong>: debe asignar un socio o beneficiario a cada boleta.
          Quedan <b id="contadorRestantesAlert"><?= $this->cantidadSeleccionada ?></b>
          boleta<?= $this->cantidadSeleccionada != 1 ? 's' : '' ?> por asignar.
        <?php endif; ?>
      </div>
      <div class="sb-footer-actions">
        <a href="/page/evento/reservar" class="sb-back-btn">
          <i class="fa-solid fa-angle-left"></i>&nbsp; Regresar
        </a>
        <button type="submit" class="sb-continue-btn" id="btnContinuar">
          Continuar &nbsp;<i class="fa-solid fa-angle-right"></i>
        </button>
        <div id="loadingSnippet" class="d-none sb-loading">
          <div class="spinner-border" role="status" style="width:1.6rem;height:1.6rem;">
            <span class="visually-hidden">Cargando...</span>
          </div>
          <span>Procesando…</span>
        </div>
      </div>
    </div>

  </form>
</div>


<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Lógica para mostrar/ocultar el panel de invitar por acción
    const panelInvitarPorAccion = document.getElementById('panelInvitarPorAccion');
    const alertaInvitarCosocio = document.getElementById('alertaInvitarCosocio');
    const btnMostrarPanelInvitar = document.getElementById('btnMostrarPanelInvitar');
    const btnOcultarPanelInvitar = document.getElementById('btnOcultarPanelInvitar');
    if (btnMostrarPanelInvitar && btnOcultarPanelInvitar && panelInvitarPorAccion && alertaInvitarCosocio) {
      btnMostrarPanelInvitar.addEventListener('click', function () {
        panelInvitarPorAccion.style.display = 'block';
        alertaInvitarCosocio.style.display = 'none';
      });
      btnOcultarPanelInvitar.addEventListener('click', function () {
        const hayCosocioAgregado = beneficiariosSeleccionados.some(b => b.invitado_tipo === 3);
        if (hayCosocioAgregado) {
          Swal.fire({
            icon: 'warning',
            title: 'No se puede ocultar',
            text: 'No puedes ocultar esta sección porque ya has agregado al menos un cosocio. Si deseas quitar esta sección, primero elimina todos los cosocios agregados.',
            confirmButtonText: 'Entendido'
          });
          return;
        }
        panelInvitarPorAccion.style.display = 'none';
        alertaInvitarCosocio.style.display = 'none';
      });
    }

    <?php if ($this->beneficiario_bloqueado): ?>
      Swal.fire({
        icon: 'warning',
        title: 'Beneficiario Bloqueado',
        text: <?= $this->errorBeneficiario ? "'" . htmlspecialchars($this->errorBeneficiario) . "'" : "'Uno o más beneficiarios están bloqueados temporalmente. Por favor, inténtalo más tarde.'" ?>,
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#3085d6',
      });
    <?php endif; ?>

    const cantidadSeleccionada = <?php echo $this->cantidadSeleccionada ?>;
    const beneficiariosData = <?php echo json_encode($this->beneficiariosStats['listado'] ?? []) ?>;
    const usuarioActualDocumento = "<?php echo $this->socio->SBE_CODI ?>";
    const invitadosPermitidos = <?= $this->invitadosPermitidos ? 'true' : 'false' ?>;

    let beneficiariosSeleccionados = [];
    let sociosAccionEncontrados = [];
    let accionesConsultadas = new Map();

    let socioPrincipal;
    <?php if (!$this->existeReservaSocio): ?>
      socioPrincipal = beneficiariosData.find(b => b.socio_principal);
    <?php else: ?>
      socioPrincipal = null;
    <?php endif; ?>
    // No auto-agregar al titular si ya tiene boleta en otra reserva activa: en la fila
    // aparece como "Bloqueado", así que no debe entrar en la selección automáticamente.
    if (socioPrincipal && socioPrincipal.bloqueado_reserva) {
      socioPrincipal = null;
    }
    if (socioPrincipal) {
      beneficiariosSeleccionados.push({
        id: socioPrincipal.beneficiario.SBE_CODI,
        nombre: socioPrincipal.beneficiario.SBE_NOMB,
        apellido: socioPrincipal.beneficiario.SBE_APEL,
        edad: socioPrincipal.edad,
        menor25: socioPrincipal.menor25,
        hijo: socioPrincipal.hijo,
        es_socio_principal: true,
        invitadoReserva_correo_invitado: socioPrincipal.beneficiario.SBE_MAIL || '',
        invitadoReserva_fecha_nacimiento: socioPrincipal.beneficiario.SBE_FNAC?.date || '',
        invitadoReserva_telefono: socioPrincipal.beneficiario.SBE_NCEL || '',
        invitadoReserva_beneficiario_cupo: socioPrincipal.beneficiario.SBE_CUPO || '',
        invitadoReserva_beneficiario_principal: true,
        invitadoReserva_estado_invitado: 'A',
        invitado_tipo: 1,
        invitadoReserva_numero_carnet: socioPrincipal.beneficiario.SBE_NCAR || ''
      });
    }

    const contadorBeneficiarios = document.getElementById('contadorBeneficiarios');
    const contadorRestantes = document.getElementById('contadorRestantes');
    const contadorRestantesAlert = document.getElementById('contadorRestantesAlert');
    const divMostrarBtnInvitar = document.getElementById('divMostrarBtnInvitar');
    const btnContinuar = document.getElementById('btnContinuar');
    const botonesAgregar = document.querySelectorAll('.btn-agregar');

    const numeroAccionInput = document.getElementById('numeroAccion');
    const btnConsultarAccion = document.getElementById('btnConsultarAccion');
    const loadingConsulta = document.getElementById('loadingConsulta');
    const resultadosAccion = document.getElementById('resultadosAccion');
    const listaSociosAccion = document.getElementById('listaSociosAccion');

    function consultarPorAccion () {
      const numeroAccion = numeroAccionInput.value.trim();
      if (!numeroAccion) { Swal.fire('Error', 'Por favor ingrese un número de acción', 'error'); return; }
      if (numeroAccion.length < 3) { Swal.fire('Error', 'El número de acción debe tener al menos 3 dígitos', 'error'); return; }
      if (!/^\d+$/.test(numeroAccion)) { Swal.fire('Error', 'El número de acción debe contener solo dígitos', 'error'); return; }
      if (accionesConsultadas.has(numeroAccion)) {
        Swal.fire({ icon: 'info', title: 'Número de acción ya consultado', text: `El número de acción ${numeroAccion} ya fue consultado. Sus beneficiarios están visibles en la lista.`, confirmButtonText: 'Entendido' });
        numeroAccionInput.value = '';
        return;
      }

      btnConsultarAccion.disabled = true;
      loadingConsulta.classList.remove('d-none');

      fetch('/page/evento/consultarsocioinvitado', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ncar: numeroAccion })
      })
        .then(response => response.json())
        .then(data => {
          btnConsultarAccion.disabled = false;
          loadingConsulta.classList.add('d-none');
          if (!data || data.success === false) {
            Swal.fire('Información', data.message || 'No se encontró información del socio', 'info');
            numeroAccionInput.value = '';
            return;
          }

          const socio = data.data;
          const socioNormalizado = {
            ...socio,
            edad: calcularEdad(socio.SBE_FNAC),
            menor25: calcularEdad(socio.SBE_FNAC) < 25,
            hijo: (socio.parentesco === 'Hijo' || socio.parentesco === 'Hija' || socio.parentesco === 'hijo' || socio.parentesco === 'hija')
          };

          const yaSeleccionado = beneficiariosSeleccionados.some(b => b.id === socioNormalizado.SBE_CODI);
          if (!yaSeleccionado && beneficiariosSeleccionados.length < cantidadSeleccionada) {
            beneficiariosSeleccionados.push({
              id: socioNormalizado.SBE_CODI,
              nombre: socioNormalizado.sbe_nomb,
              apellido: socioNormalizado.sbe_apel,
              edad: socioNormalizado.edad,
              menor25: socioNormalizado.menor25,
              hijo: socioNormalizado.hijo,
              invitadoReserva_correo_invitado: socioNormalizado.SBE_MAIL || '',
              invitadoReserva_fecha_nacimiento: socioNormalizado.SBE_FNAC?.date || '',
              invitadoReserva_telefono: socioNormalizado.SBE_NCEL || '',
              invitadoReserva_beneficiario_cupo: socioNormalizado.SBE_CUPO || '',
              invitadoReserva_beneficiario_principal: false,
              invitadoReserva_estado_invitado: 'S',
              invitado_tipo: 3,
              invitadoReserva_numero_carnet: socioNormalizado.SBE_NCAR || ''
            });
            fetch('/page/evento/bloquearbeneficiario', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: `beneficiario_doc=${encodeURIComponent(socioNormalizado.SBE_CODI)}&accion=${numeroAccion}&estado=1`
            }).catch(() => { });
          } else if (!yaSeleccionado) {
            Swal.fire('Aviso', 'Ya alcanzaste el número máximo de invitados', 'warning');
          }

          accionesConsultadas.set(numeroAccion, [socioNormalizado]);
          sociosAccionEncontrados = [...sociosAccionEncontrados, socioNormalizado];
          mostrarSociosEncontrados();
          resultadosAccion.style.display = 'block';
          actualizarEstado();
          numeroAccionInput.value = '';
        })
        .catch(error => {
          btnConsultarAccion.disabled = false;
          loadingConsulta.classList.add('d-none');
          Swal.fire('Error', 'Error al consultar los beneficiarios', 'error');
        });
    }

    function mostrarSociosEncontrados () {
      listaSociosAccion.innerHTML = '';
      const sociosPorAccion = new Map();
      accionesConsultadas.forEach((socios, numeroAccion) => { sociosPorAccion.set(numeroAccion, socios); });

      sociosPorAccion.forEach((socios, numeroAccion) => {
        const encabezadoAccion = document.createElement('div');
        encabezadoAccion.className = 'col-12';
        encabezadoAccion.dataset.accionHeader = numeroAccion;
        encabezadoAccion.innerHTML = `
          <div class="sb-accion-header">
            <span class="sb-accion-label"><i class="fas fa-user"></i>Carnet: <strong>${numeroAccion}</strong></span>
          </div>
        `;
        const numeroAccionElement = document.getElementById('numeroAccionResult');
        numeroAccionElement.textContent = numeroAccion;
        listaSociosAccion.appendChild(encabezadoAccion);

        socios.forEach(socio => {
          const socioCard = document.createElement('div');
          socioCard.className = 'col-md-12';
          socioCard.dataset.accionCard = numeroAccion;
          const yaSeleccionado = beneficiariosSeleccionados.some(b => b.id === socio.SBE_CODI);

          socioCard.innerHTML = `
            <div class="beneficiario-card sb-beneficiario-row ${yaSeleccionado ? 'sb-row-selected' : ''}"
                 data-beneficiario-id="${socio.SBE_CODI}" data-accion="${numeroAccion}">
              <div class="sb-bene-info">
                <div class="sb-bene-name"><i class="fas fa-user-circle me-2 text-white"></i>${socio.sbe_nomb} ${socio.sbe_apel}</div>
                <div class="sb-bene-tags">
                  ${socio.menor25 ? '<span class="sb-tag sb-tag-age">&lt; 25</span>' : ''}
                </div>
              </div>
              <div class="sb-bene-action">
                <button type="button"
                        class="btn btn-outline-primary btn-sm btn-agregar-accion sb-btn-toggle"
                        data-beneficiario-id="${socio.SBE_CODI}"
                        data-accion="${numeroAccion}">
                  <i class="fas fa-plus"></i> Agregar
                </button>
              </div>
            </div>
          `;
          listaSociosAccion.appendChild(socioCard);
        });
      });

      actualizarBotonesAccion();
    }

    function calcularEdad (fechaNacimiento) {
      if (!fechaNacimiento || !fechaNacimiento.date) return 0;
      const fecha = new Date(fechaNacimiento.date);
      const hoy = new Date();
      let edad = hoy.getFullYear() - fecha.getFullYear();
      const mes = hoy.getMonth() - fecha.getMonth();
      if (mes < 0 || (mes === 0 && hoy.getDate() < fecha.getDate())) edad--;
      return edad;
    }

    function actualizarBotonesAccion () {
      const botonesAccion = document.querySelectorAll('.btn-agregar-accion');
      const totalSeleccionados = beneficiariosSeleccionados.length;
      const hayCupo = totalSeleccionados < cantidadSeleccionada;

      botonesAccion.forEach(btn => {
        if (btn.disabled) return;
        const beneficiarioId = btn.dataset.beneficiarioId;
        const yaSeleccionado = beneficiariosSeleccionados.some(b => b.id === beneficiarioId);
        if (yaSeleccionado) {
          btn.innerHTML = '<i class="fas fa-minus"></i> Quitar';
          btn.className = 'btn btn-danger btn-sm btn-agregar-accion sb-btn-toggle';
        } else {
          btn.innerHTML = '<i class="fas fa-plus"></i> Agregar';
          btn.className = 'btn btn-outline-primary btn-sm btn-agregar-accion sb-btn-toggle';
          btn.disabled = !hayCupo;
        }
      });
    }

    function eliminarAccionConsultada (numeroAccion) {
      const sociosDeEstaAccion = accionesConsultadas.get(numeroAccion) || [];
      const sociosSeleccionados = sociosDeEstaAccion.filter(socio =>
        beneficiariosSeleccionados.some(b => b.id === socio.SBE_CODI)
      );
      if (sociosSeleccionados.length > 0) {
        Swal.fire({ title: 'Beneficiarios seleccionados', text: `No se puede eliminar la acción ${numeroAccion} porque tiene beneficiarios seleccionados.`, icon: 'warning', confirmButtonText: 'Entendido' });
        return;
      }
      Swal.fire({
        title: '¿Eliminar socio?',
        text: `¿Está seguro de que desea eliminar el socio con número de acción ${numeroAccion}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          accionesConsultadas.delete(numeroAccion);
          sociosAccionEncontrados = [];
          accionesConsultadas.forEach((socios) => { sociosAccionEncontrados = [...sociosAccionEncontrados, ...socios]; });
          if (accionesConsultadas.size === 0) {
            resultadosAccion.style.display = 'none';
          } else {
            mostrarSociosEncontrados();
          }
          actualizarEstado();
          Swal.fire({ title: 'Socio eliminado', text: `El socio con número de acción ${numeroAccion} ha sido eliminado.`, icon: 'success', timer: 2000, showConfirmButton: false });
        }
      });
    }
    window.eliminarAccionConsultada = eliminarAccionConsultada;

    function actualizarEstadoConsultaAccion () {
      numeroAccionInput.disabled = false;
      btnConsultarAccion.disabled = false;
      numeroAccionInput.classList.remove('bg-light');
      btnConsultarAccion.classList.remove('btn-secondary');
      btnConsultarAccion.classList.add('btn-outline-primary');
      btnConsultarAccion.innerHTML = '<i class="fas fa-search me-1"></i>Consultar';
    }

    btnConsultarAccion.addEventListener('click', consultarPorAccion);
    numeroAccionInput.addEventListener('keypress', function (e) {
      if (e.key === 'Enter') { e.preventDefault(); consultarPorAccion(); }
    });

    document.addEventListener('click', function (e) {
      if (e.target.closest('.btn-agregar-accion')) {
        const btn = e.target.closest('.btn-agregar-accion');
        const beneficiarioId = btn.dataset.beneficiarioId;
        const numeroAccion = btn.dataset.accion;

        let socioData = null;
        accionesConsultadas.forEach((socios) => {
          const encontrado = socios.find(s => s.SBE_CODI === beneficiarioId);
          if (encontrado) socioData = encontrado;
        });

        const estado = obtenerEstadoBotonAccion(beneficiarioId);
        if (!socioData) return;

        const index = beneficiariosSeleccionados.findIndex(b => b.id === beneficiarioId);

        if (index > -1) {
          // ── Quitar: pedir confirmación antes de proceder ──
          Swal.fire({
            title: '¿Quitar socio?',
            text: `¿Está seguro de que desea quitar al socio con carnet ${numeroAccion}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, quitar',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (!result.isConfirmed) return;

            fetch('/page/evento/bloquearbeneficiario', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: `beneficiario_doc=${encodeURIComponent(beneficiarioId)}&accion=${numeroAccion}&estado=${estado}`
            })
              .then(res => res.json())
              .then(data => {
                if (!data.success) { Swal.fire('Error', data.mensaje || 'No se pudo procesar la acción', 'error'); return; }
              })
              .catch(error => { console.error('Error al bloquear socio de acción:', error); });

            beneficiariosSeleccionados.splice(index, 1);
            const cardCol = btn.closest('[data-accion-card]');
            const headerCol = listaSociosAccion.querySelector(`[data-accion-header="${numeroAccion}"]`);
            if (cardCol) cardCol.remove();
            if (headerCol) headerCol.remove();
            accionesConsultadas.delete(numeroAccion);
            sociosAccionEncontrados = sociosAccionEncontrados.filter(s => s.SBE_CODI !== beneficiarioId);
            if (listaSociosAccion.querySelectorAll('[data-accion-card]').length === 0) {
              resultadosAccion.style.display = 'none';
            }
            actualizarEstado();
            actualizarBotonesAccion();
            Swal.fire({ title: 'Socio eliminado', text: 'El socio ha sido eliminado correctamente.', icon: 'success', timer: 2000, showConfirmButton: false });
          });
        } else {
          // ── Agregar ──
          if (beneficiariosSeleccionados.length >= cantidadSeleccionada) {
            Swal.fire({ icon: 'warning', title: 'Límite alcanzado', text: 'Ya has seleccionado la cantidad máxima de personas permitidas.' });
            return;
          }

          fetch('/page/evento/bloquearbeneficiario', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `beneficiario_doc=${encodeURIComponent(beneficiarioId)}&accion=${numeroAccion}&estado=${estado}`
          })
            .then(res => res.json())
            .then(data => {
              if (!data.success) { Swal.fire('Error', data.mensaje || 'No se pudo procesar la acción', 'error'); return; }
            })
            .catch(error => { console.error('Error al bloquear socio de acción:', error); });

          beneficiariosSeleccionados.push({
            id: beneficiarioId,
            nombre: socioData.SBE_NOMB,
            apellido: socioData.SBE_APEL,
            edad: socioData.edad,
            menor25: socioData.menor25,
            hijo: socioData.hijo,
            es_socio_principal: false,
            invitadoReserva_correo_invitado: socioData.SBE_MAIL || '',
            invitadoReserva_fecha_nacimiento: socioData.SBE_FNAC?.date || '',
            invitadoReserva_telefono: socioData.SBE_NCEL || '',
            invitadoReserva_beneficiario_cupo: socioData.SBE_CUPO || '',
            invitadoReserva_estado_invitado: 'S',
            invitado_tipo: 3,
            invitadoReserva_numero_carnet: socioData.SBE_NCAR || '',
          });

          actualizarEstado();
          actualizarBotonesAccion();

          const card = btn.closest('.beneficiario-card');
          if (card) card.classList.add('sb-row-selected');
        }
      }
    });

    function actualizarEstado () {
      const totalSeleccionados = beneficiariosSeleccionados.length;
      const restantes = cantidadSeleccionada - totalSeleccionados;

      contadorBeneficiarios.textContent = totalSeleccionados;
      contadorRestantes.textContent = restantes;
      contadorRestantesAlert.textContent = restantes;

      botonesAgregar.forEach(btn => {
        if (btn.disabled) return;
        const beneficiarioId = btn.dataset.beneficiarioId;
        const beneficiarioData = beneficiariosData.find(b => b.beneficiario.SBE_CODI === beneficiarioId);
        const yaSeleccionado = beneficiariosSeleccionados.some(b => b.id === beneficiarioId);

        if (yaSeleccionado) {
          btn.innerHTML = '<i class="fas fa-minus"></i> Quitar';
          btn.className = 'btn btn-danger btn-sm btn-agregar sb-btn-toggle';
          btn.disabled = false;
          // mark the parent card
          const card = btn.closest('.beneficiario-card');
          if (card) card.classList.add('sb-row-selected');
        } else {
          btn.innerHTML = '<i class="fas fa-plus"></i> Agregar';
          btn.className = 'btn btn-outline-primary btn-sm btn-agregar sb-btn-toggle';
          btn.disabled = restantes <= 0;
          const card = btn.closest('.beneficiario-card');
          if (card) card.classList.remove('sb-row-selected');
        }
      });

      btnContinuar.disabled = false;
      actualizarBotonesAccion();
      actualizarEstadoConsultaAccion();

      if (restantes <= 0) {
        contadorRestantes.classList.add('text-danger');
        contadorRestantesAlert.classList.add('text-danger');
        divMostrarBtnInvitar.classList.add('d-none');
        botonesAgregar.forEach(btn => {
          const beneficiarioId = btn.dataset.beneficiarioId;
          const yaSeleccionado = beneficiariosSeleccionados.some(b => b.id === beneficiarioId);
          if (!yaSeleccionado) {
            btn.disabled = true;
            btn.classList.add('btn-secondary');
            btn.classList.remove('btn-outline-primary');
          }
        });
      } else {
        contadorRestantes.classList.remove('text-danger');
        contadorRestantesAlert.classList.remove('text-danger');
        divMostrarBtnInvitar.classList.remove('d-none');
      }
    }

    botonesAgregar.forEach(btn => {
      btn.addEventListener('click', function () {
        const beneficiarioId = this.dataset.beneficiarioId;
        const numeroAccion = this.dataset.accion;
        const beneficiarioData = beneficiariosData.find(b => b.beneficiario.SBE_CODI === beneficiarioId);
        const estado = obtenerEstadoBoton(beneficiarioId);
        const index = beneficiariosSeleccionados.findIndex(b => b.id === beneficiarioId);

        fetch('/page/evento/bloquearbeneficiario', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `beneficiario_doc=${encodeURIComponent(beneficiarioId)}&accion=${numeroAccion}&estado=${estado}`
        })
          .then(res => res.json())
          .then(data => {
            if (!data.success) { Swal.fire('Error', data.mensaje || 'No se pudo procesar la acción', 'error'); return; }
          })
          .catch(error => { console.error('Error al comunicar bloqueo de beneficiario:', error); });

        if (index > -1) {
          beneficiariosSeleccionados.splice(index, 1);
        } else {
          if (beneficiariosSeleccionados.length >= cantidadSeleccionada) {
            Swal.fire({ icon: 'warning', title: 'Lmite alcanzado', text: 'Ya has seleccionado la cantidad máxima de personas permitidas.' });
            return;
          }
          if (beneficiariosSeleccionados.length < cantidadSeleccionada) {
            beneficiariosSeleccionados.push({
              id: beneficiarioId,
              nombre: beneficiarioData.beneficiario.SBE_NOMB,
              apellido: beneficiarioData.beneficiario.SBE_APEL,
              edad: beneficiarioData.edad,
              menor25: beneficiarioData.menor25,
              hijo: beneficiarioData.hijo,
              es_socio_principal: false,
              invitadoReserva_correo_invitado: beneficiarioData.beneficiario.SBE_MAIL || '',
              invitadoReserva_fecha_nacimiento: beneficiarioData.beneficiario.SBE_FNAC?.date || '',
              invitadoReserva_telefono: beneficiarioData.beneficiario.SBE_NCEL || '',
              invitadoReserva_beneficiario_cupo: beneficiarioData.beneficiario.SBE_CUPO || '',
              invitadoReserva_estado_invitado: 'A',
              invitado_tipo: beneficiarioData.hijo ? 2 : 1,
              invitadoReserva_numero_carnet: beneficiarioData.beneficiario.SBE_NCAR || ''
            });
          }
        }

        actualizarEstado();
      });
    });

    // Cuando el evento no permite invitados no asociados, el cupo completo debe
    // cubrirse con socios/beneficiarios antes de poder continuar.
    function cupoIncompletoSinInvitados () {
      if (invitadosPermitidos) return false;
      if (beneficiariosSeleccionados.length >= cantidadSeleccionada) return false;
      const faltan = cantidadSeleccionada - beneficiariosSeleccionados.length;
      Swal.fire({
        icon: 'warning',
        title: 'Cupo incompleto',
        text: `Este evento no permite invitados no asociados. Debe asignar un socio o beneficiario a las ${faltan} boleta${faltan > 1 ? 's' : ''} restante${faltan > 1 ? 's' : ''} antes de continuar.`,
        confirmButtonText: 'Entendido'
      });
      return true;
    }

    btnContinuar.addEventListener('click', function (e) {
      e.preventDefault();

      if (cupoIncompletoSinInvitados()) return;

      const beneficiariosSeleccionadosActuales = beneficiariosSeleccionados.filter(b => !b.es_socio_principal).length;
      const cantidadPersonas = cantidadSeleccionada;
      const socioPrincipalData = beneficiariosSeleccionados.find(b => b.es_socio_principal);
      let nombreSocioPrincipal = '<?= $this->socio->sbe_nomb ?>';
      if (socioPrincipalData && typeof socioPrincipalData === 'object' && socioPrincipalData.nombre) {
        nombreSocioPrincipal = socioPrincipalData.nombre;
      }

      if (beneficiariosSeleccionadosActuales === 0) {
        const mensaje = `¿` + nombreSocioPrincipal + ` confirma la reserva para ${cantidadPersonas} persona${cantidadPersonas > 1 ? 's' : ''}?`;
        Swal.fire({
          title: 'Confirmación',
          html: mensaje,
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Sí, continuar',
          cancelButtonText: 'No, cancelar',
          customClass: { actions: 'my-swal-actions', confirmButton: 'me-3' }
        }).then((result) => { if (result.isConfirmed) enviarDatos(); });
      } else if (beneficiariosSeleccionados.length < cantidadPersonas) {
        const totalPersonas = beneficiariosSeleccionados.length;
        const personasRestantes = cantidadPersonas - totalPersonas;
        const mensaje = `Ha seleccionado ${beneficiariosSeleccionadosActuales} beneficiario${beneficiariosSeleccionadosActuales > 1 ? 's' : ''}.<br><br>` +
          `La reserva es para ${cantidadPersonas} personas. Las ${personasRestantes} persona${personasRestantes > 1 ? 's' : ''} restante${personasRestantes > 1 ? 's' : ''} se considerará${personasRestantes > 1 ? 'n' : ''} como invitado${personasRestantes > 1 ? 's' : ''}.<br><br>` +
          `¿Desea continuar?`;
        Swal.fire({
          title: 'Confirmar reserva',
          html: mensaje,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Sí, continuar',
          cancelButtonText: 'No, cancelar',
          customClass: { actions: 'my-swal-actions', confirmButton: 'me-3' }
        }).then((result) => { if (result.isConfirmed) enviarDatos(); });
      } else {
        enviarDatos();
      }
    });

    document.getElementById('formSeleccionBeneficiarios').addEventListener('submit', function (e) {
      e.preventDefault();
      if (cupoIncompletoSinInvitados()) return;
      enviarDatos();
    });

    function enviarDatos () {
      const restantes = cantidadSeleccionada - beneficiariosSeleccionados.length;
      const btn = document.getElementById("btnContinuar");
      const loading = document.getElementById("loadingSnippet");
      const formData = new FormData();
      formData.append('beneficiarios_seleccionados', JSON.stringify(beneficiariosSeleccionados));
      formData.append('cantidad_no_asociados', restantes);

      btn.classList.add("d-none");
      loading.classList.remove("d-none");

      fetch('/page/evento/procesarseleccion', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            document.getElementById('formSeleccionBeneficiarios').submit();
          } else {
            alert('Error al procesar la selección: ' + data.message);
            btn.classList.remove("d-none");
            loading.classList.add("d-none");
          }
        })
        .catch(error => {
          alert('Error al procesar la selección');
          btn.classList.remove("d-none");
          loading.classList.add("d-none");
        });
    }

    actualizarEstado();
    actualizarBotonesBloqueados();

    function actualizarBotonesBloqueados () {
      const numeroAccion = "<?php echo $this->socio->MAC_NUME ?>";
      const numeroDocumento = "<?php echo $this->socio->SBE_CODI ?>";
      if (!numeroAccion || !numeroDocumento) { console.warn('Falta información del socio.'); return; }

      fetch('/page/evento/obtenerdocumentosbloqueados', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `accion=${encodeURIComponent(numeroAccion)}&documento=${encodeURIComponent(numeroDocumento)}`
      })
        .then(response => response.json())
        .then(data => {
          if (!data) return;
          const bloqueados = data.map(b => b.beneficiario_bloqueodocumento);
          const totalSeleccionados = beneficiariosSeleccionados.length;
          const hayCupo = totalSeleccionados < cantidadSeleccionada;

          botonesAgregar.forEach(btn => {
            const beneficiarioId = btn.dataset.beneficiarioId;
            const estaSeleccionado = beneficiariosSeleccionados.some(b => b.id === beneficiarioId);
            const estaBloqueado = bloqueados.includes(beneficiarioId);
            if (estaBloqueado && !estaSeleccionado) {
              btn.disabled = true;
              btn.innerHTML = '<i class="fas fa-lock"></i> Bloqueado';
              btn.classList.add('btn-secondary');
              btn.classList.remove('btn-outline-primary', 'btn-danger');
            } else if (!estaSeleccionado) {
              if (hayCupo) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus"></i> Agregar';
                btn.className = 'btn btn-outline-primary btn-sm btn-agregar sb-btn-toggle';
              } else {
                btn.disabled = true;
                btn.classList.add('btn-secondary');
                btn.classList.remove('btn-outline-primary');
              }
            }
          });

          const botonesAccion = document.querySelectorAll('.btn-agregar-accion');
          botonesAccion.forEach(btn => {
            const beneficiarioId = btn.dataset.beneficiarioId;
            const estaSeleccionado = beneficiariosSeleccionados.some(b => b.id === beneficiarioId);
            const estaBloqueado = bloqueados.includes(beneficiarioId);
            if (estaBloqueado && !estaSeleccionado) {
              btn.disabled = true;
              btn.innerHTML = '<i class="fas fa-lock"></i> Bloqueado';
              btn.classList.add('btn-secondary');
              btn.classList.remove('btn-outline-primary', 'btn-danger');
            } else if (!estaSeleccionado) {
              if (hayCupo) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus"></i> Agregar';
                btn.className = 'btn btn-outline-primary btn-sm btn-agregar-accion sb-btn-toggle';
              } else {
                btn.disabled = true;
                btn.classList.add('btn-secondary');
                btn.classList.remove('btn-outline-primary');
              }
            }
          });
        });
    }
    setInterval(actualizarBotonesBloqueados, 3000);

    function obtenerEstadoBoton (beneficiarioId) {
      return beneficiariosSeleccionados.some(b => b.id === beneficiarioId) ? 0 : 1;
    }
    function obtenerEstadoBotonAccion (beneficiarioId) {
      return beneficiariosSeleccionados.some(b => b.id === beneficiarioId) ? 0 : 1;
    }
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    validarSesion();
    setInterval(validarSesion, 5000);
  });
</script>

<style>
  /* ============================================================
   SELECCIONAR BENEFICIARIOS — Step 2 — Dark glass
============================================================ */

  /* Ocupa exactamente el espacio entre header (60px) y footer (30px); el scroll es interior */
  .contenedor-general {
    height: calc(100vh - 60px - 30px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding-bottom: 0 !important;
  }

  .sb-wrap {
    max-width: 100%;
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
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

  /* Zona de contenido con scroll interior */
  .sb-body {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    overflow-x: hidden;
  }

  /* ── Back button ─ */
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

  /* ── Topbar row (igual que reservar.php) ── */
  .rv-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    flex-wrap: wrap;
  }

  /* ── Step badge (scoped a rv-topbar, igual que reservar.php) ── */
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

  /* ── Timer pill (igual que reservar.php) ── */
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

  .rv-timer-icon {
    font-size: 0.78rem;
    color: #ffc107;
    flex-shrink: 0;
  }

  .rv-timer-display {
    font-size: 1.05rem;
    font-weight: 700;
    color: #ffffff;
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
    color: white;
    font-weight: 600;
  }

  .rv-timer.urgent {
    border-color: rgba(220, 53, 69, 0.55);
    background: rgba(220, 53, 69, 0.1);
  }

  .rv-timer.urgent .rv-timer-icon,
  .rv-timer.urgent .rv-timer-display {
    color: #ef9a9a;
  }

  /* ── Summary bar ── */
  .sb-summary-bar {
    display: flex;
    align-items: center;
    background: rgba(6, 6, 6, 0.55);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 6px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  }

  .sb-summary-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0.9rem 0.5rem;
    gap: 2px;
  }

  .sb-summary-sep {
    width: 1px;
    height: 2.5rem;
    background: rgba(255, 255, 255, 0.08);
    flex-shrink: 0;
  }

  .sb-summary-label {
    font-size: 0.82rem;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: white;
    font-weight: 600;
    text-align: center;
  }

  .sb-summary-val {
    font-size: 1.3rem;
    font-weight: 700;
    color: #ffffff;
    line-height: 1;
  }

  .sb-counter-restantes {
    color: #ffc107;
  }

  /* ── Cards ── */
  .sb-card {
    background: rgba(6, 6, 6, 0.6);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 6px;
    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.45);
    padding: 8px 20px 0px 20px;
  }

  .sb-card-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    flex-direction: column;
    gap: 2px;
  }

  .sb-card-title {
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #ffffff;
  }
  
  .sb-beneficiario-title {
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    color: #ffffff;
  }

  .sb-card-subtitle {
    font-size: 0.82rem;
    color: white;
  }

  .sb-card-body {
    padding: 0;
  }

  /* ─ Beneficiary list ── */
  .lista-beneficiarios {
    max-height: 35vh;
    overflow-y: auto;
    overflow-x: hidden;
  }

  .sb-beneficiarios-list {
    display: flex;
    flex-direction: column;
  }

  .beneficiario-card.sb-beneficiario-row {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 0.35rem 1.25rem !important;
    border: 1px solid rgba(255, 193, 7, 0.5) !important;
    border-radius: 6px !important;
    box-shadow: none !important;
    background: transparent !important;
    transition: background 0.15s ease !important;
    cursor: default !important;
    transform: none !important;
    margin-bottom: 0.5rem !important;
  }

  .beneficiario-card.sb-beneficiario-row:hover {
    background: rgba(255, 255, 255, 0.04) !important;
    transform: none !important;
    box-shadow: none !important;
  }

  .sb-row-principal {
    background: rgba(255, 193, 7, 0.07) !important;
    border-left: 3px solid rgba(255, 193, 7, 0.5) !important;
  }

  .sb-row-selected {
    background: rgba(255, 255, 255, 0.07) !important;
    border-left: 3px solid rgba(255, 255, 255, 0.35) !important;
  }

  .sb-bene-info {
    flex: 1;
    min-width: 0;
    display: flex;
    /* flex-direction: column; */
    gap: 5px;
  }

  .sb-bene-name {
    font-size: 0.88rem;
    font-weight: 600;
    color: #ffffff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .sb-bene-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
  }

  .sb-tag {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    padding: 2px 6px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
  }

  .sb-tag-principal {
    background: rgba(255, 193, 7, 0.18);
    color: #ffc107;
  }

  .sb-tag-age {
    background: rgba(74, 144, 226, 0.2);
    color: #7ec8f5;
  }

  .sb-tag-hijo {
    background: rgba(29, 106, 63, 0.25);
    color: #81e6a8;
  }

  .sb-bene-action {
    flex-shrink: 0;
    margin-left: 1rem;
  }

  /* ── Toggle buttons (Bootstrap overrides for dark bg) ── */
  .sb-btn-toggle {
    border-radius: 3px !important;
    font-size: 0.90rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.3px !important;
    padding: 4px 12px !important;
    min-width: 80px;
    transition: background 0.15s ease, border-color 0.15s ease !important;
  }

  .sb-wrap .btn-outline-primary {
    color: #ffc107 !important;
    border-color: rgba(255, 193, 7, 0.4) !important;
    background: rgba(255, 193, 7, 0.1) !important;
  }

  .sb-wrap .btn-outline-primary:hover {
    background: rgba(255, 193, 7, 0.25) !important;
    border-color: #ffc107 !important;
    color: #ffd54f !important;
  }

  .sb-wrap .btn-danger {
    background: rgba(220, 53, 69, 0.18) !important;
    border-color: rgba(220, 53, 69, 0.4) !important;
    color: #ef9a9a !important;
  }

  .sb-wrap .btn-danger:hover {
    background: rgba(220, 53, 69, 0.35) !important;
    color: #ffb3b3 !important;
  }

  .sb-wrap .btn-secondary {
    background: rgba(255, 255, 255, 0.05) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    color: rgba(255, 255, 255, 0.22) !important;
  }

  /* ── Empty state ── */
  .sb-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 2.5rem 1rem;
    color: rgba(255, 255, 255, 0.3);
    font-size: 0.85rem;
    text-align: center;
  }

  .sb-empty-state i {
    font-size: 2rem;
    color: rgba(255, 255, 255, 0.12);
  }

  /* ── Cosocio alert ── */
  .sb-cosocio-alert {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 0.85rem 1.25rem;
    background: rgba(6, 6, 6, 0.55);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-left: 3px solid rgba(255, 193, 7, 0.5);
    border-radius: 6px;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.65);
    flex-wrap: wrap;
  }

  .sb-cosocio-text {
    display: flex;
    align-items: center;
    color: white;
    font-size: 1rem;
  }

  /* ── Small action buttons ── */
  .sb-btn-sm {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    padding: 5px 14px;
    border: 1px solid;
    border-radius: 3px;
    cursor: pointer;
    transition: background 0.15s ease;
  }

  .sb-btn-primary {
    background: rgba(255, 193, 7, 0.15);
    border-color: rgba(255, 193, 7, 0.45);
    color: #ffc107;
  }

  .sb-btn-primary:hover {
    background: rgba(255, 193, 7, 0.3);
    border-color: #ffc107;
    color: #ffd54f;
  }

  .sb-btn-ghost {
    background: transparent;
    border-color: rgba(255, 255, 255, 0.15);
    color: white;
  }

  .sb-btn-ghost:hover {
    background: rgba(255, 255, 255, 0.07);
    color: white;
  }

  /* ─ Search row ── */
  .sb-search-row {
    display: flex;
    align-items: flex-end;
    gap: 2rem;
    flex-wrap: wrap;
  }

  .sb-search-input-wrap {
    flex: 1;
    min-width: 200px;
  }

  .sb-field-label {
    display: block;
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: white;
    margin-bottom: 0.4rem;
  }

  .sb-input {
    border-radius: 3px !important;
    border: 3px solid rgba(255, 193, 7, 0.5) !important;
    background: rgba(255, 255, 255, 0.06) !important;
    color: #ffffff !important;
    font-size: 0.9rem !important;
  }

  .sb-input::placeholder {
    color: rgba(255, 255, 255, 0.22) !important;
  }

  .sb-input:focus {
    border-color: rgba(255, 193, 7, 0.5) !important;
    box-shadow: none !important;
    background: rgba(255, 255, 255, 0.09) !important;
    color: #ffffff !important;
  }

  .sb-search-btn-wrap {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  /* ── Consultar button (pill ámbar) ── */
  .sb-btn-search {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    padding: 8px 20px;
    background: rgba(255, 193, 7, 0.1);
    border: 1px solid rgba(255, 193, 7, 0.38);
    border-radius: 20px;
    color: #ffc107;
    cursor: pointer;
    transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
    white-space: nowrap;
  }

  .sb-btn-search:hover {
    background: rgba(255, 193, 7, 0.22);
    border-color: #ffc107;
    color: #ffd54f;
  }

  .sb-btn-search:disabled {
    opacity: 0.38;
    cursor: not-allowed;
  }

  /* ── Spinner de búsqueda ── */
  .sb-search-spinner .spinner-border {
    width: 1.1rem;
    height: 1.1rem;
    border-color: rgba(255, 255, 255, 0.35) !important;
    border-right-color: transparent !important;
  }

  /* ── Resultados ── */
  #resultadosAccion {
    padding: 0 1.25rem 1rem;
  }

  .sb-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.08);
    margin-bottom: 0.85rem;
  }

  .sb-results-label {
    margin-bottom: 0px;
  }

  /* ── Acción header en resultados (pill) ─ */
  .sb-accion-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.5rem 0.9rem;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
  }

  .sb-accion-label {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.78rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.65);
  }

  .sb-accion-label strong {
    color: #ffffff;
    font-weight: 700;
  }

  /* ── Quitar button (pill rojo) ── */
  .sb-btn-remove {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    padding: 4px 12px;
    background: transparent;
    border: 1px solid rgba(220, 53, 69, 0.38);
    border-radius: 20px;
    color: #ef9a9a;
    cursor: pointer;
    transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
  }

  .sb-btn-remove:hover {
    background: rgba(220, 53, 69, 0.18);
    border-color: rgba(220, 53, 69, 0.6);
    color: #ffb3b3;
  }

  /* ── Info note ── */
  .sb-info-note {
    padding: 0.75rem 1.25rem;
    background: rgba(6, 6, 6, 0.55);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-left: 3px solid rgba(255, 193, 7, 0.5);
    font-size: 0.83rem;
    color: white;
    border-radius: 6px;
  }

  /* ── Footer ── */
  .sb-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 1.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    gap: 5px;
  }

  .sb-footer-actions {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
  }

  /* Footer variant of the back button: same size as "Continuar", bigger font for visibility */
  .sb-footer-actions .sb-back-btn {
    height: 45px;
    min-width: 200px;
    justify-content: center;
    font-size: 1.2rem;
    padding: 0 20px;
  }

  .sb-continue-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 1.2rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    min-width: 200px;
    background: #ffc107;
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    color: #ffffff !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    transition: background 0.18s ease, border-color 0.18s ease;
    cursor: pointer;
    height: 45px;
  }

  .sb-continue-btn:hover:not(:disabled) {
    background: rgba(255, 255, 255, 0.13);
    border-color: #ffc107;
    color: #ffffff !important;
  }

  .sb-continue-btn:disabled {
    opacity: 0.35;
    cursor: not-allowed;
  }

  .sb-loading {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 0.82rem;
    color: rgba(255, 255, 255, 0.45);
  }

  .sb-loading .spinner-border {
    border-color: rgba(255, 255, 255, 0.3) !important;
    border-right-color: transparent !important;
  }

  /* ── Override Bootstrap card defaults ── */
  .sb-wrap .card:not(.sb-card):not(.beneficiario-card) {
    border-radius: 6px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    background: rgba(6, 6, 6, 0.55);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
  }

  /* ── Responsive ─ */
  @media (max-width: 767px) {
    /* Break the fixed-height/overflow chain so the page scrolls naturally on mobile */
    .contenedor-general { height: auto !important; overflow: visible !important; }
    .sb-wrap { overflow: visible !important; flex: none !important; }
    .card-principal { overflow: visible !important; flex: none !important; padding: 12px 10px !important; margin-bottom: 30px !important; }
    .sb-body { overflow: visible !important; height: auto !important; flex: none !important; }

    /* Topbar left side: never wrap */
    .rv-topbar > div:first-child { flex-wrap: nowrap !important; }
    .rv-topbar .step-badge { font-size: 0.72rem; padding: 5px 10px; white-space: nowrap; }
    .rv-topbar .step-name { display: none; }
    .rv-topbar .step-divider { display: none; }

    /* Timer: hide label to save width */
    .rv-timer { padding: 5px 10px; gap: 0.3rem; }
    .rv-timer-label { display: none; }
    .rv-timer-display { font-size: 0.88rem; letter-spacing: 1px; }

    /* Cards: tighter horizontal padding */
    .sb-card { padding: 8px 12px 0 12px; }

    /* Beneficiary rows: stack name+tags vertically, keep info+button on one row */
    .beneficiario-card.sb-beneficiario-row { flex-wrap: nowrap !important; padding: 0.55rem 0.75rem !important; }
    .sb-bene-info { flex-direction: column; gap: 2px; }
    .sb-bene-name { white-space: normal; font-size: 0.82rem; }
    .sb-bene-action { margin-left: 0.5rem; }

    /* Footer: stack alert + actions vertically */
    .sb-footer { flex-direction: column; align-items: stretch; gap: 0.75rem; }
    .sb-footer-actions { width: 100%; }
    .sb-footer-actions .sb-back-btn { flex: 1; min-width: 0; font-size: 1.05rem; }
    .sb-continue-btn { flex: 1; font-size: 1.1rem; }

    /* Cosocio alert: stack text + action buttons */
    .sb-cosocio-alert { flex-direction: column; align-items: flex-start; }
  }

  @media (max-width: 480px) {
    /* Summary bar: shrink text to keep 3 items on one line */
    .sb-summary-label { font-size: 0.55rem; letter-spacing: 0.3px; }
    .sb-summary-val { font-size: 0.95rem; }
    .sb-summary-item { padding: 0.55rem 0.15rem; }
    .sb-summary-sep { height: 2rem; }

    /* Back button: tighten */
    .sb-back-btn { font-size: 0.7rem; padding: 5px 10px 5px 8px; }
  }
</style>