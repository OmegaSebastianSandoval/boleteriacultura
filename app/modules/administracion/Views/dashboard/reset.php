<style>
  .reset-container {
    /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh; */
    /* padding: 50px 0; */
  }

  .reset-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: none;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
  }



  .reset-header h4 {
    margin: 0;
    font-weight: 700;
    font-size: 2rem;
  }

  .reset-body {
    padding: 40px;
  }

  .warning-section {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
  }

  .warning-section h5 {
    color: #856404;
    margin-bottom: 15px;
    font-weight: 600;
  }

  .delete-items {
    list-style: none;
    padding: 0;
  }

  .delete-items li {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 8px;
    padding: 15px 20px;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    transition: transform 0.2s ease;
  }

  .delete-items li:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  .delete-items li i {
    color: #721c24;
    margin-right: 15px;
    font-size: 1.2rem;
  }

  .delete-items li strong {
    color: #721c24;
  }

  .reset-btn {
    background: linear-gradient(45deg, #dc3545, #c82333);
    border: none;
    border-radius: 50px;
    padding: 15px 40px;
    font-size: 1.2rem;
    font-weight: 600;
    color: white;

    transition: all 0.3s ease;
    box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
  }

  .reset-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(220, 53, 69, 0.4);
    color: white;
  }

  .backup-btn {
    background: linear-gradient(45deg, #007bff, #0056b3);
    border: none;
    border-radius: 50px;
    padding: 15px 40px;
    font-size: 1.2rem;
    font-weight: 600;
    color: white;

    transition: all 0.3s ease;
    box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3);
  }

  .backup-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(0, 123, 255, 0.4);
    color: white;
  }

  .backup-btn i {
    margin-right: 10px;
  }

  .download-btn {
    background: linear-gradient(45deg, #28a745, #1e7e34);
    border: none;
    border-radius: 50px;
    padding: 15px 40px;
    font-size: 1.2rem;
    font-weight: 600;
    color: white;
    /* text-transform: uppercase; */
    /* letter-spacing: 1px; */
    transition: all 0.3s ease;
    box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
  }

  .download-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(40, 167, 69, 0.4);
    color: white;
  }

  .download-btn i {
    margin-right: 10px;
  }

  /* Modal Styles */
  .modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
  }

  .modal-header {
    background: linear-gradient(45deg, #dc3545, #c82333);
    color: white;
    border-radius: 20px 20px 0 0;
    border-bottom: none;
  }

  .modal-header .close {
    color: white;
    opacity: 1;
  }

  .modal-body {
    padding: 30px;
    text-align: center;
  }

  .modal-footer {
    border-top: none;
    padding: 20px 30px;
    justify-content: center;
  }

  .modal-footer .btn {
    border-radius: 50px;
    padding: 10px 30px;
    font-weight: 600;

  }

  .modal-footer .btn-danger {
    background: linear-gradient(45deg, #dc3545, #c82333);
    border: none;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
  }

  .modal-footer .btn-secondary {
    background: #6c757d;
    border: none;
  }
</style>

<h1 class="titulo-principal  mb-4">
  <i class="fas fa-chart-bar"></i> <?php echo $this->titlesection; ?>
</h1>
<div class="reset-container">
  <div class="container-fluid">

    <div class="card reset-card">

      <div class="card-body reset-body">
        <div class="warning-section">
          <h5><i class="fas fa-exclamation-circle"></i> Advertencia Importante</h5>
          <p>Esta acción reiniciará completamente el sistema de boletería. Los siguientes datos serán eliminados permanentemente y no podrán ser recuperados:</p>
        </div>

        <ul class="delete-items">
          <li>
            <i class="fas fa-ticket-alt"></i>
            <div>
              <strong>Boletas:</strong> Todas las boletas no finalizadas y sus registros asociados.
            </div>
          </li>
          <li>
            <i class="fas fa-calendar-check"></i>
            <div>
              <strong>Reservas:</strong> Todas las reservas pendientes y en proceso.
            </div>
          </li>
          <li>
            <i class="fas fa-users"></i>
            <div>
              <strong>Invitados:</strong> Todos los invitados registrados para reservas y boletas.
            </div>
          </li>
          <li>
            <i class="fas fa-search"></i>
            <div>
              <strong>Logs de escaneo:</strong> Registros de escaneo de boletas.
            </div>
          </li>
          <li>
            <i class="fas fa-shopping-cart"></i>
            <div>
              <strong>Cola de compra:</strong> Elementos en la cola de compra pendientes.
            </div>
          </li>
        </ul>

        <div class="text-center mt-4">
          <div class="row justify-content-center">
            <div class="col-md-4 mb-3">
              <a href="<?php echo $this->route; ?>/backupdb" class="btn backup-btn w-100">
                <i class="fas fa-database"></i> Descargar Backup DB
              </a>
            </div>
            <div class="col-md-4 mb-3">
              <a href="<?php echo $this->route; ?>/downloadboleteria" class="btn download-btn w-100">
                <i class="fas fa-download"></i> Descargar Boletería
              </a>
            </div>
            <div class="col-md-4 mb-3">
              <button type="button" class="btn reset-btn w-100" data-bs-toggle="modal" data-bs-target="#confirmModal">
                <i class="fas fa-redo"></i> Reiniciar Boletería
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel"><i class="fas fa-exclamation-triangle"></i> Confirmar Reinicio</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="text-center">
          <i class="fas fa-question-circle fa-3x text-warning mb-3"></i>
          <p class="mb-2"><strong>¿Estás completamente seguro?</strong></p>
          <p>Esta acción eliminará todos los datos mencionados anteriormente de forma permanente.</p>
          <p class="text-muted">Esta operación no se puede deshacer.</p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times"></i> Cancelar
        </button>
        <a href="<?php echo $this->route; ?>/acceptreset" class="btn btn-danger">
          <i class="fas fa-check"></i> Confirmar Reinicio
        </a>
      </div>
    </div>
  </div>
</div>