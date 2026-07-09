<?php
/**
 * clase que genera la insercion y edicion  de reservasauditoria en la base de datos
 */
class Administracion_Model_DbTable_Reservasauditoria extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'reservas_auditoria';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'id';

	/**
	 * Normaliza cualquier valor a un string seguro para concatenar en SQL.
	 * Algunos llamadores (p.ej. Page_mainController::logAuditoria) pueden pasar
	 * arreglos crudos en campos pensados para texto; antes PHP los convertia
	 * silenciosamente a la palabra "Array" al concatenarlos, ahora los
	 * codificamos a JSON para no perder informacion ni tronar.
	 *
	 * @param mixed $value
	 * @return string
	 */
	private function _toDbString($value)
	{
		if ($value === null) {
			return '';
		}
		if (is_array($value) || is_object($value)) {
			return json_encode($value, JSON_UNESCAPED_UNICODE);
		}
		if (is_bool($value)) {
			return $value ? '1' : '0';
		}
		return (string) $value;
	}

	/**
	 * insert recibe la informacion de un reservasauditoria y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data)
	{
		$conn = $this->_conn->getConnection();
		$campos = [
			'reserva_id', 'numero_carnet', 'documento_socio', 'accion', 'controlador', 'metodo',
			'estado_anterior', 'estado_nuevo', 'mesa_id_anterior', 'mesa_id_nuevo',
			'invitados_antes', 'invitados_despues', 'datos_json', 'ip_address', 'user_agent',
			'session_data', 'url_completa', 'parametros_get', 'parametros_post', 'observaciones',
			'fecha_creacion', 'usuario_sistema',
		];
		$valores = [];
		foreach ($campos as $campo) {
			$valores[$campo] = $conn->real_escape_string($this->_toDbString($data[$campo] ?? ''));
		}
		$query = "INSERT INTO reservas_auditoria( " . implode(', ', $campos) . ") VALUES ( '"
			. implode("', '", $valores) . "')";
		$res = $this->_conn->query($query);
		return mysqli_insert_id($conn);
	}

	/**
	 * update Recibe la informacion de un reservasauditoria  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data, $id)
	{
		$conn = $this->_conn->getConnection();
		$campos = [
			'reserva_id', 'numero_carnet', 'documento_socio', 'accion', 'controlador', 'metodo',
			'estado_anterior', 'estado_nuevo', 'mesa_id_anterior', 'mesa_id_nuevo',
			'invitados_antes', 'invitados_despues', 'datos_json', 'ip_address', 'user_agent',
			'session_data', 'url_completa', 'parametros_get', 'parametros_post', 'observaciones',
			'fecha_creacion', 'usuario_sistema',
		];
		$sets = [];
		foreach ($campos as $campo) {
			$valor = $conn->real_escape_string($this->_toDbString($data[$campo] ?? ''));
			$sets[] = "$campo = '$valor'";
		}
		$idEsc = $conn->real_escape_string((string) $id);
		$query = "UPDATE reservas_auditoria SET " . implode(', ', $sets) . " WHERE id = '" . $idEsc . "'";
		$res = $this->_conn->query($query);
	}

	public function getByReserva($reservaId, $orderBy = 'fecha_creacion DESC')
	{
		return $this->getList("reserva_id = '$reservaId'", $orderBy);
	}

	/**
	 * Obtener auditoría por socio
	 */
	public function getBySocio($documentoSocio, $orderBy = 'fecha_creacion DESC')
	{
		return $this->getList("documento_socio = '$documentoSocio'", $orderBy);
	}

	/**
	 * Obtener auditoría por rango de fechas
	 */
	public function getByFechas($fechaInicio, $fechaFin, $orderBy = 'fecha_creacion DESC')
	{
		return $this->getList("fecha_creacion BETWEEN '$fechaInicio' AND '$fechaFin'", $orderBy);
	}
}