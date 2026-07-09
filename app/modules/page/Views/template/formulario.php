<div class="col-lg-12 fondo-transparente text-center text-white pt-4 pb-2 mt-lg-5 mt-4 order-5 mb-2">

  <?php if ($_GET["timeout"]) { ?>
    <!-- Mensaje de sesión expirada -->
    <div class="title-header-login">
      <div class="title-sesion text-center yw">¡Reserva expirada!</div>
    </div>

    <hr class="linea-separa mb-3">

    <div class="info-login mb-3">
      <span class="d-block f-1">
        Tu sesión de reserva ha finalizado por <b>tiempo de inactividad.</b>
        Por favor, vuelve a iniciar sesión para realizar una nueva reserva y garantizar la disponibilidad de
        mesas.
      </span>
    </div>
  <?php } elseif ($this->reservaCerrada) { ?>
    <!-- Mensaje de venta cerrada -->
    <div class="title-header-login">
      <div class="title-sesion text-center yw">¡Venta Cerrada!</div>
    </div>

    <hr class="linea-separa mb-3">

    <div class="info-login mb-3">
      <span class="d-block f-1">
        La venta de boletas ha finalizado. Gracias por tu interés.
      </span>
    </div>
  <?php } else { ?>
    <!-- Mensaje normal -->
    <?php echo $contenido->contenido_descripcion; ?>

    <hr class="linea-separa mt-2">
  <?php } ?>

  <!-- Botón o contador (solo si no está cerrada la venta) -->
  <?php if (!$this->reservaCerrada) { ?>
    <div id="boton-o-contador" class="text-center my-lg-4 my-2 position-relative d-inline-block">

      <?php if ($this->reservaAbierta) { ?>
        <!-- Solo renderizar el botón si la reserva está abierta -->
        <a id="btn-login" href="/page/login" class="btn btn-warning rounded-pill px-4 py-2 fs-5 fw-bold btn-con-espacio">
          <span>Adquiera sus boletas aquí</span>
        </a>
        <img id="img-cursor" src="/images/cf/cursor.png" alt="Cursor" class="img-cursor">
      <?php } else { ?>
        <!-- Contador de cuenta regresiva -->
        <div id="contador" class="d-flex flex-wrap justify-content-center gap-3 align-items-center">
          <div class="cuadro-tiempo">
            <div id="dias">0</div>
            <small>Días</small>
          </div>
          <div class="cuadro-tiempo">
            <div id="horas">0</div>
            <small>Horas</small>
          </div>
          <div class="cuadro-tiempo">
            <div id="minutos">0</div>
            <small>Minutos</small>
          </div>
          <div class="cuadro-tiempo">
            <div id="segundos">0</div>
            <small>Segundos</small>
          </div>
        </div>
      <?php } ?>

    </div>
  <?php } ?>

</div>


