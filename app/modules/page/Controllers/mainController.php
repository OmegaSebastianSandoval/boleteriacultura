<?php

/**
 *
 */

class Page_mainController extends Controllers_Abstract
{

  public $template;
  public $socio;
  public $UMBRAL_MAX_PERSONAS;

  public $editarpage = 0;
  public $sesionInfo;

  public function init()
  {
    $this->liberarMesasVencidasSiCorresponde();
    $this->generarTokenCsrfLogin();
    $this->setLayout('page_page');
    $this->template = new Page_Model_Template_Template($this->_view);
    $infopageModel = new Page_Model_DbTable_Informacion();
    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $this->_view->socio_header = $this->socio = $this->consultarSocioSession();
    $evento = $eventoModel->getById(1);
    $this->_view->UMBRAL_MAX_PERSONAS = $this->UMBRAL_MAX_PERSONAS = $evento->evento_invitados_socio ?? 300;
    //print_r($this->_view->socio_header);
    $this->_view->sesionInfo = $this->sesionInfo = $this->consultarSesion();
    $informacion = $infopageModel->getById(1);
    $this->_view->reservaSesion = $this->consultarReserva();
    $this->_view->infopage = $informacion;
    $this->_view->verBoletas = $this->verBoletas();
    $this->_view->verComprar = $this->verComprar();

    $this->getLayout()->setData("meta_description", "$informacion->info_pagina_descripcion");
    $this->getLayout()->setData("meta_keywords", "$informacion->info_pagina_tags");
    $this->getLayout()->setData("scripts", "$informacion->info_pagina_scripts");

    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $evento = $eventoModel->getById(1);
    $fechaInicioReserva = $evento->evento_fecha_apertura_reserva;
    $fechaActual = date('Y-m-d H:i:s');
    if (strtotime($fechaActual) >= strtotime($fechaInicioReserva)) {
      $this->_view->reservaAbierta = true;
    } else {
      $this->_view->reservaAbierta = false;
    }
    $publicidadModel = new Page_Model_DbTable_Publicidad();
    $this->_view->botonesFlotantes = $publicidadModel->getList("publicidad_seccion='3' AND publicidad_estado='1'", "orden ASC");
    $this->getLayout()->setData("evento_imagenfondo", "$evento->evento_imagenfondo");
    $this->getLayout()->setData("evento_colorfondo", "$evento->evento_colorfondo");
    $botonesModel = new Page_Model_DbTable_Publicidad();
    $this->_view->botones = $botonesModel->getList("publicidad_seccion='3' AND publicidad_estado='1'", "orden ASC");

    $header = $this->_view->getRoutPHP('modules/page/Views/partials/header.php');
    $this->getLayout()->setData("header", $header);
    $enlaceModel = new Page_Model_DbTable_Enlace();
    $this->_view->enlaces = $enlaceModel->getList("", "orden ASC");
    $footer = $this->_view->getRoutPHP('modules/page/Views/partials/footer.php');
    $this->getLayout()->setData("footer", $footer);
    $adicionales = $this->_view->getRoutPHP('modules/page/Views/partials/adicionales.php');
    $this->getLayout()->setData("adicionales", $adicionales);
    $this->usuario();
  }

