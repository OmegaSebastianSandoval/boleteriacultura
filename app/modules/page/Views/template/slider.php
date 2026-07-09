<div>
  <?php

  if ($columna->contenido_titulo_ver == 1) {
    echo '<h2 class="contenido-disenio-title">' . $columna->contenido_titulo . '</h2>';
  }
  ?>
  <?php echo $columna->contenido_descripcion; ?>
  <div id="slider_<?php echo $columna->contenido_id; ?>"
    class="slider_<?php echo $columna->contenido_id; ?>  sliderCont w-100 shadow-sm">

    <?php foreach ($slidercontent as $slider): ?>
      <?php $slider = $slider["nietos"];
      // print_r($slider->contenido_descripcion);
      ?>

      <div class=" itemSlider itemSlider_<?php echo $columna->contenido_id; ?>">
        <div class="row w-100">

          <div class="col-lg-6">

            <?php if ($slider->contenido_imagen) { ?>
              <img class="img-slider img-fluid" src="/images/<?php echo $slider->contenido_imagen; ?>"
                alt="<?php echo $slider->contenido_titulo; ?>">
            <?php } else { ?>
              <img class="img-slider img-fluid" src="/assets/pic7.jpg" alt="<?php echo $slider->contenido_titulo; ?>">

            <?php } ?>
          </div>
          <div class="col-lg-6 content-slider content-slider_<?php echo $columna->contenido_id; ?>">



            <?php if ($slider->contenido_descripcion != '') { ?>
              <?php if ($slider->contenido_titulo_ver == 1) {
                echo '<h3 class="titulo-slider">' . $slider->contenido_titulo . '</h3>';
              } ?>
              <div class="descripcion-slider">

                <?php echo $slider->contenido_descripcion; ?>
              </div>
            <?php } ?>

            <?php if ($slider->contenido_introduccion != '') { ?>
              <div class="introduccion-slider">
                <?php echo $slider->contenido_introduccion; ?>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<script>
  // console.log(<?php echo $columna->contenido_id; ?>);

  $('#slider_<?php echo $columna->contenido_id; ?>').slick({
    infinity: false,
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 2000,
    dots: false,
    arrows: true,

    responsive: [{
      breakpoint: 1200,
      settings: {
        infinity: false,

        slidesToShow: 1,
        slidesToScroll: 1,
        dots: false,
        arrows: true
      }
    },
    {
      breakpoint: 900,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1,
        dots: false,
        arrows: true
      }
    },
    {
      breakpoint: 770,
      settings: {
        slidesToScroll: 1,
        dots: false,
        arrows: true
      }
    },
    ]
  });

</script>