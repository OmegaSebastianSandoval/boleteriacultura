<?php

/**
 * Controlador de Auditoriaboleta que permite la  creacion, edicion  y eliminacion de los auditoriaboleta del Sistema
 */
class Administracion_auditoriaboletaController extends Administracion_mainController
{
	/**
	 * $mainModel  instancia del modelo de  base de datos auditoriaboleta
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
	protected $_csrf_section = "administracion_auditoriaboleta";

	/**
	 * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
	 * @var string
	 */
	protected $namepages;



	/**
	 * Inicializa las variables principales del controlador auditoriaboleta .
	 *
	 * @return void.
	 */
	public function init()
	{
		$this->mainModel = new Administracion_Model_DbTable_Auditoriaboleta();
		$this->namefilter = "parametersfilterauditoriaboleta";
		$this->route = "/administracion/auditoriaboleta";
		$this->namepages = "pages_auditoriaboleta";
		$this->namepageactual = "page_actual_auditoriaboleta";
		$this->_view->route = $this->route;
		if (Session::getInstance()->get($this->namepages)) {
			$this->pages = Session::getInstance()->get($this->namepages);
		} else {
			$this->pages = 20;
		}
		parent::init();
	}


	/**
	 * Recibe la informacion y  muestra un listado de  auditoriaboleta con sus respectivos filtros.
	 *
	 * @return void.
	 */
	public function indexAction()
	{
		$title = "Administración de auditoriaboleta";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;
		$this->filters();
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$filters = (object)Session::getInstance()->get($this->namefilter);
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
		$this->_view->lists = $this->mainModel->getListPages($filters, $order, $start, $amount);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->list_auditoriaboleta_boleta_id = $this->getAuditoriaboletaboletaid();
		$this->_view->list_auditoriaboleta_boleta_reserva_id = $this->getAuditoriaboletaboletareservaid();
		$this->_view->list_auditoriaboleta_usuario_validador_id = $this->getAuditoriaboletausuariovalidadorid();
		$this->_view->list_auditoriaboleta_usuario_validador_nombre = $this->getAuditoriaboletausuariovalidadornombre();
	}

	/**
	 * Genera la Informacion necesaria para editar o crear un  auditoriaboleta  y muestra su formulario
	 *
	 * @return void.
	 */
	public function manageAction()
	{
		$this->_view->route = $this->route;
		$this->_csrf_section = "manage_auditoriaboleta_" . date("YmdHis");
		$this->_csrf->generateCode($this->_csrf_section);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$this->_view->list_auditoriaboleta_boleta_id = $this->getAuditoriaboletaboletaid();
		$this->_view->list_auditoriaboleta_boleta_reserva_id = $this->getAuditoriaboletaboletareservaid();
		$this->_view->list_auditoriaboleta_usuario_validador_id = $this->getAuditoriaboletausuariovalidadorid();
		$this->_view->list_auditoriaboleta_usuario_validador_nombre = $this->getAuditoriaboletausuariovalidadornombre();
		$this->_view->list_auditoriaboleta_resultado = $this->getAuditoriaboletaresultado();
		$this->_view->list_auditoriaboleta_metodo_escaneado = $this->getAuditoriaboletametodoescaneado();
		$id = $this->_getSanitizedParam("id");
		if ($id > 0) {
			$content = $this->mainModel->getById($id);
			if ($content->auditoriaboleta_id) {
				$this->_view->content = $content;
				$this->_view->routeform = $this->route . "/update";
				$title = "Actualizar auditoriaboleta";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			} else {
				$this->_view->routeform = $this->route . "/insert";
				$title = "Crear auditoriaboleta";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			}
		} else {
			$this->_view->routeform = $this->route . "/insert";
			$title = "Crear auditoriaboleta";
			$this->getLayout()->setTitle($title);
			$this->_view->titlesection = $title;
		}
	}

