<?php 
/**
* clase que genera la insercion y edicion  de termino en la base de datos
*/
class Administracion_Model_DbTable_Terminos extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'terminos';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'termino_id';

	/**
	 * insert recibe la informacion de un termino y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data){
		$termino_estado = $data['termino_estado'];
		$termino_titulo = $data['termino_titulo'];
		$termino_enlace = $data['termino_enlace'];
		$termino_texto = $data['termino_texto'];
		$termino_seccion = $data['termino_seccion'];
		$query = "INSERT INTO terminos( termino_estado, termino_titulo, termino_enlace, termino_texto, termino_seccion) VALUES ( '$termino_estado', '$termino_titulo', '$termino_enlace', '$termino_texto', '$termino_seccion')";
		$res = $this->_conn->query($query);
        return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un termino  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data,$id){
		
		$termino_estado = $data['termino_estado'];
		$termino_titulo = $data['termino_titulo'];
		$termino_enlace = $data['termino_enlace'];
		$termino_texto = $data['termino_texto'];
		$termino_seccion = $data['termino_seccion'];
		$query = "UPDATE terminos SET  termino_estado = '$termino_estado', termino_titulo = '$termino_titulo', termino_enlace = '$termino_enlace', termino_texto = '$termino_texto', termino_seccion = '$termino_seccion' WHERE termino_id = '".$id."'";
		$res = $this->_conn->query($query);
	}
}