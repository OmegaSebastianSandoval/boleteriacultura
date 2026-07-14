<?php

/**
 * Controlador de Ambientes que permite la  creacion, edicion  y eliminacion de los ambiente del Sistema
 */
class Administracion_ambientesController extends Administracion_mainController
{
	public $botonpanel = 7;

	/**
	 * $mainModel  instancia del modelo de  base de datos ambiente
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
	protected $_csrf_section = "administracion_ambientes";

	/**
	 * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
	 * @var string
	 */
	protected $namepages;



	/**
	 * Inicializa las variables principales del controlador ambientes .
	 *
	 * @return void.
	 */
	public function init()
	{
		$this->mainModel = new Administracion_Model_DbTable_Ambientes();
		$this->namefilter = "parametersfilterambientes";
		$this->route = "/administracion/ambientes";
		$this->namepages = "pages_ambientes";
		$this->namepageactual = "page_actual_ambientes";
		$this->_view->route = $this->route;
		if (Session::getInstance()->get($this->namepages)) {
			$this->pages = Session::getInstance()->get($this->namepages);
		} else {
			$this->pages = 20;
		}
		parent::init();
	}


	/**
	 * Recibe la informacion y  muestra un listado de  ambiente con sus respectivos filtros.
	 *
	 * @return void.
	 */
	public function indexAction()
	{
		$title = "Administración de ambientes";
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
		$this->_view->list_ambiente_piso = $this->getAmbientepiso();
		$this->_view->list_ambiente_categoria = $this->getAmbientecategoria();
	}

	/**
	 * Genera la Informacion necesaria para editar o crear un  ambiente  y muestra su formulario
	 *
	 * @return void.
	 */
	public function manageAction()
	{
		$this->_view->route = $this->route;

		$this->_view->successEliminarReserva = Session::getInstance()->get('success_eliminar_reserva');
		$this->_view->errorEliminarReserva = Session::getInstance()->get('error_eliminar_reserva');
		Session::getInstance()->set('success_eliminar_reserva', '');
		Session::getInstance()->set('error_eliminar_reserva', '');

		$this->_csrf_section = "manage_ambientes_" . date("YmdHis");
		$this->_csrf->generateCode($this->_csrf_section);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$this->_view->list_ambiente_piso = $this->getAmbientepiso();
		$this->_view->list_ambiente_categoria = $this->getAmbientecategoria();
		$id = $this->_getSanitizedParam("id");
		if ($id > 0) {
			$content = $this->mainModel->getById($id);
			if ($content->ambiente_id) {
				$this->_view->content = $content;
				$this->_view->routeform = $this->route . "/update";
				$title = "Ambiente interactivo";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
				$mesasModel = new Administracion_Model_DbTable_Mesas();
				$mesas = $mesasModel->getList("mesa_ambiente = '{$content->ambiente_id}' AND mesa_tipo LIKE '%mesa%'");

				$this->_view->mesasModal = $mesas;

				$mesas = $mesasModel->getList("mesa_ambiente = '{$content->ambiente_id}' ");
				if ($mesas && count($mesas) > 0 && is_countable($mesas)) {
					foreach ($mesas as &$mesa) {
						$mesa->mesa_id = (int) $mesa->mesa_id;
						$mesa->mesa_ambiente = (int) $mesa->mesa_ambiente;
						$mesa->mesa_codigo = is_numeric($mesa->mesa_codigo) ? (int) $mesa->mesa_codigo : $mesa->mesa_codigo;
						$mesa->mesa_capacidad = (int) $mesa->mesa_capacidad;
						$mesa->mesa_estado = (int) $mesa->mesa_estado;
						$mesa->mesa_activa = (int) $mesa->mesa_activa;
						$mesa->orden = (int) $mesa->orden;
						$mesa->mesa_pos_x = (int) $mesa->mesa_pos_x;
						$mesa->mesa_pos_y = (int) $mesa->mesa_pos_y;
						$mesa->mesa_ancho = (int) $mesa->mesa_ancho;
						$mesa->mesa_alto = (int) $mesa->mesa_alto;
						$mesa->mesa_rotacion = (int) $mesa->mesa_rotacion;
					}
				}
				$this->_view->mesas = $mesas;

				// print_r($this->_view->mesas);
			} else {
				$this->_view->routeform = $this->route . "/insert";
				$title = "Crear ambiente";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			}
		} else {
			$this->_view->routeform = $this->route . "/insert";
			$title = "Crear ambiente";
			$this->getLayout()->setTitle($title);
			$this->_view->titlesection = $title;
		}
	}

