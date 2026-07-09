<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<div class="content-dashboard">
		<form enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>" data-toggle="">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->info_pagina_id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->info_pagina_id; ?>" />
			<?php } ?>

			<a id="redes" name="redes"></a>
			<div class="div-dashboard d-none">
				<h2>
					<img src="/skins/administracion/images/redessociales.png"> Redes Sociales
				</h2>
				<div align="center" class="pading-dashboard">
					<br>
					<div class="row">
						<div class="col-6 form-group">
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono fondo-facebook"><i class="fab fa-facebook-f"></i></span>
								</div>
								<input type="text" value="<?= $this->content->info_pagina_facebook; ?>" name="info_pagina_facebook"
									id="info_pagina_facebook" class="form-control">
							</div>
						</div>
						<div class="col-6 form-group">
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono fondo-instagram"><i class="fab fa-instagram"></i></span>
								</div>
								<input type="text" value="<?= $this->content->info_pagina_instagram; ?>" name="info_pagina_instagram"
									id="info_pagina_instagram" class="form-control">
							</div>
						</div>
						<div class="col-6 form-group">
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono fondo-twitter"><i class="fab fa-twitter"></i></span>
								</div>
								<input type="text" value="<?= $this->content->info_pagina_twitter; ?>" name="info_pagina_twitter"
									id="info_pagina_twitter" class="form-control">
							</div>
						</div>
						<div class="col-6 form-group">
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono fondo-pinterest"><i class="fab fa-pinterest"></i></span>
								</div>
								<input type="text" value="<?= $this->content->info_pagina_pinterest; ?>" name="info_pagina_pinterest"
									id="info_pagina_pinterest" class="form-control">
							</div>
						</div>
						<div class="col-6 form-group">
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono fondo-youtube"><i class="fab fa-youtube"></i></span>
								</div>
								<input type="text" value="<?= $this->content->info_pagina_youtube; ?>" name="info_pagina_youtube"
									id="info_pagina_youtube" class="form-control">
							</div>
						</div>
						<div class="col-6 form-group">
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono fondo-flickr"><i class="fab fa-flickr"></i></span>
								</div>
								<input type="text" value="<?= $this->content->info_pagina_flickr; ?>" name="info_pagina_flickr"
									id="info_pagina_flickr" class="form-control">
							</div>
						</div>
						<div class="col-6 form-group">
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono fondo-linkedin"><i class="fab fa-linkedin"></i></span>
								</div>
								<input type="text" value="<?= $this->content->info_pagina_linkedin; ?>" name="info_pagina_linkedin"
									id="info_pagina_linkedin" class="form-control">
							</div>
						</div>
						<div class="col-6 form-group">
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono fondo-google"><i class="fab fa-google-plus-g"></i></span>
								</div>
								<input type="text" value="<?= $this->content->info_pagina_google; ?>" name="info_pagina_google"
									id="info_pagina_google" class="form-control">
							</div>
						</div>
						<div class="col-6 form-group">
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono fondo-whatsapp"><i class="fab fa-whatsapp"></i></span>
								</div>
								<input type="text" value="<?= $this->content->info_pagina_whatsapp; ?>" name="info_pagina_whatsapp"
									id="info_pagina_whatsapp" class="form-control">
							</div>
						</div>
					</div>
				</div>
			</div>
			<a id="favicon" name="favicon"></a>
			<div class="div-dashboard d-none">
				<h2>
					<img src="/skins/administracion/images/redessociales.png"> Favicon
				</h2>
				<div align="center" class="pading-dashboard">
					<br>
					<div class="row">
						<div class="col-12 form-group">
							<input type="file" name="info_pagina_favicon" id="info_pagina_favicon" class="form-control  file-image"
								data-buttonName="btn-primary" accept="image/ico" <?php if (!$this->content->info_pagina_favicon) {
																																	} ?>>
							<div class="help-block with-errors"></div>
							<?php if ($this->content->info_pagina_favicon) { ?>
								<div id="imagen_info_pagina_favicon">
									<img src="/images/<?= $this->content->info_pagina_favicon; ?>"
										class="img-thumbnail thumbnail-administrator" />
									<div><button class="btn btn-danger btn-sm" type="button"
											onclick="eliminarImagen('info_pagina_favicon','<?php echo $this->route . "/deleteimage"; ?>')"><i
												class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
								</div>
							<?php } ?>
						</div>
						<div class="col-12 text-center">

							<div class="alert alert-warning" role="alert">
								Tamaño de la imagen 32x32 px
							</div>


						</div>

					</div>
				</div>
			</div>

			<a id="contactenos" name="contactenos"></a>
			<div class="div-dashboard d-none">
				<h2>
					<img src="/skins/administracion/images/informaciondecotactenos.png"> Información de Contáctenos
				</h2>
				<div class="pading-dashboard">
					<br>
					<div class="row">
						<div class="col-4 form-group">
							<label for="info_pagina_telefono" class="control-label">Teléfonos:</label>
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono fondo-telefono"><i class="fas fa-phone"></i></span>
								</div>
								<textarea name="info_pagina_telefono" id="info_pagina_telefono" class="form-control"
									rows="2"><?= $this->content->info_pagina_telefono; ?></textarea>
							</div>
						</div>
						<div class="col-4 form-group">
							<label for="info_pagina_correos_contacto" class="form-label">Correo Contacto:</label>
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono fondo-correo"><i class="fas fa-envelope"></i></span>
								</div>
								<textarea name="info_pagina_correos_contacto" id="info_pagina_correos_contacto" class="form-control"
									rows="2"><?= $this->content->info_pagina_correos_contacto; ?></textarea>
							</div>
						</div>
						<div class="col-4 form-group">
							<label for="info_pagina_direccion_contacto" class="form-label">Dirección:</label>
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono fondo-direccion"><i class="fas fa-map-marked-alt"></i></span>
								</div>
								<textarea name="info_pagina_direccion_contacto" id="info_pagina_direccion_contacto" class="form-control"
									rows="2"><?= $this->content->info_pagina_direccion_contacto; ?></textarea>
							</div>
						</div>
						<div class="col-6 form-group">
							<label for="info_pagina_informacion_contacto" class="form-label">Información Contacto:</label>
							<textarea name="info_pagina_informacion_contacto" id="info_pagina_informacion_contacto"
								class="form-control tinyeditor"
								rows="10"><?= $this->content->info_pagina_informacion_contacto; ?></textarea>
						</div>
						<div class="col-6 form-group">
							<label for="info_pagina_informacion_contacto_footer" class="control-label">Información Contacto
								Footer:</label>
							<textarea name="info_pagina_informacion_contacto_footer" id="info_pagina_informacion_contacto_footer"
								class="form-control tinyeditor"
								rows="10"><?= $this->content->info_pagina_informacion_contacto_footer; ?></textarea>
						</div>
					</div>
				</div>
			</div>
			<a id="configcorreo" name="configcorreo"></a>
			<div class="div-dashboard">
				<h2>
					<i class="fas fa-envelope"></i> Configuración Envio Correo
				</h2>
				<div class="pading-dashboard">
					<div class="content-table mb-3">
						<table class=" table table-striped  table-hover table-administrator text-left">
							<tbody>
								<tr>
									<th scope="row">Host</th>
									<td>
										<input class="form-control" type="text" name="info_pagina_host" id="info_pagina_host"
											value="<?= $this->content->info_pagina_host; ?>" required>
									</td>
									<th scope="row">Port</th>
									<td>
										<input class="form-control" type="number" name="info_pagina_port" id="info_pagina_port"
											value="<?= $this->content->info_pagina_port; ?>" required>

									</td>
								</tr>
								<tr>
									<th scope="row">Username</th>
									<td>
										<input class="form-control" type="text" name="info_pagina_username" id="info_pagina_username"
											value="<?= $this->content->info_pagina_username; ?>" required>

									</td>
									<th scope="row">Password</th>
									<td>
										<input class="form-control" type="password" name="info_pagina_password" id="info_pagina_password"
											value="<?= $this->content->info_pagina_password; ?>" required>

									</td>
								</tr>
								<tr>
									<th scope="row">Correo remitente</th>
									<td>
										<input class="form-control" type="text" name="info_pagina_correo_remitente"
											id="info_pagina_correo_remitente" value="<?= $this->content->info_pagina_correo_remitente; ?>"
											required>

									</td>
									<th scope="row">Nombre remitente</th>
									<td>
										<input class="form-control" type="text" name="info_pagina_nombre_remitente"
											id="info_pagina_nombre_remitente" value="<?= $this->content->info_pagina_nombre_remitente; ?>"
											required>

									</td>
								</tr>
								<tr>
									<th scope="row">Correo(s) oculto</th>
									<td>
										<input class="form-control" type="text" name="info_pagina_correo_oculto"
											id="info_pagina_correo_oculto" value="<?= $this->content->info_pagina_correo_oculto; ?>">
									</td>
									<th scope="row">SMTP</th>
									<td>
										<input class="form-control" type="text" name="info_pagina_smtp"
											id="info_pagina_smtp" value="<?= $this->content->info_pagina_smtp; ?>">
									</td>
								</tr>
								<tr>
									<th scope="row">Nivel de debug</th>
									<td>
										<input class="form-control" type="text" name="info_pagina_debug"
											id="info_pagina_debug" value="<?= $this->content->info_pagina_debug; ?>">
									</td>
									<th scope="row">Asunto del correo</th>
									<td>
										<input class="form-control" type="text" name="info_pagina_asunto_correo"
											id="info_pagina_asunto_correo" value="<?= $this->content->info_pagina_asunto_correo; ?>">
									</td>
								</tr>
								<tr>
									<th scope="row" colspan="4" style="background-color: #f8f9fa;">
										<strong>Probar Configuración</strong>
									</th>
								</tr>
								<tr>
									<th scope="row">Correo de prueba</th>
									<td>
										<input class="form-control" type="email" name="correo_prueba" id="correo_prueba"
											placeholder="ejemplo@correo.com">
										<small class="form-text text-muted">Ingresa el correo donde quieres recibir el email de prueba</small>
									</td>
									<td colspan="2">
										<div class="d-flex flex-column align-items-center justify-content-center h-100">
											<button type="button" class="btn btn-info" id="btnProbarCorreo" onclick="probarEnvioCorreo()">
												<i class="fas fa-paper-plane"></i> Probar Envío de Correo
											</button>
											<div id="resultadoPrueba" style="margin-top: 15px;"></div>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<a id="maps" name="maps"></a>
			<div class="div-dashboard d-none">
				<h2>
					<img src="/skins/administracion/images/googlemaps.png"> Google Maps
				</h2>
				<div class="pading-dashboard">
					<div class="row">
						<div class="col-4 form-group">
							<label for="info_pagina_latitud" class="control-label">Latitud:</label>
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono-grande fondo-mapa-latitud"><img
											src="/skins/administracion/images/mapa-latitud.png"></span>
								</div>
								<input type="text" value="<?= $this->content->info_pagina_latitud; ?>" name="info_pagina_latitud"
									id="info_pagina_latitud" class="form-control">
							</div>
						</div>
						<div class="col-4 form-group">
							<label for="info_pagina_longitud" class="control-label">Longitud:</label>
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono-grande fondo-mapa-longitud"><img
											src="/skins/administracion/images/mapa-longitud.png"></span>
								</div>
								<input type="text" value="<?= $this->content->info_pagina_longitud; ?>" name="info_pagina_longitud"
									id="info_pagina_longitud" class="form-control">
							</div>
						</div>
						<div class="col-4 form-group">
							<label for="info_pagina_zoom" class="control-label">Zoom:</label>
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono-grande fondo-mapa-zoom"><img
											src="/skins/administracion/images/mapa-zoom.png"></span>
								</div>
								<input type="text" value="<?= $this->content->info_pagina_zoom; ?>" name="info_pagina_zoom"
									id="info_pagina_zoom" class="form-control">
							</div>
						</div>
					</div>
				</div>
			</div>

			<a id="seo" name="seo"></a>
			<div class="div-dashboard d-none">
				<h2>
					<img src="/skins/administracion/images/seo.png"> Archivo SEO
				</h2>
				<div class="pading-dashboard">
					<div class="row">
						<div class="col-4 form-group">
							<h5> Descripción: </h5>
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono-grande fondo-descripcion"><img
											src="/skins/administracion/images/descripcion.png" width="50px;"></span>
								</div>
								<textarea name="info_pagina_descripcion" id="info_pagina_descripcion" class="form-control"
									rows="6"><?= $this->content->info_pagina_descripcion; ?></textarea>
							</div>
						</div>
						<div class="col-4 form-group">
							<h5> Tags: </h5>
							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono-grande fondo-tags"><img
											src="/skins/administracion/images/tags.png" width="50px;"></span>
								</div>
								<textarea name="info_pagina_tags" id="info_pagina_tags" class="form-control"
									rows="6"><?= $this->content->info_pagina_tags; ?></textarea>
							</div>
						</div>
						<div class="col-4">
							<h5> Archivos SEO: </h5>
							<div class="contenedor-informacion">
								<div class="contenido">
									<input type="file" name="info_pagina_robot" id="info_pagina_robot" class="form-control  file-robot"
										data-buttonName="btn-primary" onchange="validardocumento('info_pagina_robot');" accept="text/plain">
									<br>
									<input type="file" name="info_pagina_sitemap" id="info_pagina_sitemap"
										class="form-control  file-sitemap" data-buttonName="btn-primary"
										onchange="validardocumento('info_pagina_sitemap');" accept="text/xml">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<a id="politicadatos" name="politicadatos"></a>
			<div class="div-dashboard d-none">
				<h2>
					<img src="/skins/administracion/images/seo.png"> Política de Manejo de Datos
				</h2>
				<div class="pading-dashboard">
					<div class="row">
						<div class="col-12">
							<div><label>Título</label>

								<div class="input-group-prepend ">
								</div>
								<input type="text" name="info_pagina_titulo_legal" id="info_pagina_titulo_legal" class="form-control"
									value="<?= $this->content->info_pagina_titulo_legal; ?>">
							</div>
							<div style="margin-top:2%;">
								<label>Descripción</label>
								<textarea name="info_pagina_descripcion_legal" id="info_pagina_descripcion_legal"
									class="form-control tinyeditor"
									rows="10"><?= $this->content->info_pagina_descripcion_legal; ?></textarea>

							</div>

						</div>
						<div></div>
					</div>
				</div>
			</div>
			<a id="scripts" name="scripts"></a>
			<div class="div-dashboard d-none">
				<h2>
					<img src="/skins/administracion/images/logo-scripts.png"> Scripts Head
				</h2>
				<br>
				<div class="pading-dashboard">
					<div class="row">
						<div class="col-12 form-group">

							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono-grande fondo-scripts"><img
											src="/skins/administracion/images/scripts.png" width="50px;"></span>
								</div>
								<textarea name="info_pagina_scripts" id="info_pagina_scripts" class="form-control"
									rows="10"><?= $this->content->info_pagina_scripts; ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>

			<a id="metricas" name="metricas"></a>
			<div class="div-dashboard d-none">
				<h2>
					<img src="/skins/administracion/images/logo-scripts.png"> Script métricas
				</h2>
				<br>
				<div class="pading-dashboard">
					<div class="row">
						<div class="col-12 form-group">

							<div class="input-group">
								<div class="input-group-prepend ">
									<span class="input-group-text input-icono-grande fondo-scripts"><i
											class="fas fa-chart-pie icono-metricas"></i></span>
								</div>
								<textarea name="info_pagina_metricas" id="info_pagina_metricas" class="form-control"
									rows="10"><?= $this->content->info_pagina_metricas; ?></textarea>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="botones-acciones">
				<button class="btn btn-guardar" type="submit">Guardar</button>
				<a href="/administracion/panel" class="btn btn-cancelar">Cancelar</a>
			</div>
		</form>
	</div>
