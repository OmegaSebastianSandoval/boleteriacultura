<?php

/**
 * Controlador de Mesas que permite la  creacion, edicion  y eliminacion de los mesa del Sistema
 */
class Administracion_mesasController extends Administracion_mainController
{
	public $botonpanel = 8;

	/**
	 * $mainModel  instancia del modelo de  base de datos mesa
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
	protected $_csrf_section = "administracion_mesas";

	/**
	 * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
	 * @var string
	 */
	protected $namepages;

	public $namepageactual;



	/**
	 * Inicializa las variables principales del controlador mesas .
	 *
	 * @return void.
	 */
	public function init()
	{
		$this->mainModel = new Administracion_Model_DbTable_Mesas();
		$this->namefilter = "parametersfiltermesas";
		$this->route = "/administracion/mesas";
		$this->namepages = "pages_mesas";
		$this->namepageactual = "page_actual_mesas";
		$this->_view->route = $this->route;
		if (Session::getInstance()->get($this->namepages)) {
			$this->pages = Session::getInstance()->get($this->namepages);
		} else {
			$this->pages = 20;
		}
		parent::init();
	}


	/**
	 * Recibe la informacion y  muestra un listado de  mesa con sus respectivos filtros.
	 *
	 * @return void.
	 */
	public function indexAction()
	{
		$title = "Administracion de mesas";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;
		$this->filters();
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$filters = (object) Session::getInstance()->get($this->namefilter);
		$this->_view->filters = $filters;
		$filters = $this->getFilter();
		$order = "";
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

		$reservaModel = new Administracion_Model_DbTable_Reservas();
		foreach ($lists as $list) {
			$mesa_id = $list->mesa_id;
			$list->reserva = $reservaModel->getList(" FIND_IN_SET($mesa_id,reserva_mesa_id) AND reserva_estado IN(2,3,11)", "")[0];
		}

		$this->_view->lists = $lists;
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->list_mesa_ambiente = $this->getMesaambiente();
	}

	/**
	 * Genera la Informacion necesaria para editar o crear un  mesa  y muestra su formulario
	 *
	 * @return void.
	 */
	public function manageAction()
	{
		$this->_view->route = $this->route;
		$this->_csrf_section = "manage_mesas_" . date("YmdHis");
		$this->_csrf->generateCode($this->_csrf_section);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$this->_view->list_mesa_ambiente = $this->getMesaambiente();
		$this->_view->list_mesa_forma = $this->getMesaforma();
		$id = $this->_getSanitizedParam("id");
		if ($id > 0) {
			$content = $this->mainModel->getById($id);
			if ($content->mesa_id) {
				$this->_view->content = $content;
				$this->_view->routeform = $this->route . "/update";
				$title = "Actualizar mesa";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			} else {
				$this->_view->routeform = $this->route . "/insert";
				$title = "Crear mesa";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			}
		} else {
			$this->_view->routeform = $this->route . "/insert";
			$title = "Crear mesa";
			$this->getLayout()->setTitle($title);
			$this->_view->titlesection = $title;
		}
	}

