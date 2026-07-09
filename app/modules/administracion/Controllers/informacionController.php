<?php

/**
 * Controlador de informacion que permite la  creacion, edicion  y eliminacion de los info pagina del Sistema
 */
class Administracion_informacionController extends Administracion_mainController
{
	public $botonpanel = 1;
	/**
	 * $mainModel  instancia del modelo de  base de datos info pagina
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
	protected $_csrf_section = "administracion_informacion";

	/**
	 * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
	 * @var string
	 */
	protected $namepages;



	/**
	 * Inicializa las variables principales del controlador informacion .
	 *
	 * @return void.
	 */
	public function init()
	{
		$this->mainModel = new Administracion_Model_DbTable_Informacion();
		$this->namefilter = "parametersfilterinformacion";
		$this->route = "/administracion/informacion";
		$this->namepages = "pages_informacion";
		$this->_view->route = $this->route;
		if (Session::getInstance()->get($this->namepages)) {
			$this->pages = Session::getInstance()->get($this->namepages);
		} else {
			$this->pages = 20;
		}
		parent::init();
	}


	/**
	 *  muestra el formulario para realizar la edicion de la informacion
	 *
	 * @return void.
	 */
	public function indexAction()
	{
		$title = "Administración de Información";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;

		$this->_csrf_section = "manage_informacion_" . date("YmdHis");
		$this->_csrf->generateCode($this->_csrf_section);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$id = 1;
		if ($id > 0) {
			$content = $this->mainModel->getById($id);
			if ($content->info_pagina_id) {
				$this->_view->content = $content;
				$this->_view->routeform = $this->route . "/update";
				$this->getLayout()->setTitle("Actualizar info pagina");
			} else {
				$this->_view->routeform = $this->route . "/insert";
				$this->getLayout()->setTitle("Crear info pagina");
			}
		} else {
			$this->_view->routeform = $this->route . "/insert";
			$this->getLayout()->setTitle("Crear info pagina");
		}
	}


