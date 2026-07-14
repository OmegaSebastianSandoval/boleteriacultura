<?php

/**
 *
 */

class Validacion_editarreservaController extends Validacion_mainController
{
  protected $mainModel;
  protected $route;
  protected $_csrf_section = "editarreserva";
  public $csrf;
  public function init()
  {
    if (!Session::getInstance()->get("user")) {
      header('Location: /validacion/');
      exit;
    }
    parent::init();
  }

  public function indexAction()
  {
    $user = Session::getInstance()->get("user");

    $this->_view->user = $user;
  }

  public function consultarreservaAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $numeroReserva = $this->_getSanitizedParam('numero_reserva');

    if (!$numeroReserva) {
      echo json_encode(['error' => 'Número de reserva requerido']);
      return;
    }

    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reservas = $reservasModel->getList(" (id = '$numeroReserva' OR reserva_numero_carnet = '$numeroReserva' OR reserva_documento = '$numeroReserva') AND reserva_estado IN (2,3,11) ", "");
    // error_log(print_r($reservas, true));
    if (!$reservas || count($reservas) == 0) {
      echo json_encode(['error' => 'No se encontraron reservas']);
      return;
    }

    // Si hay múltiples reservas, devolver la lista para que el usuario seleccione
    if (count($reservas) > 1) {
      // Agregar información adicional de mesas a cada reserva
      foreach ($reservas as &$reserva) {
        $reserva->mesa_info = $this->obtenerInfoMesas($reserva->reserva_mesa_id);
        $reserva->tiene_invitados_incompletos = $this->tieneInvitadosIncompletos($reserva->id);
      }

      echo json_encode([
        'success' => true,
        'multiple' => true,
        'reservas' => $reservas
      ]);
      return;
    }

    // Si hay una sola reserva, continuar con la lógica actual
    $reserva = $reservas[0];

    if ($reserva && $reserva->reserva_boleteria_reenviada >= 1) {
      echo json_encode(['error' => 'Boletería ya enviada']);
      return;
    }

    // Agregar información de mesas también para reserva individual
    $reserva->mesa_info = $this->obtenerInfoMesas($reserva->reserva_mesa_id);
    $reserva->tiene_invitados_incompletos = $this->tieneInvitadosIncompletos($reserva->id);

