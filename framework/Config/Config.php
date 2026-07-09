<?php

/**
*
*/

class Config_Config
{
    private $_config;
    protected static $_instance = null;

    private function __construct(){}

    public function init($config = array())
    {
        $this->_config = $config;
    }

    public function getValue($path){
        $path= explode('/',$path);
        $campo1=$path[0];
        $campo2=$path[1];
        return $this->_config[$campo1][$campo2];
    }
    public static function getInstance(){
        if (!isset(self::$_instance)) {
            self::$_instance = new Config_Config();
        }
        return self::$_instance;
    }

    public function __clone()
    {
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
    }

}