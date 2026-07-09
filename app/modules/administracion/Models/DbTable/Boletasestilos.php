<?php

/**
 * clase que genera la insercion y edicion  de boletasestilo en la base de datos
 */
class Administracion_Model_DbTable_Boletasestilos extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'boletas_estilos';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'boletas_estilo_id';

	/**
	 * insert recibe la informacion de un boletasestilo y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data)
	{
		$boletas_estilo_estado = $data['boletas_estilo_estado'];
		$boletas_estilo_titulo = $data['boletas_estilo_titulo'];
		$boletas_estilo_fondo = $data['boletas_estilo_fondo'];
		$boletas_estilo_textofooter = $data['boletas_estilo_textofooter'];
		$query = "INSERT INTO boletas_estilos( boletas_estilo_estado, boletas_estilo_titulo, boletas_estilo_fondo, boletas_estilo_textofooter) VALUES ( '$boletas_estilo_estado', '$boletas_estilo_titulo', '$boletas_estilo_fondo', '$boletas_estilo_textofooter')";
		$res = $this->_conn->query($query);
		return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un boletasestilo  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data, $id)
	{

		$boletas_estilo_estado = $data['boletas_estilo_estado'];
		$boletas_estilo_titulo = $data['boletas_estilo_titulo'];
		$boletas_estilo_fondo = $data['boletas_estilo_fondo'];
		$boletas_estilo_textofooter = $data['boletas_estilo_textofooter'];
		$query = "UPDATE boletas_estilos SET  boletas_estilo_estado = '$boletas_estilo_estado', boletas_estilo_titulo = '$boletas_estilo_titulo', boletas_estilo_fondo = '$boletas_estilo_fondo', boletas_estilo_textofooter = '$boletas_estilo_textofooter' WHERE boletas_estilo_id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}
}
