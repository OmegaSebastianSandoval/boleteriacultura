<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-left" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->boletas_estilo_id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->boletas_estilo_id; ?>" />
			<?php } ?>

			<div class="alert alert-warning">
				<strong>Nota:</strong>
				<ul class="mb-0">
					<li>El tamaño recomendado para la imagen de fondo es <strong>874 × 1240 px</strong> (A5 vertical).</li>
					<li>Solo se permiten imágenes JPG o JPEG.</li>
					<li>Ubica íconos o logotipos en la parte superior sin superar <strong>300 px</strong> de altura.</li>
				</ul>
			</div>

			<div class="row g-2 align-items-end">
				<div class="col-2 d-grid">
					<label class="control-label">Activo</label>
					<input type="checkbox" name="boletas_estilo_estado" value="1" class="form-control switch-form"
						<?php if ($this->getObjectVariable($this->content, 'boletas_estilo_estado') == 1) { echo "checked"; } ?>>
				</div>
				<div class="col-3">
					<label for="boletas_estilo_titulo" class="control-label">Título</label>
					<input type="text" value="<?= $this->content->boletas_estilo_titulo; ?>" name="boletas_estilo_titulo" id="boletas_estilo_titulo" class="form-control">
				</div>
				<div class="col-3">
					<label for="boletas_estilo_fondo">Fondo</label>
					<input type="file" name="boletas_estilo_fondo" id="boletas_estilo_fondo" class="form-control file-image" data-buttonName="btn-primary" accept="image/jpg, image/jpeg">
					<?php if ($this->content->boletas_estilo_fondo) { ?>
						<div id="imagen_boletas_estilo_fondo" class="mt-1">
							<img src="/images/<?= $this->content->boletas_estilo_fondo; ?>" class="img-thumbnail thumbnail-administrator" />
							<div><button class="btn btn-danger btn-sm mt-1" type="button" onclick="eliminarImagen('boletas_estilo_fondo','<?php echo $this->route . "/deleteimage"; ?>')"><i class="glyphicon glyphicon-remove"></i> Eliminar</button></div>
						</div>
					<?php } ?>
				</div>
				<div class="col-4">
					<label for="boletas_estilo_textofooter" class="control-label">Texto Footer</label>
					<input type="text" value="<?= $this->content->boletas_estilo_textofooter; ?>" name="boletas_estilo_textofooter" id="boletas_estilo_textofooter" class="form-control">
				</div>
			</div>
		</div>
		<div class="botones-acciones">
			<button class="btn btn-guardar" type="submit">Guardar</button>
			<a href="<?php echo $this->route; ?>" class="btn btn-cancelar">Cancelar</a>
		</div>
	</form>
</div>
