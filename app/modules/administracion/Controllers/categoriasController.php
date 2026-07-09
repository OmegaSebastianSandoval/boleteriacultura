<?php
/**
* Controlador de Categorias que permite la  creacion, edicion  y eliminacion de los categoria del Sistema
*/
class Administracion_categoriasController extends Administracion_mainController
{
      public $botonpanel = 5;

	/**
	 * $mainModel  instancia del modelo de  base de datos categoria
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
	protected $pages ;

	/**
	 * $namefilter nombre de la variable a la fual se le van a guardar los filtros
	 * @var string
	 */
	protected $namefilter;

	/**
	 * $_csrf_section  nombre de la variable general csrf  que se va a almacenar en la session
	 * @var string
	 */
	protected $_csrf_section = "administracion_categorias";

	/**
	 * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
	 * @var string
	 */
	protected $namepages;



	/**
     * Inicializa las variables principales del controlador categorias .
     *
     * @return void.
     */
	public function init()
	{
		$this->mainModel = new Administracion_Model_DbTable_Categorias();
		$this->namefilter = "parametersfiltercategorias";
		$this->route = "/administracion/categorias";
		$this->namepages ="pages_categorias";
		$this->namepageactual ="page_actual_categorias";
		$this->_view->route = $this->route;
		if(Session::getInstance()->get($this->namepages)){
			$this->pages = Session::getInstance()->get($this->namepages);
		} else {
			$this->pages = 20;
		}
		parent::init();
	}


	/**
     * Recibe la informacion y  muestra un listado de  categoria con sus respectivos filtros.
     *
     * @return void.
     */
	public function indexAction()
	{
		$title = "Administraci&oacute;n del precio";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;
		$this->filters();
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$filters =(object)Session::getInstance()->get($this->namefilter);
        $this->_view->filters = $filters;
		$filters = $this->getFilter();
		$order = "orden ASC";
		$list = $this->mainModel->getList($filters,$order);
		$amount = $this->pages;
		$page = $this->_getSanitizedParam("page");
		if (!$page && Session::getInstance()->get($this->namepageactual)) {
		   	$page = Session::getInstance()->get($this->namepageactual);
		   	$start = ($page - 1) * $amount;
		} else if(!$page){
			$start = 0;
		   	$page=1;
			Session::getInstance()->set($this->namepageactual,$page);
		} else {
			Session::getInstance()->set($this->namepageactual,$page);
		   	$start = ($page - 1) * $amount;
		}
		$this->_view->register_number = count($list);
		$this->_view->pages = $this->pages;
		$this->_view->totalpages = ceil(count($list)/$amount);
		$this->_view->page = $page;
		$this->_view->lists = $this->mainModel->getListPages($filters,$order,$start,$amount);
		$this->_view->csrf_section = $this->_csrf_section;
	}

	/**
     * Genera la Informacion necesaria para editar o crear un  categoria  y muestra su formulario
     *
     * @return void.
     */
	public function manageAction()
	{
		$this->_view->route = $this->route;
		$this->_csrf_section = "manage_categorias_".date("YmdHis");
		$this->_csrf->generateCode($this->_csrf_section);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$id = $this->_getSanitizedParam("id");
		if ($id > 0) {
			$content = $this->mainModel->getById($id);
			if($content->categoria_id){
				$this->_view->content = $content;
				$this->_view->routeform = $this->route."/update";
				$title = "Actualizar categoria";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			}else{
				$this->_view->routeform = $this->route."/insert";
				$title = "Crear categoria";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			}
		} else {
			$this->_view->routeform = $this->route."/insert";
			$title = "Crear categoria";
			$this->getLayout()->setTitle($title);
			$this->_view->titlesection = $title;
		}
	}

	/**
     * Inserta la informacion de un categoria  y redirecciona al listado de categoria.
     *
     * @return void.
     */
	public function insertAction(){
		$this->setLayout('blanco');
		$csrf = $this->_getSanitizedParam("csrf");
		if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf ) {	
			$data = $this->getData();
			$id = $this->mainModel->insert($data);
			$this->mainModel->changeOrder($id,$id);
			$data['categoria_id']= $id;
			$data['log_log'] = print_r($data,true);
			$data['log_tipo'] = 'CREAR CATEGORIA';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: '.$this->route.''.'');
	}

