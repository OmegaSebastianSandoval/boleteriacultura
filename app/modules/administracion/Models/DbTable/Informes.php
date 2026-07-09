<?php 
/**
* clase que genera la insercion y edicion  de Informes en la base de datos
*/
class Administracion_Model_DbTable_Informes extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'informes';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'informe_id';

	/**
	 * insert recibe la informacion de un Informes y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data){
		$informe_nombre = $data['informe_nombre'];
		$informe_descripcion = $data['informe_descripcion'];
		$informe_archivo = $data['informe_archivo'];
		$informes_fecha_creacion = $data['informes_fecha_creacion'];
		$informes_fecha_actualizacion = $data['informes_fecha_actualizacion'];
		$informes_usuario_creacion = $data['informes_usuario_creacion'];
		$informes_usuario_actualizacion = $data['informes_usuario_actualizacion'];
		$query = "INSERT INTO informes( informe_nombre, informe_descripcion, informe_archivo, informes_fecha_creacion, informes_fecha_actualizacion, informes_usuario_creacion, informes_usuario_actualizacion) VALUES ( '$informe_nombre', '$informe_descripcion', '$informe_archivo', '$informes_fecha_creacion', '$informes_fecha_actualizacion', '$informes_usuario_creacion', '$informes_usuario_actualizacion')";
		$res = $this->_conn->query($query);
        return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un Informes  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data,$id){
		
		$informe_nombre = $data['informe_nombre'];
		$informe_descripcion = $data['informe_descripcion'];
		$informe_archivo = $data['informe_archivo'];
		$informes_fecha_creacion = $data['informes_fecha_creacion'];
		$informes_fecha_actualizacion = $data['informes_fecha_actualizacion'];
		$informes_usuario_creacion = $data['informes_usuario_creacion'];
		$informes_usuario_actualizacion = $data['informes_usuario_actualizacion'];
		$query = "UPDATE informes SET  informe_nombre = '$informe_nombre', informe_descripcion = '$informe_descripcion', informe_archivo = '$informe_archivo', informes_fecha_creacion = '$informes_fecha_creacion', informes_fecha_actualizacion = '$informes_fecha_actualizacion', informes_usuario_creacion = '$informes_usuario_creacion', informes_usuario_actualizacion = '$informes_usuario_actualizacion' WHERE informe_id = '".$id."'";
		$res = $this->_conn->query($query);
	}
}