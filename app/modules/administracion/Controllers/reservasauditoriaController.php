<?php
/**
 * Controlador de Reservasauditoria que permite la  creacion, edicion  y eliminacion de los reservasauditoria del Sistema
 */
class Administracion_reservasauditoriaController extends Administracion_mainController
{
      public $botonpanel = 18;

	/**
	 * $mainModel  instancia del modelo de  base de datos reservasauditoria
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
	protected $_csrf_section = "administracion_reservasauditoria";

	/**
	 * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
	 * @var string
	 */
	protected $namepages;
	protected $namepageactual;



	/**
	 * Inicializa las variables principales del controlador reservasauditoria .
	 *
	 * @return void.
	 */
	public function init()
	{
		$this->mainModel = new Administracion_Model_DbTable_Reservasauditoria();
		$this->namefilter = "parametersfilterreservasauditoria";
		$this->route = "/administracion/reservasauditoria";
		$this->namepages = "pages_reservasauditoria";
		$this->namepageactual = "page_actual_reservasauditoria";
		$this->_view->route = $this->route;
		if (Session::getInstance()->get($this->namepages)) {
			$this->pages = Session::getInstance()->get($this->namepages);
		} else {
			$this->pages = 20;
		}
		parent::init();
	}


	/**
	 * Recibe la informacion y  muestra un listado de  reservasauditoria con sus respectivos filtros.
	 *
	 * @return void.
	 */
	public function indexAction()
	{
		$title = "Auditoria de Reservas";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;
		$this->filters();
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$filters = (object) Session::getInstance()->get($this->namefilter);
		$this->_view->filters = $filters;
		$filters = $this->getFilter();
		$order = "fecha_creacion DESC, id DESC";



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

		
	}

	/**
	 * Genera la Informacion necesaria para editar o crear un  reservasauditoria  y muestra su formulario
	 *
	 * @return void.
	 */
	public function manageAction()
	{
		$this->_view->route = $this->route;
		$this->_csrf_section = "manage_reservasauditoria_" . date("YmdHis");
		$this->_csrf->generateCode($this->_csrf_section);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$id = $this->_getSanitizedParam("id");
		if ($id > 0) {
			$content = $this->mainModel->getById($id);
			if ($content->id) {
				$this->_view->content = $content;
				$this->_view->routeform = $this->route . "/update";
				$title = "Actualizar reservasauditoria";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			} else {
				$this->_view->routeform = $this->route . "/insert";
				$title = "Crear reservasauditoria";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			}
		} else {
			$this->_view->routeform = $this->route . "/insert";
			$title = "Crear reservasauditoria";
			$this->getLayout()->setTitle($title);
			$this->_view->titlesection = $title;
		}
	}

