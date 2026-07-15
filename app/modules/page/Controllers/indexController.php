<?php

/**
 *
 */

class Page_indexController extends Page_mainController
{

  public function indexAction()
  {

    if (Session::getInstance()->get('ncar')) {
      header('Location: /page/evento/');
    }
    // $this->_view->banner = $this->template->banner(1);
    // $this->_view->contenido = $this->template->getContentseccion(1);
    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $eventoConfiguracion = $eventoModel->getById(1);
    $this->_view->eventoConfiguracion = $eventoConfiguracion;
    $fechaInicioReserva = $eventoConfiguracion->evento_fecha_apertura_reserva;
    $fechaCierreReserva = $eventoConfiguracion->evento_fecha_cierre_reserva;
    $fechaActual = date('Y-m-d H:i:s');


    $reservaAbierta = false;
    $reservaCerrada = false;

    if (
      strtotime($fechaActual) >= strtotime($fechaInicioReserva) &&
      strtotime($fechaActual) <= strtotime($fechaCierreReserva)
    ) {
      $reservaAbierta = true;
      $reservaCerrada = false;
    } elseif (strtotime($fechaActual) > strtotime($fechaCierreReserva)) {
      // La venta ya cerró
      $reservaAbierta = false;
      $reservaCerrada = true;
    } else {
      // Aún no ha comenzado la venta
      $reservaAbierta = false;
      $reservaCerrada = false;
    }

    $this->_view->reservaAbierta = $reservaAbierta;
    $this->_view->reservaCerrada = $reservaCerrada;
    $publicidadModel = new Page_Model_DbTable_Publicidad();

    $popup = $publicidadModel->getList("publicidad_seccion='4' AND publicidad_estado=1", "")[0];
    $this->_view->popup = $popup;
  }
  public function limpiarreservasAction()
  {
    error_reporting(E_ALL);
    $this->setLayout('blanco');
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $bloqueadosBeneficiariosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();
    $mesasBloqueadasModel = new Administracion_Model_DbTable_Mesasbloqueo();
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $logModel = new Administracion_Model_DbTable_Log();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $beneficiarioBloqueadosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();

    $fechaActual = date('Y-m-d H:i:s');

    // Log de inicio
    $logData = array();
    $logData['log_log'] = "Iniciando limpiarreservasAction a las $fechaActual";
    $logData['log_tipo'] = 'LIMPIAR RESERVAS - INICIO';
    $logModel->insert($logData);

    $fechaLimite = date('Y-m-d H:i:s', strtotime('-15 minutes'));

    $reservasVencidas = $reservasModel->getList(
      "reserva_fecha_cierre_reserva <= '$fechaLimite' AND reserva_estado = '1'",
      "reserva_fecha_cierre_reserva ASC"
    );

    if ($reservasVencidas && is_countable($reservasVencidas) && count($reservasVencidas) > 0) {
      foreach ($reservasVencidas as $reserva) {

        if ($reserva->reserva_estado != 2 || $reserva->reserva_estado != 3 || $reserva->reserva_estado != 4 || $reserva->reserva_estado != 7 || $reserva->reserva_estado != 11) {
          $reservasModel->editField(
            $reserva->id,
            'reserva_estado',
            8
          );
          //$reservasModel->deleteRegister($reserva->id);
        }


        $logData['reserva_id'] = $reserva->id;
        $logData['log_log'] = "Reserva vencida inactivada. ID: {$reserva->id}, Documento: {$reserva->reserva_documento}";
        $logData['log_tipo'] = 'LIMPIAR RESERVAS - RESERVA INACTIVADA';
        $logModel->insert($logData);

        if ($reserva->reserva_mesa_id) {
          //   $mesasModel->editField(
          //     $reserva->reserva_mesa_id,
          //     'mesa_estado',
          //     0
          //   );
          $mesasIds = array_map('trim', explode(',', $reserva->reserva_mesa_id));
          foreach ($mesasIds as $mesaId) {
            $mesasModel->editField(
              $mesaId,
              'mesa_estado',
              0
            );
            $logData['log_log'] = "Mesa liberada. Mesa ID: {$mesaId} para reserva ID: {$reserva->id}";
            $logData['log_tipo'] = 'LIMPIAR RESERVAS - MESA LIBERADA';

            $reservasModel->editField(
              $reserva->id,
              'reserva_mesa_id',
              0
            );
            $logData['log_log'] = "Mesa liberada. Mesa ID: {$reserva->reserva_mesa_id} para reserva ID: {$reserva->id}";
            $logData['log_tipo'] = 'LIMPIAR RESERVAS - MESA LIBERADA';
            $logModel->insert($logData);
          }
        }
        $mesasBloqueadasReservaInactiva = $mesasBloqueadasModel->getList("mesa_bloqueo_documento = '{$reserva->reserva_documento}' AND mesa_bloqueo_estado = '1'", "");
        if ($mesasBloqueadasReservaInactiva && is_countable($mesasBloqueadasReservaInactiva) && count($mesasBloqueadasReservaInactiva) > 0) {
          foreach ($mesasBloqueadasReservaInactiva as $mesaBloqueada) {
            $mesasBloqueadasModel->deleteRegister(
              $mesaBloqueada->mesa_bloqueo_id
            );
            $logData['log_log'] = "Mesa bloqueada eliminada. Mesa Bloqueo ID: {$mesaBloqueada->mesa_bloqueo_id} para documento: {$reserva->reserva_documento}";
            $logData['log_tipo'] = 'LIMPIAR RESERVAS - MESA BLOQUEADA ELIMINADA';
            $logModel->insert($logData);
          }
        }


        $beneficiariosBloqueadosReservaInactiva = $bloqueadosBeneficiariosModel->getList("beneficiario_bloqueo_por_asociado_documento = '{$reserva->reserva_documento}' AND beneficiario_bloqueo_estado = '1'", "");
        if ($beneficiariosBloqueadosReservaInactiva && is_countable($beneficiariosBloqueadosReservaInactiva) && count($beneficiariosBloqueadosReservaInactiva) > 0) {
          foreach ($beneficiariosBloqueadosReservaInactiva as $beneficiarioBloqueado) {
            $bloqueadosBeneficiariosModel->deleteRegister(
              $beneficiarioBloqueado->beneficiario_bloqueo_id
            );
            $logData['log_log'] = "Beneficiario bloqueado eliminado. Bloqueo ID: {$beneficiarioBloqueado->beneficiario_bloqueo_id} para documento: {$reserva->reserva_documento}";
            $logData['log_tipo'] = 'LIMPIAR RESERVAS - BENEFICIARIO BLOQUEADO ELIMINADO';
            $logModel->insert($logData);
          }
        }


        $invitadosReservas = $invitadosModel->getList("reserva_id_reserva = '{$reserva->id}'", "");
        if ($invitadosReservas && is_countable($invitadosReservas) && count($invitadosReservas) > 0) {
          foreach ($invitadosReservas as $invitado) {
            // Log antes de eliminar
            $this->logAuditoria('NO ELIMINAR INVITADO - INICIO', $reserva->id, [
              'invitado' => print_r($invitado, true)
            ]);
            //$invitadosModel->deleteRegister($invitado->id_invitado);
            // Log después de eliminar
            $this->logAuditoria('NO ELIMINAR INVITADO - FIN', $reserva->id, [
              'invitado' => print_r($invitado, true)
            ]);
          }
        }
      }
    }
    $fechaActual = date('Y-m-d H:i:s');
    $beneficiariosBloqueadosVencidos = $beneficiarioBloqueadosModel->getList("beneficiario_bloqueo_expiracion < '$fechaActual'", "");
    if ($beneficiariosBloqueadosVencidos && is_countable($beneficiariosBloqueadosVencidos) && count($beneficiariosBloqueadosVencidos) > 0) {
      foreach ($beneficiariosBloqueadosVencidos as $beneficiarioBloqueado) {
        $beneficiarioBloqueadosModel->deleteRegister($beneficiarioBloqueado->beneficiario_bloqueo_id);
        $logData['log_log'] = "Beneficiario bloqueado vencido eliminado. Bloqueo ID: {$beneficiarioBloqueado->beneficiario_bloqueo_id}";
        $logData['log_tipo'] = 'LIMPIAR RESERVAS - BENEFICIARIO BLOQUEADO VENCIDO ELIMINADO';
        $logModel->insert($logData);
      }
    }

    // Mesas bloqueadas vencidas (mesa_bloqueo_fecha_expiracion): a diferencia de los
    // beneficiarios, antes no había barrido directo por expiración, solo indirecto vía
    // reserva_documento cuando la reserva asociada vencía. Eso dejaba bloqueos huérfanos
    // marcando capacidades como "Agotado" para todos los usuarios indefinidamente.
    $mesasBloqueadasVencidas = $mesasBloqueadasModel->getList("mesa_bloqueo_estado = '1' AND mesa_bloqueo_fecha_expiracion < '$fechaActual'", "");
    if ($mesasBloqueadasVencidas && is_countable($mesasBloqueadasVencidas) && count($mesasBloqueadasVencidas) > 0) {
      foreach ($mesasBloqueadasVencidas as $mesaBloqueadaVencida) {
        $mesasBloqueadasModel->deleteRegister($mesaBloqueadaVencida->mesa_bloqueo_id);
        $logData['log_log'] = "Mesa bloqueada vencida eliminada. Bloqueo ID: {$mesaBloqueadaVencida->mesa_bloqueo_id}, Capacidad: {$mesaBloqueadaVencida->mesa_bloqueo_capacidad}";
        $logData['log_tipo'] = 'LIMPIAR RESERVAS - MESA BLOQUEADA VENCIDA ELIMINADA';
        $logModel->insert($logData);
      }
    }

    // Log de fin
    $logData['reserva_id'] = null;
    $logData['log_log'] = "Finalizó limpiarreservasAction a las " . date('Y-m-d H:i:s');
    $logData['log_tipo'] = 'LIMPIAR RESERVAS - FIN';
    $logModel->insert($logData);
  }

