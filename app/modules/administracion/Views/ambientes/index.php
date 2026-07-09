<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form action="<?php echo $this->route; ?>" method="post">
		<div class="content-dashboard">
			<div class="row">
				<div class="col-2">
					<label>Piso</label>
					<label class="input-group">
						<select class="form-select" name="ambiente_piso">
							<option value="">Todas</option>
							<?php foreach ($this->list_ambiente_piso as $key => $value): ?>
								<option value="<?= $key; ?>" <?php if ($this->getObjectVariable($this->filters, 'ambiente_piso') == $key) {
																								echo "selected";
																							} ?>><?= $value; ?></option>
							<?php endforeach ?>
						</select>
					</label>
				</div>
				<div class="col-2">
					<label>Nombre</label>
					<label class="input-group">
						<input type="text" class="form-control" name="ambiente_nombre"
							value="<?php echo $this->getObjectVariable($this->filters, 'ambiente_nombre') ?>"></input>
					</label>
				</div>
				<div class="col-2 d-none">
					<label>Capacidad</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="ambiente_capacidad"
							value="<?php echo $this->getObjectVariable($this->filters, 'ambiente_capacidad') ?>"></input>
					</label>
				</div>
				<div class="col-2 d-none">
					<label>Categoria</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-cafe "><i class="far fa-list-alt"></i></span>
						</div>
						<select class="form-control" name="ambiente_categoria">
							<option value="">Todas</option>
							<?php foreach ($this->list_ambiente_categoria as $key => $value): ?>
								<option value="<?= $key; ?>" <?php if ($this->getObjectVariable($this->filters, 'ambiente_categoria') == $key) {
																								echo "selected";
																							} ?>><?= $value; ?></option>
							<?php endforeach ?>
						</select>
					</label>
				</div>
				<div class="col-2">
					<label>Activo (Si, No)</label>
					<label class="input-group">
						<!-- <div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-morado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="ambiente_estado"
							value="<?php echo $this->getObjectVariable($this->filters, 'ambiente_estado') ?>"></input> -->
						<select name="ambiente_estado" id="ambiente_estado" class="form-select">
							<option value="">Todos</option>
							<option value="1" <?php if ($this->getObjectVariable($this->filters, 'ambiente_estado') == 1) {
																	echo "selected";
																} ?>>Si</option>
							<option value="0" <?php if ($this->getObjectVariable($this->filters, 'ambiente_estado') == 0) {
																	echo "selected";
																} ?>>No</option>
						</select>
					</label>
				</div>
				<div class="col-2 d-none">
					<label>Disponible</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="ambiente_imagen_disponible"
							value="<?php echo $this->getObjectVariable($this->filters, 'ambiente_imagen_disponible') ?>"></input>
					</label>
				</div>
				<div class="col-2 d-none">
					<label>Pendiente</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-rojo-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="ambiente_imagen_pendiente"
							value="<?php echo $this->getObjectVariable($this->filters, 'ambiente_imagen_pendiente') ?>"></input>
					</label>
				</div>
				<div class="col-2 d-none">
					<label>Ocupado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-verde-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="ambiente_imagen_ocupado"
							value="<?php echo $this->getObjectVariable($this->filters, 'ambiente_imagen_ocupado') ?>"></input>
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
		<div class="franja-paginas">
			<div class="row">
				<div class="col-5">
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
				<div class="col-3">
					<div class="text-end"><a class="btn btn-sm btn-success" href="<?php echo $this->route . "\manage"; ?>"> <i
								class="fas fa-plus-square"></i> Crear Nuevo</a></div>
				</div>
			</div>
		</div>
		<div class="content-table">
			<table class=" table table-striped  table-hover table-administrator text-start">
				<thead>
					<tr>
						<td>Piso</td>
						<td>Nombre</td>
						<!-- <td>Capacidad</td> -->
						<!-- <td>Categoria</td> -->
						<td>¿Ambiente activo?</td>
						<td>Descuento</td>
						<!-- <td>Disponible</td>
						<td>Pendiente</td>
						<td>Ocupado</td>
						<td width="100">Orden</td> -->
						<td width="150"></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->lists as $content) { ?>
						<?php $id = $content->ambiente_id; ?>
						<tr>
							<td><?= $this->list_ambiente_piso[$content->ambiente_piso]; ?>
							<td><?= $content->ambiente_nombre; ?></td>
							<!-- <td><?= $content->ambiente_capacidad; ?></td> -->
							<!-- <td><?= $this->list_ambiente_categoria[$content->ambiente_categoria]; ?> -->
							<td><?= $content->ambiente_estado == 1 ? 'Sí' : 'No'; ?></td>
							<td><?= $content->ambiente_descuento; ?></td>
							<!-- <td>
								<?php if ($content->ambiente_imagen_disponible) { ?>
									<img src="/images/<?= $content->ambiente_imagen_disponible; ?>"
										class="img-thumbnail thumbnail-administrator" />
								<?php } ?>
								<div><?= $content->ambiente_imagen_disponible; ?></div>
							</td>
							<td>
								<?php if ($content->ambiente_imagen_pendiente) { ?>
									<img src="/images/<?= $content->ambiente_imagen_pendiente; ?>"
										class="img-thumbnail thumbnail-administrator" />
								<?php } ?>
								<div><?= $content->ambiente_imagen_pendiente; ?></div>
							</td>
							<td>
								<?php if ($content->ambiente_imagen_ocupado) { ?>
									<img src="/images/<?= $content->ambiente_imagen_ocupado; ?>"
										class="img-thumbnail thumbnail-administrator" />
								<?php } ?>
								<div><?= $content->ambiente_imagen_ocupado; ?></div>
							</td>
							<td>
								<input type="hidden" id="<?= $id; ?>" value="<?= $content->orden; ?>"></input>
								<button class="up_table btn btn-primary btn-sm"><i class="fas fa-angle-up"></i></button>
								<button class="down_table btn btn-primary btn-sm"><i class="fas fa-angle-down"></i></button>
							</td> -->
							<td class="text-end">
								<div>
									<a class="btn btn-azul btn-sm <?php if (Session::getInstance()->get('kt_login_level') == '2') {
																									echo "d-none";
																								} ?>" href="<?php echo $this->route; ?>/manage?id=<?= $id ?>"
										data-bs-toggle="tooltip" data-placement="top" title="Editar"><i class="fas fa-pen-alt"></i></a>
									<span class="d-none" data-bs-toggle="tooltip" data-placement="top" title="Eliminar"><a
											class="btn btn-rojo btn-sm" data-bs-toggle="modal" data-bs-target="#modal<?= $id ?>"><i
												class="fas fa-trash-alt"></i></a></span>
									<button type="button" class="btn btn-azul-claro btn-sm btn-open-grid" data-id="<?= $id ?>"
										data-url="<?php echo $this->route; ?>/manage?id=<?= $id ?>&display=1">
										<i class="fas fa-eye"></i>
									</button>

									<a class="btn btn-primary btn-sm" target="_blank" href="/administracion/reservas/exportarreservaslistbyambiente?ambiente=<?= $id ?>&excel=1"
										data-bs-toggle="tooltip" data-placement="top" title="Descargar Invitados"><i class="fas fa-download"></i></a>

								</div>

								<!-- Modal -->
								<div class="modal fade text-start" id="modal<?= $id ?>" tabindex="-1" role="dialog"
									aria-labelledby="myModalLabel">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="modal-title" id="myModalLabel">Eliminar Registro</h4>
												<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
														aria-hidden="true">&times;</span></button>
											</div>
											<div class="modal-body">
												<div class="">¿Esta seguro de eliminar este registro?</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
												<a class="btn btn-danger"
													href="<?php echo $this->route; ?>/delete?id=<?= $id ?>&csrf=<?= $this->csrf; ?><?php echo ''; ?>">Eliminar</a>
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
				<div class="col-5">
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
				<div class="col-3">
					<div class="text-end"><a class="btn btn-sm btn-success" href="<?php echo $this->route . "\manage"; ?>"> <i
								class="fas fa-plus-square"></i> Crear Nuevo</a></div>
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


