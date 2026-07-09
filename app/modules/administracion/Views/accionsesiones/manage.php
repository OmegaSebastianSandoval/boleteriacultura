<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>"
		data-toggle="validator">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->accion_sesion_id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->accion_sesion_id; ?>" />
			<?php } ?>
			<div class="row">
				<div class="col-12 form-group">
					<label for="accion_sesion_accion_numero" class="control-label">accion_sesion_accion_numero</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->accion_sesion_accion_numero; ?>"
							name="accion_sesion_accion_numero" id="accion_sesion_accion_numero" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="accion_sesion_documento_socio" class="control-label">accion_sesion_documento_socio</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rojo-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->accion_sesion_documento_socio; ?>"
							name="accion_sesion_documento_socio" id="accion_sesion_documento_socio" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="accion_sesion_fecha_inicio" class="control-label">accion_sesion_fecha_inicio</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->accion_sesion_fecha_inicio; ?>"
							name="accion_sesion_fecha_inicio" id="accion_sesion_fecha_inicio" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="accion_sesion_fecha_fin" class="control-label">accion_sesion_fecha_fin</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rosado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->accion_sesion_fecha_fin; ?>" name="accion_sesion_fecha_fin"
							id="accion_sesion_fecha_fin" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="accion_sesion_sesion_activa" class="control-label">accion_sesion_sesion_activa</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->accion_sesion_sesion_activa; ?>"
							name="accion_sesion_sesion_activa" id="accion_sesion_sesion_activa" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="accion_sesion_ip_usuario" class="control-label">accion_sesion_ip_usuario</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->accion_sesion_ip_usuario; ?>" name="accion_sesion_ip_usuario"
							id="accion_sesion_ip_usuario" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="accion_sesion_user_agent" class="control-label">accion_sesion_user_agent</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->accion_sesion_user_agent; ?>" name="accion_sesion_user_agent"
							id="accion_sesion_user_agent" class="form-control">
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