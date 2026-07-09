<?php

/**
 * Controlador de Eventos que permite la  creacion, edicion  y eliminacion de los eventos del Sistema
 */
class Administracion_infoboletasController extends Administracion_mainController
{
  public $botonpanel = 14;

  /**
   * $mainModel  instancia del modelo de  base de datos eventos
   * @var modeloContenidos
   */
  public $mainModel;

  /**
   * $route  url del controlador base
   * @var string
   */
  protected $route;

  /**
   * $pages cantidad de registros a mostrar por pagina]
   * @var integer
   */
  protected $pages;

  /**
   * $namefilter nombre de la variable a la fual se le van a guardar los filtros
   * @var string
   */
  protected $namefilter;

  /**
   * $_csrf_section  nombre de la variable general csrf  que se va a almacenar en la session
   * @var string
   */
  protected $_csrf_section = "administracion_infoboletas";

  protected $namepages;

  protected $namepageactual = "";

  public function init()
  {
    $this->mainModel = new Administracion_Model_DbTable_Boletasinfo();
    $this->namefilter = "parametersfiltereventos";
    $this->route = "/administracion/infoboletas";
    $this->namepages = "pages_infoboletas";
    $this->namepageactual = "page_actual_infoboletas";
    $this->_view->route = $this->route;
    if (Session::getInstance()->get($this->namepages)) {
      $this->pages = Session::getInstance()->get($this->namepages);
    } else {
      $this->pages = 500;
    }
    parent::init();
  }

  public function indexAction()
  {
    $this->route = "/administracion/infoboletas";
    $this->_view->route = $this->route;

    $title = "Información de Boletas (Validadas/Utilizadas)";
    $this->getLayout()->setTitle($title);
    $this->_view->titlesection = $title;

    $this->filters();
    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
    $filters = (object) Session::getInstance()->get($this->namefilter);
    $this->_view->filters = $filters;

    // Estadísticas generales
    $estadisticas = $this->mainModel->getEstadisticasEvento1();
    $this->_view->estadisticas = $estadisticas;

    // Filtros y paginación para reservas (solo boletas estado 2 o 3)
    $filtersql = $this->getFilter();
    $order = "";
    $amount = $this->pages;
    $page = $this->_getSanitizedParam("page");
    if (!$page && Session::getInstance()->get($this->namepageactual)) {
      $page = Session::getInstance()->get($this->namepageactual);
      $start = ($page - 1) * $amount;
    } else if (!$page) {
      $start = 0;
      $page = 1;
      Session::getInstance()->set($this->namepageactual, $page);
    } else {
      Session::getInstance()->set($this->namepageactual, $page);
      $start = ($page - 1) * $amount;
    }

    // Total de reservas con boletas estado 2 o 3, 11
    $total = $this->mainModel->getTotalReservasConBoletas($filtersql);

    // Lista paginada de reservas con información de boletas estado 2 o 3, 11
    $list = $this->mainModel->getReservasConBoletas($filtersql, $order, $start, $amount);

    // Aplicar verificación de "listo para envío" y filtrar si es necesario
    $filteredList = [];
    foreach ($list as $item) {
      $item->listo_para_enviar = $this->listoParaEnviar($item->id);

      // Aplicar filtro de "listo para envío" si está configurado
      if (isset($filters->listo_envio) && $filters->listo_envio != '') {
        if ($filters->listo_envio == '1' && $item->listo_para_enviar == 1) {
          $filteredList[] = $item;
        } elseif ($filters->listo_envio == '0' && $item->listo_para_enviar == 0) {
          $filteredList[] = $item;
        }
      } else {
        $filteredList[] = $item;
      }
    }

    // Si se aplicó el filtro de "listo para envío", actualizar totales
    if (isset($filters->listo_envio) && $filters->listo_envio != '') {
      $total = count($filteredList);
      $list = $filteredList;
    } else {
      $list = $filteredList;
    }

    // Obtener inconsistencias y organizarlas por reserva
    $reservasConInconsistencias = $this->mainModel->getBoletasConInconsistencia();
    $inconsistenciasPorReserva = [];
    foreach ($reservasConInconsistencias as $inconsistencia) {
      $reservaId = $inconsistencia->boleta_reserva_id;
      if (!isset($inconsistenciasPorReserva[$reservaId])) {
        $inconsistenciasPorReserva[$reservaId] = [];
      }
      $inconsistenciasPorReserva[$reservaId][] = $inconsistencia;
    }

    $invitadosReservaModel = new Administracion_Model_DbTable_Invitadosreservas();
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $boletasinfoModel = new Administracion_Model_DbTable_Boletasinfo();

    $reservasAprobadasBoleteria = $reservasModel->getList("reserva_estado IN (2, 3, 11)", "id ASC");
    $inconsistenciasDocumentosPorReserva = [];

    foreach ($reservasAprobadasBoleteria as $reserva) {
      $reserva->invitados = $invitadosReservaModel->getList("reserva_id_reserva = '{$reserva->id}'", "");
      $reserva->boletas = $boletasinfoModel->getList("boleta_reserva_id = '{$reserva->id}'", "");

      // Validar consistencia de documentos entre invitados y boletas
      $validacionDocumentos = $this->validarConsistenciaDocumentos($reserva->invitados, $reserva->boletas);
      $reserva->inconsistencias_documentos = $validacionDocumentos;

      // Si hay inconsistencias, agregarlas al array organizado por reserva
      if (!$validacionDocumentos['valido']) {
        $inconsistenciasDocumentosPorReserva[$reserva->id] = [
          'reserva' => $reserva,
          'inconsistencias' => $validacionDocumentos['inconsistencias'],
          'resumen' => $validacionDocumentos['resumen']
        ];
      }
    }

    $this->_view->reservasAprobadasBoleteria = $reservasAprobadasBoleteria;
    $this->_view->inconsistenciasDocumentosPorReserva = $inconsistenciasDocumentosPorReserva;
    $this->_view->inconsistenciasPorReserva = $inconsistenciasPorReserva;
    $this->_view->register_number = $total;
    $this->_view->pages = $this->pages;
    $this->_view->totalpages = ceil($total / $amount);
    $this->_view->page = $page;
    $this->_view->lists = $list;
    $this->_view->csrf_section = $this->_csrf_section;
  }
  /**
   * Valida que los documentos de los invitados coincidan con los documentos de las boletas
   * Permite boletas extra solo si están en estado 3 (anuladas)
   * @param array $invitados Array de objetos invitados
   * @param array $boletas Array de objetos boletas
   * @return array ['valido' => bool, 'inconsistencias' => array]
   */
  private function validarConsistenciaDocumentos($invitados, $boletas)
  {
    $inconsistencias = [];

    // Crear arrays de documentos para comparación
    $documentosInvitados = [];
    $documentosBoletas = [];
    $documentosBoletasEstado3 = [];

    // Recopilar documentos de invitados
    foreach ($invitados as $invitado) {
      if (!empty($invitado->documento_invitado)) {
        $documentosInvitados[] = trim($invitado->documento_invitado);
      }
    }

    // Recopilar documentos de boletas separados por estado
    foreach ($boletas as $boleta) {
      if (!empty($boleta->boleta_documento)) {
        $documento = trim($boleta->boleta_documento);
        $documentosBoletas[] = $documento;

        if ($boleta->boleta_estado == 3) {
          $documentosBoletasEstado3[] = $documento;
        }
      }
    }

    // Validar que todos los invitados tengan al menos una boleta
    foreach ($documentosInvitados as $docInvitado) {
      if (!in_array($docInvitado, $documentosBoletas)) {
        $inconsistencias[] = [
          'tipo' => 'invitado_sin_boleta',
          'documento' => $docInvitado,
          'mensaje' => "El invitado con documento {$docInvitado} no tiene boleta asignada"
        ];
      }
    }

    // Validar boletas sin invitado correspondiente (solo permitidas si están en estado 3)
    foreach ($documentosBoletas as $docBoleta) {
      if (!in_array($docBoleta, $documentosInvitados)) {
        // Buscar la boleta para verificar su estado
        $boletaEncontrada = null;
        foreach ($boletas as $boleta) {
          if (trim($boleta->boleta_documento) == $docBoleta) {
            $boletaEncontrada = $boleta;
            break;
          }
        }

        if ($boletaEncontrada && $boletaEncontrada->boleta_estado != 3) {
          $inconsistencias[] = [
            'tipo' => 'boleta_sin_invitado',
            'documento' => $docBoleta,
            'boleta_uid' => $boletaEncontrada->boleta_uid,
            'boleta_estado' => $boletaEncontrada->boleta_estado,
            'mensaje' => "La boleta {$boletaEncontrada->boleta_uid} con documento {$docBoleta} no corresponde a ningún invitado y no está anulada (estado != 3)"
          ];
        }
      }
    }

    return [
      'valido' => count($inconsistencias) == 0,
      'inconsistencias' => $inconsistencias,
      'resumen' => [
        'total_invitados' => count($documentosInvitados),
        'total_boletas' => count($documentosBoletas),
        'boletas_anuladas' => count($documentosBoletasEstado3),
        'inconsistencias_encontradas' => count($inconsistencias)
      ]
    ];
  }
  public function consultarAction()
  {
    $this->route = "/administracion/infoboletas";
    $this->_view->route = $this->route;

    $title = "Consultar Boletas por Reserva o Documento";
    $this->getLayout()->setTitle($title);
    $this->_view->titlesection = $title;

    // Inicializar variables
    $boletas = [];
    $reservaInfo = null;
    $tipoConsulta = '';
    $valorConsulta = '';
    $mensaje = '';

    // Procesar consulta si es POST
    if ($this->getRequest()->isPost()) {
      $tipoConsulta = $this->_getSanitizedParam("tipo_consulta");
      $valorConsulta = trim($this->_getSanitizedParam("valor_consulta"));

      if (($valorConsulta)) {
        if ($tipoConsulta == 'reserva') {
          // Consulta por reserva
          $boletas = $this->mainModel->getBoletasByReserva($valorConsulta);
          if (($boletas)) {
            $reservaInfo = $this->mainModel->getReservaById($valorConsulta);
          } else {
            $mensaje = "No se encontraron boletas para la reserva #$valorConsulta";
          }
        } elseif ($tipoConsulta == 'documento') {
          // Consulta por documento
          $boletas = $this->mainModel->getBoletasByDocumento($valorConsulta);
          if (!($boletas)) {
            $mensaje = "No se encontraron boletas para el documento $valorConsulta";
          }
        }

        // Obtener información adicional para cada boleta
        if (($boletas)) {
          $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
          $usuariosModel = new Administracion_Model_DbTable_Usuario();
          $mesasModel = new Administracion_Model_DbTable_Mesas();
          $ambientesModel = new Administracion_Model_DbTable_Ambientes();

          foreach ($boletas as $boleta) {
            // Información del invitado
            if ($boleta->boleta_asignacion) {
              $boleta->invitadoInfo = $invitadosModel->getById($boleta->boleta_asignacion);
            }

            // Información del usuario validador
            if ($boleta->boleta_usuario_validador) {
              $boleta->usuarioValidador = $usuariosModel->getById($boleta->boleta_usuario_validador);
            }

            // Información de la mesa
            if ($boleta->boleta_mesa_id) {
              $mesa = $mesasModel->getById($boleta->boleta_mesa_id);
              if ($mesa) {
                $boleta->mesaInfo = $mesa;
                // Información del ambiente
                $ambiente = $ambientesModel->getById($mesa->mesa_ambiente_id);
                if ($ambiente) {
                  $boleta->ambienteInfo = $ambiente;
                }
              }
            }

            // Información de la reserva (si no se obtuvo antes)
            if (!$reservaInfo && $boleta->boleta_reserva_id) {
              $reservaInfo = $this->mainModel->getReservaById($boleta->boleta_reserva_id);
            }
          }
        }
      } else {
        $mensaje = "Por favor ingrese un valor para consultar";
      }
    }

    // Pasar datos a la vista
    $this->_view->boletas = $boletas;
    $this->_view->reservaInfo = $reservaInfo;
    $this->_view->tipoConsulta = $tipoConsulta;
    $this->_view->valorConsulta = $valorConsulta;
    $this->_view->mensaje = $mensaje;
  }
  // public function getBoletasValidadasAction()
  // {
  //   $this->setLayout('blanco');
  //   $id = $this->_getSanitizedParam("id");
  //   $evento = $this->mainModel->getById($id);
  //   // Solo usar el modelo Boletasinfo
  //   $boletasModel = $this->mainModel;
  //   $boletasEvento = $boletasModel->getList("boleta_evento_id = '$id'");

