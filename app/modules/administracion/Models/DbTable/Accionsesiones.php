<?php 
/**
* clase que genera la insercion y edicion  de accionsesion en la base de datos
*/
class Administracion_Model_DbTable_Accionsesiones extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'accion_sesiones';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'accion_sesion_id';

	/**
	 * insert recibe la informacion de un accionsesion y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data){
		$accion_sesion_accion_numero = $data['accion_sesion_accion_numero'];
		$accion_sesion_documento_socio = $data['accion_sesion_documento_socio'];
		$accion_sesion_fecha_inicio = $data['accion_sesion_fecha_inicio'];
		$accion_sesion_fecha_fin = $data['accion_sesion_fecha_fin'] ?? null;
		$accion_sesion_sesion_activa = $data['accion_sesion_sesion_activa'];
		$accion_sesion_ip_usuario = $data['accion_sesion_ip_usuario'];
		$accion_sesion_user_agent = $data['accion_sesion_user_agent'];
		$accion_sesion_dispositivo = $data['accion_sesion_dispositivo'];
		$accion_sesion_last_ping = $data['accion_sesion_last_ping'] ?? null;
		$fechaFinSql = $accion_sesion_fecha_fin !== null ? "'$accion_sesion_fecha_fin'" : 'NULL';
		$lastPingSql = $accion_sesion_last_ping !== null ? "'$accion_sesion_last_ping'" : 'NULL';
		$query = "INSERT INTO accion_sesiones( accion_sesion_accion_numero, accion_sesion_documento_socio, accion_sesion_fecha_inicio, accion_sesion_fecha_fin, accion_sesion_sesion_activa, accion_sesion_ip_usuario, accion_sesion_user_agent, accion_sesion_dispositivo, accion_sesion_last_ping) VALUES ( '$accion_sesion_accion_numero', '$accion_sesion_documento_socio', '$accion_sesion_fecha_inicio', $fechaFinSql, '$accion_sesion_sesion_activa', '$accion_sesion_ip_usuario', '$accion_sesion_user_agent', '$accion_sesion_dispositivo', $lastPingSql)";
		$res = $this->_conn->query($query);
        return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un accionsesion  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data,$id){
		
		$accion_sesion_accion_numero = $data['accion_sesion_accion_numero'];
		$accion_sesion_documento_socio = $data['accion_sesion_documento_socio'];
		$accion_sesion_fecha_inicio = $data['accion_sesion_fecha_inicio'];
		$accion_sesion_fecha_fin = $data['accion_sesion_fecha_fin'];
		$accion_sesion_sesion_activa = $data['accion_sesion_sesion_activa'];
		$accion_sesion_ip_usuario = $data['accion_sesion_ip_usuario'];
		$accion_sesion_user_agent = $data['accion_sesion_user_agent'];
		$accion_sesion_dispositivo = $data['accion_sesion_dispositivo'];
		$query = "UPDATE accion_sesiones SET  accion_sesion_accion_numero = '$accion_sesion_accion_numero', accion_sesion_documento_socio = '$accion_sesion_documento_socio', accion_sesion_fecha_inicio = '$accion_sesion_fecha_inicio', accion_sesion_fecha_fin = '$accion_sesion_fecha_fin', accion_sesion_sesion_activa = '$accion_sesion_sesion_activa', accion_sesion_ip_usuario = '$accion_sesion_ip_usuario', accion_sesion_user_agent = '$accion_sesion_user_agent', accion_sesion_dispositivo = '$accion_sesion_dispositivo' WHERE accion_sesion_id = '".$id."'";
		$res = $this->_conn->query($query);
	}
}