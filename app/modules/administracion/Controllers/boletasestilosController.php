<?php

/**
 * Clase PDF para previsualización que permite usar un diseño específico
 */
class MYPDFPREVIEW extends TCPDF
{
	private $disenoEstilo;

	/**
	 * Constructor que recibe el diseño específico a usar
	 */
	public function __construct($disenoEstilo, $orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
	{
		$this->disenoEstilo = $disenoEstilo;
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
	}

	public function Header()
	{
		$w = $this->getPageWidth();
		$h = $this->getPageHeight();

		// get the current page break margin
		$bMargin = $this->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $this->AutoPageBreak;
		// disable auto-page-break
		$this->SetAutoPageBreak(false, 0);

		// set background image usando el diseño específico pasado al constructor
		if ($this->disenoEstilo && $this->disenoEstilo->boletas_estilo_fondo) {
			$img_file = IMAGE_PATH . $this->disenoEstilo->boletas_estilo_fondo;
			if (file_exists($img_file)) {
				$this->Image($img_file, 0, 0, $w, $h, '', '', '', false, 300, '', false, false, 0);
			}
		}

		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$this->setPageMark();
	}

	public function Footer()
	{
		// Position at 15 mm from bottom
		$this->SetY(-12);
		// Set font
		$this->SetFont('helvetica', 'x', 10);
		$this->SetTextColor(255, 255, 255);
		// Page number usando el texto del diseño específico
		if ($this->disenoEstilo && $this->disenoEstilo->boletas_estilo_textofooter) {
			$this->Cell(0, 10, $this->disenoEstilo->boletas_estilo_textofooter, 0, false, 'C', 0, '', 0, false, 'T', 'M');
		}
	}
}

/**
 * Controlador de Boletasestilos que permite la  creacion, edicion  y eliminacion de los boletasestilo del Sistema
 */
class Administracion_boletasestilosController extends Administracion_mainController
{
	public $botonpanel = 25;
	/**
	 * $mainModel  instancia del modelo de  base de datos boletasestilo
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
	protected $_csrf_section = "administracion_boletasestilos";

	/**
	 * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
	 * @var string
	 */
	protected $namepages;

	protected $namepageactual;



	/**
	 * Inicializa las variables principales del controlador boletasestilos .
	 *
	 * @return void.
	 */
	public function init()
	{
		$this->mainModel = new Administracion_Model_DbTable_Boletasestilos();
		$this->namefilter = "parametersfilterboletasestilos";
		$this->route = "/administracion/boletasestilos";
		$this->namepages = "pages_boletasestilos";
		$this->namepageactual = "page_actual_boletasestilos";
		$this->_view->route = $this->route;
		if (Session::getInstance()->get($this->namepages)) {
			$this->pages = Session::getInstance()->get($this->namepages);
		} else {
			$this->pages = 20;
		}
		parent::init();
	}


	/**
	 * Recibe la informacion y  muestra un listado de  boletasestilo con sus respectivos filtros.
	 *
	 * @return void.
	 */
	public function indexAction()
	{
		$title = "Administración de Estilos de Boletas";
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
	}

	/**
	 * Genera la Informacion necesaria para editar o crear un  boletasestilo  y muestra su formulario
	 *
	 * @return void.
	 */
	public function manageAction()
	{
		$this->_view->route = $this->route;
		$this->_csrf_section = "manage_boletasestilos_" . date("YmdHis");
		$this->_csrf->generateCode($this->_csrf_section);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$id = $this->_getSanitizedParam("id");
		if ($id > 0) {
			$content = $this->mainModel->getById($id);
			if ($content->boletas_estilo_id) {
				$this->_view->content = $content;
				$this->_view->routeform = $this->route . "/update";
				$title = "Actualizar de Estilos de Boletas";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			} else {
				$this->_view->routeform = $this->route . "/insert";
				$title = "Crear de Estilos de Boletas";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			}
		} else {
			$this->_view->routeform = $this->route . "/insert";
			$title = "Crear de Estilos de Boletas";
			$this->getLayout()->setTitle($title);
			$this->_view->titlesection = $title;
		}
	}

