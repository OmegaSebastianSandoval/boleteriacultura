<?php

final class Db_Connection
{
    protected $_conn = null;
    protected $_host = null;
    protected $_user = null;
    protected $_dbname = null;
    protected $_password = null;
    protected $_port = null;

    private static $_instance = null;

    private function __construct()
    {
        $this->_host = Config_Config::getInstance()->getValue('db/host');
        $this->_dbname = Config_Config::getInstance()->getValue('db/name');
        $this->_user = Config_Config::getInstance()->getValue('db/user');
        $this->_password = Config_Config::getInstance()->getValue('db/password');
        $this->_port = Config_Config::getInstance()->getValue('db/port');
    }

    public function getConnection()
    {
        if (null == $this->_conn) {
            $this->_conn = new mysqli($this->_host, $this->_user, $this->_password, $this->_dbname, $this->_port);
            $this->_conn->set_charset('utf8');
            if ($this->_conn->connect_error) {
                throw new Exception('Could not connect to database');
            }
        }

        return $this->_conn;
    }

    public function query($query)
    {

        return new Db_Result($this->getConnection()->query($query));
    }

    public static function getInstance()
    {
        if (null == self::$_instance) {
            self::$_instance = new Db_Connection();
        }
        return self::$_instance;
    }
}