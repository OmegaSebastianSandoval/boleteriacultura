<style>
  header {
    display: none;
  }
  footer {
    display: none;
  }
  .auth-page {
    margin: 0;
    padding: 0;
    height: 100vh;
    overflow: hidden;
    font-family: "Exo 2", system-ui, -apple-system, "Segoe UI", sans-serif;
    background: #f5f5f5;
  }

  .auth-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: url(/skins/administracion/images/imagen-fondo.jpg);
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    z-index: 0;
  }

  .auth-layout {
    position: relative;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 24px;
  }

  .auth-main {
    width: 100%;
    max-width: 440px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 95vh;
  }

  /* ========== AUTH CARD ========== */
  .auth-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 16px;
    box-shadow:
      0 8px 32px rgba(0, 0, 0, 0.08),
      0 2px 8px rgba(0, 0, 0, 0.04);
    padding: 48px 40px;
    animation: slideUp 0.6s cubic-bezier(0.23, 1, 0.320, 1);
  }

  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .auth-card-header {
    margin-bottom: 40px;
    text-align: center;
  }

  .auth-logo {
    margin: 0;
  }

  .auth-logo img {
    max-height: 80px;
    width: auto;
    display: inline-block;
    object-fit: contain;
  }

  .auth-card-content {
    width: 100%;
  }

  /* ========== FORM STRUCTURE ========== */
  .auth-form-container {
    width: 100%;
  }

  .auth-form-header {
    margin-bottom: 32px;
  }

  .auth-title {
    font-size: 28px;
    font-weight: 600;
    letter-spacing: -0.5px;
    color: #1a1a1a;
    margin: 0 0 8px 0;
    line-height: 1.2;
  }

  .auth-subtitle {
    font-size: 14px;
    color: #666666;
    font-weight: 400;
    margin: 0;
    line-height: 1.5;
  }

  .auth-form {
    width: 100%;
  }

  /* ========== FORM GROUPS & INPUTS ========== */
  .auth-form-group {
    margin-bottom: 20px;
  }

  .auth-form-group:last-of-type {
    margin-bottom: 28px;
  }

  .auth-label {
    display: block;
    font-size: 13px;
    font-weight: 500;
    color: #3a3a3a;
    margin-bottom: 8px;
    letter-spacing: 0.3px;
    text-transform: uppercase;
  }

  .auth-input {
    width: 100%;
    padding: 11px 14px;
    font-size: 15px;
    line-height: 1.6;
    color: #1a1a1a;
    background-color: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    transition: all 0.2s ease;
    font-family: inherit;
    box-sizing: border-box;
    -webkit-appearance: none;
  }

  .auth-input::placeholder {
    color: #999999;
    opacity: 1;
  }

  .auth-input:hover {
    border-color: #d0d0d0;
    background-color: #fafafa;
  }

  .auth-input:focus {
    outline: none;
    border-color: #666666;
    background-color: #ffffff;
    box-shadow: 0 0 0 3px rgba(102, 102, 102, 0.04);
  }

  .auth-input:focus::placeholder {
    color: #aaaaaa;
  }

  /* ========== ALERTS ========== */
  .auth-alert {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 14px;
    margin-bottom: 20px;
    border-radius: 8px;
    font-size: 13px;
    line-height: 1.5;
    animation: slideDown 0.3s ease;
  }

  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-8px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .auth-alert i {
    flex-shrink: 0;
    margin-top: 2px;
    font-size: 14px;
  }

  .auth-alert-error {
    background-color: #fee;
    border: 1px solid #fdd;
    color: #c41414;
  }

  .auth-alert-error i {
    color: #c41414;
  }

  /* ========== FORM FOOTER ========== */
  .auth-form-footer {
    margin-bottom: 28px;
    text-align: right;
  }

  .auth-link-secondary {
    font-size: 13px;
    color: #666666;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
  }

  .auth-link-secondary:hover {
    color: #1a1a1a;
    text-decoration: underline;
  }

  /* ========== BUTTON ========== */
  .auth-button-primary {
    width: 100%;
    padding: 11px 16px;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.2px;
    color: #ffffff;
    background-color: #1a1a1a;
    border: 1px solid #1a1a1a;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: inherit;
    -webkit-appearance: none;
  }

  .auth-button-primary:hover {
    background-color: #333333;
    border-color: #333333;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }

  .auth-button-primary:active {
    transform: translateY(1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  }

  .auth-button-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background-color: #ccc;
    border-color: #ccc;
  }

  .auth-button-primary i {
    font-size: 12px;
    opacity: 0.8;
  }

  /* ========== FOOTER ========== */
  .auth-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    text-align: center;
    padding: 20px 24px;
    font-size: 12px;
    color: rgba(255, 255, 255, 0.8);
    font-weight: 400;
    line-height: 1.6;
  }

  .auth-footer p {
    margin: 4px 0;
  }

  .auth-footer a {
    color: rgba(255, 255, 255, 1);
    text-decoration: none;
    font-weight: 500;
    transition: opacity 0.2s ease;
  }

  .auth-footer a:hover {
    opacity: 0.9;
    text-decoration: underline;
  }

  /* ========================================
    RESPONSIVE DESIGN
    ======================================== */

  @media (max-width: 768px) {
    .auth-card {
      padding: 40px 32px;
    }

    .auth-title {
      font-size: 24px;
    }

    .auth-logo img {
      max-height: 44px;
    }

    .auth-card-header {
      margin-bottom: 32px;
    }
  }

  @media (max-width: 520px) {
    .auth-layout {
      padding: 16px;
    }

    .auth-main {
      max-width: 100%;
    }

    .auth-card {
      padding: 32px 24px;
    }

    .auth-card-header {
      margin-bottom: 28px;
    }

    .auth-title {
      font-size: 22px;
      margin-bottom: 6px;
    }

    .auth-subtitle {
      font-size: 13px;
    }

    .auth-form-header {
      margin-bottom: 24px;
    }

    .auth-form-group {
      margin-bottom: 16px;
    }

    .auth-label {
      font-size: 12px;
    }

    .auth-input {
      padding: 10px 12px;
      font-size: 16px;
    }

    .auth-form-footer {
      margin-bottom: 24px;
    }

    .auth-button-primary {
      padding: 10px 14px;
      font-size: 13px;
    }

    .auth-footer {
      font-size: 11px;
      padding: 16px 16px;
    }
  }

  .footer {
    position: fixed;
    bottom: 0;
    right: 0;
    z-index: 1000;
  }

