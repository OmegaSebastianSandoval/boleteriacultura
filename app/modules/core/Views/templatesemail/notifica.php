<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>
</head>
<?php
$meses = [
    'January' => 'enero', 'February' => 'febrero', 'March' => 'marzo', 'April' => 'abril',
    'May' => 'mayo', 'June' => 'junio', 'July' => 'julio', 'August' => 'agosto',
    'September' => 'septiembre', 'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'
];

$reserva = $this->contenido;
$evento = $this->evento ?? null;
// Nota: la clase View no implementa __isset(), asi que empty()/isset() sobre
// propiedades de $this (p.ej. $this->nombreAmbiente) siempre las trata como
// vacias sin importar el valor real. Por eso se usa ?? en vez de empty() aqui.
$nombreAmbiente = $this->nombreAmbiente ?? '';

// Funcion anonima (no declarada globalmente): esta plantilla se puede incluir mas de
// una vez en el mismo proceso PHP (p.ej. placetopayController::sondaAction() notifica
// varias reservas en un solo request), y una "function" con nombre aqui provocaria un
// error fatal de "Cannot redeclare function" en la segunda inclusion.
$formatearFechaEs = function ($valor, $meses) {
    $fecha = new DateTime($valor);
    return $fecha->format('d') . ' de ' . $meses[$fecha->format('F')] . ' de ' . $fecha->format('Y');
};

// Preferimos la fecha oficial del evento; si no esta configurada (o quedo en la
// fecha "cero" de MySQL), usamos la fecha de la reserva para no dejar el dato vacio.
$fechaEvento = '';
if ($evento && !empty($evento->evento_fecha) && $evento->evento_fecha !== '0000-00-00') {
    $fechaEvento = $formatearFechaEs($evento->evento_fecha, $meses);
} elseif (!empty($reserva->reserva_fecha_inicio_reserva)) {
    $fechaEvento = $formatearFechaEs($reserva->reserva_fecha_inicio_reserva, $meses);
} elseif (!empty($reserva->reserva_fecha)) {
    $fechaEvento = $formatearFechaEs($reserva->reserva_fecha, $meses);
}

$metodosPago = [
    'cargo' => 'Cargo a la acción',
    'linea' => 'Pago en línea',
    'datafono' => 'Pago por datáfono',
];
$metodoPagoTexto = $metodosPago[$reserva->reserva_metodo_pago ?? ''] ?? ($reserva->reserva_metodo_pago ?: '—');
if (($reserva->reserva_metodo_pago ?? '') === 'cargo' && !empty($reserva->reserva_numero_cuotas)) {
    $metodoPagoTexto .= ' a ' . $reserva->reserva_numero_cuotas . ' cuota' . ((int) $reserva->reserva_numero_cuotas === 1 ? '' : 's');
}

$totalPagar = (float) ($reserva->reserva_total_pagar ?? 0);
$primerNombre = trim((string) ($reserva->reserva_nombre_cliente ?? ''));
$saludo = $primerNombre !== '' ? 'Hola, ' . $primerNombre : '¡Hola!';

