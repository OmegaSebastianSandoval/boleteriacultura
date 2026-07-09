<?php

/**
 * clase que genera la insercion y edicion de cupos adicionales de una reserva en la base de datos
 */
class Administracion_Model_DbTable_Reservacuposadicionales extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'reserva_cupos_adicionales';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'id';

	/**
	 * insert recibe la informacion de un registro de cupos adicionales y la inserta en la base de datos
	 * @param  array $data
	 * @return integer identificador del registro que se inserto
	 */
	public function insert($data)
	{
		$reserva_id = $data['reserva_id'];
		$mesa_id = $data['mesa_id'];
		$cupos_capacidad_anterior = $data['cupos_capacidad_anterior'];
		$cupos_capacidad_nueva = $data['cupos_capacidad_nueva'];
		$cupos_adicionales = $data['cupos_adicionales'];
		$precio_unitario = $data['precio_unitario'] ?? 0;
		$precio_total = $data['precio_total'] ?? 0;
		$cupos_estado = $data['cupos_estado'] ?? 0;
		$cupos_metodo_pago = $data['cupos_metodo_pago'] ?? null;
		$cupos_fecha_creacion = $data['cupos_fecha_creacion'] ?? date('Y-m-d H:i:s');
		$cupos_usuario_creacion = $data['cupos_usuario_creacion'] ?? '';
		$cupos_fecha_pago = $data['cupos_fecha_pago'] ?? null;
		$invitados_ids = $data['invitados_ids'] ?? null;

		$metodoPagoSql = $cupos_metodo_pago ? "'" . $cupos_metodo_pago . "'" : 'NULL';
		$fechaPagoSql = $cupos_fecha_pago ? "'" . $cupos_fecha_pago . "'" : 'NULL';
		$invitadosIdsSql = $invitados_ids ? "'" . $invitados_ids . "'" : 'NULL';

		$query = "INSERT INTO reserva_cupos_adicionales (reserva_id, mesa_id, cupos_capacidad_anterior, cupos_capacidad_nueva, cupos_adicionales, precio_unitario, precio_total, cupos_estado, cupos_metodo_pago, cupos_fecha_creacion, cupos_usuario_creacion, cupos_fecha_pago, invitados_ids) VALUES ('$reserva_id', '$mesa_id', '$cupos_capacidad_anterior', '$cupos_capacidad_nueva', '$cupos_adicionales', '$precio_unitario', '$precio_total', '$cupos_estado', $metodoPagoSql, '$cupos_fecha_creacion', '$cupos_usuario_creacion', $fechaPagoSql, $invitadosIdsSql)";
		$this->_conn->query($query);
		return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * deleteByReserva elimina todos los cupos adicionales asociados a una reserva
	 * (usado al eliminar una reserva por completo)
	 * @param  integer $reservaId identificador de la reserva
	 * @return void
	 */
	public function deleteByReserva($reservaId)
	{
		$reservaId = intval($reservaId);
		$query = "DELETE FROM reserva_cupos_adicionales WHERE reserva_id = '$reservaId'";
		$this->_conn->query($query);
	}
}
