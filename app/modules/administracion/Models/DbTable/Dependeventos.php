<?php 
/**
* clase que genera la clase dependiente  de Conferencista en la base de datos
*/
class Administracion_Model_DbTable_Dependeventos extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'eventos';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'evento_id';

}