	/**
	 * Inserta la informacion de un auditoriaboleta  y redirecciona al listado de auditoriaboleta.
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

			$data['auditoriaboleta_id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'CREAR AUDITORIABOLETA';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y Actualiza la informacion de un auditoriaboleta  y redirecciona al listado de auditoriaboleta.
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
			if ($content->auditoriaboleta_id) {
				$data = $this->getData();
				$this->mainModel->update($data, $id);
			}
			$data['auditoriaboleta_id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'EDITAR AUDITORIABOLETA';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y elimina un auditoriaboleta  y redirecciona al listado de auditoriaboleta.
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
					$data['log_tipo'] = 'BORRAR AUDITORIABOLETA';
					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($data);
				}
			}
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Auditoriaboleta.
	 *
	 * @return array con toda la informacion recibida del formulario.
	 */
	private function getData()
	{
		$data = array();
		$data['auditoriaboleta_boleta_uid'] = $this->_getSanitizedParam("auditoriaboleta_boleta_uid");
		$data['auditoriaboleta_boleta_token'] = $this->_getSanitizedParam("auditoriaboleta_boleta_token");
		$data['auditoriaboleta_documento_escaneado'] = $this->_getSanitizedParam("auditoriaboleta_documento_escaneado");
		if ($this->_getSanitizedParam("auditoriaboleta_boleta_id") == '') {
			$data['auditoriaboleta_boleta_id'] = '0';
		} else {
			$data['auditoriaboleta_boleta_id'] = $this->_getSanitizedParam("auditoriaboleta_boleta_id");
		}
		if ($this->_getSanitizedParam("auditoriaboleta_boleta_reserva_id") == '') {
			$data['auditoriaboleta_boleta_reserva_id'] = '0';
		} else {
			$data['auditoriaboleta_boleta_reserva_id'] = $this->_getSanitizedParam("auditoriaboleta_boleta_reserva_id");
		}
		if ($this->_getSanitizedParam("auditoriaboleta_boleta_evento_id") == '') {
			$data['auditoriaboleta_boleta_evento_id'] = '0';
		} else {
			$data['auditoriaboleta_boleta_evento_id'] = $this->_getSanitizedParam("auditoriaboleta_boleta_evento_id");
		}
		if ($this->_getSanitizedParam("auditoriaboleta_boleta_mesa") == '') {
			$data['auditoriaboleta_boleta_mesa'] = '0';
		} else {
			$data['auditoriaboleta_boleta_mesa'] = $this->_getSanitizedParam("auditoriaboleta_boleta_mesa");
		}
		$data['auditoriaboleta_boleta_numero_ticket'] = $this->_getSanitizedParam("auditoriaboleta_boleta_numero_ticket");
		if ($this->_getSanitizedParam("auditoriaboleta_usuario_validador_id") == '') {
			$data['auditoriaboleta_usuario_validador_id'] = '0';
		} else {
			$data['auditoriaboleta_usuario_validador_id'] = $this->_getSanitizedParam("auditoriaboleta_usuario_validador_id");
		}
		$data['auditoriaboleta_usuario_validador_nombre'] = $this->_getSanitizedParam("auditoriaboleta_usuario_validador_nombre");
		$data['auditoriaboleta_numero_carnet'] = $this->_getSanitizedParam("auditoriaboleta_numero_carnet");
		$data['auditoriaboleta_accion'] = $this->_getSanitizedParam("auditoriaboleta_accion");
		$data['auditoriaboleta_resultado'] = $this->_getSanitizedParam("auditoriaboleta_resultado");
		$data['auditoriaboleta_motivo_fallo'] = $this->_getSanitizedParam("auditoriaboleta_motivo_fallo");
		$data['auditoriaboleta_metodo_escaneado'] = $this->_getSanitizedParam("auditoriaboleta_metodo_escaneado");
		$data['auditoriaboleta_ip_address'] = $this->_getSanitizedParam("auditoriaboleta_ip_address");
		$data['auditoriaboleta_user_agent'] = $this->_getSanitizedParam("auditoriaboleta_user_agent");
		$data['auditoriaboleta_dispositivo_info'] = $this->_getSanitizedParam("auditoriaboleta_dispositivo_info");
		$data['auditoriaboleta_url_completa'] = $this->_getSanitizedParam("auditoriaboleta_url_completa");
		$data['auditoriaboleta_referer'] = $this->_getSanitizedParam("auditoriaboleta_referer");
		$data['auditoriaboleta_parametros_get'] = $this->_getSanitizedParam("auditoriaboleta_parametros_get");
		$data['auditoriaboleta_parametros_post'] = $this->_getSanitizedParam("auditoriaboleta_parametros_post");
		$data['auditoriaboleta_fecha_hora'] = $this->_getSanitizedParam("auditoriaboleta_fecha_hora");
		if ($this->_getSanitizedParam("auditoriaboleta_timestamp_unix") == '') {
			$data['auditoriaboleta_timestamp_unix'] = '0';
		} else {
			$data['auditoriaboleta_timestamp_unix'] = $this->_getSanitizedParam("auditoriaboleta_timestamp_unix");
		}
		$data['auditoriaboleta_datos_boleta_antes'] = $this->_getSanitizedParam("auditoriaboleta_datos_boleta_antes");
		$data['auditoriaboleta_datos_boleta_despues'] = $this->_getSanitizedParam("auditoriaboleta_datos_boleta_despues");
		$data['auditoriaboleta_datos_reserva'] = $this->_getSanitizedParam("auditoriaboleta_datos_reserva");
		$data['auditoriaboleta_datos_sesion'] = $this->_getSanitizedParam("auditoriaboleta_datos_sesion");
		$data['auditoriaboleta_observaciones'] = $this->_getSanitizedParam("auditoriaboleta_observaciones");
		return $data;
	}

