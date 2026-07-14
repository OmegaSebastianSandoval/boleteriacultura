<?php
function getEstadoReservaPHP($estado)
{
	$estados = array(
		'1' => array('texto' => 'Reserva creada', 'badge_class' => 'text-bg-primary'),
		'2' => array('texto' => 'Reserva pagada por cargo a la acción', 'badge_class' => 'text-bg-success'),
		'3' => array('texto' => 'Reserva pago aprobado - PlaceToPay', 'badge_class' => 'text-bg-success'),
		'4' => array('texto' => 'Reserva pago pendiente - PlaceToPay', 'badge_class' => 'text-bg-warning'),
		'5' => array('texto' => 'Reserva pago fallido - PlaceToPay', 'badge_class' => 'text-bg-danger'),
		'6' => array('texto' => 'Reserva pago rechazado - PlaceToPay', 'badge_class' => 'text-bg-danger'),
		'7' => array('texto' => 'Reserva pago pendiente - Sistema', 'badge_class' => 'text-bg-info'),
		'8' => array('texto' => 'Reserva cancelada por inactividad', 'badge_class' => 'text-bg-secondary'),
		'C' => array('texto' => 'Reserva cancelada', 'badge_class' => 'text-bg-dark'),
		'11' => array('texto' => 'Pago por datáfono', 'badge_class' => 'text-bg-success')
	);
	return isset($estados[$estado]) ? $estados[$estado] : array('texto' => 'Estado desconocido', 'badge_class' => 'text-bg-light');
}
?>
<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>

<style>
	.modal-lg {
		max-width: 90% !important;
	}

	.table-sm td,
	.table-sm th {
		padding: 0.3rem;
		font-size: 0.875rem;
	}

	.badge {
		font-size: 0.75em;
	}

	.modal-body {
		max-height: 70vh;
		overflow-y: auto;
	}

	.alert-info {
		background-color: #d1ecf1;
		border-color: #bee5eb;
		color: #0c5460;
	}

	table {
		font-size: 15px;
	}

	.content-table .table tbody {
		font-size: 14px;
	}
