<?php

/**
 * Controlador de Invitadosreserva que permite la  creacion, edicion  y eliminacion de los invitadosReserva del Sistema
 */
class Administracion_invitadosreservaController extends Administracion_mainController
{
	/**
	 * $mainModel  instancia del modelo de  base de datos invitadosReserva
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
	protected $_csrf_section = "administracion_invitadosreserva";

	/**
	 * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
	 * @var string
	 */
	protected $namepages;



	/**
	 * Inicializa las variables principales del controlador invitadosreserva .
	 *
	 * @return void.
	 */
	public function init()
	{
		$this->mainModel = new Administracion_Model_DbTable_Invitadosreserva();
		$this->namefilter = "parametersfilterinvitadosreserva";
		$this->route = "/administracion/invitadosreserva";
		$this->namepages = "pages_invitadosreserva";
		$this->namepageactual = "page_actual_invitadosreserva";
		$this->_view->route = $this->route;
		if (Session::getInstance()->get($this->namepages)) {
			$this->pages = Session::getInstance()->get($this->namepages);
		} else {
			$this->pages = 20;
		}
		parent::init();
	}


	/**
	 * Recibe la informacion y  muestra un listado de  invitadosReserva con sus respectivos filtros.
	 *
	 * @return void.
	 */
	public function indexAction()
	{
		$title = "Administración de invitadosReserva";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;
		$this->filters();
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$filters = (object)Session::getInstance()->get($this->namefilter);
		$this->_view->filters = $filters;
		$filters = $this->getFilter();
		$order = "orden ASC";
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
	 * Genera la Informacion necesaria para editar o crear un  invitadosReserva  y muestra su formulario
	 *
	 * @return void.
	 */
	public function manageAction()
	{
		$this->_view->route = $this->route;
		$this->_csrf_section = "manage_invitadosreserva_" . date("YmdHis");
		$this->_csrf->generateCode($this->_csrf_section);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$id = $this->_getSanitizedParam("id");
		if ($id > 0) {
			$content = $this->mainModel->getById($id);
			if ($content->id_invitado) {
				$this->_view->content = $content;
				$this->_view->routeform = $this->route . "/update";
				$title = "Actualizar invitadosReserva";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			} else {
				$this->_view->routeform = $this->route . "/insert";
				$title = "Crear invitadosReserva";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			}
		} else {
			$this->_view->routeform = $this->route . "/insert";
			$title = "Crear invitadosReserva";
			$this->getLayout()->setTitle($title);
			$this->_view->titlesection = $title;
		}
	}

	/**
	 * Inserta la informacion de un invitadosReserva  y redirecciona al listado de invitadosReserva.
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
			$this->mainModel->changeOrder($id, $id);
			$data['id_invitado'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'CREAR INVITADOSRESERVA';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y Actualiza la informacion de un invitadosReserva  y redirecciona al listado de invitadosReserva.
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
			if ($content->id_invitado) {
				$data = $this->getData();
				$this->mainModel->update($data, $id);
			}
			$data['id_invitado'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'EDITAR INVITADOSRESERVA';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y elimina un invitadosReserva  y redirecciona al listado de invitadosReserva.
	 *
	 * @return void.
	 */
	public function deleteAction()
	{
		$this->setLayout('blanco');
		$csrf = $this->_getSanitizedParam("csrf");
		if (Session::getInstance()->get('csrf')[$this->_csrf_section] == $csrf) {
			$id =  $this->_getSanitizedParam("id");
			if (isset($id) && $id > 0) {
				$content = $this->mainModel->getById($id);
				if (isset($content)) {
					$this->mainModel->deleteRegister($id);
					$data = (array)$content;
					$data['log_log'] = print_r($data, true);
					$data['log_tipo'] = 'BORRAR INVITADOSRESERVA';
					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($data);
				}
			}
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Invitadosreserva.
	 *
	 * @return array con toda la informacion recibida del formulario.
	 */
	private function getData()
	{
		$data = array();
		$data['documento_invitado'] = $this->_getSanitizedParamHtml("documento_invitado");
		$data['reserva_id_reserva'] = $this->_getSanitizedParamHtml("reserva_id_reserva");
		$data['invitadoReserva_nombre_invitado'] = $this->_getSanitizedParam("invitadoReserva_nombre_invitado");
		$data['invitadoReserva_apellido_invitado'] = $this->_getSanitizedParam("invitadoReserva_apellido_invitado");
		$data['invitadoReserva_correo_invitado'] = $this->_getSanitizedParamHtml("invitadoReserva_correo_invitado");
		$data['invitadoReserva_estado_invitado'] = $this->_getSanitizedParam("invitadoReserva_estado_invitado");
		$data['invitadoReserva_fecha_nacimiento'] = $this->_getSanitizedParamHtml("invitadoReserva_fecha_nacimiento");
		$data['invitadoReserva_telefono'] = $this->_getSanitizedParam("invitadoReserva_telefono");
		$data['invitadosReserva_fecha_creacion'] = $this->_getSanitizedParamHtml("invitadosReserva_fecha_creacion");
		$data['invitadosReserva_fecha_actualizacion'] = $this->_getSanitizedParamHtml("invitadosReserva_fecha_actualizacion");
		$data['invitadosReserva_usuario_creacion'] = $this->_getSanitizedParam("invitadosReserva_usuario_creacion");
		$data['invitadosReserva_actualizacion'] = $this->_getSanitizedParamHtml("invitadosReserva_actualizacion");
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
			$filters = (object)Session::getInstance()->get($this->namefilter);
			if ($filters->reserva_id_reserva != '') {
				$filtros = $filtros . " AND reserva_id_reserva LIKE '%" . $filters->reserva_id_reserva . "%'";
			}
			if ($filters->invitadoReserva_nombre_invitado != '') {
				$filtros = $filtros . " AND invitadoReserva_nombre_invitado LIKE '%" . $filters->invitadoReserva_nombre_invitado . "%'";
			}
			if ($filters->invitadoReserva_correo_invitado != '') {
				$filtros = $filtros . " AND invitadoReserva_correo_invitado LIKE '%" . $filters->invitadoReserva_correo_invitado . "%'";
			}
			if ($filters->invitadoReserva_estado_invitado != '') {
				$filtros = $filtros . " AND invitadoReserva_estado_invitado LIKE '%" . $filters->invitadoReserva_estado_invitado . "%'";
			}
			if ($filters->invitadoReserva_telefono != '') {
				$filtros = $filtros . " AND invitadoReserva_telefono LIKE '%" . $filters->invitadoReserva_telefono . "%'";
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
			$parramsfilter['reserva_id_reserva'] =  $this->_getSanitizedParam("reserva_id_reserva");
			$parramsfilter['invitadoReserva_nombre_invitado'] =  $this->_getSanitizedParam("invitadoReserva_nombre_invitado");
			$parramsfilter['invitadoReserva_correo_invitado'] =  $this->_getSanitizedParam("invitadoReserva_correo_invitado");
			$parramsfilter['invitadoReserva_estado_invitado'] =  $this->_getSanitizedParam("invitadoReserva_estado_invitado");
			$parramsfilter['invitadoReserva_telefono'] =  $this->_getSanitizedParam("invitadoReserva_telefono");
			Session::getInstance()->set($this->namefilter, $parramsfilter);
		}
		if ($this->_getSanitizedParam("cleanfilter") == 1) {
			Session::getInstance()->set($this->namefilter, '');
			Session::getInstance()->set($this->namepageactual, 1);
		}
	}
}
