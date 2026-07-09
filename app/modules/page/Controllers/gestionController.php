<?php

class Page_gestionController extends Page_mainController
{
  public function indexAction()
  {
    // Mostrar formulario de búsqueda
    $error = $this->_getSanitizedParam('error');
    $this->_view->error = $error;
    Session::getInstance()->set('ncar', "");
    Session::getInstance()->set('socio', "");
  }

  public function buscarAction()
  {
    $this->setLayout('blanco');

    $ncar = $this->_getSanitizedParam('ncar');

    if (!$ncar) {
      echo json_encode([
        'success' => false,
        'message' => 'Debe ingresar un número de carnet'
      ]);
      return;
    }

    // Log del intento de búsqueda
    $logModel = new Administracion_Model_DbTable_Log();
    $logModel->insert([
      'log_tipo' => 'GESTION_BUSQUEDA',
      'log_log' => "Búsqueda de socio con carnet: $ncar",
      'log_fecha' => date('Y-m-d H:i:s'),
      'log_usuario' => $ncar
    ]);

    // Consultar socio usando el método del mainController
    $loginServiceUrl = URL_BASE . '/querys/buscarUsuario.php';
    $postData = http_build_query([
      'token' => $this->generarToken(),
      'ncar' => $ncar
    ]);

    $ch = curl_init($loginServiceUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
      $logModel->insert([
        'log_tipo' => 'GESTION_ERROR',
        'log_log' => 'Error cURL: ' . curl_error($ch),
        'log_fecha' => date('Y-m-d H:i:s'),
        'log_usuario' => $ncar
      ]);

      echo json_encode([
        'success' => false,
        'message' => 'Error al consultar el socio'
      ]);
      curl_close($ch);
      return;
    }

    curl_close($ch);
    $socio = json_decode($response);
    if (!$socio || !$socio->SBE_CODI) {
      $logModel->insert([
        'log_tipo' => 'GESTION_SOCIO_NO_ENCONTRADO',
        'log_log' => "Socio no encontrado con carnet: $ncar",
        'log_fecha' => date('Y-m-d H:i:s'),
        'log_usuario' => $ncar
      ]);

      echo json_encode([
        'success' => false,
        'message' => 'Socio no encontrado'
      ]);
      return;
    }

    // Validaciones del socio (igual que en loginController)
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $invitadosReservasModel = new Administracion_Model_DbTable_Invitadosreservas();
    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $evento = $eventoModel->getById(1);
    $UMBRAL_MAX_PERSONAS = $evento->evento_invitados_socio ?? 300;



    // Verificar límite de invitados
    $existeReservasSocio = $reservasModel->getList(
      " reserva_documento = '$socio->SBE_CODI' ",
      "id DESC"
    );

    $totalInvitadosReservas = 0;
    $totalInvitadosReservasPendientes = 0;

    if (is_countable($existeReservasSocio) && count($existeReservasSocio) >= 1) {
      foreach ($existeReservasSocio as $reservaSocio) {
        $invitados = $invitadosReservasModel->getList(
          "reserva_id_reserva = '{$reservaSocio->id}'",
          "id_invitado DESC"
        );

        foreach ($invitados as $invitado) {
          if ($invitado->invitado_estado == 2 || $invitado->invitado_estado == 3) {
            $totalInvitadosReservas++;
          }
          if ($invitado->invitado_estado == 1 || $invitado->invitado_estado == 4 || $invitado->invitado_estado == 7) {
            $totalInvitadosReservasPendientes++;
          }
        }
      }
    }

    if (($totalInvitadosReservas + $totalInvitadosReservasPendientes) >= $UMBRAL_MAX_PERSONAS) {
      $logModel->insert([
        'log_tipo' => 'GESTION_LIMITE_ALCANZADO',
        'log_log' => "Socio alcanzó límite de invitados: $socio->SBE_CODI",
        'log_fecha' => date('Y-m-d H:i:s'),
        'log_usuario' => $ncar
      ]);

      echo json_encode([
        'success' => false,
        'message' => 'Este socio ya alcanzó el límite máximo de invitados permitidos'
      ]);
      return;
    }

    // Crear sesión activa (igual que en loginController)
    if ($socio && $socio->SBE_CODI) {
      $accionSesionesModel = new Administracion_Model_DbTable_Accionsesiones();

      // Inactivar sesiones anteriores
      $sesionesAnteriores = $accionSesionesModel->getList(
        "accion_sesion_documento_socio = '$socio->SBE_CODI'",
        "accion_sesion_id DESC"
      );

      foreach ($sesionesAnteriores as $sesionAnterior) {
        $accionSesionesModel->editField(
          $sesionAnterior->accion_sesion_id,
          'accion_sesion_sesion_activa',
          0
        );
      }

      // Crear nueva sesión
      $dataSesion = [
        'accion_sesion_documento_socio' => $socio->SBE_CODI,
        'accion_sesion_sesion_activa' => 1,
        'accion_sesion_fecha_inicio' => date('Y-m-d H:i:s'),
        'accion_sesion_ip' => $_SERVER['REMOTE_ADDR'] ?? 'GESTION'
      ];

      $idSesion = $accionSesionesModel->insert($dataSesion);
      $accionSesionesModel->editField(
        $idSesion,
        'accion_sesion_fecha_fin',
        date('Y-m-d H:i:s', strtotime('+1 hour'))
      );
      $sesionInfo = $accionSesionesModel->getById($idSesion);
      Session::getInstance()->set('sesion', $sesionInfo);
    }
    if (APPLICATION_ENV != 'production') {
      $socio->sbe_mail = 'desarrollo8@omegawebsystems.com';
    }

    // Validar edad del socio
    $edad = $this->getAge($socio->SBE_FNAC ?? null);
    $esMenor18 = $edad !== null && $edad < 18;

    if (!$socio->SBE_FNAC) {
      $logModel->insert([
        'log_tipo' => 'GESTION_FAIL_FNAC',
        'log_log' => 'Falta fecha de nacimiento',
        'log_fecha' => date('Y-m-d H:i:s'),
        'log_usuario' => $ncar
      ]);

      echo json_encode([
        'success' => false,
        'error' => 'missing_birthdate',
        'message' => 'Error interno, no se pudo obtener la fecha de nacimiento del socio.'
      ]);
      return;
    }

    if ($esMenor18) {
      $logModel->insert([
        'log_tipo' => 'GESTION_FAIL_MENOR18',
        'log_log' => 'Usuario menor de 18 años',
        'log_fecha' => date('Y-m-d H:i:s'),
        'log_usuario' => $ncar
      ]);

      echo json_encode([
        'success' => false,
        'error' => 'under_18',
        'message' => 'Atención: No se puede iniciar sesión para este socio, debe ser mayor de 18 años para realizar reservas.'
      ]);
      return;
    }

    // Generar sesión igual que en login exitoso
    Session::getInstance()->set('ncar', $ncar);
    Session::getInstance()->set('socio', $socio);

    $logModel->insert([
      'log_tipo' => 'GESTION_SESION_CREADA',
      'log_log' => "Sesión creada exitosamente para: $socio->SBE_CODI",
      'log_fecha' => date('Y-m-d H:i:s'),
      'log_usuario' => $ncar
    ]);

    echo json_encode([
      'success' => true,
      'message' => 'Sesión creada exitosamente',
      'redirect' => '/page/evento'
    ]);
    return;
  }

  /**
   * Calcula la edad a partir de un objeto de fecha
   * @param object|null $dateObj Objeto con propiedad 'date'
   * @return int|null Edad en años o null si no se puede calcular
   */
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
}
