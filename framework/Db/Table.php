<?php

abstract class Db_Table
{
  protected $_conn = null;
  protected $_name = '';
  protected $_id = '';

  function __construct()
  {
    $this->_conn = App::getDbConnection();
    if ($this->_name == '') {
      throw new Exception('Table name not set');
    }
  }


  public function getById($id)
  {
    $res = $this->_conn->query('SELECT * FROM ' . $this->_name . ' WHERE ' . $this->_id . ' = "' . $id . '"')->fetchAsObject();
    if (isset($res[0])) {
      return $res[0];
    }
    return false;
  }

  public function changeOrder($orden, $id)
  {
    $update = "UPDATE " . $this->_name . " SET orden ='" . $orden . "' WHERE " . $this->_id . " = '" . $id . "'";
    $this->_conn->query($update);
  }

  public function deleteRegister($id)
  {
    $update = "DELETE FROM " . $this->_name . " WHERE " . $this->_id . " = '" . $id . "'";
    $this->_conn->query($update);
  }

  public function getList($filters = '', $order = '')
  {
    $filter = '';
    if ($filters != '') {
      $filter = ' WHERE ' . $filters;
    }
    $orders = "";
    if ($order != '') {
      $orders = ' ORDER BY ' . $order;
    }
    $select = 'SELECT * FROM ' . $this->_name . ' ' . $filter . ' ' . $orders;
    $res = $this->_conn->query($select)->fetchAsObject();
    return $res;
  }

  public function getListPages($filters = '', $order = '', $page, $amount)
  {
    $filter = '';
    if ($filters != '') {
      $filter = ' WHERE ' . $filters;
    }
    $orders = "";
    if ($order != '') {
      $orders = ' ORDER BY ' . $order;
    }
    $select = 'SELECT * FROM ' . $this->_name . ' ' . $filter . ' ' . $orders . ' LIMIT ' . $page . ' , ' . $amount;
    $res = $this->_conn->query($select)->fetchAsObject();
    return $res;
  }

  public function editField($id, $field, $value)
  {
    $query = ' UPDATE ' . $this->_name . ' SET ' . $field . ' = "' . $value . '" WHERE ' . $this->_id . ' = "' . $id . '"';
    $res = $this->_conn->query($query);
  }
  public function getListCount($filters = "", $order = "")
  {
    $filter = '';
    if ($filters != '') {
      $filter = ' WHERE ' . $filters;
    }
    $orders = "";
    if ($order != '') {
      $orders = ' ORDER BY ' . $order;
    }
    $select = 'SELECT COUNT(*) AS total FROM ' . $this->_name . ' ' . $filter . ' ' . $orders;
    $res = $this->_conn->query($select)->fetchAsObject();
    return $res;
  }
}
