<?php 
/**
* clase que genera la insercion y edicion  de terminos aceptaciones en la base de datos
*/
class Administracion_Model_DbTable_Terminosaceptaciones extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'terminos_aceptaciones';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'aceptacion_id';

	/**
	 * insert recibe la informacion de un termino aceptacion y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data){
		$aceptacion_reserva_id = $data['aceptacion_reserva_id'];
		$aceptacion_termino_id = $data['aceptacion_termino_id'];
		$aceptacion_termino_titulo = $data['aceptacion_termino_titulo'];
		$aceptacion_usuario_documento = $data['aceptacion_usuario_documento'];
		$aceptacion_usuario_carnet = $data['aceptacion_usuario_carnet'];
		$aceptacion_usuario_nombre = $data['aceptacion_usuario_nombre'];
		$aceptacion_usuario_apellido = $data['aceptacion_usuario_apellido'];
		$aceptacion_usuario_email = $data['aceptacion_usuario_email'];
		$aceptacion_ip = $data['aceptacion_ip'];
		$aceptacion_user_agent = $data['aceptacion_user_agent'];
		$aceptacion_dispositivo = $data['aceptacion_dispositivo'];
		$aceptacion_navegador = $data['aceptacion_navegador'];
		$aceptacion_fecha = $data['aceptacion_fecha'];
		$aceptacion_datos_adicionales = $data['aceptacion_datos_adicionales'];
		$aceptacion_estado = $data['aceptacion_estado'];
		$aceptacion_tipo = $data['aceptacion_tipo'];
		$query = "INSERT INTO terminos_aceptaciones( aceptacion_reserva_id, aceptacion_termino_id, aceptacion_termino_titulo, aceptacion_usuario_documento, aceptacion_usuario_carnet, aceptacion_usuario_nombre, aceptacion_usuario_apellido, aceptacion_usuario_email, aceptacion_ip, aceptacion_user_agent, aceptacion_dispositivo, aceptacion_navegador, aceptacion_fecha, aceptacion_datos_adicionales, aceptacion_estado, aceptacion_tipo) VALUES ( '$aceptacion_reserva_id', '$aceptacion_termino_id', '$aceptacion_termino_titulo', '$aceptacion_usuario_documento', '$aceptacion_usuario_carnet', '$aceptacion_usuario_nombre', '$aceptacion_usuario_apellido', '$aceptacion_usuario_email', '$aceptacion_ip', '$aceptacion_user_agent', '$aceptacion_dispositivo', '$aceptacion_navegador', '$aceptacion_fecha', '$aceptacion_datos_adicionales', '$aceptacion_estado', '$aceptacion_tipo')";
		$res = $this->_conn->query($query);
        return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un termino aceptacion  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data,$id){
		
		$aceptacion_reserva_id = $data['aceptacion_reserva_id'];
		$aceptacion_termino_id = $data['aceptacion_termino_id'];
		$aceptacion_termino_titulo = $data['aceptacion_termino_titulo'];
		$aceptacion_usuario_documento = $data['aceptacion_usuario_documento'];
		$aceptacion_usuario_carnet = $data['aceptacion_usuario_carnet'];
		$aceptacion_usuario_nombre = $data['aceptacion_usuario_nombre'];
		$aceptacion_usuario_apellido = $data['aceptacion_usuario_apellido'];
		$aceptacion_usuario_email = $data['aceptacion_usuario_email'];
		$aceptacion_ip = $data['aceptacion_ip'];
		$aceptacion_user_agent = $data['aceptacion_user_agent'];
		$aceptacion_dispositivo = $data['aceptacion_dispositivo'];
		$aceptacion_navegador = $data['aceptacion_navegador'];
		$aceptacion_fecha = $data['aceptacion_fecha'];
		$aceptacion_datos_adicionales = $data['aceptacion_datos_adicionales'];
		$aceptacion_estado = $data['aceptacion_estado'];
		$aceptacion_tipo = $data['aceptacion_tipo'];
		$query = "UPDATE terminos_aceptaciones SET  aceptacion_reserva_id = '$aceptacion_reserva_id', aceptacion_termino_id = '$aceptacion_termino_id', aceptacion_termino_titulo = '$aceptacion_termino_titulo', aceptacion_usuario_documento = '$aceptacion_usuario_documento', aceptacion_usuario_carnet = '$aceptacion_usuario_carnet', aceptacion_usuario_nombre = '$aceptacion_usuario_nombre', aceptacion_usuario_apellido = '$aceptacion_usuario_apellido', aceptacion_usuario_email = '$aceptacion_usuario_email', aceptacion_ip = '$aceptacion_ip', aceptacion_user_agent = '$aceptacion_user_agent', aceptacion_dispositivo = '$aceptacion_dispositivo', aceptacion_navegador = '$aceptacion_navegador', aceptacion_fecha = '$aceptacion_fecha', aceptacion_datos_adicionales = '$aceptacion_datos_adicionales', aceptacion_estado = '$aceptacion_estado', aceptacion_tipo = '$aceptacion_tipo' WHERE aceptacion_id = '".$id."'";
		$res = $this->_conn->query($query);
	}
}