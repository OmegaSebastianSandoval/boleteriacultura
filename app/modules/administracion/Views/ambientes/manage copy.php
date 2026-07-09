<?php
function calcularCellSize($columnas, $filas)
{
	// Ancho y alto de referencia del contenedor (ajusta según tu diseño)
	$containerWidth = 820; // Ancho del contenedor en píxeles
	$containerHeight = 750; // Alto del contenedor en píxeles (ajusta según el modal o col-8)

	// Calcular el ancho de col-8 (8/12 del contenedor)
	$col8Width = $containerWidth * (8 / 12);

	// Calcular cellSize basado en el ancho (dividiendo por columnas)
	$cellSizeWidth = floor($col8Width / $columnas);

	// Calcular cellSize basado en el alto (dividiendo por filas)
	$cellSizeHeight = floor($containerHeight / $filas);

	// Tomar el menor de los dos para asegurar que el grid quepa en ambas dimensiones
	return min($cellSizeWidth, $cellSizeHeight);
}
?>

<?php
// Datos simulados para el ejemplo
$ambiente = ['columnas' => $this->content->ambiente_columnas, 'filas' => $this->content->ambiente_filas];

// Variables para resaltar mesa específica (usado en validación)
$destacarMesa = $_GET['destacar_mesa'] ?? null;
$modoValidacion = $_GET['modo'] ?? null;

if ($this->mesas) {
	$elementos = array_map(
		function ($el) {
			return (object) $el;
		},
		$this->mesas
	);
} else {
	$elementos = [];
}
?>

<?php if ($_GET["display"] == 1) { ?>
	<?php $pxCelda = calcularCellSize($this->content->ambiente_columnas, $this->content->ambiente_filas); ?>
	<style>
		header,
		#panel-botones,
		.panel-titulo,
		footer {
			display: none;
		}

		body {
			background: #EBF0F6;
			min-width: 100vw;
			overflow-x: hidden;
		}

		.grid {
			margin: 15px 15px !important;
		}

		#contenido_panel {
			width: 99% !important;
		}
	</style>

	<div class="row mx-auto mt-3">
		<div class="col-12">
			<div class="card">
				<span class="d-flex justify-content-center"
					style="font-size: 25px;"><?= $this->content->ambiente_nombre; ?></span>
			</div>
		</div>
		<div class="col-7">
			<div id="grid" class="grid" style="width: fit-content;"></div>
		</div>
		<div class="col-5 my-2">
			<?php
			// Contar mesas ocupadas y disponibles
			$mesasOcupadas = 0;
			$mesasDisponibles = 0;
			foreach ($this->mesasModal as $mesa) {
				if ($mesa->mesa_estado == 1) {
					$mesasOcupadas++;
				} else {
					$mesasDisponibles++;
				}
			}
			$totalMesas = $mesasOcupadas + $mesasDisponibles;
			?>

			<div class="text-center mb-3">
				<strong>Total de mesas: <?= $totalMesas ?></strong>
			</div>
			<canvas id="mesasChart" style="max-width: 400px; margin: 0 auto;"></canvas>

			<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
			<script>
				const ctx = document.getElementById('mesasChart').getContext('2d');
				new Chart(ctx, {
					type: 'doughnut',
					data: {
						labels: ['Ocupadas', 'Disponibles'],
						datasets: [{
							data: [<?= $mesasOcupadas ?>, <?= $mesasDisponibles ?>],
							backgroundColor: [
								'#ffc107', // Amarillo para ocupadas
								'#28a745' // Verde para disponibles
							],
							borderColor: [
								'#ffffff',
								'#ffffff'
							],
							borderWidth: 2
						}]
					},
					options: {
						responsive: true,
						plugins: {
							legend: {
								position: 'bottom',
								labels: {
									font: {
										size: 14
									}
								}
							},
							tooltip: {
								enabled: true
							}
						},
						cutout: '60%' // Hace el doughnut más delgado
					}
				});
			</script>

			<div class="nameMesa"></div>

		</div>
	</div>

