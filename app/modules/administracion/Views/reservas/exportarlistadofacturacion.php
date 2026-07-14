<?php
// Vista para exportar la tabla de listadofacturacion a Excel
// Reutiliza la lógica de la tabla de listadofacturacion.php pero sin estilos ni scripts innecesarios
function getEstadoReservaPHP($estado)
{
  $estados = array(
    '1' => array('texto' => 'Reserva creada'),
    '2' => array('texto' => 'Reserva pagada por cargo a la acción'),
    '3' => array('texto' => 'Reserva pago aprobado - PlaceToPay'),
    '4' => array('texto' => 'Reserva pago pendiente - PlaceToPay'),
    '5' => array('texto' => 'Reserva pago fallido - PlaceToPay'),
    '6' => array('texto' => 'Reserva pago rechazado - PlaceToPay'),
    '7' => array('texto' => 'Reserva pago pendiente - Sistema'),
    '8' => array('texto' => 'Reserva cancelada por inactividad'),
    'C' => array('texto' => 'Reserva cancelada'),
    '11'=> array('texto' => 'Pago por datáfono', 'badge_class' => 'text-bg-success')
  );
  return ($estados[$estado]) ? $estados[$estado] : array('texto' => 'Estado desconocido');
}

$estadometodo = [
  'cargo' => 'Cargo a la acción',
  'linea' => 'Pago en línea'
];
?>
<head>
  <meta charset="UTF-8">
