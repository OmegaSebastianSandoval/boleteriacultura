<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<?php
	// echo "<pre>";
	// print_r($this->lists);
	// echo "</pre>";
	// Array de estados de reserva
	$estados_reserva = array(
		'1' => array('texto' => 'Reserva iniciada', 'badge_class' => 'text-bg-primary'),
		'2' => array('texto' => 'Cargo a la acción', 'badge_class' => 'text-bg-success'),
		'3' => array('texto' => 'Pago en línea', 'badge_class' => 'text-bg-success'),
		'4' => array('texto' => 'Reserva pago pendiente - PlaceToPay', 'badge_class' => 'text-bg-warning'),
		'5' => array('texto' => 'Reserva pago fallido - PlaceToPay', 'badge_class' => 'text-bg-danger'),
		'6' => array('texto' => 'Reserva pago rechazado - PlaceToPay', 'badge_class' => 'text-bg-danger'),
		'7' => array('texto' => 'Reserva pago pendiente - Sistema', 'badge_class' => 'text-bg-info'),
		'8' => array('texto' => 'Reserva cancelada', 'badge_class' => 'text-bg-secondary'),
		'C' => array('texto' => 'Reserva cancelada', 'badge_class' => 'text-bg-dark'),
		'11' => array('texto' => 'Pago por datáfono', 'badge_class' => 'text-bg-success'),
	);

	?>

	<form action="<?php echo $this->route; ?>" method="post">
		<div class="content-dashboard">
			<div class="row">
				<!-- <div class="col-3">
					<label>N&uacute;mero de acci&oacute;n</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="accion_sesion_accion_numero"
							value="<?php echo $this->getObjectVariable($this->filters, 'accion_sesion_accion_numero') ?>"></input>
					</label>
				</div> -->
				<div class="col-2">
					<label>Documento</label>
					<label class="input-group">
						<input type="text" class="form-control" name="accion_sesion_documento_socio"
							value="<?php echo $this->getObjectVariable($this->filters, 'accion_sesion_documento_socio') ?>"></input>
					</label>
				</div>
				<!-- <div class="col-3">
					<label>Fecha de inicio</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="accion_sesion_fecha_inicio"
							value="<?php echo $this->getObjectVariable($this->filters, 'accion_sesion_fecha_inicio') ?>"></input>
					</label>
				</div>
				<div class="col-3">
					<label>Fecha de fin</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-morado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="accion_sesion_fecha_fin"
							value="<?php echo $this->getObjectVariable($this->filters, 'accion_sesion_fecha_fin') ?>"></input>
					</label>
				</div> -->
				<div class="col-2">
					<label>Sesión activa</label>
					<label class="input-group">
						<!-- <input type="text" class="form-control" name="accion_sesion_sesion_activa"
							value="<?php echo $this->getObjectVariable($this->filters, 'accion_sesion_sesion_activa') ?>"></input> -->
						<select class="form-select" name="accion_sesion_sesion_activa">
							<option value="">-- Seleccione --</option>
							<option value="1" <?php if ($this->getObjectVariable($this->filters, 'accion_sesion_sesion_activa') === '1') {
																	echo 'selected';
																} ?>>Si</option>
							<option value="0" <?php if ($this->getObjectVariable($this->filters, 'accion_sesion_sesion_activa') === '0') {
																	echo 'selected';
																} ?>>No</option>
						</select>
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
		<div class="franja-paginas ">
			<div class="row">
				<div class="col-5">
					<div class="titulo-registro">Se encontraron <?php echo $this->register_number; ?> Registros</div>
				</div>
				<div class="col-2 text-end">
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
				<div class="col-3">
					<div class="text-end d-none"><a class="btn btn-sm btn-success"
							href="<?php echo $this->route . "\manage"; ?>"> <i class="fas fa-plus-square"></i> Crear Nuevo</a></div>
				</div>
			</div>
		</div>
		<div class="content-table">
			<table class=" table table-striped  table-hover table-administrator text-start table-sm">
				<thead>
					<tr>
						<td>Documento</td>
						<td>Estado</td>
						<td>Tiempo</td>
						<td>Reserva</td>
						<td width="180px" class="text-center">Acciones</td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->lists as $content) { ?>
						<?php $id = $content->accion_sesion_id; ?>
						<?php
						// Cálculo del tiempo transcurrido
						$ahora = new DateTime();
						$fecha_inicio = new DateTime($content->accion_sesion_fecha_inicio);

						if ($content->accion_sesion_sesion_activa == 1) {
							$diferencia = $fecha_inicio->diff($ahora);
							$badge_class = 'text-bg-warning';
							$tiempo_texto = '';
						} else {
							if ($content->accion_sesion_fecha_fin) {
								$fecha_fin = new DateTime($content->accion_sesion_fecha_fin);
								$diferencia = $fecha_inicio->diff($fecha_fin);
							} else {
								$diferencia = $fecha_inicio->diff($ahora);
							}
							$badge_class = 'text-bg-secondary';
							$tiempo_texto = '';
						}

						$tiempo = '';
						if ($diferencia->d > 0) {
							$tiempo .= $diferencia->d . 'd ';
						}
						if ($diferencia->h > 0) {
							$tiempo .= $diferencia->h . 'h ';
						}
						if ($diferencia->i > 0 || $diferencia->h > 0) {
							$tiempo .= $diferencia->i . 'm';
						}
						if (empty($tiempo)) {
							$tiempo = '< 1m';
						}
						?>
						<tr>
							<td><strong><?= $content->accion_sesion_documento_socio; ?></strong></td>
							<td>
								<span class="badge <?= $content->accion_sesion_sesion_activa == 1 ? 'text-bg-success' : 'text-bg-danger'; ?>"><?= $content->accion_sesion_sesion_activa == 1 ? 'Activa' : 'Inactiva'; ?></span>
							</td>
							<td>
								<span class="badge <?= $badge_class; ?>"><?= $tiempo_texto . $tiempo; ?></span>
							</td>
							<td>
								<?php if ($content->reserva) { ?>
									<span class="badge text-bg-primary">#<?= $content->reserva->id; ?></span>
								<?php } else { ?>
									<span class="badge text-bg-light text-dark">Sin reserva</span>
								<?php } ?>
							</td>
							<td class="text-center">
								<div class="d-flex gap-1 justify-content-center flex-wrap">
									<button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetalle<?= $id ?>" title="Ver detalles">
										<i class="fas fa-eye"></i>
									</button>
									<?php if ($content->accion_sesion_sesion_activa == 1) { ?>
										<button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalInactivar<?= $id ?>" title="Finalizar sesión">
											<i class="fas fa-stop"></i>
										</button>
										<?php if ($content->reserva) { ?>
											<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalInactivarReserva<?= $id ?>" title="Finalizar sesión y reserva">
												<i class="fas fa-times-circle"></i>
											</button>
										<?php } ?>
									<?php } ?>
								</div>
							</td>
						</tr>
					<?php } ?>

				</tbody>
			</table>
		</div>

		<!-- Modales -->
		<?php foreach ($this->lists as $content) { ?>
			<?php $id = $content->accion_sesion_id; ?>

			<!-- Modal de Detalles -->
			<div class="modal fade" id="modalDetalle<?= $id ?>" tabindex="-1" aria-labelledby="modalDetalleLabel<?= $id ?>" aria-hidden="true">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="modalDetalleLabel<?= $id ?>">
								<i class="fas fa-info-circle"></i> Detalles de la Sesión - <?= $content->accion_sesion_documento_socio; ?>
							</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-6">
									<h6 class="text-primary"><i class="fas fa-user"></i> Información de Sesión</h6>
									<table class="table table-sm table-bordered">
										<tr>
											<td><strong>ID Sesión:</strong></td>
											<td><?= $content->accion_sesion_id; ?></td>
										</tr>
										<tr>
											<td><strong>Documento:</strong></td>
											<td><?= $content->accion_sesion_documento_socio; ?></td>
										</tr>
										<tr>
											<td><strong>N° Acción:</strong></td>
											<td><?= $content->accion_sesion_accion_numero; ?></td>
										</tr>
										<tr>
											<td><strong>Estado:</strong></td>
											<td>
												<span class="badge <?= $content->accion_sesion_sesion_activa == 1 ? 'text-bg-success' : 'text-bg-danger'; ?>">
													<?= $content->accion_sesion_sesion_activa == 1 ? 'Activa' : 'Inactiva'; ?>
												</span>
											</td>
										</tr>
										<tr>
											<td><strong>Fecha Inicio:</strong></td>
											<td><?= $content->accion_sesion_fecha_inicio; ?></td>
										</tr>
										<tr>
											<td><strong>Fecha Fin:</strong></td>
											<td><?= $content->accion_sesion_fecha_fin ?? 'N/A'; ?></td>
										</tr>
										<tr>
											<td><strong>Último Ping:</strong></td>
											<td><?= $content->accion_sesion_last_ping; ?></td>
										</tr>
										<tr>
											<td><strong>IP Usuario:</strong></td>
											<td><?= $content->accion_sesion_ip_usuario; ?></td>
										</tr>
										<tr>
											<td><strong>User Agent:</strong></td>
											<td><small><?= substr($content->accion_sesion_user_agent, 0, 50); ?>...</small></td>
										</tr>
										<tr>
											<td><strong>Dispositivo:</strong></td>
											<td><small><?= $content->accion_sesion_dispositivo ?></small></td>
										</tr>
									</table>
								</div>
								<div class="col-md-6">
									<?php if (isset($content->cola_compra) && $content->cola_compra) { ?>
										<h6 class="text-success"><i class="fas fa-shopping-cart"></i> Cola de Compras</h6>
										<table class="table table-sm table-bordered">
											<tr>
												<td><strong>ID Cola:</strong></td>
												<td><?= $content->cola_compra->cola_compras_id; ?></td>
											</tr>
											<tr>
												<td><strong>Estado:</strong></td>
												<td>
													<span class="badge <?= $content->cola_compra->cola_compras_estado == 'activo' ? 'text-bg-success' : 'text-bg-danger'; ?>">
														<?= $content->cola_compra->cola_compras_estado; ?>
													</span>
												</td>
											</tr>
											<tr>
												<td><strong>Creado:</strong></td>
												<td><?= $content->cola_compra->cola_compras_creado_el; ?></td>
											</tr>
											<tr>
												<td><strong>Inicio:</strong></td>
												<td><?= $content->cola_compra->cola_compras_inicio_el; ?></td>
											</tr>
											<tr>
												<td><strong>Vence:</strong></td>
												<td><?= $content->cola_compra->cola_compras_vence_el; ?></td>
											</tr>
										</table>
									<?php } ?>

									<?php if (isset($content->reserva) && $content->reserva) { ?>
										<?php

										$estado_actual = $content->reserva->reserva_estado;
										$estado_info = isset($estados_reserva[$estado_actual]) ? $estados_reserva[$estado_actual] : array('texto' => 'Estado desconocido', 'badge_class' => 'text-bg-secondary');
										?>
										<h6 class="text-primary mt-3"><i class="fas fa-calendar-check"></i> Reserva</h6>
										<table class="table table-sm table-bordered">
											<tr>
												<td><strong>ID Reserva:</strong></td>
												<td><?= $content->reserva->id; ?></td>
											</tr>
											<tr>
												<td><strong>Cliente:</strong></td>
												<td><?= $content->reserva->reserva_nombre_cliente . ' ' . $content->reserva->reserva_apellido_cliente; ?></td>
											</tr>
											<tr>
												<td><strong>Email:</strong></td>
												<td><?= $content->reserva->reserva_correo; ?></td>
											</tr>
											<tr>
												<td><strong>Teléfono:</strong></td>
												<td><?= $content->reserva->reserva_telefono; ?></td>
											</tr>
											<tr>
												<td><strong>Total a Pagar:</strong></td>
												<td><strong>$<?= number_format($content->reserva->reserva_total_pagar, 2); ?></strong></td>
											</tr>
											<tr>
												<td><strong>Estado:</strong></td>
												<td>
													<span class="badge <?= $estado_info['badge_class']; ?>">
														<?= $estado_info['texto']; ?>
													</span>
												</td>
											</tr>
											<tr>
												<td><strong>Fecha Creación:</strong></td>
												<td><?= $content->reserva->reserva_fecha_creacion; ?></td>
											</tr>
											<tr>
												<td><strong>Límite Pago:</strong></td>
												<td><?= $content->reserva->reserva_fecha_limite_pago; ?></td>
											</tr>
										</table>
									<?php } else { ?>
										<div class="alert alert-info mt-3">
											<i class="fas fa-info-circle"></i> No hay reserva asociada
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<?php if ($content->accion_sesion_sesion_activa == 1) { ?>
								<button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalInactivar<?= $id ?>" data-bs-dismiss="modal">
									<i class="fas fa-stop"></i> Finalizar sesión
								</button>
								<?php if ($content->reserva) { ?>
									<button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalInactivarReserva<?= $id ?>" data-bs-dismiss="modal">
										<i class="fas fa-times-circle"></i> Finalizar sesión y reserva
									</button>
								<?php } ?>
							<?php } ?>
							<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
						</div>
					</div>
				</div>
			</div>

			<!-- Modal para Inactivar -->
			<div class="modal fade text-start" id="modalInactivar<?= $id ?>" tabindex="-1" aria-labelledby="modalInactivarLabel<?= $id ?>" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-danger text-white">
							<h5 class="modal-title" id="modalInactivarLabel<?= $id ?>">
								<i class="fas fa-exclamation-triangle"></i> Finalizar Sesión
							</h5>
							<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<p>¿Está seguro de finalizar esta sesión?</p>
							<div class="alert alert-warning">
								<strong>Documento:</strong> <?= $content->accion_sesion_documento_socio; ?><br>
								<?php if (isset($content->cola_compra) && $content->cola_compra) { ?>
									<strong>Cola de compras:</strong> <?= $content->cola_compra->cola_compras_estado; ?>
								<?php } ?>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
							<a class="btn btn-danger btn-sm" href="<?php echo $this->route; ?>/inactivar?id=<?= $id ?><?php if (isset($content->cola_compra) && $content->cola_compra) echo '&coldaid=' . $content->cola_compra->cola_compras_id; ?>&csrf=<?= $this->csrf; ?>">
								<i class="fas fa-stop"></i> Finalizar
							</a>
						</div>
					</div>
				</div>
			</div>

			<!-- Modal para Inactivar y finalizar reserva -->
			<?php if ($content->reserva) { ?>
				<div class="modal fade text-start" id="modalInactivarReserva<?= $id ?>" tabindex="-1" aria-labelledby="modalInactivarReservaLabel<?= $id ?>" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-warning">
								<h5 class="modal-title" id="modalInactivarReservaLabel<?= $id ?>">
									<i class="fas fa-exclamation-circle"></i> Finalizar sesión y reserva
								</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<p>¿Está seguro de finalizar esta sesión y cancelar la reserva asociada?</p>
								<div class="alert alert-danger">
									<strong>Documento:</strong> <?= $content->accion_sesion_documento_socio; ?><br>
									<strong>Reserva ID:</strong> <?= $content->reserva->id; ?><br>
									<strong>Cliente:</strong> <?= $content->reserva->reserva_nombre_cliente . ' ' . $content->reserva->reserva_apellido_cliente; ?>
								</div>
								<p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
								<a class="btn btn-danger btn-sm" href="<?php echo $this->route; ?>/inactivar?id=<?= $id ?><?php if (isset($content->cola_compra) && $content->cola_compra) echo '&coldaid=' . $content->cola_compra->cola_compras_id; ?>&csrf=<?= $this->csrf; ?>&reservaid=<?= $content->reserva->id ?>">
									<i class="fas fa-times-circle"></i> Finalizar Todo
								</a>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		<?php } ?>

		<input type="hidden" id="csrf" value="<?php echo $this->csrf ?>"><input type="hidden" id="page-route"
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
	.info {
		text-transform: capitalize;
		font-weight: 600;
	}

	.table-sm thead td {
		font-size: 13px;
		font-weight: 600;
		background-color: #f8f9fa;
	}

	.table-sm tbody td {
		font-size: 12px;
		vertical-align: middle;
		padding: 0.5rem;
	}

	.btn-group-sm .btn {
		padding: 0.25rem 0.5rem;
		font-size: 0.75rem;
	}

	.badge {
		font-size: 0.7rem;
		padding: 0.35em 0.5em;
	}

	.modal-body table tr td:first-child {
		width: 40%;
		background-color: #f8f9fa;
	}

	/* Estilos para los botones de acción */
	.d-flex.gap-1 .btn {
		padding: 0.25rem 0.4rem;
		font-size: 0.7rem;
		white-space: nowrap;
	}

	.d-flex.gap-1 {
		gap: 0.25rem !important;
	}
</style>