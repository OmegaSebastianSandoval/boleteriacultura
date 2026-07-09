<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form action="<?php echo $this->route; ?>" method="post">
		<div class="content-dashboard">
			<div class="row">
				<div class="col-2">
					<label>Reserva ID</label>
					<label class="input-group">
						<select class="form-control" name="reserva_id_reserva">
							<option value="">Todas</option>
							<?php foreach ($this->list_reserva_id_reserva as $key => $value): ?>
								<option value="<?= $key; ?>" <?php if ($this->getObjectVariable($this->filters, 'reserva_id_reserva') == $key) {
																								echo "selected";
																							} ?>><?= $value; ?></option>
							<?php endforeach ?>
						</select>
					</label>
				</div>
				<div class="col-2">
					<label>Documento</label>
					<label class="input-group">
						<input type="text" class="form-control" name="documento_invitado"
							value="<?php echo $this->getObjectVariable($this->filters, 'documento_invitado') ?>"></input>
					</label>
				</div>
				<div class="col-2">
					<label>Número de Carnet</label>
					<label class="input-group">
						<input type="text" class="form-control" name="invitadoReserva_numero_carnet"
							value="<?php echo $this->getObjectVariable($this->filters, 'invitadoReserva_numero_carnet') ?>" placeholder="Ej: 12345"></input>
					</label>
				</div>
				<div class="col-2">
					<label>Reservas</label>
					<label class="input-group">
						<select class="form-control" name="reserva_invitados">
							<option value="">Todas</option>
							<option <?php echo $this->getObjectVariable($this->filters, 'reserva_invitados') == 1 ? 'selected' : '' ?>
								value="1">Sin invitado registrados</option>
							<option <?php echo $this->getObjectVariable($this->filters, 'reserva_invitados') == 2 ? 'selected' : '' ?>
								value="2">Con invitados registrados</option>
						</select>
					</label>
				</div>
				<div class="col-3 d-none">
					<label>titulo-principal</label>
					<label class="input-group">
						<select class="form-control" name="invitado_tipo">
							<option value="">Todas</option>
							<?php foreach ($this->list_invitado_tipo as $key => $value): ?>
								<option value="<?= $key; ?>" <?php if ($this->getObjectVariable($this->filters, 'invitado_tipo') == $key) {
																								echo "selected";
																							} ?>><?= $value; ?></option>
							<?php endforeach ?>
						</select>
					</label>
				</div>
				<div class="col-1">
					<label>&nbsp;</label>
					<button type="submit" class="btn btn-block btn-azul"><i class="fas fa-filter"></i></button>
				</div>
				<div class="col-1">
					<label>&nbsp;</label>
					<a class="btn btn-block btn-azul-claro" href="<?php echo $this->route; ?>?cleanfilter=1"><i class="fas fa-eraser"></i></a>
				</div>
			</div>
		</div>
	</form>
	<div align="center">
		<ul class="pagination justify-content-center">
			<?php
			$url = $this->route;
			$min = $this->page - 10;
			if ($min < 0) {
				$min = 1;
			}
			$max = $this->page + 10;
			if ($this->totalpages > 1) {
				if ($this->page != 1)
					echo '<li class="page-item" ><a class="page-link"  href="' . $url . '?page=' . ($this->page - 1) . '"> &laquo; Anterior </a></li>';
				for ($i = 1; $i <= $this->totalpages; $i++) {
					if ($this->page == $i)
						echo '<li class="active page-item"><a class="page-link">' . $this->page . '</a></li>';
					else {
						if ($i <= $max and $i >= $min) {
							echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>  ';
						}
					}
				}
				if ($this->page != $this->totalpages)
					echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page + 1) . '">Siguiente &raquo;</a></li>';
			}
			?>
		</ul>
	</div>
	<div class="content-dashboard">
		<div class="franja-paginas">
			<div class="row g-0 justify-content-between">
				<div class="col-4">
					<div class="titulo-registro">Se encontraron <?php echo $this->register_number; ?> Registros</div>
				</div>
				<div class="col-4 d-flex gap-2">
					<div class="texto-paginas">Registros por pagina:</div>

					<select class="form-control w-auto form-control-sm selectpagination">
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
				<div class="col-4">
					<div class="text-end d-flex gap-2 justify-content-end">
						<?php if ($_SESSION['kt_login_level'] == 1) { ?>
							<div class="btn-group" role="group">
								<button type="button" class="btn btn-sm btn-info dropdown-toggle" data-bs-toggle="dropdown"
									aria-expanded="false">
									<i class="fas fa-history"></i> Historial
								</button>
								<ul class="dropdown-menu">
									<li><a class="dropdown-item" href="<?php echo $this->route . "/actividad"; ?>">
											<i class="fas fa-history"></i> Vista Resumida
										</a></li>
									<li><a class="dropdown-item" href="<?php echo $this->route . "/logs"; ?>">
											<i class="fas fa-list-alt"></i> Logs Técnicos
										</a></li>
								</ul>
							</div>
						<?php } ?>
						<a class="btn btn-sm btn-warning" href="/administracion/reservas/exportarinvitados/?excel=1"
							target="_blank"> <i class="fas fa-plus-square"></i>Exportar invitados</a>

						<?php if ($_SESSION['kt_login_level'] == 1) { ?>
							<a class="btn btn-sm btn-success" href="<?php echo $this->route . "/manage"; ?>"> <i
									class="fas fa-plus-square"></i> Crear Nuevo</a>
						<?php } ?>
					</div>
				</div>
			</div>

		</div>
		<div class="content-table">
			<table class=" table table-striped  table-hover table-administrator text-start">
				<thead>
					<tr>
						<td>Id reserva</td>
						<td>Documento</td>
						<td>Número de carnet</td>
						<td>Nombre</td>
						<td>Apellido</td>
						<td>Tipo de invitado</td>
						<td>Invitado estado</td>
						<td width="100"></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->lists as $content) { ?>
						<?php $id = $content->id_invitado; ?>
						<tr>
							<td><?= $this->list_reserva_id_reserva[$content->reserva_id_reserva]; ?>
							<td><?= $content->documento_invitado; ?></td>
							<td><?= $content->invitadoReserva_numero_carnet; ?></td>
							<td><?= $content->invitadoReserva_nombre_invitado; ?></td>
							<td><?= $content->invitadoReserva_apellido_invitado; ?></td>
							<td><?= $this->list_invitado_tipo[$content->invitado_tipo]; ?></td>
							<td><?= $this->list_invitadoReserva_estado_invitado[$content->invitadoReserva_estado_invitado]; ?></td>
							<td class="text-end">

								<?php if ($_SESSION['kt_login_level'] == 1) { ?>
									<div>
										<a class="btn btn-azul btn-sm" href="<?php echo $this->route; ?>/manage?id=<?= $id ?>"
											data-bs-toggle="tooltip" data-placement="top" title="Editar"><i class="fas fa-pen-alt"></i></a>
										<span data-bs-toggle="tooltip" data-placement="top" title="Eliminar"><a class="btn btn-rojo btn-sm"
												data-bs-toggle="modal" data-bs-target="#modal<?= $id ?>"><i class="fas fa-trash-alt"></i></a></span>
									</div>
								<?php } ?>
								<!-- Modal de Confirmación de Eliminación -->
								<div class="modal fade text-start" id="modal<?= $id ?>" tabindex="-1" role="dialog"
									aria-labelledby="myModalLabel<?= $id ?>">
									<div class="modal-dialog modal-lg" role="document">
										<div class="modal-content">
											<div class="modal-header" style="background:var(--surface);border-bottom:1px solid var(--border);padding:14px 20px;">
												<h5 class="modal-title" id="myModalLabel<?= $id ?>">
													<i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación de Invitado
												</h5>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body">
												<div class="alert alert-danger">
													<h6><i class="fas fa-trash-alt"></i> ¿Está completamente seguro de eliminar este invitado?</h6>
													<p class="mb-0"><strong>Esta accin NO se puede deshacer.</strong></p>
												</div>

												<div class="alert alert-warning">
													<h6><i class="fas fa-calculator"></i> Impacto en la Reserva</h6>
													<p>Al eliminar este invitado se producirán los siguientes cambios automáticos:</p>
													<ul class="mb-0">
														<li><strong>Recálculo del valor total:</strong> La reserva se recalculará según los invitados
															restantes</li>
														<li><strong>Actualización del conteo:</strong> Se reducirá el número total de invitados</li>
														<li><strong>Ajuste de beneficios:</strong> Se recalcularán descuentos y beneficios aplicables
														</li>
														<li><strong>Registro en logs:</strong> Se guardará un registro detallado de esta acción</li>
													</ul>
												</div>

												<div class="card">
													<div class="card-header">
														<h6 class="mb-0"><i class="fas fa-user"></i> Datos del Invitado a Eliminar</h6>
													</div>
													<div class="card-body">
														<div class="row">
															<div class="col-md-6">
																<strong>Nombre:</strong> <?= $content->invitadoReserva_nombre_invitado; ?><br>
																<strong>Documento:</strong> <?= $content->documento_invitado; ?><br>
																<strong>Estado:</strong>
																<?= $this->list_invitadoReserva_estado_invitado[$content->invitadoReserva_estado_invitado]; ?>
															</div>
															<div class="col-md-6">
																<strong>Reserva:</strong>
																<?= $this->list_reserva_id_reserva[$content->reserva_id_reserva]; ?><br>
																<strong>Tipo:</strong> <?= $this->list_invitado_tipo[$content->invitado_tipo]; ?><br>
																<!--   -->
															</div>
														</div>
													</div>
												</div>

												<div class="form-check mt-3">
													<input class="form-check-input" type="checkbox" id="confirmarEliminacion<?= $id ?>" required>
													<label class="form-check-label fw-bold text-danger" for="confirmarEliminacion<?= $id ?>">
														Confirmo que entiendo que esta eliminación cambiará el valor total de la reserva y no se puede
														deshacer
													</label>
												</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-sm btn-azul-claro" data-bs-dismiss="modal">
													<i class="fas fa-times"></i> Cancelar
												</button>
												<a class="btn btn-sm btn-rojo" id="btnConfirmarEliminar<?= $id ?>" style="display: none;"
													href="<?php echo $this->route; ?>/delete?id=<?= $id ?>&csrf=<?= $this->csrf; ?>">
													<i class="fas fa-trash-alt"></i> Confirmar Eliminaci&oacute;n
												</a>
											</div>
										</div>
									</div>
								</div>

								<script>
									document.addEventListener('DOMContentLoaded', function() {
										const checkbox<?= $id ?> = document.getElementById('confirmarEliminacion<?= $id ?>');
										const btnEliminar<?= $id ?> = document.getElementById('btnConfirmarEliminar<?= $id ?>');

										if (checkbox<?= $id ?> && btnEliminar<?= $id ?>) {
											checkbox<?= $id ?>.addEventListener('change', function() {
												btnEliminar<?= $id ?>.style.display = this.checked ? 'inline-block' : 'none';
											});

											// Resetear cuando se cierra el modal
											document.getElementById('modal<?= $id ?>').addEventListener('hidden.bs.modal', function() {
												checkbox<?= $id ?>.checked = false;
												btnEliminar<?= $id ?>.style.display = 'none';
											});
										}
									});
								</script>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="franja-paginas" style="border-top:1px solid #CCCCCC;margin-top:2%;">
			<div class="row" style="padding-top:1%;">
				<div class="col-4">
					<div class="titulo-registro">Se encontraron <?php echo $this->register_number; ?> Registros</div>
				</div>
				<div class="col-4 d-flex gap-2">
					<div class="texto-paginas">Registros por pagina:</div>
					<select class="form-control w-auto form-control-sm selectpagination">
						<option value="20" <?php if ($this->pages == 20) { echo 'selected'; } ?>>20</option>
						<option value="30" <?php if ($this->pages == 30) { echo 'selected'; } ?>>30</option>
						<option value="50" <?php if ($this->pages == 50) { echo 'selected'; } ?>>50</option>
						<option value="100" <?php if ($this->pages == 100) { echo 'selected'; } ?>>100</option>
					</select>
				</div>
			</div>
		</div>
		<input type="hidden" id="csrf" value="<?php echo $this->csrf ?>"><input type="hidden" id="page-route"
			value="<?php echo $this->route; ?>/changepage">
	</div>
	<div align="center">
		<ul class="pagination justify-content-center">
			<?php
			$url = $this->route;
			$min = $this->page - 10;
			if ($min < 0) {
				$min = 1;
			}
			$max = $this->page + 10;
			if ($this->totalpages > 1) {
				if ($this->page != 1)
					echo '<li class="page-item" ><a class="page-link"  href="' . $url . '?page=' . ($this->page - 1) . '"> &laquo; Anterior </a></li>';
				for ($i = 1; $i <= $this->totalpages; $i++) {
					if ($this->page == $i)
						echo '<li class="active page-item"><a class="page-link">' . $this->page . '</a></li>';
					else {
						if ($i <= $max and $i >= $min) {
							echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>  ';
						}
					}
				}
				if ($this->page != $this->totalpages)
					echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page + 1) . '">Siguiente &raquo;</a></li>';
			}
			?>
		</ul>
	</div>
</div>