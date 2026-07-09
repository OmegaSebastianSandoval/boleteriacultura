<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>"
		data-toggle="validator">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->informe_id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->informe_id; ?>" />
			<?php } ?>
			<div class="row">
				<div class="col-12 form-group">
					<label for="informe_nombre" class="form-label">Nombre</label>
					<textarea name="informe_nombre" id="informe_nombre" class="form-control tinyeditor" rows="10"
						required><?= $this->content->informe_nombre; ?></textarea>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="informe_descripcion" class="control-label">DEscripcion Informe</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->informe_descripcion; ?>" name="informe_descripcion"
							id="informe_descripcion" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="informe_archivo" class="control-label">Informe Archivo</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->informe_archivo; ?>" name="informe_archivo"
							id="informe_archivo" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="informes_fecha_creacion" class="control-label">Fecha De Creacion</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->informes_fecha_creacion; ?>" name="informes_fecha_creacion"
							id="informes_fecha_creacion" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="informes_fecha_actualizacion" class="form-label">Fecha De Actulizacion</label>
					<textarea name="informes_fecha_actualizacion" id="informes_fecha_actualizacion"
						class="form-control tinyeditor" rows="10"
						required><?= $this->content->informes_fecha_actualizacion; ?></textarea>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="informes_usuario_creacion" class="form-label">Usuario Creacion</label>
					<textarea name="informes_usuario_creacion" id="informes_usuario_creacion" class="form-control tinyeditor"
						rows="10"><?= $this->content->informes_usuario_creacion; ?></textarea>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="informes_usuario_actualizacion" class="form-label">Usuario Actualizacion</label>
					<textarea name="informes_usuario_actualizacion" id="informes_usuario_actualizacion"
						class="form-control tinyeditor" rows="10"><?= $this->content->informes_usuario_actualizacion; ?></textarea>
					<div class="help-block with-errors"></div>
				</div>
			</div>
		</div>
		<div class="botones-acciones">
			<button class="btn btn-guardar" type="submit">Guardar</button>
			<a href="<?php echo $this->route; ?>" class="btn btn-cancelar">Cancelar</a>
		</div>
	</form>
</div>