<?php
$fechaApertura = null;
if (($this->eventoConfiguracion) && !empty($this->eventoConfiguracion->evento_fecha_apertura_reserva)) {
  $fechaApertura = strtotime($this->eventoConfiguracion->evento_fecha_apertura_reserva);
}
?>
<script>
  document.addEventListener("DOMContentLoaded", () => {
    // ========================
    // 🔹 DATOS DESDE PHP
    // ========================
    const reservaAbierta = <?= $this->reservaAbierta ? 'true' : 'false' ?>;
    const reservaCerrada = <?= $this->reservaCerrada ? 'true' : 'false' ?>;
    const fechaAperturaTimestamp = <?= $fechaApertura ? $fechaApertura * 1000 : 'null' ?>; // en milisegundos

    // ========================
    // 🔹 REFERENCIAS DEL DOM
    // ========================
    const contenedor = document.getElementById("boton-o-contador");
    const contadorEl = document.getElementById("contador");

    // ========================
    // 🔹 FUNCIONES DE UI
    // ========================
    const crearBoton = () => {
      // Limpiar el contenedor
      contenedor.innerHTML = '';

      // Crear el botón
      const btnLogin = document.createElement('a');
      btnLogin.id = 'btn-login';
      btnLogin.href = '/page/login';
      btnLogin.className = 'btn btn-warning rounded-pill px-4 py-2 fs-5 fw-bold btn-con-espacio';
      btnLogin.innerHTML = '<span>Adquiera sus boletas aquí</span>';

      // Crear la imagen del cursor
      const imgCursor = document.createElement('img');
      imgCursor.id = 'img-cursor';
      imgCursor.src = '/images/cf/cursor.png';
      imgCursor.alt = 'Cursor';
      imgCursor.className = 'img-cursor';

      // Agregar al contenedor
      contenedor.appendChild(btnLogin);
      contenedor.appendChild(imgCursor);
    };

    // ========================
    // 🔹 INICIO DEL SCRIPT
    // ========================

    // Caso 1 → La venta ya cerró
    if (reservaCerrada) {
      return;
    }

    // Caso 2 → Ya está abierta desde el backend
    if (reservaAbierta) {
      // El botón ya está renderizado desde PHP
      return;
    }

    // Caso 3 → No hay fecha de apertura
    if (!fechaAperturaTimestamp) {
      // El contador ya está renderizado desde PHP
      return;
    }

    // Caso 4 → Aún no ha llegado la hora → Contador activo
    const actualizarContador = () => {
      const ahora = Date.now();
      const diferencia = fechaAperturaTimestamp - ahora;

      // Llegó la hora → Crear botón dinámicamente
      if (diferencia <= 0) {
        console.log("🚀 Hora alcanzada: activando botón");
        crearBoton();
        clearInterval(intervalo);
        return;
      }

      // Cálculo del tiempo restante
      const totalSegundos = Math.floor(diferencia / 1000);
      const dias = Math.floor(totalSegundos / (3600 * 24));
      const horas = Math.floor((totalSegundos % (3600 * 24)) / 3600);
      const minutos = Math.floor((totalSegundos % 3600) / 60);
      const segundos = totalSegundos % 60;

      // Actualizar DOM
      const diasEl = document.getElementById("dias");
      const horasEl = document.getElementById("horas");
      const minutosEl = document.getElementById("minutos");
      const segundosEl = document.getElementById("segundos");

      if (diasEl) diasEl.textContent = dias;
      if (horasEl) horasEl.textContent = horas.toString().padStart(2, "0");
      if (minutosEl) minutosEl.textContent = minutos.toString().padStart(2, "0");
      if (segundosEl) segundosEl.textContent = segundos.toString().padStart(2, "0");
    };

    // Primera actualización inmediata
    actualizarContador();

    // Actualizar cada segundo
    const intervalo = setInterval(actualizarContador, 1000);
  });
</script>


<style>
  header {
    display: none;
  }

  body {
    background-image: url('/images/cf/PC/FondoPC.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    width: 100vw;
    height: 100vh;
  }

  .title-header-login {
    margin: 0 auto;
    padding: 5px 5px 15px 5px;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  .yw {
    color: rgba(254, 210, 96, 255);
  }

  .title-sesion {
    font-weight: bold;
    font-size: 25px;
  }

  .imgH {
    max-width: 100%;
  }

  .cuadro-tiempo {
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid white;
    padding: 10px 15px;
    border-radius: 10px;
    min-width: 110px;
  }

  .cuadro-tiempo div {
    font-size: 65px;
    font-weight: bold;
  }

  .cuadro-tiempo small {
    font-size: 15px;
    display: block;
    margin-top: 4px;
    color: #fff;
  }

  .f-1 {
    font-size: 20px;
    line-height: 25px;
  }

  .linea-separa {
    height: 3px;
    width: 70%;
    margin: auto;
    background: white;
  }

  .text-final {
    width: 70%;
    margin: 0 auto;
    font-size: 23px;
    text-align: justify;
    letter-spacing: 6.3px;
    color: white;
    /* si deseas blanco */
    word-break: break-word;
  }

  .img-left {
    width: 30vw;
  }

  .img-cursor {
    position: absolute;
    top: 15px;
    /* Ajusta según lo que necesites */
    right: -18%;
    transform: translateX(-50%);
    width: 70px;
    z-index: 2;
  }

  .img-centro {
    left: 25px;
  }

  @media screen and (max-width: 985px) {
    .fondo-transparente {
      background-color: rgba(0, 0, 0, 0.6);
      width: 100%;
      margin: auto;
    }

    .img-ultima {
      width: 45%;
    }

    .f-1 {
      font-size: 15px;
      line-height: 17px;
    }

    .f-2 {
      font-size: 30px;
      padding-top: 15px;
      width: 80%;
      margin: auto;
      line-height: 35px;
    }

    .small-none {
      display: none !important;
    }

    .img-centro {
      width: 60%;
      left: 19px;
      position: relative;
    }

    /* Responsive contador */
    .cuadro-tiempo {
      min-width: 70px;
      padding: 6px 8px;
    }

    .cuadro-tiempo div {
      font-size: 36px;
    }

    .cuadro-tiempo small {
      font-size: 12px;
    }

    #contador {
      gap: 8px;
    }

    .img-cursor {
      width: 48px;
      top: 8px;
      right: -10%;
    }

    .text-final {
      text-align: center !important;
      letter-spacing: normal !important;
    }

    footer span {
      font-size: 10px;
    }
  }
</style>