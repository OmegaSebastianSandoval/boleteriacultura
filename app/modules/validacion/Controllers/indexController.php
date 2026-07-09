<?php

/**
 *
 */

class Validacion_indexController extends Validacion_mainController
{
	protected $mainModel;
	protected $route;
	protected $_csrf_section = "login_validacion";
	public $csrf;
	public function init()
	{
		$this->mainModel = new Core_Model_DbTable_User();
		$this->route = '/validacion/';
		$this->_view->route = $this->route;
		$this->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		parent::init();
	}

	public function indexAction()
	{
		$user = Session::getInstance()->get("user");
		$uid = $this->_getSanitizedParam("uid");
		$token = $this->_getSanitizedParam("token");
		if ($user) {
			if ($uid && $token) {
				header("Location: /validacion/validar/?uid={$uid}&token={$token}");
				exit;
			} else {
				header('Location: /validacion/evento/');
			}
			exit;
		}
		$csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];


		$this->_view->csrf = $csrf;
		$this->_view->uid = $uid;
		$this->_view->token = $token;
		$this->_view->error_login = Session::getInstance()->get("error_login");
		Session::getInstance()->set("error_login", "");
		$publicidadModel = new Administracion_Model_DbTable_Publicidad();
		$publicidad = $publicidadModel->getList("publicidad_seccion = '2'", "orden ASC")[0];
		$this->_view->publicidad = $publicidad;
	}
	public function validarusuarioAction()
	{
		Session::getInstance()->set("error_login", "");
		$isPost = $this->getRequest()->isPost();
		$user = $this->_getSanitizedParam("user");
		$password = $this->_getSanitizedParam("password");

		$uid = $this->_getSanitizedParam("uid");
		$token = $this->_getSanitizedParam("token");

		$csrf = $this->_getSanitizedParam("csrf");

		// Log intento de login
		$this->logAuditoriaBoleta('INTENTO_LOGIN', 'exitoso', [
			'boleta_uid' => $uid,
			'boleta_token' => $token,
			'observaciones' => "Intento de login con usuario: {$user}"
		]);

		if (!$isPost || !$user || !$password || $this->csrf !== $csrf) {
			// Log error en datos de login
			$this->logAuditoriaBoleta('ERROR_LOGIN_DATOS', 'fallido', [
				'boleta_uid' => $uid,
				'boleta_token' => $token,
				'motivo_fallo' => 'Datos de login incompletos o CSRF inválido',
				'observaciones' => "Usuario intentado: {$user}"
			]);

			Session::getInstance()->set("error_login", "Lo sentimos ocurrio un error intente de nuevo.");
			if ($uid && $token) {
				header("Location: /validacion/?uid={$uid}&token={$token}");
			} else {
				header('Location: /validacion/?v=1');
			}
			exit;
		}

		$userModel = new core_Model_DbTable_User();

		if (!$userModel->autenticateUser($user, $password)) {
			// Log credenciales incorrectas
			$this->logAuditoriaBoleta('ERROR_LOGIN_CREDENCIALES', 'fallido', [
				'boleta_uid' => $uid,
				'boleta_token' => $token,
				'motivo_fallo' => 'Usuario o contraseña incorrectos',
				'observaciones' => "Usuario intentado: {$user}"
			]);

			Session::getInstance()->set("error_login", "El Usuario o Contraseña son incorrectos.");

			if ($uid && $token) {

				header("Location: /validacion/?uid={$uid}&token={$token}");
			} else {
				header('Location: /validacion/?v=2');
			}
			exit;
		}

		$resUser = $userModel->searchUserByUser($user);

		if ($resUser->user_state != 1) {
			// Log usuario inactivo
			$this->logAuditoriaBoleta('ERROR_LOGIN_USUARIO_INACTIVO', 'fallido', [
				'boleta_uid' => $uid,
				'boleta_token' => $token,
				'motivo_fallo' => 'Usuario se encuentra inactivo',
				'observaciones' => "Usuario: {$user}, ID: {$resUser->user_id}"
			]);

			Session::getInstance()->set("error_login", "El Usuario se encuentra inactivo.");
			if ($uid && $token) {
				header("Location: /validacion/?uid={$uid}&token={$token}");
			} else {
				header('Location: /validacion/?v=3');
			}
			exit;
		}
		Session::getInstance()->set("user", $resUser);

		// Log login exitoso
		$this->logAuditoriaBoleta('LOGIN_EXITOSO', 'exitoso', [
			'boleta_uid' => $uid,
			'boleta_token' => $token,
			'observaciones' => "Login exitoso para usuario: {$user}, ID: {$resUser->user_id}"
		]);

		Session::getInstance()->set("user", $resUser);

		if ($uid && $token) {
			header("Location: /validacion/validar/?uid={$uid}&token={$token}");
			exit;
		}


		//LOG
		$data['log_tipo'] = "LOGIN";
		$data['log_usuario'] = $resUser->user_user;
		$logModel = new Administracion_Model_DbTable_Log();
		$logModel->insert($data);

		header('Location: /validacion/evento/');
		exit;
	}

	public function logoutAction()
	{
		$user = Session::getInstance()->get("user");

		// Log logout
		$this->logAuditoriaBoleta('LOGOUT', 'exitoso', [
			'observaciones' => "Logout de usuario: " . ($user ? $user->user_user : 'desconocido')
		]);

		Session::getInstance()->set("user", "");
		Session::getInstance()->set("error_login", "");
		header('Location: /validacion/');
		exit;
	}
}
