<div class="caja-contenido-simple design-three three-<?php echo $contenido->contenido_id; ?>" style="background-color:
  <?php
  if ($contenido->contenido_fondo_color) {
    echo $contenido->contenido_fondo_color;
  } else if ($colorfondo) {
    echo $colorfondo;
  }
  ?> 
    <?php
    if ($contenido->contenido_borde == '1') {
      echo '; border: 2px solid #13436B; border-radius:20px; padding: 0 !important; overflow: hidden; ';
    }
    ?>">

  <?php if ($contenido->contenido_titulo_ver == 1) { ?>
    <h2 class="contenido-disenio-title"><?php echo $contenido->contenido_titulo; ?></h2>
  <?php } ?>
  <?php if ($contenido->contenido_imagen) { ?>
    <div class="imagen-contenido">
     <img src="/images/<?php echo $contenido->contenido_imagen; ?>" class="img-fluid">
    </div>
    <?php if ($contenido->contenido_borde != '1') { ?>
      </br>
    <?php } ?>
  <?php } ?>
  <div>
    <div class="descripcion" style="<?php if ($contenido->contenido_borde == '1' && $contenido->contenido_descripcion) {
      echo ' padding: 10px ;';
    } ?>">
      <?php echo $contenido->contenido_descripcion; ?>
    </div>
    <?php if ($contenido->contenido_enlace) { ?>
      <div>
        <a href="<?php $contenido->contenido_enlace; ?>" <?php if ($contenido->contenido_enlace_abrir == 1) { ?>
            target="_blank" <?php } ?> class="event-btn">
          <?php if ($contenido->contenido_vermas) { ?>     <?php echo $contenido->contenido_vermas; ?>   <?php } else { ?>Ver
            Más<?php } ?>
        </a>
      </div>
    <?php } ?>
  </div>
</div>