  //   $boletasTodas = count($boletasEvento);
  //   $boletasValidadas = 0;
  //   foreach ($boletasEvento as $boleta) {
  //     if (($boleta->boleta_estado) && ($boleta->boleta_estado == 2)) {
  //       $boletasValidadas++;
  //     }
  //   }

  //   $res = array(
  //     'evento_titulo' => ($evento->evento_titulo) ? mb_convert_encoding($evento->evento_titulo, 'ISO-8859-1', 'UTF-8') : '',
  //     'evento_bono' => ($evento->evento_bono) ? (int) $evento->evento_bono : 0,
  //     'boletasValidadas' => $boletasValidadas,
  //     'boletasTodas' => $boletasTodas,
  //     'boletasEvento' => $boletasEvento,
  //   );

  //   echo json_encode($res);
  //   exit;
  // }

  public function getdetallesboletasAction()
  {
    $this->setLayout('blanco');
    $reservaId = $this->_getSanitizedParam("id");

    // Obtener las boletas de esta reserva
    $boletas = $this->mainModel->getBoletasByReserva($reservaId);
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $usuariosModel = new Administracion_Model_DbTable_Usuario();
    foreach ($boletas as $b) {
      $b->invitadoInfo = $invitadosModel->getList("  documento_invitado ='{$b->boleta_documento}' AND id_invitado ='{$b->boleta_asignacion}'")[0];

      if ($b->boleta_usuario_validador) {
        $b->usuarioValidador = $usuariosModel->getById($b->boleta_usuario_validador);
      }
    }

    // Obtener información de la reserva
    $reserva = $this->mainModel->getReservaById($reservaId);

    $this->_view->boletas = $boletas;
    $this->_view->reserva = $reserva;
    $this->_view->reservaId = $reservaId;
  }

  public function reenviarBoletasAction()
  {
    $this->setLayout('blanco');
    $reservaId = $this->_getSanitizedParam("id");

    try {
      // Aquí implementarías la lógica para reenviar las boletas por email
      // Por ahora devolvemos un éxito simulado
      $response = array(
        'success' => true,
        'message' => 'Boletas reenviadas correctamente'
      );
    } catch (Exception $e) {
      $response = array(
        'success' => false,
        'message' => 'Error al reenviar las boletas: ' . $e->getMessage()
      );
    }

    echo json_encode($response);
    exit;
  }

  public function exportarexcelAction()
  {
    // Implementar exportación a Excel
    $this->setLayout('blanco');

    // Por ahora redirigir de vuelta
    header('Location: ' . $this->route);
    exit;
  }

