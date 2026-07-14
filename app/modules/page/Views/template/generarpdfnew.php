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
$apellido = $this->reserva->reserva_apellido_cliente;
$precio = $this->reserva->reserva_total_pagar;
$invitado = $this->invitado;

// Acceso null-safe con ?? (no isset()/ternario): si pisoInfo/ambiente vienen null
// por cualquier motivo, esto NO debe emitir un Warning de PHP, porque este template
// se captura con ob_start()/ob_get_clean() (ver View::getRoutPHP) y CUALQUIER salida,
// incluidos los warnings, se inyecta tal cual en el HTML que arma el PDF con TCPDF,
// rompiendo el parseo y dejando la boleta en blanco.
$esSillaPdf = ($this->mesasInfo->mesa_tipo ?? null) === 'silla';
$etiquetaElementoPdf = $esSillaPdf ? 'Silla' : 'Mesa';
$mesaNombrePdf = $this->mesasInfo->mesa_nombre ?? '';
$pisoNombrePdf = $this->mesasInfo->pisoInfo->piso_nombre ?? '';
$ambienteNombrePdf = $this->ambiente->ambiente_nombre ?? '';
$boletaUidPdf = $this->boleta->boleta_uid ?? '';
$invitadoNombrePdf = trim(($this->invitado->invitadoReserva_nombre_invitado ?? '') . ' ' . ($this->invitado->invitadoReserva_apellido_invitado ?? ''));
$invitadoDocumentoPdf = $this->invitado->documento_invitado ?? '';
?>
<table border="0" cellpadding="5" cellspacing="0" width="100%" style="z-index: 99999;">
  <tr>
    <td style="height:230px">&nbsp;</td>
  </tr>

  <tr align="center" style="text-align:center">
    <td width="5%">&nbsp;</td>
    <td width="90%" valign="top" align="center"
      style="text-align: center; vertical-align: top;background-color: #fff; ">
      <br>
      <br>
      <table border="0" valign="top" align="center" width="100%" style="margin: 0 auto;">
        <tr>
          <!-- Celda con el identificador -->
          <td width="100%" style="text-align: center;">
            <img src="<?= "/images_sales/qrs_news/" . $boletaUidPdf . ".png" ?>" alt="qr" width="150"
              height="150" />
          </td>
        </tr>
      </table>
      <p style="font-size: 13px; color: #000; font-weight: 500; line-height: 5px; text-align: center; margin: 0;">
        <?= $invitadoNombrePdf ?>
      </p>
      <p style="font-size: 13px; color: #000; font-weight: 500; line-height: 5px; text-align: center; margin: 0;">
        <?= $invitadoDocumentoPdf ?>
      </p>
      <br>
      <p style="font-size: 13px; color: #000; font-weight: 500; line-height: 5px; text-align: center; margin: 0;">
        <?= $etiquetaElementoPdf ?>: <?= $mesaNombrePdf ?>
      </p>
      <!-- <p style="font-size: 13px; color: #000; font-weight: 500; line-height: 5px; text-align: center; margin: 0;">
        <?= $etiquetaElementoPdf ?> c贸digo: <?= $this->mesasInfo->mesa_codigo ?? '' ?>
      </p> -->
      <p style="font-size: 13px; color: #000; font-weight: 500; line-height: 5px; text-align: center; margin: 0;">
        <?= $pisoNombrePdf ?>
      </p>
      <p style="font-size: 13px; color: #000; font-weight: 500; line-height: 5px; text-align: center; margin: 0;">
        Ambiente: <?= $ambienteNombrePdf ?>
      </p>
      <p style="font-size: 13px; color: #000; font-weight: 500; line-height: 5px; text-align: center; margin: 0;">
        <!--Lugar: <?= $lugar ?>-->
      </p>

      <!-- <p valign="middle"
        style="border: 1px solid #000; font-size: 18px; font-weight: bold; text-align: center; width: 100px; margin: 10px auto 0 auto;">
        <?= $boletaUidPdf ?>
      </p> -->
      <table border="0" align="center" width="100%" cellpadding="10">
        <tr align="center">
          <td width="25%"></td>
          <td width="50%" style="text-align: center; border: 1px solid #000;">
            <b><?= $boletaUidPdf ?></b>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="height: 30px;">&nbsp;</td>
        </tr>
      </table>
      <!-- espacio en blanco -->

    </td>

    <td width="5%">&nbsp;</td>

  </tr>
</table>