	/**
	 * Inserta la informacion de un mesa  y redirecciona al listado de mesa.
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
			if ($_FILES['mesa_imagen_disponible']['name'] != '') {
				$data['mesa_imagen_disponible'] = $uploadImage->upload("mesa_imagen_disponible");
			}
			if ($_FILES['mesa_imagen_pendiente']['name'] != '') {
				$data['mesa_imagen_pendiente'] = $uploadImage->upload("mesa_imagen_pendiente");
			}
			if ($_FILES['mesa_imagen_ocupada']['name'] != '') {
				$data['mesa_imagen_ocupada'] = $uploadImage->upload("mesa_imagen_ocupada");
			}
			if ($_FILES['mesa_imagen_ubicacion_en_ambiente']['name'] != '') {
				$data['mesa_imagen_ubicacion_en_ambiente'] = $uploadImage->upload("mesa_imagen_ubicacion_en_ambiente");
			}
			if ($_FILES['mesa_imagen_ubicacion_en_piso']['name'] != '') {
				$data['mesa_imagen_ubicacion_en_piso'] = $uploadImage->upload("mesa_imagen_ubicacion_en_piso");
			}
			$id = $this->mainModel->insert($data);
			$this->mainModel->changeOrder($id, $id);
			$data['mesa_id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'CREAR MESA';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y Actualiza la informacion de un mesa  y redirecciona al listado de mesa.
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
			if ($content->mesa_id) {
				$data = $this->getData();
				$uploadImage = new Core_Model_Upload_Image();
				if ($_FILES['mesa_imagen_disponible']['name'] != '') {
					if ($content->mesa_imagen_disponible) {
						$uploadImage->delete($content->mesa_imagen_disponible);
					}
					$data['mesa_imagen_disponible'] = $uploadImage->upload("mesa_imagen_disponible");
				} else {
					$data['mesa_imagen_disponible'] = $content->mesa_imagen_disponible;
				}

				if ($_FILES['mesa_imagen_pendiente']['name'] != '') {
					if ($content->mesa_imagen_pendiente) {
						$uploadImage->delete($content->mesa_imagen_pendiente);
					}
					$data['mesa_imagen_pendiente'] = $uploadImage->upload("mesa_imagen_pendiente");
				} else {
					$data['mesa_imagen_pendiente'] = $content->mesa_imagen_pendiente;
				}

				if ($_FILES['mesa_imagen_ocupada']['name'] != '') {
					if ($content->mesa_imagen_ocupada) {
						$uploadImage->delete($content->mesa_imagen_ocupada);
					}
					$data['mesa_imagen_ocupada'] = $uploadImage->upload("mesa_imagen_ocupada");
				} else {
					$data['mesa_imagen_ocupada'] = $content->mesa_imagen_ocupada;
				}

				if ($_FILES['mesa_imagen_ubicacion_en_ambiente']['name'] != '') {
					if ($content->mesa_imagen_ubicacion_en_ambiente) {
						$uploadImage->delete($content->mesa_imagen_ubicacion_en_ambiente);
					}
					$data['mesa_imagen_ubicacion_en_ambiente'] = $uploadImage->upload("mesa_imagen_ubicacion_en_ambiente");
				} else {
					$data['mesa_imagen_ubicacion_en_ambiente'] = $content->mesa_imagen_ubicacion_en_ambiente;
				}

				if ($_FILES['mesa_imagen_ubicacion_en_piso']['name'] != '') {
					if ($content->mesa_imagen_ubicacion_en_piso) {
						$uploadImage->delete($content->mesa_imagen_ubicacion_en_piso);
					}
					$data['mesa_imagen_ubicacion_en_piso'] = $uploadImage->upload("mesa_imagen_ubicacion_en_piso");
				} else {
					$data['mesa_imagen_ubicacion_en_piso'] = $content->mesa_imagen_ubicacion_en_piso;
				}
				$this->mainModel->update($data, $id);
			}
			$data['mesa_id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'EDITAR MESA';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y elimina un mesa  y redirecciona al listado de mesa.
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
					if (isset($content->mesa_imagen_disponible) && $content->mesa_imagen_disponible != '') {
						$uploadImage->delete($content->mesa_imagen_disponible);
					}

					if (isset($content->mesa_imagen_pendiente) && $content->mesa_imagen_pendiente != '') {
						$uploadImage->delete($content->mesa_imagen_pendiente);
					}

					if (isset($content->mesa_imagen_ocupada) && $content->mesa_imagen_ocupada != '') {
						$uploadImage->delete($content->mesa_imagen_ocupada);
					}

					if (isset($content->mesa_imagen_ubicacion_en_ambiente) && $content->mesa_imagen_ubicacion_en_ambiente != '') {
						$uploadImage->delete($content->mesa_imagen_ubicacion_en_ambiente);
					}

					if (isset($content->mesa_imagen_ubicacion_en_piso) && $content->mesa_imagen_ubicacion_en_piso != '') {
						$uploadImage->delete($content->mesa_imagen_ubicacion_en_piso);
					}
					$this->mainModel->deleteRegister($id);
					$data = (array) $content;
					$data['log_log'] = print_r($data, true);
					$data['log_tipo'] = 'BORRAR MESA';
					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($data);
				}
			}
		}
		header('Location: ' . $this->route . '' . '');
	}



	/**
	 * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Mesas.
	 *
	 * @return array con toda la informacion recibida del formulario.
	 */
	private function getData()
	{
		$data = array();
		if ($this->_getSanitizedParam("mesa_ambiente") == '') {
			$data['mesa_ambiente'] = '0';
		} else {
			$data['mesa_ambiente'] = $this->_getSanitizedParam("mesa_ambiente");
		}
		$data['mesa_codigo'] = $this->_getSanitizedParam("mesa_codigo");
		$data['mesa_nombre'] = $this->_getSanitizedParam("mesa_nombre");
		if ($this->_getSanitizedParam("mesa_capacidad") == '') {
			$data['mesa_capacidad'] = '0';
		} else {
			$data['mesa_capacidad'] = $this->_getSanitizedParam("mesa_capacidad");
		}
		$data['mesa_forma'] = $this->_getSanitizedParam("mesa_forma");
		if ($this->_getSanitizedParam("mesa_activa") == '') {
			$data['mesa_activa'] = '0';
		} else {
			$data['mesa_activa'] = $this->_getSanitizedParam("mesa_activa");
		}
		$data['mesa_imagen_disponible'] = "";
		$data['mesa_imagen_pendiente'] = "";
		$data['mesa_imagen_ocupada'] = "";
		$data['mesa_imagen_ubicacion_en_ambiente'] = "";
		$data['mesa_imagen_ubicacion_en_piso'] = "";
		$data['mesa_pos_x'] = $this->_getSanitizedParam("mesa_pos_x");
		$data['mesa_pos_y'] = $this->_getSanitizedParam("mesa_pos_y");
		$data['mesa_tipo'] = $this->_getSanitizedParam("mesa_tipo");
		$data['mesa_ancho'] = $this->_getSanitizedParam("mesa_ancho");
		$data['mesa_alto'] = $this->_getSanitizedParam("mesa_alto");
		$data['mesa_rotacion'] = $this->_getSanitizedParam("mesa_rotacion");
		$data['mesa_estado'] = $this->_getSanitizedParam("mesa_estado");
		$data['mesa_provision'] = $this->_getSanitizedParam("mesa_provision");
		return $data;
	}

