<?php
/**
 * clase que genera la insercion y edicion  de beneficiariosbloqueo en la base de datos
 */
class Administracion_Model_DbTable_Beneficiariosbloqueos extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'beneficiario_bloqueos';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'beneficiario_bloqueo_id';

	/**
	 * insert recibe la informacion de un beneficiariosbloqueo y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data)
	{
		$beneficiario_bloqueodocumento = $data['beneficiario_bloqueodocumento'];
		$beneficiario_bloqueo_por_asociado_documento = $data['beneficiario_bloqueo_por_asociado_documento'];
		$beneficiario_bloqueo_fecha_bloqueo = $data['beneficiario_bloqueo_fecha_bloqueo'];
		$beneficiario_bloqueo_expiracion = $data['beneficiario_bloqueo_expiracion'];
		$beneficiario_bloqueo_estado = $data['beneficiario_bloqueo_estado'];
		$beneficiario_bloqueo_macnume = $data['beneficiario_bloqueo_macnume'] ?? null; // Nuevo campo opcional
		$query = "INSERT INTO beneficiario_bloqueos( beneficiario_bloqueodocumento, beneficiario_bloqueo_por_asociado_documento, beneficiario_bloqueo_fecha_bloqueo, beneficiario_bloqueo_expiracion, beneficiario_bloqueo_estado, beneficiario_bloqueo_macnume) VALUES ( '$beneficiario_bloqueodocumento', '$beneficiario_bloqueo_por_asociado_documento', '$beneficiario_bloqueo_fecha_bloqueo', '$beneficiario_bloqueo_expiracion', '$beneficiario_bloqueo_estado', '$beneficiario_bloqueo_macnume')";
		$res = $this->_conn->query($query);
		return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un beneficiariosbloqueo  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data, $id)
	{

		$beneficiario_bloqueodocumento = $data['beneficiario_bloqueodocumento'];
		$beneficiario_bloqueo_por_asociado_documento = $data['beneficiario_bloqueo_por_asociado_documento'];
		$beneficiario_bloqueo_fecha_bloqueo = $data['beneficiario_bloqueo_fecha_bloqueo'];

		$beneficiario_bloqueo_expiracion = $data['beneficiario_bloqueo_expiracion'];
		$beneficiario_bloqueo_estado = $data['beneficiario_bloqueo_estado'];
		$beneficiario_bloqueo_macnume = $data['beneficiario_bloqueo_macnume'] ?? null; // Nuevo campo opcional
		$query = "UPDATE beneficiario_bloqueos SET  beneficiario_bloqueodocumento = '$beneficiario_bloqueodocumento', beneficiario_bloqueo_por_asociado_documento = '$beneficiario_bloqueo_por_asociado_documento', beneficiario_bloqueo_fecha_bloqueo = '$beneficiario_bloqueo_fecha_bloqueo', beneficiario_bloqueo_expiracion = '$beneficiario_bloqueo_expiracion', beneficiario_bloqueo_estado = '$beneficiario_bloqueo_estado', beneficiario_bloqueo_macnume = '$beneficiario_bloqueo_macnume' WHERE beneficiario_bloqueo_id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}

	public function inactivarBeneficiariosBloqueados($documento)
	{
		if (!$documento) {
			return;
		}
		$query = "DELETE FROM beneficiario_bloqueos
		WHERE beneficiario_bloqueo_por_asociado_documento = '$documento'
			AND beneficiario_bloqueo_estado = 1;
		";
		$res = $this->_conn->query($query);
	}

	/**
	 * liberarPorDocumentos elimina los bloqueos (como bloqueado o como quien bloquea)
	 * asociados a un conjunto de documentos (usado al eliminar una reserva por completo)
	 * @param  array $documentos documentos de los invitados/beneficiarios a liberar
	 * @return void
	 */
	public function liberarPorDocumentos(array $documentos = [])
	{
		$documentos = array_filter(array_map('trim', $documentos));
		if (empty($documentos)) {
			return;
		}
		$documentosEsc = array_map(function ($doc) {
			return "'" . addslashes($doc) . "'";
		}, $documentos);
		$in = implode(',', $documentosEsc);
		$query = "DELETE FROM beneficiario_bloqueos
		WHERE beneficiario_bloqueodocumento IN ($in)
			OR beneficiario_bloqueo_por_asociado_documento IN ($in)";
		$this->_conn->query($query);
	}
}