</style>

<div class="auth-page">
  <div class="auth-background"></div>
  <div class="auth-layout">
    <div class="auth-main">
      <div class="auth-card">
        <div class="auth-card-header">
          <div class="auth-logo">
            <img src="/skins/administracion/images/logo-horizontal.png" alt="Logo">
          </div>
        </div>
        <div class="auth-card-content">
          <div class="auth-form-container">
            <div class="auth-form-header">
              <h1 class="auth-title">Módulo de Validación</h1>
              <p class="auth-subtitle">Inicia sesión en tu cuenta para continuar</p>
            </div>

            <form class="auth-form" autocomplete="off" action="/validacion/index/validarusuario" method="post" novalidate>
              <?php if ($this->error_login) { ?>
                <div class="auth-alert auth-alert-error" role="alert">
                  <i class="fas fa-exclamation-circle"></i>
                  <span><?php echo $this->error_login; ?></span>
                </div>
              <?php } ?>

              <div class="auth-form-group">
                <label for="user" class="auth-label">Usuario o Correo</label>
                <input type="text" class="auth-input" id="user" name="user" placeholder="Ingresa tu usuario o correo"
                  autocomplete="username" required aria-label="Usuario o Correo">
              </div>

              <div class="auth-form-group">
                <label for="password" class="auth-label">Contraseña</label>
                <input type="password" class="auth-input" id="password" name="password" placeholder="Ingresa tu contraseña"
                  autocomplete="current-password" required aria-label="Contraseña">
              </div>

              <input type="hidden" id="csrf" name="csrf" value="<?php echo $this->csrf; ?>" />

              <button type="submit" class="auth-button-primary">
                <span>Iniciar sesión</span>
                <i class="fas fa-arrow-right"></i>
              </button>
            </form>
          </div>
        </div>
      </div>
      <div class="auth-footer footer">
        <p>&copy; <?php echo date('Y') ?> Todos los derechos reservados | Diseñado por <a
            href="https://omegasolucionesweb.com" target="_blank">OMEGA SOLUCIONES WEB</a></p>
        <p>info@omegawebsystems.com | 318 642 5229 | 350 708 7228</p>
      </div>
    </div>
  </div>
</div>


<script>
  function togglePassword (inputId, iconElement) {
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