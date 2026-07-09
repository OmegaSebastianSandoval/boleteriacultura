<?php

/**
 * Controlador de Accionsesiones que permite la  creacion, edicion  y eliminacion de los accionsesion del Sistema
 */
class Administracion_accionsesionesController extends Administracion_mainController
{
	public $botonpanel = 16;
	/**
	 * $mainModel  instancia del modelo de  base de datos accionsesion
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
	protected $_csrf_section = "administracion_accionsesiones";

	/**
	 * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
	 * @var string
	 */
	protected $namepages;

	protected $namepageactual;



	/**
	 * Inicializa las variables principales del controlador accionsesiones .
	 *
	 * @return void.
	 */
	public function init()
	{
		$this->mainModel = new Administracion_Model_DbTable_Accionsesiones();
		$this->namefilter = "parametersfilteraccionsesiones";
		$this->route = "/administracion/accionsesiones";
		$this->namepages = "pages_accionsesiones";
		$this->namepageactual = "page_actual_accionsesiones";
		$this->_view->route = $this->route;
		if (Session::getInstance()->get($this->namepages)) {
			$this->pages = Session::getInstance()->get($this->namepages);
		} else {
			$this->pages = 20;
		}
		parent::init();
	}


	/**
	 * Recibe la informacion y  muestra un listado de  accionsesion con sus respectivos filtros.
	 *
	 * @return void.
	 */
	public function indexAction()
	{
		$title = "Administraci&oacute;n de sesiones";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;
		$this->filters();
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$filters = (object) Session::getInstance()->get($this->namefilter);
		$this->_view->filters = $filters;
		$filters = $this->getFilter();
		$order = "accion_sesion_id DESC";
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
		$lists = $this->mainModel->getListPages($filters, $order, $start, $amount);
		$colaComprasModel = new Administracion_Model_DbTable_Colacompra();
		$reservasModel = new Administracion_Model_DbTable_Reservas();
		foreach ($lists as $key => $item) {
			if ($item->accion_sesion_documento_socio && $item->accion_sesion_sesion_activa == 1) {
				$item->cola_compra = $colaComprasModel->getList("cola_compras_socio_documento = '{$item->accion_sesion_documento_socio}' AND cola_compras_estado = 'activo'", "cola_compras_id DESC")[0];
				$item->reserva = $reservasModel->getList("reserva_documento = '{$item->accion_sesion_documento_socio}' AND reserva_estado = '1'", "id DESC")[0];
			}
		}
		// echo "<pre>";
		// print_r($lists);
		// echo "</pre>";
		$this->_view->lists = $lists;
		$this->_view->csrf_section = $this->_csrf_section;
	}

	/**
	 * Genera la Informacion necesaria para editar o crear un  accionsesion  y muestra su formulario
	 *
	 * @return void.
	 */
	public function manageAction()
	{
		$this->_view->route = $this->route;
		$this->_csrf_section = "manage_accionsesiones_" . date("YmdHis");
		$this->_csrf->generateCode($this->_csrf_section);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$id = $this->_getSanitizedParam("id");
		if ($id > 0) {
			$content = $this->mainModel->getById($id);
			if ($content->accion_sesion_id) {
				$this->_view->content = $content;
				$this->_view->routeform = $this->route . "/update";
				$title = "Actualizar accionsesion";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			} else {
				$this->_view->routeform = $this->route . "/insert";
				$title = "Crear accionsesion";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			}
		} else {
			$this->_view->routeform = $this->route . "/insert";
			$title = "Crear accionsesion";
			$this->getLayout()->setTitle($title);
			$this->_view->titlesection = $title;
		}
	}

