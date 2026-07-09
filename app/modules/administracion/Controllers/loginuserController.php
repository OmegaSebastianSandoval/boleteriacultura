<?php

/**
*
*/

class Administracion_loginuserController extends Controllers_Abstract
{

	protected $mainModel;
	protected $route;
	protected $_csrf_section = "login_admin";
	public $csrf;

	public function init()
	{
		$this->mainModel = new Core_Model_DbTable_User();
		$this->route = "/administracion/users";
		$this->_view->route = $this->route;
		$this->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		parent::init();
	}

	public function indexAction()
	{
		Session::getInstance()->set("error_login","");
		$isPost = $this->getRequest()->isPost();
		$user= $this->_getSanitizedParam("user");
		$password = $this->_getSanitizedParam("password");
		$csrf = $this->_getSanitizedParam("csrf");
		$isError = false;
		$busco = "no";
		$error = 0;
		if($isPost == true && $user && $password && $this->csrf == $csrf  ){
			$userModel = new core_Model_DbTable_User();
			$busco = "si";
			if ($userModel->autenticateUser($user,$password) == true) {
				$resUser = $userModel->searchUserByUser($user);
				if($resUser->user_state == 1){
					Session::getInstance()->set("kt_login_id",$resUser->user_id);
					Session::getInstance()->set("kt_login_level",$resUser->user_level);
					Session::getInstance()->set("kt_login_user",$resUser->user_user);
					Session::getInstance()->set("kt_login_name",$resUser->user_names." ".$resUser->user_lastnames);

					//LOG
					$data['log_tipo'] = "LOGIN";
					$data['log_usuario'] = $resUser->user_user;
					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($data);

				} else {
					$isError = true;
					$error = 3;
					Session::getInstance()->set("error_login","El Usuario se encuentra inactivo.");
				}
			} else {
				$isError = true;
				Session::getInstance()->set("error_login","El Usuario o Contraseña son incorrectos.");
			}
		} else {
			$isError = true;
			$error=1;
			Session::getInstance()->set("error_login","Lo sentimos ocurrio un error intente de nuevo.");
		}
		if($isError == false){
			if($_SESSION['kt_login_user']=="consulta"){
			    header("Location: /administracion/reservas");
			    exit;
			}
			
			if(Session::getInstance()->get("kt_login_level") == '1'){
				header("Location: /administracion/dashboard");
			} 
			if(Session::getInstance()->get("kt_login_level") == '2'){
				header("Location: /administracion/dashboard");
			} 
		} else {
			header('Location: /administracion/');
		}
	}


	public function forgotpasswordAction()
	{
		$this->setLayout('blanco');
        $this->_csrf_section = "login_admin";
        $modelUser = new Core_Model_DbTable_User();
        $email = $this->_getSanitizedParam("email");
        $error = true;
        $message = "Usuario No encontrado";
        $filter = " user_email = '".$email."' ";
        $user = $modelUser->getList($filter, "")[0];
        $id = $user->user_id;
        Session::getInstance()->set("error_olvido",$message);
        if ($user) {
         	$sendingemail = new Core_Model_Sendingemail($this->_view);
            $code = Session::getInstance()->get('csrf')['page_csrf'];
            $modelUser->editCode($id,$code);
            $user = $modelUser->getById($user->user_id);
            if ($sendingemail->forgotpassword($user) == true ) {
                $error = false;
                $message = "Se ha enviado a su correo un mensaje de recuperación de contraseña.";
                Session::getInstance()->set("mensaje_olvido",$message);
                Session::getInstance()->set("error_olvido","");
            } else {
                $message = "Lo sentimos ocurrio un error y no se pudo enviar su mensaje";
                Session::getInstance()->set("error_olvido",$message); 
            }
        }
		header('Location: /administracion/index/olvido');
	}

	public function logoutAction()
	{
		//LOG
		$data['log_tipo'] = "LOGOUT";
		$logModel = new Administracion_Model_DbTable_Log();
		$logModel->insert($data);

		Session::getInstance()->set("kt_login_id","");
		Session::getInstance()->set("kt_login_level","");
		Session::getInstance()->set("kt_login_user","");
		Session::getInstance()->set("kt_login_name","");
		header('Location: /administracion/');
	}

}