<?php } else { ?>
	<?php $pxCelda = "25"; ?>

	<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
	<div class="container-fluid">
		<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>"
			data-toggle="validator">
			<div class="content-dashboard">
				<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
				<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
				<?php if ($this->content->ambiente_id) { ?>
					<input type="hidden" name="id" id="id" value="<?= $this->content->ambiente_id; ?>" />
				<?php } ?>
				<div class="row">
					<div class="col-2 form-group d-grid">
						<label class="control-label">Activo (Si, No)</label>
						<input type="checkbox" name="ambiente_estado" value="1" class="form-control switch-form " <?php if ($this->getObjectVariable($this->content, 'ambiente_estado') == 1) {
																																																				echo "checked";
																																																			} ?>></input>
						<div class="help-block with-errors"></div>
					</div>


					<!-- <div class="col-12 form-group">
					<label for="ambiente_evento" class="control-label">ambiente_evento</label>
					<label class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text input-icono  fondo-morado "><i class="fas fa-pencil-alt"></i></span>
						</div>
						<input type="text" value="<?= $this->content->ambiente_evento; ?>" name="ambiente_evento"
							id="ambiente_evento" class="form-control">
					</label>
					<div class="help-block with-errors"></div>
				</div> -->
					<div class="col-2 form-group">
						<label class="control-label">Piso</label>
						<label class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text input-icono  fondo-verde "><i class="far fa-list-alt"></i></span>
							</div>
							<select class="form-control" name="ambiente_piso">
								<option value="">Seleccione...</option>
								<?php foreach ($this->list_ambiente_piso as $key => $value) { ?>
									<option <?php if ($this->getObjectVariable($this->content, "ambiente_piso") == $key) {
														echo "selected";
													} ?>
										value="<?php echo $key; ?>" /> <?= $value; ?></option>
								<?php } ?>
							</select>
						</label>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-2 form-group">
						<label for="ambiente_nombre" class="control-label">Nombre</label>
						<label class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
							</div>
							<input type="text" value="<?= $this->content->ambiente_nombre; ?>" name="ambiente_nombre"
								id="ambiente_nombre" class="form-control">
						</label>
						<div class="help-block with-errors"></div>
					</div>
					<!-- <div class="col-3 form-group">
						<label for="ambiente_capacidad" class="control-label">Capacidad</label>
						<label class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text input-icono  fondo-azul-claro "><i class="fas fa-pencil-alt"></i></span>
							</div>
							<input type="text" value="<?= $this->content->ambiente_capacidad; ?>" name="ambiente_capacidad"
								id="ambiente_capacidad" class="form-control">
						</label>
						<div class="help-block with-errors"></div>
					</div> -->
					<div class="col-2 form-group">
						<label class="control-label">Categoria</label>
						<label class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text input-icono  fondo-verde-claro "><i class="far fa-list-alt"></i></span>
							</div>
							<select class="form-control" name="ambiente_categoria">
								<option value="">Seleccione...</option>
								<?php foreach ($this->list_ambiente_categoria as $key => $value) { ?>
									<option <?php if ($this->getObjectVariable($this->content, "ambiente_categoria") == $key) {
														echo "selected";
													} ?> value="<?php echo $key; ?>" /> <?= $value; ?></option>
								<?php } ?>
							</select>
						</label>
						<div class="help-block with-errors"></div>
					</div>

					<div class="col-3 form-group d-none">
						<label for="ambiente_imagen_disponible">Disponible</label>
						<input type="file" name="ambiente_imagen_disponible" id="ambiente_imagen_disponible"
							class="form-control  file-image" data-buttonName="btn-primary"
							accept="image/gif, image/jpg, image/jpeg, image/png">
						<div class="help-block with-errors"></div>
						<?php if ($this->content->ambiente_imagen_disponible) { ?>
							<div id="imagen_ambiente_imagen_disponible">
								<img src="/images/<?= $this->content->ambiente_imagen_disponible; ?>"
									class="img-thumbnail thumbnail-administrator" />
								<div><button class="btn btn-danger btn-sm" type="button"
										onclick="eliminarImagen('ambiente_imagen_disponible','<?php echo $this->route . "/deleteimage"; ?>')"><i
											class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
							</div>
						<?php } ?>
					</div>
					<div class="col-3 form-group  d-none">
						<label for="ambiente_imagen_pendiente">Pendiente</label>
						<input type="file" name="ambiente_imagen_pendiente" id="ambiente_imagen_pendiente"
							class="form-control  file-image" data-buttonName="btn-primary"
							accept="image/gif, image/jpg, image/jpeg, image/png">
						<div class="help-block with-errors"></div>
						<?php if ($this->content->ambiente_imagen_pendiente) { ?>
							<div id="imagen_ambiente_imagen_pendiente">
								<img src="/images/<?= $this->content->ambiente_imagen_pendiente; ?>"
									class="img-thumbnail thumbnail-administrator" />
								<div><button class="btn btn-danger btn-sm" type="button"
										onclick="eliminarImagen('ambiente_imagen_pendiente','<?php echo $this->route . "/deleteimage"; ?>')"><i
											class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
							</div>
						<?php } ?>
					</div>
					<div class="col-3 form-group  d-none">
						<label for="ambiente_imagen_ocupado">Ocupado</label>
						<input type="file" name="ambiente_imagen_ocupado" id="ambiente_imagen_ocupado"
							class="form-control  file-image" data-buttonName="btn-primary"
							accept="image/gif, image/jpg, image/jpeg, image/png">
						<div class="help-block with-errors"></div>
						<?php if ($this->content->ambiente_imagen_ocupado) { ?>
							<div id="imagen_ambiente_imagen_ocupado">
								<img src="/images/<?= $this->content->ambiente_imagen_ocupado; ?>"
									class="img-thumbnail thumbnail-administrator" />
								<div><button class="btn btn-danger btn-sm" type="button"
										onclick="eliminarImagen('ambiente_imagen_ocupado','<?php echo $this->route . "/deleteimage"; ?>')"><i
											class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
							</div>
						<?php } ?>
					</div>
					<div class="col-3 form-group  d-none">
						<label for="ambiente_imagen_ubicacion_en_piso">Ubicación en piso</label>
						<input type="file" name="ambiente_imagen_ubicacion_en_piso" id="ambiente_imagen_ubicacion_en_piso"
							class="form-control  file-image" data-buttonName="btn-primary"
							accept="image/gif, image/jpg, image/jpeg, image/png">
						<div class="help-block with-errors"></div>
						<?php if ($this->content->ambiente_imagen_ubicacion_en_piso) { ?>
							<div id="imagen_ambiente_imagen_ubicacion_en_piso">
								<img src="/images/<?= $this->content->ambiente_imagen_ubicacion_en_piso; ?>"
									class="img-thumbnail thumbnail-administrator" />
								<div><button class="btn btn-danger btn-sm" type="button"
										onclick="eliminarImagen('ambiente_imagen_ubicacion_en_piso','<?php echo $this->route . "/deleteimage"; ?>')"><i
											class="glyphicon glyphicon-remove"></i> Eliminar Imagen</button></div>
							</div>
						<?php } ?>
					</div>


					<div class="col-2 form-group">
						<label for="ambiente_filas" class="control-label">Filas</label>
						<label class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
							</div>
							<input type="text" value="<?= $this->content->ambiente_filas; ?>" name="ambiente_filas" id="ambiente_filas"
								class="form-control">
						</label>
						<div class="help-block with-errors"></div>
					</div>
					<div class="col-2 form-group">
						<label for="ambiente_columnas" class="control-label">Columnas</label>
						<label class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
							</div>
							<input type="text" value="<?= $this->content->ambiente_columnas; ?>" name="ambiente_columnas"
								id="ambiente_columnas" class="form-control">
						</label>
						<div class="help-block with-errors"></div>
					</div>

					<div class="col-2 form-group">
						<label for="ambiente_descuento" class="control-label">Descuento</label>
						<label class="input-group">
							<div class="input-group-prepend">
								<span class="input-group-text input-icono  fondo-cafe "><i class="fas fa-pencil-alt"></i></span>
							</div>
							<input type="number" value="<?= $this->content->ambiente_descuento; ?>" name="ambiente_descuento"
								id="ambiente_descuento" class="form-control" min="0" max="100" step="0.01"
								oninput="validarDescuento(this)">
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

	<div id="modal-nueva-mesa" class="modal-mesa-bg">
		<div class="modal-mesa">
			<div class="modal-mesa-header">
				<h3><i class="fas fa-plus-circle"></i> Agregar nueva mesa</h3>
				<button class="modal-mesa-close" onclick="cerrarModalMesa()">&times;</button>
			</div>
			<div class="modal-mesa-body">
				<div class="form-group">
					<label>Tipo</label>
					<select id="input_tipo" class="form-control" onchange="actualizarCapacidadDefault()">
						<option value="mesa">Mesa</option>
						<option value="puerta">Puerta</option>
						<option value="barra">Barra</option>
						<option value="pista">Pista</option>
						<option value="tarima">Tarima</option>
						<option value="banos">Baños</option>
						<option value="escalera">Escaleras</option>
						<option value="pantalla">Pantalla</option>
						<option value="terraza">Terraza</option>
						<option value="ventana">Ventana</option>
					</select>
				</div>
				<div class="form-group">
					<label>Código</label>
					<input id="input_codigo" type="text" class="form-control" value="">
				</div>
				<div class="form-group">
					<label>Nombre</label>
					<input id="input_nombre" type="text" class="form-control" value="Nueva">
				</div>
				<div class="form-row">
					<div class="form-group col">
						<label>Ancho</label>
						<input id="input_ancho" type="number" class="form-control" value="2" min="1" onchange="actualizarCapacidadDefault()">
					</div>
					<div class="form-group col">
						<label>Alto</label>
						<input id="input_alto" type="number" class="form-control" value="2" min="1" onchange="actualizarCapacidadDefault()">
					</div>
					<div class="form-group col">
						<label>Rotación</label>
						<input id="input_rotacion" type="number" class="form-control" value="0" step="1">
					</div>
				</div>
				<div class="form-group">
					<label>Capacidad</label>
					<input id="input_capacidad" type="number" class="form-control" min="1" step="1">

					<!-- <select id="input_capacidad" class="form-control">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="4">4</option>
						<option value="6">6</option>
						<option value="8">8</option>
						<option value="10">10</option>
					</select> -->
				</div>
				<div class="form-group">
					<label>Estado</label>
					<select id="input_estado" class="form-control">
						<option value="0">Libre</option>
						<option value="1">Ocupada</option>
						<!-- <option value="2">Reservada</option> -->
					</select>
				</div>
				<!-- Inputs ocultos para datos adicionales de la mesa -->
				<input type="hidden" id="mesa_capacidad">
				<input type="hidden" id="input_forma">
				<input type="hidden" id="input_activa">
				<input type="hidden" id="input_orden">
				<input type="hidden" id="input_imagen_disponible">
				<input type="hidden" id="input_imagen_pendiente">
				<input type="hidden" id="input_imagen_ocupada">
				<input type="hidden" id="input_pos_x">
				<input type="hidden" id="input_pos_y">
				<input type="hidden" id="input_imagen_ubicacion_en_ambiente">
				<input type="hidden" id="input_imagen_ubicacion_en_piso">
			</div>
			<div class="modal-mesa-footer">
				<button class="btn btn-primary" id="btn-modal-mesa-accion"><i class="fas fa-check"></i> Crear</button>
				<button class="btn btn-secondary" onclick="cerrarModalMesa()">Cancelar</button>
			</div>
		</div>
	</div>



	<h2 style="text-align:center;">Ambiente Interactivo</h2>
	<div class="d-flex justify-content-end gap-5 px-3">

		<div class=" ">
			<button class="btn btn-warning" onclick="agregarElemento()">Agregar nuevo elemento</button>
		</div>
		<div class=" d-none ">
			<button class="btn btn-primary" onclick="guardarCambios()">Guardar Cambios</button>
		</div>
	</div>
	<div class="p-4 w-100 overflow-scroll">
		<div id="grid" class="grid" style="width: fit-content;"></div>
	</div>
	<div class="container mb-5 d-flex justify-content-end d-none">
		<button class="btn btn-warning" onclick="agregarElemento()">Agregar nueva mesa</button>
	</div>
	<div class="container mb-5 d-flex justify-content-end d-none ">
		<button class="btn btn-primary" onclick="guardarCambios()">Guardar Cambios</button>
	</div>

<?php } ?>