	/**
	 * Inserta la informacion de un ambiente  y redirecciona al listado de ambiente.
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
			if ($_FILES['ambiente_imagen_disponible']['name'] != '') {
				$data['ambiente_imagen_disponible'] = $uploadImage->upload("ambiente_imagen_disponible");
			}
			if ($_FILES['ambiente_imagen_pendiente']['name'] != '') {
				$data['ambiente_imagen_pendiente'] = $uploadImage->upload("ambiente_imagen_pendiente");
			}
			if ($_FILES['ambiente_imagen_ocupado']['name'] != '') {
				$data['ambiente_imagen_ocupado'] = $uploadImage->upload("ambiente_imagen_ocupado");
			}
			if ($_FILES['ambiente_imagen_ubicacion_en_piso']['name'] != '') {
				$data['ambiente_imagen_ubicacion_en_piso'] = $uploadImage->upload("ambiente_imagen_ubicacion_en_piso");
			}
			$id = $this->mainModel->insert($data);
			$this->mainModel->changeOrder($id, $id);
			$data['ambiente_id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'CREAR AMBIENTE';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y Actualiza la informacion de un ambiente  y redirecciona al listado de ambiente.
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
			if ($content->ambiente_id) {
				$data = $this->getData();
				$uploadImage = new Core_Model_Upload_Image();
				if ($_FILES['ambiente_imagen_disponible']['name'] != '') {
					if ($content->ambiente_imagen_disponible) {
						$uploadImage->delete($content->ambiente_imagen_disponible);
					}
					$data['ambiente_imagen_disponible'] = $uploadImage->upload("ambiente_imagen_disponible");
				} else {
					$data['ambiente_imagen_disponible'] = $content->ambiente_imagen_disponible;
				}

				if ($_FILES['ambiente_imagen_pendiente']['name'] != '') {
					if ($content->ambiente_imagen_pendiente) {
						$uploadImage->delete($content->ambiente_imagen_pendiente);
					}
					$data['ambiente_imagen_pendiente'] = $uploadImage->upload("ambiente_imagen_pendiente");
				} else {
					$data['ambiente_imagen_pendiente'] = $content->ambiente_imagen_pendiente;
				}

				if ($_FILES['ambiente_imagen_ocupado']['name'] != '') {
					if ($content->ambiente_imagen_ocupado) {
						$uploadImage->delete($content->ambiente_imagen_ocupado);
					}
					$data['ambiente_imagen_ocupado'] = $uploadImage->upload("ambiente_imagen_ocupado");
				} else {
					$data['ambiente_imagen_ocupado'] = $content->ambiente_imagen_ocupado;
				}

				if ($_FILES['ambiente_imagen_ubicacion_en_piso']['name'] != '') {
					if ($content->ambiente_imagen_ubicacion_en_piso) {
						$uploadImage->delete($content->ambiente_imagen_ubicacion_en_piso);
					}
					$data['ambiente_imagen_ubicacion_en_piso'] = $uploadImage->upload("ambiente_imagen_ubicacion_en_piso");
				} else {
					$data['ambiente_imagen_ubicacion_en_piso'] = $content->ambiente_imagen_ubicacion_en_piso;
				}
				$this->mainModel->update($data, $id);
			}
			$data['ambiente_id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'EDITAR AMBIENTE';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '/manage?id=' . $id . '&saved=1');
	}

	/**
	 * Recibe un identificador  y elimina un ambiente  y redirecciona al listado de ambiente.
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
					if (isset($content->ambiente_imagen_disponible) && $content->ambiente_imagen_disponible != '') {
						$uploadImage->delete($content->ambiente_imagen_disponible);
					}

					if (isset($content->ambiente_imagen_pendiente) && $content->ambiente_imagen_pendiente != '') {
						$uploadImage->delete($content->ambiente_imagen_pendiente);
					}

					if (isset($content->ambiente_imagen_ocupado) && $content->ambiente_imagen_ocupado != '') {
						$uploadImage->delete($content->ambiente_imagen_ocupado);
					}

					if (isset($content->ambiente_imagen_ubicacion_en_piso) && $content->ambiente_imagen_ubicacion_en_piso != '') {
						$uploadImage->delete($content->ambiente_imagen_ubicacion_en_piso);
					}
					// 	$this->mainModel->deleteRegister($id);
					$data = (array) $content;
					$data['log_log'] = print_r($data, true);
					$data['log_tipo'] = 'BORRAR AMBIENTE';
					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($data);
				}
			}
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Ambientes.
	 *
	 * @return array con toda la informacion recibida del formulario.
	 */
	private function getData()
	{
		$data = array();
		if ($this->_getSanitizedParam("ambiente_evento") == '') {
			$data['ambiente_evento'] = '0';
		} else {
			$data['ambiente_evento'] = $this->_getSanitizedParam("ambiente_evento");
		}
		if ($this->_getSanitizedParam("ambiente_piso") == '') {
			$data['ambiente_piso'] = '0';
		} else {
			$data['ambiente_piso'] = $this->_getSanitizedParam("ambiente_piso");
		}
		$data['ambiente_nombre'] = $this->_getSanitizedParam("ambiente_nombre");
		if ($this->_getSanitizedParam("ambiente_capacidad") == '') {
			$data['ambiente_capacidad'] = '0';
		} else {
			$data['ambiente_capacidad'] = $this->_getSanitizedParam("ambiente_capacidad");
		}
		$data['ambiente_categoria'] = $this->_getSanitizedParam("ambiente_categoria");
		if ($this->_getSanitizedParam("ambiente_estado") == '') {
			$data['ambiente_estado'] = '0';
		} else {
			$data['ambiente_estado'] = $this->_getSanitizedParam("ambiente_estado");
		}
		$data['ambiente_imagen_disponible'] = "";
		$data['ambiente_imagen_pendiente'] = "";
		$data['ambiente_imagen_ocupado'] = "";
		$data['ambiente_imagen_ubicacion_en_piso'] = "";
		if ($this->_getSanitizedParam("ambiente_filas") == '') {
			$data['ambiente_filas'] = '0';
		} else {
			$data['ambiente_filas'] = $this->_getSanitizedParam("ambiente_filas");
		}
		if ($this->_getSanitizedParam("ambiente_columnas") == '') {
			$data['ambiente_columnas'] = '0';
		} else {
			$data['ambiente_columnas'] = $this->_getSanitizedParam("ambiente_columnas");
		}
		$data['ambiente_descuento'] = $this->_getSanitizedParam("ambiente_descuento");

		$fechaPartido = $this->_getSanitizedParam("ambiente_fecha_partido");
		$data['ambiente_fecha_partido'] = $fechaPartido ? str_replace('T', ' ', $fechaPartido) . ':00' : null;

		$precioSilla = $this->_getSanitizedParam("ambiente_precio_silla");
		$data['ambiente_precio_silla'] = ($precioSilla === '') ? null : $precioSilla;

		return $data;
	}

