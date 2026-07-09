<div style="width: 100%; background: #6e6e6e20; padding: 50px; font-size: 15px;">
  <table style="max-width: 600px; background: #FFF; border: 2px solid #266F31; margin: auto; padding: 20px;">
    <tr>
      <td style="background-color: #266F31;">
        <img src="/skins/page/images/logo.png" alt="" height="50">
      </td>
    </tr>
    <tr>
      <td style="padding: 10px 20px; padding-bottom: 20px;">
        <span style="color: #333333;">
          Hola <?php echo $this->data['sbe_nomb'] ?>, para Crear tu contraseña, haz click en el siguiente enlace:
        </span>
      </td>
    </tr>
    <tr>
      <td style="padding: 3px 20px;">
        <a href="<?php echo $this->url ?>">
          <?php echo $this->url ?>
        </a>
      </td>
    </tr>
  </table>
</div>