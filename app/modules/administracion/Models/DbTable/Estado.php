<?php 
/**
* clase que genera la insercion y edicion  de Estados en la base de datos
*/
class Administracion_Model_DbTable_Estado extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'estado';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'estado_id';

	/**
	 * insert recibe la informacion de un Estados y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data){
		$estado_codigo = $data['estado_codigo'];
		$estado_nombre = $data['estado_nombre'];
		$estado_fecha_creacion = $data['estado_fecha_creacion'];
		$estado_fecha_actualizacion = $data['estado_fecha_actualizacion'];
		$estado_usuario_creacion = $data['estado_usuario_creacion'];
		$estado_usuario_actualizacion = $data['estado_usuario_actualizacion'];
		$query = "INSERT INTO estado( estado_codigo, estado_nombre, estado_fecha_creacion, estado_fecha_actualizacion, estado_usuario_creacion, estado_usuario_actualizacion) VALUES ( '$estado_codigo', '$estado_nombre', '$estado_fecha_creacion', '$estado_fecha_actualizacion', '$estado_usuario_creacion', '$estado_usuario_actualizacion')";
		$res = $this->_conn->query($query);
        return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un Estados  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data,$id){
		
		$estado_codigo = $data['estado_codigo'];
		$estado_nombre = $data['estado_nombre'];
		$estado_fecha_creacion = $data['estado_fecha_creacion'];
		$estado_fecha_actualizacion = $data['estado_fecha_actualizacion'];
		$estado_usuario_creacion = $data['estado_usuario_creacion'];
		$estado_usuario_actualizacion = $data['estado_usuario_actualizacion'];
		$query = "UPDATE estado SET  estado_codigo = '$estado_codigo', estado_nombre = '$estado_nombre', estado_fecha_creacion = '$estado_fecha_creacion', estado_fecha_actualizacion = '$estado_fecha_actualizacion', estado_usuario_creacion = '$estado_usuario_creacion', estado_usuario_actualizacion = '$estado_usuario_actualizacion' WHERE estado_id = '".$id."'";
		$res = $this->_conn->query($query);
	}
}