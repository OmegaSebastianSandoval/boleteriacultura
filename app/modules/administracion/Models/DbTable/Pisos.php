<?php
/**
 * clase que genera la insercion y edicion  de piso en la base de datos
 */
class Administracion_Model_DbTable_Pisos extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'pisos';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'piso_id';

	/**
	 * insert recibe la informacion de un piso y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data)
	{
		$piso_evento = $data['piso_evento'];
		$piso_nombre = $data['piso_nombre'];
		$piso_color = $data['piso_color'];
		$piso_estado = $data['piso_estado'];
		$piso_imagen_disponible = $data['piso_imagen_disponible'];
		$piso_imagen_pendiente = $data['piso_imagen_pendiente'];
		$piso_imagen_ocupado = $data['piso_imagen_ocupado'];
		$query = "INSERT INTO pisos( piso_evento, piso_nombre, piso_color, piso_estado, piso_imagen_disponible, piso_imagen_pendiente, piso_imagen_ocupado) VALUES ( '$piso_evento', '$piso_nombre', '$piso_color', '$piso_estado', '$piso_imagen_disponible', '$piso_imagen_pendiente', '$piso_imagen_ocupado')";
		$res = $this->_conn->query($query);
		return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un piso  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data, $id)
	{

		$piso_evento = $data['piso_evento'];
		$piso_nombre = $data['piso_nombre'];
		$piso_color = $data['piso_color'];
		$piso_estado = $data['piso_estado'];
		$piso_imagen_disponible = $data['piso_imagen_disponible'];
		$piso_imagen_pendiente = $data['piso_imagen_pendiente'];
		$piso_imagen_ocupado = $data['piso_imagen_ocupado'];
		$query = "UPDATE pisos SET  piso_evento = '$piso_evento', piso_nombre = '$piso_nombre', piso_color = '$piso_color', piso_estado = '$piso_estado', piso_imagen_disponible = '$piso_imagen_disponible', piso_imagen_pendiente = '$piso_imagen_pendiente', piso_imagen_ocupado = '$piso_imagen_ocupado' WHERE piso_id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}
}