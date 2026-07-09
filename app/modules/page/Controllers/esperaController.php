<?php

class Page_esperaController extends Page_mainController
{
  public $colaMaximo;

  public function init()
  {
    $this->colaMaximo = 10000;
    parent::init();

  }
  public function indexAction()
  {
    //print_r( Session::getInstance()->get('colaCompra'));
    $colaCompraModel = new Administracion_Model_DbTable_Colacompra();

    // Fecha actual en PHP para reemplazar NOW() en consultas
    $fechaActual = date('Y-m-d H:i:s');

    // Limpiar colas vencidas antes de procesar
    $this->limpiarColasVencidas();

    // Verificar si ya tengo una cola activa en sesión
    $colaExistente = Session::getInstance()->get('colaCompra');
    $idCola = null;

    // Primero verificar si ya tengo una cola válida
    if ($colaExistente && isset($colaExistente->cola_compras_id)) {
      $miColaAnterior = $colaCompraModel->getById($colaExistente->cola_compras_id);
      Session::getInstance()->set('colaCompra', $miColaAnterior);
      if (
        $miColaAnterior &&
        $miColaAnterior->cola_compras_socio_documento == $this->socio->SBE_CODI &&
        ($miColaAnterior->cola_compras_estado == 'espera' || $miColaAnterior->cola_compras_estado == 'activo')
      ) {
        // Mi cola anterior sigue válida, usar esa
        $idCola = $miColaAnterior->cola_compras_id;

        // Si mi cola está activa y no ha vencido, redirigir directamente a evento
        if (
          $miColaAnterior->cola_compras_estado == 'activo' &&
          $miColaAnterior->cola_compras_vence_el &&
          strtotime($miColaAnterior->cola_compras_vence_el) > time()
        ) {
          header('Location: /page/evento/');
          exit;
        }
      }
    }

    // Si no tengo una cola válida, verificar si necesito crear una nueva o puedo pasar directo
    if (!$idCola) {
      // Verificar cuántas personas están activas ANTES de crear mi cola
      $activosEnCola = $colaCompraModel->getList(
        " cola_compras_estado='activo' AND cola_compras_vence_el > '" . $fechaActual . "'",
        ""
      );
      $totalActivos = count($activosEnCola);

      // Si hay espacio disponible, crear y activar inmediatamente
      if ($totalActivos < intval($this->colaMaximo)) {
        $idCola = $colaCompraModel->insertSimple($this->socio->SBE_CODI);

        // Activar inmediatamente
        $fechaVencimiento = date('Y-m-d H:i:s', strtotime('+60 minutes'));
        $colaCompraModel->editField($idCola, 'cola_compras_estado', 'activo');
        $colaCompraModel->editField($idCola, 'cola_compras_vence_el', $fechaVencimiento);
        $colaCompraModel->editField($idCola, 'cola_compras_inicio_el', date('Y-m-d H:i:s'));

        // Guardar en sesión
        $colaActual = $colaCompraModel->getById($idCola);
        Session::getInstance()->set('colaCompra', $colaActual);

        // Actualizar sesión si existe
        $accionSesionesModel = new Administracion_Model_DbTable_Accionsesiones();
        $sesionInfo = Session::getInstance()->get('sesion');
        if ($sesionInfo && isset($sesionInfo->accion_sesion_id) && $sesionInfo->accion_sesion_id) {
          $accionSesionesModel->editField($sesionInfo->accion_sesion_id, 'accion_sesion_fecha_fin', $fechaVencimiento);
          $sesionInfo = $accionSesionesModel->getById($sesionInfo->accion_sesion_id);
          if ($sesionInfo) {
            Session::getInstance()->set('sesion', $sesionInfo);
          }
        }

        // Redirigir directamente a evento
        header('Location: /page/evento/');
        exit;
      } else {
        // No hay espacio, crear cola en estado de espera
        $idCola = $colaCompraModel->insertSimple($this->socio->SBE_CODI);
        $colaActual = $colaCompraModel->getById($idCola);
        Session::getInstance()->set('colaCompra', $colaActual);
      }
    }

    // Recalcular activos después de posibles cambios
    $activosEnCola = $colaCompraModel->getList(
      " cola_compras_estado='activo' AND cola_compras_vence_el > '" . $fechaActual . "'",
      ""
    );
    $totalActivos = count($activosEnCola);

    // Verificar una vez más si puedo activar mi cola (solo si soy el siguiente)
    $miCola = $colaCompraModel->getById($idCola);
    if ($miCola && $miCola->cola_compras_estado == 'espera' && $totalActivos < intval($this->colaMaximo)) {
      // Verificar si SOY el siguiente en la cola
      $siguienteEnCola = $colaCompraModel->getList(
        "cola_compras_estado='espera'",
        "cola_compras_creado_el ASC"
      );

      // Solo activar si soy el primero en la cola de espera
      if (count($siguienteEnCola) > 0 && $siguienteEnCola[0]->cola_compras_id == $idCola) {
        // Puedo activar mi cola
        $fechaVencimiento = date('Y-m-d H:i:s', strtotime('+60 minutes'));
        $colaCompraModel->editField($idCola, 'cola_compras_estado', 'activo');
        $colaCompraModel->editField($idCola, 'cola_compras_vence_el', $fechaVencimiento);
        $colaCompraModel->editField($idCola, 'cola_compras_inicio_el', date('Y-m-d H:i:s'));

        // Actualizar sesión si existe
        $accionSesionesModel = new Administracion_Model_DbTable_Accionsesiones();
        $sesionInfo = Session::getInstance()->get('sesion');
        if ($sesionInfo && isset($sesionInfo->accion_sesion_id) && $sesionInfo->accion_sesion_id) {
          $accionSesionesModel->editField($sesionInfo->accion_sesion_id, 'accion_sesion_fecha_fin', $fechaVencimiento);
          $sesionInfo = $accionSesionesModel->getById($sesionInfo->accion_sesion_id);
          if ($sesionInfo) {
            Session::getInstance()->set('sesion', $sesionInfo);
          }
        }

        // Actualizar cola en sesión
        $colaActual = $colaCompraModel->getById($idCola);
        Session::getInstance()->set('colaCompra', $colaActual);

        header('Location: /page/evento/');
        exit;
      }
    }

    // Si llegamos aquí, mostrar vista de espera
    $this->_view->totalActivos = $totalActivos;
    $this->_view->posicionEnCola = $this->calcularPosicionEnCola($idCola);
    $this->_view->colaId = $idCola;
    $this->_view->socioDocumento = $this->socio->SBE_CODI;
  }