    echo json_encode(['success' => true, 'reserva' => $reserva]);
  }

  /**
   * Obtiene información de las mesas asignadas a una reserva
   */
  private function obtenerInfoMesas($mesaIds)
  {
    if (!$mesaIds) {
      return [];
    }

    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $ambienteModel = new Administracion_Model_DbTable_Ambientes();
    $pisosModel = new Administracion_Model_DbTable_Pisos();

    $mesaIdsArray = array_map('trim', explode(',', $mesaIds));
    $mesasInfo = [];

    foreach ($mesaIdsArray as $mesaId) {
      $mesa = $mesasModel->getById($mesaId);
      if ($mesa) {
        $ambiente = $ambienteModel->getById($mesa->mesa_ambiente);
        $piso = $ambiente ? $pisosModel->getById($ambiente->ambiente_piso) : null;

        $mesasInfo[] = [
          'mesa_nombre' => $mesa->mesa_nombre,
          'mesa_capacidad' => $mesa->mesa_capacidad,
          'mesa_tipo' => $mesa->mesa_tipo,
          'ambiente_nombre' => $ambiente ? $ambiente->ambiente_nombre : 'N/A',
          'piso_nombre' => $piso ? $piso->piso_nombre : 'N/A'
        ];
      }
    }

    return $mesasInfo;
  }

  /**
   * Verifica si una reserva tiene invitados con datos incompletos
   */
  private function tieneInvitadosIncompletos($reservaId)
  {
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $invitados = $invitadosModel->getList("reserva_id_reserva = '{$reservaId}'");

    if (!$invitados) {
      return false;
    }

    foreach ($invitados as $invitado) {
      $esIncompleto = (
        !$invitado->documento_invitado ||
        str_starts_with($invitado->invitadoReserva_nombre_invitado, 'Invitado') ||
        !$invitado->invitadoReserva_nombre_invitado
      );

      if ($esIncompleto) {
        return true;
      }
    }

    return false;
  }

  public function consultarinvitadosAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $reservaId = $this->_getSanitizedParam('reserva_id');

    if (!$reservaId) {
      echo json_encode(['error' => 'ID de reserva requerido']);
      return;
    }

    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $boletasinfoModel = new Administracion_Model_DbTable_Boletasinfo();
    $invitados = $invitadosModel->getList("reserva_id_reserva = '{$reservaId}'");

    if (!$invitados) {
      echo json_encode(['error' => 'No se encontraron invitados para esta reserva']);
      return;
    }

    $invitadosCompletos = [];
    $invitadosIncompletos = [];

    foreach ($invitados as $invitado) {
      $esIncompleto = (
        !$invitado->documento_invitado ||
        str_starts_with($invitado->invitadoReserva_nombre_invitado, 'Invitado') ||
        !$invitado->invitadoReserva_nombre_invitado
      );

      if ($esIncompleto) {
        $invitadosIncompletos[] = $invitado;
      } else {
        $invitado->boleta = $boletasinfoModel->getList("boleta_documento = '{$invitado->documento_invitado}' AND boleta_reserva_id = '{$reservaId}'", "");
        $invitadosCompletos[] = $invitado;
      }
    }

    // Solo mostrar si hay invitados incompletos
    // if (empty($invitadosIncompletos)) {
    //   echo json_encode(['error' => 'Todos los invitados ya tienen documento asociado']);
    //   return;
    // }

    echo json_encode([
      'success' => true,
      'invitados_completos' => $invitadosCompletos,
      'invitados_incompletos' => $invitadosIncompletos
    ]);
  }

  public function actualizarinvitadoAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $invitadoId = $this->_getSanitizedParam('invitado_id');
    $nombre = $this->_getSanitizedParam('nombre');
    $apellido = $this->_getSanitizedParam('apellido');
    $documento = $this->_getSanitizedParam('documento');

    if (!$invitadoId || !$nombre || !$apellido || !$documento) {
      echo json_encode(['error' => 'Todos los campos son requeridos']);
      return;
    }

    try {
      $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
      $invitado = $invitadosModel->getById($invitadoId);

      if (!$invitado) {
        echo json_encode(['error' => 'Invitado no encontrado']);
        return;
      }


      $invitadosModel->editField($invitadoId, 'invitadoReserva_nombre_invitado', $nombre);
      $invitadosModel->editField($invitadoId, 'invitadoReserva_apellido_invitado', $apellido);
      $invitadosModel->editField($invitadoId, 'documento_invitado', $documento);

      echo json_encode(['success' => true, 'message' => 'Invitado actualizado correctamente']);
    } catch (Exception $e) {
      echo json_encode(['error' => 'Error al actualizar el invitado: ' . $e->getMessage()]);
    }
  }

  public function validardocumentoAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');
    $documento = trim($this->_getSanitizedParam('documento'));
    if (!$documento) {
      echo json_encode(['status' => false, 'message' => 'documento de reserva inválido.']);
      return;
    }
    $invitadosReservasModel = new Administracion_Model_DbTable_Invitadosreservas();
    $invitado = $invitadosReservasModel->getList(" documento_invitado = '$documento' ", "")[0];
    if ($invitado) {
      echo json_encode(['status' => false, 'message' => 'El documento ingresado ya se encuentra registrado en una reserva diferente.']);
      return;
    } else {
      echo json_encode(['status' => true, 'message' => 'Documento de invitado válido.']);
    }
  }

  public function generarboletasAction()
  {
    //$this->setLayout('blanco');
    //error_reporting(E_ALL);
    // Obtener parámetros
    $idReserva = $this->_getSanitizedParam("id_reserva");
    $idInvitado = $this->_getSanitizedParam("id_invitado");
    $forzarReenvio = $this->_getSanitizedParam("forzar_reenvio"); // Nueva bandera: 1 = incluir todas las boletas

    // Validar que al menos uno de los parámetros esté presente
    if (empty($idReserva) && empty($idInvitado)) {
      $this->handleError("Debe proporcionar al menos el ID de la reserva o el ID del invitado.");
    }

    // Modelos necesarios
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $invitadosReservaModel = new Administracion_Model_DbTable_Invitadosreservas();
    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $logModel = new Administracion_Model_DbTable_Log();
    $ambienteModel = new Administracion_Model_DbTable_Ambientes();
    $pisosModel = new Administracion_Model_DbTable_Pisos();

    try {
      // Log inicio del proceso
      $logData = [
        'log_log' => "INICIO: Proceso de boletería iniciado para reserva $idReserva. Forzar reenvío: " . ($forzarReenvio == '1' ? 'SÍ' : 'NO'),
        'log_tipo' => 'REENVIO BOLETERIA - INICIO'
      ];
      $logModel->insert($logData);

      // Si solo se proporciona ID del invitado, obtener la reserva
      if (empty($idReserva) && !empty($idInvitado)) {
        $logData = [
          'log_log' => "INFO: Buscando reserva para invitado ID: $idInvitado",
          'log_tipo' => 'REENVIO BOLETERIA - BUSQUEDA INVITADO'
        ];
        $logModel->insert($logData);

        $invitado = $invitadosReservaModel->getById($idInvitado);
        if (!$invitado) {
          $this->handleError("Invitado no encontrado con ID: $idInvitado");
        }
        $idReserva = $invitado->reserva_id_reserva;

        $logData = [
          'log_log' => "INFO: Reserva encontrada para invitado $idInvitado: Reserva ID $idReserva",
          'log_tipo' => 'REENVIO BOLETERIA - RESERVA ENCONTRADA'
        ];
        $logModel->insert($logData);
      }

      // Obtener datos de la reserva
      $logData = [
        'log_log' => "INFO: Obteniendo datos de reserva ID: $idReserva",
        'log_tipo' => 'REENVIO BOLETERIA - OBTENIENDO RESERVA'
      ];
      $logModel->insert($logData);

      $reserva = $reservasModel->getById($idReserva);
      if (!$reserva) {
        $this->handleError("Reserva no encontrada con ID: $idReserva");
      }

      if ($reserva->reserva_boleteria_reenviada) {
        $this->handleError("La boletería para la reserva ID: $idReserva ya ha sido reenviada.");
      }

      $logData = [
        'log_log' => "INFO: Reserva obtenida - Cliente: {$reserva->reserva_nombre_cliente} {$reserva->reserva_apellido_cliente}, Fecha: {$reserva->reserva_fecha}, Total personas: {$reserva->reserva_total_personas}",
        'log_tipo' => 'REENVIO BOLETERIA - RESERVA OBTENIDA'
      ];
      $logModel->insert($logData);

      // Log detallado de la reserva completa
      $logData = [
        'log_log' => "DETALLE RESERVA COMPLETA: " . print_r($reserva, true),
        'log_tipo' => 'REENVIO BOLETERIA - DETALLE RESERVA'
      ];
      $logModel->insert($logData);

      // Obtener invitados de la reserva (todos o uno específico)
      $logData = [
        'log_log' => "INFO: Obteniendo lista de invitados para reserva ID: $idReserva" . ($idInvitado ? " - Solo invitado ID: $idInvitado" : ""),
        'log_tipo' => 'REENVIO BOLETERIA - OBTENIENDO INVITADOS'
      ];
      $logModel->insert($logData);

      if ($idInvitado) {
        // Si se especifica un invitado, obtener solo ese
        $invitadoEspecifico = $invitadosReservaModel->getById($idInvitado);
        if (!$invitadoEspecifico) {
          $this->handleError("Invitado no encontrado con ID: $idInvitado");
        }
        if ($invitadoEspecifico->reserva_id_reserva != $idReserva) {
          $this->handleError("El invitado ID: $idInvitado no pertenece a la reserva ID: $idReserva");
        }
        $invitadosReserva = [$invitadoEspecifico];
      } else {
        // Si no se especifica invitado, obtener todos los de la reserva
        $invitadosReserva = $invitadosReservaModel->getList("reserva_id_reserva = '$idReserva'", "");
      }

      if (empty($invitadosReserva)) {
        $this->handleError("No se encontraron invitados para la reserva ID: $idReserva");
      }

      // Solo validar duplicados si NO es un invitado específico
      if (!$idInvitado) {
        foreach ($invitadosReserva as $invitadoInd) {
          $boletaModel = new Administracion_Model_DbTable_Boletasinfo();
          $existeBoletaInvitado = $boletaModel->getList("boleta_documento = '{$invitadoInd->documento_invitado}'", "");
          if ($existeBoletaInvitado) {
            $this->handleError("Ya existe una boleta para el invitado ID: {$invitadoInd->id_invitado} y con el documento: {$invitadoInd->documento_invitado}");
          }
        }
      }

      $logData = [
        'log_log' => "INFO: Encontrados " . count($invitadosReserva) . " invitados para reserva ID: $idReserva",
        'log_tipo' => 'REENVIO BOLETERIA - INVITADOS OBTENIDOS'
      ];
      $logModel->insert($logData);

      // Log detallado de todos los invitados
      $logData = [
        'log_log' => "DETALLE TODOS LOS INVITADOS: " . print_r($invitadosReserva, true),
        'log_tipo' => 'REENVIO BOLETERIA - DETALLE INVITADOS'
      ];
      $logModel->insert($logData);

      // Validar cantidad de invitados vs reserva
      $cantidadTotalReserva = $reserva->reserva_total_personas;
      $cantidadTotalInvitados = count($invitadosReserva);

      if ($cantidadTotalReserva != $cantidadTotalInvitados) {
        $logData = [
          'log_log' => "ADVERTENCIA: Reserva ID $idReserva - Cantidad de invitados ($cantidadTotalInvitados) no coincide con total de reserva ($cantidadTotalReserva)",
          'log_tipo' => 'REENVIO BOLETERIA - ADVERTENCIA'
        ];
        $logModel->insert($logData);
      }

      // Preparar datos para el reenvío
      $mesaIds = array_map('trim', explode(',', $reserva->reserva_mesa_id));
      $mesasDisponibles = [];
      $mesasInfo = [];

      $logData = [
        'log_log' => "INFO: Procesando mesas asignadas - IDs: " . implode(', ', $mesaIds),
        'log_tipo' => 'REENVIO BOLETERIA - PROCESANDO MESAS'
      ];
      $logModel->insert($logData);

      foreach ($mesaIds as $mesaId) {
        $mesa = $mesasModel->getById($mesaId);

        if ($mesa) {
          $mesasDisponibles[$mesaId] = [
            'mesa' => $mesa,
            'capacidad' => (int) $mesa->mesa_capacidad,
            'ocupados' => 0
          ];
          $mesa->ambienteinfo = $ambienteModel->getById($mesa->mesa_ambiente);
          $mesa->pisoInfo = $pisosModel->getById($mesa->ambienteinfo->ambiente_piso);
          $mesasInfo[$mesaId] = $mesa;

          $logData = [
            'log_log' => "INFO: Mesa procesada - ID: $mesaId, Nombre: {$mesa->mesa_nombre}, Capacidad: {$mesa->mesa_capacidad}, Ambiente: {$mesa->ambienteinfo->ambiente_nombre}, Piso: {$mesa->pisoInfo->piso_nombre}",
            'log_tipo' => 'REENVIO BOLETERIA - MESA PROCESADA'
          ];
          $logModel->insert($logData);
        } else {
          $logData = [
            'log_log' => "ERROR: Mesa no encontrada - ID: $mesaId",
            'log_tipo' => 'REENVIO BOLETERIA - ERROR MESA'
          ];
          $logModel->insert($logData);
        }
      }

      // Validar capacidad total de mesas
      $capacidadTotal = array_sum(array_column($mesasDisponibles, 'capacidad'));
      $logData = [
        'log_log' => "INFO: Validación de capacidad - Total mesas: " . count($mesasDisponibles) . ", Capacidad total: $capacidadTotal, Invitados: $cantidadTotalInvitados",
        'log_tipo' => 'REENVIO BOLETERIA - VALIDACION CAPACIDAD'
      ];
      $logModel->insert($logData);

      if ($capacidadTotal < $cantidadTotalInvitados) {
        $this->handleError("La capacidad total de las mesas ($capacidadTotal) es menor al número de invitados ($cantidadTotalInvitados).");
      }

      // VALIDACIÓN PREVIA: Verificar que TODOS los invitados tengan datos completos
      $invitadosConDatosIncompletos = [];
      $logData = [
        'log_log' => "INFO: Iniciando validación de datos de " . count($invitadosReserva) . " invitados",
        'log_tipo' => 'REENVIO BOLETERIA - VALIDACION INICIADA'
      ];
      $logModel->insert($logData);

      foreach ($invitadosReserva as $invitado) {
        $validacion = $this->validarDatosInvitado($invitado);
        if (!$validacion['valido']) {
          $invitadosConDatosIncompletos[] = [
            'documento' => $invitado->documento_invitado ?? 'SIN DOCUMENTO',
            'nombre' => $invitado->invitadoReserva_nombre_invitado ?? 'SIN NOMBRE',
            'razon' => $validacion['razon']
          ];

          $logData = [
            'log_log' => "ADVERTENCIA: Invitado con datos incompletos - ID: {$invitado->id_invitado}, Documento: " . ($invitado->documento_invitado ?? 'SIN DOCUMENTO') . ", Razón: {$validacion['razon']}",
            'log_tipo' => 'REENVIO BOLETERIA - DATOS INCOMPLETOS'
          ];
          $logModel->insert($logData);
        } else {
          $logData = [
            'log_log' => "INFO: Invitado validado correctamente - ID: {$invitado->id_invitado}, Documento: {$invitado->documento_invitado}, Nombre: {$invitado->invitadoReserva_nombre_invitado}",
            'log_tipo' => 'REENVIO BOLETERIA - INVITADO VALIDADO'
          ];
          $logModel->insert($logData);
        }
      }

      // Si hay invitados con datos incompletos, no procesar nada
      if (!empty($invitadosConDatosIncompletos) && !$idInvitado) {
        $totalIncompletos = count($invitadosConDatosIncompletos);
        $logData = [
          'log_log' => "ERROR: Proceso detenido - $totalIncompletos invitado(s) con datos incompletos",
          'log_tipo' => 'REENVIO BOLETERIA - PROCESO DETENIDO'
        ];
        $logModel->insert($logData);

        $this->handleError("No se puede procesar la boletería. Se encontraron $totalIncompletos invitado(s) con datos incompletos. Todos los invitados deben tener documento y nombre válidos.");
      }

      $logData = [
        'log_log' => "INFO: Validación completada - Todos los invitados tienen datos válidos",
        'log_tipo' => 'REENVIO BOLETERIA - VALIDACION COMPLETADA'
      ];
      $logModel->insert($logData);

      $qrsGenerados = [];
      $invitadosOmitidos = [];
      $invitadosNuevos = [];
      $invitadosReenviados = []; // Nueva categoría para boletas existentes que se reenvían
      $contadorTicket = 0;

      $logData = [
        'log_log' => "INFO: Iniciando procesamiento de " . count($invitadosReserva) . " invitados para reserva $idReserva",
        'log_tipo' => 'REENVIO BOLETERIA - PROCESAMIENTO INICIADO'
      ];
      $logModel->insert($logData);

      foreach ($invitadosReserva as $invitado) {
        // Si es un invitado específico, validar que tenga datos completos
        if ($idInvitado) {
          $validacion = $this->validarDatosInvitado($invitado);
          if (!$validacion['valido']) {
            $this->handleError("El invitado seleccionado tiene datos incompletos: {$validacion['razon']}. Complete los datos antes de generar la boleta.");
          }
        }

        // Verificar si ya existe boleta para este documento e invitado
        $existeBoleta = $boletasModel->getList("boleta_documento = '{$invitado->documento_invitado}' AND boleta_reserva_id = '$idReserva'", "");

        if ($existeBoleta && count($existeBoleta) > 0) {
          // Si es un invitado individual y ya tiene boleta, mostrar error específico
          if ($idInvitado) {
            $this->handleError("Ya existe una boleta para este invitado (Documento: {$invitado->documento_invitado}). No se puede generar una boleta duplicada.");
          }
        }

        $existeArchivo = !empty($existeBoleta) ? file_exists("/images_sales/qrs_news/{$existeBoleta[0]->boleta_uid}.png") : false;

        if ($existeBoleta && count($existeBoleta) > 0 && $existeArchivo) {
          // Si no se fuerza el reenvío, omitir
          if (!$forzarReenvio || $forzarReenvio != '1') {
            // Invitado ya tiene boleta, omitir
            $invitadosOmitidos[] = [
              'documento' => $invitado->documento_invitado,
              'nombre' => $invitado->invitadoReserva_nombre_invitado,
              'boleta_id' => $existeBoleta[0]->boleta_id,
              'boleta_uid' => $existeBoleta[0]->boleta_uid,
              'razon' => 'Ya tiene boleta existente'
            ];

            $logData = [
              'log_log' => "OMITIDO: Ya existe boleta para documento {$invitado->documento_invitado} en reserva $idReserva (Boleta ID: {$existeBoleta[0]->boleta_id})",
              'log_tipo' => 'REENVIO BOLETERIA - INVITADO OMITIDO'
            ];
            $logModel->insert($logData);
            continue;
          } else {
            // Si se fuerza el reenvío, incluir la boleta existente
            $boletaExistente = $existeBoleta[0];
            $mesaAsignada = $boletaExistente->boleta_mesa_id;

            // Actualizar campos de reenvío en la boleta existente
            $boletasModel->editField($boletaExistente->boleta_id, 'boleta_reenvio', 1);
            $boletasModel->editField($boletaExistente->boleta_id, 'boleta_reenvio_fecha', date('Y-m-d H:i:s'));

            $logData = [
              'log_log' => "ACTUALIZADO: Boleta existente actualizada para reenvío - ID: {$boletaExistente->boleta_id}, Documento: {$invitado->documento_invitado}, Fecha reenvío: " . date('Y-m-d H:i:s'),
              'log_tipo' => 'REENVIO BOLETERIA - BOLETA ACTUALIZADA'
            ];
            $logModel->insert($logData);

            $qrsGenerados[] = [
              "boleta_id" => $boletaExistente->boleta_id,
              "boleta_uid" => $boletaExistente->boleta_uid,
              "boleta_token" => $boletaExistente->boleta_token,
              "boleta_numero_ticket" => $boletaExistente->boleta_numero_ticket,
              "rutaQR" => "images_sales/qrs_news/{$boletaExistente->boleta_uid}.png",
              "email" => $reserva->reserva_correo,
              "nombre" => $reserva->reserva_nombre_cliente . " " . $reserva->reserva_apellido_cliente,
              "telefono" => $reserva->reserva_telefono,
              "estado" => $boletaExistente->boleta_estado,
              "documento" => $invitado->documento_invitado,
              "tipoBoleta" => $invitado->invitadoReserva_estado_invitado,
              "mesa_asignada" => $mesaAsignada,
              "tipo_envio" => "REENVIADA" // Marcador para identificar que es una boleta existente
            ];

            $invitadosReenviados[] = [
              'documento' => $invitado->documento_invitado,
              'nombre' => $invitado->invitadoReserva_nombre_invitado,
              'boleta_id' => $boletaExistente->boleta_id,
              'boleta_uid' => $boletaExistente->boleta_uid
            ];

            $logData = [
              'log_log' => "REENVIADA: Boleta existente incluida para documento {$invitado->documento_invitado} en reserva $idReserva (Boleta ID: {$boletaExistente->boleta_id})",
              'log_tipo' => 'REENVIO BOLETERIA - BOLETA REENVIADA'
            ];
            $logModel->insert($logData);
            continue;
          }
        }

        // Buscar mesa disponible
        $mesaAsignadaId = null;
        foreach ($mesasDisponibles as $id => &$info) {
          if ($info['ocupados'] < $info['capacidad']) {
            $mesaAsignadaId = $id;
            $info['ocupados']++;
            break;
          }
        }

        if (!$mesaAsignadaId) {
          $logData = [
            'log_log' => "ERROR: No se pudo asignar mesa para invitado {$invitado->documento_invitado} - Todas las mesas están llenas",
            'log_tipo' => 'REENVIO BOLETERIA - ERROR ASIGNACION MESA'
          ];
          $logModel->insert($logData);
          $this->handleError("No se pudo asignar una mesa a todos los invitados nuevos.");
        }

        $logData = [
          'log_log' => "INFO: Mesa asignada ID $mesaAsignadaId para invitado {$invitado->documento_invitado}",
          'log_tipo' => 'REENVIO BOLETERIA - MESA ASIGNADA'
        ];
        $logModel->insert($logData);

        // Crear nueva boleta
        $contadorTicket++;
        $dataBoleta = [
          "boleta_reserva_id" => $idReserva,
          "boleta_evento_id" => 1, // ID del evento principal
          "boleta_numero_ticket" => $contadorTicket,
          "boleta_uid" => "", // Se actualizará después
          "boleta_token" => "", // Se actualizará después
          "boleta_estado" => 1,
          "boleta_fecha_creacion" => date("Y-m-d H:i:s"),
          "boleta_fecha_validacion" => null,
          "boleta_metodo_validacion" => null,
          "boleta_dispositivo_validacion" => null,
          "boleta_ip_validacion" => null,
          "boleta_fecha_expiracion" => null,
          "boleta_observaciones" => null,
          "boleta_usuario_validador" => null,
          "boleta_documento" => $invitado->documento_invitado,
          "boleta_tipo_boleta" => $invitado->invitadoReserva_estado_invitado,
          "boleta_asignacion" => $invitado->id_invitado,
          "boleta_mesa_id" => $mesaAsignadaId
        ];

        $nextId = $boletasModel->getNextBoletaId();
        $id = $boletasModel->insert($dataBoleta);

        $logData = [
          'log_log' => "INFO: Nueva boleta creada - ID: $id, Documento: {$invitado->documento_invitado}, Ticket: $contadorTicket",
          'log_tipo' => 'REENVIO BOLETERIA - BOLETA CREADA'
        ];
        $logModel->insert($logData);

        // Generar token único
        $yearMonth = date("Ym", strtotime($reserva->reserva_fecha));
        $customUid = "B-{$yearMonth}-" . str_pad($nextId, 7, "0", STR_PAD_LEFT);
        $baseString = "{$idReserva}-{$reserva->reserva_correo}-{$yearMonth}-{$nextId}";
        $token = substr(base_convert(hash('sha256', $baseString), 16, 36), 0, 12);

        $boletasModel->updateGeneratedQR($id, $customUid, $token, 1, $invitado->id_invitado, $mesasDisponibles[$mesaAsignadaId]['mesa']->mesa_id);

        $logData = [
          'log_log' => "INFO: Token generado para boleta $id - UID: $customUid, Token: $token",
          'log_tipo' => 'REENVIO BOLETERIA - TOKEN GENERADO'
        ];
        $logModel->insert($logData);

        // Generar token único
        // $yearMonth = date("Ym", strtotime($reserva->reserva_fecha));
        // $customUid = "B-{$yearMonth}-" . str_pad($nextId, 7, "0", STR_PAD_LEFT);
        // $baseString = "{$idReserva}-{$reserva->reserva_correo}-{$yearMonth}-{$nextId}";
        // $token = substr(base_convert(hash('sha256', $baseString), 16, 36), 0, 12);

        // $boletasModel->updateGeneratedQR($id, $customUid, $token, 1, $invitado->id_invitado);

        $boleta = $boletasModel->getById($id);
        $ambienteModel = new Administracion_Model_DbTable_Ambientes();
        $ambiente = $ambienteModel->getById($mesasDisponibles[$mesaAsignadaId]['mesa']->mesa_ambiente);
        // Generar QR y PDF
        $rutaQR = $this->generarQRReenvio($customUid, $token, $invitado->documento_invitado);
        // error_log("Ruta QR generada: $rutaQR");
        // error_log("Generando PDF para boleta ID: $id, UID: $customUid, Token: $token, Mesa: {$mesasDisponibles[$mesaAsignadaId]['mesa']->mesa_nombre}, Invitado: {$invitado->invitadoReserva_nombre_invitado}");

        // error_log("mesa info: " . print_r($mesasDisponibles[$mesaAsignadaId]['mesa'], true));
        // error_log("mesa info: " . print_r($mesasDisponibles[$mesaAsignadaId], true));

        $this->generarPDFReenvio($reserva, $boleta, $mesasDisponibles[$mesaAsignadaId]['mesa'], $ambiente, $invitado);
        // $this->generarPDFReenvio($reserva, $boleta, $mesasInfo, $invitado);

        $logData = [
          'log_log' => "INFO: PDF generado exitosamente para boleta $id - UID: $customUid",
          'log_tipo' => 'REENVIO BOLETERIA - PDF GENERADO'
        ];
        $logModel->insert($logData);


        $qrsGenerados[] = [
          "boleta_id" => $id,
          "boleta_uid" => $customUid,
          "boleta_token" => $token,
          "boleta_numero_ticket" => $contadorTicket,
          "rutaQR" => $rutaQR,
          "email" => $invitado->invitadoReserva_correo_invitado ?? $reserva->reserva_correo,
          "nombre" => $invitado->invitadoReserva_nombre_invitado ?? $reserva->reserva_nombre_cliente . " " . $reserva->reserva_apellido_cliente,
          "telefono" => $reserva->reserva_telefono,
          "estado" => $boleta->boleta_estado,
          "documento" => $invitado->documento_invitado,
          "tipoBoleta" => $invitado->invitadoReserva_estado_invitado,
          "mesa_asignada" => $mesaAsignadaId,
          "tipo_envio" => "NUEVA" // Marcador para identificar que es una boleta nueva
        ];

        $invitadosNuevos[] = [
          'documento' => $invitado->documento_invitado,
          'nombre' => $invitado->invitadoReserva_nombre_invitado,
          'boleta_id' => $id
        ];

        $logData = [
          'log_log' => "NUEVO: Boleta creada para documento {$invitado->documento_invitado} en reserva $idReserva (Boleta ID: $id) - QR: $rutaQR",
          'log_tipo' => 'REENVIO BOLETERIA - NUEVA BOLETA'
        ];
        $logModel->insert($logData);
      }

      // Log resumen del proceso
      $totalNuevas = count($invitadosNuevos);
      $totalReenviadas = count($invitadosReenviados);
      $totalOmitidos = count($invitadosOmitidos);

      $logData = [
        'log_log' => "RESUMEN REENVÍO - Reserva: $idReserva, Nuevas boletas: $totalNuevas, Boletas reenviadas: $totalReenviadas, Omitidos: $totalOmitidos, Forzar reenvío: " . ($forzarReenvio == '1' ? 'SÍ' : 'NO'),
        'log_tipo' => 'REENVIO BOLETERIA - RESUMEN'
      ];
      $logModel->insert($logData);

      // Log detallado de arrays con print_r
      $logData = [
        'log_log' => "DETALLE QRs GENERADOS: " . print_r($qrsGenerados, true),
        'log_tipo' => 'REENVIO BOLETERIA - DETALLE QRs'
      ];
      $logModel->insert($logData);

      $logData = [
        'log_log' => "DETALLE INVITADOS NUEVOS: " . print_r($invitadosNuevos, true),
        'log_tipo' => 'REENVIO BOLETERIA - DETALLE NUEVOS'
      ];
      $logModel->insert($logData);

      $logData = [
        'log_log' => "DETALLE INVITADOS REENVIADOS: " . print_r($invitadosReenviados, true),
        'log_tipo' => 'REENVIO BOLETERIA - DETALLE REENVIADOS'
      ];
      $logModel->insert($logData);

      $logData = [
        'log_log' => "DETALLE INVITADOS OMITIDOS: " . print_r($invitadosOmitidos, true),
        'log_tipo' => 'REENVIO BOLETERIA - DETALLE OMITIDOS'
      ];
      $logModel->insert($logData);

      // Enviar correo si hay boletas (nuevas o reenviadas)
      if (!empty($qrsGenerados)) {
        $logData = [
          'log_log' => "INFO: Iniciando envío de email para reserva $idReserva - Total boletas a enviar: " . count($qrsGenerados) . ($idInvitado ? " (Invitado individual)" : ""),
          'log_tipo' => 'REENVIO BOLETERIA - EMAIL INICIADO'
        ];
        $logModel->insert($logData);

        $email = new Core_Model_Sendingemail($this->_view);
        $resultado = $email->generarCorreoBoleteriaNew($reserva, $qrsGenerados);

        if ($resultado) {
          $logData = [
            'log_log' => "SUCCESS: Email enviado exitosamente para reserva $idReserva" . ($idInvitado ? " (Invitado individual)" : ""),
            'log_tipo' => 'REENVIO BOLETERIA - EMAIL ENVIADO'
          ];
          $logModel->insert($logData);

          // Marcar como enviada si es toda la reserva, O si es el último invitado individual
          if (!$idInvitado) {
            // Caso: procesamiento de toda la reserva
            $reservasModel->editField($idReserva, 'reserva_boleteria_enviada', 1);
            $reservasModel->editField($idReserva, 'reserva_boleteria_reenviada', 1);
            $reservasModel->editField($idReserva, 'reserva_boleteria_reenviada_fecha', date('Y-m-d H:i:s'));
          } else {
            // Caso: procesamiento individual - verificar si es el último invitado
            $esUltimoInvitado = $this->verificarSiEsUltimoInvitadoSinBoleta($idReserva, $idInvitado);

            if ($esUltimoInvitado) {
              $logData = [
                'log_log' => "INFO: Este es el último invitado sin boleta para la reserva $idReserva. Marcando reserva como completada.",
                'log_tipo' => 'REENVIO BOLETERIA - ULTIMO INVITADO'
              ];
              $logModel->insert($logData);

              $reservasModel->editField($idReserva, 'reserva_boleteria_enviada', 1);
              $reservasModel->editField($idReserva, 'reserva_boleteria_reenviada', 1);
              $reservasModel->editField($idReserva, 'reserva_boleteria_reenviada_fecha', date('Y-m-d H:i:s'));
            }
          }

          if ($idInvitado) {
            $this->_view->mensaje = "Boleta generada y enviada exitosamente para el invitado seleccionado.";
          } elseif ($totalNuevas > 0 && $totalReenviadas > 0) {
            $this->_view->mensaje = "Boletería procesada correctamente: $totalNuevas nuevas boletas generadas y $totalReenviadas boletas reenviadas.";
          } elseif ($totalNuevas > 0) {
            $this->_view->mensaje = "Boletería generada correctamente: $totalNuevas nuevas boletas enviadas.";
          } elseif ($totalReenviadas > 0) {
            $this->_view->mensaje = "Boletería reenviada correctamente: $totalReenviadas boletas existentes reenviadas.";
          }
        } else {
          $logData = [
            'log_log' => "ERROR: Falló el envío de email para reserva $idReserva" . ($idInvitado ? " (Invitado individual)" : ""),
            'log_tipo' => 'REENVIO BOLETERIA - EMAIL FALLIDO'
          ];
          $logModel->insert($logData);

          $this->handleError("Error al enviar el correo de boletería.");
        }
      } else {
        if ($idInvitado) {
          $this->_view->mensaje = "No se generó boleta para el invitado seleccionado (posiblemente ya tiene una boleta existente).";
        } elseif ($forzarReenvio == '1') {
          $this->_view->mensaje = "No se encontraron boletas para enviar.";
        } else {
          $this->_view->mensaje = "No se generaron nuevas boletas. Todos los invitados ya tenían boletería. Use forzar_reenvio=1 para incluir boletas existentes.";
        }
      }

      // Pasar datos a la vista para mostrar resumen
      $this->_view->reserva = $reserva;
      $this->_view->invitadosNuevos = $invitadosNuevos;
      $this->_view->invitadosOmitidos = $invitadosOmitidos;
      $this->_view->invitadosReenviados = $invitadosReenviados; // Nueva categoría
      $this->_view->qrsGenerados = $qrsGenerados;
      $this->_view->forzarReenvio = $forzarReenvio;

      // Log final exitoso
      $logData = [
        'log_log' => "COMPLETADO: Proceso de boletería finalizado exitosamente para reserva $idReserva",
        'log_tipo' => 'REENVIO BOLETERIA - COMPLETADO'
      ];
      $logModel->insert($logData);
    } catch (Exception $e) {
      $logData = [
        'log_log' => addslashes("ERROR en reenvío de boletería: {$e->getMessage()}"),
        'log_tipo' => 'REENVIO BOLETERIA - ERROR'
      ];
      $logModel->insert($logData);

      $this->handleError("Error en el proceso:  {$e->getMessage()}");
    }
  }

  private function generarQRReenvio($uid, $token, $documento)
  {
    $logModel = new Administracion_Model_DbTable_Log();

    $logData = [
      'log_log' => "INICIO GENERACIÓN QR - UID: $uid, Token: $token",
      'log_tipo' => 'GENERACION QR - INICIO'
    ];
    $logModel->insert($logData);

    $textoQR = $documento;
    $rutaQR = "images_sales/qrs_news/{$uid}.png";

    $logData = [
      'log_log' => "GENERANDO QR - Texto: $textoQR, Ruta: $rutaQR",
      'log_tipo' => 'GENERACION QR - PROCESANDO'
    ];
    $logModel->insert($logData);

    QRcode::png($textoQR, $rutaQR, "Q", 5, 3);

    $logData = [
      'log_log' => "QR GENERADO EXITOSAMENTE - Ruta: $rutaQR, Archivo existe: " . (file_exists($rutaQR) ? 'SÍ' : 'NO'),
      'log_tipo' => 'GENERACION QR - COMPLETADO'
    ];
    $logModel->insert($logData);

    return $rutaQR;
  }

  private function generarPDFReenvio($reserva, $boleta, $mesasInfo, $ambiente, $invitado)
  {
    $logModel = new Administracion_Model_DbTable_Log();

    $logData = [
      'log_log' => "INICIO GENERACIÓN PDF - Boleta ID: {$boleta->boleta_id}, UID: {$boleta->boleta_uid}, Invitado: {$invitado->documento_invitado}",
      'log_tipo' => 'GENERACION PDF - INICIO'
    ];
    $logModel->insert($logData);

    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $ambienteModel = new Administracion_Model_DbTable_Ambientes();
    // error_log(print_r($mesasInfo,true));
    $evento = $eventoModel->getById(1); // ID del evento principal

    $this->_view->reserva = $reserva;
    $this->_view->boleta = $boleta;
    $this->_view->invitado = $invitado;
    $this->_view->mesasInfo = $mesasInfo;
    $this->_view->ambiente = $ambiente;

    $this->_view->evento = $evento;

    $pdf = new MYPDFNEWINFO('P', 'mm', 'A5', true, 'UTF-8', false);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(0);
    $pdf->SetAutoPageBreak(false, 0);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    $content = $this->_view->getRoutPHP('modules/page/Views/template/generarpdfnew.php');
    $pdf->writeHTML($content, true, false, true, false, '');

    $pdf->SetFont('helvetica', '', 10);

    ob_clean();

    $name = PDFS_PATH_NEWS . "boleta_cena_{$boleta->boleta_uid}.pdf";
    $pdf->SetTitle("Boleta cena {$boleta->boleta_uid}");

    $logData = [
      'log_log' => "GENERANDO PDF - Ruta: $name, Tamaño estimado: " . strlen($content) . " caracteres",
      'log_tipo' => 'GENERACION PDF - PROCESANDO'
    ];
    $logModel->insert($logData);

    $pdf->Output($name, 'F');

    $logData = [
      'log_log' => "PDF GENERADO EXITOSAMENTE - Ruta: $name, Archivo existe: " . (file_exists($name) ? 'SÍ' : 'NO') . ", Tamaño: " . (file_exists($name) ? filesize($name) : '0') . " bytes",
      'log_tipo' => 'GENERACION PDF - COMPLETADO'
    ];
    $logModel->insert($logData);
  }
  /**
   * Verifica si el invitado actual es el último que faltaba por generar boleta en la reserva
   * @param int $idReserva ID de la reserva
   * @param int $idInvitadoActual ID del invitado que se acaba de procesar
   * @return bool true si es el último invitado sin boleta, false en caso contrario
   */
  private function verificarSiEsUltimoInvitadoSinBoleta($idReserva, $idInvitadoActual)
  {
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
    $logModel = new Administracion_Model_DbTable_Log();

    // Obtener todos los invitados de la reserva con datos completos
    $todosLosInvitados = $invitadosModel->getList("reserva_id_reserva = '{$idReserva}'");

    $invitadosConDatosCompletos = [];
    foreach ($todosLosInvitados as $invitado) {
      $esIncompleto = (
        !$invitado->documento_invitado ||
        str_starts_with($invitado->invitadoReserva_nombre_invitado, 'Invitado') ||
        !$invitado->invitadoReserva_nombre_invitado
      );

      if (!$esIncompleto) {
        $invitadosConDatosCompletos[] = $invitado;
      }
    }

    $logData = [
      'log_log' => "VERIFICACION ULTIMO INVITADO - Reserva: $idReserva, Total invitados con datos completos: " . count($invitadosConDatosCompletos),
      'log_tipo' => 'VERIFICACION ULTIMO INVITADO'
    ];
    $logModel->insert($logData);

    // Verificar cuántos de estos invitados ya tienen boleta
    $invitadosConBoleta = 0;
    foreach ($invitadosConDatosCompletos as $invitado) {
      $existeBoleta = $boletasModel->getList("boleta_documento = '{$invitado->documento_invitado}' AND boleta_reserva_id = '{$idReserva}'", "");

      if ($existeBoleta && count($existeBoleta) > 0) {
        $invitadosConBoleta++;

        $logData = [
          'log_log' => "INVITADO CON BOLETA - ID: {$invitado->id_invitado}, Documento: {$invitado->documento_invitado}",
          'log_tipo' => 'VERIFICACION ULTIMO INVITADO'
        ];
        $logModel->insert($logData);
      } else {
        $logData = [
          'log_log' => "INVITADO SIN BOLETA - ID: {$invitado->id_invitado}, Documento: {$invitado->documento_invitado}",
          'log_tipo' => 'VERIFICACION ULTIMO INVITADO'
        ];
        $logModel->insert($logData);
      }
    }

    $totalInvitadosCompletos = count($invitadosConDatosCompletos);

    // Si todos los invitados con datos completos ya tienen boleta, entonces este era el último
    $esUltimo = ($invitadosConBoleta == $totalInvitadosCompletos);

    $logData = [
      'log_log' => "RESULTADO VERIFICACION - Reserva: $idReserva, Invitados completos: $totalInvitadosCompletos, Con boleta: $invitadosConBoleta, Es último: " . ($esUltimo ? 'SÍ' : 'NO'),
      'log_tipo' => 'VERIFICACION ULTIMO INVITADO'
    ];
    $logModel->insert($logData);

    return $esUltimo;
  }

  /**
   * Valida que los datos mínimos del invitado estén completos
   * @param object $invitado Objeto del invitado a validar
   * @return array ['valido' => bool, 'razon' => string]
   */
  private function validarDatosInvitado($invitado)
  {
    // Validar documento
    if (empty($invitado->documento_invitado) || trim($invitado->documento_invitado) == '') {
      return [
        'valido' => false,
        'razon' => 'Documento vacío o inválido'
      ];
    }

    // Validar nombre
    if (empty($invitado->invitadoReserva_nombre_invitado) || trim($invitado->invitadoReserva_nombre_invitado) == '') {
      return [
        'valido' => false,
        'razon' => 'Nombre vacío o inválido'
      ];
    }

    return [
      'valido' => true,
      'razon' => ''
    ];
  }



  public function listoParaEnviar($id)
  {
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $invitados = $invitadosModel->getListWithReserva("reserva_id_reserva = '$id'", "");
    $listo = 1;
    foreach ($invitados as $invitado) {
      if (!$invitado->documento_invitado) {
        $listo = 0;
        break;
      }
    }
    return $listo;
  }
  protected function handleError($message, $type = 'error')
  {
    // Detectar si es una llamada AJAX (para generación individual de boletas)
    $isAjax = (
      !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) || (
      isset($_SERVER['CONTENT_TYPE']) &&
      strpos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') !== false &&
      !empty($this->_getSanitizedParam('id_invitado'))
    );

    if ($isAjax) {
      // Para llamadas AJAX, responder con JSON
      $this->setLayout('blanco');
      header('Content-Type: application/json');
      http_response_code(400); // Bad Request
      echo json_encode(['error' => $message, 'type' => $type]);
      exit;
    }

    // Para llamadas normales, guardar en sesión y redirigir
    Session::getInstance()->set('flash_message', $message);
    Session::getInstance()->set('flash_type', $type);

    // Redirigir al index
    header('Location: ' . $this->route);
    exit;
  }
}
