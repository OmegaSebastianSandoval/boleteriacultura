<h1 class="titulo-principal"><i class="fas fa-calendar-plus"></i> <?php echo $this->titlesection; ?></h1>

<div class="container-fluid">
  <div class="content-dashboard">

    <!-- Paso 1: Buscar Socio -->
    <div class="card mb-4" id="paso-buscar-socio">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-search"></i> Paso 1: Buscar Socio</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <label>Número de Carnet</label>
            <input type="text" class="form-control" id="buscar_ncar" placeholder="Ej: 12345" value="80212301">
          </div>
          <!-- <div class="col-md-4">
            <label>Documento</label>
            <input type="text" class="form-control" id="buscar_documento" placeholder="Ej: 1234567890">
          </div> -->
          <div class="col-md-4">
            <label>&nbsp;</label>
            <button type="button" class="btn btn-primary btn-block" id="btn-buscar-socio">
              <i class="fas fa-search"></i> Buscar
            </button>
          </div>
        </div>

        <!-- Información del socio encontrado -->
        <div id="socio-info" class="mt-3" style="display: none;">
          <div class="alert alert-success">
            <h6><strong>Socio Encontrado:</strong></h6>
            <p class="mb-1"><strong>Nombre:</strong> <span id="socio-nombre"></span></p>
            <p class="mb-1"><strong>Documento:</strong> <span id="socio-documento"></span></p>
            <p class="mb-1"><strong>Carnet:</strong> <span id="socio-carnet"></span></p>
            <p class="mb-1"><strong>Acción:</strong> <span id="socio-accion"></span></p>
            <p class="mb-1"><strong>Email:</strong> <span id="socio-email"></span></p>
            <p class="mb-1"><strong>Teléfono:</strong> <span id="socio-telefono"></span></p>
            <p class="mb-1"><strong>Es menor de 18:</strong> <span id="socio-menor-25"></span></p>
            <p class="mb-1"><strong>Es hijo:</strong> <span id="socio-hijo"></span></p>

            <p class="mb-0"><strong>Reservas:</strong>
              <span class="badge bg-success" id="reservas-aceptadas">0 Aceptadas</span>
              <span class="badge bg-warning" id="reservas-pendientes">0 Pendientes</span>
            </p>
          </div>
          <div class="text-center">
            <div class="spinner-border text-primary" role="status" id="loading-beneficiarios">
              <span class="visually-hidden">Cargando beneficiarios...</span>
            </div>
            <p class="mt-2 text-muted" id="loading-text">Cargando beneficiarios...</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Paso 2: Seleccionar Beneficiarios -->
    <div class="card mb-4" id="paso-seleccionar-beneficiarios" style="display: none;">
      <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-users"></i> Paso 2: Seleccionar Beneficiarios e Invitados</h5>
      </div>
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-md-12">
            <label>Seleccione la capacidad de las mesas:</label>
            <div id="capacidades-container">
              <button type="button" class="btn btn-outline-primary btn-capacidad" data-capacidad="1">1 persona</button>
              <button type="button" class="btn btn-outline-primary btn-capacidad" data-capacidad="2">2 personas</button>
              <button type="button" class="btn btn-outline-primary btn-capacidad" data-capacidad="4">4 personas</button>
              <button type="button" class="btn btn-outline-primary btn-capacidad" data-capacidad="6">6 personas</button>
              <button type="button" class="btn btn-outline-primary btn-capacidad" data-capacidad="8">8 personas</button>
              <button type="button" class="btn btn-outline-primary btn-capacidad" data-capacidad="10">10 personas</button>
            </div>
            <!-- <div class="mt-2">
              <strong>Mesas seleccionadas:</strong>
              <span id="mesas-seleccionadas-display" class="text-muted">Ninguna</span>
            </div> -->
            <!-- <div class="mt-2">
              <strong>Total de personas:</strong>
              <span id="total-personas-display" class="badge bg-primary">0</span>
            </div> -->
          </div>
        </div>

        <div class="table-responsive" id="beneficiarios-table" style="display: none;">
          <table class="table table-bordered">
            <thead class="table-light">
              <tr>
                <th>Seleccionar</th>
                <th>Documento</th>
                <th>Nombre</th>
                <th>Edad</th>
                <th>Parentesco</th>
                <th>Detalles</th>
              </tr>
            </thead>
            <tbody id="beneficiarios-tbody">
            </tbody>
          </table>
        </div>

        <div class="row mt-3">
          <div class="col-md-12 text-end">
            <button type="button" class="btn btn-success btn-block" id="btn-continuar-mesas">
              Continuar a Mesas <i class="fas fa-arrow-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Paso 3: Seleccionar Mesas -->
    <div class="card mb-4" id="paso-seleccionar-mesas" style="display: none;">
      <div class="card-header bg-warning">
        <h5 class="mb-0"><i class="fas fa-chair"></i> Paso 3: Seleccionar Mesa</h5>
      </div>
      <div class="card-body">

        <!-- 3a: Seleccionar Piso -->
        <div id="seleccion-piso">
          <h6 class="mb-3">Seleccione el piso:</h6>
          <div id="pisos-container" class="row">
            <!-- Se llenarán dinámicamente -->
          </div>
        </div>

        <!-- 3b: Seleccionar Ambiente -->
        <div id="seleccion-ambiente" style="display: none;">
          <button type="button" class="btn btn-sm btn-secondary mb-3" id="btn-volver-pisos">
            <i class="fas fa-arrow-left"></i> Volver a Pisos
          </button>
          <h6 class="mb-3">Seleccione el ambiente:</h6>
          <div id="ambientes-container" class="row">
            <!-- Se llenarán dinámicamente -->
          </div>
        </div>

        <!-- 3c: Seleccionar Mesa (Plano) -->
        <div id="seleccion-mesa" style="display: none;">
          <button type="button" class="btn btn-sm btn-secondary mb-3" id="btn-volver-ambientes">
            <i class="fas fa-arrow-left"></i> Volver a Ambientes
          </button>
          <h6 class="mb-3">Seleccione la mesa:</h6>
          <div id="mesas-plano-container" class="position-relative border rounded p-4" style="min-height: 400px; background: #f8f9fa;">
            <!-- Plano de mesas se llenará dinámicamente -->
          </div>
          <div class="mt-3 text-center">
            <button type="button" class="btn btn-success" id="btn-continuar-resumen" style="display: none;">
              Continuar al Resumen <i class="fas fa-arrow-right"></i>
            </button>
          </div>
        </div>

      </div>
    </div>

    <!-- Paso 4: Resumen y Confirmación -->
    <div class="card mb-4" id="paso-resumen" style="display: none;">
      <div class="card-header bg-success text-white">
        <h5 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Paso 4: Resumen y Confirmación</h5>
      </div>
      <div class="card-body">

        <div class="row">
          <!-- Información de la reserva -->
          <div class="col-md-6">
            <div class="card mb-3">
              <div class="card-header">
                <h6 class="mb-0">Información de la Reserva</h6>
              </div>
              <div class="card-body">
                <p class="mb-1"><strong>Socio:</strong> <span id="resumen-socio"></span></p>
                <p class="mb-1"><strong>Documento:</strong> <span id="resumen-documento"></span></p>
                <p class="mb-1"><strong>Total Personas:</strong> <span id="resumen-total-personas"></span></p>
                <p class="mb-1"><strong>Mesa:</strong> <span id="resumen-mesa"></span></p>
                <p class="mb-0"><strong>Ubicación:</strong> <span id="resumen-ubicacion"></span></p>
              </div>
            </div>
          </div>

          <!-- Lista de invitados -->
          <div class="col-md-6">
            <div class="card mb-3">
              <div class="card-header">
                <h6 class="mb-0">Lista de Invitados</h6>
              </div>
              <div class="card-body">
                <div id="resumen-invitados-lista" style="max-height: 300px; overflow-y: auto;">
                  <!-- Se llenará dinámicamente -->
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Método de pago -->
        <div class="row mt-3">
          <div class="col-md-6 mx-auto">
            <div class="card">
              <div class="card-header bg-info text-white">
                <h6 class="mb-0">Método de Pago</h6>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <label class="form-label">Seleccione el método de pago:</label>
                  <select class="form-select" id="metodo-pago-select">
                    <option value="">-- Seleccione --</option>
                    <option value="cargo">Cargo a la Acción</option>
                    <?php if (isset($this->evento->evento_datafono) && $this->evento->evento_datafono == 1): ?>
                      <option value="datafono">Datáfono</option>
                    <?php endif; ?>
                  </select>
                </div>

                <?php if (isset($this->evento->evento_cuotas) && $this->evento->evento_cuotas == 1): ?>
                  <div class="mb-3" id="cuotas-container">
                    <label class="form-label">Número de Cuotas:</label>
                    <select class="form-select" id="numero-cuotas-select">
                      <?php
                      $maxCuotas = isset($this->evento->evento_max_cuotas) ? intval($this->evento->evento_max_cuotas) : 12;
                      for ($i = 1; $i <= $maxCuotas; $i++):
                      ?>
                        <option value="<?php echo $i ?>" <?php echo $i == 1 ? 'selected' : '' ?>>
                          <?php echo $i ?> <?php echo $i == 1 ? 'Cuota (Contado)' : 'Cuotas' ?>
                        </option>
                      <?php endfor; ?>
                    </select>
                    <small class="text-muted">Seleccione el número de cuotas para el pago</small>
                  </div>
                <?php else: ?>
                  <input type="hidden" id="numero-cuotas-select" value="1">
                <?php endif; ?>

                <div class="d-grid gap-2">
                  <button type="button" class="btn btn-success btn-lg" id="btn-confirmar-reserva" disabled>
                    <i class="fas fa-check-circle"></i> Confirmar Reserva
                  </button>
                  <button type="button" class="btn btn-outline-secondary" id="btn-volver-mesas">
                    <i class="fas fa-arrow-left"></i> Volver a Selección de Mesas
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<style>
  .btn-capacidad {
    margin: 5px;
  }

  .btn-capacidad.active {
    background-color: #0d6efd;
    color: white;
  }

  /* Estilos para pisos y ambientes */
  .piso-card,
  .ambiente-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #dee2e6;
  }

  .piso-card:hover,
  .ambiente-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-color: #0d6efd;
  }

  /* Estilos para elementos del plano (sistema de posicionamiento absoluto) */
  .elemento {
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #6c757d;
    border-radius: 6px;
    font-weight: 500;
    background: white;
    transition: all 0.3s ease;
    font-size: 11px;
    overflow: hidden;
    text-align: center;
  }

  .elemento .mesa-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    padding: 2px;
  }

  .elemento .mesa-content i {
    font-size: 14px;
    margin-bottom: 2px;
  }

  /* Mesas disponibles (seleccionables) */
  .elemento.mesa.disponible {
    background: #d4edda;
    border-color: #28a745;
    color: #155724;
    cursor: pointer;
  }

  .elemento.mesa.disponible:hover {
    background: #28a745;
    color: white;
    transform: scale(1.05);
    box-shadow: 0 3px 10px rgba(40, 167, 69, 0.5);
    z-index: 10;
  }

  /* Mesas ocupadas */
  .elemento.mesa.ocupada {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
    cursor: not-allowed;
    opacity: 0.7;
  }

  /* Mesa seleccionada */
  .elemento.seleccionada {
    background: #0d6efd !important;
    border-color: #0a58ca !important;
    color: white !important;
    transform: scale(1.08) !important;
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.6) !important;
    z-index: 20 !important;
  }

  /* Paredes (decoración visual, no seleccionable) */
  .elemento.pared {
    background: #6c757d;
    border-color: #495057;
    color: white;
    cursor: default;
    opacity: 0.8;
  }

  /* Decoraciones (elementos decorativos, no seleccionables) */
  .elemento.decoracion {
    background: #e9ecef;
    border-color: #adb5bd;
    color: #6c757d;
    cursor: default;
    opacity: 0.6;
  }

  /* Container del plano con posicionamiento relativo */
  #mesas-plano-container {
    position: relative;
    overflow: auto;
    max-width: 100%;
  }