	/**
     * Recibe un identificador  y Actualiza la informacion de un categoria  y redirecciona al listado de categoria.
     *
     * @return void.
     */
	public function updateAction(){
		$this->setLayout('blanco');
		$csrf = $this->_getSanitizedParam("csrf");
		if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf ) {
			$id = $this->_getSanitizedParam("id");
			$content = $this->mainModel->getById($id);
			if ($content->categoria_id) {
				$data = $this->getData();
					$this->mainModel->update($data,$id);
			}
			$data['categoria_id']=$id;
			$data['log_log'] = print_r($data,true);
			$data['log_tipo'] = 'EDITAR CATEGORIA';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);}
		header('Location: '.$this->route.''.'');
	}

	/**
     * Recibe un identificador  y elimina un categoria  y redirecciona al listado de categoria.
     *
     * @return void.
     */
	public function deleteAction()
	{
		$this->setLayout('blanco');
		$csrf = $this->_getSanitizedParam("csrf");
		if (Session::getInstance()->get('csrf')[$this->_csrf_section] == $csrf ) {
			$id =  $this->_getSanitizedParam("id");
			if (isset($id) && $id > 0) {
				$content = $this->mainModel->getById($id);
				if (isset($content)) {
					$this->mainModel->deleteRegister($id);$data = (array)$content;
					$data['log_log'] = print_r($data,true);
					$data['log_tipo'] = 'BORRAR CATEGORIA';
					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($data); }
			}
		}
		header('Location: '.$this->route.''.'');
	}

	/**
     * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Categorias.
     *
     * @return array con toda la informacion recibida del formulario.
     */
	private function getData()
	{
		$data = array();
		$data['categoria_nombre'] = $this->_getSanitizedParam("categoria_nombre");
		$data['categoria_descripcion'] = $this->_getSanitizedParam("categoria_descripcion");
		if($this->_getSanitizedParam("categoria_estado") == '' ) {
			$data['categoria_estado'] = '0';
		} else {
			$data['categoria_estado'] = $this->_getSanitizedParam("categoria_estado");
		}
		$data['categoria_precio'] = $this->_getSanitizedParam("categoria_precio");
		$data['categoria_precio_socio'] = $this->_getSanitizedParam("categoria_precio_socio");
		$data['categoria_precio_socio_hijo'] = $this->_getSanitizedParam("categoria_precio_socio_hijo");
		$data['categoria_precio_invitado'] = $this->_getSanitizedParam("categoria_precio_invitado");
		$data['categoria_precio_invitado_socio'] = $this->_getSanitizedParam("categoria_precio_invitado_socio");
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
        if (Session::getInstance()->get($this->namefilter)!="") {
            $filters =(object)Session::getInstance()->get($this->namefilter);
            if ($filters->categoria_nombre != '') {
                $filtros = $filtros." AND categoria_nombre LIKE '%".$filters->categoria_nombre."%'";
            }
            if ($filters->categoria_estado != '') {
                $filtros = $filtros." AND categoria_estado LIKE '%".$filters->categoria_estado."%'";
            }
            if ($filters->categoria_precio_socio != '') {
                $filtros = $filtros." AND categoria_precio_socio LIKE '%".$filters->categoria_precio_socio."%'";
            }
            if ($filters->categoria_precio_socio_hijo != '') {
                $filtros = $filtros." AND categoria_precio_socio_hijo LIKE '%".$filters->categoria_precio_socio_hijo."%'";
            }
            if ($filters->categoria_precio_invitado != '') {
                $filtros = $filtros." AND categoria_precio_invitado LIKE '%".$filters->categoria_precio_invitado."%'";
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
        if ($this->getRequest()->isPost()== true) {
        	Session::getInstance()->set($this->namepageactual,1);
            $parramsfilter = array();
					$parramsfilter['categoria_nombre'] =  $this->_getSanitizedParam("categoria_nombre");
					$parramsfilter['categoria_estado'] =  $this->_getSanitizedParam("categoria_estado");
					$parramsfilter['categoria_precio_socio'] =  $this->_getSanitizedParam("categoria_precio_socio");
					$parramsfilter['categoria_precio_socio_hijo'] =  $this->_getSanitizedParam("categoria_precio_socio_hijo");
					$parramsfilter['categoria_precio_invitado'] =  $this->_getSanitizedParam("categoria_precio_invitado");Session::getInstance()->set($this->namefilter, $parramsfilter);
        }
        if ($this->_getSanitizedParam("cleanfilter") == 1) {
            Session::getInstance()->set($this->namefilter, '');
            Session::getInstance()->set($this->namepageactual,1);
        }
    }
}