  public function getEstadisticasAction()
  {
    $this->setLayout('blanco');

    // Obtener estadísticas actualizadas
    $estadisticas = $this->mainModel->getEstadisticasEvento1();

    echo json_encode($estadisticas);
    exit;
  }
  protected function getFilter()
  {
    $filtros = " 1 = 1 ";
    if (Session::getInstance()->get($this->namefilter) != "") {
      $filters = (object) Session::getInstance()->get($this->namefilter);

      // Filtro por ID de reserva
      if (isset($filters->id_reserva) && $filters->id_reserva != '') {
        $filtros .= " AND r.id LIKE '%" . $filters->id_reserva . "%'";
      }

      // Filtro por carnet
      if (isset($filters->carnet) && $filters->carnet != '') {
        $filtros .= " AND r.reserva_numero_carnet LIKE '%" . $filters->carnet . "%'";
      }

      // Filtro por documento
      if (isset($filters->documento) && $filters->documento != '') {
        $filtros .= " AND r.reserva_documento LIKE '%" . $filters->documento . "%'";
      }

      // Filtro por nombre o apellido
      if (isset($filters->nombre_apellido) && $filters->nombre_apellido != '') {
        $filtros .= " AND (r.reserva_nombre_cliente LIKE '%" . $filters->nombre_apellido . "%' OR r.reserva_apellido_cliente LIKE '%" . $filters->nombre_apellido . "%')";
      }

      // Filtro por estado de envío
      if (isset($filters->estado_envio) && $filters->estado_envio != '') {
        switch ($filters->estado_envio) {
          case 'sin_enviar':
            $filtros .= " AND (r.reserva_boleteria_reenviada IS NULL OR r.reserva_boleteria_reenviada = 0)";
            break;
          case 'enviado':
            $filtros .= " AND r.reserva_boleteria_reenviada = 1";
            break;
          case 'completado':
            $filtros .= " AND (boletas_validadas = total_boletas AND total_boletas > 0)";
            break;
          case 'parcial':
            $filtros .= " AND (boletas_validadas > 0 AND boletas_validadas < total_boletas)";
            break;
        }
      }

      // Mantener filtros legacy para compatibilidad
      if (isset($filters->boleta_compra_email) && $filters->boleta_compra_email != '') {
        $filtros = $filtros . " AND r.reserva_correo LIKE '%" . $filters->boleta_compra_email . "%'";
      }
      if (isset($filters->boleta_compra_nombre) && $filters->boleta_compra_nombre != '') {
        $filtros = $filtros . " AND r.reserva_nombre_cliente LIKE '%" . $filters->boleta_compra_nombre . "%'";
      }
      if (isset($filters->boleta_compra_documento) && $filters->boleta_compra_documento != '') {
        $filtros = $filtros . " AND r.reserva_documento LIKE '%" . $filters->boleta_compra_documento . "%'";
      }
    }
    return $filtros;
  }

  /**
   * Recibe y asigna los filtros de este controlador
   *
   * @return void
   */
  protected function filters()
  {
    if ($this->getRequest()->isPost() == true) {
      Session::getInstance()->set($this->namepageactual, 1);
      $parramsfilter = array();

      // Nuevos filtros individuales
      $parramsfilter['id_reserva'] = $this->_getSanitizedParam("id_reserva");
      $parramsfilter['carnet'] = $this->_getSanitizedParam("carnet");
      $parramsfilter['documento'] = $this->_getSanitizedParam("documento");
      $parramsfilter['nombre_apellido'] = $this->_getSanitizedParam("nombre_apellido");
      $parramsfilter['estado_envio'] = $this->_getSanitizedParam("estado_envio");
      $parramsfilter['listo_envio'] = $this->_getSanitizedParam("listo_envio");

      // Mantener filtros legacy para compatibilidad
      $parramsfilter['boleta_compra_email'] = $this->_getSanitizedParam("boleta_compra_email");
      $parramsfilter['boleta_compra_nombre'] = $this->_getSanitizedParam("boleta_compra_nombre");
      $parramsfilter['boleta_compra_documento'] = $this->_getSanitizedParam("boleta_compra_documento");

      Session::getInstance()->set($this->namefilter, $parramsfilter);
    }
    if ($this->_getSanitizedParam("cleanfilter") == 1) {
      Session::getInstance()->set($this->namefilter, '');
      Session::getInstance()->set($this->namepageactual, 1);
    }
  }


  protected function handleError($message, $type = 'error')
  {
    // Guardar mensaje en la sesión
    Session::getInstance()->set('flash_message', $message);
    Session::getInstance()->set('flash_type', $type);

    // Redirigir al index
    header('Location: ' . $this->route);
    exit;
  }

