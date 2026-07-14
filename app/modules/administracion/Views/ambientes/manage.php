<?php
function calcularCellSize($columnas, $filas, $soloMapa = false)
{
	if ($soloMapa) {
		// iframes en reservas/infodocumento: contenedor col-md-6, celda pequeña
		$anchoUtil = floor(480 / $columnas);
		$altoUtil  = floor(400 / $filas);
	} else {
		// modal de ambientes: contenedor col-8
		$anchoUtil = floor(800 / $columnas);
		$altoUtil  = floor(650 / $filas);
	}
	return max(18, min($anchoUtil, $altoUtil));
}
?>

<?php
// Datos simulados para el ejemplo
$ambiente = ['columnas' => $this->content->ambiente_columnas, 'filas' => $this->content->ambiente_filas];

// Variables para resaltar mesa específica (usado en validación)
// destacar_mesa puede traer una lista de ids separados por coma cuando la reserva
// tiene varias sillas/mesas asignadas (ver reservas/index.php e infodocumento/index.php)
$destacarMesa = $_GET['destacar_mesa'] ?? null;
$destacarMesaIds = $destacarMesa ? array_filter(array_map('intval', explode(',', $destacarMesa))) : [];
$destacarMesaSelector = implode(', ', array_map(function ($id) {
	return '.elemento[data-mesa-id="' . $id . '"]';
}, $destacarMesaIds));
$modoValidacion = $_GET['modo'] ?? null;
$soloMapa = isset($_GET['solo_mapa']) && $_GET['solo_mapa'] == '1';

if ($this->mesas) {
	$elementos = array_map(
		function ($el) {
			return (object) $el;
		},
		$this->mesas
	);
} else {
	$elementos = [];
}
?>

<?php if ($_GET["display"] == 1) { ?>
	<?php
	// ============================================================
	// TAMAÑO DE CELDA POR CONTEXTO — cambia estos valores a gusto
	// ============================================================
	$pxCeldaSoloMapa = 12; // iframes de reservas e infodocumento (columna col-md-6)
	$pxCeldaModal    = 29; // modal de ambientes (columna col-8)
	// ============================================================

	$pxCelda = $soloMapa ? $pxCeldaSoloMapa : $pxCeldaModal;
	if (isset($_GET['px']) && (int)$_GET['px'] > 0) {
		$pxCelda = max(8, min(60, (int)$_GET['px']));
	}
	?>
	<style>
		header,
		#panel-botones,
		.panel-titulo,
		footer {
			display: none;
		}

		body {
			background: #EBF0F6;
			width: 100%;
			/* overflow: hidden; */
		}

		.grid {
			margin: 15px 15px !important;
		}

		#contenido_panel {
			width: 99% !important;
		}
	</style>

	<?php
	$mesasOcupadas = 0;
	$mesasDisponibles = 0;
	foreach ($this->mesasModal as $mesa) {
		if ($mesa->mesa_estado == 1) $mesasOcupadas++;
		else $mesasDisponibles++;
	}
	$totalMesas = $mesasOcupadas + $mesasDisponibles;
	?>

	<!-- Fila 1: grid full width -->
	<div class="row mx-auto mt-2">
		<div class="col-12 px-3" id="grid-wrapper">
			<div id="grid" class="grid" style="width:fit-content;"></div>
		</div>
	</div>

	<!-- Fila 2: gráfica e indicadores | info de la mesa -->
	<?php if (!$soloMapa && $modoValidacion !== 'validacion'): ?>

	<!-- Alert-info col-12 cubriendo gráfica e info -->
	<div class="row mx-auto mt-3 px-2">
		<div class="col-12">
			<div class="alert alert-info mb-0 py-2 px-3" style="font-size:.78rem;">
				<i class="fas fa-hand-pointer"></i> <strong>Da clic en una mesa</strong> del mapa para ver su información, reserva e invitados asignados.
			</div>
		</div>
	</div>

	<div class="row mx-auto mt-2 g-3 px-2">

		<!-- col-6: KPIs + doughnut -->
		<div class="col-6">
			<div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:14px;height:100%;">

				<div style="font-size:.72rem;font-weight:700;color:#94a3b8;letter-spacing:.08em;text-transform:uppercase;margin-bottom:10px;">
					<i class="fas fa-chart-pie" style="margin-right:4px;"></i> Ocupación del ambiente
				</div>

				<div class="row g-2 mb-3">
					<div class="col-4">
						<div style="background:#eff6ff;border-radius:8px;padding:8px;text-align:center;">
							<div style="font-size:1.4rem;font-weight:700;color:#3b82f6;"><?= $totalMesas ?></div>
							<div style="font-size:.65rem;color:#64748b;">Total mesas</div>
						</div>
					</div>
					<div class="col-4">
						<div style="background:#fffbeb;border-radius:8px;padding:8px;text-align:center;">
							<div style="font-size:1.4rem;font-weight:700;color:#f59e0b;"><?= $mesasOcupadas ?></div>
							<div style="font-size:.65rem;color:#64748b;">Mesas ocupadas</div>
						</div>
					</div>
					<div class="col-4">
						<div style="background:#f0fdf4;border-radius:8px;padding:8px;text-align:center;">
							<div style="font-size:1.4rem;font-weight:700;color:#22c55e;"><?= $mesasDisponibles ?></div>
							<div style="font-size:.65rem;color:#64748b;">Disponibles</div>
						</div>
					</div>
				</div>

				<canvas id="mesasChart" style="max-width:180px;margin:0 auto;display:block;"></canvas>

				<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
				<script>
					(function(){
						const ctx = document.getElementById('mesasChart').getContext('2d');
						new Chart(ctx, {
							type: 'doughnut',
							data: {
								labels: ['Mesas ocupadas','Disponibles'],
								datasets: [{
									data: [<?= $mesasOcupadas ?>, <?= $mesasDisponibles ?>],
									backgroundColor: ['#f59e0b','#22c55e'],
									borderColor: ['#fff','#fff'],
									borderWidth: 3
								}]
							},
							options: {
								responsive: true,
								plugins: {
									legend: { position: 'bottom', labels: { font: { size: 10 }, padding: 10 } },
									tooltip: { enabled: true }
								},
								cutout: '65%'
							}
						});
					})();
				</script>
			</div>
		</div>

		<!-- col-6: info al dar clic en mesa -->
		<div class="col-6">
			<div style="height:100%;display:flex;flex-direction:column;gap:10px;">

				<div class="nameMesa" style="flex:1;transition:all .2s;">
					<div style="background:#f8fafc;border:1px dashed #cbd5e1;border-radius:12px;padding:24px;text-align:center;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;">
						<i class="fas fa-chair" style="font-size:2rem;color:#cbd5e1;"></i>
						<div style="font-size:.82rem;color:#94a3b8;">Selecciona una mesa en el mapa</div>
						<div style="font-size:.75rem;color:#cbd5e1;">Verás aquí los detalles de la mesa,<br>la reserva asignada y la lista de invitados.</div>
					</div>
				</div>

			</div>
		</div>

	</div>

	<!-- Leyenda 100% ancho -->
	<div class="row mx-auto px-2 mt-2">
		<div class="col-12">
			<div class="alert alert-info mb-0 py-2 px-3" style="font-size:.75rem;">
				<i class="fas fa-info-circle"></i>
				<strong>Leyenda:</strong> Las mesas en <span style="color:#f59e0b;font-weight:600;">amarillo</span> tienen reserva asignada. Las <span style="color:#22c55e;font-weight:600;">verdes</span> están disponibles.
			</div>
		</div>
	</div>
	<?php endif; ?>

