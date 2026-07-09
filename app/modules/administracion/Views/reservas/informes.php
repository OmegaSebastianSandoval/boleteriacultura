<h1 class="titulo-principal"><i class="fas fa-file-invoice"></i> <?php echo $this->titlesection; ?></h1>

<div class="container-fluid">
  <div class="content-dashboard">

    <p style="font-size:.85rem;color:var(--text-muted);margin-bottom:1.5rem;">
      <i class="fas fa-info-circle"></i>
      Descarga los diferentes informes de reservas e invitados en formato Excel.
    </p>

    <!-- ══ SECCIÓN INVITADOS ══ -->
    <div class="cfg-section-title">
      <span class="cfg-icon"><i class="fas fa-users"></i></span>
      Informes de invitados
    </div>

    <div class="row g-3 mb-4 mt-2">

      <div class="col-md-6">
        <div class="div-dashboard" style="margin-top:0;">
          <div class="informe-card">
            <div class="informe-icon" style="background:#3b82f620;color:#3b82f6;">
              <i class="fas fa-user-check"></i>
            </div>
            <div class="informe-body">
              <div class="informe-title">Invitados confirmados</div>
              <div class="informe-filename"><i class="fas fa-file-excel"></i> invitados_prod{hoy}.xls</div>
            </div>
            <a href="<?php echo $this->route; ?>/exportar?excel=1" class="btn btn-azul btn-sm informe-btn" target="_blank">
              <i class="fas fa-download"></i> Descargar
            </a>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="div-dashboard" style="margin-top:0;">
          <div class="informe-card">
            <div class="informe-icon" style="background:#6366f120;color:#6366f1;">
              <i class="fas fa-list-ul"></i>
            </div>
            <div class="informe-body">
              <div class="informe-title">Reservas con todos los invitados</div>
              <div class="informe-filename"><i class="fas fa-file-excel"></i> reservas_invitados{hoy}.xls</div>
            </div>
            <a href="<?php echo $this->route; ?>/exportarinvitados?excel=1" class="btn btn-azul btn-sm informe-btn" target="_blank">
              <i class="fas fa-download"></i> Descargar
            </a>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="div-dashboard" style="margin-top:0;">
          <div class="informe-card">
            <div class="informe-icon" style="background:#8b5cf620;color:#8b5cf6;">
              <i class="fas fa-id-badge"></i>
            </div>
            <div class="informe-body">
              <div class="informe-title">Reservas con invitados socios</div>
              <div class="informe-filename"><i class="fas fa-file-excel"></i> reservas_principales{hoy}.xls</div>
            </div>
            <a href="<?php echo $this->route; ?>/exportarinvitadosinv?excel=1" class="btn btn-azul btn-sm informe-btn" target="_blank">
              <i class="fas fa-download"></i> Descargar
            </a>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="div-dashboard" style="margin-top:0;">
          <div class="informe-card">
            <div class="informe-icon" style="background:#f59e0b20;color:#f59e0b;">
              <i class="fas fa-user-clock"></i>
            </div>
            <div class="informe-body">
              <div class="informe-title">Invitados faltantes por registrar</div>
              <div class="informe-filename"><i class="fas fa-file-excel"></i> reservas_faltantes{hoy}.xls</div>
            </div>
            <a href="<?php echo $this->route; ?>/exportarfaltantes?excel=1" class="btn btn-azul btn-sm informe-btn" target="_blank">
              <i class="fas fa-download"></i> Descargar
            </a>
          </div>
        </div>
      </div>

    </div>

    <!-- ══ SECCIÓN RESERVAS ══ -->
    <div class="cfg-section-title">
      <span class="cfg-icon"><i class="fas fa-calendar-check"></i></span>
      Informes de reservas
    </div>

    <div class="row g-3 mb-4 mt-2">

      <div class="col-md-6">
        <div class="div-dashboard" style="margin-top:0;">
          <div class="informe-card">
            <div class="informe-icon" style="background:#22c55e20;color:#22c55e;">
              <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="informe-body">
              <div class="informe-title">Reservas confirmadas</div>
              <div class="informe-filename"><i class="fas fa-file-excel"></i> reservas_listado{hoy}.xls</div>
            </div>
            <a href="<?php echo $this->route; ?>/exportarreservas?excel=1" class="btn btn-success btn-sm informe-btn" target="_blank">
              <i class="fas fa-download"></i> Descargar
            </a>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="div-dashboard" style="margin-top:0;">
          <div class="informe-card">
            <div class="informe-icon" style="background:#10b98120;color:#10b981;">
              <i class="fas fa-table"></i>
            </div>
            <div class="informe-body">
              <div class="informe-title">Listado completo de reservas</div>
              <div class="informe-filename"><i class="fas fa-file-excel"></i> reservas_listado{hoy}.xls</div>
            </div>
            <a href="<?php echo $this->route; ?>/exportarreservaslist?excel=1" class="btn btn-success btn-sm informe-btn" target="_blank">
              <i class="fas fa-download"></i> Descargar
            </a>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="div-dashboard" style="margin-top:0;">
          <div class="informe-card">
            <div class="informe-icon" style="background:#0ea5e920;color:#0ea5e9;">
              <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="informe-body">
              <div class="informe-title">Listado para facturación</div>
              <div class="informe-filename"><i class="fas fa-file-excel"></i> listadofacturacion_{hoy}.xls</div>
            </div>
            <a href="<?php echo $this->route; ?>/exportarlistadofacturacion?excel=1" class="btn btn-success btn-sm informe-btn" target="_blank">
              <i class="fas fa-download"></i> Descargar
            </a>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="div-dashboard" style="margin-top:0;">
          <div class="informe-card">
            <div class="informe-icon" style="background:#ef444420;color:#ef4444;">
              <i class="fas fa-user-times"></i>
            </div>
            <div class="informe-body">
              <div class="informe-title">Socios sin reservas finalizadas</div>
              <div class="informe-filename"><i class="fas fa-file-excel"></i> nofinalizadas_socio{hoy}.xls</div>
            </div>
            <a href="<?php echo $this->route; ?>/exportarnofinalizadas?excel=1" class="btn btn-success btn-sm informe-btn" target="_blank">
              <i class="fas fa-download"></i> Descargar
            </a>
          </div>
        </div>
      </div>

    </div>

  </div>
</div>

<style>
  .informe-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
  }

  .informe-icon {
    width: 46px;
    height: 46px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.15rem;
    flex-shrink: 0;
  }

  .informe-body {
    flex: 1;
    min-width: 0;
  }

  .informe-title {
    font-size: .88rem;
    font-weight: 700;
    color: var(--text-primary, #2d3748);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .informe-filename {
    font-size: .72rem;
    color: var(--text-muted, #94a3b8);
    margin-top: 3px;
  }

  .informe-filename i {
    color: #22c55e;
  }

  .informe-btn {
    white-space: nowrap;
    flex-shrink: 0;
  }

  @media (max-width: 576px) {
    .informe-card {
      flex-wrap: wrap;
    }
    .informe-btn {
      width: 100%;
      text-align: center;
    }
  }
</style>
