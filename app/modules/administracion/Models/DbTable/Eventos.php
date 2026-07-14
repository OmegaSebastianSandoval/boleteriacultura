<?php

/**
 * clase que genera la insercion y edicion  de eventos en la base de datos
 */
class Administracion_Model_DbTable_Eventos extends Db_Table
{
  /**
   * [ nombre de la tabla actual]
   * @var string
   */
  protected $_name = 'eventos';

  /**
   * [ identificador de la tabla actual en la base de datos]
   * @var string
   */
  protected $_id = 'evento_id';

  /**
   * insert recibe la informacion de un eventos y la inserta en la base de datos
   * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
   * @return integer      identificador del  registro que se inserto
   */
  public function insert($data)
  {
    $evento_titulo = $data['evento_titulo'];
    $evento_fecha_auditoria = $data['evento_fecha_auditoria'];
    $evento_fecha_apertura_reserva = $data['evento_fecha_apertura_reserva'];
    $evento_fecha_cierre_reserva = $data['evento_fecha_cierre_reserva'];
    $evento_descripcion = $data['evento_descripcion'];
    $evento_asignacion_lugar = $data['evento_asignacion_lugar'];
    $evento_lugar = $data['evento_lugar'];
    $evento_politica_reserva = $data['evento_politica_reserva'];
    $evento_acomodacion = $data['evento_acomodacion'];
    $orden = $data['orden'];
    $evento_fecha_creacion = $data['evento_fecha_creacion'];
    $evento_fecha_actualizacion = $data['evento_fecha_actualizacion'];
    $evento_usuario_creacion = $data['evento_usuario_creacion'];
    $evento_usuario_actualizacion = $data['evento_usuario_actualizacion'];
    $evento_cupo_maximo = $data['evento_cupo_maximo'];
    $evento_invitados_socio = $data['evento_invitados_socio'];
    $evento_imagen_evento = $data['evento_imagen_evento'];
    $evento_fecha_inicio = $data['evento_fecha_inicio'];
    $evento_fecha_fin = $data['evento_fecha_fin'];
    $evento_dia_semana = $data['evento_dia_semana'];
    $evento_fecha = $data['evento_fecha'];
    $evento_imagenfondo = $data['evento_imagenfondo'];
    $evento_colorfondo = $data['evento_colorfondo'];

    $evento_max_cuotas = $data['evento_max_cuotas'];
    $evento_datafono = $data['evento_datafono'];
    $evento_cuotas = $data['evento_cuotas'];

    $evento_imagenfondo_home = $data['evento_imagenfondo_home'];
    $evento_imagenfondo_login = $data['evento_imagenfondo_login'];

    $evento_imagenfondo_home_responsive = $data['evento_imagenfondo_home_responsive'];
    $evento_imagenfondo_login_responsive = $data['evento_imagenfondo_login_responsive'];

    $evento_menu_habilitado = $data['evento_menu_habilitado'];
    $evento_invitados_permitidos = $data['evento_invitados_permitidos'];


    $query = "INSERT INTO eventos( evento_titulo, evento_fecha_auditoria, evento_fecha_apertura_reserva, evento_fecha_cierre_reserva, evento_descripcion, evento_asignacion_lugar, evento_lugar, evento_politica_reserva, evento_acomodacion, orden, evento_fecha_creacion, evento_fecha_actualizacion, evento_usuario_creacion, evento_usuario_actualizacion, evento_cupo_maximo, evento_invitados_socio, evento_imagen_evento, evento_fecha_inicio, evento_fecha_fin, evento_dia_semana, evento_fecha, evento_imagenfondo, evento_colorfondo, evento_max_cuotas, evento_datafono, evento_cuotas, evento_imagenfondo_home, evento_imagenfondo_login, evento_imagenfondo_home_responsive, evento_imagenfondo_login_responsive, evento_menu_habilitado, evento_invitados_permitidos) VALUES ( '$evento_titulo', '$evento_fecha_auditoria', '$evento_fecha_apertura_reserva', '$evento_fecha_cierre_reserva', '$evento_descripcion', '$evento_asignacion_lugar', '$evento_lugar', '$evento_politica_reserva', '$evento_acomodacion', '$orden', '$evento_fecha_creacion', '$evento_fecha_actualizacion', '$evento_usuario_creacion', '$evento_usuario_actualizacion', '$evento_cupo_maximo', '$evento_invitados_socio', '$evento_imagen_evento', '$evento_fecha_inicio', '$evento_fecha_fin', '$evento_dia_semana', '$evento_fecha', '$evento_imagenfondo', '$evento_colorfondo', '$evento_max_cuotas', '$evento_datafono', '$evento_cuotas', '$evento_imagenfondo_home', '$evento_imagenfondo_login', '$evento_imagenfondo_home_responsive', '$evento_imagenfondo_login_responsive', '$evento_menu_habilitado', '$evento_invitados_permitidos')";
    $res = $this->_conn->query($query);
    return mysqli_insert_id($this->_conn->getConnection());
  }

