<?php

/**
* 
*/
class Http_Requests
{
	protected $_controller;
	protected $_metod;
	protected $_argument;
	protected $_params;
	protected $_isPost = false;

	function __construct($get, $post)
    {
        if (count($post) > 0) {
            $this->_isPost = true;
        }
        $this->_params = array_merge($get, $post);
    }

    public function _getParam($name, $default = null)
    {
        if (isset($this->_params[$name])) {
            return $this->_params[$name];
        }
        return $default;
    }

    public function isPost()
    {
        return $this->_isPost;
    }

    public function getControllerName()
    {
        return $this->_controller;
    }

    public function getViewName()
    {
        return $this->_view;
    }
}