</style>
<div class="container-fluid">
	<form action="<?php echo $this->route; ?>" method="post">
		<div class="content-dashboard">
			<div class="row g-2">
				<div class="col-2 d-none">
					<label>Evento</label>
					<label class="input-group">
						<!-- <input type="text" class="form-control" name="reserva_id_evento" value="<?php echo $this->getObjectVariable($this->filters, 'reserva_id_evento') ?>"></input> -->
						<!-- <select name="reserva_id_evento" id="reserva_id_evento" class="form-control">
							<option value="" selected disabled>Seleccione...</option>
							<?php foreach ($this->eventos as $key => $evento): ?>
								<option value="<?php echo $key ?>" <?php if ($key == $this->getObjectVariable($this->filters, 'reserva_id_evento')) {
																											echo 'selected';
																										} ?>><?php echo $evento->evento_titulo ?></option>
							<?php endforeach; ?>
						</select> -->
					</label>
				</div>
				<div class="col">
					<label>ID reserva</label>
					<label class="input-group">
						<input type="text" class="form-control" name="id"
							value="<?php echo $this->getObjectVariable($this->filters, 'id') ?>"></input>
					</label>
				</div>
				<div class="col">
					<label>Documento</label>
					<label class="input-group">
						<input type="text" class="form-control" name="reserva_documento"
							value="<?php echo $this->getObjectVariable($this->filters, 'reserva_documento') ?>"></input>
					</label>
				</div>
				<div class="col">
					<label>Número de carnet</label>
					<label class="input-group">
						<input type="text" class="form-control" name="reserva_numero_carnet"
							value="<?php echo $this->getObjectVariable($this->filters, 'reserva_numero_carnet') ?>"></input>
					</label>
				</div>
				<div class="col">
					<label>Nombre o apellido</label>
					<label class="input-group">
						<input type="text" class="form-control" name="reserva_nombre_cliente"
							value="<?php echo $this->getObjectVariable($this->filters, 'reserva_nombre_cliente') ?>"></input>
					</label>
				</div>
				<div class="col">
					<label>Ambiente</label>
					<select class="form-select" name="ambiente_id">
						<option value="">Todos</option>
						<?php foreach ($this->ambientes as $amb): ?>
							<option value="<?php echo $amb->ambiente_id; ?>"
								<?php echo ($this->getObjectVariable($this->filters, 'ambiente_id') == $amb->ambiente_id) ? 'selected' : ''; ?>>
								<?php echo htmlspecialchars($amb->ambiente_nombre); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col">
					<label>Código de mesa</label>
					<label class="input-group">
						<input type="text" class="form-control" name="mesa_codigo"
							value="<?php echo $this->getObjectVariable($this->filters, 'mesa_codigo') ?>"></input>
					</label>
				</div>
				<div class="col-2 d-none">
					<label>Número de Personas</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-verde-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="reserva_total_personas"
							value="<?php echo $this->getObjectVariable($this->filters, 'reserva_total_personas') ?>"></input>
					</label>
				</div>
				<div class="col-2 d-none">
					<label>Telefono Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-azul "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="reserva_telefono"
							value="<?php echo $this->getObjectVariable($this->filters, 'reserva_telefono') ?>"></input>
					</label>
				</div>
				<div class="col-1">
					<label>&nbsp;</label>
					<button type="submit" class="btn btn-block btn-azul" data-bs-toggle="tooltip" data-placement="top" title="Filtrar">
						<i class="fas fa-filter"></i>
					</button>
				</div>
				<div class="col-1">
					<label>&nbsp;</label>
					<a class="btn btn-block btn-azul-claro" data-bs-toggle="tooltip" data-placement="top" title="Limpiar Filtro" href="<?php echo $this->route; ?>?cleanfilter=1">
						<i class="fas fa-eraser"></i>
					</a>
				</div>
			</div>
		</div>
	</form>
	<div align="center">
		<ul class="pagination justify-content-center">
			<?php
			$url = $this->route;
			if ($this->totalpages > 1) {
				if ($this->page != 1)
					echo '<li class="page-item" ><a class="page-link"  href="' . $url . '?page=' . ($this->page - 1) . '"> &laquo; Anterior </a></li>';
				for ($i = 1; $i <= $this->totalpages; $i++) {
					if ($this->page == $i)
						echo '<li class="active page-item"><a class="page-link">' . $this->page . '</a></li>';
					else
						echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>  ';
				}
				if ($this->page != $this->totalpages)
					echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page + 1) . '">Siguiente &raquo;</a></li>';
			}
			?>
		</ul>
	</div>
	<div class="content-dashboard">
		
				<!-- Modal unico de Detalles Reserva (lazy AJAX) -->
				<div class="modal fade text-start" id="modalDetallesReserva" tabindex="-1" role="dialog">
					<div class="modal-dialog modal-xl" style="max-width:92%;width:92%;" role="document">
						<div class="modal-content">
							<div class="modal-header" style="background:var(--surface);border-bottom:1px solid var(--border);">
								<h4 class="modal-title" style="font-size:.95rem;font-weight:600;display:flex;align-items:center;gap:8px;"><i class="fas fa-info-circle" style="color:var(--brand-green);"></i> <span id="modalDetallesTitulo">Detalles de la reserva</span></h4>
								<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
							</div>
							<div class="modal-body" style="background:var(--bg);padding:1rem;" id="modalDetallesCuerpo">
								<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted" style="font-size:.85rem;">Cargando...</p></div>
							</div>
							<div class="modal-footer" id="modalDetallesFooter" style="display:none;background:var(--surface);border-top:1px solid var(--border);justify-content:space-between;align-items:center;">
								<div style="font-size:.9rem;">
									<span style="color:var(--text-muted);">Total de la reserva:</span>
									<strong id="modalDetallesTotal" style="font-size:1.05rem;margin-left:8px;"></strong>
								</div>
								<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i>Cerrar</button>
							</div>
						</div>
					</div>
				</div>

