<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>"
		data-toggle="validator">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->id_invitado) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->id_invitado; ?>" />
			<?php } ?>
			<div class="row">
				<div class="col-12 form-group">
					<label for="documento_invitado" class="form-label">documento invitado</label>
					<textarea name="documento_invitado" id="documento_invitado" class="form-control tinyeditor"
						rows="10"><?= $this->content->documento_invitado; ?></textarea>
					<div class="help-block with-errors"></div>
				</div>
				<div class="row">
					<div class="col-12 form-group">
						<label for="reserva_id_reserva" class="form-label">id reserva</label>
						<textarea name="reserva_id_reserva" id="reserva_id_reserva" class="form-control tinyeditor"
							rows="10"><?= $this->content->reserva_id_reserva; ?></textarea>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-12 form-group">
						<label for="invitadoReserva_nombre_invitado" class="control-label">Nombre Invitado</label>
						<label class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text input-icono  fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
							</div>
							<input type="text" value="<?= $this->content->invitadoReserva_nombre_invitado; ?>"
								name="invitadoReserva_nombre_invitado" id="invitadoReserva_nombre_invitado" class="form-control">
						</label>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-12 form-group">
						<label for="invitadoReserva_correo_invitado" class="form-label">Correo Invitado</label>
						<textarea name="invitadoReserva_correo_invitado" id="invitadoReserva_correo_invitado"
							class="form-control tinyeditor"
							rows="10"><?= $this->content->invitadoReserva_correo_invitado; ?></textarea>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-12 form-group">
						<label for="invitadoReserva_estado_invitado" class="control-label">Estado Invitado</label>
						<label class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text input-icono  fondo-rojo-claro "><i class="fas fa-pencil-alt"></i></span>
							</div>
							<input type="text" value="<?= $this->content->invitadoReserva_estado_invitado; ?>"
								name="invitadoReserva_estado_invitado" id="invitadoReserva_estado_invitado" class="form-control">
						</label>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-12 form-group">
						<label for="invitadoReserva_fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
						<textarea name="invitadoReserva_fecha_nacimiento" id="invitadoReserva_fecha_nacimiento"
							class="form-control tinyeditor"
							rows="10"><?= $this->content->invitadoReserva_fecha_nacimiento; ?></textarea>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-12 form-group">
						<label for="invitadoReserva_telefono" class="control-label">Telefono invitado</label>
						<label class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text input-icono  fondo-azul "><i class="fas fa-pencil-alt"></i></span>
							</div>
							<input type="text" value="<?= $this->content->invitadoReserva_telefono; ?>"
								name="invitadoReserva_telefono" id="invitadoReserva_telefono" class="form-control">
						</label>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-12 form-group">
						<label for="invitadosReserva_fecha_creacion" class="form-label">Fecha De Creacion</label>
						<textarea name="invitadosReserva_fecha_creacion" id="invitadosReserva_fecha_creacion"
							class="form-control tinyeditor"
							rows="10"><?= $this->content->invitadosReserva_fecha_creacion; ?></textarea>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-12 form-group">
						<label for="invitadosReserva_fecha_actualizacion" class="form-label">Fecha De Actualizacion</label>
						<textarea name="invitadosReserva_fecha_actualizacion" id="invitadosReserva_fecha_actualizacion"
							class="form-control tinyeditor"
							rows="10"><?= $this->content->invitadosReserva_fecha_actualizacion; ?></textarea>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-12 form-group">
						<label for="invitadosReserva_usuario_creacion" class="control-label">Usuario Creacion</label>
						<label class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text input-icono  fondo-rosado "><i class="fas fa-pencil-alt"></i></span>
							</div>
							<input type="text" value="<?= $this->content->invitadosReserva_usuario_creacion; ?>"
								name="invitadosReserva_usuario_creacion" id="invitadosReserva_usuario_creacion" class="form-control">
						</label>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-12 form-group">
						<label for="invitadosReserva_actualizacion" class="form-label">Usuario Actualizacion</label>
						<textarea name="invitadosReserva_actualizacion" id="invitadosReserva_actualizacion"
							class="form-control tinyeditor"
							rows="10"><?= $this->content->invitadosReserva_actualizacion; ?></textarea>
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