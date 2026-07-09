<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>"
		data-toggle="validator">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->piso_id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->piso_id; ?>" />
			<?php } ?>
			<div class="row">
				<input type="hidden" name="piso_evento" value="<?php echo $this->content->piso_evento ?>">
				<div class="col-2 form-group d-grid">
					<label class="control-label">Activar (Si, No)</label>
					<input type="checkbox" name="piso_estado" value="1" class="form-control switch-form " <?php if ($this->getObjectVariable($this->content, 'piso_estado') == 1) {
																																																	echo "checked";
																																																} ?>></input>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-10 form-group">
					<label for="piso_nombre" class="control-label">Nombre</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->piso_nombre; ?>" name="piso_nombre" id="piso_nombre"
							class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-4 form-group d-none">
					<label for="piso_color" class="control-label">Color</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-naranja "><i class="fas fa-palette"></i></span>
						</div>
						<input type="color" value="<?= $this->content->piso_color; ?>" name="piso_color" id="piso_color"
							class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>

				<div class="col-4 form-group d-none">
					<label for="piso_imagen_disponible">Disponible</label>
					<input type="file" name="piso_imagen_disponible" id="piso_imagen_disponible" class="form-control  file-image"
						data-buttonName="btn-primary" accept="image/gif, image/jpg, image/jpeg, image/png">
					<div class="help-block with-errors"></div>
					<?php if ($this->content->piso_imagen_disponible) { ?>
						<div id="imagen_piso_imagen_disponible">
							<img src="/images/<?= $this->content->piso_imagen_disponible; ?>"
								class="img-thumbnail thumbnail-administrator" />
							<div><button class="btn btn-danger btn-sm" type="button"
									onclick="eliminarImagen('piso_imagen_disponible','<?php echo $this->route . "/deleteimage"; ?>')"><i
										class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
						</div>
					<?php } ?>
				</div>
				<div class="col-4 form-group d-none">
					<label for="piso_imagen_pendiente">Pendiente</label>
					<input type="file" name="piso_imagen_pendiente" id="piso_imagen_pendiente" class="form-control  file-image"
						data-buttonName="btn-primary" accept="image/gif, image/jpg, image/jpeg, image/png">
					<div class="help-block with-errors"></div>
					<?php if ($this->content->piso_imagen_pendiente) { ?>
						<div id="imagen_piso_imagen_pendiente">
							<img src="/images/<?= $this->content->piso_imagen_pendiente; ?>"
								class="img-thumbnail thumbnail-administrator" />
							<div><button class="btn btn-danger btn-sm" type="button"
									onclick="eliminarImagen('piso_imagen_pendiente','<?php echo $this->route . "/deleteimage"; ?>')"><i
										class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
						</div>
					<?php } ?>
				</div>
				<div class="col-4 form-group d-none">
					<label for="piso_imagen_ocupado">Ocupado</label>
					<input type="file" name="piso_imagen_ocupado" id="piso_imagen_ocupado" class="form-control  file-image"
						data-buttonName="btn-primary" accept="image/gif, image/jpg, image/jpeg, image/png">
					<div class="help-block with-errors"></div>
					<?php if ($this->content->piso_imagen_ocupado) { ?>
						<div id="imagen_piso_imagen_ocupado">
							<img src="/images/<?= $this->content->piso_imagen_ocupado; ?>"
								class="img-thumbnail thumbnail-administrator" />
							<div><button class="btn btn-danger btn-sm" type="button"
									onclick="eliminarImagen('piso_imagen_ocupado','<?php echo $this->route . "/deleteimage"; ?>')"><i
										class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="botones-acciones">
			<button class="btn btn-guardar" type="submit">Guardar</button>
			<a href="<?php echo $this->route; ?>" class="btn btn-cancelar">Cancelar</a>
		</div>
	</form>
</div>