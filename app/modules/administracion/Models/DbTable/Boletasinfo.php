<?php

/**
 * clase que genera la insercion y edicion  de boleta en la base de datos
 */
class MYPDFNEWINFO extends TCPDF
{

	//Page header
	//Page header
	public function Header()
	{
		$boletasDisenos = new Administracion_Model_DbTable_Boletasestilos();
		$disenoActivo = $boletasDisenos->getList(" boletas_estilo_estado = 1 ")[0];
		// $img_file = PUBLIC_PATH . '/corte/Fondo.jpg';
		$w = $this->getPageWidth();
		$h = $this->getPageHeight();

		// // resize = true (último parámetro en 1) para forzar que llene toda la página
		// $this->Image($img_file, 0, 0, $w, $h, '', '', '', false, 300, '', false, false, 0);

		// get the current page break margin
		$bMargin = $this->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $this->AutoPageBreak;
		// disable auto-page-break
		$this->SetAutoPageBreak(false, 0);
		// set bacground image (si no hay un estilo activo configurado, se omite el fondo
		// en vez de generar un Warning por acceder a una propiedad de null)
		if ($disenoActivo) {
			$img_file = IMAGE_PATH . $disenoActivo->boletas_estilo_fondo;
			$this->Image($img_file, 0, 0, $w, $h, '', '', '', false, 300, '', false, false, 0);
		}
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$this->setPageMark();
	}
	public function Footer()
	{
		$boletasDisenos = new Administracion_Model_DbTable_Boletasestilos();
		$disenoActivo = $boletasDisenos->getList(" boletas_estilo_estado = 1 ")[0];
		// Position at 15 mm from bottom
		$this->SetY(-12);
		// Set font
		$this->SetFont('helvetica', 'x', 10);
		$this->SetTextColor(255, 255, 255);
		// Page number
		$this->Cell(0, 10, $disenoActivo->boletas_estilo_textofooter ?? '', 0, false, 'C', 0, '', 0, false, 'T', 'M');
	}
}

