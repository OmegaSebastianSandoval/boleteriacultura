<?php

class Page_acomodacionesController extends Page_mainController


{
  private $comodaciones_asientos = array();
  private $largo = 30;
  private $ancho = 30;

  public function AsientosEventos()
  {

    // Inicializa la matriz de asientos
    for ($i = 0; $i < $this->largo; $i++) {
      for ($j = 0; $j < $this->ancho; $j++) {
        $codigo = chr(65 + $i) . ($j + 1);  // A1, A2, ..., B1, B2, ...
        $this->comodaciones_asientos[$codigo] = 'Libre';
      }
    }
  }


  public function reservarAsiento($codigo)
  {
    if (isset($this->comodaciones_asientos[$codigo]) && $this->comodaciones_asientos[$codigo] === 'Libre') {
      $this->comodaciones_asientos[$codigo] = 'Reservado';
      return true;
    }
    return false;
  }



  public function guardarAcomodacionAction()
  {
    // $this->AsientosEventos(); // Comentado para evitar reinicializar los asientos

    $data = $this->getData();

    // Calcular la capacidad total
    $capacidadTotal = substr_count($data['acomodaciones_asientos'], '1');
    $data['acomodaciones_capacidad'] = $capacidadTotal;

    $acomodacionesModel = new Administracion_Model_DbTable_Acomodaciones();

    $id = $acomodacionesModel->insert($data);

    if ($id) {
      $data['id'] = $id;
      // $response['message'] = 'Acomodación guardada con éxito';
      header('Location: /administracion/acomodaciones/');
    } else {
      // $response['message'] = 'Error al guardar la acomodación';
      header('Location: /administracion/acomodaciones/');
    }

    // header('Content-Type: application/json');
    // echo json_encode($response);
    // exit;
  }

  private function getData()
  {
    $data = array();
    $data['acomodaciones_nombre'] = $_POST["acomodaciones_nombre"] ?? null;
    $data['acomodaciones_largo'] = empty($_POST["acomodaciones_largo"]) ? '0' : $_POST["acomodaciones_largo"];
    $data['acomodaciones_ancho'] = empty($_POST["acomodaciones_ancho"]) ? '0' : $_POST["acomodaciones_ancho"];
    $data['acomodaciones_asientos'] = $_POST["acomodaciones_asientos"] ?? null;
    $data['acomodaciones_capacidad'] = empty($_POST["acomodaciones_capacidad"]) ? '0' : $_POST["acomodaciones_capacidad"];

    return $data;
  }
}
