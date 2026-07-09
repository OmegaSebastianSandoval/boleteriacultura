<?php

/**
*
*/

class Session
{
	protected static $_instance = null;

	private function __construct()
    {
    }

    public function set($name,$value){
    	$_SESSION[$name]=$value;
    }

    public function get($name){
    	return $_SESSION[$name];
    }

	public static function getInstance()
    {
        if (null == self::$_instance) {
            self::$_instance = new Session();
        }
        return self::$_instance;
    }
}