	/**
	 * Genera los valores del campo auditoriaboleta_boleta_id.
	 *
	 * @return array cadena con los valores del campo auditoriaboleta_boleta_id.
	 */
	private function getAuditoriaboletaboletaid()
	{
		$modelData = new Administracion_Model_DbTable_Dependboletasinfo();
		$data = $modelData->getList();
		$array = array();
		foreach ($data as $key => $value) {
			$array[$value->boleta_id] = $value->boleta_id;
		}
		return $array;
	}


	/**
	 * Genera los valores del campo auditoriaboleta_boleta_reserva_id.
	 *
	 * @return array cadena con los valores del campo auditoriaboleta_boleta_reserva_id.
	 */
	private function getAuditoriaboletaboletareservaid()
	{
		$modelData = new Administracion_Model_DbTable_Dependreservas();
		$data = $modelData->getList();
		$array = array();
		foreach ($data as $key => $value) {
			$array[$value->id] = $value->id;
		}
		return $array;
	}


	/**
	 * Genera los valores del campo auditoriaboleta_usuario_validador_id.
	 *
	 * @return array cadena con los valores del campo auditoriaboleta_usuario_validador_id.
	 */
	private function getAuditoriaboletausuariovalidadorid()
	{
		$modelData = new Administracion_Model_DbTable_Dependuser();
		$data = $modelData->getList();
		$array = array();
		foreach ($data as $key => $value) {
			$array[$value->user_id] = $value->user_user;
		}
		return $array;
	}


	/**
	 * Genera los valores del campo auditoriaboleta_usuario_validador_nombre.
	 *
	 * @return array cadena con los valores del campo auditoriaboleta_usuario_validador_nombre.
	 */
	private function getAuditoriaboletausuariovalidadornombre()
	{
		$modelData = new Administracion_Model_DbTable_Dependuser();
		$data = $modelData->getList();
		$array = array();
		foreach ($data as $key => $value) {
			$array[$value->user_user] = $value->user_user;
		}
		return $array;
	}


	/**
	 * Genera los valores del campo auditoriaboleta_resultado.
	 *
	 * @return array cadena con los valores del campo auditoriaboleta_resultado.
	 */
	private function getAuditoriaboletaresultado()
	{
		$array = array();
		$array['Data'] = 'Data';
		return $array;
	}


