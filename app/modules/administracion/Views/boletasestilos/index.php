<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form action="<?php echo $this->route; ?>" method="post">
		<div class="content-dashboard">
			<div class="row">
				<div class="col-3 d-none">
					<label>Activo(Si,No)</label>
					<label class="input-group">
						<input type="text" class="form-control" name="boletas_estilo_estado" value="<?php echo $this->getObjectVariable($this->filters, 'boletas_estilo_estado') ?>"></input>
					</label>
				</div>
				<div class="col-2">
					<label>Título</label>
					<label class="input-group">
						<input type="text" class="form-control" name="boletas_estilo_titulo" value="<?php echo $this->getObjectVariable($this->filters, 'boletas_estilo_titulo') ?>"></input>
					</label>
				</div>
				<div class="col-2">
					<label>Fondo</label>
					<label class="input-group">
						<input type="text" class="form-control" name="boletas_estilo_fondo" value="<?php echo $this->getObjectVariable($this->filters, 'boletas_estilo_fondo') ?>"></input>
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
	<div class="alert alert-warning text-center">
		<strong>Nota:</strong> Solo un dise&ntilde;o puede estar activo al mismo tiempo. Al activar un dise&ntilde;o, los dem&aacute;s se desactivan autom&aacute;ticamente.
	</div>

	<?php if (isset($_GET['error']) && $_GET['error'] == 1) { ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Error:</strong> Debe haber al menos un dise&ntilde;o activo.
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php } ?>
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
					<div class="text-end"><a class="btn btn-sm btn-success" href="<?php echo $this->route . "\manage"; ?>"> <i class="fas fa-plus-square"></i> Crear Nuevo</a></div>
				</div>
			</div>
		</div>
		<div class="content-table">
			<table class=" table table-striped  table-hover table-administrator text-left">
				<thead>
					<tr>
						<td>Activo(Si,No)</td>
						<td>Título</td>
						<td>Fondo</td>
						<td width="150"></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->lists as $content) { ?>
						<?php $id =  $content->boletas_estilo_id; ?>
						<tr>
							<td><?= $content->boletas_estilo_estado == 1 ? 'Si' : 'No'; ?></td>
							<td><?= $content->boletas_estilo_titulo; ?></td>
							<td>
								<?php if ($content->boletas_estilo_fondo) { ?>
									<img src="/images/<?= $content->boletas_estilo_fondo; ?>" class="img-thumbnail thumbnail-administrator" />
								<?php } ?>
								<div><?= $content->boletas_estilo_fondo; ?></div>
							</td>
							<td class="text-end">
								<div>
									<a class="btn btn-azul btn-sm" href="<?php echo $this->route; ?>/manage?id=<?= $id ?>" data-bs-toggle="tooltip" data-placement="top" title="Editar"><i class="fas fa-pen-alt"></i></a>
									<a class="btn btn-primary btn-sm" href="<?php echo $this->route; ?>/previsualizar?id=<?= $id ?>" target="_blank" data-bs-toggle="tooltip" data-placement="top" title="Previsualizar PDF"><i class="fas fa-eye"></i></a>
									<span data-bs-toggle="tooltip" data-placement="top" title="Eliminar"><a class="btn btn-rojo btn-sm" data-bs-toggle="modal" data-bs-target="#modal<?= $id ?>"><i class="fas fa-trash-alt"></i></a></span>
								</div>
								<!-- Modal -->
								<div class="modal fade text-left" id="modal<?= $id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
									<div class="modal-dialog" role="document">
										<div class="modal-content">
											<div class="modal-header">
												<h4 class="modal-title" id="myModalLabel">Eliminar Registro</h4>
												<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
											</div>
											<div class="modal-body">
												<div class="">¿Esta seguro de eliminar este registro?</div>
											</div>
											<div class="modal-footer">
												<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
												<a class="btn btn-danger" href="<?php echo $this->route; ?>/delete?id=<?= $id ?>&csrf=<?= $this->csrf; ?><?php echo ''; ?>">Eliminar</a>
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
		<input type="hidden" id="csrf" value="<?php echo $this->csrf ?>"><input type="hidden" id="page-route" value="<?php echo $this->route; ?>/changepage">
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