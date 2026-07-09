<?php

/**
 * Controlador de Reservas que permite la  creacion, edicion  y eliminacion de los reservas del Sistema
 */
class Administracion_reservasController extends Administracion_mainController
{
  public $botonpanel = 12;

  /**
   * $mainModel  instancia del modelo de  base de datos reservas
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
  protected $_csrf_section = "administracion_reservas";

  /**
   * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
   * @var string
   */
  protected $namepages;

  protected $namepageactual;



  /**
   * Inicializa las variables principales del controlador reservas .
   *
   * @return void.
   */
  public function init()
  {
    $this->mainModel = new Administracion_Model_DbTable_Reservas();
    $this->namefilter = "parametersfilterreservas";
    $this->route = "/administracion/reservas";
    $this->namepages = "pages_reservas";
    $this->namepageactual = "page_actual_reservas";
    $this->_view->route = $this->route;
    if (Session::getInstance()->get($this->namepages)) {
      $this->pages = Session::getInstance()->get($this->namepages);
    } else {
      $this->pages = 20;
    }

    // Cargar eventos siempre disponibles para las vistas
    $this->_view->eventos = $this->getEventos();
    $url = $_SERVER['REQUEST_URI'];
    if (str_contains($url, 'listadofacturacion')) {
      $this->botonpanel = 21;
    }
    if (str_contains($url, 'informes')) {
      $this->botonpanel = 24;
    }
    parent::init();
  }


  /**
   * Recibe la informacion y  muestra un listado de  reservas con sus respectivos filtros.
   *
   * @return void.
   */
  public function indexAction()
  {
    $title = "Administración de reservas";
    $this->getLayout()->setTitle($title);
    $this->_view->titlesection = $title;
    $this->filters();
    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
    $filters = (object) Session::getInstance()->get($this->namefilter);
    $this->_view->filters = $filters;

    $this->_view->successEliminarReserva = Session::getInstance()->get('success_eliminar_reserva');
    $this->_view->errorEliminarReserva = Session::getInstance()->get('error_eliminar_reserva');
    Session::getInstance()->set('success_eliminar_reserva', '');
    Session::getInstance()->set('error_eliminar_reserva', '');

    $ambientesModel = new Administracion_Model_DbTable_Ambientes();
    $this->_view->ambientes = $ambientesModel->getList("ambiente_estado = '1'", "ambiente_nombre ASC");

    $filters = $this->getFilter();
    $order = "id DESC";
    $list = $this->mainModel->getList($filters, $order);
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
    $this->_view->register_number = count($list);
    $this->_view->pages = $this->pages;
    $this->_view->totalpages = ceil(count($list) / $amount);
    $this->_view->page = $page;
    $this->_view->lists = $this->mainModel->getListPages($filters, $order, $start, $amount);
    $this->_view->csrf_section = $this->_csrf_section;

    // Enriquecimiento liviano para columnas de tabla: máximo 2 queries por fila
    try {
      $mesasModelIdx     = new Administracion_Model_DbTable_Mesas();
      $ambientesModelIdx = new Administracion_Model_DbTable_Ambientes();
    } catch (Exception $e) {
      $mesasModelIdx     = null;
      $ambientesModelIdx = null;
    }
    foreach ($this->_view->lists as &$reserva) {
      $reserva->ambiente_nombre = '-';
      $reserva->mesa_nombre     = '-';
      if (!empty($reserva->reserva_mesa_id) && $mesasModelIdx) {
        $mesaIds      = array_map('trim', explode(',', $reserva->reserva_mesa_id));
        $firstMesaId  = isset($mesaIds[0]) ? $mesaIds[0] : null;
        if ($firstMesaId && is_numeric($firstMesaId)) {
          $mesa = $mesasModelIdx->getById($firstMesaId);
          if ($mesa) {
            $reserva->mesa_nombre = $mesa->mesa_nombre ?? '-';
            if (!empty($mesa->mesa_ambiente) && $ambientesModelIdx) {
              $amb = $ambientesModelIdx->getById($mesa->mesa_ambiente);
              if ($amb) {
                $reserva->ambiente_nombre = $amb->ambiente_nombre ?? '-';
              }
            }
          }
        }
      }
    }
    unset($reserva);
  }

  /**
   * Retorna en JSON los detalles completos de una reserva para carga por AJAX.
   */
  public function detallesreservaAction()
  {
    header('Content-Type: application/json');
    $id = $this->_getSanitizedParam('id');
    if (!$id || !is_numeric($id)) {
      echo json_encode(['error' => 'ID inválido']);
      exit;
    }
    $detalles = $this->obtenerDetallesReserva((int)$id);
    if (!$detalles) {
      echo json_encode(['error' => 'Reserva no encontrada']);
      exit;
    }
    $detalles->estado_info = $this->getEstadoReserva($detalles->reserva_estado);
    echo json_encode($detalles);
    exit;
  }

  /**
   * Retorna en JSON el historial de cambios de tipo (Socio / Cosocio / Invitado)
   * de los invitados de una reserva.
   *
   * Se lee de `reservas_auditoria` (columna reserva_id indexada) en vez de
   * buscar con LIKE sobre el contenido JSON de la tabla `log`, que obliga a un
   * recorrido completo de la tabla y a parsear cada JSON guardado.
   * Administracion_newinvitadosController::registrarCambioTipoInvitado() es
   * quien escribe estos registros al momento de guardar el cambio.
   */
  public function historialtiposAction()
  {
    header('Content-Type: application/json');
    $id = $this->_getSanitizedParam('id');
    if (!$id || !is_numeric($id)) {
      echo json_encode(['error' => 'ID inválido']);
      exit;
    }
    $reservaId = (int) $id;

    $auditoriaModel = new Administracion_Model_DbTable_Reservasauditoria();
    $registros = $auditoriaModel->getList(
      "reserva_id = '{$reservaId}' AND accion = 'CAMBIO_TIPO_INVITADO'",
      'fecha_creacion DESC'
    );

    $historial = array();
    if ($registros) {
      foreach ($registros as $registro) {
        $detalle = json_decode($registro->datos_json, true);
        $historial[] = array(
          'fecha' => $registro->fecha_creacion,
          'usuario' => $registro->usuario_sistema,
          'invitado' => (is_array($detalle) && !empty($detalle['invitado_nombre'])) ? $detalle['invitado_nombre'] : 'N/D',
          'documento' => $registro->documento_socio,
          'estado_anterior' => $registro->estado_anterior,
          'estado_nuevo' => $registro->estado_nuevo,
        );
      }
    }

    echo json_encode(['historial' => $historial]);
    exit;
  }

  /**
   * Genera la Informacion necesaria para editar o crear un  reservas  y muestra su formulario
   *
   * @return void.
   */
  public function manageAction()
  {
    $this->_view->route = $this->route;
    $this->_csrf_section = "manage_reservas_" . date("YmdHis");
    $this->_csrf->generateCode($this->_csrf_section);
    $this->_view->csrf_section = $this->_csrf_section;
    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
    $id = $this->_getSanitizedParam("id");
    if ($id > 0) {
      $content = $this->mainModel->getById($id);
      if ($content->id) {
        $this->_view->content = $content;
        $this->_view->routeform = $this->route . "/update";
        $title = "Actualizar reservas";
        $this->getLayout()->setTitle($title);
        $this->_view->titlesection = $title;
      } else {
        $this->_view->routeform = $this->route . "/insert";
        $title = "Crear reservas";
        $this->getLayout()->setTitle($title);
        $this->_view->titlesection = $title;
      }
    } else {
      $this->_view->routeform = $this->route . "/insert";
      $title = "Crear reservas";
      $this->getLayout()->setTitle($title);
      $this->_view->titlesection = $title;
    }
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $this->_view->socio = $invitadosModel->getList("reserva_id_reserva = '$id'", "");
  }


