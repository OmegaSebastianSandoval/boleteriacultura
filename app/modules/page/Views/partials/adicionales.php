
<div class="floating-btn">

  <?php foreach ($this->botonesFlotantes as $key => $boton) { ?>
    <a
      href="<?php echo $boton->publicidad_enlace ?>"
      target="<?php echo $boton->publicidad_tipo_enlace == 1 ? '_blank' : '' ?>" style="background: <?php echo $boton->publicidad_color_fondo ?>;"
      class="<?php echo $boton->publicidad_posicion ?>"
      data-bs-toggle="tooltip" data-bs-placement="left" data-bs-title="<?php echo $boton->publicidad_nombre ?>">
      <?php if ($boton->publicidad_texto_enlace) { ?>
        <span>
          <?php echo $boton->publicidad_texto_enlace ?>
        </span>
      <?php } ?>

      <?php if ($boton->publicidad_imagen) { ?>
        <img src="/images/<?php echo $boton->publicidad_imagen ?>" alt="floating button">
      <?php } ?>
    </a>
  <?php } ?>
</div>