// Datos que varian segun el resultado del pago (aprobado/pendiente/fallido/rechazado),
// preparados en Core_Model_Sendingemail::notificaPago(). Mismos ?? por la nota del
// __isset() de la clase View explicada mas arriba.
$estadoColor = $this->estadoColor ?? '#77bc23';
$estadoIcono = $this->estadoIcono ?? '&#10003;';
$estadoTitulo = $this->estadoTitulo ?? '¡Tu pago fue confirmado!';
$estadoMensaje = $this->estadoMensaje ?? '';
$estadoNota = $this->estadoNota ?? '';
$estadoNotaBg = $this->estadoNotaBg ?? '#fdf4e9';
$estadoNotaTexto = $this->estadoNotaTexto ?? '#5a4b30';
$ctaTexto = $this->ctaTexto ?? 'Ver mi reserva';
?>
<body style="margin: 0; padding: 0; background-color: #eef2f6;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #eef2f6; padding: 28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; background-color: #ffffff; border-radius: 10px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(13,58,88,0.08);">

                    <!-- Encabezado con logo -->
                    <tr>
                        <td align="center" style="background-color: #ffffff; padding: 28px 24px 18px;">
                            <img src="<?= $this->logoUrl ?>" alt="Club El Nogal" style="max-width: 150px; height: auto; display: block;">
                        </td>
                    </tr>

                    <!-- Banner de estado del pago -->
                    <tr>
                        <td align="center" style="background-color: <?= $estadoColor ?>; padding: 26px 24px 30px;">
                            <table role="presentation" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center" style="width: 52px; height: 52px; border-radius: 50%; background-color: #ffffff; font-size: 26px; line-height: 52px; color: <?= $estadoColor ?>; font-weight: bold;"><?= $estadoIcono ?></td>
                                </tr>
                            </table>
                            <h1 style="margin: 14px 0 4px; font-size: 21px; color: #ffffff; font-weight: 700;"><?= htmlspecialchars($estadoTitulo) ?></h1>
                            <p style="margin: 0; font-size: 13px; color: #ffffff; opacity: 0.85; letter-spacing: 0.3px;">
                                Reserva #<?= (int) $reserva->id ?><?= !empty($reserva->reserva_numero_carnet) ? ' &middot; Carnet ' . htmlspecialchars($reserva->reserva_numero_carnet) : '' ?>
                            </p>
                        </td>
                    </tr>

                    <!-- Cuerpo -->
                    <tr>
                        <td style="padding: 28px 28px 8px;">
                            <p style="margin: 0 0 6px; font-size: 16px; color: #0d3a58; font-weight: 700;"><?= htmlspecialchars($saludo) ?>,</p>
                            <?php if ($estadoMensaje !== ''): ?>
                            <p style="margin: 0 0 20px; font-size: 14.5px; line-height: 1.6; color: #4a5a68;">
                                <?= htmlspecialchars($estadoMensaje) ?>
                            </p>
                            <?php endif; ?>

                            <?php if ($estadoNota !== ''): ?>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 20px;">
                                <tr>
                                    <td style="padding: 12px 14px; background-color: <?= $estadoNotaBg ?>; border-left: 4px solid <?= $estadoColor ?>; border-radius: 4px; font-size: 13px; line-height: 1.55; color: <?= $estadoNotaTexto ?>;">
                                        <?= htmlspecialchars($estadoNota) ?>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>

                            <?php if ($ctaTexto !== ''): ?>
                            <table role="presentation" align="center" cellpadding="0" cellspacing="0" style="margin: 0 auto 26px;">
                                <tr>
                                    <td align="center" style="border-radius: 24px; background-color: <?= $estadoColor ?>;">
                                        <a href="<?= $this->enlace_completo; ?>" target="_blank" style="display: inline-block; padding: 13px 32px; font-size: 14px; font-weight: 700; letter-spacing: 0.3px; text-decoration: none; color: #ffffff;"><?= htmlspecialchars($ctaTexto) ?></a>
                                    </td>
                                </tr>
                            </table>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Detalle de la compra -->
                    <tr>
                        <td style="padding: 0 28px 8px;">
                            <p style="margin: 0 0 12px; font-size: 12px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #77bc23; border-bottom: 2px solid #eef2f6; padding-bottom: 8px;">
                                Detalle de tu compra
                            </p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size: 13.5px; color: #33424f;">
                                <tr>
                                    <td style="padding: 10px 12px; background-color: #f7f9fb; font-weight: 700; width: 42%; border-bottom: 1px solid #eef2f6;">Reserva #</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f6;"><?= (int) $reserva->id ?></td>
                                </tr>
                                <?php if ($evento && $evento->evento_titulo): ?>
                                <tr>
                                    <td style="padding: 10px 12px; background-color: #f7f9fb; font-weight: 700; border-bottom: 1px solid #eef2f6;">Evento</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f6;"><?= htmlspecialchars($evento->evento_titulo) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($fechaEvento): ?>
                                <tr>
                                    <td style="padding: 10px 12px; background-color: #f7f9fb; font-weight: 700; border-bottom: 1px solid #eef2f6;">Fecha</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f6;"><?= $fechaEvento ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($evento->evento_lugar)): ?>
                                <tr>
                                    <td style="padding: 10px 12px; background-color: #f7f9fb; font-weight: 700; border-bottom: 1px solid #eef2f6;">Lugar</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f6;"><?= htmlspecialchars($evento->evento_lugar) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($nombreAmbiente !== ''): ?>
                                <tr>
                                    <td style="padding: 10px 12px; background-color: #f7f9fb; font-weight: 700; border-bottom: 1px solid #eef2f6;">Ambiente</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f6;"><?= htmlspecialchars($nombreAmbiente) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <td style="padding: 10px 12px; background-color: #f7f9fb; font-weight: 700; border-bottom: 1px solid #eef2f6;">Cantidad de personas</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f6;"><?= (int) $reserva->reserva_total_personas ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 10px 12px; background-color: #f7f9fb; font-weight: 700; border-bottom: 1px solid #eef2f6;">Método de pago</td>
                                    <td style="padding: 10px 12px; border-bottom: 1px solid #eef2f6;"><?= htmlspecialchars($metodoPagoTexto) ?></td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px; background-color: #f0f7e9; font-weight: 700; color: #0d3a58;">Total pagado</td>
                                    <td style="padding: 12px; background-color: #f0f7e9; font-weight: 700; color: #0d3a58; font-size: 15px;">$<?= number_format($totalPagar, 0, ',', '.') ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #f7f9fb; padding: 18px 24px; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 11.5px; color: #9aa7b1;">Club El Nogal &middot; Este es un mensaje automático, por favor no respondas a este correo.</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