	/**
	 * Genera los valores del campo Piso.
	 *
	 * @return array cadena con los valores del campo Piso.
	 */
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
	 * Genera los valores del campo Categoria.
	 *
	 * @return array cadena con los valores del campo Categoria.
	 */
	private function getAmbientecategoria()
	{
		$modelData = new Administracion_Model_DbTable_Dependcategorias();
		$data = $modelData->getList();
		$array = array();
		foreach ($data as $key => $value) {
			$array[$value->categoria_id] = $value->categoria_nombre;
		}
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
			$filters = (object) Session::getInstance()->get($this->namefilter);
			if ($filters->ambiente_piso != '') {
				$filtros = $filtros . " AND ambiente_piso LIKE '%" . $filters->ambiente_piso . "%'";
			}
			if ($filters->ambiente_nombre != '') {
				$filtros = $filtros . " AND ambiente_nombre LIKE '%" . $filters->ambiente_nombre . "%'";
			}
			if ($filters->ambiente_capacidad != '') {
				$filtros = $filtros . " AND ambiente_capacidad LIKE '%" . $filters->ambiente_capacidad . "%'";
			}
			if ($filters->ambiente_categoria != '') {
				$filtros = $filtros . " AND ambiente_categoria LIKE '%" . $filters->ambiente_categoria . "%'";
			}
			if ($filters->ambiente_estado != '') {
				$filtros = $filtros . " AND ambiente_estado LIKE '%" . $filters->ambiente_estado . "%'";
			}
			if ($filters->ambiente_imagen_disponible != '') {
				$filtros = $filtros . " AND ambiente_imagen_disponible LIKE '%" . $filters->ambiente_imagen_disponible . "%'";
			}
			if ($filters->ambiente_imagen_pendiente != '') {
				$filtros = $filtros . " AND ambiente_imagen_pendiente LIKE '%" . $filters->ambiente_imagen_pendiente . "%'";
			}
			if ($filters->ambiente_imagen_ocupado != '') {
				$filtros = $filtros . " AND ambiente_imagen_ocupado LIKE '%" . $filters->ambiente_imagen_ocupado . "%'";
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
			$parramsfilter['ambiente_piso'] = $this->_getSanitizedParam("ambiente_piso");
			$parramsfilter['ambiente_nombre'] = $this->_getSanitizedParam("ambiente_nombre");
			$parramsfilter['ambiente_capacidad'] = $this->_getSanitizedParam("ambiente_capacidad");
			$parramsfilter['ambiente_categoria'] = $this->_getSanitizedParam("ambiente_categoria");
			$parramsfilter['ambiente_estado'] = $this->_getSanitizedParam("ambiente_estado");
			$parramsfilter['ambiente_imagen_disponible'] = $this->_getSanitizedParam("ambiente_imagen_disponible");
			$parramsfilter['ambiente_imagen_pendiente'] = $this->_getSanitizedParam("ambiente_imagen_pendiente");
			$parramsfilter['ambiente_imagen_ocupado'] = $this->_getSanitizedParam("ambiente_imagen_ocupado");
			Session::getInstance()->set($this->namefilter, $parramsfilter);
		}
		if ($this->_getSanitizedParam("cleanfilter") == 1) {
			Session::getInstance()->set($this->namefilter, '');
			Session::getInstance()->set($this->namepageactual, 1);
		}
	}

	public function consultaMesaAction()
	{
		$this->setLayout('blanco');

		$codigoMesa = $this->_getSanitizedParam("mesa_codigo");
		$mesasModel = new Administracion_Model_DbTable_Mesas();
		$reservasModel = new Administracion_Model_DbTable_Reservas();
		$invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();

		$lista = $mesasModel->getById($codigoMesa);
		$reserva = $reservasModel->getList(" FIND_IN_SET($codigoMesa, reserva_mesa_id) ", "");
		$reserva = !empty($reserva) ? $reserva[0] : null;
		$invitados = $reserva ? $invitadosModel->getList(" reserva_id_reserva = '$reserva->id' ", "") : [];

		$cuposPendientes = null;
		if ($reserva) {
			$cuposModel = new Administracion_Model_DbTable_Reservacuposadicionales();
			$pendientes = $cuposModel->getList("reserva_id = '{$reserva->id}' AND cupos_estado = 0", "id DESC");
			$cuposPendientes = !empty($pendientes) ? $pendientes[0] : null;
		}

		$respuesta = [
			"mesa" => $lista,
			"reserva" => $reserva,
			"invitados" => $invitados,
			// mesa_provision = '0' significa "es provisional"; NULL/vacío significa que no lo es.
			// OJO: NO usar comprobación truthy aquí, porque en PHP la cadena "0" es falsy.
			"es_provisional" => $lista->mesa_provision !== null && $lista->mesa_provision !== '',
			"cupos_pendientes" => $cuposPendientes
		];

		// Enviamos respuesta en JSON
		header('Content-Type: application/json');
		echo json_encode($respuesta);
		exit;
	}

	public function testAction()
	{
		$this->setLayout('blanco');
		// This is a test action to ensure the controller is working correctly.
		// You can add any test logic here.
		echo "Test action executed successfully.";
	}

	public function guardarelementosAction()
	{
		$this->setLayout('blanco');

		$raw = file_get_contents('php://input');
		$elementos = json_decode($raw, true);
		echo "<pre>";
		print_r($elementos);
		echo "</pre>";


		if (is_array($elementos)) {
			$mesasModel = new Administracion_Model_DbTable_Mesas();

			foreach ($elementos as $mesa) {
				if (isset($mesa['mesa_id']) && $mesa['mesa_id'] > 0) {
					$mesasModel->update($mesa, $mesa['mesa_id']);
				} else {
					unset($mesa['mesa_id']);
					$mesasModel->insert($mesa);
				}
			}

			echo json_encode(['status' => 'success', 'message' => 'Elementos guardados correctamente.']);
		} else {
			echo json_encode(['status' => 'error', 'message' => 'No se recibieron datos válidos.']);
		}
	}

