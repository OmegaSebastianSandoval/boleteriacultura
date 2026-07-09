<h1 class="titulo-principal"><i class="fas fa-ticket-alt"></i>Resultado Generación de Boletas</h1>
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <?php if (($this->error) && $this->error): ?>
        <div class="alert alert-danger">
          <h4><i class="fas fa-exclamation-triangle"></i> Error</h4>
          <p><?php echo $this->error; ?></p>
          <?php if (($this->invitadosConDatosIncompletos) && !empty($this->invitadosConDatosIncompletos)): ?>
            <h5>Invitados con datos incompletos:</h5>
            <ul>
              <?php foreach ($this->invitadosConDatosIncompletos as $invitado): ?>
                <li><strong><?php echo $invitado['documento']; ?></strong> - <?php echo $invitado['nombre']; ?>
                  (<?php echo $invitado['razon']; ?>)</li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      <?php elseif (($this->mensaje) && $this->mensaje): ?>
        <div class="alert alert-success">
          <h4><i class="fas fa-check-circle"></i> Éxito</h4>
          <p><?php echo $this->mensaje; ?></p>
        </div>
      <?php endif; ?>

      <?php if (($this->reserva)): ?>
        <div class="card">
          <div class="card-header">
            <h5><i class="fas fa-info-circle"></i> Información de la Reserva</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <p><strong>Cliente:</strong>
                  <?php echo $this->reserva->reserva_nombre_cliente . ' ' . $this->reserva->reserva_apellido_cliente; ?>
                </p>
                <p><strong>Email:</strong> <?php echo $this->reserva->reserva_correo; ?></p>
                <p><strong>Teléfono:</strong> <?php echo $this->reserva->reserva_telefono; ?></p>
              </div>
              <div class="col-md-6">
                <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($this->reserva->reserva_fecha)); ?></p>
                <p><strong>Total Personas:</strong> <?php echo $this->reserva->reserva_total_personas; ?></p>
                <p><strong>Forzar Reenvío:</strong>
                  <?php echo (($this->forzarReenvio) && $this->forzarReenvio == '1') ? 'Sí' : 'No'; ?></p>
              </div>
            </div>
          </div>
        </div>

        <?php if (($this->invitadosNuevos) && !empty($this->invitadosNuevos)): ?>
          <div class="card mt-3">
            <div class="card-header bg-success text-white">
              <h5><i class="fas fa-plus-circle"></i> Boletas Nuevas Generadas (<?php echo count($this->invitadosNuevos); ?>)
              </h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Documento</th>
                      <th>Nombre</th>
                      <th>ID Boleta</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($this->invitadosNuevos as $invitado): ?>
                      <tr>
                        <td><?php echo $invitado['documento']; ?></td>
                        <td><?php echo $invitado['nombre']; ?></td>
                        <td><?php echo $invitado['boleta_id']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if (($this->invitadosReenviados) && !empty($this->invitadosReenviados)): ?>
          <div class="card mt-3">
            <div class="card-header bg-warning text-dark">
              <h5><i class="fas fa-envelope"></i> Boletas Reenviadas (<?php echo count($this->invitadosReenviados); ?>)</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Documento</th>
                      <th>Nombre</th>
                      <th>ID Boleta</th>
                      <th>UID Boleta</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($this->invitadosReenviados as $invitado): ?>
                      <tr>
                        <td><?php echo $invitado['documento']; ?></td>
                        <td><?php echo $invitado['nombre']; ?></td>
                        <td><?php echo $invitado['boleta_id']; ?></td>
                        <td><?php echo $invitado['boleta_uid']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <?php if (($this->invitadosOmitidos) && !empty($this->invitadosOmitidos)): ?>
          <div class="card mt-3">
            <div class="card-header bg-secondary text-white">
              <h5><i class="fas fa-ban"></i> Boletas Omitidas (<?php echo count($this->invitadosOmitidos); ?>)</h5>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>Documento</th>
                      <th>Nombre</th>
                      <th>Razón</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($this->invitadosOmitidos as $invitado): ?>
                      <tr>
                        <td><?php echo $invitado['documento']; ?></td>
                        <td><?php echo $invitado['nombre']; ?></td>
                        <td><?php echo $invitado['razon']; ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        <?php endif; ?>

      <?php endif; ?>

      <div class="mt-3">
        <a href="/administracion/infoboletas" class="btn btn-primary">
          <i class="fas fa-arrow-left"></i> Volver a la Lista
        </a>
      </div>
    </div>
  </div>
</div>