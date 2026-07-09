<?php

/**
*
*/

class App
{
	protected $_config;
    protected $_route;
    protected $_request;
    protected $_response;
    protected $_session;

	function __construct($path, $env, $config)
	{
        $this->_config = Config_Config::getInstance();
        $this->_session = Session::getInstance();
        $this->_config->init($config[$env]);
        $this->_response = new Http_Response();
        $this->_request = new Http_Requests($_GET, $_POST);  
        $this->_route = new Http_Routes();
	}

	public function init()
    {
    	$controllerName = $this->_route->getModuleClass()."_".$this->_route->getController()."Controller";
        $controller = new $controllerName($this->_response, $this->_request, $this->_route);
        $action=$this->_route->getAction().'Action';
        $controller->$action();
        $controller->render();
    }
    public static function getDbConnection()
    {
        return Db_Connection::getInstance();
    }

	public function run()
    {
        $this->init();
    }
}