<?php } else { ?>
	<?php
		$_cols = (int)($this->content->ambiente_columnas ?? 0);
		$_rows = (int)($this->content->ambiente_filas ?? 0);
		$pxCelda = ($_cols > 0 && $_rows > 0) ? max(30, min(floor(1100 / $_cols), floor(900 / $_rows))) : 30;
	?>

	<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
	<div class="container-fluid">
		<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>"
			data-toggle="validator">
			<div class="content-dashboard mb-0">
				<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
				<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
				<?php if ($this->content->ambiente_id) { ?>
					<input type="hidden" name="id" id="id" value="<?= $this->content->ambiente_id; ?>" />
				<?php } ?>
				<div class="row">
					<div class="col-2 form-group d-grid">
						<label class="control-label">Activo (Si, No)</label>
						<input type="checkbox" name="ambiente_estado" value="1" class="form-control switch-form " <?php if ($this->getObjectVariable($this->content, 'ambiente_estado') == 1) {
																																																				echo "checked";
																																																			} ?>></input>
						<div class="help-block with-errors"></div>
					</div>


					<!-- <div class="col-12 form-group">
					<label for="ambiente_evento" class="control-label">ambiente_evento</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->ambiente_evento; ?>" name="ambiente_evento"
							id="ambiente_evento" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div> -->
					<div class="col-2 form-group">
						<label class="control-label">Piso</label>
						<label class="input-group">

							<select class="form-control" name="ambiente_piso">
								<option value="">Seleccione...</option>
								<?php foreach ($this->list_ambiente_piso as $key => $value) { ?>
									<option <?php if ($this->getObjectVariable($this->content, "ambiente_piso") == $key) {
														echo "selected";
													} ?>
										value="<?php echo $key; ?>" /> <?= $value; ?></option>
								<?php } ?>
							</select>
						</label>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-2 form-group">
						<label for="ambiente_nombre" class="control-label">Nombre</label>
						<label class="input-group">

							<input type="text" value="<?= $this->content->ambiente_nombre; ?>" name="ambiente_nombre"
								id="ambiente_nombre" class="form-control">
						</label>
						<div class="help-block with-errors"></div>
					</div>
					<!-- <div class="col-3 form-group">
						<label for="ambiente_capacidad" class="control-label">Capacidad</label>
						<label class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text input-icono  fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
							</div>
							<input type="text" value="<?= $this->content->ambiente_capacidad; ?>" name="ambiente_capacidad"
								id="ambiente_capacidad" class="form-control">
						</label>
						<div class="help-block with-errors"></div>
					</div> -->
					<div class="col-2 form-group">
						<label class="control-label">Categoria</label>
						<label class="input-group">

							<select class="form-control" name="ambiente_categoria">
								<option value="">Seleccione...</option>
								<?php foreach ($this->list_ambiente_categoria as $key => $value) { ?>
									<option <?php if ($this->getObjectVariable($this->content, "ambiente_categoria") == $key) {
														echo "selected";
													} ?> value="<?php echo $key; ?>" /> <?= $value; ?></option>
								<?php } ?>
							</select>
						</label>
						<div class="help-block with-errors"></div>
					</div>

					<div class="col-3 form-group d-none">
						<label for="ambiente_imagen_disponible">Disponible</label>
						<input type="file" name="ambiente_imagen_disponible" id="ambiente_imagen_disponible"
							class="form-control  file-image" data-buttonName="btn-primary"
							accept="image/gif, image/jpg, image/jpeg, image/png">
						<div class="help-block with-errors"></div>
						<?php if ($this->content->ambiente_imagen_disponible) { ?>
							<div id="imagen_ambiente_imagen_disponible">
								<img src="/images/<?= $this->content->ambiente_imagen_disponible; ?>"
									class="img-thumbnail thumbnail-administrator" />
								<div><button class="btn btn-danger btn-sm" type="button"
										onclick="eliminarImagen('ambiente_imagen_disponible','<?php echo $this->route . "/deleteimage"; ?>')"><i
											class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
							</div>
						<?php } ?>
					</div>
					<div class="col-3 form-group  d-none">
						<label for="ambiente_imagen_pendiente">Pendiente</label>
						<input type="file" name="ambiente_imagen_pendiente" id="ambiente_imagen_pendiente"
							class="form-control  file-image" data-buttonName="btn-primary"
							accept="image/gif, image/jpg, image/jpeg, image/png">
						<div class="help-block with-errors"></div>
						<?php if ($this->content->ambiente_imagen_pendiente) { ?>
							<div id="imagen_ambiente_imagen_pendiente">
								<img src="/images/<?= $this->content->ambiente_imagen_pendiente; ?>"
									class="img-thumbnail thumbnail-administrator" />
								<div><button class="btn btn-danger btn-sm" type="button"
										onclick="eliminarImagen('ambiente_imagen_pendiente','<?php echo $this->route . "/deleteimage"; ?>')"><i
											class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
							</div>
						<?php } ?>
					</div>
					<div class="col-3 form-group  d-none">
						<label for="ambiente_imagen_ocupado">Ocupado</label>
						<input type="file" name="ambiente_imagen_ocupado" id="ambiente_imagen_ocupado"
							class="form-control  file-image" data-buttonName="btn-primary"
							accept="image/gif, image/jpg, image/jpeg, image/png">
						<div class="help-block with-errors"></div>
						<?php if ($this->content->ambiente_imagen_ocupado) { ?>
							<div id="imagen_ambiente_imagen_ocupado">
								<img src="/images/<?= $this->content->ambiente_imagen_ocupado; ?>"
									class="img-thumbnail thumbnail-administrator" />
								<div><button class="btn btn-danger btn-sm" type="button"
										onclick="eliminarImagen('ambiente_imagen_ocupado','<?php echo $this->route . "/deleteimage"; ?>')"><i
											class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
							</div>
						<?php } ?>
					</div>
					<div class="col-3 form-group  d-none">
						<label for="ambiente_imagen_ubicacion_en_piso">Ubicación en piso</label>
						<input type="file" name="ambiente_imagen_ubicacion_en_piso" id="ambiente_imagen_ubicacion_en_piso"
							class="form-control  file-image" data-buttonName="btn-primary"
							accept="image/gif, image/jpg, image/jpeg, image/png">
						<div class="help-block with-errors"></div>
						<?php if ($this->content->ambiente_imagen_ubicacion_en_piso) { ?>
							<div id="imagen_ambiente_imagen_ubicacion_en_piso">
								<img src="/images/<?= $this->content->ambiente_imagen_ubicacion_en_piso; ?>"
									class="img-thumbnail thumbnail-administrator" />
								<div><button class="btn btn-danger btn-sm" type="button"
										onclick="eliminarImagen('ambiente_imagen_ubicacion_en_piso','<?php echo $this->route . "/deleteimage"; ?>')"><i
											class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
							</div>
						<?php } ?>
					</div>


					<div class="col-1 form-group">
						<label for="ambiente_filas" class="control-label">Filas</label>
						<label class="input-group">

							<input type="text" value="<?= $this->content->ambiente_filas; ?>" name="ambiente_filas" id="ambiente_filas"
								class="form-control">
						</label>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-1 form-group">
						<label for="ambiente_columnas" class="control-label">Columnas</label>
						<label class="input-group">

							<input type="text" value="<?= $this->content->ambiente_columnas; ?>" name="ambiente_columnas"
								id="ambiente_columnas" class="form-control">
						</label>
						<div class="help-block with-errors"></div>
					</div>

					<div class="col-2 form-group">
						<label for="ambiente_descuento" class="control-label">Descuento</label>
						<label class="input-group">

							<input type="number" value="<?= $this->content->ambiente_descuento; ?>" name="ambiente_descuento"
								id="ambiente_descuento" class="form-control" min="0" max="100" step="0.01"
								oninput="validarDescuento(this)">
						</label>
						<div class="help-block with-errors"></div>
					</div>

					<div class="col-2 form-group">
						<label for="ambiente_fecha_partido" class="control-label">Inicio del partido</label>
						<label class="input-group">
							<input type="datetime-local" value="<?= $this->content->ambiente_fecha_partido ? date('Y-m-d\TH:i', strtotime($this->content->ambiente_fecha_partido)) : ''; ?>" name="ambiente_fecha_partido"
								id="ambiente_fecha_partido" class="form-control">
						</label>
						<div class="help-block with-errors">Las reservas cierran automáticamente al arrancar el partido, o antes si no quedan mesas disponibles.</div>
					</div>
					<div class="col-2 form-group">
						<label for="ambiente_precio_silla" class="control-label">Precio silla (por defecto)</label>
						<label class="input-group">
							<input type="number" value="<?= ($this->content->ambiente_precio_silla) ? $this->content->ambiente_precio_silla : ''; ?>" name="ambiente_precio_silla"
								id="ambiente_precio_silla" class="form-control" min="0" step="1">
						</label>
						<div class="help-block with-errors">Precio sugerido al crear una silla nueva en este ambiente. No afecta sillas ya creadas.</div>
					</div>
					<div class="col-12">
						<?php if (!empty($_GET['saved'])): ?>
						<div id="toast-saved" style="display:flex;align-items:center;justify-content:center;gap:9px;background:#f0fdf4;color:#15803d;border:1px solid #bbf7d0;padding:13px 24px;border-radius:8px;font-size:0.95rem;font-weight:500;margin-top:4px;">
							<i class="fas fa-check-circle" style="color:#22c55e;"></i> Cambios guardados correctamente
						</div>
						<script>setTimeout(function(){var t=document.getElementById('toast-saved');if(t){t.style.transition='opacity .6s';t.style.opacity='0';setTimeout(function(){t.remove();},600);}},3000);</script>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="botones-acciones my-3">
				<button class="btn btn-guardar" type="submit">Guardar</button>
				<a href="<?php echo $this->route; ?>" class="btn btn-cancelar">Cancelar</a>
				<?php if ($this->content->ambiente_id): ?>
				<button type="button" class="btn btn-azul-claro" onclick="openResumenAmbiente()">
					<i class="fas fa-eye"></i> Ver resumen
				</button>
				<?php endif; ?>
			</div>
		</form>
	</div>

	<!-- Modal resumen ambiente -->
	<div class="modal fade text-start" id="modalResumenAmbiente" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog" style="max-width:95%;width:95%;" role="document">
			<div class="modal-content">
				<div class="modal-header" style="background:var(--surface);border-bottom:1px solid var(--border);">
					<h4 class="modal-title" style="font-size:.95rem;font-weight:600;display:flex;align-items:center;gap:8px;">
						<i class="fas fa-door-open" style="color:var(--brand-green);"></i>
						<span id="resumenAmbienteNombre">Ambiente</span>
					</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body" id="resumenAmbienteCuerpo" style="background:var(--bg);padding:1rem;max-height:93vh;overflow-y:auto;">
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

	.amb-kpi { background:var(--surface);border:1px solid var(--border);border-radius:8px;padding:8px 12px;display:flex;align-items:center;gap:10px;height:100%; }
	.amb-kpi-icon { width:32px;height:32px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:.95rem;flex-shrink:0; }
	.amb-kpi-val { font-size:1.1rem;font-weight:700;line-height:1; }
	.amb-kpi-label { font-size:.68rem;color:var(--text-muted);margin-top:2px; }
	.bar-chart-wrap { display:flex;align-items:flex-end;gap:4px;height:80px; }
	.bar-col { display:flex;flex-direction:column;align-items:center;flex:1; }
	.bar-col .bar { width:100%;background:var(--brand-green);border-radius:3px 3px 0 0;min-height:2px;transition:height .3s; }
	.bar-col .bar-lbl { font-size:9px;color:var(--text-muted);margin-top:3px; }
	.bar-col .bar-val { font-size:9px;color:var(--text-muted);margin-bottom:2px; }
	.mesa-chip { display:inline-flex;align-items:center;gap:4px;background:var(--surface);border:1px solid var(--border);border-radius:6px;padding:3px 8px;font-size:.78rem;margin:2px; }
	</style>

	<script>
	(function() {
		var MESES = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
		var ES_ADMIN_MAESTRO = <?= Session::getInstance()->get('kt_login_id') == '1' ? 'true' : 'false' ?>;
		// Al eliminar una reserva desde aquí, se debe volver a este mismo ambiente (no al listado)
		var URL_RETORNO_ELIMINAR = '/administracion/ambientes/manage?id=<?= (int) ($this->content->ambiente_id ?? 0) ?>';

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
				h += '<div class="bar-col"><div class="bar-val">' + (v > 0 ? v : '') + '</div><div class="bar" style="height:' + pct + 'px;"></div><div class="bar-lbl">' + MESES[i] + '</div></div>';
			});
			return h + '</div>';
		}

		function buildModal(d) {
			var amb = d.ambiente;
			var estadoBadge = amb.ambiente_estado == 1 ? bdg('text-bg-success','Activo') : bdg('text-bg-secondary','Inactivo');
			var iframeSrc = '/administracion/ambientes/manage?id=' + amb.ambiente_id + '&display=1&solo_mapa=1&px=22';
			var ocupadas   = parseInt(d.mesas_ocupadas)     || 0;
			var libres     = parseInt(d.mesas_libres)       || 0;
			var provisionales = parseInt(d.mesas_provisionales) || 0;
			var totalM     = parseInt(d.total_mesas)        || 1;
			var pctOcup    = Math.round((ocupadas   / totalM) * 100);
			var pctLibre   = Math.round((libres     / totalM) * 100);
			var pctProv    = Math.max(0, 100 - pctOcup - pctLibre);
			var tm = 'color:var(--text-muted);font-size:.74rem;white-space:nowrap;padding:3px 0;width:100px;';
			var tv = 'padding:3px 0;font-size:.79rem;';
			var h = '';
			h += '<div class="row g-3 mb-3" style="align-items:stretch;">';
			h += '<div class="col-md-4" style="display:flex;flex-direction:column;gap:10px;">';
			h += '<div class="row g-2">';
			h += kpiCard('chair','var(--brand-green)',d.total_mesas,'Mesas');
			h += kpiCard('users','var(--brand)',d.capacidad_total,'Capacidad');
			h += kpiCard('calendar-check','#f59e0b',d.reservas_activas,'Reservas activas');
			h += kpiCard('user-friends','#6366f1',d.total_invitados,'Invitados totales');
			h += '</div>';
			h += '<div class="div-dashboard" style="margin-top:0;flex:1;"><div style="padding:14px 15px;">';
			h += '<p style="font-size:.7rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:30px;"><i class="fas fa-chart-bar" style="margin-right:4px;"></i>Reservas ' + new Date().getFullYear() + '</p>';
			h += '<div class="pading-dashboard">' + barChart(d.reservas_por_mes) + '</div>';
			h += '</div></div></div>';
			var conicGrad = 'conic-gradient(#f59e0b 0% ' + pctOcup + '%, #fbbf24 ' + pctOcup + '% ' + (pctOcup + pctProv) + '%, #22c55e ' + (pctOcup + pctProv) + '% 100%)';
			h += '<div class="col-md-4"><div class="div-dashboard" style="margin-top:0;height:100%;"><div style="padding:14px 15px;display:flex;flex-direction:column;height:100%;box-sizing:border-box;">';
			h += '<p style="font-size:.66rem;font-weight:700;color:var(--text-muted);text-transform:uppercase;letter-spacing:.06em;margin-bottom:10px;"><i class="fas fa-chart-pie" style="margin-right:4px;"></i>Capacidad del ambiente</p>';
			h += '<div style="display:flex;align-items:center;justify-content:center;flex:1;padding:10px 0;"><div style="position:relative;width:140px;height:140px;">';
			h += '<div style="width:140px;height:140px;border-radius:50%;background:' + conicGrad + ';"></div>';
			h += '<div style="position:absolute;inset:20px;border-radius:50%;background:var(--surface);display:flex;flex-direction:column;align-items:center;justify-content:center;"><div style="font-size:1.5rem;font-weight:700;line-height:1;">' + pctOcup + '%</div><div style="font-size:.65rem;color:var(--text-muted);">Ocupado</div></div>';
			h += '</div></div>';
			h += '<div class="row g-2 mt-1">';
			h += '<div class="col-4"><div style="text-align:center;padding:8px;background:#f59e0b15;border-radius:8px;"><div style="font-size:1.1rem;font-weight:700;color:#f59e0b;">' + ocupadas + '</div><div style="font-size:.65rem;color:var(--text-muted);">Ocupadas</div></div></div>';
			h += '<div class="col-4"><div style="text-align:center;padding:8px;background:#22c55e15;border-radius:8px;"><div style="font-size:1.1rem;font-weight:700;color:#22c55e;">' + libres + '</div><div style="font-size:.65rem;color:var(--text-muted);">Libres</div></div></div>';
			h += '<div class="col-4"><div style="text-align:center;padding:8px;background:#fbbf2415;border-radius:8px;"><div style="font-size:1.1rem;font-weight:700;color:#fbbf24;">' + provisionales + '</div><div style="font-size:.65rem;color:var(--text-muted);">Prov.</div></div></div>';
			h += '</div></div></div></div>';
			h += '<div class="col-md-4"><div class="div-dashboard" style="margin-top:0;height:100%;">';
			h += '<h2><i class="fas fa-door-open"></i> ' + fv(amb.ambiente_nombre) + ' &nbsp;' + estadoBadge + '</h2>';
			h += '<div style="padding:14px 15px;"><table style="width:100%;border-collapse:collapse;">';
			h += '<tr><td style="' + tm + '">Nombre</td><td style="' + tv + '"><strong>' + fv(amb.ambiente_nombre) + '</strong></td></tr>';
			h += '<tr><td style="' + tm + '">Piso</td><td style="' + tv + '">' + fv(d.piso_nombre) + '</td></tr>';
			h += '<tr><td style="' + tm + '">Capacidad</td><td style="' + tv + '">' + fv(amb.ambiente_capacidad) + ' personas</td></tr>';
			h += '<tr><td style="' + tm + '">Total mesas</td><td style="' + tv + '">' + fv(d.total_mesas) + '</td></tr>';
			h += '<tr><td style="' + tm + '">Estado</td><td style="' + tv + '">' + (amb.ambiente_estado == 1 ? bdg('text-bg-success','Activo') : bdg('text-bg-secondary','Inactivo')) + '</td></tr>';
			if (amb.ambiente_descuento > 0) h += '<tr><td style="' + tm + '">Descuento</td><td style="' + tv + '"><strong>' + fv(amb.ambiente_descuento) + '%</strong></td></tr>';
			h += '</table></div></div></div>';
			h += '</div>';
			var _px = 22, _gap = 2;
			var _iframeH = Math.max(200, (parseInt(amb.ambiente_filas) || 10) * (_px + _gap) + 80);
			h += '<div class="div-dashboard" style="margin-top:12px;">';
			h += '<h2><i class="fas fa-map-marked-alt"></i> Mapa del ambiente</h2>';
			h += '<div style="padding:8px 16px 4px;"><div class="alert alert-info py-2 px-3 mb-0" style="font-size:.75rem;"><i class="fas fa-hand-pointer me-1"></i> <strong>Doble clic en una mesa</strong> para ver su detalle al lado.</div></div>';
			h += '<div class="row g-2" style="padding:8px 16px 14px;">';
			h += '<div class="col-8"><div style="max-height:65vh;overflow-y:auto;overflow-x:hidden;border-radius:4px;">';
			h += '<div id="mapaSpin" style="text-align:center;padding:40px 0;"><div class="spinner-border spinner-border-sm text-secondary"></div></div>';
			h += '<iframe src="' + iframeSrc + '" style="width:100%;max-width:100%;border:none;display:block;height:' + _iframeH + 'px;" onload="document.getElementById(\'mapaSpin\').style.display=\'none\';"></iframe>';
			h += '</div></div>';
			h += '<div class="col-4"><div id="mapaDetalle" style="height:100%;">';
			h += '<div style="background:var(--bg);border:1px dashed var(--border);border-radius:10px;padding:20px;text-align:center;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;min-height:200px;">';
			h += '<i class="fas fa-chair" style="font-size:2rem;color:var(--text-muted);opacity:.4;"></i>';
			h += '<div style="font-size:.82rem;color:var(--text-muted);">Selecciona una mesa</div>';
			h += '</div></div></div>';
			h += '</div>';
			h += '<div style="padding:0 16px 14px;"><div class="alert alert-info py-1 px-3 mb-0" style="font-size:.73rem;"><i class="fas fa-info-circle me-1"></i> <strong>Leyenda:</strong> Las mesas en <span style="color:#f59e0b;font-weight:600;">amarillo</span> tienen reserva asignada. Las <span style="color:#22c55e;font-weight:600;">verdes</span> están disponibles.</div></div>';
			h += '</div>';
			return h;
		}

		function renderDetalleMesa(panel, data) {
			if (data.error) { panel.innerHTML = '<div style="padding:16px;text-align:center;color:#ef4444;font-size:.82rem;"><i class="fas fa-exclamation-circle"></i> Error al cargar.</div>'; return; }
			var mesa = data.mesa || {}, reserva = data.reserva || null, invitados = data.invitados || [];
			var es_provisional = data.es_provisional || false, cuposPendientes = data.cupos_pendientes || null;
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
			var capTxt = es_provisional ? '<i class="fas fa-exclamation-triangle"></i> Mesa provisional'
				: (reserva ? '<i class="fas fa-calendar-check"></i> Reserva #' + reserva.id : '<i class="fas fa-chair"></i> Mesa disponible');
			var tm = 'color:var(--text-muted);font-size:.74rem;white-space:nowrap;padding:3px 0;width:100px;';
			var tv = 'padding:3px 0;font-size:.79rem;';
			var h = '<div style="border:1px solid var(--border);border-radius:10px;overflow:hidden;">';
			h += '<div style="background:' + capColor + ';padding:8px 14px;display:flex;align-items:center;gap:8px;">';
			h += '<span style="color:#fff;font-weight:600;font-size:.82rem;">' + capTxt + '</span>';
			h += '<span style="margin-left:auto;color:rgba(255,255,255,.8);font-size:.74rem;">' + fvL(mesa.mesa_codigo) + '</span></div>';
			h += '<div style="padding:12px 14px;background:var(--surface);">';
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
					h += '<div style="background:#f5f3ff;border:1px solid #c4b5fd;border-radius:6px;padding:8px 10px;font-size:.78rem;color:#5b21b6;margin-bottom:10px;">';
					h += '<div style="font-weight:600;"><i class="fas fa-user-plus"></i> +' + cuposPendientes.cupos_adicionales + ' cupo(s) adicional(es) pendientes de pago</div>';
					h += '<div style="margin-top:2px;opacity:.85;">Capacidad ' + cuposPendientes.cupos_capacidad_anterior + ' → ' + cuposPendientes.cupos_capacidad_nueva + '</div></div>';
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
						h += badgeTipoInvL(inv.invitadoReserva_estado_invitado || '') + '</div>';
					});
					h += '</div>';
				}
				if ([2,3,11].indexOf(parseInt(reserva.reserva_estado)) !== -1) {
					h += '<hr style="border-color:var(--border);margin:10px 0 8px;">';
					h += '<div style="display:flex;gap:6px;">';
					h += '<input type="number" id="input-nueva-capacidad" min="' + (parseInt(mesa.mesa_capacidad)+1) + '" placeholder="Ej: ' + (parseInt(mesa.mesa_capacidad)+1) + '" style="width:90px;padding:6px 8px;border:1px solid #e2e8f0;border-radius:6px;font-size:.8rem;">';
					h += '<button onclick="solicitarAumentoCapacidad(' + mesa.mesa_id + ')" style="flex:1;padding:6px 10px;background:#8b5cf6;color:#fff;border:none;border-radius:6px;font-size:.78rem;font-weight:600;cursor:pointer;"><i class="fas fa-plus-circle"></i> Generar cupos adicionales</button>';
					h += '</div><div id="resultado-cupos-adicionales" style="margin-top:8px;"></div>';
				}
			} else if (!es_provisional) {
				h += '<div style="background:#f0fdf4;border:1px solid #86efac;border-radius:6px;padding:8px 10px;font-size:.78rem;color:#166534;margin-top:8px;"><i class="fas fa-check-circle"></i> Mesa disponible sin reserva asignada.</div>';
			}
			// Provisional funciona igual que ocupada: mismos botones de acción, solo cambia el término mostrado.
			var estaOcupada = parseInt(mesa.mesa_estado) === 1 || es_provisional;
			h += '<hr style="border-color:var(--border);margin:10px 0 8px;"><div style="display:flex;gap:8px;">';
			if (estaOcupada) {
				h += '<button onclick="liberarMesaDirect(' + mesa.mesa_id + ',' + (reserva ? reserva.id : 'null') + ')" style="flex:1;padding:7px 10px;background:#22c55e;color:#fff;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;"><i class="fas fa-unlock-alt"></i> Liberar</button>';
				if (reserva) h += '<button onclick="iniciarCambioMesa(' + mesa.mesa_id + ',' + reserva.id + ')" style="flex:1;padding:7px 10px;background:#3b82f6;color:#fff;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;"><i class="fas fa-exchange-alt"></i> Cambiar mesa</button>';
			} else {
				h += '<button onclick="cambiarEstadoMesa(' + mesa.mesa_id + ',1)" style="flex:1;padding:7px 10px;background:#f59e0b;color:#fff;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;"><i class="fas fa-lock"></i> Marcar como ocupada</button>';
			}
			h += '</div>';
			if (estaOcupada && reserva) {
				h += '<div id="panel-cambio-mesa" style="display:none;margin-top:10px;border:1px solid var(--border);border-radius:8px;overflow:hidden;">';
				h += '<div style="background:#3b82f6;padding:8px 12px;display:flex;align-items:center;justify-content:space-between;"><span style="color:#fff;font-size:.8rem;font-weight:600;"><i class="fas fa-exchange-alt"></i> Selecciona la mesa destino</span><button onclick="document.getElementById(\'panel-cambio-mesa\').style.display=\'none\'" style="background:rgba(255,255,255,.2);border:none;color:#fff;border-radius:4px;padding:2px 8px;font-size:.8rem;cursor:pointer;">&times;</button></div>';
				h += '<div id="lista-mesas-disponibles" style="padding:10px;"></div></div>';
			}
			h += '</div></div>';
			panel.innerHTML = h;
		}

		window.cambiarEstadoMesa = function(mesaId, nuevoEstado) {
			fetch('/administracion/ambientes/cambiarEstadoMesa?mesa_id=' + mesaId + '&estado=' + nuevoEstado)
				.then(function(r) { return r.json(); })
				.then(function(data) {
					if (data.error) { alert('Error: ' + data.error); return; }
					var iframe = document.querySelector('#resumenAmbienteCuerpo iframe');
					if (iframe) iframe.src = iframe.src;
					var panel = document.getElementById('mapaDetalle');
					if (panel) {
						var msg = nuevoEstado === 0 ? 'Mesa marcada como <strong>libre</strong>.' : 'Mesa marcada como <strong>ocupada</strong>.';
						panel.innerHTML = '<div style="padding:20px;text-align:center;"><i class="fas fa-check-circle" style="font-size:1.6rem;color:#22c55e;display:block;margin-bottom:8px;"></i><div style="font-size:.82rem;color:var(--text-muted);">' + msg + '</div></div>';
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
					window.location.href = '/administracion/ambientes/eliminarreserva?id=' + reservaId
						+ '&csrf=' + encodeURIComponent(data.csrf || '')
						+ '&redirect=' + encodeURIComponent(URL_RETORNO_ELIMINAR);
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
					if (data.error) { lista.innerHTML = '<div style="color:#ef4444;font-size:.8rem;padding:8px;">' + data.error + '</div>'; return; }
					var mesas = data.mesas_disponibles || [];
					if (!mesas.length) { lista.innerHTML = '<div style="font-size:.8rem;color:var(--text-muted);text-align:center;padding:12px;">No hay mesas disponibles.</div>'; return; }
					var h = '<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:6px;">';
					mesas.forEach(function(m) {
						var diff = m.cap_diff > 0 ? ' (+' + m.cap_diff + ')' : '';
						var bg = m.cap_diff > 0 ? '#f0fdf4' : '#eff6ff';
						var color = m.cap_diff > 0 ? '#166534' : '#1e40af';
						h += '<button onclick="previewReasignacion(' + mesaId + ',' + m.mesa_id + ')" style="padding:6px 4px;background:' + bg + ';border:1px solid ' + color + '30;border-radius:7px;font-size:.74rem;color:' + color + ';font-weight:600;cursor:pointer;text-align:center;">';
						h += (m.mesa_nombre || 'Mesa') + '<br><span style="font-size:.65rem;opacity:.8;">' + m.mesa_capacidad + ' pax' + diff + '</span></button>';
					});
					h += '</div>';
					lista.innerHTML = h;
				})
				.catch(function() { lista.innerHTML = '<div style="color:#ef4444;font-size:.8rem;padding:8px;">Error al cargar mesas.</div>'; });
		};
		window.previewReasignacion = function(origenId, destinoId) {
			var lista = document.getElementById('lista-mesas-disponibles');
			lista.innerHTML += '<div style="margin-top:8px;display:flex;gap:8px;">'
				+ '<button onclick="confirmarReasignacion(' + origenId + ',' + destinoId + ')" style="flex:1;padding:6px 10px;background:#22c55e;color:#fff;border:none;border-radius:6px;font-size:.8rem;font-weight:600;cursor:pointer;"><i class="fas fa-check"></i> Confirmar</button>'
				+ '<button onclick="document.getElementById(\'panel-cambio-mesa\').style.display=\'none\'" style="flex:1;padding:6px 10px;background:#e2e8f0;color:#374151;border:none;border-radius:6px;font-size:.8rem;font-weight:600;cursor:pointer;">Cancelar</button>'
				+ '</div>';
		};
		window.confirmarReasignacion = function(origenId, destinoId) {
			var lista = document.getElementById('lista-mesas-disponibles');
			lista.innerHTML = '<div style="text-align:center;padding:12px;"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>';
			fetch('/administracion/ambientes/liberarMesa', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({mesa_id_origen:origenId,mesa_id_destino:destinoId}) })
				.then(function(r) { return r.json(); })
				.then(function(data) {
					if (data.error) { lista.innerHTML = '<div style="color:#ef4444;font-size:.8rem;padding:8px;">' + data.error + '</div>'; return; }
					var iframe = document.querySelector('#resumenAmbienteCuerpo iframe');
					if (iframe) iframe.src = iframe.src;
					var detalle = document.getElementById('mapaDetalle');
					if (detalle) detalle.innerHTML = '<div style="padding:20px;text-align:center;"><i class="fas fa-check-circle" style="font-size:1.6rem;color:#22c55e;display:block;margin-bottom:8px;"></i><div style="font-size:.82rem;color:var(--text-muted);">' + (data.message || 'Reasignada correctamente.') + '</div></div>';
				})
				.catch(function() { lista.innerHTML = '<div style="color:#ef4444;font-size:.8rem;padding:8px;">Error al reasignar.</div>'; });
		};
		window.solicitarAumentoCapacidad = function(mesaId) {
			var input = document.getElementById('input-nueva-capacidad');
			var resultado = document.getElementById('resultado-cupos-adicionales');
			var nuevaCapacidad = parseInt(input.value, 10);
			if (!nuevaCapacidad || nuevaCapacidad <= 0) { resultado.innerHTML = '<div style="color:#ef4444;font-size:.78rem;">Ingresa una capacidad válida.</div>'; return; }
			resultado.innerHTML = '<div style="text-align:center;padding:8px;"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></div>';
			fetch('/administracion/ambientes/aumentarCapacidadMesa', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({mesa_id:mesaId,nueva_capacidad:nuevaCapacidad}) })
				.then(function(r) { return r.json(); })
				.then(function(data) {
					if (data.error) { resultado.innerHTML = '<div style="color:#ef4444;font-size:.78rem;"><i class="fas fa-exclamation-circle"></i> ' + data.error + '</div>'; return; }
					resultado.innerHTML = '<div style="background:#f5f3ff;border:1px solid #c4b5fd;border-radius:6px;padding:8px 10px;font-size:.78rem;color:#5b21b6;">' + data.message + '</div>';
					var iframe = document.querySelector('#resumenAmbienteCuerpo iframe');
					if (iframe) iframe.src = iframe.src;
				})
				.catch(function() { resultado.innerHTML = '<div style="color:#ef4444;font-size:.78rem;">Error al generar los cupos.</div>'; });
		};

		window.addEventListener('message', function(e) {
			if (!e.data || e.data.type !== 'amb-mesa-detail') return;
			var panel = document.getElementById('mapaDetalle');
			if (panel) renderDetalleMesa(panel, e.data);
		});

		window.openResumenAmbiente = function() {
			var modalEl = document.getElementById('modalResumenAmbiente');
			var cuerpo  = document.getElementById('resumenAmbienteCuerpo');
			var nombre  = document.getElementById('resumenAmbienteNombre');
			var id = <?= (int)($this->content->ambiente_id ?? 0) ?>;
			if (!id) return;
			var spin = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted" style="font-size:.85rem;">Cargando...</p></div>';
			cuerpo.innerHTML = spin;
			nombre.textContent = 'Cargando...';
			bootstrap.Modal.getOrCreateInstance(modalEl).show();
			fetch('/administracion/ambientes/infoAmbiente?id=' + id)
				.then(function(r) { return r.json(); })
				.then(function(d) {
					if (d.error) { cuerpo.innerHTML = '<div class="alert alert-danger">' + d.error + '</div>'; return; }
					nombre.textContent = d.ambiente.ambiente_nombre;
					cuerpo.innerHTML = buildModal(d);
				})
				.catch(function() { cuerpo.innerHTML = '<div class="alert alert-danger">Error al cargar la información.</div>'; });
		};

		document.addEventListener('DOMContentLoaded', function() {
			var modalEl = document.getElementById('modalResumenAmbiente');
			if (modalEl) {
				modalEl.addEventListener('hidden.bs.modal', function() {
					document.getElementById('resumenAmbienteCuerpo').innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted" style="font-size:.85rem;">Cargando...</p></div>';
					document.getElementById('resumenAmbienteNombre').textContent = 'Ambiente';
				});
			}
		});
	})();
	</script>

	<div id="modal-nueva-mesa" class="modal-mesa-bg">
		<div class="modal-mesa">
			<div class="modal-mesa-header">
				<h3><i class="fas fa-plus-circle"></i> Agregar nueva mesa</h3>
				<button class="modal-mesa-close" onclick="cerrarModalMesa()">&times;</button>
			</div>
			<div class="modal-mesa-body">
				<div class="form-group">
					<label>Tipo</label>
					<select id="input_tipo" class="form-control" onchange="actualizarDimensionesPorTipo(); actualizarCapacidadDefault(); actualizarVisibilidadPrecioSilla();">
						<option value="mesa">Mesa</option>
						<option value="silla">Silla</option>
						<option value="puerta">Puerta</option>
						<option value="barra">Barra</option>
						<option value="pista">Pista</option>
						<option value="tarima">Tarima</option>
						<option value="banos">Baños</option>
						<option value="escalera">Escaleras</option>
						<option value="pantalla">Pantalla</option>
						<option value="terraza">Terraza</option>
						<option value="ventana">Ventana</option>
					</select>
				</div>
				<div class="form-group">
					<label>Código</label>
					<input id="input_codigo" type="text" class="form-control" value="">
				</div>
				<div class="form-group">
					<label>Nombre</label>
					<input id="input_nombre" type="text" class="form-control" value="Nueva">
				</div>
				<div class="form-row">
					<div class="form-group col">
						<label>Ancho</label>
						<input id="input_ancho" type="number" class="form-control" value="2" min="1" onchange="actualizarCapacidadDefault()">
					</div>
					<div class="form-group col">
						<label>Alto</label>
						<input id="input_alto" type="number" class="form-control" value="2" min="1" onchange="actualizarCapacidadDefault()">
					</div>
					<div class="form-group col">
						<label>Rotación</label>
						<input id="input_rotacion" type="number" class="form-control" value="0" step="1">
					</div>
				</div>
				<div class="form-group">
					<label>Capacidad</label>
					<input id="input_capacidad" type="number" class="form-control" min="1" step="1">

					<!-- <select id="input_capacidad" class="form-control">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="4">4</option>
						<option value="6">6</option>
						<option value="8">8</option>
						<option value="10">10</option>
					</select> -->
				</div>
				<div class="form-group" id="grupo_precio_silla" style="display:none;">
					<label>Precio (silla)</label>
					<input id="input_precio" type="number" class="form-control" min="0" step="1" placeholder="<?php echo ($this->content->ambiente_precio_silla) ? $this->content->ambiente_precio_silla : ''; ?>">
					<small class="text-muted">Precio individual de esta silla. Si se deja vacío se guarda sin precio.</small>
				</div>
				<div class="form-group">
					<label>Estado</label>
					<select id="input_estado" class="form-control">
						<option value="0">Libre</option>
						<option value="1">Ocupada</option>
						<!-- <option value="2">Reservada</option> -->
					</select>
				</div>
				<!-- Inputs ocultos para datos adicionales de la mesa -->
				<input type="hidden" id="mesa_capacidad">
				<input type="hidden" id="input_forma">
				<input type="hidden" id="input_activa">
				<input type="hidden" id="input_orden">
				<input type="hidden" id="input_imagen_disponible">
				<input type="hidden" id="input_imagen_pendiente">
				<input type="hidden" id="input_imagen_ocupada">
				<input type="hidden" id="input_pos_x">
				<input type="hidden" id="input_pos_y">
				<input type="hidden" id="input_imagen_ubicacion_en_ambiente">
				<input type="hidden" id="input_imagen_ubicacion_en_piso">
			</div>
			<div class="modal-mesa-footer">
				<button class="btn btn-primary" id="btn-modal-mesa-accion"><i class="fas fa-check"></i> Crear</button>
				<button class="btn btn-secondary" onclick="cerrarModalMesa()">Cancelar</button>
			</div>
		</div>
	</div>

	<!-- Botón flotante para agregar elemento -->
	<button class="btn-floating-add" id="btn-agregar-elemento"
		data-bs-toggle="tooltip"
		data-bs-placement="left"
		data-bs-title="Agregar nuevo elemento">
		<i class="fas fa-plus"></i>
	</button>

	<div class="container-fluid mb-3">
		<div class="content-dashboard">

			<div id="scroll-top-bar-grid" style="overflow-x:auto; overflow-y:hidden; width:100%; height:18px; margin-bottom:2px;">
			</div>

			<div class="w-100 overflow-scroll" id="scroll-bottom-bar-grid">
				<div id="grid" class="grid" style="width: fit-content;"></div>
			</div>
		
		</div>
	</div>
	<div class="container mb-5 d-flex justify-content-end d-none">
		<button class="btn btn-warning" onclick="agregarElemento()">Agregar nueva mesa</button>
	</div>
	<div class="container mb-5 d-flex justify-content-end d-none ">
		<button class="btn btn-primary" onclick="guardarCambios()">Guardar Cambios</button>
	</div>