  public function listadofacturacionAction()
  {
    $title = "Listado para facturación";
    $this->getLayout()->setTitle($title);
    $this->_view->titlesection = $title;

    // Procesar filtros incluyendo fechas
    $this->filtersListadoFacturacion();
    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
    $filters = Session::getInstance()->get($this->namefilter . '_facturacion');
    if (!$filters) {
      $filters = new stdClass();
    } else {
      $filters = (object) $filters;
    }
    $this->_view->filters = $filters;
    $this->_view->csrf_section = $this->_csrf_section;

    // Obtener filtros con fechas aplicados
    $filtrosConFechas = $this->getFilterListadoFacturacion();

    // Obtener todas las reservas confirmadas (estados 2 y 3) con filtros aplicados
    $reservas = $this->mainModel->getList($filtrosConFechas, "id DESC");
    foreach ($reservas as $reserva) {
      // Cargar todos los invitados de la reserva (no solo los principales)
      $reserva->invitados = $this->InvitadosReservas($reserva->id);
      // Cargar categorías y datos de ambiente/mesa para cálculo de precios
      $detalles = $this->obtenerDetallesReserva($reserva->id);
      // Si obtenerDetallesReserva trae categorías, asígnalas
      if ($detalles && ($detalles->categorias)) {
        $reserva->categorias = $detalles->categorias;
      } else {
        $reserva->categorias = [];
      }
      // Asignar información de mesas enriquecidas con ambiente_nombre
      if ($detalles && isset($detalles->mesas)) {
        $reserva->mesas = $detalles->mesas;
        $ambientesDetalles = isset($detalles->ambientes) ? $detalles->ambientes : [];
        foreach ($reserva->mesas as $mesa) {
          foreach ($ambientesDetalles as $amb) {
            if ($amb->ambiente_id == $mesa->mesa_ambiente) {
              $mesa->ambiente_nombre = $amb->ambiente_nombre;
              break;
            }
          }
        }
      } else {
        $reserva->mesas = [];
      }
    }
    $this->_view->content = $reservas;

    $ambientesModel = new Administracion_Model_DbTable_Ambientes();
    $this->_view->ambientes = $ambientesModel->getList("ambiente_estado = '1'", "ambiente_nombre ASC");

    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $ambienteIdFiltro = isset($filters->ambiente_id) && !empty($filters->ambiente_id) ? intval($filters->ambiente_id) : null;
    if ($ambienteIdFiltro) {
      $this->_view->mesas_select = $mesasModel->getList("mesa_ambiente = '$ambienteIdFiltro'", "mesa_nombre ASC");
    } else {
      $this->_view->mesas_select = $mesasModel->getList("1=1", "mesa_nombre ASC");
    }
  }
  /**
   * Inserta la informacion de un reservas  y redirecciona al listado de reservas.
   *
   * @return void.
   */


  /**
   * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Reservas.
   *
   * @return array con toda la informacion recibida del formulario.
   */
  private function getData()
  {
    $data = array();
    if ($this->_getSanitizedParam("reserva_id_evento") == '') {
      $data['reserva_id_evento'] = '0';
    } else {
      $data['reserva_id_evento'] = $this->_getSanitizedParam("reserva_id_evento");
    }
    $data['reserva_nombre_cliente'] = $this->_getSanitizedParam("reserva_nombre_cliente");
    $data['reserva_fecha'] = $this->_getSanitizedParam("reserva_fecha");
    $data['reserva_hora'] = $this->_getSanitizedParam("reserva_hora");
    if ($this->_getSanitizedParam("reserva_total_personas") == '') {
      $data['reserva_total_personas'] = '0';
    } else {
      $data['reserva_total_personas'] = $this->_getSanitizedParam("reserva_total_personas");
    }
    $data['reserva_telefono'] = $this->_getSanitizedParam("reserva_telefono");
    $data['reserva_correo'] = $this->_getSanitizedParam("reserva_correo");
    $data['reserva_comentario'] = $this->_getSanitizedParamHtml("reserva_comentario");
    $data['reserva_fecha_creacion'] = $this->_getSanitizedParam("reserva_fecha_creacion");
    $data['reserva_fecha_actualizacion'] = $this->_getSanitizedParam("reserva_fecha_actualizacion");
    $data['reserva_usuario_creacion'] = $this->_getSanitizedParam("reserva_usuario_creacion");
    $data['reserva_usuario_actualizacion'] = $this->_getSanitizedParam("reserva_usuario_actualizacion");
    $data['reserva_fecha_inicio_reserva'] = $this->_getSanitizedParam("reserva_fecha_inicio_reserva");
    $data['reserva_fecha_cierre_reserva'] = $this->_getSanitizedParam("reserva_fecha_cierre_reserva");
    $data['reserva_fecha_limite_pago'] = $this->_getSanitizedParam("reserva_fecha_limite_pago");
    return $data;
  }
  /**
   * Genera la consulta con los filtros de este controlador.
   *
   * @return array cadena con los filtros que se van a asignar a la base de datos
   */
  protected function getFilter()
  {
    $filtros = " 1 = 1 AND reserva_estado IN ( 2, 3, 11)";
    if (Session::getInstance()->get($this->namefilter) != "") {
      $filters = (object) Session::getInstance()->get($this->namefilter);
      if ($filters->id != '') {
        $filtros = $filtros . " AND id = '" . $filters->id . "'";
      }
      if ($filters->reserva_id_evento != '') {
        $filtros = $filtros . " AND reserva_id_evento LIKE '%" . $filters->reserva_id_evento . "%'";
      }
      if ($filters->reserva_nombre_cliente != '') {
        $filtros = $filtros . " AND (reserva_nombre_cliente LIKE '%" . $filters->reserva_nombre_cliente . "%' OR reserva_apellido_cliente LIKE '%" . $filters->reserva_nombre_cliente . "%')";
      }
      if ($filters->reserva_total_personas != '') {
        $filtros = $filtros . " AND reserva_total_personas LIKE '%" . $filters->reserva_total_personas . "%'";
      }
      if ($filters->reserva_telefono != '') {
        $filtros = $filtros . " AND reserva_telefono LIKE '%" . $filters->reserva_telefono . "%'";
      }

      if ($filters->reserva_documento != '') {
        $filtros = $filtros . " AND reserva_documento LIKE '%" . $filters->reserva_documento . "%'";
      }
      if ($filters->reserva_numero_carnet != '') {
        $filtros = $filtros . " AND reserva_numero_carnet LIKE '%" . $filters->reserva_numero_carnet . "%'";
      }
      if (!empty($filters->mesa_codigo)) {
        $mesasModel = new Administracion_Model_DbTable_Mesas();
        $mesasPorCodigo = $mesasModel->getList("mesa_codigo = '" . addslashes($filters->mesa_codigo) . "'");
        if (!empty($mesasPorCodigo)) {
          $filtros .= " AND FIND_IN_SET('" . intval($mesasPorCodigo[0]->mesa_id) . "', reserva_mesa_id)";
        } else {
          $filtros .= " AND 1 = 0";
        }
      } elseif (!empty($filters->ambiente_id)) {
        $mesasModel = new Administracion_Model_DbTable_Mesas();
        $mesasAmbiente = $mesasModel->getList("mesa_ambiente = '" . intval($filters->ambiente_id) . "'");
        if (!empty($mesasAmbiente)) {
          $findInSets = [];
          foreach ($mesasAmbiente as $mesa) {
            $findInSets[] = "FIND_IN_SET('" . intval($mesa->mesa_id) . "', reserva_mesa_id)";
          }
          $filtros .= " AND (" . implode(' OR ', $findInSets) . ")";
        } else {
          $filtros .= " AND 1 = 0";
        }
      }
    }
    return $filtros;
  }

