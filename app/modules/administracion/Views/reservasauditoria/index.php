<h1 class="titulo-principal"><i class="fas fa-search"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">

	<!-- ══ FILTROS (sin cambios) ══ -->
	<form action="<?php echo $this->route; ?>" method="post">
		<div class="content-dashboard">
			<div class="row">
				<div class="col-2">
					<label>ID Reserva</label>
					<div class="input-group">
						<input type="text" class="form-control" name="reserva_id"
							value="<?php echo $this->getObjectVariable($this->filters, 'reserva_id') ?>" placeholder="Ej: 123">
					</div>
				</div>
				<div class="col-2">
					<label>Carnet</label>
					<div class="input-group">
						<input type="text" class="form-control" name="numero_carnet"
							value="<?php echo $this->getObjectVariable($this->filters, 'numero_carnet') ?>"
							placeholder="Numero de carnet">
					</div>
				</div>
				<div class="col-2">
					<label>Documento</label>
					<div class="input-group">
						<input type="text" class="form-control" name="documento_socio"
							value="<?php echo $this->getObjectVariable($this->filters, 'documento_socio') ?>"
							placeholder="Documento socio">
					</div>
				</div>
				<div class="col-2">
					<label>Accion</label>
					<div class="input-group">
						<select class="form-select" name="accion">
							<option value="">Todas las acciones</option>
							<option value="INICIO_CONTROLADOR" <?php echo $this->getObjectVariable($this->filters, 'accion') == 'INICIO_CONTROLADOR' ? 'selected' : ''; ?>>Inicio Controlador</option>
							<option value="RESERVA_CREADA" <?php echo $this->getObjectVariable($this->filters, 'accion') == 'RESERVA_CREADA' ? 'selected' : ''; ?>>Reserva Creada</option>
							<option value="RESERVA_ELIMINADA" <?php echo $this->getObjectVariable($this->filters, 'accion') == 'RESERVA_ELIMINADA' ? 'selected' : ''; ?>>Reserva Eliminada</option>
							<option value="MESA_CONFIRMADA" <?php echo $this->getObjectVariable($this->filters, 'accion') == 'MESA_CONFIRMADA' ? 'selected' : ''; ?>>Mesa Confirmada</option>
							<option value="INVITADO_AGREGADO" <?php echo $this->getObjectVariable($this->filters, 'accion') == 'INVITADO_AGREGADO' ? 'selected' : ''; ?>>Invitado Agregado</option>
							<option value="PAGO_PROCESADO" <?php echo $this->getObjectVariable($this->filters, 'accion') == 'PAGO_PROCESADO' ? 'selected' : ''; ?>>Pago Procesado</option>
							<option value="LIMPIEZA_RESERVA" <?php echo $this->getObjectVariable($this->filters, 'accion') == 'LIMPIEZA_RESERVA' ? 'selected' : ''; ?>>Limpieza Reserva</option>
						</select>
					</div>
				</div>
				<div class="col-2">
					<label>Fecha Desde</label>
					<div class="input-group">
						<input type="date" class="form-control" name="fecha_desde"
							value="<?php echo $this->getObjectVariable($this->filters, 'fecha_desde') ?>">
					</div>
				</div>
				<div class="col-2">
					<label>Fecha Hasta</label>
					<div class="input-group">
						<input type="date" class="form-control" name="fecha_hasta"
							value="<?php echo $this->getObjectVariable($this->filters, 'fecha_hasta') ?>">
					</div>
				</div>
				<div class="col-12 mt-3">
					<div class="row">
						<div class="col-6">
							<button type="submit" class="btn btn-block btn-azul"><i class="fas fa-filter"></i> Filtrar</button>
						</div>
						<div class="col-6">
							<a class="btn btn-block btn-azul-claro" href="<?php echo $this->route; ?>?cleanfilter=1"><i
									class="fas fa-eraser"></i> Limpiar</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>

	<!-- ══ PAGINACIÓN SUPERIOR ══ -->
	<div align="center">
		<ul class="pagination justify-content-center">
			<?php
			$url = $this->route;
			$min = $this->page - 10;
			if ($min < 0) $min = 1;
			$max = $this->page + 10;
			if ($this->totalpages > 1) {
				if ($this->page != 1)
					echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page - 1) . '"> &laquo; Anterior </a></li>';
				for ($i = 1; $i <= $this->totalpages; $i++) {
					if ($this->page == $i)
						echo '<li class="active page-item"><a class="page-link">' . $this->page . '</a></li>';
					else {
						if ($i <= $max && $i >= $min)
							echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>  ';
					}
				}
				if ($this->page != $this->totalpages)
					echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page + 1) . '">Siguiente &raquo;</a></li>';
			}
			?>
		</ul>
	</div>

	<!-- ══ TABLA ══ -->
	<div class="content-dashboard">

		<!-- Franja superior -->
		<div class="franja-paginas">
			<div class="row">
				<div class="col-5">
					<div class="titulo-registro">Se encontraron <?php echo $this->register_number; ?> Registros</div>
				</div>
				<div class="col-3 text-end">
					<div class="texto-paginas">Registros por pagina:</div>
				</div>
				<div class="col-1">
					<select class="form-control form-control-sm selectpagination">
						<option value="20" <?php if ($this->pages == 20) echo 'selected'; ?>>20</option>
						<option value="30" <?php if ($this->pages == 30) echo 'selected'; ?>>30</option>
						<option value="50" <?php if ($this->pages == 50) echo 'selected'; ?>>50</option>
						<option value="100" <?php if ($this->pages == 100) echo 'selected'; ?>>100</option>
					</select>
				</div>
				<div class="col-3">
					<div class="text-end">
						<button type="button" class="btn btn-sm btn-info d-none" onclick="exportarExcel()">
							<i class="fas fa-download"></i> Exportar Excel
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Tabla -->
		<div class="content-table table-responsive">
			<table class="table table-striped table-hover table-administrator text-start">
				<thead>
					<tr>
						<td width="60">ID</td>
						<td width="75">Reserva</td>
						<td width="100">Carnet</td>
						<td width="120">Documento</td>
						<td width="200">Accion</td>
						<td width="120">Fecha/Hora</td>
						<td width="100">IP</td>
						<td width="150">Estados</td>
						<td width="100">Detalles</td>
					</tr>
				</thead>
				<tbody>
					<?php
					$modales = [];
					foreach ($this->lists as $content) {
						$id = $content->id;

						$datosJson        = json_decode($content->datos_json,       true);
						$sessionData      = json_decode($content->session_data,      true);
						$parametrosGet    = json_decode($content->parametros_get,    true);
						$parametrosPost   = json_decode($content->parametros_post,   true);
						$invitadosAntes   = json_decode($content->invitados_antes,   true);
						$invitadosDespues = json_decode($content->invitados_despues, true);

						// Color de fila
						$rowClass = '';
						if (strpos($content->accion, 'ERROR') !== false)
							$rowClass = 'table-danger';
						elseif (strpos($content->accion, 'ELIMINAD') !== false || strpos($content->accion, 'LIMPIE') !== false)
							$rowClass = 'table-warning';
						elseif (strpos($content->accion, 'CREADA') !== false || strpos($content->accion, 'CONFIRMAD') !== false)
							$rowClass = 'table-success';
						elseif (strpos($content->accion, 'INICIO') !== false)
							$rowClass = 'table-info';

						// Icono de acción
						$iconoAccion = 'fas fa-circle';
						if (strpos($content->accion, 'ERROR') !== false)
							$iconoAccion = 'fas fa-exclamation-triangle text-danger';
						elseif (strpos($content->accion, 'ELIMINAD') !== false)
							$iconoAccion = 'fas fa-trash text-warning';
						elseif (strpos($content->accion, 'CREADA') !== false)
							$iconoAccion = 'fas fa-plus-circle text-success';
						elseif (strpos($content->accion, 'MESA') !== false)
							$iconoAccion = 'fas fa-chair text-primary';
						elseif (strpos($content->accion, 'INVITADO') !== false)
							$iconoAccion = 'fas fa-user-plus text-info';
						elseif (strpos($content->accion, 'PAGO') !== false)
							$iconoAccion = 'fas fa-credit-card text-success';
						elseif (strpos($content->accion, 'INICIO') !== false)
							$iconoAccion = 'fas fa-play-circle text-primary';

						// ── MODAL ──────────────────────────────────────────────────────
						ob_start();
						?>
						<div class="modal fade text-start" id="modalDetalles<?php echo $id; ?>" tabindex="-1" aria-hidden="true">
							<div class="modal-dialog modal-xl" style="max-width:92%;width:92%;">
								<div class="modal-content">

									<div class="modal-header" style="background:var(--surface);border-bottom:1px solid var(--border);padding:14px 20px;">
										<h4 class="modal-title" style="font-size:.95rem;font-weight:600;display:flex;align-items:center;gap:8px;margin:0;">
											<i class="fas fa-clipboard-list" style="color:var(--brand-green);"></i>
											Log #<?php echo $id; ?> &mdash; <?php echo htmlentities(str_replace('_', ' ', $content->accion)); ?>
										</h4>
										<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
									</div>

									<div class="modal-body" style="padding:16px;background:var(--bg);">
										<div class="row g-3">

											<!-- Info General -->
											<div class="col-md-6">
												<div class="div-dashboard" style="margin-top:0;height:100%;">
													<h2><i class="fas fa-info-circle"></i> Información General</h2>
													<div class="pading-dashboard" style="height:auto;">
														<table class="table table-sm table-borderless mb-0" style="font-size:.83rem;">
															<tr>
																<td class="aud-label">ID</td>
																<td><strong><?php echo htmlentities($content->id); ?></strong></td>
															</tr>
															<tr>
																<td class="aud-label">Reserva ID</td>
																<td><?php echo $content->reserva_id ? '<span class="badge bg-primary">' . htmlentities($content->reserva_id) . '</span>' : '<span class="text-muted">N/A</span>'; ?></td>
															</tr>
															<tr>
																<td class="aud-label">Carnet</td>
																<td><?php echo htmlentities($content->numero_carnet ?: 'N/A'); ?></td>
															</tr>
															<tr>
																<td class="aud-label">Documento</td>
																<td><?php echo htmlentities($content->documento_socio ?: 'N/A'); ?></td>
															</tr>
															<tr>
																<td class="aud-label">Controlador</td>
																<td><small><?php echo htmlentities($content->controlador); ?></small></td>
															</tr>
															<tr>
																<td class="aud-label">Método</td>
																<td><small><?php echo htmlentities($content->metodo); ?></small></td>
															</tr>
															<tr>
																<td class="aud-label">IP</td>
																<td><small class="text-muted"><?php echo htmlentities($content->ip_address ?: 'N/A'); ?></small></td>
															</tr>
															<tr>
																<td class="aud-label">URL</td>
																<td><small class="text-muted" style="word-break:break-all;"><?php echo htmlentities($content->url_completa ?: 'N/A'); ?></small></td>
															</tr>
														</table>
													</div>
												</div>
											</div>

											<!-- Estados y Cambios -->
											<div class="col-md-6">
												<div class="div-dashboard" style="margin-top:0;height:100%;">
													<h2><i class="fas fa-exchange-alt"></i> Estados y Cambios</h2>
													<div class="pading-dashboard" style="height:auto;">
														<table class="table table-sm table-borderless mb-0" style="font-size:.83rem;">
															<tr>
																<td class="aud-label">Estado anterior</td>
																<td><?php echo htmlentities($content->estado_anterior ?: 'N/A'); ?></td>
															</tr>
															<tr>
																<td class="aud-label">Estado nuevo</td>
																<td><?php echo htmlentities($content->estado_nuevo ?: 'N/A'); ?></td>
															</tr>
															<tr>
																<td class="aud-label">Mesa anterior</td>
																<td><?php echo htmlentities($content->mesa_id_anterior ?: 'N/A'); ?></td>
															</tr>
															<tr>
																<td class="aud-label">Mesa nueva</td>
																<td><?php echo htmlentities($content->mesa_id_nuevo ?: 'N/A'); ?></td>
															</tr>
															<tr>
																<td class="aud-label">Observaciones</td>
																<td><?php echo htmlentities($content->observaciones ?: 'N/A'); ?></td>
															</tr>
															<tr>
																<td class="aud-label">User Agent</td>
																<td><small class="text-muted"><?php echo htmlentities(substr($content->user_agent ?: 'N/A', 0, 60)); ?></small></td>
															</tr>
														</table>
													</div>
												</div>
											</div>

										</div><!-- /row -->

										<?php if ($datosJson): ?>
										<div class="div-dashboard" style="margin-top:12px;">
											<h2><i class="fas fa-code"></i> Datos JSON</h2>
											<div class="pading-dashboard" style="height:auto;">
												<pre class="aud-json"><?php echo htmlentities(json_encode($datosJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
											</div>
										</div>
										<?php endif; ?>

										<?php if ($invitadosAntes || $invitadosDespues): ?>
										<div class="row g-3" style="margin-top:4px;">
											<?php if ($invitadosAntes): ?>
											<div class="col-md-6">
												<div class="div-dashboard" style="margin-top:0;">
													<h2><i class="fas fa-users"></i> Invitados — Antes</h2>
													<div class="pading-dashboard" style="height:auto;">
														<pre class="aud-json"><?php echo htmlentities(json_encode($invitadosAntes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
													</div>
												</div>
											</div>
											<?php endif; ?>
											<?php if ($invitadosDespues): ?>
											<div class="col-md-6">
												<div class="div-dashboard" style="margin-top:0;">
													<h2><i class="fas fa-users"></i> Invitados — Después</h2>
													<div class="pading-dashboard" style="height:auto;">
														<pre class="aud-json"><?php echo htmlentities(json_encode($invitadosDespues, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
													</div>
												</div>
											</div>
											<?php endif; ?>
										</div>
										<?php endif; ?>

										<?php if (($parametrosGet && !empty($parametrosGet)) || ($parametrosPost && !empty($parametrosPost))): ?>
										<div class="row g-3" style="margin-top:4px;">
											<?php if ($parametrosGet && !empty($parametrosGet)): ?>
											<div class="col-md-6">
												<div class="div-dashboard" style="margin-top:0;">
													<h2><i class="fas fa-link"></i> Parámetros GET</h2>
													<div class="pading-dashboard" style="height:auto;">
														<pre class="aud-json"><?php echo htmlentities(json_encode($parametrosGet, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
													</div>
												</div>
											</div>
											<?php endif; ?>
											<?php if ($parametrosPost && !empty($parametrosPost)): ?>
											<div class="col-md-6">
												<div class="div-dashboard" style="margin-top:0;">
													<h2><i class="fas fa-paper-plane"></i> Parámetros POST</h2>
													<div class="pading-dashboard" style="height:auto;">
														<pre class="aud-json"><?php echo htmlentities(json_encode($parametrosPost, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
													</div>
												</div>
											</div>
											<?php endif; ?>
										</div>
										<?php endif; ?>

										<?php if ($sessionData): ?>
										<div class="div-dashboard" style="margin-top:12px;">
											<h2><i class="fas fa-database"></i> Datos de Sesión</h2>
											<div class="pading-dashboard" style="height:auto;">
												<pre class="aud-json"><?php echo htmlentities(json_encode($sessionData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
											</div>
										</div>
										<?php endif; ?>

									</div><!-- /modal-body -->

									<div class="modal-footer" style="border-top:1px solid var(--border);padding:10px 16px;">
										<button type="button" class="btn btn-azul btn-sm" data-bs-dismiss="modal">
											<i class="fas fa-times"></i> Cerrar
										</button>
									</div>

								</div>
							</div>
						</div>
						<?php
						$modales[] = ob_get_clean();
						?>

						<tr class="<?php echo $rowClass; ?>">
							<td><strong><?php echo htmlentities($content->id); ?></strong></td>
							<td>
								<?php if ($content->reserva_id): ?>
									<span class="badge bg-primary"><?php echo htmlentities($content->reserva_id); ?></span>
								<?php else: ?>
									<span class="text-muted">-</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ($content->numero_carnet): ?>
									<span class="badge bg-secondary"><?php echo htmlentities($content->numero_carnet); ?></span>
								<?php else: ?>
									<span class="text-muted">-</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ($content->documento_socio): ?>
									<strong><?php echo htmlentities($content->documento_socio); ?></strong>
								<?php else: ?>
									<span class="text-muted">-</span>
								<?php endif; ?>
							</td>
							<td>
								<i class="<?php echo $iconoAccion; ?>"></i>
								<small><?php echo htmlentities(str_replace('_', ' ', $content->accion)); ?></small>
							</td>
							<td>
								<small>
									<?php echo date('d/m/Y', strtotime($content->fecha_creacion)); ?><br>
									<?php echo date('H:i:s', strtotime($content->fecha_creacion)); ?>
								</small>
							</td>
							<td>
								<small class="text-muted"><?php echo htmlentities($content->ip_address ?: '-'); ?></small>
							</td>
							<td>
								<?php if ($content->estado_anterior || $content->estado_nuevo): ?>
									<small>
										<?php if ($content->estado_anterior): ?>
											<span class="badge bg-light text-dark"><?php echo htmlentities($content->estado_anterior); ?></span>
										<?php endif; ?>
										<?php if ($content->estado_anterior && $content->estado_nuevo): ?>
											<i class="fas fa-arrow-right text-muted"></i>
										<?php endif; ?>
										<?php if ($content->estado_nuevo): ?>
											<span class="badge bg-info"><?php echo htmlentities($content->estado_nuevo); ?></span>
										<?php endif; ?>
									</small>
								<?php else: ?>
									<span class="text-muted">-</span>
								<?php endif; ?>
							</td>
							<td class="text-center">
								<button type="button" class="btn btn-azul btn-sm" data-bs-toggle="modal"
									data-bs-target="#modalDetalles<?php echo $id; ?>" title="Ver detalles completos">
									<i class="fas fa-eye"></i>
								</button>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

		<!-- Franja inferior -->
		<div class="franja-paginas" style="border-top:1px solid #CCCCCC;margin-top:2%;">
			<div class="row" style="padding-top:1%;">
				<div class="col-5">
					<div class="titulo-registro">Se encontraron <?php echo $this->register_number; ?> Registros</div>
				</div>
				<div class="col-3 text-end">
					<div class="texto-paginas">Registros por pagina:</div>
				</div>
				<div class="col-1">
					<select class="form-control form-control-sm selectpagination">
						<option value="20" <?php if ($this->pages == 20) echo 'selected'; ?>>20</option>
						<option value="30" <?php if ($this->pages == 30) echo 'selected'; ?>>30</option>
						<option value="50" <?php if ($this->pages == 50) echo 'selected'; ?>>50</option>
						<option value="100" <?php if ($this->pages == 100) echo 'selected'; ?>>100</option>
					</select>
				</div>
				<div class="col-3"></div>
			</div>
		</div>

		<input type="hidden" id="csrf" value="<?php echo $this->csrf ?>">
		<input type="hidden" id="page-route" value="<?php echo $this->route; ?>/changepage">
	</div>

	<!-- ══ PAGINACIÓN INFERIOR ══ -->
	<div align="center">
		<ul class="pagination justify-content-center">
			<?php
			$url = $this->route;
			$min = $this->page - 10;
			if ($min < 0) $min = 1;
			$max = $this->page + 10;
			if ($this->totalpages > 1) {
				if ($this->page != 1)
					echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page - 1) . '"> &laquo; Anterior </a></li>';
				for ($i = 1; $i <= $this->totalpages; $i++) {
					if ($this->page == $i)
						echo '<li class="active page-item"><a class="page-link">' . $this->page . '</a></li>';
					else {
						if ($i <= $max && $i >= $min)
							echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>  ';
					}
				}
				if ($this->page != $this->totalpages)
					echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page + 1) . '">Siguiente &raquo;</a></li>';
			}
			?>
		</ul>
	</div>

</div>

<!-- ══ MODALES ══ -->
<?php echo implode("\n", $modales); ?>

<style>
	.aud-label {
		color: var(--text-muted, #94a3b8);
		font-size: .8rem;
		width: 130px;
		white-space: nowrap;
		padding: 4px 8px 4px 0;
		vertical-align: top;
	}

	.aud-json {
		background: var(--bg, #f8f9fa);
		border: 1px solid var(--border, #e2e8f0);
		border-radius: 6px;
		padding: .75rem 1rem;
		font-size: .78rem;
		max-height: 280px;
		overflow-y: auto;
		white-space: pre-wrap;
		word-break: break-word;
		font-family: 'Courier New', monospace;
		color: var(--text-primary, #2d3748);
		margin: 0;
	}

	@media (max-width: 768px) {
		.aud-json { font-size: .72rem; max-height: 180px; }
	}
</style>

<script>
	document.addEventListener("DOMContentLoaded", function () {
		const selectPagination = document.querySelectorAll('.selectpagination');
		selectPagination.forEach(function (sel) {
			sel.addEventListener('change', function () {
				const pages = this.value;
				const csrf  = document.getElementById('csrf').value;
				const route = document.getElementById('page-route').value;

				if (typeof fetch !== 'undefined') {
					fetch(route, {
						method: 'POST',
						headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
						body: `pages=${pages}&csrf=${csrf}`
					}).then(() => location.reload()).catch(() => location.reload());
				} else if (typeof $ !== 'undefined') {
					$.ajax({
						type: 'POST', url: route,
						data: 'pages=' + pages + '&csrf=' + csrf,
						success: function () { location.reload(); },
						error:   function () { location.reload(); }
					});
				} else {
					location.reload();
				}
			});
		});
	});

	function exportarExcel() {
		alert('Funcion de exportacion en desarrollo');
	}
</script>