<!-- Modal Ambiente -->
<div class="modal fade text-start" id="modalGrid" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog" style="max-width:95%;width:95%;" role="document">
		<div class="modal-content">
			<div class="modal-header" style="background:var(--surface);border-bottom:1px solid var(--border);">
				<h4 class="modal-title" id="modalGridTitulo" style="font-size:.95rem;font-weight:600;display:flex;align-items:center;gap:8px;">
					<i class="fas fa-door-open" style="color:var(--brand-green);"></i>
					<span id="modalGridNombre">Ambiente</span>
				</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body" id="modalGridCuerpo" style="background:var(--bg);padding:1rem;max-height:93vh;overflow-y:auto;">
				<div class="text-center py-5">
					<div class="spinner-border text-primary" role="status"></div>
					<p class="mt-2 text-muted" style="font-size:.85rem;">Cargando...</p>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
/* El skin de administración fuerza .modal a z-index:1200, por encima del de SweetAlert2 (1060) */
.swal2-container { z-index: 20000 !important; }

.amb-kpi {
	background: var(--surface);
	border: 1px solid var(--border);
	border-radius: 8px;
	padding: 8px 12px;
	display: flex;
	align-items: center;
	gap: 10px;
	height: 100%;
}
.amb-kpi-icon {
	width: 32px; height: 32px;
	border-radius: 7px;
	display: flex; align-items: center; justify-content: center;
	font-size: .95rem; flex-shrink: 0;
}
.amb-kpi-val { font-size: 1.1rem; font-weight: 700; line-height: 1; }
.amb-kpi-label { font-size: .68rem; color: var(--text-muted); margin-top: 2px; }
.bar-chart-wrap { display: flex; align-items: flex-end; gap: 4px; height: 80px; }
.bar-col { display: flex; flex-direction: column; align-items: center; flex: 1; }
.bar-col .bar { width: 100%; background: var(--brand-green); border-radius: 3px 3px 0 0; min-height: 2px; transition: height .3s; }
.bar-col .bar-lbl { font-size: 9px; color: var(--text-muted); margin-top: 3px; }
.bar-col .bar-val { font-size: 9px; color: var(--text-muted); margin-bottom: 2px; }
.mesa-chip {
	display: inline-flex; align-items: center; gap: 4px;
	background: var(--surface); border: 1px solid var(--border);
	border-radius: 6px; padding: 3px 8px; font-size: .78rem; margin: 2px;
}
</style>

