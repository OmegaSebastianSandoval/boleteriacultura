<?php

/**
 * clase que genera la insercion y edicion  de reservas en la base de datos
 */
class Administracion_Model_DbTable_Reservas extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'reservas';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'id';

	const TRUNCATE_SECRET = 'boleteria_secure_truncate_2025';

	/**
	 * insert recibe la informacion de un reservas y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data)
	{

		$reserva_id_evento = $data['reserva_id_evento'];
		$reserva_nombre_cliente = $data['reserva_nombre_cliente'];
		$reserva_apellido_cliente = $data['reserva_apellido_cliente'];
		$reserva_numero_carnet = $data['reserva_numero_carnet'];
		$reserva_documento = $data['reserva_documento'];
		$reserva_fecha = $data['reserva_fecha'];
		$reserva_hora = $data['reserva_hora'] ?? date('H:i:s');
		$reserva_total_personas = $data['reserva_total_personas'] ?? 0;
		$reserva_telefono = $data['reserva_telefono'];
		$reserva_correo = $data['reserva_correo'];
		$reserva_comentario = $data['reserva_comentario'] ?? '';
		$reserva_fecha_creacion = $data['reserva_fecha_creacion'];
		$reserva_fecha_actualizacion = $data['reserva_fecha_actualizacion'];
		$reserva_usuario_creacion = $data['reserva_usuario_creacion'];
		$reserva_usuario_actualizacion = $data['reserva_usuario_actualizacion'] ?? $data['reserva_usuario_creacion'];
		$reserva_fecha_inicio_reserva = $data['reserva_fecha_inicio_reserva'] ?? null;
		$reserva_fecha_cierre_reserva = $data['reserva_fecha_cierre_reserva'] ?? null;
		$reserva_fecha_limite_pago = $data['reserva_fecha_limite_pago'] ?? null;
		$reserva_estado = $data['reserva_estado'];
		$reserva_estado_texto = $data['reserva_estado_texto'] ?? '';
		$reserva_estado_texto2 = $data['reserva_estado_texto2'] ?? '';
		$reserva_mesa_id = $data['reserva_mesa_id'] ?? null;
		$reserva_total_pagar = $data['reserva_total_pagar'] ?? 0;
		$reserva_metodo_pago = $data['reserva_metodo_pago'] ?? '';
		$reserva_numero_cuotas = $data['reserva_numero_cuotas'] ?? 1;
		$reserva_fecha_pago = $data['reserva_fecha_pago'] ?? null;
		$reserva_ip = $data['reserva_ip'] ?? '';
		$reserva_user_agent = $data['reserva_user_agent'] ?? '';

		$query = "INSERT INTO reservas( 
			reserva_id_evento, 
			reserva_nombre_cliente, 
			reserva_apellido_cliente, 
			reserva_fecha, 
			reserva_hora, 
			reserva_total_personas, 
			reserva_telefono, 
			reserva_correo, 
			reserva_comentario, 
			reserva_fecha_creacion, 
			reserva_fecha_actualizacion, 
			reserva_usuario_creacion, 
			reserva_usuario_actualizacion, 
			reserva_fecha_inicio_reserva, 
			reserva_fecha_cierre_reserva, 
			reserva_fecha_limite_pago, 
			reserva_numero_carnet, 
			reserva_documento, 
			reserva_estado,
			reserva_estado_texto,
			reserva_estado_texto2,
			reserva_mesa_id,
			reserva_total_pagar,
			reserva_metodo_pago,
			reserva_numero_cuotas,
			reserva_fecha_pago,
			reserva_ip,
			reserva_user_agent
		) VALUES ( 
			'$reserva_id_evento', 
			'$reserva_nombre_cliente', 
			'$reserva_apellido_cliente', 
			'$reserva_fecha', 
			'$reserva_hora', 
			'$reserva_total_personas', 
			'$reserva_telefono', 
			'$reserva_correo', 
			'$reserva_comentario', 
			'$reserva_fecha_creacion', 
			'$reserva_fecha_actualizacion', 
			'$reserva_usuario_creacion', 
			'$reserva_usuario_actualizacion', 
			'$reserva_fecha_inicio_reserva', 
			'$reserva_fecha_cierre_reserva', 
			'$reserva_fecha_limite_pago', 
			'$reserva_numero_carnet', 
			'$reserva_documento', 
			'$reserva_estado',
			'$reserva_estado_texto',
			'$reserva_estado_texto2',
			'$reserva_mesa_id',
			'$reserva_total_pagar',
			'$reserva_metodo_pago',
			'$reserva_numero_cuotas',
			" . ($reserva_fecha_pago ? "'$reserva_fecha_pago'" : "NULL") . ",
			'$reserva_ip',
			'$reserva_user_agent'
		)";
		$res = $this->_conn->query($query);
		return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un reservas  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data, $id)
	{

		$reserva_id_evento = $data['reserva_id_evento'];
		$reserva_nombre_cliente = $data['reserva_nombre_cliente'];
		$reserva_apellido_cliente = $data['reserva_apellido_cliente'];
		$reserva_numero_carnet = $data['reserva_numero_carnet'];
		$reserva_documento = $data['reserva_documento'];

		$reserva_fecha = $data['reserva_fecha'];
		$reserva_hora = $data['reserva_hora'];
		$reserva_telefono = $data['reserva_telefono'];
		$reserva_correo = $data['reserva_correo'];
		$reserva_comentario = $data['reserva_comentario'];
		$reserva_fecha_creacion = $data['reserva_fecha_creacion'];
		$reserva_fecha_actualizacion = $data['reserva_fecha_actualizacion'];
		$reserva_usuario_creacion = $data['reserva_usuario_creacion'];
		$reserva_usuario_actualizacion = $data['reserva_usuario_actualizacion'];
		$reserva_fecha_inicio_reserva = $data['reserva_fecha_inicio_reserva'];
		$reserva_fecha_cierre_reserva = $data['reserva_fecha_cierre_reserva'];
		$reserva_fecha_limite_pago = $data['reserva_fecha_limite_pago'];
		$reserva_estado = $data['reserva_estado'];
		$query = "UPDATE reservas SET  reserva_id_evento = '$reserva_id_evento', reserva_nombre_cliente = '$reserva_nombre_cliente', reserva_apellido_cliente = '$reserva_apellido_cliente', reserva_fecha = '$reserva_fecha', reserva_hora = '$reserva_hora',  reserva_telefono = '$reserva_telefono', reserva_correo = '$reserva_correo', reserva_comentario = '$reserva_comentario', reserva_fecha_creacion = '$reserva_fecha_creacion', reserva_fecha_actualizacion = '$reserva_fecha_actualizacion', reserva_usuario_creacion = '$reserva_usuario_creacion', reserva_usuario_actualizacion = '$reserva_usuario_actualizacion', reserva_fecha_inicio_reserva = '$reserva_fecha_inicio_reserva', reserva_fecha_cierre_reserva = '$reserva_fecha_cierre_reserva', reserva_fecha_limite_pago = '$reserva_fecha_limite_pago', reserva_numero_carnet = '$reserva_numero_carnet', reserva_documento = '$reserva_documento', reserva_estado = '$reserva_estado' WHERE id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}

	public function updateReservaMesa($data, $id)
	{
		$reserva_mesa_id = $data['reserva_mesa_id'];
		$query = "UPDATE reservas SET reserva_mesa_id = '$reserva_mesa_id' WHERE id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}

	public function updateReservaPago($data, $id)
	{

		$reserva_total_pagar = $data['reserva_total_pagar'];
		$reserva_metodo_pago = $data['reserva_metodo_pago'];
		$reserva_numero_cuotas = ($data['reserva_numero_cuotas'] && $data['reserva_numero_cuotas'] >= 1) ? $data['reserva_numero_cuotas'] : 0;
		$reserva_total_personas = $data['reserva_total_personas'];
		$reserva_ip = $data['reserva_ip'] ?? '';
		$reserva_user_agent = $data['reserva_user_agent'] ?? '';
		$reserva_aceptaterminos = $data['reserva_aceptaterminos'] ?? 0;
		$reserva_aceptapoliticas = $data['reserva_aceptapoliticas'] ?? 0;
		$query = "UPDATE reservas SET reserva_total_pagar = '$reserva_total_pagar', reserva_metodo_pago = '$reserva_metodo_pago', reserva_numero_cuotas = '$reserva_numero_cuotas', reserva_total_personas = '$reserva_total_personas', reserva_ip = '$reserva_ip', reserva_user_agent = '$reserva_user_agent', reserva_aceptaterminos = '$reserva_aceptaterminos', reserva_aceptapoliticas = '$reserva_aceptapoliticas' WHERE id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}

	public function updateReservaPagoCargo($data, $id)
	{
		$reserva_estado = $data['reserva_estado'];
		$reserva_estado_texto = $data['reserva_estado_texto'];
		$reserva_estado_texto2 = $data['reserva_estado_texto2'];
		$reserva_fecha_pago = $data['reserva_fecha_pago'];

		$query = "UPDATE reservas SET reserva_estado = '$reserva_estado', reserva_estado_texto = '$reserva_estado_texto', reserva_estado_texto2 = '$reserva_estado_texto2', reserva_fecha_pago = '$reserva_fecha_pago' WHERE id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}
	public function updateReservaPagoLinea($data, $id)
	{
		$reserva_estado = $data['reserva_estado'];
		$reserva_estado_texto = $data['reserva_estado_texto'];
		$reserva_estado_texto2 = $data['reserva_estado_texto2'];

		$query = "UPDATE reservas SET reserva_estado = '$reserva_estado', reserva_estado_texto = '$reserva_estado_texto', reserva_estado_texto2 = '$reserva_estado_texto2' WHERE id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}

	/**
	 * Actualiza el estado cuando el pago es APROBADO
	 */
	public function updatePagoAprobado($id, $authorization = '', $franquicia = '')
	{
		$query = "UPDATE reservas SET 
			reserva_estado = '3', 
			reserva_estado_texto = 'Aprobado', 
			reserva_estado_texto2 = 'El pago ha sido aprobado exitosamente',
			reserva_cus = '" . addslashes($authorization) . "',
			reserva_franquicia = '" . addslashes($franquicia) . "',
			reserva_fecha_pago = '" . date('Y-m-d H:i:s') . "'
			WHERE id = '" . intval($id) . "'";
		return $this->_conn->query($query);
	}

	/**
	 * Actualiza el estado cuando el pago está PENDIENTE
	 */
	public function updatePagoPendiente($id, $authorization = '', $franquicia = '')
	{
		$query = "UPDATE reservas SET 
			reserva_estado = '4', 
			reserva_estado_texto = 'Pendiente', 
			reserva_estado_texto2 = 'El pago se encuentra pendiente de confirmación',
			reserva_cus = '" . addslashes($authorization) . "',
			reserva_franquicia = '" . addslashes($franquicia) . "',
			reserva_fecha_pago = '" . date('Y-m-d H:i:s') . "'
			WHERE id = '" . intval($id) . "'";
		return $this->_conn->query($query);
	}

	/**
	 * Actualiza el estado cuando el pago FALLA
	 */
	public function updatePagoFallido($id, $authorization = '', $franquicia = '')
	{
		$query = "UPDATE reservas SET 
			reserva_estado = '5', 
			reserva_estado_texto = 'Fallido', 
			reserva_estado_texto2 = 'La transacción ha sido cancelada por el usuario',
			reserva_cus = '" . addslashes($authorization) . "',
			reserva_franquicia = '" . addslashes($franquicia) . "',
			reserva_fecha_pago = '" . date('Y-m-d H:i:s') . "'
			WHERE id = '" . intval($id) . "'";
		return $this->_conn->query($query);
	}

	/**
	 * Actualiza el estado cuando el pago es RECHAZADO
	 */
	public function updatePagoRechazado($id, $authorization = '', $franquicia = '')
	{
		$query = "UPDATE reservas SET 
			reserva_estado = '6', 
			reserva_estado_texto = 'Rechazado', 
			reserva_estado_texto2 = 'El pago ha sido rechazado por la entidad financiera',
			reserva_cus = '" . addslashes($authorization) . "',
			reserva_franquicia = '" . addslashes($franquicia) . "',
			reserva_fecha_pago = '" . date('Y-m-d H:i:s') . "'
			WHERE id = '" . intval($id) . "'";
		return $this->_conn->query($query);
	}

	public function getIntentos()
	{

		$select = "
	SELECT 
    r.reserva_numero_carnet,
    r.reserva_documento,
    r.reserva_nombre_cliente,
    r.reserva_apellido_cliente,
    r.reserva_telefono,
    r.reserva_correo,
    COUNT(r.id) AS intentos_reserva,
    GROUP_CONCAT(r.reserva_estado SEPARATOR ', ') AS estados_intentados,
    GROUP_CONCAT(r.id SEPARATOR ', ') AS ids_reservas
FROM reservas r
WHERE r.reserva_estado NOT IN (2, 3, 11)
  AND NOT EXISTS (
      SELECT 1 
      FROM reservas r2
      WHERE r2.reserva_numero_carnet = r.reserva_numero_carnet
        AND r2.reserva_estado IN (2, 3, 11)
  )
GROUP BY r.reserva_numero_carnet, r.reserva_documento, r.reserva_nombre_cliente, 
         r.reserva_apellido_cliente, r.reserva_telefono, r.reserva_correo
ORDER BY intentos_reserva DESC;


		";
		$res = $this->_conn->query($select)->fetchAsObject();
		return $res;
	}

	public function getBackupData()
	{
		$select = "SELECT * FROM reservas";
		$res = $this->_conn->query($select)->fetchAsObject();
		return $res;
	}

	public function getBackupSQL()
	{
		$sql = "-- Backup de la tabla reservas generado el " . date('Y-m-d H:i:s') . "\n\n";

		// Obtener CREATE TABLE
		$createResult = $this->_conn->query("SHOW CREATE TABLE `{$this->_name}`");
		$createData = $createResult->fetchAsArray();
		$createRow = $createData[0];
		$sql .= $createRow['Create Table'] . ";\n\n";

		// Obtener datos
		$dataResult = $this->_conn->query("SELECT * FROM `{$this->_name}`");
		$data = $dataResult->fetchAsArray();
		foreach ($data as $row) {
			$columns = array_keys($row);
			$values = array_map(function ($v) {
				return "'" . addslashes($v) . "'";
			}, $row);
			$sql .= "INSERT INTO `{$this->_name}` (`" . implode('`,`', $columns) . "`) VALUES (" . implode(',', $values) . ");\n";
		}

		return $sql;
	}


	public function truncateTables($securityToken)
	{
		if (!hash_equals($securityToken, hash('sha256', self::TRUNCATE_SECRET))) {
			throw new Exception('Invalid security token for truncate operation');
		}

		$tables = [
			'accion_sesiones',
			'auditoriaboleta',
			'beneficiario_bloqueos',
			'boletas_info',
			'cola_compras',
			'invitadosreserva',
			'log',
			'mesas_bloqueo',
			'reservas',
			'reservas_auditoria',
			'terminos'
		];

		foreach ($tables as $table) {
			$sql = "TRUNCATE TABLE `$table`;";
			$this->_conn->query($sql);
		}

		

		return true;
	}
}
