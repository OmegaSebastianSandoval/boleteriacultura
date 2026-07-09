<?php

/**
 * clase que genera la insercion y edicion  de ambiente en la base de datos
 */
class Administracion_Model_DbTable_Ambientes extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'ambientes';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'ambiente_id';

	/**
	 * insert recibe la informacion de un ambiente y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data)
	{
		$ambiente_evento = $data['ambiente_evento'];
		$ambiente_piso = $data['ambiente_piso'];
		$ambiente_nombre = $data['ambiente_nombre'];
		$ambiente_capacidad = $data['ambiente_capacidad'];
		$ambiente_categoria = $data['ambiente_categoria'];
		$ambiente_estado = $data['ambiente_estado'];
		$ambiente_imagen_disponible = $data['ambiente_imagen_disponible'];
		$ambiente_imagen_pendiente = $data['ambiente_imagen_pendiente'];
		$ambiente_imagen_ocupado = $data['ambiente_imagen_ocupado'];
		$ambiente_imagen_ubicacion_en_piso = $data['ambiente_imagen_ubicacion_en_piso'];
		$ambiente_filas = $data['ambiente_filas'] ?? 0;
		$ambiente_columnas = $data['ambiente_columnas'] ?? 0;
		$ambiente_descuento = $data['ambiente_descuento'] ?? 0;
		$ambiente_fecha_partido = $data['ambiente_fecha_partido'] ?? null;
		$fechaPartidoSql = $ambiente_fecha_partido ? "'$ambiente_fecha_partido'" : 'NULL';

		$query = "INSERT INTO ambientes( ambiente_evento, ambiente_piso, ambiente_nombre, ambiente_capacidad, ambiente_categoria, ambiente_estado, ambiente_imagen_disponible, ambiente_imagen_pendiente, ambiente_imagen_ocupado, ambiente_imagen_ubicacion_en_piso, ambiente_filas, ambiente_columnas, ambiente_descuento, ambiente_fecha_partido) VALUES ( '$ambiente_evento', '$ambiente_piso', '$ambiente_nombre', '$ambiente_capacidad', '$ambiente_categoria', '$ambiente_estado', '$ambiente_imagen_disponible', '$ambiente_imagen_pendiente', '$ambiente_imagen_ocupado', '$ambiente_imagen_ubicacion_en_piso', '$ambiente_filas', '$ambiente_columnas', '$ambiente_descuento', $fechaPartidoSql)";
		$res = $this->_conn->query($query);
		return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un ambiente  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data, $id)
	{

		$ambiente_evento = $data['ambiente_evento'];
		$ambiente_piso = $data['ambiente_piso'];
		$ambiente_nombre = $data['ambiente_nombre'];
		$ambiente_capacidad = $data['ambiente_capacidad'];
		$ambiente_categoria = $data['ambiente_categoria'];
		$ambiente_estado = $data['ambiente_estado'];
		$ambiente_imagen_disponible = $data['ambiente_imagen_disponible'];
		$ambiente_imagen_pendiente = $data['ambiente_imagen_pendiente'];
		$ambiente_imagen_ocupado = $data['ambiente_imagen_ocupado'];
		$ambiente_imagen_ubicacion_en_piso = $data['ambiente_imagen_ubicacion_en_piso'];
		$ambiente_filas = $data['ambiente_filas'];
		$ambiente_columnas = $data['ambiente_columnas'];
		$ambiente_descuento = $data['ambiente_descuento'] ?? 0;
		$ambiente_fecha_partido = $data['ambiente_fecha_partido'] ?? null;
		$fechaPartidoSql = $ambiente_fecha_partido ? "'$ambiente_fecha_partido'" : 'NULL';

		$query = "UPDATE ambientes SET  ambiente_evento = '$ambiente_evento', ambiente_piso = '$ambiente_piso', ambiente_nombre = '$ambiente_nombre', ambiente_capacidad = '$ambiente_capacidad', ambiente_categoria = '$ambiente_categoria', ambiente_estado = '$ambiente_estado', ambiente_imagen_disponible = '$ambiente_imagen_disponible', ambiente_imagen_pendiente = '$ambiente_imagen_pendiente', ambiente_imagen_ocupado = '$ambiente_imagen_ocupado', ambiente_imagen_ubicacion_en_piso = '$ambiente_imagen_ubicacion_en_piso', ambiente_filas = '$ambiente_filas', ambiente_columnas = '$ambiente_columnas', ambiente_descuento = '$ambiente_descuento', ambiente_fecha_partido = $fechaPartidoSql WHERE ambiente_id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}
}