  public function verlogsAction()
  {
    $this->route = "/administracion/infoboletas";
    $this->_view->route = $this->route;

    $title = "Logs del Sistema de Boletas (Reenvío y Reenvío Simple)";
    $this->getLayout()->setTitle($title);
    $this->_view->titlesection = $title;

    // Obtener filtros
    $tipoFiltro = $this->_getSanitizedParam("tipo");
    $fechaDesde = $this->_getSanitizedParam("fecha_desde");
    $fechaHasta = $this->_getSanitizedParam("fecha_hasta");
    $reservaId = $this->_getSanitizedParam("reserva_id");
    $sesionCompleta = $this->_getSanitizedParam("sesion_completa"); // Nueva opción

    // Modelo de logs
    $logModel = new Administracion_Model_DbTable_Log();

    // Construir filtro - incluir tanto REENVIO BOLETERIA como REENVIO SIMPLE
    $filtro = "(log_tipo LIKE '%REENVIO BOLETERIA%' OR log_tipo LIKE '%REENVIO SIMPLE%')";

    if (!empty($tipoFiltro)) {
      $filtro .= " AND log_tipo = '$tipoFiltro'";
    }

    if (!empty($fechaDesde)) {
      $filtro .= " AND DATE(log_fecha) >= '$fechaDesde'";
    }

    if (!empty($fechaHasta)) {
      $filtro .= " AND DATE(log_fecha) <= '$fechaHasta'";
    }

    if (!empty($reservaId)) {
      if ($sesionCompleta == '1') {
        // Modo sesión completa: buscar todos los logs en un rango de tiempo amplio que contengan la reserva
        // Primero encontrar logs que contengan explícitamente la reserva
        $logsReserva = $logModel->getList("(log_tipo LIKE '%REENVIO BOLETERIA%' OR log_tipo LIKE '%REENVIO SIMPLE%') AND (log_log LIKE '%reserva $reservaId%' OR log_log LIKE '%Reserva ID: $reservaId%')", "log_fecha ASC");

        if (!empty($logsReserva)) {
          // Obtener el rango de tiempo de la primera y última entrada
          $primeraFecha = $logsReserva[0]->log_fecha;
          $ultimaFecha = end($logsReserva)->log_fecha;

          // Ampliar el rango por 5 minutos antes y después para capturar logs relacionados
          $fechaInicio = date('Y-m-d H:i:s', strtotime($primeraFecha) - 300); // 5 minutos antes
          $fechaFin = date('Y-m-d H:i:s', strtotime($ultimaFecha) + 300);     // 5 minutos después

          // Filtrar por el rango de tiempo ampliado
          $filtro .= " AND log_fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
        } else {
          // Si no encuentra logs explícitos, usar filtro normal
          $filtro .= " AND (log_log LIKE '%reserva $reservaId%' OR log_log LIKE '%Reserva ID: $reservaId%')";
        }
      } else {
        // Buscar directamente por ID de reserva en el mensaje
        $filtro .= " AND (log_log LIKE '%reserva $reservaId%' OR log_log LIKE '%Reserva ID: $reservaId%')";
      }
    }

    // Obtener logs con paginación
    $order = "log_fecha DESC";

    // Obtener logs (sin paginación por ahora, obtener todos y paginar manualmente)
    // echo $filtro; // Debug del filtro - comentado para producción
    $logs = $logModel->getList($filtro, $order);

    // Obtener total para paginación
    $totalLogs = count($logs);

    // Implementar paginación manual
    $amount = 50; // Mostrar 50 logs por página
    $page = $this->_getSanitizedParam("page");
    if (!$page) {
      $page = 1;
    }

    $start = ($page - 1) * $amount;
    $logsPaginados = array_slice($logs, $start, $amount);
    // Obtener tipos únicos de logs para el filtro - incluir tanto REENVIO BOLETERIA como REENVIO SIMPLE
    $todosLogs = $logModel->getList("(log_tipo LIKE '%REENVIO BOLETERIA%' OR log_tipo LIKE '%REENVIO SIMPLE%')", "");
    $tiposUnicos = [];
    foreach ($todosLogs as $log) {
      if (!in_array($log->log_tipo, $tiposUnicos)) {
        $tiposUnicos[] = $log->log_tipo;
      }
    }

    // Pasar datos a la vista
    $this->_view->logs = $logsPaginados;
    $this->_view->tiposLogs = $tiposUnicos;
    $this->_view->totalLogs = $totalLogs;
    $this->_view->pages = ceil($totalLogs / $amount);
    $this->_view->page = $page;
    $this->_view->tipoFiltro = $tipoFiltro;
    $this->_view->fechaDesde = $fechaDesde;
    $this->_view->fechaHasta = $fechaHasta;
    $this->_view->reservaId = $reservaId;
    $this->_view->sesionCompleta = $sesionCompleta;
    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
    $this->_view->csrf_section = $this->_csrf_section;
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

      // Obtener todos los invitados de la reserva
      $logData = [
        'log_log' => "INFO: Obteniendo lista de invitados para reserva ID: $idReserva",
        'log_tipo' => 'REENVIO BOLETERIA - OBTENIENDO INVITADOS'
      ];
      $logModel->insert($logData);

      $invitadosReserva = $invitadosReservaModel->getList("reserva_id_reserva = '$idReserva'", "");

      if (empty($invitadosReserva)) {
        $this->handleError("No se encontraron invitados para la reserva ID: $idReserva");
      }

      // NOTA: ya no se aborta todo el proceso si algún invitado tiene boleta existente.
      // Con el envío 1 por 1 (guests/index.php) es normal que parte de los invitados ya
      // tengan su boleta enviada individualmente; el loop de procesamiento de abajo se
      // encarga de omitir/reenviar cada boleta existente según corresponda, sin bloquear
      // a los invitados que sí necesitan que se les genere la suya.

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

      // Validar datos de cada invitado. Con el envío 1 por 1, es normal que algunos
      // invitados aún no tengan sus datos completos mientras otros ya sí — en vez de
      // abortar todo el proceso, se omiten solo los incompletos y se procesan los demás.
      $idsInvitadosIncompletos = [];
      $invitadosDatosIncompletos = [];
      $logData = [
        'log_log' => "INFO: Iniciando validación de datos de " . count($invitadosReserva) . " invitados",
        'log_tipo' => 'REENVIO BOLETERIA - VALIDACION INICIADA'
      ];
      $logModel->insert($logData);

      foreach ($invitadosReserva as $invitado) {
        $validacion = $this->validarDatosInvitado($invitado);
        if (!$validacion['valido']) {
          $idsInvitadosIncompletos[$invitado->id_invitado] = true;
          $invitadosDatosIncompletos[] = [
            'documento' => $invitado->documento_invitado ?? 'SIN DOCUMENTO',
            'nombre' => $invitado->invitadoReserva_nombre_invitado ?? 'SIN NOMBRE',
            'apellido' => $invitado->invitadoReserva_apellido_invitado ?? 'SIN APELLIDO',
            'razon' => $validacion['razon']
          ];

          $logData = [
            'log_log' => "ADVERTENCIA: Invitado con datos incompletos (se omite) - ID: {$invitado->id_invitado}, Documento: " . ($invitado->documento_invitado ?? 'SIN DOCUMENTO') . ", Razón: {$validacion['razon']}",
            'log_tipo' => 'REENVIO BOLETERIA - DATOS INCOMPLETOS'
          ];
          $logModel->insert($logData);
        } else {
          $logData = [
            'log_log' => "INFO: Invitado validado correctamente - ID: {$invitado->id_invitado}, Documento: {$invitado->documento_invitado}, Nombre: {$invitado->invitadoReserva_nombre_invitado}, Apellido: {$invitado->invitadoReserva_apellido_invitado}",
            'log_tipo' => 'REENVIO BOLETERIA - INVITADO VALIDADO'
          ];
          $logModel->insert($logData);
        }
      }

      $logData = [
        'log_log' => "INFO: Validación completada - " . count($idsInvitadosIncompletos) . " invitado(s) con datos incompletos serán omitidos",
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
        // Invitado con datos incompletos: se omite, no bloquea a los demás.
        if (isset($idsInvitadosIncompletos[$invitado->id_invitado])) {
          continue;
        }

        // Verificar si ya existe boleta para este documento e invitado
        $existeBoleta = $boletasModel->getList("boleta_documento = '{$invitado->documento_invitado}' AND boleta_reserva_id = '$idReserva'", "");

        $existeArchivo = file_exists("/images_sales/qrs_news/{$existeBoleta[0]->boleta_uid}.png");

        if ($existeBoleta && count($existeBoleta) > 0 && $existeArchivo) {
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
          'log_log' => "INFO: Iniciando envío de email para reserva $idReserva - Total boletas a enviar: " . count($qrsGenerados),
          'log_tipo' => 'REENVIO BOLETERIA - EMAIL INICIADO'
        ];
        $logModel->insert($logData);

        $email = new Core_Model_Sendingemail($this->_view);
        $resultado = $email->generarCorreoBoleteriaNew($reserva, $qrsGenerados);

        if ($resultado) {
          $logData = [
            'log_log' => "SUCCESS: Email enviado exitosamente para reserva $idReserva",
            'log_tipo' => 'REENVIO BOLETERIA - EMAIL ENVIADO'
          ];
          $logModel->insert($logData);

          $reservasModel->editField($idReserva, 'reserva_boleteria_enviada', 1);

          $reservasModel->editField($idReserva, 'reserva_boleteria_reenviada', 1);
          $reservasModel->editField($idReserva, 'reserva_boleteria_reenviada_fecha', date('Y-m-d H:i:s'));

          if ($totalNuevas > 0 && $totalReenviadas > 0) {
            $this->_view->mensaje = "Boletería procesada correctamente: $totalNuevas nuevas boletas generadas y $totalReenviadas boletas reenviadas.";
          } elseif ($totalNuevas > 0) {
            $this->_view->mensaje = "Boletería generada correctamente: $totalNuevas nuevas boletas enviadas.";
          } elseif ($totalReenviadas > 0) {
            $this->_view->mensaje = "Boletería reenviada correctamente: $totalReenviadas boletas existentes reenviadas.";
          }
          if (!empty($idsInvitadosIncompletos)) {
            $this->_view->mensaje .= " (" . count($idsInvitadosIncompletos) . " invitado(s) se omitieron por tener datos incompletos.)";
          }
        } else {
          $logData = [
            'log_log' => "ERROR: Falló el envío de email para reserva $idReserva",
            'log_tipo' => 'REENVIO BOLETERIA - EMAIL FALLIDO'
          ];
          $logModel->insert($logData);

          $this->handleError("Error al enviar el correo de boletería.");
        }
      } else {
        if (!empty($idsInvitadosIncompletos) && count($idsInvitadosIncompletos) == count($invitadosReserva)) {
          $this->_view->mensaje = "No se generaron boletas: todos los invitados tienen datos incompletos (falta documento y/o nombre válidos).";
        } elseif ($forzarReenvio == '1') {
          $this->_view->mensaje = "No se encontraron boletas para enviar.";
        } else {
          $this->_view->mensaje = "No se generaron nuevas boletas. Todos los invitados con datos completos ya tenían boletería. Use forzar_reenvio=1 para incluir boletas existentes.";
        }
      }

      // Pasar datos a la vista para mostrar resumen
      $this->_view->reserva = $reserva;
      $this->_view->invitadosNuevos = $invitadosNuevos;
      $this->_view->invitadosOmitidos = $invitadosOmitidos;
      $this->_view->invitadosReenviados = $invitadosReenviados; // Nueva categoría
      $this->_view->invitadosDatosIncompletos = $invitadosDatosIncompletos;
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

  /**
   * Función secreta para reenviar correo de boletas existentes
   * Solo reenvía si ya existen QR, PDF y registros de boletas
   * Acceso: /administracion/infoboletas/reenviosecretoemail?reserva_id=XXX&clave=XXXXXX
   */
  public function reenviosecretoemailAction()
  {
    $this->setLayout('blanco');

    // Clave secreta para acceso
    $claveSecreta = "NoGaL2024ReEnvio";
    $claveIngresada = $this->_getSanitizedParam("clave");
    $reservaId = $this->_getSanitizedParam("reserva_id");

    // Validar clave secreta
    if ($claveIngresada !== $claveSecreta) {
      echo json_encode([
        'success' => false,
        'message' => 'Acceso denegado - Clave incorrecta'
      ]);
      exit;
    }

    // Validar parámetro de reserva
    if (!$reservaId) {
      echo json_encode([
        'success' => false,
        'message' => 'Falta el parámetro reserva_id'
      ]);
      exit;
    }

    try {
      // Modelos necesarios
      $reservasModel = new Administracion_Model_DbTable_Reservas();
      $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
      $logModel = new Administracion_Model_DbTable_Log();

      // Log inicio del proceso secreto
      $logData = [
        'log_log' => "INICIO REENVIO SECRETO: Proceso iniciado para reserva $reservaId",
        'log_tipo' => 'REENVIO SECRETO - INICIO'
      ];
      $logModel->insert($logData);

      // Obtener datos de la reserva
      $reserva = $reservasModel->getById($reservaId);
      if (!$reserva) {
        throw new Exception("Reserva no encontrada con ID: $reservaId");
      }

      $logData = [
        'log_log' => "INFO: Reserva encontrada - Cliente: {$reserva->reserva_nombre_cliente} {$reserva->reserva_apellido_cliente}",
        'log_tipo' => 'REENVIO SECRETO - RESERVA ENCONTRADA'
      ];
      $logModel->insert($logData);

      // Obtener boletas existentes de esta reserva
      $boletas = $boletasModel->getList("boleta_reserva_id = '$reservaId'", "boleta_id ASC");

      if (empty($boletas)) {
        throw new Exception("No se encontraron boletas para la reserva ID: $reservaId");
      }

      $logData = [
        'log_log' => "INFO: Encontradas " . count($boletas) . " boletas para la reserva",
        'log_tipo' => 'REENVIO SECRETO - BOLETAS ENCONTRADAS'
      ];
      $logModel->insert($logData);

      // Validar que TODAS las boletas tengan archivos QR y PDF
      $qrsGenerados = [];
      $archivosIncompletos = [];

      foreach ($boletas as $boleta) {
        // Verificar QR
        $rutaQR = "images_sales/qrs_news/{$boleta->boleta_uid}.png";
        $existeQR = file_exists($rutaQR);

        // Verificar PDF
        $rutaPDF = PDFS_PATH_NEWS . "boleta_cena_{$boleta->boleta_uid}.pdf";
        $existePDF = file_exists($rutaPDF);

        if (!$existeQR || !$existePDF) {
          $archivosIncompletos[] = [
            'boleta_id' => $boleta->boleta_id,
            'boleta_uid' => $boleta->boleta_uid,
            'qr_existe' => $existeQR,
            'pdf_existe' => $existePDF
          ];

          $logData = [
            'log_log' => "ERROR: Archivos incompletos para boleta {$boleta->boleta_uid} - QR: " . ($existeQR ? 'SÍ' : 'NO') . ", PDF: " . ($existePDF ? 'SÍ' : 'NO'),
            'log_tipo' => 'REENVIO SECRETO - ARCHIVOS INCOMPLETOS'
          ];
          $logModel->insert($logData);
        } else {
          // Agregar boleta válida a la lista para envío
          $qrsGenerados[] = [
            "boleta_id" => $boleta->boleta_id,
            "boleta_uid" => $boleta->boleta_uid,
            "boleta_token" => $boleta->boleta_token,
            "boleta_numero_ticket" => $boleta->boleta_numero_ticket,
            "rutaQR" => $rutaQR,
            "email" => $reserva->reserva_correo,
            "nombre" => $reserva->reserva_nombre_cliente . " " . $reserva->reserva_apellido_cliente,
            "telefono" => $reserva->reserva_telefono,
            "estado" => $boleta->boleta_estado,
            "documento" => $boleta->boleta_documento,
            "tipoBoleta" => $boleta->boleta_tipo_boleta,
          ];

          $logData = [
            'log_log' => "OK: Boleta válida {$boleta->boleta_uid} - QR y PDF existen",
            'log_tipo' => 'REENVIO SECRETO - BOLETA VALIDA'
          ];
          $logModel->insert($logData);
        }
      }

      // Si hay archivos incompletos, no enviar nada
      if (!empty($archivosIncompletos)) {
        $totalIncompletos = count($archivosIncompletos);
        throw new Exception("Se encontraron $totalIncompletos boleta(s) con archivos incompletos. No se puede enviar el correo.");
      }

      // Si no hay QRs válidos para enviar
      if (empty($qrsGenerados)) {
        throw new Exception("No hay boletas válidas para enviar");
      }

      $logData = [
        'log_log' => "INFO: Todas las boletas tienen archivos válidos. Preparando envío de " . count($qrsGenerados) . " boletas",
        'log_tipo' => 'REENVIO SECRETO - PREPARANDO ENVIO'
      ];
      $logModel->insert($logData);

      // Enviar correo usando la clase de email existente
      $email = new Core_Model_Sendingemail($this->_view);
      $resultado = $email->generarCorreoBoleteriaNewUnico($reserva, $qrsGenerados);

      if ($resultado) {
        // Actualizar flag de reenvío en la reserva
        $reservasModel->editField($reservaId, 'reserva_boleteria_reenviada', 1);
        $reservasModel->editField($reservaId, 'reserva_boleteria_reenviada_fecha', date('Y-m-d H:i:s'));

        // Actualizar flag de reenvío en cada boleta
        foreach ($boletas as $boleta) {
          $boletasModel->editField($boleta->boleta_id, 'boleta_reenvio', 1);
          $boletasModel->editField($boleta->boleta_id, 'boleta_reenvio_fecha', date('Y-m-d H:i:s'));
        }

        $logData = [
          'log_log' => "ÉXITO: Correo enviado exitosamente para reserva $reservaId con " . count($qrsGenerados) . " boletas",
          'log_tipo' => 'REENVIO SECRETO - EXITO'
        ];
        $logModel->insert($logData);

        echo json_encode([
          'success' => true,
          'message' => "Correo reenviado exitosamente para reserva $reservaId",
          'boletas_enviadas' => count($qrsGenerados),
          'email_destino' => $reserva->reserva_correo,
          'cliente' => $reserva->reserva_nombre_cliente . " " . $reserva->reserva_apellido_cliente
        ]);
      } else {
        throw new Exception("Error al enviar el correo");
      }
    } catch (Exception $e) {
      $logData = [
        'log_log' => "ERROR REENVIO SECRETO: " . $e->getMessage(),
        'log_tipo' => 'REENVIO SECRETO - ERROR'
      ];
      $logModel->insert($logData);

      echo json_encode([
        'success' => false,
        'message' => 'Error en el reenvío: ' . $e->getMessage()
      ]);
    }
    // curl "https://tudominio.com/administracion/infoboletas/reenviosecretoemail?reserva_id=123&clave=NoGaL2024ReEnvio"
    exit;
  }

  public function escaneologsAction()
  {
    $this->route = "/administracion/infoboletas";
    $this->_view->route = $this->route;

    $title = "Logs de Auditoría - Trazabilidad de Escaneo";
    $this->getLayout()->setTitle($title);
    $this->_view->titlesection = $title;

    // Procesar filtros si es POST
    if ($this->getRequest()->isPost()) {
      $accionFiltro = $this->_getSanitizedParam("accion");
      $resultadoFiltro = $this->_getSanitizedParam("resultado");
      $metodoFiltro = $this->_getSanitizedParam("metodo");
      $fechaDesde = $this->_getSanitizedParam("fecha_desde");
      $fechaHasta = $this->_getSanitizedParam("fecha_hasta");
      $documentoFiltro = $this->_getSanitizedParam("documento");
      $usuarioFiltro = $this->_getSanitizedParam("usuario");
    } else {
      // Obtener filtros de GET
      $accionFiltro = $this->_getSanitizedParam("accion");
      $resultadoFiltro = $this->_getSanitizedParam("resultado");
      $metodoFiltro = $this->_getSanitizedParam("metodo");
      $fechaDesde = $this->_getSanitizedParam("fecha_desde");
      $fechaHasta = $this->_getSanitizedParam("fecha_hasta");
      $documentoFiltro = $this->_getSanitizedParam("documento");
      $usuarioFiltro = $this->_getSanitizedParam("usuario");
    }

    // Modelo de auditoría
    $boleterialogs = new Administracion_Model_DbTable_Auditoriaboleta();

    // Construir filtro
    $filtro = "1=1";

    if (!empty($accionFiltro)) {
      $filtro .= " AND auditoriaboleta_accion = '$accionFiltro'";
    }

    if (!empty($resultadoFiltro)) {
      $filtro .= " AND auditoriaboleta_resultado = '$resultadoFiltro'";
    }

    if (!empty($metodoFiltro)) {
      $filtro .= " AND auditoriaboleta_metodo_escaneado = '$metodoFiltro'";
    }

    if (!empty($fechaDesde)) {
      $filtro .= " AND DATE(auditoriaboleta_fecha_hora) >= '$fechaDesde'";
    }

    if (!empty($fechaHasta)) {
      $filtro .= " AND DATE(auditoriaboleta_fecha_hora) <= '$fechaHasta'";
    }

    if (!empty($documentoFiltro)) {
      $filtro .= " AND auditoriaboleta_documento_escaneado LIKE '%$documentoFiltro%'";
    }

    if (!empty($usuarioFiltro)) {
      $filtro .= " AND auditoriaboleta_usuario_validador_nombre LIKE '%$usuarioFiltro%'";
    }

    // Ordenar por fecha más reciente
    $order = "auditoriaboleta_id DESC";

    // Paginación manual ya que getList no soporta LIMIT
    $amount = 200; // Mostrar 50 logs por página
    $page = $this->_getSanitizedParam("page");
    if (!$page) {
      $page = 1;
    }

    // Obtener todos los logs con filtros aplicados
    $allLogs = $boleterialogs->getList($filtro, $order);

    // Obtener total para paginación
    $totalLogs = count($allLogs);

    // Paginación manual
    $start = ($page - 1) * $amount;
    $logs = array_slice($allLogs, $start, $amount);

    // Obtener valores únicos para los filtros (de todos los logs)
    $todasAcciones = $boleterialogs->getList("1=1", "");
    $acciones = [];
    $resultados = [];
    $metodos = [];

    foreach ($todasAcciones as $log) {
      if ($log->auditoriaboleta_accion && !in_array($log->auditoriaboleta_accion, $acciones)) {
        $acciones[] = $log->auditoriaboleta_accion;
      }
      if ($log->auditoriaboleta_resultado && !in_array($log->auditoriaboleta_resultado, $resultados)) {
        $resultados[] = $log->auditoriaboleta_resultado;
      }
      if ($log->auditoriaboleta_metodo_escaneado && !in_array($log->auditoriaboleta_metodo_escaneado, $metodos)) {
        $metodos[] = $log->auditoriaboleta_metodo_escaneado;
      }
    }

    // Pasar datos a la vista
    $this->_view->logs = $logs;
    $this->_view->totalLogs = $totalLogs;
    $this->_view->totalpages = ceil($totalLogs / $amount);
    $this->_view->page = $page;
    $this->_view->amount = $amount;

    // Filtros para la vista
    $this->_view->accionFiltro = $accionFiltro;
    $this->_view->resultadoFiltro = $resultadoFiltro;
    $this->_view->metodoFiltro = $metodoFiltro;
    $this->_view->fechaDesde = $fechaDesde;
    $this->_view->fechaHasta = $fechaHasta;
    $this->_view->documentoFiltro = $documentoFiltro;
    $this->_view->usuarioFiltro = $usuarioFiltro;

    // Opciones para los filtros
    $this->_view->acciones = $acciones;
    $this->_view->resultados = $resultados;
    $this->_view->metodos = $metodos;

    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
    $this->_view->csrf_section = $this->_csrf_section;
  }

  /**
   * Muestra sesiones de auditoría agrupadas para mejor trazabilidad
   */
  public function sesionesAction()
  {
    $this->route = "/administracion/infoboletas";
    $this->_view->route = $this->route;

    $title = "Sesiones de Auditoría - Trazabilidad Completa";
    $this->getLayout()->setTitle($title);
    $this->_view->titlesection = $title;

    $auditoriaModel = new Administracion_Model_DbTable_Auditoriaboleta();

    // Filtros
    $fechaDesde = $this->_getSanitizedParam('fecha_desde') ?: date('Y-m-d', strtotime('-7 days'));
    $fechaHasta = $this->_getSanitizedParam('fecha_hasta') ?: date('Y-m-d');
    $usuarioFiltro = $this->_getSanitizedParam('usuario');
    $metodoFiltro = $this->_getSanitizedParam('metodo');
    $resultadoFiltro = $this->_getSanitizedParam('resultado');

    // Paginación
    $page = (int) $this->_getSanitizedParam("page") ?: 1;
    $amount = 25;
    $start = ($page - 1) * $amount;

    // Obtener sesiones con filtros
    $limite = $amount;
    $sesiones = $auditoriaModel->getSesionesRecientes($limite, $usuarioFiltro);

    // Aplicar filtros adicionales
    if ($metodoFiltro || $resultadoFiltro || $fechaDesde || $fechaHasta) {
      $sesiones = array_filter($sesiones, function ($sesion) use ($metodoFiltro, $resultadoFiltro, $fechaDesde, $fechaHasta) {
        // Filtro por método
        if ($metodoFiltro && $sesion->auditoriaboleta_metodo_escaneado != $metodoFiltro) {
          return false;
        }

        // Filtro por resultado (si tiene validación exitosa)
        if ($resultadoFiltro == 'exitoso' && !$sesion->validacion_completada) {
          return false;
        }
        if ($resultadoFiltro == 'fallido' && $sesion->validacion_completada) {
          return false;
        }

        // Filtro por fecha
        $fechaSesion = date('Y-m-d', strtotime($sesion->sesion_inicio));
        if ($fechaDesde && $fechaSesion < $fechaDesde) {
          return false;
        }
        if ($fechaHasta && $fechaSesion > $fechaHasta) {
          return false;
        }

        return true;
      });
    }

    // Estadísticas de sesiones
    $estadisticas = $auditoriaModel->getEstadisticasSesiones($fechaDesde, $fechaHasta);

    // Datos para la vista
    $this->_view->sesiones = $sesiones;
    $this->_view->estadisticas = $estadisticas;
    $this->_view->totalSesiones = count($sesiones);
    $this->_view->page = $page;
    $this->_view->amount = $amount;

    // Filtros para la vista
    $this->_view->fechaDesde = $fechaDesde;
    $this->_view->fechaHasta = $fechaHasta;
    $this->_view->usuarioFiltro = $usuarioFiltro;
    $this->_view->metodoFiltro = $metodoFiltro;
    $this->_view->resultadoFiltro = $resultadoFiltro;

    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
    $this->_view->csrf_section = $this->_csrf_section;
  }

  /**
   * Obtiene los detalles de una sesión específica vía AJAX
   */
  public function detallesesionAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $sessionId = $this->_getSanitizedParam('session_id');

    if (!$sessionId) {
      echo json_encode(['error' => 'Session ID requerido']);
      exit;
    }

    $auditoriaModel = new Administracion_Model_DbTable_Auditoriaboleta();
    $logs = $auditoriaModel->getLogsPorSesion($sessionId);

    // Procesar los logs para enviar al frontend
    $logsProcesados = [];
    foreach ($logs as $log) {
      $logsProcesados[] = [
        'id' => $log->auditoriaboleta_id,
        'accion' => $log->auditoriaboleta_accion,
        'resultado' => $log->auditoriaboleta_resultado,
        'fecha_hora' => $log->auditoriaboleta_fecha_hora,
        'motivo_fallo' => $log->auditoriaboleta_motivo_fallo,
        'observaciones' => $log->auditoriaboleta_observaciones,
        'documento' => $log->auditoriaboleta_documento_escaneado,
        'metodo' => $log->auditoriaboleta_metodo_escaneado,
        'url' => $log->auditoriaboleta_url_completa,
        'ip' => $log->auditoriaboleta_ip_address
      ];
    }

    echo json_encode([
      'success' => true,
      'session_id' => $sessionId,
      'logs' => $logsProcesados,
      'total_logs' => count($logsProcesados)
    ]);
    exit;
  }
  public function reenviarboletassimpleAction()
  {
    // Obtener parámetros
    $idReserva = $this->_getSanitizedParam("id_reserva");

    // Validar parámetro requerido
    if (empty($idReserva)) {
      $this->handleError("Debe proporcionar el ID de la reserva.");
    }

    // Modelo de logs
    $logModel = new Administracion_Model_DbTable_Log();

    // Log de inicio
    $logData = [
      'log_log' => "INICIO REENVIO SIMPLE - Reserva ID: $idReserva",
      'log_tipo' => 'REENVIO SIMPLE - INICIO'
    ];
    $logModel->insert($logData);

    // Modelos necesarios
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $invitadosReservaModel = new Administracion_Model_DbTable_Invitadosreservas();
    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
    $mesasModel = new Administracion_Model_DbTable_Mesas();

    try {
      // Obtener datos de la reserva
      $reserva = $reservasModel->getById($idReserva);
      if (!$reserva) {
        $this->handleError("Reserva no encontrada con ID: $idReserva");
      }

      // Log de reserva encontrada
      $logData = [
        'log_log' => "Reserva encontrada - Cliente: {$reserva->reserva_nombre_cliente} {$reserva->reserva_apellido_cliente}, Email: {$reserva->reserva_correo}",
        'log_tipo' => 'REENVIO SIMPLE - RESERVA'
      ];
      $logModel->insert($logData);

      // Obtener todos los invitados de la reserva
      $invitadosReserva = $invitadosReservaModel->getList("reserva_id_reserva = '$idReserva'", "");
      if (!($invitadosReserva)) {
        $this->handleError("No se encontraron invitados para la reserva ID: $idReserva");
      }

      // Obtener todas las boletas existentes para esta reserva
      $boletasExistentes = $boletasModel->getList("boleta_reserva_id = '$idReserva'", "");

      // Log de datos encontrados
      $logData = [
        'log_log' => "Datos obtenidos - Invitados: " . count($invitadosReserva) . ", Boletas existentes: " . count($boletasExistentes),
        'log_tipo' => 'REENVIO SIMPLE - DATOS'
      ];
      $logModel->insert($logData);

      // Arrays para el procesamiento
      $documentosInvitados = [];
      $documentosBoletas = [];
      $boletasParaReenvio = [];
      $boletasParaCrear = [];
      $boletasParaInvalidar = [];

      // Mapear documentos de invitados
      foreach ($invitadosReserva as $invitado) {
        $documentosInvitados[] = $invitado->documento_invitado;
      }

      // Log del análisis de archivos
      $logData = [
        'log_log' => "Iniciando análisis de boletas y archivos existentes",
        'log_tipo' => 'REENVIO SIMPLE - ANALISIS'
      ];
      $logModel->insert($logData);

      // Mapear documentos de boletas y verificar archivos
      foreach ($boletasExistentes as $boleta) {
        $documentosBoletas[] = $boleta->boleta_documento;

        // Verificar si el archivo QR existe
        $archivoQR = "images_sales/qrs_news/{$boleta->boleta_uid}.png";
        $archivoPDF = PDFS_PATH_NEWS . "boleta_cena_{$boleta->boleta_uid}.pdf";
        $existeQR = file_exists($archivoQR);
        $existePDF = file_exists($archivoPDF);

        if (in_array($boleta->boleta_documento, $documentosInvitados)) {
          // Existe invitado y boleta
          if ($existeQR && $existePDF) {
            $boletasParaReenvio[] = $boleta;
          } else {
            // No existen archivos completos, crear nueva boleta
            $boletasParaCrear[] = $this->getInvitadoPorDocumentoSimple($invitadosReserva, $boleta->boleta_documento);

            // Log de boleta sin archivos
            $logData = [
              'log_log' => "Boleta ID {$boleta->boleta_id} (UID: {$boleta->boleta_uid}) - Archivos faltantes (QR: " . ($existeQR ? 'SI' : 'NO') . ", PDF: " . ($existePDF ? 'SI' : 'NO') . ") - Marcada para recrear",
              'log_tipo' => 'REENVIO SIMPLE - ARCHIVOS FALTANTES'
            ];
            $logModel->insert($logData);
          }
        } else {
          // Existe boleta pero no invitado - VALIDAR ANTES DE INVALIDAR
          // PERO PRIMERO VERIFICAR SI YA ESTÁ VALIDADA
          if ($boleta->boleta_estado == 2) {
            // Esta boleta está validada y se intentaría invalidar - ERROR
            $logData = [
              'log_log' => "ERROR: Boleta ID {$boleta->boleta_id} (UID: {$boleta->boleta_uid}) está VALIDADA y no puede ser invalidada",
              'log_tipo' => 'REENVIO SIMPLE - ERROR VALIDADA'
            ];
            $logModel->insert($logData);

            $this->handleError("No se puede procesar la boletería. La boleta #{$boleta->boleta_id} (UID: {$boleta->boleta_uid}) con documento {$boleta->boleta_documento} ya está VALIDADA y no puede ser invalidada por seguridad. Contacte al administrador del sistema.");
          } else {
            // Solo agregar a invalidar si NO está validada
            $boletasParaInvalidar[] = $boleta;

            // Log de boleta huérfana
            $logData = [
              'log_log' => "Boleta ID {$boleta->boleta_id} (UID: {$boleta->boleta_uid}) - Documento {$boleta->boleta_documento} no existe en invitados - Marcada para invalidar",
              'log_tipo' => 'REENVIO SIMPLE - BOLETA HUERFANA'
            ];
            $logModel->insert($logData);
          }
        }
      }

      // Buscar invitados sin boleta
      foreach ($invitadosReserva as $invitado) {
        if (!in_array($invitado->documento_invitado, $documentosBoletas)) {
          $boletasParaCrear[] = $invitado;
        }
      }

      // Log del resumen de análisis
      $logData = [
        'log_log' => "Resumen análisis - Para reenvío: " . count($boletasParaReenvio) . ", Para crear: " . count($boletasParaCrear) . ", Para invalidar: " . count($boletasParaInvalidar),
        'log_tipo' => 'REENVIO SIMPLE - RESUMEN'
      ];
      $logModel->insert($logData);

      // Procesar invalidaciones
      foreach ($boletasParaInvalidar as $boleta) {
        $boletasModel->editField($boleta->boleta_id, 'boleta_estado', 3);

        // Log de invalidación
        $logData = [
          'log_log' => "Boleta invalidada - Reserva ID: $idReserva, Boleta ID: {$boleta->boleta_id}, UID: {$boleta->boleta_uid}, Documento: {$boleta->boleta_documento}",
          'log_tipo' => 'REENVIO SIMPLE - INVALIDADA'
        ];
        $logModel->insert($logData);
      }

      // Obtener mesas disponibles
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
        }
      }