	/**
	 * Genera los valores del campo Ambiente.
	 *
	 * @return array cadena con los valores del campo Ambiente.
	 */
	private function getMesaambiente()
	{
		$modelData = new Administracion_Model_DbTable_Dependambientes();
		$data = $modelData->getList();
		$array = array();

		$pisos = $this->getAmbientepiso();

		foreach ($data as $key => $value) {
			$array[$value->ambiente_id] = $value->ambiente_nombre . " (" . $pisos[$value->ambiente_piso] . ")";
		}
		return $array;
	}

	private function getAmbientepiso()
	{
		$modelData = new Administracion_Model_DbTable_Dependpisos();
		$data = $modelData->getList();
		$array = array();
		foreach ($data as $key => $value) {
			$array[$value->piso_id] = $value->piso_nombre;
		}
		return $array;
	}

	/**
	 * Genera los valores del campo Forma.
	 *
	 * @return array cadena con los valores del campo Forma.
	 */
	private function getMesaforma()
	{
		$array = array();
		$array['Data'] = 'Data';
		return $array;
	}

	/**
	 * Genera la consulta con los filtros de este controlador.
	 *
	 * @return array cadena con los filtros que se van a asignar a la base de datos
	 */
	protected function getFilter()
	{
		// 		$filtros = " 1 = 1   AND mesa_tipo ='mesa'  AND mesa_activa = '1' ";
		$filtros = " 1 = 1   AND mesa_tipo ='mesa' ";
		if (Session::getInstance()->get($this->namefilter) != "") {
			$filters = (object) Session::getInstance()->get($this->namefilter);
			if ($filters->mesa_ambiente != '') {
				$filtros = $filtros . " AND mesa_ambiente = '" . $filters->mesa_ambiente . "'";
			}
			if ($filters->mesa_codigo != '') {
				$filtros = $filtros . " AND mesa_codigo LIKE '%" . $filters->mesa_codigo . "%'";
			}
			if ($filters->mesa_nombre != '') {
				$filtros = $filtros . " AND mesa_nombre LIKE '%" . $filters->mesa_nombre . "%'";
			}
			if ($filters->mesa_capacidad != '') {
				$filtros = $filtros . " AND mesa_capacidad LIKE '" . $filters->mesa_capacidad . "'";
			}
			if ($filters->mesa_activa != '') {
				$filtros = $filtros . " AND mesa_activa LIKE '" . $filters->mesa_activa . "'";
			}
			if ($filters->mesa_imagen_disponible != '') {
				if ($filters->mesa_imagen_disponible != 2) {
					$filtros = $filtros . " AND mesa_estado LIKE '" . $filters->mesa_imagen_disponible . "'";
				} else {
					$filtros = $filtros . " AND mesa_provision IS NOT NULL  AND mesa_provision <> '' ";
				}
			}
			if ($filters->mesa_imagen_pendiente != '') {
				$filtros = $filtros . " AND mesa_imagen_pendiente LIKE '%" . $filters->mesa_imagen_pendiente . "%'";
			}
			if ($filters->mesa_imagen_ocupada != '') {
				$filtros = $filtros . " AND mesa_imagen_ocupada LIKE '%" . $filters->mesa_imagen_ocupada . "%'";
			}
			if ($filters->mesa_imagen_ubicacion_en_ambiente != '') {
				$filtros = $filtros . " AND mesa_imagen_ubicacion_en_ambiente LIKE '%" . $filters->mesa_imagen_ubicacion_en_ambiente . "%'";
			}
			if ($filters->mesa_imagen_ubicacion_en_piso != '') {
				$filtros = $filtros . " AND mesa_imagen_ubicacion_en_piso LIKE '%" . $filters->mesa_imagen_ubicacion_en_piso . "%'";
			}
			if (isset($filters->mesa_tipo) && $filters->mesa_tipo != '') {
				$filtros = $filtros . " AND mesa_tipo = '" . $filters->mesa_tipo . "'";
			}
			if (isset($filters->mesa_ancho) && $filters->mesa_ancho != '') {
				$filtros = $filtros . " AND mesa_ancho LIKE '%" . $filters->mesa_ancho . "%'";
			}
			if (isset($filters->mesa_alto) && $filters->mesa_alto != '') {
				$filtros = $filtros . " AND mesa_alto LIKE '%" . $filters->mesa_alto . "%'";
			}
			if (isset($filters->mesa_rotacion) && $filters->mesa_rotacion != '') {
				$filtros = $filtros . " AND mesa_rotacion LIKE '%" . $filters->mesa_rotacion . "%'";
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
			$parramsfilter['mesa_ambiente'] = $this->_getSanitizedParam("mesa_ambiente");
			$parramsfilter['mesa_codigo'] = $this->_getSanitizedParam("mesa_codigo");
			$parramsfilter['mesa_nombre'] = $this->_getSanitizedParam("mesa_nombre");
			$parramsfilter['mesa_capacidad'] = $this->_getSanitizedParam("mesa_capacidad");
			$parramsfilter['mesa_activa'] = $this->_getSanitizedParam("mesa_activa");
			$parramsfilter['mesa_imagen_disponible'] = $this->_getSanitizedParam("mesa_imagen_disponible");
			$parramsfilter['mesa_imagen_pendiente'] = $this->_getSanitizedParam("mesa_imagen_pendiente");
			$parramsfilter['mesa_imagen_ocupada'] = $this->_getSanitizedParam("mesa_imagen_ocupada");
			$parramsfilter['mesa_imagen_ubicacion_en_ambiente'] = $this->_getSanitizedParam("mesa_imagen_ubicacion_en_ambiente");
			$parramsfilter['mesa_imagen_ubicacion_en_piso'] = $this->_getSanitizedParam("mesa_imagen_ubicacion_en_piso");
			$parramsfilter['mesa_tipo'] = $this->_getSanitizedParam("mesa_tipo");
			$parramsfilter['mesa_ancho'] = $this->_getSanitizedParam("mesa_ancho");
			$parramsfilter['mesa_alto'] = $this->_getSanitizedParam("mesa_alto");
			$parramsfilter['mesa_rotacion'] = $this->_getSanitizedParam("mesa_rotacion");
			Session::getInstance()->set($this->namefilter, $parramsfilter);
		}
		if ($this->_getSanitizedParam("cleanfilter") == 1) {
			Session::getInstance()->set($this->namefilter, '');
			Session::getInstance()->set($this->namepageactual, 1);
		}
	}
}