</style>

<script>
  (function() {
    'use strict';

    // Variables globales
    let socioData = null;
    let beneficiariosData = [];
    let totalPersonas = 0;
    let pisoSeleccionado = null;
    let ambienteSeleccionado = null;
    let mesaSeleccionada = null;
    let mesasData = [];

    document.addEventListener('DOMContentLoaded', function() {
      initReservaScript();
    });

    function initReservaScript() {
      // Paso 1: Buscar socio
      document.getElementById('btn-buscar-socio').addEventListener('click', buscarSocio);

      // Paso 2: Seleccionar capacidad y beneficiarios
      document.getElementById('capacidades-container').addEventListener('click', seleccionarCapacidad);
      document.getElementById('btn-continuar-mesas').addEventListener('click', continuarAMesas);

      // Paso 3: Navegación mesas
      document.getElementById('btn-volver-pisos')?.addEventListener('click', () => mostrarSeccion('piso'));
      document.getElementById('btn-volver-ambientes')?.addEventListener('click', () => mostrarSeccion('ambiente'));
      document.getElementById('btn-continuar-resumen')?.addEventListener('click', mostrarResumen);

      // Paso 4: Resumen
      document.getElementById('metodo-pago-select')?.addEventListener('change', function() {
        validarMetodoPago();
        manejarCuotas(this.value);
      });
      document.getElementById('btn-confirmar-reserva')?.addEventListener('click', confirmarReserva);
      document.getElementById('btn-volver-mesas')?.addEventListener('click', volverAMesas);
    }

    function manejarCuotas(metodoPago) {
      const cuotasContainer = document.getElementById('cuotas-container');
      const cuotasSelect = document.getElementById('numero-cuotas-select');

      if (!cuotasContainer) return; // Si no hay cuotas configuradas en el evento

      // Mostrar cuotas solo si es cargo a la acción
      if (metodoPago === 'cargo') {
        cuotasContainer.style.display = 'block';
      } else {
        cuotasContainer.style.display = 'none';
        // Reset a 1 cuota si no es cargo
        cuotasSelect.value = '1';
      }
    }

    function buscarSocio() {
      const ncar = document.getElementById('buscar_ncar').value.trim();

      if (!ncar) {
        alert('Debe ingresar el número de carnet');
        return;
      }

      const formData = new FormData();
      formData.append('ncar', ncar);

      fetch('/administracion/reservas/buscarsocio', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(response => {
          if (response.success) {
            socioData = response.data;
            document.getElementById('socio-nombre').textContent = socioData.sbe_nomb + ' ' + (socioData.sbe_apel || '');
            document.getElementById('socio-documento').textContent = socioData.SBE_CODI;
            document.getElementById('socio-accion').textContent = socioData.MAC_NUME || 'N/A';
            document.getElementById('socio-carnet').textContent = ncar;
            document.getElementById('socio-email').textContent = socioData.sbe_mail || 'N/A';
            document.getElementById('socio-telefono').textContent = socioData.sbe_ncel || 'N/A';
            document.getElementById('socio-menor-25').textContent = socioData.menor25 ? "Sí" : "No";
            document.getElementById('socio-hijo').textContent = socioData.hijo ? "Sí" : "No";
            document.getElementById('reservas-aceptadas').textContent = response.reservas.aceptadas + ' Aceptadas';
            document.getElementById('reservas-pendientes').textContent = response.reservas.pendientes + ' Pendientes';

            const socioInfo = document.getElementById('socio-info');
            fadeIn(socioInfo);

            // Cargar automáticamente los beneficiarios
            cargarBeneficiarios();
          } else {
            alert(response.message || 'No se encontró el socio');
          }
        })
        .catch(error => {
          console.error('Error Fetch:', error);
          alert('Error al buscar el socio');
        });
    }

    function cargarBeneficiarios() {
      if (!socioData || !socioData.MAC_NUME) {
        alert('Debe buscar un socio primero');
        return;
      }

      const formData = new FormData();
      formData.append('mac_nume', socioData.MAC_NUME);

      fetch('/administracion/reservas/cargarbeneficiarios', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(response => {
          if (response.success) {
            beneficiariosData = response.data;

            // Ocultar el spinner de carga
            document.getElementById('loading-beneficiarios').style.display = 'none';
            document.getElementById('loading-text').style.display = 'none';

            cargarTablaBeneficiarios();

            const pasoBeneficiarios = document.getElementById('paso-seleccionar-beneficiarios');
            fadeIn(pasoBeneficiarios);

            setTimeout(() => {
              pasoBeneficiarios.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
              });
            }, 400);
          } else {
            // Ocultar spinner en caso de error también
            document.getElementById('loading-beneficiarios').style.display = 'none';
            document.getElementById('loading-text').style.display = 'none';
            alert(response.message || 'Error al cargar beneficiarios');
          }
        })
        .catch(error => {
          // Ocultar spinner en caso de error
          document.getElementById('loading-beneficiarios').style.display = 'none';
          document.getElementById('loading-text').style.display = 'none';
          console.error('Error Fetch:', error);
          alert('Error al cargar beneficiarios');
        });
    }

    function cargarTablaBeneficiarios() {
      const tbody = document.getElementById('beneficiarios-tbody');
      tbody.innerHTML = '';

      // Agregar todos los beneficiarios (incluyendo el socio)
      beneficiariosData.forEach(function(b, index) {
        if (b.edad < 18) return; // Filtrar menores de 18

        const esSocio = b.documento === socioData.SBE_CODI;
        const estaBloqueado = b.bloqueado === true && !esSocio;
        const claseFilaSocio = esSocio ? 'table-primary' : (estaBloqueado ? 'table-danger' : '');
        const badgeSocio = esSocio ? '<span class="badge bg-primary">SOCIO</span>' : '';
        const badgeBloqueado = estaBloqueado ? '<span class="badge bg-danger ms-1" title="Ya tiene una reserva activa">BLOQUEADO</span>' : '';
        const checked = esSocio ? 'checked' : '';
        const disabled = estaBloqueado ? 'disabled data-bloqueado="true"' : 'disabled'; // Todos deshabilitados inicialmente hasta que se seleccione capacidad
        const claseSocioCheck = esSocio ? 'socio-check' : '';

        const row = `
          <tr class="${claseFilaSocio}" style="opacity: ${(esSocio || !estaBloqueado) ? '0.5' : '0.4'}">
            <td><input type="checkbox" class="beneficiario-check ${claseSocioCheck}" data-index="${index}" data-bloqueado="${estaBloqueado}" ${checked} ${disabled}></td>
            <td>${b.documento}</td>
            <td>${b.nombre} ${b.apellido || ''} ${badgeSocio}${badgeBloqueado}</td>
            <td>${b.edad || 'N/A'}</td>
            <td>${b.parentesco || 'N/A'}</td>
            <td>
              ${b.menor25 ? '<span class="badge bg-info">Menor 25</span>' : ''}
              ${b.hijo ? '<span class="badge bg-success">Hijo</span>' : ''}
            </td>
          </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
      });

      const beneficiariosTable = document.getElementById('beneficiarios-table');
      fadeIn(beneficiariosTable);

      // Agregar evento para validar selección de checkboxes
      tbody.addEventListener('change', function(e) {
        if (e.target.classList.contains('beneficiario-check')) {
          validarSeleccion();
        }
      });
    }

    function validarSeleccion() {
      if (totalPersonas === 0) return;

      const checksSeleccionados = document.querySelectorAll('.beneficiario-check:checked');
      const cantidadSeleccionados = checksSeleccionados.length;

      // Bloquear/desbloquear checkboxes (sin tocar los bloqueados por reserva activa)
      const checksNoSeleccionados = document.querySelectorAll('.beneficiario-check:not(:checked):not(.socio-check)');

      if (cantidadSeleccionados >= totalPersonas) {
        checksNoSeleccionados.forEach(check => {
          if (check.getAttribute('data-bloqueado') === 'true') return;
          check.disabled = true;
          check.parentElement.parentElement.style.opacity = '0.5';
        });
      } else {
        checksNoSeleccionados.forEach(check => {
          if (check.getAttribute('data-bloqueado') === 'true') return;
          check.disabled = false;
          check.parentElement.parentElement.style.opacity = '1';
        });
      }

      actualizarMensajeSeleccion();
    }

    function actualizarMensajeSeleccion() {
      const checksSeleccionados = document.querySelectorAll('.beneficiario-check:checked');
      const cantidadSeleccionados = checksSeleccionados.length;

      let mensajeDiv = document.getElementById('mensaje-validacion');
      if (!mensajeDiv) {
        mensajeDiv = document.createElement('div');
        mensajeDiv.id = 'mensaje-validacion';
        mensajeDiv.className = 'alert mt-3';
        document.querySelector('#paso-seleccionar-beneficiarios .card-body').appendChild(mensajeDiv);
      }

      if (totalPersonas > 0) {
        const invitadosAutomaticos = totalPersonas - cantidadSeleccionados;
        mensajeDiv.style.display = 'block';

        if (invitadosAutomaticos === 0) {
          mensajeDiv.className = 'alert alert-success mt-3';
          mensajeDiv.innerHTML = `<strong>✓ Perfecto!</strong> Ha seleccionado ${cantidadSeleccionados} beneficiarios de ${totalPersonas} personas.`;
        } else if (invitadosAutomaticos > 0) {
          mensajeDiv.className = 'alert alert-info mt-3';
          mensajeDiv.innerHTML = `<strong>ℹ Información:</strong> Ha seleccionado ${cantidadSeleccionados} beneficiarios. Se agregarán automáticamente ${invitadosAutomaticos} invitado(s) para completar ${totalPersonas} personas.`;
        }
      } else {
        if (mensajeDiv) {
          mensajeDiv.style.display = 'none';
        }
      }
    }

    function seleccionarCapacidad(e) {
      const boton = e.target.closest('.btn-capacidad');
      if (!boton) return;

      e.preventDefault();

      // Remover clase active de todos los botones
      document.querySelectorAll('.btn-capacidad').forEach(btn => {
        btn.classList.remove('active');
      });

      // Agregar clase active al botón clickeado
      boton.classList.add('active');

      // Obtener capacidad seleccionada
      totalPersonas = parseInt(boton.getAttribute('data-capacidad'));

      // Habilitar checkboxes (excepto el del socio y los bloqueados)
      const checks = document.querySelectorAll('.beneficiario-check:not(.socio-check)');
      checks.forEach(check => {
        if (check.getAttribute('data-bloqueado') === 'true') return;
        check.checked = false;
        check.disabled = false;
        check.parentElement.parentElement.style.opacity = '1';
      });

      // El socio siempre debe estar visible
      const checkSocio = document.querySelector('.beneficiario-check.socio-check');
      if (checkSocio) {
        checkSocio.parentElement.parentElement.style.opacity = '1';
      }

      // Actualizar mensaje
      actualizarMensajeSeleccion();
    }

    function continuarAMesas() {
      const beneficiariosSeleccionados = [];
      const checksSeleccionados = document.querySelectorAll('.beneficiario-check:checked');

      checksSeleccionados.forEach(function(check) {
        const index = parseInt(check.getAttribute('data-index'));
        beneficiariosSeleccionados.push(beneficiariosData[index]);
      });

      if (totalPersonas === 0) {
        alert('Debe seleccionar al menos una capacidad de mesa');
        return;
      }

      const cantidadInvitados = totalPersonas - beneficiariosSeleccionados.length;

      if (cantidadInvitados < 0) {
        alert('Error en la selección.');
        return;
      }

      // Guardar datos en el servidor
      const formData = new FormData();
      formData.append('socio', JSON.stringify(socioData));
      formData.append('beneficiarios_seleccionados', JSON.stringify(beneficiariosSeleccionados));
      formData.append('cantidad_no_asociados', cantidadInvitados);
      formData.append('capacidades_mesas', JSON.stringify([totalPersonas]));

      fetch('/administracion/reservas/guardardatosreserva', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(response => {
          if (response.success) {
            // Mostrar paso de mesas y cargar pisos
            const pasoMesas = document.getElementById('paso-seleccionar-mesas');
            fadeIn(pasoMesas);
            cargarPisos();

            setTimeout(() => {
              pasoMesas.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
              });
            }, 400);
          } else {
            alert(response.message || 'Error al guardar datos');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error al guardar datos');
        });
    }

    // === FUNCIONES PASO 3: MESAS ===

    function cargarPisos() {
      const formData = new FormData();
      formData.append('capacidad', totalPersonas);

      fetch('/administracion/reservas/obtenerpisos', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(response => {
          if (response.success && response.data.length > 0) {
            mostrarPisos(response.data);
          } else {
            alert('No hay pisos con mesas disponibles para esta capacidad');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error al cargar pisos');
        });
    }

    function mostrarPisos(pisos) {
      const container = document.getElementById('pisos-container');
      container.innerHTML = '';

      pisos.forEach(piso => {
        const col = document.createElement('div');
        col.className = 'col-md-4 col-sm-6 mb-3';
        col.innerHTML = `
          <div class="card piso-card h-100" data-piso-id="${piso.piso_id}">
            <div class="card-body text-center">
              <i class="fas fa-building fa-3x mb-3 text-primary"></i>
              <h5 class="card-title">${piso.piso_nombre}</h5>
              <span class="badge bg-success">${piso.total_mesas} mesas disponibles</span>
            </div>
          </div>
        `;

        col.querySelector('.piso-card').addEventListener('click', function() {
          seleccionarPiso(piso);
        });

        container.appendChild(col);
      });

      mostrarSeccion('piso');
    }

    function seleccionarPiso(piso) {
      pisoSeleccionado = piso;
      cargarAmbientes(piso.piso_id);
    }

    function cargarAmbientes(pisoId) {
      const formData = new FormData();
      formData.append('piso_id', pisoId);
      formData.append('capacidad', totalPersonas);

      fetch('/administracion/reservas/obtenerambientes', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(response => {
          if (response.success && response.data.length > 0) {
            mostrarAmbientes(response.data);
          } else {
            alert('No hay ambientes con mesas disponibles en este piso');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error al cargar ambientes');
        });
    }

    function mostrarAmbientes(ambientes) {
      const container = document.getElementById('ambientes-container');
      container.innerHTML = '';

      ambientes.forEach(ambiente => {
        const col = document.createElement('div');
        col.className = 'col-md-4 col-sm-6 mb-3';
        col.innerHTML = `
          <div class="card ambiente-card h-100" data-ambiente-id="${ambiente.ambiente_id}">
            <div class="card-body text-center">
              <i class="fas fa-door-open fa-3x mb-3 text-info"></i>
              <h5 class="card-title">${ambiente.ambiente_nombre}</h5>
              <p class="text-muted mb-2">${ambiente.categoria_nombre || 'Sin categoría'}</p>
              <span class="badge bg-success">${ambiente.total_mesas} mesas disponibles</span>
            </div>
          </div>
        `;

        col.querySelector('.ambiente-card').addEventListener('click', function() {
          seleccionarAmbiente(ambiente);
        });

        container.appendChild(col);
      });

      mostrarSeccion('ambiente');
    }

    function seleccionarAmbiente(ambiente) {
      // Estructurar el ambiente con su categoría completa
      ambienteSeleccionado = {
        ...ambiente,
        categoria: {
          categoria_id: ambiente.categoria_id,
          categoria_nombre: ambiente.categoria_nombre,
          categoria_descripcion: ambiente.categoria_descripcion,
          categoria_precio_socio: ambiente.categoria_precio_socio,
          categoria_precio_socio_hijo: ambiente.categoria_precio_socio_hijo,
          categoria_precio_invitado: ambiente.categoria_precio_invitado
        }
      };
      cargarMesas(ambiente.ambiente_id);
    }

    function cargarMesas(ambienteId) {
      const formData = new FormData();
      formData.append('ambiente_id', ambienteId);
      formData.append('capacidad', totalPersonas);

      fetch('/administracion/reservas/obtenermesasdisponibles', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(response => {
          if (response.success) {
            mesasData = response.mesas_disponibles || [];
            mostrarPlanoMesas(response);
          } else {
            alert('No hay mesas disponibles con esa capacidad en este ambiente');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error al cargar mesas');
        });
    }

    function mostrarPlanoMesas(data) {
      const container = document.getElementById('mesas-plano-container');
      container.innerHTML = '';

      const mesasDisponibles = data.mesas_disponibles || [];
      const todosElementos = data.todos_elementos || [];
      const ambiente = data.ambiente;

      if (!ambiente || todosElementos.length === 0) {
        container.innerHTML = '<p class="text-center text-muted">No hay elementos disponibles</p>';
        return;
      }

      // Configuración del grid con posicionamiento absoluto (como en page)
      const filas = parseInt(ambiente.ambiente_filas) || 10;
      const columnas = parseInt(ambiente.ambiente_columnas) || 10;
      const cellSize = 50; // Tamaño de cada celda en px
      const gap = 2; // Espacio entre celdas

      // Configurar el contenedor con posicionamiento relativo para los absolutos
      container.style.position = 'relative';
      container.style.width = (columnas * (cellSize + gap)) + 'px';
      container.style.height = (filas * (cellSize + gap)) + 'px';
      container.style.margin = '0 auto';
      container.style.border = '2px solid #ddd';
      container.style.background = '#f8f9fa';

      // Crear array de IDs de mesas disponibles para búsqueda rápida
      const mesasDisponiblesIds = mesasDisponibles.map(m => m.mesa_id);

      // Renderizar todos los elementos
      todosElementos.forEach(elemento => {
        // Obtener posición y dimensiones
        const posX = parseInt(elemento.mesa_pos_x);
        const posY = parseInt(elemento.mesa_pos_y);
        const ancho = parseInt(elemento.mesa_ancho) || 1;
        const alto = parseInt(elemento.mesa_alto) || 1;
        const rotacion = parseInt(elemento.mesa_rotacion) || 0;

        // Validar posición (debe estar dentro del grid)
        if (isNaN(posX) || isNaN(posY) || posX < 0 || posY < 0 || posX >= columnas || posY >= filas) {
          console.warn('Elemento sin posición válida:', elemento.mesa_nombre, 'X:', posX, 'Y:', posY);
          return;
        }

        const mesaDiv = document.createElement('div');
        const mesaTipo = elemento.mesa_tipo || 'mesa';
        const esDisponible = mesasDisponiblesIds.includes(elemento.mesa_id);

        // Determinar clases CSS según tipo y disponibilidad
        let clases = ['elemento', mesaTipo];

        if (mesaTipo === 'mesa') {
          clases.push(esDisponible ? 'disponible' : 'ocupada');
        }

        mesaDiv.className = clases.join(' ');
        mesaDiv.setAttribute('data-mesa-id', elemento.mesa_id);

        // Posicionar con absolute (como en page)
        mesaDiv.style.position = 'absolute';
        mesaDiv.style.width = (cellSize * ancho + gap * (ancho - 1)) + 'px';
        mesaDiv.style.height = (cellSize * alto + gap * (alto - 1)) + 'px';
        mesaDiv.style.left = (posX * (cellSize + gap)) + 'px';
        mesaDiv.style.top = (posY * (cellSize + gap)) + 'px';
        mesaDiv.style.transform = `rotate(${rotacion}deg)`;

        // Contenido según tipo
        let icono = 'fa-chair';
        if (mesaTipo === 'pared') icono = 'fa-square';
        if (mesaTipo === 'decoracion') icono = 'fa-circle';

        mesaDiv.innerHTML = `
          <div class="mesa-content">
            <i class="fas ${icono}"></i>
            <div><small>${elemento.mesa_nombre || ''}</small></div>
          </div>
        `;

        // Solo hacer clic en mesas disponibles
        if (mesaTipo === 'mesa' && esDisponible) {
          mesaDiv.style.cursor = 'pointer';
          mesaDiv.dataset.seleccionable = 'true';
          mesaDiv.addEventListener('click', function(e) {
            e.stopPropagation(); // Evitar propagación del evento
            seleccionarMesa(elemento, mesaDiv);
          }, false);
        } else {
          mesaDiv.style.cursor = 'default';
          mesaDiv.dataset.seleccionable = 'false';
        }

        container.appendChild(mesaDiv);
      });

      mostrarSeccion('mesa');
    }

    function seleccionarMesa(mesa, elemento) {
      // Si hace clic en la misma mesa, deseleccionar
      if (elemento.classList.contains('seleccionada')) {
        elemento.classList.remove('seleccionada');
        mesaSeleccionada = null;
        document.getElementById('btn-continuar-resumen').style.display = 'none';
        return;
      }

      // Quitar TODAS las selecciones anteriores
      const seleccionadas = document.querySelectorAll('.elemento.seleccionada');
      seleccionadas.forEach(m => {
        m.classList.remove('seleccionada');
      });

      // Marcar nueva selección (solo una)
      elemento.classList.add('seleccionada');

      // Guardar mesa con toda la información incluyendo categoría y descuento del ambiente
      mesaSeleccionada = {
        ...mesa,
        categoria: ambienteSeleccionado.categoria,
        ambiente_descuento: ambienteSeleccionado.ambiente_descuento
      };

      // Mostrar botón continuar
      document.getElementById('btn-continuar-resumen').style.display = 'inline-block';
    }

    function mostrarSeccion(seccion) {
      document.getElementById('seleccion-piso').style.display = 'none';
      document.getElementById('seleccion-ambiente').style.display = 'none';
      document.getElementById('seleccion-mesa').style.display = 'none';

      if (seccion === 'piso') {
        document.getElementById('seleccion-piso').style.display = 'block';
      } else if (seccion === 'ambiente') {
        document.getElementById('seleccion-ambiente').style.display = 'block';
      } else if (seccion === 'mesa') {
        document.getElementById('seleccion-mesa').style.display = 'block';
      }
    }

    // === FUNCIONES PASO 4: RESUMEN ===

    function mostrarResumen() {
      if (!mesaSeleccionada) {
        alert('Debe seleccionar una mesa');
        return;
      }

      // Llenar datos del resumen
      const checksSeleccionados = document.querySelectorAll('.beneficiario-check:checked');
      const beneficiariosSeleccionados = [];

      checksSeleccionados.forEach(check => {
        const index = parseInt(check.getAttribute('data-index'));
        beneficiariosSeleccionados.push(beneficiariosData[index]);
      });

      const cantidadInvitados = totalPersonas - beneficiariosSeleccionados.length;

      document.getElementById('resumen-socio').textContent = socioData.sbe_nomb + ' ' + (socioData.sbe_apel || '');
      document.getElementById('resumen-documento').textContent = socioData.SBE_CODI;
      document.getElementById('resumen-total-personas').textContent = totalPersonas;
      document.getElementById('resumen-mesa').textContent = mesaSeleccionada.mesa_nombre + ' (Cap: ' + mesaSeleccionada.mesa_capacidad + ')';
      document.getElementById('resumen-ubicacion').textContent = pisoSeleccionado.piso_nombre + ' - ' + ambienteSeleccionado.ambiente_nombre;

      // Obtener información de la categoría y precios
      const categoria = mesaSeleccionada.categoria;
      const precioSocio = parseFloat(categoria?.categoria_precio_socio || 0);
      const precioSocioHijo = parseFloat(categoria?.categoria_precio_socio_hijo || 0);
      const precioInvitado = parseFloat(categoria?.categoria_precio_invitado || 0);
      const descuento = parseFloat(mesaSeleccionada.ambiente_descuento || 0);

      // Lista de invitados
      const listaContainer = document.getElementById('resumen-invitados-lista');
      listaContainer.innerHTML = '';

      let totalGeneral = 0;
      let contador = 1;

      beneficiariosSeleccionados.forEach(b => {
        const esSocio = (b.documento === socioData.SBE_CODI);
        const esMenor25 = b.menor25 || false;
        const esHijo = b.hijo || false;

        // Calcular precio según tipo
        let precio = 0;
        let tipoParticipante = '';

        if (esMenor25 && esHijo) {
          precio = precioSocioHijo;
          tipoParticipante = 'Beneficiario Hijo < 25';
        } else {
          precio = precioSocio;
          tipoParticipante = esSocio ? 'Socio Principal' : 'Beneficiario';
        }

        totalGeneral += precio;

        const badge = esSocio ? '<span class="badge bg-primary ms-2">SOCIO</span>' : '';

        listaContainer.innerHTML += `
          <div class="mb-2 pb-2 border-bottom d-flex justify-content-between align-items-center">
            <div>
              <strong>${contador}.</strong> ${b.nombre} ${b.apellido || ''} ${badge}<br>
              <small class="text-muted">${tipoParticipante}</small>
            </div>
            <span class="badge bg-success rounded-pill fs-6">$${precio.toLocaleString('es-CO')}</span>
          </div>
        `;
        contador++;
      });

      // Agregar invitados
      for (let i = 1; i <= cantidadInvitados; i++) {
        totalGeneral += precioInvitado;

        listaContainer.innerHTML += `
          <div class="mb-2 pb-2 border-bottom d-flex justify-content-between align-items-center">
            <div>
              <strong>${contador}.</strong> Invitado ${i} <span class="badge bg-secondary ms-2">PENDIENTE</span><br>
              <small class="text-muted">Invitado</small>
            </div>
            <span class="badge bg-info rounded-pill fs-6">$${precioInvitado.toLocaleString('es-CO')}</span>
          </div>
        `;
        contador++;
      }

      // Calcular total con descuento si aplica
      let totalConDescuento = totalGeneral;
      if (descuento > 0) {
        totalConDescuento = totalGeneral - (totalGeneral * (descuento / 100));
      }

      // Mostrar totales
      let totalesHTML = `
        <div class="mt-3 pt-3 border-top">
          <div class="d-flex justify-content-between mb-2">
            <strong>Subtotal:</strong>
            <strong>$${totalGeneral.toLocaleString('es-CO')}</strong>
          </div>
      `;

      if (descuento > 0) {
        totalesHTML += `
          <div class="d-flex justify-content-between mb-2 text-success">
            <span>Descuento (${descuento}%):</span>
            <span>-$${(totalGeneral - totalConDescuento).toLocaleString('es-CO')}</span>
          </div>
        `;
      }

      totalesHTML += `
          <div class="d-flex justify-content-between bg-light p-2 rounded">
            <strong class="text-primary fs-5">TOTAL A PAGAR:</strong>
            <strong class="text-primary fs-4">$${totalConDescuento.toLocaleString('es-CO')}</strong>
          </div>
        </div>
      `;

      listaContainer.insertAdjacentHTML('beforeend', totalesHTML);

      // Mostrar paso resumen
      const pasoResumen = document.getElementById('paso-resumen');
      fadeIn(pasoResumen);

      setTimeout(() => {
        pasoResumen.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }, 400);
    }

    function validarMetodoPago() {
      const metodo = document.getElementById('metodo-pago-select').value;
      document.getElementById('btn-confirmar-reserva').disabled = !metodo;
    }

    function confirmarReserva() {
      const metodoPago = document.getElementById('metodo-pago-select').value;
      const numeroCuotasElement = document.getElementById('numero-cuotas-select');
      const numeroCuotas = numeroCuotasElement ? numeroCuotasElement.value : '1';

      if (!metodoPago) {
        alert('Debe seleccionar un método de pago');
        return;
      }

      // Solo validar cuotas si el contenedor está visible (evento tiene cuotas habilitadas)
      const cuotasContainer = document.getElementById('cuotas-container');
      if (cuotasContainer && cuotasContainer.style.display !== 'none' && !numeroCuotas) {
        alert('Debe seleccionar el número de cuotas');
        return;
      }

      if (!confirm('¿Está seguro de confirmar esta reserva?')) {
        return;
      }

      const formData = new FormData();
      formData.append('mesa_id', mesaSeleccionada.mesa_id);
      formData.append('metodo_pago', metodoPago);
      formData.append('numero_cuotas', numeroCuotas);

      fetch('/administracion/reservas/crearreserva', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(response => {
          if (response.success) {
            alert('¡Reserva creada exitosamente! ID: ' + response.reserva_id);
            // Recargar página o redirigir
            window.location.href = '/administracion/reservas/manage?id=' + response.reserva_id;
          } else {
            alert('Error: ' + response.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Error al crear la reserva');
        });
    }

    function volverAMesas() {
      document.getElementById('paso-resumen').style.display = 'none';
      document.getElementById('paso-seleccionar-mesas').style.display = 'block';
    }

    function fadeIn(element) {
      element.style.display = 'block';
      element.style.opacity = '0';

      let opacity = 0;
      const interval = setInterval(function() {
        opacity += 0.1;
        element.style.opacity = opacity;

        if (opacity >= 1) {
          clearInterval(interval);
          element.style.opacity = '1';
        }
      }, 40);
    }
  })();
</script>