<div class="slider-principal">
  <div id="carouselprincipal<?php echo $this->seccionbanner; ?>" class="carousel slide" data-bs-ride="carousel">
    <?php if (count($this->banners) > 1) { ?>
      <div class="carousel-indicators">
        <?php foreach ($this->banners as $key => $banner) { ?>
          <button type="button" data-bs-target="#carouselprincipal<?php echo $this->seccionbanner; ?>"
            data-bs-slide-to="<?php echo $key; ?>" class="<?php if ($key == 0) { ?>active <?php } ?>" aria-current="true"
            aria-label="Slide <?php echo $key + 1; ?>"></button>
        <?php } ?>
      </div>
    <?php } ?>

    <div class="carousel-inner">
      <?php foreach ($this->banners as $key => $banner) { ?>
        <div class="carousel-item <?php if ($key == 0) { ?>active <?php } ?>">
          <?php if ($banner->publicidad_enlace) { ?>
            <a href="<?php echo $banner->publicidad_enlace; ?>" <?php if ($banner->publicidad_tipo_enlace == 1) { ?>
                target="blank" <?php } ?>>
            <?php } ?>

            <?php if ($this->id_youtube($banner->publicidad_video) != false) { ?>
              <div class="fondo-video-youtube">
                <div class="banner-video-youtube" id="videobanner<?php echo $banner->publicidad_id; ?> "
                  data-video="<?php echo $this->id_youtube($banner->publicidad_video); ?>"></div>
              </div>
            <?php } else { ?>
              <!--<div class="fondo-imagen" style="background-image: url(/images/<?php echo $banner->publicidad_imagen; ?>);"></div>-->
              <div class="fondo-imagen d-none d-md-flex justify-content-center align-items-center">
                <img src="/images/<?php echo $banner->publicidad_imagen; ?>" alt="Imagen Banner Nogal"
                  class="img-fluid imagen-banner">
                <!-- <h2><?php echo $banner->publicidad_nombre; ?></h2> -->
              </div>
              <!-- <img src="/images/<?php echo $banner->publicidad_imagen; ?>" style="width:100%;" class="fondo-imagen"> -->
              <div class="fondo-imagen-responsive d-md-none d-flex justify-content-center align-items-center">
                <img src="/images/<?php echo $banner->publicidad_imagenresponsive; ?>" alt="Imagen Responsive Nogal"
                  class="img-fluid imagen-responsive">
                <!-- <h2><?php echo $banner->publicidad_nombre; ?></h2> -->
              </div>
              <!-- <img src="/images/<?php echo $banner->publicidad_imagenresponsive; ?>" style="width:100%;" class="fondo-imagen-responsive"> -->
            <?php } ?>
            <?php if ($banner->publicidad_enlace) { ?>

            </a>
          <?php } ?>

        </div>
      <?php } ?>
    </div>
    <?php if (count($this->banners) > 1) { ?>
      <button type="button" class="carousel-control-prev"
        data-bs-target="#carouselprincipal<?php echo $this->seccionbanner; ?>" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button type="button" class="carousel-control-next"
        data-bs-target="#carouselprincipal<?php echo $this->seccionbanner; ?>" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    <?php } ?>
  </div>
</div>