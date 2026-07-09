<style>
  .minimal-table {
    font-size: 0.85rem;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  }

  .minimal-table th {
    background: #f8f9fa;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 12px 8px;
    border: none;
    color: #495057;
  }

  .minimal-table td {
    padding: 10px 8px;
    border: none;
    border-bottom: 1px solid #f1f3f4;
    vertical-align: middle;
  }

  .minimal-table tbody tr:hover {
    background-color: #f8f9fa;
  }

  .minimal-badge {
    font-size: 0.7rem;
    padding: 4px 8px;
    border-radius: 12px;
    font-weight: 500;
  }

  .page-title {
    font-size: 1.5rem;
    font-weight: 300;
    color: #FFF;
    margin-bottom: 1.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
  }

  .stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
  }
</style>

<div class="container-fluid" style="padding: 20px;">
  <div class="row">
    <div class="col-12">
      <h1 class="page-title">Socios Menores - Revisi&oacute;n de Datos</h1>

      <?php if (is_countable($this->cosociosmenores) && count($this->cosociosmenores) >= 1): ?>
        <div class="stats-card">
          <strong>Total de registros encontrados:</strong> <?php echo count($this->cosociosmenores); ?> socios menores
        </div>

        <div class="table-responsive">
          <table class="table minimal-table">
            <thead>
              <tr>
                <th style="width: 80px;">ID</th>
                <th style="width: 80px;">Reserva</th>
                <th style="width: 120px;">Documento</th>
                <th>Nombre</th>
                <th style="width: 110px;">F. Nacimiento</th>
                <th style="width: 80px;">Estado</th>
                <th style="width: 80px;">
                  < 25</th>
                <th style="width: 70px;">Hijo</th>
                <th style="width: 80px;">Principal</th>
                <th style="width: 100px;">Pago</th>
                <th style="width: 140px;">F. Creacion</th>
                <th style="width: 100px;">Usuario</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($this->cosociosmenores as $menor): ?>
                <tr>
                  <td><small class="text-muted"><?php echo htmlspecialchars($menor->id_invitado); ?></small></td>
                  <td><small class="text-muted"><?php echo htmlspecialchars($menor->reserva_id_reserva); ?></small></td>
                  <td><strong><?php echo htmlspecialchars($menor->documento_invitado); ?></strong></td>
                  <td><?php echo htmlspecialchars($menor->invitadoReserva_nombre_invitado); ?></td>
                  <td><small><?php echo htmlspecialchars($menor->invitadoReserva_fecha_nacimiento); ?></small></td>
                  <td>
                    <span
                      class="badge minimal-badge text-bg-<?php echo $menor->invitadoReserva_estado_invitado == 'S' ? 'success' : 'secondary'; ?>">
                      <?php echo $menor->invitadoReserva_estado_invitado == 'S' ? 'Cosocio' : $menor->invitadoReserva_estado_invitado; ?>
                    </span>
                  </td>
                  <td>
                    <span
                      class="badge minimal-badge text-bg-<?php echo $menor->invitadoReserva_beneficiario_menor25 == '1' ? 'warning' : 'light text-dark'; ?>">
                      <?php echo $menor->invitadoReserva_beneficiario_menor25 == '1' ? 'Si' : 'No'; ?>
                    </span>
                  </td>
                  <td>
                    <span
                      class="badge minimal-badge text-bg-<?php echo $menor->invitadoReserva_beneficiario_hijo == '1' ? 'info' : 'light text-dark'; ?>">
                      <?php echo $menor->invitadoReserva_beneficiario_hijo == '1' ? 'Si' : 'No'; ?>
                    </span>
                  </td>
                  <td>
                    <span
                      class="badge minimal-badge text-bg-<?php echo $menor->invitadoReserva_beneficiario_principal == '1' ? 'primary' : 'light text-dark'; ?>">
                      <?php echo $menor->invitadoReserva_beneficiario_principal == '1' ? 'Si' : 'No'; ?>
                    </span>
                  </td>
                  <td><small class="text-muted"><?php echo htmlspecialchars($menor->reserva_metodo_pago); ?></small></td>
                  <td><small
                      class="text-muted"><?php echo date('d/m/Y H:i', strtotime($menor->invitadosReserva_fecha_creacion)); ?></small>
                  </td>
                  <td><small
                      class="text-muted"><?php echo htmlspecialchars($menor->invitadosReserva_usuario_creacion); ?></small>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

      <?php else: ?>
        <div class="alert alert-light border" style="text-align: center; padding: 2rem;">
          <h5 class="text-muted">📋 No se encontraron registros</h5>
          <p class="text-muted mb-0">No hay socios menores con fallos en este momento.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>