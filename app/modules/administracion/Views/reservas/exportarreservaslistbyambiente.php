<?php
$tipo = [
  'A' => 'Socio',
  'S' => 'Cosocio',
  'P' => 'Invitado',
  'H' => 'Hijo',
];
$ambienteNombre = $this->ambiente->ambiente_nombre ?? 'Ambiente';
$pisoNombre     = $this->piso->piso_nombre ?? '';
?>
<head><meta charset="UTF-8"></head>

<table border="0" style="margin-bottom:10px;">
  <tr>
    <td style="font-size:14px;font-weight:bold;">Listado de invitados — <?php echo htmlspecialchars($ambienteNombre); ?> / <?php echo htmlspecialchars($pisoNombre); ?></td>
  </tr>
  <tr>
    <td style="font-size:11px;color:#555;">Generado: <?php echo date('d/m/Y H:i'); ?></td>
  </tr>
</table>

<table border="1" style="border-collapse:collapse;width:100%;">
  <tr style="background-color:#d0d0d0;">
    <td style="border:1px solid #000;padding:6px;font-weight:bold;">#</td>
    <td style="border:1px solid #000;padding:6px;font-weight:bold;">Reserva</td>
    <td style="border:1px solid #000;padding:6px;font-weight:bold;">Documento</td>
    <td style="border:1px solid #000;padding:6px;font-weight:bold;">Nombre</td>
    <td style="border:1px solid #000;padding:6px;font-weight:bold;">Apellido</td>
    <td style="border:1px solid #000;padding:6px;font-weight:bold;">Tipo</td>
    <td style="border:1px solid #000;padding:6px;font-weight:bold;">Mesa</td>
    <td style="border:1px solid #000;padding:6px;font-weight:bold;">Titular reserva</td>
    <td style="border:1px solid #000;padding:6px;font-weight:bold;">N° Acción</td>
    <td style="border:1px solid #000;padding:6px;font-weight:bold;">Fecha reserva</td>
  </tr>

  <?php $fila = 1; ?>
  <?php foreach ($this->content as $reserva): ?>
    <?php
      $mesaNombres = [];
      if (!empty($reserva->mesas)) {
        foreach ($reserva->mesas as $m) {
          $mesaNombres[] = $m->mesa_nombre ?? '';
        }
      }
      $mesaStr = implode(', ', array_filter($mesaNombres));
      $titular = trim(($reserva->reserva_nombre_cliente ?? '') . ' ' . ($reserva->reserva_apellido_cliente ?? ''));
    ?>
    <?php if (!empty($reserva->invitados)): ?>
      <?php foreach ($reserva->invitados as $inv): ?>
        <tr>
          <td style="border:1px solid #000;padding:5px;text-align:center;"><?php echo $fila++; ?></td>
          <td style="border:1px solid #000;padding:5px;"><?php echo htmlspecialchars($reserva->id ?? ''); ?></td>
          <td style="border:1px solid #000;padding:5px;"><?php echo htmlspecialchars($inv->documento_invitado ?? ''); ?></td>
          <td style="border:1px solid #000;padding:5px;"><?php echo htmlspecialchars($inv->invitadoReserva_nombre_invitado ?? ''); ?></td>
          <td style="border:1px solid #000;padding:5px;"><?php echo htmlspecialchars($inv->invitadoReserva_apellido_invitado ?? ''); ?></td>
          <td style="border:1px solid #000;padding:5px;"><?php echo htmlspecialchars($tipo[$inv->invitadoReserva_estado_invitado] ?? ($inv->invitadoReserva_estado_invitado ?? '')); ?></td>
          <td style="border:1px solid #000;padding:5px;"><?php echo htmlspecialchars($mesaStr ?: '—'); ?></td>
          <td style="border:1px solid #000;padding:5px;"><?php echo htmlspecialchars($titular); ?></td>
          <td style="border:1px solid #000;padding:5px;"><?php echo htmlspecialchars($reserva->reserva_numero_carnet ?? ''); ?></td>
          <td style="border:1px solid #000;padding:5px;"><?php echo htmlspecialchars($reserva->reserva_fecha ?? ''); ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  <?php endforeach; ?>
</table>
