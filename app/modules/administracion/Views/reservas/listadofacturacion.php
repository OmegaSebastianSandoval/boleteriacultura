<?php
// Helper para obtener el estado de la reserva (puedes ajustar si lo necesitas)
function getEstadoReservaPHP($estado)
{
  $estados = array(
    '1' => array('texto' => 'Reserva creada', 'badge_class' => 'text-bg-primary'),
    '2' => array('texto' => 'Cargo a la acción', 'badge_class' => 'text-bg-success'),
    '3' => array('texto' => 'Pago en línea', 'badge_class' => 'text-bg-success'),
    '4' => array('texto' => 'Reserva pago pendiente - PlaceToPay', 'badge_class' => 'text-bg-warning'),
    '5' => array('texto' => 'Reserva pago fallido - PlaceToPay', 'badge_class' => 'text-bg-danger'),
    '6' => array('texto' => 'Reserva pago rechazado - PlaceToPay', 'badge_class' => 'text-bg-danger'),
    '7' => array('texto' => 'Reserva pago pendiente - Sistema', 'badge_class' => 'text-bg-info'),
    '8' => array('texto' => 'Reserva cancelada por inactividad', 'badge_class' => 'text-bg-secondary'),
    'C' => array('texto' => 'Reserva cancelada', 'badge_class' => 'text-bg-dark'),
    '11'=> array('texto' => 'Pago por datáfono', 'badge_class' => 'text-bg-success'),
  );
  return ($estados[$estado]) ? $estados[$estado] : array('texto' => 'Estado desconocido', 'badge_class' => 'text-bg-light');
}

$estadometodo = [
  'cargo' => 'Cargo a la acción',
  'linea' => 'Pago en línea',
  'datafono' => 'Pago por datáfono',
];


