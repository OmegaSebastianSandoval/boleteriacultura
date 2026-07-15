<?php

class Page_guestsController extends Page_mainController
{
  public $idEvento = 1;




  public function indexAction()
  {
    if (!Session::getInstance()->get('ncar')) {
      header('Location: /');
    }



    // Socio
    $socio = $this->consultarSocioSession();
    if (!$socio || !$socio->SBE_CODI) {
      $this->_view->error = 'No se pudo identificar al socio en sesión';
      return;
    }

    // Publicidad tipo popup (misma sección que el popup del index, pero el siguiente registro, no el primero)
    $publicidadModel = new Page_Model_DbTable_Publicidad();
    $popupsGuests = $publicidadModel->getList("publicidad_seccion='4' AND publicidad_estado=1", "");
    $this->_view->popupGuests = $popupsGuests[1] ?? null;

    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $evento = $eventoModel->getById($this->idEvento);
    $this->_view->menuHabilitado = (int) ($evento->evento_menu_habilitado ?? 1) === 1;

    $id = $this->_getDecryptedParam('id'); // opcional
    if ($id == 896) {
      $id = null;
    }

    $reservaModel = new Administracion_Model_DbTable_Reservas();
    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
    $invitadosReservaModel = new Administracion_Model_DbTable_Invitadosreservas();
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $ambientesModel = new Administracion_Model_DbTable_Ambientes();

    // Listado de reservas del socio
    $reservasSocio = $reservaModel->getList("reserva_documento = '{$socio->SBE_CODI}' AND id != '896'", 'id DESC');
    $listaReservas = [];
    if (is_countable($reservasSocio)) {
      foreach ($reservasSocio as $r) {
        $estado = (int) $r->reserva_estado;
        if (!in_array($estado, [2, 3, 11], true))
          continue; // activos
        $boletas = $boletasModel->getList("boleta_reserva_id = '{$r->id}'", '');
        $qrsCount = 0;
        if ($boletas) {
          foreach ($boletas as $b) {
            if ($b->boleta_uid)
              $qrsCount++;
          }
        }
        $invitadosRes = $invitadosReservaModel->getList("reserva_id_reserva = '{$r->id}' AND invitadoReserva_estado_invitado ='P' AND (documento_invitado = '' OR documento_invitado IS NULL)", '');
        $faltanInvitados = count($invitadosRes);
        $cantidadInvitados = (int) $r->reserva_total_personas;
        $facturaCompleta = $r->reserva_fact_nit && $r->reserva_fact_razon && $r->reserva_fact_mail && $r->reserva_fact_dire && $r->reserva_fact_tele;
        $nombreAmbiente = '';
        if ($r->reserva_mesa_id) {
          $mesa = $mesasModel->getById($r->reserva_mesa_id);
          if ($mesa) {
            $ambiente = $ambientesModel->getById($mesa->mesa_ambiente);
            if ($ambiente) {
              $nombreAmbiente = $ambiente->ambiente_nombre;
            }
          }
        }
        $listaReservas[] = (object) [
          'id' => $r->id,
          'nombre_ambiente' => $nombreAmbiente,
          'estado' => $estado,
          'estado_texto' => $this->mapEstadoReserva($estado),
          'total_personas' => (int) $r->reserva_total_personas,
          'boleteria_enviada' => (int) $r->reserva_boleteria_enviada,
          'qrs_generados' => $qrsCount,
          'boletas_esperadas' => (int) $r->reserva_total_personas,
          'faltan_invitados' => $faltanInvitados,
          'factura_completa' => (bool) $facturaCompleta,
          'puede_gestionar' => !(int) $r->reserva_boleteria_enviada,
          'puede_ver' => (int) $r->reserva_boleteria_reenviada === 1 || $qrsCount == $cantidadInvitados,
          'qr_anteriores' => (int) $r->reserva_boleteria_enviada  >= 1
        ];
      }
    }
    $this->_view->reservas = $listaReservas;
    // echo "<pre>";
    // print_r($this->_view->reservas);
    // echo "</pre>";
    // Si no hay id => mostrar listado
    if (!$id) {
      $this->_view->listado = true;
      return;
    }

    // Con id se trabaja la reserva concreta
    $reserva = $reservaModel->getById($id);
    if (!$reserva || $reserva->reserva_documento != $socio->SBE_CODI) {
      $this->_view->error = 'Reserva no encontrada o no pertenece al socio';
      $this->_view->listado = true;
      return;
    }

    // Si ya tiene boletas enviadas => redirigir a detalle de servicios (pasando id)
    if ((int) $reserva->reserva_boleteria_enviada === 1) {
      header("Location: /page/servicios/info?id=" . enc_id($reserva->id));
      return;
    }

    // Flujo de edición de invitados como antes
    $invitados = $invitadosReservaModel->getList("reserva_id_reserva = $id");
    $totalInvitados = count($invitados);
    $totalInvitadosReserva = (int) $reserva->reserva_total_personas;
    if ($totalInvitados !== $totalInvitadosReserva) {
      $this->_view->error = 'El número de invitados no coincide con el total de la reserva';
    }
    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
    $boletasReserva = $boletasModel->getList("boleta_reserva_id = '$id'", "");
    $boletasPorInvitado = [];
    foreach ($boletasReserva as $b) {
      if ($b->boleta_asignacion) $boletasPorInvitado[$b->boleta_asignacion] = $b;
    }
    foreach ($invitados as $invitado) {
      $invitado->principal = $invitado->documento_invitado == $reserva->reserva_documento;
      $invitado->boleta_enviada = isset($boletasPorInvitado[$invitado->id_invitado]);
    }
    $errorBoleta = Session::getInstance()->get('errorBoleteria');
    if ($errorBoleta) {
      $this->_view->errorBoleta = $errorBoleta;
      Session::getInstance()->set('errorBoleteria', null);
    }
    $nombreAmbiente = '';
    if ($reserva->reserva_mesa_id) {
      $mesaIds = array_map('trim', explode(',', $reserva->reserva_mesa_id));
      $mesa = $mesasModel->getById($mesaIds[0]);
      if ($mesa) {
        $ambiente = $ambientesModel->getById($mesa->mesa_ambiente);
        if ($ambiente) $nombreAmbiente = $ambiente->ambiente_nombre;
      }
    }

    $this->_view->reserva = $reserva;
    $this->_view->invitados = $invitados;
    $this->_view->listado = false;
    $this->_view->socio = $socio;
    $this->_view->nombreAmbiente = $nombreAmbiente;

    $terminosModel = new Administracion_Model_DbTable_Terminos();
    $this->_view->terminos = $terminosModel->getList("termino_estado = 1 AND termino_seccion = 2", "termino_id ASC");

    // $this->_view->mostrarModalExcel = $totalInvitados > 5;
    $this->_view->mostrarModalExcel = false;
  } // fin indexAction

