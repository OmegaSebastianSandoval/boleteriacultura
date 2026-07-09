<?php 
/**
* clase que genera la clase dependiente  de mesa en la base de datos
*/
class Administracion_Model_DbTable_Dependambientes extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'ambientes';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'ambiente_id';

}