</head>
<table border="1">
  <thead>
    <tr>
      <th>No. reserva</th>
      <th>Nombre</th>
      <th>Apellido</th>
      <th>Documento</th>
      <th>No. carnet</th>
      <th>Telfono</th>
      <th>Códigos Mesa</th>
      <th>Ambiente</th>
      <th>Pago</th>
      <th>Cuotas</th>
      <th>Franquicia</th>
      <th>Fecha de la reserva</th>
      <th>Estado</th>
      <th>Cantidad invitados</th>
      <th>Cantidad invitados calculado</th>
      <th>Socio</th>
      <th>Total socio</th>
      <th>Socio hijo < 25</th>
      <th>Total socio hijo < 25</th>
      <th>Cosocio</th>
      <th>Total cosocio</th>
      <th>Cosocio hijo < 25</th>
      <th>Total cosocio hijo < 25</th>
      <th>Invitado</th>
      <th>Total invitado</th>
      <th>Valor total calculado</th>
      <th>Valor total de la bd</th>
      <th>NIT o Cédula facturación</th>
      <th>Razón social facturación</th>
      <th>Dirección facturación</th>
      <th>Teléfono facturación</th>
      <th>Correo facturación</th>
    </tr>
  </thead>
  <tbody>
    <?php
    if (is_countable($this->content) && count($this->content) > 0):
      foreach ($this->content as $reserva):
        $totalInvitados = 0;
        $socio = 0;
        $socioHijo25 = 0;
        $cosocio = 0;
        $cosocioHijo25 = 0;
        $invitado = 0;
        $valorTotal = 0;
        // Reserva de sillas individuales: usar las tarifas de silla de la categoría
        // (categoria_precio_silla_*), no las de mesa. Mismo esquema que
        // eventoController::resumenAction().
        $esModoSilla = !empty($reserva->mesas) && is_array($reserva->mesas) && ($reserva->mesas[0]->mesa_tipo === 'silla');
        $campoSocioUnit = $esModoSilla ? 'categoria_precio_silla_socio' : 'categoria_precio_socio';
        $campoSocioHijoUnit = $esModoSilla ? 'categoria_precio_silla_socio_hijo' : 'categoria_precio_socio_hijo';
        $campoInvitadoUnit = $esModoSilla ? 'categoria_precio_silla_invitado' : 'categoria_precio_invitado';
        $precioSocio = (isset($reserva->categorias[0]) && isset($reserva->categorias[0]->$campoSocioUnit)) ? floatval($reserva->categorias[0]->$campoSocioUnit) : 0;
        $precioSocioHijo = (isset($reserva->categorias[0]) && isset($reserva->categorias[0]->$campoSocioHijoUnit)) ? floatval($reserva->categorias[0]->$campoSocioHijoUnit) : 0;
        $precioCosocio = $precioSocio;
        $precioCosocioHijo = $precioSocioHijo;
        $precioInvitado = (isset($reserva->categorias[0]) && isset($reserva->categorias[0]->$campoInvitadoUnit)) ? floatval($reserva->categorias[0]->$campoInvitadoUnit) : 0;
        $valorPorTipo = [
          'Socio' => 0,
          'Socio hijo < 25' => 0,
          'Cosocio' => 0,
          'Cosocio hijo < 25' => 0,
          'Invitado' => 0,
        ];
        if (!empty($reserva->invitados)) {
          foreach ($reserva->invitados as $invitadoObj) {
            $totalInvitados++;
            $tipo = isset($invitadoObj->invitadoReserva_estado_invitado) ? $invitadoObj->invitadoReserva_estado_invitado : '';
            $esMenor25 = isset($invitadoObj->invitadoReserva_beneficiario_menor25) && $invitadoObj->invitadoReserva_beneficiario_menor25;
            $esHijo = isset($invitadoObj->invitadoReserva_beneficiario_hijo) && $invitadoObj->invitadoReserva_beneficiario_hijo;
            $valor = 0;
            $tipoParticipante = '';
            if (($reserva->categorias) && is_array($reserva->categorias) && count($reserva->categorias) > 0) {
              $categoria = $reserva->categorias[0];
              if ($tipo == 'A') { // Socio
                if ($esMenor25 && $esHijo) {
                  $valor = $precioSocioHijo;
                  $tipoParticipante = 'Socio hijo < 25';
                  $socioHijo25++;
                } else {
                  $valor = $precioSocio;
                  $tipoParticipante = 'Socio';
                  $socio++;
                }
              } elseif ($tipo == 'S') { // Cosocio
                if ($esMenor25 && $esHijo) {
                  $valor = $precioCosocioHijo;
                  $tipoParticipante = 'Cosocio hijo < 25';
                  $cosocioHijo25++;
                } else {
                  $valor = $precioCosocio;
                  $tipoParticipante = 'Cosocio';
                  $cosocio++;
                }
              } else { // Invitado
                $valor = $precioInvitado;
                $tipoParticipante = 'Invitado';
                $invitado++;
              }
            }
            $valorTotal += $valor;
            $valorPorTipo[$tipoParticipante] += $valor;
          }
        }
        ?>
        <tr>
          <td><?= $reserva->id ?></td>
          <td><?= $reserva->reserva_nombre_cliente ?></td>
          <td><?= $reserva->reserva_apellido_cliente ?? '' ?></td>
          <td><?= $reserva->reserva_documento ?? '' ?></td>
          <td><?= $reserva->reserva_numero_carnet ?? '' ?></td>
          <td><?= $reserva->reserva_telefono ?? '' ?></td>
          <td>
            <?php 
            if (!empty($reserva->mesas) && is_array($reserva->mesas)) {
              $codigosMesa = array();
              foreach ($reserva->mesas as $mesa) {
                $codigosMesa[] = $mesa->mesa_codigo;
              }
              echo implode(', ', $codigosMesa);
            } else {
              echo 'Sin mesas asignadas';
            }
            ?>
          </td>
          <td>
            <?php
            if (!empty($reserva->mesas)) {
              $ambNombres = [];
              foreach ($reserva->mesas as $mesa) {
                if (!empty($mesa->ambiente_nombre) && !in_array($mesa->ambiente_nombre, $ambNombres)) {
                  $ambNombres[] = $mesa->ambiente_nombre;
                }
              }
              echo htmlspecialchars(implode(', ', $ambNombres));
            } else {
              echo '—';
            }
            ?>
          </td>
          <td><?= $estadometodo[$reserva->reserva_metodo_pago] ?? '' ?></td>
          <td>
            <?= (isset($reserva->reserva_numero_cuotas) && $reserva->reserva_numero_cuotas > 0) ? $reserva->reserva_numero_cuotas : 'N/A' ?>
          </td>
           <td>
            <?= (isset($reserva->reserva_franquicia) && $reserva->reserva_franquicia > 0) ? $reserva->reserva_franquicia : 'N/A' ?>
          </td>
          <td><?= date('Y-m-d H:i:s', strtotime($reserva->reserva_fecha_creacion)) ?></td>
          
          <td><?= getEstadoReservaPHP($reserva->reserva_estado)['texto'] ?></td>
          <td><?= $reserva->reserva_total_personas ?></td>
          <td><?= $totalInvitados ?></td>
          <td><?= $socio ?></td>
          <td>$<?= number_format($socio * $precioSocio, 0, ',', '.') ?></td>
          <td><?= $socioHijo25 ?></td>
          <td>$<?= number_format($socioHijo25 * $precioSocioHijo, 0, ',', '.') ?></td>
          <td><?= $cosocio ?></td>
          <td>$<?= number_format($cosocio * $precioCosocio, 0, ',', '.') ?></td>
          <td><?= $cosocioHijo25 ?></td>
          <td>$<?= number_format($cosocioHijo25 * $precioCosocioHijo, 0, ',', '.') ?></td>
          <td><?= $invitado ?></td>
          <td>$<?= number_format($invitado * $precioInvitado, 0, ',', '.') ?></td>
          <td>$<?= number_format($valorTotal, 0, ',', '.') ?></td>
          <td>$<?= number_format($reserva->reserva_total_pagar, 0, ',', '.') ?></td>
          <td><?= $reserva->reserva_fact_nit ?? '' ?></td>
          <td><?= $reserva->reserva_fact_razon ?? '' ?></td>
          <td><?= $reserva->reserva_fact_dire ?? '' ?></td>
          <td><?= $reserva->reserva_fact_tele ?? '' ?></td>
          <td><?= $reserva->reserva_fact_mail ?? '' ?></td>
        </tr>
        <?php
      endforeach;
    else:
      ?>
      <tr>
        <td colspan="32">No hay reservas para mostrar.</td>
      </tr>
    <?php endif; ?>
  </tbody>
</table>