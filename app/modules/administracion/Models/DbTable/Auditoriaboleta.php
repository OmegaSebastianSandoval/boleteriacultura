<?php
/**
 * clase que genera la insercion y edicion  de auditoriaboleta en la base de datos
 */
class Administracion_Model_DbTable_Auditoriaboleta extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'auditoriaboleta';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'auditoriaboleta_id';

	/**
	 * insert recibe la informacion de un auditoriaboleta y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data)
	{
		$auditoriaboleta_session_id = $data['auditoriaboleta_session_id'];
		$auditoriaboleta_boleta_uid = $data['auditoriaboleta_boleta_uid'];
		$auditoriaboleta_boleta_token = $data['auditoriaboleta_boleta_token'];
		$auditoriaboleta_documento_escaneado = $data['auditoriaboleta_documento_escaneado'];
		$auditoriaboleta_boleta_id = $data['auditoriaboleta_boleta_id'];
		$auditoriaboleta_boleta_reserva_id = $data['auditoriaboleta_boleta_reserva_id'];
		$auditoriaboleta_boleta_evento_id = $data['auditoriaboleta_boleta_evento_id'];
		$auditoriaboleta_boleta_mesa = $data['auditoriaboleta_boleta_mesa'];
		$auditoriaboleta_boleta_numero_ticket = $data['auditoriaboleta_boleta_numero_ticket'];
		$auditoriaboleta_usuario_validador_id = $data['auditoriaboleta_usuario_validador_id'];
		$auditoriaboleta_usuario_validador_nombre = $data['auditoriaboleta_usuario_validador_nombre'];
		$auditoriaboleta_numero_carnet = $data['auditoriaboleta_numero_carnet'];
		$auditoriaboleta_accion = $data['auditoriaboleta_accion'];
		$auditoriaboleta_resultado = $data['auditoriaboleta_resultado'];
		$auditoriaboleta_motivo_fallo = $data['auditoriaboleta_motivo_fallo'];
		$auditoriaboleta_metodo_escaneado = $data['auditoriaboleta_metodo_escaneado'];
		$auditoriaboleta_ip_address = $data['auditoriaboleta_ip_address'];
		$auditoriaboleta_user_agent = $data['auditoriaboleta_user_agent'];
		$auditoriaboleta_dispositivo_info = $data['auditoriaboleta_dispositivo_info'];
		$auditoriaboleta_url_completa = $data['auditoriaboleta_url_completa'];
		$auditoriaboleta_referer = $data['auditoriaboleta_referer'];
		$auditoriaboleta_parametros_get = $data['auditoriaboleta_parametros_get'];
		$auditoriaboleta_parametros_post = $data['auditoriaboleta_parametros_post'];
		$auditoriaboleta_fecha_hora = $data['auditoriaboleta_fecha_hora'];
		$auditoriaboleta_timestamp_unix = $data['auditoriaboleta_timestamp_unix'];
		$auditoriaboleta_datos_boleta_antes = $data['auditoriaboleta_datos_boleta_antes'];
		$auditoriaboleta_datos_boleta_despues = $data['auditoriaboleta_datos_boleta_despues'];
		$auditoriaboleta_datos_reserva = $data['auditoriaboleta_datos_reserva'];
		$auditoriaboleta_datos_sesion = $data['auditoriaboleta_datos_sesion'];
		$auditoriaboleta_observaciones = $data['auditoriaboleta_observaciones'];
		$query = "INSERT INTO auditoriaboleta( auditoriaboleta_session_id, auditoriaboleta_boleta_uid, auditoriaboleta_boleta_token, auditoriaboleta_documento_escaneado, auditoriaboleta_boleta_id, auditoriaboleta_boleta_reserva_id, auditoriaboleta_boleta_evento_id, auditoriaboleta_boleta_mesa, auditoriaboleta_boleta_numero_ticket, auditoriaboleta_usuario_validador_id, auditoriaboleta_usuario_validador_nombre, auditoriaboleta_numero_carnet, auditoriaboleta_accion, auditoriaboleta_resultado, auditoriaboleta_motivo_fallo, auditoriaboleta_metodo_escaneado, auditoriaboleta_ip_address, auditoriaboleta_user_agent, auditoriaboleta_dispositivo_info, auditoriaboleta_url_completa, auditoriaboleta_referer, auditoriaboleta_parametros_get, auditoriaboleta_parametros_post, auditoriaboleta_fecha_hora, auditoriaboleta_timestamp_unix, auditoriaboleta_datos_boleta_antes, auditoriaboleta_datos_boleta_despues, auditoriaboleta_datos_reserva, auditoriaboleta_datos_sesion, auditoriaboleta_observaciones) VALUES ( '$auditoriaboleta_session_id', '$auditoriaboleta_boleta_uid', '$auditoriaboleta_boleta_token', '$auditoriaboleta_documento_escaneado', '$auditoriaboleta_boleta_id', '$auditoriaboleta_boleta_reserva_id', '$auditoriaboleta_boleta_evento_id', '$auditoriaboleta_boleta_mesa', '$auditoriaboleta_boleta_numero_ticket', '$auditoriaboleta_usuario_validador_id', '$auditoriaboleta_usuario_validador_nombre', '$auditoriaboleta_numero_carnet', '$auditoriaboleta_accion', '$auditoriaboleta_resultado', '$auditoriaboleta_motivo_fallo', '$auditoriaboleta_metodo_escaneado', '$auditoriaboleta_ip_address', '$auditoriaboleta_user_agent', '$auditoriaboleta_dispositivo_info', '$auditoriaboleta_url_completa', '$auditoriaboleta_referer', '$auditoriaboleta_parametros_get', '$auditoriaboleta_parametros_post', '$auditoriaboleta_fecha_hora', '$auditoriaboleta_timestamp_unix', '$auditoriaboleta_datos_boleta_antes', '$auditoriaboleta_datos_boleta_despues', '$auditoriaboleta_datos_reserva', '$auditoriaboleta_datos_sesion', '$auditoriaboleta_observaciones')";
		$res = $this->_conn->query($query);
		return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un auditoriaboleta  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data, $id)
	{

		$auditoriaboleta_boleta_uid = $data['auditoriaboleta_boleta_uid'];
		$auditoriaboleta_boleta_token = $data['auditoriaboleta_boleta_token'];
		$auditoriaboleta_documento_escaneado = $data['auditoriaboleta_documento_escaneado'];
		$auditoriaboleta_boleta_id = $data['auditoriaboleta_boleta_id'];
		$auditoriaboleta_boleta_reserva_id = $data['auditoriaboleta_boleta_reserva_id'];
		$auditoriaboleta_boleta_evento_id = $data['auditoriaboleta_boleta_evento_id'];
		$auditoriaboleta_boleta_mesa = $data['auditoriaboleta_boleta_mesa'];
		$auditoriaboleta_boleta_numero_ticket = $data['auditoriaboleta_boleta_numero_ticket'];
		$auditoriaboleta_usuario_validador_id = $data['auditoriaboleta_usuario_validador_id'];
		$auditoriaboleta_usuario_validador_nombre = $data['auditoriaboleta_usuario_validador_nombre'];
		$auditoriaboleta_numero_carnet = $data['auditoriaboleta_numero_carnet'];
		$auditoriaboleta_accion = $data['auditoriaboleta_accion'];
		$auditoriaboleta_resultado = $data['auditoriaboleta_resultado'];
		$auditoriaboleta_motivo_fallo = $data['auditoriaboleta_motivo_fallo'];
		$auditoriaboleta_metodo_escaneado = $data['auditoriaboleta_metodo_escaneado'];
		$auditoriaboleta_ip_address = $data['auditoriaboleta_ip_address'];
		$auditoriaboleta_user_agent = $data['auditoriaboleta_user_agent'];
		$auditoriaboleta_dispositivo_info = $data['auditoriaboleta_dispositivo_info'];
		$auditoriaboleta_url_completa = $data['auditoriaboleta_url_completa'];
		$auditoriaboleta_referer = $data['auditoriaboleta_referer'];
		$auditoriaboleta_parametros_get = $data['auditoriaboleta_parametros_get'];
		$auditoriaboleta_parametros_post = $data['auditoriaboleta_parametros_post'];
		$auditoriaboleta_fecha_hora = $data['auditoriaboleta_fecha_hora'];
		$auditoriaboleta_timestamp_unix = $data['auditoriaboleta_timestamp_unix'];
		$auditoriaboleta_datos_boleta_antes = $data['auditoriaboleta_datos_boleta_antes'];
		$auditoriaboleta_datos_boleta_despues = $data['auditoriaboleta_datos_boleta_despues'];
		$auditoriaboleta_datos_reserva = $data['auditoriaboleta_datos_reserva'];
		$auditoriaboleta_datos_sesion = $data['auditoriaboleta_datos_sesion'];
		$auditoriaboleta_observaciones = $data['auditoriaboleta_observaciones'];
		$query = "UPDATE auditoriaboleta SET  auditoriaboleta_boleta_uid = '$auditoriaboleta_boleta_uid', auditoriaboleta_boleta_token = '$auditoriaboleta_boleta_token', auditoriaboleta_documento_escaneado = '$auditoriaboleta_documento_escaneado', auditoriaboleta_boleta_id = '$auditoriaboleta_boleta_id', auditoriaboleta_boleta_reserva_id = '$auditoriaboleta_boleta_reserva_id', auditoriaboleta_boleta_evento_id = '$auditoriaboleta_boleta_evento_id', auditoriaboleta_boleta_mesa = '$auditoriaboleta_boleta_mesa', auditoriaboleta_boleta_numero_ticket = '$auditoriaboleta_boleta_numero_ticket', auditoriaboleta_usuario_validador_id = '$auditoriaboleta_usuario_validador_id', auditoriaboleta_usuario_validador_nombre = '$auditoriaboleta_usuario_validador_nombre', auditoriaboleta_numero_carnet = '$auditoriaboleta_numero_carnet', auditoriaboleta_accion = '$auditoriaboleta_accion', auditoriaboleta_resultado = '$auditoriaboleta_resultado', auditoriaboleta_motivo_fallo = '$auditoriaboleta_motivo_fallo', auditoriaboleta_metodo_escaneado = '$auditoriaboleta_metodo_escaneado', auditoriaboleta_ip_address = '$auditoriaboleta_ip_address', auditoriaboleta_user_agent = '$auditoriaboleta_user_agent', auditoriaboleta_dispositivo_info = '$auditoriaboleta_dispositivo_info', auditoriaboleta_url_completa = '$auditoriaboleta_url_completa', auditoriaboleta_referer = '$auditoriaboleta_referer', auditoriaboleta_parametros_get = '$auditoriaboleta_parametros_get', auditoriaboleta_parametros_post = '$auditoriaboleta_parametros_post', auditoriaboleta_fecha_hora = '$auditoriaboleta_fecha_hora', auditoriaboleta_timestamp_unix = '$auditoriaboleta_timestamp_unix', auditoriaboleta_datos_boleta_antes = '$auditoriaboleta_datos_boleta_antes', auditoriaboleta_datos_boleta_despues = '$auditoriaboleta_datos_boleta_despues', auditoriaboleta_datos_reserva = '$auditoriaboleta_datos_reserva', auditoriaboleta_datos_sesion = '$auditoriaboleta_datos_sesion', auditoriaboleta_observaciones = '$auditoriaboleta_observaciones' WHERE auditoriaboleta_id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}

	/**
	 * Obtiene el historial de auditoría para una boleta específica
	 * @param string $uid UID de la boleta
	 * @param int $limite Número máximo de registros a devolver
	 * @return array Array con los registros de auditoría
	 */
	public function getHistorialPorUID($uid, $limite = 50)
	{
		$query = "SELECT * FROM auditoriaboleta 
				  WHERE auditoriaboleta_boleta_uid = '$uid' 
				  ORDER BY auditoriaboleta_fecha_hora DESC 
				  LIMIT $limite";
		$res = $this->_conn->query($query)->fetchAsObject();
		return $res;
	}

	/**
	 * Obtiene el historial de auditoría para un documento específico
	 * @param string $documento Documento escaneado
	 * @param int $limite Número máximo de registros a devolver
	 * @return array Array con los registros de auditoría
	 */
	public function getHistorialPorDocumento($documento, $limite = 50)
	{
		$query = "SELECT * FROM auditoriaboleta 
				  WHERE auditoriaboleta_documento_escaneado = '$documento' 
				  ORDER BY auditoriaboleta_fecha_hora DESC 
				  LIMIT $limite";
		$res = $this->_conn->query($query)->fetchAsObject();
		return $res;
	}

	/**
	 * Obtiene estadísticas de auditoría por fecha
	 * @param string $fechaInicio Fecha de inicio (Y-m-d)
	 * @param string $fechaFin Fecha de fin (Y-m-d)
	 * @return array Array con las estadísticas
	 */
	public function getEstadisticasPorFecha($fechaInicio, $fechaFin)
	{
		$query = "SELECT 
					auditoriaboleta_accion,
					auditoriaboleta_resultado,
					auditoriaboleta_metodo_escaneado,
					COUNT(*) as total,
					DATE(auditoriaboleta_fecha_hora) as fecha
				  FROM auditoriaboleta 
				  WHERE DATE(auditoriaboleta_fecha_hora) BETWEEN '$fechaInicio' AND '$fechaFin'
				  GROUP BY auditoriaboleta_accion, auditoriaboleta_resultado, auditoriaboleta_metodo_escaneado, DATE(auditoriaboleta_fecha_hora)
				  ORDER BY fecha DESC";
		$res = $this->_conn->query($query)->fetchAsObject();
		return $res;
	}

	/**
	 * Detecta intentos sospechosos por IP
	 * @param int $minutos Ventana de tiempo en minutos
	 * @param int $maxIntentos Número máximo de intentos fallidos permitidos
	 * @return array Array con IPs sospechosas
	 */
	public function detectarIntentosSospechosos($minutos = 10, $maxIntentos = 5)
	{
		$fechaLimite = date('Y-m-d H:i:s', strtotime("-{$minutos} minutes"));
		$query = "SELECT 
					auditoriaboleta_ip_address,
					COUNT(*) as total_intentos,
					SUM(CASE WHEN auditoriaboleta_resultado = 'fallido' THEN 1 ELSE 0 END) as intentos_fallidos,
					GROUP_CONCAT(DISTINCT auditoriaboleta_documento_escaneado) as documentos_intentados
				  FROM auditoriaboleta 
				  WHERE auditoriaboleta_fecha_hora >= '$fechaLimite'
				  GROUP BY auditoriaboleta_ip_address
				  HAVING intentos_fallidos >= $maxIntentos
				  ORDER BY intentos_fallidos DESC";
		$res = $this->_conn->query($query)->fetchAsObject();
		return $res;
	}

	/**
	 * Obtiene todos los logs de una sesión específica agrupados cronológicamente
	 * @param string $sessionId ID de la sesión
	 * @return array Array con todos los logs de la sesión
	 */
	public function getLogsPorSesion($sessionId)
	{
		$query = "SELECT * FROM auditoriaboleta 
				  WHERE auditoriaboleta_session_id = '$sessionId'
				  ORDER BY auditoriaboleta_fecha_hora ASC";
		$res = $this->_conn->query($query)->fetchAsObject();
		return $res;
	}

	/**
	 * Obtiene las sesiones más recientes agrupadas con resumen
	 * @param int $limite Número de sesiones a mostrar
	 * @param string $usuarioId Filtrar por usuario específico (opcional)
	 * @return array Array con resumen de sesiones
	 */
	public function getSesionesRecientes($limite = 50, $usuarioId = null)
	{
		$whereUsuario = $usuarioId ? "AND auditoriaboleta_usuario_validador_id = '$usuarioId'" : "";

		$query = "SELECT 
					auditoriaboleta_session_id,
					MIN(auditoriaboleta_fecha_hora) as sesion_inicio,
					MAX(auditoriaboleta_fecha_hora) as sesion_fin,
					COUNT(*) as total_acciones,
					auditoriaboleta_usuario_validador_nombre,
					auditoriaboleta_usuario_validador_id,
					auditoriaboleta_documento_escaneado,
					auditoriaboleta_metodo_escaneado,
					GROUP_CONCAT(DISTINCT auditoriaboleta_accion ORDER BY auditoriaboleta_fecha_hora) as secuencia_acciones,
					GROUP_CONCAT(DISTINCT auditoriaboleta_resultado) as resultados,
					SUM(CASE WHEN auditoriaboleta_resultado = 'exitoso' THEN 1 ELSE 0 END) as acciones_exitosas,
					SUM(CASE WHEN auditoriaboleta_resultado = 'fallido' THEN 1 ELSE 0 END) as acciones_fallidas,
					MAX(CASE WHEN auditoriaboleta_accion = 'VALIDACION_EXITOSA' THEN 1 ELSE 0 END) as validacion_completada
				  FROM auditoriaboleta 
				  WHERE auditoriaboleta_session_id IS NOT NULL $whereUsuario
				  GROUP BY auditoriaboleta_session_id
				  ORDER BY sesion_inicio DESC
				  LIMIT $limite";
		$res = $this->_conn->query($query)->fetchAsObject();
		return $res;
	}

	/**
	 * Obtiene estadísticas de sesiones por método de escaneo
	 * @param string $fechaInicio Fecha de inicio (Y-m-d)
	 * @param string $fechaFin Fecha de fin (Y-m-d)
	 * @return array Array con estadísticas por método
	 */
	public function getEstadisticasSesiones($fechaInicio, $fechaFin)
	{
		$query = "SELECT 
					auditoriaboleta_metodo_escaneado,
					COUNT(DISTINCT auditoriaboleta_session_id) as total_sesiones,
					AVG(sesion_stats.total_acciones) as promedio_acciones_por_sesion,
					AVG(sesion_stats.duracion_segundos) as promedio_duracion_segundos,
					SUM(sesion_stats.validacion_completada) as sesiones_exitosas
				  FROM (
					SELECT 
						auditoriaboleta_session_id,
						auditoriaboleta_metodo_escaneado,
						COUNT(*) as total_acciones,
						TIMESTAMPDIFF(SECOND, MIN(auditoriaboleta_fecha_hora), MAX(auditoriaboleta_fecha_hora)) as duracion_segundos,
						MAX(CASE WHEN auditoriaboleta_accion = 'VALIDACION_EXITOSA' THEN 1 ELSE 0 END) as validacion_completada
					FROM auditoriaboleta 
					WHERE DATE(auditoriaboleta_fecha_hora) BETWEEN '$fechaInicio' AND '$fechaFin'
					  AND auditoriaboleta_session_id IS NOT NULL
					GROUP BY auditoriaboleta_session_id, auditoriaboleta_metodo_escaneado
				  ) as sesion_stats
				  GROUP BY auditoriaboleta_metodo_escaneado
				  ORDER BY total_sesiones DESC";
		$res = $this->_conn->query($query)->fetchAsObject();
		return $res;
	}
}