<?php
// filepath: /Users/sebastian/OmegaWork/Projects/Nogal/app/modules/administracion/Models/DbTable/FlujosReserva.php
class Administracion_Model_DbTable_FlujosReserva extends Db_Table
{
  protected $_name = 'flujos_reserva';
  protected $_id = 'flujo_id';

  public function insert($data)
  {
    $usuario_documento = $data['usuario_documento'];
    $evento_id = $data['evento_id'];
    $paso_actual = $data['paso_actual'];
    $paso_anterior = $data['paso_anterior'] ?? null;
    $token_sesion = $data['token_sesion'];
    $fecha_inicio = $data['fecha_inicio'];
    $fecha_ultima_actividad = $data['fecha_ultima_actividad'];
    $fecha_expiracion = $data['fecha_expiracion'];
    $datos_temporales = $data['datos_temporales'] ?? null;
    $estado = $data['estado'] ?? 'activo';
    $reserva_id = $data['reserva_id'] ?? null;
    $mesa_temporal_id = $data['mesa_temporal_id'] ?? null;
    $ip_usuario = $data['ip_usuario'] ?? null;
    $user_agent = $data['user_agent'] ?? null;

    $query = "INSERT INTO flujos_reserva (
      usuario_documento, evento_id, paso_actual, paso_anterior, token_sesion, 
      fecha_inicio, fecha_ultima_actividad, fecha_expiracion, datos_temporales, 
      estado, reserva_id, mesa_temporal_id, ip_usuario, user_agent
    ) VALUES (
      '$usuario_documento', '$evento_id', '$paso_actual', " . 
      ($paso_anterior ? "'$paso_anterior'" : "NULL") . ", '$token_sesion', 
      '$fecha_inicio', '$fecha_ultima_actividad', '$fecha_expiracion', " .
      ($datos_temporales ? "'$datos_temporales'" : "NULL") . ", '$estado', " .
      ($reserva_id ? "'$reserva_id'" : "NULL") . ", " .
      ($mesa_temporal_id ? "'$mesa_temporal_id'" : "NULL") . ", " .
      ($ip_usuario ? "'$ip_usuario'" : "NULL") . ", " .
      ($user_agent ? "'" . mysqli_real_escape_string($this->_conn->getConnection(), $user_agent) . "'" : "NULL") . "
    )";