  /**
   * Recibe y asigna los filtros específicos para el listado de facturación
   *
   * @return void
   */
  protected function filtersListadoFacturacion()
  {
    if ($this->getRequest()->isPost() == true) {
      $parramsfilter = array();
      $parramsfilter['id'] = $this->_getSanitizedParam("id");
      $parramsfilter['reserva_id_evento'] = $this->_getSanitizedParam("reserva_id_evento");
      $parramsfilter['reserva_nombre_cliente'] = $this->_getSanitizedParam("reserva_nombre_cliente");
      $parramsfilter['reserva_total_personas'] = $this->_getSanitizedParam("reserva_total_personas");
      $parramsfilter['reserva_telefono'] = $this->_getSanitizedParam("reserva_telefono");
      $parramsfilter['reserva_numero_carnet'] = $this->_getSanitizedParam("reserva_numero_carnet");
      $parramsfilter['reserva_documento'] = $this->_getSanitizedParam("reserva_documento");
      $parramsfilter['fecha_inicio'] = $this->_getSanitizedParam("fecha_inicio");
      $parramsfilter['fecha_fin'] = $this->_getSanitizedParam("fecha_fin");
      $parramsfilter['ambiente_id'] = $this->_getSanitizedParam("ambiente_id");
      $parramsfilter['mesa_codigo'] = $this->_getSanitizedParam("mesa_codigo");

      Session::getInstance()->set($this->namefilter . '_facturacion', $parramsfilter);
    }
    if ($this->_getSanitizedParam("cleanfilter") == 1) {
      Session::getInstance()->set($this->namefilter . '_facturacion', '');
    }
  }