	public function crearmesaAction()
	{
		$this->setLayout('blanco');
		$raw = file_get_contents('php://input');
		$mesaModel = new Administracion_Model_DbTable_Mesas();
		$data = json_decode($raw, true);
		if (!is_array($data)) {
			http_response_code(400);
			echo json_encode(['error' => 'Datos inválidos']);
			return;
		}
		// Validación backend: código y nombre únicos en el ambiente
		$ambiente = isset($data['mesa_ambiente']) ? (int) $data['mesa_ambiente'] : 0;
		$codigo = isset($data['mesa_codigo']) ? trim($data['mesa_codigo']) : '';
		$nombre = isset($data['mesa_nombre']) ? trim($data['mesa_nombre']) : '';
		if ($codigo !== '') {
			$existeCodigo = $mesaModel->getList("mesa_codigo = '{$codigo}'");
			if ($existeCodigo && count($existeCodigo) > 0) {
				echo json_encode(['error' => 'Ya existe una mesa con ese código en este ambiente.']);
				return;
			}
		}
		if ($nombre !== '') {
			$existeNombre = $mesaModel->getList("mesa_ambiente = '{$ambiente}' AND LOWER(mesa_nombre) = '" . strtolower($nombre) . "'");
			if ($existeNombre && count($existeNombre) > 0) {
				echo json_encode(['error' => 'Ya existe una mesa con ese nombre en este ambiente.']);
				return;
			}
		}
		// Elimina id si viene para evitar problemas
		if (isset($data['mesa_id'])) {
			unset($data['mesa_id']);
		}
		$id = $mesaModel->insert($data);
		if ($id) {
			$obj = $mesaModel->getById($id);
			// Normaliza tipos para JS
			if ($obj) {
				$obj->mesa_id = (int) $obj->mesa_id;
				$obj->mesa_ambiente = (int) $obj->mesa_ambiente;
				$obj->mesa_capacidad = isset($obj->mesa_capacidad) ? (int) $obj->mesa_capacidad : 0;
				$obj->mesa_estado = isset($obj->mesa_estado) ? (int) $obj->mesa_estado : 0;
				$obj->mesa_activa = isset($obj->mesa_activa) ? (int) $obj->mesa_activa : 1;
				$obj->orden = isset($obj->orden) ? (int) $obj->orden : 0;
				$obj->mesa_pos_x = isset($obj->mesa_pos_x) ? (int) $obj->mesa_pos_x : 0;
				$obj->mesa_pos_y = isset($obj->mesa_pos_y) ? (int) $obj->mesa_pos_y : 0;
				$obj->mesa_ancho = isset($obj->mesa_ancho) ? (int) $obj->mesa_ancho : 1;
				$obj->mesa_alto = isset($obj->mesa_alto) ? (int) $obj->mesa_alto : 1;
				$obj->mesa_rotacion = isset($obj->mesa_rotacion) ? (int) $obj->mesa_rotacion : 0;
				$obj->mesa_precio = (isset($obj->mesa_precio) && $obj->mesa_precio !== null && $obj->mesa_precio !== '') ? (float) $obj->mesa_precio : null;
			}
			header('Content-Type: application/json');
			echo json_encode($obj);
		} else {
			http_response_code(500);
			echo json_encode(['error' => 'No se pudo crear la mesa']);
		}
	}

	public function eliminarmesaAction()
	{
		$this->setLayout('blanco');
		$raw = file_get_contents('php://input');
		$data = json_decode($raw, true);
		if (!is_array($data) || !isset($data['mesa_id'])) {
			http_response_code(400);
			echo json_encode(['error' => 'Datos inválidos']);
			return;
		}
		$mesa_id = (int) $data['mesa_id'];
		$mesaModel = new Administracion_Model_DbTable_Mesas();
		$mesa = $mesaModel->getById($mesa_id);
		if (!$mesa) {
			echo json_encode(['error' => 'Mesa no encontrada']);
			return;
		}
		// Verificar si está ocupada
		if ($mesa->mesa_estado == 1) {
			echo json_encode(['error' => 'No se puede eliminar una mesa ocupada']);
			return;
		}
		// Verificar si tiene reservas
		$reservasModel = new Administracion_Model_DbTable_Reservas();
		$reservas = $reservasModel->getList(" FIND_IN_SET($mesa_id, reserva_mesa_id) ", "");
		if ($reservas && count($reservas) > 0) {
			echo json_encode(['error' => 'No se puede eliminar una mesa asignada a una reserva']);
			return;
		}
		// Eliminar (desactivar)
		// $mesaModel->editField($mesa_id, 'mesa_activa', 0);
		$mesaModel->deleteRegister($mesa_id);
		header('Content-Type: application/json');
		echo json_encode(['success' => true]);
	}

	public function editarmesaAction()
	{
		// error_reporting(E_ALL);
		$this->setLayout('blanco');
		$input = json_decode(file_get_contents('php://input'), true);
		if (!$input || !isset($input['mesa_id'])) {
			echo json_encode(['error' => 'Datos incompletos']);
			return;
		}
		$mesa_id = $input['mesa_id'];
		$codigo = trim($input['mesa_codigo'] ?? '');
		$nombre = trim($input['mesa_nombre'] ?? '');
		$ambiente = $input['mesa_ambiente'] ?? null;
		if ($codigo === '' || $nombre === '' || !$ambiente) {
			echo json_encode(['error' => 'Código, nombre y ambiente son obligatorios']);
			return;
		}
		$mesaModel = new Administracion_Model_DbTable_Mesas();

		$mesaCodigoExiste = $mesaModel->getList("mesa_codigo = '{$codigo}' AND mesa_id != '{$mesa_id}'");
		if ($mesaCodigoExiste && count($mesaCodigoExiste) > 0) {
			echo json_encode(['error' => 'Ya existe una mesa con ese código.']);
			return;
		}
		// Validar unicidad de código y nombre (excepto la mesa actual)
		$mesas = $mesaModel->getList("mesa_ambiente = '{$ambiente}' AND mesa_id != '{$mesa_id}'");
		foreach ($mesas as $m) {
			if (strtolower($m->mesa_codigo) == strtolower($codigo)) {
				echo json_encode(['error' => 'Ya existe una mesa con ese código.']);
				return;
			}
			if (strtolower($m->mesa_nombre) == strtolower($nombre)) {
				echo json_encode(['error' => 'Ya existe una mesa con ese nombre.']);
				return;
			}
		}
		// 
		// Actualizar mesa usando el modelo
		$data = [
			'mesa_tipo' => $input['mesa_tipo'] ?? 'mesa',
			'mesa_codigo' => $codigo,
			'mesa_nombre' => $nombre,
			'mesa_ancho' => $input['mesa_ancho'] ?? 2,
			'mesa_alto' => $input['mesa_alto'] ?? 2,
			'mesa_capacidad' => $input['mesa_capacidad'] ?? 0,
			'mesa_rotacion' => $input['mesa_rotacion'] ?? 0,
			'mesa_estado' => $input['mesa_estado'] ?? 0,
			'mesa_pos_x' => $input['mesa_pos_x'] ?? 0,
			'mesa_pos_y' => $input['mesa_pos_y'] ?? 0,
			'mesa_activa' => $input['mesa_activa'] ?? 1,
			'mesa_ambiente' => $ambiente,
			// Precio solo aplica a sillas; para otros tipos queda NULL.
			'mesa_precio' => (($input['mesa_tipo'] ?? 'mesa') === 'silla' && isset($input['mesa_precio']) && $input['mesa_precio'] !== '')
				? $input['mesa_precio'] : null
		];
		$mesaModel->update($data, $mesa_id);
		$obj = $mesaModel->getById($mesa_id);
		// Normaliza tipos para JS
		if ($obj) {
			$obj->mesa_id = (int) $obj->mesa_id;
			$obj->mesa_ambiente = (int) $obj->mesa_ambiente;
			$obj->mesa_capacidad = isset($obj->mesa_capacidad) ? (int) $obj->mesa_capacidad : 0;
			$obj->mesa_estado = isset($obj->mesa_estado) ? (int) $obj->mesa_estado : 0;
			$obj->mesa_activa = isset($obj->mesa_activa) ? (int) $obj->mesa_activa : 1;
			$obj->orden = isset($obj->orden) ? (int) $obj->orden : 0;
			$obj->mesa_pos_x = isset($obj->mesa_pos_x) ? (int) $obj->mesa_pos_x : 0;
			$obj->mesa_pos_y = isset($obj->mesa_pos_y) ? (int) $obj->mesa_pos_y : 0;
			$obj->mesa_ancho = isset($obj->mesa_ancho) ? (int) $obj->mesa_ancho : 1;
			$obj->mesa_alto = isset($obj->mesa_alto) ? (int) $obj->mesa_alto : 1;
			$obj->mesa_rotacion = isset($obj->mesa_rotacion) ? (int) $obj->mesa_rotacion : 0;
			$obj->mesa_precio = (isset($obj->mesa_precio) && $obj->mesa_precio !== null && $obj->mesa_precio !== '') ? (float) $obj->mesa_precio : null;
		}
		header('Content-Type: application/json');
		echo json_encode($obj);
	}

