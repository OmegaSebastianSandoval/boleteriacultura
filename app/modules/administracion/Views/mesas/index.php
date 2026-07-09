<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form action="<?php echo $this->route; ?>" method="post">
		<div class="content-dashboard">
			<div class="row">
				<div class="col-2">
					<label>Ambiente</label>
					<label class="input-group">
						<!-- <div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-morado "><i class="far fa-list-alt"></i></span>
						</div> -->
						<select class="form-select" name="mesa_ambiente">
							<option value="">Todas</option>
							<?php foreach ($this->list_mesa_ambiente as $key => $value): ?>
								<option value="<?= $key; ?>" <?php if ($this->getObjectVariable($this->filters, 'mesa_ambiente') == $key) {
																								echo "selected";
																							} ?>><?= $value; ?></option>
							<?php endforeach ?>
						</select>
					</label>
				</div>
				<div class="col-2">
					<label>Código</label>
					<label class="input-group">
						<!-- <div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-rosado "><i class="fas fa-pencil-alt"></i></span>
						</div> -->
						<input type="text" class="form-control" name="mesa_codigo"
							value="<?php echo $this->getObjectVariable($this->filters, 'mesa_codigo') ?>"></input>
					</label>
				</div>
				<!-- <div class="col-2">
					<label>Nombre</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="mesa_nombre"
							value="<?php echo $this->getObjectVariable($this->filters, 'mesa_nombre') ?>"></input>
					</label>
				</div> -->
				<div class="col-2">
					<label>Capacidad</label>
					<label class="input-group">
						<!-- <div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
						</div> -->
						<input type="text" class="form-control" name="mesa_capacidad"
							value="<?php echo $this->getObjectVariable($this->filters, 'mesa_capacidad') ?>"></input>
					</label>
				</div>
				<div class="col-2">
					<label>Activa</label>
					<label class="input-group">
						<!-- <div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
					 -->

						<select class="form-select" name="mesa_activa">
							<option value="">Todas</option>

							<option value="1" <?php if ($this->getObjectVariable($this->filters, 'mesa_activa') == 1) {
																	echo "selected";
																} ?>>Si</option>
							<option value="0" <?php if ($this->getObjectVariable($this->filters, 'mesa_activa') == 0) {
																	echo "selected";
																} ?>>No</option>

						</select>

					</label>
				</div>
				<div class="col-2">
					<label>Estado</label>
					<label class="input-group">
						<!-- <div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-azul "><i class="fas fa-pencil-alt"></i></span>
						</div> -->

						<select class="form-select" name="mesa_imagen_disponible">
							<option value="">Todas</option>

							<option value="1" <?php if ($this->getObjectVariable($this->filters, 'mesa_imagen_disponible') == 1) {
																	echo "selected";
																} ?>>Ocupada</option>

							<option value="0" <?php if ($this->getObjectVariable($this->filters, 'mesa_imagen_disponible') == 0) {
																	echo "selected";
																} ?>>Libre</option>
							<option value="2" <?php if ($this->getObjectVariable($this->filters, 'mesa_imagen_disponible') == 2) {
																	echo "selected";
																} ?>>Provisión</option>

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
		<div class="franja-paginas">
			<div class="row">
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
				<div class="col-3 d-none">
					<div class="text-end"><a class="btn btn-sm btn-success" href="<?php echo $this->route . "\manage"; ?>"> <i
								class="fas fa-plus-square"></i> Crear Nuevo</a></div>
				</div>
			</div>
		</div>
		<div class="content-table">
			<table class=" table table-striped  table-hover table-administrator text-start">
				<thead>
					<tr>
						<td>Ambiente</td>
						<td>Código</td>
						<td>Nombre</td>
						<td>Capacidad</td>
						<td>¿Mesa activa?</td>
						<td>Estado</td>
						<td width="100">Mesa en provisión</td>
						<!-- <td width="100">Orden</td> -->
						<td width=""></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->lists as $content) { ?>
						<?php $id = $content->mesa_id; ?>
						<tr>
							<td><?= $this->list_mesa_ambiente[$content->mesa_ambiente]; ?></td>
							<td><?= $content->mesa_codigo; ?></td>
							<td><?= $content->mesa_nombre; ?></td>
							<td><?= $content->mesa_capacidad; ?></td>
							<td><?= $content->mesa_activa == 1 ? 'Activo' : 'Inactivo'; ?></td>
							<td>
								<div><?= $content->mesa_estado == 1 ? 'Ocupada' : 'Libre'; ?></div>
							</td>
							<td>
								<div><?= $content->mesa_provision != '' ? 'Provisión' : 'No'; ?></div>
							</td>
							<!-- <td>
								<input type="hidden" id="<?= $id; ?>" value="<?= $content->orden; ?>"></input>
								<button class="up_table btn btn-primary btn-sm"><i class="fas fa-angle-up"></i></button>
								<button class="down_table btn btn-primary btn-sm"><i class="fas fa-angle-down"></i></button>
							</td> -->
							<td class="text-end">
								<div>
									<a class="btn btn-azul btn-sm" href="<?php echo $this->route; ?>/manage?id=<?= $id ?>"
										data-bs-toggle="tooltip" data-placement="top" title="Editar"><i class="fas fa-pen-alt"></i></a>

									<?php if ($content->reserva) { ?>
										<button type="button" data-bs-toggle="modal" data-bs-target="#modal_reserva<?= $id ?>"
											style="display:inline-flex;align-items:center;border-radius:6px;overflow:hidden;border:none;padding:0;font-size:.875rem;font-weight:600;line-height:1.5;cursor:pointer;vertical-align:middle;">
											<span style="background:#1d4ed8;color:#fff;padding:.3rem .45rem;"><i class="fas fa-calendar-check"></i></span>
											<span style="background:#dbeafe;color:#1e40af;padding:.3rem .5rem;">Reserva</span>
										</button>
									<?php } ?>

									<span data-bs-toggle="tooltip" data-placement="top" title="Eliminar" class="d-none"><a class="btn btn-rojo btn-sm"
											data-bs-toggle="modal" data-bs-target="#modal<?= $id ?>"><i class="fas fa-trash-alt"></i></a></span>
									<?= $content->mesa_provision == 2 ? '<span style="color: black; font-weight: bold;">P</span>' : ''; ?>
								</div>
								<!-- Modal -->
								<div class="modal fade text-start" id="modal<?= $id ?>" tabindex="-1" role="dialog"
									aria-labelledby="myModalLabel">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="modal-title" id="myModalLabel">Eliminar Registro</h4>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body">
												<div class="">驴Esta seguro de eliminar este registro?</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
												<a class="btn btn-danger"
													href="<?php echo $this->route; ?>/delete?id=<?= $id ?>&csrf=<?= $this->csrf; ?><?php echo ''; ?>">Eliminar</a>
											</div>
										</div>
									</div>
								</div>

								<!-- Modal Reserva -->
								<div class="modal fade text-start" id="modal_reserva<?= $id ?>" tabindex="-1" role="dialog">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h6 class="modal-title"><i class="fas fa-calendar-check me-1"></i> Reserva #<?php echo $content->reserva->id; ?></h6>
												<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
											</div>
											<div class="modal-body py-2">
												<div class="d-flex gap-4 flex-wrap" style="font-size:.85rem;">
													<div><span class="text-muted">Doc:</span> <strong><?php echo $content->reserva->reserva_documento; ?></strong></div>
													<div><span class="text-muted">Nombre:</span> <strong><?php echo $content->reserva->reserva_nombre_cliente . " " . $content->reserva->reserva_apellido_cliente; ?></strong></div>
													<div><span class="text-muted">Acción:</span> <strong><?php echo $content->reserva->reserva_numero_carnet; ?></strong></div>
												</div>
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
		<div class="franja-paginas">
			<div class="row">
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
				<div class="col-3 d-none">
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