<style>
  :root {
    --primary-color: #192a4b;
    --accent-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --border-radius: 12px;
    --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
  }

  body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    height: auto;
  }

  .main-content {
    min-height: calc(100dvh - 196.5px);
    padding: 1rem 0;
  }

  /* Header Section */
  .header-section {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    position: sticky;
    top: 0;
    z-index: 1000;
  }

  .brand-section h4 {
    color: var(--primary-color);
  }

  /* Info Panel (Izquierda) */
  .info-panel {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    box-shadow: var(--box-shadow);
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
  }

  .welcome-section {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e2e8f0;
  }

  .welcome-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color) 0%, #2d3748 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    flex-shrink: 0;
  }

  .welcome-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    color: var(--primary-color);
  }

  .welcome-subtitle {
    font-size: 0.95rem;
    margin: 0.25rem 0 0 0;
    color: #666;
  }

  .method-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    transition: var(--transition);
  }

  .method-item:hover {
    transform: translateX(5px);
    border-color: var(--primary-color);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  }

  .method-icon-small {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    flex-shrink: 0;
  }

  .method-content h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: var(--primary-color);
  }

  .method-content p {
    margin: 0.25rem 0 0 0;
    color: #666;
    font-size: 0.85rem;
  }

  /* Lista de invitados panel */
  .invitados-panel {
    background: white;
    border-radius: var(--border-radius);
    height: 100%;
    box-shadow: var(--box-shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .invitados-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #2d3748 100%);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
  }

  .invitados-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
  }

  .invitados-container {
    flex: 1;
    padding: 1.5rem;
    background: #f8f9fa;
    overflow-y: auto;
    display: grid;
    align-items: center;
  }

  /* Estilos para invitados incompletos con inputs */
  .invitado-incompleto {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
  }

  .invitado-incompleto .form-control {
    margin-bottom: 0;
    border: 0;
    border-radius: 6px;
    transition: var(--transition);
  }

  .invitado-incompleto .form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(25, 42, 75, 0.25);
  }

  .btn-save-invitado {
    background: var(--accent-color);
    border: none;
    color: white;
    padding: 0.4rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
    transition: var(--transition);
  }

  .btn-save-invitado:hover {
    background: #218838;
    color: white !important;
    transform: translateY(-1px);
  }

  /* Botón de generar boleta */
  .btn-sm {
    font-size: 0.8rem;
    padding: 0.3rem 0.6rem;
  }

  .btn-back {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: var(--border-radius);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition);
    box-shadow: var(--box-shadow);
  }

  .btn-back:hover {
    background: #1a365d;
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
  }

  /* Responsive Design */
  @media (max-width: 992px) {
    .main-content {
      height: auto;
      overflow: visible;
    }

    .info-panel,
    .invitados-panel {
      height: auto;
      margin-bottom: 1rem;
    }
  }

  @media (max-width: 768px) {
    .welcome-section {
      flex-direction: column;
      text-align: center;
      gap: 0.5rem;
    }

    .welcome-title {
      font-size: 1.25rem;
    }

    .method-item {
      flex-direction: column;
      text-align: center;
      gap: 0.5rem;
    }
  }

  /* Estilos para el modal de selección de reservas */
  .reserva-item {
    cursor: pointer;
    transition: var(--transition);
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.5rem;
  }

  .reserva-item:hover {
    background-color: #f8f9fa;
    border-color: var(--primary-color);
    transform: translateX(5px);
  }

  .reserva-item:last-child {
    margin-bottom: 0;
  }

  .reserva-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 0.5rem;
  }

  .reserva-id {
    font-weight: bold;
    color: var(--primary-color);
    font-size: 1.1rem;
  }

  .reserva-fecha {
    color: #666;
    font-size: 0.9rem;
  }

  .reserva-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
  }

  .info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
  }

  .info-item i {
    width: 16px;
    color: var(--primary-color);
  }

  .mesas-info {
    margin-top: 0.5rem;
  }

  .mesa-tag {
    background: #e2e8f0;
    color: #333;
    padding: 0.2rem 0.5rem;
    border-radius: 4px;
    font-size: 0.8rem;
    margin-right: 0.5rem;
    margin-bottom: 0.2rem;
    display: inline-block;
  }

  .status-badge {
    padding: 0.2rem 0.6rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
  }

  .status-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
  }

  .status-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  }

  /* Estilos para la información de la reserva */
  .card {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .card-header {
    border-radius: 8px 8px 0 0;
    border-bottom: 1px solid #dee2e6;
  }

  .card-body .info-item {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    color: #333;
  }

  .card-body .info-item i {
    width: 20px;
    flex-shrink: 0;
  }

  .card-body .mesa-tag {
    background: var(--primary-color);
    color: white;
    padding: 0.3rem 0.6rem;
    border-radius: 6px;
    font-size: 0.8rem;
    margin-right: 0.5rem;
    margin-bottom: 0.3rem;
    display: inline-block;
    font-weight: 500;
  }

  body.swal2-shown:not(.swal2-no-backdrop, .swal2-toast-shown) {
    height: auto !important;
  }
</style>

<?php
// Mostrar mensajes flash si existen
$flashMessage = Session::getInstance()->get('flash_message');
$flashType = Session::getInstance()->get('flash_type');

if ($flashMessage) {
  Session::getInstance()->set('flash_message', null);
  Session::getInstance()->set('flash_type', null);

  $alertClass = $flashType === 'error' ? 'alert-danger' : 'alert-success';
  echo '<div class="container mt-3">';
  echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
  echo '<i class="fas fa-' . ($flashType === 'error' ? 'exclamation-triangle' : 'check-circle') . ' me-2"></i>';
  echo htmlspecialchars($flashMessage);
  echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
  echo '</div>';
  echo '</div>';
}
?>

<!-- Header con navegación -->
<div class="header-section">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center py-3">

      <div class="brand-section d-flex align-items-center gap-3">
        <a href="/validacion/evento" class="btn-back">
          <i class="fas fa-arrow-left me-2"></i> Volver al inicio
        </a>
        <h4 class="mb-0 text-primary fw-bold">
          <i class="fas fa-shield-check me-2"></i>
          Editar invitados
        </h4>
      </div>

    </div>
  </div>
</div>
<div class="container p-0">

  <div class="main-content">
    <div class="container-fluid">
      <div class="row g-4 h-100">
        <!-- Columna izquierda - Información y bienvenida -->
        <div class="col-12 col-xl-4">
          <div class="info-panel">
            <div class="welcome-section">
              <div class="welcome-icon">
                <i class="fas fa-edit"></i>
              </div>
              <div>
                <h3 class="welcome-title">Editar Reserva</h3>
                <p class="welcome-subtitle">Ingrese el número de reserva para consultar y editar</p>
              </div>
            </div>

            <div class="methods-section">
              <div class="method-item">
                <div class="method-content w-100">
                  <h5>Consultar Reserva</h5>
                  <p>Ingrese el número de reserva para buscar información</p>
                  <div class="mt-3">
                    <input type="number" id="numero-reserva" class="form-control"
                      placeholder="Número de reserva, carnet o documento"
                      style="border-radius: 8px; border: 2px solid #e2e8f0;">
                    <button id="consultar-reserva" class="btn btn-primary mt-2 w-100"
                      style="border-radius: 8px;">Consultar Reserva</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Columna derecha - Lista de invitados -->
        <div class="col-12 col-xl-8">
          <div class="invitados-panel">
            <div class="invitados-header">
              <h3>Invitados de la Reserva</h3>
            </div>
            <div class="invitados-container">
              <div id="invitados-list" class="w-100">
                <div class="text-center text-muted">
                  <i class="fas fa-users fa-3x mb-3"></i>
                  <p>Seleccione una reserva para ver los invitados</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para seleccionar reserva cuando hay múltiples coincidencias -->
<div class="modal fade" id="modalSeleccionReserva" tabindex="-1" aria-labelledby="modalSeleccionReservaLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalSeleccionReservaLabel">
          <i class="fas fa-search me-2"></i>
          Múltiples reservas encontradas
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="mb-3">Se encontraron múltiples reservas. Por favor seleccione la que desea editar:</p>
        <div id="reservas-lista" class="list-group">
          <!-- Las reservas se cargarán aquí dinámicamente -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<script>
  let currentReservaId = null; // Variable global para almacenar el ID de la reserva actual

  document.getElementById('consultar-reserva').addEventListener('click', function() {
    const numeroReserva = document.getElementById('numero-reserva').value.trim();

    if (!numeroReserva) {
      Swal.fire({
        icon: 'warning',
        title: 'Campo requerido',
        text: 'Por favor ingrese el número de reserva',
        confirmButtonText: 'Entendido'
      });
      return;
    }

    // Mostrar loading
    document.getElementById('invitados-list').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Cargando...</p></div>';

    // Consultar reserva
    fetch('/validacion/editarreserva/consultarreserva', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `numero_reserva=${encodeURIComponent(numeroReserva)}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          document.getElementById('invitados-list').innerHTML = '<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>' + data.error + '</p></div>';
          return;
        }

        // Si hay múltiples reservas, mostrar modal de selección
        if (data.multiple && data.reservas) {
          mostrarModalSeleccionReserva(data.reservas);
          return;
        }

        // Reserva única encontrada, procesar normalmente
        procesarReservaSeleccionada(data.reserva);
      })
      .catch(err => {
        console.error('Error:', err);
        document.getElementById('invitados-list').innerHTML = '<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>Error al consultar reserva</p></div>';
      });
  });

  function mostrarModalSeleccionReserva(reservas) {
    const reservasLista = document.getElementById('reservas-lista');
    let html = '';

    reservas.forEach(reserva => {
      const fechaFormateada = new Date(reserva.reserva_fecha).toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
      });

      const mesasHtml = reserva.mesa_info.map(mesa =>
        `<span class="mesa-tag">${mesa.mesa_nombre} (${mesa.ambiente_nombre} - ${mesa.piso_nombre})</span>`
      ).join('');
      const esSillaReserva = reserva.mesa_info.length > 0 && reserva.mesa_info.every(mesa => mesa.mesa_tipo === 'silla');
      const etiquetaMesas = esSillaReserva ? 'Sillas asignadas' : 'Mesas asignadas';
      const etiquetaSinMesas = esSillaReserva ? 'Sin sillas asignadas' : 'Sin mesas asignadas';

      const statusBadge = reserva.tiene_invitados_incompletos ?
        '<span class="status-badge status-warning"><i class="fas fa-exclamation-triangle me-1"></i>Invitados incompletos</span>' :
        '<span class="status-badge status-success"><i class="fas fa-check-circle me-1"></i>Invitados completos</span>';

      html += `
        <div class="reserva-item" onclick="seleccionarReserva(${reserva.id})">
          <div class="reserva-header">
            <span class="reserva-id">Reserva #${reserva.id}</span>
            <span class="reserva-fecha">${fechaFormateada}</span>
          </div>
          <div class="reserva-info">
            <div class="info-item">
              <i class="fas fa-user"></i>
              <span>${reserva.reserva_nombre_cliente} ${reserva.reserva_apellido_cliente}</span>
            </div>
            <div class="info-item">
              <i class="fas fa-users"></i>
              <span>${reserva.reserva_total_personas} personas</span>
            </div>
            <div class="info-item">
              <i class="fas fa-id-card"></i>
              <span>${reserva.reserva_documento || 'Sin documento'}</span>
            </div>
            <div class="info-item">
              <i class="fas fa-credit-card"></i>
              <span>Carnet: ${reserva.reserva_numero_carnet || 'Sin carnet'}</span>
            </div>
          </div>
          <div class="mesas-info">
            <strong>${etiquetaMesas}:</strong><br>
            ${mesasHtml || `<span class="text-muted">${etiquetaSinMesas}</span>`}
          </div>
          <div class="mt-2">
            ${statusBadge}
          </div>
        </div>
      `;
    });

    reservasLista.innerHTML = html;

    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('modalSeleccionReserva'));
    modal.show();
  }

  function seleccionarReserva(reservaId) {
    // Cerrar el modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('modalSeleccionReserva'));
    modal.hide();

    // Actualizar el campo de número de reserva
    document.getElementById('numero-reserva').value = reservaId;
    currentReservaId = reservaId;

    // Cargar los invitados de la reserva seleccionada
    consultarInvitadosPorReserva(reservaId);
  }

  function procesarReservaSeleccionada(reserva) {
    // Lógica original para una sola reserva
    const reservaId = reserva.id;
    currentReservaId = reservaId;

    // Mostrar loading
    document.getElementById('invitados-list').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Cargando invitados...</p></div>';

    // Como ya tenemos la información de la reserva, podemos pasarla directamente
    fetch('/validacion/editarreserva/consultarinvitados', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `reserva_id=${encodeURIComponent(reservaId)}`
      })
      .then(response => response.json())
      .then(dataInvitados => {
        if (dataInvitados.error) {
          document.getElementById('invitados-list').innerHTML = '<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>' + dataInvitados.error + '</p></div>';
          return;
        }

        // Necesitamos obtener la información de mesas para la reserva
        consultarInfoReservaCompleta(reserva, dataInvitados);
      })
      .catch(err => {
        console.error('Error:', err);
        document.getElementById('invitados-list').innerHTML = '<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>Error al cargar invitados</p></div>';
      });
  }

  function consultarInfoReservaCompleta(reserva, dataInvitados) {
    // Obtener información completa de la reserva con datos de mesas
    fetch('/validacion/editarreserva/consultarreserva', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `numero_reserva=${encodeURIComponent(reserva.id)}`
      })
      .then(response => response.json())
      .then(dataReserva => {
        let reservaInfo = null;

        if (dataReserva.success && !dataReserva.multiple) {
          reservaInfo = dataReserva.reserva;
        } else if (dataReserva.multiple && dataReserva.reservas) {
          reservaInfo = dataReserva.reservas.find(r => r.id == reserva.id);
        }

        mostrarInvitados(dataInvitados.invitados_completos, dataInvitados.invitados_incompletos, reservaInfo);
      })
      .catch(err => {
        console.error('Error al obtener info completa:', err);
        // Si falla, mostrar sin información de reserva
        mostrarInvitados(dataInvitados.invitados_completos, dataInvitados.invitados_incompletos);
      });
  }

  function consultarInvitadosPorReserva(reservaId) {
    // Mostrar loading
    document.getElementById('invitados-list').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Cargando invitados...</p></div>';

    // Primero obtener información de la reserva
    fetch('/validacion/editarreserva/consultarreserva', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `numero_reserva=${encodeURIComponent(reservaId)}`
      })
      .then(response => response.json())
      .then(dataReserva => {
        let reservaInfo = null;

        if (dataReserva.success && !dataReserva.multiple) {
          // Reserva única
          reservaInfo = dataReserva.reserva;
        } else if (dataReserva.multiple && dataReserva.reservas) {
          // Múltiples reservas, buscar la que coincide con el ID
          reservaInfo = dataReserva.reservas.find(r => r.id == reservaId);
        }

        // Ahora consultar invitados
        fetch('/validacion/editarreserva/consultarinvitados', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `reserva_id=${encodeURIComponent(reservaId)}`
          })
          .then(response => response.json())
          .then(dataInvitados => {
            if (dataInvitados.error) {
              document.getElementById('invitados-list').innerHTML = '<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>' + dataInvitados.error + '</p></div>';
              return;
            }

            mostrarInvitados(dataInvitados.invitados_completos, dataInvitados.invitados_incompletos, reservaInfo);
          })
          .catch(err => {
            console.error('Error:', err);
            document.getElementById('invitados-list').innerHTML = '<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>Error al cargar invitados</p></div>';
          });
      })
      .catch(err => {
        console.error('Error:', err);
        document.getElementById('invitados-list').innerHTML = '<div class="text-center text-danger"><i class="fas fa-exclamation-triangle fa-3x mb-3"></i><p>Error al obtener información de la reserva</p></div>';
      });
  }

  function mostrarInvitados(invitadosCompletos, invitadosIncompletos, reservaInfo = null) {
    let html = '';

    // SIEMPRE mostrar información de la reserva al principio, si está disponible
    if (reservaInfo) {
      html += mostrarInfoReserva(reservaInfo);
      console.log('Mostrando información de la reserva:', reservaInfo);
    }

    // Sección de invitados con datos completos
    if (invitadosCompletos && invitadosCompletos.length > 0) {
      html += '<div class="mb-4">';
      html += '<h5 class="text-success mb-3"><i class="fas fa-check-circle me-2"></i>Invitados con datos completos</h5>';
      html += '<div class="alert alert-info" role="alert">Se deben generar las boletas   para cada invitado, le llegará al correo al titular de la reserva.</div>';

      html += '<div class="list-group">';

      invitadosCompletos.forEach(invitado => {
        // Verificar si ya tiene boleta asignada
        const tieneBoleta = invitado.boleta && invitado.boleta.length > 0;

        html += `
          <div class="list-group-item d-flex justify-content-between align-items-center">
            <div class="flex-grow-1">
              <h6 class="mb-1">${invitado.invitadoReserva_nombre_invitado + ' ' + invitado.invitadoReserva_apellido_invitado || 'Sin nombre'}</h6>
              <small class="text-muted">Documento: ${invitado.documento_invitado || 'Sin documento'}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
              <span class="badge bg-success">Completo</span>
              ${tieneBoleta ?
            '<span class="badge bg-info"><i class="fas fa-check-circle me-1"></i>Boleta Enviada</span>' :
            `<button class="btn btn-sm btn-primary" onclick="generarBoletaIndividual(${invitado.id_invitado})">
                  <i class="fas fa-ticket-alt me-1"></i>Generar Boleta
                </button>`
          }
            </div>
          </div>
        `;
      });

      html += '</div></div>';
    }

    // Sección de invitados con datos incompletos
    if (invitadosIncompletos && invitadosIncompletos.length > 0) {
      html += '<div class="mb-4">';
      html += '<h5 class="text-warning mb-3"><i class="fas fa-exclamation-triangle me-2"></i>Invitados que requieren actualización</h5>';

      html += '<div class="alert alert-danger" role="alert">Por favor, complete los datos de cada invitado y guarde los cambios individualmente.</div>';

      invitadosIncompletos.forEach(invitado => {
        html += `
          <div class="invitado-incompleto">
            <div class="row">
               <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label">Documento:</label>
                <input type="text" class="form-control documento-input" 
                       data-invitado-id="${invitado.id_invitado}"
                       value="${invitado.documento_invitado || ''}" 
                       placeholder="Ingrese documento">
              </div>
              <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label">Nombre:</label>
                <input type="text" class="form-control nombre-input" 
                       data-invitado-id="${invitado.id_invitado}"
                       placeholder="${invitado.invitadoReserva_nombre_invitado || ''}" 
                       >
              </div>
              <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label">Apellido:</label>
                <input type="text" class="form-control apellido-input" 
                       data-invitado-id="${invitado.id_invitado}"
                       placeholder="${invitado.invitadoReserva_apellido_invitado || ''}" 
                       >
              </div>
              <div class="col-12 col-md-6 col-lg-3 d-flex align-items-end mt-2 mt-md-0">
                <button class="btn btn-save-invitado w-100" onclick="guardarInvitado(${invitado.id_invitado})">
                  <i class="fas fa-save me-1"></i>Guardar
                </button>
              </div>
            </div>
          </div>
        `;
      });

      html += '</div>';
    }

    // Verificar si hay invitados (completos o incompletos)
    const hayInvitados = (invitadosCompletos && invitadosCompletos.length > 0) ||
      (invitadosIncompletos && invitadosIncompletos.length > 0);

    // Si no hay invitados, agregar mensaje informativo
    if (!hayInvitados) {
      html += '<div class="text-center text-muted mt-4"><i class="fas fa-users fa-2x mb-2"></i><p>No hay invitados registrados para esta reserva</p></div>';
    }

    // Si no hay contenido en absoluto (ni reserva ni invitados), mostrar mensaje por defecto
    if (html === '') {
      html = '<div class="text-center text-muted"><i class="fas fa-users fa-2x mb-2"></i><p>Seleccione una reserva para ver los invitados</p></div>';
    }

    document.getElementById('invitados-list').innerHTML = html;
  }

  function mostrarInfoReserva(reservaInfo) {
    if (!reservaInfo) {
      return '';
    }

    const fechaFormateada = new Date(reservaInfo.reserva_fecha).toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });

    const mesasHtml = (reservaInfo.mesa_info && Array.isArray(reservaInfo.mesa_info)) ?
      reservaInfo.mesa_info.map(mesa =>
        `<span class="mesa-tag">${mesa.mesa_nombre} (${mesa.ambiente_nombre} - ${mesa.piso_nombre})</span>`
      ).join('') :
      '<span class="text-muted">Sin mesas asignadas</span>';
    const esSillaReservaInfo = (reservaInfo.mesa_info && Array.isArray(reservaInfo.mesa_info) && reservaInfo.mesa_info.length > 0)
      ? reservaInfo.mesa_info.every(mesa => mesa.mesa_tipo === 'silla') : false;
    const etiquetaMesasInfo = esSillaReservaInfo ? 'Sillas asignadas' : 'Mesas asignadas';
    const etiquetaSinMesasInfo = esSillaReservaInfo ? 'Sin sillas asignadas' : 'Sin mesas asignadas';

    return `
      <div class="card mb-3" style="background: #f8f9fa; border: 1px solid #e9ecef;">
        <div class="card-header bg-light">
          <h6 class="mb-0 text-primary">
            <i class="fas fa-info-circle me-2"></i>
            Información de la Reserva
          </h6>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="info-item mb-2">
                <i class="fas fa-hashtag text-primary me-2"></i>
                <strong>ID:</strong> ${reservaInfo.id}
              </div>
              <div class="info-item mb-2">
                <i class="fas fa-calendar text-primary me-2"></i>
                <strong>Fecha:</strong> ${fechaFormateada}
              </div>
              <div class="info-item mb-2">
                <i class="fas fa-user text-primary me-2"></i>
                <strong>Cliente:</strong> ${reservaInfo.reserva_nombre_cliente} ${reservaInfo.reserva_apellido_cliente}
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-item mb-2">
                <i class="fas fa-users text-primary me-2"></i>
                <strong>Personas:</strong> ${reservaInfo.reserva_total_personas}
              </div>
              <div class="info-item mb-2">
                <i class="fas fa-id-card text-primary me-2"></i>
                <strong>Documento:</strong> ${reservaInfo.reserva_documento || 'No registrado'}
              </div>
              <div class="info-item mb-2">
                <i class="fas fa-credit-card text-primary me-2"></i>
                <strong>Carnet:</strong> ${reservaInfo.reserva_numero_carnet || 'No registrado'}
              </div>
            </div>
          </div>
          <div class="mt-3">
            <div class="info-item">
              <i class="fas fa-table text-primary me-2"></i>
              <strong>${etiquetaMesasInfo}:</strong><br>
              <div class="mt-2">
                ${mesasHtml || `<span class="text-muted">${etiquetaSinMesasInfo}</span>`}
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  function guardarInvitado(invitadoId) {
    const nombreInput = document.querySelector(`input.nombre-input[data-invitado-id="${invitadoId}"]`);
    const apellidoInput = document.querySelector(`input.apellido-input[data-invitado-id="${invitadoId}"]`);
    const documentoInput = document.querySelector(`input.documento-input[data-invitado-id="${invitadoId}"]`);

    const nombre = nombreInput.value.trim();
    const apellido = apellidoInput.value.trim();
    const documento = documentoInput.value.trim();

    if (!nombre || !documento || !apellido) {
      Swal.fire({
        icon: 'warning',
        title: 'Campos requeridos',
        text: 'Por favor complete el nombre, apellido y documento del invitado',
        confirmButtonText: 'Entendido'
      });
      return;
    }

    // Mostrar loading en el botón
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';
    btn.disabled = true;

    // Validar documento antes de guardar
    fetch('/validacion/editarreserva/validardocumento', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `documento=${encodeURIComponent(documento)}`
      })
      .then(response => response.json())
      .then(validacion => {
        if (!validacion.status) {
          Swal.fire({
            icon: 'error',
            title: 'Documento inválido',
            text: validacion.message || 'El documento ya está registrado en otra reserva.',
            confirmButtonText: 'Entendido'
          });
          btn.innerHTML = originalText;
          btn.disabled = false;
          return;
        }
        // Si es válido, guardar invitado
        fetch('/validacion/editarreserva/actualizarinvitado', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `invitado_id=${encodeURIComponent(invitadoId)}&nombre=${encodeURIComponent(nombre)}&apellido=${encodeURIComponent(apellido)}&documento=${encodeURIComponent(documento)}`
          })
          .then(response => response.json())
          .then(data => {
            if (data.error) {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error,
                confirmButtonText: 'Entendido'
              });
            } else {
              Swal.fire({
                icon: 'success',
                title: 'Guardado',
                text: 'Datos del invitado actualizados correctamente',
                confirmButtonText: 'Entendido'
              }).then(() => {
                // Redirigir a la misma página con el ID de la reserva para mantener el contexto
                if (currentReservaId) {
                  window.location.href = `/validacion/editarreserva?idreserva=${currentReservaId}`;
                } else {
                  // Si no hay ID de reserva, recargar la lista de invitados
                  document.getElementById('consultar-reserva').click();
                }
              });
            }
          })
          .catch(err => {
            console.error('Error:', err);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error al guardar los datos del invitado',
              confirmButtonText: 'Entendido'
            });
          })
          .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
          });
      })
      .catch(err => {
        console.error('Error:', err);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al validar el documento',
          confirmButtonText: 'Entendido'
        });
        btn.innerHTML = originalText;
        btn.disabled = false;
      });
  }

  function generarBoletaIndividual(invitadoId) {
    Swal.fire({
      title: '¿Generar boleta?',
      text: '¿Está seguro de que desea generar la boleta para este invitado?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#28a745',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, generar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        // Mostrar loading
        Swal.fire({
          title: 'Generando boleta...',
          html: 'Por favor espere mientras se genera la boleta',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        // Llamar al endpoint de generación de boletas
        fetch('/validacion/editarreserva/generarboletas', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id_invitado=${encodeURIComponent(invitadoId)}`
          })
          .then(response => {
            if (response.ok) {
              // Si la respuesta es exitosa, redirigir o mostrar éxito
              Swal.fire({
                icon: 'success',
                title: 'Boleta generada',
                text: 'La boleta se ha generado exitosamente',
                confirmButtonText: 'Entendido'
              }).then(() => {
                // Redirigir a la misma página con el ID de la reserva
                window.location.href = `/validacion/editarreserva?idreserva=${currentReservaId}`;
              });
            } else {
              // Si hay error en la respuesta HTTP, intentar parsear JSON
              response.json().then(errorData => {
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: errorData.error || 'Error al generar la boleta',
                  confirmButtonText: 'Entendido'
                });
              }).catch(() => {
                // Si no es JSON, mostrar mensaje genérico
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Error al generar la boleta. Por favor intente nuevamente.',
                  confirmButtonText: 'Entendido'
                });
              });
            }
          })
          .catch(err => {
            console.error('Error:', err);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error de conexión. Por favor intente nuevamente.',
              confirmButtonText: 'Entendido'
            });
          });
      }
    });
  }

  // Permitir consultar con Enter
  document.getElementById('numero-reserva').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
      document.getElementById('consultar-reserva').click();
    }
  });

  // Cargar automáticamente la reserva si viene el parámetro idreserva en la URL
  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const idReserva = urlParams.get('idreserva');

    if (idReserva) {
      // Establecer el ID de la reserva y cargar automáticamente
      document.getElementById('numero-reserva').value = idReserva;
      currentReservaId = idReserva;

      // Cargar los datos automáticamente
      consultarReservaPorId(idReserva);
    }
  });

  // Función auxiliar para consultar reserva por ID
  function consultarReservaPorId(idReserva) {
    // Mostrar loading
    document.getElementById('invitados-list').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Cargando reserva...</p></div>';

    // Consultar invitados directamente con el ID de la reserva
    consultarInvitadosPorReserva(idReserva);
  }
</script>