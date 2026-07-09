<h1 class="titulo-principal"><i class="fas fa-cogs"></i>
    <?php echo $this->titlesection; ?>
</h1>
<div class="container-fluid">
    <form class="text-start" enctype="multipart/form-data" method="post" action="<?php echo $this->routeform; ?>">
        <div class="content-dashboard">
            <input type="hidden" name="csrf" id="csrf" value="<?php echo $this->csrf ?>">
            <input type="hidden" name="csrf_section" id="csrf_section" value="<?php echo $this->csrf_section ?>">
            <?php if ($this->content->publicidad_id) { ?>
                <input type="hidden" name="id" id="id" value="<?= $this->content->publicidad_id; ?>" />
            <?php } ?>

            <div class="row g-2">
                <div class="col-2 mb-1">
                    <label class="control-label">Sección <span class="text-danger">*</span></label>
                    <select class="form-select" name="publicidad_seccion" id="publicidad_seccion" required onchange="cambiarContenido();">
                        <option value="">Seleccione...</option>
                        <?php foreach ($this->list_publicidad_seccion as $key => $value) { ?>
                            <option <?php if ($this->getObjectVariable($this->content, "publicidad_seccion") == $key) { echo "selected"; } ?> value="<?php echo $key; ?>"><?= $value; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-2 mb-1">
                    <label for="publicidad_nombre" class="control-label">Nombre <span class="text-danger">*</span></label>
                    <input type="text" value="<?= $this->content->publicidad_nombre; ?>" name="publicidad_nombre" id="publicidad_nombre" class="form-control" required>
                </div>
                <div class="col-2 mb-1">
                    <label for="publicidad_fecha" class="control-label">Fecha</label>
                    <input readonly type="text" value="<?php echo $this->content->publicidad_fecha ?: date('Y-m-d'); ?>" name="publicidad_fecha" id="publicidad_fecha" class="form-control" data-provide="" data-date-format="yyyy-mm-dd" data-date-language="es">
                </div>
                <div class="col-2 mb-1">
                    <label class="control-label">Estado</label>
                    <select class="form-select" name="publicidad_estado" required>
                        <?php foreach ($this->list_publicidad_estado as $key => $value) { ?>
                            <option <?php if ($this->getObjectVariable($this->content, "publicidad_estado") == $key) { echo "selected"; } ?> value="<?php echo $key; ?>"><?= $value; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-2 mb-1" id="vid" style="display:<?php echo ((int)$this->content->publicidad_seccion === 1 || !$this->content->publicidad_seccion) ? 'block' : 'none'; ?>;">
                    <label for="publicidad_video" class="control-label">Video</label>
                    <input type="text" value="<?= $this->content->publicidad_video; ?>" name="publicidad_video" id="publicidad_video" class="form-control">
                </div>
                <div class="col-2 mb-1">
                    <label for="publicidad_color_fondo" class="control-label">Color Fondo</label>
                    <input type="text" value="<?= $this->content->publicidad_color_fondo; ?>" name="publicidad_color_fondo" id="publicidad_color_fondo" class="form-control colorpicker">
                </div>
                <div class="col-2 mb-1">
                    <label for="publicidad_enlace" class="control-label">Enlace</label>
                    <input type="text" value="<?= $this->content->publicidad_enlace; ?>" name="publicidad_enlace" id="publicidad_enlace" class="form-control">
                </div>
                <div class="col-2 mb-1">
                    <label class="control-label">Tipo Enlace</label>
                    <select class="form-select" name="publicidad_tipo_enlace">
                        <option value="">Seleccione...</option>
                        <?php foreach ($this->list_publicidad_tipo_enlace as $key => $value) { ?>
                            <option <?php if ($this->getObjectVariable($this->content, "publicidad_tipo_enlace") == $key) { echo "selected"; } ?> value="<?php echo $key; ?>"><?= $value; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-2 mb-1">
                    <label for="publicidad_texto_enlace" class="control-label">Texto Enlace</label>
                    <input type="text" value="<?= $this->content->publicidad_texto_enlace; ?>" name="publicidad_texto_enlace" id="publicidad_texto_enlace" class="form-control">
                </div>
                <div class="col-3 mb-1">
                    <label for="publicidad_imagen">Imagen</label>
                    <input type="file" name="publicidad_imagen" id="publicidad_imagen" class="form-control file-image" data-buttonName="btn-primary" accept="image/gif, image/jpg, image/jpeg, image/png">
                    <?php if ($this->content->publicidad_imagen) { ?>
                        <div id="imagen_publicidad_imagen" class="mt-1">
                            <img src="/images/<?= $this->content->publicidad_imagen; ?>" class="img-thumbnail thumbnail-administrator" />
                            <div><button class="btn btn-danger btn-sm mt-1" type="button" onclick="eliminarImagen('publicidad_imagen','<?php echo $this->route . "/deleteimage"; ?>')"><i class="glyphicon glyphicon-remove"></i> Eliminar</button></div>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-3 mb-1">
                    <label for="publicidad_imagenresponsive">Imagen Responsive</label>
                    <input type="file" name="publicidad_imagenresponsive" id="publicidad_imagenresponsive" class="form-control file-image" data-buttonName="btn-primary" accept="image/gif, image/jpg, image/jpeg, image/png">
                    <?php if ($this->content->publicidad_imagenresponsive) { ?>
                        <div id="imagen_publicidad_imagenresponsive" class="mt-1">
                            <img src="/images/<?= $this->content->publicidad_imagenresponsive; ?>" class="img-thumbnail thumbnail-administrator" />
                            <div><button class="btn btn-danger btn-sm mt-1" type="button" onclick="eliminarImagen('publicidad_imagenresponsive','<?php echo $this->route . "/deleteimage"; ?>')"><i class="glyphicon glyphicon-remove"></i> Eliminar</button></div>
                        </div>
                    <?php } ?>
                </div>
                <div class="col-12 mb-1">
                    <label for="publicidad_descripcion" class="form-label">Descripción</label>
                    <textarea name="publicidad_descripcion" id="publicidad_descripcion" class="form-control tinyeditor" rows="5"><?= $this->content->publicidad_descripcion; ?></textarea>
                </div>
            </div>
        </div>
        <div id="alerta-campos" class="alert alert-info py-2 mb-2 d-none">
            <i class="fas fa-info-circle"></i> Complete el formulario para habilitar el guardado.
        </div>
        <div class="botones-acciones">
            <button class="btn btn-guardar" type="submit" id="btn-guardar">Guardar</button>
            <a href="<?php echo $this->route; ?>" class="btn btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>

<style>
    .tox-tinymce {
        height: 300px !important;
    }
</style>

<script>
function cambiarContenido() {
    const sec = document.getElementById('publicidad_seccion').value;
    document.getElementById('vid').style.display = (sec == '1' || sec === '') ? 'block' : 'none';
    verificarCampos();
}

function verificarCampos() {
    const seccion = document.getElementById('publicidad_seccion').value;
    const nombre  = document.getElementById('publicidad_nombre').value.trim();
    const btn     = document.getElementById('btn-guardar');
    const alerta  = document.getElementById('alerta-campos');
    const invalido = !seccion || !nombre;

    btn.style.pointerEvents = invalido ? 'none' : '';
    btn.style.opacity       = invalido ? '0.45' : '';
    btn.style.filter        = invalido ? 'grayscale(40%)' : '';
    alerta.classList.toggle('d-none', !invalido);
}

document.addEventListener('DOMContentLoaded', function () {
    verificarCampos();
    document.getElementById('publicidad_seccion').addEventListener('change', verificarCampos);
    document.getElementById('publicidad_nombre').addEventListener('input', verificarCampos);
});
</script>
