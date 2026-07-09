<?php $tipo = [
  'A' => 'Socio',
  'S' => 'Cosocio',
  'P' => 'Invitado'
];
$i = 0;

// Función simple para obtener información de ubicación
function obtenerUbicacion($reserva)
{
  $mesas_info = [];
  $ambientes_info = [];
  $pisos_info = [];

  if (isset($reserva->mesas) && is_array($reserva->mesas)) {
    foreach ($reserva->mesas as $mesa) {
      $mesas_info[] = $mesa->mesa_nombre ?? '';
      if (!in_array($mesa->ambiente_nombre ?? '', $ambientes_info) && !empty($mesa->ambiente_nombre)) {
        $ambientes_info[] = $mesa->ambiente_nombre;
      }
      if (!in_array($mesa->piso_nombre ?? '', $pisos_info) && !empty($mesa->piso_nombre)) {
        $pisos_info[] = $mesa->piso_nombre;
      }
    }
  }

  return [
    'mesas' => implode(', ', array_filter($mesas_info)),
    'ambientes' => implode(', ', array_filter($ambientes_info)),
    'pisos' => implode(', ', array_filter($pisos_info))
  ];
}
?>

<head>
  <meta charset="UTF-8">
</head>
<table border="1" style="border-collapse: collapse; width: 100%;">
  <!-- Encabezado de la tabla -->
  <tr style="background-color: #d0d0d0;">
    <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Reserva</td>
    <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Documento</td>
    <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Nombres</td>
    <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Apellido</td>
    <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Mesa</td>
    <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Piso</td>
    <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Tipo</td>
    <td style="border: 1px solid #000; padding: 8px; font-weight: bold; text-align: left;">Ambiente</td>
  </tr>

  <?php foreach ($this->content as $key => $value): ?>
    <?php $ubicacion = obtenerUbicacion($value); ?>

    <!-- Listado de invitados -->
    <?php if (!empty($value->invitados) && is_array($value->invitados)): ?>
      <?php foreach ($value->invitados as $invitado): ?>
        <?php $apellido_mostrar = $invitado->invitadoReserva_apellido_invitado; ?>
        <?php if ($invitado->documento_invitado === $value->reserva_documento) {
          // $invitado->invitadoReserva_nombre_invitado = $value->reserva_nombre_cliente;
          // $invitado->invitadoReserva_apellido_invitado = $value->reserva_apellido_cliente;
          // $apellido_mostrar = $value->invitadoReserva_apellido_invitado;
        }
        ?>

        <tr>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;">
            <?php echo htmlspecialchars($value->id ?? ''); ?>
          </td>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;">
            <?php echo htmlspecialchars($invitado->documento_invitado ?? ''); ?>
          </td>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;">
            <?php echo htmlspecialchars($invitado->invitadoReserva_nombre_invitado ?? ''); ?>
          </td>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;">
            <?php echo htmlspecialchars($invitado->invitadoReserva_apellido_invitado ?? ''); ?>
          </td>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;">
            <?php echo htmlspecialchars($ubicacion['mesas'] ?: 'No especificado'); ?>
          </td>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;">
            <?php echo htmlspecialchars($ubicacion['pisos'] ?: 'No especificado'); ?>
          </td>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;">
            <?php echo htmlspecialchars($tipo[$invitado->invitadoReserva_estado_invitado] ?? $invitado->invitadoReserva_estado_invitado); ?>
          </td>
          <td style="border: 1px solid #000; padding: 8px; text-align: left;">
            <?php echo htmlspecialchars($ubicacion['ambientes'] ?: 'No especificado'); ?>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>

  <?php endforeach; ?>
</table>