	/**
	 * Inserta la informacion de un infopage  y redirecciona al listado de infopage.
	 *
	 * @return void.
	 */
	public function insertAction()
	{
		$csrf = $this->_getSanitizedParam("csrf");
		if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
			$data = $this->getData();
			$uploadDocument =  new Core_Model_Upload_Document();
			$uploadImage =  new Core_Model_Upload_Image();
			if ($_FILES['info_pagina_favicon']['name'] != '') {
				$data['info_pagina_favicon'] = $uploadImage->upload("info_pagina_favicon");
			}

			if ($_FILES['info_pagina_robot']['name'] != '') {
				$data['info_pagina_robot'] = $uploadDocument->uploadpublic("info_pagina_robot", "robots.txt");
			}
			if ($_FILES['info_pagina_sitemap']['name'] != '') {
				$data['info_pagina_sitemap'] = $uploadDocument->uploadpublic("info_pagina_sitemap", "sitemap.xml");
			}
			if ($_FILES['info_pagina_favicon']['name'] != '') {
				$data['info_pagina_favicon'] = $uploadImage->upload("info_pagina_favicon");
			}
			$id = $this->mainModel->insert($data);
			$this->mainModel->changeOrder($id, $id);
		}
		header('Location: /administracion/panel');
	}

	/**
	 * Recibe un identificador  y Actualiza la informacion de un infopage  y redirecciona al listado de infopage.
	 *
	 * @return void.
	 */
	public function updateAction()
	{
		$csrf = $this->_getSanitizedParam("csrf");
		$uploadImage =  new Core_Model_Upload_Image();
		if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
			$id = $this->_getSanitizedParam("id");
			$content = $this->mainModel->getById($id);
			if ($content->info_pagina_id) {
				$data = $this->getData();
				$uploadDocument =  new Core_Model_Upload_Document();
				if ($_FILES['info_pagina_robot']['name'] != '') {
					$data['info_pagina_robot'] = $uploadDocument->uploadpublic("info_pagina_robot", "robots.txt");
				} else {
					$data['info_pagina_robot'] = $content->info_pagina_robot;
				}

				if ($_FILES['info_pagina_sitemap']['name'] != '') {
					$data['info_pagina_sitemap'] = $uploadDocument->uploadpublic("info_pagina_sitemap", "sitemap.xml");
				} else {
					$data['info_pagina_sitemap'] = $content->info_pagina_sitemap;
				}
				$uploadImage =  new Core_Model_Upload_Image();

				if ($_FILES['info_pagina_favicon']['name'] != '') {
					if ($content->info_pagina_favicon) {
						$uploadImage->delete($content->info_pagina_favicon);
					}
					$data['info_pagina_favicon'] = $uploadImage->upload("info_pagina_favicon");
				} else {
					$data['info_pagina_favicon'] = $content->info_pagina_favicon;
				}
				$this->mainModel->update($data, $id);
			}
		}
		header('Location: /administracion/informacion');
	}


	/**
	 * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Infopage.
	 *
	 * @return array con toda la informacion recibida del formulario.
	 */
	private function getData()
	{
		$data = array();
		$data['info_pagina_facebook'] = $this->_getSanitizedParam("info_pagina_facebook");
		$data['info_pagina_instagram'] = $this->_getSanitizedParam("info_pagina_instagram");
		$data['info_pagina_twitter'] = $this->_getSanitizedParam("info_pagina_twitter");
		$data['info_pagina_pinterest'] = $this->_getSanitizedParam("info_pagina_pinterest");
		$data['info_pagina_youtube'] = $this->_getSanitizedParam("info_pagina_youtube");
		$data['info_pagina_flickr'] = $this->_getSanitizedParam("info_pagina_flickr");
		$data['info_pagina_linkedin'] = $this->_getSanitizedParam("info_pagina_linkedin");
		$data['info_pagina_google'] = $this->_getSanitizedParam("info_pagina_google");
		$data['info_pagina_telefono'] = $this->_getSanitizedParam("info_pagina_telefono");
		$data['info_pagina_whatsapp'] = $this->_getSanitizedParam("info_pagina_whatsapp");
		$data['info_pagina_correos_contacto'] = $this->_getSanitizedParam("info_pagina_correos_contacto");
		$data['info_pagina_direccion_contacto'] = $this->_getSanitizedParam("info_pagina_direccion_contacto");
		$data['info_pagina_informacion_contacto'] = $this->_getSanitizedParamHtml("info_pagina_informacion_contacto");
		$data['info_pagina_informacion_contacto_footer'] = $this->_getSanitizedParamHtml("info_pagina_informacion_contacto_footer");
		$data['info_pagina_latitud'] = $this->_getSanitizedParam("info_pagina_latitud");
		$data['info_pagina_longitud'] = $this->_getSanitizedParam("info_pagina_longitud");
		$data['info_pagina_zoom'] = $this->_getSanitizedParam("info_pagina_zoom");
		$data['info_pagina_descripcion'] = $this->_getSanitizedParam("info_pagina_descripcion");
		$data['info_pagina_tags'] = $this->_getSanitizedParam("info_pagina_tags");
		$data['info_pagina_robot'] = "";
		$data['info_pagina_sitemap'] = "";
		$data['info_pagina_scripts'] = $this->_getSanitizedParamHtml("info_pagina_scripts");
		$data['info_pagina_metricas'] = $this->_getSanitizedParamHtml("info_pagina_metricas");

		$data['info_pagina_host'] = $this->_getSanitizedParamHtml("info_pagina_host");
		$data['info_pagina_port'] = $this->_getSanitizedParamHtml("info_pagina_port");
		$data['info_pagina_username'] = $this->_getSanitizedParamHtml("info_pagina_username");
		$data['info_pagina_password'] = $this->_getSanitizedParamHtml("info_pagina_password");
		$data['info_pagina_correo_remitente'] = $this->_getSanitizedParamHtml("info_pagina_correo_remitente");
		$data['info_pagina_nombre_remitente'] = $this->_getSanitizedParamHtml("info_pagina_nombre_remitente");
		$data['info_pagina_correo_oculto'] = $this->_getSanitizedParamHtml("info_pagina_correo_oculto");

		$data['info_pagina_titulo_legal'] = $this->_getSanitizedParamHtml("info_pagina_titulo_legal");
		$data['info_pagina_descripcion_legal'] = $this->_getSanitizedParamHtml("info_pagina_descripcion_legal");
		$data['info_pagina_favicon'] = "";


		$data['info_pagina_smtp'] = $this->_getSanitizedParamHtml("info_pagina_smtp");
		$data['info_pagina_debug'] = $this->_getSanitizedParamHtml("info_pagina_debug");
		$data['info_pagina_asunto_correo'] = $this->_getSanitizedParamHtml("info_pagina_asunto_correo");

		return $data;
	}

	/**
	 * Prueba el envío de correo con las credenciales configuradas
	 * 
	 * @return void Retorna respuesta JSON con el resultado de la prueba
	 */
	public function pruebaenvioAction()
	{
		$this->setLayout('blanco');
		header('Content-Type: application/json');

		try {
			// Obtener las credenciales desde el formulario o la base de datos
			$host = $this->_getSanitizedParam("host");
			$port = $this->_getSanitizedParam("port");
			$username = $this->_getSanitizedParam("username");
			$password = $this->_getSanitizedParam("password");
			$correoRemitente = $this->_getSanitizedParam("correo_remitente");
			$nombreRemitente = $this->_getSanitizedParam("nombre_remitente");
			$smtp = $this->_getSanitizedParam("smtp");
			$debug = $this->_getSanitizedParam("debug");
			$correoDestino = $this->_getSanitizedParam("correo_destino");
			$asuntoCorreo = $this->_getSanitizedParam("asunto_correo");

			// Validar que se hayan enviado todos los datos necesarios
			if (
				empty($host) || empty($port) || empty($username) || empty($password) ||
				empty($correoRemitente) || empty($nombreRemitente) || empty($correoDestino)
			) {
				echo json_encode([
					'success' => false,
					'mensaje' => 'Faltan datos requeridos para la prueba de envío'
				]);
				exit;
			}

			// Crear instancia de PHPMailer con las credenciales proporcionadas
			$mail = new PHPMailer;
			$mail->CharSet = 'UTF-8';
			$mail->isSMTP();
			$mail->SMTPDebug = 0; // Sin debug para la respuesta limpia
			$mail->SMTPSecure = $smtp ?: 'tls';
			$mail->Host = $host;
			$mail->Port = $port;
			$mail->SMTPAuth = true;
			$mail->Username = $username;
			$mail->Password = $password;
			$mail->setFrom($correoRemitente, $nombreRemitente);

			// Configurar el correo de prueba
			$mail->addAddress($correoDestino);
			$mail->Subject = "Prueba de Configuración de Correo - " . date('Y-m-d H:i:s') . ' - ' . $asuntoCorreo;

			$mensaje = "
				<html>
				<body style='font-family: Arial, sans-serif;'>
					<h2 style='color: #333;'>Prueba de Envío de Correo</h2>
					<p>Este es un correo de prueba para validar la configuración del servidor SMTP.</p>
					<hr>
					<p><strong>Configuración utilizada:</strong></p>
					<ul>
						<li><strong>Host:</strong> {$host}</li>
						<li><strong>Puerto:</strong> {$port}</li>
						<li><strong>Usuario:</strong> {$username}</li>
						<li><strong>SMTP Secure:</strong> {$smtp}</li>
						<li><strong>Correo Remitente:</strong> {$correoRemitente}</li>
						<li><strong>Nombre Remitente:</strong> {$nombreRemitente}</li>
						<li><strong>Asunto del Correo:</strong> {$asuntoCorreo}</li>
					</ul>
					<hr>
					<p style='color: #28a745;'><strong>✓ La configuración es correcta. El correo se envió exitosamente.</strong></p>
					<p style='font-size: 12px; color: #666;'>Fecha y hora: " . date('Y-m-d H:i:s') . "</p>
				</body>
				</html>
			";

			$mail->msgHTML($mensaje);
			$mail->AltBody = "Prueba de configuración de correo - La configuración es correcta";

			// Intentar enviar el correo
			if ($mail->send()) {
				echo json_encode([
					'success' => true,
					'mensaje' => '✓ Correo enviado exitosamente a ' . $correoDestino . '. Por favor, verifica tu bandeja de entrada.'
				]);
			} else {
				echo json_encode([
					'success' => false,
					'mensaje' => 'Error al enviar el correo: ' . $mail->ErrorInfo
				]);
			}
		} catch (Exception $e) {
			echo json_encode([
				'success' => false,
				'mensaje' => 'Excepción capturada: ' . $e->getMessage()
			]);
		}
		exit;
	}
}
