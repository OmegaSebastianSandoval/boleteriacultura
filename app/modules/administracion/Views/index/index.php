<div class="auth-form-container">
  <div class="auth-form-header">
    <h1 class="auth-title">Bienvenido de vuelta</h1>
    <p class="auth-subtitle">Inicia sesión en tu cuenta para continuar</p>
  </div>

  <form class="auth-form" autocomplete="off" action="/administracion/loginuser" method="post" novalidate>
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

    <div class="auth-form-footer">
      <a href="/administracion/index/olvido" class="auth-link-secondary">¿Olvidaste tu contraseña?</a>
    </div>

    <input type="hidden" id="csrf" name="csrf" value="<?php echo $this->csrf; ?>" />

    <button type="submit" class="auth-button-primary">
      <span>Iniciar sesión</span>
      <i class="fas fa-arrow-right"></i>
    </button>
  </form>
</div>