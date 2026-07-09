<?php

class Layout
{
    protected $_view;
    protected $_layoutFilename;
    protected $_content = '';
    protected $_titlepage = DEFAULT_TITLE;
    protected $_data =array();

    function __construct($layoutName)
    {
        $layoutName = str_replace('_','/',$layoutName);
        $this->_layoutFilename = LAYOUTS_PATH.$layoutName.'.php';
    }

    public function setView($view)
    {
        $this->_view = $view;
    }
    public function setTitle($title){
        $this->_titlepage=$title;
    }

    public function setData($name,$data){
        $this->_data[$name]= $data;
    }

    public function render($viewFilename)
    {
        ob_start();
        $this->_view->render($viewFilename);
        $this->_content = ob_get_clean();
        $fileContents = file_get_contents($this->_layoutFilename);
        return eval("?>".$fileContents);
    }
}