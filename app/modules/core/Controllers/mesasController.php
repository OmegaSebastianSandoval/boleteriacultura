<?php
/**
* Controlador de Usuario que permite la  creacion, edicion  y eliminacion de los Usuarios del Sistema
*/
class Core_userController extends Controllers_Abstract
{
	public function validationnombreAction()
    {
               $mesasModel = new Administracion_Model_DbTable_Mesas();

        $nombre= $this->_getSanitizedParam("mesa_nombre");
        $nombre2= $this->_getSanitizedParam("nombre");
        $res_user = $mesasModel->getList("mesa_nombre = '$nombre'" ,"");
        if(  $nombre2 !='' &&  $nombre2 ==  $nombre  ){
            http_response_code(200);
        } else {
	        if ( $res_user != false ) {
	            header("HTTP/1.0 400 Usuario no Disponible");
	        } else {
	            http_response_code(200);
	        }
    	}
    }

    public function validationcodigoAction()
    {
        $mesasModel = new Administracion_Model_DbTable_Mesas();
        $codigo= $this->_getSanitizedParam("mesa_codigo");
        $codigo2= $this->_getSanitizedParam("codigo");
        $res_user = $mesasModel->getList("mesa_codigo = '$codigo'" ,"");
        if( $codigo2 !='' && $codigo2 == $codigo  ){
            http_response_code(200);
        } else {
            if ( isset($res_user[0])) {
                header("HTTP/1.0 400 Codigo ya existe");
            } else {
                http_response_code(200);
            }
        }
    }



}