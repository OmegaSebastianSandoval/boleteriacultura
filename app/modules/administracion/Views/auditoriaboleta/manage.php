<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>" data-bs-toggle="validator">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->auditoriaboleta_id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->auditoriaboleta_id; ?>" />
			<?php } ?>
			<div class="row">
				<div class="col-12 form-group">
					<label for="auditoriaboleta_boleta_uid" class="control-label">auditoriaboleta_boleta_uid</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_boleta_uid; ?>" name="auditoriaboleta_boleta_uid" id="auditoriaboleta_boleta_uid" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_boleta_token" class="control-label">auditoriaboleta_boleta_token</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_boleta_token; ?>" name="auditoriaboleta_boleta_token" id="auditoriaboleta_boleta_token" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_documento_escaneado" class="control-label">auditoriaboleta_documento_escaneado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_documento_escaneado; ?>" name="auditoriaboleta_documento_escaneado" id="auditoriaboleta_documento_escaneado" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label class="control-label">auditoriaboleta_boleta_id</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rosado "><i class="far fa-list-alt"></i></span>
						</div>
						<select class="form-control" name="auditoriaboleta_boleta_id">
							<option value="">Seleccione...</option>
							<?php foreach ($this->list_auditoriaboleta_boleta_id as $key => $value) { ?>
								<option <?php if ($this->getObjectVariable($this->content, "auditoriaboleta_boleta_id") == $key) {
													echo "selected";
												} ?> value="<?php echo $key; ?>" /> <?= $value; ?></option>
							<?php } ?>
						</select>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label class="control-label">auditoriaboleta_boleta_reserva_id</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde-claro "><i class="far fa-list-alt"></i></span>
						</div>
						<select class="form-control" name="auditoriaboleta_boleta_reserva_id">
							<option value="">Seleccione...</option>
							<?php foreach ($this->list_auditoriaboleta_boleta_reserva_id as $key => $value) { ?>
								<option <?php if ($this->getObjectVariable($this->content, "auditoriaboleta_boleta_reserva_id") == $key) {
													echo "selected";
												} ?> value="<?php echo $key; ?>" /> <?= $value; ?></option>
							<?php } ?>
						</select>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_boleta_evento_id" class="control-label">auditoriaboleta_boleta_evento_id</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_boleta_evento_id; ?>" name="auditoriaboleta_boleta_evento_id" id="auditoriaboleta_boleta_evento_id" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_boleta_mesa" class="control-label">auditoriaboleta_boleta_mesa</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_boleta_mesa; ?>" name="auditoriaboleta_boleta_mesa" id="auditoriaboleta_boleta_mesa" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_boleta_numero_ticket" class="control-label">auditoriaboleta_boleta_numero_ticket</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rojo-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_boleta_numero_ticket; ?>" name="auditoriaboleta_boleta_numero_ticket" id="auditoriaboleta_boleta_numero_ticket" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label class="control-label">auditoriaboleta_usuario_validador_id</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul-claro "><i class="far fa-list-alt"></i></span>
						</div>
						<select class="form-control" name="auditoriaboleta_usuario_validador_id">
							<option value="">Seleccione...</option>
							<?php foreach ($this->list_auditoriaboleta_usuario_validador_id as $key => $value) { ?>
								<option <?php if ($this->getObjectVariable($this->content, "auditoriaboleta_usuario_validador_id") == $key) {
													echo "selected";
												} ?> value="<?php echo $key; ?>" /> <?= $value; ?></option>
							<?php } ?>
						</select>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label class="control-label">auditoriaboleta_usuario_validador_nombre</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul "><i class="far fa-list-alt"></i></span>
						</div>
						<select class="form-control" name="auditoriaboleta_usuario_validador_nombre">
							<option value="">Seleccione...</option>
							<?php foreach ($this->list_auditoriaboleta_usuario_validador_nombre as $key => $value) { ?>
								<option <?php if ($this->getObjectVariable($this->content, "auditoriaboleta_usuario_validador_nombre") == $key) {
													echo "selected";
												} ?> value="<?php echo $key; ?>" /> <?= $value; ?></option>
							<?php } ?>
						</select>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_numero_carnet" class="control-label">auditoriaboleta_numero_carnet</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_numero_carnet; ?>" name="auditoriaboleta_numero_carnet" id="auditoriaboleta_numero_carnet" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_accion" class="control-label">auditoriaboleta_accion</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_accion; ?>" name="auditoriaboleta_accion" id="auditoriaboleta_accion" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label class="control-label">auditoriaboleta_resultado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado "><i class="far fa-list-alt"></i></span>
						</div>
						<select class="form-control" name="auditoriaboleta_resultado">
							<option value="">Seleccione...</option>
							<?php foreach ($this->list_auditoriaboleta_resultado as $key => $value) { ?>
								<option <?php if ($this->getObjectVariable($this->content, "auditoriaboleta_resultado") == $key) {
													echo "selected";
												} ?> value="<?php echo $key; ?>" /> <?= $value; ?></option>
							<?php } ?>
						</select>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_motivo_fallo" class="control-label">auditoriaboleta_motivo_fallo</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_motivo_fallo; ?>" name="auditoriaboleta_motivo_fallo" id="auditoriaboleta_motivo_fallo" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label class="control-label">auditoriaboleta_metodo_escaneado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rojo-claro "><i class="far fa-list-alt"></i></span>
						</div>
						<select class="form-control" name="auditoriaboleta_metodo_escaneado">
							<option value="">Seleccione...</option>
							<?php foreach ($this->list_auditoriaboleta_metodo_escaneado as $key => $value) { ?>
								<option <?php if ($this->getObjectVariable($this->content, "auditoriaboleta_metodo_escaneado") == $key) {
													echo "selected";
												} ?> value="<?php echo $key; ?>" /> <?= $value; ?></option>
							<?php } ?>
						</select>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_ip_address" class="control-label">auditoriaboleta_ip_address</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rosado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_ip_address; ?>" name="auditoriaboleta_ip_address" id="auditoriaboleta_ip_address" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_user_agent" class="control-label">auditoriaboleta_user_agent</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_user_agent; ?>" name="auditoriaboleta_user_agent" id="auditoriaboleta_user_agent" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_dispositivo_info" class="control-label">auditoriaboleta_dispositivo_info</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_dispositivo_info; ?>" name="auditoriaboleta_dispositivo_info" id="auditoriaboleta_dispositivo_info" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_url_completa" class="control-label">auditoriaboleta_url_completa</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_url_completa; ?>" name="auditoriaboleta_url_completa" id="auditoriaboleta_url_completa" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_referer" class="control-label">auditoriaboleta_referer</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rosado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_referer; ?>" name="auditoriaboleta_referer" id="auditoriaboleta_referer" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_parametros_get" class="control-label">auditoriaboleta_parametros_get</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_parametros_get; ?>" name="auditoriaboleta_parametros_get" id="auditoriaboleta_parametros_get" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_parametros_post" class="control-label">auditoriaboleta_parametros_post</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_parametros_post; ?>" name="auditoriaboleta_parametros_post" id="auditoriaboleta_parametros_post" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_fecha_hora" class="control-label">auditoriaboleta_fecha_hora</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_fecha_hora; ?>" name="auditoriaboleta_fecha_hora" id="auditoriaboleta_fecha_hora" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_timestamp_unix" class="control-label">auditoriaboleta_timestamp_unix</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rojo-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_timestamp_unix; ?>" name="auditoriaboleta_timestamp_unix" id="auditoriaboleta_timestamp_unix" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_datos_boleta_antes" class="control-label">auditoriaboleta_datos_boleta_antes</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_datos_boleta_antes; ?>" name="auditoriaboleta_datos_boleta_antes" id="auditoriaboleta_datos_boleta_antes" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_datos_boleta_despues" class="control-label">auditoriaboleta_datos_boleta_despues</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_datos_boleta_despues; ?>" name="auditoriaboleta_datos_boleta_despues" id="auditoriaboleta_datos_boleta_despues" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_datos_reserva" class="control-label">auditoriaboleta_datos_reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rojo-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_datos_reserva; ?>" name="auditoriaboleta_datos_reserva" id="auditoriaboleta_datos_reserva" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_datos_sesion" class="control-label">auditoriaboleta_datos_sesion</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_datos_sesion; ?>" name="auditoriaboleta_datos_sesion" id="auditoriaboleta_datos_sesion" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 form-group">
					<label for="auditoriaboleta_observaciones" class="control-label">auditoriaboleta_observaciones</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->auditoriaboleta_observaciones; ?>" name="auditoriaboleta_observaciones" id="auditoriaboleta_observaciones" class="form-control">
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