<script>
(function() {
	var MESES = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
	var ES_ADMIN_MAESTRO = <?= Session::getInstance()->get('kt_login_id') == '1' ? 'true' : 'false' ?>;

	<?php if (!empty($this->successEliminarReserva)): ?>
		Swal.fire({ icon: 'success', title: 'Listo', text: <?= json_encode($this->successEliminarReserva) ?>, confirmButtonText: 'Aceptar' });
	<?php endif; ?>
	<?php if (!empty($this->errorEliminarReserva)): ?>
		Swal.fire({ icon: 'error', title: 'No se pudo eliminar', text: <?= json_encode($this->errorEliminarReserva) ?>, confirmButtonText: 'Aceptar' });
	<?php endif; ?>

	function fv(v) { return (v !== null && v !== undefined && String(v).trim() !== '') ? v : '—'; }
	function fmt(n) { return '$ ' + Number(n).toLocaleString('es-CO'); }
	function bdg(cls, txt) { return '<span class="badge ' + cls + '">' + txt + '</span>'; }

	function badgeEstado(est) {
		var m = {'1':'text-bg-primary','2':'text-bg-success','3':'text-bg-success','4':'text-bg-warning','5':'text-bg-danger','6':'text-bg-danger','7':'text-bg-info','8':'text-bg-secondary','C':'text-bg-dark','11':'text-bg-success'};
		var t = {'1':'Creada','2':'Cargo acción','3':'Pago en línea','4':'Pendiente','5':'Fallido','6':'Rechazado','7':'Pend. sistema','8':'Inactiva','C':'Cancelada','11':'Datáfono'};
		return bdg(m[est] || 'text-bg-light', t[est] || est);
	}

	function kpiCard(icon, iconBg, val, label) {
		return '<div class="col-6"><div class="amb-kpi">'
			+ '<div class="amb-kpi-icon" style="background:' + iconBg + '20;color:' + iconBg + '"><i class="fas fa-' + icon + '"></i></div>'
			+ '<div><div class="amb-kpi-val">' + val + '</div><div class="amb-kpi-label">' + label + '</div></div>'
			+ '</div></div>';
	}

	function barChart(data) {
		var max = Math.max.apply(null, data) || 1;
		var h = '<div class="bar-chart-wrap">';
		data.forEach(function(v, i) {
			var pct = Math.round((v / max) * 76);
			h += '<div class="bar-col">'
				+ '<div class="bar-val">' + (v > 0 ? v : '') + '</div>'
				+ '<div class="bar" style="height:' + pct + 'px;"></div>'
				+ '<div class="bar-lbl">' + MESES[i] + '</div>'
				+ '</div>';
		});
		return h + '</div>';
	}

	function buildModal(d) {
		var amb = d.ambiente;
		var estadoBadge = amb.ambiente_estado == 1 ? bdg('text-bg-success', 'Activo') : bdg('text-bg-secondary', 'Inactivo');
		var iframeSrc = '/administracion/ambientes/manage?id=' + amb.ambiente_id + '&display=1&solo_mapa=1&px=22';

		var ocupadas      = parseInt(d.mesas_ocupadas)     || 0;
		var libres        = parseInt(d.mesas_libres)       || 0;
		var provisionales = parseInt(d.mesas_provisionales)|| 0;
		var totalM        = parseInt(d.total_mesas)        || 1;
		var pctOcup       = Math.round((ocupadas      / totalM) * 100);
		var pctLibre      = Math.round((libres         / totalM) * 100);
		var pctProv       = Math.max(0, 100 - pctOcup - pctLibre);

		var tm = 'color:var(--text-muted);font-size:.74rem;white-space:nowrap;padding:3px 0;width:100px;';
		var tv = 'padding:3px 0;font-size:.79rem;';

		var h = '';

		// ── 3 columnas ──
		h += '<div class="row g-3 mb-3" style="align-items:stretch;">';

		// ── COL 1: KPIs (sin div padre) + Reservas en div-dashboard debajo ──
		h += '<div class="col-md-4" style="display:flex;flex-direction:column;gap:10px;">';
		h += '<div class="row g-2">';
		h += kpiCard('chair',          'var(--brand-green)', d.total_mesas,     'Mesas');
		h += kpiCard('users',          'var(--brand)',       d.capacidad_total,  'Capacidad');
		h += kpiCard('calendar-check', '#f59e0b',            d.reservas_activas, 'Reservas activas');
		h += kpiCard('user-friends',   '#6366f1',            d.total_invitados,  'Invitados totales');
		h += '</div>';
		h += '<div class="div-dashboard" style="margin-top:0;flex:1;">';
		h += '<div style="padding:14px 15px;">';
		h += '<p style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:30px;"><i class="fas fa-chart-bar" style="margin-right:4px;"></i>Reservas ' + new Date().getFullYear() + '</p>';
		h += '<div class="pading-dashboard">' + barChart(d.reservas_por_mes) + '</div>';
		h += '</div>';
		h += '</div>';
		h += '</div>';

		// ── COL 2: Gráfica capacidad (doughnut CSS) ──
		var conicGrad = 'conic-gradient(#f59e0b 0% ' + pctOcup + '%, #fbbf24 ' + pctOcup + '% ' + (pctOcup + pctProv) + '%, #22c55e ' + (pctOcup + pctProv) + '% 100%)';
		h += '<div class="col-md-4">';
		h += '<div class="div-dashboard" style="margin-top:0;height:100%;">';
		h += '<div style="padding:14px 15px;display:flex;flex-direction:column;height:100%;box-sizing:border-box;">';
		h += '<p style="font-size:.66rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;"><i class="fas fa-chart-pie" style="margin-right:4px;"></i>Capacidad del ambiente</p>';
		h += '<div style="display:flex;align-items:center;justify-content:center;flex:1;padding:10px 0;">';
		h += '<div style="position:relative;width:140px;height:140px;">';
		h += '<div style="width:140px;height:140px;border-radius:50%;background:' + conicGrad + ';"></div>';
		h += '<div style="position:absolute;inset:20px;border-radius:50%;background:var(--surface);display:flex;flex-direction:column;align-items:center;justify-content:center;">';
		h += '<div style="font-size:1.5rem;font-weight:700;line-height:1;">' + pctOcup + '%</div>';
		h += '<div style="font-size:.65rem;color:var(--text-muted);">Ocupado</div>';
		h += '</div></div></div>';
		h += '<div class="row g-2 mt-1">';
		h += '<div class="col-4"><div style="text-align:center;padding:8px;background:#f59e0b15;border-radius:8px;"><div style="font-size:1.1rem;font-weight:700;color:#f59e0b;">' + ocupadas + '</div><div style="font-size:.65rem;color:var(--text-muted);">Ocupadas</div></div></div>';
		h += '<div class="col-4"><div style="text-align:center;padding:8px;background:#22c55e15;border-radius:8px;"><div style="font-size:1.1rem;font-weight:700;color:#22c55e;">' + libres + '</div><div style="font-size:.65rem;color:var(--text-muted);">Libres</div></div></div>';
		h += '<div class="col-4"><div style="text-align:center;padding:8px;background:#fbbf2415;border-radius:8px;"><div style="font-size:1.1rem;font-weight:700;color:#fbbf24;">' + provisionales + '</div><div style="font-size:.65rem;color:var(--text-muted);">Prov.</div></div></div>';
		h += '</div>';
		h += '</div></div></div>';

		// ── COL 3: Detalle estático del ambiente ──
		h += '<div class="col-md-4">';
		h += '<div class="div-dashboard" style="margin-top:0;height:100%;">';
		h += '<h2><i class="fas fa-door-open"></i> ' + fv(amb.ambiente_nombre) + ' &nbsp;' + estadoBadge + '</h2>';
		h += '<div style="padding:14px 15px;">';
		h += '<table style="width:100%;border-collapse:collapse;">';
		h += '<tr><td style="' + tm + '">Nombre</td><td style="' + tv + '"><strong>' + fv(amb.ambiente_nombre) + '</strong></td></tr>';
		h += '<tr><td style="' + tm + '">Piso</td><td style="' + tv + '">' + fv(d.piso_nombre) + '</td></tr>';
		h += '<tr><td style="' + tm + '">Capacidad</td><td style="' + tv + '">' + fv(amb.ambiente_capacidad) + ' personas</td></tr>';
		h += '<tr><td style="' + tm + '">Total mesas</td><td style="' + tv + '">' + fv(d.total_mesas) + '</td></tr>';
		h += '<tr><td style="' + tm + '">Estado</td><td style="' + tv + '">' + (amb.ambiente_estado == 1 ? bdg('text-bg-success','Activo') : bdg('text-bg-secondary','Inactivo')) + '</td></tr>';
		if (amb.ambiente_descuento > 0) h += '<tr><td style="' + tm + '">Descuento</td><td style="' + tv + '"><strong>' + fv(amb.ambiente_descuento) + '%</strong></td></tr>';
		h += '</table>';
		h += '</div></div></div>';

		h += '</div>'; // row g-3

		// ── Mapa + panel detalle de mesa al lado ──
		h += '<div class="div-dashboard" style="margin-top:12px;">';
		h += '<h2><i class="fas fa-map-marked-alt"></i> Mapa del ambiente</h2>';
		h += '<div style="padding:8px 16px 4px;">';
		h += '<div class="alert alert-info py-2 px-3 mb-0" style="font-size:.75rem;"><i class="fas fa-hand-pointer me-1"></i> <strong>Doble clic en una mesa</strong> para ver su detalle al lado.</div>';
		h += '</div>';
		h += '<div class="row g-2" style="padding:8px 16px 14px;">';

		// iframe mapa — col-8, altura calculada por filas*px para evitar cortes con grids verticales
		var _px = 22, _gap = 2;
		var _iframeH = Math.max(200, (parseInt(amb.ambiente_filas) || 10) * (_px + _gap) + 80);
		h += '<div class="col-8">';
		h += '<div style="max-height:65vh;overflow-y:auto;overflow-x:hidden;border-radius:4px;">';
		h += '<div id="mapaSpin" style="text-align:center;padding:40px 0;"><div class="spinner-border spinner-border-sm text-secondary"></div></div>';
		h += '<iframe src="' + iframeSrc + '" style="width:100%;max-width:100%;border:none;display:block;height:' + _iframeH + 'px;" onload="document.getElementById(\'mapaSpin\').style.display=\'none\';"></iframe>';
		h += '</div>';
		h += '</div>';

		// Panel detalle de mesa — col-4
		h += '<div class="col-4">';
		h += '<div id="mapaDetalle" style="height:100%;">';
		h += '<div style="background:var(--bg);border:1px dashed var(--border);border-radius:10px;padding:20px;text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;min-height:200px;">';
		h += '<i class="fas fa-chair" style="font-size:2rem;color:var(--text-muted);opacity:.4;"></i>';
		h += '<div style="font-size:.82rem;color:var(--text-muted);">Selecciona una mesa</div>';
		h += '<div style="font-size:.74rem;color:var(--text-muted);opacity:.7;">Verás aquí los detalles<br>de la mesa y su reserva.</div>';
		h += '</div></div></div>';

		h += '</div>'; // row mapa+detalle
		h += '<div style="padding:0 16px 14px;">';
		h += '<div class="alert alert-info py-1 px-3 mb-0" style="font-size:.73rem;"><i class="fas fa-info-circle me-1"></i> <strong>Leyenda:</strong> Las mesas en <span style="color:#f59e0b;font-weight:600;">amarillo</span> tienen reserva asignada. Las <span style="color:#22c55e;font-weight:600;">verdes</span> están disponibles.</div>';
		h += '</div>';
		h += '</div>'; // div-dashboard mapa

		return h;
	}

	function renderDetalleMesa(panel, data) {
		if (data.error) {
			panel.innerHTML = '<div style="padding:16px;text-align:center;color:#ef4444;font-size:.82rem;"><i class="fas fa-exclamation-circle"></i> Error al cargar.</div>';
			return;
		}
		var mesa          = data.mesa || {};
		var reserva       = data.reserva || null;
		var invitados     = data.invitados || [];
		var es_provisional= data.es_provisional || false;
		var cuposPendientes = data.cupos_pendientes || null;

		function fvL(v) { return (v !== null && v !== undefined && String(v).trim() !== '') ? v : '—'; }
		function bdgL(cls, txt) { return '<span class="badge ' + cls + '" style="font-size:.7rem;">' + txt + '</span>'; }

		function badgeEstadoL(est) {
			var m = {'1':'text-bg-primary','2':'text-bg-success','3':'text-bg-success','4':'text-bg-warning','7':'text-bg-info','C':'text-bg-dark','11':'text-bg-success'};
			var t = {'1':'Creada','2':'Cargo acción','3':'Pago en línea','4':'Pendiente','7':'Pend. sistema','C':'Cancelada','11':'Datáfono'};
			return bdgL(m[est] || 'text-bg-secondary', t[est] || ('Estado ' + est));
		}
		function badgeTipoInvL(tipo) {
			var m = {'S':'text-bg-primary','A':'text-bg-primary','P':'text-bg-secondary','H':'text-bg-info'};
			var t = {'S':'Cosocio','A':'Socio','P':'Invitado','H':'Hijo'};
			return bdgL(m[tipo] || 'text-bg-light', t[tipo] || tipo);
		}

		var capColor = es_provisional ? '#f59e0b' : (reserva ? '#3b82f6' : '#22c55e');
		var capTxt   = es_provisional ? '<i class="fas fa-exclamation-triangle"></i> Mesa provisional'
		             : (reserva ? '<i class="fas fa-calendar-check"></i> Reserva #' + reserva.id : '<i class="fas fa-chair"></i> Mesa disponible');

		var tm = 'color:var(--text-muted);font-size:.74rem;white-space:nowrap;padding:3px 0;width:100px;';
		var tv = 'padding:3px 0;font-size:.79rem;';

		var h = '<div style="border:1px solid var(--border);border-radius:10px;overflow:hidden;">';
		h += '<div style="background:' + capColor + ';padding:8px 14px;display:flex;align-items:center;gap:8px;">';
		h += '<span style="color:#fff;font-weight:600;font-size:.82rem;">' + capTxt + '</span>';
		h += '<span style="margin-left:auto;color:rgba(255,255,255,.8);font-size:.74rem;">' + fvL(mesa.mesa_codigo) + '</span>';
		h += '</div>';
		h += '<div style="padding:12px 14px;background:var(--surface);">';

		// Mesa info
		h += '<p style="font-size:.66rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Mesa</p>';
		h += '<table style="width:100%;border-collapse:collapse;margin-bottom:10px;">';
		h += '<tr><td style="' + tm + '">Nombre</td><td style="' + tv + '"><strong>' + fvL(mesa.mesa_nombre) + '</strong></td></tr>';
		if (mesa.mesa_capacidad) h += '<tr><td style="' + tm + '">Capacidad</td><td style="' + tv + '">' + mesa.mesa_capacidad + ' pax</td></tr>';
		h += '</table>';

		if (reserva) {
			h += '<hr style="border-color:var(--border);margin:8px 0;">';
			h += '<p style="font-size:.66rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Reserva</p>';
			h += '<table style="width:100%;border-collapse:collapse;margin-bottom:8px;">';
			h += '<tr><td style="' + tm + '">Titular</td><td style="' + tv + '"><strong>' + fvL(reserva.reserva_nombre_cliente) + ' ' + fvL(reserva.reserva_apellido_cliente) + '</strong></td></tr>';
			h += '<tr><td style="' + tm + '">CC</td><td style="' + tv + '">' + fvL(reserva.reserva_documento) + '</td></tr>';
			if (reserva.reserva_numero_carnet) h += '<tr><td style="' + tm + '">N° Acción</td><td style="' + tv + '">' + fvL(reserva.reserva_numero_carnet) + '</td></tr>';
			if (reserva.reserva_fecha) h += '<tr><td style="' + tm + '">Fecha</td><td style="' + tv + '">' + reserva.reserva_fecha + (reserva.reserva_hora ? ' ' + reserva.reserva_hora : '') + '</td></tr>';
			if (reserva.reserva_total_personas) h += '<tr><td style="' + tm + '">Personas</td><td style="' + tv + '">' + reserva.reserva_total_personas + '</td></tr>';
			h += '<tr><td style="' + tm + '">Estado</td><td style="' + tv + '">' + badgeEstadoL(String(reserva.reserva_estado)) + '</td></tr>';
			h += '</table>';

			if (cuposPendientes) {
				h += '<div style="background:#f5f3ff;border:1px solid #c4b5fd;border-radius:6px;padding:8px 10px;font-size:.78rem;color:#5b21b6;margin-bottom:10px;">'
					+ '<div style="font-weight:600;"><i class="fas fa-user-plus"></i> +' + cuposPendientes.cupos_adicionales + ' cupo(s) adicional(es) pendientes de pago</div>'
					+ '<div style="margin-top:2px;opacity:.85;">Capacidad ' + cuposPendientes.cupos_capacidad_anterior + ' → ' + cuposPendientes.cupos_capacidad_nueva + ' · $' + Number(cuposPendientes.precio_total).toLocaleString('es-CO') + '</div>'
					+ '</div>';
			}

			if (invitados.length > 0) {
				h += '<hr style="border-color:var(--border);margin:8px 0;">';
				h += '<p style="font-size:.66rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:4px;">Invitados (' + invitados.length + ')</p>';
				h += '<div style="max-height:160px;overflow-y:auto;">';
				invitados.forEach(function(inv, i) {
					var nombre = ((inv.invitadoReserva_nombre_invitado || '') + ' ' + (inv.invitadoReserva_apellido_invitado || '')).trim() || 'Sin nombre';
					h += '<div style="display:flex;align-items:center;gap:6px;padding:3px 0;border-bottom:1px solid var(--border);font-size:.77rem;">';
					h += '<span style="background:var(--border);border-radius:50%;width:18px;height:18px;display:inline-flex;align-items:center;justify-content:center;font-size:.62rem;flex-shrink:0;">' + (i+1) + '</span>';
					h += '<span style="flex:1;">' + nombre + '</span>';
					h += badgeTipoInvL(inv.invitadoReserva_estado_invitado || '');
					h += '</div>';
				});
				h += '</div>';
			}
		} else if (!es_provisional) {
			h += '<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:6px;padding:8px 10px;font-size:.78rem;color:#166534;margin-top:8px;"><i class="fas fa-check-circle"></i> Mesa disponible sin reserva asignada.</div>';
		}

		// ── Aumentar capacidad (solo si hay reserva pagada asociada) ──
		if (reserva && [2, 3, 11].indexOf(parseInt(reserva.reserva_estado)) !== -1) {
			h += '<hr style="border-color:var(--border);margin:10px 0 8px;">';
			h += '<p style="font-size:.66rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:6px;"><i class="fas fa-chair"></i> Aumentar capacidad</p>';
			h += '<div style="display:flex;gap:6px;">';
			h += '<input type="number" id="input-nueva-capacidad" min="' + (parseInt(mesa.mesa_capacidad) + 1) + '" placeholder="Ej: ' + (parseInt(mesa.mesa_capacidad) + 1) + '" style="width:90px;padding:6px 8px;border:1px solid #e2e8f0;border-radius:6px;font-size:.8rem;">';
			h += '<button onclick="solicitarAumentoCapacidad(' + mesa.mesa_id + ')" style="flex:1;padding:6px 10px;background:#8b5cf6;color:#fff;border:none;border-radius:6px;font-size:.78rem;font-weight:600;cursor:pointer;" onmouseover="this.style.background=\'#7c3aed\'" onmouseout="this.style.background=\'#8b5cf6\'"><i class="fas fa-plus-circle"></i> Generar cupos adicionales</button>';
			h += '</div>';
			h += '<div id="resultado-cupos-adicionales" style="margin-top:8px;"></div>';
		}

		// ── Acciones ──
		var estaOcupada = parseInt(mesa.mesa_estado) === 1;
		h += '<hr style="border-color:var(--border);margin:10px 0 8px;">';
		h += '<div style="display:flex;gap:8px;">';
		if (estaOcupada) {
			h += '<button onclick="liberarMesaDirect(' + mesa.mesa_id + ',' + (reserva ? reserva.id : 'null') + ')" style="flex:1;padding:7px 10px;background:#22c55e;color:#fff;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;" onmouseover="this.style.background=\'#16a34a\'" onmouseout="this.style.background=\'#22c55e\'"><i class="fas fa-unlock-alt"></i> Liberar</button>';
			if (reserva) {
				h += '<button onclick="iniciarCambioMesa(' + mesa.mesa_id + ',' + reserva.id + ')" style="flex:1;padding:7px 10px;background:#3b82f6;color:#fff;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;" onmouseover="this.style.background=\'#2563eb\'" onmouseout="this.style.background=\'#3b82f6\'"><i class="fas fa-exchange-alt"></i> Cambiar mesa</button>';
			}
		} else {
			h += '<button onclick="cambiarEstadoMesa(' + mesa.mesa_id + ',1)" style="flex:1;padding:7px 10px;background:#f59e0b;color:#fff;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;" onmouseover="this.style.background=\'#d97706\'" onmouseout="this.style.background=\'#f59e0b\'"><i class="fas fa-lock"></i> Marcar como ocupada</button>';
		}
		h += '</div>';
		if (estaOcupada && reserva) {
			h += '<div id="panel-cambio-mesa" style="display:none;margin-top:10px;border:1px solid var(--border);border-radius:8px;overflow:hidden;">';
			h += '<div style="background:#3b82f6;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;"><span style="color:#fff;font-size:.8rem;font-weight:600;"><i class="fas fa-exchange-alt"></i> Selecciona la mesa destino</span><button onclick="document.getElementById(\'panel-cambio-mesa\').style.display=\'none\'" style="background:rgba(255,255,255,.2);border:none;color:#fff;border-radius:4px;padding:2px 8px;font-size:.8rem;cursor:pointer;">&times;</button></div>';
			h += '<div id="lista-mesas-disponibles" style="padding:10px;"></div>';
			h += '</div>';
		}

		h += '</div></div>'; // padding + card
		panel.innerHTML = h;
	}

	window.cambiarEstadoMesa = function(mesaId, nuevoEstado) {
		fetch('/administracion/ambientes/cambiarEstadoMesa?mesa_id=' + mesaId + '&estado=' + nuevoEstado)
			.then(function(r) { return r.json(); })
			.then(function(data) {
				if (data.error) { alert('Error: ' + data.error); return; }
				// Recargar iframe del mapa
				var iframe = document.querySelector('#modalGridCuerpo iframe');
				if (iframe) iframe.src = iframe.src;
				// Actualizar panel con mensaje de confirmación
				var panel = document.getElementById('mapaDetalle');
				if (panel) {
					var msg = nuevoEstado === 0 ? 'Mesa marcada como <strong>libre</strong>.' : 'Mesa marcada como <strong>ocupada</strong>.';
					panel.innerHTML = '<div style="padding:20px;text-align:center;"><i class="fas fa-check-circle" style="font-size:1.6rem;color:#22c55e;display:block;margin-bottom:8px;"></i><div style="font-size:.82rem;color:var(--text-muted);">' + msg + '<br><span style="font-size:.75rem;opacity:.7;">Doble clic en la mesa para ver el detalle actualizado.</span></div></div>';
				}
			})
			.catch(function() { alert('Error al cambiar el estado de la mesa.'); });
	};

	window.liberarMesaDirect = function(mesaId, reservaId) {
		if (reservaId && ES_ADMIN_MAESTRO) {
			Swal.fire({
				icon: 'warning',
				title: '¿Liberar mesa y eliminar la reserva?',
				html: 'Esta mesa tiene la reserva <strong>#' + reservaId + '</strong> asignada.<br><br>' +
					'Al liberarla se <strong>eliminará la reserva por completo</strong>: se borrarán sus invitados, boletas y cupos adicionales, y quedará registrado en el log.<br><br>' +
					'Esta acción es <strong>irreversible</strong>.',
				showCancelButton: true,
				confirmButtonText: 'Sí, liberar y eliminar',
				cancelButtonText: 'Cancelar',
				confirmButtonColor: '#dc3545'
			}).then(function(result) {
				if (result.isConfirmed) {
					ejecutarEliminarReserva(reservaId);
				}
			});
			return;
		}
		if (reservaId && !ES_ADMIN_MAESTRO) {
			Swal.fire({
				icon: 'info',
				title: 'Mesa liberada',
				text: 'La mesa quedará marcada como libre, pero solo el administrador autorizado puede eliminar la reserva asociada.',
				confirmButtonText: 'Entendido'
			}).then(function() {
				cambiarEstadoMesa(mesaId, 0);
			});
			return;
		}
		cambiarEstadoMesa(mesaId, 0);
	};

	// Pide un token csrf fresco (el capturado al cargar la página puede haber rotado
	// por otras llamadas AJAX del mapa de mesas) y recién ahí ejecuta la eliminación.
	window.ejecutarEliminarReserva = function(reservaId) {
		fetch('/administracion/ambientes/obtenerCsrfEliminarReserva')
			.then(function(r) { return r.json(); })
			.then(function(data) {
				window.location.href = '/administracion/ambientes/eliminarreserva?id=' + reservaId + '&csrf=' + encodeURIComponent(data.csrf || '');
			})
			.catch(function() {
				Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo obtener el token de seguridad. Intenta de nuevo.' });
			});
	};

	window.iniciarCambioMesa = function(mesaId, reservaId) {
		var panel = document.getElementById('panel-cambio-mesa');
		if (!panel) return;
		panel.style.display = 'block';
		var lista = document.getElementById('lista-mesas-disponibles');
		lista.innerHTML = '<div style="text-align:center;padding:12px;"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>';
		fetch('/administracion/ambientes/mesasDisponiblesParaReasignar?mesa_id=' + mesaId + '&reserva_id=' + reservaId)
			.then(function(r) { return r.json(); })
			.then(function(data) {
				if (data.error) {
					lista.innerHTML = '<div style="color:#ef4444;font-size:.8rem;padding:8px;"><i class="fas fa-exclamation-circle"></i> ' + data.error + '</div>';
					return;
				}
				var mesas = data.mesas_disponibles || [];
				if (mesas.length === 0) {
					lista.innerHTML = '<div style="font-size:.8rem;color:var(--text-muted);text-align:center;padding:12px;"><i class="fas fa-info-circle"></i> No hay mesas disponibles con igual o mayor capacidad.</div>';
					return;
				}
				var h = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:6px;">';
				mesas.forEach(function(m) {
					var diff     = m.cap_diff > 0 ? ' (+' + m.cap_diff + ')' : '';
					var capBg    = m.cap_diff > 0 ? '#f0fdf4' : '#eff6ff';
					var capColor = m.cap_diff > 0 ? '#166534' : '#1e40af';
					var nombre   = m.mesa_nombre.replace(/'/g, '');
					h += '<button onclick="seleccionarMesaDestino(' + mesaId + ',' + m.mesa_id + ',\'' + nombre + '\')"'
					   + ' style="border:1px solid #e2e8f0;border-radius:7px;padding:6px 10px;background:#fff;cursor:pointer;text-align:left;width:100%;"'
					   + ' onmouseover="this.style.borderColor=\'#3b82f6\';this.style.background=\'#eff6ff\'"'
					   + ' onmouseout="this.style.borderColor=\'#e2e8f0\';this.style.background=\'#fff\'">'
					   + '<div style="font-size:.8rem;font-weight:600;color:#1e293b;">' + m.mesa_nombre + '</div>'
					   + '<div style="margin-top:2px;"><span style="background:' + capBg + ';color:' + capColor + ';border-radius:4px;padding:1px 5px;font-size:.68rem;">' + m.mesa_capacidad + ' pax' + diff + '</span></div>'
					   + '</button>';
				});
				h += '</div>';
				lista.innerHTML = h;
			})
			.catch(function() { lista.innerHTML = '<div style="color:#ef4444;font-size:.8rem;padding:8px;">Error al cargar mesas.</div>'; });
	};

	window.seleccionarMesaDestino = function(origenId, destinoId, destinoNombre) {
		var lista = document.getElementById('lista-mesas-disponibles');
		lista.innerHTML = '<div style="background:#fef3c7;border:1px solid #f59e0b;border-radius:7px;padding:10px 12px;font-size:.82rem;">'
			+ '<div style="font-weight:600;margin-bottom:8px;"><i class="fas fa-question-circle" style="color:#f59e0b;"></i> &nbsp;¿Mover reserva a <strong>' + destinoNombre + '</strong>?</div>'
			+ '<div style="display:flex;gap:8px;">'
			+ '<button onclick="confirmarReasignacion(' + origenId + ',' + destinoId + ')" style="flex:1;padding:6px 10px;background:#22c55e;color:#fff;border:none;border-radius:6px;font-size:.8rem;font-weight:600;cursor:pointer;"><i class="fas fa-check"></i> Confirmar</button>'
			+ '<button onclick="document.getElementById(\'panel-cambio-mesa\').style.display=\'none\'" style="flex:1;padding:6px 10px;background:#e2e8f0;color:#374151;border:none;border-radius:6px;font-size:.8rem;font-weight:600;cursor:pointer;">Cancelar</button>'
			+ '</div></div>';
	};

	window.confirmarReasignacion = function(origenId, destinoId) {
		var lista = document.getElementById('lista-mesas-disponibles');
		lista.innerHTML = '<div style="text-align:center;padding:12px;"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> <span style="font-size:.8rem;color:var(--text-muted);">Reasignando...</span></div>';
		fetch('/administracion/ambientes/liberarMesa', {
			method: 'POST',
			headers: {'Content-Type': 'application/json'},
			body: JSON.stringify({mesa_id_origen: origenId, mesa_id_destino: destinoId})
		})
		.then(function(r) { return r.json(); })
		.then(function(data) {
			if (data.error) {
				lista.innerHTML = '<div style="color:#ef4444;font-size:.8rem;padding:8px;"><i class="fas fa-exclamation-circle"></i> ' + data.error + '</div>';
				return;
			}
			var iframe = document.querySelector('#modalGridCuerpo iframe');
			if (iframe) iframe.src = iframe.src;
			var detalle = document.getElementById('mapaDetalle');
			if (detalle) {
				detalle.innerHTML = '<div style="padding:20px;text-align:center;">'
					+ '<i class="fas fa-check-circle" style="font-size:1.6rem;color:#22c55e;display:block;margin-bottom:8px;"></i>'
					+ '<div style="font-size:.82rem;color:var(--text-muted);">' + (data.message || 'Reserva reasignada correctamente.') + '<br><span style="font-size:.75rem;opacity:.7;">Doble clic en la mesa para ver el detalle actualizado.</span></div>'
					+ '</div>';
			}
		})
		.catch(function() { lista.innerHTML = '<div style="color:#ef4444;font-size:.8rem;padding:8px;">Error al reasignar.</div>'; });
	};

	window.solicitarAumentoCapacidad = function(mesaId) {
		var input = document.getElementById('input-nueva-capacidad');
		var resultado = document.getElementById('resultado-cupos-adicionales');
		var nuevaCapacidad = parseInt(input.value, 10);
		if (!nuevaCapacidad || nuevaCapacidad <= 0) {
			resultado.innerHTML = '<div style="color:#ef4444;font-size:.78rem;">Ingresa una capacidad válida.</div>';
			return;
		}
		resultado.innerHTML = '<div style="text-align:center;padding:8px;"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>';
		fetch('/administracion/ambientes/aumentarCapacidadMesa', {
			method: 'POST',
			headers: {'Content-Type': 'application/json'},
			body: JSON.stringify({mesa_id: mesaId, nueva_capacidad: nuevaCapacidad})
		})
		.then(function(r) { return r.json(); })
		.then(function(data) {
			if (data.error) {
				resultado.innerHTML = '<div style="color:#ef4444;font-size:.78rem;padding:6px 0;"><i class="fas fa-exclamation-circle"></i> ' + data.error + '</div>';
				return;
			}
			resultado.innerHTML = '<div style="background:#f5f3ff;border:1px solid #c4b5fd;border-radius:6px;padding:8px 10px;font-size:.78rem;color:#5b21b6;"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
			var iframe = document.querySelector('#modalGridCuerpo iframe');
			if (iframe) iframe.src = iframe.src;
		})
		.catch(function() { resultado.innerHTML = '<div style="color:#ef4444;font-size:.78rem;">Error al generar los cupos adicionales.</div>'; });
	};

	// Listener global para el postMessage del iframe del mapa (70/30 layout)
	window.addEventListener('message', function(e) {
		if (!e.data || e.data.type !== 'amb-mesa-detail') return;
		var panel = document.getElementById('mapaDetalle');
		if (!panel) return;
		renderDetalleMesa(panel, e.data);
	});

	function initAmbientesModal() {
		var modalEl = document.getElementById('modalGrid');
		var cuerpo  = document.getElementById('modalGridCuerpo');
		var nombre  = document.getElementById('modalGridNombre');
		if (!modalEl || !cuerpo || !nombre) return;
		var spin = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted" style="font-size:.85rem;">Cargando...</p></div>';

		document.querySelectorAll('.btn-open-grid').forEach(function(btn) {
			btn.addEventListener('click', function() {
				var id = this.dataset.id;
				nombre.textContent = 'Ambiente';
				cuerpo.innerHTML = spin;
				bootstrap.Modal.getOrCreateInstance(modalEl).show();

				fetch('/administracion/ambientes/infoAmbiente?id=' + id)
					.then(function(r) { return r.json(); })
					.then(function(d) {
						if (d.error) { cuerpo.innerHTML = '<div class="alert alert-danger">' + d.error + '</div>'; return; }
						nombre.textContent = d.ambiente.ambiente_nombre;
						cuerpo.innerHTML = buildModal(d);
					})
					.catch(function() {
						cuerpo.innerHTML = '<div class="alert alert-danger">Error al cargar la información. Intente nuevamente.</div>';
					});
			});
		});

		modalEl.addEventListener('hidden.bs.modal', function() {
			cuerpo.innerHTML = spin;
			nombre.textContent = 'Ambiente';
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initAmbientesModal);
	} else {
		initAmbientesModal();
	}
})();
</script>