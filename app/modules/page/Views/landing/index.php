<!-- CSS Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- JS Bootstrap + Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

<link rel="icon" href="https://elnogalwellnessnuts.com/wp-content/uploads/2022/04/Logo-El-Nogal-12-e1653450988352-100x100.png" sizes="32x32">

<div class="container mt-5">
    <div class="row g-0">
        
        <!-- Solo visible en responsive -->
        <div class="col-12 d-lg-none order-1  mb-5 mb-lg-0 mt-5 mt-lg-0">
            <img src="/images/cf/Responsive/LogosNogalResp.png" class="imgH">
        </div>

        <!-- GranFiestaPC - Visible en todos los tamaños -->
        <div class="col-12 col-lg-8 order-3 order-lg-3 mb-5 mb-lg-0 mt-5 mt-lg-0">
            <div class="d-flex justify-content-center">
                <img src="/images/cf/PC/GranFiestaPC.png" class="imgH img-centro">
            </div>
        </div>

        <!-- LogosNogalPC - Visible en todos los tamaños -->
        <div class="col-lg-2 d-none d-lg-block order-2 order-lg-4">
            <img src="/images/cf/PC/LogosNogalPC.png" class="imgH">
        </div>

        <div class="col-12 col-lg-2 order-4 order-lg-2 text-center text-lg-start mt-5 mt-lg-0 mb-5 mb-lg-0">
            <img src="/images/cf/PC/FechaPC.png" class="imgH img-ultima">
        </div>




        <div class="col-lg-12 fondo-transparente text-center text-white py-4 mt-4 order-5">
            <span class="d-block f-1">Mejoramos nuestra experiencia con <b>boletería 100% en línea.</b></span>
            <span class="d-block f-1">¡Fácil, rápido y sin filas!</span>
            <span class="d-block mb-2 f-2">Plataforma digital disponible el jueves 14 de agosto desde las 9 a.m.</span>

            <hr class="linea-separa">

            <div class="d-flex flex-column flex-lg-row align-items-center justify-content-center gap-3 mt-3 mb-2" id="cuenta-regresiva">
                <div class="text-center">
                    <span class="d-block mb-2 fs-5">Faltan:</span>
                </div>
                <div class="d-flex gap-3">
                    <div class="cuadro-tiempo text-center">
                        <div id="dias">--</div>
                        <small>Días</small>
                    </div>
                    <div class="cuadro-tiempo text-center">
                        <div id="horas">--</div>
                        <small>Horas</small>
                    </div>
                    <div class="cuadro-tiempo text-center">
                        <div id="minutos">--</div>
                        <small>Minutos</small>
                    </div>
                    <div class="cuadro-tiempo text-center">
                        <div id="segundos">--</div>
                        <small>Segundos</small>
                    </div>
                </div>
            </div>

            <span class="d-block text-final mt-4">para el inicio de la venta de boleteria</span>
        </div>
    </div>
</div>


<style>
@import url('https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap');

body {
  font-family: 'Raleway', sans-serif;
}

body {
    background-image: url('/images/cf/PC/FondoPC.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    max-width: 100vw;
    max-height: 100vh;
}

.imgH {
    max-width: 100%;
}

.fondo-transparente {
    background-color: rgba(0, 0, 0, 0.6);
    width: 70%;
    margin: auto;
}

.cuadro-tiempo {
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid white;
    padding: 10px 15px;
    border-radius: 10px;
    min-width: 128px;
}

.cuadro-tiempo div {
    font-size: 24px;
    font-weight: bold;
}

.cuadro-tiempo small {
    font-size: 12px;
    display: block;
    margin-top: 4px;
    color: #fff;
}

.f-1 {
    font-size: 22px;
    line-height: 28px;
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
  color: white; /* si deseas blanco */
  word-break: break-word;
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
        font-size: 35px;
        line-height: 50px;
    }

    .f-2 {
        font-size: 30px;
        padding-top: 35px;
        width: 80%;
        margin: auto;
    }

    .small-none {
        display: none !important;
    }

    .img-centro {
        width: 65%;
        left: 35px;
        position: relative;
    }

    .text-final {
        text-align: center !important;
        letter-spacing: normal !important;
    }
}

</style>


<script>
function actualizarCuentaRegresiva() {
    const fechaObjetivo = new Date("August 14, 2025 09:00:00").getTime();
    const ahora = new Date().getTime();
    const diferencia = fechaObjetivo - ahora;

    if (diferencia <= 0) {
        document.getElementById("cuenta-regresiva").innerHTML = "¡Ya está disponible!";
        return;
    }

    const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
    const horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
    const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);

    document.getElementById("dias").textContent = dias;
    document.getElementById("horas").textContent = horas;
    document.getElementById("minutos").textContent = minutos;
    document.getElementById("segundos").textContent = segundos;
}

actualizarCuentaRegresiva();
setInterval(actualizarCuentaRegresiva, 1000);
</script>

<!-- Google tag (gtag.js) -->
<!-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-41X0NVLGGV"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-41X0NVLGGV');
</script> -->