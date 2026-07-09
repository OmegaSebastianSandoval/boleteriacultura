<?php
/**
 * clase que genera la insercion y edicion  de colacompra en la base de datos
 */
class Administracion_Model_DbTable_Colacompra extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'cola_compras';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'cola_compras_id';

	/**
	 * insert recibe la informacion de un colacompra y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data)
	{
		$cola_compras_socio_documento = $data['cola_compras_socio_documento'];
		$cola_compras_estado = $data['cola_compras_estado'];
		$cola_compras_creado_el = $data['cola_compras_creado_el'];
		$cola_compras_inicio_el = $data['cola_compras_inicio_el'];
		$cola_compras_vence_el = $data['cola_compras_vence_el'];
		$query = "INSERT INTO cola_compras( cola_compras_socio_documento, cola_compras_estado, cola_compras_creado_el, cola_compras_inicio_el, cola_compras_vence_el) VALUES ( '$cola_compras_socio_documento', '$cola_compras_estado', '$cola_compras_creado_el', '$cola_compras_inicio_el', '$cola_compras_vence_el')";
		$res = $this->_conn->query($query);
		return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un colacompra  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data, $id)
	{

		$cola_compras_socio_documento = $data['cola_compras_socio_documento'];
		$cola_compras_estado = $data['cola_compras_estado'];
		$cola_compras_creado_el = $data['cola_compras_creado_el'];
		$cola_compras_inicio_el = $data['cola_compras_inicio_el'];
		$cola_compras_vence_el = $data['cola_compras_vence_el'];
		$query = "UPDATE cola_compras SET  cola_compras_socio_documento = '$cola_compras_socio_documento', cola_compras_estado = '$cola_compras_estado', cola_compras_creado_el = '$cola_compras_creado_el', cola_compras_inicio_el = '$cola_compras_inicio_el', cola_compras_vence_el = '$cola_compras_vence_el' WHERE cola_compras_id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}
	public function insertSimple($socioDocumento)
	{
		$fechaCreacion = date('Y-m-d H:i:s');
		$query = "INSERT INTO cola_compras(cola_compras_socio_documento, cola_compras_creado_el, cola_compras_estado) VALUES ('$socioDocumento', '$fechaCreacion', 'espera')";
		$res = $this->_conn->query($query);
		return mysqli_insert_id($this->_conn->getConnection());
	}

}