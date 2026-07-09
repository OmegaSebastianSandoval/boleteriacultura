<script src='https://www.google.com/recaptcha/api.js'></script>
<!-- <?php print_r($this->eventoConfiguracion); ?> -->
<div class="bloque-content-login">
  <div class="title-header-login">
    <div class="title-sesion text-center yw">Estamos en una gran celebración</div>
  </div>

  <div class="subtitle-body-login ">
    <?php if ($this->reservaAbierta || $_GET['test'] == 1) { ?>
      <form action="/page/login/validar" method="post" id="loginForm" class="px-4">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::getInstance()->get('csrf_login')) ?>">
        <div class="row">
          <div class="col-lg-12 mb-3 mt-3">
            <label class="form-label">Número de carnet</label>
            <div class="input-group">
              <input type="text" class="form-control" name="user" id="user" required>
            </div>
            <div class="help-block with-errors"></div>
          </div>
          <div class="col-lg-12 mb-3">
            <label class="form-label">Contraseña</label>
            <div class="input-group">
              <input type="password" class="form-control" name="pass" id="pass" required>
              <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword('pass', this)">
                <i class="fa-solid fa-eye" id="eye-icon-pass"></i>
              </span>
            </div>
            <div class="help-block with-errors"></div>
          </div>
          <div class="col-12">
            <div class="d-flex justify-content-center">
              <div class="g-recaptcha mb-3 d-flex justify-content-center"
                data-sitekey="6LfFDZskAAAAAE2HmM7Z16hOOToYIWZC_31E61Sr"></div>
            </div>
          </div>
          <div class="col-12">
            <a href="/page/login/recuperar" class="d-none justify-content-center ahref">
              ¿Asignar o recuperar contraseña?
            </a>
          </div>
          <?php if ($this->idRes && $this->idRes >= 1): ?>
            <input type="hidden" name="id_res" value="<?= enc_id($this->idRes) ?>">
          <?php endif; ?>
          <div class="col-12 d-flex justify-content-center mt-3 mb-3">
            <button class="btn-login" id="btnSubmit">Ingresar</button>
          </div>
          <div id="loaderLine" style="display: none; margin-top: 15px;">
            <div class="loader"></div>
            <p class="text-center mt-2">Validando credenciales...</p>
          </div>
        </div>
      </form>
    <?php } else { ?>
      <div class="alert alert-primary text-center">
        La compra de boleteria no se encuentra habilitada. <br>Gracias por ser parte de esta gran celebración.
      </div>
    <?php } ?>
  </div>

</div>


<style>
  header {
    display: none;
  }

  .imgH {
    max-width: 100%;
    height: auto;
  }

  .img-centro {
    display: block;
    margin: auto;
  }

  .img-left {
    display: block;
    margin: 0 auto 10px auto;
  }

  .img-ultima {
    display: block;
    margin: 0 auto;
  }

  .card {
    height: 300px;
    /* Altura ejemplo */
    background-color: #f8f9fa;
    border-radius: 1rem;
  }

  .h-100vh {
    height: 85vh;
  }

  .title-header-login {
    background: #1a1631;
    width: 100%;
    margin: 0 auto;
    padding: 25px 10px 25px 10px;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  }

  .subtitle-body-login {
    width: 100%;
    margin: 0 auto;
    background: #fff;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
  }

  .linea {
    height: 2px;
    background-color: #fff;
    width: 25%;
    border: none;
    margin: auto;
  }

  .yw {
    color: rgba(254, 210, 96, 255);
  }

  .title-sesion {
    font-weight: bold;
    font-size: 25px;
  }

  .subtitle-sesion {
    color: white;
    width: 84%;
    margin: auto;
  }

  .titulo {
    font-size: 1.8rem;
    font-weight: bold;
    color: #ffc107;
  }

  .subtitulo {
    font-size: 1rem;
    color: #555;
  }

  .form-label {
    font-weight: 600;
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
      display: none;
    }

    .text-final {
      text-align: center !important;
      letter-spacing: normal !important;
    }

    footer span {
      font-size: 10px;
    }

    .h-100vh {
      height: auto;
    }

    .title-header-login {
      width: 100%;
    }

    .title-sesion {
      font-size: 20px;
    }

    .subtitle-body-login {
      width: 100%;
    }
  }
</style>


<!-- Google tag (gtag.js) -->
<!-- <script async src="https://www.googletagmanager.com/gtag/js?id=G-41X0NVLGGV"></script>
<script>
  window.dataLayer = window.dataLayer || [];

  function gtag() {
    dataLayer.push(arguments);
  }
  gtag('js', new Date());

  gtag('config', 'G-41X0NVLGGV');
</script> -->

<script>
  function togglePassword(inputId, iconElement) {
    const passwordInput = document.getElementById(inputId);
    const icon = iconElement.querySelector('i');

    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      icon.className = 'fa-solid fa-eye-slash';
    } else {
      passwordInput.type = 'password';
      icon.className = 'fa-solid fa-eye';
    }
  }
</script>