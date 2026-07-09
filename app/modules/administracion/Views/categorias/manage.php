<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->categoria_id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->categoria_id; ?>" />
			<?php } ?>

			<div class="row g-2 align-items-end">
				<div class="col-auto d-grid">
					<label class="control-label">Activo</label>
					<input type="checkbox" name="categoria_estado" value="1" class="form-control switch-form"
						<?php if ($this->getObjectVariable($this->content, 'categoria_estado') == 1) { echo "checked"; } ?>>
				</div>
				<div class="col">
					<label for="categoria_nombre" class="control-label">Nombre</label>
					<input type="text" value="<?= $this->content->categoria_nombre; ?>" name="categoria_nombre" id="categoria_nombre" class="form-control">
				</div>
				<div class="col">
					<label for="categoria_precio_socio" class="control-label">Precio socio</label>
					<input type="text" value="<?= $this->content->categoria_precio_socio; ?>" name="categoria_precio_socio" id="categoria_precio_socio" class="form-control">
				</div>
				<div class="col">
					<label for="categoria_precio_socio_hijo" class="control-label">Precio socio hijo</label>
					<input type="text" value="<?= $this->content->categoria_precio_socio_hijo; ?>" name="categoria_precio_socio_hijo" id="categoria_precio_socio_hijo" class="form-control">
				</div>
				<div class="col">
					<label for="categoria_precio_invitado" class="control-label">Precio invitado</label>
					<input type="text" value="<?= $this->content->categoria_precio_invitado; ?>" name="categoria_precio_invitado" id="categoria_precio_invitado" class="form-control">
				</div>

				<div class="col-12 d-none">
					<textarea name="categoria_descripcion" id="categoria_descripcion" class="form-control" rows="10"><?= $this->content->categoria_descripcion; ?></textarea>
				</div>
				<input type="hidden" name="categoria_precio" value="<?= $this->content->categoria_precio; ?>">
			</div>
		</div>
		<div class="botones-acciones">
			<button class="btn btn-guardar" type="submit">Guardar</button>
			<a href="<?php echo $this->route; ?>" class="btn btn-cancelar">Cancelar</a>
		</div>
	</form>
</div>
