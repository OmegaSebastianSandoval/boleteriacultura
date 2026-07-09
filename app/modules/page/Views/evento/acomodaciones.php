<div class="container event-detail-bx">
  <form action="/page/evento/guardarAcomodacion" method="post" class="row" id="reserva">
    <div class="col-12">
      <h1 class="title text-center">
        Tus lugares
      </h1>
    </div>
    <div class="col-md-3">
      <div class="row h-100 align-items-center align-content-center">
        <div class="col-12">
          <h4 class="title">
            Numero de asientos: <?php echo $this->invitados_reserva ?>
          </h4>
        </div>
        <div class="col-12 d-flex align-items-center">
          <i class="asiento fa-solid fa-chair"></i>
          Disponible
        </div>
        <div class="col-12 d-flex align-items-center">
          <i class="asiento fa-solid fa-chair seleccionado"></i>
          Seleccionado
        </div>
        <div class="col-12 d-flex align-items-center">
          <i class="asiento fa-solid fa-chair ocupado"></i>
          Ocupado
        </div>
      </div>
    </div>
    <div class="col-md-9 mt-5">
      <div class="row justify-content-end">
        <?php
        function numero_a_letra($numero)
        {
          if ($numero < 26) {
            return chr(65 + $numero);
          } else {
            $primeraLetra = chr(65 + floor($numero / 26) - 1);
            $segundaLetra = chr(65 + ($numero % 26));
            return $primeraLetra . $segundaLetra;
          }
        }

        $ancho = $this->acomodacion->acomodaciones_ancho;
        $alto = $this->acomodacion->acomodaciones_largo;
        $cadena = $this->acomodacion->acomodaciones_asientos;

        // Paso 1: Crear un arreglo con los asientos ocupados
        $asientos_ocupados = [];
        foreach ($this->invitados as $invitado) {
          $partes = explode(",", $invitado->OBSERVACION);
          $acomodacion = explode("=>", $partes[3]);
          $asiento = trim($acomodacion[1]);
          $asientos_ocupados[] = $asiento;
        }
        foreach ($this->socios as $socio) {
          $asientos_ocupados[] = $socio->orden;
        }

        $contador = 0;

        echo "<div class='acomodaciones'>";

        for ($i = 0; $i < $alto; $i++) {
          echo "<div class='fila'>";
          echo "<span class='numero-fila'>" . ($i + 1) . "</span>";

          for ($j = 0; $j < $ancho; $j++) {
            $caracter = $cadena[$contador];
            $place = numero_a_letra($j) . ($i + 1);

            // Verificar si el asiento está ocupado
            $clase_ocupado = in_array($place, $asientos_ocupados) ? ' ocupado' : '';

            if ($caracter == '1') {
              echo "<i class='asiento fa-solid fa-chair" . $clase_ocupado . "' data-place='" . $place . "'></i>";
            } else {
              echo "<i class='asiento eliminado" . $clase_ocupado . "' data-place='" . $place . "'></i>";
            }

            $contador++;
          }
          echo "</div>";
        }

        echo "<div class='letras-columnas'>";
        echo "<span class='letra-columna'></span>";
        for ($j = 0; $j < $ancho; $j++) {
          echo "<span class='letra-columna'>" . numero_a_letra($j) . "</span>";
        }
        echo "</div>";
        echo "</div>";

        ?>
      </div>
    </div>
    <div class="col-12 d-flex justify-content-center mt-5">
      <input type="hidden" id="campoOculto" name="asientos_seleccionados" value="">
      <input type="hidden" id="" name="reserva" value="<?php echo $_GET['id']; ?>">
      <input type="hidden" id="numInvitados" name="" value="<?php echo $this->invitados_reserva ?>">
      <button type="submit" class="btn-guardar">Guardar</button>
    </div>
  </form>
</div>
<style>
  .acomodaciones {
    overflow-x: auto;
  }

  .asiento {
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 24px;
    margin: 5px;
    width: 30px;
    height: 30px;
    aspect-ratio: 1/1;
    color: #222220;
  }

  .numero-fila {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 5px;
    width: 30px;
    height: 30px;
    aspect-ratio: 1/1;
    color: #222220;
  }

  .letra-columna {
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 5px;
    width: 30px;
    height: 30px;
    aspect-ratio: 1/1;
    color: #222220;
  }

  .asiento:not(.eliminado) {
    cursor: pointer;
  }

  .asiento.ocupado {
    color: red;
    cursor: not-allowed;
  }

  .asiento.seleccionado {
    color: green;
  }

  .eliminado {
    opacity: 0.5;
  }

  .fila {
    display: flex;
    align-items: center;
  }

  .numero-fila,
  .letra-columna {
    font-size: 20px;
    margin: 5px;
    display: inline-block;
    width: 30px;
    text-align: center;
    font-family: "Orelega One";
  }

  .letras-columnas {
    display: flex;
    text-align: center;
    font-family: "Orelega One";
  }

  .acomodaciones {
    width: auto;
    margin: auto;
  }

  .btn-guardar {
    border: 1px solid #222220;
    background-color: transparent;
    color: #222220;
    padding: 12px 20px;
    border-radius: 0;
    font-size: 0.8rem;
    letter-spacing: 3px;
    text-transform: uppercase;
    font-weight: 700;
    transition: 300ms ease;
    text-decoration: none;
  }

  .btn-guardar:hover {
    background-color: #222220;
    color: #fff;
    cursor: pointer;
    transition: 300ms ease;
  }
</style>

<script>
  $(document).ready(function () {
    let maxInvitados = $('#numInvitados').val();
    let asientosSeleccionados = [];

    // Función para actualizar el campo oculto
    function actualizarCampoOculto () {
      $("#campoOculto").val(asientosSeleccionados.join(","));
    }

    $(".asiento").click(function () {
      // Verificar que no esté ocupado o eliminado
      if (!$(this).hasClass("ocupado") && !$(this).hasClass("eliminado")) {
        // Si el asiento ya está seleccionado, deseleccionarlo
        if ($(this).hasClass("seleccionado")) {
          $(this).removeClass("seleccionado");
          let indice = asientosSeleccionados.indexOf($(this).attr("data-place"));
          if (indice > -1) {
            asientosSeleccionados.splice(indice, 1);
          }
        } else if (asientosSeleccionados.length < maxInvitados) {
          // Si no se ha alcanzado el límite, seleccionar el asiento
          $(this).addClass("seleccionado");
          asientosSeleccionados.push($(this).attr("data-place"));
        }

        actualizarCampoOculto();
      }
    });
    $("#reserva").submit(function (e) {
      if (asientosSeleccionados.length != maxInvitados) {
        e.preventDefault();  // Detiene el envío del formulario
        alert("Por favor, selecciona " + maxInvitados + " asientos antes de continuar.");
      }
    });
  });
</script>