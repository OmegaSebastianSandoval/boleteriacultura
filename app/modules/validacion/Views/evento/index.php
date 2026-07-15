<style>
  .btn-menu {
    display: none;
  }
</style>


<style>
  :root {
    --primary-color: #192a4b;
    --secondary-color: #f8f9fa;
    --accent-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --info-color: #17a2b8;
    --border-radius: 12px;
    --box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
  }

  body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    /* min-height: 100vh; */
    height: auto;
  }

  .main-content {
    min-height: calc(100dvh - 196.5px);
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

  .user-info .badge {
    font-size: 0.9rem;
    padding: 8px 16px;
    border-radius: 25px;
  }

  /* Main Content */
  .main-content {
    padding: 1rem 0;
    /* height: calc(100vh - 80px); */
    overflow: hidden;
  }

  .main-content .row {
    height: 100%;
  }

  /* Info Panel (Izquierda) */
  .info-panel {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    height: 100%;
    box-shadow: var(--box-shadow);
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    overflow-y: auto;
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

  /* Tip Card - Destacado */
  .tip-card {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: 2px solid #ffc107;
    border-radius: var(--border-radius);
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    animation: fadeInUp 0.6s ease-out;
  }

  .tip-icon {
    width: 50px;
    height: 50px;
    background: var(--warning-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    color: white;
    flex-shrink: 0;
  }

  .tip-card h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #8b5e00;
  }

  .tip-card p {
    margin: 0.25rem 0 0 0;
    color: #8b5e00;
    font-size: 0.9rem;
  }

  /* Methods Section */
  .methods-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
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

  /* System Status */
  .system-status {
    margin-top: auto;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
  }

  .status-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--accent-color);
    font-weight: 500;
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
  }

  .manual-entry-link {
    text-align: center;
  }

  .btn-manual {
    color: var(--primary-color);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border: 1px solid var(--primary-color);
    border-radius: 20px;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    background: transparent;
  }

  .btn-manual:hover {
    background: var(--primary-color);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(25, 42, 75, 0.2);
  }

  /* Scanner Panel (Derecha) */
  .scanner-panel {
    background: white;
    border-radius: var(--border-radius);
    height: 100%;
    box-shadow: var(--box-shadow);
    display: flex;
    flex-direction: column;
    overflow: hidden;
  }

  .scanner-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #2d3748 100%);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
  }

  .scanner-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
  }

  .scanner-container {
    flex: 1;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    position: relative;
  }

  #qr-reader {
    width: 100%;
    max-width: 450px;
    margin: 0 auto;
    border-radius: 8px;
    overflow: hidden;
    flex: 1;
    max-height: 400px;
  }

  .scanner-controls {
    margin-top: 1rem;
    text-align: center;
  }

  #restart-scanner {
    border-radius: 25px;
    padding: 10px 20px;
    font-weight: 500;
  }

  /* Estilos para botones generados por Html5QrcodeScanner */
  #html5-qrcode-button-camera-start {
    background: linear-gradient(135deg, var(--primary-color) 0%, #2d3748 100%) !important;
    color: #fff !important;
    border: none !important;
    padding: 12px 24px !important;
    border-radius: 25px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 15px rgba(25, 42, 75, 0.3) !important;
  }

  #html5-qrcode-button-camera-start:hover {
    background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(25, 42, 75, 0.4) !important;
  }

  #html5-qrcode-button-camera-stop {
    background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%) !important;
    color: #fff !important;
    border: none !important;
    padding: 12px 24px !important;
    border-radius: 25px !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    transition: all 0.3s ease !important;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3) !important;
  }

  #html5-qrcode-button-camera-stop:hover {
    background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%) !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4) !important;
  }

  .pulse {
    animation: pulse 2s infinite;
  }

  @keyframes pulse {

    0%,
    100% {
      opacity: 1;
    }

    50% {
      opacity: 0.5;
    }
  }

  /* Responsive Design */
  @media (max-width: 992px) {
    .main-content {
      height: auto;
      overflow: visible;
    }

    .main-content .row {
      height: auto;
    }

    .info-panel,
    .scanner-panel {
      height: auto;
      margin-bottom: 1rem;
    }

    .scanner-container {
      min-height: 300px;
    }

    #qr-reader {
      /* max-height: 300px; */
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

    .tip-card {
      flex-direction: column;
      text-align: center;
      gap: 0.5rem;
    }

    .method-item {
      flex-direction: column;
      text-align: center;
      gap: 0.5rem;
    }
  }

  /* Animations */
  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .method-card {
    animation: fadeInUp 0.6s ease-out;
  }

  .method-card:nth-child(1) {
    animation-delay: 0.1s;
  }

  .method-card:nth-child(2) {
    animation-delay: 0.2s;
  }

  /* Modal Styles */
  .modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
    overflow: hidden;
  }

  .modal-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #2d3748 100%);
    color: white;
    border: none;
    padding: 0;
    position: relative;
    overflow: hidden;
  }

  .modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
    opacity: 0.1;
  }

  .modal-title-wrapper {
    display: flex;
    align-items: center;
    padding: 1.5rem 2rem;
    position: relative;
    z-index: 1;
  }

  .modal-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
  }

  .modal-icon i {
    font-size: 24px;
    color: #fff;
  }

  .modal-title {
    font-weight: 700;
    font-size: 1.25rem;
    margin: 0;
    color: white;
  }

  .modal-subtitle {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    font-weight: 400;
  }

  .btn-close {
    filter: brightness(0) invert(1);
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 2;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.8;
    transition: all 0.3s ease;
  }

  .btn-close:hover {
    opacity: 1;
    transform: scale(1.1);
  }

  .modal-body {
    padding: 2rem;
  }

  .manual-input-section {
    margin-bottom: 1rem;
  }

  .form-label {
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 0.5rem;
  }

  .manual-input {
    padding: 12px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    font-size: 1rem;
    transition: var(--transition);
    font-family: 'Monaco', 'Menlo', monospace;
    letter-spacing: 1px;
  }

  .manual-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(25, 42, 75, 0.25);
    outline: none;
  }

  .input-help {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #666;
    display: flex;
    align-items: center;
  }

  .modal-footer {
    padding: 1.5rem 2rem;
    border: none;
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
  }

  .modal-footer .btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    transition: var(--transition);
  }

  .modal-footer .btn-secondary {
    background: #e2e8f0;
    color: #4a5568;
    border: none;
  }

  .modal-footer .btn-secondary:hover {
    background: #cbd5e0;
    color: #2d3748;
    transform: translateY(-1px);
  }

  .modal-footer .btn-primary {
    background: linear-gradient(135deg, var(--primary-color) 0%, #2d3748 100%);
    border: none;
  }

  .modal-footer .btn-primary:hover {
    background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(25, 42, 75, 0.3);
  }
</style>
<!-- Header con navegación -->
<div class="header-section">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center py-3">
      <div class="brand-section">
        <h4 class="mb-0 text-primary fw-bold">
          <i class="fas fa-shield-check me-2"></i>
          Sistema de Validación
        </h4>
      </div>
      <div class="user-info">
        <!-- <span class="badge bg-primary">
          <i class="fas fa-user me-1"></i>
          <?= $this->user->user_user ?>
        </span> -->
       
          <a href="/validacion/editarreserva" class="badge bg-success text-decoration-none">
            <i class="fas fa-user-plus me-1"></i>
            Completar invitados
          </a>
        
      </div>
    </div>
  </div>
</div>
<div class="container p-0">

  <div class="main-content">
    <div class="container-fluid">
      <div class="row g-4 h-100">
        <!-- Columna izquierda - Información y bienvenida -->
        <div class="col-12 col-lg-5">
          <div class="info-panel">
            <!-- Bienvenida compacta -->
            <div class="welcome-section">
              <div class="welcome-icon">
                <i class="fa-solid fa-qrcode"></i>
              </div>
              <div>
                <h2 class="welcome-title">¡Hola, <?= $this->user->user_user ?>!</h2>
                <p class="welcome-subtitle">Sistema de validación de boletas</p>
              </div>
            </div>

            <!-- Consejo destacado -->
            <div class="tip-card">
              <div class="tip-icon">
                <i class="fas fa-lightbulb"></i>
              </div>
              <div>
                <h4>Métodos disponibles</h4>
                <p>Puede usar <strong>cámara</strong> o <strong>pistola USB</strong> simultáneamente</p>
              </div>
            </div>

            <!-- Métodos de escaneo -->
            <div class="methods-section">
              <div class="method-item">
                <div class="method-icon-small bg-primary">
                  <i class="fas fa-camera"></i>
                </div>
                <div class="method-content">
                  <h5>Escaneo por cámara</h5>
                  <p>Use la cámara para escanear códigos QR</p>
                </div>
              </div>

              <div class="method-item">
                <div class="method-icon-small bg-success">
                  <i class="fas fa-barcode"></i>
                </div>
                <div class="method-content">
                  <h5>Pistola lectora USB</h5>
                  <p>Conecte el lector y apunte al código</p>
                </div>
              </div>
            </div>

            <!-- Estado del sistema -->
            <div class="system-status">
              <div class="status-indicator">
                <i class="fas fa-circle text-success pulse"></i>
                <span>Sistema listo para escanear</span>
              </div>
              <div class="manual-entry-link">
                <a href="#" id="manual-entry-btn" class="btn-manual">
                  <i class="fas fa-keyboard me-1"></i>
                  Ingresar manualmente
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Columna derecha - Área del escáner -->
        <div class="col-12 col-lg-7">
          <div class="scanner-panel">
            <div class="scanner-header">
              <h3><i class="fas fa-scanner me-2"></i>Área de escaneo</h3>
            </div>

            <!-- Input oculto para el lector físico -->
            <input type="text" id="scanner-input" autofocus style="opacity:0; position:absolute; left:-9999px;">

            <div class="scanner-container">
              <!-- Visor para la cámara -->
              <div id="qr-reader"></div>

              <div class="scanner-controls">
                <button id="restart-scanner" class="btn btn-outline-primary" style="display:none;">
                  <i class="fas fa-redo me-2"></i>Reiniciar escáner
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para ingreso manual -->
<div class="modal fade" id="manualEntryModal" tabindex="-1" aria-labelledby="manualEntryModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title-wrapper">
          <div class="modal-icon">
            <i class="fas fa-keyboard"></i>
          </div>
          <div>
            <h5 class="modal-title mb-0" id="manualEntryModalLabel">Ingreso manual</h5>
            <p class="modal-subtitle mb-0">Escriba el No. documento de la boleta</p>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="manual-input-section">
          <label for="manual-code-input" class="form-label">No. de documento:</label>
          <input type="text" class="form-control manual-input" id="manual-code-input"
            placeholder="Ingrese el No. documento aquí..." autocomplete="off">
          <div class="input-help">
            <i class="fas fa-info-circle me-1"></i>
            Ingrese el No. documento exactamente como aparece en la boleta
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-2"></i>Cancelar
        </button>
        <button type="button" class="btn btn-primary" id="validate-manual-code">
          <i class="fas fa-check-circle me-2"></i>Validar documento
        </button>
      </div>
    </div>
  </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
  let isProcessing = false;
  let qrScanner;

  /* ==== VALIDACIÓN (usada por ambos métodos) ==== */
  function validarDocumento (documento, metodo = 'camera') {
    if (isProcessing || !documento) return;
    isProcessing = true;

    fetch('/validacion/evento/consultardocumento', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `documento=${encodeURIComponent(documento)}&metodo_escaneado=${encodeURIComponent(metodo)}`
    })
      .then(response => response.json())

      .then(data => {
        console.log(data);

        if (data.success) {
          // Enviar boletaInfo por POST usando un formulario oculto
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = '/validacion/validar';
          form.style.display = 'none';

          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'boletaInfo';
          input.value = JSON.stringify(data.boletaInfo);

          // Agregar método de escaneo al formulario
          const metodoInput = document.createElement('input');
          metodoInput.type = 'hidden';
          metodoInput.name = 'metodo_escaneado';
          metodoInput.value = metodo;

          form.appendChild(input);
          form.appendChild(metodoInput);
          document.body.appendChild(form);
          form.submit();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Boleta no válida',
            text: 'La boleta no es válida.',
            confirmButtonText: 'Entendido'
          })



          // Detener cámara después de un scan fallido
          if (qrScanner) {
            qrScanner.clear();
            document.getElementById('restart-scanner').style.display = 'block';
          }
        }
      })
      .catch(err => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al validar el documento.',
        });
      })
      .finally(() => {
        isProcessing = false;
      });
  }

  /* ==== ESCÁNER POR CÁMARA ==== */
  function onScanSuccess (decodedText) {
    validarDocumento(decodedText, 'camera');
  }

  function startCameraScanner () {
    qrScanner = new Html5QrcodeScanner('qr-reader', {
      fps: 10,
      qrbox: 200,
      rememberLastUsedCamera: true,
      videoConstraints: {
        facingMode: { ideal: 'environment' }
      }
    });
    qrScanner.render(onScanSuccess);
    document.getElementById('restart-scanner').style.display = 'none';
  }

  /* ==== VERIFICACIÓN / SOLICITUD DE PERMISO DE CÁMARA ====
     Html5QrcodeScanner no avisa de forma clara cuando el permiso de cámara
     falla o no se puede solicitar; por eso se pide explícitamente con
     getUserMedia y se informa al usuario el motivo exacto. */
  function verificarSoporteCamara () {
    if (!window.isSecureContext) {
      Swal.fire({
        icon: 'warning',
        title: 'Conexión no segura',
        html: 'El escaneo por cámara requiere una conexión segura (HTTPS). Contacte al administrador o utilice el ingreso manual / lector USB mientras tanto.',
        confirmButtonText: 'Entendido'
      });
      return false;
    }
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      Swal.fire({
        icon: 'warning',
        title: 'Cámara no disponible',
        html: 'Este navegador no soporta el acceso a la cámara. Utilice el ingreso manual o el lector USB.',
        confirmButtonText: 'Entendido'
      });
      return false;
    }
    return true;
  }

  function solicitarPermisoCamara () {
    if (!verificarSoporteCamara()) {
      document.getElementById('restart-scanner').style.display = 'block';
      return;
    }

    // Si el navegador ya tiene el permiso en estado "denied" (bloqueado por el
    // usuario en un intento anterior), getUserMedia() rechaza de inmediato SIN
    // volver a mostrar el aviso nativo — por eso hay que detectar ese caso
    // aparte y explicarle al usuario cómo desbloquearlo manualmente.
    if (navigator.permissions && navigator.permissions.query) {
      navigator.permissions.query({ name: 'camera' })
        .then(function (status) {
          manejarEstadoPermisoCamara(status.state);
          // En navegadores compatibles (p. ej. Chrome), si el usuario cambia el
          // permiso desde la configuración del sitio sin recargar, este evento
          // permite reintentar automáticamente.
          status.onchange = function () {
            manejarEstadoPermisoCamara(status.state);
          };
        })
        .catch(function () {
          // Firefox y otros navegadores no soportan 'camera' en la Permissions API.
          intentarAccederCamara();
        });
    } else {
      intentarAccederCamara();
    }
  }

  function manejarEstadoPermisoCamara (state) {
    if (state === 'denied') {
      Swal.fire({
        icon: 'error',
        title: 'Cámara bloqueada para este sitio',
        html: 'El navegador tiene el permiso de cámara <strong>bloqueado</strong> para este sitio, por eso no vuelve a mostrar el aviso para activarla.<br><br>' +
          'Para desbloquearla: haga clic en el ícono de candado / información junto a la dirección del sitio, busque <strong>Cámara</strong> y cambie el permiso a <strong>Permitir</strong>. Luego recargue esta página.<br><br>' +
          'Mientras tanto puede usar el lector USB o el ingreso manual.',
        confirmButtonText: 'Recargar página',
        showCancelButton: true,
        cancelButtonText: 'Cerrar'
      }).then(function (result) {
        if (result.isConfirmed) location.reload();
      });
      document.getElementById('restart-scanner').style.display = 'block';
      return;
    }
    // 'granted' o 'prompt': intentar acceder (si es 'prompt', esto dispara el aviso nativo del navegador)
    intentarAccederCamara();
  }

  function intentarAccederCamara () {
    navigator.mediaDevices.getUserMedia({ video: true })
      .then(function (stream) {
        // Se detiene este stream de prueba; Html5QrcodeScanner abre el suyo propio.
        stream.getTracks().forEach(function (track) { track.stop(); });
        startCameraScanner();
      })
      .catch(function (err) {
        console.error('Permiso de cámara no concedido:', err);
        let mensaje = 'No se pudo acceder a la cámara.';
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
          mensaje = 'Debe conceder el permiso de acceso a la cámara desde la configuración del navegador para poder escanear boletas. Mientras tanto puede usar el lector USB o el ingreso manual.';
        } else if (err.name === 'NotFoundError') {
          mensaje = 'No se detectó ninguna cámara en este dispositivo.';
        } else if (err.name === 'NotReadableError') {
          mensaje = 'La cámara está siendo utilizada por otra aplicación.';
        }
        Swal.fire({
          icon: 'warning',
          title: 'Permiso de cámara requerido',
          html: mensaje,
          confirmButtonText: 'Entendido'
        });
        document.getElementById('restart-scanner').style.display = 'block';
      });
  }

  /* ==== ESCÁNER POR LECTOR FÍSICO (USB) ==== */
  const input = document.getElementById("scanner-input");

  input.addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
      const documento = input.value.trim();
      validarDocumento(documento, 'usb_scanner');
      input.value = ""; // limpiar para siguiente scan
    }
  });

  // Mantener el foco en el input oculto solo cuando no hay modal abierto
  let modalOpen = false;

  window.addEventListener("click", (e) => {
    // No enfocar el input si se está haciendo clic en el botón del modal o si el modal está abierto
    if (!modalOpen && !e.target.closest('#manual-entry-btn') && !e.target.closest('.modal')) {
      input.focus();
    }
  });

  input.focus();

  /* === ARRANCAR AMBOS ESCÁNERES === */
  solicitarPermisoCamara();

  // Evento para reiniciar cámara
  document.getElementById('restart-scanner').addEventListener('click', solicitarPermisoCamara);

  // Evento para abrir modal de ingreso manual
  document.getElementById('manual-entry-btn').addEventListener('click', function (e) {
    // console.log('Botón manual clicado');
    e.preventDefault();
    e.stopPropagation();
    modalOpen = true;

    const modalElement = document.getElementById('manualEntryModal');
    const modal = new bootstrap.Modal(modalElement);
    modal.show();

    // Enfocar el input cuando se abra el modal
    modalElement.addEventListener('shown.bs.modal', function () {
      // console.log('Modal mostrado, enfocando input');
      document.getElementById('manual-code-input').focus();
    }, { once: true });
  });

  // Evento para validar código manual
  document.getElementById('validate-manual-code').addEventListener('click', function () {
    const code = document.getElementById('manual-code-input').value.trim();

    if (!code) {
      Swal.fire({
        icon: 'warning',
        title: 'Código requerido',
        text: 'Por favor ingrese un código de boleta.',
        confirmButtonText: 'Entendido'
      });
      return;
    }

    // Cerrar modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('manualEntryModal'));
    modal.hide();
    modalOpen = false;

    // Limpiar input
    document.getElementById('manual-code-input').value = '';

    // Validar documento
    validarDocumento(code, 'manual');
  });

  // Permitir validar con Enter en el input manual
  document.getElementById('manual-code-input').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
      document.getElementById('validate-manual-code').click();
    }
  });

  // Limpiar input al cerrar modal y restaurar foco al scanner
  document.getElementById('manualEntryModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('manual-code-input').value = '';
    modalOpen = false;
    // Restaurar foco al input del escáner después de cerrar modal
    setTimeout(() => {
      if (!modalOpen) {
        input.focus();
      }
    }, 100);
  });

  // Evento adicional para manejar cuando el modal se muestra
  document.getElementById('manualEntryModal').addEventListener('shown.bs.modal', function () {
    modalOpen = true;
  });
</script>