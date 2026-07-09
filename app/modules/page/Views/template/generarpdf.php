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
$documento = $this->invitado->documento_invitado;
$nombre = $this->invitado->invitadoReserva_nombre_invitado;
$apellido = $this->invitado->invitadoReserva_apellido_invitado;
$precio = $this->reserva->reserva_total_pagar;

$invitado = $this->invitado;
?>
<table border="0" cellpadding="5" cellspacing="0" width="100%">
  <br>

  <tr>
    <!-- Columna izquierda -->
    <td width="50%" align="center" valign="top">
      <img src="/images_sales/assets/logo.png" altet="Logo" />
      <br>
      <br>
      <img src="/images/cf/PC/GranFiestaPCNew.png" alt="<?= $this->evento->evento_titulo; ?>" title="<?= $this->evento->evento_titulo; ?>"/>
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
          <td width="200" style="background-color: #ffff00; color: #222220; font-size: 18px; font-weight: bold; text-align: center;">
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
          <img src="<?= "/images_sales/qrs/".$this->boleta->boleta_uid.".png" ?>" alt="qr" width="250" height="250" />
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
        <p align="left" style="font-size: 15px; color: #fff; font-weight: 500;line-height: -25px">
         <?= $documento ?>
      </p>
      <p align="left" style="font-size: 15px; color: #fff; font-weight: 500;line-height: -25px">
       <?= $this->mesas->mesa_nombre ?>
      </p>
      <p align="left" style="font-size: 15px; color: #fff; font-weight: 500;line-height: -25px">
        Ambiente: <?= $this->ambiente->ambiente_nombre ?>
      </p>
        <p align="left" style="font-size: 15px; color: #fff; font-weight: 500;line-height: -25px">
        <?= $this->piso->piso_nombre ?>
      </p>
      <!--<p align="left" style="font-size: 15px; color: #fff; font-weight: 500;line-height: -25px">-->
      <!--  Lugar: <?= $lugar ?>-->
      <!--</p>-->

      <span style="font-size: 21px; color: #FFF; font-weight: 500;" align="right">
        <img src="/images/cf/PC/FechaPC.png"/>
      </span>

    </td>
  </tr>
</table>