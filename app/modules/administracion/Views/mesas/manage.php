<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>

<div class="container-fluid">
	<form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->mesa_id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->mesa_id; ?>" />
			<?php } ?>

			<div class="row g-2 align-items-end">
				<div class="col-auto d-grid">
					<label class="control-label">Activar</label>
					<input type="checkbox" name="mesa_activa" value="1" class="form-control switch-form"
						<?php if ($this->getObjectVariable($this->content, 'mesa_activa') == 1) { echo "checked"; } ?>>
				</div>

				<?php if ($this->content->mesa_estado == 0) { ?>
				<div class="col">
					<label class="control-label">Estado</label>
					<select name="mesa_estado" class="form-select">
						<option value="0" <?= ($this->content->mesa_estado == 0) ? 'selected' : ''; ?>>Libre</option>
						<option value="1" <?= ($this->content->mesa_estado == 1) ? 'selected' : ''; ?>>Ocupado</option>
					</select>
				</div>
				<?php } else { ?>
					<input type="hidden" name="mesa_estado" value="<?= $this->content->mesa_estado; ?>">
				<?php } ?>

				<div class="col">
					<label class="control-label">Ambiente</label>
					<select class="form-select" name="mesa_ambiente">
						<option value="">Seleccione...</option>
						<?php foreach ($this->list_mesa_ambiente as $key => $value) { ?>
							<option <?php if ($this->getObjectVariable($this->content, "mesa_ambiente") == $key) { echo "selected"; } ?> value="<?php echo $key; ?>"><?= $value; ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col">
					<label for="mesa_codigo" class="control-label">Código</label>
					<input type="text" value="<?= $this->content->mesa_codigo; ?>" name="mesa_codigo" id="mesa_codigo"
						data-remote="/core/mesas/validationcodigo?codigo=<?= $this->content->mesa_codigo; ?>"
						class="form-control">
				</div>
				<div class="col">
					<label for="mesa_nombre" class="control-label">Nombre</label>
					<input type="text" value="<?= $this->content->mesa_nombre; ?>" name="mesa_nombre" id="mesa_nombre"
						data-remote="/core/mesas/validationnombre?nombre=<?= $this->content->mesa_nombre; ?>"
						data-error="El nombre ya existe" class="form-control">
				</div>
				<div class="col">
					<label for="mesa_capacidad" class="control-label">Capacidad</label>
					<input type="text" value="<?= $this->content->mesa_capacidad; ?>" name="mesa_capacidad" id="mesa_capacidad" class="form-control">
				</div>
				<div class="col">
					<label for="mesa_tipo" class="control-label">Tipo</label>
					<select class="form-select" name="mesa_tipo" id="mesa_tipo" onchange="document.getElementById('grupo_mesa_precio').style.display = (this.value === 'silla') ? '' : 'none';">
						<?php $tipoActual = $this->content->mesa_tipo ?: 'mesa'; ?>
						<option value="mesa" <?= $tipoActual === 'mesa' ? 'selected' : '' ?>>Mesa</option>
						<option value="silla" <?= $tipoActual === 'silla' ? 'selected' : '' ?>>Silla</option>
					</select>
				</div>
				<div class="col" id="grupo_mesa_precio" style="<?= ($this->content->mesa_tipo ?: 'mesa') === 'silla' ? '' : 'display:none;' ?>">
					<label for="mesa_precio" class="control-label">Precio (silla)</label>
					<input type="number" min="0" step="1" value="<?= $this->content->mesa_precio; ?>" name="mesa_precio" id="mesa_precio" class="form-control">
				</div>
				<div class="col-auto d-grid">
					<label class="control-label">Provisión</label>
					<input type="checkbox" name="mesa_provision" value="0" class="form-control switch-form"
						<?php if ((string)$this->getObjectVariable($this->content, 'mesa_provision') === '0') { echo "checked"; } ?>>
				</div>
			</div>
		</div>

		<input type="hidden" name="mesa_forma"     value="<?= $this->content->mesa_forma; ?>">
		<input type="hidden" name="mesa_ancho"     value="<?= $this->content->mesa_ancho; ?>">
		<input type="hidden" name="mesa_alto"      value="<?= $this->content->mesa_alto; ?>">
		<input type="hidden" name="mesa_rotacion"  value="<?= $this->content->mesa_rotacion; ?>">
		<input type="hidden" name="mesa_pos_x"     value="<?= $this->content->mesa_pos_x; ?>">
		<input type="hidden" name="mesa_pos_y"     value="<?= $this->content->mesa_pos_y; ?>">
		<input type="hidden" name="mesa_imagen_disponible"           value="<?= $this->content->mesa_imagen_disponible; ?>">
		<input type="hidden" name="mesa_imagen_pendiente"            value="<?= $this->content->mesa_imagen_pendiente; ?>">
		<input type="hidden" name="mesa_imagen_ocupada"              value="<?= $this->content->mesa_imagen_ocupada; ?>">
		<input type="hidden" name="mesa_imagen_ubicacion_en_ambiente" value="<?= $this->content->mesa_imagen_ubicacion_en_ambiente; ?>">
		<input type="hidden" name="mesa_imagen_ubicacion_en_piso"    value="<?= $this->content->mesa_imagen_ubicacion_en_piso; ?>">

		<div class="botones-acciones">
			<button class="btn btn-guardar" type="submit">Guardar</button>
			<a href="<?php echo $this->route; ?>" class="btn btn-cancelar">Cancelar</a>
		</div>
	</form>
</div>