	/**
	 * Inserta la informacion de un reservasauditoria  y redirecciona al listado de reservasauditoria.
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

			$data['id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'CREAR RESERVASAUDITORIA';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y Actualiza la informacion de un reservasauditoria  y redirecciona al listado de reservasauditoria.
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
			if ($content->id) {
				$data = $this->getData();
				$this->mainModel->update($data, $id);
			}
			$data['id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'EDITAR RESERVASAUDITORIA';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y elimina un reservasauditoria  y redirecciona al listado de reservasauditoria.
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
					$data['log_tipo'] = 'BORRAR RESERVASAUDITORIA';
					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($data);
				}
			}
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Reservasauditoria.
	 *
	 * @return array con toda la informacion recibida del formulario.
	 */
	private function getData()
	{
		$data = array();
		if ($this->_getSanitizedParam("reserva_id") == '') {
			$data['reserva_id'] = '0';
		} else {
			$data['reserva_id'] = $this->_getSanitizedParam("reserva_id");
		}
		$data['numero_carnet'] = $this->_getSanitizedParam("numero_carnet");
		$data['documento_socio'] = $this->_getSanitizedParam("documento_socio");
		$data['accion'] = $this->_getSanitizedParam("accion");
		$data['controlador'] = $this->_getSanitizedParam("controlador");
		$data['metodo'] = $this->_getSanitizedParam("metodo");
		$data['estado_anterior'] = $this->_getSanitizedParam("estado_anterior");
		$data['estado_nuevo'] = $this->_getSanitizedParam("estado_nuevo");
		$data['mesa_id_anterior'] = $this->_getSanitizedParam("mesa_id_anterior");
		$data['mesa_id_nuevo'] = $this->_getSanitizedParam("mesa_id_nuevo");
		$data['invitados_antes'] = $this->_getSanitizedParam("invitados_antes");
		$data['invitados_despues'] = $this->_getSanitizedParam("invitados_despues");
		$data['datos_json'] = $this->_getSanitizedParam("datos_json");
		$data['ip_address'] = $this->_getSanitizedParam("ip_address");
		$data['user_agent'] = $this->_getSanitizedParam("user_agent");
		$data['session_data'] = $this->_getSanitizedParam("session_data");
		$data['url_completa'] = $this->_getSanitizedParam("url_completa");
		$data['parametros_get'] = $this->_getSanitizedParam("parametros_get");
		$data['parametros_post'] = $this->_getSanitizedParam("parametros_post");
		$data['observaciones'] = $this->_getSanitizedParam("observaciones");
		$data['fecha_creacion'] = $this->_getSanitizedParam("fecha_creacion");
		$data['usuario_sistema'] = $this->_getSanitizedParam("usuario_sistema");
		return $data;
	}
	/**
	 * Genera la consulta con los filtros de este controlador.
	 *
	 * @return string cadena con los filtros que se van a asignar a la base de datos
	 */
	protected function getFilter()
	{
		$filtros = " 1 = 1 ";
		if (Session::getInstance()->get($this->namefilter) != "") {
			$filters = (object) Session::getInstance()->get($this->namefilter);
			if ($filters->reserva_id != '') {
				$filtros = $filtros . " AND reserva_id = '" . $filters->reserva_id . "'";
			}
			if ($filters->numero_carnet != '') {
				$filtros = $filtros . " AND numero_carnet LIKE '%" . $filters->numero_carnet . "%'";
			}
			if ($filters->documento_socio != '') {
				$filtros = $filtros . " AND documento_socio LIKE '%" . $filters->documento_socio . "%'";
			}
			if ($filters->accion != '') {
				$filtros = $filtros . " AND accion = '" . $filters->accion . "'";
			}
			if ($filters->controlador != '') {
				$filtros = $filtros . " AND controlador LIKE '%" . $filters->controlador . "%'";
			}
			if ($filters->metodo != '') {
				$filtros = $filtros . " AND metodo LIKE '%" . $filters->metodo . "%'";
			}
			if ($filters->fecha_desde != '') {
				$filtros = $filtros . " AND DATE(fecha_creacion) >= '" . $filters->fecha_desde . "'";
			}
			if ($filters->fecha_hasta != '') {
				$filtros = $filtros . " AND DATE(fecha_creacion) <= '" . $filters->fecha_hasta . "'";
			}
		}
		return $filtros;
	}    /**
			 * Recibe y asigna los filtros de este controlador
			 *
			 * @return void
			 */
	protected function filters()
	{
		if ($this->getRequest()->isPost() == true) {
			Session::getInstance()->set($this->namepageactual, 1);
			$parramsfilter = array();
			$parramsfilter['reserva_id'] = $this->_getSanitizedParam("reserva_id");
			$parramsfilter['numero_carnet'] = $this->_getSanitizedParam("numero_carnet");
			$parramsfilter['documento_socio'] = $this->_getSanitizedParam("documento_socio");
			$parramsfilter['accion'] = $this->_getSanitizedParam("accion");
			$parramsfilter['controlador'] = $this->_getSanitizedParam("controlador");
			$parramsfilter['metodo'] = $this->_getSanitizedParam("metodo");
			$parramsfilter['fecha_desde'] = $this->_getSanitizedParam("fecha_desde");
			$parramsfilter['fecha_hasta'] = $this->_getSanitizedParam("fecha_hasta");
			Session::getInstance()->set($this->namefilter, $parramsfilter);
		}
		if ($this->_getSanitizedParam("cleanfilter") == 1) {
			Session::getInstance()->set($this->namefilter, '');
			Session::getInstance()->set($this->namepageactual, 1);
		}
	}

	/**
	 * Cambiar cantidad de elementos por p¨¢gina
	 */
	public function changepageAction()
	{
		$this->setLayout('blanco');
		$pages = $this->_getSanitizedParam("pages");
		if ($pages) {
			Session::getInstance()->set($this->namepages, $pages);
		}
		header('Location: ' . $this->route . '');
	}
}