  private function calcularPosicionEnCola($idCola)
  {
    $colaCompraModel = new Administracion_Model_DbTable_Colacompra();

    // Obtener mi cola actual
    $miCola = $colaCompraModel->getById($idCola);
    if (!$miCola) {
      return 0;
    }

    // Contar cuántas colas están en espera y se crearon antes que la mía
    $colasAnteriores = $colaCompraModel->getList(
      "cola_compras_estado='espera' AND cola_compras_creado_el < '{$miCola->cola_compras_creado_el}'",
      "cola_compras_creado_el ASC"
    );

    return count($colasAnteriores); // Mi posición exacta en la cola de espera
  }

  // Método para limpiar colas vencidas o abandonadas
  private function limpiarColasVencidas()
  {
    $colaCompraModel = new Administracion_Model_DbTable_Colacompra();

    // Fechas calculadas en PHP en lugar de NOW() y DATE_SUB(NOW(), INTERVAL 2 HOUR)
    $fechaActual = date('Y-m-d H:i:s');
    $fechaAntigua = date('Y-m-d H:i:s', strtotime('-2 hours'));

    // Marcar como vencidas las colas activas que ya pasaron su tiempo límite
    $colasVencidas = $colaCompraModel->getList(
      "cola_compras_estado='activo' AND cola_compras_vence_el < '" . $fechaActual . "'",
      ""
    );

    foreach ($colasVencidas as $cola) {
      $colaCompraModel->editField($cola->cola_compras_id, 'cola_compras_estado', 'vencido');
    }

    // Opcional: Limpiar colas muy antiguas en estado de espera (más de 2 horas)
    $colasAntiguas = $colaCompraModel->getList(
      "cola_compras_estado='espera' AND cola_compras_creado_el < '" . $fechaAntigua . "'",
      ""
    );

    foreach ($colasAntiguas as $cola) {
      $colaCompraModel->editField($cola->cola_compras_id, 'cola_compras_estado', 'vencido');
    }
  }