class Administracion_Model_DbTable_Boletasinfo extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'boletas_info';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'boleta_id';

	/**
	 * insert recibe la informacion de un boleta y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data)
	{
		$boleta_reserva_id = $data['boleta_reserva_id'];
		$boleta_evento_id = $data['boleta_evento_id'];
		$boleta_numero_ticket = $data['boleta_numero_ticket'];
		$boleta_uid = $data['boleta_uid'] ?? '';
		$boleta_token = $data['boleta_token'] ?? '';
		$boleta_estado = $data['boleta_estado'];
		$boleta_fecha_creacion = $data['boleta_fecha_creacion'];
		$boleta_fecha_validacion = $data['boleta_fecha_validacion'] ?? null;
		$boleta_metodo_validacion = $data['boleta_metodo_validacion'] ?? null;
		$boleta_dispositivo_validacion = $data['boleta_dispositivo_validacion'] ?? null;
		$boleta_ip_validacion = $data['boleta_ip_validacion'] ?? null;
		$boleta_fecha_expiracion = $data['boleta_fecha_expiracion'] ?? null;
		$boleta_observaciones = $data['boleta_observaciones'] ?? null;
		$boleta_usuario_validador = $data['boleta_usuario_validador'] ?? null;
		$boleta_documento = $data['boleta_documento'] ?? null;
		$boleta_tipo_boleta = $data['boleta_tipo_boleta'] ?? null;
		$boleta_asignacion = $data['boleta_asignacion'] ?? null;

		$query = "INSERT INTO boletas_info(
			boleta_reserva_id, boleta_evento_id, boleta_numero_ticket, boleta_uid, boleta_token,
			boleta_estado, boleta_fecha_creacion, boleta_fecha_validacion, boleta_metodo_validacion,
			boleta_dispositivo_validacion, boleta_ip_validacion, boleta_fecha_expiracion,
			boleta_observaciones, boleta_usuario_validador, boleta_documento, boleta_tipo_boleta, boleta_asignacion
		) VALUES (
			'$boleta_reserva_id', '$boleta_evento_id', '$boleta_numero_ticket', '$boleta_uid', '$boleta_token',
			'$boleta_estado', '$boleta_fecha_creacion', " . ($boleta_fecha_validacion ? "'$boleta_fecha_validacion'" : "NULL") . ",
			" . ($boleta_metodo_validacion ? "'$boleta_metodo_validacion'" : "NULL") . ",
			" . ($boleta_dispositivo_validacion ? "'$boleta_dispositivo_validacion'" : "NULL") . ",
			" . ($boleta_ip_validacion ? "'$boleta_ip_validacion'" : "NULL") . ",
			" . ($boleta_fecha_expiracion ? "'$boleta_fecha_expiracion'" : "NULL") . ",
			" . ($boleta_observaciones ? "'$boleta_observaciones'" : "NULL") . ",
			" . ($boleta_usuario_validador ? "'$boleta_usuario_validador'" : "NULL") . ",
			" . ($boleta_documento ? "'$boleta_documento'" : "NULL") . ",
			" . ($boleta_tipo_boleta ? "'$boleta_tipo_boleta'" : "NULL") . ",
			" . ($boleta_asignacion ? "'$boleta_asignacion'" : "NULL") . "
		)";

		$res = $this->_conn->query($query);
		return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un boleta  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data, $id)
	{

		$boleta_reserva_id = $data['boleta_reserva_id'];
		$boleta_evento_id = $data['boleta_evento_id'];
		$boleta_numero_ticket = $data['boleta_numero_ticket'];
		$boleta_uid = $data['boleta_uid'];
		$boleta_token = $data['boleta_token'];
		$boleta_estado = $data['boleta_estado'];
		$boleta_fecha_creacion = $data['boleta_fecha_creacion'];
		$boleta_fecha_validacion = $data['boleta_fecha_validacion'];
		$boleta_metodo_validacion = $data['boleta_metodo_validacion'];
		$boleta_dispositivo_validacion = $data['boleta_dispositivo_validacion'];
		$boleta_ip_validacion = $data['boleta_ip_validacion'];
		$boleta_fecha_expiracion = $data['boleta_fecha_expiracion'];
		$boleta_observaciones = $data['boleta_observaciones'];
		$boleta_usuario_validador = $data['boleta_usuario_validador'];
		$boleta_documento = $data['boleta_documento'] ?? null;
		$boleta_tipo_boleta = $data['boleta_tipo_boleta'] ?? null;
		$boleta_asignacion = $data['boleta_asignacion'];
		$query = "UPDATE boletas_info SET  boleta_reserva_id = '$boleta_reserva_id', boleta_evento_id = '$boleta_evento_id', boleta_numero_ticket = '$boleta_numero_ticket', boleta_uid = '$boleta_uid', boleta_token = '$boleta_token', boleta_estado = '$boleta_estado', boleta_fecha_creacion = '$boleta_fecha_creacion', boleta_fecha_validacion = '$boleta_fecha_validacion', boleta_metodo_validacion = '$boleta_metodo_validacion', boleta_dispositivo_validacion = '$boleta_dispositivo_validacion', boleta_ip_validacion = '$boleta_ip_validacion', boleta_fecha_expiracion = '$boleta_fecha_expiracion', boleta_observaciones = '$boleta_observaciones', boleta_usuario_validador = '$boleta_usuario_validador', boleta_documento = '$boleta_documento', boleta_tipo_boleta = '$boleta_tipo_boleta', boleta_asignacion = '$boleta_asignacion' WHERE boleta_id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}
	public function getEstadisticasEvento1()
	{
		// Se parte de "reservas" (no de "boletas_info") y con el mismo filtro de estado
		// que usa la tabla de abajo (getReservasConBoletas), para que "Total reservas"
		// cuente TODAS las reservas aprobadas -tengan o no boleta generada todavía-, no
		// solo las que ya tienen al menos una (antes usaba INNER JOIN y las excluía).
		// "Total boletas"/"Validadas" solo cuentan boletas no anuladas (estado 1 o 2),
		// igual que "Generadas" en getReservasConBoletas, para que ambos números coordinen.
		$query = "SELECT
        COUNT(CASE WHEN b.boleta_estado IN (1,2) THEN 1 END) AS total_boletas,
        COUNT(CASE WHEN b.boleta_estado = 2 THEN 1 END) AS boletas_validadas,
        COUNT(CASE WHEN b.boleta_estado = 1 THEN 1 END) AS boletas_sin_validar,
        COUNT(DISTINCT r.id) AS total_reservas
    FROM reservas r
    LEFT JOIN boletas_info b ON b.boleta_reserva_id = r.id AND b.boleta_evento_id = 1
    WHERE r.reserva_estado IN (2,3,11)";
		$result = $this->_conn->query($query);
		return $result->fetchAsObject()[0];
	}
	public function getTotalReservasConBoletas($filters = "")
	{
		$query = "SELECT COUNT(DISTINCT r.id) as total
	       FROM reservas r
	       LEFT JOIN boletas_info b ON r.id = b.boleta_reserva_id AND b.boleta_evento_id = 1
	       WHERE r.reserva_estado IN (2,3,11)";

		if ($filters && $filters != " 1 = 1 ") {
			$query .= " AND " . $filters;
		}

		$result = $this->_conn->query($query);
		return $result->fetchAsObject()[0]->total;
	}

	public function getReservasConBoletas($filters = "", $order = "", $start = 0, $amount = 20)
	{
		// invitados_sin_boleta: con el envío 1 por 1 ya no todos los invitados tienen
		// boleta al mismo tiempo, así que "pendientes" ahora significa invitados de la
		// reserva a los que aún les falta que se les genere/envíe la boleta (no boletas
		// generadas sin validar en la puerta, que es un concepto distinto).
		$query = "SELECT
	       r.*,
	       COUNT(CASE WHEN b.boleta_estado = 2 THEN 1 END) as boletas_validadas,
				 COUNT(CASE WHEN b.boleta_estado IN (1,2) THEN 1 END) AS total_boletas,
				 (
				   SELECT COUNT(*) FROM invitadosreserva i
				   WHERE i.reserva_id_reserva = r.id
				     AND NOT EXISTS (
				       SELECT 1 FROM boletas_info bi
				       WHERE bi.boleta_asignacion = i.id_invitado AND bi.boleta_estado IN (1, 2)
				     )
				 ) AS invitados_sin_boleta
	       FROM reservas r
	       LEFT JOIN boletas_info b ON r.id = b.boleta_reserva_id AND b.boleta_evento_id = 1
	       WHERE r.reserva_estado IN (2,3,11)";

		if ($filters && $filters != " 1 = 1 ") {
			$query .= " AND " . $filters;
		}

		$query .= " GROUP BY r.id";

		if ($order) {
			$query .= " ORDER BY " . $order;
		} else {
			$query .= " ORDER BY r.id DESC";
		}

		$query .= " LIMIT $start, $amount";
		// echo $query;

		$result = $this->_conn->query($query);
		if (method_exists($result, 'fetchAsObject')) {
			return $result->fetchAsObject();
		} else {
			return [];
		}
	}
	public function getNextBoletaId()
	{
		$sql = "SELECT COALESCE(MAX(boleta_id), 0) + 1 AS next_id FROM boletas_info";
		$result = $this->_conn->query($sql)->fetchAsObject();
		return $result[0]->next_id;
	}

	public function updateGeneratedQR($id, $customUid, $token, $eventoId = 1, $asignacion, $mesaId)
	{
		$query = "UPDATE boletas_info SET boleta_uid = '$customUid', boleta_token = '$token', boleta_asignacion = '$asignacion', boleta_mesa = '$mesaId' WHERE boleta_id = '$id'";
		$res = $this->_conn->query($query);

		return $res;
	}

	public function getById($id)
	{
		$query = "SELECT * FROM boletas_info WHERE boleta_id = '$id'";
		$result = $this->_conn->query($query);
		if ($result) {
			$rows = $result->fetchAsObject();
			return $rows[0] ?? null;
		}
		return null;
	}

	public function getList($where = "", $order = "")
	{
		$query = "SELECT * FROM boletas_info";
		if ($where) {
			$query .= " WHERE " . $where;
		}
		if ($order) {
			$query .= " ORDER BY " . $order;
		}
		$result = $this->_conn->query($query);
		if ($result) {
			return $result->fetchAsObject();
		}
		return [];
	}

	public function getBoletasByReserva($reservaId)
	{
		$query = "SELECT * FROM boletas_info WHERE boleta_reserva_id = '$reservaId' ORDER BY boleta_estado ASC";
		$result = $this->_conn->query($query);
		if ($result) {
			return $result->fetchAsObject();
		}
		return [];
	}

	/**
	 * deleteByReserva elimina todas las boletas asociadas a una reserva
	 * (usado al eliminar una reserva por completo)
	 * @param  integer $reservaId identificador de la reserva
	 * @return void
	 */
	public function deleteByReserva($reservaId)
	{
		$reservaId = intval($reservaId);
		$query = "DELETE FROM boletas_info WHERE boleta_reserva_id = '$reservaId'";
		$this->_conn->query($query);
	}

	public function getReservaById($reservaId)
	{
		$query = "SELECT r.*, COUNT(b.boleta_id) as total_boletas
		          FROM reservas r
		          LEFT JOIN boletas_info b ON r.id = b.boleta_reserva_id
		          WHERE r.id = '$reservaId'
		          GROUP BY r.id";
		$result = $this->_conn->query($query);
		if ($result) {
			$rows = $result->fetchAsObject();
			return $rows[0] ?? null;
		}
		return null;
	}
	public function getBoletasConInconsistencia()
	{
		$query = "
		SELECT 
    b.boleta_id,
    b.boleta_reserva_id,
    b.boleta_evento_id,
    b.boleta_numero_ticket,
    b.boleta_uid,
    b.boleta_token,
    b.boleta_estado,
    b.boleta_fecha_creacion,
    b.boleta_documento AS documento_boleta,
    i.id_invitado,
    i.documento_invitado,
    i.invitadoReserva_nombre_invitado,
		i.invitadoReserva_apellido_invitado,
    i.invitadoReserva_correo_invitado,
    i.invitadoReserva_estado_invitado,
    i.invitado_evento,
    1 AS documento_diferente
FROM boletas_info b
INNER JOIN invitadosreserva i 
    ON b.boleta_asignacion = i.id_invitado
WHERE b.boleta_documento IS NOT NULL
  AND i.documento_invitado IS NOT NULL
  AND b.boleta_documento <> i.documento_invitado
  AND b.boleta_estado IN (1, 2) 
ORDER BY `b`.`boleta_reserva_id` ASC
		
		";
		$result = $this->_conn->query($query);
		if ($result) {
			return $result->fetchAsObject();
		}
		return [];
	}


	/**
	 * Obtiene boletas por número de documento
	 * @param string $documento Número de documento a buscar
	 * @return array Array de objetos boleta
	 */
	public function getBoletasByDocumento($documento)
	{
		$documento = trim($documento);
		$query = "SELECT b.*, r.reserva_nombre_cliente, r.reserva_apellido_cliente, r.reserva_correo
		          FROM boletas_info b
		          LEFT JOIN reservas r ON b.boleta_reserva_id = r.id
		          WHERE b.boleta_documento = '$documento'
		          ORDER BY b.boleta_fecha_creacion DESC";

		$result = $this->_conn->query($query);
		if ($result) {
			return $result->fetchAsObject();
		}
		return [];
	}
}
