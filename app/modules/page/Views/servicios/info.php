<style>
  /* ── Info de boletas ───────────────────────────────────────── */
  .rl-boletas-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0 16px;
    border-bottom: 1.5px solid #222220;
    margin-bottom: 28px;
    background: #fff;
  }

  .rl-event-header {
    border: 1.5px solid #e8e8e8;
    border-radius: 3px;
    margin-bottom: 24px;
    overflow: hidden;
  }

  .rl-event-header-top {
    background: #222220;
    color: #fff;
    padding: 14px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .rl-event-header-top h4 {
    font-family: "Orelega One", serif;
    font-size: 1.1rem;
    margin: 0;
    color: #ffc107;
    letter-spacing: 0.01em;
  }

  .rl-event-stats {
    display: flex;
    gap: 24px;
  }

  .rl-stat {
    text-align: center;
  }

  .rl-stat .num {
    font-family: "Orelega One", serif;
    font-size: 1.6rem;
    line-height: 1;
    color: #fff;
  }

  .rl-stat .lbl {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.55);
    display: block;
    margin-top: 2px;
  }

  .rl-event-header-body {
    padding: 16px 20px;
    background: #fff;
  }

  .rl-event-title {
    font-family: "Orelega One", serif;
    font-size: 1.2rem;
    color: #222220;
    margin: 0 0 12px;
  }

  .rl-meta-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 8px 16px;
  }

  .rl-meta-item {
    font-size: 0.78rem;
    color: #555;
    line-height: 1.4;
  }

  .rl-meta-item strong {
    display: block;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #7A7A7A;
    margin-bottom: 1px;
  }

  /* Tabla de boletas */
  .rl-tickets-box {
    border: 1.5px solid #e8e8e8;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 24px;
    background: #fff;
  }

  .rl-tickets-box-header {
    background: #fafafa;
    border-bottom: 1px solid #e8e8e8;
    padding: 12px 20px;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .rl-tickets-box-header h5 {
    font-family: "Orelega One", serif;
    font-size: 0.95rem;
    margin: 0;
    color: #222220;
  }

  .responsive-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
    table-layout: fixed;
  }

  .responsive-table thead th {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #7A7A7A;
    background: #fafafa;
    padding: 10px 16px;
    border-bottom: 1px solid #e8e8e8;
    text-align: left;
  }

  .responsive-table tbody tr {
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.12s;
    background: #fff;
  }

  .responsive-table tbody tr:last-child {
    border-bottom: none;
  }

  .responsive-table tbody tr:hover {
    background: #fafafa;
  }

  .responsive-table td {
    padding: 12px 16px;
    font-size: 0.82rem;
    color: #333;
    vertical-align: middle;
    background: transparent;
  }

  .rl-ticket-num {
    font-family: "Orelega One", serif;
    font-size: 0.85rem;
    color: #222220;
  }

  .rl-participant-name {
    font-weight: 700;
    font-size: 0.82rem;
    color: #222220;
    display: block;
  }

  .rl-participant-email {
    font-size: 0.73rem;
    color: #7A7A7A;
    display: block;
    margin-top: 1px;
  }

  /* Badges */
  .rl-badge-sm {
    display: inline-flex;
    align-items: center;
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.07em;
    text-transform: uppercase;
    padding: 3px 8px;
    border-radius: 2px;
    border: 1px solid transparent;
  }

  .rl-badge-sm.socio    { background:#f0faf5; color:#1a7a4a; border-color:#b2dfc8; }
  .rl-badge-sm.cosocio  { background:#f5fbff; color:#1a4a7a; border-color:#b2d0e8; }
  .rl-badge-sm.invitado { background:#f5f5f5; color:#555;    border-color:#ddd; }
  .rl-badge-sm.pendiente{ background:#fffaed; color:#7a5c00; border-color:#ffc107; }
  .rl-badge-sm.validada { background:#f0faf5; color:#1a7a4a; border-color:#b2dfc8; }
  .rl-badge-sm.cancelada{ background:#fdf5f5; color:#7a1a1a; border-color:#e2b2b2; }
  .rl-badge-sm.principal{ background:#fff8e1; color:#7a5c00; border-color:#ffc107; margin-left:4px; }

  /* Acciones */
  .rl-btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: 1.5px solid #222220;
    border-radius: 2px;
    color: #222220;
    text-decoration: none;
    transition: background 0.15s, color 0.15s;
    font-size: 0.78rem;
  }

  .rl-btn-icon:hover {
    background: #222220;
    color: #ffc107;
  }

  .rl-btn-icon.info {
    border-color: #1a4a7a;
    color: #1a4a7a;
  }

  .rl-btn-icon.info:hover {
    background: #1a4a7a;
    color: #fff;
  }

  /* Info card */
  .rl-info-card {
    border: 1.5px solid #e8e8e8;
    border-radius: 3px;
    padding: 18px 20px;
    background: #fff;
  }

  .rl-info-card h6 {
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #7A7A7A;
    margin-bottom: 14px;
  }

  .rl-info-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .rl-info-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 0.8rem;
    color: #444;
    padding: 7px 0;
    border-bottom: 1px solid #f0f0f0;
    line-height: 1.4;
  }

  .rl-info-list li:last-child { border-bottom: none; }

  .rl-info-list li i {
    font-size: 0.72rem;
    margin-top: 2px;
    flex-shrink: 0;
    width: 14px;
    text-align: center;
  }

  /* Empty */
  .rl-empty-box {
    text-align: center;
    padding: 60px 20px;
    border: 1.5px dashed #ddd;
    border-radius: 3px;
    background: #fff;
  }

  .rl-empty-box i {
    font-size: 2.2rem;
    color: #ccc;
    display: block;
    margin-bottom: 14px;
  }

  .rl-empty-box h4 {
    font-family: "Orelega One", serif;
    color: #999;
    font-size: 1.2rem;
    margin-bottom: 8px;
  }

  .rl-empty-box p {
    color: #aaa;
    font-size: 0.82rem;
    margin-bottom: 20px;
  }

  /* Responsive */
  @media screen and (max-width: 800px) {
    .rl-event-stats { gap: 16px; }
    .rl-stat .num { font-size: 1.3rem; }

    .responsive-table thead {
      display: none;
    }

    .responsive-table, .responsive-table tbody, .responsive-table tr, .responsive-table td {
      display: block;
      width: 100%;
    }

    .responsive-table tr {
      border: 1.5px solid #e8e8e8;
      border-radius: 3px;
      margin-bottom: 10px;
      padding: 12px 14px;
    }

    .responsive-table td {
      padding: 6px 0;
      border-bottom: 1px solid #f0f0f0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 0.8rem;
    }

    .responsive-table td:last-child { border-bottom: none; }

    .responsive-table td::before {
      content: attr(data-label);
      font-size: 0.62rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #7A7A7A;
      flex-shrink: 0;
      margin-right: 8px;
    }

    .rl-boletas-nav { flex-direction: column; align-items: flex-start; gap: 10px; }
    .rl-event-header-top { flex-direction: column; gap: 14px; align-items: flex-start; }
    .rl-meta-grid { grid-template-columns: 1fr 1fr; }
  }

  @media screen and (min-width: 801px) and (max-width: 1024px) {
    .responsive-table th,
    .responsive-table td { padding: 10px 12px; }
  }
</style>

<div class="container py-4" style="max-width: 960px; background: #fff; border-radius: 3px;">

  <div class="rl-boletas-nav">
    <a href="/page/guests" class="event-btn" style="font-size:0.72rem;padding:9px 16px;">Volver</a>
    <a href="/page/evento" class="event-btn btn-red" style="font-size:0.72rem;padding:9px 16px;">Realizar otra compra</a>
  </div>

  <?php if (isset($this->debugInfo)): ?>
    <div class="alert alert-info" style="font-size:0.8rem;border-radius:2px;">
      <strong>Debug:</strong>
      ID: <?= $this->debugInfo['id_enviado'] ?> |
      Reserva: <?= $this->debugInfo['reserva_encontrada'] ?> |
      Tiene boletas: <?= $this->debugInfo['tiene_boletas'] ?>
    </div>
  <?php endif; ?>

  <?php if (!$this->tieneBoletas): ?>
    <div class="rl-empty-box">
      <i class="fas fa-ticket-alt"></i>
      <h4>Sin información disponible</h4>
      <p><?= htmlspecialchars($this->mensaje) ?></p>
      <a href="/page/guests" class="event-btn" style="font-size:0.72rem;padding:10px 20px;">
        Ver mis reservas
      </a>
    </div>

  <?php else: ?>

    <!-- Header del evento -->
    <div class="rl-event-header">
      <div class="rl-event-header-top">
        <h4><i class="fas fa-ticket-alt me-2" style="font-size:0.9rem;"></i>Mis Boletas</h4>
        <div class="rl-event-stats">
          <div class="rl-stat">
            <div class="num"><?= $this->totalBoletas ?></div>
            <span class="lbl">Total</span>
          </div>
          <div class="rl-stat">
            <div class="num" style="color:#ffc107;"><?= $this->boletasValidadas ?></div>
            <span class="lbl">Validadas</span>
          </div>
        </div>
      </div>
      <div class="rl-event-header-body">
        <h5 class="rl-event-title"><?= htmlspecialchars($this->evento->evento_titulo) ?></h5>
        <div class="rl-meta-grid">
          <div class="rl-meta-item">
            <strong>Fecha del evento</strong>
            <?= date('d/m/Y', strtotime($this->evento->evento_fecha)) ?>
          </div>
          <div class="rl-meta-item">
            <strong>Hora</strong>
            <?= date('H:i', strtotime($this->evento->evento_fecha_inicio)) ?>
          </div>
          <?php if ($this->mesaInfo): ?>
            <?php foreach ($this->mesaInfo as $mesa): ?>
              <div class="rl-meta-item">
                <strong>Mesa</strong>
                <?= htmlspecialchars($mesa->mesa_nombre) ?>
              </div>
              <div class="rl-meta-item">
                <strong>Ubicación</strong>
                <?= htmlspecialchars($mesa->piso_nombre) ?> — <?= htmlspecialchars($mesa->ambiente_nombre) ?>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
          <div class="rl-meta-item">
            <strong>Reserva #</strong>
            <?= $this->reserva->id ?>
          </div>
          <div class="rl-meta-item">
            <strong>Estado</strong>
            <span class="rl-badge-sm validada">Confirmada</span>
          </div>
          <div class="rl-meta-item">
            <strong>Total personas</strong>
            <?= $this->reserva->reserva_total_personas ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Lista de boletas -->
    <div class="rl-tickets-box">
      <div class="rl-tickets-box-header">
        <i class="fas fa-list" style="font-size:0.75rem;color:#7A7A7A;"></i>
        <h5>Lista de Boletas</h5>
      </div>
      <div class="table-responsive" style="overflow:visible;">
        <table class="responsive-table">
          <thead>
            <tr>
              <th width="80">Boleta #</th>
              <th>Participante</th>
              <th width="120">Documento</th>
              <th width="100">Tipo</th>
              <th width="120">Validación</th>
              <th width="100" style="text-align:center;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($this->boletas as $boleta): ?>
              <tr>
                <td data-label="Boleta #">
                  <span class="rl-ticket-num">#<?= str_pad($boleta->boleta_id, 4, '0', STR_PAD_LEFT) ?></span>
                </td>
                <td data-label="Participante">
                  <div>
                    <span class="rl-participant-name">
                      <?= htmlspecialchars($boleta->invitado_nombre ? $boleta->invitado_nombre . ' ' . $boleta->invitado_apellido : 'N/A') ?>
                      <?php if ($boleta->es_socio_principal): ?>
                        <span class="rl-badge-sm principal"><i class="fas fa-star" style="font-size:0.55rem;"></i> Principal</span>
                      <?php endif; ?>
                    </span>
                    <?php if ($boleta->invitado_correo): ?>
                      <span class="rl-participant-email"><?= htmlspecialchars($boleta->invitado_correo) ?></span>
                    <?php endif; ?>
                  </div>
                </td>
                <td data-label="Documento">
                  <span style="font-size:0.8rem;color:#555;"><?= htmlspecialchars($boleta->invitado_documento ?? 'N/A') ?></span>
                </td>
                <td data-label="Tipo">
                  <?php
                    $tipo = $boleta->invitadoReserva_estado_invitado;
                    $tipoClass = $tipo == 'A' ? 'socio' : ($tipo == 'S' ? 'cosocio' : 'invitado');
                    $tipoText  = $tipo == 'A' ? 'Socio' : ($tipo == 'S' ? 'Cosocio' : 'Invitado');
                  ?>
                  <span class="rl-badge-sm <?= $tipoClass ?>"><?= $tipoText ?></span>
                </td>
                <td data-label="Estado">
                  <?php
                    switch ($boleta->boleta_estado) {
                      case '1': $estClass = 'pendiente'; $estText = 'Pendiente'; break;
                      case '2': $estClass = 'validada';  $estText = 'Validada';  break;
                      case '3': $estClass = 'cancelada'; $estText = 'Cancelada'; break;
                      default:  $estClass = 'invitado';  $estText = 'Sin estado'; break;
                    }
                  ?>
                  <span class="rl-badge-sm <?= $estClass ?>"><?= $estText ?></span>
                </td>
                <td data-label="Acciones" style="text-align:center;">
                  <?php if ($boleta->boleta_uid && file_exists(PDFS_PATH_NEWS . "boleta_cena_{$boleta->boleta_uid}.pdf")): ?>
                    <div class="d-flex gap-1 justify-content-center">
                      <a class="rl-btn-icon" download
                        href="/pdfs_news/boleta_cena_<?= $boleta->boleta_uid ?>.pdf"
                        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Descargar PDF">
                        <i class="fas fa-download"></i>
                      </a>
                      <a class="rl-btn-icon info"
                        href="/pdfs_news/boleta_cena_<?= $boleta->boleta_uid ?>.pdf"
                        target="_blank"
                        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Ver Detalle">
                        <i class="fas fa-eye"></i>
                      </a>
                    </div>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Info adicional -->
    <div class="row mt-2">
      <div class="col-md-6">
        <div class="rl-info-card">
          <h6><i class="fas fa-info-circle me-1"></i>Información Importante</h6>
          <ul class="rl-info-list">
            <li>
              <i class="fas fa-check" style="color:#1a7a4a;"></i>
              Presenta tu boleta digital en el ingreso al evento.
            </li>
            <li>
              <i class="fas fa-id-card" style="color:#1a4a7a;"></i>
              Lleva tu documento de identidad original.
            </li>
            <li>
              <i class="fas fa-clock" style="color:#7a5c00;"></i>
              Llega 60 minutos antes del inicio del evento.
            </li>
            <li>
              <i class="fas fa-envelope" style="color:#222220;"></i>
              Revisa tu correo electrónico para actualizaciones.
            </li>
          </ul>
        </div>
      </div>
    </div>

  <?php endif; ?>
</div>

<!-- Modal para detalles de boleta -->
<div class="modal fade" id="modalDetalleBoleta" tabindex="-1" aria-labelledby="modalDetalleBoletaLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" style="border:1.5px solid #222220;border-radius:3px;">
      <div class="modal-header" style="border-bottom:1px solid #e8e8e8;padding:14px 20px;">
        <h5 class="modal-title" id="modalDetalleBoletaLabel" style="font-family:'Orelega One',serif;font-size:1rem;color:#222220;">
          Detalle de Boleta
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalDetalleBoletaContent" style="font-size:0.85rem;"></div>
      <div class="modal-footer" style="border-top:1px solid #e8e8e8;padding:12px 20px;">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
          style="border:1.5px solid #aaa;color:#666;background:transparent;border-radius:2px;font-size:0.72rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>

<script>
  function reenviarBoletas() {
    Swal.fire({
      title: '¿Reenviar boletas?',
      text: 'Se enviarán todas tus boletas a tu correo electrónico registrado.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Continuar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#222220',
      cancelButtonColor: '#6c757d'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('/page/servicios/reenviarboletas')
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({ icon: 'success', title: 'Éxito', text: data.message, confirmButtonText: 'Continuar', confirmButtonColor: '#222220' });
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonText: 'Continuar', confirmButtonColor: '#222220' });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({ icon: 'error', title: 'Error', text: 'Ocurrió un error al reenviar las boletas', confirmButtonText: 'Continuar', confirmButtonColor: '#222220' });
          });
      }
    });
  }
</script>
