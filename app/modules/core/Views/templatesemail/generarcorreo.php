<?php

/* echo "<pre>";
print_r($this->boletas);
echo "</pre>"; */
function encryptString($string, $key = 'omeganogal2025')
{
  $iv = openssl_random_pseudo_bytes(16);
  $encrypted = openssl_encrypt($string, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
  return base64_encode($iv . $encrypted);
}

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

$resevaId = $this->reserva->id;
$evento = $this->evento->evento_titulo;
$cantidad = intval($this->reserva->reserva_total_personas);


$total = $this->evento->reserva_total_pagar;
if ($this->reserva->reserva_metodo_pago == 'cargo') {
  $entidad = "Cargo a la acción - " . $this->reserva->reserva_numero_cuotas . " cuotas";
} else {

  $entidad = $this->reserva->reserva_franquicia;
}
$email = $this->reserva->reserva_correo;
$lugar = $this->evento->evento_lugar;
$documento = $this->reserva->reserva_documento;
$nombre = $this->reserva->reserva_nombre_cliente;
$precio = $this->reserva->reserva_total_pagar;

// Datos de facturación electrónica diligenciados por el cliente (no confundir
// con el nombre/documento/correo del titular de la reserva usados arriba).
$facturaRazonSocial = $this->reserva->reserva_fact_razon;
$facturaNit = $this->reserva->reserva_fact_nit;
$facturaDireccion = $this->reserva->reserva_fact_dire;
$facturaTelefono = $this->reserva->reserva_fact_tele;
$facturaCorreo = $this->reserva->reserva_fact_mail;

?>

<div marginwidth="0" marginheight="0" style="background-color:#FFF;padding:0;text-align:center" bgcolor="#FFF">
  <table width="100%" style="background-color:#FFF" bgcolor="#FFF">
    <tbody>
      <tr>
        <td></td>
        <td width="600">
          <div dir="ltr" style="margin:0 auto;padding:70px 0;width:100%;max-width:600px" width="100%">
            <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
              <tbody>
                <tr>
                  <td align="center" valign="top">
                    <div style="background-color:#FFF;padding-top:20px; padding-bottom:20px" width="100%">

                      <p style="margin:0"><img
                          src="https://nogalencasa.com/corte/logonegro.png"
                          alt="https://www.clubelnogal.com/"
                          style="border:none;display:inline-block;font-size:14px;font-weight:bold;height:auto;outline:none;text-decoration:none;text-transform:capitalize;vertical-align:middle;max-width:100px;margin-left:0;margin-right:0;">


                      </p>
                    </div>
                    <table border="0" cellpadding="0" cellspacing="0" width="100%"
                      style="background-color:#fff;border:1px solid #dedede;border-radius:3px" bgcolor="#fff">
                      <tbody>
                        <tr>
                          <td align="center" valign="top">

                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                              style="background-color:#0d3a58;color:#fff;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:Helvetica Neue,Helvetica,Roboto,Arial,sans-serif;border-radius:3px 3px 0 0"
                              bgcolor="#0d3a58">
                              <tbody>
                                <tr>
                                  <td style="padding:36px 48px;display:block">
                                    <h1
                                      style="font-family:Helvetica Neue,Helvetica,Roboto,Arial,sans-serif;font-size:30px;font-weight:300;line-height:150%;margin:0;text-align:left;color:#fff;background-color:inherit"
                                      bgcolor="inherit">Gracias por tu <span>compra</span></h1>
                                  </td>
                                </tr>
                              </tbody>
                            </table>

                          </td>
                        </tr>
                        <tr>
                          <td align="center" valign="top">

                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                              <tbody>
                                <tr>
                                  <td valign="top" style="background-color:#fff" bgcolor="#fff">

                                    <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                      <tbody>
                                        <tr>
                                          <td valign="top" style="padding:48px 48px 32px">
                                            <div
                                              style="color:#636363;font-family:Helvetica Neue,Helvetica,Roboto,Arial,sans-serif;font-size:14px;line-height:150%;text-align:left"
                                              align="left">

                                              <p style="margin:0 0 16px">Hola, <?= $nombre ?></p>
                                              <p style="margin:0 0 16px">Hemos terminado de procesar tu compra.</p>

                                              <h2
                                                style="color:#0d3a58;display:block;font-family:Helvetica Neue,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">

                                                Compra #<?= $resevaId ?> <br><?= "$dia de $mes de $anio"; ?></h2>

                                              <div style="margin-bottom:40px">
                                                <table cellspacing="0" cellpadding="6" border="1"
                                                  style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif"
                                                  width="100%">
                                                  <thead>
                                                    <tr>
                                                      <th scope="col"
                                                        style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left"
                                                        align="left">Evento</th>
                                                      <th scope="col"
                                                        style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left"
                                                        align="left">Cantidad</th>

                                                      <th scope="col"
                                                        style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left"
                                                        align="left">Precio</th>
                                                    </tr>
                                                  </thead>
                                                  <tbody>
                                                    <tr>
                                                      <td
                                                        style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"
                                                        align="left">
                                                        <?= $evento ?>
                                                        <!--  <ul style="font-size:small;margin:1em 0 0;padding:0;list-style:none">
                                                          <li style="margin:.5em 0 0;padding:0">
                                                            <strong style="float:left;margin-right:.25em;clear:both">ELIJE EL TIPO DE :</strong>
                                                            <p style="margin:0">En grano</p>
                                                          </li>
                                                        </ul> -->
                                                      </td>
                                                      <td
                                                        style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif"
                                                        align="left">
                                                        <?= $cantidad ?>
                                                      </td>

                                                      <td
                                                        style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif"
                                                        align="left">
                                                        <span><span>$</span><?= $precio >= 0 ? number_format($precio) : $precio ?></span>
                                                      </td>
                                                    </tr>

                                                  </tbody>
                                                  <tfoot>

                                                    <tr>
                                                      <th scope="row" colspan="2"
                                                        style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left"
                                                        align="left">Método de pago:</th>
                                                      <td
                                                        style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left"
                                                        align="left"><?= $entidad ?></td>
                                                    </tr>
                                                    <tr>
                                                      <th scope="row" colspan="2"
                                                        style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left"
                                                        align="left">Total:</th>
                                                      <td
                                                        style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left"
                                                        align="left">
                                                        <span><span>$</span><?= $precio >= 0 ? number_format($precio) : $precio ?></span>
                                                      </td>
                                                    </tr>
                                                    <!-- <tr>
                                                      <th scope="row" colspan="3" style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left" align="left">Nota:</th>
                                                      <td style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left" align="left">Nota aquí...</td>
                                                    </tr> -->
                                                  </tfoot>
                                                </table>
                                              </div>

                                              <table cellspacing="0" cellpadding="0" border="0"
                                                style="width:100%;vertical-align:top;margin-bottom:40px;padding:0"
                                                width="100%">
                                                <tbody>
                                                  <tr>
                                                    <td valign="top" width="50%"
                                                      style="text-align:left;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;border:0;padding:0"
                                                      align="left">
                                                      <h2
                                                        style="color:#0d3a58;display:block;font-family:Helvetica Neue,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">
                                                        Datos de facturación</h2>

                                                      <address
                                                        style="padding:12px;color:#636363;border:1px solid #e5e5e5">
                                                        <?= $facturaRazonSocial ?><br>
                                                        NIT/CC. <?= $facturaNit ?><br>
                                                        <?= $facturaDireccion ?><br>
                                                        Tel. <?= $facturaTelefono ?><br>
                                                        <a href="mailto:<?= $facturaCorreo ?>" target="_blank"><?= $facturaCorreo ?></a>
                                                      </address>
                                                    </td>
                                                  </tr>
                                                </tbody>
                                              </table>
                                              <div style="margin-bottom:40px">
                                                <h2
                                                  style="color:#0d3a58;display:block;font-family:Helvetica Neue,Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:0 0 18px;text-align:left">

                                                  Boletas</h2>

                                                <table cellspacing="0" cellpadding="6" border="1"
                                                  style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;width:100%;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif"
                                                  width="100%">
                                                  <thead>
                                                    <tr>
                                                      <th scope="col"
                                                        style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left"
                                                        align="left">Evento</th>
                                                      <th scope="col"
                                                        style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left"
                                                        align="left">Tipo</th>
                                                      <th scope="col"
                                                        style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left"
                                                        align="left">Nombre</th>
                                                      <th scope="col"
                                                        style="color:#636363;border:1px solid #e5e5e5;vertical-align:middle;padding:12px;text-align:left"
                                                        align="left">Ticket</th>
                                                    </tr>
                                                  </thead>
                                                  <tbody>
                                                    <?php foreach ($this->boletas as $boleta) { ?>

                                                      <tr>
                                                        <td
                                                          style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"
                                                          align="left">

                                                          <?= $evento ?>

                                                        </td>
                                                        <td
                                                          style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif"
                                                          align="left">
                                                          <?= $boleta["tipoBoleta"] == 'P' ? 'Invitado' : 'Socio' ?>
                                                        </td>
                                                        <td
                                                          style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;word-wrap:break-word"
                                                          align="left">

                                                          <?php
                                                          foreach ($this->invitados as $useri) {
                                                            if ($boleta["documento"] == $useri->documento_invitado) {
                                                              echo $useri->invitadoReserva_nombre_invitado. " " . $useri->invitadoReserva_apellido_invitado;
                                                            }
                                                          }
                                                          ?>

                                                        </td>
                                                        <td
                                                          style="color:#636363;border:1px solid #e5e5e5;padding:12px;text-align:left;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif"
                                                          align="left">
                                                          <?php $ruta = PDFS_PATH_NEWS . "boleta_cena_" . $boleta["boleta_uid"] . ".pdf" ?>
                                                          <?php if (file_exists($ruta)) {
                                                            $rutaEncriptada = urlencode(encryptString($ruta));
                                                          ?>
                                                            <a href="<?= RUTA ?>/page/leerpdf/?token=<?= $rutaEncriptada ?>"
                                                              target="_blank">
                                                              <?= $boleta["boleta_uid"] ?>
                                                            </a>
                                                          <?php } ?>

                                                        </td>
                                                      </tr>

                                                    <?php } ?>

                                                  </tbody>
                                                </table>
                                              </div>

                                              <p style="margin:0 0 16px">Gracias por tu <span class="il">compra</span>.
                                              </p>
                                            </div>
                                          </td>
                                        </tr>
                                      </tbody>
                                    </table>

                                  </td>
                                </tr>
                              </tbody>
                            </table>

                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
                <tr>
                  <td align="center" valign="top">

                    <table border="0" cellpadding="10" cellspacing="0" width="100%">
                      <tbody>
                        <tr>
                          <td valign="top" style="padding:0;border-radius:6px">
                            <table border="0" cellpadding="10" cellspacing="0" width="100%">
                              <tbody>
                                <tr>
                                  <td colspan="2" valign="middle"
                                    style="border-radius:6px;border:0;font-family:Helvetica Neue,Helvetica,Roboto,Arial,sans-serif;font-size:12px;line-height:150%;text-align:center;padding:24px 0;color:#3c3c3c"
                                    align="center">
                                    <p style="margin:0 0 16px">Copyright © <?= date('Y') ?> Club El Nogal.</p>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </td>
                        </tr>
                      </tbody>
                    </table>

                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </td>
        <td></td>
      </tr>
    </tbody>
  </table>
  <div class="yj6qo"></div>
  <div class="adL">
  </div>
</div>