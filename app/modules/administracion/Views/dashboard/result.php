<?php

/**
 * Vista para mostrar el resultado del reinicio de boletería
 */
?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h1><?php echo $this->titlesection; ?></h1>
      <div class="alert <?php echo $this->success ? 'alert-success' : 'alert-danger'; ?>" role="alert">
        <?php echo $this->message; ?>
      </div>
      <a href="/administracion/dashboard" class="btn btn-primary">Volver al Dashboard</a>
    </div>
  </div>
</div>