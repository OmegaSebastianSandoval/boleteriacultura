<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>"
		data-bs-toggle="validator">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->id_invitado) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->id_invitado; ?>" />
			<?php } ?>
			<div class="row">
				<div class="col-3 form-group d-grid d-none">
					<label class="control-label">¿Actualizar información de la reserva?</label>
					<input type="checkbox" name="actualziarvalorreseva" value="1" class="form-control switch-form "></input>
					<div class="help-block with-errors"></div>
				</div>
			</div>

			<div class="row">
				<div class="col-3 form-group">
					<label class="control-label">Reserva</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul-claro "><i class="far fa-list-alt"></i></span>
						</div>
						<?php if (!$this->content->reserva_id_reserva) { ?>
							<select class="form-control" name="reserva_id_reserva" required>
								<option value="">Seleccione...</option>
								<?php foreach ($this->list_reserva_id_reserva as $key => $value) { ?>
									<option <?php if ($this->getObjectVariable($this->content, "reserva_id_reserva") == $key) {
														echo "selected";
													} ?> value="<?php echo $key; ?>" /> <?= $value; ?></option>
								<?php } ?>
							</select>
						<?php } else { ?>
							<input type="text" class="form-control"
								value="<?= $this->list_reserva_id_reserva[$this->content->reserva_id_reserva]; ?>"
								name="reserva_id_reserva" readonly>
						<?php } ?>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group">
					<label for="documento_invitado" class="control-label">Documento</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->documento_invitado; ?>" name="documento_invitado"
							id="documento_invitado" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group">
					<label for="invitadoReserva_nombre_invitado" class="control-label">Nombre del invitado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->invitadoReserva_nombre_invitado; ?>"
							name="invitadoReserva_nombre_invitado" id="invitadoReserva_nombre_invitado" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group">
					<label for="invitadoReserva_apellido_invitado" class="control-label">Apellido del invitado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->invitadoReserva_apellido_invitado; ?>"
							name="invitadoReserva_apellido_invitado" id="invitadoReserva_apellido_invitado" class="form-control" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group">
					<label for="invitadoReserva_correo_invitado" class="control-label">Correo del invitado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="email" value="<?= $this->content->invitadoReserva_correo_invitado; ?>"
							name="invitadoReserva_correo_invitado" id="invitadoReserva_correo_invitado" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group">
					<label class="control-label">Estado del invitado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rojo-claro "><i class="far fa-list-alt"></i></span>
						</div>
						<select class="form-control" name="invitadoReserva_estado_invitado" id="invitadoReserva_estado_invitado"
							required>
							<option value="">Seleccione...</option>
							<?php foreach ($this->list_invitadoReserva_estado_invitado as $key => $value) { ?>
								<option <?php if ($this->getObjectVariable($this->content, "invitadoReserva_estado_invitado") == $key) {
													echo "selected";
												} ?> value="<?php echo $key; ?>" /> <?= $value; ?></option>
							<?php } ?>
						</select>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group">
					<label for="invitadoReserva_fecha_nacimiento" class="control-label">Fecha de nacimiento</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->invitadoReserva_fecha_nacimiento; ?>"
							name="invitadoReserva_fecha_nacimiento" id="invitadoReserva_fecha_nacimiento" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group">
					<label for="invitadoReserva_telefono" class="control-label">Teléfono del invitado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rosado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->invitadoReserva_telefono; ?>" name="invitadoReserva_telefono"
							id="invitadoReserva_telefono" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group d-none">
					<label for="invitadosReserva_fecha_creacion" class="control-label">Fecha de creación</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->invitadosReserva_fecha_creacion; ?>"
							name="invitadosReserva_fecha_creacion" id="invitadosReserva_fecha_creacion" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group d-none">
					<label for="invitadosReserva_fecha_actualizacion" class="control-label">Fecha de actualización</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-verde "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->invitadosReserva_fecha_actualizacion; ?>"
							name="invitadosReserva_fecha_actualizacion" id="invitadosReserva_fecha_actualizacion"
							class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group d-none">
					<label for="invitadosReserva_usuario_creacion" class="control-label">Usuario de creación</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rosado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->invitadosReserva_usuario_creacion; ?>"
							name="invitadosReserva_usuario_creacion" id="invitadosReserva_usuario_creacion" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group d-none">
					<label for="invitadosReserva_actualizacion" class="control-label">Actualización</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->invitadosReserva_actualizacion; ?>"
							name="invitadosReserva_actualizacion" id="invitadosReserva_actualizacion" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group">
					<label class="control-label">Tipo de invitado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-rojo-claro "><i class="far fa-list-alt"></i></span>
						</div>
						<select class="form-control" id="invitado_tipo_display" disabled
							style="background-color: #f8f9fa; cursor: not-allowed;">
							<option value="">Seleccione...</option>
							<?php foreach ($this->list_invitado_tipo as $key => $value) { ?>
								<option <?php if ($this->getObjectVariable($this->content, "invitado_tipo") == $key) {
													echo "selected";
												} ?>
									value="<?php echo $key; ?>" /> <?= $value; ?></option>
							<?php } ?>
						</select>
						<input type="hidden" name="invitado_tipo" id="invitado_tipo"
							value="<?= $this->getObjectVariable($this->content, 'invitado_tipo'); ?>" required>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group d-grid">
					<label class="control-label">Evento del invitado</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado "><i class="far fa-list-alt"></i></span>
						</div>
						<select class="form-control" name="invitado_evento">
							<option value="">Seleccione...</option>
							<?php foreach ($this->list_invitado_evento as $key => $value) { ?>
								<option <?php if ($this->getObjectVariable($this->content, "invitado_evento") == $key) {
													echo "selected";
												} ?> value="<?php echo $key; ?>" /> <?= $value; ?></option>
							<?php } ?>
						</select>
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group d-grid">
					<label class="control-label">Beneficiario menor de 25</label>
					<input type="checkbox" name="invitadoReserva_beneficiario_menor25" value="1" class="form-control switch-form "
						<?php if ($this->getObjectVariable($this->content, 'invitadoReserva_beneficiario_menor25') == 1) {
							echo "checked";
						} ?>></input>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group d-grid">
					<label class="control-label">Beneficiario hijo</label>
					<input type="checkbox" name="invitadoReserva_beneficiario_hijo" value="1" class="form-control switch-form "
						<?php if ($this->getObjectVariable($this->content, 'invitadoReserva_beneficiario_hijo') == 1) {
							echo "checked";
						} ?>></input>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group">
					<label for="invitadoReserva_beneficiario_cupo" class="control-label">Cupo del beneficiario</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->invitadoReserva_beneficiario_cupo; ?>"
							name="invitadoReserva_beneficiario_cupo" id="invitadoReserva_beneficiario_cupo" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group d-grid">
					<label class="control-label">Beneficiario principal</label>
					<input type="checkbox" name="invitadoReserva_beneficiario_principal" value="1"
						class="form-control switch-form " <?php if ($this->getObjectVariable($this->content, 'invitadoReserva_beneficiario_principal') == 1) {
																								echo "checked";
																							} ?>></input>
					<div class="help-block with-errors"></div>
				</div>
				<div class="col-3 form-group">
					<label for="invitadoReserva_numero_carnet" class="control-label">N&uacute;mero de carnet</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->invitadoReserva_numero_carnet; ?>"
							name="invitadoReserva_numero_carnet" id="invitadoReserva_numero_carnet" class="form-control">
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

