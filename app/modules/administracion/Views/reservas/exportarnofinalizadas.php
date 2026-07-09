<?php
$estados_descripciones = [
  6 => 'Pago rechazado',
  8 => 'Cancelada'
];
$tipo = [
  'A' => 'Socio',
  'S' => 'Cosocio',
  'P' => 'Invitado',
  '1' => 'Socio',
  '2' => 'Invitado',
];
$i = 0;
?>

<head>
  <meta charset="UTF-8">
</head>
<table border="1" style="border-collapse: collapse; width: 100%;">

  <?php foreach ($this->content as $key => $value): ?>
    <?php $i++; ?>

    <!-- Información del socio -->
    <tr style="background-color: #f0f0f0;">
      <td style="border: 1px solid #000; padding: 8px; text-align: left;" colspan="4">
        <strong>Socio:</strong>
        <?php echo htmlspecialchars($value->reserva_nombre_cliente . ' ' . $value->reserva_apellido_cliente); ?> |
        <strong>Carnet:</strong> <?php echo htmlspecialchars($value->reserva_numero_carnet); ?> |
        <strong>Documento:</strong> <?php echo htmlspecialchars($value->reserva_documento); ?> |
        <strong>Email:</strong> <?php echo htmlspecialchars($value->reserva_correo); ?>
      </td>
    </tr>

    <!-- Información de intentos -->
    <tr>
      <td style="border: 1px solid #000; padding: 8px; text-align: left;" colspan="4">
        <strong>Intentos de reserva:</strong> <?php echo htmlspecialchars($value->intentos_reserva); ?> |
        <strong>IDs de reservas:</strong> <?php echo htmlspecialchars($value->ids_reservas); ?>
      </td>
    </tr>

    <!-- Estados de los intentos -->
    <?php
    $estados_array = explode(',', $value->estados_intentados);
    $ids_array = explode(',', $value->ids_reservas);
    ?>
    <tr>
      <td colspan="4"
        style="border: 1px solid #000; padding: 8px; background-color: #ffe6e6; text-align: left; font-weight: bold;">
        Estados de los intentos
      </td>
    </tr>

    <tr>
      <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">#</td>
      <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">ID Reserva</td>
      <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Estado</td>
      <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Fecha</td>
    </tr>

    <?php foreach ($estados_array as $index => $estado): ?>
      <?php
      $estado_num = trim($estado);
      $id_reserva = isset($ids_array[$index]) ? trim($ids_array[$index]) : 'N/A';
      $descripcion = isset($estados_descripciones[$estado_num]) ? $estados_descripciones[$estado_num] : 'Desconocido';

      // Buscar información adicional de la reserva
      $fecha_reserva = '';
      $total_pagar = '';
      if (!empty($value->invitados) && is_array($value->invitados)) {
        foreach ($value->invitados as $invitado) {
          if (isset($invitado->reservaInfo) && $invitado->reservaInfo->id == $id_reserva) {
            $fecha_reserva = $invitado->reservaInfo->reserva_fecha ?? '';
            $total_pagar = $invitado->reservaInfo->reserva_total_pagar ?? '';
            break;
          }
        }
      }
      ?>
      <tr>
        <td style="border: 1px solid #000; padding: 8px; text-align: left;"><?php echo ($index + 1); ?></td>
        <td style="border: 1px solid #000; padding: 8px; text-align: left;"><?php echo htmlspecialchars($id_reserva); ?>
        </td>
        <td style="border: 1px solid #000; padding: 8px; text-align: left;"><?php echo htmlspecialchars($descripcion); ?>
        </td>
        <td style="border: 1px solid #000; padding: 8px; text-align: left;">
          <?php echo htmlspecialchars($fecha_reserva ?: '-'); ?></td>
      </tr>
    <?php endforeach; ?>

    <!-- Invitados en reservas fallidas -->
    <?php if (!empty($value->invitados) && is_array($value->invitados)): ?>
      <tr>
        <td colspan="4"
          style="border: 1px solid #000; padding: 8px; background-color: #d0d0d0; text-align: left; font-weight: bold;">
          Invitados en reservas fallidas
        </td>
      </tr>

      <tr>
        <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">#</td>
        <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Nombre</td>
        <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Documento</td>
        <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Email</td>
        <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Tipo de persona</td>
      </tr>

      <?php $invitado_num = 1; ?>
      <?php foreach ($value->invitados as $invitado): ?>
        <?php
        $tipoKey = $invitado->invitadoReserva_estado_invitado ?? ($invitado->invitado_tipo ?? '');
        $tipoLabel = $tipo[$tipoKey] ?? $tipoKey;
        ?>
        <tr>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;"><?php echo $invitado_num++; ?></td>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;">
            <?php echo htmlspecialchars($invitado->invitadoReserva_nombre_invitado ? $invitado->invitadoReserva_nombre_invitado . ' ' . $invitado->invitadoReserva_apellido_invitado : '-'); ?>
          </td>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;">
            <?php echo htmlspecialchars($invitado->documento_invitado ?? '-'); ?>
          </td>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;">
            <?php echo htmlspecialchars($invitado->invitadoReserva_correo_invitado ?? '-'); ?>
          </td>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;">
            <?php echo htmlspecialchars($tipoLabel ?: '-'); ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <!-- Mensaje cuando no hay invitados -->
      <tr>
        <td style="border: 1px solid #000; padding: 8px; text-align: left;" colspan="5">
          No hay invitados registrados en reservas fallidas
        </td>
      </tr>
    <?php endif; ?>

    <!-- Espaciador entre socios -->
    <tr>
      <td colspan="5" style="height: 20px; border: none;"></td>
    </tr>

  <?php endforeach ?>

</table>