	/**
	 * Inserta la informacion de un accionsesion  y redirecciona al listado de accionsesion.
	 *
	 * @return void.
	 */
	public function insertAction()
	{
		$this->setLayout('blanco');
		$csrf = $this->_getSanitizedParam("csrf");
		if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
			$data = $this->getData();
			$id = $this->mainModel->insert($data);

			$data['accion_sesion_id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'CREAR ACCIONSESION';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y Actualiza la informacion de un accionsesion  y redirecciona al listado de accionsesion.
	 *
	 * @return void.
	 */
	public function updateAction()
	{
		$this->setLayout('blanco');
		$csrf = $this->_getSanitizedParam("csrf");
		if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
			$id = $this->_getSanitizedParam("id");
			$content = $this->mainModel->getById($id);
			if ($content->accion_sesion_id) {
				$data = $this->getData();
				$this->mainModel->update($data, $id);
			}
			$data['accion_sesion_id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'EDITAR ACCIONSESION';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y elimina un accionsesion  y redirecciona al listado de accionsesion.
	 *
	 * @return void.
	 */
	public function deleteAction()
	{
		$this->setLayout('blanco');
		$csrf = $this->_getSanitizedParam("csrf");
		if (Session::getInstance()->get('csrf')[$this->_csrf_section] == $csrf) {
			$id = $this->_getSanitizedParam("id");
			if (isset($id) && $id > 0) {
				$content = $this->mainModel->getById($id);
				if (isset($content)) {
					$this->mainModel->deleteRegister($id);
					$data = (array) $content;
					$data['log_log'] = print_r($data, true);
					$data['log_tipo'] = 'BORRAR ACCIONSESION';
					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($data);
				}
			}
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Accionsesiones.
	 *
	 * @return array con toda la informacion recibida del formulario.
	 */
	private function getData()
	{
		$data = array();
		$data['accion_sesion_accion_numero'] = $this->_getSanitizedParam("accion_sesion_accion_numero");
		$data['accion_sesion_documento_socio'] = $this->_getSanitizedParam("accion_sesion_documento_socio");
		$data['accion_sesion_fecha_inicio'] = $this->_getSanitizedParam("accion_sesion_fecha_inicio");
		$data['accion_sesion_fecha_fin'] = $this->_getSanitizedParam("accion_sesion_fecha_fin");
		if ($this->_getSanitizedParam("accion_sesion_sesion_activa") == '') {
			$data['accion_sesion_sesion_activa'] = '0';
		} else {
			$data['accion_sesion_sesion_activa'] = $this->_getSanitizedParam("accion_sesion_sesion_activa");
		}
		$data['accion_sesion_ip_usuario'] = $this->_getSanitizedParam("accion_sesion_ip_usuario");
		$data['accion_sesion_user_agent'] = $this->_getSanitizedParam("accion_sesion_user_agent");
		return $data;
	}
	/**
	 * Genera la consulta con los filtros de este controlador.
	 *
	 * @return array cadena con los filtros que se van a asignar a la base de datos
	 */
	protected function getFilter()
	{
		$filtros = " 1 = 1 ";
		if (Session::getInstance()->get($this->namefilter) != "") {
			$filters = (object) Session::getInstance()->get($this->namefilter);
			if ($filters->accion_sesion_accion_numero != '') {
				$filtros = $filtros . " AND accion_sesion_accion_numero LIKE '%" . $filters->accion_sesion_accion_numero . "%'";
			}
			if ($filters->accion_sesion_documento_socio != '') {
				$filtros = $filtros . " AND accion_sesion_documento_socio LIKE '%" . $filters->accion_sesion_documento_socio . "%'";
			}
			if ($filters->accion_sesion_fecha_inicio != '') {
				$filtros = $filtros . " AND accion_sesion_fecha_inicio LIKE '%" . $filters->accion_sesion_fecha_inicio . "%'";
			}
			if ($filters->accion_sesion_fecha_fin != '') {
				$filtros = $filtros . " AND accion_sesion_fecha_fin LIKE '%" . $filters->accion_sesion_fecha_fin . "%'";
			}
			if ($filters->accion_sesion_sesion_activa != '') {
				$filtros = $filtros . " AND accion_sesion_sesion_activa LIKE '%" . $filters->accion_sesion_sesion_activa . "%'";
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
			$parramsfilter['accion_sesion_accion_numero'] = $this->_getSanitizedParam("accion_sesion_accion_numero");
			$parramsfilter['accion_sesion_documento_socio'] = $this->_getSanitizedParam("accion_sesion_documento_socio");
			$parramsfilter['accion_sesion_fecha_inicio'] = $this->_getSanitizedParam("accion_sesion_fecha_inicio");
			$parramsfilter['accion_sesion_fecha_fin'] = $this->_getSanitizedParam("accion_sesion_fecha_fin");
			$parramsfilter['accion_sesion_sesion_activa'] = $this->_getSanitizedParam("accion_sesion_sesion_activa");
			Session::getInstance()->set($this->namefilter, $parramsfilter);
		}
		if ($this->_getSanitizedParam("cleanfilter") == 1) {
			Session::getInstance()->set($this->namefilter, '');
			Session::getInstance()->set($this->namepageactual, 1);
		}
	}
	public function inactivarAction()
	{
		$this->setLayout('blanco');
		$id = $this->_getSanitizedParam("id");
		$reservaId = $this->_getSanitizedParam("reservaid");
		if (isset($id) && $id > 0) {
			$content = $this->mainModel->getById($id);
			if (isset($content)) {
				$colaComprasModel = new Administracion_Model_DbTable_Colacompra();
				$this->mainModel->editField($id, 'accion_sesion_sesion_activa', 0);
				$colaComprasModel->editField($content->cola_compra->cola_compras_id, 'cola_compras_estado', 'finalizado');
				if ($reservaId) {
					$reservasModel = new Administracion_Model_DbTable_Reservas();
					$reserva = $reservasModel->getById($reservaId);
					if ($reserva->reserva_estado == 1) {

						$reservasModel->editField($reservaId, 'reserva_estado', 8);
						$mesaIds = $reserva->reserva_mesa_id;
						$mesaIdsArray = explode(',', $mesaIds);
						foreach ($mesaIdsArray as $mesaId) {
							if (!empty($mesaId)) {
								$mesasModel = new Administracion_Model_DbTable_Mesas();
								$mesasModel->editField($mesaId, 'mesa_estado', 0);
							}
						}
						$reservasModel->editField($reservaId, 'reserva_mesa_id', 0);
					}
					$data['accion_sesion_id'] = $id;
					$data['log_log'] = print_r($data, true);
					$data['log_tipo'] = 'INACTIVAR ACCIONSESION';
					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($data);
				}
			}
		}
		header('Location: ' . $this->route . '' . '');
	}
}
