<?php
/**
 * Controlador de Pisos que permite la  creacion, edicion  y eliminacion de los piso del Sistema
 */
class Administracion_pisosController extends Administracion_mainController
{
    
      public $botonpanel = 6;

	/**
	 * $mainModel  instancia del modelo de  base de datos piso
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
	protected $_csrf_section = "administracion_pisos";

	/**
	 * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
	 * @var string
	 */
	protected $namepages;



	/**
	 * Inicializa las variables principales del controlador pisos .
	 *
	 * @return void.
	 */
	public function init()
	{
		$this->mainModel = new Administracion_Model_DbTable_Pisos();
		$this->namefilter = "parametersfilterpisos";
		$this->route = "/administracion/pisos";
		$this->namepages = "pages_pisos";
		$this->namepageactual = "page_actual_pisos";
		$this->_view->route = $this->route;
		if (Session::getInstance()->get($this->namepages)) {
			$this->pages = Session::getInstance()->get($this->namepages);
		} else {
			$this->pages = 20;
		}
		parent::init();
	}


	/**
	 * Recibe la informacion y  muestra un listado de  piso con sus respectivos filtros.
	 *
	 * @return void.
	 */
	public function indexAction()
	{
		$title = "Administración de pisos";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;
		$this->filters();
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$filters = (object) Session::getInstance()->get($this->namefilter);
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
	 * Genera la Informacion necesaria para editar o crear un  piso  y muestra su formulario
	 *
	 * @return void.
	 */
	public function manageAction()
	{
		$this->_view->route = $this->route;
		$this->_csrf_section = "manage_pisos_" . date("YmdHis");
		$this->_csrf->generateCode($this->_csrf_section);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$id = $this->_getSanitizedParam("id");
		if ($id > 0) {
			$content = $this->mainModel->getById($id);
			if ($content->piso_id) {
				$this->_view->content = $content;
				$this->_view->routeform = $this->route . "/update";
				$title = "Actualizar piso";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			} else {
				$this->_view->routeform = $this->route . "/insert";
				$title = "Crear piso";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			}
		} else {
			$this->_view->routeform = $this->route . "/insert";
			$title = "Crear piso";
			$this->getLayout()->setTitle($title);
			$this->_view->titlesection = $title;
		}
	}

	/**
	 * Inserta la informacion de un piso  y redirecciona al listado de piso.
	 *
	 * @return void.
	 */
	public function insertAction()
	{
		$this->setLayout('blanco');
		$csrf = $this->_getSanitizedParam("csrf");
		if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
			$data = $this->getData();
			$uploadImage = new Core_Model_Upload_Image();
			if ($_FILES['piso_imagen_disponible']['name'] != '') {
				$data['piso_imagen_disponible'] = $uploadImage->upload("piso_imagen_disponible");
			}
			if ($_FILES['piso_imagen_pendiente']['name'] != '') {
				$data['piso_imagen_pendiente'] = $uploadImage->upload("piso_imagen_pendiente");
			}
			if ($_FILES['piso_imagen_ocupado']['name'] != '') {
				$data['piso_imagen_ocupado'] = $uploadImage->upload("piso_imagen_ocupado");
			}
			$id = $this->mainModel->insert($data);
			$this->mainModel->changeOrder($id, $id);
			$data['piso_id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'CREAR PISO';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y Actualiza la informacion de un piso  y redirecciona al listado de piso.
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
			if ($content->piso_id) {
				$data = $this->getData();
				$uploadImage = new Core_Model_Upload_Image();
				if ($_FILES['piso_imagen_disponible']['name'] != '') {
					if ($content->piso_imagen_disponible) {
						$uploadImage->delete($content->piso_imagen_disponible);
					}
					$data['piso_imagen_disponible'] = $uploadImage->upload("piso_imagen_disponible");
				} else {
					$data['piso_imagen_disponible'] = $content->piso_imagen_disponible;
				}

				if ($_FILES['piso_imagen_pendiente']['name'] != '') {
					if ($content->piso_imagen_pendiente) {
						$uploadImage->delete($content->piso_imagen_pendiente);
					}
					$data['piso_imagen_pendiente'] = $uploadImage->upload("piso_imagen_pendiente");
				} else {
					$data['piso_imagen_pendiente'] = $content->piso_imagen_pendiente;
				}

				if ($_FILES['piso_imagen_ocupado']['name'] != '') {
					if ($content->piso_imagen_ocupado) {
						$uploadImage->delete($content->piso_imagen_ocupado);
					}
					$data['piso_imagen_ocupado'] = $uploadImage->upload("piso_imagen_ocupado");
				} else {
					$data['piso_imagen_ocupado'] = $content->piso_imagen_ocupado;
				}
				$this->mainModel->update($data, $id);
			}
			$data['piso_id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'EDITAR PISO';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y elimina un piso  y redirecciona al listado de piso.
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
					$uploadImage = new Core_Model_Upload_Image();
					if (isset($content->piso_imagen_disponible) && $content->piso_imagen_disponible != '') {
						$uploadImage->delete($content->piso_imagen_disponible);
					}

					if (isset($content->piso_imagen_pendiente) && $content->piso_imagen_pendiente != '') {
						$uploadImage->delete($content->piso_imagen_pendiente);
					}

					if (isset($content->piso_imagen_ocupado) && $content->piso_imagen_ocupado != '') {
						$uploadImage->delete($content->piso_imagen_ocupado);
					}
				// 	$this->mainModel->deleteRegister($id);
					$data = (array) $content;
					$data['log_log'] = print_r($data, true);
					$data['log_tipo'] = 'BORRAR PISO';
					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($data);
				}
			}
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Pisos.
	 *
	 * @return array con toda la informacion recibida del formulario.
	 */
	private function getData()
	{
		$data = array();
		$data['piso_evento'] = '1';
		$data['piso_nombre'] = $this->_getSanitizedParam("piso_nombre");
		$data['piso_color'] = $this->_getSanitizedParam("piso_color");
		if ($this->_getSanitizedParam("piso_estado") == '') {
			$data['piso_estado'] = '0';
		} else {
			$data['piso_estado'] = $this->_getSanitizedParam("piso_estado");
		}
		$data['piso_imagen_disponible'] = "";
		$data['piso_imagen_pendiente'] = "";
		$data['piso_imagen_ocupado'] = "";
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
			if ($filters->piso_nombre != '') {
				$filtros = $filtros . " AND piso_nombre LIKE '%" . $filters->piso_nombre . "%'";
			}
			if ($filters->piso_color != '') {
				$filtros = $filtros . " AND piso_color LIKE '%" . $filters->piso_color . "%'";
			}
			if ($filters->piso_estado != '') {
				$filtros = $filtros . " AND piso_estado LIKE '%" . $filters->piso_estado . "%'";
			}
			if ($filters->piso_imagen_disponible != '') {
				$filtros = $filtros . " AND piso_imagen_disponible LIKE '%" . $filters->piso_imagen_disponible . "%'";
			}
			if ($filters->piso_imagen_pendiente != '') {
				$filtros = $filtros . " AND piso_imagen_pendiente LIKE '%" . $filters->piso_imagen_pendiente . "%'";
			}
			if ($filters->piso_imagen_ocupado != '') {
				$filtros = $filtros . " AND piso_imagen_ocupado LIKE '%" . $filters->piso_imagen_ocupado . "%'";
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
			$parramsfilter['piso_nombre'] = $this->_getSanitizedParam("piso_nombre");
			$parramsfilter['piso_color'] = $this->_getSanitizedParam("piso_color");
			$parramsfilter['piso_estado'] = $this->_getSanitizedParam("piso_estado");
			$parramsfilter['piso_imagen_disponible'] = $this->_getSanitizedParam("piso_imagen_disponible");
			$parramsfilter['piso_imagen_pendiente'] = $this->_getSanitizedParam("piso_imagen_pendiente");
			$parramsfilter['piso_imagen_ocupado'] = $this->_getSanitizedParam("piso_imagen_ocupado");
			Session::getInstance()->set($this->namefilter, $parramsfilter);
		}
		if ($this->_getSanitizedParam("cleanfilter") == 1) {
			Session::getInstance()->set($this->namefilter, '');
			Session::getInstance()->set($this->namepageactual, 1);
		}
	}
}