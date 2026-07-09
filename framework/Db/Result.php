<?php

class Db_Result
{
    protected $_res;

    function __construct($res)
    {
        $this->_res = $res;
    }

    public function fetchAsObject()
    {
        $return = array();
        if($this->_res){
            while ($obj = $this->_res->fetch_object()) {
                $return[] = $obj;
            }
        }
        return $return;
    }

    public function fetchAsArray()
    {
        $return = array();
        while ($obj = $this->_res->fetch_array()) {
            $return[] = $obj;
        }
        return $return;
    }
}