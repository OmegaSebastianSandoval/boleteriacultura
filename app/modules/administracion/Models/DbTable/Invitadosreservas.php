<?php

/**
 * clase que genera la insercion y edicion  de invitadosReserva en la base de datos
 */
class Administracion_Model_DbTable_Invitadosreservas extends Db_Table
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
   * insert recibe la informacion de un invitadosReserva y la inserta en la base de datos
   * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
   * @return integer      identificador del  registro que se inserto
   */
  public function insert($data)
  {
    $reserva_id_reserva = $data['reserva_id_reserva'];
    $documento_invitado = $data['documento_invitado'];
    $invitadoReserva_nombre_invitado = $data['invitadoReserva_nombre_invitado'];
    $invitadoReserva_apellido_invitado = $data['invitadoReserva_apellido_invitado'];
    $invitadoReserva_correo_invitado = $data['invitadoReserva_correo_invitado'];
    $invitadoReserva_estado_invitado = $data['invitadoReserva_estado_invitado'];
    $invitadoReserva_fecha_nacimiento = $data['invitadoReserva_fecha_nacimiento'] ?? null;
    $invitadoReserva_telefono = $data['invitadoReserva_telefono'] ?? null;
    $invitadosReserva_fecha_creacion = $data['invitadosReserva_fecha_creacion'] ?? null;
    $invitadosReserva_fecha_actualizacion = $data['invitadosReserva_fecha_actualizacion'] ?? null;
    $invitadosReserva_usuario_creacion = $data['invitadosReserva_usuario_creacion'] ?? null;
    $invitadosReserva_actualizacion = $data['invitadosReserva_actualizacion'] ?? null;
    $invitado_tipo = $data['invitado_tipo'];
    $invitado_evento = $data['invitado_evento'];
    $invitadoReserva_beneficiario_menor25 = $data['invitadoReserva_beneficiario_menor25'] ?? 0;
    $invitadoReserva_beneficiario_hijo = $data['invitadoReserva_beneficiario_hijo'] ?? 0;
    $invitadoReserva_beneficiario_principal = $data['invitadoReserva_beneficiario_principal'] ?? 0;
    $invitadoReserva_beneficiario_cupo = $data['invitadoReserva_beneficiario_cupo'] ?? '';
    $invitadoReserva_numero_carnet = $data['invitadoReserva_numero_carnet'] ?? '';
    $query = "INSERT INTO invitadosreserva( reserva_id_reserva, documento_invitado, invitadoReserva_nombre_invitado,  invitadoReserva_apellido_invitado, invitadoReserva_correo_invitado, invitadoReserva_estado_invitado, invitadoReserva_fecha_nacimiento, invitadoReserva_telefono, invitadosReserva_fecha_creacion, invitadosReserva_fecha_actualizacion, invitadosReserva_usuario_creacion, invitadosReserva_actualizacion, invitado_tipo, invitado_evento,invitadoReserva_beneficiario_menor25, invitadoReserva_beneficiario_hijo, invitadoReserva_beneficiario_principal, invitadoReserva_beneficiario_cupo, invitadoReserva_numero_carnet) VALUES ( '$reserva_id_reserva', '$documento_invitado','$invitadoReserva_nombre_invitado', '$invitadoReserva_apellido_invitado', '$invitadoReserva_correo_invitado', '$invitadoReserva_estado_invitado', '$invitadoReserva_fecha_nacimiento', '$invitadoReserva_telefono', '$invitadosReserva_fecha_creacion', '$invitadosReserva_fecha_actualizacion', '$invitadosReserva_usuario_creacion', '$invitadosReserva_actualizacion', '$invitado_tipo', '$invitado_evento', '$invitadoReserva_beneficiario_menor25', '$invitadoReserva_beneficiario_hijo', '$invitadoReserva_beneficiario_principal', '$invitadoReserva_beneficiario_cupo', '$invitadoReserva_numero_carnet')";

    $res = $this->_conn->query($query);
    return mysqli_insert_id($this->_conn->getConnection());
  }

  /**
   * update Recibe la informacion de un invitadosReserva  y actualiza la informacion en la base de datos
   * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
   * @param  integer    identificador al cual se le va a realizar la actualizacion
   * @return void
   */
  public function update($data, $id)
  {

    $reserva_id_reserva = $data['reserva_id_reserva'];
    $documento_invitado = $data['documento_invitado'];
    $invitadoReserva_nombre_invitado = $data['invitadoReserva_nombre_invitado'];
    $invitadoReserva_apellido_invitado = $data['invitadoReserva_apellido_invitado'];
    $invitadoReserva_correo_invitado = $data['invitadoReserva_correo_invitado'];
    $invitadoReserva_estado_invitado = $data['invitadoReserva_estado_invitado'];
    $invitadoReserva_fecha_nacimiento = $data['invitadoReserva_fecha_nacimiento'];
    $invitadoReserva_telefono = $data['invitadoReserva_telefono'];
    $invitadosReserva_fecha_creacion = $data['invitadosReserva_fecha_creacion'];
    $invitadosReserva_fecha_actualizacion = $data['invitadosReserva_fecha_actualizacion'];
    $invitadosReserva_usuario_creacion = $data['invitadosReserva_usuario_creacion'];
    $invitadosReserva_actualizacion = $data['invitadosReserva_actualizacion'];
    $invitado_tipo = $data['invitado_tipo'];
    $invitado_evento = $data['invitado_evento'];
    $invitadoReserva_beneficiario_menor25 = $data['invitadoReserva_beneficiario_menor25'] ?? 0;
    $invitadoReserva_beneficiario_hijo = $data['invitadoReserva_beneficiario_hijo'] ?? 0;
    $invitadoReserva_beneficiario_principal = $data['invitadoReserva_beneficiario_principal'] ?? 0;
    $invitadoReserva_beneficiario_cupo = $data['invitadoReserva_beneficiario_cupo'] ?? '';
    $invitadoReserva_numero_carnet = $data['invitadoReserva_numero_carnet'] ?? '';
    $query = "UPDATE invitadosreserva SET  reserva_id_reserva = '$reserva_id_reserva',documento_invitado ='$documento_invitado', invitadoReserva_nombre_invitado = '$invitadoReserva_nombre_invitado', invitadoReserva_apellido_invitado = '$invitadoReserva_apellido_invitado', invitadoReserva_correo_invitado = '$invitadoReserva_correo_invitado', invitadoReserva_estado_invitado = '$invitadoReserva_estado_invitado', invitadoReserva_fecha_nacimiento = '$invitadoReserva_fecha_nacimiento', invitadoReserva_telefono = '$invitadoReserva_telefono', invitadosReserva_fecha_creacion = '$invitadosReserva_fecha_creacion', invitadosReserva_fecha_actualizacion = '$invitadosReserva_fecha_actualizacion', invitadosReserva_usuario_creacion = '$invitadosReserva_usuario_creacion', invitadosReserva_actualizacion = '$invitadosReserva_actualizacion', invitado_tipo = '$invitado_tipo', invitado_evento = '$invitado_evento', invitadoReserva_beneficiario_menor25 = '$invitadoReserva_beneficiario_menor25', invitadoReserva_beneficiario_hijo = '$invitadoReserva_beneficiario_hijo', invitadoReserva_beneficiario_principal = '$invitadoReserva_beneficiario_principal', invitadoReserva_beneficiario_cupo = '$invitadoReserva_beneficiario_cupo', invitadoReserva_numero_carnet = '$invitadoReserva_numero_carnet' WHERE id_invitado = '" . $id . "'";
    $res = $this->_conn->query($query);
  }

  /**
   * Actualiza solo los campos editables de un invitado
   */
  public function updateCamposEditables($data, $id)
  {
    $nombre = $data['invitadoReserva_nombre_invitado'];
    $apellido = $data['invitadoReserva_apellido_invitado'];
    $documento = $data['documento_invitado'];
    $correo = $data['invitadoReserva_correo_invitado'];
    $fecha_nacimiento = $data['invitadoReserva_fecha_nacimiento'];
    $telefono = $data['invitadoReserva_telefono'];
    $menu = $data['invitadoReserva_menu'] ?? 'normal';
    $query = "UPDATE invitadosreserva SET invitadoReserva_nombre_invitado = '$nombre', invitadoReserva_apellido_invitado = '$apellido', documento_invitado = '$documento', invitadoReserva_correo_invitado = '$correo', invitadoReserva_fecha_nacimiento = '$fecha_nacimiento', invitadoReserva_telefono = '$telefono', invitadoReserva_menu = '$menu' WHERE
id_invitado = '" . $id . "'";
    $this->_conn->query($query);
  }

  public function getListWithReserva($filters = '', $order = '')
  {
    $filter = '';
    if ($filters != '') {
      $filter = ' WHERE ' . $filters;
    }
    $orders = "";
    if ($order != '') {
      $orders = ' ORDER BY ' . $order;
    }
    $select = 'SELECT * FROM ' . $this->_name . ' LEFT JOIN reservas ON ' . $this->_name . '.reserva_id_reserva = reservas.id ' . $filter . ' ' . $orders;
    $res = $this->_conn->query($select)->fetchAsObject();
    return $res;
  }

  public function deleteInvitados($id)
  {
    $update = "DELETE FROM " . $this->_name . " WHERE reserva_id_reserva = '" . $id . "'";
    $this->_conn->query($update);
  }

  /**
   * Retorna los documentos que ya están en una reserva activa o pendiente de confirmación
   * (estados 2, 3, 11 = confirmadas/pagadas; 4, 7 = pago pendiente Placetopay/sistema)
   * @param array $documentos Lista de números de documento a verificar
   * @return array Documentos bloqueados (ya en reserva activa o pendiente)
   */
  public function getDocumentosEnReservasActivas(array $documentos)
  {
    if (empty($documentos)) return [];

    $conn = $this->_conn->getConnection();
    $escaped = array_map(function($d) use ($conn) {
      return "'" . $conn->real_escape_string(trim($d)) . "'";
    }, $documentos);

    $inList = implode(',', $escaped);

    $query = "SELECT DISTINCT i.documento_invitado
              FROM invitadosreserva i
              INNER JOIN reservas r ON i.reserva_id_reserva = r.id
              WHERE i.documento_invitado IN ($inList)
              AND r.reserva_estado IN (2, 3, 4, 7, 11)";

    $res = $this->_conn->query($query)->fetchAsObject();
    return array_column($res, 'documento_invitado');
  }
  public function getMenores()
  {
    $query = "SELECT 
    i.*, 
    r.reserva_metodo_pago
    FROM invitadosreserva i
    INNER JOIN reservas r 
        ON i.reserva_id_reserva = r.id
    WHERE i.invitadoReserva_estado_invitado LIKE '%S%'
      AND i.invitadoReserva_beneficiario_menor25 = 1
     ORDER BY r.reserva_metodo_pago ASC;
    ";
    $res = $this->_conn->query($query)->fetchAsObject();
    return $res;
  }
}
