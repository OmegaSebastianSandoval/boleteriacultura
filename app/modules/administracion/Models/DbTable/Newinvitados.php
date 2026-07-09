<?php 
/**
* clase que genera la insercion y edicion  de newinvitados en la base de datos
*/
class Administracion_Model_DbTable_Newinvitados extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'invitadosreserva';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'id_invitado';

	/**
	 * insert recibe la informacion de un newinvitados y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data){
		$reserva_id_reserva = $data['reserva_id_reserva'];
		$documento_invitado = $data['documento_invitado'];
		$invitadoReserva_nombre_invitado = $data['invitadoReserva_nombre_invitado'];
		$invitadoReserva_apellido_invitado = $data['invitadoReserva_apellido_invitado'];
		$invitadoReserva_correo_invitado = $data['invitadoReserva_correo_invitado'];
		$invitadoReserva_estado_invitado = $data['invitadoReserva_estado_invitado'];
		$invitadoReserva_fecha_nacimiento = $data['invitadoReserva_fecha_nacimiento'];
		$invitadoReserva_telefono = $data['invitadoReserva_telefono'];
		$invitadosReserva_fecha_creacion = date('Y-m-d H:i:s');
		$invitadosReserva_fecha_actualizacion = date('Y-m-d H:i:s');
		$invitadosReserva_usuario_creacion = $_SESSION['kt_login_user'];
		$invitadosReserva_actualizacion = $_SESSION['kt_login_user'];
		$invitado_tipo = $data['invitado_tipo'];
		$invitado_evento = $data['invitado_evento'];
		$invitadoReserva_beneficiario_menor25 = $data['invitadoReserva_beneficiario_menor25'];
		$invitadoReserva_beneficiario_hijo = $data['invitadoReserva_beneficiario_hijo'];
		$invitadoReserva_beneficiario_cupo = $data['invitadoReserva_beneficiario_cupo'];
		$invitadoReserva_beneficiario_principal = $data['invitadoReserva_beneficiario_principal'];
		$invitadoReserva_numero_carnet = $data['invitadoReserva_numero_carnet'];
		$query = "INSERT INTO invitadosreserva( reserva_id_reserva, documento_invitado, invitadoReserva_nombre_invitado, invitadoReserva_apellido_invitado, invitadoReserva_correo_invitado, invitadoReserva_estado_invitado, invitadoReserva_fecha_nacimiento, invitadoReserva_telefono, invitadosReserva_fecha_creacion, invitadosReserva_fecha_actualizacion, invitadosReserva_usuario_creacion, invitadosReserva_actualizacion, invitado_tipo, invitado_evento, invitadoReserva_beneficiario_menor25, invitadoReserva_beneficiario_hijo, invitadoReserva_beneficiario_cupo, invitadoReserva_beneficiario_principal, invitadoReserva_numero_carnet) VALUES ( '$reserva_id_reserva', '$documento_invitado', '$invitadoReserva_nombre_invitado', '$invitadoReserva_apellido_invitado', '$invitadoReserva_correo_invitado', '$invitadoReserva_estado_invitado', '$invitadoReserva_fecha_nacimiento', '$invitadoReserva_telefono', '$invitadosReserva_fecha_creacion', '$invitadosReserva_fecha_actualizacion', '$invitadosReserva_usuario_creacion', '$invitadosReserva_actualizacion', '$invitado_tipo', '$invitado_evento', '$invitadoReserva_beneficiario_menor25', '$invitadoReserva_beneficiario_hijo', '$invitadoReserva_beneficiario_cupo', '$invitadoReserva_beneficiario_principal', '$invitadoReserva_numero_carnet')";
		$res = $this->_conn->query($query);
        return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un newinvitados  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data,$id){
		$invitadosModel = new Administracion_Model_DbTable_Newinvitados();
		$infoanterior = $invitadosModel->getById($id);
		$reserva_id_reserva = $data['reserva_id_reserva'];
		$documento_invitado = $data['documento_invitado'];
		$invitadoReserva_nombre_invitado = $data['invitadoReserva_nombre_invitado'];
		$invitadoReserva_apellido_invitado = $data['invitadoReserva_apellido_invitado'];
		$invitadoReserva_correo_invitado = $data['invitadoReserva_correo_invitado'];
		$invitadoReserva_estado_invitado = $data['invitadoReserva_estado_invitado'];
		$invitadoReserva_fecha_nacimiento = $data['invitadoReserva_fecha_nacimiento'];
		$invitadoReserva_telefono = $data['invitadoReserva_telefono'];
		$invitadosReserva_fecha_creacion = $infoanterior->invitadosReserva_fecha_creacion;
		$invitadosReserva_fecha_actualizacion = date('Y-m-d H:i:s');
		$invitadosReserva_usuario_creacion = $infoanterior->invitadosReserva_usuario_creacion;
		$invitadosReserva_actualizacion = $_SESSION['kt_login_user'];
		$invitado_tipo = $data['invitado_tipo'];
		$invitado_evento = $data['invitado_evento'];
		$invitadoReserva_beneficiario_menor25 = $data['invitadoReserva_beneficiario_menor25'];
		$invitadoReserva_beneficiario_hijo = $data['invitadoReserva_beneficiario_hijo'];
		$invitadoReserva_beneficiario_cupo = $data['invitadoReserva_beneficiario_cupo'];
		$invitadoReserva_beneficiario_principal = $data['invitadoReserva_beneficiario_principal'];
		$invitadoReserva_numero_carnet = $data['invitadoReserva_numero_carnet'];
		$query = "UPDATE invitadosreserva SET  reserva_id_reserva = '$reserva_id_reserva', documento_invitado = '$documento_invitado', invitadoReserva_nombre_invitado = '$invitadoReserva_nombre_invitado', invitadoReserva_apellido_invitado = '$invitadoReserva_apellido_invitado', invitadoReserva_correo_invitado = '$invitadoReserva_correo_invitado', invitadoReserva_estado_invitado = '$invitadoReserva_estado_invitado', invitadoReserva_fecha_nacimiento = '$invitadoReserva_fecha_nacimiento', invitadoReserva_telefono = '$invitadoReserva_telefono', invitadosReserva_fecha_creacion = '$invitadosReserva_fecha_creacion', invitadosReserva_fecha_actualizacion = '$invitadosReserva_fecha_actualizacion', invitadosReserva_usuario_creacion = '$invitadosReserva_usuario_creacion', invitadosReserva_actualizacion = '$invitadosReserva_actualizacion', invitado_tipo = '$invitado_tipo', invitado_evento = '$invitado_evento', invitadoReserva_beneficiario_menor25 = '$invitadoReserva_beneficiario_menor25', invitadoReserva_beneficiario_hijo = '$invitadoReserva_beneficiario_hijo', invitadoReserva_beneficiario_cupo = '$invitadoReserva_beneficiario_cupo', invitadoReserva_beneficiario_principal = '$invitadoReserva_beneficiario_principal', invitadoReserva_numero_carnet = '$invitadoReserva_numero_carnet' WHERE id_invitado = '".$id."'";
		$res = $this->_conn->query($query);
	}
}