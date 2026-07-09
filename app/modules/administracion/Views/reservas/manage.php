<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>"
		data-toggle="validator">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->id; ?>" />
			<?php } ?>
			<div class="row">
				<div class="col-md-6 form-group d-none">
					<label for="reserva_id_evento" class="control-label">Evento</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->eventos[$this->content->reserva_id_evento]->evento_titulo; ?>"
							name="reserva_id_evento" id="reserva_id_evento" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-md-6 form-group">
					<label for="reserva_nombre_cliente" class="control-label">Nombre cliente</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rosado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->reserva_nombre_cliente; ?>" name="reserva_nombre_cliente"
							id="reserva_nombre_cliente" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-md-6 form-group">
					<label for="reserva_fecha" class="control-label">Fecha Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul-claro "><i class="fas fa-calendar-alt"></i></span>
						</div>
						<input type="text"
							value="<?php if ($this->content->reserva_fecha) {
												echo $this->content->reserva_fecha;
											} else {
												echo date('Y-m-d');
											} ?>"
							name="reserva_fecha" id="reserva_fecha" class="form-control" data-provide="datepicker"
							data-date-format="yyyy-mm-dd" data-date-language="es">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-md-6 form-group d-none">
					<label for="reserva_hora" class="control-label">Hora Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->reserva_hora; ?>" name="reserva_hora" id="reserva_hora"
							class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>

				<div class="col-md-6 form-group">
					<label for="reserva_telefono" class="control-label">Telefono Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->reserva_telefono; ?>" name="reserva_telefono"
							id="reserva_telefono" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-md-6 form-group">
					<label for="reserva_correo" class="control-label">Correo Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->reserva_correo; ?>" name="reserva_correo" id="reserva_correo"
							class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>

				<div class="col-md-6 form-group d-none">
					<label for="reserva_fecha_creacion" class="control-label">Fecha Creacion Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->reserva_fecha_creacion; ?>" name="reserva_fecha_creacion"
							id="reserva_fecha_creacion" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-md-6 form-group d-none">
					<label for="reserva_fecha_actualizacion" class="control-label">Fecha Actualizacion Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->reserva_fecha_actualizacion; ?>"
							name="reserva_fecha_actualizacion" id="reserva_fecha_actualizacion" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-md-6 form-group d-none">
					<label for="reserva_usuario_creacion" class="control-label">Usuario Creacion Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rojo-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->reserva_usuario_creacion; ?>" name="reserva_usuario_creacion"
							id="reserva_usuario_creacion" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-md-6 form-group d-none">
					<label for="reserva_usuario_actualizacion" class="control-label">Usuario Actualizacion Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->reserva_usuario_actualizacion; ?>"
							name="reserva_usuario_actualizacion" id="reserva_usuario_actualizacion" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-md-6 form-group d-none">
					<label for="reserva_fecha_inicio_reserva" class="control-label">Fecha Inicio Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul-claro "><i class="fas fa-calendar-alt"></i></span>
						</div>
						<input type="text"
							value="<?php if ($this->content->reserva_fecha_inicio_reserva) {
												echo $this->content->reserva_fecha_inicio_reserva;
											} else {
												echo date('Y-m-d');
											} ?>"
							name="reserva_fecha_inicio_reserva" id="reserva_fecha_inicio_reserva" class="form-control"
							data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-md-6 form-group d-none">
					<label for="reserva_fecha_cierre_reserva" class="control-label">Fecha Cierre Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rosado "><i class="fas fa-calendar-alt"></i></span>
						</div>
						<input type="text"
							value="<?php if ($this->content->reserva_fecha_cierre_reserva) {
												echo $this->content->reserva_fecha_cierre_reserva;
											} else {
												echo date('Y-m-d');
											} ?>"
							name="reserva_fecha_cierre_reserva" id="reserva_fecha_cierre_reserva" class="form-control"
							data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-md-6 form-group d-none">
					<label for="reserva_fecha_limite_pago" class="control-label">Fecha Limite Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde-claro "><i class="fas fa-calendar-alt"></i></span>
						</div>
						<input type="text"
							value="<?php if ($this->content->reserva_fecha_limite_pago) {
												echo $this->content->reserva_fecha_limite_pago;
											} else {
												echo date('Y-m-d');
											} ?>"
							name="reserva_fecha_limite_pago" id="reserva_fecha_limite_pago" class="form-control"
							data-provide="datepicker" data-date-format="yyyy-mm-dd" data-date-language="es">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-md-12 form-group">
					<label for="reserva_comentario" class="form-label">Comentario Reserva</label>
					<textarea name="reserva_comentario" id="reserva_comentario" class="form-control tinyeditor"
						rows="10"><?= $this->content->reserva_comentario; ?></textarea>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-12 my-3">
					<h2>Invitados</h2>
				</div>
				<?php foreach ($this->socio as $i) { ?>
					<div class="row">
						<div class="col-12">
							<?php if ($i->invitado_tipo == '1') { ?>
								<hr>
								<span class="text-danger">Socio</span>
							<?php } ?>
						</div>
						<div class="col-md-4">
							<label for="user">Documento</label>
							<input type="text" class="form-control" name="documento_invitado_0" id="" required
								value="<?php echo $i->documento_invitado ?>">
						</div>
						<div class="col-md-4">
							<label for="user">Nombre</label>
							<input type="text" class="form-control" name="invitadoReserva_nombre_invitado_0" id="" required
								value="<?php echo $i->invitadoReserva_nombre_invitado ?>">
						</div>
						<div class="col-md-4">
							<label for="user">Fecha Nacimiento</label>
							<input type="text" class="form-control" name="invitadoReserva_fecha_nacimiento_0" id="" required
								value="<?php echo $i->invitadoReserva_fecha_nacimiento ?>">
						</div>
						<div class="col-md-6">
							<label for="user">Correo</label>
							<input type="text" class="form-control" name="invitadoReserva_correo_invitado_0" id="" required
								value="<?php echo $i->invitadoReserva_correo_invitado ?>">
						</div>
						<div class="col-md-4">
							<label for="user">Teléfono</label>
							<input type="text" class="form-control" name="invitadoReserva_telefono_0" id="" required
								value="<?php echo $i->invitadoReserva_telefono ?>">
						</div>
						<div class="col-md-2">
							<label for="user">Acomodacion</label>
							<input type="text" class="form-control" name="invitadoReserva_telefono_0" id="" required
								value="<?php echo $i->orden ?>">
						</div>
						<div class="col-12">
							<hr>
						</div>
					</div>
				<?php } ?>
				<?php foreach ($this->invitados as $inv): ?>
					<?php
					$partes = explode(",", $inv->OBSERVACION);
					$acomodacion = explode("=>", $partes[3]);
					$asiento = trim($acomodacion[1]);
					?>
					<div class="col-md-4">
						<label for="user">Nombres</label>
						<input type="text" class="form-control" name="" id="" required value="<?php echo $inv->NOMBRES ?>">
					</div>
					<div class="col-md-4">
						<label for="user">Apellidos</label>
						<input type="text" class="form-control" name="" id="" required value="<?php echo $inv->APELLIDOS ?>">
					</div>
					<div class="col-md-4">
						<label for="user">Acomodación</label>
						<input type="text" class="form-control" name="" id="" required value="<?php echo $asiento ?>">
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="botones-acciones">
			<button class="btn btn-guardar" type="submit">Guardar</button>
			<a href="<?php echo $this->route; ?>" class="btn btn-cancelar">Cancelar</a>
		</div>
	</form>
</div>