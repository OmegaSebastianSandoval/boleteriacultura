<?php

class Http_Response
{
    protected $_body;

    public function setBody($content)
    {
        $this->_body = $content;
    }

    public function getBody()
    {
        return $this->_body;
    }
}