<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form action="<?php echo $this->route; ?>" method="post">
		<div class="content-dashboard">
			<div class="row">
				<div class="col-2">
					<label>Activo</label>
					<label class="input-group">
						<!-- <div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-azul "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="piso_estado"
							value="<?php echo $this->getObjectVariable($this->filters, 'piso_estado') ?>"></input> -->
						<select name="piso_estado" id="" class="form-select">
							<option value="">Todos</option>
							<option value="1" <?php echo ($this->getObjectVariable($this->filters, 'piso_estado') == 1) ? 'selected' : ''; ?>>Si</option>
							<option value="0" <?php echo ($this->getObjectVariable($this->filters, 'piso_estado') == 0) ? 'selected' : ''; ?>>No</option>
						</select>
					</label>
				</div>
				<div class="col-2">
					<label>Nombre</label>
					<label class="input-group">
						<input type="text" class="form-control" name="piso_nombre"
							value="<?php echo $this->getObjectVariable($this->filters, 'piso_nombre') ?>"></input>
					</label>
				</div>
				<div class="col-3 d-none">
					<label>Color</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-naranja "><i class="fas fa-palette"></i></span>
						</div>
						<input type="text" class="form-control" name="piso_color"
							value="<?php echo $this->getObjectVariable($this->filters, 'piso_color') ?>"></input>
					</label>
				</div>

				<div class="col-3 d-none">
					<label>Disponible</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="piso_imagen_disponible"
							value="<?php echo $this->getObjectVariable($this->filters, 'piso_imagen_disponible') ?>"></input>
					</label>
				</div>
				<div class="col-3 d-none">
					<label>Pendiente</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="piso_imagen_pendiente"
							value="<?php echo $this->getObjectVariable($this->filters, 'piso_imagen_pendiente') ?>"></input>
					</label>
				</div>
				<div class="col-3 d-none">
					<label>Ocupado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono fondo-verde-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" class="form-control" name="piso_imagen_ocupado"
							value="<?php echo $this->getObjectVariable($this->filters, 'piso_imagen_ocupado') ?>"></input>
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
						<td width="200">&iquest;Piso activo?</td>
						<td>Nombre</td>
						<!-- <td>Color</td> -->
						<!-- <td>Disponible</td>
						<td>Pendiente</td>
						<td>Ocupado</td>
						<td width="100">Orden</td> -->
						<td width="100"></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->lists as $content) { ?>
						<?php $id = $content->piso_id; ?>
						<tr>
							<td><?= $content->piso_estado == 1 ? 'Activo' : 'Inactivo'; ?></td>
							<td><?= $content->piso_nombre; ?></td>
							<!-- <td>
								<div
									style="display: inline-block; width: 20px; height: 20px; background-color: <?= $content->piso_color; ?>; border: 1px solid #ccc; margin-right: 8px; vertical-align: middle;">
								</div>
								<?= $content->piso_color; ?>
							</td> -->
							<!-- <td>
								<?php if ($content->piso_imagen_disponible) { ?>
									<img src="/images/<?= $content->piso_imagen_disponible; ?>"
										class="img-thumbnail thumbnail-administrator" />
								<?php } ?>
								<div><?= $content->piso_imagen_disponible; ?></div>
							</td>
							<td>
								<?php if ($content->piso_imagen_pendiente) { ?>
									<img src="/images/<?= $content->piso_imagen_pendiente; ?>"
										class="img-thumbnail thumbnail-administrator" />
								<?php } ?>
								<div><?= $content->piso_imagen_pendiente; ?></div>
							</td>
							<td>
								<?php if ($content->piso_imagen_ocupado) { ?>
									<img src="/images/<?= $content->piso_imagen_ocupado; ?>" class="img-thumbnail thumbnail-administrator" />
								<?php } ?>
								<div><?= $content->piso_imagen_ocupado; ?></div>
							</td>
							<td>
								<input type="hidden" id="<?= $id; ?>" value="<?= $content->orden; ?>"></input>
								<button class="up_table btn btn-primary btn-sm"><i class="fas fa-angle-up"></i></button>
								<button class="down_table btn btn-primary btn-sm"><i class="fas fa-angle-down"></i></button>
							</td> -->
							<td class="text-end">
								<div>
									<a class="btn btn-azul btn-sm" href="<?php echo $this->route; ?>/manage?id=<?= $id ?>"
										data-bs-toggle="tooltip" data-placement="top" title="Editar"><i class="fas fa-pen-alt"></i></a>
									<!-- <span data-bs-toggle="tooltip" data-placement="top" title="Eliminar"><a class="btn btn-rojo btn-sm"
											data-bs-toggle="modal" data-bs-target="#modal<?= $id ?>"><i class="fas fa-trash-alt"></i></a></span> -->
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
												<div class="">���Esta seguro de eliminar este registro?</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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