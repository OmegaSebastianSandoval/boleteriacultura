<?php

class Page_invitadosReservaController extends Page_mainController
{

  

        
    public function insertReservaInvitadosAction()
    {
        $data = $this->getData(); 
        $reservaInvitadosModel = new Administracion_Model_DbTable_Invitadosreservas(); 
    
        $id = $reservaInvitadosModel->insert($data);
    
        $response = array();
    
        if ($id) {
            $data['id'] = $id;
            $data['log_log'] = print_r($data, true);
            $data['log_tipo'] = 'CREAR RESERVA DE INVITADOS';
    
            $logModel = new Administracion_Model_DbTable_Log();
            $logModel->insert($data);
    
            $response['message'] = 'Invitados agregado correctamente,  creada con éxito';
        } else {
            $response['message'] = 'Error al crear la reserva de invitados';
        }
    
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    
private function getData()
	{
		$data = array();
		$data['documento_invitado'] = $this->_getSanitizedParamHtml("documento_invitado");
		$data['reserva_id_reserva'] = $this->_getSanitizedParamHtml("reserva_id_reserva");
		$data['invitadoReserva_nombre_invitado'] = $this->_getSanitizedParam("invitadoReserva_nombre_invitado");
        $data['invitadoReserva_apellido_invitado'] = $this->_getSanitizedParam("invitadoReserva_apellido_invitado");
		$data['invitadoReserva_correo_invitado'] = $this->_getSanitizedParamHtml("invitadoReserva_correo_invitado");
		$data['invitadoReserva_estado_invitado'] = $this->_getSanitizedParam("invitadoReserva_estado_invitado");
		$data['invitadoReserva_fecha_nacimiento'] = $this->_getSanitizedParamHtml("invitadoReserva_fecha_nacimiento");
		$data['invitadoReserva_telefono'] = $this->_getSanitizedParam("invitadoReserva_telefono");
		$data['invitadosReserva_fecha_creacion'] = $this->_getSanitizedParamHtml("invitadosReserva_fecha_creacion");
		$data['invitadosReserva_fecha_actualizacion'] = $this->_getSanitizedParamHtml("invitadosReserva_fecha_actualizacion");
		$data['invitadosReserva_usuario_creacion'] = $this->_getSanitizedParam("invitadosReserva_usuario_creacion");
		$data['invitadosReserva_actualizacion'] = $this->_getSanitizedParamHtml("invitadosReserva_actualizacion");
		return $data;
	}

    public function indexInvitadosAction($filtro = array())
     {
    $filtros = " 1 = 1 ";
    
    // Verifica si se pasaron filtros adicionales y los agrega a la consulta
    if (!empty($filtro)) {
        if (!empty($filtro['reserva_id_reserva'])) {
            $filtros .= " AND reserva_id_reserva LIKE '" . $filtro['reserva_id_reserva'] . "'";
        }
        // Agrega otros filtros según tus necesidades
    }
    
    $invitadosReservaModel = new Administracion_Model_DbTable_Invitadosreservas();
    $order = "orden ASC"; // Puedes cambiar el orden según tus necesidades
    $listaInvitadosReserva = $invitadosReservaModel->getList($filtros, $order);
    
    // Añadido: Enviar la respuesta como JSON
    header('Content-Type: application/json');
    
    echo json_encode($listaInvitadosReserva);
}

}


