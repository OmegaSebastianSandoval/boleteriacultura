<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>"
		data-toggle="validator">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->evento_id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->evento_id; ?>" />
			<?php } ?>
			<div class="row">
				<div class="col-lg-6">
					<div class="row">
						<div class="col-md-12 col-lg-6 form-group">
							<label for="evento_titulo" class="control-label">Nombre evento</label>
							<label class="input-group">
								<input type="text" value="<?= $this->content->evento_titulo; ?>" name="evento_titulo" id="evento_titulo"
									class="form-control" required>
							</label>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-lg-6 form-group">
							<label for="evento_fecha" class="control-label">Fecha del evento</label>
							<label class="input-group">
								<input type="date" value="<?= $this->content->evento_fecha; ?>" name="evento_fecha" id="evento_fecha"
									class="form-control">
							</label>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-lg-6 form-group">
							<label for="evento_fecha_inicio" class="control-label">Fecha inicio evento</label>
							<label class="input-group">
								<input type="text" value="<?= $this->content->evento_fecha_inicio; ?>" name="evento_fecha_inicio"
									id="evento_fecha_inicio" class="form-control datetime-picker">
							</label>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-lg-6 form-group">
							<label for="evento_fecha_fin" class="control-label">Fecha fin evento</label>
							<label class="input-group">
								<input type="text" value="<?= $this->content->evento_fecha_fin; ?>" name="evento_fecha_fin"
									id="evento_fecha_fin" class="form-control datetime-picker">
							</label>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-lg-6 form-group">
							<label for="evento_fecha_apertura_reserva" class="control-label">Fecha apertura reserva</label>
							<label class="input-group">
								<input type="text" value="<?= $this->content->evento_fecha_apertura_reserva; ?>"
									name="evento_fecha_apertura_reserva" id="evento_fecha_apertura_reserva"
									class="form-control datetime-picker">
							</label>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-lg-6 form-group">
							<label for="evento_fecha_cierre_reserva" class="control-label">Fecha cierre reserva</label>
							<label class="input-group">
								<input type="text" value="<?= $this->content->evento_fecha_cierre_reserva; ?>"
									name="evento_fecha_cierre_reserva" id="evento_fecha_cierre_reserva" class="form-control datetime-picker"
									onchange="validarFechas()">
							</label>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-lg-6 form-group">
							<label for="evento_lugar" class="control-label">Lugar evento</label>
							<label class="input-group">
								<input type="text" value="<?= $this->content->evento_lugar; ?>" name="evento_lugar" id="evento_lugar"
									class="form-control" required>
							</label>
							<div class="help-block with-errors"></div>
						</div>

						<div class="col-lg-6 form-group">
							<label for="evento_cupo_maximo" class="control-label">Evento cupo máximo</label>
							<label class="input-group">
								<input type="text" value="<?= $this->content->evento_cupo_maximo; ?>" name="evento_cupo_maximo"
									id="evento_cupo_maximo" class="form-control">
							</label>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-lg-6 form-group">
							<label for="evento_invitados_socio" class="control-label">Invitados por socio</label>
							<label class="input-group">
								<input type="text" value="<?= $this->content->evento_invitados_socio; ?>" name="evento_invitados_socio"
									id="evento_invitados_socio" class="form-control">
							</label>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-lg-6 form-group">
							<label for="evento_colorfondo" class="control-label">Color de fondo</label>
							<label class="input-group">
								<input type="text" value="<?= $this->content->evento_colorfondo; ?>" name="evento_colorfondo"
									id="evento_colorfondo" class="form-control colorpicker">
							</label>
							<div class="help-block with-errors"></div>
						</div>

						<div class="col-4 form-group d-grid">
							<label class="control-label">Cuotas (Si, No)</label>
							<input type="checkbox" name="evento_cuotas" id="evento_cuotas" value="1" class="form-control switch-form " <?php if ($this->getObjectVariable($this->content, 'evento_cuotas') == 1) {
																																																														echo "checked";
																																																													} ?>></input>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-4 form-group d-grid">
							<label class="control-label">Datafono (Si, No)</label>
							<input type="checkbox" name="evento_datafono" value="1" class="form-control switch-form " <?php if ($this->getObjectVariable($this->content, 'evento_datafono') == 1) {
																																																					echo "checked";
																																																				} ?>></input>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-4 form-group" id="campo_max_cuotas" style="display: <?php echo ($this->getObjectVariable($this->content, 'evento_cuotas') == 1) ? 'block' : 'none'; ?>;">
							<label for="evento_max_cuotas" class="control-label">M&aacute;ximo de cuotas</label>
							<label class="input-group">
								<input type="text" value="<?= $this->content->evento_max_cuotas; ?>" name="evento_max_cuotas"
									id="evento_max_cuotas" class="form-control">
							</label>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-md-12 form-group d-none">
							<label for="evento_descripcion" class="form-label">Descripción evento</label>
							<textarea name="evento_descripcion" id="evento_descripcion" class="form-control tinyeditor"
								rows="10"><?= $this->content->evento_descripcion; ?></textarea>
							<div class="help-block with-errors"></div>
						</div>
						<div class="col-md-12 form-group d-none">
							<label for="evento_politica_reserva" class="form-label">Política reserva evento</label>
							<textarea name="evento_politica_reserva" id="evento_politica_reserva" class="form-control tinyeditor"
								rows="10"><?= $this->content->evento_politica_reserva; ?></textarea>
							<div class="help-block with-errors"></div>
						</div>
					</div>
				</div>
				
				<div class="col-lg-6">
					<div class="row">
						<div class="col-md-6 form-group">
							<label for="evento_imagen_evento">Imagen evento</label>
							<input type="file" name="evento_imagen_evento" id="evento_imagen_evento" class="form-control  file-image"
								data-buttonName="btn-primary" accept="image/gif, image/jpg, image/jpeg, image/png" <?php if (!$this->content->evento_id) {
																																																			echo 'required';
																																																		} ?>>
							<div class="help-block with-errors"></div>
							<?php if ($this->content->evento_imagen_evento) { ?>
								<div id="imagen_evento_imagen_evento">
									<img src="/images/<?= $this->content->evento_imagen_evento; ?>"
										class="img-thumbnail thumbnail-administrator" />
									<div><button class="btn btn-danger btn-sm" type="button"
											onclick="eliminarImagen('evento_imagen_evento','<?php echo $this->route . "/deleteimage"; ?>')"><i
												class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
								</div>
							<?php } ?>
						</div>
						<div class="col-md-6 form-group">
							<label for="evento_imagenfondo">Imagen evento</label>
							<input type="file" name="evento_imagenfondo" id="evento_imagenfondo" class="form-control  file-image"
								data-buttonName="btn-primary" accept="image/gif, image/jpg, image/jpeg, image/png" <?php if (!$this->content->evento_id) {
																																																			echo 'required';
																																																		} ?>>
							<div class="help-block with-errors"></div>
							<?php if ($this->content->evento_imagenfondo) { ?>
								<div id="imagen_evento_imagenfondo">
									<img src="/images/<?= $this->content->evento_imagenfondo; ?>"
										class="img-thumbnail thumbnail-administrator" />
									<div><button class="btn btn-danger btn-sm" type="button"
											onclick="eliminarImagen('evento_imagenfondo','<?php echo $this->route . "/deleteimage"; ?>')"><i
												class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
								</div>
							<?php } ?>
						</div>
						<div class="col-md-6 form-group">
							<label for="evento_imagenfondo_home">Imagen fondo home</label>
							<input type="file" name="evento_imagenfondo_home" id="evento_imagenfondo_home" class="form-control  file-image"
								data-buttonName="btn-primary" accept="image/gif, image/jpg, image/jpeg, image/png" <?php if (!$this->content->evento_id) {
																																																			echo 'required';
																																																		} ?>>
							<div class="help-block with-errors"></div>
							<?php if ($this->content->evento_imagenfondo_home) { ?>
								<div id="imagen_evento_imagenfondo_home">
									<img src="/images/<?= $this->content->evento_imagenfondo_home; ?>"
										class="img-thumbnail thumbnail-administrator" />
									<div><button class="btn btn-danger btn-sm" type="button"
											onclick="eliminarImagen('evento_imagenfondo_home','<?php echo $this->route . "/deleteimage"; ?>')"><i
												class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
								</div>
							<?php } ?>
						</div>

						<div class="col-md-6 form-group">
							<label for="evento_imagenfondo_home_responsive">Imagen fondo home responsive</label>
							<input type="file" name="evento_imagenfondo_home_responsive" id="evento_imagenfondo_home_responsive" class="form-control  file-image"
								data-buttonName="btn-primary" accept="image/gif, image/jpg, image/jpeg, image/png" <?php if (!$this->content->evento_id) {
																																																			echo 'required';
																																																		} ?>>
							<div class="help-block with-errors"></div>
							<?php if ($this->content->evento_imagenfondo_home_responsive) { ?>
								<div id="imagen_evento_imagenfondo_home_responsive">
									<img src="/images/<?= $this->content->evento_imagenfondo_home_responsive; ?>"
										class="img-thumbnail thumbnail-administrator" />
									<div><button class="btn btn-danger btn-sm" type="button"
											onclick="eliminarImagen('evento_imagenfondo_home_responsive','<?php echo $this->route . "/deleteimage"; ?>')"><i
												class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
								</div>
							<?php } ?>
						</div>

						<div class="col-md-6 form-group">
							<label for="evento_imagenfondo_login">Imagen fondo login</label>
							<input type="file" name="evento_imagenfondo_login" id="evento_imagenfondo_login" class="form-control  file-image"
								data-buttonName="btn-primary" accept="image/gif, image/jpg, image/jpeg, image/png" <?php if (!$this->content->evento_id) {
																																																			echo 'required';
																																																		} ?>>
							<div class="help-block with-errors"></div>
							<?php if ($this->content->evento_imagenfondo_login) { ?>
								<div id="imagen_evento_imagenfondo_login">
									<img src="/images/<?= $this->content->evento_imagenfondo_login; ?>"
										class="img-thumbnail thumbnail-administrator" />
									<div><button class="btn btn-danger btn-sm" type="button"
											onclick="eliminarImagen('evento_imagenfondo_login','<?php echo $this->route . "/deleteimage"; ?>')"><i
												class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
								</div>
							<?php } ?>
						</div>

						<div class="col-md-6 form-group">
							<label for="evento_imagenfondo_login_responsive">Imagen fondo login responsive</label>
							<input type="file" name="evento_imagenfondo_login_responsive" id="evento_imagenfondo_login_responsive" class="form-control  file-image"
								data-buttonName="btn-primary" accept="image/gif, image/jpg, image/jpeg, image/png" <?php if (!$this->content->evento_id) {
																																																			echo 'required';
																																																		} ?>>
							<div class="help-block with-errors"></div>
							<?php if ($this->content->evento_imagenfondo_login_responsive) { ?>
								<div id="imagen_evento_imagenfondo_login_responsive">
									<img src="/images/<?= $this->content->evento_imagenfondo_login_responsive; ?>"
										class="img-thumbnail thumbnail-administrator" />
									<div><button class="btn btn-danger btn-sm" type="button"
											onclick="eliminarImagen('evento_imagenfondo_login_responsive','<?php echo $this->route . "/deleteimage"; ?>')"><i
												class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="botones-acciones">
				<button class="btn btn-guardar" type="submit">Guardar</button>
				<a href="<?php echo $this->route; ?>" class="btn btn-cancelar">Cancelar</a>
			</div>
	</form>
</div>

<script>
	function validarFechas() {
		var fecha1 = document.getElementById("evento_fecha_cierre_reserva").value;
		var fecha2 = document.getElementById("evento_fecha_fin").value;
		var fechaCampo1 = new Date(fecha1);
		var fechaCampo2 = new Date(fecha2);

		if (fechaCampo1 > fechaCampo2) {
			alert("La fecha de cierre de reservas no puede ser mayor a la de finalización de evento.");
			var campo1 = document.getElementById("evento_fecha_cierre_reserva");
			campo1.value = fecha2;
			return false;
		}
		return true;
	}

	// Controlar visibilidad del campo evento_max_cuotas
	document.addEventListener('DOMContentLoaded', function() {
		const checkboxCuotas = $('#evento_cuotas');
		const campoMaxCuotas = $('#campo_max_cuotas');
		const inputMaxCuotas = $('#evento_max_cuotas');

		function toggleMaxCuotas() {
			if (checkboxCuotas.prop('checked')) {
				campoMaxCuotas.show();
			} else {
				campoMaxCuotas.hide();
				inputMaxCuotas.val('0');
			}
		}

		// Escuchar el evento específico de Bootstrap Switch
		checkboxCuotas.on('switchChange.bootstrapSwitch', function(event, state) {
			if (state) {
				campoMaxCuotas.show();
			} else {
				campoMaxCuotas.hide();
				inputMaxCuotas.val('0');
			}
		});
	});
</script>
<style>
	input:read-only {
		background-color: #f9f9f9;
	}
</style>