      // Procesar creación de nuevas boletas
      $contadorTicket = count($boletasExistentes) + 1;
      $qrsGenerados = [];

      foreach ($boletasParaCrear as $invitado) {
        // Log de creación de boleta
        $logData = [
          'log_log' => "Creando nueva boleta para documento: {$invitado->documento_invitado}, Nombre: {$invitado->invitadoReserva_nombre_invitado}, Apellido: {$invitado->invitadoReserva_apellido_invitado}",
          'log_tipo' => 'REENVIO SIMPLE - CREANDO BOLETA'
        ];
        $logModel->insert($logData);

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
          $this->handleError("No se pudo asignar una mesa a todos los invitados nuevos.");
        }

        // Crear nueva boleta
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

        // Generar token único
        $yearMonth = date("Ym", strtotime($reserva->reserva_fecha));
        $customUid = "B-{$yearMonth}-" . str_pad($nextId, 7, "0", STR_PAD_LEFT);
        $baseString = "{$idReserva}-{$reserva->reserva_correo}-{$yearMonth}-{$nextId}";
        $token = substr(base_convert(hash('sha256', $baseString), 16, 36), 0, 12);

        $boletasModel->updateGeneratedQR($id, $customUid, $token, 1, $invitado->id_invitado, $mesaAsignadaId);

