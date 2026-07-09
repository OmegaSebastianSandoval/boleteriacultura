<?php

class Page_reservasController extends Page_mainController
{
  public function insertReservaAction()
  {
    $data = $this->getData();
    $reservaModel = new Administracion_Model_DbTable_Reservas();

    $id = $reservaModel->insert($data);

    $response = array();

    if ($id) {

      $data['id'] = $id;
      $data['log_log'] = print_r($data, true);
      $data['log_tipo'] = 'CREAR RESERVAS';

      $logModel = new Administracion_Model_DbTable_Log();
      $logModel->insert($data);

      $response['message'] = 'Reserva creada con éxito';
    } else {

      $response['message'] = 'Error al crear la reserva';
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
  }


  private function getData()
  {
    $data = array();
    $data['reserva_id_evento'] = $this->_getSanitizedParam("reserva_id_evento");
    $data['reserva_nombre_cliente'] = $this->_getSanitizedParam("reserva_nombre_cliente");
    $data['reserva_fecha'] = date('Y-m-d'); // Fecha actual en formato 'Y-m-d'
    $data['reserva_hora'] = date('H:i:s'); // Hora actual en formato 'H:i:s'

    $data['reserva_telefono'] = $this->_getSanitizedParam("reserva_telefono");
    $data['reserva_correo'] = $this->_getSanitizedParam("reserva_correo");
    $data['reserva_comentario'] = $this->_getSanitizedParam("reserva_comentario");
    $data['reserva_fecha_creacion'] = date('Y-m-d H:i:s'); // Fecha y hora actual en formato 'Y-m-d H:i:s'
    $data['reserva_fecha_actualizacion'] = date('Y-m-d H:i:s'); // Fecha y hora actual en formato 'Y-m-d H:i:s'
    $data['reserva_usuario_creacion'] = $this->_getSanitizedParam("reserva_usuario_creacion");
    $data['reserva_usuario_actualizacion'] = $this->_getSanitizedParam("reserva_usuario_actualizacion");
    $data['reserva_fecha_inicio_reserva'] = $this->_getSanitizedParam("reserva_fecha_inicio_reserva");
    $data['reserva_fecha_cierre_reserva'] = $this->_getSanitizedParam("reserva_fecha_cierre_reserva");
    $data['reserva_fecha_limite_pago'] = $this->_getSanitizedParam("reserva_fecha_limite_pago");
    return $data;
  }



  public function indexAction($filtro = array())
  {
    $filtros = " 1 = 1 ";
    if (!empty($filtro)) {
      if (!empty($filtro['evento_titulo'])) {
        $filtros .= " AND evento_titulo LIKE '%" . $filtro['evento_titulo'] . "%'";
      }
      if (!empty($filtro['evento_costo'])) {
        $filtros .= " AND evento_costo LIKE '%" . $filtro['evento_costo'] . "%'";
      }
    }
    $mainModel = new Administracion_Model_DbTable_Reservas();
    $order = "orden ASC";
    $listaReservas = $mainModel->getList($filtros, $order);

    // Añadido: Enviar la respuesta como JSON
    header('Content-Type: application/json');
    echo json_encode($listaReservas);
  }


  public function reservaSocioAction()
  {
    $filtros = " 1 = 1 ";
    $reservaNumeroCarnet = $this->_getSanitizedParam("reserva_numero_carnet");

    if (!empty($reservaNumeroCarnet)) {
      $filtros .= " AND reserva_numero_carnet LIKE '%" . $reservaNumeroCarnet . "%'";
    }

    $mainModel = new Administracion_Model_DbTable_Reservas();
    $order = "orden ASC";
    $listaReservas = $mainModel->getList($filtros, $order);


    header('Content-Type: application/json');
    echo json_encode($listaReservas);
  }

  public function obtenerReservaAction()
  {
    $filtros = " 1 = 1 ";

    $reservaIdEvento = $this->_getSanitizedParam("reserva_id_evento");



    if (!empty($reservaIdEvento)) {
      $filtros .= " AND reserva_id_evento = " . (int)$reservaIdEvento;
    }

    $mainModel = new Administracion_Model_DbTable_Reservas();
    $order = "orden ASC";
    $listaReservas = $mainModel->getList($filtros, $order);


    header('Content-Type: application/json');
    echo json_encode($listaReservas);
  }
}
