<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>"
		data-toggle="validator">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->mesa_bloqueo_id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->mesa_bloqueo_id; ?>" />
			<?php } ?>
			<div class="row">
				<div class="col-12 form-group">
					<label for="mesa_bloqueo_mesa" class="control-label">mesa_bloqueo_mesa</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->mesa_bloqueo_mesa; ?>" name="mesa_bloqueo_mesa"
							id="mesa_bloqueo_mesa" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="mesa_bloqueo_documento" class="control-label">mesa_bloqueo_documento</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->mesa_bloqueo_documento; ?>" name="mesa_bloqueo_documento"
							id="mesa_bloqueo_documento" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="mesa_bloqueo_fecha" class="control-label">mesa_bloqueo_fecha</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rojo-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->mesa_bloqueo_fecha; ?>" name="mesa_bloqueo_fecha"
							id="mesa_bloqueo_fecha" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="mesa_bloqueo_fecha_expiracion" class="control-label">mesa_bloqueo_fecha_expiracion</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rosado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->mesa_bloqueo_fecha_expiracion; ?>"
							name="mesa_bloqueo_fecha_expiracion" id="mesa_bloqueo_fecha_expiracion" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="mesa_bloqueo_estado" class="control-label">mesa_bloqueo_estado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->mesa_bloqueo_estado; ?>" name="mesa_bloqueo_estado"
							id="mesa_bloqueo_estado" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="mesa_bloqueo_macnume" class="control-label">mesa_bloqueo_macnume</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->mesa_bloqueo_macnume; ?>" name="mesa_bloqueo_macnume"
							id="mesa_bloqueo_macnume" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="mesa_bloqueo_reserva" class="control-label">mesa_bloqueo_reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->mesa_bloqueo_reserva; ?>" name="mesa_bloqueo_reserva"
							id="mesa_bloqueo_reserva" class="form-control">
					</label>
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