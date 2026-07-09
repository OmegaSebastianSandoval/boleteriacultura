<?php $totalPagar = (float) ($this->reserva->reserva_total_pagar ?? 0); ?>

<div class="av-wrap container py-2 py-lg-3 mb-3 mb-lg-5">
  <div class="card-principal av-card">

    <!-- ── Encabezado de éxito ── -->
    <div class="av-success-header text-center">
      <div class="av-success-icon"><i class="fa-solid fa-check"></i></div>
      <h1 class="av-title">¡Pago exitoso!</h1>
      <p class="av-subtitle">Será un gusto reencontrarnos</p>
    </div>

    <?php if ($this->reserva): ?>
      <!-- ── Resumen de la compra ── -->
      <div class="rs-card av-summary-card mb-3">
        <div class="rs-card-header d-flex justify-content-between align-items-center">
          <h5><i class="fa-solid fa-receipt me-2"></i>Resumen de tu reserva</h5>
          <span class="rs-badge">#<?= (int) $this->reserva->id ?></span>
        </div>
        <div class="rs-card-body">
          <?php if ($this->evento && $this->evento->evento_titulo): ?>
            <p class="rs-data-row">
              <span class="rs-data-key">Evento</span>
              <span class="rs-data-val"><?= htmlspecialchars($this->evento->evento_titulo) ?></span>
            </p>
          <?php endif; ?>
          <?php if ($this->evento && $this->evento->evento_fecha_inicio): ?>
            <p class="rs-data-row">
              <span class="rs-data-key">Fecha</span>
              <span class="rs-data-val"><?= date('d/m/Y', strtotime($this->evento->evento_fecha_inicio)) ?></span>
            </p>
          <?php endif; ?>
          <p class="rs-data-row">
            <span class="rs-data-key">Personas</span>
            <span class="rs-data-val"><?= (int) $this->reserva->reserva_total_personas ?></span>
          </p>
          <p class="rs-data-row <?= $totalPagar > 0 ? '' : 'rs-data-row-last' ?>">
            <span class="rs-data-key">Estado</span>
            <span class="rs-data-val">
              <span class="av-status-pill">
                <i class="fa-solid fa-circle-check"></i> <?= htmlspecialchars($this->estadoTexto) ?>
              </span>
            </span>
          </p>
          <?php if ($totalPagar > 0): ?>
            <p class="rs-data-row rs-data-row-last av-total-row">
              <span class="rs-data-key">Total pagado</span>
              <span class="rs-data-val av-total-val">$<?= number_format($totalPagar, 0, ',', '.') ?></span>
            </p>
          <?php endif; ?>
        </div>

        <?php if ($this->mesaInfo): ?>
          <div class="rs-card-header av-sub-header">
            <i class="fa-solid fa-table me-2"></i>
            <h5>Mesa<?= count($this->mesaInfo) != 1 ? 's' : '' ?> reservada<?= count($this->mesaInfo) != 1 ? 's' : '' ?></h5>
          </div>
          <div class="rs-card-body p-0">
            <?php foreach ($this->mesaInfo as $index => $mesa): ?>
              <?php
              $mesaNombre = $mesa->mesa_nombre ?: ($mesa->mesa_codigo ? 'Mesa ' . $mesa->mesa_codigo : 'Mesa #' . $mesa->mesa_id);
              $pisoNombre = $mesa->piso_nombre ?: '';
              ?>
              <div class="rs-mesa-bloque">
                <div class="rs-mesa-dato">
                  <span class="rs-mesa-key">Mesa</span>
                  <span class="rs-mesa-val"><?= htmlspecialchars($mesaNombre) ?></span>
                </div>
                <div class="rs-mesa-dato">
                  <span class="rs-mesa-key">Capacidad</span>
                  <span class="rs-mesa-val"><?= (int) $mesa->mesa_capacidad ?> personas</span>
                </div>
                <?php if ($pisoNombre): ?>
                  <div class="rs-mesa-dato">
                    <span class="rs-mesa-key">Piso</span>
                    <span class="rs-mesa-val"><?= htmlspecialchars($pisoNombre) ?></span>
                  </div>
                <?php endif; ?>
                <div class="rs-mesa-dato rs-mesa-dato-last">
                  <span class="rs-mesa-key">Ambiente</span>
                  <span class="rs-mesa-val"><?= htmlspecialchars($mesa->ambiente_nombre) ?></span>
                </div>
              </div>
              <?php if ($index < count($this->mesaInfo) - 1): ?>
                <div class="rs-mesa-sep"></div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <!-- ── Nota invitados ── -->
    <div class="av-note">
      <i class="fas fa-info-circle me-2"></i>
      <span>
        Le agradecemos diligenciar la información requerida para el registro de sus invitados, la factura electrónica y el menú gastronómico.
        En caso de no haber finalizado el proceso, podrá completarlo iniciando sesión nuevamente en la plataforma.
      </span>
    </div>

    <!-- ── Acciones ── -->
    <div class="av-actions">
      <a href="/page/guests?id=<?= enc_id($this->id) ?>" class="av-btn-primary">
        <i class="fa-solid fa-clipboard-check"></i> Finalizar detalles de la reserva
      </a>
      <a href="/page/evento/reservar/" class="av-btn-ghost">
        Seguir comprando <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>

  </div>
</div>


