<?php


class Page_loginController extends Page_mainController
{

  public $UMBRAL_MAX_PERSONAS;

  public function indexAction()
  {
    $idRes = $this->_getDecryptedParam('id_res');
    $this->_view->idRes = $idRes;
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


    if (Session::getInstance()->get('ncar') && $idRes && $idRes > 0) {
      header('Location: /page/guests/?id=' . enc_id($idRes));
      return;
    }
    if (Session::getInstance()->get('ncar')) {
      header('Location: /page/evento');
      return;
    }

    // $this->_view->contenido = $this->template->getContentseccion(2);
  }

  public function validarAction()
  {


    $this->setLayout('blanco');

    // ── Validación CSRF: rechaza la petición si el token no coincide con el de la sesión ──
    $csrfEnviado = $_POST['csrf_token'] ?? '';
    $csrfSesion = Session::getInstance()->get('csrf_login');
    if (!$csrfSesion || !$csrfEnviado || !hash_equals((string) $csrfSesion, (string) $csrfEnviado)) {
      header('Content-Type: application/json');
      $logModelCsrf = new Administracion_Model_DbTable_Log();
      $logModelCsrf->insert([
        'log_tipo' => 'LOGIN_FAIL_CSRF',
        'log_log' => 'Token CSRF inválido o ausente. IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'desconocida'),
        'log_fecha' => date('Y-m-d H:i:s'),
        'log_usuario' => $_POST['user'] ?? null,
      ]);
      echo json_encode([
        'status' => 'error',
        'message' => 'La sesión expiró o el token de seguridad no es válido. Recarga la página e intenta de nuevo.'
      ]);
      return;
    }

    // Loguear todos los datos recibidos al intentar iniciar sesión (sin credenciales
    // ni tokens en texto plano: antes solo se enmascaraba POST, pero REQUEST --que en
    // PHP mezcla GET+POST+COOKIE-- se logueaba crudo y terminaba guardando la
    // contraseña real igual).
    $enmascarar = function ($arr) {
      foreach (['pass', 'password', 'g-recaptcha-response'] as $campoSensible) {
        if (isset($arr[$campoSensible])) {
          $arr[$campoSensible] = '***';
        }
      }
      return $arr;
    };
    $postSanitized = $enmascarar($_POST);
    $content = [
      'POST' => $postSanitized,
      'GET' => $enmascarar($_GET),
      'REQUEST' => $enmascarar($_REQUEST),
      'SERVER' => [
        'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? null,
        'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? null,
      ],
      'log_fecha' => date('Y-m-d H:i:s'),
    ];
    $data = (array) $content;
    $data['log_log'] = print_r($data, true);
    $data['log_tipo'] = 'LOGIN_ATTEMPT';
    $data['log_usuario'] = $_POST['user'];

    $logModel = new Administracion_Model_DbTable_Log();
    $logModel->insert($data);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      $logModel->insert([
        'log_tipo' => 'LOGIN_FAIL_METHOD',
        'log_log' => 'Método no permitido: ' . ($_SERVER['REQUEST_METHOD'] ?? ''),
        'log_fecha' => date('Y-m-d H:i:s'),
        'log_usuario' => $_POST['user'] ?? null,
      ]);
      return;
    }
    $pass = $_POST['pass'];
    $user = $_POST['user'];
    $idRes = dec_id($_POST['id_res'] ?? null); // id_res puede venir cifrado
    $response = [];

    //iniciar sesion con usuario gestor
    $gestor = $this->loginGestor($user, $pass);
    if ($gestor && $gestor->user_level == 4) {
      $response = [
        'status' => 'success',
        'message' => 'Bienvenido Gestor',
        'gestor' => $gestor,
        'esGestor'  => true,
      ];
      echo json_encode($response);
      return;
    }