  /**
   * Libera mesas que quedaron bloqueadas (mesa_estado = 1) por más de 1 hora sin que
   * el usuario completara el pago (reserva_estado sigue en '1' = Creada, nunca llegó
   * a iniciar el pago). No toca NUNCA reservas en estados 2,3,4,7,11 (pago iniciado o
   * aprobado) porque la condición exige reserva_estado = '1' exactamente.
   *
   * No hay cron disponible en el hosting, así que esto se ejecuta como efecto
   * secundario de cualquier request al módulo page, pero con un límite de 1 corrida
   * cada 5 minutos (vía el log) para no penalizar cada petición con estas consultas.
   */
  private function liberarMesasVencidasSiCorresponde()
  {
    $logModel = new Administracion_Model_DbTable_Log();

    $ultimaEjecucion = $logModel->getListPages("log_tipo = 'LIMPIAR_MESAS_AUTO_INICIO'", "log_id DESC", 0, 1);
    if ($ultimaEjecucion && is_countable($ultimaEjecucion) && count($ultimaEjecucion) > 0) {
      if (strtotime($ultimaEjecucion[0]->log_fecha) > strtotime('-5 minutes')) {
        return;
      }
    }

    $logModel->insert([
      'log_log' => 'Iniciando limpieza automática de mesas bloqueadas por más de 1 hora',
      'log_tipo' => 'LIMPIAR_MESAS_AUTO_INICIO'
    ]);

    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $reservasModel = new Administracion_Model_DbTable_Reservas();

    $limiteFecha = date('Y-m-d H:i:s', strtotime('-1 hour'));
    $mesasVencidas = $mesasModel->getMesasBloqueadasVencidas($limiteFecha);

    if (!$mesasVencidas || !is_countable($mesasVencidas) || count($mesasVencidas) === 0) {
      return;
    }

    $reservasYaProcesadas = [];

    foreach ($mesasVencidas as $mesa) {
      // Solo reservas que NUNCA iniciaron el pago (reserva_estado = '1' exacto,
      // nunca 2/3/4/7/11) pueden ser canceladas por esta limpieza.
      $reservasConEstaMesa = $reservasModel->getList(
        "reserva_estado = '1' AND FIND_IN_SET('{$mesa->mesa_id}', reserva_mesa_id)",
        ""
      );

      if ($reservasConEstaMesa && is_countable($reservasConEstaMesa) && count($reservasConEstaMesa) > 0) {
        foreach ($reservasConEstaMesa as $reserva) {
          if (in_array($reserva->id, $reservasYaProcesadas)) {
            continue;
          }
          $reservasYaProcesadas[] = $reserva->id;

          $reservasModel->editField($reserva->id, 'reserva_estado', 8);

          $mesasDeEstaReserva = array_filter(array_map('trim', explode(',', $reserva->reserva_mesa_id)));
          foreach ($mesasDeEstaReserva as $idMesaLiberar) {
            $mesasModel->liberarMesaVencida($idMesaLiberar);
          }
          $reservasModel->editField($reserva->id, 'reserva_mesa_id', null);

          $logModel->insert([
            'log_log' => "Reserva {$reserva->id} (documento {$reserva->reserva_documento}) cancelada automáticamente: mesa bloqueada por más de 1 hora sin completar el pago. Mesas liberadas: {$reserva->reserva_mesa_id}",
            'log_tipo' => 'LIMPIAR_MESAS_AUTO_RESERVA_CANCELADA'
          ]);
        }
      } else {
        // Antes de tratarla como huérfana: si CUALQUIER reserva con pago iniciado o
        // aprobado (2,3,4,7,11) referencia esta mesa, NO se libera. Solo se limpia su
        // fecha de bloqueo para que deje de aparecer en futuros barridos (la mesa queda
        // ocupada permanentemente por esa compra).
        $reservasPagadasConEstaMesa = $reservasModel->getList(
          "reserva_estado IN ('2','3','4','7','11') AND FIND_IN_SET('{$mesa->mesa_id}', reserva_mesa_id)",
          ""
        );

        if ($reservasPagadasConEstaMesa && is_countable($reservasPagadasConEstaMesa) && count($reservasPagadasConEstaMesa) > 0) {
          $mesasModel->limpiarFechaBloqueo($mesa->mesa_id);
          $logModel->insert([
            'log_log' => "Mesa {$mesa->mesa_id} pertenece a una reserva con pago iniciado/aprobado; se conserva ocupada y se limpia su fecha de bloqueo.",
            'log_tipo' => 'LIMPIAR_MESAS_AUTO_MESA_PAGADA_PROTEGIDA'
          ]);
          continue;
        }

        // Mesa bloqueada sin ninguna reserva (ni creada ni pagada) que la referencie: huérfana real
        $mesasModel->liberarMesaVencida($mesa->mesa_id);
        $logModel->insert([
          'log_log' => "Mesa {$mesa->mesa_id} liberada por vencimiento (huérfana, sin reserva activa asociada)",
          'log_tipo' => 'LIMPIAR_MESAS_AUTO_MESA_HUERFANA'
        ]);
      }
    }
  }

  public function usuario()
  {
    $userModel = new Core_Model_DbTable_User();
    $user = $userModel->getById(Session::getInstance()->get("kt_login_id"));
    if ($user->user_id == 1) {
      $this->editarpage = 1;
    }
  }