  public function pruebaenvioAction()
  {
    $this->setLayout('blanco');
    $emailModel = new Core_Model_Mail();
    $asunto = "PRUEBA DE ENVIO - FIESTA - NOGAL";
    $tabla = "<table>
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Edad</th>
          <th>Relación</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Juan Pérez</td>
          <td>30</td>
          <td>Amigo</td>
        </tr>
        <tr>
          <td>María López</td>
          <td>25</td>
          <td>Hermana</td>
        </tr>
      </tbody>
    </table>";

    $content = $tabla;

    $bccs = [
      "desarrollo8@omegawebsystems.com",
    ];

    $emailModel->getMail()->Subject = $asunto;
    $emailModel->getMail()->msgHTML($content);
    $emailModel->getMail()->AltBody = $content;
    $emailModel->getMail()->SMTPDebug = 1;

    foreach ($bccs as $bcc) {
      $emailModel->getMail()->addBCC($bcc);
    }
    //$emailModel->getMail()->addAddress($email);

    // Intentar enviar
    $enviado = $emailModel->sed();
    if (!$enviado) {
      // Si falla, reintentar con Gmail
      $mail = $emailModel->getMail();
      // Reconfigurar
      $mail->isSMTP();
      $mail->SMTPDebug = 2;
      $mail->SMTPSecure = "tls";
      $mail->Host = "smtp.office365.com'";
      $mail->Port = 587;
      $mail->SMTPAuth = true;
      $mail->Username = "cenadegala@clubelnogal.com";
      $mail->Password = "Cena2025*+";
      $mail->setFrom("cenadegala@clubelnogal.com", "Cena de Gala Club el Nogal");
      // Limpiar destinatarios y volver a agregarlos
      $mail->clearAddresses();
      $mail->clearBCCs();
      foreach ($bccs as $bcc) {
        $mail->addBCC($bcc);
      }
      //$mail->addAddress($email);
      $mail->Subject = $asunto;
      $mail->msgHTML($content);
      $mail->AltBody = $content;
      $enviado = $mail->send();
    }
    echo $emailModel->getMail()->ErrorInfo;
  }