  private function mapEstadoReserva($estado)
  {
    switch ($estado) {
      case 1:
        return 'Creada';
      case 2:
        return 'Pagada Acción';
      case 3:
        return 'Pago Aprobado';
      case 4:
        return 'Pago Pendiente';
      case 5:
        return 'Pago Fallido';
      case 6:
        return 'Pago Rechazado';
      case 7:
        return 'Pago Pendiente Sistema';
      case 8:
        return 'Cancelada';
      case 11:
        return 'Pago por datáfono';
      default:
        return 'Desconocido';
    }
  }

  public function editarinvitadosAction()
  {
    $this->setLayout('blanco');
    $reservaId = $this->_getDecryptedParam('reserva_id');
    $invitados = $_POST['invitados'] ?? [];
    $invitadosReservaModel = new Administracion_Model_DbTable_Invitadosreservas();

    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $evento = $eventoModel->getById($this->idEvento);
    $menuHabilitado = (int) ($evento->evento_menu_habilitado ?? 1) === 1;

    $actualizados = 0;
    foreach ($invitados as $id_invitado => $data) {
      $updateData = [
        'invitadoReserva_nombre_invitado' => $data['invitadoReserva_nombre_invitado'] ?? null,
        'invitadoReserva_apellido_invitado' => $data['invitadoReserva_apellido_invitado'] ?? null,
        'documento_invitado' => $data['documento_invitado'] ?? null,
        'invitadoReserva_correo_invitado' => $data['invitadoReserva_correo_invitado'] ?? null,
        'invitadoReserva_fecha_nacimiento' => $data['invitadoReserva_fecha_nacimiento'] ?? null,
        'invitadoReserva_telefono' => $data['invitadoReserva_telefono'] ?? null,
        'invitadoReserva_menu' => ($menuHabilitado && ($data['invitadoReserva_menu'] ?? 'normal') === 'vegetariano') ? 'vegetariano' : 'normal',
      ];
      $invitadosReservaModel->updateCamposEditables($updateData, $id_invitado);
      $actualizados++;
    }
    $this->registrarAceptacionTerminos($reservaId, 'registro_boletas');

    $this->_view->mensaje = "$actualizados invitados actualizados correctamente.";
    header("Location: /page/guests/index?id=" . enc_id($reservaId));
  }
  public function enviarAction()
  {
    $this->setLayout('blanco');
    $idReserva = $this->_getDecryptedParam("id");
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reservasInvitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $ambienteModel = new Administracion_Model_DbTable_Ambientes();
    $pisosModel = new Administracion_Model_DbTable_Pisos();
    $logModel = new Administracion_Model_DbTable_Log();

    $reserva = $reservasModel->getById($idReserva);
    if (!$reserva) {
      $this->_view->error = 'Reserva no encontrada.';
      return;
    }

    // Validar que la factura electrónica esté completa antes de generar boletas
    $facturaCompleta = $reserva->reserva_fact_nit && $reserva->reserva_fact_razon && $reserva->reserva_fact_mail && $reserva->reserva_fact_dire && $reserva->reserva_fact_tele;
    if (!$facturaCompleta) {
      Session::getInstance()->set('errorBoleteria', 'Debe diligenciar los datos de factura electrónica antes de generar las boletas.');
      header("Location: /page/guests/index?id=" . enc_id($idReserva));
      return;
    }
    $invitadosReserva = $reservasInvitadosModel->getList("reserva_id_reserva = '$idReserva'", "");

    $cantidadTotalReserva = $reserva->reserva_total_personas;
    $cantidadTotalInvitados = count($invitadosReserva);
    if ($cantidadTotalReserva != $cantidadTotalInvitados) {
      $this->_view->error = "La cantidad de invitados no coincide con la reserva.";
      exit;
    }

    // Paso 1: Dividir las mesas y obtener capacidades
    $mesaIds = array_map('trim', explode(',', $reserva->reserva_mesa_id));
    $mesasDisponibles = [];

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
      }
    }

    // Paso 2: Validar si hay suficiente capacidad total
    $capacidadTotal = array_sum(array_column($mesasDisponibles, 'capacidad'));

    if ($capacidadTotal < $cantidadTotalInvitados) {
      $this->_view->error = "La capacidad total de las mesas es menor al número de invitados.";
      exit;
    }
    $qrsGenerados = [];
    foreach ($invitadosReserva as $i => $invitado) {
      // Verifica si ya existe boleta para el documento
      // $existeBoleta = $boletasModel->getList(" boleta_documento = '{$invitado->documento_invitado}'", "");
      // if ($existeBoleta && count($existeBoleta) > 0) {

      //   Session::getInstance()->set('errorBoleteria', "Ya existe una boleta para el documento {$invitado->documento_invitado}.");
      //   header("Location: /page/guests/index?id=$idReserva&error=1");
      //   exit;
      // }
      // Buscar la primera mesa con espacio
      $mesaAsignadaId = null;
      foreach ($mesasDisponibles as $id => &$info) {
        if ($info['ocupados'] < $info['capacidad']) {
          $mesaAsignadaId = $id;
          $info['ocupados']++;
          break;
        }
      }

      if (!$mesaAsignadaId) {
        $this->_view->error = "No se pudo asignar una mesa a todos los invitados.";
        exit;
      }

      // Crear boleta
      $i++;
      $dataBoleta = [
        "boleta_reserva_id" => $idReserva,
        "boleta_evento_id" => 1, // ID del evento principal
        "boleta_numero_ticket" => $i,
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
      $token = base_convert($id, 10, 36);
      $yearMonth = date("Ym", strtotime($reserva->reserva_fecha));
      $customUid = "B-{$yearMonth}-" . str_pad($nextId, 7, "0", STR_PAD_LEFT);
      $baseString = "{$idReserva}-{$reserva->reserva_correo}-{$yearMonth}-{$nextId}";
      $token = substr(base_convert(hash('sha256', $baseString), 16, 36), 0, 12);

      $boletasModel->updateGeneratedQR($id, $customUid, $token, $this->idEvento, $invitado->id_invitado, $mesaAsignadaId);

      $boleta = $boletasModel->getById($id);
      $qrsGenerados[] = [
        "boleta_id" => $id,
        "boleta_uid" => $customUid,
        "boleta_token" => $token,
        "boleta_numero_ticket" => $i,
        "rutaQR" => $this->generarQR($customUid, $invitado->documento_invitado),
        "email" => $reserva->reserva_correo,
        "nombre" => $reserva->reserva_nombre_cliente . " " . $reserva->reserva_apellido_cliente,
        "telefono" => $reserva->reserva_telefono,
        "estado" => $boleta->boleta_estado,
        "documento" => $invitado->documento_invitado,
        "tipoBoleta" => $invitado->invitadoReserva_estado_invitado,
        "mesa_asignada" => $mesaAsignadaId
      ];

      // Obtener la mesa correspondiente para PDF
      $mesaSeleccionada = $mesasDisponibles[$mesaAsignadaId]['mesa'];
      $ambienteModel = new Administracion_Model_DbTable_Ambientes();
      $ambiente = $ambienteModel->getById($mesaSeleccionada->mesa_ambiente);

      $this->generarpdfs($reserva, $boleta, $mesaSeleccionada, $ambiente, $invitado);
    }

    $logModel = new Administracion_Model_DbTable_Log();
    $dataLog = array();
    $dataLog['log_log'] = print_r($qrsGenerados, true);
    $dataLog['log_tipo'] = 'QR GENERADOS';
    $logModel->insert($dataLog);
    $email = new Core_Model_Sendingemail($this->_view);
    $res = $email->generarCorreoBoleteria($reserva, $qrsGenerados);
    $reservasModel->editField($idReserva, 'reserva_boleteria_enviada', $res); // Cambia el estado de la reserva a 4 (Boletería enviada)
    $reservasModel->editField($idReserva, 'reserva_boleteria_reenviada', $res); // Cambia el estado de la reserva a 4 (Boletería enviada)
    header("Location: /page/guests/finish?id=" . enc_id($idReserva) . "&result=$res");
    return;
  }

  /**
   * Genera y envía la boleta de UN solo invitado, sin requerir que el resto
   * de la reserva esté completa. Reutiliza la misma lógica de asignación de
   * mesa y generación de PDF/QR que enviarAction(), pero para un invitado.
   * Si el invitado ya tenía boleta generada, simplemente la reenvía por correo.
   */
  public function enviarUnoAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $reservaId = $this->_getDecryptedParam('reserva_id');
    $idInvitado = $this->_getSanitizedParam('id_invitado'); // numérico (va en el body, no en URL)
    $nombre = trim($this->_getSanitizedParam('invitadoReserva_nombre_invitado'));
    $apellido = trim($this->_getSanitizedParam('invitadoReserva_apellido_invitado'));
    $documento = trim($this->_getSanitizedParam('documento_invitado'));
    $confirmar = trim($this->_getSanitizedParam('confirmar_documento'));
    $menu = trim($this->_getSanitizedParam('invitadoReserva_menu'));

    if (empty($reservaId) || empty($idInvitado)) {
      echo json_encode(['status' => false, 'message' => 'Datos inválidos.']);
      return;
    }

    $socio = $this->consultarSocioSession();
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reserva = $reservasModel->getById($reservaId);
    if (!$reserva || !$socio || $reserva->reserva_documento != $socio->SBE_CODI) {
      echo json_encode(['status' => false, 'message' => 'Reserva no encontrada o no autorizada.']);
      return;
    }

    $facturaCompleta = $reserva->reserva_fact_nit && $reserva->reserva_fact_razon && $reserva->reserva_fact_mail && $reserva->reserva_fact_dire && $reserva->reserva_fact_tele;
    if (!$facturaCompleta) {
      echo json_encode(['status' => false, 'message' => 'Debe diligenciar los datos de factura electrónica antes de enviar boletas.']);
      return;
    }

    $invitadosReservaModel = new Administracion_Model_DbTable_Invitadosreservas();
    $invitado = $invitadosReservaModel->getById($idInvitado);
    if (!$invitado || $invitado->reserva_id_reserva != $reservaId) {
      echo json_encode(['status' => false, 'message' => 'Invitado no encontrado en esta reserva.']);
      return;
    }

    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
    $boletaExistente = $boletasModel->getList("boleta_asignacion = '$idInvitado'", "")[0] ?? null;

    // Si aún no tiene boleta, validamos y guardamos sus datos antes de generarla
    if (!$boletaExistente) {
      if (strlen($nombre) < 3 || strlen($apellido) < 1) {
        echo json_encode(['status' => false, 'message' => 'Nombre y apellido deben tener mínimo 3 caracteres.']);
        return;
      }
      if (!preg_match('/^[A-Za-z0-9]{7,20}$/', $documento)) {
        echo json_encode(['status' => false, 'message' => 'Documento inválido (7 a 20 caracteres alfanuméricos).']);
        return;
      }
      if ($documento !== $confirmar) {
        echo json_encode(['status' => false, 'message' => 'Los documentos no coinciden.']);
        return;
      }
      // Solo bloquear si el documento ya está en OTRA reserva activa o pendiente de confirmación
      // (2,3,11 = confirmadas/pagadas; 4,7 = pago pendiente Placetopay/sistema; no cuenta reservas propias canceladas/antiguas)
      $enOtraReservaActiva = $invitadosReservaModel->getListWithReserva(
        "documento_invitado = '$documento' AND reserva_id_reserva != '$reservaId' AND reserva_estado IN (2,3,4,7,11)"
      );
      if ($enOtraReservaActiva) {
        echo json_encode(['status' => false, 'message' => 'El documento ya está registrado en otra reserva activa.']);
        return;
      }

      $eventoModel = new Administracion_Model_DbTable_Eventos();
      $evento = $eventoModel->getById($this->idEvento);
      $menuHabilitado = (int) ($evento->evento_menu_habilitado ?? 1) === 1;
      $menu = ($menuHabilitado && $menu === 'vegetariano') ? 'vegetariano' : 'normal';
      $updateData = [
        'invitadoReserva_nombre_invitado' => $nombre,
        'invitadoReserva_apellido_invitado' => $apellido,
        'documento_invitado' => $documento,
        'invitadoReserva_correo_invitado' => $invitado->invitadoReserva_correo_invitado,
        'invitadoReserva_fecha_nacimiento' => $invitado->invitadoReserva_fecha_nacimiento ?: '1990-01-01',
        'invitadoReserva_telefono' => $invitado->invitadoReserva_telefono,
        'invitadoReserva_menu' => $menu,
      ];
      $invitadosReservaModel->updateCamposEditables($updateData, $idInvitado);
      $invitado = $invitadosReservaModel->getById($idInvitado);
    }

    if (!$boletaExistente) {
      $mesasModel = new Administracion_Model_DbTable_Mesas();
      $ambienteModel = new Administracion_Model_DbTable_Ambientes();
      $pisosModel = new Administracion_Model_DbTable_Pisos();

      // Buscar la primera mesa/silla de la reserva con cupo disponible
      $mesaIds = array_map('trim', explode(',', $reserva->reserva_mesa_id));
      $mesaAsignadaId = null;
      $mesaSeleccionada = null;
      foreach ($mesaIds as $mesaId) {
        $mesa = $mesasModel->getById($mesaId);
        if (!$mesa) continue;
        $ocupados = count($boletasModel->getList("boleta_mesa = '$mesaId' AND boleta_reserva_id = '$reservaId'", ""));
        if ($ocupados < (int) $mesa->mesa_capacidad) {
          $mesaAsignadaId = $mesaId;
          $mesaSeleccionada = $mesa;
          break;
        }
      }
      if (!$mesaAsignadaId) {
        echo json_encode(['status' => false, 'message' => 'No hay capacidad disponible en las mesas asignadas a la reserva.']);
        return;
      }
      // Piso/ambiente para el PDF (mismo dato que enviarAction, aquí faltaba y dejaba
      // la línea de "Piso" en blanco en la boleta individual).
      $mesaSeleccionada->ambienteinfo = $ambienteModel->getById($mesaSeleccionada->mesa_ambiente);
      $mesaSeleccionada->pisoInfo = $mesaSeleccionada->ambienteinfo
        ? $pisosModel->getById($mesaSeleccionada->ambienteinfo->ambiente_piso)
        : null;

      // Número de ticket consistente con la posición del invitado dentro de la reserva
      $todosInvitados = $invitadosReservaModel->getList("reserva_id_reserva = '$reservaId'", "id_invitado ASC");
      $numeroTicket = 1;
      foreach ($todosInvitados as $idx => $inv) {
        if ($inv->id_invitado == $idInvitado) {
          $numeroTicket = $idx + 1;
          break;
        }
      }

      $dataBoleta = [
        "boleta_reserva_id" => $reservaId,
        "boleta_evento_id" => 1,
        "boleta_numero_ticket" => $numeroTicket,
        "boleta_uid" => "",
        "boleta_token" => "",
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
      ];
      $nextId = $boletasModel->getNextBoletaId();
      $id = $boletasModel->insert($dataBoleta);
      $yearMonth = date("Ym", strtotime($reserva->reserva_fecha));
      $customUid = "B-{$yearMonth}-" . str_pad($nextId, 7, "0", STR_PAD_LEFT);
      $baseString = "{$reservaId}-{$reserva->reserva_correo}-{$yearMonth}-{$nextId}";
      $token = substr(base_convert(hash('sha256', $baseString), 16, 36), 0, 12);

      $boletasModel->updateGeneratedQR($id, $customUid, $token, $this->idEvento, $invitado->id_invitado, $mesaAsignadaId);
      $boleta = $boletasModel->getById($id);

      // El QR debe generarse ANTES del PDF: la plantilla del PDF referencia el
      // archivo PNG del QR por su ruta, y si aún no existe en disco, TCPDF no
      // puede incrustarlo (deja esa zona del PDF en blanco).
      $rutaQR = $this->generarQR($boleta->boleta_uid, $invitado->documento_invitado);
      $ambiente = $ambienteModel->getById($mesaSeleccionada->mesa_ambiente);
      $this->generarpdfs($reserva, $boleta, $mesaSeleccionada, $ambiente, $invitado);
    } else {
      // La boleta ya existe: se regenera el PDF/QR (uid y token no cambian, es
      // idempotente) para que cualquier corrección a la plantilla se refleje al
      // reenviar, en vez de quedar reenviando para siempre el PDF viejo del disco.
      $boleta = $boletaExistente;
      $mesasModel = new Administracion_Model_DbTable_Mesas();
      $ambienteModel = new Administracion_Model_DbTable_Ambientes();
      $pisosModel = new Administracion_Model_DbTable_Pisos();

      // El QR debe generarse ANTES del PDF (ver comentario en la rama anterior).
      $rutaQR = $this->generarQR($boleta->boleta_uid, $invitado->documento_invitado);

      $mesaSeleccionada = $boleta->boleta_mesa ? $mesasModel->getById($boleta->boleta_mesa) : null;
      $ambiente = null;
      if ($mesaSeleccionada) {
        $mesaSeleccionada->ambienteinfo = $ambienteModel->getById($mesaSeleccionada->mesa_ambiente);
        $mesaSeleccionada->pisoInfo = $mesaSeleccionada->ambienteinfo
          ? $pisosModel->getById($mesaSeleccionada->ambienteinfo->ambiente_piso)
          : null;
        $ambiente = $ambienteModel->getById($mesaSeleccionada->mesa_ambiente);
        $this->generarpdfs($reserva, $boleta, $mesaSeleccionada, $ambiente, $invitado);
      }
    }

    $qrData = [
      "boleta_id" => $boleta->boleta_id,
      "boleta_uid" => $boleta->boleta_uid,
      "boleta_token" => $boleta->boleta_token,
      "boleta_numero_ticket" => $boleta->boleta_numero_ticket,
      "rutaQR" => $rutaQR,
      "email" => $reserva->reserva_correo,
      "nombre" => $reserva->reserva_nombre_cliente . " " . $reserva->reserva_apellido_cliente,
      "telefono" => $reserva->reserva_telefono,
      "estado" => $boleta->boleta_estado,
      "documento" => $invitado->documento_invitado,
      "tipoBoleta" => $invitado->invitadoReserva_estado_invitado,
      "mesa_asignada" => $boleta->boleta_mesa
    ];

    $email = new Core_Model_Sendingemail($this->_view);
    $res = $email->generarCorreoBoleteria($reserva, [$qrData]);

    $logModel = new Administracion_Model_DbTable_Log();
    $logModel->insert([
      'log_log' => "Boleta " . ($boletaExistente ? 're-enviada' : 'generada y enviada') . " individualmente - Invitado: {$invitado->id_invitado}, Documento: {$invitado->documento_invitado}, Reserva: $reservaId, Resultado email: " . ($res ? 'OK' : 'FALLO'),
      'log_tipo' => 'ENVIO_BOLETA_INDIVIDUAL'
    ]);

    echo json_encode([
      'status' => (bool) $res,
      'message' => $res ? 'Boleta enviada correctamente.' : 'Ocurrió un error al enviar el correo.',
      'reenviada' => (bool) $boletaExistente
    ]);
  }

  public function finishAction()
  {
    //$this->setLayout('blanco');
    $idReserva = $this->_getDecryptedParam("id");
    $result = $this->_getSanitizedParam("result");
    if ($result == 1) {
      $this->_view->mensaje = "Boletería enviada correctamente.";
    } else {
      $this->_view->error = "Error al enviar la boletería.";
    }
    $this->_view->idReserva = $idReserva;
  }
  public function generarQR($uid, $documento)
  {
    if (!class_exists('QRcode')) {
      return null; // No disponible
    }
    $textoQR = $documento;
    // Ruta absoluta: una ruta relativa depende del cwd del proceso PHP, que puede variar
    // según cómo el hosting invoque el script (rewrite, FPM, etc.), causando que el PNG
    // se escriba en un lugar distinto de donde el PDF/email luego lo busca.
    $rutaQR = PUBLIC_PATH . "images_sales/qrs_news/{$uid}.png";
    QRcode::png($textoQR, $rutaQR, "Q", 5, 3);
    $this->normalizarPngQR($rutaQR);
    return $rutaQR;
  }

  /**
   * phpqrcode genera PNGs de 1 bit por píxel (formato mínimo blanco/negro). El parser
   * nativo de PNG de TCPDF no maneja bien esa profundidad de bit y el QR queda en blanco
   * al embeberlo en el PDF, aunque el archivo exista y sea válido. Se recarga con GD y se
   * regraba como PNG truecolor estándar (mismo contenido visual, profundidad de 8 bits)
   * para que TCPDF lo embeba correctamente.
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
  public function generarQROLD($uid, $token)
  {
    if (!class_exists('QRcode')) {
      return null; // No disponible
    }
    $textoQR = RUTA . "/validacion?uid={$uid}&token={$token}";
    $rutaQR = "images_sales/qrs_news/{$uid}.png";
    QRcode::png($textoQR, $rutaQR, "Q", 5, 3);
    return $rutaQR;
  }

  public function facturaAction()
  {
    $this->setLayout('blanco');

    // Forzar respuesta JSON
    header('Content-Type: application/json');

    // Sanitizar y obtener parámetros
    $id = (string) $this->_getDecryptedParam('id');
    $nit = trim($this->_getSanitizedParam('nit'));
    $direccion = trim($this->_getSanitizedParam('direccion'));
    $telefono = trim($this->_getSanitizedParam('telefono'));
    $razon_social = trim($this->_getSanitizedParam('razon_social'));
    $correo_factura = trim($this->_getSanitizedParam('correo_factura'));

    // ===== VALIDACIONES =====

    // ID válido
    if (empty($id) || !ctype_digit($id) || $id <= 0) {
      echo json_encode(['status' => 'error', 'message' => 'ID de reserva inválido.']);
      return;
    }

    // NIT/Cédula (mínimo 4 y máximo 20 caracteres, solo letras, números y guiones)
    if (empty($nit) || !preg_match('/^[A-Za-z0-9\-]{4,20}$/', $nit)) {
      echo json_encode(['status' => 'error', 'message' => 'NIT/Cédula inválido. Solo letras, números y guiones, entre 4 y 20 caracteres.']);
      return;
    }

    // Razón social (mínimo 2 y máximo 100 caracteres, permite acentos y caracteres especiales básicos)
    if (empty($razon_social) || !preg_match('/^[\p{L}0-9\s\.\,\-]{2,100}$/u', $razon_social)) {
      echo json_encode(['status' => 'error', 'message' => 'Razón social inválida. Use solo letras, números, espacios y signos permitidos, entre 2 y 100 caracteres.']);
      return;
    }

    // Correo válido
    if (empty($correo_factura) || !filter_var($correo_factura, FILTER_VALIDATE_EMAIL)) {
      echo json_encode(['status' => 'error', 'message' => 'Correo electrónico inválido.']);
      return;
    }

    // Dirección (mínimo 5 y máximo 150 caracteres, permite letras, números, espacios y algunos signos básicos)
    if (empty($direccion) || !preg_match('/^[\p{L}0-9\s\#\-\.\,]{5,150}$/u', $direccion)) {
      echo json_encode(['status' => 'error', 'message' => 'Dirección inválida. Use entre 5 y 150 caracteres, con letras, números y signos permitidos.']);
      return;
    }

    // Teléfono (solo números, opcionalmente con prefijo +, entre 7 y 15 dígitos)
    if (empty($telefono) || !preg_match('/^\+?[0-9]{7,15}$/', $telefono)) {
      echo json_encode(['status' => 'error', 'message' => 'Teléfono inválido. Debe contener entre 7 y 15 dígitos, opcionalmente con prefijo +.']);
      return;
    }


    // ===== GUARDAR EN BD =====
    $reservasModel = new Administracion_Model_DbTable_Reservas();

    try {
      $reservasModel->editField($id, 'reserva_fact_nit', $nit);
      $reservasModel->editField($id, 'reserva_fact_razon', $razon_social);
      $reservasModel->editField($id, 'reserva_fact_mail', $correo_factura);
      $reservasModel->editField($id, 'reserva_fact_dire', $direccion);
      $reservasModel->editField($id, 'reserva_fact_tele', $telefono);

      echo json_encode([
        'status' => 'success',
        'message' => 'Datos de factura electrónica guardados correctamente.'
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'status' => 'error',
        'message' => 'Error al guardar los datos: ' . $e->getMessage()
      ]);
    }
  }

  public function generarpdfs($reserva, $boleta, $mesasInfo, $ambiente, $invitado)
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


  public function pdfviewAction()
  {
    $this->setLayout('blanco');




    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $ambienteModel = new Administracion_Model_DbTable_Ambientes();
    $evento = $eventoModel->getById(1);

    $reservaModel = new Administracion_Model_DbTable_Reservas();
    $reserva = $reservaModel->getById(1025);

    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
    $boleta = $boletasModel->getById(1563);

    $invitadosReservasModel = new Administracion_Model_DbTable_Invitadosreservas();
    $invitado = $invitadosReservasModel->getById($boleta->boleta_asignacion);

    $ambiente = $ambienteModel->getById(13);

    $mesaModel = new Administracion_Model_DbTable_Mesas();
    $mesasResult = $mesaModel->getMesasConDetalles("mesa_id = {$boleta->boleta_mesa}");
    $mesasInfo = $mesasResult[0];

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

    $pdf->Output($name, 'I');
  }

  // Acción AJAX para validar si la factura electrónica está completa para una reserva
  public function validarfacturaAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');
    $id = (string) $this->_getDecryptedParam('id');
    if (empty($id) || !ctype_digit($id) || $id <= 0) {
      echo json_encode(['status' => false, 'message' => 'ID de reserva inválido.']);
      return;
    }
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reserva = $reservasModel->getById($id);
    if (!$reserva) {
      echo json_encode(['status' => false, 'message' => 'Reserva no encontrada.']);
      return;
    }
    $facturaCompleta = $reserva->reserva_fact_nit && $reserva->reserva_fact_razon && $reserva->reserva_fact_mail && $reserva->reserva_fact_dire && $reserva->reserva_fact_tele;
    if ($facturaCompleta) {
      echo json_encode(['status' => true, 'message' => 'Factura electrónica completa.']);
    } else {
      echo json_encode(['status' => false, 'message' => 'Debe diligenciar los datos de factura electrónica antes de generar las boletas.']);
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

  /**
   * Registra la aceptación de términos y condiciones
   * @param int $reservaId ID de la reserva
   * @param string $tipo Tipo de aceptación: 'checkout', 'registro_boletas', etc.
   */
  private function registrarAceptacionTerminos($reservaId, $tipo = 'registro_boletas')
  {
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $terminosModel = new Administracion_Model_DbTable_Terminos();
    $aceptacionesModel = new Administracion_Model_DbTable_Terminosaceptaciones();
    $logModel = new Administracion_Model_DbTable_Log();

    $reserva = $reservasModel->getById($reservaId);
    if (!$reserva) {
      $logModel->insert([
        'log_log' => "ERROR: Reserva no encontrada - ID: $reservaId",
        'log_tipo' => 'ERROR_ACEPTACION_TERMINOS'
      ]);
      return false;
    }

    // Obtener términos según la sección (2 = registro de boletas)
    $seccion = ($tipo === 'registro_boletas') ? 2 : 1;
    $terminos = $terminosModel->getList("termino_estado = 1 AND termino_seccion = $seccion", "termino_id ASC");

    if (!$terminos || count($terminos) == 0) {
      $logModel->insert([
        'log_log' => "INFO: No hay términos activos para la sección $seccion",
        'log_tipo' => 'INFO_ACEPTACION_TERMINOS'
      ]);
      return false;
    }

    // Obtener socio de sesión
    $socio = $this->consultarSocioSession();

    // Detectar información del dispositivo
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
    $dispositivoInfo = $this->detectarDispositivo($userAgent);

    // Preparar datos adicionales
    $datosAdicionales = [
      'reserva_info' => [
        'id' => $reservaId,
        'total_personas' => $reserva->reserva_total_personas ?? 0,
        'total_pagar' => $reserva->reserva_total_pagar ?? 0,
        'metodo_pago' => $reserva->reserva_metodo_pago ?? '',
        'fecha_evento' => $reserva->reserva_fecha ?? ''
      ],
      'usuario_info' => [
        'ncar' => Session::getInstance()->get('ncar'),
        'mac_nume' => $socio->MAC_NUME ?? null,
        'tipo_usuario' => 'socio'
      ],
      'contexto' => [
        'seccion' => $seccion === 2 ? 'Registro de Boletas' : 'Checkout',
        'tipo_aceptacion' => $tipo,
        'url_origen' => $_SERVER['HTTP_REFERER'] ?? '',
        'timestamp' => date('Y-m-d H:i:s')
      ],
      'datos_factura' => [
        'nit' => $reserva->reserva_fact_nit ?? '',
        'razon_social' => $reserva->reserva_fact_razon ?? '',
        'direccion' => $reserva->reserva_fact_dire ?? '',
        'telefono' => $reserva->reserva_fact_tele ?? ''
      ]
    ];

    // Registrar cada término aceptado
    $terminosRegistrados = 0;
    foreach ($terminos as $termino) {
      $dataAceptacion = [
        'aceptacion_reserva_id' => $reservaId,
        'aceptacion_termino_id' => $termino->termino_id,
        'aceptacion_termino_titulo' => $termino->termino_titulo,
        'aceptacion_usuario_documento' => $reserva->reserva_documento,
        'aceptacion_usuario_carnet' => $reserva->reserva_numero_carnet ?? '',
        'aceptacion_usuario_nombre' => $reserva->reserva_nombre_cliente ?? '',
        'aceptacion_usuario_apellido' => $reserva->reserva_apellido_cliente ?? '',
        'aceptacion_usuario_email' => $reserva->reserva_correo ?? '',
        'aceptacion_ip' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        'aceptacion_user_agent' => $userAgent,
        'aceptacion_dispositivo' => $dispositivoInfo['tipo'],
        'aceptacion_navegador' => $dispositivoInfo['navegador'],
        'aceptacion_fecha' => date('Y-m-d H:i:s'),
        'aceptacion_datos_adicionales' => json_encode($datosAdicionales, JSON_UNESCAPED_UNICODE),
        'aceptacion_estado' => '1',
        'aceptacion_tipo' => $tipo
      ];

      try {
        $aceptacionId = $aceptacionesModel->insert($dataAceptacion);
        $terminosRegistrados++;

        // Log de auditoría exitoso
        $logModel->insert([
          'log_log' => json_encode([
            'aceptacion_id' => $aceptacionId,
            'termino_id' => $termino->termino_id,
            'termino_titulo' => $termino->termino_titulo,
            'reserva_id' => $reservaId,
            'tipo' => $tipo,
            'usuario' => $reserva->reserva_documento
          ], JSON_UNESCAPED_UNICODE),
          'log_tipo' => 'TERMINO_ACEPTADO_' . strtoupper($tipo)
        ]);
      } catch (Exception $e) {
        // Log de error si falla el registro
        $logModel->insert([
          'log_log' => json_encode([
            'error' => $e->getMessage(),
            'termino_id' => $termino->termino_id,
            'reserva_id' => $reservaId,
            'tipo' => $tipo
          ], JSON_UNESCAPED_UNICODE),
          'log_tipo' => 'ERROR_ACEPTACION_TERMINO_' . strtoupper($tipo)
        ]);
      }
    }

    // Log resumen
    $logModel->insert([
      'log_log' => "RESUMEN: $terminosRegistrados términos registrados para reserva $reservaId (tipo: $tipo, sección: $seccion)",
      'log_tipo' => 'RESUMEN_ACEPTACION_TERMINOS'
    ]);

    return $terminosRegistrados > 0;
  }

  /**
   * Detecta el tipo de dispositivo y navegador
   */
  private function detectarDispositivo($userAgent)
  {
    $dispositivo = 'Desktop';
    $navegador = 'Desconocido';

    // Detectar dispositivo
    if (preg_match('/mobile/i', $userAgent)) {
      $dispositivo = 'Mobile';
    } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
      $dispositivo = 'Tablet';
    }

    // Detectar navegador
    if (preg_match('/edg/i', $userAgent)) {
      $navegador = 'Edge';
    } elseif (preg_match('/chrome/i', $userAgent)) {
      $navegador = 'Chrome';
    } elseif (preg_match('/firefox/i', $userAgent)) {
      $navegador = 'Firefox';
    } elseif (preg_match('/safari/i', $userAgent)) {
      $navegador = 'Safari';
    } elseif (preg_match('/opera|opr/i', $userAgent)) {
      $navegador = 'Opera';
    }

    return [
      'tipo' => $dispositivo,
      'navegador' => $navegador
    ];
  }

  public function cargarExcelAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $reservaId = $this->_getDecryptedParam('reserva_id');
    $datosExcel = json_decode($_POST['datos_excel'], true);

    if (!$reservaId || !is_array($datosExcel)) {
      echo json_encode(['status' => false, 'message' => 'Datos inválidos.']);
      return;
    }

    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reserva = $reservasModel->getById($reservaId);
    if (!$reserva) {
      echo json_encode(['status' => false, 'message' => 'Reserva no encontrada.']);
      return;
    }

    $cantidadInvitados = (int) $reserva->reserva_total_personas;
    $invitadosModel = $invitadosReservasModel = new Administracion_Model_DbTable_Invitadosreservas();
    $invitadosConDatos = $invitadosModel->getList(" reserva_id_reserva = '$reservaId' AND (documento_invitado IS NOT NULL AND documento_invitado != '') ", "");
    $totalEsperado = $cantidadInvitados - count($invitadosConDatos);
    if (count($datosExcel) !== $totalEsperado) {
      echo json_encode(['status' => false, 'message' => "El Excel debe contener exactamente $totalEsperado registros."]);
      return;
    }

    $documentos = [];
    $errores = [];

    foreach ($datosExcel as $index => $fila) {
      $nombre = trim($fila['nombre'] ?? '');
      $apellido = trim($fila['apellido'] ?? '');
      $documento = trim($fila['documento'] ?? '');

      if (!$nombre || !$apellido || !$documento) {
        $errores[] = "Fila " . ($index + 1) . ": Campos incompletos.";
        continue;
      }

      if (!preg_match('/^[A-Za-z0-9]{7,20}$/', $documento)) {
        $errores[] = "Fila " . ($index + 1) . ": Documento inválido.";
        continue;
      }

      if (in_array($documento, $documentos)) {
        $errores[] = "Fila " . ($index + 1) . ": Documento duplicado en el Excel.";
        continue;
      }

      $invitadosReservasModel = new Administracion_Model_DbTable_Invitadosreservas();
      $existeOtra = $invitadosReservasModel->getListWithReserva("documento_invitado = '$documento' AND reserva_id_reserva != '$reservaId'
      AND reserva_estado IN (2,3,4,7,11)", "");
      if ($existeOtra) {
        $errores[] = "Fila " . ($index + 1) . ": Documento $documento ya registrado en otra reserva.";
        continue;
      }

      $documentos[] = $documento;
    }

    if ($errores) {
      echo json_encode(['status' => false, 'message' => 'Errores encontrados:', 'errores' => $errores]);
      return;
    }

    // Filtrar datos: quitar si documento ya existe en esta reserva
    $datosFiltrados = [];
    foreach ($datosExcel as $dato) {
      $doc = trim($dato['documento']);
      $existeEnEsta = $invitadosReservasModel->getList("documento_invitado = '$doc' AND reserva_id_reserva = '$reservaId'", "");
      if (!$existeEnEsta) {
        $datosFiltrados[] = $dato;
      }
    }

    // Si todo ok, devolver los datos filtrados
    echo json_encode(['status' => true, 'message' => 'Datos válidos.', 'datos' => $datosFiltrados]);
  }
}