  /**
   * Genera (una sola vez por sesión) el token CSRF que protege el formulario de login.
   * Se ejecuta en cada request del módulo page, por lo que el token siempre está
   * disponible en $_SESSION cuando se renderiza cualquier formulario de login.
   */
  private function generarTokenCsrfLogin()
  {
    if (!Session::getInstance()->get('csrf_login')) {
      try {
        $token = bin2hex(random_bytes(32));
      } catch (Exception $e) {
        // Fallback por si random_bytes no está disponible en el entorno
        $token = hash('sha256', uniqid((string) mt_rand(), true));
      }
      Session::getInstance()->set('csrf_login', $token);
    }
  }
  public function consultarReserva()
  {

    $reserva = Session::getInstance()->get('reserva');
    if ($reserva == null || !$reserva->id) {
      return null;
    }
    $reservaModel = new Administracion_Model_DbTable_Reservas();
    $reserva = $reservaModel->getById($reserva->id);
    return $reserva ?? null;
  }
  public function consultarSocioSession()
  {
    $socio = Session::getInstance()->get('socio');
    if (!$socio || $socio == null || !$socio->SBE_CODI) {
      $socio = $this->consultarSocio();
      Session::getInstance()->set('socio', $socio);
    }
    return $socio;
  }

  public function consultarBeneficiariosSession()
  {

    //TEST

    $socio = $this->consultarSocioSession();
    //return $this->consultarBeneficiarios($socio->MAC_NUME);

    $beneficiarios = Session::getInstance()->get('beneficiarios');

    // Fecha límite para ser mayor de 18 años
    $fechaLimite = new DateTime('-18 years');



    if ($beneficiarios && is_countable($beneficiarios) && count($beneficiarios) >= 1) {
      // Filtrar beneficiarios mayores de edad
      $beneficiariosMayores = array_filter($beneficiarios, function ($beneficiario) use ($fechaLimite, $socio) {
        $fechaNacimiento = new DateTime($beneficiario->SBE_FNAC->date);
        // Mayor de edad
        $esMayor = $fechaNacimiento <= $fechaLimite;

        // Es el socio logueado (mismo SBE_CODI)
        $esSocio = $beneficiario->SBE_CODI == $socio->SBE_CODI;

        return $esMayor || $esSocio;
      });

      return $beneficiariosMayores ?? [];
    }
    if (!$socio || !$socio->SBE_CODI) {
      return null;
    }
    $beneficiarios = $this->consultarBeneficiarios($socio->MAC_NUME);
    if (!$beneficiarios) {
      return [];
    }
    // Filtrar beneficiarios mayores de edad
    $beneficiariosMayores = array_filter($beneficiarios, function ($beneficiario) use ($fechaLimite, $socio) {
      $fechaNacimiento = new DateTime($beneficiario->SBE_FNAC->date);
      // Mayor de edad
      $esMayor = $fechaNacimiento <= $fechaLimite;

      // Es el socio logueado (mismo SBE_CODI)
      $esSocio = $beneficiario->SBE_CODI == $socio->SBE_CODI;

      return $esMayor || $esSocio;
    });


    Session::getInstance()->set('beneficiarios', $beneficiariosMayores);
    return $beneficiariosMayores;
  }

