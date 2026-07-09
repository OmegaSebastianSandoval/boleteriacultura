<?php $tipo = [
  'A' => 'Socio',
  'S' => 'Cosocio',
  'P' => 'Invitado'
];
$i = 0; ?>
<head>
  <meta charset="UTF-8">
</head>
<div class="container">
  <table width="100%" border="1">
    <thead>
      <tr>
        <th>Item No. </th>
        <th>Número de reserva</th>
        <th>Nombre asociado</th>
        <th>Número de documento</th>
        <th>Número de carnet</th>
        <th>Correo electrónico</th>
        <th>Teléfono</th>
        <th>Tipo</th>
        <th>Ambiente</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->content as $key => $value): ?>
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

        <?php if ($value->invitados && is_countable($value->invitados) && count($value->invitados) > 0) { ?>
          <tr>
            <td><?php echo ++$i; ?></td>
            <td><?php echo $value->id; ?></td>
            <td><?php echo $value->reserva_nombre_cliente . " " . $value->reserva_apellido_cliente; ?></td>
            <td><?php echo $value->reserva_documento; ?></td>
            <td><?php echo $value->reserva_numero_carnet; ?></td>
            <td><?php echo $value->reserva_correo; ?></td>
            <td><?php echo $value->reserva_telefono; ?></td>
            <td><?php echo $tipo['A']; ?></td>
            <td style="border: 1px solid #000; padding: 8px; text-align: left;" colspan="5">
              <strong>Ubicaci&oacute;n:</strong> <?php echo htmlspecialchars($pisos_str ?: 'No especificado'); ?> |
              <strong>Ambiente:</strong> <?php echo htmlspecialchars($ambientes_str ?: 'No especificado'); ?> |
              <strong>Mesas:</strong> <?php echo htmlspecialchars($mesas_str ?: 'No especificado'); ?>
            </td>
          </tr>

        <?php } ?>
      <?php endforeach ?>
    </tbody>
  </table>
</div>