	/**
	 * Inserta la informacion de un boletasestilo  y redirecciona al listado de boletasestilo.
	 *
	 * @return void.
	 */
	public function insertAction()
	{
		$this->setLayout('blanco');
		$csrf = $this->_getSanitizedParam("csrf");
		if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
			$data = $this->getData();
			$uploadImage =  new Core_Model_Upload_Image();
			if ($_FILES['boletas_estilo_fondo']['name'] != '') {
				$data['boletas_estilo_fondo'] = $uploadImage->upload("boletas_estilo_fondo");
			}
			if ($data['boletas_estilo_estado'] != 1) {
				$demasDisenos = $this->mainModel->getList("boletas_estilo_estado = 1 ");
				if (!$demasDisenos) {
					header('Location: ' . $this->route . '?error=1');
					return;
				}
			}
			$id = $this->mainModel->insert($data);

			$disenoCreado = $this->mainModel->getById($id);
			if ($disenoCreado && $disenoCreado->boletas_estilo_estado == 1) {
				$demasDisenos = $this->mainModel->getList(" boletas_estilo_id != " . $id . " AND boletas_estilo_estado = 1 ");
				foreach ($demasDisenos as $diseno) {
					$this->mainModel->editField($diseno->boletas_estilo_id, "boletas_estilo_estado", 0);
				}
			}

			$data['boletas_estilo_id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'CREAR BOLETASESTILO';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y Actualiza la informacion de un boletasestilo  y redirecciona al listado de boletasestilo.
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
			if ($content->boletas_estilo_id) {
				$data = $this->getData();
				$uploadImage =  new Core_Model_Upload_Image();
				if ($_FILES['boletas_estilo_fondo']['name'] != '') {
					if ($content->boletas_estilo_fondo) {
						$uploadImage->delete($content->boletas_estilo_fondo);
					}
					$data['boletas_estilo_fondo'] = $uploadImage->upload("boletas_estilo_fondo");
				} else {
					$data['boletas_estilo_fondo'] = $content->boletas_estilo_fondo;
				}

				if ($data['boletas_estilo_estado'] != 1) {
					$demasDisenos = $this->mainModel->getList(" boletas_estilo_id != '{$id}' AND boletas_estilo_estado = 1 ");
					if (!$demasDisenos) {
						header('Location: ' . $this->route . '?error=1');
						return;
					}
				}
				$this->mainModel->update($data, $id);
				$disenoEditado = $this->mainModel->getById($id);
				if ($disenoEditado && $disenoEditado->boletas_estilo_estado == 1) {
					$demasDisenos = $this->mainModel->getList(" boletas_estilo_id != '{$id}' AND boletas_estilo_estado = 1 ");
					foreach ($demasDisenos as $diseno) {
						$this->mainModel->editField($diseno->boletas_estilo_id, "boletas_estilo_estado", "0");
					}
				}
			}
			$data['boletas_estilo_id'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'EDITAR BOLETASESTILO';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y elimina un boletasestilo  y redirecciona al listado de boletasestilo.
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
					$uploadImage =  new Core_Model_Upload_Image();
					if (isset($content->boletas_estilo_fondo) && $content->boletas_estilo_fondo != '') {
						$uploadImage->delete($content->boletas_estilo_fondo);
					}
					$this->mainModel->deleteRegister($id);
					$data = (array)$content;
					$data['log_log'] = print_r($data, true);
					$data['log_tipo'] = 'BORRAR BOLETASESTILO';
					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($data);
				}
			}
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Boletasestilos.
	 *
	 * @return array con toda la informacion recibida del formulario.
	 */
	private function getData()
	{
		$data = array();
		$data['boletas_estilo_estado'] = $this->_getSanitizedParam("boletas_estilo_estado");
		$data['boletas_estilo_titulo'] = $this->_getSanitizedParam("boletas_estilo_titulo");
		$data['boletas_estilo_textofooter'] = $this->_getSanitizedParam("boletas_estilo_textofooter");
		$data['boletas_estilo_fondo'] = "";
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
			if ($filters->boletas_estilo_estado != '') {
				$filtros = $filtros . " AND boletas_estilo_estado LIKE '%" . $filters->boletas_estilo_estado . "%'";
			}
			if ($filters->boletas_estilo_titulo != '') {
				$filtros = $filtros . " AND boletas_estilo_titulo LIKE '%" . $filters->boletas_estilo_titulo . "%'";
			}
			if ($filters->boletas_estilo_fondo != '') {
				$filtros = $filtros . " AND boletas_estilo_fondo LIKE '%" . $filters->boletas_estilo_fondo . "%'";
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
			$parramsfilter['boletas_estilo_estado'] =  $this->_getSanitizedParam("boletas_estilo_estado");
			$parramsfilter['boletas_estilo_titulo'] =  $this->_getSanitizedParam("boletas_estilo_titulo");
			$parramsfilter['boletas_estilo_fondo'] =  $this->_getSanitizedParam("boletas_estilo_fondo");
			Session::getInstance()->set($this->namefilter, $parramsfilter);
		}
		if ($this->_getSanitizedParam("cleanfilter") == 1) {
			Session::getInstance()->set($this->namefilter, '');
			Session::getInstance()->set($this->namepageactual, 1);
		}
	}

	/**
	 * Genera una previsualización en PDF de cómo se vería una boleta con el diseño seleccionado
	 *
	 * @return void.
	 */
	public function previsualizarAction()
	{
		$this->setLayout('blanco');
		$id = $this->_getSanitizedParam("id");

		if (!$id || $id <= 0) {
			echo "ID de diseño no válido";
			return;
		}

		$disenoEstilo = $this->mainModel->getById($id);

		if (!$disenoEstilo || !$disenoEstilo->boletas_estilo_id) {
			echo "Diseño no encontrado";
			return;
		}

		// Crear datos de prueba
		$datosPrueba = $this->generarDatosPrueba();

		// Generar PDF con el diseño específico
		$this->generarPDFPreview($disenoEstilo, $datosPrueba);
	}

	/**
	 * Genera datos de prueba para la previsualización del PDF
	 *
	 * @return object datos de prueba
	 */
	private function generarDatosPrueba()
	{
		$datos = new stdClass();

		// Datos de la reserva de prueba
		$datos->reserva = new stdClass();
		$datos->reserva->id = 'DEMO-001';
		$datos->reserva->reserva_nombre_cliente = 'Juan Pérez Ejemplo';
		$datos->reserva->reserva_correo = 'ejemplo@demo.com';
		$datos->reserva->reserva_numero_carnet = '12345678';
		$datos->reserva->reserva_total_pagar = '150.000';
		$datos->reserva->reserva_total_personas = 4;

		// Datos del evento de prueba
		$datos->evento = new stdClass();
		$datos->evento->evento_titulo = 'Evento de Ejemplo';
		$datos->evento->evento_fecha = date('Y-m-d');
		$datos->evento->evento_lugar = 'Salón Principal';

		// Datos de la boleta de prueba
		$datos->boleta = new stdClass();
		$datos->boleta->boleta_uid = 'PREV-' . strtoupper(substr(md5(time()), 0, 8));
		$datos->boleta->boleta_mesa = 1;

		// Datos del invitado de prueba
		$datos->invitado = new stdClass();
		$datos->invitado->invitadoReserva_nombre_invitado = 'María Perez';
		$datos->invitado->invitadoReserva_apellido_invitado = 'López Lopez';
		$datos->invitado->documento_invitado = '87654321';

		// Datos de la mesa de prueba
		$datos->mesasInfo = new stdClass();
		$datos->mesasInfo->mesa_nombre = 'Mesa VIP 01';
		$datos->mesasInfo->mesa_codigo = 'MV-001';
		$datos->mesasInfo->pisoInfo = new stdClass();
		$datos->mesasInfo->pisoInfo->piso_nombre = 'Primer Piso';

		// Datos del ambiente de prueba
		$datos->ambiente = new stdClass();
		$datos->ambiente->ambiente_nombre = 'Salón Principal';

		return $datos;
	}

	/**
	 * Genera el PDF de previsualización con el diseño específico
	 *
	 * @param object $disenoEstilo diseño de boleta a usar
	 * @param object $datosPrueba datos de prueba para el PDF
	 * @return void
	 */
	private function generarPDFPreview($disenoEstilo, $datosPrueba)
	{
		// Crear una clase PDF personalizada que use el diseño específico
		$pdf = new MYPDFPREVIEW($disenoEstilo, 'P', 'mm', 'A5', true, 'UTF-8', false);
		$pdf->SetMargins(10, 10, 10);
		$pdf->SetHeaderMargin(0);
		$pdf->SetFooterMargin(0);
		$pdf->SetAutoPageBreak(false, 0);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$pdf->AddPage();
		$pdf->SetFont('helvetica', '', 12);

		// Generar código QR de prueba
		$qrPath = $this->generarQRPrueba($datosPrueba->boleta->boleta_uid, $datosPrueba->invitado->documento_invitado);

		// Crear vista con datos de prueba
		$this->_view->reserva = $datosPrueba->reserva;
		$this->_view->evento = $datosPrueba->evento;
		$this->_view->boleta = $datosPrueba->boleta;
		$this->_view->invitado = $datosPrueba->invitado;
		$this->_view->mesasInfo = $datosPrueba->mesasInfo;
		$this->_view->ambiente = $datosPrueba->ambiente;

		$content = $this->_view->getRoutPHP('modules/page/Views/template/generarpdfnew.php');
		$pdf->writeHTML($content, true, false, true, false, '');

		ob_clean();

		$pdf->SetTitle("Previsualización - " . $disenoEstilo->boletas_estilo_titulo);
		$pdf->Output("preview_" . $disenoEstilo->boletas_estilo_titulo . ".pdf", 'I');
	}

	/**
	 * Genera un código QR de prueba para la previsualización
	 *
	 * @param string $uid identificador único de la boleta
	 * @param string $documento documento del invitado
	 * @return string ruta del archivo QR generado
	 */
	private function generarQRPrueba($uid, $documento)
	{
		if (!class_exists('QRcode')) {
			require_once 'phpqrcode/qrlib.php';
		}

		$textoQR = $documento;
		$rutaQR = "images_sales/qrs_news/{$uid}.png";

		// Crear directorio si no existe
		if (!file_exists("images_sales/qrs_news/")) {
			mkdir("images_sales/qrs_news/", 0777, true);
		}

		QRcode::png($textoQR, $rutaQR, "Q", 5, 3);
		return $rutaQR;
	}
}
