<h1 class="titulo-principal"><i class="fas fa-search"></i> <?php echo $this->titlesection; ?></h1>

<div class="container-fluid">

  <!-- Formulario de consulta -->
  <div class="content-dashboard">
    <div class="card">
      <div class="card-header">
        <h5><i class="fas fa-filter"></i> Buscar Boletas</h5>
      </div>
      <div class="card-body">
        <form action="<?php echo $this->route; ?>/consultar" method="post">


          <div class="row">
            <div class="col-md-3">
              <label for="tipo_consulta">Tipo de Consulta</label>
              <select class="form-control" name="tipo_consulta" id="tipo_consulta" required>
                <option value="">Seleccione...</option>
                <option value="reserva" <?php echo ($this->tipoConsulta == 'reserva') ? 'selected' : ''; ?>>Por Reserva
                </option>
                <option value="documento" <?php echo ($this->tipoConsulta == 'documento') ? 'selected' : ''; ?>>Por
                  Documento</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="valor_consulta">Valor a Buscar</label>
              <input type="text" class="form-control" name="valor_consulta" id="valor_consulta"
                placeholder="Ingrese el número de reserva o documento"
                value="<?php echo htmlspecialchars($this->valorConsulta); ?>" required>
            </div>

            <div class="col-md-3">
              <label>&nbsp;</label><br>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Consultar
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Mensaje de información o error -->
  <?php if (($this->mensaje)): ?>
    <div class="alert alert-warning mt-3">
      <i class="fas fa-info-circle"></i> <?php echo $this->mensaje; ?>
    </div>
  <?php endif; ?>

  <!-- Información de la reserva -->
  <?php if ($this->reservaInfo): ?>
    <div class="content-dashboard mt-3">
      <div class="card">
        <div class="card-header">
          <h5><i class="fas fa-bookmark"></i> Información de la Reserva #<?php echo $this->reservaInfo->id; ?></h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <strong>Cliente:</strong>
              <?php echo htmlspecialchars($this->reservaInfo->reserva_nombre_cliente . ' ' . $this->reservaInfo->reserva_apellido_cliente); ?><br>
              <strong>Documento:</strong> <?php echo htmlspecialchars($this->reservaInfo->reserva_documento); ?><br>
              <strong>Correo:</strong> <?php echo htmlspecialchars($this->reservaInfo->reserva_correo); ?>
            </div>
            <div class="col-md-6">
              <strong>Teléfono:</strong> <?php echo htmlspecialchars($this->reservaInfo->reserva_telefono); ?><br>
              <strong>Carnet:</strong> <?php echo htmlspecialchars($this->reservaInfo->reserva_numero_carnet); ?><br>
              <strong>Estado:</strong>
              <?php
              $estados = [1 => 'Pendiente', 2 => 'Aprobada', 3 => 'Completada', 4 => 'Cancelada'];
              echo ($estados[$this->reservaInfo->reserva_estado]) ? $estados[$this->reservaInfo->reserva_estado] : 'Desconocido';
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Resultados de boletas -->
  <?php if (($this->boletas)): ?>
    <div class="content-dashboard mt-3">
      <div class="card">
        <div class="card-header">
          <h5><i class="fas fa-ticket-alt"></i> Boletas Encontradas (<?php echo count($this->boletas); ?>)</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>UID</th>
                  <th>Reserva</th>
                  <th>Documento</th>
                  <th>Invitado</th>
                  <th>Estado</th>
                  <th>Mesa</th>
                  <th>Ambiente</th>
                  <th>Fecha Creación</th>
                  <th>Fecha Validación</th>
                  <th>Validador</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($this->boletas as $boleta): ?>
                  <tr>
                    <td><?php echo $boleta->boleta_id; ?></td>
                    <td>
                      <code><?php echo htmlspecialchars($boleta->boleta_uid); ?></code>
                    </td>
                    <td><?php echo $boleta->boleta_reserva_id; ?></td>
                    <td><?php echo htmlspecialchars($boleta->boleta_documento); ?></td>
                    <td>
                      <?php if (($boleta->invitadoInfo)): ?>
                        <?php echo htmlspecialchars($boleta->invitadoInfo->invitadoReserva_nombre_invitado) . ' ' . htmlspecialchars($boleta->invitadoInfo->invitadoReserva_apellido_invitado); ?>
                      <?php else: ?>
                        <span class="text-muted">Sin asignar</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php
                      $estadoClass = '';
                      $estadoTexto = '';
                      switch ($boleta->boleta_estado) {
                        case 1:
                          $estadoClass = 'bg-warning';
                          $estadoTexto = 'Pendiente';
                          break;
                        case 2:
                          $estadoClass = 'bg-success';
                          $estadoTexto = 'Validada';
                          break;
                        case 3:
                          $estadoClass = 'bg-danger';
                          $estadoTexto = 'Cancelada';
                          break;
                        default:
                          $estadoClass = 'bg-secondary';
                          $estadoTexto = 'Desconocido';
                      }
                      ?>
                      <span class="badge <?php echo $estadoClass; ?>">
                        <?php echo $estadoTexto; ?>
                      </span>
                    </td>
                    <td>
                      <?php if (($boleta->mesaInfo)): ?>
                        Mesa <?php echo $boleta->mesaInfo->mesa_numero; ?>
                      <?php else: ?>
                        <span class="text-muted">Sin asignar</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (($boleta->ambienteInfo)): ?>
                        <?php echo htmlspecialchars($boleta->ambienteInfo->ambiente_nombre); ?>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($boleta->boleta_fecha_creacion): ?>
                        <?php echo date('d/m/Y H:i', strtotime($boleta->boleta_fecha_creacion)); ?>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if ($boleta->boleta_fecha_validacion): ?>
                        <?php echo date('d/m/Y H:i', strtotime($boleta->boleta_fecha_validacion)); ?>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if (($boleta->usuarioValidador)): ?>
                        <?php echo htmlspecialchars($boleta->usuarioValidador->user_usuario); ?>
                      <?php else: ?>
                        <span class="text-muted">-</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Botón para volver -->
  <div class="mt-3">
    <a href="<?php echo $this->route; ?>" class="btn btn-secondary">
      <i class="fas fa-arrow-left"></i> Volver al Listado Principal
    </a>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Cambiar placeholder según el tipo de consulta seleccionado
    const tipoConsulta = document.getElementById('tipo_consulta');
    const valorConsulta = document.getElementById('valor_consulta');

    tipoConsulta.addEventListener('change', function() {
      if (this.value === 'reserva') {
        valorConsulta.placeholder = 'Ingrese el número de reserva (ej: 123)';
      } else if (this.value === 'documento') {
        valorConsulta.placeholder = 'Ingrese el número de documento';
      } else {
        valorConsulta.placeholder = 'Ingrese el número de reserva o documento';
      }
    });
  });
</script>