  /**
   * Genera la consulta con los filtros específicos para listado de facturación
   *
   * @return string cadena con los filtros que se van a asignar a la base de datos
   */
  protected function getFilterListadoFacturacion()
  {
    $filtros = " 1 = 1 AND reserva_estado IN (2, 3, 11)";
    if (Session::getInstance()->get($this->namefilter . '_facturacion') != "") {
      $filters = (object) Session::getInstance()->get($this->namefilter . '_facturacion');
      if ($filters->id != '') {
        $filtros = $filtros . " AND id = '" . $filters->id . "'";
      }
      if ($filters->reserva_id_evento != '') {
        $filtros = $filtros . " AND reserva_id_evento LIKE '%" . $filters->reserva_id_evento . "%'";
      }
      if ($filters->reserva_nombre_cliente != '') {
        $filtros = $filtros . " AND (reserva_nombre_cliente LIKE '%" . $filters->reserva_nombre_cliente . "%' OR reserva_apellido_cliente LIKE '%" . $filters->reserva_nombre_cliente . "%')";
      }
      if ($filters->reserva_total_personas != '') {
        $filtros = $filtros . " AND reserva_total_personas LIKE '%" . $filters->reserva_total_personas . "%'";
      }
      if ($filters->reserva_telefono != '') {
        $filtros = $filtros . " AND reserva_telefono LIKE '%" . $filters->reserva_telefono . "%'";
      }
      if ($filters->reserva_documento != '') {
        $filtros = $filtros . " AND reserva_documento LIKE '%" . $filters->reserva_documento . "%'";
      }
      if ($filters->reserva_numero_carnet != '') {
        $filtros = $filtros . " AND reserva_numero_carnet LIKE '%" . $filters->reserva_numero_carnet . "%'";
      }
      // Filtros de fecha
      if ($filters->fecha_inicio != '') {
        $filtros = $filtros . " AND reserva_fecha >= '" . $filters->fecha_inicio . "'";
      }
      if ($filters->fecha_fin != '') {
        $filtros = $filtros . " AND reserva_fecha <= '" . $filters->fecha_fin . "'";
      }
      if (!empty($filters->mesa_codigo)) {
        $mesasModel = new Administracion_Model_DbTable_Mesas();
        $mesasPorCodigo = $mesasModel->getList("mesa_codigo = '" . addslashes($filters->mesa_codigo) . "'");
        if (!empty($mesasPorCodigo)) {
          $filtros .= " AND FIND_IN_SET('" . intval($mesasPorCodigo[0]->mesa_id) . "', reserva_mesa_id)";
        } else {
          $filtros .= " AND 1 = 0";
        }
      } elseif (!empty($filters->ambiente_id)) {
        $mesasModel = new Administracion_Model_DbTable_Mesas();
        $mesasAmbiente = $mesasModel->getList("mesa_ambiente = '" . intval($filters->ambiente_id) . "'");
        if (!empty($mesasAmbiente)) {
          $findInSets = [];
          foreach ($mesasAmbiente as $mesa) {
            $findInSets[] = "FIND_IN_SET('" . intval($mesa->mesa_id) . "', reserva_mesa_id)";
          }
          $filtros .= " AND (" . implode(' OR ', $findInSets) . ")";
        } else {
          $filtros .= " AND 1 = 0";
        }
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
      $parramsfilter['id'] = $this->_getSanitizedParam("id");
      $parramsfilter['reserva_id_evento'] = $this->_getSanitizedParam("reserva_id_evento");
      $parramsfilter['reserva_nombre_cliente'] = $this->_getSanitizedParam("reserva_nombre_cliente");
      $parramsfilter['reserva_total_personas'] = $this->_getSanitizedParam("reserva_total_personas");
      $parramsfilter['reserva_telefono'] = $this->_getSanitizedParam("reserva_telefono");
      $parramsfilter['reserva_numero_carnet'] = $this->_getSanitizedParam("reserva_numero_carnet");
      $parramsfilter['reserva_documento'] = $this->_getSanitizedParam("reserva_documento");
      $parramsfilter['ambiente_id'] = $this->_getSanitizedParam("ambiente_id");
      $parramsfilter['mesa_codigo'] = $this->_getSanitizedParam("mesa_codigo");

      Session::getInstance()->set($this->namefilter, $parramsfilter);
    }
    if ($this->_getSanitizedParam("cleanfilter") == 1) {
      Session::getInstance()->set($this->namefilter, '');
      Session::getInstance()->set($this->namepageactual, 1);
    }
  }

  public function exportarAction()
  {
    $reservasConfirmadas = $this->mainModel->getList("reserva_estado IN (2,3,11)", "id DESC");
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    foreach ($reservasConfirmadas as $reserva) {
      $reserva->mesas = !empty($reserva->reserva_mesa_id)
        ? $mesasModel->getMesasConDetalles("mesa_id IN ($reserva->reserva_mesa_id)")
        : [];
      $reserva->invitados = $this->InvitadosReservasInfo($reserva->id);
    }

    $this->_view->content = $reservasConfirmadas;

    $this->setLayout('blanco');
    $hoy = date("YmdHis");
    $excel = $this->_getSanitizedParam("excel");

    if ($excel == 1) {
      header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
      header("Content-type:   application/x-msexcel; charset=utf-8");
      header("Content-Disposition: attachment; filename=invitados_prod" . $hoy . ".xls");
    }
  }
  /**
   * Exporta la tabla de listadofacturacion a Excel
   */
  public function exportarlistadofacturacionAction()
  {
    // Procesar filtros incluyendo fechas
    $this->filtersListadoFacturacion();

    // Obtener filtros con fechas aplicados
    $filtrosConFechas = $this->getFilterListadoFacturacion();

    // Obtener reservas con filtros aplicados
    $reservas = $this->mainModel->getList($filtrosConFechas, "id DESC");
    foreach ($reservas as $reserva) {
      $reserva->invitados = $this->InvitadosReservas($reserva->id);
      $detalles = $this->obtenerDetallesReserva($reserva->id);
      if ($detalles && ($detalles->categorias)) {
        $reserva->categorias = $detalles->categorias;
      } else {
        $reserva->categorias = [];
      }
      // Asignar información de mesas enriquecidas con ambiente_nombre
      if ($detalles && isset($detalles->mesas)) {
        $reserva->mesas = $detalles->mesas;
        $ambientesDetalles = isset($detalles->ambientes) ? $detalles->ambientes : [];
        foreach ($reserva->mesas as $mesa) {
          foreach ($ambientesDetalles as $amb) {
            if ($amb->ambiente_id == $mesa->mesa_ambiente) {
              $mesa->ambiente_nombre = $amb->ambiente_nombre;
              break;
            }
          }
        }
      } else {
        $reserva->mesas = [];
      }
    }
    $this->_view->content = $reservas;
    $this->_view->titlesection = "Exportar Listado para Facturación";
    $this->setLayout('blanco');
    $hoy = date("YmdHis");
    $excel = $this->_getSanitizedParam("excel");
    if ($excel == 1) {
      header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
      header("Content-type:   application/x-msexcel; charset=utf-8");
      header("Content-Disposition: attachment; filename=listadofacturacion_" . $hoy . ".xls");
    }
  }
  public function InvitadosReservasInfo($reservaId)
  {
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    return $invitadosModel->getList("reserva_id_reserva = '$reservaId' AND invitadoReserva_beneficiario_principal != 1 AND (documento_invitado <> '' )", "");
  }

  public function exportarfaltantesAction()
  {

    $reservasConfirmadas = $this->mainModel->getList("reserva_estado IN (2,3,11)", "id DESC");
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    foreach ($reservasConfirmadas as $reserva) {
      $reserva->mesas = !empty($reserva->reserva_mesa_id)
        ? $mesasModel->getMesasConDetalles("mesa_id IN ($reserva->reserva_mesa_id)")
        : [];
      $reserva->invitados = $this->InvitadosReservasFaltantes($reserva->id);
    }

    $this->_view->content = $reservasConfirmadas;

    $this->setLayout('blanco');
    $hoy = date("YmdHis");
    $excel = $this->_getSanitizedParam("excel");

    if ($excel == 1) {
      header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
      header("Content-type:   application/x-msexcel; charset=utf-8");
      header("Content-Disposition: attachment; filename=reservas_faltantes" . $hoy . ".xls");
    }
  }
  public function exportarinvitadosAction()
  {
    $order = "orden ASC";
    $filters = $this->getFilter();
    $reservasConfirmadas = $this->mainModel->getList("reserva_estado IN (2,3,11)", "id DESC");
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    foreach ($reservasConfirmadas as $reserva) {
      $reserva->mesas = !empty($reserva->reserva_mesa_id)
        ? $mesasModel->getMesasConDetalles("mesa_id IN ($reserva->reserva_mesa_id)")
        : [];
      $reserva->invitados = $this->InvitadosReservas($reserva->id);
    }

    $this->_view->content = $reservasConfirmadas;

    $this->setLayout('blanco');
    $hoy = date("YmdHis");
    $excel = $this->_getSanitizedParam("excel");

    if ($excel == 1) {
      header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
      header("Content-type:   application/x-msexcel; charset=utf-8");
      header("Content-Disposition: attachment; filename=reservas_invitados" . $hoy . ".xls");
    }
  }
  public function InvitadosReservasFaltantes($reservaId)
  {

    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    return $invitadosModel->getList("reserva_id_reserva = '$reservaId' AND invitadoReserva_beneficiario_principal != 1 AND (documento_invitado = '' OR documento_invitado IS NULL) AND invitadoReserva_nombre_invitado LIKE '%Invitado%'", "");
  }

  public function InvitadosReservasinv($reservaId)
  {
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    return $invitadosModel->getList("reserva_id_reserva = '$reservaId' AND invitadoReserva_estado_invitado != 'P' AND invitado_tipo = '1'", "");
  }
  public function exportarinvitadosinvAction()
  {

    $reservasConfirmadas = $this->mainModel->getList("reserva_estado IN (2,3,11)", "id DESC");
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    foreach ($reservasConfirmadas as $reserva) {
      $reserva->mesas = !empty($reserva->reserva_mesa_id)
        ? $mesasModel->getMesasConDetalles("mesa_id IN ($reserva->reserva_mesa_id)")
        : [];
      $reserva->invitados = $this->InvitadosReservasinv($reserva->id);
    }

    $this->_view->content = $reservasConfirmadas;

    $this->setLayout('blanco');
    $hoy = date("YmdHis");
    $excel = $this->_getSanitizedParam("excel");

    if ($excel == 1) {
      header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
      header("Content-type:   application/x-msexcel; charset=utf-8");
      header("Content-Disposition: attachment; filename=reservas_principales" . $hoy . ".xls");
    }
  }

  public function exportarreservasAction()
  {
    $order = "orden ASC";
    $filters = $this->getFilter();
    $reservasConfirmadas = $this->mainModel->getList("reserva_estado IN (2,3,11)", "id DESC");
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    foreach ($reservasConfirmadas as $reserva) {
      $reserva->mesas = !empty($reserva->reserva_mesa_id)
        ? $mesasModel->getMesasConDetalles("mesa_id IN ($reserva->reserva_mesa_id)")
        : [];
      $reserva->invitados = $this->InvitadosReservas($reserva->id);
    }
    // echo "<pre>";
    // print_r($reservasConfirmadas);
    // echo "</pre>";

    $this->_view->content = $reservasConfirmadas;

    $this->setLayout('blanco');
    $hoy = date("YmdHis");
    $excel = $this->_getSanitizedParam("excel");

    if ($excel == 1) {
      header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
      header("Content-type:   application/x-msexcel; charset=utf-8");
      header("Content-Disposition: attachment; filename=reservas_listado" . $hoy . ".xls");
    }
  }
  public function exportarreservaslistAction()
  {
    $order = "orden ASC";
    $filters = $this->getFilter();
    $reservasConfirmadas = $this->mainModel->getList("reserva_estado IN (2,3,11)", "id DESC");
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    foreach ($reservasConfirmadas as $reserva) {
      $reserva->mesas = !empty($reserva->reserva_mesa_id)
        ? $mesasModel->getMesasConDetalles("mesa_id IN ($reserva->reserva_mesa_id)")
        : [];
      $reserva->invitados = $this->InvitadosReservas($reserva->id);
    }
    // echo "<pre>";
    // print_r($reservasConfirmadas);
    // echo "</pre>";

    $this->_view->content = $reservasConfirmadas;

    $this->setLayout('blanco');
    $hoy = date("YmdHis");
    $excel = $this->_getSanitizedParam("excel");

    if ($excel == 1) {
      header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
      header("Content-type:   application/x-msexcel; charset=utf-8");
      header("Content-Disposition: attachment; filename=reservas_listado" . $hoy . ".xls");
    }
  }
  /**
   * Obtiene la lista de eventos disponibles
   * @return array Array con los eventos indexados por ID
   */
  public function getEventos()
  {
    $data = array();
    try {
      $eventosModel = new Administracion_Model_DbTable_Eventos();
      $eventos = $eventosModel->getList("", "evento_id ASC");
      foreach ($eventos as $evento) {
        $data[$evento->evento_id] = $evento;
      }

      // Asegurar que siempre esté disponible el evento principal (ID 1) si existe
      if (empty($data) && !isset($data[1])) {
        $eventoDefault = $eventosModel->getById(1);
        if ($eventoDefault) {
          $data[1] = $eventoDefault;
        }
      }
    } catch (Exception $e) {
      // En caso de error, crear un evento por defecto
      $data[1] = (object) array(
        'evento_id' => 1,
        'evento_titulo' => 'Evento Principal',
        'evento_descripcion' => 'Evento por defecto'
      );
    }

    return $data;
  }

  /**
   * Obtiene los detalles completos de una reserva incluyendo mesas, invitados, piso, ambiente, etc.
   * @param int $reservaId ID de la reserva
   * @return object|null Objeto con todos los detalles de la reserva o null si no existe
   */
  public function obtenerDetallesReserva($reservaId)
  {
    // Obtener información básica de la reserva
    $reserva = $this->mainModel->getById($reservaId);
    if (!$reserva) {
      return null;
    }

    // Obtener información del evento
    if (!empty($reserva->reserva_id_evento)) {
      try {
        $eventosModel = new Administracion_Model_DbTable_Eventos();
        $evento = $eventosModel->getById($reserva->reserva_id_evento);
        $reserva->evento = $evento;
      } catch (Exception $e) {
        $reserva->evento = null;
      }
    }

    // Obtener invitados de la reserva
    try {
      $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
      $invitados = $invitadosModel->getList("reserva_id_reserva = '$reservaId'", "");
      $reserva->invitados = $invitados;
    } catch (Exception $e) {
      $reserva->invitados = array();
    }

    // Obtener información de las mesas si están asignadas
    if (!empty($reserva->reserva_mesa_id)) {
      try {
        $mesasModel = new Administracion_Model_DbTable_Mesas();
        $ambientesModel = new Administracion_Model_DbTable_Ambientes();
        $pisosModel = new Administracion_Model_DbTable_Pisos();
        $categoriasModel = new Administracion_Model_DbTable_Categorias();

        // Separar los IDs de mesas (pueden ser múltiples separados por coma)
        $mesaIds = array_map('trim', explode(',', $reserva->reserva_mesa_id));
        $mesas = array();
        $ambientes = array();
        $pisos = array();
        $categorias = array();
        $categoria_principal = null;

        foreach ($mesaIds as $mesaId) {
          if (!empty($mesaId) && is_numeric($mesaId)) {
            $mesa = $mesasModel->getById($mesaId);
            if ($mesa) {
              $mesas[] = $mesa;

              // Obtener información del ambiente
              if (!empty($mesa->mesa_ambiente)) {
                $ambiente = $ambientesModel->getById($mesa->mesa_ambiente);
                if ($ambiente) {
                  $ambientes[$ambiente->ambiente_id] = $ambiente;

                  // Obtener información del piso
                  if (!empty($ambiente->ambiente_piso)) {
                    $piso = $pisosModel->getById($ambiente->ambiente_piso);
                    if ($piso) {
                      $pisos[$piso->piso_id] = $piso;
                    }
                  }

                  // Obtener información de la categoría
                  if (!empty($ambiente->ambiente_categoria)) {
                    $categoria = $categoriasModel->getById($ambiente->ambiente_categoria);
                    if ($categoria) {
                      $categorias[$categoria->categoria_id] = $categoria;
                      // Usar la primera categoría encontrada para cálculos
                      if ($categoria_principal === null) {
                        $categoria_principal = $categoria;
                      }
                    }
                  }
                }
              }
            }
          }
        }

        // Asignar los datos obtenidos
        $reserva->mesas = $mesas;
        $reserva->ambientes = array_values($ambientes);
        $reserva->pisos = array_values($pisos);
        $reserva->categorias = array_values($categorias);

        // Para compatibilidad, asignar el primer elemento encontrado
        $reserva->mesa = !empty($mesas) ? $mesas[0] : null;
        $reserva->ambiente = !empty($ambientes) ? array_values($ambientes)[0] : null;
        $reserva->piso = !empty($pisos) ? array_values($pisos)[0] : null;
        $reserva->categoria = $categoria_principal;

        // Calcular precios individuales y total
        if ($categoria_principal && !empty($reserva->invitados)) {
          foreach ($reserva->invitados as $invitado) {
            $invitado->precio_boleta = $this->calcularPrecioBoleta($invitado, $categoria_principal);
          }
        }
      } catch (Exception $e) {
        $reserva->mesas = array();
        $reserva->ambientes = array();
        $reserva->pisos = array();
        $reserva->categorias = array();
        $reserva->mesa = null;
        $reserva->ambiente = null;
        $reserva->piso = null;
        $reserva->categoria = null;
      }
    } else {
      // Si no hay mesa asignada pero hay invitados, intentar calcular con precio base
      if (!empty($reserva->invitados)) {
        foreach ($reserva->invitados as $invitado) {
          $invitado->precio_boleta = 0; // Sin categoría, precio 0
        }
        $reserva->total_reserva = 0;
      }
    }

    return $reserva;
  }

  /**
   * Obtiene los invitados de una reserva
   * @param int $reservaId ID de la reserva
   * @return array Array con los invitados
   */
  public function InvitadosReservas($reservaId)
  {
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    return $invitadosModel->getList("reserva_id_reserva = '$reservaId'", "");
  }

  /**
   * Obtiene el texto y badge del estado de la reserva
   * @param string $estado Código del estado de la reserva
   * @return array Array con 'texto' y 'badge_class'
   */
  public function getEstadoReserva($estado)
  {
    $estados = array(
      '1' => array('texto' => 'Reserva creada', 'badge_class' => 'badge-primary'),
      '2' => array('texto' => 'Reserva pagada por cargo a la acción', 'badge_class' => 'badge-success'),
      '3' => array('texto' => 'Reserva pago aprobado - PlaceToPay', 'badge_class' => 'badge-success'),
      '4' => array('texto' => 'Reserva pago pendiente - PlaceToPay', 'badge_class' => 'badge-warning'),
      '5' => array('texto' => 'Reserva pago fallido - PlaceToPay', 'badge_class' => 'badge-danger'),
      '6' => array('texto' => 'Reserva pago rechazado - PlaceToPay', 'badge_class' => 'badge-danger'),
      '7' => array('texto' => 'Reserva pago pendiente - Sistema', 'badge_class' => 'badge-info'),
      '8' => array('texto' => 'Reserva cancelada por inactividad', 'badge_class' => 'badge-secondary'),
      'C' => array('texto' => 'Reserva cancelada', 'badge_class' => 'badge-dark'),
      '11' => array('texto' => 'Reserva pagada en datafono', 'badge_class' => 'badge-success'),
    );

    if (isset($estados[$estado])) {
      return $estados[$estado];
    }

    // Estado por defecto si no se encuentra
    return array('texto' => 'Estado desconocido', 'badge_class' => 'badge-light');
  }
  public function exportarreservaslistbyambienteAction()
  {
    $ambiente = $this->_getSanitizedParam("ambiente");
    $ambienteModel = new Administracion_Model_DbTable_Ambientes();
    $ambienteInfo = $ambienteModel->getById($ambiente);
    $pisoModel = new Administracion_Model_DbTable_Pisos();
    $pisoInfo = $pisoModel->getById($ambienteInfo->ambiente_piso ?? 0);

    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $reservasConfirmadas = $this->mainModel->getList("reserva_estado IN (2,3,11)", "id DESC");

    $resultado = [];
    foreach ($reservasConfirmadas as $reserva) {
      if (empty($reserva->reserva_mesa_id)) continue;
      $mesas = $mesasModel->getMesasConDetalles(
        "mesa_id IN (" . $reserva->reserva_mesa_id . ") AND mesa_ambiente = '" . intval($ambiente) . "'"
      );
      if (empty($mesas)) continue; // reserva no pertenece a este ambiente
      $reserva->mesas = $mesas;
      $reserva->invitados = $this->InvitadosReservas($reserva->id);
      $resultado[] = $reserva;
    }

    $this->_view->content = $resultado;
    $this->_view->ambiente = $ambienteInfo;
    $this->_view->piso = $pisoInfo;

    $this->setLayout('blanco');
    $hoy = date("YmdHis");
    $excel = $this->_getSanitizedParam("excel");

    if ($excel == 1) {
      header("Content-Type: application/vnd.ms-excel; charset=utf-8");
      header("Content-type: application/x-msexcel; charset=utf-8");
      header("Content-Disposition: attachment; filename=invitados_" . ($ambienteInfo->ambiente_nombre ?? 'ambiente') . "_" . ($pisoInfo->piso_nombre ?? '') . "_" . $hoy . ".xls");
    }
  }

  /**
   * Calcula el precio de una boleta según el tipo de invitado y categoría
   * @param object $invitado Objeto del invitado
   * @param object $categoria Objeto de la categoría
   * @return float Precio calculado
   */
  public function calcularPrecioBoleta($invitado, $categoria)
  {
    if (!$categoria) {
      return 0;
    }

    // Si el estado del invitado es 'P' (invitado pendiente), usar precio de invitado
    if ($invitado->invitadoReserva_estado_invitado == 'P') {
      return floatval($categoria->categoria_precio_invitado ?? 0);
    }

    // Si es de tipo 'S' (Socio) o 'A' (Asociado), usar precio de socio
    if ($invitado->invitado_tipo == 'S' || $invitado->invitado_tipo == 'A') {
      // Si es hijo del socio y tiene cupo, usar precio especial de hijo
      if ($invitado->invitadoReserva_beneficiario_hijo == 1 && $invitado->invitadoReserva_beneficiario_cupo == 1) {
        return floatval($categoria->categoria_precio_socio_hijo ?? 0);
      }
      return floatval($categoria->categoria_precio_socio ?? 0);
    }

    // Si es tipo 'H' (Hijo), usar precio de hijo de socio
    if ($invitado->invitado_tipo == 'H') {
      return floatval($categoria->categoria_precio_socio_hijo ?? 0);
    }

    // Por defecto, usar precio general
    return floatval($categoria->categoria_precio ?? 0);
  }



  public function exportarnofinalizadasAction()
  {
    $order = "orden ASC";
    $filters = $this->getFilter();
    $intentosReservasSocio = $this->mainModel->getIntentos();
    // echo "<pre>";
    // print_r($intentosReservasSocio);
    // echo "</pre>";
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $socioSinReserva = [];
    foreach ($intentosReservasSocio as $reserva) {

      $invitadosReservas = $invitadosModel->getList("documento_invitado = '$reserva->reserva_documento' ", "");
      $reserva->invitados = $invitadosReservas;
      foreach ($invitadosReservas as $invitado) {
        $idReserva = $invitado->reserva_id_reserva;
        $reservaInfo = $this->mainModel->getById($idReserva);
        $invitado->reservaInfo = $reservaInfo;
        if ($reservaInfo && ($reservaInfo->reserva_estado == 2 || $reservaInfo->reserva_estado == 3 || $reservaInfo->reserva_estado == 11)) {
          // Si el socio ya tiene una reserva confirmada, saltar al siguiente
          continue 2;
        }
      }
      $socioSinReserva[] = $reserva;
    }
    // echo "<pre>";
    // print_r($socioSinReserva);
    // echo "</pre>";

    $this->_view->content = $socioSinReserva;

    $this->setLayout('blanco');
    $hoy = date("YmdHis");
    $excel = $this->_getSanitizedParam("excel");

    if ($excel == 1) {
      header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
      header("Content-type:   application/x-msexcel; charset=utf-8");
      header("Content-Disposition: attachment; filename=nofinalizadas_socio" . $hoy . ".xls");
    }
  }
  public function reservarAction()
  {
    $title = "Crear Nueva Reserva Manual";
    $this->getLayout()->setTitle($title);
    $this->_view->titlesection = $title;

    $this->_csrf_section = "admin_reservar_" . date("YmdHis");
    $this->_csrf->generateCode($this->_csrf_section);
    $this->_view->csrf_section = $this->_csrf_section;
    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];

    // Cargar eventos disponibles
    $eventosModel = new Administracion_Model_DbTable_Eventos();
    $this->_view->evento = $evento = $eventosModel->getById(1); // Evento principal

    // Obtener mesas disponibles
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $this->_view->mesasDisponibles = $mesasModel->getListMesasDisponibles();

    // Inicializar datos de sesión para el flujo
    Session::getInstance()->set('admin_reserva_data', null);
  }

