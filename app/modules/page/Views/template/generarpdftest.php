<?php
$meses = [
  'January' => 'enero',
  'February' => 'febrero',
  'March' => 'marzo',
  'April' => 'abril',
  'May' => 'mayo',
  'June' => 'junio',
  'July' => 'julio',
  'August' => 'agosto',
  'September' => 'septiembre',
  'October' => 'octubre',
  'November' => 'noviembre',
  'December' => 'diciembre'
];

$fecha = new DateTime($this->evento->evento_fecha);
$dia = $fecha->format('d');
$mes = $meses[$fecha->format('F')];
$anio = $fecha->format('Y');

$pedidoId = $this->reserva->id;
$evento = $this->evento->evento_titulo;
$cantidad = intval($this->reserva->reserva_total_personas);


$total = $this->evento->reserva_total_pagar;

$entidad = $this->reserva->reserva_franquicia;
$email = $this->reserva->reserva_correo;
$lugar = $this->evento->evento_lugar;
$documento = $this->reserva->reserva_numero_carnet;
$nombre = $this->reserva->reserva_nombre_cliente;
$precio = $this->reserva->reserva_total_pagar;
?>
<table border="0" cellpadding="5" cellspacing="0" width="100%">
  <br>

  <tr>
    <!-- Columna izquierda -->
    <td width="50%" align="center" valign="top">
      <img src="/images_sales/assets/logo.png" altet="Logo" />
      <br>
      <br>
      <img src="/images/cf/PC/GranFiestaPCNew.png" alt="<?= $this->evento->evento_titulo; ?>"
        title="<?= $this->evento->evento_titulo; ?>" />
    </td>

    <!-- Columna derecha -->
    <td width="50%" valign="top" align="center">
      <br>
      <br>
      <table border="0" cellpadding="6" cellspacing="0" width="100%">
        <tr>
          <!-- Celda vacía que ocupa el 100% menos 200px -->
          <td width="57%">&nbsp;</td>

          <!-- Celda con el identificador -->
          <td width="200"
            style="background-color: #ffff00; color: #222220; font-size: 18px; font-weight: bold; text-align: center;">
            <?= $this->boleta->boleta_uid ?>
          </td>
        </tr>
      </table>

      <br>
      <br>

      <table border="0" valign="top" align="center" width="100%">
        <tr>
          <!-- Celda con el identificador -->
          <td width="100%">
            <?php
            // Verificar primero en la ruta de QRs nuevos
            $rutaQRNueva = "/images_sales/qrs_news/" . $this->boleta->boleta_uid . ".png";
            // Verificar en la ruta de QRs originales  
            $rutaQROriginal = "/images_sales/qrs/" . $this->boleta->boleta_uid . ".png";

            $rutaQRFinal = $rutaQRNueva; // Por defecto usar la nueva
            
            // Verificar cuál existe físicamente
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $rutaQRNueva)) {
              $rutaQRFinal = $rutaQRNueva;
            } elseif (file_exists($_SERVER['DOCUMENT_ROOT'] . $rutaQROriginal)) {
              $rutaQRFinal = $rutaQROriginal;
            }
            ?>
            <img src="<?= $rutaQRFinal ?>" alt="qr" width="250" height="250" />
          </td>
        </tr>
      </table>

      <br>

      <p align="left" style="font-size: 21px; color: #ffcc00; font-weight: 500;line-height: 15px">
        <?= $evento ?>
      </p>
      <p align="left" style="font-size: 15px; color: #fff; font-weight: 500;line-height: -25px">
        Sr(a). <?= $nombre ?>
      </p>
      <?php foreach ($this->mesasInfo as $mesa): ?>
        <p align="left" style="font-size: 15px; color: #fff; font-weight: 500;line-height: -25px">
          Mesa: <?= $mesa->mesa_codigo ?>
        </p>
        <p align="left" style="font-size: 15px; color: #fff; font-weight: 500;line-height: -25px">
          Ambiente: <?= $mesa->ambienteinfo->ambiente_nombre ?>
        </p>
      <?php endforeach; ?>
      <p align="left" style="font-size: 15px; color: #fff; font-weight: 500;line-height: -25px">
        Lugar: <?= $lugar ?>
      </p>

      <span style="font-size: 21px; color: #FFF; font-weight: 500;" align="right">
        <img src="/images/cf/PC/FechaPC.png" />
      </span>

    </td>
  </tr>
</table>