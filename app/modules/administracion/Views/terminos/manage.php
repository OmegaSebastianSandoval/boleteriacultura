<h1 class="titulo-principal"><i class="fas fa-cogs"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
	<form class="text-left" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>">
		<div class="content-dashboard">
			<input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
			<input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
			<?php if ($this->content->termino_id) { ?>
				<input type="hidden" name="id" id="id" value="<?= $this->content->termino_id; ?>" />
			<?php } ?>

			<div class="alert alert-warning py-2 mb-3">
				Ten en cuenta que si introduces un <strong>enlace</strong>, este se abrirá en una <strong>nueva pestaña</strong>. Si introduces <strong>texto</strong>, se mostrará como un <strong>modal</strong> en la misma página.
			</div>

			<div class="row g-2 align-items-end mb-3">
				<div class="col-auto d-grid">
					<label class="control-label">Activo</label>
					<input type="checkbox" name="termino_estado" value="1" class="form-control switch-form"
						<?php if ($this->getObjectVariable($this->content, 'termino_estado') == 1) { echo "checked"; } ?>>
				</div>
				<div class="col">
					<label for="termino_seccion" class="control-label">Sección</label>
					<select name="termino_seccion" id="termino_seccion" class="form-select">
						<option value="">-- Seleccione --</option>
						<?php foreach ($this->list_secciones as $key => $value) { ?>
							<option value="<?= $key ?>" <?php if ($this->content->termino_seccion == $key) echo "selected"; ?>><?= $value ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col">
					<label for="termino_titulo" class="control-label">Título</label>
					<input type="text" value="<?= $this->content->termino_titulo; ?>" name="termino_titulo" id="termino_titulo" class="form-control">
				</div>
				<div class="col">
					<label for="termino_enlace" class="control-label">Enlace</label>
					<input type="text" value="<?= $this->content->termino_enlace; ?>" name="termino_enlace" id="termino_enlace" class="form-control">
				</div>
			</div>

			<div>
				<label for="termino_texto" class="form-label">Texto</label>
				<textarea name="termino_texto" id="termino_texto" class="form-control tinyeditor" rows="5"><?= $this->content->termino_texto; ?></textarea>
			</div>
		</div>
		<div class="botones-acciones">
			<button class="btn btn-guardar" type="submit">Guardar</button>
			<a href="<?php echo $this->route; ?>" class="btn btn-cancelar">Cancelar</a>
		</div>
	</form>
</div>
<style>
    .tox-tinymce {
        height: 300px !important;
    }
</style>