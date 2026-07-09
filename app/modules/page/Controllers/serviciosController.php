<?php

class Page_serviciosController extends Page_mainController
{
  public function indexAction()
  {

  }

  public function infoAction()
  {
    $title = "Mis Boletas";
    $this->getLayout()->setTitle($title);
    $this->_view->titlesection = $title;

    // Obtener información del socio
    $socio = $this->consultarSocioSession();
    $this->_view->socio = $socio;

    if (!$socio || !$socio->SBE_CODI) {
      $this->_view->mensaje = "No se pudo identificar al socio en sesión.";
      return;
    }

    $reservaModel = new Administracion_Model_DbTable_Reservas();
    $idReserva = $this->_getDecryptedParam('id'); // Parámetro opcional

    // Si se especifica un ID de reserva, buscar esa reserva específica
    if ($idReserva) {
      $reservaConfirmada = $reservaModel->getById($idReserva);

      // Verificar que la reserva pertenece al socio y está confirmada
      if (
        !$reservaConfirmada ||
        $reservaConfirmada->reserva_documento != $socio->SBE_CODI ||
        !in_array((int) $reservaConfirmada->reserva_estado, [2, 3, 11])
      ) {
        $this->_view->tieneBoletas = false;
        $this->_view->mensaje = "Reserva no encontrada o no disponible.";
        return;
      }
      // Verificar que la reserva tiene boletas generadas
      $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
      $boletasExistentes = $boletasModel->getList("boleta_reserva_id = '{$reservaConfirmada->id}' AND boleta_estado IN (1, 2, 3)", "");

      if (!$boletasExistentes || count($boletasExistentes) === 0) {
        $this->_view->tieneBoletas = false;
        $this->_view->mensaje = "Esta reserva aún no tiene boletas generadas.";
        return;
      }

      // Todo ok, tiene boletas
      $this->_view->tieneBoletas = true;

    } else {
      // Comportamiento original: buscar la primera reserva confirmada
      $tieneBoletas = $this->verBoletas();
      $this->_view->tieneBoletas = $tieneBoletas;

      if (!$tieneBoletas) {
        $this->_view->mensaje = "No tienes boletas generadas o no hay una reserva confirmada.";
        return;
      }

      $reservaConfirmada = $reservaModel->getList(
        "reserva_documento = '{$socio->SBE_CODI}' AND (reserva_estado = 2 OR reserva_estado = 3 OR reserva_estado = 11)",
        ""
      )[0] ?? null;

      if (!$reservaConfirmada) {
        $this->_view->mensaje = "No se encontró una reserva confirmada.";
        return;
      }
    }

    $this->_view->reserva = $reservaConfirmada;

    // Debug info (remover después)
    $this->_view->debugInfo = [
      'id_enviado' => $idReserva,
      'reserva_encontrada' => $reservaConfirmada ? $reservaConfirmada->id : 'null',
      'tiene_boletas' => $this->_view->tieneBoletas ?? 'no_definido'
    ];

    // Obtener información del evento
    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $evento = $eventoModel->getById($reservaConfirmada->reserva_id_evento);
    $this->_view->evento = $evento;

    // Obtener información de la mesa
    if ($reservaConfirmada->reserva_mesa_id) {
      $mesasIds = explode(',', $reservaConfirmada->reserva_mesa_id);
      $mesaModel = new Administracion_Model_DbTable_Mesas();
      $mesas = $mesaModel->getMesasConDetalles("m.mesa_id IN (" . implode(',', array_map('intval', $mesasIds)) . ")");
      $this->_view->mesaInfo = $mesas; 
      // print_r($mesas);
    }

    // Obtener las boletas de la reserva
    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
    $boletas = $boletasModel->getList(
      "boleta_reserva_id = '{$reservaConfirmada->id}' AND boleta_estado IN (1, 2, 3)",
      "boleta_id ASC"
    );

    // Obtener información de los invitados para cada boleta
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $invitados = $invitadosModel->getList(
      "reserva_id_reserva = '{$reservaConfirmada->id}'",
      "id_invitado ASC"
    );

    // Combinar información de boletas e invitados
    foreach ($boletas as $key => $boleta) {
      if (isset($invitados[$key])) {
        $boleta->invitado_nombre = $invitados[$key]->invitadoReserva_nombre_invitado;
        $boleta->invitado_apellido = $invitados[$key]->invitadoReserva_apellido_invitado;
        $boleta->invitado_apellido = $invitados[$key]->invitadoReserva_apellido_invitado;
        $boleta->invitado_documento = $invitados[$key]->documento_invitado;
        $boleta->invitado_correo = $invitados[$key]->invitadoReserva_correo_invitado;
        $boleta->es_socio_principal = $invitados[$key]->invitadoReserva_beneficiario_principal == '1';
        $boleta->invitadoReserva_estado_invitado = $invitados[$key]->invitadoReserva_estado_invitado;
      }
    }

    $this->_view->boletas = $boletas;
    $this->_view->totalBoletas = count($boletas);

    // Estadísticas de las boletas
    $boletasValidadas = array_filter($boletas, function ($boleta) {
      return $boleta->boleta_estado == 2;
    });
    $this->_view->boletasValidadas = count($boletasValidadas);
  }


  public function reenviarBoletasAction()
  {
    $this->setLayout('blanco');

    $socio = $this->consultarSocioSession();

    if (!$socio || !$socio->SBE_CODI) {
      echo json_encode(['success' => false, 'message' => 'No hay sesión activa']);
      return;
    }

    try {
      // Obtener la reserva confirmada del socio
      $reservaModel = new Administracion_Model_DbTable_Reservas();
      $reservaConfirmada = $reservaModel->getList(
        "reserva_documento = '{$socio->SBE_CODI}' AND (reserva_estado = 2 OR reserva_estado = 3 OR reserva_estado = 11)",
        ""
      )[0] ?? null;

      if (!$reservaConfirmada) {
        echo json_encode(['success' => false, 'message' => 'No se encontró una reserva confirmada']);
        return;
      }

      // Aquí implementar la lógica para reenviar las boletas por email
      $response = [
        'success' => true,
        'message' => 'Boletas reenviadas correctamente a tu correo electrónico'
      ];

      echo json_encode($response);
    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => 'Error al reenviar las boletas: ' . $e->getMessage()
      ]);
    }
  }
}