  public function falloshijosmenoresAction()
  {
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $cosociosmenores = $invitadosModel->getMenores();
    // print_r($cosociosmenores);
    $this->_view->cosociosmenores = $cosociosmenores;
  }

  public function validarsesionAction()
  {
    $this->setLayout('blanco');
    $session = Session::getInstance()->get('sesion');
    if ($session) {
      $accionSesionesModel = new Administracion_Model_DbTable_Accionsesiones();
      $sesionActualizada = $accionSesionesModel->getById($session->accion_sesion_id);
      if ($sesionActualizada->accion_sesion_sesion_activa == 0) {
        echo json_encode(array('success' => true, 'mensaje' => 'La sesión ha sido cerrada por el administrador.', 'sesion_activa' => 'salir'));
        exit;
      } else {
        echo json_encode(array('success' => true, 'mensaje' => 'La sesión está activa.', 'sesion_activa' => 'activo'));
      }
    }
  }

  public function reenviarboleteriaAction()
  {
    $this->setLayout('blanco');

    // Obtener parámetros
    $idReserva = $this->_getSanitizedParam("id_reserva");
    $idInvitado = $this->_getSanitizedParam("id_invitado");
    $forzarReenvio = $this->_getSanitizedParam("forzar_reenvio"); // Nueva bandera: 1 = incluir todas las boletas

    // Validar que al menos uno de los parámetros esté presente
    if (empty($idReserva) && empty($idInvitado)) {
      $this->_view->error = "Debe proporcionar al menos el ID de la reserva o el ID del invitado.";
      return;
    }

    // Modelos necesarios
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $invitadosReservaModel = new Administracion_Model_DbTable_Invitadosreservas();
    $boletasModel = new Administracion_Model_DbTable_Boletas();
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $logModel = new Administracion_Model_DbTable_Log();
    $ambienteModel = new Administracion_Model_DbTable_Ambientes();

    try {
      // Si solo se proporciona ID del invitado, obtener la reserva
      if (empty($idReserva) && !empty($idInvitado)) {
        $invitado = $invitadosReservaModel->getById($idInvitado);
        if (!$invitado) {
          $this->_view->error = "Invitado no encontrado con ID: $idInvitado";
          return;
        }
        $idReserva = $invitado->reserva_id_reserva;
      }

      // Obtener datos de la reserva
      $reserva = $reservasModel->getById($idReserva);
      if (!$reserva) {
        $this->_view->error = "Reserva no encontrada con ID: $idReserva";
        return;
      }

      // Obtener todos los invitados de la reserva
      $invitadosReserva = $invitadosReservaModel->getList("reserva_id_reserva = '$idReserva'", "");

      if (empty($invitadosReserva)) {
        $this->_view->error = "No se encontraron invitados para la reserva ID: $idReserva";
        return;
      }

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

      foreach ($mesaIds as $mesaId) {
        $mesa = $mesasModel->getById($mesaId);

        if ($mesa) {
          $mesasDisponibles[$mesaId] = [
            'mesa' => $mesa,
            'capacidad' => (int) $mesa->mesa_capacidad,
            'ocupados' => 0
          ];
          $mesa->ambienteinfo = $ambienteModel->getById($mesa->mesa_ambiente);
          $mesasInfo[$mesaId] = $mesa;
        }
      }

      // Validar capacidad total de mesas
      $capacidadTotal = array_sum(array_column($mesasDisponibles, 'capacidad'));
      if ($capacidadTotal < $cantidadTotalInvitados) {
        $this->_view->error = "La capacidad total de las mesas ($capacidadTotal) es menor al número de invitados ($cantidadTotalInvitados).";
        return;
      }

      // VALIDACIÓN PREVIA: Verificar que TODOS los invitados tengan datos completos
      $invitadosConDatosIncompletos = [];
      foreach ($invitadosReserva as $invitado) {
        $validacion = $this->validarDatosInvitado($invitado);
        if (!$validacion['valido']) {
          $invitadosConDatosIncompletos[] = [
            'documento' => $invitado->documento_invitado ?? 'SIN DOCUMENTO',
            'nombre' => $invitado->invitadoReserva_nombre_invitado ?? 'SIN NOMBRE',
            'apellido' => $invitado->invitadoReserva_apellido_invitado ?? 'SIN APELLIDO',
            'razon' => $validacion['razon']
          ];
        }
      }

      // Si hay invitados con datos incompletos, no procesar nada
      if (!empty($invitadosConDatosIncompletos)) {
        $totalIncompletos = count($invitadosConDatosIncompletos);
        $this->_view->error = "No se puede procesar la boletería. Se encontraron $totalIncompletos invitado(s) con datos incompletos. Todos los invitados deben tener documento y nombre válidos.";
        $this->_view->invitadosConDatosIncompletos = $invitadosConDatosIncompletos;

        // Log del error
        $logData = [
          'log_log' => "ERROR: Proceso cancelado por datos incompletos en reserva $idReserva. Invitados afectados: $totalIncompletos",
          'log_tipo' => 'REENVIO BOLETERIA - DATOS INCOMPLETOS CRITICOS'
        ];
        $logModel->insert($logData);

        return;
      }

      $qrsGenerados = [];
      $invitadosOmitidos = [];
      $invitadosNuevos = [];
      $invitadosReenviados = []; // Nueva categoría para boletas existentes que se reenvían
      $contadorTicket = 0;

      foreach ($invitadosReserva as $invitado) {
        // Verificar si ya existe boleta para este documento e invitado
        $existeBoleta = $boletasModel->getList("boleta_documento = '{$invitado->documento_invitado}' AND boleta_reserva_id = '$idReserva'", "");

        if ($existeBoleta && count($existeBoleta) > 0) {
          // Si no se fuerza el reenvío, omitir
          if (!$forzarReenvio || $forzarReenvio != '1') {
            // Invitado ya tiene boleta, omitir
            $invitadosOmitidos[] = [
              'documento' => $invitado->documento_invitado,
              'nombre' => $invitado->invitadoReserva_nombre_invitado,
              'apellido' => $invitado->invitadoReserva_apellido_invitado,
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
              'apellido' => $invitado->invitadoReserva_apellido_invitado,
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
          $this->_view->error = "No se pudo asignar una mesa a todos los invitados nuevos.";
          return;
        }

        // Crear nueva boleta
        $contadorTicket++;
        $dataBoleta = [
          "boleta_reserva_id" => $idReserva,
          "boleta_numero_ticket" => $contadorTicket,
          "boleta_estado" => 1,
          "boleta_fecha_creacion" => date("Y-m-d H:i:s"),
          "boleta_documento" => $invitado->documento_invitado,
          "boleta_tipo_boleta" => $invitado->invitadoReserva_estado_invitado,
          "boleta_mesa_id" => $mesaAsignadaId
        ];

        $nextId = $boletasModel->getNextBoletaId();
        $id = $boletasModel->insert($dataBoleta);

        // Generar token único
        $yearMonth = date("Ym", strtotime($reserva->reserva_fecha));
        $customUid = "B-{$yearMonth}-" . str_pad($nextId, 7, "0", STR_PAD_LEFT);
        $baseString = "{$idReserva}-{$reserva->reserva_correo}-{$yearMonth}-{$nextId}";
        $token = substr(base_convert(hash('sha256', $baseString), 16, 36), 0, 12);

        $boletasModel->updateGeneratedQR($id, $customUid, $token, 1, $invitado->id_invitado);

        $boleta = $boletasModel->getById($id);

        // Generar QR y PDF
        $rutaQR = $this->generarQRReenvio($customUid, $token);
        // $this->generarPDFReenvio($reserva, $boleta, $mesasDisponibles[$mesaAsignadaId]['mesa'], $invitado);
        $this->generarPDFReenvio($reserva, $boleta, $mesasInfo, $invitado);


        $qrsGenerados[] = [
          "boleta_id" => $id,
          "boleta_uid" => $customUid,
          "boleta_token" => $token,
          "boleta_numero_ticket" => $contadorTicket,
          "rutaQR" => $rutaQR,
          "email" => $reserva->reserva_correo,
          "nombre" => $reserva->reserva_nombre_cliente . " " . $reserva->reserva_apellido_cliente,
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
          'apellido' => $invitado->invitadoReserva_apellido_invitado,
          'boleta_id' => $id
        ];

        $logData = [
          'log_log' => "NUEVO: Boleta creada para documento {$invitado->documento_invitado} en reserva $idReserva (Boleta ID: $id)",
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

      // Enviar correo si hay boletas (nuevas o reenviadas)
      if (!empty($qrsGenerados)) {
        $email = new Core_Model_Sendingemail($this->_view);
        // $resultado = $email->generarCorreoBoleteriatest($reserva, $qrsGenerados);

        // if ($resultado) {
        //   $reservasModel->editField($idReserva, 'reserva_boleteria_enviada', 1);

        //   if ($totalNuevas > 0 && $totalReenviadas > 0) {
        //     $this->_view->mensaje = "Boletería procesada correctamente: $totalNuevas nuevas boletas generadas y $totalReenviadas boletas reenviadas.";
        //   } elseif ($totalNuevas > 0) {
        //     $this->_view->mensaje = "Boletería generada correctamente: $totalNuevas nuevas boletas enviadas.";
        //   } elseif ($totalReenviadas > 0) {
        //     $this->_view->mensaje = "Boletería reenviada correctamente: $totalReenviadas boletas existentes reenviadas.";
        //   }
        // } else {
        //   $this->_view->error = "Error al enviar el correo de boletería.";
        // }
      } else {
        if ($forzarReenvio == '1') {
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
    } catch (Exception $e) {
      $logData = [
        'log_log' => "ERROR en reenvío de boletería: " . $e->getMessage(),
        'log_tipo' => 'REENVIO BOLETERIA - ERROR'
      ];
      $logModel->insert($logData);

      $this->_view->error = "Error en el proceso: " . $e->getMessage();
    }
  }

  private function generarQRReenvio($uid, $token)
  {
    if (APPLICATION_ENV == 'development') {
      $textoQR = RUTA . "/validacion?uid={$uid}&token={$token}";
    } else {
      $textoQR = RUTA . "/validacion?uid={$uid}&token={$token}";
    }
    // Ruta absoluta: la plantilla del PDF (generarpdfnew.php) lee el PNG desde
    // PUBLIC_PATH; con ruta relativa dependiente del cwd del proceso PHP podían
    // no coincidir y el QR quedar en blanco en el PDF.
    $rutaQR = PUBLIC_PATH . "images_sales/qrs_news/{$uid}.png";
    QRcode::png($textoQR, $rutaQR, "Q", 5, 3);
    $this->normalizarPngQR($rutaQR);
    return $rutaQR;
  }

  /**
   * phpqrcode genera PNGs de 1 bit por píxel; el parser nativo de PNG de TCPDF no maneja
   * bien esa profundidad y el QR queda en blanco al embeberlo, aunque el archivo exista.
   * Se regraba con GD como PNG truecolor estándar (mismo contenido, 8 bits) para TCPDF.
   */
  private function normalizarPngQR($ruta)
  {
    if (!file_exists($ruta) || !function_exists('imagecreatefrompng')) {
      return;
    }
    $img = @imagecreatefrompng($ruta);
    if (!$img) {
      return;
    }
    $w = imagesx($img);
    $h = imagesy($img);
    $normalizado = imagecreatetruecolor($w, $h);
    imagecopy($normalizado, $img, 0, 0, 0, 0, $w, $h);
    imagedestroy($img);
    imagepng($normalizado, $ruta);
    imagedestroy($normalizado);
  }

  private function generarPDFReenvio($reserva, $boleta, $mesasInfo, $invitado)
  {
    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $ambienteModel = new Administracion_Model_DbTable_Ambientes();


    $evento = $eventoModel->getById(1); // ID del evento principal

    $this->_view->reserva = $reserva;
    $this->_view->boleta = $boleta;
    $this->_view->invitado = $invitado;
    $this->_view->mesasInfo = $mesasInfo;

    $this->_view->evento = $evento;

    $pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(0);
    $pdf->SetAutoPageBreak(false, 0);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);

    $content = $this->_view->getRoutPHP('modules/page/Views/template/generarpdftest.php');
    $pdf->writeHTML($content, true, false, true, false, '');
    $pdf->SetFont('helvetica', '', 10);

    ob_clean();
    $name = PDFS_PATH_NEWS . "boleta_{$boleta->boleta_uid}.pdf";
    $pdf->SetTitle("Boleta {$boleta->boleta_uid}");
    $pdf->Output($name, 'F');
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

  public function heartbeatAction()
  {
    $this->setLayout('blanco');
    $socio = Session::getInstance()->get('socio');
    $session = Session::getInstance()->get('sesion');

    if ($session) {
      $accionSesionesModel = new Administracion_Model_DbTable_Accionsesiones();
      $sesionActualizada = $accionSesionesModel->getById($session->accion_sesion_id);
      $accionSesionesModel->editField($sesionActualizada->accion_sesion_id, 'accion_sesion_last_ping', date('Y-m-d H:i:s'));
    }
  }
}