<div class="franja-paginas">
			<div class="row">
				<div class="col-8">
					<div class="titulo-registro">Se encontraron <?php echo $this->register_number; ?> Registros</div>
				</div>
				<div class="col-3 text-end">
					<div class="texto-paginas">Registros por pagina:</div>
				</div>
				<div class="col-1">
					<select class="form-control form-control-sm selectpagination">
						<option value="20" <?php if ($this->pages == 20) {
																	echo 'selected';
																} ?>>20</option>
						<option value="30" <?php if ($this->pages == 30) {
																	echo 'selected';
																} ?>>30</option>
						<option value="50" <?php if ($this->pages == 50) {
																	echo 'selected';
																} ?>>50</option>
						<option value="100" <?php if ($this->pages == 100) {
																	echo 'selected';
																} ?>>100</option>
					</select>
				</div>
			</div>
		</div>
		<div class="content-table">
			<table class=" table table-striped  table-hover table-administrator text-start">
				<thead>
					<tr>
						<td>ID</td>
						<td>Nombre</td>
						<td>Fecha y hora</td>
						<td>Ambiente / Mesa</td>
						<td>Personas</td>
						<td>Tipo de pago</td>
						<td></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->lists as $content) { ?>
						<?php $id = $content->id; ?>
						<tr>
							<td><span class="badge bg-secondary"><?= $content->id ?></span></td>
							<td>
								<?= $content->reserva_nombre_cliente . ' ' . ($content->reserva_apellido_cliente ?? '') ?>
								<br><small><strong>CC:</strong> <?= $content->reserva_documento ?> / <strong>Acción</strong>: <?= $content->reserva_numero_carnet ?></small>
							</td>
							<td><?= $content->reserva_fecha ?? '' ?><br><small class="text-muted"><?= $content->reserva_hora ?? '' ?></small></td>
							<td><?= $content->ambiente_nombre ?? '-' ?><br><small class="text-muted"><?= $content->mesa_nombre ?? '-' ?></small></td>
							<td><?= $content->reserva_total_personas ?></td>
							<td>
								<?php
								// Función helper para estados

								$estadoInfo = getEstadoReservaPHP($content->reserva_estado);
								echo '<span class="badge ' . $estadoInfo['badge_class'] . '">' . $estadoInfo['texto'] . '</span>';
								?>
							</td>
							<td class="text-end">
								<div>
									<button class="btn btn-info btn-sm btn-ver-detalles" data-id="<?= $id ?>"
										data-bs-toggle="tooltip" title="Ver detalles">
										<i class="fas fa-eye"></i>
									</button>
									<!-- <?php if ($content->reserva_estado != 'C') { ?>
									<span  data-bs-toggle="tooltip" data-placement="top" title="Cancelar"><a class="btn btn-rojo btn-sm"  data-bs-toggle="modal" data-bs-target="#modalCancel<?= $id ?>"  ><i class="fa-solid fa-ban"></i></a></span>
									<?php } ?>
									<a class="btn btn-azul btn-sm" href="<?php echo $this->route; ?>/manage?id=<?= $id ?>"  data-bs-toggle="tooltip" data-placement="top" title="Editar"><i class="fas fa-pen-alt"></i></a> -->
									<?php if (Session::getInstance()->get('kt_login_id') == '1'): ?>
										<span data-bs-toggle="tooltip" data-placement="top" title="Eliminar reserva (libera la mesa)">
											<a class="btn btn-rojo btn-sm" data-bs-toggle="modal" data-bs-target="#modal<?= $id ?>"><i class="fas fa-trash-alt"></i></a>
										</span>
									<?php endif; ?>
								</div>
								<?php if (Session::getInstance()->get('kt_login_id') == '1'): ?>
									<!-- Modal -->
									<div class="modal fade text-start" id="modal<?= $id ?>" tabindex="-1" role="dialog"
										aria-labelledby="myModalLabel">
										<div class="modal-dialog" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h4 class="modal-title" id="myModalLabel">Eliminar reserva #<?= $id ?></h4>
													<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
												</div>
												<div class="modal-body">
													<div class="alert alert-danger mb-0">
														¿Está seguro de eliminar esta reserva? Esta acción es <strong>irreversible</strong>:
														se liberará la mesa asignada, se eliminarán los invitados, boletas y cupos
														adicionales de esta reserva, y quedará registrado en el log.
													</div>
												</div>
												<div class="modal-footer">
													<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
													<a class="btn btn-danger"
														href="<?php echo $this->route; ?>/eliminarreserva?id=<?= $id ?>&csrf=<?= $this->csrf; ?>">Sí, eliminar</a>
												</div>
											</div>
										</div>
									</div>
								<?php endif; ?>
								<div class="modal fade text-start" id="modalCancel<?= $id ?>" tabindex="-1" role="dialog"
									aria-labelledby="myModalLabel">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="modal-title" id="myModalLabel">Cancelar</h4>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body">
												<div class="">¿Esta seguro de cancelar esta reserva?</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
												<a class="btn btn-danger"
													href="<?php echo $this->route; ?>/cancelarReserva?id=<?= $id ?>">Cancelar
													reserva</a>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="franja-paginas" style="border-top: 1px solid #CCCCCC;margin-top: 2%;">
			<div class="row" style="padding-top: 1%;">
				<div class="col-8">
					<div class="titulo-registro">Se encontraron <?php echo $this->register_number; ?> Registros</div>
				</div>
				<div class="col-3 text-end">
					<div class="texto-paginas">Registros por Página:</div>
				</div>
				<div class="col-1">
					<select class="form-control form-control-sm selectpagination">
						<option value="20" <?php if ($this->pages == 20) {
																	echo 'selected';
																} ?>>20</option>
						<option value="30" <?php if ($this->pages == 30) {
																	echo 'selected';
																} ?>>30</option>
						<option value="50" <?php if ($this->pages == 50) {
																	echo 'selected';
																} ?>>50</option>
						<option value="100" <?php if ($this->pages == 100) {
																	echo 'selected';
																} ?>>100</option>
					</select>
				</div>
			</div>
		</div>
		<input type="hidden" id="csrf" value="<?php echo $this->csrf ?>"><input type="hidden" id="order-route"
			value="<?php echo $this->route; ?>/order"><input type="hidden" id="page-route"
			value="<?php echo $this->route; ?>/changepage">
	</div>
	<div align="center">
		<ul class="pagination justify-content-center">
			<?php
			$url = $this->route;
			if ($this->totalpages > 1) {
				if ($this->page != 1)
					echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page - 1) . '"> &laquo; Anterior </a></li>';
				for ($i = 1; $i <= $this->totalpages; $i++) {
					if ($this->page == $i)
						echo '<li class="active page-item"><a class="page-link">' . $this->page . '</a></li>';
					else
						echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>  ';
				}
				if ($this->page != $this->totalpages)
					echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page + 1) . '">Siguiente &raquo;</a></li>';
			}
			?>
		</ul>
	</div>
