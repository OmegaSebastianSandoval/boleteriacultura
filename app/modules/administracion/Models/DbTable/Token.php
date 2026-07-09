<?php 
/**
* clase que genera la insercion y edicion  de token en la base de datos
*/
class Administracion_Model_DbTable_Token extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'token';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'id';

	/**
	 * insert recibe la informacion de un token y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data){
		$UsuarioID = $data['UsuarioID'];
		$Token = $data['Token'];
		$FechaCreacion = $data['FechaCreacion'];
		$Estado = $data['Estado'];
		$query = "INSERT INTO token( UsuarioID, Token, FechaCreacion, Estado) VALUES ( '$UsuarioID', '$Token', '$FechaCreacion', '$Estado')";
		$res = $this->_conn->query($query);
        return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un token  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data,$id){
		
		$UsuarioID = $data['UsuarioID'];
		$Token = $data['Token'];
		$FechaCreacion = $data['FechaCreacion'];
		$Estado = $data['Estado'];
		$query = "UPDATE token SET  UsuarioID = '$UsuarioID', Token = '$Token', FechaCreacion = '$FechaCreacion', Estado = '$Estado' WHERE id = '".$id."'";
		$res = $this->_conn->query($query);
	}
	public function getAdapter()
    {
        $res = $this->_conn;
        return $res;
    }

}