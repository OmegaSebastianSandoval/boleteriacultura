<?php 
/**
* clase que genera la insercion y edicion  de mesasbloqueo en la base de datos
*/
class Administracion_Model_DbTable_Mesasbloqueo extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'mesas_bloqueo';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'mesa_bloqueo_id';

	/**
	 * insert recibe la informacion de un mesasbloqueo y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data){
		$mesa_bloqueo_mesa = $data['mesa_bloqueo_mesa'];
		$mesa_bloqueo_documento = $data['mesa_bloqueo_documento'];
		$mesa_bloqueo_fecha = $data['mesa_bloqueo_fecha'];
		$mesa_bloqueo_fecha_expiracion = $data['mesa_bloqueo_fecha_expiracion'];
		$mesa_bloqueo_estado = $data['mesa_bloqueo_estado'];
		$mesa_bloqueo_macnume = $data['mesa_bloqueo_macnume'];
		$mesa_bloqueo_reserva = $data['mesa_bloqueo_reserva'];
		$mesa_bloqueo_capacidad = $data['mesa_bloqueo_capacidad'] ?? null; // Opcional, si no se proporciona, se puede omitir
		$query = "INSERT INTO mesas_bloqueo( mesa_bloqueo_mesa, mesa_bloqueo_documento, mesa_bloqueo_fecha, mesa_bloqueo_fecha_expiracion, mesa_bloqueo_estado, mesa_bloqueo_macnume, mesa_bloqueo_reserva, mesa_bloqueo_capacidad) VALUES ( '$mesa_bloqueo_mesa', '$mesa_bloqueo_documento', '$mesa_bloqueo_fecha', '$mesa_bloqueo_fecha_expiracion', '$mesa_bloqueo_estado', '$mesa_bloqueo_macnume', '$mesa_bloqueo_reserva', '$mesa_bloqueo_capacidad')";
		$res = $this->_conn->query($query);
        return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un mesasbloqueo  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data,$id){
		
		$mesa_bloqueo_mesa = $data['mesa_bloqueo_mesa'];
		$mesa_bloqueo_documento = $data['mesa_bloqueo_documento'];
		$mesa_bloqueo_fecha = $data['mesa_bloqueo_fecha'];
		$mesa_bloqueo_fecha_expiracion = $data['mesa_bloqueo_fecha_expiracion'];
		$mesa_bloqueo_estado = $data['mesa_bloqueo_estado'];
		$mesa_bloqueo_macnume = $data['mesa_bloqueo_macnume'];
		$mesa_bloqueo_reserva = $data['mesa_bloqueo_reserva'];
		$mesa_bloqueo_capacidad = $data['mesa_bloqueo_capacidad'] ?? null; // Opcional, si no se proporciona, se puede omitir
		$query = "UPDATE mesas_bloqueo SET  mesa_bloqueo_mesa = '$mesa_bloqueo_mesa', mesa_bloqueo_documento = '$mesa_bloqueo_documento', mesa_bloqueo_fecha = '$mesa_bloqueo_fecha', mesa_bloqueo_fecha_expiracion = '$mesa_bloqueo_fecha_expiracion', mesa_bloqueo_estado = '$mesa_bloqueo_estado', mesa_bloqueo_macnume = '$mesa_bloqueo_macnume', mesa_bloqueo_reserva = '$mesa_bloqueo_reserva', mesa_bloqueo_capacidad = '$mesa_bloqueo_capacidad' WHERE mesa_bloqueo_id = '".$id."'";
		$res = $this->_conn->query($query);
	}

	/**
	 * liberarPorReservaOMesas elimina los bloqueos de mesa asociados a una reserva
	 * y/o a un conjunto de mesas (usado al eliminar una reserva por completo)
	 * @param  integer $reservaId  identificador de la reserva
	 * @param  array   $mesaIds    identificadores de mesa a liberar
	 * @return void
	 */
	public function liberarPorReservaOMesas($reservaId, array $mesaIds = [])
	{
		$reservaId = intval($reservaId);
		$mesaIds = array_filter(array_map('intval', $mesaIds));
		$condiciones = ["mesa_bloqueo_reserva = '$reservaId'"];
		if (!empty($mesaIds)) {
			$condiciones[] = "mesa_bloqueo_mesa IN (" . implode(',', $mesaIds) . ")";
		}
		$query = "DELETE FROM mesas_bloqueo WHERE " . implode(' OR ', $condiciones);
		$this->_conn->query($query);
	}
}