        // Log de boleta creada
        $logData = [
          'log_log' => "Boleta creada exitosamente - Reserva ID: $idReserva, Boleta ID: $id, UID: $customUid, Mesa: $mesaAsignadaId",
          'log_tipo' => 'REENVIO SIMPLE - BOLETA CREADA'
        ];
        $logModel->insert($logData);

        // Generar QR y PDF usando funciones existentes
        $this->generarQRReenvio($customUid, $token, $invitado->documento_invitado);

        // Obtener la boleta actualizada para generar el PDF
        $boletaCompleta = $boletasModel->getById($id);
        $ambienteModel = new Administracion_Model_DbTable_Ambientes();
        $pisosModel = new Administracion_Model_DbTable_Pisos();
        $mesa->ambienteinfo = $ambienteModel->getById($mesa->mesa_ambiente);
        $mesa->pisoInfo = $pisosModel->getById($mesa->ambienteinfo->ambiente_piso);
        $mesainfo = $mesa;
        $ambienteModel = new Administracion_Model_DbTable_Ambientes();
        $ambiente = $ambienteModel->getById($mesa->mesa_ambiente);

        // Generar PDF
        $this->generarPDFReenvio($reserva, $boletaCompleta, $mesa, $ambiente, $invitado);
        // $this->generarPDFReenvio($reserva, $boleta, $mesasDisponibles[$mesaAsignadaId]['mesa'], $ambiente, $invitado);

