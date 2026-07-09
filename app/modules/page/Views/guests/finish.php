<style>
  .boleteria-finish-box {
    max-width: 480px;
    margin: 60px auto;
    background: #fff;
    border: 1.5px solid #222220;
    border-radius: 3px;
    padding: 40px 36px 32px;
    text-align: center;
  }

  .boleteria-title {
    font-family: "Orelega One", serif;
    color: #222220;
    font-size: 1.6rem;
    margin-bottom: 6px;
    line-height: 1.1;
  }

  .boleteria-subtitle {
    font-size: 0.78rem;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: #7A7A7A;
    margin-bottom: 28px;
    font-weight: 600;
  }

  .boleteria-divider {
    width: 40px;
    height: 2px;
    background: #ffc107;
    margin: 0 auto 28px;
    border: none;
  }

  .boleteria-alert {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    font-size: 0.9rem;
    font-weight: 500;
    margin: 0 0 24px;
    padding: 18px 20px;
  }

  .boleteria-alert-success {
    background: #f5fdf9;
    color: #1a5c3a;
    border: 1px solid #b2dfc8;
    border-radius: 2px;
  }

  .boleteria-alert-danger {
    background: #fdf5f5;
    color: #7a1a1a;
    border: 1px solid #e2b2b2;
    border-radius: 2px;
  }

  .boleteria-icon {
    font-size: 1.8rem;
    line-height: 1;
    display: block;
    margin-bottom: 4px;
  }

  .boleteria-alert .alert-text {
    font-size: 0.88rem;
    line-height: 1.5;
  }

  .boleteria-actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
    margin-top: 8px;
  }

  .boleteria-actions .event-btn {
    font-size: 0.72rem;
    padding: 10px 20px;
    letter-spacing: 0.1em;
  }

  .boleteria-actions .btn-red {
    background-color: transparent;
    border-color: #dc3545;
    color: #dc3545;
  }

  .boleteria-actions .btn-red:hover {
    background-color: #dc3545;
    color: #fff;
  }
</style>

<div class="container">
  <div class="boleteria-finish-box">

    <div class="boleteria-title">Boletería</div>
    <p class="boleteria-subtitle">Confirmación de registro</p>
    <hr class="boleteria-divider">

    <?php if ($this->mensaje): ?>
      <div class="boleteria-alert boleteria-alert-success">
        <span class="boleteria-icon">✓</span>
        <span class="alert-text"><?= $this->mensaje ?></span>
      </div>
    <?php endif; ?>

    <?php if ($this->error): ?>
      <div class="boleteria-alert boleteria-alert-danger">
        <span class="boleteria-icon">✕</span>
        <span class="alert-text"><?= $this->error ?></span>
        <span class="alert-text" style="font-size:0.78rem;color:#999;margin-top:4px;">Por favor contáctanos para resolver el inconveniente.</span>
      </div>
    <?php endif; ?>

    <div class="boleteria-actions">
      <a href="/page/evento" class="event-btn btn-red">Otra compra</a>
      <a href="/page/servicios/info?id=<?= enc_id($this->idReserva) ?>" class="event-btn">Ver Boletas</a>
    </div>

  </div>
</div>