	public function mesasDisponiblesParaReasignarAction()
	{
		$this->setLayout('blanco');
		header('Content-Type: application/json');

		$mesa_id = (int) $this->_getSanitizedParam('mesa_id');
		$reserva_id = (int) $this->_getSanitizedParam('reserva_id');
		$mesaModel = new Administracion_Model_DbTable_Mesas();

		if (!$mesa_id) {
			echo json_encode(['error' => 'ID inválido']);
			exit;
		}

		$mesaOrigen = $mesaModel->getById($mesa_id);
		if (!$mesaOrigen) {
			echo json_encode(['error' => 'Mesa no encontrada']);
			exit;
		}

		$capacidad = (int) ($mesaOrigen->mesa_capacidad ?? 0);
		$ambiente = (int) ($mesaOrigen->mesa_ambiente ?? 0);

		// Determinar si la reserva está completa (todos los invitados registrados)
		$total_personas = 0;
		$invitados_count = 0;
		$completa = false;

		if ($reserva_id) {
			$reservasModel = new Administracion_Model_DbTable_Reservas();
			$invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
			$reserva = $reservasModel->getById($reserva_id);

			if ($reserva) {
				$total_personas = (int) ($reserva->reserva_total_personas ?? 0);
				$invitados = $invitadosModel->getList("reserva_id_reserva = '$reserva_id'", "");
				$invitados_count = count($invitados);
				$completa = $total_personas > 0 && $invitados_count >= $total_personas;
			}
		}

		// Si completa → mesas de igual o mayor capacidad (para agregar más invitados)
		// Si incompleta → mesas de igual o menor capacidad (mesa inferior)
		if ($completa) {
			$filtroCapacidad = "mesa_capacidad >= '$capacidad'";
			$orden = "mesa_capacidad ASC, mesa_nombre ASC";
		} else {
			$filtroCapacidad = "mesa_capacidad <= '$capacidad'";
			$orden = "mesa_capacidad DESC, mesa_nombre ASC";
		}

		$mesas = $mesaModel->getList(
			"mesa_estado = 0 AND mesa_activa = 1 AND mesa_tipo = 'mesa' AND $filtroCapacidad AND mesa_ambiente = '$ambiente' AND mesa_id != '$mesa_id'",
			$orden
		);

		$result = [];
		foreach ($mesas as $m) {
			$cap = (int) ($m->mesa_capacidad ?? 0);
			$result[] = [
				'mesa_id' => (int) $m->mesa_id,
				'mesa_nombre' => $m->mesa_nombre,
				'mesa_codigo' => $m->mesa_codigo,
				'mesa_capacidad' => $cap,
				'mismo_ambiente' => ((int) $m->mesa_ambiente) === $ambiente,
				'cap_diff' => $cap - $capacidad,
			];
		}

		echo json_encode([
			'mesa_origen' => [
				'mesa_id' => (int) $mesaOrigen->mesa_id,
				'mesa_nombre' => $mesaOrigen->mesa_nombre,
				'mesa_capacidad' => $capacidad,
			],
			'reserva_info' => [
				'total_personas' => $total_personas,
				'invitados_count' => $invitados_count,
				'completa' => $completa,
			],
			'mesas_disponibles' => $result,
		]);
		exit;
	}

