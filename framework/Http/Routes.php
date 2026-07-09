<?php

/**
*
*/
class Http_Routes
{
	const DEFAULT_MODULE = 'page';
	const DEFAULT_CONTROLLER = 'index';
    const DEFAULT_ACTION = 'index';

	protected $_module;
	protected $_controller;
	protected $_action;
	protected $_controllername;
	protected $_routs = array();

	function __construct()
	{
		$routes=explode('/',explode('?', $_SERVER['REQUEST_URI'])[0]);
		if ($routes[1]){
			$this->_module = $routes[1];
		} else {
			$this->_module = self::DEFAULT_MODULE;
		}
		if ($routes[2]){
			$this->_controller = $routes[2];
		} else{
			$this->_controller = self::DEFAULT_CONTROLLER;
		}
		if ($routes[3]){
			$this->_action = $routes[3];
		} else {
		 	$this->_action = self::DEFAULT_ACTION;
		}
		if(count($routes)>=4){
			for($r = 4; $r <= count($routes); $r++){
				array_push($this->_routs , $routes[$r]);
			}
		}
	}

	public function getModule()
	{
		return strtolower($this->_module);
	}

	public function getModuleClass()
	{
		return ucfirst(strtolower($this->_module));
	}

	public function getController()
	{
		return strtolower($this->_controller);
	}
	public function getControllerClass()
	{
		return ucfirst(strtolower($this->_controller));
	}
	public function getAction()
	{
		return strtolower($this->_action);
	}
	public function getRoutes()
	{
		return $this->_routs;
	}

}