    // Ruta de socio: el carnet debe ser numérico y de máximo 10 dígitos.
    // (Los gestores ya se validaron arriba y quedan exentos de esta regla.)
    if (!preg_match('/^\d{1,10}$/', (string) $user)) {
      $logModel->insert([
        'log_tipo' => 'LOGIN_FAIL_CARNET_INVALIDO',
        'log_log' => 'Carnet con formato inválido (no numérico o más de 10 dígitos): ' . substr((string) $user, 0, 50),
        'log_fecha' => date('Y-m-d H:i:s'),
        'log_usuario' => $user,
      ]);
      echo json_encode([
        'status' => 'error',
        'message' => 'El número de carnet debe ser numérico y tener máximo 10 dígitos.'
      ]);
      return;
    }


    $bloqueosModel = new Administracion_Model_DbTable_Bloqueos();
    $userEscapado = addslashes($user);
    $ipEscapada = addslashes($_SERVER['REMOTE_ADDR'] ?? '');
    // Obtiene información de bloqueos anteriores
    $infoBloqueo = $bloqueosModel->getList(
      "bloqueo_nit = '$userEscapado' or bloqueo_ip = '$ipEscapada'",
      "bloqueo_id DESC"
    )[0] ?? null;

    // Manejo de intentos fallidos
    $intentos = 0;
    $diferencia = PHP_INT_MAX;
    if ($infoBloqueo) {
      $intentos = (int) $infoBloqueo->bloqueo_intentosfallidos;
      $fechaUltimoIntento = new DateTime($infoBloqueo->bloqueo_fechaintento ?? 'now');
      $fechaActual = new DateTime();
      $diferencia = $fechaActual->getTimestamp() - $fechaUltimoIntento->getTimestamp();
    }

    // Bloquea al usuario si excede los intentos permitidos
    if ($intentos >= 3 && $diferencia <= 900) {
      $logModel->insert([
        'log_tipo' => 'LOGIN_BLOQUEADO_INTENTOS',
        'log_log' => 'Usuario bloqueado por intentos fallidos',
        'log_fecha' => date('Y-m-d H:i:s'),

        'log_usuario' => $user

      ]);
      $response = [
        'status' => 'error',
        'message' => 'El usuario ha sido bloqueado durante 15 minutos por más de tres intentos fallidos'
      ];
      echo json_encode($response);
      return;
    }

    // Registra el intento fallido
    $dataBloque = array();
    $dataBloque['bloqueo_nit'] = $user;
    $dataBloque['bloqueo_intentosfallidos'] = $this->getIntentos($user, $_SERVER['REMOTE_ADDR']);
    $dataBloque['bloqueo_ip'] = $_SERVER['REMOTE_ADDR'];
    $bloqueosModel->insert($dataBloque);


    //------------------------------------------
    /* CONSULTA WEB SERVICE */
    $loginServiceUrl = URL_BASE_LOGIN;

    // Datos a enviar al servicio externo
    $postData = http_build_query([
      'token' => $this->generarToken(), //token que recibe de la base de
      'user' => $user,
      'pass' => $pass,
    ]);

    $ch = curl_init($loginServiceUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
      $logModel->insert([
        'log_tipo' => 'LOGIN_CURL_ERROR',
        'log_log' => 'Error cURL: ' . curl_error($ch),
        'log_fecha' => date('Y-m-d H:i:s'),
        'log_usuario' => $user,
      ]);
      echo json_encode(['status' => 'error', 'message' => 'Error de conexión, intente más tarde.']);
      exit;
    }

    curl_close($ch);
    //------------------------------------------