<style>
	/* ========================================
	   VARIABLES CSS MODERNAS
	   ======================================== */
	:root {
		--primary-color: #667eea;
		--primary-hover: #5568d3;
		--success-color: #48bb78;
		--success-hover: #38a169;
		--danger-color: #f56565;
		--danger-hover: #e53e3e;
		--warning-color: #ed8936;
		--warning-hover: #dd6b20;
		--info-color: #4299e1;
		--dark-color: #2d3748;
		--light-bg: #f7fafc;
		--card-bg: #ffffff;
		--border-color: #e2e8f0;
		--text-primary: #2d3748;
		--text-secondary: #718096;
		--shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.08);
		--shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
		--shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05);
		--shadow-xl: 0 20px 25px rgba(0, 0, 0, 0.15), 0 10px 10px rgba(0, 0, 0, 0.04);
		--transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	}

	/* ========================================
	   ESTILOS GLOBALES MEJORADOS
	   ======================================== */
	.titulo-principal {
		background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
		-webkit-background-clip: text;
		-webkit-text-fill-color: transparent;
		background-clip: text;
		font-weight: 800;
		font-size: 2rem;
		margin-bottom: 2rem;
		text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
		display: flex;
		align-items: center;
		gap: 0.75rem;
	}

	.titulo-principal i {
		-webkit-text-fill-color: var(--primary-color);
		filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.1));
	}

	.content-dashboard {
		background: var(--card-bg);
		border-radius: 16px;
		padding: 2rem;
		box-shadow: var(--shadow-md);
		border: 1px solid var(--border-color);
		margin-bottom: 2rem;
		transition: var(--transition);
	}

	.content-dashboard:hover {
		box-shadow: var(--shadow-lg);
		transform: translateY(-2px);
	}

	/* ========================================
	   FORMULARIOS MODERNOS
	   ======================================== */
	.form-group label.control-label {
		font-weight: 600;
		color: var(--text-primary);
		margin-bottom: 0.5rem;
		font-size: 0.875rem;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.form-group .input-group {
		box-shadow: var(--shadow-sm);
		border-radius: 10px;
		overflow: hidden;
		transition: var(--transition);
		border: 2px solid transparent;
	}

	.form-group .input-group:focus-within {
		box-shadow: var(--shadow-md);
		border-color: var(--primary-color);
		transform: translateY(-1px);
	}

	.input-group-prepend .input-group-text {
		border: none;
		padding: 0.75rem 1rem;
		font-size: 1.1rem;
	}

	.input-group .form-control {
		border: none;
		padding: 0.75rem 1rem;
		font-size: 1rem;
		transition: var(--transition);
	}

	.input-group .form-control:focus {
		box-shadow: none;
		outline: none;
	}

	/* Iconos de colores modernos con gradientes */
	.fondo-morado { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
	.fondo-verde { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); }
	.fondo-cafe { background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%); }
	.fondo-azul-claro { background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%); }
	.fondo-verde-claro { background: linear-gradient(135deg, #68d391 0%, #48bb78 100%); }

	/* Switch mejorado */
	.switch-form {
		width: 60px !important;
		height: 30px !important;
		border-radius: 30px !important;
		appearance: none;
		background: #cbd5e0;
		outline: none;
		cursor: pointer;
		position: relative;
		transition: var(--transition);
		box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
	}

	.switch-form:checked {
		background: linear-gradient(135deg, var(--success-color) 0%, var(--success-hover) 100%);
	}

	.switch-form::before {
		content: '';
		position: absolute;
		width: 24px;
		height: 24px;
		border-radius: 50%;
		top: 3px;
		left: 3px;
		background: white;
		transition: var(--transition);
		box-shadow: 0 2px 4px rgba(0,0,0,0.2);
	}

	.switch-form:checked::before {
		transform: translateX(30px);
	}

	/* ========================================
	   BOTONES MODERNOS
	   ======================================== */
	.botones-acciones {
		display: flex;
		gap: 1rem;
		justify-content: flex-end;
		margin-top: 2rem;
		padding-top: 2rem;
		border-top: 2px solid var(--border-color);
	}

	.btn-guardar, .btn-cancelar {
		padding: 0.875rem 2.5rem;
		border-radius: 10px;
		font-weight: 600;
		font-size: 1rem;
		text-transform: uppercase;
		letter-spacing: 0.5px;
		transition: var(--transition);
		border: none;
		cursor: pointer;
		box-shadow: var(--shadow-md);
		position: relative;
		overflow: hidden;
	}

	.btn-guardar {
		background: linear-gradient(135deg, var(--success-color) 0%, var(--success-hover) 100%);
		color: white;
	}

	.btn-guardar:hover {
		transform: translateY(-2px);
		box-shadow: var(--shadow-lg);
	}

	.btn-guardar:active {
		transform: translateY(0);
	}

	.btn-cancelar {
		background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
		color: white;
	}

	.btn-cancelar:hover {
		transform: translateY(-2px);
		box-shadow: var(--shadow-lg);
	}

	.btn-warning {
		background: linear-gradient(135deg, var(--warning-color) 0%, var(--warning-hover) 100%);
		color: white;
		padding: 0.875rem 2rem;
		border-radius: 10px;
		font-weight: 600;
		border: none;
		box-shadow: var(--shadow-md);
		transition: var(--transition);
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.btn-warning:hover {
		transform: translateY(-2px);
		box-shadow: var(--shadow-lg);
	}

	/* ========================================
	   MODAL MEJORADO
	   ======================================== */
	.modal-mesa-bg {
		display: none;
		position: fixed;
		top: 0;
		left: 0;
		width: 100vw;
		height: 100vh;
		background: rgba(0, 0, 0, 0.6);
		backdrop-filter: blur(4px);
		z-index: 1000;
		align-items: center;
		justify-content: center;
		animation: fadeInBg 0.3s ease;
	}

	@keyframes fadeInBg {
		from { opacity: 0; }
		to { opacity: 1; }
	}

	.modal-mesa {
		background: var(--card-bg);
		border-radius: 20px;
		box-shadow: var(--shadow-xl);
		width: 420px;
		max-width: 95vw;
		padding: 0;
		display: flex;
		flex-direction: column;
		animation: modalSlideIn 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
		border: 1px solid var(--border-color);
	}

	@keyframes modalSlideIn {
		from {
			opacity: 0;
			transform: translateY(-50px) scale(0.95);
		}
		to {
			opacity: 1;
			transform: translateY(0) scale(1);
		}
	}

	.modal-mesa-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 1.5rem 2rem;
		background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
		border-radius: 20px 20px 0 0;
	}

	.modal-mesa-header h3 {
		margin: 0;
		font-size: 1.35rem;
		color: white;
		font-weight: 700;
		display: flex;
		align-items: center;
		gap: 0.5rem;
	}

	.modal-mesa-close {
		background: rgba(255, 255, 255, 0.2);
		border: none;
		font-size: 1.5rem;
		color: white;
		cursor: pointer;
		transition: var(--transition);
		width: 36px;
		height: 36px;
		border-radius: 50%;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.modal-mesa-close:hover {
		background: rgba(255, 255, 255, 0.3);
		transform: rotate(90deg);
	}

	.modal-mesa-body {
		padding: 2rem;
		max-height: 65vh;
		overflow-y: auto;
	}

	.modal-mesa-body::-webkit-scrollbar {
		width: 8px;
	}

	.modal-mesa-body::-webkit-scrollbar-track {
		background: var(--light-bg);
		border-radius: 10px;
	}

	.modal-mesa-body::-webkit-scrollbar-thumb {
		background: var(--primary-color);
		border-radius: 10px;
	}

	.modal-mesa-footer {
		display: flex;
		justify-content: flex-end;
		gap: 0.75rem;
		padding: 1.5rem 2rem;
		background: var(--light-bg);
		border-radius: 0 0 20px 20px;
	}

	.modal-mesa .form-group {
		margin-bottom: 1.25rem;
	}

	.modal-mesa .form-group label {
		font-weight: 600;
		color: var(--text-primary);
		margin-bottom: 0.5rem;
		display: block;
		font-size: 0.875rem;
	}

	.modal-mesa .form-row {
		display: flex;
		gap: 0.75rem;
	}

	.modal-mesa .form-row .form-group {
		flex: 1;
		margin-bottom: 0;
	}

	.modal-mesa .form-control {
		width: 100%;
		padding: 0.75rem 1rem;
		border: 2px solid var(--border-color);
		border-radius: 10px;
		font-size: 1rem;
		box-sizing: border-box;
		transition: var(--transition);
		background: var(--card-bg);
	}

	.modal-mesa .form-control:focus {
		border-color: var(--primary-color);
		outline: none;
		box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
	}

	.btn-primary {
		background: linear-gradient(135deg, var(--success-color) 0%, var(--success-hover) 100%);
		color: #fff;
		border: none;
		border-radius: 10px;
		padding: 0.75rem 1.5rem;
		font-weight: 600;
		cursor: pointer;
		transition: var(--transition);
		box-shadow: var(--shadow-sm);
		text-transform: uppercase;
		letter-spacing: 0.5px;
		font-size: 0.875rem;
	}

	.btn-primary:hover {
		transform: translateY(-2px);
		box-shadow: var(--shadow-md);
	}

	.btn-secondary {
		background: #e2e8f0;
		color: var(--text-primary);
		border: none;
		border-radius: 10px;
		padding: 0.75rem 1.5rem;
		font-weight: 600;
		cursor: pointer;
		transition: var(--transition);
		text-transform: uppercase;
		letter-spacing: 0.5px;
		font-size: 0.875rem;
	}

	.btn-secondary:hover {
		background: #cbd5e0;
		transform: translateY(-2px);
	}

	.btn-danger {
		background: linear-gradient(135deg, var(--danger-color) 0%, var(--danger-hover) 100%);
		color: white;
		border: none;
		border-radius: 10px;
		padding: 0.75rem 1.5rem;
		font-weight: 600;
		cursor: pointer;
		transition: var(--transition);
		box-shadow: var(--shadow-sm);
		text-transform: uppercase;
		letter-spacing: 0.5px;
		font-size: 0.875rem;
	}

	.btn-danger:hover {
		transform: translateY(-2px);
		box-shadow: var(--shadow-md);
	}

	@keyframes modalFadeIn {
		from {
			opacity: 0;
			transform: translateY(-30px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	/* ========================================
	   GRID INTERACTIVO MEJORADO
	   ======================================== */
	.grid {
		position: relative;
		display: grid;
		grid-template-columns: repeat(<?= $ambiente['columnas'] ?>,
				<?= $pxCelda . "px" ?>);
		grid-template-rows: repeat(<?= $ambiente['filas'] ?>,
				<?= $pxCelda . "px" ?>);
		gap: 2px;
		border: 3px solid var(--primary-color);
		margin: 30px auto;
		background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
		width: max-content;
		border-radius: 12px;
		padding: 8px;
		box-shadow: var(--shadow-lg);
	}

	.grid-cell {
		width:
			<?= $pxCelda . "px" ?>;
		height:
			<?= $pxCelda . "px" ?>;
		border: 1px dashed #cbd5e0;
		background: rgba(255, 255, 255, 0.5);
		transition: var(--transition);
		border-radius: 4px;
	}

	.grid-cell:hover {
		background: rgba(102, 126, 234, 0.1);
	}

	/* ========================================
	   ELEMENTOS DEL GRID (MESAS, BARRA, ETC.)
	   ======================================== */
	.elemento {
		position: absolute;
		border-radius: 8px;
		font-size: 11px;
		font-weight: 700;
		color: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		box-sizing: border-box;
		cursor: move;
		z-index: 2;
		transform-origin: center center;
		transition: var(--transition);
		box-shadow: var(--shadow-md);
		text-shadow: 0 1px 2px rgba(0,0,0,0.3);
		border: 2px solid rgba(255, 255, 255, 0.3);
	}

	.elemento:hover {
		transform: scale(1.05);
		box-shadow: var(--shadow-lg);
		z-index: 10;
	}

	.mesa {
		background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
	}

	.barra {
		background: linear-gradient(135deg, #795548 0%, #5d4037 100%);
	}

	.puerta {
		background: linear-gradient(135deg, #607D8B 0%, #455a64 100%);
	}

	.pista {
		background: linear-gradient(135deg, #9C27B0 0%, #7b1fa2 100%);
	}

	.tarima {
		background: linear-gradient(135deg, #3F51B5 0%, #303f9f 100%);
	}

	.bano {
		background: linear-gradient(135deg, #FF5722 0%, #e64a19 100%);
	}

	.escalera {
		background: linear-gradient(135deg, #E91E63 0%, #c2185b 100%);
	}

	.ascensor {
		background: linear-gradient(135deg, #00BCD4 0%, #0097a7 100%);
	}

	.pantalla {
		background: linear-gradient(135deg, #FF9800 0%, #f57c00 100%);
	}

	.terraza {
		background: linear-gradient(135deg, #ec94a2 0%, #e57383 100%);
	}

	.ventana {
		background: linear-gradient(135deg, #ff00ff 0%, #cc00cc 100%);
	}

	.libre {
		background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
	}

	.ocupada {
		background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
	}

	.reservada {
		background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
	}

	.pendiente {
		background: linear-gradient(135deg, #ecc94b 0%, #d69e2e 100%);
	}

	.celda-hover {
		background: rgba(102, 126, 234, 0.2) !important;
		border: 2px solid var(--primary-color) !important;
		z-index: 1;
		animation: pulseCell 0.6s ease-in-out;
	}

	@keyframes pulseCell {
		0%, 100% { transform: scale(1); }
		50% { transform: scale(1.05); }
	}

	/* Estado especial: amarillo para mesa_estado == 1 */
	.amarillo-estado {
		background: linear-gradient(135deg, #ffd54f 0%, #ffc107 100%) !important;
		color: #333 !important;
		border: 3px solid #ffb300 !important;
		animation: pulseYellow 2s ease-in-out infinite;
	}

	@keyframes pulseYellow {
		0%, 100% { box-shadow: 0 0 10px rgba(255, 193, 7, 0.6); }
		50% { box-shadow: 0 0 20px rgba(255, 193, 7, 0.9); }
	}

	.inactivo {
		background: linear-gradient(135deg, #a0aec0 0%, #718096 100%) !important;
		color: #e2e8f0 !important;
		border: 2px solid #cbd5e0 !important;
		opacity: 0.5 !important;
		pointer-events: none !important;
		cursor: default !important;
		filter: grayscale(100%);
	}

	/* ========================================
	   CONTENEDOR DE GRÁFICO Y ESTADÍSTICAS
	   ======================================== */
	.card {
		background: var(--card-bg);
		border-radius: 16px;
		box-shadow: var(--shadow-md);
		border: 1px solid var(--border-color);
		transition: var(--transition);
		overflow: hidden;
	}

	.card:hover {
		box-shadow: var(--shadow-lg);
		transform: translateY(-2px);
	}

	.card-header {
		background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
		padding: 1.25rem 1.5rem;
		font-weight: 700;
		font-size: 1.125rem;
		border: none;
	}

	.card-body {
		padding: 1.5rem;
	}

	.text-center.mb-3 strong {
		font-size: 1.25rem;
		color: var(--text-primary);
		font-weight: 700;
	}

	.nameMesa .card {
		margin-top: 1rem;
		animation: slideInRight 0.4s ease;
	}

	@keyframes slideInRight {
		from {
			opacity: 0;
			transform: translateX(20px);
		}
		to {
			opacity: 1;
			transform: translateX(0);
		}
	}

	/* ========================================
	   MODO VALIDACIÓN
	   ======================================== */
	<?php if ($destacarMesa && $modoValidacion === 'validacion'): ?>

	.elemento {
		background: linear-gradient(135deg, #a0aec0 0%, #718096 100%) !important;
		color: #e2e8f0 !important;
		border: 2px solid #cbd5e0 !important;
		opacity: 0.4 !important;
		pointer-events: none !important;
		cursor: default !important;
		filter: grayscale(100%) blur(1px);
	}

	.elemento[data-mesa-id="<?= $destacarMesa ?>"] {
		background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%) !important;
		border: 4px solid #FF6B35 !important;
		box-shadow: 0 0 30px rgba(255, 215, 0, 0.9), 0 0 60px rgba(255, 165, 0, 0.6) !important;
		animation: pulseHighlightEnhanced 2s infinite !important;
		z-index: 1000 !important;
		color: #1a202c !important;
		font-weight: 900 !important;
		opacity: 1 !important;
		filter: none !important;
		font-size: 14px !important;
	}

	@keyframes pulseHighlightEnhanced {
		0%, 100% {
			box-shadow: 0 0 30px rgba(255, 215, 0, 0.9), 0 0 60px rgba(255, 165, 0, 0.6);
			transform: scale(1);
		}
		50% {
			box-shadow: 0 0 40px rgba(255, 215, 0, 1), 0 0 80px rgba(255, 165, 0, 0.8);
			transform: scale(1.08);
		}
	}

	.grid-cell {
		pointer-events: none !important;
	}

	.modal-mesa-bg,
	.btn-warning,
	.btn-primary,
	.d-none {
		display: none !important;
	}

	.text-center.mb-3 {
		display: none !important;
	}

	.col-7 {
		width: 100% !important;
		display: grid;
		place-items: center;
		overflow: auto;
	}

	.col-5.my-2 {
		display: none !important;
	}

	<?php endif; ?>

	/* ========================================
	   RESPONSIVE MEJORADO
	   ======================================== */
	@media (max-width: 768px) {
		.titulo-principal {
			font-size: 1.5rem;
		}

		.content-dashboard {
			padding: 1.25rem;
		}

		.botones-acciones {
			flex-direction: column;
		}

		.btn-guardar, .btn-cancelar {
			width: 100%;
		}

		.modal-mesa {
			width: 95vw;
		}
	}

	/* ========================================
	   ANIMACIONES ADICIONALES
	   ======================================== */
	@keyframes fadeIn {
		from { opacity: 0; }
		to { opacity: 1; }
	}

	.content-dashboard,
	.grid {
		animation: fadeIn 0.5s ease;
	}

	/* ========================================
	   SCROLLBAR PERSONALIZADO
	   ======================================== */
	.p-4.w-100.overflow-scroll::-webkit-scrollbar {
		width: 12px;
		height: 12px;
	}

	.p-4.w-100.overflow-scroll::-webkit-scrollbar-track {
		background: var(--light-bg);
		border-radius: 10px;
	}

	.p-4.w-100.overflow-scroll::-webkit-scrollbar-thumb {
		background: linear-gradient(135deg, var(--primary-color) 0%, #764ba2 100%);
		border-radius: 10px;
		border: 2px solid var(--light-bg);
	}

	.p-4.w-100.overflow-scroll::-webkit-scrollbar-thumb:hover {
		background: linear-gradient(135deg, #764ba2 0%, var(--primary-color) 100%);
	}
</style>

<!-- <script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script> -->
<script src="/components/interactjs/interact.min.js"></script>

<script>
	// --- Lógica para edición/creación de mesas desde el modal ---
	let modalMesaModo = 'crear';
	let mesaEditandoId = null;

	function abrirModalMesa(modo, mesa, display = 0) { // Default display to 0
		modalMesaModo = modo;
		mesaEditandoId = mesa && mesa.mesa_id ? mesa.mesa_id : null;

		if (display == 1) {
			// When display=1, show the table name in .nameMesa div and skip modal
			mostrarNombreMesa(mesa);
			return;
		}

		// Only attempt to open modal if display != 1
		const modal = document.getElementById('modal-nueva-mesa');
		if (!modal) {
			console.error('No se encontró el elemento con ID modal-nueva-mesa');
			return;
		}

		// Modal logic
		modal.style.display = 'flex';
		modal.style.alignItems = 'center';
		modal.style.justifyContent = 'center';

		// Cambiar título y botón
		const titulo = document.querySelector('#modal-mesa-titulo');
		if (titulo) {
			titulo.innerHTML = modo === 'editar' ? '<i class="fas fa-edit"></i> Editar mesa' : '<i class="fas fa-plus-circle"></i> Agregar nueva mesa';
		}
		const footer = document.querySelector('.modal-mesa-footer');
		if (modo === 'editar' && mesa && mesa.mesa_estado == 0) {
			footer.innerHTML = `
				<button class="btn btn-danger" id="btn-eliminar-mesa"><i class="fas fa-trash"></i> Eliminar</button>
				<button class="btn btn-primary" id="btn-modal-mesa-accion"><i class="fas fa-check"></i> Guardar</button>
				<button class="btn btn-secondary" onclick="cerrarModalMesa()">Cancelar</button>
			`;
			document.getElementById('btn-eliminar-mesa').onclick = eliminarMesaDesdeModal;
			document.getElementById('btn-modal-mesa-accion').onclick = function() {
				crearElementoDesdeModal();
			};
		} else {
			footer.innerHTML = `
				<button class="btn btn-primary" id="btn-modal-mesa-accion"><i class="fas fa-check"></i> Crear</button>
				<button class="btn btn-secondary" onclick="cerrarModalMesa()">Cancelar</button>
			`;
			document.getElementById('btn-modal-mesa-accion').onclick = function() {
				crearElementoDesdeModal();
			};
		}

		// Rellenar campos si es edición
		document.getElementById('input_tipo').value = mesa && mesa.mesa_tipo ? mesa.mesa_tipo : 'mesa';
		document.getElementById('input_codigo').value = mesa && mesa.mesa_codigo ? mesa.mesa_codigo : '';
		document.getElementById('input_nombre').value = mesa && mesa.mesa_nombre ? mesa.mesa_nombre : 'Nueva';
		document.getElementById('input_ancho').value = mesa && mesa.mesa_ancho ? mesa.mesa_ancho : 2;
		document.getElementById('input_alto').value = mesa && mesa.mesa_alto ? mesa.mesa_alto : 2;
		document.getElementById('input_rotacion').value = mesa && typeof mesa.mesa_rotacion !== 'undefined' ? mesa.mesa_rotacion : 0;
		document.getElementById('input_estado').value = mesa && typeof mesa.mesa_estado !== 'undefined' ? mesa.mesa_estado : 0;
		// Setear capacidad
		if (mesa && typeof mesa.mesa_capacidad !== 'undefined' && mesa.mesa_capacidad !== null && mesa.mesa_capacidad !== '') {
			document.getElementById('input_capacidad').value = mesa.mesa_capacidad;
		} else {
			actualizarCapacidadDefault();
		}
		// Ocultos
		document.getElementById('input_forma').value = mesa && typeof mesa.mesa_forma !== 'undefined' ? mesa.mesa_forma : '';
		document.getElementById('input_activa').value = mesa && typeof mesa.mesa_activa !== 'undefined' ? mesa.mesa_activa : '';
		document.getElementById('input_orden').value = mesa && typeof mesa.orden !== 'undefined' ? mesa.orden : '';
		document.getElementById('input_imagen_disponible').value = mesa && typeof mesa.mesa_imagen_disponible !== 'undefined' ? mesa.mesa_imagen_disponible : '';
		document.getElementById('input_imagen_pendiente').value = mesa && typeof mesa.mesa_imagen_pendiente !== 'undefined' ? mesa.mesa_imagen_pendiente : '';
		document.getElementById('input_imagen_ocupada').value = mesa && typeof mesa.mesa_imagen_ocupada !== 'undefined' ? mesa.mesa_imagen_ocupada : '';
		document.getElementById('input_pos_x').value = mesa && typeof mesa.mesa_pos_x !== 'undefined' ? mesa.mesa_pos_x : '';
		document.getElementById('input_pos_y').value = mesa && typeof mesa.mesa_pos_y !== 'undefined' ? mesa.mesa_pos_y : '';
		document.getElementById('input_imagen_ubicacion_en_ambiente').value = mesa && typeof mesa.mesa_imagen_ubicacion_en_ambiente !== 'undefined' ? mesa.mesa_imagen_ubicacion_en_ambiente : '';
		document.getElementById('input_imagen_ubicacion_en_piso').value = mesa && typeof mesa.mesa_imagen_ubicacion_en_piso !== 'undefined' ? mesa.mesa_imagen_ubicacion_en_piso : '';
	}

	function mostrarNombreMesa(mesaSelect) {
		const nameMesaDiv = document.querySelector('.nameMesa');

		if (!nameMesaDiv) {
			console.error('No se encontró el div con clase .nameMesa');
			return;
		}

		fetch(`/administracion/ambientes/consultaMesa?mesa_codigo=${mesaSelect.mesa_id}`)
			.then(response => response.json())
			.then(data => {
				if (!data || !data.mesa) {
					nameMesaDiv.innerHTML = `<p class="text-muted">No hay información para esta mesa.</p>`;
					return;
				}

				const {
					mesa,
					reserva,
					invitados,
					es_provisional
				} = data;

				if (es_provisional) {
					nameMesaDiv.innerHTML = `
						<div class="card shadow-sm">
							<div class="card-header bg-warning text-white">
								Mesa ${mesa.mesa_codigo} - ${mesa.mesa_nombre || 'Sin nombre'}
							</div>
							<div class="card-body">
								<p class="text-warning">Mesa provisional</p>
							</div>
						</div>
					`;
					return;
				}

				// Construimos lista de invitados
				const invitadosHtml = invitados && invitados.length > 0 ?
					invitados.map(inv => `<li>${inv.invitadoReserva_nombre_invitado ? inv.invitadoReserva_nombre_invitado + ' ' + inv.invitadoReserva_apellido_invitado : 'Invitado sin nombre'}</li>`).join('') :
					'<li>No hay invitados registrados</li>';

				// HTML bonito
				nameMesaDiv.innerHTML = `
					<div class="card shadow-sm">
						<div class="card-header bg-primary text-white">
							Mesa ${mesa.mesa_codigo} - ${mesa.mesa_nombre || 'Sin nombre'}
						</div>
						<div class="card-body">
							<p><strong>ID Reserva:</strong> ${reserva?.id || 'Sin reserva'}</p>
							<h6>Invitados:</h6>
							<ul class="list-unstyled mb-0">
								${invitadosHtml}
							</ul>
						</div>
					</div>
				`;
			})
			.catch(error => {
				console.error('Error en la consulta:', error);
				nameMesaDiv.innerHTML = `<p class="text-danger">Error al cargar la información</p>`;
			});
	}


	function agregarElemento() {
		//RESET: Asegurarse de que está en modo crear
		modalMesaModo = 'crear';
		mesaEditandoId = null;

		// Abrir modal en modo crear con valores por defecto
		abrirModalMesa('crear', null);
	}

	document.addEventListener('DOMContentLoaded', function() {
		document.getElementById('btn-modal-mesa-accion').onclick = function() {
			crearElementoDesdeModal();
		};
	});

	// Asigna listeners de edición a cada elemento visual de mesa/barra/puerta
	// Permite abrir el modal de edición al hacer doble clic sobre el elemento
	function agregarListenersEdicion(div, el) {
		div.addEventListener('dblclick', function(e) {
			const display = <?= isset($_GET["display"]) ? $_GET["display"] : 0 ?>;
			// Al hacer doble clic, se abre el modal en modo edición con los datos de la mesa
			abrirModalMesa('editar', el, display);
		});
	}
	// --- Variables globales de configuración y datos ---
	const columnas = <?= $ambiente['columnas'] ?>; // Número de columnas del ambiente
	const filas = <?= $ambiente['filas'] ?>; // Número de filas del ambiente
	const cellSize = <?= $pxCelda; ?>; // Tamaño de cada celda en px
	const gap = 2; // Espacio entre celdas
	let elementos = <?= json_encode($elementos) ?>; // Array de mesas/barra/puerta
	const grid = document.getElementById('grid'); // Contenedor visual del grid

	// occupancyMap: matriz que indica qué mesa ocupa cada celda (para colisiones y validaciones)
	const occupancyMap = Array.from({
		length: filas
	}, () => Array(columnas).fill(null));

	// Crea la grilla visual de celdas (background), no las mesas
	function crearGrillaVisual() {
		window.celdas = [];
		for (let i = 0; i < filas * columnas; i++) {
			const celda = document.createElement('div');
			celda.className = 'grid-cell';
			grid.appendChild(celda);
			window.celdas.push(celda);
		}
	}
	// --- Resaltado visual de celdas destino al mover/crear mesas ---
	let celdasHover = [];
	// Resalta las celdas donde se colocaría una mesa/barra/puerta (útil para feedback visual al mover)
	function resaltarCeldasFigura(col, row, ancho, alto) {
		limpiarResaltado();
		for (let i = 0; i < alto; i++) {
			for (let j = 0; j < ancho; j++) {
				const r = row + i;
				const c = col + j;
				if (r >= 0 && c >= 0 && r < filas && c < columnas) {
					const idx = r * columnas + c;
					window.celdas[idx].classList.add('celda-hover');
					celdasHover.push(idx);
				}
			}
		}
	}
	// Limpia el resaltado de celdas
	function limpiarResaltado() {
		celdasHover.forEach(idx => {
			if (window.celdas[idx]) window.celdas[idx].classList.remove('celda-hover');
		});
		celdasHover = [];
	}

	// Marca en occupancyMap las celdas ocupadas por una mesa/barra/puerta
	function marcarOcupado(el, id) {
		for (let i = 0; i < el.mesa_alto; i++) {
			for (let j = 0; j < el.mesa_ancho; j++) {
				const y = el.mesa_pos_y + i;
				const x = el.mesa_pos_x + j;
				if (y < filas && x < columnas) {
					occupancyMap[y][x] = id;
				}
			}
		}
	}

	// Verifica si una nueva posición de mesa colisiona con otra o sale del grid
	// id: permite ignorar la propia mesa al moverla
	function hayColision(nuevo, id) {
		for (let i = 0; i < nuevo.mesa_alto; i++) {
			for (let j = 0; j < nuevo.mesa_ancho; j++) {
				const y = nuevo.y + i;
				const x = nuevo.x + j;
				if (y >= filas || x >= columnas) return true; // Fuera de límites
				if (occupancyMap[y][x] !== null && occupancyMap[y][x] !== id) return true; // Colisión con otra mesa
			}
		}
		return false;
	}
	let idTemporal = 0;

	// Abre el modal para crear una nueva mesa/barra/puerta
	function agregarElemento() {
		// RESET: Asegurarse de que está en modo crear
		modalMesaModo = 'crear';
		mesaEditandoId = null;

		document.getElementById('modal-nueva-mesa').style.display = 'flex';
		document.getElementById('modal-nueva-mesa').style.alignItems = 'center';
		document.getElementById('modal-nueva-mesa').style.justifyContent = 'center';
	}

	// function getClosestCapacity(val) {
	// 	const options = [1, 2, 4, 6, 8, 10];
	// 	return options.reduce((prev, curr) => Math.abs(curr - val) < Math.abs(prev - val) ? curr : prev);
	// }

	function actualizarCapacidadDefault() {
		const tipo = document.getElementById('input_tipo').value;
		const ancho = parseInt(document.getElementById('input_ancho').value) || 1;
		const alto = parseInt(document.getElementById('input_alto').value) || 1;
		// const capacidadCalculada = tipo === 'mesa' ? ancho * alto : 0;
		// const capacidadDefault = tipo === 'mesa' ? getClosestCapacity(capacidadCalculada) : 1;
		if (tipo === 'mesa') {
			const capacidadCalculada = ancho * alto;
			document.getElementById('input_capacidad').value = capacidadCalculada;
		} else {
			document.getElementById('input_capacidad').value = 1;
		}
		// document.getElementById('input_capacidad').value = capacidadDefault;
	}

	// Cierra el modal y limpia todos los campos (incluidos los ocultos)
	function cerrarModalMesa() {
		document.getElementById('modal-nueva-mesa').style.display = 'none';
		// Limpiar todos los campos del modal, incluidos los ocultos
		const ids = [
			'input_tipo', 'input_codigo', 'input_nombre', 'input_ancho', 'input_alto', 'input_rotacion', 'input_estado',
			'input_capacidad', 'input_forma', 'input_activa', 'input_orden',
			'input_imagen_disponible', 'input_imagen_pendiente', 'input_imagen_ocupada',
			'input_pos_x', 'input_pos_y', 'input_imagen_ubicacion_en_ambiente', 'input_imagen_ubicacion_en_piso'
		];
		ids.forEach(id => {
			const el = document.getElementById(id);
			if (el) {
				if (el.tagName === 'SELECT') {
					el.selectedIndex = 0;
				} else {
					el.value = '';
				}
			}
		});
	}


	// Toma los datos del modal y decide si crear o editar una mesa
	function crearElementoDesdeModal() {
		// Obtiene los valores de los campos del modal
		const tipo = document.getElementById('input_tipo').value;
		const nombre = document.getElementById('input_nombre').value;
		const codigo = document.getElementById('input_codigo').value.trim();

		const ancho = parseInt(document.getElementById('input_ancho').value);
		const alto = parseInt(document.getElementById('input_alto').value);
		const rotacion = parseInt(document.getElementById('input_rotacion').value);
		const estado = document.getElementById('input_estado').value;
		// Prepara el objeto de datos para enviar al backend
		const mesaData = {
			mesa_tipo: tipo,
			mesa_codigo: codigo,
			mesa_nombre: nombre,
			mesa_ancho: ancho,
			mesa_capacidad: parseInt(document.getElementById('input_capacidad').value) || 0,
			mesa_alto: alto,
			mesa_rotacion: rotacion,
			mesa_estado: estado,
			mesa_pos_x: document.getElementById('input_pos_x').value || 0,
			mesa_pos_y: document.getElementById('input_pos_y').value || 0,
			mesa_ambiente: <?= $this->content->ambiente_id ?>,
			mesa_activa: document.getElementById('input_activa').value || 1,
			mesa_forma: document.getElementById('input_forma').value || '',
			orden: document.getElementById('input_orden').value || 0,
			mesa_imagen_disponible: document.getElementById('input_imagen_disponible').value || '',
			mesa_imagen_pendiente: document.getElementById('input_imagen_pendiente').value || '',
			mesa_imagen_ocupada: document.getElementById('input_imagen_ocupada').value || '',
			mesa_imagen_ubicacion_en_ambiente: document.getElementById('input_imagen_ubicacion_en_ambiente').value || '',
			mesa_imagen_ubicacion_en_piso: document.getElementById('input_imagen_ubicacion_en_piso').value || ''
		};
		// Decide si es edición o creación
		if (modalMesaModo === 'editar') {
			mesaData.mesa_id = mesaEditandoId;
			editarMesaDesdeModal(mesaData);
		} else {
			crearMesaDesdeModal(mesaData);
		}
	}

	// Renderiza todos los elementos (mesas, barras, puertas) sobre el grid visual
	// Asigna listeners, estilos y funcionalidad de drag&drop
	function renderElementos() {
		elementos.forEach(el => {
			marcarOcupado(el, el.mesa_id); // Marca las celdas ocupadas por este elemento

			// Crea el div visual para la mesa/barra/puerta
			const div = document.createElement('div');
			// Si la mesa está en estado 1, agregamos la clase especial para resaltarla en amarillo
			let estadoClase = '';
			if (el.mesa_estado == 1) {
				estadoClase = 'amarillo-estado';
			}
			let estadoActivo
			if (el.mesa_activa == 0) {
				estadoActivo = 'inactivo'
			}

			div.className = `elemento ${el.mesa_tipo} ${el.mesa_estado || ''} ${estadoClase} ${estadoActivo}`;
			div.innerText = el.mesa_nombre || el.mesa_tipo;
			div.dataset.id = el.mesa_id;
			div.dataset.mesaId = el.mesa_id; // Agregar data-mesa-id para la funcionalidad de resaltado
			div.dataset.rotation = el.mesa_rotacion || '0';

			// Posiciona y dimensiona el elemento según sus datos
			Object.assign(div.style, {
				width: (cellSize * el.mesa_ancho + gap * (el.mesa_ancho - 1)) + 'px',
				height: (cellSize * el.mesa_alto + gap * (el.mesa_alto - 1)) + 'px',
				top: (el.mesa_pos_y * (cellSize + gap)) + 'px',
				left: (el.mesa_pos_x * (cellSize + gap)) + 'px',
				transform: `rotate(${el.mesa_rotacion || 0}deg)`
			});
			//console.log(div)
			grid.appendChild(div);
			agregarListenersEdicion(div, el); // Permite editar al hacer doble clic

			<?php if ($modoValidacion !== 'validacion'): ?>
				// --- Drag & Drop con interact.js ---
				interact(div)
					.draggable({
						modifiers: [
							interact.modifiers.snap({
								targets: [interact.snappers.grid({
									x: cellSize + gap,
									y: cellSize + gap
								})]
							})
						],
						listeners: {
							// Mientras se arrastra, se muestra el área destino resaltada
							move(event) {
								const target = event.target;
								const x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx;
								const y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;
								target.style.transform = `translate(${x}px, ${y}px) rotate(${target.dataset.rotation || '0'}deg)`;
								target.setAttribute('data-x', x);
								target.setAttribute('data-y', y);
								const col = Math.round((parseFloat(target.style.left) + x) / (cellSize + gap));
								const row = Math.round((parseFloat(target.style.top) + y) / (cellSize + gap));
								const id = parseInt(target.dataset.id);
								const el = elementos.find(e => e.mesa_id === id);
								const ancho = el.mesa_ancho || 1;
								const alto = el.mesa_alto || 1;
								resaltarCeldasFigura(col, row, ancho, alto);
								guardarCambios()
							},
							// Al soltar, valida colisiones y actualiza posición
							end(event) {
								const target = event.target;
								const x = parseFloat(target.style.left) + (parseFloat(target.getAttribute('data-x')) || 0);
								const y = parseFloat(target.style.top) + (parseFloat(target.getAttribute('data-y')) || 0);
								const col = Math.round(x / (cellSize + gap));
								const row = Math.round(y / (cellSize + gap));
								const id = parseInt(target.dataset.id);
								const el = elementos.find(e => e.mesa_id === id);
								limpiarResaltado();
								const nuevo = {
									...el,
									x: col,
									y: row
								};
								if (hayColision(nuevo, id)) {
									alert('¡Colisión detectada!');
									target.style.transform = `rotate(${target.dataset.rotation || '0'}deg)`;
									target.removeAttribute('data-x');
									target.removeAttribute('data-y');
									return;
								}
								// Actualiza la posición en el array de datos
								el.mesa_pos_x = col;
								el.mesa_pos_y = row;
								// Actualiza la posición visual
								target.style.left = (col * (cellSize + gap)) + 'px';
								target.style.top = (row * (cellSize + gap)) + 'px';
								target.style.transform = `rotate(${target.dataset.rotation || '0'}deg)`;
								target.removeAttribute('data-x');
								target.removeAttribute('data-y');
								// Limpia ocupación anterior y marca la nueva
								for (let i = 0; i < filas; i++) {
									for (let j = 0; j < columnas; j++) {
										if (occupancyMap[i][j] === id) occupancyMap[i][j] = null;
									}
								}
								marcarOcupado(el, id);
								guardarCambios()
							}
						}
					});
			<?php endif; ?>
			// 🔒 Resize desactivado por ahora
			/* ...resize code... */
		});
	}

	// --- Crear y editar mesa vía AJAX ---
	// Envía los datos al backend para crear una nueva mesa
	function crearMesaDesdeModal(mesaData) {
		// Validación en frontend: código y nombre únicos
		const existeCodigo = elementos.some(e => String(e.mesa_codigo || '').toLowerCase() === mesaData.mesa_codigo.toLowerCase());
		const existeNombre = elementos.some(e => String(e.mesa_nombre || '').toLowerCase() === mesaData.mesa_nombre.toLowerCase());
		if (existeCodigo) {
			alert('Ya existe una mesa con ese código.');
			return;
		}
		if (existeNombre) {
			alert('Ya existe una mesa con ese nombre.');
			return;
		}
		fetch('/administracion/ambientes/crearmesa', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(mesaData)
			})
			.then(res => res.json())
			.then(data => {
				if (data && data.error) {
					alert(data.error);
					return;
				}
				if (data && data.mesa_id) {
					// Agrega la nueva mesa al array local y refresca la vista
					elementos.push(data);
					// Limpiar variables globales después de crear
					modalMesaModo = 'crear';
					mesaEditandoId = null;
					cerrarModalMesa();
					renderizarTodo();
				} else {
					alert('Error al crear la mesa.');
				}
			})
			.catch(() => alert('Error de red al crear la mesa.'));
	}

	// Envía los datos al backend para editar una mesa existente
	function editarMesaDesdeModal(mesaData) {
		// Validación en frontend: código y nombre únicos (excepto la mesa que se edita)
		const existeCodigo = elementos.some(e => String(e.mesa_codigo || '').toLowerCase() === mesaData.mesa_codigo.toLowerCase() && e.mesa_id !== mesaData.mesa_id);
		const existeNombre = elementos.some(e => String(e.mesa_nombre || '').toLowerCase() === mesaData.mesa_nombre.toLowerCase() && e.mesa_id !== mesaData.mesa_id);
		if (existeCodigo) {
			alert('Ya existe una mesa con ese código.');
			return;
		}
		if (existeNombre) {
			alert('Ya existe una mesa con ese nombre.');
			return;
		}
		fetch('/administracion/ambientes/editarmesa', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(mesaData)
			})
			.then(res => res.json())
			.then(data => {
				if (data && data.error) {
					alert(data.error);
					return;
				}
				if (data && data.mesa_id) {
					// Actualiza la mesa en el array local y refresca la vista
					const idx = elementos.findIndex(e => e.mesa_id === data.mesa_id);
					if (idx !== -1) elementos[idx] = data;
					// Limpiar variables globales después de editar
					modalMesaModo = 'crear';
					mesaEditandoId = null;
					cerrarModalMesa();
					renderizarTodo();
				} else {
					alert('Error al editar la mesa.');
				}
			})
			.catch(() => alert('Error de red al editar la mesa.'));
	}

	// Envía los datos al backend para eliminar una mesa
	function eliminarMesaDesdeModal() {
		const mesa_id = mesaEditandoId;
		if (!mesa_id) return;
		if (!confirm('¿Estás seguro de que quieres eliminar esta mesa?')) return;
		fetch('/administracion/ambientes/eliminarmesa', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify({
					mesa_id: mesa_id
				})
			})
			.then(res => res.json())
			.then(data => {
				console.log(data);

				if (data.error) {
					alert(data.error);
					cerrarModalMesa();
					return;
				}
				if (data.success) {
					alert('Mesa eliminada correctamente');

					try {
						elementos = elementos.filter(e => e.mesa_id !== mesa_id);
					} catch (error) {
						console.error('Error al filtrar elementos:', error);
					}

					//  elementos = elementos.filter(e => e.mesa_id !== mesa_id);
					cerrarModalMesa();
					renderizarTodo();
				}
			})
			.catch(() => alert('Error al eliminar la mesa.'));
	}


	// Limpia y vuelve a renderizar toda la grilla y los elementos
	function renderizarTodo() {
		grid.innerHTML = '';
		crearGrillaVisual();
		renderElementos();
	}

	// Envía todos los elementos actuales al backend para guardar cambios
	function guardarCambios() {
		fetch('/administracion/ambientes/guardarelementos', {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json'
				},
				body: JSON.stringify(elementos)
			})
			.then(res => res.text())
			.then(data => {
				// Aquí se podría mostrar feedback visual de éxito
				//console.log(data);
			})
			.catch(err => alert('Error al guardar'));
	}

	// Inicializa la visualización al cargar la página
	renderizarTodo();

	// Validación del campo descuento
	function validarDescuento(input) {
		let valor = input.value;

		// Permitir campo vacío
		if (valor === '') {
			input.setCustomValidity('');
			return;
		}

		// Convertir a número
		let numero = parseFloat(valor);

		// Validar que sea un número válido
		if (isNaN(numero)) {
			input.setCustomValidity('Debe ingresar un número válido');
			return;
		}

		// Validar rango 0-100
		if (numero < 0) {
			input.value = 0;
			numero = 0;
		} else if (numero > 100) {
			input.value = 100;
			numero = 100;
		}

		input.setCustomValidity('');
	}
</script>