<style>
  /* ============================================================
   AVISO DE PAGO — Dark glass (misma cascada que reservar / resumen)
  ============================================================ */

  .contenedor-general {
    height: calc(100vh - 60px - 30px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding-bottom: 0 !important;
  }

  .av-wrap {
    max-width: 640px;
    margin: 0 auto;
    flex: 1;
    min-height: 0;
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
  }

  .card-principal.av-card {
    background-color: rgba(21, 25, 29, 1);
    padding: 1.35rem 1.5rem;
    border-radius: 6px;
    max-height: 100%;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }

  /* ── Encabezado de éxito ── */
  .av-success-header {
    margin-bottom: 0.85rem;
    flex-shrink: 0;
  }

  .av-success-icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(255, 193, 7, 0.12);
    border: 1px solid rgba(255, 193, 7, 0.35);
    color: #ffc107;
    font-size: 1.7rem;
    box-shadow: 0 0 0 6px rgba(255, 193, 7, 0.05);
  }

  .av-title {
    font-size: 1.3rem;
    font-weight: 500;
    letter-spacing: 1px;
    color: #ffffff;
    margin-bottom: 0.2rem;
  }

  .av-subtitle {
    font-size: 0.68rem;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.5);
    margin: 0;
  }

  /* ── Resumen card ── */
  .av-summary-card {
    flex-shrink: 1;
    min-height: 0;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
  }

  .rs-card {
    background: rgba(6, 6, 6, 0.55);
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 6px;
    overflow: hidden;
  }

  .rs-card-header {
    background: rgba(255, 255, 255, 0.04);
    border-bottom: 1px solid rgba(255, 255, 255, 0.07);
    padding: 6px 14px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
  }

  .rs-card-header i {
    color: #ffc107;
    font-size: 0.9rem;
  }

  .rs-card-header h5 {
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: white;
    font-weight: 700;
    margin: 0;
  }

  .av-sub-header {
    border-top: 1px solid rgba(255, 255, 255, 0.07);
  }

  .rs-card-body {
    padding: 6px 16px;
  }

  .rs-card-body.p-0 {
    padding: 0 !important;
    max-height: 22vh;
    overflow-y: auto;
  }

  .rs-badge {
    display: inline-block;
    padding: 3px 12px;
    border-radius: 20px;
    background: #ffc107;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    color: #111;
  }

  /* ── Data rows ── */
  .rs-data-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 8px;
    font-size: 0.8rem;
    padding: 5px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    margin-bottom: 0 !important;
  }

  .rs-data-row-last {
    border-bottom: none !important;
  }

  .rs-data-key {
    color: rgba(255, 255, 255, 0.55);
    font-weight: 600;
    white-space: nowrap;
  }

  .rs-data-val {
    color: #fff;
    font-weight: 600;
    text-align: right;
  }

  .av-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    padding: 3px 10px;
    border-radius: 20px;
    background: rgba(40, 167, 69, 0.18);
    color: #6fcf97;
    border: 1px solid rgba(40, 167, 69, 0.3);
  }

  .av-total-row {
    padding-top: 8px;
    margin-top: 2px;
    border-top: 1px dashed rgba(255, 193, 7, 0.25) !important;
  }

  .av-total-val {
    font-size: 1.05rem;
    color: #ffc107;
  }

  /* ── Mesas ── */
  .rs-mesa-bloque {
    display: grid;
    grid-template-columns: 1fr 1fr;
  }

  .rs-mesa-dato {
    padding: 5px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  }

  .rs-mesa-dato:nth-child(odd) {
    border-right: 1px solid rgba(255, 255, 255, 0.06);
  }

  .rs-mesa-dato-last {
    border-bottom: none !important;
  }

  .rs-mesa-key {
    display: block;
    font-size: 0.66rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255, 255, 255, 0.4);
    font-weight: 700;
    margin-bottom: 2px;
  }

  .rs-mesa-val {
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
  }

  .rs-mesa-sep {
    height: 1px;
    background: rgba(255, 193, 7, 0.15);
    margin: 0;
  }

  /* ── Nota ── */
  .av-note {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.85rem;
    margin: 0.75rem 0 0;
    font-size: 0.82rem;
    line-height: 1.3;
    color: rgba(255, 255, 255, 0.7);
    background: rgba(74, 144, 226, 0.08);
    border-left: 3px solid #4a90e2;
    border-radius: 4px;
    flex-shrink: 0;
  }

  .av-note i {
    flex-shrink: 0;
  }

  /* ── Acciones ── */
  .av-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 0.65rem;
    padding-top: 0.75rem;
    flex-shrink: 0;
  }

  .av-btn-primary,
  .av-btn-ghost {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.4px;
    text-transform: uppercase;
    padding: 9px 26px;
    min-width: 200px;
    border-radius: 20px;
    text-decoration: none;
    transition: background 0.18s ease, transform 0.15s ease, color 0.18s ease;
  }

  .av-btn-primary {
    background: #ffc107;
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: #111 !important;
  }

  .av-btn-primary:hover {
    background: #ffd54f;
    transform: translateY(-1px);
    color: #111 !important;
  }

  .av-btn-ghost {
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.15);
    color: rgba(255, 255, 255, 0.85) !important;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
  }

  .av-btn-ghost:hover {
    background: rgba(255, 255, 255, 0.13);
    color: #fff !important;
  }

  /* ── Responsive ── */
  @media (max-width: 767px) {
    .contenedor-general { height: auto !important; min-height: calc(100vh - 60px - 30px); overflow: visible; }
    .av-wrap { overflow: visible; }
    .card-principal.av-card { padding: 1.1rem 1rem; max-height: none; overflow: visible; }
    .av-summary-card { overflow: visible; }
    .av-title { font-size: 1.35rem; }
    .rs-mesa-bloque { grid-template-columns: 1fr; }
    .rs-mesa-dato:nth-child(odd) { border-right: none; }
    .av-actions { flex-direction: column; align-items: stretch; }
    .av-btn-primary, .av-btn-ghost { width: 100%; }
  }
</style>
