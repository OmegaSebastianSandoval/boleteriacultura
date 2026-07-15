<?php

class Page_eventoController extends Page_mainController
{
  public $namefilter = 'eventos_page';
  public $namepageactual;
  public $pages = 6;

  public $socio;

  public $id;

  public $totalAceptadas = 0;
  public $totalPendientes = 0;
  public $UMBRAL_MAX_PERSONAS;

  public function init()
  {
    if (!Session::getInstance()->get('ncar')) {
      header('Location: /');
      exit;
    }
    $this->socio = $this->consultarSocioSession();
    if (!$this->socio) {
      header('Location: /page/login/');
      exit;
    }
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    // $existeReservaSocio = $reservasModel->getList("res_erva_documento = '{$this->socio->SBE_CODI}'", "id DESC")[0];



    // if (
    //   ($existeReservaSocio->reserva_estado == 2 || $existeReservaSocio->reserva_estado == 3)
    //   && (strpos($_SERVER['REQUEST_URI'], 'aviso') === false
    //     && strpos($_SERVER['REQUEST_URI'], 'generarpago') === false
    //     && strpos($_SERVER['REQUEST_URI'], 'resumen') === false)
    // ) {
    //   // header('Location: /page/guests/?id=' . $existeReservaSocio->id);
    //   // exit;
    // }
    // if ($existeReservaSocio->reserva_estado == 4) {
    //   header('Location: /page/respuesta?id=' . $existeReservaSocio->id);
    //   return;

    // }
    $reservasModel = new Administracion_Model_DbTable_Reservas();

    // Listado de TODAS las reservas del socio (no solo la última)
    $reservasSocio = $reservasModel->getList("reserva_documento = '{$this->socio->SBE_CODI}'", "id DESC");

    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $evento = $eventoModel->getById(1);
    $this->_view->UMBRAL_MAX_PERSONAS = $this->UMBRAL_MAX_PERSONAS = $evento->evento_invitados_socio;

    $UMBRAL_MAX_PERSONAS = $this->UMBRAL_MAX_PERSONAS ?? $evento->evento_invitados_socio;

    $this->totalAceptadas = 0;                 // estados 2,3,11
    $this->totalPendientes = 0;                // estados 1,4,7
    $redirect = false;
    $idResRedirect = null;

    if (is_countable($reservasSocio) && count($reservasSocio) > 0) {
      foreach ($reservasSocio as $reservaSocio) {
        $estado = (int) $reservaSocio->reserva_estado;
        $personas = (int) $reservaSocio->reserva_total_personas;

        // Aceptadas
        if (in_array($estado, [2, 3, 11], true) && !$redirect) {
          $this->totalAceptadas += $personas;
          if ($this->totalAceptadas >= $UMBRAL_MAX_PERSONAS) {
            $redirect = true;
            // $idResRedirect = $reservaSocio->id;
            break;
          }
        }

        // Pendientes (solo se suman si aún no se disparó redirect)
        if (in_array($estado, [1, 4, 7], true) && !$redirect) {
          $this->totalPendientes += $personas;
        }
      }
    }

    // Si alcanzó umbral solo con aceptadas => redirigir a guests
    if (
      $redirect
      && strpos($_SERVER['REQUEST_URI'], 'guests') === false
      && strpos($_SERVER['REQUEST_URI'], 'aviso') === false
      && strpos($_SERVER['REQUEST_URI'], 'generarpago') === false
      && strpos($_SERVER['REQUEST_URI'], 'resumen') === false
    ) {
      header('Location: /page/guests/?id=' . enc_id($idResRedirect));
      exit;
    }

    // Si NO hubo redirect por aceptadas, pero aceptadas + pendientes >= umbral => bloquear ingreso
    if (
      !$redirect
      && ($this->totalAceptadas + $this->totalPendientes) >= $this->UMBRAL_MAX_PERSONAS
      && (strpos($_SERVER['REQUEST_URI'], 'aviso') === false
        && strpos($_SERVER['REQUEST_URI'], 'generarpago') === false
        && strpos($_SERVER['REQUEST_URI'], 'resumen') === false
        && strpos($_SERVER['REQUEST_URI'], 'reservar') === false
        && strpos($_SERVER['REQUEST_URI'], 'index') === false
        && strpos($_SERVER['REQUEST_URI'], '') === false
        && strpos($_SERVER['REQUEST_URI'], 'guests') === false)
    ) {
      // Ajusta destino / parámetro de error a tu flujo
      header('Location: /?cupo_maximo=1');
      exit;
    }

    // stdClass Object ( [cola_compras_id] => 30 [cola_compras_socio_documento] => PRUEBA7 [cola_compras_estado] => espera [cola_compras_creado_el] => 2025-08-07 11:31:50 [cola_compras_inicio_el] => [cola_compras_vence_el] => )
    $infoColaSesion = Session::getInstance()->get('colaCompra');
    if ($infoColaSesion && $infoColaSesion->cola_compras_estado == 'espera') {
      header('Location: /page/espera/');
    }

    $this->id = $this->_getSanitizedParam('id') ?: 1;
    parent::init();
  }

  public function indexAction()
  {
    $id = $this->id;
    //error_reporting(E_ALL);
    $eventosModel = new Administracion_Model_DbTable_Eventos();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    //$reservasModel = new Administracion_Model_DbTable_Reservas();
    $this->_view->evento = $evento = $eventosModel->getById($id);
    if (!$evento) {
      header('Location: /page/notfound/');
      exit;
    }

    //$this->_view->socio = $this->socio;

    $sumaInvitadosTotalEvento = 0;

    $invitados_suma = $invitadosModel->getList("invitado_evento = '" . $id . "'", "");
    foreach ($invitados_suma as $i) {
      $sumaInvitadosTotalEvento++;
    }

    //$this->_view->sumaInvitadosTotalEvento = $sumaInvitadosTotalEvento;
    $this->logAuditoria('INICIO_CONTROLADOR', null, [
      'observaciones' => 'Usuario accede al controlador de eventos',
      'session_data' => [
        'ncar' => Session::getInstance()->get('ncar'),
        'colaCompra' => Session::getInstance()->get('colaCompra')
      ]
    ]);
    header('location: /page/evento/reservar/');
  }

  public function notfoundAction()
  {
    // Renderizar la vista de "no encontrado"
  }