        // $qrsGenerados[] = [
        //   'boleta_id' => $id,
        //   'documento' => $invitado->documento_invitado,
        //   'nombre' => $invitado->invitadoReserva_nombre_invitado,
        //   'uid' => $customUid,
        //   'token' => $token,
        //   'tipo' => 'nueva'
        // ];
        $qrsGenerados[] = [
          "boleta_id" => $id,
          "boleta_uid" => $customUid,
          "boleta_token" => $token,
          "boleta_numero_ticket" => $contadorTicket,
          "rutaQR" => "images_sales/qrs_news/{$boleta->boleta_uid}.png",
          "email" => $reserva->reserva_correo,
          "nombre" => $reserva->reserva_nombre_cliente . " " . $reserva->reserva_apellido_cliente,
          "telefono" => $reserva->reserva_telefono,
          "estado" => $boleta->boleta_estado,
          "documento" => $invitado->documento_invitado,
          "tipoBoleta" => $invitado->invitadoReserva_estado_invitado,
          "mesa_asignada" => $mesaAsignadaId,
          "tipo_envio" => "NUEVA" // Marcador para identificar que es una boleta nueva
        ];
        $contadorTicket++;
      }

      // Agregar boletas para reenvío (sin generar nuevos archivos)
      foreach ($boletasParaReenvio as $boleta) {
        $invitadoBoleta = $invitadosReservaModel->getById($boleta->boleta_asignacion);
        if ($invitadoBoleta->documento_invitado != $boleta->boleta_documento) {
          $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();

          $invitadoBoleta = $invitadosModel->getList("documento_invitado = '{$boleta->boleta_documento}' AND reserva_id_reserva = '$idReserva'")[0];

          $boletasModel->editField($boleta->boleta_id, 'boleta_asignacion', $invitadoBoleta->id_invitado);
          $logData = [
            'log_log' => "Boleta re-asignada - Reserva ID: $idReserva, Boleta ID: {$boleta->boleta_id}, UID: {$boleta->boleta_uid}, Documento: {$boleta->boleta_documento}, Nuevo Invitado: {$invitadoBoleta->id_invitado}",
            'log_tipo' => 'REENVIO SIMPLE - BOLETA RE-ASIGNADA'
          ];
          $logModel->insert($logData);
        }
        $boletasModel->editField($boleta->boleta_id, 'boleta_reenvio', 1);
        $boletasModel->editField($boleta->boleta_id, 'boleta_reenvio_fecha', date('Y-m-d H:i:s'));

        // Log de reenvío
        $logData = [
          'log_log' => "Boleta marcada para reenvío - Reserva ID: $idReserva, Boleta ID: {$boleta->boleta_id}, UID: {$boleta->boleta_uid}, Documento: {$boleta->boleta_documento}",
          'log_tipo' => 'REENVIO SIMPLE - MARCADA REENVIO'
        ];
        $logModel->insert($logData);

        $invitadoBoleta = $invitadosReservaModel->getById($boleta->boleta_asignacion);
        // $qrsGenerados[] = [
        //   'boleta_id' => $boleta->boleta_id,
        //   'documento' => $boleta->boleta_documento,
        //   'nombre' => $boleta->boleta_nombre,
        //   'uid' => $boleta->boleta_uid,
        //   'token' => $boleta->boleta_token,
        //   'tipo' => 'reenvio'
        // ];
        $qrsGenerados[] = [
          "boleta_id" => $boleta->boleta_id,
          "boleta_uid" => $boleta->boleta_uid,
          "boleta_token" => $boleta->boleta_token,
          "boleta_numero_ticket" => $boleta->boleta_numero_ticket,
          "rutaQR" => "images_sales/qrs_news/{$boleta->boleta_uid}.png",
          "email" => $reserva->reserva_correo,
          "nombre" => $reserva->reserva_nombre_cliente . " " . $reserva->reserva_apellido_cliente,
          "telefono" => $reserva->reserva_telefono,
          "estado" => $boleta->boleta_estado,
          "documento" => $boleta->boleta_documento,
          "tipoBoleta" => $boleta->boleta_tipo_boleta,
          "mesa_asignada" => $mesaAsignadaId,
          "tipo_envio" => "REENVIO" // Marcador para identificar que es reenvío
        ];
      }
      $logData = [
        'log_log' => print_r($qrsGenerados, true),
        'log_tipo' => 'REENVIO SIMPLE - PREPARANDO ENVIO - DOCUMENTO: ' . $boleta->boleta_documento
      ];
      $logModel->insert($logData);
      // Actualizar estado de la reserva
      $reservasModel->editField($idReserva, 'reserva_boleteria_reenviada', 1);

      // Log del proceso de envío
      $logData = [
        'log_log' => "Preparando envío de email - Total boletas: " . count($qrsGenerados) . " (Nuevas: " . count($boletasParaCrear) . ", Reenvío: " . count($boletasParaReenvio) . ")",
        'log_tipo' => 'REENVIO SIMPLE - PREPARANDO ENVIO'
      ];
      $logModel->insert($logData);

      // Enviar email si hay boletas para procesar
      if (($qrsGenerados)) {
        $email = new Core_Model_Sendingemail($this->_view);
        $resultado = $email->generarCorreoBoleteriaNew($reserva, $qrsGenerados);

        if ($resultado) {
          // Log de éxito en envío
          $logData = [
            'log_log' => "Email enviado exitosamente a {$reserva->reserva_correo} con " . count($qrsGenerados) . " boletas",
            'log_tipo' => 'REENVIO SIMPLE - EMAIL ENVIADO'
          ];
          $logModel->insert($logData);
        } else {
          // Log de error en envío
          $logData = [
            'log_log' => "Error al enviar email a {$reserva->reserva_correo}",
            'log_tipo' => 'REENVIO SIMPLE - ERROR EMAIL'
          ];
          $logModel->insert($logData);
        }
      }

      // Log de finalización exitosa
      $logData = [
        'log_log' => "PROCESO COMPLETADO EXITOSAMENTE - Reserva ID: $idReserva, Total boletas procesadas: " . count($qrsGenerados),
        'log_tipo' => 'REENVIO SIMPLE - COMPLETADO'
      ];
      $logModel->insert($logData);

      // Redirigir con mensaje de éxito
      Session::getInstance()->set('flash_message', 'Boletería procesada correctamente. Total: ' . count($qrsGenerados) . ' boletas.');
      Session::getInstance()->set('flash_type', 'success');
      header('Location: ' . $this->route);
      exit;
    } catch (Exception $e) {
      // Log de error
      $logData = [
        'log_log' => "ERROR EN PROCESO - Reserva ID: $idReserva, Error: " . $e->getMessage(),
        'log_tipo' => 'REENVIO SIMPLE - ERROR'
      ];
      $logModel->insert($logData);

      $this->handleError('Error al procesar la boletería: ' . $e->getMessage());
    }
  }

  private function getInvitadoPorDocumentoSimple($invitados, $documento)
  {
    foreach ($invitados as $invitado) {
      if ($invitado->documento_invitado == $documento) {
        return $invitado;
      }
    }
    return null;
  }
}
