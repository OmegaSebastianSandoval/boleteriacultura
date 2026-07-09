<?php 
/**
* clase que genera la insercion y edicion  de logs en la base de datos
*/
class Administracion_Model_DbTable_Log extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'log';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'log_id';

	/**
	 * Normaliza cualquier valor a un string seguro para concatenar en SQL.
	 * Los llamadores de este modelo (hay decenas en todo el sitio) a veces
	 * pasan arreglos crudos (p.ej. $_GET) en campos pensados para texto; antes
	 * PHP los convertia silenciosamente a la palabra "Array" al concatenarlos,
	 * ahora los codificamos a JSON para no perder informacion ni tronar.
	 *
	 * @param mixed $value
	 * @return string
	 */
	private function _toDbString($value)
	{
		if ($value === null) {
			return '';
		}
		if (is_array($value) || is_object($value)) {
			return json_encode($value, JSON_UNESCAPED_UNICODE);
		}
		if (is_bool($value)) {
			return $value ? '1' : '0';
		}
		return (string) $value;
	}

	/**
	 * insert recibe la informacion de un log y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data){
		$conn = $this->_conn->getConnection();
		$log_usuario = $this->_toDbString($data['log_usuario'] ?? '');
		$log_tipo = $this->_toDbString($data['log_tipo'] ?? '');
		$log_log = $this->_toDbString($data['log_log'] ?? '');
		$log_fecha = date("Y-m-d H:i:s");
		if($_SESSION['kt_login_user']!=""){
			$log_usuario = $_SESSION['kt_login_user'];
		}
		// Escapar antes de concatenar: sin esto, secuencias como \n dentro del JSON
		// son interpretadas por MySQL como el propio caracter de control, dejando
		// el JSON guardado invalido (json_decode falla al leerlo despues).
		$log_usuario = $conn->real_escape_string($log_usuario);
		$log_tipo = $conn->real_escape_string($log_tipo);
		$log_fecha = $conn->real_escape_string($log_fecha);
		$log_log = $conn->real_escape_string($log_log);
		$query = "INSERT INTO log( log_usuario, log_tipo, log_fecha, log_log) VALUES ( '$log_usuario', '$log_tipo', '$log_fecha', '$log_log')";
		//echo $query;
		$res = $this->_conn->query($query);
        return mysqli_insert_id($conn);
	}

	public function insertBatch($rows){
		if (empty($rows)) {
			return;
		}
		$conn = $this->_conn->getConnection();

		$log_usuario = '';
		if (!empty($_SESSION['kt_login_user'])) {
			$log_usuario = $_SESSION['kt_login_user'];
		}
		$log_usuario = $conn->real_escape_string($log_usuario);
		$log_fecha = date("Y-m-d H:i:s");

		$values = [];
		foreach ($rows as $row) {
			$tipo = $conn->real_escape_string($this->_toDbString($row['log_tipo'] ?? ''));
			$log = $conn->real_escape_string($this->_toDbString($row['log_log'] ?? ''));
			$values[] = "('$log_usuario', '$tipo', '$log_fecha', '$log')";
		}

		$query = "INSERT INTO log (log_usuario, log_tipo, log_fecha, log_log) VALUES " . implode(', ', $values);
		$this->_conn->query($query);
	}

	/**
	 * update Recibe la informacion de un log  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data,$id){
		$log_usuario = $data['log_usuario'];
		$log_tipo = $data['log_tipo'];
		$log_fecha = $data['log_fecha'];
		$log_log = $data['log_log'];
		$query = "UPDATE log SET  log_usuario = '$log_usuario', log_tipo = '$log_tipo', log_fecha = '$log_fecha', log_log = '$log_log' WHERE log_id = '".$id."'";
		$res = $this->_conn->query($query);
	}

    public function getTipos($filters = '',$order = '')
    {
        $filter = '';
        if($filters != ''){
            $filter = ' WHERE '.$filters;
        }
        $orders ="";
        if($order != ''){
            $orders = ' ORDER BY '.$order;
        }
        $select = 'SELECT log_tipo FROM '.$this->_name.' '.$filter.' GROUP BY log_tipo '.$orders;
        $res = $this->_conn->query( $select )->fetchAsObject();
        return $res;
    }

}