  // Endpoint AJAX para verificar estado de la cola
  public function verificarestadoAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $idCola = $this->_getSanitizedParam('colaId');
    if (!$idCola) {
      echo json_encode(['error' => 'ID de cola no proporcionado']);
      return;
    }

    $colaCompraModel = new Administracion_Model_DbTable_Colacompra();

    // Limpiar colas vencidas antes de verificar
    $this->limpiarColasVencidas();

    // Verificar estado de mi cola
    $miCola = $colaCompraModel->getById($idCola);
    if (!$miCola) {
      echo json_encode(['error' => 'Cola no encontrada']);
      return;
    }


    // Si mi cola está activa, redirigir
    if ($miCola->cola_compras_estado == 'activo') {
      echo json_encode(['redirect' => '/page/evento/']);
      return;
    }

    $fechaActual = date('Y-m-d H:i:s');
    // Contar cuántas personas están activas delante mío
    $activosEnCola = $colaCompraModel->getList(" cola_compras_estado='activo' AND cola_compras_vence_el > '$fechaActual'", "");
    $totalActivos = count($activosEnCola);

    // Si hay cupos disponibles, verificar si SOY EL SIGUIENTE en la cola
    if ($totalActivos < $this->colaMaximo) {
      // Obtener el siguiente en la fila (el más antiguo en estado 'espera')
      $siguienteEnCola = $colaCompraModel->getList(
        "cola_compras_estado='espera'",
        "cola_compras_creado_el ASC"
      );

      // Verificar si SOY el siguiente en la cola
      if (count($siguienteEnCola) > 0 && $siguienteEnCola[0]->cola_compras_id == $idCola) {
        // ¡Es mi turno! Activar mi cola
        $fechaVencimiento = date('Y-m-d H:i:s', strtotime('+60 minutes'));
        $colaCompraModel->editField($idCola, 'cola_compras_estado', 'activo');
        $colaCompraModel->editField($idCola, 'cola_compras_vence_el', $fechaVencimiento);
        $colaCompraModel->editField($idCola, 'cola_compras_inicio_el', date('Y-m-d H:i:s'));

        // Crear sesión activa
        $accionSesionesModel = new Administracion_Model_DbTable_Accionsesiones();
        $sesionInfo = Session::getInstance()->get('sesion');

        // Verificar que la sesión existe antes de intentar actualizarla
        if ($sesionInfo && isset($sesionInfo->accion_sesion_id) && $sesionInfo->accion_sesion_id) {
          $idSesion = $sesionInfo->accion_sesion_id;
          $accionSesionesModel->editField($idSesion, 'accion_sesion_fecha_fin', $fechaVencimiento);

          $sesionInfo = $accionSesionesModel->getById($idSesion);
          if ($sesionInfo) {
            Session::getInstance()->set('sesion', $sesionInfo);
          }
        }

        //actualizar cola en sesión
        $miColaActualizada = $colaCompraModel->getById($idCola);
        Session::getInstance()->set('colaCompra', $miColaActualizada);

        echo json_encode(['redirect' => '/page/evento/']);
        return;
      }
      // Si no soy el siguiente, simplemente continúo esperando
    }

    // Calcular posición en cola
    $posicion = $this->calcularPosicionEnCola($idCola);

    echo json_encode([
      'waiting' => true,
      'position' => $posicion,
      'totalActive' => $totalActivos
    ]);
  }
}