	public function aumentarCapacidadMesaAction()
	{
		$this->setLayout('blanco');
		header('Content-Type: application/json');

		$data = json_decode(file_get_contents('php://input'), true);
		if (!is_array($data) || empty($data['mesa_id']) || empty($data['nueva_capacidad'])) {
			echo json_encode(['error' => 'Datos inválidos']);
			exit;
		}

		$mesaId = (int) $data['mesa_id'];
		$nuevaCapacidad = (int) $data['nueva_capacidad'];

		$mesaModel = new Administracion_Model_DbTable_Mesas();
		$reservasModel = new Administracion_Model_DbTable_Reservas();
		$ambienteModel = new Administracion_Model_DbTable_Ambientes();
		$categoriasModel = new Administracion_Model_DbTable_Categorias();
		$cuposModel = new Administracion_Model_DbTable_Reservacuposadicionales();

		$mesa = $mesaModel->getById($mesaId);
		if (!$mesa) {
			echo json_encode(['error' => 'Mesa no encontrada']);
			exit;
		}
		if ($mesa->mesa_tipo === 'silla') {
			// Una silla siempre tiene capacidad 1 por diseño (round-robin de boletas,
			// etiquetas, etc. asumen esto); "aumentar capacidad" no aplica aquí, sería
			// vender/asignar otra silla individual.
			echo json_encode(['error' => 'No se puede aumentar la capacidad de una silla individual.']);
			exit;
		}

		$capacidadActual = (int) $mesa->mesa_capacidad;
		if ($nuevaCapacidad <= $capacidadActual) {
			echo json_encode(['error' => 'La nueva capacidad debe ser mayor a la actual (' . $capacidadActual . ')']);
			exit;
		}

		// La mesa debe tener una reserva pagada asociada (estados 2, 3, 11)
		$reservas = $reservasModel->getList("FIND_IN_SET('$mesaId', reserva_mesa_id) AND reserva_estado IN (2,3,11)", "id DESC");
		$reserva = !empty($reservas) ? $reservas[0] : null;
		if (!$reserva) {
			echo json_encode(['error' => 'Esta mesa no tiene una reserva pagada asociada']);
			exit;
		}

		$ambiente = $ambienteModel->getById($mesa->mesa_ambiente);
		$categoria = ($ambiente && $ambiente->ambiente_categoria) ? $categoriasModel->getById($ambiente->ambiente_categoria) : null;
		$precioUnitario = $categoria ? (float) $categoria->categoria_precio_socio : 0;

		$cuposAdicionales = $nuevaCapacidad - $capacidadActual;
		$precioTotal = $cuposAdicionales * $precioUnitario;

		// Actualizar la capacidad física de la mesa de una vez
		$mesaModel->editField($mesaId, 'mesa_capacidad', $nuevaCapacidad);

		// Registrar los cupos adicionales como pendientes de pago
		$cuposId = $cuposModel->insert([
			'reserva_id' => $reserva->id,
			'mesa_id' => $mesaId,
			'cupos_capacidad_anterior' => $capacidadActual,
			'cupos_capacidad_nueva' => $nuevaCapacidad,
			'cupos_adicionales' => $cuposAdicionales,
			'precio_unitario' => $precioUnitario,
			'precio_total' => $precioTotal,
			'cupos_estado' => 0,
			'cupos_fecha_creacion' => date('Y-m-d H:i:s'),
		]);

		$logModel = new Administracion_Model_DbTable_Log();
		$logModel->insert([
			'log_usuario' => '',
			'log_tipo' => 'AUMENTAR CAPACIDAD MESA - CUPOS ADICIONALES',
			'log_fecha' => date('Y-m-d H:i:s'),
			'log_log' => "Mesa #$mesaId ({$mesa->mesa_nombre}) — Reserva #{$reserva->id}\n"
				. "Capacidad: $capacidadActual -> $nuevaCapacidad (+$cuposAdicionales)\n"
				. "Precio unitario: $precioUnitario | Precio total: $precioTotal\n"
				. "Registro cupos adicionales ID: $cuposId",
		]);

		echo json_encode([
			'success' => true,
			'message' => 'Capacidad actualizada a ' . $nuevaCapacidad . ' pax. Se generaron ' . $cuposAdicionales . ' cupo(s) adicional(es) pendientes de pago por $' . number_format($precioTotal, 0, ',', '.') . '.',
			'cupos_adicionales' => $cuposAdicionales,
			'precio_total' => $precioTotal,
		]);
		exit;
	}

