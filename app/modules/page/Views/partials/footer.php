<span class="ftr-copy">
  Copyright &copy; <?php echo date('Y') ?> Corporación Club El Nogal &nbsp;·&nbsp; Todos los derechos reservados
</span>

<script>
  let userId = "<?php echo $_SESSION['socio']->SBE_CODI; ?>";
  if (userId) {
    let heartbeatInterval;
    function startHeartbeat() {
      heartbeatInterval = setInterval(() => {
        fetch("/page/index/heartbeat", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ userId })
        }).catch(() => {});
      }, 5000);
    }
    startHeartbeat();
  }
</script>

<style>
  footer {
    position: fixed !important;
    bottom: 0;
    left: 0;
    right: 0;
    width: 100%;
    height: 30px !important;
    background: rgba(5, 5, 5, 0.86) !important;
    backdrop-filter: blur(22px);
    -webkit-backdrop-filter: blur(22px);
    border-top: 1px solid rgba(255, 193, 7, 0.18);
    box-shadow: 0 -2px 20px rgba(0, 0, 0, 0.35);
    z-index: 999;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .ftr-copy {
    font-size: 0.65rem;
    color: rgba(255, 255, 255);
    letter-spacing: 0.5px;
    font-weight: 500;
  }

  /* Add bottom padding to main so content isn't hidden behind footer */
  .contenedor-general {
    padding-bottom: 36px;
  }
</style>