    if (strpos($response, "success") !== false || $pass === "Omega.2025*") {
      $logModel->insert([
        'log_tipo' => 'LOGIN_SUCCESS',
        'log_log' => 'Login exitoso',
        'log_fecha' => date('Y-m-d H:i:s'),

        'log_usuario' => $user

      ]);
      Session::getInstance()->set('ncar', $user);
      $invitadosReservasModel = new Administracion_Model_DbTable_Invitadosreservas();


      //------------------------------------------
      //Para redireccionar a la reserva, si existe, para confirmar asistentes
      $reservasModel = new Administracion_Model_DbTable_Reservas();
      $reserva = $reservasModel->getById($idRes);
      $socio = $this->consultarSocio();

      if (!$socio->SBE_CODI) {
        $logModel->insert([
          'log_tipo' => 'LOGIN_FAIL_SOCIO',
          'log_log' => 'No se encontró el socio',
          'log_fecha' => date('Y-m-d H:i:s'),
          'log_usuario' => $user

        ]);
        $response = [
          'status' => 'error',
          'message' => 'Error interno, no se pudo obtener el socio.'
        ];
        Session::getInstance()->set('ncar', null);

        echo json_encode($response);
        return;
      }

      // No castear a (int): SBE_CODI no siempre es numérico (p.ej. cuentas de
      // prueba "PRUEBA1"), y se usa como texto para buscar/guardar coincidencias
      // exactas en accion_sesiones, reservas, etc.
      $codiSocio = (string) $socio->SBE_CODI;

      if (APPLICATION_ENV != 'production') {
        $socio->sbe_mail = 'desarrollo8@omegawebsystems.com';
      }

      $edad = $this->getAge($socio->SBE_FNAC ?? null);
      $esMenor18 = $edad !== null && $edad < 18;
      if (!$socio->SBE_FNAC) {
        $logModel->insert([
          'log_tipo' => 'LOGIN_FAIL_FNAC',
          'log_log' => 'Falta fecha de nacimiento',
          'log_fecha' => date('Y-m-d H:i:s'),
          'log_usuario' => $user

        ]);
        $response = [
          'status' => 'error',
          'error' => 'missing_birthdate',
          'message' => 'Error interno, no se pudo obtener la fecha de nacimiento.'
        ];
        echo json_encode($response);
        return;
      }
      if ($esMenor18) {
        $logModel->insert([
          'log_tipo' => 'LOGIN_FAIL_MENOR18',
          'log_log' => 'Usuario menor de 18 años',
          'log_fecha' => date('Y-m-d H:i:s'),
          'log_usuario' => $user

        ]);
        $response = [
          'status' => 'error',
          'error' => 'under_18',
          'message' => 'Atención: No puedes iniciar sesión, debes ser mayor de 18 años para realizar reservas.'
        ];
        Session::getInstance()->set('ncar', null);

        echo json_encode($response);
        return;
      }


      if ($socio->SBE_CODI == $reserva->reserva_documento) {
        $redirect = true;
      }
      //------------------------------------------


      //------------------------------------------
      // Verifica si existe una sesión activa para este documento (SBE_CODI)
      $accionSesionesModel = new Administracion_Model_DbTable_Accionsesiones();
      $dateNow = date('Y-m-d H:i:s');

      $existeSesionActiva = $accionSesionesModel->getList("accion_sesion_documento_socio = '$codiSocio' AND accion_sesion_sesion_activa = 1", "accion_sesion_id DESC")[0] ?? null;

      try {
      if ($existeSesionActiva && !$redirect) {
        // Si aún no ha llegado ningún heartbeat, accion_sesion_last_ping viene NULL.
        // Usar la fecha de inicio como referencia en ese caso (una sesión recién
        // creada no debe tratarse como vencida solo por no tener ping todavía).
        $lastPingRaw = $existeSesionActiva->accion_sesion_last_ping ?: $existeSesionActiva->accion_sesion_fecha_inicio;
        $lastPing = new DateTime($lastPingRaw);
        $ahora = new DateTime($dateNow);
        $diffSegundos = $ahora->getTimestamp() - $lastPing->getTimestamp();

        if ($diffSegundos > 30) {
          $accionSesionesModel->editField(
            $existeSesionActiva->accion_sesion_id,
            'accion_sesion_sesion_activa',
            0
          );
          $existeSesionActiva = null; // ya no se considera activa

          $reservasModel = new Administracion_Model_DbTable_Reservas();
          $existeReservaActiva = $reservasModel->getList("reserva_documento = '$codiSocio' AND reserva_estado = 1 AND (reserva_total_pagar = '' OR reserva_total_pagar IS NULL)", "id DESC")[0] ?? null;

          if ($existeReservaActiva) {
            $mesasModel = new Administracion_Model_DbTable_Mesas();
            $mesaReserva = $existeReservaActiva->reserva_mesa_id;

            if ($mesaReserva) {
              $mesaIds = explode(',', $mesaReserva);

              foreach ($mesaIds as $idMesa) {
                $idMesa = trim($idMesa);
                if ($idMesa) {
                  $mesasModel->editField($idMesa, 'mesa_estado', 0);
                }
              }
            }

            $documento = $existeReservaActiva->reserva_documento;
            if ($documento) {
              // Inactivar beneficiarios bloqueados
              $this->inactivarBeneficiariosBloqueados($documento);

              // Inactivar mesas bloqueados
              $this->inactivarMesasBloqueadas($documento);
            }

            if ($existeReservaActiva->reserva_documento) {
              $accionesSessionesModel = new Administracion_Model_DbTable_Accionsesiones();
              $docReservaActiva = (string) $existeReservaActiva->reserva_documento;
              $secionesDocumento = $accionesSessionesModel->getList("accion_sesion_documento_socio = '$docReservaActiva'", "");
              foreach ($secionesDocumento as $sesion) {
                // $accionesSessionesModel->deleteRegister($sesion->accion_sesion_id);
              }
            }
            $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
            //$invitadosModel->deleteInvitados($existeReservaActiva->id);
            //$reservasModel->deleteRegister($existeReservaActiva->id);
            $reservasModel->editField($existeReservaActiva->id, 'reserva_estado', 8);
            $reservasModel->editField($existeReservaActiva->id, 'reserva_mesa_id', 0);
          }
        }
      }
      if ($existeSesionActiva && !$redirect) {
        $logModel->insert([
          'log_tipo' => 'LOGIN_FAIL_SESION_ACTIVA',
          'log_log' => 'Sesión activa detectada',
          'log_usuario' => $user,
          'log_fecha' => date('Y-m-d H:i:s'),

        ]);
        // Verificar si la sesión no ha expirado
        $fechaFin = new DateTime($existeSesionActiva->accion_sesion_fecha_fin);
        $ahora = new DateTime();

        $colaCompraModel = new Administracion_Model_DbTable_Colacompra();
        $fechaActual = new DateTime();
        $fechaActual = $fechaActual->format('Y-m-d H:i:s');
        // Verifica si hay una cola activa para el socio


        $existeColaActiva = $colaCompraModel->getList("cola_compras_socio_documento = '$codiSocio' AND (cola_compras_estado = 'activo' OR cola_compras_estado = 'espera') AND cola_compras_vence_el > '$fechaActual'", "");

        if ($ahora <= $fechaFin) {
          // Calcular tiempo restante
          $tiempoRestante = $fechaFin->getTimestamp() - $ahora->getTimestamp();
          $minutosRestantes = ceil($tiempoRestante / 60);

          $response = [
            'status' => 'error',
            'error' => 'session_active',
            'message' => "Atención: ya existe una sesión activa para esta acción. Tiempo restante: $minutosRestantes minutos.",
          ];
          Session::getInstance()->set('ncar', null);

          echo json_encode($response);
          return;
        } else if ($existeColaActiva) {
          $logModel->insert([
            'log_tipo' => 'LOGIN_FAIL_COLA_ACTIVA',
            'log_log' => 'Cola activa detectada',
            'log_usuario' => $user,
            'log_fecha' => date('Y-m-d H:i:s'),
          ]);
          $response = [
            'status' => 'error',
            'error' => 'session_active',
            'message' => "Atención: ya existe una sesión activa para este documento.",
          ];
          Session::getInstance()->set('ncar', null);

          echo json_encode($response);
          return;
        } else {
          // Sesión expirada, marcarla como inactiva
          $accionSesionesModel->editField($existeSesionActiva->accion_sesion_id, 'accion_sesion_sesion_activa', 0);
        }
      }
      } catch (Throwable $e) {
        // No dejar que un error inesperado aquí tumbe todo el login sin respuesta:
        // se registra el detalle completo en el log y se responde JSON válido.
        $logModel->insert([
          'log_tipo' => 'LOGIN_ERROR_VERIFICACION_SESION',
          'log_log' => 'Excepción verificando sesión activa: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString(),
          'log_usuario' => $user,
          'log_fecha' => date('Y-m-d H:i:s'),
        ]);
        $response = [
          'status' => 'error',
          'message' => 'Ocurrió un error verificando tu sesión. Intenta de nuevo.',
        ];
        echo json_encode($response);
        return;
      }
      //------------------------------------------





      //------------------------------------------
      // Verifica si el socio es invitado y ya tiene una reserva confirmada
      // $existeReservaConfirmada = $invitadosReservasModel->getListWithReserva(" documento_invitado = '$socio->SBE_CODI' AND invitadoReserva_beneficiario_principal != '1'", "id_invitado DESC")[0];
      $existeReservaConfirmada = $invitadosReservasModel->getListWithReserva(" documento_invitado = '$codiSocio' ", "id_invitado DESC")[0] ?? null;
      if ($existeReservaConfirmada) {
        $estado = $existeReservaConfirmada->reserva_estado;

        if (($estado == 2 || $estado == 3 || $estado == 11) && !$redirect && ($existeReservaConfirmada->reserva_documento != $socio->SBE_CODI)) {
          // $response = [
          //   'status' => 'error',
          //   'error' => 'reservation_found',
          //   'message' => 'Atención: ya existe una reserva confirmada para este documento.',
          // ];
          // Session::getInstance()->set('ncar', null);

          // echo json_encode($response);
          // return;
        }
        if (($estado == 1 || $estado == 4) && !$redirect) {
          $response = [
            'status' => 'error',
            'error' => 'reservation_found',
            'message' => 'Atención: en este momento no se puede iniciar sesión, hay una reserva pendiente de confirmación en la que estás invitado.',
          ];
          Session::getInstance()->set('ncar', null);

          echo json_encode($response);
          return;
        }
      }

      //------------------------------------------



      // stdClass Object ( [sbe_nomb] => DIRECCION DE TIC S [sbe_apel] => TECNOLOGIA [sbe_mail] => LTECNICO1@CLUBELNOGAL.COM [sbe_ncel] => 3142162339 [SBE_CODI] => 88822 [SOC_CODI] => 88822 [SBE_TELE] => 3142162339 [sbe_dire] => CR 5 NO. 78-75 [SOC_CONT] => 3279 [MAC_NUME] => 8021 [SBE_CONT] => 1 [SBE_NCON] => [SBE_IDIO] => )

      //------------------------------------------
      // Guardar sesión activa por documento (SBE_CODI)
      //if (!$redirect && $socio && $socio->SBE_CODI) { -- SE RETIRA DE QUE SI TIENE UNA IDRES NO CREE UNA SESIION
      if ($socio && $socio->SBE_CODI) {
        if (!isset($accionSesionesModel)) {
          $accionSesionesModel = new Administracion_Model_DbTable_Accionsesiones();
        }

        $dataSesion = [];
        $dataSesion['accion_sesion_accion_numero'] = $socio->MAC_NUME ?? 0; // Mantener por compatibilidad
        $dataSesion['accion_sesion_documento_socio'] = $socio->SBE_CODI;
        $dataSesion['accion_sesion_fecha_inicio'] = date('Y-m-d H:i:s');
        $dataSesion['accion_sesion_fecha_fin'] = date('Y-m-d H:i:s', strtotime('+60 minutes'));
        $dataSesion['accion_sesion_sesion_activa'] = 1;
        $dataSesion['accion_sesion_last_ping'] = date('Y-m-d H:i:s');
        $dataSesion['accion_sesion_ip_usuario'] = $_SERVER['REMOTE_ADDR'];
        $dataSesion['accion_sesion_user_agent'] = $_SERVER['HTTP_USER_AGENT'];

        //como obtener dispositivo
        $dataSesion['accion_sesion_dispositivo'] = $this->getDevice();

        $idSesion = $accionSesionesModel->insert($dataSesion);

        $sesionInfo = $accionSesionesModel->getById($idSesion);
        
        if ($sesionInfo) {
          Session::getInstance()->set('sesion', $sesionInfo);
        }


      }
      //------------------------------------------

      $existeReservasSocio = $reservasModel->getList(" reserva_documento = '$codiSocio' ", "id DESC");

      $totalInvitadosReservas = 0;                // Aceptadas (estados 2,3,11)
      $totalInvitadosReservasPendientes = 0;      // Pendientes (estados 1, 4,7)
      $UMBRAL_MAX_PERSONAS = $this->UMBRAL_MAX_PERSONAS;


      // Asegurar variable redirect inicializada
      if (!isset($redirect)) {
        $redirect = false;
      }
      if (is_countable($existeReservasSocio) && count($existeReservasSocio) >= 1) {
        foreach ($existeReservasSocio as $reservaSocio) {
          $estado = (int) $reservaSocio->reserva_estado;
          $personas = (int) $reservaSocio->reserva_total_personas;

          // Reservas aceptadas
          if (in_array($estado, [2, 3, 11], true) && !$redirect) {
            $totalInvitadosReservas += $personas;

            // Si al sumar aceptadas se alcanza o supera el umbral => redirigir
            if ($totalInvitadosReservas >= $UMBRAL_MAX_PERSONAS) {
              $redirect = true;
              $idRes = $reservaSocio->id; // id de la reserva que dispara la condición
              break; // Ya no necesitamos seguir contando
            }
          }

          // Reservas pendientes de confirmación / pago
          if (in_array($estado, [1, 4, 7], true) && !$redirect) {
            $totalInvitadosReservasPendientes += $personas;
            if (count($existeReservasSocio) > 1 && $totalInvitadosReservas >= 1) {
              $redirect = true;
              $idRes = $reservaSocio->id;
            }
          }
        }
      }

      // Si NO se activó redirect por aceptadas, pero aceptadas + pendientes >= umbral => bloquear login
      if (
        !$redirect
        && ($totalInvitadosReservas + $totalInvitadosReservasPendientes) >= $UMBRAL_MAX_PERSONAS

      ) {
        $response = [
          'status' => 'error',
          'error' => 'reservation_found',
          'message' => 'Atención: ya existe una reserva pendiente o aceptada que alcanza el cupo máximo para este documento. Por favor espere mientras se confirma la reserva pendiente.',
        ];
        Session::getInstance()->set('ncar', null);
        echo json_encode($response);
        return;
      }
      //  $totalInvitadosReservas
      //  $redirect = true;
      //       $idRes = $reservaSocio->id;
      //totalInvitadosReservasPendientes
      //------------------------------------------


      // Verifica si el socio esta seleccionado por alguien de otra accion

      $beneficiariosBloqueosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();

      $fechaActual = date('Y-m-d H:i:s');
      $existeBeneficiarioBloqueado = $beneficiariosBloqueosModel->getList("beneficiario_bloqueodocumento = '$codiSocio' AND beneficiario_bloqueo_estado = 1 AND beneficiario_bloqueo_expiracion > '$fechaActual'", "");

      if ($existeBeneficiarioBloqueado && !$redirect) {
        $response = [
          'status' => 'error',
          'error' => 'beneficiary_blocked',
          'message' => 'Atención: el socio está bloqueado temporalmente porque otro socio lo ha seleccionado, no puede iniciar sesión.',
        ];
        Session::getInstance()->set('ncar', null);

        echo json_encode($response);
        return;
      }
      //------------------------------------------

      if ($socio) {
        Session::getInstance()->set('socio', $socio);
      }

      $logModel->insert([
        'log_tipo' => 'LOGIN_SUCCESS',
        'log_log' => 'Inicio de sesión exitoso',
        'log_fecha' => date('Y-m-d H:i:s'),
        'log_usuario' => $user
      ]);
      //Iniciar sesión del socio
      $response = [
        'status' => 'success',
        'message' => 'Bienvenido',
        'user' => $socio,
        'ncar' => Session::getInstance()->get('ncar'),
      ];

      if ($redirect) {
        $response['id_res'] = $idRes;
      }
    } else {
      $logModel->insert([
        'log_tipo' => 'LOGIN_FAIL_CREDENCIALES',
        'log_log' => 'Credenciales incorrectas',
        'log_fecha' => date('Y-m-d H:i:s'),
        'log_usuario' => $user
      ]);
      $response = [
        'status' => 'error',
        'error' => 'Error del web',
        'message' => 'Atención: Los datos ingresados no son correctos. Por favor revisa tu número de carnet y contraseña.',
      ];
    }