	public function liberarMesaAction()
	{
		$this->setLayout('blanco');
		header('Content-Type: application/json');

		$data = json_decode(file_get_contents('php://input'), true);
		if (!is_array($data) || empty($data['mesa_id_origen']) || empty($data['mesa_id_destino'])) {
			echo json_encode(['error' => 'Debes seleccionar una mesa de reemplazo']);
			exit;
		}

		$origenId = (int) $data['mesa_id_origen'];
		$destinoId = (int) $data['mesa_id_destino'];
		$mesaModel = new Administracion_Model_DbTable_Mesas();
		$reservasModel = new Administracion_Model_DbTable_Reservas();

		if ($origenId === $destinoId) {
			echo json_encode(['error' => 'La mesa destino debe ser diferente a la origen']);
			exit;
		}

		$mesaOrigen = $mesaModel->getById($origenId);
		$mesaDestino = $mesaModel->getById($destinoId);

		if (!$mesaOrigen) {
			echo json_encode(['error' => 'Mesa origen no encontrada']);
			exit;
		}
		if (!$mesaDestino) {
			echo json_encode(['error' => 'Mesa destino no encontrada']);
			exit;
		}
		if ((int) $mesaOrigen->mesa_estado !== 1) {
			echo json_encode(['error' => 'La mesa origen ya está libre']);
			exit;
		}
		if ((int) $mesaDestino->mesa_estado !== 0) {
			echo json_encode(['error' => 'La mesa destino ya está ocupada']);
			exit;
		}

		// Verificar que la mesa destino no esté en ninguna reserva activa
		$yaAsignada = $reservasModel->getList("FIND_IN_SET('$destinoId', reserva_mesa_id)", "");
		if (!empty($yaAsignada)) {
			echo json_encode(['error' => 'La mesa destino ya está asignada a una reserva']);
			exit;
		}

		// Capturar estado ANTES para el log
		$reservas = $reservasModel->getList("FIND_IN_SET('$origenId', reserva_mesa_id)", "");
		$reserva = !empty($reservas) ? $reservas[0] : null;

		$antes = [
			'mesa_origen' => ['id' => $origenId, 'nombre' => $mesaOrigen->mesa_nombre, 'estado' => 'Ocupada (1)', 'capacidad' => $mesaOrigen->mesa_capacidad],
			'mesa_destino' => ['id' => $destinoId, 'nombre' => $mesaDestino->mesa_nombre, 'estado' => 'Libre (0)', 'capacidad' => $mesaDestino->mesa_capacidad],
			'reserva_id' => $reserva ? $reserva->id : null,
			'reserva_mesas_antes' => $reserva ? $reserva->reserva_mesa_id : null,
		];

		// Mover la reserva: quitar origen, agregar destino
		foreach ($reservas as $r) {
			$ids = array_filter(
				array_map('trim', explode(',', $r->reserva_mesa_id ?? '')),
				function ($m) use ($origenId) {
					return $m !== '' && (int) $m !== $origenId; }
			);
			$ids[] = (string) $destinoId;
			$reservasModel->editField($r->id, 'reserva_mesa_id', implode(',', array_values($ids)));
		}

		// Actualizar estado de ambas mesas
		$mesaModel->editField($origenId, 'mesa_estado', 0);
		$mesaModel->editField($origenId, 'mesa_provision', 0);
		$mesaModel->editField($destinoId, 'mesa_estado', 1);

		// Actualizar reserva_total_personas al capacity de la nueva mesa y ajustar invitados
		$invitados_eliminados = 0;
		if ($reserva) {
			$nuevaCapacidad = (int) ($mesaDestino->mesa_capacidad ?? 0);
			$reservasModel->editField($reserva->id, 'reserva_total_personas', $nuevaCapacidad);

			// Si la nueva mesa tiene menor capacidad, eliminar invitados excedentes (los de menor ID = últimos agregados al invertir)
			$invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
			$todosInvitados = $invitadosModel->getList("reserva_id_reserva = '{$reserva->id}'", "id_invitado ASC");
			if (count($todosInvitados) > $nuevaCapacidad && $nuevaCapacidad > 0) {
				$excedentes = array_slice($todosInvitados, $nuevaCapacidad);
				foreach ($excedentes as $inv) {
					$invitadosModel->deleteRegister($inv->id_invitado);
					$invitados_eliminados++;
				}
			}
		}

		// Estado DESPUÉS para el log
		$reservaActualizada = $reserva ? $reservasModel->getById($reserva->id) : null;
		$despues = [
			'mesa_origen' => ['id' => $origenId, 'nombre' => $mesaOrigen->mesa_nombre, 'estado' => 'Libre (0)'],
			'mesa_destino' => ['id' => $destinoId, 'nombre' => $mesaDestino->mesa_nombre, 'estado' => 'Ocupada (1)', 'capacidad' => $mesaDestino->mesa_capacidad],
			'reserva_id' => $reserva ? $reserva->id : null,
			'reserva_mesas_despues' => $reservaActualizada ? $reservaActualizada->reserva_mesa_id : null,
			'reserva_personas_despues' => $reservaActualizada ? $reservaActualizada->reserva_total_personas : null,
			'invitados_eliminados' => $invitados_eliminados,
		];

		$logModel = new Administracion_Model_DbTable_Log();
		$logModel->insert([
			'log_usuario' => '',
			'log_tipo' => 'LIBERAR MESA - REASIGNACION',
			'log_fecha' => date('Y-m-d H:i:s'),
			'log_log' => "ANTES:\n" . print_r($antes, true)
				. "\nDESPUES:\n" . print_r($despues, true),
		]);

		$msg = 'Reserva reasignada y mesa liberada correctamente';
		if ($invitados_eliminados > 0) {
			$msg .= ". Se eliminaron $invitados_eliminados invitado(s) por exceder la capacidad de la nueva mesa.";
		}

		echo json_encode(['success' => true, 'message' => $msg, 'invitados_eliminados' => $invitados_eliminados]);
		exit;
	}

	public function reservasParaAsignarAction()
	{
		$this->setLayout('blanco');
		header('Content-Type: application/json');

		$q = trim($this->_getSanitizedParam('q') ?? '');
		$reservasModel = new Administracion_Model_DbTable_Reservas();

		$filtro = "reserva_estado IN (1, 2, 3, 11)";
		if ($q !== '') {
			$q = addslashes($q);
			$filtro .= " AND (reserva_nombre_cliente LIKE '%$q%'"
				. " OR reserva_apellido_cliente LIKE '%$q%'"
				. " OR reserva_documento LIKE '%$q%'"
				. " OR reserva_numero_carnet LIKE '%$q%'"
				. " OR id LIKE '%$q%')";
		}

		$reservas = $reservasModel->getList($filtro, "reserva_fecha ASC");
		echo json_encode(array_slice($reservas, 0, 20));
		exit;
	}

	public function asignarMesaAction()
	{
		$this->setLayout('blanco');
		header('Content-Type: application/json');

		$data = json_decode(file_get_contents('php://input'), true);
		if (!is_array($data) || empty($data['mesa_id']) || empty($data['reserva_id'])) {
			echo json_encode(['error' => 'Datos inválidos']);
			exit;
		}

		$mesa_id = (int) $data['mesa_id'];
		$reserva_id = (int) $data['reserva_id'];
		$mesaModel = new Administracion_Model_DbTable_Mesas();
		$reservasModel = new Administracion_Model_DbTable_Reservas();

		$mesa = $mesaModel->getById($mesa_id);
		if (!$mesa) {
			echo json_encode(['error' => 'Mesa no encontrada']);
			exit;
		}
		if ((int) $mesa->mesa_estado === 1) {
			echo json_encode(['error' => 'La mesa ya está ocupada']);
			exit;
		}

		$reserva = $reservasModel->getById($reserva_id);
		if (!$reserva) {
			echo json_encode(['error' => 'Reserva no encontrada']);
			exit;
		}
		if (!in_array((string) $reserva->reserva_estado, ['1', '2', '3', '11'])) {
			echo json_encode(['error' => 'La reserva no está en un estado válido']);
			exit;
		}

		// Validar que la mesa no esté ya en otra reserva
		$yaAsignada = $reservasModel->getList("FIND_IN_SET('$mesa_id', reserva_mesa_id)", "");
		if (!empty($yaAsignada)) {
			echo json_encode(['error' => 'La mesa ya está asignada a otra reserva']);
			exit;
		}

		// Estado ANTES
		$antes = [
			'mesa' => ['id' => $mesa_id, 'nombre' => $mesa->mesa_nombre, 'estado' => 'Libre (0)', 'capacidad' => $mesa->mesa_capacidad],
			'reserva_id' => $reserva_id,
			'reserva_mesas_antes' => $reserva->reserva_mesa_id,
		];

		// Agregar mesa a la reserva
		$mesasActuales = array_filter(
			array_map('trim', explode(',', $reserva->reserva_mesa_id ?? '')),
			function ($m) {
				return $m !== ''; }
		);
		$mesasActuales[] = (string) $mesa_id;
		$nuevasMesas = implode(',', array_values($mesasActuales));
		$reservasModel->editField($reserva_id, 'reserva_mesa_id', $nuevasMesas);
		$mesaModel->editField($mesa_id, 'mesa_estado', 1);

		// Estado DESPUÉS
		$despues = [
			'mesa' => ['id' => $mesa_id, 'nombre' => $mesa->mesa_nombre, 'estado' => 'Ocupada (1)'],
			'reserva_id' => $reserva_id,
			'reserva_mesas_despues' => $nuevasMesas,
		];

		$logModel = new Administracion_Model_DbTable_Log();
		$logModel->insert([
			'log_usuario' => '',
			'log_tipo' => 'ASIGNAR MESA',
			'log_fecha' => date('Y-m-d H:i:s'),
			'log_log' => "ANTES:\n" . print_r($antes, true)
				. "\nDESPUES:\n" . print_r($despues, true),
		]);

		echo json_encode(['success' => true, 'message' => 'Mesa asignada a la reserva #' . $reserva_id]);
		exit;
	}

