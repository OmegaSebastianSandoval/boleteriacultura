<?php $tipo = [
  'A' => 'Socio',
  'S' => 'Cosocio',
  'P' => 'Invitado'
];
$i = 0;
?>

<head>
  <meta charset="UTF-8">
</head>
<table border="1" style="border-collapse: collapse; width: 100%;">

  <?php foreach ($this->content as $key => $value): ?>
    <?php $i++; ?>

    <!-- Fila de la reserva -->
    <tr style="background-color: #f0f0f0;">
      <td style="border: 1px solid #000; padding: 8px; text-align: left;" colspan="5">
        <strong>Reserva ID:</strong> <?php echo $value->id; ?> |
        <strong>Cliente:</strong> <?php echo htmlspecialchars($value->reserva_nombre_cliente ?? ''); ?> |
        <strong>Email:</strong> <?php echo htmlspecialchars($value->reserva_correo ?? ''); ?>
      </td>
    </tr>

    <!-- Informaci��n de ubicaci��n -->
    <?php
    // Preparar informaci��n de mesas, ambientes y pisos
    $mesas_info = [];
    $ambientes_info = [];
    $pisos_info = [];

    if (isset($value->mesas) && is_array($value->mesas)) {
      foreach ($value->mesas as $mesa) {
        $mesas_info[] = $mesa->mesa_nombre . ' (' . $mesa->mesa_capacidad . ' personas)';
        if (!in_array($mesa->ambiente_nombre, $ambientes_info)) {
          $ambientes_info[] = $mesa->ambiente_nombre;
        }
        if (!in_array($mesa->piso_nombre, $pisos_info)) {
          $pisos_info[] = $mesa->piso_nombre;
        }
      }
    }

    $mesas_str = implode(', ', $mesas_info);
    $ambientes_str = implode(', ', $ambientes_info);
    $pisos_str = implode(', ', $pisos_info);
    ?>

    <tr>
      <td style="border: 1px solid #000; padding: 8px; text-align: left;" colspan="5">
        <strong>Ubicaci&oacute;n:</strong> <?php echo htmlspecialchars($pisos_str ?: 'No especificado'); ?> |
        <strong>Ambiente:</strong> <?php echo htmlspecialchars($ambientes_str ?: 'No especificado'); ?> |
        <strong>Mesas:</strong> <?php echo htmlspecialchars($mesas_str ?: 'No especificado'); ?>
      </td>
    </tr>

    <!-- Invitados de esta reserva -->
    <?php if (!empty($value->invitados) && is_array($value->invitados)): ?>
      <!-- Encabezado de invitados -->
      <tr>
        <td colspan="5"
          style="border: 1px solid #000; padding: 8px; background-color: #d0d0d0; text-align: left; font-weight: bold;">
          Invitados
        </td>
      </tr>

      <tr>
        <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">#</td>
        <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Nombre</td>
        <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Documento</td>
        <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Email</td>
        <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Tipo</td>

      </tr>

      <?php $invitado_num = 1; ?>
      <?php foreach ($value->invitados as $invitado): ?>
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
            <?php echo htmlspecialchars($tipo[$invitado->invitadoReserva_estado_invitado] ?? $invitado->invitadoReserva_estado_invitado); ?>
          </td>

        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <!-- Mensaje cuando no hay invitados -->
      <tr>
        <td style="border: 1px solid #000; padding: 8px; text-align: left;" colspan="5">
          Los invitados de la reserva no son socios del club
        </td>
      </tr>
    <?php endif; ?>

    <!-- Espaciador entre reservas -->
    <tr>
      <td colspan="5" style="height: 15px; border: none;"></td>
    </tr>

  <?php endforeach ?>

</table>