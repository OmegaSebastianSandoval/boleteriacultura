<h1 class="titulo-principal"><i class="fas fa-search"></i> Verificador de reserva</h1>

<style>
  .table-sm td, .table-sm th { padding: 0.3rem; font-size: 0.875rem; }
  .badge { font-size: 0.75em; }
  table { font-size: 15px; }
  .content-table .table tbody { font-size: 14px; }
  h5, h6 { text-align: start; }
</style>

<div class="container-fluid">
  <?php
  $busqueda_documento = ($this->busqueda_documento) ? $this->busqueda_documento : '';
  $busqueda_tipo = ($this->busqueda_tipo) ? $this->busqueda_tipo : null;
  $busqueda_resultados = ($this->busqueda_resultados) ? $this->busqueda_resultados : null;

  $estadosBadge = [
    1  => ['texto' => 'Reserva creada',                        'clase' => 'text-bg-primary'],
    2  => ['texto' => 'Pagada por cargo a la acción',          'clase' => 'text-bg-success'],
    3  => ['texto' => 'Pago aprobado - PlaceToPay',            'clase' => 'text-bg-success'],
    4  => ['texto' => 'Pago pendiente - PlaceToPay',           'clase' => 'text-bg-warning'],
    5  => ['texto' => 'Pago fallido - PlaceToPay',             'clase' => 'text-bg-danger'],
    6  => ['texto' => 'Pago rechazado - PlaceToPay',           'clase' => 'text-bg-danger'],
    7  => ['texto' => 'Pago pendiente - Sistema',              'clase' => 'text-bg-info'],
    8  => ['texto' => 'Cancelada por inactividad',             'clase' => 'text-bg-secondary'],
    'C'=> ['texto' => 'Cancelada',                             'clase' => 'text-bg-dark'],
    11 => ['texto' => 'Pago por datáfono',                     'clase' => 'text-bg-success'],
  ];

  function badgeEstadoInv($tipo) {
    switch ($tipo) {
      case 'S': return '<span class="badge text-bg-primary">Cosocio</span>';
      case 'A': return '<span class="badge text-bg-primary">Socio</span>';
      case 'P': return '<span class="badge text-bg-secondary">Invitado</span>';
      case 'H': return '<span class="badge text-bg-info">Hijo</span>';
      default:  return '<span class="badge text-bg-light" style="color:#333;">N/A</span>';
    }
  }
  ?>

  <div class="content-dashboard">
    <form method="get" class="row g-2 align-items-end">
      <div class="col-12 col-md-8">
        <label for="documento_busqueda" class="form-label visually-hidden">Buscar</label>
        <input type="text" class="form-control" id="documento_busqueda" name="documento_busqueda"
          placeholder="Buscar por ID de la reserva, documento o número de carnet"
          value="<?php echo htmlspecialchars($busqueda_documento); ?>" required>
      </div>
      <div class="col-6 col-md-2 d-grid">
        <button type="submit" class="btn btn-azul"><i class="fas fa-search"></i> Buscar</button>
      </div>
      <div class="col-6 col-md-2 d-grid">
        <a href="?" class="btn btn-azul-claro"><i class="fas fa-eraser"></i> Limpiar</a>
      </div>
    </form>
  </div>

  <?php if ($busqueda_documento !== ""): ?>
    <?php if (($busqueda_tipo === "principal" || $busqueda_tipo === "invitado") && !empty($busqueda_resultados)): ?>
      <?php foreach ($busqueda_resultados as $resultado): ?>
      <div class="div-dashboard mt-3 mb-3">
        <div class="pading-dashboard" style="height:auto;">
        <?php
        $reserva    = $resultado['reserva'] ?? null;
        $mesas      = $resultado['mesas'] ?? [];
        $invitados  = $resultado['invitados'] ?? [];
        $invitado   = $resultado['invitado'] ?? null;

        $primeraMesa = !empty($mesas) ? $mesas[0] : null;
        $ambienteId  = $primeraMesa ? ($primeraMesa->mesa_ambiente ?? ($primeraMesa->ambiente_id ?? null)) : null;
        $mesaId      = $primeraMesa ? ($primeraMesa->mesa_id ?? null) : null;
        $ambienteNom = $primeraMesa ? ($primeraMesa->ambiente_nombre ?? 'Ambiente') : 'Ambiente';

        $cellSizeAncho = 100; // <-- Ancho del contenedor del mapa en px (baja si se sobresale, sube si se ve pequeño)
        $cellSizeAlto  = 10; // <-- Alto del contenedor del mapa en px  (baja si se sobresale, sube si se ve pequeño)

        $iframeSrc = '';
        if ($ambienteId) {
          $iframeSrc = '/administracion/ambientes/manage?id=' . $ambienteId . '&display=1&solo_mapa=1&px_w=' . $cellSizeAncho . '&px_h=' . $cellSizeAlto;
          if ($mesaId) $iframeSrc .= '&destacar_mesa=' . $mesaId . '&modo=validacion';
        }

        $estadoKey  = $reserva ? ($reserva->reserva_estado ?? 0) : 0;
        $estadoInfo = isset($estadosBadge[$estadoKey]) ? $estadosBadge[$estadoKey] : ['texto' => 'Sin estado', 'clase' => 'text-bg-light'];
        ?>

        <!-- Encabezado del resultado -->
        <div class="row g-3 mb-2 mt-1 align-items-center" style="padding: 0 12px;">
          <div class="col-auto">
            <?php if ($busqueda_tipo === "principal"): ?>
              <span style="font-size:1.4rem;color:var(--brand-green);"><i class="fas fa-user-check"></i></span>
            <?php else: ?>
              <span style="font-size:1.4rem;color:var(--brand);"><i class="fas fa-user-friends"></i></span>
            <?php endif; ?>
          </div>
          <div class="col">
            <strong style="font-size:1rem;">
              <?php echo $busqueda_tipo === "principal" ? 'Titular de la reserva' : 'Invitado de la reserva'; ?>
            </strong>
            <?php if ($reserva): ?>
              &nbsp;<span class="badge bg-secondary">#<?php echo htmlspecialchars($reserva->id ?? ''); ?></span>
              &nbsp;<span class="badge <?php echo $estadoInfo['clase']; ?>"><?php echo $estadoInfo['texto']; ?></span>
            <?php endif; ?>
          </div>
        </div>

        <!-- FILA PRINCIPAL: info general col-6 | mapa col-6 -->
        <?php if ($reserva): ?>
        <div class="row g-3 mb-3" style="align-items:stretch;">

          <!-- COL IZQUIERDA: info general -->
          <div class="col-md-6 d-flex">
            <div class="div-dashboard" style="margin-top:0;width:100%;display:flex;flex-direction:column;">
              <h2><i class="fas fa-info-circle"></i> Información general</h2>
              <div style="flex:1;padding:14px 15px;overflow-y:auto;">

                <?php if ($busqueda_tipo === "invitado" && $invitado): ?>
                  <p class="cfg-section-title" style="margin-bottom:10px;">
                    <i class="fas fa-user-friends cfg-icon" style="background:var(--brand);"></i>Datos del invitado
                  </p>
                  <table class="table table-striped table-hover table-administrator text-start mb-0">
                    <tr>
                      <td style="color:var(--text-muted);width:130px;white-space:nowrap;padding:3px 0;">Nombre</td>
                      <td style="padding:3px 0;"><strong><?php echo htmlspecialchars(trim(($invitado->invitadoReserva_nombre_invitado ?? '') . ' ' . ($invitado->invitadoReserva_apellido_invitado ?? ''))); ?></strong></td>
                    </tr>
                    <?php if (!empty($invitado->invitadoReserva_correo_invitado)): ?>
                    <tr>
                      <td style="color:var(--text-muted);padding:3px 0;">Correo</td>
                      <td style="padding:3px 0;"><?php echo htmlspecialchars($invitado->invitadoReserva_correo_invitado); ?></td>
                    </tr>
                    <?php endif; ?>
                  </table>
                  <hr style="border-color:var(--border);margin:12px 0;">
                <?php endif; ?>

                <p class="cfg-section-title" style="margin-bottom:10px;">
                  <i class="fas fa-id-card cfg-icon" style="background:var(--brand);"></i>Datos del titular
                </p>
                <table class="table table-striped table-hover table-administrator text-start mb-0">
                  <tr>
                    <td style="color:var(--text-muted);width:130px;white-space:nowrap;padding:3px 0;">Nombre</td>
                    <td style="padding:3px 0;"><strong><?php echo htmlspecialchars(trim(($reserva->reserva_nombre_cliente ?? '') . ' ' . ($reserva->reserva_apellido_cliente ?? ''))); ?></strong></td>
                  </tr>
                  <?php if (!empty($reserva->reserva_documento)): ?>
                  <tr>
                    <td style="color:var(--text-muted);padding:3px 0;">CC</td>
                    <td style="padding:3px 0;"><strong><?php echo htmlspecialchars($reserva->reserva_documento); ?></strong></td>
                  </tr>
                  <?php endif; ?>
                  <?php if (!empty($reserva->reserva_numero_carnet)): ?>
                  <tr>
                    <td style="color:var(--text-muted);padding:3px 0;">N&deg; Acción</td>
                    <td style="padding:3px 0;"><strong><?php echo htmlspecialchars($reserva->reserva_numero_carnet); ?></strong></td>
                  </tr>
                  <?php endif; ?>
                  <?php if (!empty($reserva->reserva_telefono)): ?>
                  <tr>
                    <td style="color:var(--text-muted);padding:3px 0;">Teléfono</td>
                    <td style="padding:3px 0;"><?php echo htmlspecialchars($reserva->reserva_telefono); ?></td>
                  </tr>
                  <?php endif; ?>
                  <?php if (!empty($reserva->reserva_correo)): ?>
                  <tr>
                    <td style="color:var(--text-muted);padding:3px 0;">Correo</td>
                    <td style="padding:3px 0;"><?php echo htmlspecialchars($reserva->reserva_correo); ?></td>
                  </tr>
                  <?php endif; ?>
                </table>

                <hr style="border-color:var(--border);margin:12px 0;">

                <p class="cfg-section-title" style="margin-bottom:10px;">
                  <i class="fas fa-calendar-alt cfg-icon" style="background:var(--brand-green);"></i>Datos de la reserva
                </p>
                <table class="table table-striped table-hover table-administrator text-start mb-0">
                  <?php if (!empty($reserva->reserva_fecha)): ?>
                  <tr>
                    <td style="color:var(--text-muted);width:130px;white-space:nowrap;padding:3px 0;">Fecha</td>
                    <td style="padding:3px 0;"><?php echo htmlspecialchars($reserva->reserva_fecha); ?></td>
                  </tr>
                  <?php endif; ?>
                  <?php if (!empty($reserva->reserva_hora)): ?>
                  <tr>
                    <td style="color:var(--text-muted);padding:3px 0;">Hora</td>
                    <td style="padding:3px 0;"><?php echo htmlspecialchars($reserva->reserva_hora); ?></td>
                  </tr>
                  <?php endif; ?>
                  <?php if (!empty($reserva->reserva_total_personas)): ?>
                  <tr>
                    <td style="color:var(--text-muted);padding:3px 0;">Personas</td>
                    <td style="padding:3px 0;"><?php echo htmlspecialchars($reserva->reserva_total_personas); ?></td>
                  </tr>
                  <?php endif; ?>
                  <?php if (!empty($primeraMesa)): ?>
                  <tr>
                    <td style="color:var(--text-muted);padding:3px 0;">Ambiente</td>
                    <td style="padding:3px 0;"><?php echo htmlspecialchars($ambienteNom); ?></td>
                  </tr>
                  <tr>
                    <td style="color:var(--text-muted);padding:3px 0;">Mesa</td>
                    <td style="padding:3px 0;"><span class="badge text-bg-success"><i class="fas fa-chair"></i> <?php echo htmlspecialchars($primeraMesa->mesa_nombre ?? ''); ?></span></td>
                  </tr>
                  <?php endif; ?>
                </table>

              </div>
            </div>
          </div>

          <!-- COL DERECHA: mapa del ambiente -->
          <div class="col-md-6 d-flex">
            <?php if ($iframeSrc): ?>
            <div class="div-dashboard" style="margin-top:0;width:100%;display:flex;flex-direction:column;">
              <h2><i class="fas fa-map-marked-alt"></i> <?php echo htmlspecialchars($ambienteNom); ?></h2>
              <div style="flex:1;min-height:350px;position:relative;overflow:hidden;">
                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:var(--bg);z-index:2;">
                  <div class="spinner-border spinner-border-sm text-secondary"></div>
                </div>
                <iframe src="<?php echo htmlspecialchars($iframeSrc); ?>"
                  style="width:100%;height:100%;border:none;display:block;"
                  onload="this.previousElementSibling.style.display='none'"></iframe>
              </div>
            </div>
            <?php else: ?>
            <div class="div-dashboard" style="margin-top:0;width:100%;display:flex;flex-direction:column;">
              <h2><i class="fas fa-map"></i> Ambiente / Mesa</h2>
              <div style="flex:1;display:flex;align-items:center;justify-content:center;min-height:200px;">
                <div class="text-center text-muted" style="font-size:.85rem;">
                  <i class="fas fa-map fa-2x mb-2 d-block"></i>Sin ambiente asignado
                </div>
              </div>
            </div>
            <?php endif; ?>
          </div>

        </div>
        <?php endif; ?>

        <!-- MESAS ASIGNADAS -->
        <?php if (!empty($mesas)): ?>
        <div class="div-dashboard mb-3" style="margin-top:0;">
          <h2><i class="fas fa-chair"></i> Mesas asignadas (<?php echo count($mesas); ?>)</h2>
          <div class="pading-dashboard" style="height:auto;">
            <div class="content-table mb-3">
              <table class="table table-striped table-hover table-administrator text-start mb-0">
                <thead>
                  <tr>
                    <th>Mesa</th>
                    <th>Código</th>
                    <th>Capacidad</th>
                    <th>Ambiente</th>
                    <th>Piso</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($mesas as $mesa): ?>
                  <tr>
                    <td><span class="badge text-bg-success"><i class="fas fa-chair"></i> <?php echo htmlspecialchars($mesa->mesa_nombre ?? ''); ?></span></td>
                    <td><?php echo htmlspecialchars($mesa->mesa_codigo ?? '—'); ?></td>
                    <td><?php echo !empty($mesa->mesa_capacidad) ? htmlspecialchars($mesa->mesa_capacidad) . ' pers.' : '—'; ?></td>
                    <td><?php echo htmlspecialchars($mesa->ambiente_nombre ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($mesa->piso_nombre ?? '—'); ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <!-- LISTA DE INVITADOS -->
        <?php if (!empty($invitados)): ?>
        <div class="div-dashboard mb-3" style="margin-top:0;">
          <h2><i class="fas fa-users"></i> Invitados (<?php echo count($invitados); ?>)</h2>
          <div class="pading-dashboard" style="height:auto;">
            <div class="content-table mb-3">
              <table class="table table-striped table-hover table-administrator text-start mb-0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Documento</th>
                    <th>N&deg; Acción</th>
                    <th>Correo</th>
                    <th>Tipo</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($invitados as $i => $inv): ?>
                  <tr>
                    <td><span class="badge bg-secondary"><?php echo $i + 1; ?></span></td>
                    <td><strong><?php echo htmlspecialchars(trim(($inv->invitadoReserva_nombre_invitado ?? '') . ' ' . ($inv->invitadoReserva_apellido_invitado ?? ''))); ?></strong></td>
                    <td><?php echo htmlspecialchars($inv->documento_invitado ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($inv->invitadoReserva_numero_carnet ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($inv->invitadoReserva_correo_invitado ?? '—'); ?></td>
                    <td><?php echo badgeEstadoInv($inv->invitadoReserva_estado_invitado ?? ''); ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <?php endif; ?>

        </div>
      </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="alert alert-primary mt-3" role="alert">
        <i class="fas fa-exclamation-triangle"></i> No se encontraron reservas asociadas a ese ID, documento o número de carnet.
      </div>
    <?php endif; ?>
  <?php endif; ?>
</div>