  public function consultarSesion()
  {
    // $socio = Session::getInstance()->get('socio');
    $session = Session::getInstance()->get('sesion');
    return $session;
  }
  public function consultarSocio()
  {

    // $codi = Session::getInstance()->get('codi');
    $ncar = Session::getInstance()->get('ncar');

    if (!$ncar)
      $ncar = $this->_getSanitizedParam('user'); // numero de carnet desde recuperar contraseña

    if (!$ncar)
      return null;

    $loginServiceUrl = URL_BASE . '/querys/buscarUsuario.php';

    // Datos a enviar al servicio externo
    $postData = http_build_query([
      'token' => $this->generarToken(), //tken que recibe de la base de
      // 'codi' => $codi,
      'ncar' => $ncar
    ]);

    $ch = curl_init($loginServiceUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }
  public function generarToken()
  {
    $loginServiceUrl = 'https://ev.clubelnogal.com/tokens/querys/consultar_token.php';

    // Datos a enviar al servicio externo
    $postData = http_build_query([
      'inputUsername' => 'webnogal', //tken que recibe de la base de
      'inputPassword' => 'nogal2023*'
    ]);

    $ch = curl_init($loginServiceUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL (token): ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    // return $response;
    return $response->token;
  }
  public function consultarBeneficiarios($mac_nume)
  {

    // $codi = Session::getInstance()->get('codi');
    // $ncar = Session::getInstance()->get('ncar');

    $loginServiceUrl = URL_BASE . '/querys/selectBeneficiarios.php';

    // Datos a enviar al servicio externo
    $postData = http_build_query([
      'token' => $this->generarToken(), //tken que recibe de la base de
      'mac_nume' => $mac_nume
    ]);

    $ch = curl_init($loginServiceUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }

  public function inactivarBeneficiariosBloqueados($documento)
  {
    if (!$documento) {
      return;
    }
    $beneficiariosBloqueadosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();
    $beneficiariosBloqueadosModel->inactivarBeneficiariosBloqueados($documento);
    // $beneficiariosBloqueados = $beneficiariosBloqueadosModel->getList("beneficiario_bloqueo_por_asociado_documento = '$documento' AND beneficiario_bloqueo_estado = 1", "");
    // if (!$beneficiariosBloqueados || !is_countable($beneficiariosBloqueados) || count($beneficiariosBloqueados) < 1) {
    //   return;
    // }

    // foreach ($beneficiariosBloqueados as $bloqueo) {
    //   $beneficiariosBloqueadosModel->editField($bloqueo->beneficiario_bloqueo_id, 'beneficiario_bloqueo_estado', 0);
    // }
  }

  public function inactivarMesasBloqueadas($documento)
  {
    if (!$documento) {
      return;
    }
    $mesasBloqueoModel = new Administracion_Model_DbTable_Mesasbloqueo();
    $mesasBloqueadas = $mesasBloqueoModel->getList("mesa_bloqueo_documento = '{$documento}' AND mesa_bloqueo_estado = 1", "");
    if (!$mesasBloqueadas || !is_countable($mesasBloqueadas) || count($mesasBloqueadas) < 1) {
      return;
    }
    foreach ($mesasBloqueadas as $mesaBloqueada) {
      // $mesasBloqueoModel->editField($mesaBloqueada->mesa_bloqueo_id, 'mesa_bloqueo_estado', 0);
      $mesasBloqueoModel->deleteRegister($mesaBloqueada->mesa_bloqueo_id);
    }
  }

  /**
   * Limpia/cancela una reserva de forma configurable
   * 
   * @param int $reservaId ID de la reserva a procesar
   * @param array $opciones Array de opciones para controlar qué operaciones realizar
   * @return array Resultado de la operación con detalles de lo que se ejecutó
   */
  public function limpiarReserva($reservaId, $opciones = [])
  {

    // Configuración por defecto
    $config = array_merge([
      'cambiar_estado_reserva' => false,           // Si cambiar el estado de la reserva
      'nuevo_estado_reserva' => 8,                // Nuevo estado (8 = cancelada por inactividad)
      'liberar_mesa' => true,                     // Si liberar la mesa
      'inactivar_beneficiarios_bloqueados' => true, // Si inactivar beneficiarios bloqueados
      'inactivar_mesas_bloqueadas' => true,       // Si inactivar mesas bloqueadas
      'eliminar_sesiones_activas' => false,        // Si eliminar sesiones activas del documento
      'eliminar_invitados' => true,               // Si eliminar invitados de la reserva
      'eliminar_reserva' => true,                 // Si eliminar completamente la reserva
      'validar_existencia' => true                // Si validar que la reserva existe antes de procesar
    ], $opciones);

    $resultado = [
      'success' => false,
      'message' => '',
      'operaciones_realizadas' => [],
      'errores' => []
    ];

    // Log inicial
    $logData = [
      'reserva_id' => $reservaId,
      'opciones' => $opciones,
      'config' => $config
    ];
    $logData['log_log'] = print_r($logData, true);
    $logData['log_tipo'] = 'LIMPIAR RESERVA - INICIO';
    $logModel = new Administracion_Model_DbTable_Log();
    $logModel->insert($logData);

    try {
      // Obtener la reserva
      $reservasModel = new Administracion_Model_DbTable_Reservas();
      $reserva = $reservasModel->getById($reservaId);
      if ($reserva->reserva_estado != 1) {
        return $resultado;
      }
      $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();

      // Obtener datos antes de limpiar
      $reservaAntes = $reservasModel->getById($reservaId);
      $invitadosAntes = $invitadosModel->getList("reserva_id_reserva = '$reservaId'", "");

      // Log de limpieza
      $this->logAuditoria('LIMPIEZA_RESERVA', $reservaId, [
        'estado_anterior' => $reservaAntes->reserva_estado ?? 'null',
        'mesa_id_anterior' => $reservaAntes->reserva_mesa_id ?? null,
        'invitados_antes' => json_encode($invitadosAntes),
        'observaciones' => 'Limpiando reserva incompleta o expirada'
      ]);
      // Validar existencia si está habilitado
      if ($config['validar_existencia'] && !$reserva) {
        $resultado['errores'][] = "Reserva con ID {$reservaId} no encontrada";
        $resultado['message'] = 'Reserva no encontrada';
        return $resultado;
      }

      if (!$reserva) {
        $resultado['errores'][] = "Reserva con ID {$reservaId} no existe";
        $resultado['message'] = 'Reserva no existe';
        return $resultado;
      }

      // 1. Cambiar estado de la reserva (opcional)
      if ($config['cambiar_estado_reserva']) {
        $reservasModel->editField($reservaId, 'reserva_estado', $config['nuevo_estado_reserva']);
        $resultado['operaciones_realizadas'][] = "Estado de reserva cambiado a {$config['nuevo_estado_reserva']}";

        // Log cambio de estado
        $logData = [
          'reserva_id' => $reservaId,
          'estado_anterior' => $reserva->reserva_estado ?? null,
          'estado_nuevo' => $config['nuevo_estado_reserva']
        ];
        $logData['log_log'] = print_r($logData, true);
        $logData['log_tipo'] = 'LIMPIAR RESERVA - CAMBIO ESTADO';
        $logModel = new Administracion_Model_DbTable_Log();
        $logModel->insert($logData);
      }

      // 2. Liberar mesa (opcional)
      if ($config['liberar_mesa'] && $reserva->reserva_mesa_id) {
        $mesasModel = new Administracion_Model_DbTable_Mesas();
        //$mesasModel->editField($reserva->reserva_mesa_id, 'mesa_estado', 0);
        if ($reserva->reserva_mesa_id) {
          $mesaIds = explode(',', $reserva->reserva_mesa_id); // Convertir en array
          foreach ($mesaIds as $idMesa) {
            $idMesa = trim($idMesa); // Eliminar espacios por si acaso
            if ($idMesa) {
              $mesasModel->editField($idMesa, 'mesa_estado', 0);
            }
          }
        }
        $resultado['operaciones_realizadas'][] = "Mesa ID {$reserva->reserva_mesa_id} liberada";

        // Log liberación de mesa
        $logData = [
          'reserva_id' => $reservaId,
          'mesas_liberadas' => $reserva->reserva_mesa_id
        ];
        $logData['log_log'] = print_r($logData, true);
        $logData['log_tipo'] = 'LIMPIAR RESERVA - LIBERAR MESAS';
        $logModel = new Administracion_Model_DbTable_Log();
        $logModel->insert($logData);
      }

      // 3. Inactivar beneficiarios bloqueados (opcional)
      if ($config['inactivar_beneficiarios_bloqueados'] && $reserva->reserva_documento) {
        $this->inactivarBeneficiariosBloqueados($reserva->reserva_documento);
        $resultado['operaciones_realizadas'][] = "Beneficiarios bloqueados inactivados para documento {$reserva->reserva_documento}";

        // Log inactivación beneficiarios
        $logData = [
          'reserva_id' => $reservaId,
          'documento' => $reserva->reserva_documento
        ];
        $logData['log_log'] = print_r($logData, true);
        $logData['log_tipo'] = 'LIMPIAR RESERVA - INACTIVAR BENEFICIARIOS';
        $logModel = new Administracion_Model_DbTable_Log();
        $logModel->insert($logData);
      }

      // 4. Inactivar mesas bloqueadas (opcional)
      if ($config['inactivar_mesas_bloqueadas'] && $reserva->reserva_documento) {
        $this->inactivarMesasBloqueadas($reserva->reserva_documento);
        $resultado['operaciones_realizadas'][] = "Mesas bloqueadas inactivadas para documento {$reserva->reserva_documento}";

        // Log inactivación mesas bloqueadas
        $logData = [
          'reserva_id' => $reservaId,
          'documento' => $reserva->reserva_documento
        ];
        $logData['log_log'] = print_r($logData, true);
        $logData['log_tipo'] = 'LIMPIAR RESERVA - INACTIVAR MESAS BLOQUEADAS';
        $logModel = new Administracion_Model_DbTable_Log();
        $logModel->insert($logData);
      }

      // 5. Eliminar sesiones activas (opcional)
      if ($config['eliminar_sesiones_activas'] && $reserva->reserva_documento) {
        $accionesSessionesModel = new Administracion_Model_DbTable_Accionsesiones();
        $secionesDocumento = $accionesSessionesModel->getList(
          "accion_sesion_documento_socio = '{$reserva->reserva_documento}' ",
          ""
        );
        $sesionesEliminadas = 0;
        foreach ($secionesDocumento as $sesion) {
          $accionesSessionesModel->deleteRegister($sesion->accion_sesion_id);
          $sesionesEliminadas++;
        }
        if ($sesionesEliminadas > 0) {
          $resultado['operaciones_realizadas'][] = "{$sesionesEliminadas} sesiones activas eliminadas";
        }

        // Log eliminación de sesiones
        $logData = [
          'reserva_id' => $reservaId,
          'documento' => $reserva->reserva_documento,
          'sesiones_eliminadas' => $sesionesEliminadas
        ];
        $logData['log_log'] = print_r($logData, true);
        $logData['log_tipo'] = 'LIMPIAR RESERVA - ELIMINAR SESIONES';
        $logModel = new Administracion_Model_DbTable_Log();
        $logModel->insert($logData);
      }

      // 6. Eliminar invitados (opcional)
      if ($config['eliminar_invitados']) {
        $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
        $invitadosModel->deleteInvitados($reservaId);
        $resultado['operaciones_realizadas'][] = "Invitados de la reserva eliminados";

        // Log eliminación de invitados
        $logData = [
          'reserva_id' => $reservaId
        ];
        $logData['log_log'] = print_r($logData, true);
        $logData['log_tipo'] = 'LIMPIAR RESERVA - ELIMINAR INVITADOS';
        $logModel = new Administracion_Model_DbTable_Log();
        $logModel->insert($logData);
      }

      // 7. Eliminar reserva completamente (opcional)
      if ($config['eliminar_reserva']) {
        $reservasModel->deleteRegister($reservaId);
        $resultado['operaciones_realizadas'][] = "Reserva ID {$reservaId} eliminada completamente";

        // Log eliminación de reserva
        $logData = [
          'reserva_id' => $reservaId,
          'reserva_documento' => $reserva->reserva_documento ?? null,
          'reserva_mesa_id' => $reserva->reserva_mesa_id ?? null
        ];
        $logData['log_log'] = print_r($logData, true);
        $logData['log_tipo'] = 'LIMPIAR RESERVA - ELIMINAR RESERVA';
        $logModel = new Administracion_Model_DbTable_Log();
        $logModel->insert($logData);
      }

      $resultado['success'] = true;
      $resultado['message'] = 'Reserva procesada exitosamente';

      // Log final exitoso
      $logData = [
        'reserva_id' => $reservaId,
        'resultado' => $resultado
      ];
      $logData['log_log'] = print_r($logData, true);
      $logData['log_tipo'] = 'LIMPIAR RESERVA - COMPLETADO EXITOSO';
      $logModel = new Administracion_Model_DbTable_Log();
      $logModel->insert($logData);
      // Log de finalización
      $this->logAuditoria('RESERVA_ELIMINADA', $reservaId, [
        'observaciones' => 'Reserva eliminada completamente'
      ]);
    } catch (Exception $e) {
      $resultado['errores'][] = "Error al procesar reserva: " . $e->getMessage();
      $resultado['message'] = 'Error al procesar la reserva';

      // Log de error
      $logData = [
        'reserva_id' => $reservaId,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ];
      $logData['log_log'] = print_r($logData, true);
      $logData['log_tipo'] = 'LIMPIAR RESERVA - ERROR';
      $logModel = new Administracion_Model_DbTable_Log();
      $logModel->insert($logData);
      $this->logAuditoria('ERROR_LIMPIEZA_RESERVA', $reservaId, [
        'observaciones' => 'Error al limpiar reserva: ' . $e->getMessage()
      ]);
    }

    return $resultado;
  }
  public function verBoletas()
  {
    $socio = $this->consultarSocioSession();
    if (!$socio || !$socio->SBE_CODI) {
      return false;
    }

    $reservaModel = new Administracion_Model_DbTable_Reservas();
    $existeReservaConfirmada = $reservaModel->getList(
      "reserva_documento = '{$socio->SBE_CODI}' AND (reserva_estado = 2 OR reserva_estado = 3 OR reserva_estado = 11)",
      ""
    );
    if (is_countable($existeReservaConfirmada) && count($existeReservaConfirmada) >= 1) {
      return true;
    }

    return false;
  }
  public function verBoletasOLD()
  {
    $socio = $this->consultarSocioSession();
    if (!$socio || !$socio->SBE_CODI) {
      return false;
    }

    $reservaModel = new Administracion_Model_DbTable_Reservas();
    $existeReservaConfirmada = $reservaModel->getList(
      "reserva_documento = '{$socio->SBE_CODI}' AND (reserva_estado = 2 OR reserva_estado = 3 OR reserva_estado = 11)",
      ""
    )[0] ?? null;

    // Si no existe reserva confirmada, retornar false
    if (!$existeReservaConfirmada || !$existeReservaConfirmada->id) {
      return false;
    }

    $cantidadInvitadosReserva = (int) $existeReservaConfirmada->reserva_total_personas;

    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $invitados = $invitadosModel->getList(
      "reserva_id_reserva = '{$existeReservaConfirmada->id}'",
      ""
    );

    $totalInvitados = is_countable($invitados) ? count($invitados) : 0;

    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
    $boletas = $boletasModel->getList(
      "boleta_reserva_id = '{$existeReservaConfirmada->id}'"
    );

    $cantidadBoletas = is_countable($boletas) ? count($boletas) : 0;

    // Validar que todas las cantidades sean mayores a 0 y que sean iguales
    if (
      $cantidadInvitadosReserva > 0 &&
      $totalInvitados > 0 &&
      $cantidadBoletas > 0 &&
      $cantidadBoletas === $cantidadInvitadosReserva &&
      $cantidadBoletas === $totalInvitados
    ) {
      return true;
    }

    return false;
  }

  public function logAuditoria($accion, $reservaId = null, $datosAdicionales = [])
  {
    try {
      $auditoriaModel = new Administracion_Model_DbTable_Reservasauditoria();

      // Obtener datos de sesión si existen
      $numeroCarnet = Session::getInstance()->get('ncar');
      $documentoSocio = $this->socio ? $this->socio->SBE_CODI : null;

      // Datos base del log
      $logData = [
        'reserva_id' => $reservaId,
        'numero_carnet' => $numeroCarnet,
        'documento_socio' => $documentoSocio,
        'accion' => $accion,
        'controlador' => 'eventoController',
        'metodo' => $_SERVER['PATH_INFO'] ?? $_SERVER['REQUEST_URI'] ?? 'unknown',
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        'url_completa' => $_SERVER['REQUEST_URI'] ?? null,
        'parametros_get' => json_encode($_GET),
        'parametros_post' => json_encode($_POST),
        'usuario_sistema' => $numeroCarnet,
        'fecha_creacion' => date('Y-m-d H:i:s')
      ];

      // Agregar datos específicos si se proporcionan
      if (!empty($datosAdicionales)) {
        $logData = array_merge($logData, $datosAdicionales);
      }

      // Convertir datos complejos a JSON
      if (isset($datosAdicionales['session_data'])) {
        $logData['session_data'] = json_encode($datosAdicionales['session_data']);
      }

      if (isset($datosAdicionales['datos_json'])) {
        $logData['datos_json'] = json_encode($datosAdicionales['datos_json']);
      }

      $auditoriaModel->insert($logData);
    } catch (Exception $e) {
      // Log del error pero no interrumpir el flujo
      error_log("Error en auditoría: " . $e->getMessage());
    }
  }
  public function verComprar()
  {
    $socio = $this->consultarSocioSession();
    if (!$socio || !$socio->SBE_CODI) {
      return false;
    }

    $reservaModel = new Administracion_Model_DbTable_Reservas();
    $existeReservaConfirmada = $reservaModel->getList(
      "reserva_documento = '{$socio->SBE_CODI}' AND (reserva_estado = 2 OR reserva_estado = 3 OR reserva_estado = 11)",
      ""
    );
    $total = 0;
    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $evento = $eventoModel->getById(1);
    $maximo = $evento->evento_invitados_socio ?? 10;
    if (is_countable($existeReservaConfirmada) && count($existeReservaConfirmada) >= 1) {
      foreach ($existeReservaConfirmada as $reserva) {
        $total += $reserva->reserva_total_personas;
      }
      if ($total >= $maximo) {
        return false;
      }
    }

    return true;
  }
}