	/**
	 * Genera los valores del campo auditoriaboleta_metodo_escaneado.
	 *
	 * @return array cadena con los valores del campo auditoriaboleta_metodo_escaneado.
	 */
	private function getAuditoriaboletametodoescaneado()
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
		$filtros = " 1 = 1 ";
		if (Session::getInstance()->get($this->namefilter) != "") {
			$filters = (object)Session::getInstance()->get($this->namefilter);
			if ($filters->auditoriaboleta_boleta_uid != '') {
				$filtros = $filtros . " AND auditoriaboleta_boleta_uid LIKE '%" . $filters->auditoriaboleta_boleta_uid . "%'";
			}
			if ($filters->auditoriaboleta_boleta_token != '') {
				$filtros = $filtros . " AND auditoriaboleta_boleta_token LIKE '%" . $filters->auditoriaboleta_boleta_token . "%'";
			}
			if ($filters->auditoriaboleta_documento_escaneado != '') {
				$filtros = $filtros . " AND auditoriaboleta_documento_escaneado LIKE '%" . $filters->auditoriaboleta_documento_escaneado . "%'";
			}
			if ($filters->auditoriaboleta_boleta_id != '') {
				$filtros = $filtros . " AND auditoriaboleta_boleta_id LIKE '%" . $filters->auditoriaboleta_boleta_id . "%'";
			}
			if ($filters->auditoriaboleta_boleta_reserva_id != '') {
				$filtros = $filtros . " AND auditoriaboleta_boleta_reserva_id LIKE '%" . $filters->auditoriaboleta_boleta_reserva_id . "%'";
			}
			if ($filters->auditoriaboleta_usuario_validador_id != '') {
				$filtros = $filtros . " AND auditoriaboleta_usuario_validador_id LIKE '%" . $filters->auditoriaboleta_usuario_validador_id . "%'";
			}
			if ($filters->auditoriaboleta_usuario_validador_nombre != '') {
				$filtros = $filtros . " AND auditoriaboleta_usuario_validador_nombre LIKE '%" . $filters->auditoriaboleta_usuario_validador_nombre . "%'";
			}
			if ($filters->auditoriaboleta_numero_carnet != '') {
				$filtros = $filtros . " AND auditoriaboleta_numero_carnet LIKE '%" . $filters->auditoriaboleta_numero_carnet . "%'";
			}
			if ($filters->auditoriaboleta_accion != '') {
				$filtros = $filtros . " AND auditoriaboleta_accion LIKE '%" . $filters->auditoriaboleta_accion . "%'";
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
			$parramsfilter['auditoriaboleta_boleta_uid'] =  $this->_getSanitizedParam("auditoriaboleta_boleta_uid");
			$parramsfilter['auditoriaboleta_boleta_token'] =  $this->_getSanitizedParam("auditoriaboleta_boleta_token");
			$parramsfilter['auditoriaboleta_documento_escaneado'] =  $this->_getSanitizedParam("auditoriaboleta_documento_escaneado");
			$parramsfilter['auditoriaboleta_boleta_id'] =  $this->_getSanitizedParam("auditoriaboleta_boleta_id");
			$parramsfilter['auditoriaboleta_boleta_reserva_id'] =  $this->_getSanitizedParam("auditoriaboleta_boleta_reserva_id");
			$parramsfilter['auditoriaboleta_usuario_validador_id'] =  $this->_getSanitizedParam("auditoriaboleta_usuario_validador_id");
			$parramsfilter['auditoriaboleta_usuario_validador_nombre'] =  $this->_getSanitizedParam("auditoriaboleta_usuario_validador_nombre");
			$parramsfilter['auditoriaboleta_numero_carnet'] =  $this->_getSanitizedParam("auditoriaboleta_numero_carnet");
			$parramsfilter['auditoriaboleta_accion'] =  $this->_getSanitizedParam("auditoriaboleta_accion");
			Session::getInstance()->set($this->namefilter, $parramsfilter);
		}
		if ($this->_getSanitizedParam("cleanfilter") == 1) {
			Session::getInstance()->set($this->namefilter, '');
			Session::getInstance()->set($this->namepageactual, 1);
		}
	}
}