  /**
   * update Recibe la informacion de un eventos  y actualiza la informacion en la base de datos
   * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
   * @param  integer    identificador al cual se le va a realizar la actualizacion
   * @return void
   */
  public function update($data, $id)
  {

    $evento_titulo = $data['evento_titulo'];
    $evento_fecha_auditoria = $data['evento_fecha_auditoria'];
    $evento_fecha_apertura_reserva = $data['evento_fecha_apertura_reserva'];
    $evento_fecha_cierre_reserva = $data['evento_fecha_cierre_reserva'];
    $evento_descripcion = $data['evento_descripcion'];
    $evento_asignacion_lugar = $data['evento_asignacion_lugar'];
    $evento_lugar = $data['evento_lugar'];
    $evento_politica_reserva = $data['evento_politica_reserva'];
    $evento_acomodacion = $data['evento_acomodacion'];
    $orden = $data['orden'];
    $evento_fecha_creacion = $data['evento_fecha_creacion'];
    $evento_fecha_actualizacion = $data['evento_fecha_actualizacion'];
    $evento_usuario_creacion = $data['evento_usuario_creacion'];
    $evento_usuario_actualizacion = $data['evento_usuario_actualizacion'];
    $evento_cupo_maximo = $data['evento_cupo_maximo'];
    $evento_invitados_socio = $data['evento_invitados_socio'];
    $evento_imagen_evento = $data['evento_imagen_evento'];
    $evento_fecha_inicio = $data['evento_fecha_inicio'];
    $evento_fecha_fin = $data['evento_fecha_fin'];
    $evento_dia_semana = $data['evento_dia_semana'];
    $evento_fecha = $data['evento_fecha'];
    $evento_imagenfondo = $data['evento_imagenfondo'];
    $evento_colorfondo = $data['evento_colorfondo'];

    $evento_max_cuotas = $data['evento_max_cuotas'];
    $evento_datafono = $data['evento_datafono'];
    $evento_cuotas = $data['evento_cuotas'];

    $evento_imagenfondo_home = $data['evento_imagenfondo_home'];
    $evento_imagenfondo_login = $data['evento_imagenfondo_login'];

    $evento_imagenfondo_home_responsive = $data['evento_imagenfondo_home_responsive'];
    $evento_imagenfondo_login_responsive = $data['evento_imagenfondo_login_responsive'];

    $evento_menu_habilitado = $data['evento_menu_habilitado'];
    $evento_invitados_permitidos = $data['evento_invitados_permitidos'];

    $query = "UPDATE eventos SET  evento_titulo = '$evento_titulo', evento_fecha_auditoria = '$evento_fecha_auditoria', evento_fecha_apertura_reserva = '$evento_fecha_apertura_reserva', evento_fecha_cierre_reserva = '$evento_fecha_cierre_reserva', evento_descripcion = '$evento_descripcion', evento_asignacion_lugar = '$evento_asignacion_lugar', evento_lugar = '$evento_lugar', evento_politica_reserva = '$evento_politica_reserva', evento_acomodacion = '$evento_acomodacion', orden = '$orden', evento_fecha_creacion = '$evento_fecha_creacion', evento_fecha_actualizacion = '$evento_fecha_actualizacion', evento_usuario_creacion = '$evento_usuario_creacion', evento_usuario_actualizacion = '$evento_usuario_actualizacion', evento_cupo_maximo = '$evento_cupo_maximo', evento_invitados_socio = '$evento_invitados_socio', evento_imagen_evento = '$evento_imagen_evento', evento_fecha_inicio = '$evento_fecha_inicio', evento_fecha_fin = '$evento_fecha_fin', evento_dia_semana = '$evento_dia_semana', evento_fecha = '$evento_fecha', evento_imagenfondo = '$evento_imagenfondo', evento_colorfondo = '$evento_colorfondo', evento_max_cuotas = '$evento_max_cuotas', evento_datafono = '$evento_datafono', evento_cuotas = '$evento_cuotas', evento_imagenfondo_home = '$evento_imagenfondo_home', evento_imagenfondo_login = '$evento_imagenfondo_login', evento_imagenfondo_home_responsive = '$evento_imagenfondo_home_responsive', evento_imagenfondo_login_responsive = '$evento_imagenfondo_login_responsive', evento_menu_habilitado = '$evento_menu_habilitado', evento_invitados_permitidos = '$evento_invitados_permitidos' WHERE evento_id = '$id'";
    $res = $this->_conn->query($query);
  }



  public function getAllEventos() {}
  public function getEventosCategorias($filters = '', $order = '')
  {
    $filter = '';
    if ($filters != '') {
      $filter = ' WHERE ' . $filters;
    }
    $orders = "";
    if ($order != '') {
      $orders = ' ORDER BY ' . $order;
    }
    $select = 'SELECT eventos.* FROM eventos' . $filter . ' ' . $orders;
    $res = $this->_conn->query($select)->fetchAsObject();
    return $res;
  }
  public function getEventosCategoriasId($id)
  {
    $res = $this->_conn->query('SELECT eventos.* FROM eventos WHERE ' . $this->_id . ' = "' . $id . '"')->fetchAsObject();
    if (isset($res[0])) {
      return $res[0];
    }
    return false;
  }
}