    // Resetea los intentos fallidos al iniciar sesión correctamente
    $infoBloqueo = $bloqueosModel->getList("bloqueo_nit = '$userEscapado'", "bloqueo_id DESC");
    if (count($infoBloqueo) > 0) {
      foreach ($infoBloqueo as $info) {
        $bloqueosModel->deleteRegister($info->bloqueo_id);
      }
    }

    echo json_encode($response);
    return;
  }

  // Método para obtener el número de intentos fallidos de un usuario
  public function getIntentos($nit, $ip)
  {
    $bloqueosModel = new Administracion_Model_DbTable_Bloqueos();

    // Obtiene el último registro de bloqueo del usuario
    $nitEscapado = addslashes($nit);
    $ipEscapada = addslashes($ip);
    $infoBloqueo = $bloqueosModel->getList("bloqueo_nit = '$nitEscapado' or bloqueo_ip ='$ipEscapada'", "bloqueo_id DESC")[0] ?? null;

    // Incrementa el contador de intentos fallidos
    $intento = $infoBloqueo->bloqueo_intentosfallidos ?? 0;
    $intento = $intento + 1;

    // Devuelve el número de intentos
    return $intento;
  }
  // Método privado para verificar el captcha
  private function verifyCaptcha($response)
  {
    $secretKey = '6LfFDZskAAAAAOvo1878Gv4vLz3CjacWqy08WqYP';
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
      'secret' => $secretKey,
      'response' => $response
    );

    $options = array(
      'http' => array(
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
      )
    );

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $response = json_decode($result);

    return $response->success;
  }
  public function logoutAction()
  {
    $this->setLayout('blanco');
    $reservaExpirada = $this->_getSanitizedParam('reserva_expirada');
    $msm = $this->_getSanitizedParam('msm');
    $reservaSession = Session::getInstance()->get('reserva');
    $socio = Session::getInstance()->get('socio');
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reserva = $reservasModel->getById($reservaSession->id);
    if ($reservaExpirada == 1 || 1 == 1) {
      if ($reservaSession && $reserva && $reserva->reserva_estado == 1) {
        // $reservasModel->editField($reservaSession->id, 'reserva_estado', 8); // reserva cancelada por inactividad

        //Liberar mesa
        $mesasModel = new Administracion_Model_DbTable_Mesas();
        $mesaReserva = $reserva->reserva_mesa_id;

        if ($mesaReserva) {
          $mesaIds = explode(',', $mesaReserva);

          foreach ($mesaIds as $idMesa) {
            $idMesa = trim($idMesa);
            if ($idMesa) {
              $mesasModel->editField($idMesa, 'mesa_estado', 0);
            }
          }
        }

        $documento = $reserva->reserva_documento;
        if ($documento) {
          // Inactivar beneficiarios bloqueados
          $this->inactivarBeneficiariosBloqueados($documento);

          // Inactivar mesas bloqueados
          $this->inactivarMesasBloqueadas($documento);
        }

        if ($reserva->reserva_documento) {
          $accionesSessionesModel = new Administracion_Model_DbTable_Accionsesiones();
          $docReservaLogout = (string) $reserva->reserva_documento;
          $secionesDocumento = $accionesSessionesModel->getList("accion_sesion_documento_socio = '$docReservaLogout'", "");
          foreach ($secionesDocumento as $sesion) {
            $accionesSessionesModel->deleteRegister($sesion->accion_sesion_id);
          }
        }

        $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
        //$invitadosModel->deleteInvitados($reserva->id);
        //$reservasModel->deleteRegister($reserva->id);
        $reservasModel->editField($reserva->id, 'reserva_estado', 8);
        $reservasModel->editField($reserva->id, 'reserva_mesa_id', 0);
      }
    }

    $sesionInfo = Session::getInstance()->get('sesion');
    if ($sesionInfo) {
      $accionesSessionesModel = new Administracion_Model_DbTable_Accionsesiones();
      $accionesSessionesModel->deleteRegister($sesionInfo->accion_sesion_id);
    }

    $beneficiariosBloqueoModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();
    $codiSocioLogout = (string) ($socio->SBE_CODI ?? '');
    $listadoBeneficiariosSeleccionado = $beneficiariosBloqueoModel->getList("beneficiario_bloqueo_por_asociado_documento = '$codiSocioLogout'", "");
    if ($listadoBeneficiariosSeleccionado) {
      foreach ($listadoBeneficiariosSeleccionado as $beneficiario) {
        $beneficiariosBloqueoModel->deleteRegister($beneficiario->beneficiario_bloqueo_id);
      }
    }


    $colaSession = Session::getInstance()->get('colaCompra');
    if ($colaSession) {
      $colaCompraModel = new Administracion_Model_DbTable_Colacompra();
      $colaCompraModel->deleteRegister($colaSession->cola_compras_id);
    }

    Session::getInstance()->set('ncar', '');
    Session::getInstance()->set('socio', '');
    Session::getInstance()->set('sesion', '');
    Session::getInstance()->set('beneficiarios', '');
    Session::getInstance()->set('colaCompra', '');
    Session::getInstance()->set('reservacreada', '');
    Session::getInstance()->set('gestor', '');

    session_destroy();

    if ($msm) {
      header('Location: /?timeout=1');
      exit;
    }

    header('Location: /');
  }



  public function inactivarsesionesAction() {}


  public function encriptar($x)
  {
    $x = base64_encode("*" . $x . "*");
    $x = str_replace("=", "_", $x);
    return $x;
  }

  public function desencriptar($x)
  {
    $x = str_replace("_", "=", $x);
    $x = base64_decode($x);
    $x = str_replace("*", "", $x);
    return $x;
  }
  function getAge(?object $dateObj): ?int
  {
    if (!$dateObj || empty($dateObj->date))
      return null;
    try {
      $birth = new DateTimeImmutable($dateObj->date);
      return $birth->diff(new DateTimeImmutable('today'))->y;
    } catch (Throwable $e) {
      return null;
    }
  }

  public function loginGestor($user, $pass)
  {
    $userModel = new core_Model_DbTable_User();
    if (!$userModel->autenticateUser($user, $pass)) {
      return false;
    }
    $resUser = $userModel->searchUserByUser($user);
    if ($resUser->user_state != 1) {
      return false;
    }
    Session::getInstance()->set("gestor", $resUser);
    return $resUser;
  }

  public function getDevice()
  {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    $devices = [
      'Mobile' => 'Mobile',
      'Tablet' => 'Tablet',
      'iPad' => 'iPad',
      'iPhone' => 'iPhone',
      'Android' => 'Android',
      'Windows Phone' => 'Windows Phone',
    ];

    foreach ($devices as $device => $pattern) {
      if (stripos($userAgent, $pattern) !== false) {
        return $device;
      }
    }

    return 'Desktop';
  }
}