?>
<h1 class="titulo-principal"><i class="fas fa-file-invoice"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">

  <!-- Filtros -->
  <div class="content-dashboard">
    <form method="post" action="<?php echo $this->route; ?>/listadofacturacion">
      <input type="hidden" name="_token" value="<?php echo $this->csrf; ?>">
      <div class="row">
        <div class="col-2 mb-3">
          <label>Fecha inicio</label>
          <label class="input-group">
            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
              value="<?php echo isset($this->filters->fecha_inicio) ? $this->filters->fecha_inicio : ''; ?>">
          </label>
        </div>
        <div class="col-2 mb-3">
          <label>Fecha fin</label>
          <label class="input-group">
            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
              value="<?php echo isset($this->filters->fecha_fin) ? $this->filters->fecha_fin : ''; ?>">
          </label>
        </div>
        <div class="col-2 mb-3">
          <label>Cliente</label>
          <label class="input-group">
            <input type="text" class="form-control" name="reserva_nombre_cliente"
              value="<?php echo isset($this->filters->reserva_nombre_cliente) ? $this->filters->reserva_nombre_cliente : ''; ?>"
              placeholder="Nombre o apellido">
          </label>
        </div>
        <div class="col-2 mb-3">
          <label>Documento</label>
          <label class="input-group">
            <input type="text" class="form-control" name="reserva_documento"
              value="<?php echo isset($this->filters->reserva_documento) ? $this->filters->reserva_documento : ''; ?>">
          </label>
        </div>
        <div class="col-2 mb-3">
          <label>Teléfono</label>
          <label class="input-group">
            <input type="text" class="form-control" name="reserva_telefono"
              value="<?php echo isset($this->filters->reserva_telefono) ? $this->filters->reserva_telefono : ''; ?>">
          </label>
        </div>
        <div class="col-2 mb-3">
          <label>No. carnet</label>
          <label class="input-group">
            <input type="text" class="form-control" name="reserva_numero_carnet"
              value="<?php echo isset($this->filters->reserva_numero_carnet) ? $this->filters->reserva_numero_carnet : ''; ?>">
          </label>
        </div>
        <div class="col-2 mb-3">
          <label>No. reserva</label>
          <label class="input-group">
            <input type="text" class="form-control" name="id"
              value="<?php echo isset($this->filters->id) ? $this->filters->id : ''; ?>">
          </label>
        </div>
        <div class="col-2 mb-3">
          <label>Ambiente</label>
          <select class="form-select" name="ambiente_id">
            <option value="">Todos</option>
            <?php foreach ($this->ambientes as $amb): ?>
              <option value="<?php echo $amb->ambiente_id; ?>"
                <?php echo (isset($this->filters->ambiente_id) && $this->filters->ambiente_id == $amb->ambiente_id) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($amb->ambiente_nombre); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-2 mb-3">
          <label>Código de mesa</label>
          <input type="text" class="form-control" name="mesa_codigo"
              value="<?php echo isset($this->filters->mesa_codigo) ? $this->filters->mesa_codigo : ''; ?>">
          <!--<select class="form-select" name="mesa_id">
            <option value="">Todas</option>
            <?php foreach ($this->mesas_select as $mesa): ?>
              <option value="<?php echo $mesa->mesa_id; ?>"
                <?php echo (isset($this->filters->mesa_id) && $this->filters->mesa_id == $mesa->mesa_id) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($mesa->mesa_nombre . ($mesa->mesa_codigo ? ' (' . $mesa->mesa_codigo . ')' : '')); ?>
              </option>
            <?php endforeach; ?>
          </select> -->
        </div>
        <div class="col-1 mb-3">
          <label>&nbsp;</label>
          <button type="submit" class="btn btn-block btn-azul" data-bs-toggle="tooltip" title="Filtrar">
            <i class="fas fa-filter"></i>
          </button>
        </div>
        <div class="col-1 mb-3">
          <label>&nbsp;</label>
          <a class="btn btn-block btn-azul-claro" data-bs-toggle="tooltip" title="Limpiar filtro"
            href="<?php echo $this->route; ?>/listadofacturacion?cleanfilter=1">
            <i class="fas fa-eraser"></i>
          </a>
        </div>
        <div class="col-1 mb-3">
          <label>&nbsp;</label>
          <a target="_blank" href="<?php echo $this->route; ?>/exportarlistadofacturacion?excel=1"
            class="btn btn-block btn-verde" data-bs-toggle="tooltip" title="Exportar a Excel">
            <i class="fas fa-file-excel"></i>
          </a>
        </div>
      </div>
    </form>
  </div>

  <!-- Tabla -->
  <div class="div-dashboard mt-3 mb-3">
    <h2><i class="fas fa-file-invoice"></i> Listado de facturación</h2>
    <div class="pading-dashboard" style="height:auto;">
    <div class="content-table mb-3">

      <div id="scroll-top-bar" style="overflow-x:auto; overflow-y:hidden; width:100%; height:18px; margin-bottom:2px;">
      </div>
      <div class="table-responsive" id="scroll-bottom-bar">
        <table class="table table-striped table-hover table-administrator listado-facturacion-table"
          style="min-width:1200px;">
          <thead>
            <tr>
              <td class="text-center">No. reserva</td>
              <td>Nombre</td>
              <td>Apellido</td>
              <td>Documento</td>
              <td>No. carnet</td>
              <td>Teléfono</td>
              <td>Ambiente</td>
              <td>Códigos Mesa</td>
              <td>Pago</td>
              <td>Cuotas</td>
              <td>Franquicia</td>
              <td>Fecha de la reserva</td>
              <td>Estado</td>
              <td class="text-center">Cant. invitados<br><span style="font-weight:normal;font-size:11px;">(BD)</span></td>
              <td class="text-center">Cant. invitados<br><span style="font-weight:normal;font-size:11px;">(Calculado)</span></td>
              <td class="text-center border-start">Socio</td>
              <td class="text-end">Total socio</td>
              <td class="text-center">Socio hijo &lt; 25</td>
              <td class="text-end">Total socio hijo &lt; 25</td>
              <td class="text-center border-start">Cosocio</td>
              <td class="text-end">Total cosocio</td>
              <td class="text-center">Cosocio hijo &lt; 25</td>
              <td class="text-end">Total cosocio hijo &lt; 25</td>
              <td class="text-center border-start">Invitado</td>
              <td class="text-end">Total invitado</td>
              <td class="text-end border-start">Valor total calculado</td>
              <td class="text-end">Valor total de la bd</td>
              <td class="text-center border-start">Acciones</td>
            </tr>
          </thead>
          <tbody>
            <?php
            // Recopilar todos los códigos de mesa para detectar duplicados
            $codigosMesaUsados = array();
            if (is_countable($this->content) && count($this->content) > 0) {
              foreach ($this->content as $reserva) {
                if (!empty($reserva->mesas) && is_array($reserva->mesas)) {
                  foreach ($reserva->mesas as $mesa) {
                    $codigo = $mesa->mesa_codigo;
                    if (!isset($codigosMesaUsados[$codigo])) {
                      $codigosMesaUsados[$codigo] = array();
                    }
                    $codigosMesaUsados[$codigo][] = $reserva->id;
                  }
                }
              }
            }

            // Se espera que $this->content tenga las reservas con invitados cargados (ver exportarAction)
            if (is_countable($this->content) && count($this->content) > 0):
              foreach ($this->content as $reserva):
                $totalInvitados = 0;
                $socios = 0;
                $asociados = 0;
                $hijos = 0;
                $invitados = 0;
                $hijosMenores25 = 0;
                $valorTotal = 0;
                $valorPorTipo = [
                  'S' => 0, // Socio
                  'A' => 0, // Cosocio
                  'P' => 0, // Invitado
            
                ]; // Puedes agregar más tipos si existen
            
                if (!empty($reserva->invitados)) {
                  // Lógica para: socio, socio hijo <25, cosocio, cosocio hijo <25, invitado
                  $socio = 0;
                  $socioHijo25 = 0;
                  $cosocio = 0;
                  $cosocioHijo25 = 0;
                  $invitado = 0;
                  $valorPorTipo = [
                    'Socio' => 0,
                    'Socio hijo < 25' => 0,
                    'Cosocio' => 0,
                    'Cosocio hijo < 25' => 0,
                    'Invitado' => 0,
                  ];
                  foreach ($reserva->invitados as $invitadoObj) {
                    $totalInvitados++;
                    $tipo = ($invitadoObj->invitadoReserva_estado_invitado) ? $invitadoObj->invitadoReserva_estado_invitado : '';
                    $esMenor25 = ($invitadoObj->invitadoReserva_beneficiario_menor25) && $invitadoObj->invitadoReserva_beneficiario_menor25;
                    $esHijo = ($invitadoObj->invitadoReserva_beneficiario_hijo) && $invitadoObj->invitadoReserva_beneficiario_hijo;
                    $valor = 0;
                    $tipoParticipante = '';
                    if (($reserva->categorias) && is_array($reserva->categorias) && count($reserva->categorias) > 0) {
                      $categoria = $reserva->categorias[0];
                      if ($tipo == 'A') { // Socio
                        if ($esMenor25 && $esHijo) {
                          $valor = ($categoria->categoria_precio_socio_hijo) ? floatval($categoria->categoria_precio_socio_hijo) : 0;
                          $tipoParticipante = 'Socio hijo < 25';
                          $socioHijo25++;
                        } else {
                          $valor = ($categoria->categoria_precio_socio) ? floatval($categoria->categoria_precio_socio) : 0;
                          $tipoParticipante = 'Socio';
                          $socio++;
                        }
                      } elseif ($tipo == 'S') { // Cosocio
                        if ($esMenor25 && $esHijo) {
                          $valor = ($categoria->categoria_precio_socio_hijo) ? floatval($categoria->categoria_precio_socio_hijo) : 0;
                          $tipoParticipante = 'Cosocio hijo < 25';
                          $cosocioHijo25++;
                        } else {
                          $valor = ($categoria->categoria_precio_socio) ? floatval($categoria->categoria_precio_socio) : 0;
                          $tipoParticipante = 'Cosocio';
                          $cosocio++;
                        }
                      } else { // Invitado
                        $valor = ($categoria->categoria_precio_invitado) ? floatval($categoria->categoria_precio_invitado) : 0;
                        $tipoParticipante = 'Invitado';
                        $invitado++;
                      }
                    }
                    $valorTotal += $valor;
                    $valorPorTipo[$tipoParticipante] += $valor;
                  }
                }
                $valorPorInvitado = $totalInvitados > 0 ? round($valorTotal / $totalInvitados, 2) : 0;
                // Definir precios unitarios antes de imprimir la fila
                $precioSocio = (isset($reserva->categorias[0]) && isset($reserva->categorias[0]->categoria_precio_socio)) ? floatval($reserva->categorias[0]->categoria_precio_socio) : 0;
                $precioSocioHijo = (isset($reserva->categorias[0]) && isset($reserva->categorias[0]->categoria_precio_socio_hijo)) ? floatval($reserva->categorias[0]->categoria_precio_socio_hijo) : 0;
                $precioCosocio = (isset($reserva->categorias[0]) && isset($reserva->categorias[0]->categoria_precio_socio)) ? floatval($reserva->categorias[0]->categoria_precio_socio) : 0;
                $precioCosocioHijo = (isset($reserva->categorias[0]) && isset($reserva->categorias[0]->categoria_precio_socio_hijo)) ? floatval($reserva->categorias[0]->categoria_precio_socio_hijo) : 0;
                $precioInvitado = (isset($reserva->categorias[0]) && isset($reserva->categorias[0]->categoria_precio_invitado)) ? floatval($reserva->categorias[0]->categoria_precio_invitado) : 0;

                // Verificar si alguna mesa de esta reserva está duplicada
                $mesaDuplicada = false;
                if (!empty($reserva->mesas) && is_array($reserva->mesas)) {
                  foreach ($reserva->mesas as $mesa) {
                    $codigo = $mesa->mesa_codigo;
                    if (isset($codigosMesaUsados[$codigo]) && count($codigosMesaUsados[$codigo]) > 1) {
                      $mesaDuplicada = true;
                      break;
                    }
                  }
                }
                ?>
                <tr<?= $mesaDuplicada ? ' class="mesa-duplicada"' : '' ?>>
                  <td class="text-center fw-bold"><?= $reserva->id ?></td>
                  <td><?= htmlspecialchars($reserva->reserva_nombre_cliente) ?></td>
                  <td><?= htmlspecialchars($reserva->reserva_apellido_cliente ?? '') ?></td>
                  <td><?= htmlspecialchars($reserva->reserva_documento ?? '') ?></td>
                  <td><?= htmlspecialchars($reserva->reserva_numero_carnet ?? '') ?></td>
                  <td><?= htmlspecialchars($reserva->reserva_telefono ?? '') ?></td>
                  <td>
                    <?php
                    if (!empty($reserva->mesas)) {
                      $ambNombres = [];
                      foreach ($reserva->mesas as $mesa) {
                        if (!empty($mesa->ambiente_nombre) && !in_array($mesa->ambiente_nombre, $ambNombres)) {
                          $ambNombres[] = $mesa->ambiente_nombre;
                        }
                      }
                      echo htmlspecialchars(implode(', ', $ambNombres));
                    } else {
                      echo '—';
                    }
                    ?>
                  </td>

                  <td class="text-center">
                    <?php
                    if (!empty($reserva->mesas) && is_array($reserva->mesas)) {
                      $codigosMesa = array();
                      $hayDuplicados = false;
                      $mesasConflicto = array();

                      foreach ($reserva->mesas as $mesa) {
                        $codigo = $mesa->mesa_codigo;
                        $codigosMesa[] = $codigo;

                        // Verificar si este código está duplicado en otras reservas
                        if (isset($codigosMesaUsados[$codigo]) && count($codigosMesaUsados[$codigo]) > 1) {
                          $hayDuplicados = true;
                          // Obtener las otras reservas que usan esta mesa
                          $otrasReservas = array();
                          foreach ($codigosMesaUsados[$codigo] as $reservaId) {
                            if ($reservaId != $reserva->id) {
                              $otrasReservas[] = $reservaId;
                            }
                          }
                          $mesasConflicto[] = $codigo . ' (Reservas: ' . implode(', ', $otrasReservas) . ')';
                        }
                      }

                      $codigosTexto = implode(', ', $codigosMesa);
                      echo htmlspecialchars($codigosTexto);

                      if ($hayDuplicados) {
                        $tooltipText = 'Mesas duplicadas: ' . implode('; ', $mesasConflicto);
                        echo '<span class="ms-1 text-danger" data-bs-toggle="tooltip" title="' . htmlspecialchars($tooltipText) . '">';
                        echo '<i class="fas fa-exclamation-triangle"></i>';
                        echo '</span>';
                      }
                    } else {
                      echo 'Sin mesas asignadas';
                    }
                    ?>
                  </td>
                  <td><?= $estadometodo[$reserva->reserva_metodo_pago] ?? '' ?></td>
                  <td class="text-center">
                    <?= $reserva->reserva_numero_cuotas > 0 ? $reserva->reserva_numero_cuotas : 'N/A' ?>
                  </td>
                  <td class="text-center">
                    <?= $reserva->reserva_franquicia > 0 ? $reserva->reserva_franquicia : 'N/A' ?>
                  </td>
                    <td style="text-wrap: nowrap;"><?= htmlspecialchars($reserva->reserva_fecha_inicio_reserva ?? '') ?></td>

                  <td class="text-center">
                    <?php $estadoInfo = getEstadoReservaPHP($reserva->reserva_estado); ?>
                    <span class="badge <?= $estadoInfo['badge_class'] ?>"
                      title="<?= $estadoInfo['texto'] ?>"><?= $estadoInfo['texto'] ?></span>
                  </td>
                  <td
                    class="text-center<?= ((int) $reserva->reserva_total_personas !== (int) $totalInvitados) ? ' table-warning' : '' ?>">
                    <?= $reserva->reserva_total_personas ?>
                    <?php if ((int) $reserva->reserva_total_personas !== (int) $totalInvitados): ?>
                      <span class="ms-1 text-warning" data-bs-toggle="tooltip" title="Cantidad de invitados no coincide">
                        <i class="fas fa-exclamation-triangle"></i>
                      </span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center"> <?= $totalInvitados ?> </td>
                  <td class="text-center border-start"> <?= $socio ?> </td>
                  <td class="text-end">$<?= number_format($socio * $precioSocio, 0, ',', '.') ?></td>
                  <td class="text-center"> <?= $socioHijo25 ?> </td>
                  <td class="text-end">$<?= number_format($socioHijo25 * $precioSocioHijo, 0, ',', '.') ?></td>
                  <td class="text-center border-start"> <?= $cosocio ?> </td>
                  <td class="text-end">$<?= number_format($cosocio * $precioCosocio, 0, ',', '.') ?></td>
                  <td class="text-center"> <?= $cosocioHijo25 ?> </td>
                  <td class="text-end">$<?= number_format($cosocioHijo25 * $precioCosocioHijo, 0, ',', '.') ?></td>
                  <td class="text-center border-start"> <?= $invitado ?> </td>
                  <td class="text-end">$<?= number_format($invitado * $precioInvitado, 0, ',', '.') ?></td>
                  <td
                    class="text-end border-start<?= ((float) $valorTotal !== (float) $reserva->reserva_total_pagar) ? ' table-danger' : '' ?>">
                    $<?= number_format($valorTotal, 0, ',', '.') ?>
                    <?php if ((float) $valorTotal !== (float) $reserva->reserva_total_pagar): ?>
                      <span class="ms-1 text-danger" data-bs-toggle="tooltip" title="Valor total no coincide">
                        <i class="fas fa-exclamation-circle"></i>
                      </span>
                    <?php endif; ?>
                  </td>
                  <td class="text-end">$<?= number_format($reserva->reserva_total_pagar, 0, ',', '.') ?></td>
                  <!-- <td>
                  <?php
                  foreach ($valorPorTipo as $tipo => $valor) {
                    if ($valor > 0) {
                      echo $tipo . ': $' . number_format($valor, 0, ',', '.') . '<br>';
                    }
                  }
                  ?>
                </td> -->
                  <td class="text-center border-start">
                    <?php $facturaCompleta = $reserva->reserva_fact_nit && $reserva->reserva_fact_razon && $reserva->reserva_fact_mail && $reserva->reserva_fact_dire && $reserva->reserva_fact_tele; ?>
                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                      <button type="button"
                        class="btn-2color <?= $facturaCompleta ? 'btn-2color-factura-ok' : 'btn-2color-factura-warn' ?> btn-ver-factura"
                        data-bs-toggle="modal" data-bs-target="#modalVerFactura"
                        data-reserva="<?= $reserva->id ?>"
                        data-nit="<?= htmlspecialchars($reserva->reserva_fact_nit ?? '') ?>"
                        data-razon="<?= htmlspecialchars($reserva->reserva_fact_razon ?? '') ?>"
                        data-direccion="<?= htmlspecialchars($reserva->reserva_fact_dire ?? '') ?>"
                        data-telefono="<?= htmlspecialchars($reserva->reserva_fact_tele ?? '') ?>"
                        data-correo="<?= htmlspecialchars($reserva->reserva_fact_mail ?? '') ?>"
                        data-cliente="<?= htmlspecialchars(trim(($reserva->reserva_nombre_cliente ?? '') . ' ' . ($reserva->reserva_apellido_cliente ?? ''))) ?>"
                        title="<?= $facturaCompleta ? 'Ver datos de facturación' : 'Facturación incompleta' ?>">
                        <span class="btn-2color-icon"><i class="fas fa-file-invoice"></i></span>
                        <span class="btn-2color-label">Facturación electrónica</span>
                      </button>
                      <button type="button"
                        class="btn-2color btn-2color-historial btn-ver-historial-tipos"
                        data-bs-toggle="modal" data-bs-target="#modalHistorialTipos"
                        data-reserva="<?= $reserva->id ?>"
                        title="Historial de cambios de tipo (Socio/Cosocio/Invitado)">
                        <span class="btn-2color-icon"><i class="fas fa-history"></i></span>
                        <span class="btn-2color-label">Historial de cambios</span>
                      </button>
                    </div>
                  </td>
                  </tr>
                  <?php
              endforeach;
            else:
              ?>
                <tr>
                  <td colspan="27">
                    <div class="alert alert-info mb-0" role="alert">
                      <i class="fas fa-info-circle"></i> No se encontraron reservas con los filtros aplicados.
                    </div>
                  </td>
                </tr>
              <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    </div>
  </div>

  <!-- Modal: datos de facturación electrónica (compartida, se llena vía JS) -->
  <div class="modal fade" id="modalVerFactura" tabindex="-1" aria-labelledby="modalVerFacturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalVerFacturaLabel">
            <i class="fas fa-file-invoice"></i> Facturación electrónica — Reserva #<span id="facturaModalReservaId"></span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div id="facturaModalIncompleta" class="alert alert-warning d-none" role="alert">
            <i class="fas fa-exclamation-triangle"></i> Esta reserva no tiene los datos de facturación completos.
          </div>
          <table class="table table-sm table-borderless mb-0 factura-modal-table">
            <tbody>
              <tr>
                <th>Cliente</th>
                <td id="facturaModalCliente"></td>
              </tr>
              <tr>
                <th>NIT o Cédula</th>
                <td id="facturaModalNit"></td>
              </tr>
              <tr>
                <th>Razón social</th>
                <td id="facturaModalRazon"></td>
              </tr>
              <tr>
                <th>Dirección</th>
                <td id="facturaModalDireccion"></td>
              </tr>
              <tr>
                <th>Teléfono</th>
                <td id="facturaModalTelefono"></td>
              </tr>
              <tr>
                <th>Correo</th>
                <td id="facturaModalCorreo"></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal: historial de cambios de tipo de invitado (Socio/Cosocio/Invitado), cargado vía AJAX -->
  <div class="modal fade" id="modalHistorialTipos" tabindex="-1" aria-labelledby="modalHistorialTiposLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalHistorialTiposLabel">
            <i class="fas fa-history"></i> Historial de cambios — Reserva #<span id="historialModalReservaId"></span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div id="historialModalCargando" class="text-center text-muted py-3">
            <i class="fas fa-spinner fa-spin"></i> Cargando historial...
          </div>
          <div id="historialModalVacio" class="alert alert-info d-none" role="alert">
            <i class="fas fa-info-circle"></i> No se han registrado cambios de tipo (Socio/Cosocio/Invitado) para esta reserva.
          </div>
          <div id="historialModalError" class="alert alert-danger d-none" role="alert"></div>
          <div class="table-responsive">
            <table class="table table-sm table-striped mb-0 d-none" id="historialModalTable">
              <thead>
                <tr>
                  <th>Fecha</th>
                  <th>Cambio</th>
                  <th>Usuario</th>
                </tr>
              </thead>
              <tbody id="historialModalBody"></tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <style>
    .listado-facturacion-table th,
    .listado-facturacion-table td {
      font-size: 13px;
      vertical-align: middle;
      padding: 0.45rem 0.5rem;
    }

    .listado-facturacion-table thead th {
      background: #23272b;
      color: #fff;
      position: sticky;
      top: 0;
      z-index: 2;
      border-bottom: 2px solid #444;
    }

    .listado-facturacion-table .border-start {
      border-left: 2px solid #bbb !important;
    }

    .listado-facturacion-table tbody tr:nth-child(even) {
      background: #f8f9fa;
    }

    .listado-facturacion-table tbody tr:hover {
      background: #e9ecef;
    }

    .badge {
      font-size: 0.85em;
      font-weight: 500;
      padding: 0.4em 0.7em;
    }

    .table-warning {
      background: #fff3cd !important;
    }

    .table-danger {
      background: #f8d7da !important;
    }

    .text-danger {
      color: #dc3545 !important;
    }

    .mesa-duplicada {
      background: #fff3cd !important;
      border-left: 3px solid #ffc107 !important;
    }

    .factura-modal-table th {
      width: 140px;
      color: #495057;
      font-weight: 600;
      white-space: nowrap;
    }

    /* Botones bicolor (icono en un tono, label en otro), mismo patrón que en el dashboard */
    .btn-2color {
      display: inline-flex;
      align-items: center;
      border: none;
      border-radius: 999px;
      overflow: hidden;
      text-decoration: none;
      font-size: .78rem;
      font-weight: 600;
      line-height: 1;
      cursor: pointer;
    }

    .btn-2color .btn-2color-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 7px 10px;
    }

    .btn-2color .btn-2color-label {
      padding: 6px 12px 6px 4px;
      white-space: nowrap;
    }

    .btn-2color-factura-ok .btn-2color-icon {
      background: #15803d;
      color: #fff;
    }

    .btn-2color-factura-ok .btn-2color-label {
      background: #dcfce7;
      color: #166534;
    }

    .btn-2color-factura-warn .btn-2color-icon {
      background: #b45309;
      color: #fff;
    }

    .btn-2color-factura-warn .btn-2color-label {
      background: #fef3c7;
      color: #92400e;
    }

    .btn-2color-historial .btn-2color-icon {
      background: #6d28d9;
      color: #fff;
    }

    .btn-2color-historial .btn-2color-label {
      background: #ede9fe;
      color: #5b21b6;
    }

    @media (max-width: 1200px) {

      .listado-facturacion-table th,
      .listado-facturacion-table td {
        font-size: 12px;
        padding: 0.3rem 0.3rem;
      }
    }
  </style>
  <script>
    // Inicializar tooltips de Bootstrap si están disponibles
    if (window.bootstrap) {
      document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
          new bootstrap.Tooltip(tooltipTriggerEl);
        });
      });
    }

    // Scroll sincronizado arriba y abajo
    document.addEventListener('DOMContentLoaded', function () {
      var table = document.querySelector('.listado-facturacion-table');
      var scrollTopBar = document.getElementById('scroll-top-bar');
      var scrollBottomBar = document.getElementById('scroll-bottom-bar');
      if (table && scrollTopBar && scrollBottomBar) {
        // Crear un div interior para la barra superior con el mismo ancho que la tabla
        var innerDiv = document.createElement('div');
        innerDiv.style.width = table.offsetWidth + 'px';
        innerDiv.style.height = '1px';
        scrollTopBar.appendChild(innerDiv);

        // Sincronizar scrolls
        scrollTopBar.addEventListener('scroll', function () {
          scrollBottomBar.scrollLeft = scrollTopBar.scrollLeft;
        });
        scrollBottomBar.addEventListener('scroll', function () {
          scrollTopBar.scrollLeft = scrollBottomBar.scrollLeft;
        });

        // Si la tabla cambia de tamaño, ajustar el ancho del innerDiv
        var resizeObserver = new ResizeObserver(function () {
          innerDiv.style.width = table.offsetWidth + 'px';
        });
        resizeObserver.observe(table);
      }
    });

    // Modal de facturación electrónica: se llena con los data-* del botón que la abrió
    document.addEventListener('DOMContentLoaded', function () {
      var modalFactura = document.getElementById('modalVerFactura');
      if (!modalFactura) return;

      modalFactura.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        if (!btn) return;

        var nit = btn.getAttribute('data-nit') || '';
        var razon = btn.getAttribute('data-razon') || '';
        var direccion = btn.getAttribute('data-direccion') || '';
        var telefono = btn.getAttribute('data-telefono') || '';
        var correo = btn.getAttribute('data-correo') || '';

        document.getElementById('facturaModalReservaId').textContent = btn.getAttribute('data-reserva') || '';
        document.getElementById('facturaModalCliente').textContent = btn.getAttribute('data-cliente') || '—';
        document.getElementById('facturaModalNit').textContent = nit || '—';
        document.getElementById('facturaModalRazon').textContent = razon || '—';
        document.getElementById('facturaModalDireccion').textContent = direccion || '—';
        document.getElementById('facturaModalTelefono').textContent = telefono || '—';
        document.getElementById('facturaModalCorreo').textContent = correo || '—';

        var incompleta = !(nit && razon && direccion && telefono && correo);
        document.getElementById('facturaModalIncompleta').classList.toggle('d-none', !incompleta);
      });
    });

    // Modal de historial de cambios de tipo: se llena vía AJAX al abrirse
    document.addEventListener('DOMContentLoaded', function () {
      var modalHistorial = document.getElementById('modalHistorialTipos');
      if (!modalHistorial) return;

      var cargando = document.getElementById('historialModalCargando');
      var vacio = document.getElementById('historialModalVacio');
      var errorBox = document.getElementById('historialModalError');
      var tabla = document.getElementById('historialModalTable');
      var tbody = document.getElementById('historialModalBody');

      function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str == null ? '' : str;
        return div.innerHTML;
      }

      modalHistorial.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        if (!btn) return;
        var reservaId = btn.getAttribute('data-reserva') || '';

        document.getElementById('historialModalReservaId').textContent = reservaId;

        cargando.classList.remove('d-none');
        vacio.classList.add('d-none');
        errorBox.classList.add('d-none');
        tabla.classList.add('d-none');
        tbody.innerHTML = '';

        fetch('<?php echo $this->route; ?>/historialtipos?id=' + encodeURIComponent(reservaId))
          .then(function (r) { return r.json(); })
          .then(function (data) {
            cargando.classList.add('d-none');
            if (data.error) {
              errorBox.textContent = data.error;
              errorBox.classList.remove('d-none');
              return;
            }
            var historial = data.historial || [];
            if (historial.length === 0) {
              vacio.classList.remove('d-none');
              return;
            }
            historial.forEach(function (item) {
              var tr = document.createElement('tr');
              tr.innerHTML =
                '<td style="white-space:nowrap;">' + escapeHtml(item.fecha) + '</td>' +
                '<td><span class="badge text-bg-secondary">' + escapeHtml(item.estado_anterior) + '</span> ' +
                '<i class="fas fa-arrow-right mx-1"></i> ' +
                '<span class="badge text-bg-success">' + escapeHtml(item.estado_nuevo) + '</span></td>' +
                '<td>' + escapeHtml(item.usuario) + '</td>';
              tbody.appendChild(tr);
            });
            tabla.classList.remove('d-none');
          })
          .catch(function () {
            cargando.classList.add('d-none');
            errorBox.textContent = 'Ocurrió un error al cargar el historial.';
            errorBox.classList.remove('d-none');
          });
      });
    });
  </script>