  /**
   * Busca un socio por documento o número de carnet
   */
  public function buscarsocioAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $ncar = $this->_getSanitizedParam('ncar');

    if (!$ncar) {
      echo json_encode(['success' => false, 'message' => 'Debe proporcionar número de carnet']);
      return;
    }

    // Buscar en el servicio web
    $loginServiceUrl = URL_BASE . '/querys/buscarUsuario.php';

    $postData = http_build_query([
      'token' => $this->generarToken(),
      'ncar' => $ncar ?: '',
    ]);

    $ch = curl_init($loginServiceUrl);
    curl_setopt_array($ch, [
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $postData,
      CURLOPT_RETURNTRANSFER => true
    ]);

    $raw = curl_exec($ch);
    if (curl_errno($ch)) {
      echo json_encode(['success' => false, 'message' => 'Error al consultar: ' . curl_error($ch)]);
      curl_close($ch);
      return;
    }
    curl_close($ch);

    $response = json_decode($raw);
    if (!$response) {
      echo json_encode(['success' => false, 'message' => 'Respuesta inválida del servicio']);
      return;
    }

    if (isset($response->mensaje) && $response->mensaje) {
      echo json_encode(['success' => false, 'message' => $response->mensaje]);
      return;
    }

    // Verificar si ya tiene reservas
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reservasSocio = $reservasModel->getList("reserva_documento = '{$response->SBE_CODI}'", "id DESC");