  public function reservarAction()
  {
    Session::getInstance()->set('reservacreada', null);

    // Log de inicio de acción
    $this->logAuditoria('INICIO_RESERVAR_ACTION', null, [
      'observaciones' => 'Usuario accede a la acción de reservar',
      'parametros_get' => $_GET
    ]);
    $this->_view->totalAceptadas = $this->totalAceptadas;
    $this->_view->totalPendientes = $this->totalPendientes;
    $this->_view->UMBRAL_MAX_PERSONAS = $this->UMBRAL_MAX_PERSONAS;
    $capacidadRestante = max(0, $this->UMBRAL_MAX_PERSONAS - ($this->totalAceptadas + $this->totalPendientes));
    $this->_view->capacidadRestante = $capacidadRestante;
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $reservaSession = Session::getInstance()->get('reserva');
    if ($reservaSession && $reservaSession->id) {

      $reservaInfo = $reservasModel->getById($reservaSession->id);

      // Log de verificación de reserva en sesión
      $this->logAuditoria('VERIFICACION_RESERVA_SESION', $reservaSession->id, [
        'observaciones' => 'Verificando reserva existente en sesión',
        'estado_reserva' => $reservaInfo ? $reservaInfo->reserva_estado : 'no_encontrada',
        'datos_json' => $reservaInfo
      ]);

      // Solo limpiar reservas en estados eliminables (1, 5, 6, 8)
      // NO tocar estados 2,3,4,7 (aprobadas, pagadas, pendientes de pago)
      if (in_array($reservaInfo->reserva_estado, ['1', '5', '6', '8'])) {
        $this->logAuditoria('LIMPIEZA_RESERVA_SESION', $reservaSession->id, [
          'observaciones' => 'Limpiando reserva de sesión por estado inválido',
          'estado_anterior' => $reservaInfo ? $reservaInfo->reserva_estado : 'null'
        ]);
        $this->limpiarReserva($reservaSession->id);
        Session::getInstance()->set('reserva', null);
      }
    }


    //Eliminar reserva mesas si existe alguna por usuario
    // "return" se eliminó: "boking" siempre viaja cifrado y solo se envía cuando
    // el usuario vuelve desde beneficiarios/mesa, así que su sola presencia (más la
    // verificación de dueño y de estado limpiable) ya es señal suficiente.
    $idReserva = $this->_getDecryptedParam('boking');

    $inf_reserva = $idReserva ? $reservasModel->getById($idReserva) : null;

    if ($idReserva && $inf_reserva && $inf_reserva->reserva_documento == $this->socio->SBE_CODI) {
      if (in_array($inf_reserva->reserva_estado, ['1', '5', '6', '8'])) {
        $this->logAuditoria('LIMPIEZA_RESERVA_RETURN', $idReserva, [
          'observaciones' => 'Limpiando reserva al volver desde beneficiarios/mesa',
          'estado_anterior' => $inf_reserva->reserva_estado
        ]);
        $this->limpiarReserva($idReserva);
      }
    }

    if ($this->socio->SBE_CODI && $inf_reserva) {
      if (in_array($inf_reserva->reserva_estado, ['1', '5', '6', '8'])) {
        // Log de limpieza de bloqueos
        $this->logAuditoria('LIMPIEZA_BLOQUEOS', $inf_reserva->id, [
          'observaciones' => 'Inactivando bloqueos por estado de reserva',
          'estado_reserva' => $inf_reserva->reserva_estado
        ]);
        // Inactivar beneficiarios bloqueados
        $this->inactivarBeneficiariosBloqueados($this->socio->SBE_CODI);
        // Inactivar mesas bloqueadas
        $this->inactivarMesasBloqueadas($this->socio->SBE_CODI);
      }
    }
    $this->_view->sin_disponibilidad = $this->_getSanitizedParam('error');

    //error_reporting(E_ALL);
    $id = $this->id;
    $eventosModel = new Administracion_Model_DbTable_Eventos();


    $mesasDisponibles = $mesasModel->getListMesasDisponibles();
    $this->_view->mesasDisponibles = $mesasDisponibles;
    // Sillas individuales libres (conteo global) para el modo "Sillas" del paso 1.
    $sillasTotal = $mesasModel->getSillasDisponiblesTotal();
    $this->_view->sillasDisponibles = ($sillasTotal && isset($sillasTotal[0]) && $sillasTotal[0]->cantidad_sillas !== null)
      ? (int) $sillasTotal[0]->cantidad_sillas : 0;
    $this->_view->evento = $evento = $eventosModel->getById($id);
    $this->_view->socio = $socio = $this->consultarSocioSession();
    // Log de carga de datos básicos
    $this->logAuditoria('CARGA_DATOS_RESERVAR', $idReserva, [
      'observaciones' => 'Cargando datos básicos para reservar',
      'evento_id' => $id,
      'mesas_disponibles' => count($mesasDisponibles),
      'datos_json' => [
        'evento' => $evento ? $evento->evento_nombre : null,
        'total_mesas_disponibles' => count($mesasDisponibles)
      ]
    ]);
    // Máximo de invitados permitidos
    $maxInvitados = intval($evento->evento_invitados_socio) ?? 0;
    $this->_view->maxInvitados = $maxInvitados;

    $beneficiarios = $this->consultarBeneficiariosSession();
    // print_r($this->socio);
    if (!$beneficiarios) {
      $beneficiarios = [];
      $this->_view->beneficiarios = [];
      // Log de beneficiarios vacíos
      $this->logAuditoria('BENEFICIARIOS_NO_ENCONTRADOS', null, [
        'observaciones' => 'No se encontraron beneficiarios para el socio'
      ]);
    } else {
      $this->_view->beneficiarios = $beneficiarios;
      // Log de beneficiarios cargados
      $this->logAuditoria('BENEFICIARIOS_CARGADOS', null, [
        'observaciones' => 'Beneficiarios cargados exitosamente',
        'cantidad_beneficiarios' => count($beneficiarios),
        'datos_json' => array_map(function ($b) {
          return [
            'documento' => $b->SBE_CODI ?? null,
            'nombre' => $b->SBE_NOMB ?? null
          ];
        }, $beneficiarios)
      ]);
    }
    $stats = [
      'total' => 0,
      'menores_25' => 0,
      'hijos' => 0,
      'listado' => [],
      'socio_principal' => null
    ];

    $socioEncontrado = false;

    foreach ($beneficiarios as $beneficiarioIndividual) {
      if (!is_object($beneficiarioIndividual))
        continue;

      $edad = $this->getAge($beneficiarioIndividual->SBE_FNAC ?? null);
      $esMenor25 = $edad !== null && $edad < 25;
      $esHijo = $this->esHijo($beneficiarioIndividual);

      // Verificar si es el socio principal (socio actual logueado)
      $esSocioPrincipal = false;
      if (
        $beneficiarioIndividual->SBE_CODI === $socio->SBE_CODI &&
        $beneficiarioIndividual->SBE_NOMB === $socio->sbe_nomb
      ) {
        $esSocioPrincipal = true;
        $socioEncontrado = true;
        $stats['socio_principal'] = [
          'beneficiario' => $beneficiarioIndividual,
          'edad' => $edad,
          'menor25' => $esMenor25,
          'hijo' => $esHijo,
        ];
      }

      $stats['total']++;
      if ($esMenor25)
        $stats['menores_25']++;
      if ($esHijo)
        $stats['hijos']++;

      $stats['listado'][] = [
        'beneficiario' => $beneficiarioIndividual,
        'edad' => $edad,
        'menor25' => $esMenor25,
        'hijo' => $esHijo,
        'socio_principal' => $esSocioPrincipal
      ];
    }

    // Si el socio no fue encontrado en beneficiarios, agregarlo como principal
    if (!$socioEncontrado) {
      $edadSocio = $this->getAge($socio->SBE_FNAC ?? null);
      $esMenor25Socio = $edadSocio !== null && $edadSocio < 25;
      $esHijoSocio = $this->esHijo($socio); // Verificar si el socio también es hijo

      $stats['socio_principal'] = [
        'beneficiario' => $socio,
        'edad' => $edadSocio,
        'menor25' => $esMenor25Socio,
        'hijo' => $esHijoSocio, // Cambiar de false a verificación real
      ];
    }

    // Inicializar datos de reserva en sesión
    if (!Session::getInstance()->get('reserva_data')) {
      Session::getInstance()->set('reserva_data', [
        'beneficiarios_seleccionados' => [],
        'no_beneficiarios' => [],
        'total_invitados' => 0
      ]);
    }

    $this->_view->beneficiarios = $beneficiarios ? $beneficiarios : [];
    $this->_view->beneficiariosStats = $stats;
    // Log final de carga completa
    $this->logAuditoria('RESERVAR_ACTION_COMPLETADA', $idReserva, [
      'observaciones' => 'Acción reservar completada exitosamente',
      'datos_json' => [
        'max_invitados' => $maxInvitados,
        'total_beneficiarios' => count($beneficiarios),
        'stats' => $stats ?? null
      ]
    ]);

    // Listado de reservas del socio
    $reservaModel = new Administracion_Model_DbTable_Reservas();
    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
    $invitadosReservaModel = new Administracion_Model_DbTable_Invitadosreservas();
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $ambientesModel = new Administracion_Model_DbTable_Ambientes();

    // ID 896: reserva de prueba/administración excluida del historial visible del socio
    $reservasSocio = $reservaModel->getList("reserva_documento = '{$socio->SBE_CODI}'  AND id != '896'", 'id DESC');
    $cuposModel = new Administracion_Model_DbTable_Reservacuposadicionales();
    $listaReservas = [];
    if (is_countable($reservasSocio)) {
      foreach ($reservasSocio as $r) {
        $estado = (int) $r->reserva_estado;
        if (!in_array($estado, [2, 3, 4, 11], true))
          continue; // activos + pendientes por confirmación (4)
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

        // Cupos adicionales pendientes de pago para esta reserva
        $cuposPendientes = $cuposModel->getList("reserva_id = '{$r->id}' AND cupos_estado = 0", "id DESC");
        $cuposPendiente  = !empty($cuposPendientes) ? $cuposPendientes[0] : null;

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
          'qr_anteriores' => (int) $r->reserva_boleteria_enviada >= 1,
          'cupos_pendiente_id' => $cuposPendiente ? (int) $cuposPendiente->id : null,
          'cupos_pendiente_cantidad' => $cuposPendiente ? (int) $cuposPendiente->cupos_adicionales : 0,
          'cupos_pendiente_precio' => $cuposPendiente ? (float) $cuposPendiente->precio_total : 0,
        ];
      }
    }
    $this->_view->reservasExitosas = $listaReservas;

  }

  public function seleccionarbeneficiariosAction()
  {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    // Log de inicio de selección
    $this->logAuditoria('INICIO_SELECCION_BENEFICIARIOS', null, [
      'observaciones' => 'Usuario inicia selección de beneficiarios',
      'parametros_get' => $_GET,
      'parametros_post' => $_POST ? 'datos_post_presentes' : 'sin_datos_post'
    ]);

    $beneficiario_bloqueado = $this->_getSanitizedParam('error');
    $this->_view->beneficiario_bloqueado = $beneficiario_bloqueado ?? null;
    $this->_view->errorBeneficiario = Session::getInstance()->get('errorBeneficiario') ?? null;
    Session::getInstance()->set('errorBeneficiario', null); // Limpiar el error después de mostrarlo una vez

    // Modo "cupos adicionales": el socio va a agregar invitados a una reserva ya existente
    // (no crea una reserva nueva ni selecciona mesa, ya está asignada)
    $cuposId = $this->_getDecryptedParam('cupos_id');
    $cuposPendiente = null;
    if ($cuposId) {
      $cuposModel = new Administracion_Model_DbTable_Reservacuposadicionales();
      $reservasModelCupos = new Administracion_Model_DbTable_Reservas();
      $cupos = $cuposModel->getById($cuposId);
      if ($cupos && (int) $cupos->cupos_estado === 0) {
        $reservaCupos = $reservasModelCupos->getById($cupos->reserva_id);
        if ($reservaCupos && $reservaCupos->reserva_documento == $this->socio->SBE_CODI) {
          $cuposPendiente = $cupos;
          $this->_view->cuposMode = true;
          $this->_view->cuposId = $cupos->id;
          $this->_view->reservaExistenteId = $cupos->reserva_id;
        }
      }
    }

    // SEGURIDAD: "puestos" NUNCA se toma de la URL/GET (era manipulable, ej. ?puestos=100
    // sin haber seleccionado ninguna mesa real). La única fuente confiable es la sesión,
    // que solo procesarSeleccionBeneficiariosPost() puede establecer, y solo después de
    // validar disponibilidad real de mesas para la capacidad solicitada.
    $puestos = $cuposPendiente ? $cuposPendiente->cupos_adicionales : Session::getInstance()->get('cantidad_personas_seleccionada');
    $puestosValidacion = $puestos;
    $mesasSeleccionadas = [];
    if (isset($_COOKIE['mesas_seleccionadas'])) {
      $mesasSeleccionadas = json_decode($_COOKIE['mesas_seleccionadas'], true);
    }
    // Modo de selección: 'mesa' (una mesa completa) o 'silla' (varias sillas individuales).
    // En POST viene del formulario; en el GET de re-render se recupera de sesión.
    $tipoSeleccion = $this->_getSanitizedParam('tipo_seleccion');
    if ($tipoSeleccion === '' || $tipoSeleccion === null) {
      $tipoSeleccion = Session::getInstance()->get('tipo_seleccion_mesa') ?: 'mesa';
    }
    $this->_view->tipoSeleccion = $tipoSeleccion;

    $mesaParaUno = false;
    if (is_countable($mesasSeleccionadas) && count($mesasSeleccionadas) > 0) {
      foreach ($mesasSeleccionadas as $mesaSeleccion) {
        if ($mesaSeleccion == 1) {
          $mesaParaUno = true;
        }
      }
    }

    // En modo sillas la cookie es [1,1,...], así que NO aplica el atajo "mesa para 1".
    if (($mesaParaUno || $puestosValidacion == 1) && $tipoSeleccion !== 'silla') {
      $this->_view->puestos = 1;
    }

    // Log de parámetros recibidos
    $this->logAuditoria('PARAMETROS_SELECCION_BENEFICIARIOS', null, [
      'observaciones' => 'Procesando parámetros de selección',
      'datos_json' => [
        'beneficiario_bloqueado' => $beneficiario_bloqueado,
        'puestos' => $puestos,
        'error_beneficiario_sesion' => $this->_view->errorBeneficiario
      ]
    ]);
    // El socio puede estar en varias reservas, por lo que siempre se permite
    // auto-agregarlo como titular en la nueva reserva
    $existeReservaSocio = false;
    if ($this->socio->SBE_CODI) {
      // Log de limpieza de bloqueos
      $this->logAuditoria('LIMPIEZA_BLOQUEOS_SELECCION', null, [
        'observaciones' => 'Inactivando bloqueos antes de selección'
      ]);
      // Inactivar beneficiarios bloqueados
      $this->inactivarBeneficiariosBloqueados($this->socio->SBE_CODI);
      // Inactivar mesas bloqueadas
      $this->inactivarMesasBloqueadas($this->socio->SBE_CODI);
    }
    if (!$existeReservaSocio) {

      $beneficiariosBloqueadosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();
      $bloqueoExistente = $beneficiariosBloqueadosModel->getList(
        "beneficiario_bloqueodocumento = '{$this->socio->SBE_CODI}' AND beneficiario_bloqueo_estado = 1",
        "beneficiario_bloqueo_id DESC"
      )[0];

      if (!$bloqueoExistente) {
        $data = [
          'beneficiario_bloqueodocumento' => $this->socio->SBE_CODI,
          'beneficiario_bloqueo_por_asociado_documento' => $this->socio->SBE_CODI,
          "beneficiario_bloqueo_fecha_bloqueo" => date('Y-m-d H:i:s'),
          'beneficiario_bloqueo_expiracion' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
          'beneficiario_bloqueo_macnume' => $this->socio->MAC_NUME,
          'beneficiario_bloqueo_estado' => 1, // Activo
        ];
        $idb = $beneficiariosBloqueadosModel->insert($data);
      }
    }
    $this->_view->existeReservaSocio = $existeReservaSocio;

    $reservaSession = Session::getInstance()->get('reserva');

    if ($reservaSession && $reservaSession->reserva_estado == 1 && ($reservaSession->reserva_documento == $this->socio->SBE_CODI)) {

      // Log de limpieza de reserva existente
      $this->logAuditoria('LIMPIEZA_RESERVA_EXISTENTE', $reservaSession->id, [
        'observaciones' => 'Limpiando reserva existente antes de nueva selección',
        'estado_anterior' => $reservaSession->reserva_estado
      ]);
      $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
      $reservaModel = new Administracion_Model_DbTable_Reservas();
      $invitados = $invitadosModel->getList("reserva_id_reserva = '{$reservaSession->id}'", "");
      // Log de invitados a eliminar
      $this->logAuditoria('ELIMINACION_INVITADOS_PREVIOS', $reservaSession->id, [
        'observaciones' => 'Eliminando invitados de reserva previa',
        'cantidad_invitados' => count($invitados),
        'invitados_eliminados' => array_map(function ($i) {
          return [
            'id' => $i->id_invitado,
            'documento' => $i->documento_invitado,
            'nombre' => $i->invitadoReserva_nombre_invitado,
            'apellido' => $i->invitadoReserva_apellido_invitado
          ];
        }, $invitados)
      ]);
      foreach ($invitados as $invitado) {
        $invitadosModel->deleteRegister($invitado->id_invitado);
      }

      $mesasModel = new Administracion_Model_DbTable_Mesas();
      // Liberar la mesa
      if ($reservaSession->reserva_mesa_id) {
        // Log de liberación de mesa
        $this->logAuditoria('LIBERACION_MESA_PREVIA', $reservaSession->id, [
          'observaciones' => 'Liberando mesa de reserva previa',
          'mesa_id_anterior' => $reservaSession->reserva_mesa_id
        ]);
        $mesasModel->editField($reservaSession->reserva_mesa_id, 'mesa_estado', 0);
        $reservaModel->editField($reservaSession->id, 'reserva_mesa_id', 0);
      }
    }
    // Si es POST (primera vez desde reservar), procesar y redirigir
    if ($_POST && !$beneficiario_bloqueado) {
      $cantidadPersonas = intval($_POST['cantidad_personas']);
      // Log de procesamiento POST
      $this->logAuditoria('PROCESAMIENTO_POST_SELECCION', null, [
        'observaciones' => 'Procesando datos POST de selección',
        'cantidad_personas' => $cantidadPersonas
      ]);
      $capacidadesSeleccionadas = ($_POST['mesasSeleccionadas']) ? json_decode($_POST['mesasSeleccionadas'], true) : [];

      Session::getInstance()->set('tipo_seleccion_mesa', $tipoSeleccion);
      $this->procesarSeleccionBeneficiariosPost($capacidadesSeleccionadas, $tipoSeleccion);
      // Log de redirección
      $this->logAuditoria('REDIRECCION_POST_SELECCION', null, [
        'observaciones' => 'Redirigiendo tras procesamiento POST',
        'url_destino' => '/page/evento/seleccionarbeneficiarios'
      ]);
      // Redirigir a GET para evitar problemas con el botón Volver. Ya no se pasa
      // "puestos" ni "id" por la URL: la cantidad validada queda en sesión
      // (cantidad_personas_seleccionada, fijada arriba por procesarSeleccionBeneficiariosPost).
      header('Location: /page/evento/seleccionarbeneficiarios');
      exit;
    }

    // Si viene por GET (con puestos) o con error de beneficiario bloqueado, mostrar vista
    if ($puestos || $beneficiario_bloqueado != '') {
      $cantidadPersonas = intval($puestos);
      // Log de carga de vista
      $this->logAuditoria('CARGA_VISTA_SELECCION', null, [
        'observaciones' => 'Cargando vista de selección de beneficiarios',
        'cantidad_personas' => $cantidadPersonas,
        'tiene_error' => $beneficiario_bloqueado != ''
      ]);
      $this->cargarVistaSeleccionBeneficiarios($cantidadPersonas);
    } else {
      // Log de redirección por parámetros inválidos
      $this->logAuditoria('REDIRECCION_PARAMETROS_INVALIDOS', null, [
        'observaciones' => 'Redirigiendo por parámetros inválidos',
        'url_destino' => '/page/evento/reservar?id=' . $this->id
      ]);
      // Si no tiene parámetros válidos, redirigir
      header('Location: /page/evento/reservar?id=' . $this->id);
      exit;
    }
  }

  private function procesarSeleccionBeneficiariosPost($capacidadesSeleccionadas, $tipoSeleccion = 'mesa')
  {
    $bloqueosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $mesasBloqueoModel = new Administracion_Model_DbTable_Mesasbloqueo();

    // Bloquear socio actual (igual que antes)
    $dataBloqueo = [
      'beneficiario_bloqueodocumento' => $this->socio->SBE_CODI,
      'beneficiario_bloqueo_por_asociado_documento' => $this->socio->SBE_CODI,
      'beneficiario_bloqueo_estado' => 1,
      'beneficiario_bloqueo_fecha_bloqueo' => date('Y-m-d H:i:s'),
      'beneficiario_bloqueo_expiracion' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
      'beneficiario_bloqueo_macnume' => $this->socio->MAC_NUME,
    ];
    $bloqueosModel->insert($dataBloqueo);

    // Si solo es un número, convirtelo en arreglo
    if (!is_array($capacidadesSeleccionadas)) {
      $capacidadesSeleccionadas = [$capacidadesSeleccionadas];
    }

    // ---- Modo SILLAS individuales ----
    // Cada silla es una unidad de capacidad 1; $capacidadesSeleccionadas = [1,1,...].
    // No aplica el pre-bloqueo por "capacidad de mesa"; la disponibilidad concreta por
    // ambiente se valida al confirmar (updateEstadoAtomico en confirmarmesaAction).
    if ($tipoSeleccion === 'silla') {
      $totalSolicitadas = count($capacidadesSeleccionadas);
      $sillasInfo = $mesasModel->getSillasDisponiblesTotal();
      $sillasLibres = ($sillasInfo && isset($sillasInfo[0]) && $sillasInfo[0]->cantidad_sillas !== null)
        ? (int) $sillasInfo[0]->cantidad_sillas : 0;
      if ($totalSolicitadas < 1 || $totalSolicitadas > $sillasLibres) {
        header('Location: /page/evento/reservar?error=sin_disponibilidad');
        exit;
      }
      Session::getInstance()->set('tipo_seleccion_mesa', 'silla');
      Session::getInstance()->set('mesas_solicitadas_count', $totalSolicitadas);
      Session::getInstance()->set('cantidad_personas_seleccionada', $totalSolicitadas);
      return;
    }

    // ---- Modo MESA completa ----
    // Solo se permite UNA mesa por compra (la UI ya lo fuerza, esto lo blinda en servidor
    // ante un POST manipulado con varias capacidades).
    Session::getInstance()->set('tipo_seleccion_mesa', 'mesa');
    if (count($capacidadesSeleccionadas) !== 1) {
      header('Location: /page/evento/reservar?error=sin_disponibilidad');
      exit;
    }

    // SEGURIDAD: validar que la CANTIDAD de mesas solicitadas por cada capacidad
    // no exceda el inventario real libre (getPisosDisponibles solo confirma que
    // existe *al menos una*, no cuántas; sin esto, pedir la misma capacidad
    // repetida más veces de las que existen inflaba "cantidad_personas_seleccionada"
    // sin ningún respaldo real de mesas).
    $inventarioLibre = $mesasModel->getListMesasDisponibles(); // [{mesa_capacidad, cantidad_mesas}, ...]
    $librePorCapacidad = [];
    foreach ($inventarioLibre as $fila) {
      $librePorCapacidad[(int) $fila->mesa_capacidad] = (int) $fila->cantidad_mesas;
    }
    $solicitadasPorCapacidad = array_count_values(array_map('intval', $capacidadesSeleccionadas));
    foreach ($solicitadasPorCapacidad as $capacidadPedida => $vecesPedida) {
      $libres = $librePorCapacidad[$capacidadPedida] ?? 0;
      if ($vecesPedida > $libres) {
        header('Location: /page/evento/reservar?error=sin_disponibilidad&test=' . $capacidadPedida);
        exit;
      }
    }
    // Cuántas mesas (no personas) se solicitaron legítimamente: se usa luego en
    // confirmarmesaAction para que no se confirmen más mesas de las pedidas aquí.
    Session::getInstance()->set('mesas_solicitadas_count', count($capacidadesSeleccionadas));

    foreach ($capacidadesSeleccionadas as $capacidad) {
      $pisos = $mesasModel->getPisosDisponibles($capacidad)[0] ?? null;
      $mesasBloqueadas = $mesasBloqueoModel->getList("mesa_bloqueo_estado = 1 AND mesa_bloqueo_documento != '{$this->socio->SBE_CODI}' AND mesa_bloqueo_capacidad = $capacidad", "");

      if ((!$pisos || $pisos->total_mesas == 0) || ($mesasBloqueadas && count($mesasBloqueadas) > 0)) {
        // No hay mesas disponibles para esta capacidad
        header('Location: /page/evento/reservar?error=sin_disponibilidad&test=' . $capacidad);
        exit;
      }

      // Si solo hay una mesa disponible, bloquearla
      if ($pisos && $pisos->total_mesas == 1) {
        $mesaId = $mesasModel->getMesaDisponibleUnica($capacidad)[0]->mesa_id ?? null;
        $mesasPorCapacidad = $mesasModel->getList("mesa_capacidad = $capacidad AND (mesa_estado = 0 OR mesa_estado IS NULL OR mesa_estado = '') AND (mesa_provision IS NULL OR mesa_provision = '')");

        if (($mesaId == $mesasPorCapacidad[0]->mesa_id) && count($mesasPorCapacidad) == 1) {
          $dataBloqueoMesa = [
            'mesa_bloqueo_mesa' => $mesaId,
            'mesa_bloqueo_documento' => $this->socio->SBE_CODI,
            'mesa_bloqueo_fecha' => date('Y-m-d H:i:s'),
            'mesa_bloqueo_fecha_expiracion' => date('Y-m-d H:i:s', strtotime('+20 minutes')),
            'mesa_bloqueo_estado' => 1,
            'mesa_bloqueo_macnume' => $this->socio->MAC_NUME,
            'mesa_bloqueo_reserva' => null,
            'mesa_bloqueo_capacidad' => $mesasPorCapacidad[0]->mesa_capacidad,
          ];
          $mesaBloqueoId = $mesasBloqueoModel->insert($dataBloqueoMesa);
          // Puedes loggear si lo necesitas
          $data['bloqueo'] = $mesaBloqueoId;
          $data['log_log'] = print_r($data, true);
          $data['log_tipo'] = 'Mesa Bloqueada';
          $logModel = new Administracion_Model_DbTable_Log();
          $logModel->insert($data);
        }
      }
    }

    // Guardar en sesión la suma total de personas
    $totalPersonas = array_sum($capacidadesSeleccionadas);
    Session::getInstance()->set('cantidad_personas_seleccionada', $totalPersonas);
  }
  /**
   * Carga la vista de selección de beneficiarios (común para GET y casos especiales)
   */
  private function cargarVistaSeleccionBeneficiarios($cantidadPersonas)
  {
    $id = $this->id;
    $eventosModel = new Administracion_Model_DbTable_Eventos();
    $this->_view->evento = $evento = $eventosModel->getById($id);
    $this->_view->socio = $socio = $this->consultarSocioSession();

    // Guardar cantidad seleccionada en sesión
    Session::getInstance()->set('cantidad_personas_seleccionada', $cantidadPersonas);

    // Máximo de invitados permitidos
    $maxInvitados = intval($evento->evento_invitados_socio) ?? 0;
    $this->_view->maxInvitados = $maxInvitados;
    $this->_view->cantidadSeleccionada = $cantidadPersonas;

    // Si el evento no permite invitados no asociados, todo el cupo debe cubrirse con socios/beneficiarios
    $this->_view->invitadosPermitidos = (int) ($evento->evento_invitados_permitidos ?? 1) === 1;

    $beneficiarios = $this->consultarBeneficiariosSession();
    if (!$beneficiarios) {
      $beneficiarios = [];
      $this->_view->beneficiarios = [];
    } else {
      $this->_view->beneficiarios = $beneficiarios;
    }

    $stats = [
      'total' => 0,
      'menores_25' => 0,
      'hijos' => 0,
      'listado' => [],
      'socio_principal' => null
    ];

    $socioEncontrado = false;

    if ($beneficiarios) {
      foreach ($beneficiarios as $beneficiarioIndividual) {
        if (!is_object($beneficiarioIndividual))
          continue;

        $edad = $this->getAge($beneficiarioIndividual->SBE_FNAC ?? null);
        $esMenor25 = $edad !== null && $edad < 25;
        $esHijo = $this->esHijo($beneficiarioIndividual);

        // Verificar si es el socio principal (socio actual logueado)
        $esSocioPrincipal = false;
        if (
          $beneficiarioIndividual->SBE_CODI === $socio->SBE_CODI &&
          $beneficiarioIndividual->SBE_NOMB === $socio->sbe_nomb
        ) {
          $esSocioPrincipal = true;
          $socioEncontrado = true;
          $stats['socio_principal'] = [
            'beneficiario' => $beneficiarioIndividual,
            'edad' => $edad,
            'menor25' => $esMenor25,
            'hijo' => $esHijo,
          ];
        }

        $stats['total']++;
        if ($esMenor25)
          $stats['menores_25']++;
        if ($esHijo)
          $stats['hijos']++;

        $stats['listado'][] = [
          'beneficiario' => $beneficiarioIndividual,
          'edad' => $edad,
          'menor25' => $esMenor25,
          'hijo' => $esHijo,
          'socio_principal' => $esSocioPrincipal
        ];
      }
    }

    // Si el socio no fue encontrado en beneficiarios, agregarlo como principal
    if (!$socioEncontrado) {
      $edadSocio = $this->getAge($socio->SBE_FNAC ?? null);
      $esMenor25Socio = $edadSocio !== null && $edadSocio < 25;
      $esHijoSocio = $this->esHijo($socio);

      $stats['socio_principal'] = [
        'beneficiario' => $socio,
        'edad' => $edadSocio,
        'menor25' => $esMenor25Socio,
        'hijo' => $esHijoSocio,
      ];

      // Agregar también al listado para que aparezca en la vista
      $stats['listado'][] = [
        'beneficiario' => $socio,
        'edad' => $edadSocio,
        'menor25' => $esMenor25Socio,
        'hijo' => $esHijoSocio,
        'socio_principal' => true
      ];

      $stats['total']++;
      if ($esMenor25Socio)
        $stats['menores_25']++;
      if ($esHijoSocio)
        $stats['hijos']++;
    }

    // Verificar cuáles beneficiarios ya tienen una reserva activa confirmada
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $documentosListado = array_map(function ($item) {
      return $item['beneficiario']->SBE_CODI;
    }, $stats['listado']);
    $documentosBloqueadosReserva = !empty($documentosListado)
      ? $invitadosModel->getDocumentosEnReservasActivas($documentosListado)
      : [];

    foreach ($stats['listado'] as &$item) {
      $item['bloqueado_reserva'] = in_array($item['beneficiario']->SBE_CODI, $documentosBloqueadosReserva);
    }
    unset($item);

    $this->_view->beneficiarios = $beneficiarios ? $beneficiarios : [];
    $this->_view->beneficiariosStats = $stats;
  }

  public function procesarseleccionAction()
  {
    $this->setLayout('blanco');

    if ($_POST) {
      $cantidadPersonas = Session::getInstance()->get('cantidad_personas_seleccionada') ?? 1;
      $beneficiariosSeleccionados = json_decode($_POST['beneficiarios_seleccionados'] ?? '[]', true);
      $cantidadNoAsociados = intval($_POST['cantidad_no_asociados'] ?? 0);

      // Validar que la suma coincida con la cantidad seleccionada
      $totalSeleccionados = count($beneficiariosSeleccionados) + $cantidadNoAsociados;

      if ($totalSeleccionados != $cantidadPersonas) {
        $response = [
          'success' => false,
          'message' => "La cantidad total no coincide con la seleccionada ({$cantidadPersonas} personas)."
        ];
        echo json_encode($response);
        return;
      }

      // Si el evento no permite invitados no asociados, el cupo completo debe cubrirse con socios/beneficiarios
      $eventosModel = new Administracion_Model_DbTable_Eventos();
      $evento = $eventosModel->getById($this->id);
      $invitadosPermitidos = (int) ($evento->evento_invitados_permitidos ?? 1) === 1;
      if (!$invitadosPermitidos && $cantidadNoAsociados > 0) {
        $response = [
          'success' => false,
          'message' => "Este evento no permite invitados no asociados. Debe asignar un socio o beneficiario a las {$cantidadPersonas} boletas."
        ];
        echo json_encode($response);
        return;
      }

      // Procesar beneficiarios seleccionados
      $dataBeneficiarios = [];
      foreach ($beneficiariosSeleccionados as $beneficiario) {
        $dataBeneficiarios[] = [
          'id' => $beneficiario['id'],
          'nombre' => $beneficiario['nombre'],
          'apellido' => $beneficiario['apellido'],
          'edad' => $beneficiario['edad'] ?? null,
          'menor25' => $beneficiario['menor25'] ?? false,
          'hijo' => $beneficiario['hijo'] ?? false,
          'es_beneficiario' => true,
          'es_socio_principal' => $beneficiario['id'] == $this->socio->SBE_CODI,
          'invitadoReserva_beneficiario_principal' => $beneficiario['es_socio_principal'] ?? false,
          'invitadoReserva_beneficiario_cupo' => $beneficiario['invitadoReserva_beneficiario_cupo'] ?? '',
          'invitadoReserva_correo_invitado' => $beneficiario['invitadoReserva_correo_invitado'] ?? '',
          'invitadoReserva_fecha_nacimiento' => $beneficiario['invitadoReserva_fecha_nacimiento'] ?? '',
          'invitadoReserva_telefono' => $beneficiario['invitadoReserva_telefono'] ?? '',
          'invitadoReserva_estado_invitado' => $beneficiario['invitadoReserva_estado_invitado'] ?? '',
          'invitadoReserva_numero_carnet' => $beneficiario['invitadoReserva_numero_carnet'] ?? '',

        ];
      }

      // Guardar en sesión
      Session::getInstance()->set('reserva_data', [
        'beneficiarios_seleccionados' => $dataBeneficiarios,
        'cantidad_no_asociados' => $cantidadNoAsociados,
        'total_invitados' => count($dataBeneficiarios) + $cantidadNoAsociados
      ]);

      $response = [
        'success' => true,
        'message' => 'Datos guardados correctamente',
        'data' => [
          'total_beneficiarios' => count($dataBeneficiarios),
          'cantidad_no_asociados' => $cantidadNoAsociados,
          'total_invitados' => count($dataBeneficiarios) + $cantidadNoAsociados
        ]
      ];

      echo json_encode($response);
      return;
    }

    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
  }
  public function registrarreservaAction()
  {
    //error_reporting(E_ALL);

    $this->setLayout('blanco');
    $reservaData = Session::getInstance()->get('reserva_data');
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $beneficiariosBloqueadosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();
    $logModel = new Administracion_Model_DbTable_Log();

    foreach ($reservaData['beneficiarios_seleccionados'] as $beneficiario) {

      $bloqueos = $beneficiariosBloqueadosModel->getList(
        "beneficiario_bloqueodocumento = '{$beneficiario['id']}' AND beneficiario_bloqueo_estado = 1",
        "beneficiario_bloqueo_id ASC"
      );

      // Log detallado de la validación
      $logData = [
        'contenido_id' => $id ?? null,
        'beneficiario_id' => $beneficiario['id'],
        'beneficiario_nombre' => $beneficiario['nombre'] ?? '',
        'beneficiario_apellido' => $beneficiario['apellido'] ?? '',
        'usuario_socio' => $this->socio->SBE_CODI,
        'consulta_sql' => "beneficiario_bloqueodocumento = '{$beneficiario['id']}' AND beneficiario_bloqueo_estado = 1",
        'resultado_bloqueo' => print_r($bloqueos, true),
        'fecha_log' => date('Y-m-d H:i:s'),
        'log_tipo' => 'VALIDAR BLOQUEO BENEFICIARIO'
      ];
      $logModel->insert($logData);

      // Validar quién hizo el primer bloqueo
      if ($bloqueos && is_array($bloqueos)) {
        $primerBloqueo = $bloqueos[0];

        if ($primerBloqueo->beneficiario_bloqueo_por_asociado_documento != $this->socio->SBE_CODI) {
          // Bloqueado por otro usuario: no puede continuar
          $logFail = [
            'contenido_id' => $id ?? null,
            'beneficiario_id' => $beneficiario['id'],
            'log_tipo' => 'BLOQUEO DETECTADO',
            'detalle' => 'Primer bloqueo pertenece a otro socio',
            'fecha_log' => date('Y-m-d H:i:s')
          ];
          $logModel->insert($logFail);

          Session::getInstance()->set('errorBeneficiario', 'Uno de los beneficiarios seleccionados está bloqueado. Por favor, selecciona de nuevo los beneficiarios.');
          header('Location: /page/evento/seleccionarbeneficiarios?error=beneficiario_bloqueado&puestos=' . $reservaData['total_invitados']);
          exit;
        }

        // Si el primer bloqueo es del usuario actual, puede continuar
      }
    }
    // Obtener datos de la reserva desde la sesión
    $reservaData = Session::getInstance()->get('reserva_data');
    if (!$reservaData || (empty($reservaData['beneficiarios_seleccionados']) && empty($reservaData['cantidad_no_asociados']))) {
      header('Location: /page/evento/reservar?id=' . $this->id);
      exit;
    }

    // Datos básicos de la reserva
    $data = array();
    $data['reserva_numero_carnet'] = $this->_getSanitizedParam('reserva_numero_carnet');
    $data['reserva_nombre_cliente'] = $this->_getSanitizedParam('reserva_nombre_cliente');
    $data['reserva_apellido_cliente'] = $this->_getSanitizedParam('reserva_apellido_cliente');
    $data['reserva_telefono'] = $this->_getSanitizedParam('reserva_telefono');
    $data['reserva_correo'] = $this->_getSanitizedParam('reserva_correo');
    $data['reserva_documento'] = $this->_getSanitizedParam('reserva_documento');
    $data['reserva_fecha'] = date('Y-m-d H:i:s');
    $data['reserva_id_evento'] = $this->id;
    $data['reserva_fecha_creacion'] = date('Y-m-d H:i:s');
    $data['reserva_fecha_actualizacion'] = date('Y-m-d H:i:s');
    $data['reserva_fecha_inicio_reserva'] = date('Y-m-d H:i:s');
    $data['reserva_fecha_cierre_reserva'] = Session::getInstance()->get('sesion')->accion_sesion_fecha_fin ?? date('Y-m-d H:i:s', strtotime('+20 minutes'));
    $data['reserva_fecha_limite_pago'] = date('Y-m-d H:i:s', strtotime('+20 minutes'));
    $data['reserva_estado'] = '1'; // Reserva iniciada

    $this->logAuditoria('INICIO_REGISTRO_RESERVA', null, [
      'datos_json' => $reservaData,
      'observaciones' => 'Iniciando registro de nueva reserva'
    ]);

    // Insertar reserva en la base de datos
    //error_reporting(E_ALL);
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reservaId = $reservasModel->insert($data);
    $this->logAuditoria('RESERVA_CREADA', $reservaId, [
      'estado_nuevo' => '1',
      'datos_json' => $data,
      'observaciones' => 'Reserva creada exitosamente'
    ]);
    $reservaInfo = $reservasModel->getById($reservaId);
    if (!$reservaInfo) {
      // Si no se pudo crear la reserva, redirigir
      header('Location: /page/reservar?error=reserva_no_creada');
      exit;
    } else {
      // Si se cre la reserva, guardar el ID en la sesión
      Session::getInstance()->set('reserva', $reservaInfo);
    }

    // Procesar beneficiarios seleccionados
    $orden = 1;
    // Escapar valores de sesión (vienen de JSON del cliente, no de _getSanitizedParam)
    $dbConn = App::getDbConnection()->getConnection();
    $esc = function($v) use ($dbConn) {
      return mysqli_real_escape_string($dbConn, (string)($v ?? ''));
    };

    foreach ($reservaData['beneficiarios_seleccionados'] as $beneficiario) {
      $data_invitados = array();
      $data_invitados['documento_invitado'] = $esc($beneficiario['id']);
      $data_invitados['invitadoReserva_nombre_invitado'] = $esc($beneficiario['nombre']);
      $data_invitados['invitadoReserva_apellido_invitado'] = $esc($beneficiario['apellido']);
      $fechaNac = $beneficiario['invitadoReserva_fecha_nacimiento'] ?? '';
      try {
        $data_invitados['invitadoReserva_fecha_nacimiento'] = $fechaNac ? (new DateTime($fechaNac))->format('Y-m-d') : null;
      } catch (Exception $ex) {
        $data_invitados['invitadoReserva_fecha_nacimiento'] = null;
      }
      $data_invitados['invitadoReserva_correo_invitado'] = $esc($beneficiario['invitadoReserva_correo_invitado']);
      $data_invitados['invitadoReserva_telefono'] = $esc($beneficiario['invitadoReserva_telefono']);
      $data_invitados['reserva_id_reserva'] = $reservaId;
      $data_invitados['invitadoReserva_estado_invitado'] = $esc($beneficiario['invitadoReserva_estado_invitado'] ?? 'A');
      $data_invitados['invitado_tipo'] = '1'; // Beneficiario
      $data_invitados['invitado_evento'] = $this->id;
      $data_invitados['invitadosReserva_fecha_creacion'] = date('Y-m-d H:i:s');
      $data_invitados['invitadosReserva_fecha_actualizacion'] = date('Y-m-d H:i:s');
      $data_invitados['invitadosReserva_usuario_creacion'] = Session::getInstance()->get('ncar');
      $data_invitados['invitadosReserva_actualizacion'] = Session::getInstance()->get('ncar');
      $data_invitados['invitadoReserva_beneficiario_menor25'] = $beneficiario['menor25'] ? 1 : 0;
      $data_invitados['invitadoReserva_beneficiario_hijo'] = $beneficiario['hijo'] ? 1 : 0;
      $data_invitados['invitadoReserva_beneficiario_principal'] = $beneficiario['es_socio_principal'] ? 1 : 0;
      $data_invitados['invitadoReserva_beneficiario_cupo'] = $esc($beneficiario['invitadoReserva_beneficiario_cupo'] ?? '');
      $data_invitados['invitadoReserva_numero_carnet'] = $esc($beneficiario['invitadoReserva_numero_carnet'] ?? '');
      $data_invitados['orden'] = $orden++;
      $this->logAuditoria('INVITADO_AGREGADO', $reservaId, [
        'datos_json' => $beneficiario,
        'observaciones' => 'Beneficiario agregado: ' . $beneficiario['nombre']
      ]);
      $invitadosModel->insert($data_invitados);
    }

    // Procesar invitados no asociados (solo crear registros según cantidad)
    for ($i = 1; $i <= $reservaData['cantidad_no_asociados']; $i++) {
      $data_invitados = array();
      $data_invitados['documento_invitado'] = null;
      $data_invitados['invitadoReserva_nombre_invitado'] = 'Invitado No. ' . $i;
      $data_invitados['invitadoReserva_apellido_invitado'] = '';
      $data_invitados['invitadoReserva_fecha_nacimiento'] = null;
      $data_invitados['invitadoReserva_correo_invitado'] = null;
      $data_invitados['invitadoReserva_telefono'] = null;
      $data_invitados['reserva_id_reserva'] = $reservaId;
      $data_invitados['invitadoReserva_estado_invitado'] = 'P'; // Pendiente de confirmación
      $data_invitados['invitado_tipo'] = '2'; // No asociado
      $data_invitados['invitado_evento'] = $this->id;
      $data_invitados['invitadosReserva_fecha_creacion'] = date('Y-m-d H:i:s');
      $data_invitados['invitadosReserva_fecha_actualizacion'] = date('Y-m-d H:i:s');
      $data_invitados['invitadosReserva_usuario_creacion'] = Session::getInstance()->get('ncar');
      $data_invitados['invitadosReserva_actualizacion'] = Session::getInstance()->get('ncar');
      $data_invitados['orden'] = $orden++;

      $this->logAuditoria('INVITADO_AGREGADO', $reservaId, [
        'datos_json' => $data_invitados,
        'observaciones' => 'Invitado agregado: ' . $data_invitados['invitadoReserva_nombre_invitado'] . ' ' . $data_invitados['invitadoReserva_apellido_invitado']
      ]);
      $invitadosModel->insert($data_invitados);
    }

    // Limpiar datos de la sesin
    Session::getInstance()->set('reserva_data', null);

    // Redirigir siempre a reservar mesa

    header('Location: /page/evento/reservarmesa?id=' . enc_id($reservaId));
  }

  /**
   * Agrega los beneficiarios seleccionados en sesión como invitados de la reserva
   * existente ligada a un cupo adicional, y redirige al resumen de pago normal
   * (resumenAction) acotado a esos cupos mediante el parámetro cupos_id.
   */
  public function agregarcuposreservaAction()
  {
    $cuposId = $this->_getDecryptedParam('cupos_id');
    $reservaData = Session::getInstance()->get('reserva_data');

    $cuposModel = new Administracion_Model_DbTable_Reservacuposadicionales();
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();

    $cupos = $cuposModel->getById($cuposId);
    if (!$cupos || (int) $cupos->cupos_estado !== 0) {
      header('Location: /page/evento/reservar');
      exit;
    }

    $reserva = $reservasModel->getById($cupos->reserva_id);
    if (!$reserva || $reserva->reserva_documento != $this->socio->SBE_CODI) {
      header('Location: /page/evento/reservar');
      exit;
    }

    if (!$reservaData || (empty($reservaData['beneficiarios_seleccionados']) && empty($reservaData['cantidad_no_asociados']))) {
      header('Location: /page/evento/seleccionarbeneficiarios?cupos_id=' . enc_id($cuposId));
      exit;
    }

    // Validar bloqueos de beneficiarios (mismo criterio que al registrar una reserva nueva)
    $beneficiariosBloqueadosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();
    foreach ($reservaData['beneficiarios_seleccionados'] as $beneficiario) {
      $bloqueos = $beneficiariosBloqueadosModel->getList(
        "beneficiario_bloqueodocumento = '{$beneficiario['id']}' AND beneficiario_bloqueo_estado = 1",
        "beneficiario_bloqueo_id ASC"
      );
      if ($bloqueos && is_array($bloqueos)) {
        $primerBloqueo = $bloqueos[0];
        if ($primerBloqueo->beneficiario_bloqueo_por_asociado_documento != $this->socio->SBE_CODI) {
          Session::getInstance()->set('errorBeneficiario', 'Uno de los beneficiarios seleccionados está bloqueado. Por favor, selecciona de nuevo los beneficiarios.');
          header('Location: /page/evento/seleccionarbeneficiarios?cupos_id=' . enc_id($cuposId) . '&error=beneficiario_bloqueado');
          exit;
        }
      }
    }

    // Insertar los invitados seleccionados directamente en la reserva existente
    $orden = (int) $invitadosModel->getListCount("reserva_id_reserva = '{$reserva->id}'")[0]->total + 1;
    $idsInsertados = [];

    foreach ($reservaData['beneficiarios_seleccionados'] as $beneficiario) {
      $fechaNac = $beneficiario['invitadoReserva_fecha_nacimiento'] ?? '';
      try {
        $fechaNacFormateada = $fechaNac ? (new DateTime($fechaNac))->format('Y-m-d') : null;
      } catch (Exception $ex) {
        $fechaNacFormateada = null;
      }
      $idsInsertados[] = $invitadosModel->insert([
        'documento_invitado' => $beneficiario['id'],
        'invitadoReserva_nombre_invitado' => $beneficiario['nombre'],
        'invitadoReserva_apellido_invitado' => $beneficiario['apellido'],
        'invitadoReserva_fecha_nacimiento' => $fechaNacFormateada,
        'invitadoReserva_correo_invitado' => $beneficiario['invitadoReserva_correo_invitado'] ?? '',
        'invitadoReserva_telefono' => $beneficiario['invitadoReserva_telefono'] ?? '',
        'reserva_id_reserva' => $reserva->id,
        'invitadoReserva_estado_invitado' => $beneficiario['invitadoReserva_estado_invitado'] ?? 'A',
        'invitado_tipo' => '1',
        'invitado_evento' => $this->id,
        'invitadosReserva_fecha_creacion' => date('Y-m-d H:i:s'),
        'invitadosReserva_fecha_actualizacion' => date('Y-m-d H:i:s'),
        'invitadosReserva_usuario_creacion' => Session::getInstance()->get('ncar'),
        'invitadosReserva_actualizacion' => Session::getInstance()->get('ncar'),
        'invitadoReserva_beneficiario_menor25' => $beneficiario['menor25'] ? 1 : 0,
        'invitadoReserva_beneficiario_hijo' => $beneficiario['hijo'] ? 1 : 0,
        'invitadoReserva_beneficiario_principal' => $beneficiario['es_socio_principal'] ? 1 : 0,
        'invitadoReserva_beneficiario_cupo' => $beneficiario['invitadoReserva_beneficiario_cupo'] ?? '',
        'invitadoReserva_numero_carnet' => $beneficiario['invitadoReserva_numero_carnet'] ?? '',
        'orden' => $orden++,
      ]);
    }
    // Continuar el consecutivo "Invitado No. X" en vez de reiniciar en 1
    $noAsociadosExistentes = (int) $invitadosModel->getListCount("reserva_id_reserva = '{$reserva->id}' AND invitado_tipo = '2'")[0]->total;
    for ($i = 1; $i <= (int) ($reservaData['cantidad_no_asociados'] ?? 0); $i++) {
      $numero = $noAsociadosExistentes + $i;
      $idsInsertados[] = $invitadosModel->insert([
        'documento_invitado' => null,
        'invitadoReserva_nombre_invitado' => 'Invitado No. ' . $numero,
        'invitadoReserva_apellido_invitado' => '',
        'invitadoReserva_fecha_nacimiento' => null,
        'invitadoReserva_correo_invitado' => null,
        'invitadoReserva_telefono' => null,
        'reserva_id_reserva' => $reserva->id,
        'invitadoReserva_estado_invitado' => 'P',
        'invitado_tipo' => '2',
        'invitado_evento' => $this->id,
        'invitadosReserva_fecha_creacion' => date('Y-m-d H:i:s'),
        'invitadosReserva_fecha_actualizacion' => date('Y-m-d H:i:s'),
        'invitadosReserva_usuario_creacion' => Session::getInstance()->get('ncar'),
        'invitadosReserva_actualizacion' => Session::getInstance()->get('ncar'),
        'orden' => $orden++,
      ]);
    }

    $cuposModel->editField($cuposId, 'invitados_ids', implode(',', $idsInsertados));

    $this->logAuditoria('CUPOS_INVITADOS_AGREGADOS', $reserva->id, [
      'cupos_id' => $cuposId,
      'invitados_ids' => $idsInsertados,
      'observaciones' => 'Invitados de cupos adicionales agregados a la reserva, pendiente de pago'
    ]);

    Session::getInstance()->set('reserva_data', null);

    header('Location: /page/evento/resumen?id=' . enc_id($reserva->id) . '&cupos_id=' . enc_id($cuposId));
    exit;
  }

  public function reservarmesaAction()
  {
    $id = $this->_getDecryptedParam('id');

    //Limpiar mesa si recarga reserva

    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $mesasModel = new Administracion_Model_DbTable_Mesas();

    // Obtener datos de la reserva
    $reserva = $reservasModel->getById($id);
    //print_r($reserva);
    if (!$reserva) {
      // Si no se encuentra la reserva, redirigir
      header('Location: /page/evento/reservar?error=reserva_no_encontrada');
      exit;
    }

    // Obtener datos de la reserva
    $this->_view->reserva = $reserva = $reservasModel->getById($id);
    if ($id && $reserva && $reserva->reserva_mesa_id) {
      // Liberar mesas
      $mesaReserva = $reserva->reserva_mesa_id;

      if ($mesaReserva) {
        $mesaIds = explode(',', $mesaReserva); // Convertir en array

        foreach ($mesaIds as $key => $idMesa) {

          $idMesa = trim($idMesa); // Eliminar espacios por si acaso
          if ($idMesa) {
            $infoMesa[$key] = $mesasModel->getById($idMesa);
            $mesasModel->editField($idMesa, 'mesa_estado', 0);
          }
        }
      }
      $reservasModel->editField($id, 'reserva_mesa_id', 0);
    }
    $eventosModel = new Administracion_Model_DbTable_Eventos();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();



    $this->_view->evento = $evento = $eventosModel->getById($reserva->reserva_id_evento);
    $this->_view->socio = $this->consultarSocioSession();

    // Obtener todos los invitados de la reserva para mostrar cantidad
    $invitados = $invitadosModel->getList("reserva_id_reserva = '$id'", "");
    $this->_view->cantidadPersonas = $cantidadPersonas = count($invitados);
    $mesasSeleccionadas = [];
    if (isset($_COOKIE['mesas_seleccionadas'])) {
      $mesasSeleccionadas = json_decode($_COOKIE['mesas_seleccionadas'], true);
    }

    // Modo de selección ('mesa' | 'silla'). En sillas cada pestaña elige 1 silla (capacidad 1)
    // y todas deben pertenecer al mismo ambiente.
    $tipoSeleccion = Session::getInstance()->get('tipo_seleccion_mesa') ?: 'mesa';
    $this->_view->tipoSeleccion = $tipoSeleccion;

    $pisosPorCapacidad = [];
    if ($tipoSeleccion === 'silla') {
      // Para sillas el listado de pisos es el mismo en todas las pestañas.
      $pisosSillas = $mesasModel->getPisosDisponiblesSillas();
      $this->_view->pisosDisponibles = $pisosSillas;
      foreach ($mesasSeleccionadas as $index => $capacidad) {
        $pisosPorCapacidad[$index] = $pisosSillas;
      }
    } else {
      // Obtener pisos disponibles para la capacidad requerida
      $this->_view->pisosDisponibles = $mesasModel->getPisosDisponibles($mesasSeleccionadas[0]);
      foreach ($mesasSeleccionadas as $index => $capacidad) {
        $pisosPorCapacidad[$index] = $mesasModel->getPisosDisponibles($capacidad);
      }
    }
    $this->_view->pisosPorCapacidad = $pisosPorCapacidad;
    // Pasar la capacidad para uso en JavaScript
    $this->_view->capacidadRequerida = $cantidadPersonas;
  }

  /**
   * Obtiene ambientes disponibles por piso - AJAX
   */
  public function getambientesAction()
  {
    // error_reporting(E_ALL);
    $this->setLayout('blanco');
    header('Content-Type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    $pisoId = $this->_getSanitizedParam('piso_id');
    $capacidad = $this->_getSanitizedParam('capacidad');
    $tipo = $this->_getSanitizedParam('tipo') ?: 'mesa';
    // echo $pisoId . ' - ' . $capacidad;
    if ($pisoId <= 0 || ($tipo !== 'silla' && $capacidad <= 0)) {
      echo json_encode([
        'success' => false,
        'message' => 'Parámetros inválidos'
      ]);
      return;
    }

    try {
      $mesasModel = new Administracion_Model_DbTable_Mesas();
      $ambientes = ($tipo === 'silla')
        ? $mesasModel->getAmbientesPorPisoSillas($pisoId)
        : $mesasModel->getAmbientesPorPiso($pisoId, $capacidad);

      echo json_encode([
        'success' => true,
        'data' => $ambientes,
        'count' => count($ambientes)
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => 'Error al obtener ambientes: ' . $e->getMessage()
      ]);
    }
  }

  /**
   * Obtiene mesas disponibles por ambiente - AJAX
   */
  public function getmesasAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    $ambienteId = $this->_getSanitizedParam('ambiente_id');
    $capacidad = $this->_getSanitizedParam('capacidad');
    $tipo = $this->_getSanitizedParam('tipo') ?: 'mesa';

    if ($ambienteId <= 0 || ($tipo !== 'silla' && $capacidad <= 0)) {
      echo json_encode([
        'success' => false,
        'message' => 'Parámetros inválidos'
      ]);
      return;
    }

    try {
      $mesasModel = new Administracion_Model_DbTable_Mesas();
      $ambienteModel = new Administracion_Model_DbTable_Ambientes();
      if ($tipo === 'silla') {
        $todosElementos = $mesasModel->getTodosElementosPorAmbienteSillas($ambienteId);
        $mesas = $mesasModel->getSillasPorAmbiente($ambienteId);
      } else {
        $todosElementos = $mesasModel->getTodosElementosPorAmbiente($ambienteId, $capacidad);
        $mesas = $mesasModel->getMesasPorAmbiente($ambienteId, $capacidad);
      }
      $ambiente = $ambienteModel->getById($ambienteId);

      $res = [
        'success' => true,
        'data' => $mesas,
        'todos_elementos' => $todosElementos,
        'count' => count($mesas)
      ];
      if ($ambiente) {
        $res['ambiente'] = $ambiente;
      }
      echo json_encode($res);
      return;
    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => 'Error al obtener mesas: ' . $e->getMessage()
      ]);
    }
  }

  /* Obtiene disponibilddad de mesas por AJAX*/
  public function disponiblesmesasAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $cantidadPersonas = $this->_getSanitizedParam('cantidadPersonas');
    $mesasSeleccionadasRaw = $this->_getSanitizedParam('mesasSeleccionadas');
    $mesasModel = new Administracion_Model_DbTable_Mesas();


    $pisos = [];

    // Si viene el arreglo de mesas seleccionadas y es válido
    if ($mesasSeleccionadasRaw) {
      $mesasSeleccionadas = json_decode($mesasSeleccionadasRaw, true);
      // print_r($mesasSeleccionadas);
      // return;
      if (is_array($mesasSeleccionadas) && count($mesasSeleccionadas) > 0) {
        // Buscar pisos disponibles para cada capacidad seleccionada
        foreach ($mesasSeleccionadas as $capacidad) {
          $pisosPorCapacidad = $mesasModel->getPisosDisponibles($capacidad);
          $pisos[] = [
            'capacidad' => $capacidad,
            'pisos' => $pisosPorCapacidad
          ];
        }
        echo json_encode(['pisosDisponibles' => $pisos]);
        exit;
      }
    }

    // Si no viene arreglo, usar el flujo tradicional
    $pisos = $mesasModel->getPisosDisponibles($cantidadPersonas);
    echo json_encode(['pisosDisponibles' => $pisos]);
    exit;
  }


  /**
   * Confirma la selección de mesa y actualiza la reserva
   */
  public function confirmarmesaAction()
  {
    if ($_POST) {
      $reservaId = $this->_getDecryptedParam('reserva_id');
      $mesaId = $this->_getSanitizedParam('mesa_seleccionada');

      if (!$reservaId || !$mesaId) {
        // Redirigir con error
        header('Location: /page/evento/reservarmesa?id=' . enc_id($reservaId) . '&error=datos_incompletos');
        exit;
      }

      try {
        $reservasModel = new Administracion_Model_DbTable_Reservas();
        $mesasModel = new Administracion_Model_DbTable_Mesas();

        $reserva = $reservasModel->getById($reservaId);

        if (!$reserva) {
          header('Location: /page/evento/reservarmesa?id=' . enc_id($reservaId) . '&error=reserva_no_encontrada');
          exit;
        }

        // Validar que la reserva pertenece al socio logueado
        if ($reserva->reserva_documento !== $this->socio->SBE_CODI) {
          $this->logAuditoria('MESA_CONFIRMADA_ACCESO_DENEGADO', $reservaId, [
            'observaciones' => 'Intento de confirmar mesa de reserva ajena',
            'reserva_documento' => $reserva->reserva_documento,
            'socio_actual' => $this->socio->SBE_CODI
          ]);
          header('Location: /page/evento/');
          exit;
        }

        $this->logAuditoria('MESA_CONFIRMADA', $reservaId, [
          'mesa_id_anterior' => $reserva->reserva_mesa_id,
          'mesa_id_nuevo' => $mesaId,
          'observaciones' => 'Mesa confirmada por el usuario'
        ]);

        $mesaIds = array_filter(array_map('trim', explode(',', $mesaId)));

        // Modo de selección ('mesa' | 'silla'): en mesa solo se permite 1 elemento por compra.
        $tipoSeleccion = Session::getInstance()->get('tipo_seleccion_mesa') ?: 'mesa';
        if ($tipoSeleccion === 'mesa' && count($mesaIds) !== 1) {
          $this->logAuditoria('MESA_CONFIRMADA_MULTIPLE_NO_PERMITIDA', $reservaId, [
            'observaciones' => 'En modo mesa solo se permite 1 mesa por compra',
            'mesas_enviadas' => count($mesaIds)
          ]);
          header('Location: /page/evento/reservarmesa?id=' . enc_id($reservaId) . '&error=mesa_no_disponible');
          exit;
        }

        // SEGURIDAD: no confirmar más mesas de las que se solicitaron legítimamente
        // en la selección de capacidad (evita inflar "stepIndex"/la cookie
        // mesas_seleccionadas para reclamar mesas de más).
        $mesasSolicitadas = Session::getInstance()->get('mesas_solicitadas_count');
        if ($mesasSolicitadas !== null && count($mesaIds) > (int) $mesasSolicitadas) {
          $this->logAuditoria('MESA_CONFIRMADA_EXCESO_MESAS', $reservaId, [
            'observaciones' => 'Se intentaron confirmar más mesas de las solicitadas',
            'mesas_solicitadas' => $mesasSolicitadas,
            'mesas_enviadas' => count($mesaIds)
          ]);
          header('Location: /page/evento/reservarmesa?id=' . enc_id($reservaId) . '&error=mesa_no_disponible');
          exit;
        }

        // SEGURIDAD: la capacidad total de las mesas confirmadas debe alcanzar
        // para los invitados reales de la reserva (dato de BD, no de sesión/URL).
        $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
        $totalInvitadosReserva = count($invitadosModel->getList("reserva_id_reserva = '$reservaId'", ""));

        // Paso 1: validar todas las mesas antes de escribir ninguna
        $capacidadTotalMesas = 0;
        $ambientesSeleccionados = [];
        foreach ($mesaIds as $idMesa) {
          $mesa = $mesasModel->getById($idMesa);
          if (!$mesa) {
            header('Location: /page/evento/reservarmesa?id=' . enc_id($reservaId) . '&error=mesa_no_encontrada');
            exit;
          }

          // El tipo del elemento debe coincidir con el modo de selección (no mezclar mesa/silla).
          if ($mesa->mesa_tipo !== $tipoSeleccion) {
            header('Location: /page/evento/reservarmesa?id=' . enc_id($reservaId) . '&error=mesa_no_disponible');
            exit;
          }
          $ambientesSeleccionados[$mesa->mesa_ambiente] = true;

          $mesaLibre = ($mesa->mesa_estado == '0' || $mesa->mesa_estado == '' || $mesa->mesa_estado === null)
            && ($mesa->mesa_provision === null || $mesa->mesa_provision === '');
          $mesaPropia = ($mesa->mesa_estado == '1' && in_array($idMesa, array_map('trim', explode(',', $reserva->reserva_mesa_id))));

          if (!$mesaLibre && !$mesaPropia) {
            header('Location: /page/evento/reservarmesa?id=' . enc_id($reservaId) . '&error=mesa_no_disponible');
            exit;
          }
          $capacidadTotalMesas += (int) $mesa->mesa_capacidad;
        }

        // En modo sillas todas deben pertenecer al mismo ambiente.
        if ($tipoSeleccion === 'silla' && count($ambientesSeleccionados) > 1) {
          $this->logAuditoria('SILLAS_CONFIRMADAS_AMBIENTE_MIXTO', $reservaId, [
            'observaciones' => 'Se intentaron confirmar sillas de distintos ambientes',
            'ambientes' => array_keys($ambientesSeleccionados)
          ]);
          header('Location: /page/evento/reservarmesa?id=' . enc_id($reservaId) . '&error=ambiente_mixto');
          exit;
        }

        if ($totalInvitadosReserva > 0 && $capacidadTotalMesas < $totalInvitadosReserva) {
          $this->logAuditoria('MESA_CONFIRMADA_CAPACIDAD_INSUFICIENTE', $reservaId, [
            'observaciones' => 'La capacidad de las mesas seleccionadas no alcanza para los invitados de la reserva',
            'capacidad_mesas' => $capacidadTotalMesas,
            'total_invitados' => $totalInvitadosReserva
          ]);
          header('Location: /page/evento/reservarmesa?id=' . enc_id($reservaId) . '&error=mesa_no_disponible');
          exit;
        }

        // Paso 2: bloquear atómicamente cada mesa con UPDATE condicional
        foreach ($mesaIds as $idMesa) {
          $mesaPropia = in_array($idMesa, array_map('trim', explode(',', $reserva->reserva_mesa_id)));
          if (!$mesaPropia) {
            $mesasModel->updateEstadoAtomico('1', $idMesa);
            if (mysqli_affected_rows($mesasModel->getConnection()) === 0) {
              // Otro usuario se adelantó justo ahora — liberar las que ya bloqueamos
              foreach ($mesaIds as $idMesaLiberada) {
                if ($idMesaLiberada === $idMesa)
                  break;
                $mesasModel->updateEstado('0', $idMesaLiberada);
              }
              header('Location: /page/evento/reservarmesa?id=' . enc_id($reservaId) . '&error=mesa_no_disponible');
              exit;
            }
          }
        }

        // Paso 3: vincular mesas a la reserva y redirigir
        $reservasModel->updateReservaMesa(['reserva_mesa_id' => $mesaId], $reservaId);
        header('Location: /page/evento/resumen?id=' . enc_id($reservaId));
        exit;

      } catch (Exception $e) {
        error_log('Error al confirmar mesa: ' . $e->getMessage());
        header('Location: /page/evento/reservarmesa?id=' . enc_id($reservaId) . '&error=error_sistema');
        exit;
      }
    }

    // Si no es POST, redirigir
    header('Location: /page/evento/');
    exit;
  }

  public function resumenAction()
  {


    $reservaCreada = Session::getInstance()->get('reservacreada');
    if ($reservaCreada === "oklogout") {
      header('Location: /page/login/logout');
      exit;
    }

    $id = $this->_getDecryptedParam('id');
    $error = $this->_getSanitizedParam('error');
    $errorMessage = Session::getInstance()->get('error_pago');
    if ($error && $errorMessage != '') {
      $this->_view->errorMessage = $errorMessage;
      Session::getInstance()->set('error_pago', null);
      $this->_view->error = $error;
    }
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $eventosModel = new Administracion_Model_DbTable_Eventos();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $mesasModel = new Administracion_Model_DbTable_Mesas();

    // Modo "cupos adicionales": se va a cobrar solo el delta de cupos agregados a
    // una reserva ya pagada, no el total de la reserva
    $cuposId = $this->_getDecryptedParam('cupos_id');
    $cupos = null;
    if ($cuposId) {
      $cuposModel = new Administracion_Model_DbTable_Reservacuposadicionales();
      $cuposCandidato = $cuposModel->getById($cuposId);
      if ($cuposCandidato && (int) $cuposCandidato->cupos_estado === 0 && $cuposCandidato->reserva_id == $id) {
        $cupos = $cuposCandidato;
      }
    }
    $this->_view->cuposId = $cupos ? $cupos->id : null;

    // Obtener datos de la reserva
    $reserva = $reservasModel->getById($id);
    //print_r($reserva);
    if (!$reserva) {
      // Si no se encuentra la reserva, redirigir
      header('Location: /page/evento/reservar?error=reserva_no_encontrada');
      exit;
    }
    // Verificación de propiedad: la reserva debe pertenecer al socio en sesión
    if (!$this->socio || !$this->socio->SBE_CODI || $reserva->reserva_documento != $this->socio->SBE_CODI) {
      $this->logAuditoria('ACCESO_DENEGADO_RESUMEN', $id, [
        'observaciones' => 'Intento de ver el resumen de una reserva ajena',
        'reserva_documento' => $reserva->reserva_documento,
        'socio_actual' => $this->socio->SBE_CODI ?? null
      ]);
      header('Location: /page/evento/');
      exit;
    }
    // Si la reserva está pendiente de pago y tiene processUrl, redirigir a Placetopay
    // if ($reserva->reserva_estado == 7 && !empty($reserva->request_id)) {

    //   if (!empty($reserva->reserva_processurl)) {
    //     header('Location: ' . $reserva->reserva_processurl);
    //     exit;
    //   }

    // }
    if (!$cupos && $reserva->reserva_estado != 1) {
      //$reservasModel->editField($id, 'reserva_estado', 1);
      $reservasModel->editField($id, 'reserva_estado_texto', 'El socio entró a placetopay y se retrocedió');
    }
    $this->_view->reserva = $reserva;
    $this->_view->evento = $evento = $eventosModel->getById($reserva->reserva_id_evento);
    $this->_view->socio = $socio = $this->consultarSocioSession();

    // Obtener información de la mesa y categoría para precios
    $mesaInfo = [];
    $mesasCompletas = [];
    $categoria = null;
    //print_r($reserva);

    if ($reserva->reserva_mesa_id) {
      // Separar mltiples IDs de mesa
      $idsMesa = explode(',', $reserva->reserva_mesa_id);
      $idsMesa = array_map('trim', $idsMesa);
      $idsMesaSQL = implode("','", $idsMesa);

      // Obtener todas las mesas con sus detalles
      $mesasCompletas = $mesasModel->getMesasConDetalles("m.mesa_id IN ('$idsMesaSQL')");

      if (!empty($mesasCompletas)) {
        $mesaInfo = $mesasCompletas;

        // Obtener categoría (solo se toma la del primer ambiente por ahora)
        if ($mesasCompletas[0]->categoria_id) {
          $categoriasModel = new Administracion_Model_DbTable_Categorias();
          $categoria = $categoriasModel->getById($mesasCompletas[0]->categoria_id);
        }
      }
    }

    $this->_view->mesaInfo = $mesaInfo;
    $this->_view->categoria = $categoria;

    // Obtener todos los invitados de la reserva (socio + invitados)
    $todosInvitados = $invitadosModel->getList("reserva_id_reserva = '$id'", "");

    // En modo cupos, solo se cobran/muestran los invitados agregados con este cupo
    $invitados = $todosInvitados;
    if ($cupos) {
      $idsCupos = array_filter(array_map('trim', explode(',', $cupos->invitados_ids ?? '')));
      $invitados = array_values(array_filter($todosInvitados, function ($inv) use ($idsCupos) {
        return in_array((string) $inv->id_invitado, $idsCupos, true);
      }));
    }

    // "hayCupo" (cupo/crédito del socio en el club) se calcula siempre sobre TODOS
    // los invitados de la reserva, independiente de cuáles se estén cobrando ahora
    $hayCupo = false;
    foreach ($todosInvitados as $invitado) {
      if ($invitado->documento_invitado == $socio->SBE_CODI && $invitado->invitadoReserva_beneficiario_principal == 1) {
        if (intval($invitado->invitadoReserva_beneficiario_cupo) > 0) {
          $hayCupo = true;
        }
      } else {
        $beneficiarios = $this->consultarBeneficiariosSession();
        foreach ($beneficiarios as $beneficiario) {
          if ($socio->SBE_CODI === $beneficiario->SBE_CODI) {
            if ($beneficiario->SBE_CUPO && $beneficiario->SBE_CUPO > 0) {
              $hayCupo = true;
            }
          }
        }
      }
    }

    // Si la reserva es de sillas individuales, el precio se calcula por tipo de
    // participante (socio / socio hijo <25 / invitado) IGUAL que las mesas, pero con
    // montos propios de silla (categoria_precio_silla_*), nunca los de mesa.
    // Prioridad: 1) mesa_precio de la silla concreta (override manual, opcional),
    // 2) tarifa de silla de la categoría del ambiente según tipo de participante,
    // 3) ambiente_precio_silla (precio general del ambiente) si la categoría no tiene
    // tarifas de silla configuradas, 4) $0 si no hay ningún precio definido.
    $esModoSilla = !empty($mesasCompletas) && ($mesasCompletas[0]->mesa_tipo === 'silla');
    $this->_view->esModoSilla = $esModoSilla;

    // Calcular precios para cada invitado a cobrar
    $totalGeneral = 0;
    $idxSilla = 0;
    foreach ($invitados as $invitado) {
      $invitado->es_socio_principal = ($invitado->documento_invitado == $socio->SBE_CODI && $invitado->invitadoReserva_beneficiario_principal == 1);

      // Calcular precio según tipo de participante
      $precio = 0;
      $tipoParticipante = 'N/A';

      if ($esModoSilla) {
        $silla = isset($mesasCompletas[$idxSilla]) ? $mesasCompletas[$idxSilla] : null;
        $idxSilla++;

        if ($silla && $silla->mesa_precio !== null && $silla->mesa_precio !== '') {
          // Override manual: esta silla concreta tiene un precio fijo asignado.
          $precio = (float) $silla->mesa_precio;
          $tipoParticipante = 'Silla';
        } elseif ($categoria) {
          if ($invitado->invitado_tipo == '1') { // Beneficiario
            $esMenor25 = ($invitado->invitadoReserva_beneficiario_menor25) && $invitado->invitadoReserva_beneficiario_menor25;
            $esHijo = ($invitado->invitadoReserva_beneficiario_hijo) && $invitado->invitadoReserva_beneficiario_hijo;
            if ($esMenor25 && $esHijo) {
              $precio = $categoria->categoria_precio_silla_socio_hijo;
              $tipoParticipante = 'Beneficiario Hijo < 25';
              if ($invitado->invitadoReserva_estado_invitado == 'S') {
                $tipoParticipante = 'Cosocio Hijo < 25';
              }
            } else {
              $precio = $categoria->categoria_precio_silla_socio;
              $tipoParticipante = 'Beneficiario';
              if ($invitado->invitadoReserva_estado_invitado == 'S') {
                $tipoParticipante = 'Cosocio';
              }
            }
          } else { // Invitado
            $precio = $categoria->categoria_precio_silla_invitado;
            $tipoParticipante = 'Invitado';
          }
          // La categoría no tiene tarifas de silla configuradas (todo NULL/vacío):
          // respaldo al precio general del ambiente.
          if ($precio === null || $precio === '') {
            $precio = ($silla && $silla->ambiente_precio_silla !== null && $silla->ambiente_precio_silla !== '')
              ? (float) $silla->ambiente_precio_silla
              : 0;
          }
        } elseif ($silla && $silla->ambiente_precio_silla !== null && $silla->ambiente_precio_silla !== '') {
          // Sin categoría asociada al ambiente: precio general del ambiente.
          $precio = (float) $silla->ambiente_precio_silla;
          $tipoParticipante = 'Silla';
        } else {
          $precio = 0;
          $tipoParticipante = 'Silla';
        }
      } elseif ($categoria) {
        if ($invitado->invitado_tipo == '1') { // Beneficiario
          $esMenor25 = ($invitado->invitadoReserva_beneficiario_menor25) && $invitado->invitadoReserva_beneficiario_menor25;

          $esHijo = ($invitado->invitadoReserva_beneficiario_hijo) && $invitado->invitadoReserva_beneficiario_hijo;
          if ($esMenor25 && $esHijo) {
            $precio = $categoria->categoria_precio_socio_hijo;
            $tipoParticipante = 'Beneficiario Hijo < 25';
            if ($invitado->invitadoReserva_estado_invitado == 'S') {
              $tipoParticipante = 'Cosocio Hijo < 25';
            }
          } else {
            $precio = $categoria->categoria_precio_socio;
            $tipoParticipante = 'Beneficiario';
            if ($invitado->invitadoReserva_estado_invitado == 'S') {
              $tipoParticipante = 'Cosocio';
            }
          }
        } else { // Invitado
          $precio = $categoria->categoria_precio_invitado;
          $tipoParticipante = 'Invitado';
        }
      }

      $invitado->precio_boleta = $precio;
      $invitado->tipo_participante = $tipoParticipante;
      $totalGeneral += $precio;
    }
    $totalPagar = $totalGeneral;
    $totalConDescuento = $totalGeneral;
    $descuento = !empty($mesasCompletas) ? ($mesasCompletas[0]->ambiente_descuento ?? 0) : 0;
    if ($descuento > 0) {
      $totalConDescuento = $totalGeneral - ($totalGeneral * ($descuento / 100));
      $totalPagar = $totalConDescuento;
    }
    $this->_view->invitados = $invitados;
    $this->_view->totalGeneral = $totalGeneral;
    $this->_view->totalConDescuento = $totalConDescuento;
    $this->_view->totalPagar = $totalPagar;
    $this->_view->descuento = $descuento;

    //agrego valor a pagar desde aca por seguridad
    if ($cupos) {
      $cuposModel->editField($cupos->id, 'precio_total', $totalPagar);
    } else {
      $reservasModel->editField($id, 'reserva_total_pagar', $totalPagar);
    }

    $this->_view->hayCupo = $hayCupo;

    $terminosModel = new Administracion_Model_DbTable_Terminos();
    $this->_view->terminos = $terminosModel->getList("termino_estado = 1 AND termino_seccion = 1", "termino_id ASC");
    $gestor = Session::getInstance()->get('gestor');
    $this->_view->gestor = $gestor;
  }

  /**
   * Procesa el pago de cupos adicionales (cargo a la acción o datáfono) sobre una
   * reserva ya existente. Los invitados ya fueron insertados en agregarcuposreservaAction;
   * aquí solo se valida el monto, se marca el cupo como pagado y se suma a reserva_total_personas.
   */
  private function procesarPagoCuposAdicionales($cuposId, $reservaId, $reserva, $reservaTotalPagarSubmitted)
  {
    if (!$reserva || $reserva->reserva_documento != $this->socio->SBE_CODI) {
      header('Location: /page/evento/reservar');
      exit;
    }

    $cuposModel = new Administracion_Model_DbTable_Reservacuposadicionales();
    $cupos = $cuposModel->getById($cuposId);
    if (!$cupos || (int) $cupos->cupos_estado !== 0 || $cupos->reserva_id != $reservaId) {
      header('Location: /page/evento/reservar?error=cupos_invalidos');
      exit;
    }

    if ((float) $cupos->precio_total != (float) $reservaTotalPagarSubmitted) {
      Session::getInstance()->set('error_pago', 'El valor total a pagar ha cambiado. Por favor, revisa el resumen.');
      header('Location: /page/evento/resumen?id=' . enc_id($reservaId) . '&cupos_id=' . enc_id($cuposId) . '&error=total_incorrecto');
      exit;
    }

    $metodoPago = $this->_getSanitizedParam('metodo_pago');
    if (!in_array($metodoPago, ['cargo', 'datafono'], true)) {
      header('Location: /page/evento/resumen?id=' . enc_id($reservaId) . '&cupos_id=' . enc_id($cuposId) . '&error=metodo_invalido');
      exit;
    }

    $this->registrarAceptacionTerminos($reservaId);

    $fechaPago = date('Y-m-d H:i:s');
    $cuposModel->editField($cuposId, 'cupos_estado', 1);
    $cuposModel->editField($cuposId, 'cupos_metodo_pago', $metodoPago);
    $cuposModel->editField($cuposId, 'cupos_fecha_pago', $fechaPago);

    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $nuevoTotalPersonas = (int) $reserva->reserva_total_personas + (int) $cupos->cupos_adicionales;
    $reservasModel->editField($reservaId, 'reserva_total_personas', $nuevoTotalPersonas);

    $logModel = new Administracion_Model_DbTable_Log();
    $logModel->insert([
      'log_usuario' => $this->socio->SBE_CODI,
      'log_tipo'    => 'PAGO CUPOS ADICIONALES',
      'log_fecha'   => $fechaPago,
      'log_log'     => "Reserva #{$reservaId} — Cupos adicionales #{$cuposId}\n"
                    . "Cupos pagados: {$cupos->cupos_adicionales} | Método: $metodoPago | Total pagado: {$cupos->precio_total}\n"
                    . "Total personas: {$reserva->reserva_total_personas} -> $nuevoTotalPersonas",
    ]);

    $this->logAuditoria('PAGO_CUPOS_ADICIONALES', $reservaId, [
      'cupos_id' => $cuposId,
      'cupos_adicionales' => $cupos->cupos_adicionales,
      'metodo_pago' => $metodoPago,
      'observaciones' => 'Cupos adicionales pagados y aplicados a la reserva'
    ]);

    header('Location: /page/guests?id=' . enc_id($reservaId) . '&cupos_pagados=1');
    exit;
  }

  public function pagarAction()
  {
    $this->setLayout('blanco');

    $id = $this->_getDecryptedParam('reserva_id');
    $cuposId = $this->_getDecryptedParam('cupos_id');

    $reservasModel = new Administracion_Model_DbTable_Reservas();

    $reservaTotalPagar = $this->_getSanitizedParam('total_pagar');

    $reserva = $reservasModel->getById($id);

    // Modo "cupos adicionales": flujo de pago separado, no toca el estado/monto
    // original de la reserva (ya está paga; esto es un cobro adicional)
    if ($cuposId) {
      $this->procesarPagoCuposAdicionales($cuposId, $id, $reserva, $reservaTotalPagar);
      return;
    }

    if ($reserva->reserva_total_pagar != $reservaTotalPagar) {
      header('Location: /page/evento/resumen?id=' . enc_id($id) . '&error=total_incorrecto');
      Session::getInstance()->set('error_pago', 'El valor total a pagar ha cambiado. Por favor, revisa el resumen de la reserva.');
      exit;
    }
    $reservaMetodoPago = $this->_getSanitizedParam('metodo_pago');
    $reservaNumeroCuotas = $this->_getSanitizedParam('numero_cuotas');
    $reservaTotalPersonas = $this->_getSanitizedParam('total_personas');
    $ip = $_SERVER['REMOTE_ADDR'];
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    $reservasModel->updateReservaPago([
      'reserva_total_pagar' => $reservaTotalPagar,
      'reserva_metodo_pago' => $reservaMetodoPago,
      'reserva_numero_cuotas' => $reservaNumeroCuotas,
      'reserva_total_personas' => $reservaTotalPersonas,
      'reserva_ip' => $ip,
      'reserva_user_agent' => $userAgent,
      'reserva_aceptaterminos' => 1,
      'reserva_aceptapoliticas' => 1,
    ], $id);
    // print_r($_POST);
    // print_r([
    //   'reserva_total_pagar' => $reservaTotalPagar,
    //   'reserva_metodo_pago' => $reservaMetodoPago,
    //   'reserva_numero_cuotas' => $reservaNumeroCuotas,
    //   'reserva_total_personas' => $reservaTotalPersonas,
    //   'reserva_ip' => $ip,
    //   'reserva_user_agent' => $userAgent
    // ]);
    // exit;

    $this->_view->reserva = $reserva = $reservasModel->getById($id);

    // Log antes del pago
    $this->logAuditoria('INICIO_PROCESO_PAGO', $id, [
      'estado_anterior' => $reserva->reserva_estado ?? 'null',
      'datos_json' => [
        'total_pagar' => $reservaTotalPagar,
        'metodo_pago' => $reservaMetodoPago,
        'numero_cuotas' => $reservaNumeroCuotas
      ],
      'observaciones' => 'Iniciando proceso de pago'
    ]);

    if (!$reserva) {
      header('Location: /page/evento/notfound/');
      exit;
    }
    $this->registrarAceptacionTerminos($id);
    if ($reserva->reserva_metodo_pago == 'cargo') {

      $newEstado = '2'; // Aprobado
      $estadoTexto = 'Aprobado';
      $estadoTexto2 = 'El pago se encuentra aprobado';
      $fechaPago = date('Y-m-d H:i:s');

      $this->logAuditoria('PAGO_POR_CARGO', $id, [
        'estado_anterior' => $reserva->reserva_estado,
        'estado_nuevo' => '2',
        'observaciones' => 'Pago procesado por cargo a cuenta'
      ]);

      $reservasModel->updateReservaPagoCargo([
        'reserva_estado' => $newEstado,
        'reserva_estado_texto' => $estadoTexto,
        'reserva_estado_texto2' => $estadoTexto2,
        'reserva_fecha_pago' => $fechaPago
      ], $id);

      $reservaAct = $reservasModel->getById($id);
      if ($reservaAct->reserva_estado == '2') {
        $res = 1;
        //$res = $this->notificaRegistro($id);
      }

      $gestor = Session::getInstance()->get('gestor');
      if ($gestor) {
        $reservasModel->editField($id, 'reserva_gestor_id', $gestor->gestor_id);
        $reservasModel->editField($id, 'reserva_comentario', "Reserva realizada por gestor");
      }

      header("Location: /page/evento/aviso?id=" . enc_id($id) . "&res={$res}");
      return;
      //enviarboleteriaycorreo
    } elseif ($reserva->reserva_metodo_pago == 'datafono') {
      $newEstado = '11'; // Aprobado
      $estadoTexto = 'Aprobado';
      $estadoTexto2 = 'El pago se encuentra aprobado';
      $fechaPago = date('Y-m-d H:i:s');

      $this->logAuditoria('PAGO_POR_DATAFONO', $id, [
        'estado_anterior' => $reserva->reserva_estado,
        'estado_nuevo' => '11',
        'observaciones' => 'Pago procesado por datafono'
      ]);

      $reservasModel->updateReservaPagoCargo([
        'reserva_estado' => $newEstado,
        'reserva_estado_texto' => $estadoTexto,
        'reserva_estado_texto2' => $estadoTexto2,
        'reserva_fecha_pago' => $fechaPago
      ], $id);

      $reservaAct = $reservasModel->getById($id);
      if ($reservaAct->reserva_estado == '11') {
        $res = 1;
        //$res = $this->notificaRegistro($id);
      }

      $gestor = Session::getInstance()->get('gestor');
      if ($gestor) {
        $reservasModel->editField($id, 'reserva_gestor_id', $gestor->user_id);
        $reservasModel->editField($id, 'reserva_comentario', "Reserva realizada por gestor");
      }
      header("Location: /page/evento/aviso?id=" . enc_id($id) . "&res={$res}");
      return;
    } else {
      $newEstado = '7'; // Pendiente de pago
      $estadoTexto = 'Pendiente de pago';
      $estadoTexto2 = 'El pago se encuentra pendiente de confirmación';
      $this->logAuditoria('PAGO_EN_LINEA_INICIADO', $id, [
        'estado_anterior' => $reserva->reserva_estado,
        'estado_nuevo' => '7',
        'observaciones' => 'Redirigiendo a pasarela de pago'
      ]);

      $reservasModel->updateReservaPagoLinea([
        'reserva_estado' => $newEstado,
        'reserva_estado_texto' => $estadoTexto,
        'reserva_estado_texto2' => $estadoTexto2,

      ], $id);

      header('Location: /page/evento/generarpago?id=' . enc_id($id));
    }
  }

  public function avisoAction()
  {
    $id = $this->_getDecryptedParam('id');
    // LOG DE AUDITORÍA
    $this->logAuditoria('AVISO_ACTION', $id, [
      'observaciones' => 'Usuario accede a avisoAction',
      'datos_json' => [
        'id' => $id,
        'colaCompra' => Session::getInstance()->get('colaCompra')
      ]
    ]);
    $infoColaSesion = Session::getInstance()->get('colaCompra');
    $colaCompraModel = new Administracion_Model_DbTable_Colacompra();
    if ($infoColaSesion->cola_compras_id) {
      $colaCompraModel->editField($infoColaSesion->cola_compras_id, 'cola_compras_estado', 'finalizado');
    }
    $colaExiste = $colaCompraModel->getById($infoColaSesion->cola_compras_id);
    if ($colaExiste) {
      Session::getInstance()->set('colaCompra', $colaExiste);
    }
    Session::getInstance()->set('reserva', null);
    //se crea esta variable de sesion para que el socio si se devuelva, haga un cierre se sesion para evitar conflictos
    Session::getInstance()->set('reservacreada', "oklogout");

    $this->_view->id = $id;

    // ── Resumen de compra ──
    $reservaModel = new Administracion_Model_DbTable_Reservas();
    $eventosModel = new Administracion_Model_DbTable_Eventos();
    $mesasModel = new Administracion_Model_DbTable_Mesas();

    $reserva = $id ? $reservaModel->getById($id) : null;
    $evento = null;
    $mesaInfo = [];

    // Verificación de propiedad: si hay socio en sesión, la reserva debe ser suya.
    if ($reserva && $this->socio && $this->socio->SBE_CODI && $reserva->reserva_documento != $this->socio->SBE_CODI) {
      header('Location: /page/evento/');
      exit;
    }

    if ($reserva) {
      $evento = $eventosModel->getById($reserva->reserva_id_evento);

      if ($reserva->reserva_mesa_id) {
        $idsMesa = array_map('trim', explode(',', $reserva->reserva_mesa_id));
        $idsMesaSQL = implode("','", $idsMesa);
        $mesaInfo = $mesasModel->getMesasConDetalles("m.mesa_id IN ('$idsMesaSQL')") ?: [];
      }
    }

    $this->_view->reserva = $reserva;
    $this->_view->evento = $evento;
    $this->_view->mesaInfo = $mesaInfo;
    $this->_view->esModoSilla = !empty($mesaInfo) && ($mesaInfo[0]->mesa_tipo === 'silla');
    $this->_view->estadoTexto = $reserva ? $this->mapEstadoReserva((int) $reserva->reserva_estado) : '';
  }

  public function guestsAction()
  {
    $id = $this->_getDecryptedParam('id');
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $eventosModel = new Administracion_Model_DbTable_Eventos();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $mesasModel = new Administracion_Model_DbTable_Mesas();
  }

  public function notifica_registroAction()
  {

    $id = $this->_getDecryptedParam('id');
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $eventosModel = new Administracion_Model_DbTable_Eventos();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $mesasModel = new Administracion_Model_DbTable_Mesas();

    $reserva = $reservasModel->getById($id);
    //echo $id;
    $sendingemail = new Core_Model_Sendingemail($this->_view);
    if ($sendingemail->notificaPago($reserva)) {
      return true;
    } else {
      return false;
    }
  }

  public function notificaRegistro($id)
  {

    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $eventosModel = new Administracion_Model_DbTable_Eventos();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $mesasModel = new Administracion_Model_DbTable_Mesas();

    $reserva = $reservasModel->getById($id);

    $sendingemail = new Core_Model_Sendingemail($this->_view);
    if ($reserva->reserva_estado == 2 || $reserva->reserva_estado == 3 || $reserva->reserva_estado == 11) {
      if ($sendingemail->notificaPago($reserva)) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function generarpagoAction()
  {
    $this->setLayout('blanco');
    $logModel = new Administracion_Model_DbTable_Log();
    $dataLog = [];

    $infoColaSesion = Session::getInstance()->get('colaCompra');
    $colaCompraModel = new Administracion_Model_DbTable_Colacompra();
    $dataLog['log_log'] = 'Inicio generarpagoAction. infoColaSesion: ' . print_r($infoColaSesion, true);
    $dataLog['log_tipo'] = 'GENERAR PAGO - INICIO';

    $dataLog['log_usuario'] = Session::getInstance()->get('ncar') . " - " . $this->socio->SBE_CODI;
    $logModel->insert($dataLog);

    if ($infoColaSesion->cola_compras_id) {
      $colaCompraModel->editField($infoColaSesion->cola_compras_id, 'cola_compras_estado', 'finalizado');
      $dataLog['log_log'] = 'ColaCompra editada a finalizado. ID: ' . $infoColaSesion->cola_compras_id;
      $dataLog['log_tipo'] = 'GENERAR PAGO - COLA FINALIZADA';
      $logModel->insert($dataLog);
    }
    $colaExiste = $colaCompraModel->getById($infoColaSesion->cola_compras_id);
    $dataLog['log_log'] = 'ColaCompra obtenida: ' . print_r($colaExiste, true);
    $dataLog['log_tipo'] = 'GENERAR PAGO - COLA OBTENIDA';
    $logModel->insert($dataLog);
    if ($colaExiste) {
      Session::getInstance()->set('colaCompra', $colaExiste);
      $dataLog['log_log'] = 'ColaCompra actualizada en sesión.';
      $dataLog['log_tipo'] = 'GENERAR PAGO - COLA SESION';
      $logModel->insert($dataLog);
    }
    //error_reporting(E_ALL);
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $id = $this->_getDecryptedParam("id");
    $reserva = $reservasModel->getById($id);
    $this->_view->id = $id;
    $this->_view->reserva = $reserva;
    Session::getInstance()->set('reserva', null);
    $dataLog['log_log'] = 'Reserva obtenida: ' . print_r($reserva, true);
    $dataLog['log_tipo'] = 'GENERAR PAGO - RESERVA OBTENIDA';
    $logModel->insert($dataLog);

    $placetopay = Payment_Placetopay::getInstance()->getPlacetopay();
    $placetopayData = Payment_Placetopay::getInstance()->getData();
    $date = date('Ymd');
    $reference = "CULBOL_" . $date . "_" . $id;

    $totalPagar = intval($reserva->reserva_total_pagar);
    $dataLog['log_log'] = 'Total a pagar: ' . $totalPagar;
    $dataLog['log_tipo'] = 'GENERAR PAGO - TOTAL PAGAR';
    $logModel->insert($dataLog);
    if ($totalPagar <= 0) {
      $dataLog['log_log'] = 'Total a pagar inválido. Redirigiendo.';
      $dataLog['log_tipo'] = 'GENERAR PAGO - ERROR TOTAL PAGAR';
      $logModel->insert($dataLog);
      header('Location: /page/evento/resumen?id=' . enc_id($id) . '&error=total_pagar_invalido');
      exit;
    }
    $aux = explode("/", $reserva->reserva_telefono);
    $telefono = $aux[0];
    // LOG DE AUDITORÍA
    $this->logAuditoria('GENERAR_PAGO_ACTION', $id, [
      'observaciones' => 'Usuario accede a generarpagoAction',
      'datos_json' => [
        'id' => $id,
        'colaCompra' => Session::getInstance()->get('colaCompra')
      ],
      'reserva' => $reserva
    ]);
    $request = [
      "locale" => "es_CO",
      "buyer" => [
        'name' => APPLICATION_ENV == 'production' ? $reserva->reserva_nombre_cliente : 'testname',
        'surname' => APPLICATION_ENV == 'production' ? $reserva->reserva_apellido_cliente ?? 'nolastname' : 'testlastname',
        'email' => APPLICATION_ENV == 'production' ? $reserva->reserva_correo : 'test@example.com',
        'documentType' => 'CC',
        'document' => is_numeric($reserva->reserva_documento) && $reserva->reserva_documento > 0
          ? $reserva->reserva_documento
          : $reserva->reserva_numero_carnet,
        'mobile' => $telefono,
      ],
      'payer' => [
        'name' => APPLICATION_ENV == 'production' ? $reserva->reserva_nombre_cliente : 'testname',
        'surname' => APPLICATION_ENV == 'production' ? $reserva->reserva_apellido_cliente ?? 'nolastname' : 'testlastname',
        'email' => APPLICATION_ENV == 'production' ? $reserva->reserva_correo : 'test@example.com',
        'documentType' => 'CC',
        'document' => is_numeric($reserva->reserva_documento) && $reserva->reserva_documento > 0
          ? $reserva->reserva_documento
          : $reserva->reserva_numero_carnet,
        'mobile' => $telefono,

      ],
      'payment' => [
        'reference' => $reference,
        'description' => 'Pago Boletas Fiesta Gala El Nogal Ref: ' . $id,
        'amount' => [
          'currency' => 'COP',
          'total' => $totalPagar,
        ],
      ],
      'expiration' => date('c', strtotime('+10 minutes')), // Expiration time in ISO 8601 format
      'returnUrl' => $placetopayData['returnUrl'] . '?reference=' . $reference,
      'ipAddress' => $_SERVER['REMOTE_ADDR'],
      'userAgent' => $_SERVER['HTTP_USER_AGENT'],
    ];
    $dataLog['log_log'] = 'Request Placetopay: ' . print_r($request, true);
    $dataLog['log_tipo'] = 'GENERAR PAGO - REQUEST';
    $logModel->insert($dataLog);
    try {
      $response = $placetopay->request($request);
      $dataLog['log_log'] = 'Respuesta Placetopay: ' . print_r($response, true);
      $dataLog['log_tipo'] = 'GENERAR PAGO - RESPUESTA';
      $logModel->insert($dataLog);

      if ($response->isSuccessful()) {
        $request_id = $response->requestId();
        $reservasModel->editField($id, "request_id", $request_id);
        // $reservasModel->editField($id, "reserva_processurl", $response->processUrl());
        $dataLog['log_log'] = 'Generar pago exitoso. request_id: ' . $request_id . ', processUrl: ' . $response->processUrl();
        $dataLog['log_tipo'] = 'GENERAR PAGO - EXITO';
        $logModel->insert($dataLog);


        header('Location: ' . $response->processUrl());
      } else {
        $mensaje = $response->status()->message();
        $dataLog['log_log'] = 'Error al procesar el pago: ' . $mensaje;
        $dataLog['log_tipo'] = 'GENERAR PAGO - ERROR';
        $logModel->insert($dataLog);
        $this->revertirReservaPorFalloPago($id, '7', $mensaje);
        Session::getInstance()->set('error_pago', 'Error al procesar el pago: ' . $mensaje);
        header('Location: /page/evento/resumen?id=' . enc_id($id) . '&error=error_pago');
      }
    } catch (Exception $e) {
      $dataLog['log_log'] = 'Excepción: ' . $e->getMessage();
      $dataLog['log_tipo'] = 'GENERAR PAGO - EXCEPCION';
      $logModel->insert($dataLog);
      $this->revertirReservaPorFalloPago($id, '7', $e->getMessage());
      Session::getInstance()->set('error_pago', 'Error al procesar el pago: ' . $e->getMessage());
      header('Location: /page/evento/resumen?id=' . enc_id($id) . '&error=error_pago');
    }
  }

  /**
   * Revierte una reserva a estado 1 (creada/en proceso) cuando falla la creación de la
   * sesión de pago en PlaceToPay (p.ej. el monto supera el límite configurado en la
   * pasarela). generarpagoAction() deja la reserva en estado 7 (pendiente de pago) ANTES de
   * llamar a PlaceToPay; si esa llamada falla, quedarse en 7 hace que la mesa nunca se
   * libere: tanto reservarAction() (botón "Regresar") como limpiarreservasAction() (cron
   * de 15 min) y logoutAction() solo liberan la mesa cuando reserva_estado = 1.
   *
   * @param  int    $id       id de la reserva
   * @param  string $estadoDesde estado numérico del que se revierte (solo para el log)
   * @param  string $motivo   mensaje de error de PlaceToPay o la excepción
   * @return void
   */
  private function revertirReservaPorFalloPago($id, $estadoDesde, $motivo)
  {
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reservasModel->editField($id, 'reserva_estado', 1);

    $this->logAuditoria('GENERAR_PAGO_FALLIDO_REVERTIDO', $id, [
      'estado_anterior' => $estadoDesde,
      'estado_nuevo' => '1',
      'observaciones' => 'Fallo al crear sesión de pago en PlaceToPay, se revierte a estado 1 para permitir liberar la mesa: ' . $motivo
    ]);
  }
  public function finalAction()
  {
    $reserva = $this->_getDecryptedParam('id');
  }

  /**
   * Calcula la edad. Devuelve null si no hay fecha válida.
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

  /**
   * Determina si es hijo. Ajusta la lógica según tu modelo.
   */
  function esHijo(object $b): bool
  {
    return in_array($b->parentesco, ["Hijo", "Hija", "Hijo(a)", "Hijo(a)", "hijo", "hija"]);
  }

  function bloquearbeneficiarioAction()
  {
    $this->setLayout('blanco');
    // error_reporting(E_ALL);
    $beneficiarioDocumento = $this->_getSanitizedParam('beneficiario_doc');
    $asociadoDocumento = $this->socio->SBE_CODI;
    $beneficiarioAccion = $this->_getSanitizedParam('accion');
    $newEstado = $this->_getSanitizedParam('estado');

    $beneficiariosBloqueadosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();

    // Verificar si ya está bloqueado por otro
    $bloqueoExistente = $beneficiariosBloqueadosModel->getList("beneficiario_bloqueodocumento = '$beneficiarioDocumento' AND beneficiario_bloqueo_macnume = '$beneficiarioAccion' AND beneficiario_bloqueo_por_asociado_documento = '$asociadoDocumento'  ")[0];

    if ($bloqueoExistente && $bloqueoExistente->beneficiario_bloqueo_id >= 1) {

      $beneficiariosBloqueadosModel->editField($bloqueoExistente->beneficiario_bloqueo_id, 'beneficiario_bloqueo_estado', $newEstado);
      $beneficiariosBloqueadosModel->editField($bloqueoExistente->beneficiario_bloqueo_id, 'beneficiario_bloqueo_expiracion', date('Y-m-d H:i:s', strtotime('+5 minutes')));
      echo json_encode(['success' => true, 'message' => 'Bloqueo actualizado correctamente.']);
      return;
    }

    // Insertar o actualizar bloqueo
    $data = [
      'beneficiario_bloqueodocumento' => $beneficiarioDocumento,
      'beneficiario_bloqueo_por_asociado_documento' => $asociadoDocumento,
      "beneficiario_bloqueo_fecha_bloqueo" => date('Y-m-d H:i:s'),
      'beneficiario_bloqueo_expiracion' => date('Y-m-d H:i:s', strtotime('+5 minutes')),
      'beneficiario_bloqueo_macnume' => $beneficiarioAccion,
      'beneficiario_bloqueo_estado' => $newEstado, // Activo

    ];

    $beneficiariosBloqueadosModel->insert($data);

    echo json_encode(['success' => true]);
    return;
  }
  function obtenerdocumentosbloqueadosAction()
  {
    $this->setLayout('blanco');
    $accion = $this->_getSanitizedParam('accion');

    $documento = $this->_getSanitizedParam('documento');
    $beneficiariosBloqueadosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();
    // $fechaActual
    $beneficiariosBloqueado = $beneficiariosBloqueadosModel->getList("(beneficiario_bloqueo_estado = 1) AND beneficiario_bloqueo_por_asociado_documento != '$documento'", "");
    echo json_encode($beneficiariosBloqueado);
    return;
  }

  public function obtenermesasbloqueadasAction()
  {
    $this->setLayout('blanco');
    //error_reporting(E_ALL);
    $mesasBloqueadasModel = new Administracion_Model_DbTable_Mesasbloqueo();
    $mesasBloqueadas = $mesasBloqueadasModel->getList("mesa_bloqueo_estado = 1", "");
    echo json_encode($mesasBloqueadas);
    return;
  }

  // Devuelve qué capacidades no tienen ningún ambiente disponible considerando
  // la fecha de inicio de partido de cada ambiente (ambiente_fecha_partido)
  public function obtenerCapacidadesExpiradasAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $ahora = new DateTime();

    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $capacidades = $mesasModel->getListMesasDisponibles();
    $capacidadesExpiradas = [];

    foreach ($capacidades as $cap) {
      $pisos = $mesasModel->getPisosDisponibles($cap->mesa_capacidad);
      $tieneDisponible = false;

      foreach ($pisos as $piso) {
        $ambientes = $mesasModel->getAmbientesPorPiso($piso->piso_id, $cap->mesa_capacidad);
        foreach ($ambientes as $ambiente) {
          $sinMesas = !$ambiente->total_mesas || intval($ambiente->total_mesas) === 0;
          $esPasado = false;
          if (!empty($ambiente->ambiente_fecha_partido)) {
            $cierre   = new DateTime($ambiente->ambiente_fecha_partido);
            $esPasado = $ahora > $cierre;
          }
          if (!$sinMesas && !$esPasado) {
            $tieneDisponible = true;
            break 2;
          }
        }
      }

      if (!$tieneDisponible) {
        $capacidadesExpiradas[] = intval($cap->mesa_capacidad);
      }
    }

    echo json_encode(['capacidadesExpiradas' => $capacidadesExpiradas]);
    return;
  }


  public function seleccionarmesaAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    // Permitir recibir datos por JSON (fetch) o POST tradicional
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if (is_array($data)) {
      $id = isset($data['mesa_id']) ? $data['mesa_id'] : null;
      $accion = isset($data['accion']) ? $data['accion'] : null;
      $stepIndex = isset($data['stepIndex']) ? (int) $data['stepIndex'] : null;
    } else {
      $id = $this->_getSanitizedParam('mesa_id');
      $accion = $this->_getSanitizedParam('accion');
      $stepIndex = $this->_getSanitizedParam('stepIndex');
    }

    if (!$id || !$accion || $stepIndex === null) {
      echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
      return;
    }

    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reservaSession = Session::getInstance()->get('reserva');

    if (!$reservaSession) {
      echo json_encode(['success' => false, 'message' => 'No hay reserva activa']);
      return;
    }

    try {
      // Obtener las mesas actuales de la reserva
      $reserva = $reservasModel->getById($reservaSession->id);
      $mesasActuales = $reserva->reserva_mesa_id ? explode(',', $reserva->reserva_mesa_id) : [];
      $mesasActuales = array_map('trim', $mesasActuales); // Limpiar espacios

      // Determinar el tamaño del array basado en total_mesas o el mayor índice necesario
      $totalMesas = max(
        $reservaSession->total_mesas ?? 0,
        count($mesasActuales),
        $stepIndex + 1
      );

      // Inicializar array preservando todos los índices posibles
      $mesasActualesPreservadas = array_fill(0, $totalMesas, '');
      // Rellenar con las selecciones actuales, preservando índices
      foreach ($mesasActuales as $index => $mesaId) {
        if ($index < $totalMesas && $mesaId !== '') {
          $mesasActualesPreservadas[$index] = $mesaId;
        }
      }
      if ($accion === 'seleccionar') {
        // Verificar que la mesa esté disponible o sea mía
        $mesa = $mesasModel->getById($id);
        if (!$mesa) {
          echo json_encode(['success' => false, 'message' => 'Mesa no encontrada']);
          return;
        }

        // Defensa: el tipo del elemento debe coincidir con el modo de selección de la sesión
        // (no mezclar mesas y sillas en una misma compra vía peticiones manipuladas).
        $tipoSeleccion = Session::getInstance()->get('tipo_seleccion_mesa') ?: 'mesa';
        if ($mesa->mesa_tipo !== $tipoSeleccion) {
          echo json_encode(['success' => false, 'message' => 'El elemento no corresponde al tipo de selección']);
          return;
        }

        // Provisional funciona igual que ocupada: bloquea la selección igual que mesa_estado == 1.
        $mesaProvisional = $mesa->mesa_provision !== null && $mesa->mesa_provision !== '';
        if (($mesa->mesa_estado == 1 || $mesaProvisional) && !in_array($id, $mesasActualesPreservadas)) {
          $reservaConEstaMesa = $reservasModel->getList("id = '{$reservaSession->id}' AND FIND_IN_SET('$id', reserva_mesa_id)", "");
          if (!$reservaConEstaMesa || count($reservaConEstaMesa) == 0) {
            echo json_encode(['success' => false, 'message' => 'Mesa ocupada por otro usuario']);
            return;
          }
        }

        // Deseleccionar la mesa anterior del mismo paso, si existe
        if (!empty($mesasActualesPreservadas[$stepIndex]) && $mesasActualesPreservadas[$stepIndex] !== $id) {
          $mesasModel->editField($mesasActualesPreservadas[$stepIndex], 'mesa_estado', 0);
        }

        // Actualizar la lista de mesas para el paso correspondiente
        $mesasActualesPreservadas[$stepIndex] = $id;

        // Actualizar el estado de la mesa en la base de datos y registrar cuándo se bloqueó
        $mesasModel->editField($id, 'mesa_estado', 1);
        $mesasModel->editField($id, 'mesa_fecha_bloqueo', date('Y-m-d H:i:s'));

        // Crear la cadena reserva_mesa_id preservando marcadores vacíos
        $newMesas = implode(',', $mesasActualesPreservadas);
        $reservasModel->editField($reservaSession->id, 'reserva_mesa_id', $newMesas ?: null);

        echo json_encode(['success' => true, 'message' => 'Mesa seleccionada', 'mesa_id' => $id, 'reserva_mesa_id' => $newMesas ?: null]);
      } elseif ($accion === 'deseleccionar') {
        // Verificar que la mesa sea mía
        if (!in_array($id, $mesasActualesPreservadas)) {
          echo json_encode(['success' => false, 'message' => 'No puedes deseleccionar una mesa que no es tuya']);
          return;
        }

        // Remover la mesa del paso correspondiente
        if ($mesasActualesPreservadas[$stepIndex] === $id) {
          $mesasModel->editField($id, 'mesa_estado', 0);
          $mesasActualesPreservadas[$stepIndex] = '';
          // Crear la cadena reserva_mesa_id preservando marcadores vacíos
          $newMesas = implode(',', $mesasActualesPreservadas);
          $reservasModel->editField($reservaSession->id, 'reserva_mesa_id', $newMesas ?: null);
        }

        echo json_encode(['success' => true, 'message' => 'Mesa deseleccionada', 'mesa_id' => $id, 'reserva_mesa_id' => $newMesas ?: null]);
      } else {
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        return;
      }

      // Actualizar sesión con la reserva modificada
      $reservaActualizada = $reservasModel->getById($reservaSession->id);
      if ($reservaActualizada) {
        Session::getInstance()->set('reserva', $reservaActualizada);
      }
    } catch (Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
  }
  public function culstamesasdisponiblesAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if (is_array($data)) {
      $ambienteId = isset($data['ambiente_id']) ? $data['ambiente_id'] : null;
      $capacidad = isset($data['capacidad']) ? $data['capacidad'] : null;
      $tipo = isset($data['tipo']) && $data['tipo'] ? $data['tipo'] : 'mesa';
    } else {
      $ambienteId = $this->_getSanitizedParam('ambiente_id');
      $capacidad = $this->_getSanitizedParam('capacidad');
      $tipo = $this->_getSanitizedParam('tipo') ?: 'mesa';
    }
    // Solo se permiten los tipos seleccionables por el flujo de compra.
    $tipo = ($tipo === 'silla') ? 'silla' : 'mesa';

    if (!$ambienteId || !$capacidad) {
      echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
      return;
    }
    $ambienteId = (int) $ambienteId;
    $capacidad = (int) $capacidad;

    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $reservasModel = new Administracion_Model_DbTable_Reservas();

    // Obtener la reserva actual del usuario
    $reservaSession = Session::getInstance()->get('reserva');
    $miReservaId = $reservaSession ? $reservaSession->id : null;

    // Obtener todos los elementos del ambiente con la capacidad y el tipo requeridos
    $mesas = $mesasModel->getList("mesa_ambiente = '$ambienteId' AND mesa_capacidad = '$capacidad' AND mesa_tipo = '$tipo' AND mesa_activa = '1'", "");

    if (!$mesas) {
      echo json_encode(['success' => false, 'message' => 'No hay mesas en este ambiente']);
      return;
    }

    $mesasConEstado = [];
    foreach ($mesas as $mesa) {
      // Provisional funciona igual que ocupada: se refleja como mesa_estado = 1
      // para que el mapa la pinte/bloquee igual sin necesidad de lógica aparte.
      // Aplica a elementos seleccionables (mesa y silla), no a decoraciones.
      $mesaProvisional = ($mesa->mesa_tipo === 'mesa' || $mesa->mesa_tipo === 'silla')
        && $mesa->mesa_provision !== null && $mesa->mesa_provision !== '';
      $mesaInfo = [
        'mesa_id' => $mesa->mesa_id,
        'mesa_estado' => $mesaProvisional ? 1 : $mesa->mesa_estado,
        'mesa_provision' => $mesa->mesa_provision,
        'mesa_numero' => $mesa->mesa_numero,
        'mesa_capacidad' => $mesa->mesa_capacidad,
        'mesa_posicion_x' => $mesa->mesa_pos_x,
        'mesa_posicion_y' => $mesa->mesa_pos_y,
        'mesa_ambiente' => $mesa->mesa_ambiente,
        'mesa_pos_x' => $mesa->mesa_pos_x, // Compatibilidad
        'mesa_pos_y' => $mesa->mesa_pos_y, // Compatibilidad
        'mesa_ancho' => $mesa->mesa_ancho,
        'mesa_alto' => $mesa->mesa_alto,
        'mesa_rotacion' => $mesa->mesa_rotacion,
        'mesa_codigo' => $mesa->mesa_codigo,
        'mesa_nombre' => $mesa->mesa_nombre,
        'mesa_tipo' => $mesa->mesa_tipo ? $mesa->mesa_tipo : 'Mesa',
        'es_mia' => false // Por defecto no es mía
      ];

      // Si la mesa está ocupada (estado 1), verificar si es mi reserva
      if ($mesa->mesa_estado == 1 && $miReservaId) {
        $reservaConEstaMesa = $reservasModel->getList("id = '$miReservaId' AND FIND_IN_SET('{$mesa->mesa_id}', reserva_mesa_id)", "");
        if ($reservaConEstaMesa && count($reservaConEstaMesa) > 0) {
          $mesaInfo['es_mia'] = true;
        }
      }

      $mesasConEstado[] = $mesaInfo;
    }

    echo json_encode(['success' => true, 'mesas' => $mesasConEstado]);
  }

  public function consultarbeneficiariosinvitadosAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    // 1. Recibir parámetro mac_nume (JSON fetch o POST clásico)
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);          // array asociativo

    $mac_nume = $data['mac_nume']
      ?? $this->_getSanitizedParam('mac_nume')
      ?? null;

    if (!$mac_nume) {
      echo json_encode([
        'success' => false,
        'message' => 'Parámetros inválidos'
      ]);
      return;
    }

    if ($mac_nume == $this->socio->MAC_NUME) {
      echo json_encode([
        'success' => false,
        'message' => 'No puedes consultar tus propios beneficiarios'
      ]);
      return;
    }

    // 2. Llamar al WS externo
    $loginServiceUrl = URL_BASE . '/querys/selectBeneficiarios.php';

    $postData = http_build_query([
      'token' => $this->generarToken(),
      'mac_nume' => $mac_nume
    ]);

    $ch = curl_init($loginServiceUrl);
    curl_setopt_array($ch, [
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $postData,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
      // Si el WS usa HTTPS con cert. propio añade aquí opciones de SSL
    ]);

    $rawResponse = curl_exec($ch);

    if (curl_errno($ch)) {
      echo json_encode([
        'success' => false,
        'message' => 'Error cURL: ' . curl_error($ch)
      ]);
      curl_close($ch);
      return;
    }
    curl_close($ch);

    // 3. Decodificar JSON
    $wsData = json_decode($rawResponse, true); // array asociativo

    if ($wsData === null) {                    // json_decode falló
      echo json_encode([
        'success' => false,
        'message' => 'Respuesta del servicio no es JSON válido',
        'raw' => $rawResponse          // opcional para depurar
      ]);
      return;
    }

    // 4. Procesar la data (ejemplo: verificar clave 'beneficiarios')
    //print_r($wsData["mensaje"]);
    if ($wsData['mensaje']) {
      echo json_encode([
        'success' => false,
        'message' => 'No se encontraron beneficiarios'
      ]);
      return;
    }

    $beneficiarios = $wsData;
    $beneficiariosMayores = [];
    $fechaLimite = new DateTime('-18 years');

    if (is_iterable($beneficiarios)) {
      $beneficiariosMayores = array_filter($beneficiarios, function ($b) use ($fechaLimite) {
        $fechaRaw = null;

        if (is_object($b) && isset($b->SBE_FNAC->date)) {
          $fechaRaw = $b->SBE_FNAC->date;
        } elseif (is_array($b) && isset($b['SBE_FNAC']['date'])) {
          $fechaRaw = $b['SBE_FNAC']['date'];
        }

        if (!$fechaRaw) {
          return false;
        }

        $fechaNacimiento = new DateTime($fechaRaw);
        return $fechaNacimiento <= $fechaLimite;
      });
    }

    if (empty($beneficiariosMayores)) {
      echo json_encode([
        'success' => false,
        'message' => 'No se encontraron beneficiarios mayores de edad'
      ]);
    } else {
      echo json_encode([
        'success' => true,
        'data' => array_values($beneficiariosMayores),
        'count' => count($beneficiariosMayores)
      ]);
    }
  }

  public function consultarsocioinvitadoAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $input = file_get_contents('php://input');
    $data = json_decode($input, true); // array asociativo
    $ncar = $data['ncar']
      ?? $this->_getSanitizedParam('ncar')
      ?? null;

    if (!$ncar) {
      echo json_encode(['success' => false, 'message' => 'Parámetros inválidos']);
      return;
    }
    //evitar consultar socios de la misma accion
    $beneficiarios = $this->consultarBeneficiariosSession();
    if (is_array($beneficiarios) && in_array($ncar, array_column($beneficiarios, 'SBE_NCAR'))) {
      echo json_encode([
        'success' => false,
        'message' => 'No puedes consultar beneficiarios de la misma acción'
      ]);
      return;
    }
    // Evitar consultarse a sí mismo
    if ($this->socio && isset($this->socio->MAC_NUME) && $ncar == $this->socio->MAC_NUME) {
      echo json_encode(['success' => false, 'message' => 'No puedes consultarte a ti mismo']);
      return;
    }

    $loginServiceUrl = URL_BASE . '/querys/buscarUsuario.php';

    $postData = http_build_query([
      'token' => $this->generarToken(),
      'ncar' => $ncar
    ]);

    $ch = curl_init($loginServiceUrl);
    curl_setopt_array($ch, [
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $postData,
      CURLOPT_RETURNTRANSFER => true
    ]);

    $raw = curl_exec($ch);
    if (curl_errno($ch)) {
      echo json_encode([
        'success' => false,
        'message' => 'Error cURL: ' . curl_error($ch)
      ]);
      curl_close($ch);
      return;
    }
    curl_close($ch);

    $response = json_decode($raw);
    if (!$response) {
      echo json_encode(['success' => false, 'message' => 'Respuesta inválida del servicio']);
      return;
    }

    // 2. Validar si trae mensaje de error (algunos servicios devuelven ->mensaje)
    if (isset($response->mensaje) && $response->mensaje) {
      echo json_encode(['success' => false, 'message' => $response->mensaje]);
      return;
    }
    $documento = $response->SBE_CODI;

    $beneficiariosbloqueadosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();
    $fechaActual = date('Y-m-d H:i:s');
    $existeBloqueo = $beneficiariosbloqueadosModel->getList("beneficiario_bloqueodocumento = '$documento' AND beneficiario_bloqueo_estado = 1 AND beneficiario_bloqueo_expiracion > '$fechaActual'", "beneficiario_bloqueo_id DESC")[0];
    if ($existeBloqueo) {
      echo json_encode(['success' => false, 'message' => 'El beneficiario se encuentra encuentra en una reserva pendiente']);
      return;
    }

    // 3. Validar mayoría de edad (igual lgica que consultarbeneficiariosinvitadosAction)
    $fechaLimite = new DateTime('-18 years');
    $fechaRaw = null;
    if (isset($response->SBE_FNAC->date)) {
      $fechaRaw = $response->SBE_FNAC->date;
    } elseif (isset($response->SBE_FNAC) && is_string($response->SBE_FNAC)) {
      $fechaRaw = $response->SBE_FNAC; // por si ya viene como string
    }

    if (!$fechaRaw) {
      echo json_encode(['success' => false, 'message' => 'No se encontró fecha de nacimiento']);
      return;
    }

    try {
      $fechaNacimiento = new DateTime($fechaRaw);
    } catch (Exception $e) {
      echo json_encode(['success' => false, 'message' => 'Fecha de nacimiento inválida']);
      return;
    }

    if ($fechaNacimiento > $fechaLimite) {
      echo json_encode(['success' => false, 'message' => 'El socio consultado no es mayor de edad']);
      return;
    }
    $response->SBE_NOCO = $response->sbe_nomb . " " . $response->sbe_apel;

    echo json_encode([
      'success' => true,
      'data' => $response
    ]);
  }

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
        return 'Pago datáfono Aprobado';
      default:
        return 'Desconocido';
    }
  }

  /**
   * Registra la aceptación de términos y condiciones
   */
  private function registrarAceptacionTerminos($reservaId)
  {
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $terminosModel = new Administracion_Model_DbTable_Terminos();
    $aceptacionesModel = new Administracion_Model_DbTable_Terminosaceptaciones();

    $reserva = $reservasModel->getById($reservaId);
    if (!$reserva) {
      return false;
    }

    // Obtener trminos activos
    $terminos = $terminosModel->getList("termino_estado = 1 AND termino_seccion = 1", "termino_id ASC");

    if (!$terminos || count($terminos) == 0) {
      return false;
    }

    // Detectar información del dispositivo
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
    $dispositivoInfo = $this->detectarDispositivo($userAgent);

    // Preparar datos adicionales
    $datosAdicionales = [
      'reserva_info' => [
        'total_personas' => $reserva->reserva_total_personas ?? 0,
        'total_pagar' => $reserva->reserva_total_pagar ?? 0,
        'metodo_pago' => $this->_getSanitizedParam('metodo_pago'),
        'numero_cuotas' => $this->_getSanitizedParam('numero_cuotas'),
        'fecha_evento' => $reserva->reserva_fecha ?? ''
      ],
      'usuario_info' => [
        'ncar' => Session::getInstance()->get('ncar'),
        'mac_nume' => $this->socio->MAC_NUME ?? null
      ],
      'timestamp' => date('Y-m-d H:i:s'),
      'url_origen' => $_SERVER['HTTP_REFERER'] ?? '',
      'post_data' => print_r($_POST, true)
    ];

    // Registrar cada término aceptado
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
        'aceptacion_tipo' => 'checkout' // Puede ser 'checkout', 'registro', etc.
      ];

      try {
        $aceptacionesModel->insert($dataAceptacion);

        // Log de auditoría
        $this->logAuditoria('TERMINO_ACEPTADO', $reservaId, [
          'termino_id' => $termino->termino_id,
          'termino_titulo' => $termino->termino_titulo,
          'observaciones' => 'Término aceptado por el usuario durante el proceso de pago',
          'datos_json' => json_encode($dataAceptacion, JSON_UNESCAPED_UNICODE)
        ]);
      } catch (Exception $e) {
        // Log de error si falla el registro
        $this->logAuditoria('ERROR_ACEPTACION_TERMINO', $reservaId, [
          'termino_id' => $termino->termino_id,
          'error' => $e->getMessage(),
          'observaciones' => 'Error al registrar aceptación de término'
        ]);
      }
    }

    return true;
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
    if (preg_match('/chrome/i', $userAgent)) {
      $navegador = 'Chrome';
    } elseif (preg_match('/firefox/i', $userAgent)) {
      $navegador = 'Firefox';
    } elseif (preg_match('/safari/i', $userAgent)) {
      $navegador = 'Safari';
    } elseif (preg_match('/edge/i', $userAgent)) {
      $navegador = 'Edge';
    } elseif (preg_match('/opera/i', $userAgent)) {
      $navegador = 'Opera';
    }

    return [
      'tipo' => $dispositivo,
      'navegador' => $navegador
    ];
  }
}