<?php } ?>


<style>
	:root {
		--primary-color: #667eea;
		--primary-hover: #5568d3;
		--success-color: #48bb78;
		--success-hover: #38a169;
		--danger-color: #f56565;
		--danger-hover: #e53e3e;
		--warning-color: #ed8936;
		--warning-hover: #dd6b20;
		--info-color: #4299e1;
		--dark-color: #2d3748;
		--light-bg: #f7fafc;
		--card-bg: #ffffff;
		--border-color: #e2e8f0;
		--text-primary: #2d3748;
		--text-secondary: #718096;
		--shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.08);
		--shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
		--shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05);
		--shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15), 0 10px 10px rgba(0, 0, 0, 0.04);
		--transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	}

	.modal-mesa-bg {
		display: none;
		position: fixed;
		top: 0;
		left: 0;
		width: 100vw;
		height: 100vh;
		background: rgba(0, 0, 0, 0.35);
		z-index: 1000;
		align-items: center;
		justify-content: center;
	}

	.modal-mesa {
		background: #fff;
		border-radius: 12px;
		box-shadow: 0 8px 32px rgba(0, 0, 0, 0.18);
		width: 350px;
		max-width: 95vw;
		padding: 0;
		display: flex;
		flex-direction: column;
		animation: modalFadeIn 0.2s;
	}

	.modal-mesa-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 18px 24px 10px 24px;
		border-bottom: 1px solid #eee;
	}

	.modal-mesa-header h3 {
		margin: 0;
		font-size: 1.25rem;
		color: #333;
	}

	.modal-mesa-close {
		background: none;
		border: none;
		font-size: 1.5rem;
		color: #888;
		cursor: pointer;
		transition: color 0.2s;
	}

	.modal-mesa-close:hover {
		color: #F44336;
	}

	.modal-mesa-body {
		padding: 18px 24px 0 24px;
	}

	.modal-mesa-footer {
		display: flex;
		justify-content: flex-end;
		gap: 10px;
		padding: 18px 24px 18px 24px;
		border-top: 1px solid #eee;
	}

	.modal-mesa .form-group {
		margin-bottom: 14px;
	}

	.modal-mesa .form-row {
		display: flex;
		gap: 10px;
	}

	.modal-mesa .form-row .form-group {
		flex: 1;
		margin-bottom: 0;
	}

	.modal-mesa .form-control {
		width: 100%;
		padding: 6px 10px;
		border: 1px solid #ccc;
		border-radius: 4px;
		font-size: 1rem;
		box-sizing: border-box;
	}

	.btn-primary {
		background: #4CAF50;
		color: #fff;
		border: none;
		border-radius: 4px;
		padding: 7px 18px;
		font-weight: bold;
		cursor: pointer;
		transition: background 0.2s;
	}

	.btn-primary:hover {
		background: #388e3c;
	}

	.btn-secondary {
		background: #eee;
		color: #333;
		border: none;
		border-radius: 4px;
		padding: 7px 18px;
		font-weight: bold;
		cursor: pointer;
		transition: background 0.2s;
	}

	.btn-secondary:hover {
		background: #ccc;
	}

	@keyframes modalFadeIn {
		from {
			opacity: 0;
			transform: translateY(-30px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	:root { --cell-px: <?= $pxCelda ?>px; }

	.grid {
		position: relative;
		display: grid;
		grid-template-columns: repeat(<?= $ambiente['columnas'] ?>, var(--cell-px));
		grid-template-rows: repeat(<?= $ambiente['filas'] ?>, var(--cell-px));
		gap: 2px;
		margin: auto;
		width: max-content;
		border: 3px solid var(--primary-color);
		background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
		border-radius: 12px;
		padding: 8px;
		box-shadow: var(--shadow-lg);
		overflow: hidden;
	}

	.grid-cell {
		width: var(--cell-px);
		height: var(--cell-px);
		border: 1px dashed #ddd;
	}

	.elemento {
		position: absolute;
		border-radius: 4px;
		font-size: 10px;
		font-weight: bold;
		color: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		box-sizing: border-box;
		cursor: move;
		z-index: 2;
		transform-origin: center center;
		text-shadow: 1px 1px 4px rgba(0, 0, 0, 1);
		text-align: center;
	}

	.mesa {
		background: #4CAF50;
	}

	.silla {
		background: #66BB6A;
	}

	.barra {
		background: #795548;
	}

	.puerta {
		background: #607D8B;
	}

	.pista {
		background: #9C27B0;
	}

	.tarima {
		background: #3F51B5;
	}

	.bano {
		background: #FF5722;
	}

	.escalera {
		background: #E91E63;
	}

	.ascensor {
		background: #00BCD4;
	}

	.pantalla {
		background: #FF9800;
	}

	.terraza {
		background: #ec94a2;
	}

	.ventana {
		background: fuchsia;
	}

	.libre {
		background-color: #4CAF50;
	}

	.ocupada {
		background-color: #F44336;
	}

	.reservada {
		background-color: #FF9800;
	}

	.pendiente {
		background-color: #FFC107;
	}

	.celda-hover {
		background: #b3e5fc !important;
		border: 2px solid #0288d1 !important;
		z-index: 1;
	}

	/* Estado especial: amarillo para mesa_estado == 1 (ocupada) o mesa_provision != null (provisional).
	   Ambas se ven y se comportan igual; solo cambia el término mostrado al hacer clic. */
	.amarillo-estado {
		background-color: #ffe066 !important;
		color: #333 !important;
		border: 2px solid #ffb700;
	}

	.inactivo {
		background-color: #9e9e9e !important;
		color: #666 !important;
		border: 1px solid #bdbdbd !important;
		opacity: 0.6 !important;
		pointer-events: none !important;
		cursor: default !important;
	}

	<?php if (!empty($destacarMesaIds) && $modoValidacion === 'validacion'): ?>

	/* En modo validación: todos los elementos en gris excepto los consultados */
	.elemento {
		background: #9e9e9e !important;
		/* Gris para todos los elementos */
		color: #666 !important;
		border: 1px solid #bdbdbd !important;
		opacity: 0.6 !important;
		pointer-events: none !important;
		cursor: default !important;
	}

	/* Estilos específicos para resaltar las mesas/sillas consultadas */
	<?= $destacarMesaSelector ?> {
		background: linear-gradient(45deg, #FFD700, #FFA500) !important;
		border: 3px solid #FF6B35 !important;
		box-shadow: 0 0 20px rgba(255, 215, 0, 0.8) !important;
		animation: pulseHighlight 2s infinite !important;
		z-index: 1000 !important;
		color: #333 !important;
		font-weight: bold !important;
		opacity: 1 !important;
		/* Completamente visible */
	}

	@keyframes pulseHighlight {

		0%,
		100% {
			box-shadow: 0 0 20px rgba(255, 215, 0, 0.8);
			transform: scale(1);
		}

		50% {
			box-shadow: 0 0 30px rgba(255, 215, 0, 1);
			transform: scale(1.05);
		}
	}

	.grid-cell {
		pointer-events: none !important;
	}

	/* Ocultar elementos de edición en modo validación */
	.modal-mesa-bg,
	.btn-warning,
	.btn-primary,
	.d-none {
		display: none !important;
	}

	.text-center.mb-3 {
		display: none !important;
	}

	.col-7 {
		width: 100% !important;
		display: grid;
		place-items: center;
		overflow: auto;
	}

	.col-5.my-2 {
		display: none !important;
	}

	<?php endif; ?>

	/* ========================================
	   BOTÓN FLOTANTE PARA AGREGAR ELEMENTOS
	   ======================================== */
	.btn-floating-add {
		position: fixed;
		bottom: 30px;
		right: 30px;
		width: 60px;
		height: 60px;
		border-radius: 50%;
		background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
		color: white;
		border: none;
		box-shadow: 0 4px 12px rgba(255, 152, 0, 0.4), 0 2px 4px rgba(0, 0, 0, 0.2);
		cursor: pointer;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 24px;
		z-index: 999;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		outline: none;
	}

	.btn-floating-add:hover {
		transform: scale(1.1) rotate(90deg);
		box-shadow: 0 6px 20px rgba(255, 152, 0, 0.6), 0 4px 8px rgba(0, 0, 0, 0.3);
		background: linear-gradient(135deg, #ffa726 0%, #ff9800 100%);
	}

	.btn-floating-add:active {
		transform: scale(0.95) rotate(90deg);
		box-shadow: 0 2px 8px rgba(255, 152, 0, 0.5);
	}

	.btn-floating-add i {
		transition: transform 0.3s ease;
	}

	.btn-floating-add:hover i {
		transform: rotate(0deg);
	}

	/* Animación de entrada */
	@keyframes fadeInUp {
		from {
			opacity: 0;
			transform: translateY(30px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	.btn-floating-add {
		animation: fadeInUp 0.5s ease-out;
	}

	/* Responsive para móviles */
	@media (max-width: 768px) {
		.btn-floating-add {
			width: 56px;
			height: 56px;
			bottom: 20px;
			right: 20px;
			font-size: 20px;
		}
	}



	#scroll-bottom-bar-grid {
		/* max-height: 70vh; */
		overflow: auto !important;
		padding-bottom: 15px;
	}


</style>

<!-- <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script> -->
<script src="/components/interactjs/interact.min.js"></script>

<script>
	// --- Lógica para edición/creación de mesas desde el modal ---
	let modalMesaModo = 'crear';
	let mesaEditandoId = null;

	function abrirModalMesa(modo, mesa, display = 0) { // Default display to 0

		console.log('abrirModalMesa llamado con modo:', modo, 'mesa:', mesa, 'display:', display);
		modalMesaModo = modo;
		mesaEditandoId = mesa && mesa.mesa_id ? mesa.mesa_id : null;

		if (display == 1) {
			// When display=1, show the table name in .nameMesa div and skip modal
			mostrarNombreMesa(mesa);
			return;
		}

		// Only attempt to open modal if display != 1
		const modal = document.getElementById('modal-nueva-mesa');
		if (!modal) {
			console.error('No se encontró el elemento con ID modal-nueva-mesa');
			return;
		}

		// Modal logic
		modal.style.display = 'flex';
		modal.style.alignItems = 'center';
		modal.style.justifyContent = 'center';

		// Cambiar título y botón
		const tituloHeader = document.querySelector('.modal-mesa-header h3');
		if (tituloHeader) {
			tituloHeader.innerHTML = modo === 'editar' ? '<i class="fas fa-edit"></i> Editar mesa' : '<i class="fas fa-plus-circle"></i> Agregar nueva mesa';
		}
		const footer = document.querySelector('.modal-mesa-footer');
		if (modo === 'editar' && mesa && mesa.mesa_estado == 0) {
			footer.innerHTML = `
				<button class="btn btn-danger" id="btn-eliminar-mesa"><i class="fas fa-trash"></i> Eliminar</button>
				<button class="btn btn-primary" id="btn-modal-mesa-accion"><i class="fas fa-check"></i> Guardar</button>
				<button class="btn btn-secondary" onclick="cerrarModalMesa()">Cancelar</button>
			`;
			document.getElementById('btn-eliminar-mesa').onclick = eliminarMesaDesdeModal;
			document.getElementById('btn-modal-mesa-accion').onclick = function() {
				crearElementoDesdeModal();
			};
		} else {
			footer.innerHTML = `
				<button class="btn btn-primary" id="btn-modal-mesa-accion"><i class="fas fa-check"></i> Crear</button>
				<button class="btn btn-secondary" onclick="cerrarModalMesa()">Cancelar</button>
			`;
			document.getElementById('btn-modal-mesa-accion').onclick = function() {
				crearElementoDesdeModal();
			};
		}

		// Rellenar campos si es edición
		document.getElementById('input_tipo').value = mesa && mesa.mesa_tipo ? mesa.mesa_tipo : 'mesa';
		document.getElementById('input_precio').value = mesa && mesa.mesa_precio != null && mesa.mesa_precio !== '' ? mesa.mesa_precio : '';
		actualizarVisibilidadPrecioSilla();
		document.getElementById('input_codigo').value = mesa && mesa.mesa_codigo ? mesa.mesa_codigo : '';
		document.getElementById('input_nombre').value = mesa && mesa.mesa_nombre ? mesa.mesa_nombre : 'Nueva';
		document.getElementById('input_ancho').value = mesa && mesa.mesa_ancho ? mesa.mesa_ancho : 2;
		document.getElementById('input_alto').value = mesa && mesa.mesa_alto ? mesa.mesa_alto : 2;
		document.getElementById('input_rotacion').value = mesa && typeof mesa.mesa_rotacion !== 'undefined' ? mesa.mesa_rotacion : 0;
		document.getElementById('input_estado').value = mesa && typeof mesa.mesa_estado !== 'undefined' ? mesa.mesa_estado : 0;
		// Setear capacidad
		if (mesa && typeof mesa.mesa_capacidad !== 'undefined' && mesa.mesa_capacidad !== null && mesa.mesa_capacidad !== '') {
			document.getElementById('input_capacidad').value = mesa.mesa_capacidad;
		} else {
			actualizarCapacidadDefault();
		}
		// Ocultos
		document.getElementById('input_forma').value = mesa && typeof mesa.mesa_forma !== 'undefined' ? mesa.mesa_forma : '';
		document.getElementById('input_activa').value = mesa && typeof mesa.mesa_activa !== 'undefined' ? mesa.mesa_activa : '';
		document.getElementById('input_orden').value = mesa && typeof mesa.orden !== 'undefined' ? mesa.orden : '';
		document.getElementById('input_imagen_disponible').value = mesa && typeof mesa.mesa_imagen_disponible !== 'undefined' ? mesa.mesa_imagen_disponible : '';
		document.getElementById('input_imagen_pendiente').value = mesa && typeof mesa.mesa_imagen_pendiente !== 'undefined' ? mesa.mesa_imagen_pendiente : '';
		document.getElementById('input_imagen_ocupada').value = mesa && typeof mesa.mesa_imagen_ocupada !== 'undefined' ? mesa.mesa_imagen_ocupada : '';
		document.getElementById('input_pos_x').value = mesa && typeof mesa.mesa_pos_x !== 'undefined' ? mesa.mesa_pos_x : '';
		document.getElementById('input_pos_y').value = mesa && typeof mesa.mesa_pos_y !== 'undefined' ? mesa.mesa_pos_y : '';
		document.getElementById('input_imagen_ubicacion_en_ambiente').value = mesa && typeof mesa.mesa_imagen_ubicacion_en_ambiente !== 'undefined' ? mesa.mesa_imagen_ubicacion_en_ambiente : '';
		document.getElementById('input_imagen_ubicacion_en_piso').value = mesa && typeof mesa.mesa_imagen_ubicacion_en_piso !== 'undefined' ? mesa.mesa_imagen_ubicacion_en_piso : '';
	}

	function mostrarNombreMesa(mesaSelect) {
		const nameMesaDiv = document.querySelector('.nameMesa');

		const s = 'background:var(--surface,#fff);border:1px solid var(--border,#e2e8f0);border-radius:10px;';
		const tm = 'color:#64748b;font-size:.75rem;white-space:nowrap;padding:3px 0;width:110px;';
		const tv = 'padding:3px 0;font-size:.8rem;';

		if (nameMesaDiv) nameMesaDiv.innerHTML = `<div style="${s}padding:12px 14px;"><div style="color:#94a3b8;font-size:.8rem;text-align:center;"><i class="fas fa-spinner fa-spin"></i> Cargando...</div></div>`;

		fetch(`/administracion/ambientes/consultaMesa?mesa_codigo=${mesaSelect.mesa_id}`)
			.then(r => r.json())
			.then(data => {
				if (!data || !data.mesa) {
					if (nameMesaDiv) nameMesaDiv.innerHTML = `<div style="${s}padding:12px 14px;"><p style="color:#94a3b8;font-size:.8rem;text-align:center;margin:0;"><i class="fas fa-info-circle"></i> Sin información para esta mesa.</p></div>`;
					try { window.parent.postMessage({ type: 'amb-mesa-detail', error: true }, '*'); } catch(e) {}
					return;
				}

				const { mesa, reserva, invitados, es_provisional, cupos_pendientes } = data;

				function fv(v) { return (v !== null && v !== undefined && String(v).trim() !== '') ? v : '—'; }
				function bdg(cls, txt) { return `<span class="badge ${cls}" style="font-size:.7rem;">${txt}</span>`; }

				function badgeEstado(est) {
					const m = {'1':'text-bg-primary','2':'text-bg-success','3':'text-bg-success','4':'text-bg-warning','7':'text-bg-info','C':'text-bg-dark','11':'text-bg-success'};
					const t = {'1':'Creada','2':'Cargo acción','3':'Pago en línea','4':'Pendiente','7':'Pend. sistema','C':'Cancelada','11':'Datáfono'};
					return bdg(m[est] || 'text-bg-secondary', t[est] || ('Estado ' + est));
				}

				function badgeTipoInv(tipo) {
					const m = {'S':'text-bg-primary','A':'text-bg-primary','P':'text-bg-secondary','H':'text-bg-info'};
					const t = {'S':'Cosocio','A':'Socio','P':'Invitado','H':'Hijo'};
					return bdg(m[tipo] || 'text-bg-light', t[tipo] || tipo);
				}

				const capColor = es_provisional ? '#f59e0b' : (reserva ? '#3b82f6' : '#22c55e');
				const capTxt   = es_provisional ? `<i class="fas fa-exclamation-triangle"></i> Mesa provisional` : (reserva ? `<i class="fas fa-calendar-check"></i> Reserva #${reserva.id}` : `<i class="fas fa-chair"></i> Mesa disponible`);

				let h = `<div style="${s}overflow:hidden;">`;

				// Header de color
				h += `<div style="background:${capColor};padding:8px 14px;border-radius:9px 9px 0 0;display:flex;align-items:center;gap:8px;">
					<span style="color:#fff;font-weight:600;font-size:.82rem;">${capTxt}</span>
					<span style="margin-left:auto;color:rgba(255,255,255,.8);font-size:.75rem;">${fv(mesa.mesa_codigo)}</span>
				</div>`;

				h += `<div style="padding:10px 14px;">`;

				// Info de la mesa
				h += `<div style="font-size:.72rem;font-weight:600;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;margin-bottom:6px;">Mesa</div>`;
				h += `<table style="width:100%;border-collapse:collapse;margin-bottom:10px;">`;
				h += `<tr><td style="${tm}">Nombre</td><td style="${tv}"><strong>${fv(mesa.mesa_nombre)}</strong></td></tr>`;
				h += `<tr><td style="${tm}">Tipo</td><td style="${tv}">${fv(mesa.mesa_tipo)}</td></tr>`;
				if (mesa.mesa_capacidad) h += `<tr><td style="${tm}">Capacidad</td><td style="${tv}">${mesa.mesa_capacidad} personas</td></tr>`;
				h += `</table>`;

				if (es_provisional) {
					h += `<div style="background:#fef3c7;border:1px solid #f59e0b;border-radius:6px;padding:8px 10px;font-size:.78rem;color:#92400e;"><i class="fas fa-exclamation-triangle"></i> Esta mesa está marcada como provisional.</div>`;
				} else if (reserva) {
					h += `<hr style="border-color:#e2e8f0;margin:8px 0;">`;
					h += `<div style="font-size:.72rem;font-weight:600;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;margin-bottom:6px;">Reserva</div>`;
					h += `<table style="width:100%;border-collapse:collapse;margin-bottom:10px;">`;
					h += `<tr><td style="${tm}">Titular</td><td style="${tv}"><strong>${fv(reserva.reserva_nombre_cliente)} ${fv(reserva.reserva_apellido_cliente)}</strong></td></tr>`;
					h += `<tr><td style="${tm}">CC</td><td style="${tv}">${fv(reserva.reserva_documento)}</td></tr>`;
					h += `<tr><td style="${tm}">N° Acción</td><td style="${tv}">${fv(reserva.reserva_numero_carnet)}</td></tr>`;
					if (reserva.reserva_fecha) h += `<tr><td style="${tm}">Fecha</td><td style="${tv}">${reserva.reserva_fecha}${reserva.reserva_hora ? ' — ' + reserva.reserva_hora : ''}</td></tr>`;
					if (reserva.reserva_total_personas) h += `<tr><td style="${tm}">Personas</td><td style="${tv}">${reserva.reserva_total_personas}</td></tr>`;
					if (reserva.reserva_telefono) h += `<tr><td style="${tm}">Teléfono</td><td style="${tv}">${reserva.reserva_telefono}</td></tr>`;
					h += `<tr><td style="${tm}">Estado</td><td style="${tv}">${badgeEstado(String(reserva.reserva_estado))}</td></tr>`;
					h += `</table>`;

					if (invitados && invitados.length > 0) {
						h += `<hr style="border-color:#e2e8f0;margin:8px 0;">`;
						h += `<div style="font-size:.72rem;font-weight:600;color:#94a3b8;letter-spacing:.05em;text-transform:uppercase;margin-bottom:6px;">Invitados (${invitados.length})</div>`;
						h += `<div style="max-height:160px;overflow-y:auto;">`;
						invitados.forEach(function(inv, i) {
							const nombre = (inv.invitadoReserva_nombre_invitado || '') + ' ' + (inv.invitadoReserva_apellido_invitado || '');
							h += `<div style="display:flex;align-items:center;gap:6px;padding:3px 0;border-bottom:1px solid #f1f5f9;font-size:.78rem;">`;
							h += `<span style="background:#e2e8f0;border-radius:50%;width:20px;height:20px;display:inline-flex;align-items:center;justify-content:center;font-size:.65rem;flex-shrink:0;">${i+1}</span>`;
							h += `<span style="flex:1;">${nombre.trim() || 'Sin nombre'}</span>`;
							h += badgeTipoInv(inv.invitadoReserva_estado_invitado || '');
							h += `</div>`;
						});
						h += `</div>`;
					}
				} else {
					h += `<div style="background:#f0fdf4;border:1px solid #22c55e;border-radius:6px;padding:8px 10px;font-size:.78rem;color:#166534;"><i class="fas fa-check-circle"></i> Esta mesa no tiene reserva asignada.</div>`;
				}

				// ── Botones de acción ── (provisional funciona igual que ocupada)
				if (reserva || es_provisional) {
					h += `<hr style="border-color:#e2e8f0;margin:10px 0 8px;">
					<button id="btnLiberar_${mesa.mesa_id}"
						onclick="mostrarFormLiberacion(${mesa.mesa_id}, ${reserva ? reserva.id : 'null'})"
						style="width:100%;padding:8px;background:#ef4444;color:#fff;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;"
						onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
						<i class="fas fa-exchange-alt"></i> Liberar mesa
					</button>
					<div id="panelLiberacion_${mesa.mesa_id}" style="display:none;margin-top:8px;"></div>`;
				}
				if (!reserva && !es_provisional) {
					h += `<hr style="border-color:#e2e8f0;margin:10px 0 8px;">
					<button onclick="mostrarFormAsignar(${mesa.mesa_id})"
						style="width:100%;padding:8px;background:#22c55e;color:#fff;border:none;border-radius:7px;font-size:.82rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;"
						onmouseover="this.style.background='#16a34a'" onmouseout="this.style.background='#22c55e'">
						<i class="fas fa-link"></i> Asignar a reserva
					</button>
					<div id="formAsignar_${mesa.mesa_id}" style="display:none;margin-top:8px;"></div>`;
				}

				h += `</div></div>`;
				if (nameMesaDiv) nameMesaDiv.innerHTML = h;
				// Notificar al padre (layout 70/30 en ambientes/index.php)
				try { window.parent.postMessage({ type: 'amb-mesa-detail', mesa: mesa, reserva: reserva || null, invitados: invitados || [], es_provisional: !!es_provisional, cupos_pendientes: cupos_pendientes || null }, '*'); } catch(e) {}
			})
			.catch(function() {
				if (nameMesaDiv) nameMesaDiv.innerHTML = `<div style="${s}padding:12px 14px;"><p style="color:#ef4444;font-size:.8rem;text-align:center;margin:0;"><i class="fas fa-exclamation-circle"></i> Error al cargar la información.</p></div>`;
				try { window.parent.postMessage({ type: 'amb-mesa-detail', error: true }, '*'); } catch(e) {}
			});
	}


	function agregarElemento() {
		//RESET: Asegurarse de que está en modo crear
		modalMesaModo = 'crear';
		mesaEditandoId = null;
		console.log('Agregar elemento - modo:', modalMesaModo, 'ID mesa:', mesaEditandoId);

		// Abrir modal en modo crear con valores por defecto
		abrirModalMesa('crear', null);
	}

	document.addEventListener('DOMContentLoaded', function() {
		document.getElementById('btn-modal-mesa-accion').onclick = function() {
			crearElementoDesdeModal();
		};
	});

	// Asigna listeners de edición a cada elemento visual de mesa/barra/puerta
	// Permite abrir el modal de edición al hacer doble clic sobre el elemento
	function agregarListenersEdicion(div, el) {
		div.addEventListener('dblclick', function(e) {
			const display = <?= isset($_GET["display"]) ? $_GET["display"] : 0 ?>;
			// Al hacer doble clic, se abre el modal en modo edición con los datos de la mesa
			abrirModalMesa('editar', el, display);
		});
	}
	// --- Variables globales de configuración y datos ---
	const columnas = <?= $ambiente['columnas'] ?>; // Número de columnas del ambiente
	const filas = <?= $ambiente['filas'] ?>; // Número de filas del ambiente
	let cellSize = <?= $pxCelda; ?>; // Tamaño de cada celda en px (actualizable)
	const gap = 2; // Espacio entre celdas
	let elementos = <?= json_encode($elementos) ?>; // Array de mesas/barra/puerta
	const grid = document.getElementById('grid'); // Contenedor visual del grid

	// occupancyMap: matriz que indica qué mesa ocupa cada celda (para colisiones y validaciones)
	const occupancyMap = Array.from({
		length: filas
	}, () => Array(columnas).fill(null));

	// Crea la grilla visual de celdas (background), no las mesas
	function crearGrillaVisual() {
		window.celdas = [];
		for (let i = 0; i < filas * columnas; i++) {
			const celda = document.createElement('div');
			celda.className = 'grid-cell';
			grid.appendChild(celda);
			window.celdas.push(celda);
		}
	}
	// --- Resaltado visual de celdas destino al mover/crear mesas ---
	let celdasHover = [];
	// Resalta las celdas donde se colocaría una mesa/barra/puerta (útil para feedback visual al mover)
	function resaltarCeldasFigura(col, row, ancho, alto) {
		limpiarResaltado();
		for (let i = 0; i < alto; i++) {
			for (let j = 0; j < ancho; j++) {
				const r = row + i;
				const c = col + j;
				if (r >= 0 && c >= 0 && r < filas && c < columnas) {
					const idx = r * columnas + c;
					window.celdas[idx].classList.add('celda-hover');
					celdasHover.push(idx);
				}
			}
		}
	}
	// Limpia el resaltado de celdas
	function limpiarResaltado() {
		celdasHover.forEach(idx => {
			if (window.celdas[idx]) window.celdas[idx].classList.remove('celda-hover');
		});
		celdasHover = [];
	}

	// Marca en occupancyMap las celdas ocupadas por una mesa/barra/puerta
	function marcarOcupado(el, id) {
		for (let i = 0; i < el.mesa_alto; i++) {
			for (let j = 0; j < el.mesa_ancho; j++) {
				const y = el.mesa_pos_y + i;
				const x = el.mesa_pos_x + j;
				if (y < filas && x < columnas) {
					occupancyMap[y][x] = id;
				}
			}
		}
	}

	// Verifica si una nueva posición de mesa colisiona con otra o sale del grid
	// id: permite ignorar la propia mesa al moverla
	function hayColision(nuevo, id) {
		for (let i = 0; i < nuevo.mesa_alto; i++) {
			for (let j = 0; j < nuevo.mesa_ancho; j++) {
				const y = nuevo.y + i;
				const x = nuevo.x + j;
				if (y >= filas || x >= columnas) return true; // Fuera de límites
				if (occupancyMap[y][x] !== null && occupancyMap[y][x] !== id) return true; // Colisión con otra mesa
			}
		}
		return false;
	}
	let idTemporal = 0;

	// function getClosestCapacity(val) {
	// 	const options = [1, 2, 4, 6, 8, 10];
	// 	return options.reduce((prev, curr) => Math.abs(curr - val) < Math.abs(prev - val) ? curr : prev);
	// }

	// Al crear un elemento nuevo, silla usa 1x1 por defecto; el resto de tipos usa 2x2.
	function actualizarDimensionesPorTipo() {
		if (modalMesaModo !== 'crear') return;
		const tipo = document.getElementById('input_tipo').value;
		const size = tipo === 'silla' ? 1 : 2;
		document.getElementById('input_ancho').value = size;
		document.getElementById('input_alto').value = size;
	}

	function actualizarCapacidadDefault() {
		const tipo = document.getElementById('input_tipo').value;
		const ancho = parseInt(document.getElementById('input_ancho').value) || 1;
		const alto = parseInt(document.getElementById('input_alto').value) || 1;
		// const capacidadCalculada = tipo === 'mesa' ? ancho * alto : 0;
		// const capacidadDefault = tipo === 'mesa' ? getClosestCapacity(capacidadCalculada) : 1;
		if (tipo === 'mesa') {
			const capacidadCalculada = ancho * alto;
			document.getElementById('input_capacidad').value = capacidadCalculada;
		} else {
			// Sillas y decoraciones: capacidad 1 (una silla = un asiento).
			document.getElementById('input_capacidad').value = 1;
		}
		// document.getElementById('input_capacidad').value = capacidadDefault;
	}

	// Muestra el campo de precio solo cuando el elemento es una silla.
	function actualizarVisibilidadPrecioSilla() {
		const tipo = document.getElementById('input_tipo').value;
		const grupo = document.getElementById('grupo_precio_silla');
		if (grupo) {
			grupo.style.display = (tipo === 'silla') ? 'block' : 'none';
		}
	}

	// Cierra el modal y limpia todos los campos (incluidos los ocultos)
	function cerrarModalMesa() {
		document.getElementById('modal-nueva-mesa').style.display = 'none';
		// Limpiar todos los campos del modal, incluidos los ocultos
		const ids = [
			'input_tipo', 'input_codigo', 'input_nombre', 'input_ancho', 'input_alto', 'input_rotacion', 'input_estado',
			'input_capacidad', 'input_precio', 'input_forma', 'input_activa', 'input_orden',
			'input_imagen_disponible', 'input_imagen_pendiente', 'input_imagen_ocupada',
			'input_pos_x', 'input_pos_y', 'input_imagen_ubicacion_en_ambiente', 'input_imagen_ubicacion_en_piso'
		];
		ids.forEach(id => {
			const el = document.getElementById(id);
			if (el) {
				if (el.tagName === 'SELECT') {
					el.selectedIndex = 0;
				} else {
					el.value = '';
				}
			}
		});
	}


	// Toma los datos del modal y decide si crear o editar una mesa
	function crearElementoDesdeModal() {
		// Obtiene los valores de los campos del modal
		const tipo = document.getElementById('input_tipo').value;
		const nombre = document.getElementById('input_nombre').value;
		const codigo = document.getElementById('input_codigo').value.trim();

		const ancho = parseInt(document.getElementById('input_ancho').value);
		const alto = parseInt(document.getElementById('input_alto').value);
		const rotacion = parseInt(document.getElementById('input_rotacion').value);
		const estado = document.getElementById('input_estado').value;
		// Prepara el objeto de datos para enviar al backend
		const mesaData = {
			mesa_tipo: tipo,
			mesa_codigo: codigo,
			mesa_nombre: nombre,
			mesa_ancho: ancho,
			mesa_capacidad: parseInt(document.getElementById('input_capacidad').value) || 0,
			// Precio solo para sillas; para el resto de tipos se envía null.
			mesa_precio: tipo === 'silla' ? (parseFloat(document.getElementById('input_precio').value) || null) : null,
			mesa_alto: alto,
			mesa_rotacion: rotacion,
			mesa_estado: estado,
			mesa_pos_x: document.getElementById('input_pos_x').value || 0,
			mesa_pos_y: document.getElementById('input_pos_y').value || 0,
			mesa_ambiente: <?= $this->content->ambiente_id ?>,
			mesa_activa: document.getElementById('input_activa').value || 1,
			mesa_forma: document.getElementById('input_forma').value || '',
			orden: document.getElementById('input_orden').value || 0,
			mesa_imagen_disponible: document.getElementById('input_imagen_disponible').value || '',
			mesa_imagen_pendiente: document.getElementById('input_imagen_pendiente').value || '',
			mesa_imagen_ocupada: document.getElementById('input_imagen_ocupada').value || '',
			mesa_imagen_ubicacion_en_ambiente: document.getElementById('input_imagen_ubicacion_en_ambiente').value || '',
			mesa_imagen_ubicacion_en_piso: document.getElementById('input_imagen_ubicacion_en_piso').value || ''
		};
		// Decide si es edición o creación
		if (modalMesaModo === 'editar') {
			mesaData.mesa_id = mesaEditandoId;
			editarMesaDesdeModal(mesaData);
		} else {
			crearMesaDesdeModal(mesaData);
		}
	}

	// Renderiza todos los elementos (mesas, barras, puertas) sobre el grid visual
	// Asigna listeners, estilos y funcionalidad de drag&drop
	function renderElementos() {
		elementos.forEach(el => {
			marcarOcupado(el, el.mesa_id); // Marca las celdas ocupadas por este elemento

			// Crea el div visual para la mesa/barra/puerta
			const div = document.createElement('div');
			// Solo los elementos de tipo "mesa" cambian de color por estado/provisión.
			// Provisional (mesa_provision != null/'') se ve y funciona igual que ocupada
			// (mesa_estado == 1): mismo color amarillo, solo cambia el término al hacer clic.
			let estadoClase = '';
			if (el.mesa_tipo === 'mesa' || el.mesa_tipo === 'silla') {
				const esProvisional = el.mesa_provision !== null && el.mesa_provision !== undefined && el.mesa_provision !== '';
				if (esProvisional || el.mesa_estado == 1) {
					estadoClase = 'amarillo-estado';
				}
			}
			let estadoActivo
			if (el.mesa_activa == 0) {
				estadoActivo = 'inactivo'
			}

			div.className = `elemento ${el.mesa_tipo} ${el.mesa_estado || ''} ${estadoClase} ${estadoActivo}`;
			if (el.mesa_capacidad) {
				div.innerHTML = '<div style="line-height:1.2;text-align:center;">' + (el.mesa_nombre || el.mesa_tipo) + '<br><span style="font-size:1em;opacity:.9;display:block;">' + el.mesa_capacidad + ' pax</span></div>';
			} else {
				div.innerText = el.mesa_nombre || el.mesa_tipo;
			}
			div.dataset.id = el.mesa_id;
			div.dataset.mesaId = el.mesa_id; // Agregar data-mesa-id para la funcionalidad de resaltado
			div.dataset.rotation = el.mesa_rotacion || '0';

			// Posiciona y dimensiona el elemento según sus datos
			Object.assign(div.style, {
				width: (cellSize * el.mesa_ancho + gap * (el.mesa_ancho - 1)) + 'px',
				height: (cellSize * el.mesa_alto + gap * (el.mesa_alto - 1)) + 'px',
				top: (el.mesa_pos_y * (cellSize + gap)) + 8 + 'px',
				left: (el.mesa_pos_x * (cellSize + gap)) + 8+  'px',
				transform: `rotate(${el.mesa_rotacion || 0}deg)`
			});
			//console.log(div)
			grid.appendChild(div);
			agregarListenersEdicion(div, el); // Permite editar al hacer doble clic

			<?php if ($modoValidacion !== 'validacion'): ?>
				// --- Drag & Drop con interact.js ---
				interact(div)
					.draggable({
						modifiers: [
							interact.modifiers.snap({
								targets: [interact.snappers.grid({
									x: cellSize + gap,
									y: cellSize + gap
								})]
							})
						],
						listeners: {
							// Mientras se arrastra, se muestra el área destino resaltada
							move(event) {
								const target = event.target;
								const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
								const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
								target.style.transform = `translate(${x}px, ${y}px) rotate(${target.dataset.rotation || '0'}deg)`;
								target.setAttribute('data-x', x);
								target.setAttribute('data-y', y);
								const col = Math.round((parseFloat(target.style.left) + x) / (cellSize + gap));
								const row = Math.round((parseFloat(target.style.top) + y) / (cellSize + gap));
								const id = parseInt(target.dataset.id);
								const el = elementos.find(e => e.mesa_id === id);
								const ancho = el.mesa_ancho || 1;
								const alto = el.mesa_alto || 1;
								resaltarCeldasFigura(col, row, ancho, alto);
								guardarCambios()
							},
							// Al soltar, valida colisiones y actualiza posición
							end(event) {
								const target = event.target;
								const x = parseFloat(target.style.left) + (parseFloat(target.getAttribute('data-x')) || 0);
								const y = parseFloat(target.style.top) + (parseFloat(target.getAttribute('data-y')) || 0);
								const col = Math.round(x / (cellSize + gap));
								const row = Math.round(y / (cellSize + gap));
								const id = parseInt(target.dataset.id);
								const el = elementos.find(e => e.mesa_id === id);
								limpiarResaltado();
								const nuevo = {
									...el,
									x: col,
									y: row
								};
								if (hayColision(nuevo, id)) {
									alert('¡Colisión detectada!');
									target.style.transform = `rotate(${target.dataset.rotation || '0'}deg)`;
									target.removeAttribute('data-x');
									target.removeAttribute('data-y');
									return;
								}
								// Actualiza la posición en el array de datos
								el.mesa_pos_x = col;
								el.mesa_pos_y = row;
								// Actualiza la posición visual
								target.style.left = (col * (cellSize + gap) + 8) + 'px';
								target.style.top = (row * (cellSize + gap) + 8) + 'px';
								target.style.transform = `rotate(${target.dataset.rotation || '0'}deg)`;
								target.removeAttribute('data-x');
								target.removeAttribute('data-y');
								// Limpia ocupación anterior y marca la nueva
								for (let i = 0; i < filas; i++) {
									for (let j = 0; j < columnas; j++) {
										if (occupancyMap[i][j] === id) occupancyMap[i][j] = null;
									}
								}
								marcarOcupado(el, id);
								guardarCambios()
							}
						}
					});
			<?php endif; ?>
			// 🔒 Resize desactivado por ahora
			/* ...resize code... */
		});
	}

	// --- Crear y editar mesa vía AJAX ---
	// Envía los datos al backend para crear una nueva mesa
	function crearMesaDesdeModal(mesaData) {
		// Validación en frontend: código y nombre únicos
		const existeCodigo = elementos.some(e => String(e.mesa_codigo || '').toLowerCase() === mesaData.mesa_codigo.toLowerCase());
		const existeNombre = elementos.some(e => String(e.mesa_nombre || '').toLowerCase() === mesaData.mesa_nombre.toLowerCase());
		if (existeCodigo) {
			alert('Ya existe una mesa con ese código.');
			return;
		}
		if (existeNombre) {
			alert('Ya existe una mesa con ese nombre.');
			return;
		}
		fetch('/administracion/ambientes/crearmesa', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(mesaData)
			})
			.then(res => res.json())
			.then(data => {
				if (data && data.error) {
					alert(data.error);
					return;
				}
				if (data && data.mesa_id) {
					// Agrega la nueva mesa al array local y refresca la vista
					elementos.push(data);
					// Limpiar variables globales después de crear
					modalMesaModo = 'crear';
					mesaEditandoId = null;
					cerrarModalMesa();
					renderizarTodo();
				} else {
					alert('Error al crear la mesa.');
				}
			})
			.catch(() => alert('Error de red al crear la mesa.'));
	}

	// Envía los datos al backend para editar una mesa existente
	function editarMesaDesdeModal(mesaData) {
		// Validación en frontend: código y nombre únicos (excepto la mesa que se edita)
		const existeCodigo = elementos.some(e => String(e.mesa_codigo || '').toLowerCase() === mesaData.mesa_codigo.toLowerCase() && e.mesa_id !== mesaData.mesa_id);
		const existeNombre = elementos.some(e => String(e.mesa_nombre || '').toLowerCase() === mesaData.mesa_nombre.toLowerCase() && e.mesa_id !== mesaData.mesa_id);
		if (existeCodigo) {
			alert('Ya existe una mesa con ese código.');
			return;
		}
		if (existeNombre) {
			alert('Ya existe una mesa con ese nombre.');
			return;
		}
		fetch('/administracion/ambientes/editarmesa', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(mesaData)
			})
			.then(res => res.json())
			.then(data => {
				if (data && data.error) {
					alert(data.error);
					return;
				}
				if (data && data.mesa_id) {
					// Actualiza la mesa en el array local y refresca la vista
					const idx = elementos.findIndex(e => e.mesa_id === data.mesa_id);
					if (idx !== -1) elementos[idx] = data;
					// Limpiar variables globales después de editar
					modalMesaModo = 'crear';
					mesaEditandoId = null;
					cerrarModalMesa();
					renderizarTodo();
				} else {
					alert('Error al editar la mesa.');
				}
			})
			.catch(() => alert('Error de red al editar la mesa.'));
	}

	// Envía los datos al backend para eliminar una mesa
	function eliminarMesaDesdeModal() {
		const mesa_id = mesaEditandoId;
		if (!mesa_id) return;
		if (!confirm('¿Estás seguro de que quieres eliminar esta mesa?')) return;
		fetch('/administracion/ambientes/eliminarmesa', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					mesa_id: mesa_id
				})
			})
			.then(res => res.json())
			.then(data => {
				console.log(data);

				if (data.error) {
					alert(data.error);
					cerrarModalMesa();
					return;
				}
				if (data.success) {
					alert('Mesa eliminada correctamente');

					try {
						elementos = elementos.filter(e => e.mesa_id !== mesa_id);
					} catch (error) {
						console.error('Error al filtrar elementos:', error);
					}

					//  elementos = elementos.filter(e => e.mesa_id !== mesa_id);
					cerrarModalMesa();
					renderizarTodo();
				}
			})
			.catch(() => alert('Error al eliminar la mesa.'));
	}


	// Limpia y vuelve a renderizar toda la grilla y los elementos
	function renderizarTodo() {
		grid.innerHTML = '';
		crearGrillaVisual();
		renderElementos();
	}

	// Envía todos los elementos actuales al backend para guardar cambios
	function guardarCambios() {
		fetch('/administracion/ambientes/guardarelementos', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(elementos)
			})
			.then(res => res.text())
			.then(data => {
				// Aquí se podría mostrar feedback visual de éxito
				//console.log(data);
			})
			.catch(err => alert('Error al guardar'));
	}

	renderizarTodo();

	<?php if ($soloMapa): ?>
	// Escala el grid para que quepa en el ancho disponible; nunca agranda (cap en 1)
	// La altura desbordante la maneja overflow-y:auto del contenedor padre
	(function() {
		function escalarGrid() {
			var wrapper = document.getElementById('grid-wrapper');
			var grid    = document.getElementById('grid');
			if (!wrapper || !grid) return;
			grid.style.transform = '';
			wrapper.style.height = '';
			var dispW = wrapper.clientWidth;
			var gridW = grid.scrollWidth;
			var gridH = grid.scrollHeight;
			if (gridW > 0 && dispW > 0) {
				var scale = Math.min(1, dispW / gridW) * 0.95;
				grid.style.width           = 'fit-content';
				grid.style.transformOrigin = 'top left';
				grid.style.transform       = 'scale(' + scale + ')';
				wrapper.style.height       = Math.ceil(gridH * scale) + 'px';
			}
		}
		setTimeout(escalarGrid, 80);
		window.addEventListener('resize', escalarGrid);
	})();
	<?php endif; ?>

	// ══════════════════════════════════════════
	//  LIBERAR / ASIGNAR MESA
	// ══════════════════════════════════════════

	function mostrarFormLiberacion(mesaId, reservaId) {
		const btn   = document.getElementById('btnLiberar_' + mesaId);
		const panel = document.getElementById('panelLiberacion_' + mesaId);
		if (!panel) return;

		if (panel.style.display !== 'none') { panel.style.display = 'none'; return; }

		panel.style.display = 'block';
		panel.innerHTML = '<div style="color:#94a3b8;padding:6px 0;font-size:.8rem;"><i class="fas fa-spinner fa-spin"></i> Cargando mesas disponibles...</div>';

		fetch('/administracion/ambientes/mesasDisponiblesParaReasignar?mesa_id=' + mesaId + '&reserva_id=' + reservaId)
		.then(function(r) { return r.json(); })
		.then(function(d) {
			if (d.error) {
				panel.innerHTML = '<div style="color:#ef4444;font-size:.8rem;padding:4px 0;"><i class="fas fa-exclamation-circle"></i> ' + d.error + '</div>';
				return;
			}

			const origen   = d.mesa_origen;
			const mesas    = d.mesas_disponibles;
			const rInfo    = d.reserva_info || {};
			const completa = rInfo.completa || false;

			// Mensaje contextual según completitud de invitados
			const accionTitulo = completa
				? '<i class="fas fa-user-plus"></i> Reserva completa — selecciona una mesa de mayor capacidad para agregar invitados'
				: '<i class="fas fa-compress-alt"></i> Invitados incompletos — selecciona una mesa de menor capacidad';
			const accionColor  = completa ? '#15803d' : '#b91c1c';
			const fondoColor   = completa ? '#f0fdf4' : '#fef2f2';
			const bordeColor   = completa ? '#86efac' : '#fca5a5';

			const infoInvitados = rInfo.total_personas
				? ' · ' + rInfo.invitados_count + ' / ' + rInfo.total_personas + ' invitados'
				: '';

			if (!mesas.length) {
				const sinMesaMsg = completa
					? 'No hay mesas disponibles con mayor capacidad en este ambiente.'
					: 'No hay mesas disponibles con menor o igual capacidad en este ambiente.';
				panel.innerHTML = '<div style="background:' + fondoColor + ';border:1px solid ' + bordeColor + ';border-radius:7px;padding:10px;font-size:.8rem;color:' + accionColor + ';">'
					+ '<i class="fas fa-exclamation-triangle"></i> ' + sinMesaMsg + '</div>';
				return;
			}

			function renderItem(m) {
				const diff     = m.mesa_capacidad - origen.mesa_capacidad;
				const diffTxt  = diff > 0 ? ' (+' + diff + ')' : (diff < 0 ? ' (' + diff + ')' : '');
				const diffCol  = diff > 0 ? '#15803d' : (diff < 0 ? '#b91c1c' : '#64748b');
				return '<div onclick="ejecutarLiberacion(' + mesaId + ',' + m.mesa_id + ',' + reservaId + ',\'' + origen.mesa_nombre.replace(/'/g,"\\'") + '\',\'' + m.mesa_nombre.replace(/'/g,"\\'") + '\',' + completa + ',' + (rInfo.invitados_count || 0) + ')"'
					+ ' style="padding:7px 9px;border:1px solid #e2e8f0;border-radius:6px;margin-bottom:4px;cursor:pointer;background:#fff;"'
					+ ' onmouseover="this.style.background=\'' + fondoColor + '\'" onmouseout="this.style.background=\'#fff\'">'
					+ '<strong style="font-size:.82rem;">' + m.mesa_nombre + '</strong>'
					+ '<span style="float:right;font-size:.72rem;font-weight:600;color:' + diffCol + ';">' + m.mesa_capacidad + ' pax' + diffTxt + '</span>'
					+ '</div>';
			}

			let h = '<div style="background:' + fondoColor + ';border:1px solid ' + bordeColor + ';border-radius:8px;padding:10px;">'
				+ '<div style="font-size:.72rem;font-weight:700;color:' + accionColor + ';text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">'
				+ accionTitulo + '</div>'
				+ '<div style="font-size:.76rem;color:#64748b;margin-bottom:8px;">Mesa actual: <strong>' + origen.mesa_nombre + '</strong> (' + origen.mesa_capacidad + ' pax)' + infoInvitados + ' · Reserva <strong>#' + reservaId + '</strong></div>'
				+ '<div style="max-height:220px;overflow-y:auto;">';

			mesas.forEach(function(m) { h += renderItem(m); });

			h += '</div></div>';
			panel.innerHTML = h;
		})
		.catch(function() {
			panel.innerHTML = '<div style="color:#ef4444;font-size:.8rem;">Error de red al cargar mesas.</div>';
		});
	}

	function ejecutarLiberacion(mesaOrigenId, mesaDestinoId, reservaId, nombreOrigen, nombreDestino, completa, invitadosCount) {
		let aviso = '¿Confirmar operación?\n\nReserva #' + reservaId + '\n  Desde: ' + nombreOrigen + '\n  Hacia:  ' + nombreDestino;
		if (completa) {
			aviso += '\n\nLa capacidad de la reserva se actualizará a la nueva mesa.';
		} else {
			aviso += '\n\n⚠ Si la nueva mesa tiene menor capacidad que los invitados actuales (' + invitadosCount + '), los invitados excedentes serán eliminados.';
		}
		aviso += '\n\nSe liberará la mesa original.';
		if (!confirm(aviso)) return;

		fetch('/administracion/ambientes/liberarMesa', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ mesa_id_origen: mesaOrigenId, mesa_id_destino: mesaDestinoId })
		})
		.then(function(r) { return r.json(); })
		.then(function(d) {
			if (d.error) { alert('Error: ' + d.error); return; }
			if (d.invitados_eliminados > 0) {
				alert('✓ ' + d.message);
			}
			window.location.reload();
		})
		.catch(function() { alert('Error de red al liberar la mesa.'); });
	}

	function mostrarFormAsignar(mesaId) {
		const panel = document.getElementById('formAsignar_' + mesaId);
		if (!panel) return;
		panel.style.display = 'block';
		panel.innerHTML = `
			<div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px;">
				<div style="font-size:.72rem;font-weight:700;color:#64748b;margin-bottom:6px;text-transform:uppercase;letter-spacing:.05em;">
					<i class="fas fa-search"></i> Buscar reserva
				</div>
				<input id="inputBuscarReserva_${mesaId}" type="text" placeholder="Nombre, CC o N° acción..."
					style="width:100%;padding:6px 9px;border:1px solid #cbd5e1;border-radius:6px;font-size:.8rem;box-sizing:border-box;margin-bottom:6px;"
					oninput="buscarReservasParaAsignar(${mesaId}, this.value)">
				<div id="listaReservas_${mesaId}" style="max-height:180px;overflow-y:auto;font-size:.78rem;"></div>
			</div>`;
		buscarReservasParaAsignar(mesaId, '');
	}

	function buscarReservasParaAsignar(mesaId, q) {
		const lista = document.getElementById('listaReservas_' + mesaId);
		if (!lista) return;
		lista.innerHTML = '<div style="color:#94a3b8;padding:4px 0;"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>';

		fetch('/administracion/ambientes/reservasParaAsignar?q=' + encodeURIComponent(q.trim()))
		.then(r => r.json())
		.then(function(reservas) {
			if (!reservas || !reservas.length) {
				lista.innerHTML = '<div style="color:#94a3b8;padding:4px 0;">Sin resultados.</div>';
				return;
			}
			const estadoTexto = {'1':'Creada','2':'Cargo acción','3':'Pago en línea','11':'Datáfono'};
			const estadoColor = {'1':'#94a3b8','2':'#22c55e','3':'#22c55e','11':'#22c55e'};
			lista.innerHTML = reservas.map(function(r) {
				const nombre = (r.reserva_nombre_cliente || '') + ' ' + (r.reserva_apellido_cliente || '');
				const est    = estadoTexto[r.reserva_estado] || 'Estado ' + r.reserva_estado;
				const estCol = estadoColor[r.reserva_estado] || '#94a3b8';
				return `<div onclick="confirmarAsignacion(${mesaId}, ${r.id}, '#${r.id} — ${nombre.trim()}')"
					style="padding:6px 8px;border:1px solid #e2e8f0;border-radius:6px;margin-bottom:4px;cursor:pointer;background:#fff;"
					onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='#fff'">
					<strong>#${r.id}</strong> — ${nombre.trim()}
					<br><span style="color:#64748b;font-size:.72rem;">CC: ${r.reserva_documento || '—'} · Acción: ${r.reserva_numero_carnet || '—'} · ${r.reserva_fecha || ''}</span>
					<span style="float:right;font-size:.7rem;color:${estCol};">${est}</span>
				</div>`;
			}).join('');
		})
		.catch(function() {
			lista.innerHTML = '<div style="color:#ef4444;padding:4px 0;">Error al buscar reservas.</div>';
		});
	}

	function confirmarAsignacion(mesaId, reservaId, textoReserva) {
		if (!confirm('¿Asignar esta mesa a la reserva:\n' + textoReserva + '?')) return;

		fetch('/administracion/ambientes/asignarMesa', {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ mesa_id: mesaId, reserva_id: reservaId })
		})
		.then(r => r.json())
		.then(function(d) {
			if (d.error) { alert('Error: ' + d.error); return; }
			window.location.reload();
		})
		.catch(function() { alert('Error de red al asignar la mesa.'); });
	}

	// Validación del campo descuento
	function validarDescuento(input) {
		let valor = input.value;

		// Permitir campo vacío
		if (valor === '') {
			input.setCustomValidity('');
			return;
		}

		// Convertir a número
		let numero = parseFloat(valor);

		// Validar que sea un número válido
		if (isNaN(numero)) {
			input.setCustomValidity('Debe ingresar un número válido');
			return;
		}

		// Validar rango 0-100
		if (numero < 0) {
			input.value = 0;
			numero = 0;
		} else if (numero > 100) {
			input.value = 100;
			numero = 100;
		}

		input.setCustomValidity('');
	}

	// Scroll sincronizado arriba y abajo para el grid
	document.addEventListener('DOMContentLoaded', function() {
		const grid = document.getElementById('grid');
		const scrollTopBar = document.getElementById('scroll-top-bar-grid');
		const scrollBottomBar = document.getElementById('scroll-bottom-bar-grid');

		if (grid && scrollTopBar && scrollBottomBar) {
			// Crear un div interior para la barra superior con el mismo ancho que el grid
			const innerDiv = document.createElement('div');
			innerDiv.style.width = grid.offsetWidth + 'px';
			innerDiv.style.height = '1px';
			scrollTopBar.appendChild(innerDiv);

			// Sincronizar scrolls
			scrollTopBar.addEventListener('scroll', function() {
				scrollBottomBar.scrollLeft = scrollTopBar.scrollLeft;
			});
			scrollBottomBar.addEventListener('scroll', function() {
				scrollTopBar.scrollLeft = scrollBottomBar.scrollLeft;
			});

			// Si el grid cambia de tamaño, ajustar el ancho del innerDiv
			const resizeObserver = new ResizeObserver(function() {
				innerDiv.style.width = grid.offsetWidth + 'px';
			});
			resizeObserver.observe(grid);
		}

		// Inicializar tooltip de Bootstrap 5 para el botón flotante
		if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
			const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
			tooltipTriggerList.forEach(function(tooltipTriggerEl) {
				new bootstrap.Tooltip(tooltipTriggerEl);
			});
		}

		// Agregar event listener al botón flotante
		const btnAgregarElemento = document.getElementById('btn-agregar-elemento');
		if (btnAgregarElemento) {
			btnAgregarElemento.addEventListener('click', function(e) {
				e.preventDefault();
				agregarElemento();
			});
		}
	});
</script>