</div>
<style>
	h5,
	h6 {
		text-align: start
	}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
  var modalEl  = document.getElementById('modalDetallesReserva');
  var bsModal  = new bootstrap.Modal(modalEl);
  var titulo   = document.getElementById('modalDetallesTitulo');
  var cuerpo   = document.getElementById('modalDetallesCuerpo');
  var footer   = document.getElementById('modalDetallesFooter');
  var totalEl  = document.getElementById('modalDetallesTotal');
  var spin = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted" style="font-size:.85rem;">Cargando...</p></div>';

  function fv(v) { return (v !== null && v !== undefined && String(v).trim() !== '') ? v : '&mdash;'; }
  function bdg(cls, txt) { return '<span class="badge ' + cls + '">' + txt + '</span>'; }
  function fmt(n) { return (n !== null && n !== undefined) ? '$ ' + Number(n).toLocaleString('es-CO') : '&mdash;'; }

  function badgeEstado(est) {
    switch (String(est)) {
      case 'S': return bdg('text-bg-primary', 'Socio');
      case 'A': return bdg('text-bg-primary', 'Socio');
      case 'P': return bdg('text-bg-secondary', 'Invitado');
      case 'H': return bdg('text-bg-info', 'Hijo');
      default:  return bdg('text-bg-light', 'N/A');
    }
  }

  function badgesBeneficios(inv) {
    var b = [];
    if (inv.invitadoReserva_beneficiario_menor25 == 1) b.push(bdg('text-bg-info', 'Menor 25'));
    if (inv.invitadoReserva_beneficiario_hijo     == 1) b.push(bdg('text-bg-success', 'Hijo'));
    if (inv.invitadoReserva_beneficiario_principal== 1) b.push(bdg('text-bg-primary', 'Principal'));
    if (inv.invitadoReserva_beneficiario_cupo     == 1) b.push(bdg('text-bg-warning', 'Con Cupo'));
    return b.length ? b.join(' ') : 'N/A';
  }

  function calcPrecio(inv, cat, esModoSilla) {
    if (!cat) return { tipo: '&mdash;', precio: null };
    var tipo, precio;
    if (String(inv.invitado_tipo) === '1') {
      var esMenor25 = inv.invitadoReserva_beneficiario_menor25 == 1;
      var esHijo    = inv.invitadoReserva_beneficiario_hijo    == 1;
      if (esMenor25 && esHijo) {
        precio = esModoSilla ? cat.categoria_precio_silla_socio_hijo : cat.categoria_precio_socio_hijo;
        tipo   = (String(inv.invitadoReserva_estado_invitado) === 'S') ? 'Cosocio Hijo &lt; 25' : 'Beneficiario Hijo &lt; 25';
      } else {
        precio = esModoSilla ? cat.categoria_precio_silla_socio : cat.categoria_precio_socio;
        tipo   = (String(inv.invitadoReserva_estado_invitado) === 'S') ? 'Cosocio' : 'Beneficiario';
      }
    } else {
      precio = esModoSilla ? cat.categoria_precio_silla_invitado : cat.categoria_precio_invitado;
      tipo   = 'Invitado';
    }
    return { tipo: tipo, precio: precio };
  }

  document.querySelectorAll('.btn-ver-detalles').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var id = this.dataset.id;
      titulo.innerHTML = 'Detalles de la reserva #' + id;
      cuerpo.innerHTML = spin;
      footer.style.display = 'none';
      bsModal.show();

      fetch('/administracion/reservas/detallesreserva?id=' + id)
        .then(function(r) { return r.json(); })
        .then(function(d) {
          if (d.error) { cuerpo.innerHTML = '<div class="alert alert-danger">' + d.error + '</div>'; footer.style.display = 'none'; return; }

          var estInfo  = d.estado_info || { texto: 'Sin estado', badge_class: 'badge-secondary' };
          var estBadge = bdg(estInfo.badge_class, estInfo.texto);
          var primerAmb  = (d.ambientes && d.ambientes.length) ? d.ambientes[0] : null;
          var todasLasMesas = (d.mesas && d.mesas.length) ? d.mesas.map(function(m) { return m.mesa_id; }).join(',') : '';

          var cellSizeAncho = 550; // <-- Ancho del contenedor del mapa en px (baja si se sobresale, sube si se ve pequeño)
          var cellSizeAlto  = 380; // <-- Alto del contenedor del mapa en px  (baja si se sobresale, sube si se ve pequeño)

          var iframeSrc = '';
          if (primerAmb) {
            iframeSrc = '/administracion/ambientes/manage?id=' + primerAmb.ambiente_id + '&display=1&solo_mapa=1&px_w=' + cellSizeAncho + '&px_h=' + cellSizeAlto;
            if (todasLasMesas) iframeSrc += '&destacar_mesa=' + todasLasMesas + '&modo=validacion';
          }

          /* ======================================================
             FILA SUPERIOR: col info | col iframe
             ====================================================== */
          var h = '<div class="row g-3 mb-3" style="align-items:stretch;">';

          /* ---- COL IZQUIERDA: datos cliente + reserva ---- */
          h += '<div class="col-md-6 d-flex">';
          h += '<div class="div-dashboard" style="margin-top:0;width:100%;display:flex;flex-direction:column;">';
          h += '<h2><i class="fas fa-id-card"></i> Reserva #' + d.id + ' &nbsp;' + estBadge + '</h2>';
          h += '<div style="flex:1;padding:14px 15px;overflow-y:auto;">';

          h += '<p class="cfg-section-title" style="margin-bottom:10px;"><i class="fas fa-user cfg-icon" style="background:var(--brand);"></i>Datos del Cliente</p>';
          h += '<table class="table table-sm table-borderless mb-0" style="font-size:.83rem;">';
          var nc = ((d.reserva_nombre_cliente || '') + ' ' + (d.reserva_apellido_cliente || '')).trim();
          h += '<tr><td style="color:var(--text-muted);width:130px;white-space:nowrap;padding:3px 0;">Nombre</td><td style="padding:3px 0;"><strong>' + fv(nc) + '</strong></td></tr>';
          h += '<tr><td style="color:var(--text-muted);padding:3px 0;">CC</td><td style="padding:3px 0;"><strong>' + fv(d.reserva_documento) + '</strong></td></tr>';
          h += '<tr><td style="color:var(--text-muted);padding:3px 0;">N&deg; Acci&oacute;n</td><td style="padding:3px 0;"><strong>' + fv(d.reserva_numero_carnet) + '</strong></td></tr>';
          if (d.reserva_telefono) h += '<tr><td style="color:var(--text-muted);padding:3px 0;">Tel&eacute;fono</td><td style="padding:3px 0;">' + d.reserva_telefono + '</td></tr>';
          if (d.reserva_correo)   h += '<tr><td style="color:var(--text-muted);padding:3px 0;">Correo</td><td style="padding:3px 0;">' + d.reserva_correo + '</td></tr>';
          h += '</table>';

          h += '<hr style="border-color:var(--border);margin:12px 0;">';

          h += '<p class="cfg-section-title" style="margin-bottom:10px;"><i class="fas fa-calendar-alt cfg-icon" style="background:var(--brand-green);"></i>Datos de la Reserva</p>';
          h += '<table class="table table-sm table-borderless mb-0" style="font-size:.83rem;">';
          h += '<tr><td style="color:var(--text-muted);width:130px;white-space:nowrap;padding:3px 0;">Fecha</td><td style="padding:3px 0;">' + fv(d.reserva_fecha) + '</td></tr>';
          h += '<tr><td style="color:var(--text-muted);padding:3px 0;">Hora</td><td style="padding:3px 0;">' + fv(d.reserva_hora) + '</td></tr>';
          h += '<tr><td style="color:var(--text-muted);padding:3px 0;">Personas</td><td style="padding:3px 0;">' + fv(d.reserva_total_personas) + '</td></tr>';
          if (d.reserva_total_pagar)    h += '<tr><td style="color:var(--text-muted);padding:3px 0;">Total a pagar</td><td style="padding:3px 0;"><strong>' + fmt(d.reserva_total_pagar) + '</strong></td></tr>';
          if (d.reserva_metodo_pago)    h += '<tr><td style="color:var(--text-muted);padding:3px 0;">M&eacute;todo pago</td><td style="padding:3px 0;">' + d.reserva_metodo_pago + '</td></tr>';
          if (d.reserva_comentario)     h += '<tr><td style="color:var(--text-muted);padding:3px 0;">Comentario</td><td style="padding:3px 0;font-style:italic;">' + d.reserva_comentario + '</td></tr>';
          if (d.reserva_fecha_creacion) h += '<tr><td style="color:var(--text-muted);padding:3px 0;">Creada</td><td style="padding:3px 0;">' + d.reserva_fecha_creacion + '</td></tr>';
          h += '</table>';

          h += '</div></div></div>';

          /* ---- COL DERECHA: iframe ambiente ---- */
          h += '<div class="col-md-6 d-flex">';
          if (iframeSrc) {
            var ambNom = primerAmb ? (primerAmb.ambiente_nombre || 'Ambiente') : 'Ambiente';
            h += '<div class="div-dashboard" style="margin-top:0;width:100%;display:flex;flex-direction:column;">';
            h += '<h2><i class="fas fa-map-marked-alt"></i> ' + ambNom + '</h2>';
            h += '<div style="flex:1;min-height:350px;position:relative;overflow:hidden;">';
            h += '<div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:var(--bg);z-index:2;"><div class="spinner-border spinner-border-sm text-secondary"></div></div>';
            h += '<iframe src="' + iframeSrc + '" style="width:100%;height:100%;border:none;display:block;" onload="this.previousElementSibling.style.display=&quot;none&quot;"></iframe>';
            h += '</div></div>';
          } else {
            h += '<div class="div-dashboard" style="margin-top:0;width:100%;display:flex;flex-direction:column;">';
            h += '<div style="flex:1;display:flex;align-items:center;justify-content:center;min-height:200px;"><div class="text-center text-muted" style="font-size:.85rem;"><i class="fas fa-map fa-2x mb-2 d-block"></i>Sin ambiente asignado</div></div>';
            h += '</div>';
          }
          h += '</div>';
          h += '</div>';

          /* ======================================================
             MESAS ASIGNADAS
             ====================================================== */
          if (d.mesas && d.mesas.length) {
            h += '<div class="div-dashboard mb-3" style="margin-top:12px;">';
            h += '<h2><i class="fas fa-chair"></i> Mesas asignadas (' + d.mesas.length + ')</h2>';
            h += '<div class="pading-dashboard" style="height:auto;">';
            h += '<div class="content-table mb-3">';
            h += '<table class="table table-striped table-hover table-administrator text-left mb-0">';
            h += '<thead><tr><th>Mesa</th><th>C&oacute;digo</th><th>Tipo</th><th>Capacidad</th><th>Ambiente</th></tr></thead><tbody>';
            d.mesas.forEach(function(m) {
              var ambDeMesa = '';
              if (d.ambientes && d.ambientes.length) {
                d.ambientes.forEach(function(a) {
                  if (String(a.ambiente_id) === String(m.mesa_ambiente)) ambDeMesa = a.ambiente_nombre;
                });
                if (!ambDeMesa) ambDeMesa = d.ambientes[0].ambiente_nombre;
              }
              h += '<tr>';
              h += '<td>' + bdg('text-bg-success', '<i class="fas fa-chair"></i> ' + fv(m.mesa_nombre)) + '</td>';
              h += '<td>' + fv(m.mesa_codigo) + '</td>';
              h += '<td>' + fv(m.mesa_tipo) + '</td>';
              h += '<td>' + (m.mesa_capacidad ? m.mesa_capacidad + ' pers.' : '&mdash;') + '</td>';
              h += '<td>' + (ambDeMesa || '&mdash;') + '</td>';
              h += '</tr>';
            });
            h += '</tbody></table></div></div></div>';
          }

          /* ======================================================
             LISTA DE INVITADOS
             ====================================================== */
          if (d.invitados && d.invitados.length) {
            var tieneCategoria = !!(d.categoria);
            var esModoSilla = !!(d.mesa && d.mesa.mesa_tipo === 'silla');
            h += '<div class="div-dashboard mb-0" style="margin-top:12px;">';
            h += '<h2><i class="fas fa-users"></i> Invitados (' + d.invitados.length + ')</h2>';
            h += '<div class="pading-dashboard" style="height:auto;">';
            h += '<div class="content-table mb-3">';
            h += '<table class="table table-striped table-hover table-administrator text-left mb-0">';
            h += '<thead><tr>';
            h += '<th>#</th><th>Nombre</th><th>Apellido</th><th>Documento</th><th>N&deg; Acci&oacute;n</th>';
            h += '<th>Detalles</th>';
            if (tieneCategoria) h += '<th>Tipo participante</th><th>Precio boleta</th>';
            h += '</tr></thead><tbody>';
            d.invitados.forEach(function(inv, i) {
              var pc = calcPrecio(inv, d.categoria, esModoSilla);
              h += '<tr>';
              h += '<td>' + bdg('bg-secondary', i + 1) + '</td>';
              h += '<td><strong>' + fv(inv.invitadoReserva_nombre_invitado) + '</strong></td>';
              h += '<td>' + fv(inv.invitadoReserva_apellido_invitado) + '</td>';
              h += '<td>' + fv(inv.documento_invitado) + '</td>';
              h += '<td>' + fv(inv.invitadoReserva_numero_carnet) + '</td>';
              h += '<td>' + badgesBeneficios(inv) + '</td>';
              if (tieneCategoria) {
                h += '<td><span class="badge text-bg-light" style="color:#333;">' + pc.tipo + '</span></td>';
                h += '<td><strong>' + (pc.precio !== null ? fmt(pc.precio) : '&mdash;') + '</strong></td>';
              }
              h += '</tr>';
            });
            h += '</tbody></table></div></div></div>';
          }

          cuerpo.innerHTML = h;

          if (d.reserva_total_pagar) {
            totalEl.innerHTML = fmt(d.reserva_total_pagar);
            footer.style.display = '';
          } else {
            footer.style.display = 'none';
          }
        })
        .catch(function() {
          cuerpo.innerHTML = '<div class="alert alert-danger">Error al cargar los detalles. Intente nuevamente.</div>';
        });
    });
  });
});
</script>

<?php if (!empty($this->successEliminarReserva)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      alert('<?= addslashes($this->successEliminarReserva) ?>');
    });
  </script>
<?php endif; ?>
<?php if (!empty($this->errorEliminarReserva)): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      alert('No se pudo eliminar: <?= addslashes($this->errorEliminarReserva) ?>');
    });
  </script>
<?php endif; ?>