</div>

<script>
	function probarEnvioCorreo() {
		// Obtener valores de los campos
		const host = document.getElementById('info_pagina_host').value;
		const port = document.getElementById('info_pagina_port').value;
		const username = document.getElementById('info_pagina_username').value;
		const password = document.getElementById('info_pagina_password').value;
		const correoRemitente = document.getElementById('info_pagina_correo_remitente').value;
		const nombreRemitente = document.getElementById('info_pagina_nombre_remitente').value;
		const smtp = document.getElementById('info_pagina_smtp').value;
		const debug = document.getElementById('info_pagina_debug').value;
		const correoDestino = document.getElementById('correo_prueba').value;
		const asuntoCorreo = document.getElementById('info_pagina_asunto_correo').value;

		// Validar que el correo de prueba esté ingresado
		if (!correoDestino || correoDestino.trim() === '') {
			mostrarResultado('Por favor, ingresa un correo de destino para la prueba', 'error');
			return;
		}

		// Validar formato de email
		const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
		if (!emailRegex.test(correoDestino)) {
			mostrarResultado('Por favor, ingresa un correo válido', 'error');
			return;
		}

		// Validar campos requeridos
		if (!host || !port || !username || !password || !correoRemitente || !nombreRemitente) {
			mostrarResultado('Por favor, completa todos los campos requeridos de configuración', 'error');
			return;
		}

		// Deshabilitar botón y mostrar mensaje de carga
		const btn = document.getElementById('btnProbarCorreo');
		const btnTextoOriginal = btn.innerHTML;
		btn.disabled = true;
		btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';

		mostrarResultado('Enviando correo de prueba...', 'info');

		// Preparar datos para enviar
		const formData = new FormData();
		formData.append('host', host);
		formData.append('port', port);
		formData.append('username', username);
		formData.append('password', password);
		formData.append('correo_remitente', correoRemitente);
		formData.append('nombre_remitente', nombreRemitente);
		formData.append('smtp', smtp);
		formData.append('debug', debug);
		formData.append('correo_destino', correoDestino);
		formData.append('asunto_correo', asuntoCorreo);

		// Realizar la petición AJAX
		fetch('/administracion/informacion/pruebaenvio', {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				btn.disabled = false;
				btn.innerHTML = btnTextoOriginal;

				if (data.success) {
					mostrarResultado(data.mensaje, 'success');
				} else {
					mostrarResultado(data.mensaje, 'error');
				}
			})
			.catch(error => {
				btn.disabled = false;
				btn.innerHTML = btnTextoOriginal;
				mostrarResultado('Error en la solicitud: ' + error.message, 'error');
			});
	}

	function mostrarResultado(mensaje, tipo) {
		const resultadoDiv = document.getElementById('resultadoPrueba');
		let claseAlerta = '';
		let icono = '';

		switch (tipo) {
			case 'success':
				claseAlerta = 'alert-success';
				icono = '<i class="fas fa-check-circle"></i> ';
				break;
			case 'error':
				claseAlerta = 'alert-danger';
				icono = '<i class="fas fa-times-circle"></i> ';
				break;
			case 'info':
				claseAlerta = 'alert-info';
				icono = '<i class="fas fa-info-circle"></i> ';
				break;
			default:
				claseAlerta = 'alert-secondary';
		}

		resultadoDiv.innerHTML = `
		<div class="alert ${claseAlerta} alert-dismissible fade show" role="alert">
			${icono}${mensaje}
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	`;
	}
</script>