	public function infoAmbienteAction()
	{
		header('Content-Type: application/json');
		$id = intval($this->_getSanitizedParam('id'));
		if (!$id) {
			echo json_encode(['error' => 'ID inválido']);
			exit;
		}

		$ambiente = $this->mainModel->getById($id);
		if (!$ambiente) {
			echo json_encode(['error' => 'Ambiente no encontrado']);
			exit;
		}

		$mesasModel = new Administracion_Model_DbTable_Mesas();
		$reservasModel = new Administracion_Model_DbTable_Reservas();

		$mesas = $mesasModel->getList("mesa_ambiente = '$id' AND mesa_tipo LIKE '%mesa%'", "mesa_nombre ASC");
		$totalMesas = count($mesas);
		$capacidadTotal = 0;
		$mesasOcupadas = 0;
		$mesasLibres = 0;
		$mesasProvisionales = 0;
		foreach ($mesas as $m) {
			$capacidadTotal += intval($m->mesa_capacidad ?? 0);
			if ((int) ($m->mesa_estado ?? 0) === 1)
				$mesasOcupadas++;
			else
				$mesasLibres++;
			// mesa_provision = '0' significa "es provisional" (NULL/vacío = no lo es); no usar empty()
			// aquí porque la cadena "0" es falsy para empty() y para comparaciones truthy en PHP.
			if ($m->mesa_provision !== null && $m->mesa_provision !== '')
				$mesasProvisionales++;
		}

		$reservasActivas = 0;
		$proximasReservas = [];
		$reservasPorMes = array_fill(0, 12, 0);
		$totalInvitados = 0;

		if (!empty($mesas)) {
			$mesaIds = array_map(fn($m) => intval($m->mesa_id), $mesas);
			$findInSets = implode(' OR ', array_map(fn($mid) => "FIND_IN_SET('$mid', reserva_mesa_id)", $mesaIds));
			$hoy = date('Y-m-d');
			$anio = date('Y');

			$todasReservas = $reservasModel->getList("($findInSets) AND reserva_estado IN (1,2,3,7,11)", "reserva_fecha DESC");
			$reservasActivas = count($todasReservas);

			foreach ($todasReservas as $r) {
				$totalInvitados += intval($r->reserva_total_personas ?? 0);
				if (!empty($r->reserva_fecha) && strpos($r->reserva_fecha, $anio) === 0) {
					$mes = intval(substr($r->reserva_fecha, 5, 2)) - 1;
					if ($mes >= 0 && $mes < 12)
						$reservasPorMes[$mes]++;
				}
			}

			$proximas = $reservasModel->getList("($findInSets) AND reserva_fecha >= '$hoy' AND reserva_estado IN (1,2,3,7,11)", "reserva_fecha ASC");
			$proximasReservas = array_slice($proximas, 0, 8);
		}

		$pisoNombre = '';
		$pisosModel = new Administracion_Model_DbTable_Pisos();
		$piso = $pisosModel->getById($ambiente->ambiente_piso ?? 0);
		if ($piso)
			$pisoNombre = $piso->piso_nombre;

		echo json_encode([
			'ambiente' => $ambiente,
			'piso_nombre' => $pisoNombre,
			'total_mesas' => $totalMesas,
			'capacidad_total' => $capacidadTotal,
			'mesas_ocupadas' => $mesasOcupadas,
			'mesas_libres' => $mesasLibres,
			'mesas_provisionales' => $mesasProvisionales,
			'reservas_activas' => $reservasActivas,
			'total_invitados' => $totalInvitados,
			'proximas_reservas' => $proximasReservas,
			'reservas_por_mes' => $reservasPorMes,
		]);
		exit;
	}

	public function cambiarEstadoMesaAction()
	{
		header('Content-Type: application/json');
		$mesa_id = (int) $this->_getSanitizedParam('mesa_id');
		$estado = (int) $this->_getSanitizedParam('estado');
		if (!$mesa_id) {
			echo json_encode(['error' => 'ID de mesa inválido']);
			exit;
		}
		if (!in_array($estado, [0, 1])) {
			echo json_encode(['error' => 'Estado inválido']);
			exit;
		}

		$mesaModel = new Administracion_Model_DbTable_Mesas();
		$mesa = $mesaModel->getById($mesa_id);
		if (!$mesa) {
			echo json_encode(['error' => 'Mesa no encontrada']);
			exit;
		}

		$mesaModel->editField($mesa_id, 'mesa_estado', $estado);
		echo json_encode(['success' => true, 'nuevo_estado' => $estado]);
		exit;
	}
}
