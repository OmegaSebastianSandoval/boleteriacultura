<?php 
/**
* clase que genera la insercion y edicion  de Tokens de recuperacion en la base de datos
*/
class Administracion_Model_DbTable_Retrievetokens extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'retrieve_tokens';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'token_id';

	/**
	 * insert recibe la informacion de un Tokens de recuperacion y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data){
		$token_token = $data['token_token'];
		$token_date = $data['token_date'];
		$token_doc = $data['token_doc'];
		$token_ncar = $data['token_ncar'];
		$query = "INSERT INTO retrieve_tokens( token_token, token_date, token_doc, token_ncar) VALUES ( '$token_token', '$token_date', '$token_doc', '$token_ncar')";
		$res = $this->_conn->query($query);
        return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un Tokens de recuperacion  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data,$id){
		
		$token_token = $data['token_token'];
		$token_date = $data['token_date'];
		$token_doc = $data['token_doc'];
		$token_ncar = $data['token_ncar'];
		$query = "UPDATE retrieve_tokens SET  token_token = '$token_token', token_date = '$token_date', token_doc = '$token_doc', token_ncar = '$token_ncar' WHERE token_id = '".$id."'";
		$res = $this->_conn->query($query);
	}
}