    $res = $this->_conn->query($query);
    return mysqli_insert_id($this->_conn->getConnection());
  }

  public function update($data, $id)
  {
    $updates = [];
    
    if (isset($data['paso_anterior'])) {
      $updates[] = "paso_anterior = " . ($data['paso_anterior'] ? "'{$data['paso_anterior']}'" : "NULL");
    }
    if (isset($data['paso_actual'])) {
      $updates[] = "paso_actual = '{$data['paso_actual']}'";
    }
    if (isset($data['fecha_ultima_actividad'])) {
      $updates[] = "fecha_ultima_actividad = '{$data['fecha_ultima_actividad']}'";
    }
    if (isset($data['fecha_expiracion'])) {
      $updates[] = "fecha_expiracion = '{$data['fecha_expiracion']}'";
    }
    if (isset($data['datos_temporales'])) {
      $updates[] = "datos_temporales = " . ($data['datos_temporales'] ? "'{$data['datos_temporales']}'" : "NULL");
    }
    if (isset($data['estado'])) {
      $updates[] = "estado = '{$data['estado']}'";
    }
    if (isset($data['reserva_id'])) {
      $updates[] = "reserva_id = " . ($data['reserva_id'] ? "'{$data['reserva_id']}'" : "NULL");
    }
    if (isset($data['mesa_temporal_id'])) {
      $updates[] = "mesa_temporal_id = " . ($data['mesa_temporal_id'] ? "'{$data['mesa_temporal_id']}'" : "NULL");
    }

    if (empty($updates)) {
      return false;
    }

    $query = "UPDATE flujos_reserva SET " . implode(', ', $updates) . " WHERE flujo_id = '$id'";
    $res = $this->_conn->query($query);
    return $res;
  }

  public function iniciarFlujo($usuarioDocumento, $eventoId, $paso, $datosTemporales = null)
  {
    // Cancelar flujos activos previos del mismo usuario
    $this->cancelarFlujosActivos($usuarioDocumento, $eventoId);

    $token = $this->generarToken();
    $ahora = date('Y-m-d H:i:s');

    $data = [
      'usuario_documento' => $usuarioDocumento,
      'evento_id' => $eventoId,
      'paso_actual' => $paso,
      'paso_anterior' => null,
      'token_sesion' => $token,
      'fecha_inicio' => $ahora,
      'fecha_ultima_actividad' => $ahora,
      'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
      'datos_temporales' => $datosTemporales ? json_encode($datosTemporales) : null,
      'estado' => 'activo',
      'ip_usuario' => $_SERVER['REMOTE_ADDR'] ?? null,
      'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
    ];

    $flujoId = $this->insert($data);

    // Guardar en sesión
    Session::getInstance()->set('flujo_activo', [
      'flujo_id' => $flujoId,
      'token' => $token,
      'paso_actual' => $paso
    ]);

    return $flujoId;
  }

  public function actualizarPaso($flujoId, $nuevoPaso, $datosTemporales = null)
  {
    $flujoActual = $this->getById($flujoId);
    if (!$flujoActual || $flujoActual->estado !== 'activo') {
      throw new Exception('Flujo no válido o expirado');
    }

    $dataUpdate = [
      'paso_anterior' => $flujoActual->paso_actual,
      'paso_actual' => $nuevoPaso,
      'fecha_ultima_actividad' => date('Y-m-d H:i:s'),
      'fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+30 minutes'))
    ];

    if ($datosTemporales !== null) {
      $dataUpdate['datos_temporales'] = json_encode($datosTemporales);
    }

    $this->update($dataUpdate, $flujoId);

    // Actualizar sesión
    $flujoSession = Session::getInstance()->get('flujo_activo');
    if ($flujoSession) {
      Session::getInstance()->set('flujo_activo', [
        'flujo_id' => $flujoId,
        'token' => $flujoSession['token'],
        'paso_actual' => $nuevoPaso
      ]);
    }

    return true;
  }

  public function cancelarFlujosActivos($usuarioDocumento, $eventoId)
  {
    // Obtener flujos activos
    $flujosActivos = $this->getList(
      "usuario_documento = '$usuarioDocumento' AND evento_id = '$eventoId' AND estado = 'activo'",
      "flujo_id DESC"
    );

    foreach ($flujosActivos as $flujo) {
      $this->limpiarFlujo($flujo->flujo_id);
    }
  }

  public function limpiarFlujo($flujoId)
  {
    $flujo = $this->getById($flujoId);
    if (!$flujo) {
      return false;
    }

    // Liberar bloqueos de beneficiarios asociados a este flujo
    if (property_exists($flujo, 'flujo_id')) {
      $query1 = "UPDATE beneficiariosbloqueos SET beneficiario_bloqueo_estado = 0 WHERE flujo_id = '$flujoId'";
      $this->_conn->query($query1);

      // Liberar bloqueos de mesas asociados a este flujo
      $query2 = "UPDATE mesasbloqueo SET mesa_bloqueo_estado = 0 WHERE flujo_id = '$flujoId'";
      $this->_conn->query($query2);
    }

    // Liberar mesa temporal si existe
    if (property_exists($flujo, 'mesa_temporal_id') && $flujo->mesa_temporal_id) {
      $query3 = "UPDATE mesas SET mesa_estado = '0' WHERE mesa_id = '{$flujo->mesa_temporal_id}'";
      $this->_conn->query($query3);
    }

    // Marcar flujo como abandonado
    $this->update([
      'estado' => 'abandonado',
      'fecha_ultima_actividad' => date('Y-m-d H:i:s')
    ], $flujoId);

    return true;
  }

  public function validarPasoPermitido($flujoId, $pasoSolicitado)
  {
    $flujo = $this->getById($flujoId);
    if (!$flujo || $flujo->estado !== 'activo') {
      return false;
    }

    // Definir flujo válido
    $flujosPermitidos = [
      'reservar' => ['ninguno'],
      'seleccionarbeneficiarios' => ['reservar'],
      'registrarreserva' => ['seleccionarbeneficiarios'], 
      'reservarmesa' => ['registrarreserva'],
      'confirmarmesa' => ['reservarmesa'],
      'resumen' => ['confirmarmesa'],
      'pagar' => ['resumen']
    ];

    $pasosPermitidos = $flujosPermitidos[$pasoSolicitado] ?? [];

    return in_array($flujo->paso_actual, $pasosPermitidos) ||
      in_array('ninguno', $pasosPermitidos);
  }

  public function marcarComoCompletado($flujoId, $reservaId = null)
  {
    $dataUpdate = [
      'estado' => 'completado',
      'fecha_ultima_actividad' => date('Y-m-d H:i:s')
    ];

    if ($reservaId) {
      $dataUpdate['reserva_id'] = $reservaId;
    }

    return $this->update($dataUpdate, $flujoId);
  }

  public function obtenerFlujoActivo($usuarioDocumento, $eventoId)
  {
    $flujos = $this->getList(
      "usuario_documento = '$usuarioDocumento' AND evento_id = '$eventoId' AND estado = 'activo'",
      "flujo_id DESC LIMIT 1"
    );

    return !empty($flujos) ? $flujos[0] : null;
  }

  private function generarToken()
  {
    return bin2hex(random_bytes(32)) . '_' . time();
  }
}