    $totalAceptadas = 0;
    $totalPendientes = 0;

    foreach ($reservasSocio as $reserva) {
      $estado = (int) $reserva->reserva_estado;
      // $personas = (int) $reserva->reserva_total_personas;

      if (in_array($estado, [2, 3, 11], true)) {
        $totalAceptadas++;
      }
      if (in_array($estado, [1, 4, 7], true)) {
        $totalPendientes++;
      }
    }
    $edad = $this->getAge($response->SBE_FNAC->date ?? null);
    $esMenor25 = $edad !== null && $edad < 25;
    $esHijo = $this->esHijo($response);
    $response->edad = $edad;
    $response->menor25 = $esMenor25;
    $response->hijo = $esHijo;
    // Agregar el ncar que se usó para buscar, ya que no viene en la respuesta del webservice
    $response->ncar = $ncar;


    echo json_encode([
      'success' => true,
      'data' => $response,
      'reservas' => [
        'aceptadas' => $totalAceptadas,
        'pendientes' => $totalPendientes
      ]
    ]);
  }

  /**
   * Carga beneficiarios del socio seleccionado
   */
  public function cargarbeneficiariosAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $mac_nume = $this->_getSanitizedParam('mac_nume');

    if (!$mac_nume) {
      echo json_encode(['success' => false, 'message' => 'MAC_NUME requerido']);
      return;
    }

    $loginServiceUrl = URL_BASE . '/querys/selectBeneficiarios.php';

    $postData = http_build_query([
      'token' => $this->generarToken(),
      'mac_nume' => $mac_nume
    ]);

    $ch = curl_init($loginServiceUrl);
    curl_setopt_array($ch, [
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $postData,
      CURLOPT_RETURNTRANSFER => true
    ]);

    $raw = curl_exec($ch);
    if (curl_errno($ch)) {
      echo json_encode(['success' => false, 'message' => 'Error cURL: ' . curl_error($ch)]);
      curl_close($ch);
      return;
    }
    curl_close($ch);

    $beneficiarios = json_decode($raw, true);

    if ($beneficiarios === null || isset($beneficiarios['mensaje'])) {
      echo json_encode(['success' => false, 'message' => 'No se encontraron beneficiarios']);
      return;
    }

    // Procesar beneficiarios (calcular edad, verificar si es hijo, etc.)
    $beneficiariosProcessed = [];
    foreach ($beneficiarios as $b) {
      $edad = $this->getAge($b['SBE_FNAC'] ?? null);
      $esMenor25 = $edad !== null && $edad < 25;
      $esHijo = $this->esHijo($b);

      $beneficiariosProcessed[] = [
        'documento' => $b['SBE_CODI'] ?? '',
        'nombre' => $b['SBE_NOMB'] ?? '',
        'apellido' => $b['SBE_APEL'] ?? '',
        'correo' => strtolower(trim($b['sbe_mail'] ?? '')),
        'fecha_nacimiento' => $b['SBE_FNAC']['date'] ?? '',
        'edad' => $edad,
        'menor25' => $esMenor25,
        'hijo' => $esHijo,
        'parentesco' => $b['parentesco'] ?? '',
        'bloqueado' => false
      ];
    }

    // Verificar cuáles documentos ya están en una reserva activa
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $documentos = array_column($beneficiariosProcessed, 'documento');
    $documentosBloqueados = $invitadosModel->getDocumentosEnReservasActivas($documentos);

    if (!empty($documentosBloqueados)) {
      foreach ($beneficiariosProcessed as &$b) {
        if (in_array($b['documento'], $documentosBloqueados)) {
          $b['bloqueado'] = true;
        }
      }
      unset($b);
    }

    echo json_encode([
      'success' => true,
      'data' => $beneficiariosProcessed
    ]);
  }

  /**
   * Guarda la selección de beneficiarios y cantidad
   */
  public function guardardatosreservaAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    if (!$_POST) {
      echo json_encode(['success' => false, 'message' => 'Método no permitido']);
      return;
    }

    $socio = json_decode($_POST['socio'] ?? '{}', true);
    $beneficiarios = json_decode($_POST['beneficiarios_seleccionados'] ?? '[]', true);
    $cantidadNoAsociados = intval($_POST['cantidad_no_asociados'] ?? 0);
    $capacidadesMesas = json_decode($_POST['capacidades_mesas'] ?? '[]', true);

    // Validar datos mínimos
    if (empty($socio) || empty($socio['SBE_CODI'])) {
      echo json_encode(['success' => false, 'message' => 'Datos del socio incompletos']);
      return;
    }

    // Guardar en sesión
    $reservaData = [
      'socio' => $socio,
      'beneficiarios_seleccionados' => $beneficiarios,
      'cantidad_no_asociados' => $cantidadNoAsociados,
      'capacidades_mesas' => $capacidadesMesas,
      'total_invitados' => count($beneficiarios) + $cantidadNoAsociados
    ];

    Session::getInstance()->set('admin_reserva_data', $reservaData);

    echo json_encode([
      'success' => true,
      'message' => 'Datos guardados correctamente',
      'total_personas' => $reservaData['total_invitados']
    ]);
  }

  /**
   * Muestra la página de informes con botones para descargar reportes en Excel
   *
   * @return void
   */
  public function informesAction()
  {
    $title = "Informes de reservas";
    $this->getLayout()->setTitle($title);
    $this->_view->titlesection = $title;
    $this->_view->route = $this->route;
    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
    $this->_view->csrf_section = $this->_csrf_section;
  }

  // Métodos helper (moverlos al final de la clase)
  private function getAge($fechaNacimiento)
  {
    if (!$fechaNacimiento) return null;

    try {
      if (is_array($fechaNacimiento) && isset($fechaNacimiento['date'])) {
        $fechaNacimiento = $fechaNacimiento['date'];
      }
      $birth = new DateTime($fechaNacimiento);
      return $birth->diff(new DateTime('today'))->y;
    } catch (Exception $e) {
      return null;
    }
  }

  private function esHijo($beneficiario)
  {
    $parentesco = '';
    if (is_array($beneficiario) && isset($beneficiario['parentesco'])) {
      $parentesco = $beneficiario['parentesco'];
    } elseif (is_object($beneficiario) && isset($beneficiario->parentesco)) {
      $parentesco = $beneficiario->parentesco;
    }

    return in_array(strtolower($parentesco), ['hijo', 'hija', 'hijo(a)']);
  }

  private function generarTokenold()
  {
    // Implementar según tu lógica de tokens
    return md5(date('Y-m-d') . 'tu_secret_key');
  }

  /**
   * Obtiene la lista de pisos disponibles con cantidad de mesas
   */
  public function obtenerpisosAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $capacidad = $this->_getSanitizedParam('capacidad');

    if (!$capacidad) {
      echo json_encode(['success' => false, 'message' => 'Capacidad requerida']);
      return;
    }

    try {
      $mesasModel = new Administracion_Model_DbTable_Mesas();
      // Usar el método específico de administración que no depende de sesión
      $pisos = $mesasModel->getPisosDisponiblesAdmin($capacidad);

      echo json_encode([
        'success' => true,
        'data' => $pisos
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => 'Error al obtener pisos: ' . $e->getMessage()
      ]);
    }
  }

  /**
   * Obtiene ambientes de un piso específico con cantidad de mesas disponibles
   */
  public function obtenerambientesAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $pisoId = $this->_getSanitizedParam('piso_id');
    $capacidad = $this->_getSanitizedParam('capacidad');

    if (!$pisoId) {
      echo json_encode(['success' => false, 'message' => 'ID de piso requerido']);
      return;
    }

    if (!$capacidad) {
      echo json_encode(['success' => false, 'message' => 'Capacidad requerida']);
      return;
    }

    try {
      $mesasModel = new Administracion_Model_DbTable_Mesas();
      // Usar el método específico de administración que no depende de sesión
      $ambientes = $mesasModel->getAmbientesPorPisoAdmin($pisoId, $capacidad);

      echo json_encode([
        'success' => true,
        'data' => $ambientes
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => 'Error al obtener ambientes: ' . $e->getMessage()
      ]);
    }
  }

  /**
   * Obtiene mesas disponibles de un ambiente con capacidad específica
   * y TODOS los elementos del ambiente para mostrar el plano completo
   */
  public function obtenermesasdisponiblesAction()
  {
    //error_reporting(E_ALL);
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    $ambienteId = $this->_getSanitizedParam('ambiente_id');
    $capacidad = $this->_getSanitizedParam('capacidad');

    if (!$ambienteId || !$capacidad) {
      echo json_encode(['success' => false, 'message' => 'Parámetros incompletos']);
      return;
    }

    try {
      $mesasModel = new Administracion_Model_DbTable_Mesas();
      $ambienteModel = new Administracion_Model_DbTable_Ambientes();

      // Obtener TODAS las mesas/elementos del ambiente (para mostrar el plano completo)
      $todosElementos = $mesasModel->getTodosElementosDelAmbiente($ambienteId);

      // Obtener solo las mesas disponibles con la capacidad exacta (seleccionables)
      // Usar el método específico de administración que no depende de sesión
      $mesasDisponibles = $mesasModel->getMesasPorAmbienteAdmin($ambienteId, $capacidad);

      // Obtener información del ambiente (filas, columnas, etc.)
      $ambiente = $ambienteModel->getById($ambienteId);

      echo json_encode([
        'success' => true,
        'mesas_disponibles' => $mesasDisponibles,
        'todos_elementos' => $todosElementos,
        'ambiente' => $ambiente
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => 'Error al obtener mesas: ' . $e->getMessage()
      ]);
    }
  }

  /**
   * Crea la reserva final con todos los datos
   */
  public function crearreservaAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');

    if (!$_POST) {
      echo json_encode(['success' => false, 'message' => 'Método no permitido']);
      return;
    }

    try {
      // Obtener datos de sesión
      $reservaData = Session::getInstance()->get('admin_reserva_data');

      if (!$reservaData) {
        echo json_encode(['success' => false, 'message' => 'No hay datos de reserva en sesión']);
        return;
      }

      $mesaId = $this->_getSanitizedParam('mesa_id');
      $metodoPago = $this->_getSanitizedParam('metodo_pago'); // 'cargo' o 'datafono'
      $numeroCuotas = intval($this->_getSanitizedParam('numero_cuotas') ?: 1);

      if (!$mesaId || !$metodoPago) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        return;
      }

      // Validar número de cuotas
      if ($numeroCuotas < 1 || $numeroCuotas > 12) {
        echo json_encode(['success' => false, 'message' => 'Número de cuotas inválido (1-12)']);
        return;
      }

      // Obtener información de la mesa y categoría para calcular precios
      $mesasModel = new Administracion_Model_DbTable_Mesas();
      $mesa = $mesasModel->getById($mesaId);

      if (!$mesa) {
        throw new Exception('Mesa no encontrada');
      }

      // Obtener información del ambiente y categoría
      $ambientesModel = new Administracion_Model_DbTable_Ambientes();
      $ambiente = $ambientesModel->getById($mesa->mesa_ambiente);

      $categoria = null;
      if ($ambiente && $ambiente->ambiente_categoria) {
        $categoriasModel = new Administracion_Model_DbTable_Categorias();
        $categoria = $categoriasModel->getById($ambiente->ambiente_categoria);
      }

      // Calcular el total a pagar
      $totalPagar = 0;
      $beneficiariosParaPrecio = [];

      foreach ($reservaData['beneficiarios_seleccionados'] as $beneficiario) {
        $esSocio = ($beneficiario['documento'] === $reservaData['socio']['SBE_CODI']);
        $precio = 0;

        if ($categoria) {
          $esMenor25 = isset($beneficiario['menor25']) && $beneficiario['menor25'];
          $esHijo = isset($beneficiario['hijo']) && $beneficiario['hijo'];

          // Calcular precio según tipo
          if ($esMenor25 && $esHijo) {
            $precio = floatval($categoria->categoria_precio_socio_hijo ?? 0);
          } else {
            $precio = floatval($categoria->categoria_precio_socio ?? 0);
          }
        }

        $totalPagar += $precio;
        $beneficiariosParaPrecio[] = ['beneficiario' => $beneficiario, 'precio' => $precio];
      }

      // Agregar precio de invitados no asociados
      if ($categoria && $reservaData['cantidad_no_asociados'] > 0) {
        $precioInvitado = floatval($categoria->categoria_precio_invitado ?? 0);
        $totalPagar += ($precioInvitado * $reservaData['cantidad_no_asociados']);
      }

      // Aplicar descuento si existe
      $descuento = floatval($ambiente->ambiente_descuento ?? 0);
      if ($descuento > 0) {
        $totalPagar = $totalPagar - ($totalPagar * ($descuento / 100));
      }

      // Determinar estado y textos según método de pago
      if ($metodoPago === 'cargo') {
        $estadoReserva = 2;
        $estadoTexto = 'Aprobado';
        $estadoTexto2 = 'El pago se encuentra aprobado - Cargo a la acción';
        $fechaPago = date('Y-m-d H:i:s');
      } else { // datafono
        $estadoReserva = 11;
        $estadoTexto = 'Pendiente confirmación';
        $estadoTexto2 = 'Pago pendiente de confirmación - Datáfono';
        $fechaPago = date('Y-m-d H:i:s');
      }

      // Obtener IP y User Agent
      $ip = $_SERVER['REMOTE_ADDR'] ?? '';
      $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

      // Separar nombre y apellido del socio
      $nombreCompleto = trim($reservaData['socio']['sbe_nomb'] ?? '');
      $apellido = trim($reservaData['socio']['sbe_apel'] ?? '');

      // Crear reserva
      $reservasModel = new Administracion_Model_DbTable_Reservas();
      $dataReserva = [
        'reserva_id_evento' => 1, // Evento principal
        'reserva_nombre_cliente' => $nombreCompleto,
        'reserva_apellido_cliente' => $apellido,
        'reserva_fecha' => date('Y-m-d'),
        'reserva_hora' => date('H:i:s'),
        'reserva_total_personas' => $reservaData['total_invitados'],
        'reserva_telefono' => trim($reservaData['socio']['sbe_ncel'] ?? ''),
        'reserva_correo' => trim($reservaData['socio']['sbe_mail'] ?? ''),
        'reserva_comentario' => 'Reserva creada desde panel administrativo',
        'reserva_documento' => trim($reservaData['socio']['SBE_CODI'] ?? ''),
        'reserva_numero_carnet' => trim($reservaData['socio']['ncar'] ?? ''),
        'reserva_mesa_id' => $mesaId,
        'reserva_estado' => $estadoReserva,
        'reserva_estado_texto' => $estadoTexto,
        'reserva_estado_texto2' => $estadoTexto2,
        'reserva_total_pagar' => $totalPagar,
        'reserva_metodo_pago' => $metodoPago,
        'reserva_numero_cuotas' => $numeroCuotas,
        'reserva_fecha_pago' => $fechaPago,
        'reserva_ip' => $ip,
        'reserva_user_agent' => $userAgent,
        'reserva_fecha_creacion' => date('Y-m-d H:i:s'),
        'reserva_usuario_creacion' => Session::getInstance()->get('usuarioData')->usu_id ?? 1,
        'reserva_usuario_actualizacion' => Session::getInstance()->get('usuarioData')->usu_id ?? 1,
        'reserva_fecha_actualizacion' => date('Y-m-d H:i:s'),
        'reserva_fecha_inicio_reserva' => date('Y-m-d H:i:s'),
        'reserva_fecha_cierre_reserva' => date('Y-m-d H:i:s', strtotime('+30 days')),
        'reserva_fecha_limite_pago' => date('Y-m-d H:i:s', strtotime('+7 days')),
      ];
      error_log(print_r($dataReserva, true));
      $reservaId = $reservasModel->insert($dataReserva);

      if (!$reservaId) {
        throw new Exception('Error al crear la reserva');
      }

      // Crear invitados
      $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();

      // Agregar beneficiarios seleccionados
      foreach ($beneficiariosParaPrecio as $item) {
        $beneficiario = $item['beneficiario'];
        $esSocio = ($beneficiario['documento'] === $reservaData['socio']['SBE_CODI']);

        $dataInvitado = [
          'reserva_id_reserva' => $reservaId,
          'invitadoReserva_nombre_invitado' => trim($beneficiario['nombre'] ?? ''),
          'invitadoReserva_apellido_invitado' => trim($beneficiario['apellido'] ?? ''),
          'invitadoReserva_correo_invitado' => trim($beneficiario['correo'] ?? ''),
          'documento_invitado' => trim($beneficiario['documento'] ?? ''),
          'invitado_tipo' => '1', // 1=Beneficiario (S es para otro contexto)
          'invitadoReserva_beneficiario_principal' => $esSocio ? 1 : 0,
          'invitadoReserva_estado_invitado' => 'A', // A=Aceptado (beneficiarios y socio)
          'invitadoReserva_beneficiario_hijo' => isset($beneficiario['hijo']) && $beneficiario['hijo'] ? 1 : 0,
          'invitadoReserva_beneficiario_menor25' => isset($beneficiario['menor25']) && $beneficiario['menor25'] ? 1 : 0,
          'invitadoReserva_beneficiario_cupo' => 1,
          'invitadosReserva_fecha_creacion' => date('Y-m-d H:i:s'),
          'invitadosReserva_fecha_actualizacion' => date('Y-m-d H:i:s'),
          'invitado_evento' => 1,

        ];
        // error_log(print_r($dataInvitado, true));
        // error_log(print_r($reservaData, true));
        $invitadosModel->insert($dataInvitado);
      }

      // Agregar invitados no asociados (tipo P = Pendiente)
      for ($i = 1; $i <= $reservaData['cantidad_no_asociados']; $i++) {
        $dataInvitado = [
          'reserva_id_reserva' => $reservaId,
          'invitadoReserva_nombre_invitado' => 'Invitado No.' . $i,
          'invitadoReserva_apellido_invitado' => '',
          'invitadoReserva_correo_invitado' => '',
          'documento_invitado' => '',
          'invitado_tipo' => '2', // 1=Invitado
          'invitadoReserva_beneficiario_principal' => 0,
          'invitadoReserva_estado_invitado' => 'P', // P=Pendiente (invitados sin datos)
          'invitadosReserva_fecha_creacion' => date('Y-m-d H:i:s'),
          'invitadosReserva_fecha_actualizacion' => date('Y-m-d H:i:s'),
          'invitado_evento' => 1
        ];
        // error_log(print_r($reservaData['cantidad_no_asociados'], true));
        // error_log(print_r($dataInvitado, true));

        $invitadosModel->insert($dataInvitado);
      }

      // Limpiar sesión
      Session::getInstance()->set('admin_reserva_data', null);

      echo json_encode([
        'success' => true,
        'message' => 'Reserva creada exitosamente',
        'reserva_id' => $reservaId,
        'total_pagar' => $totalPagar
      ]);
    } catch (Exception $e) {
      echo json_encode([
        'success' => false,
        'message' => 'Error al crear reserva: ' . $e->getMessage()
      ]);
    }
  }
}
function getHour($date)
{
  $post = date('H', strtotime($date)) > 12 ? 'pm' : 'am';
  return date('h:i', strtotime($date)) . ' ' . $post;
}