<!-- Modal de confirmación para cambios en la reserva -->
<div class="modal fade" id="modalConfirmacionCambios" tabindex="-1" aria-labelledby="modalConfirmacionCambiosLabel"
	aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-warning text-dark">
				<h5 class="modal-title" id="modalConfirmacionCambiosLabel">
					<i class="fas fa-exclamation-triangle"></i> Confirmar modificación de invitado
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="alert alert-warning">
					<h6><i class="fas fa-info-circle"></i> Impacto de la modificación</h6>
					<p>Los cambios que está realizando pueden afectar:</p>
					<ul>
						<li><strong>Valor total de la reserva:</strong> Modificar tipo de invitado, beneficios o estado puede
							cambiar el costo total</li>
						<li><strong>Número de invitados:</strong> Cambios en el estado pueden afectar el conteo de invitados activos
						</li>
						<li><strong>Cálculos de beneficios:</strong> Modificar beneficiarios menores de 25, hijos o cupos puede
							alterar descuentos</li>
						<li><strong>Disponibilidad de cupos:</strong> Los cambios pueden afectar la disponibilidad en la reserva
						</li>
					</ul>
				</div>

				<div class="alert alert-info d-none">
					<h6><i class="fas fa-calculator"></i> Recálculo automático</h6>
					<p class="d-none">Si marca la opción "¿Actualizar valor de la reserva?", el sistema recalculará automáticamente:</p>
					<ul>
						<li>El valor total de la reserva</li>
						<li>Los descuentos aplicables</li>
						<li>El conteo de invitados por categoría</li>
					</ul>
				</div>

				<div class="form-check mt-3">
					<input class="form-check-input" type="checkbox" id="confirmarCambios" required>
					<label class="form-check-label fw-bold text-danger" for="confirmarCambios">
						Confirmo que entiendo que estos cambios pueden modificar el valor total de la reserva y el número de
						invitados
					</label>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
					<i class="fas fa-times"></i> Cancelar
				</button>
				<button type="button" class="btn btn-warning" id="confirmarGuardar" disabled>
					<i class="fas fa-save"></i> Confirmar y Guardar
				</button>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		const form = document.querySelector('form[data-bs-toggle="validator"]');
		const modalConfirmacion = new bootstrap.Modal(document.getElementById('modalConfirmacionCambios'));
		const checkboxConfirmar = document.getElementById('confirmarCambios');
		const btnConfirmarGuardar = document.getElementById('confirmarGuardar');

		// Elementos para la dependencia de tipo de invitado
		const estadoInvitado = document.getElementById('invitadoReserva_estado_invitado');
		const tipoInvitado = document.getElementById('invitado_tipo'); // Campo oculto
		const tipoInvitadoDisplay = document.getElementById('invitado_tipo_display'); // Select visible

		let formSubmitPending = false;

		// Función para actualizar el tipo de invitado basado en el estado
		function actualizarTipoInvitado() {
			const estadoValue = estadoInvitado.value;
			let tipoValue = '';

			if (estadoValue === 'A' || estadoValue === 'S') {
				// Si es Socio (A) o Cosocio (S), el tipo debe ser 1 (Socio)
				tipoValue = '1';
			} else if (estadoValue === 'P') {
				// Si es Invitado (P), el tipo debe ser 2 (Invitado)
				tipoValue = '2';
			}

			// Actualizar ambos campos
			tipoInvitado.value = tipoValue;
			tipoInvitadoDisplay.value = tipoValue;
		}

		// Evento para cuando cambie el estado del invitado
		estadoInvitado.addEventListener('change', actualizarTipoInvitado);

		// Ejecutar la función al cargar la página para establecer el valor inicial
		actualizarTipoInvitado();

		// Evento para el checkbox de confirmación
		checkboxConfirmar.addEventListener('change', function() {
			btnConfirmarGuardar.disabled = !this.checked;
		});

		// Evento para confirmar y guardar
		btnConfirmarGuardar.addEventListener('click', function() {
			modalConfirmacion.hide();
			formSubmitPending = true;
			form.submit();
		});

		// Interceptar el envío del formulario
		form.addEventListener('submit', function(e) {
			// Si ya se confirmó, permitir el envío
			if (formSubmitPending) {
				return true;
			}

			// Prevenir el envío y mostrar modal de confirmación
			e.preventDefault();

			// Resetear el modal
			checkboxConfirmar.checked = false;
			btnConfirmarGuardar.disabled = true;

			// Mostrar el modal
			modalConfirmacion.show();
		});

		// Limpiar estado cuando se cierra el modal
		document.getElementById('modalConfirmacionCambios').addEventListener('hidden.bs.modal', function() {
			formSubmitPending = false;
			checkboxConfirmar.checked = false;
			btnConfirmarGuardar.disabled = true;
		});
	});
</script>