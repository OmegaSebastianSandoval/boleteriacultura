<?php

/**
 * Modelo del modulo Core que se encarga de  enviar todos los correos nesesarios del sistema.
 */
class Core_Model_Sendingemail
{
  /**
   * Intancia de la calse emmail
   * @var class
   */
  protected $email;

  protected $_view;

  public function __construct($view)
  {
    $this->email = new Core_Model_Mail();
    $this->_view = $view;
  }


  public function forgotpassword($user)
  {
    if ($user) {
      $code = [];
      $code['user'] = $user->user_id;
      $code['code'] = $user->code;
      $codeEmail = base64_encode(json_encode($code));
      $this->_view->url = "http://" . $_SERVER['HTTP_HOST'] . "/administracion/index/changepassword?code=" . $codeEmail;
      $this->_view->host = "http://" . $_SERVER['HTTP_HOST'] . "/";
      $this->_view->nombre = $user->user_names . " " . $user->user_lastnames;
      $this->_view->usuario = $user->user_user;
      /*fin parametros de la vista */
      //$this->email->getMail()->setFrom("desarrollo4@omegawebsystems.com","Intranet Coopcafam");
      $this->email->getMail()->addAddress($user->user_email, $user->user_names . " " . $user->user_lastnames);
      $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/forgotpassword.php');
      $this->email->getMail()->Subject = "Recuperación de Contraseña Gestor de Contenidos";
      $this->email->getMail()->msgHTML($content);
      $this->email->getMail()->AltBody = $content;
      if ($this->email->sed() == true) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function notificaPago($data)
  {
    if ($data) {

      $protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
      $dominio = $_SERVER['HTTP_HOST'];

      $enlace_completo = $protocolo . $dominio . "/page/login/?id_res=" . $data->id;

      $correo = $data->reserva_correo;
      $asunto = " Confirmación de pago exitoso -  El Nogal";
      $this->_view->contenido = $data;
      $this->_view->enlace_completo = $enlace_completo;
      $informacionModel = new Page_Model_DbTable_Informacion();
      $informacion = $informacionModel->getList("", "orden ASC")[0];
      $correosOcultos = $informacion->info_pagina_correo_oculto;


      if (APPLICATION_ENV == 'production') {

        // $this->email->getMail()->addAddress("" . $correo, $asunto);

        $correoArray = explode(",", $correosOcultos);
        foreach ($correoArray as $correoOculto) {
          //$this->email->getMail()->addBCC(trim($correoOculto), $asunto);
        }
      } else {
        $asunto = "[PRUEBAS] " . $asunto;
      }

      $this->email->getMail()->addBCC("desarrollo5@omegawebsystems.com", $asunto);
      $this->email->getMail()->addBCC("soporteomega@omegawebsystems.com", $asunto);
      $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/notifica.php');

      $this->email->getMail()->Subject = $asunto;
      $this->email->getMail()->msgHTML($content);
      $this->email->getMail()->AltBody = $content;
      return $this->email->sed();
    }
  }

  public function generarCorreoBoleteria($reserva, $qrsGenerados)
  {

    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $this->_view->evento = $eventoModel->getById($reserva->reserva_id_evento ?? 1);
    $this->_view->invitados = $invitadosModel->getList("reserva_id_reserva = $reserva->id ", "");
    $this->_view->boletas = $qrsGenerados;
    $this->_view->reserva = $reserva;
    $informacionModel = new Page_Model_DbTable_Informacion();
    $informacion = $informacionModel->getList("", "orden ASC")[0];
    $correosOcultos = $informacion->info_pagina_correo_oculto;
    $email = $reserva->reserva_correo;

    $asunto = $informacion->info_pagina_asunto_correo ?? "Envío Boleteria Club El Nogal";

    if (APPLICATION_ENV == 'production') {
      // $this->email->getMail()->addAddress($email, $asunto);

      $correoArray = explode(",", $correosOcultos);
      foreach ($correoArray as $correoOculto) {
        $this->email->getMail()->addBCC(trim($correoOculto), $asunto);
      }
    } else {
      $asunto = "[PRUEBAS] " . $asunto;
      // $this->email->getMail()->addBCC("desarrollo5@omegawebsystems.com", $asunto);
      // $this->email->getMail()->addBCC("soporteomega@omegawebsystems.com");
      $correoArray = explode(",", $correosOcultos);
      foreach ($correoArray as $correoOculto) {
        $this->email->getMail()->addBCC(trim($correoOculto), $asunto);
      }
    }
    $this->email->getMail()->Subject = $asunto;

    $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/generarcorreo.php');
    $this->email->getMail()->msgHTML($content);
    $this->email->getMail()->AltBody = $content;
    return $this->email->sed();
  }


  public function generarCorreoBoleteriaNew($reserva, $qrsGenerados)
  {

    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $this->_view->evento = $eventoModel->getById($reserva->reserva_id_evento ?? 1);
    $this->_view->invitados = $invitadosModel->getList("reserva_id_reserva = $reserva->id ", "");
    $this->_view->boletas = $qrsGenerados;
    $this->_view->reserva = $reserva;
    $informacionModel = new Page_Model_DbTable_Informacion();
    $informacion = $informacionModel->getList("", "orden ASC")[0];
    $correosOcultos = $informacion->info_pagina_correo_oculto;
    $email = $reserva->reserva_correo;
    $asunto = $informacion->info_pagina_asunto_correo ?? "Envío Boleteria Club El Nogal";



    if (APPLICATION_ENV == 'production') {
      // $this->email->getMail()->addAddress($email, $asunto);

      //iterar correos ocultos
      $correoArray = explode(",", $correosOcultos);
      foreach ($correoArray as $correoOculto) {
        //$this->email->getMail()->addBCC(trim($correoOculto), $asunto);
      }
    } else {
      $asunto = "[PRUEBAS] " . $asunto;
    }

    $this->email->getMail()->addBCC("desarrollo5@omegawebsystems.com", $asunto);

    $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/generarcorreonew.php');
    $this->email->getMail()->Subject = $asunto;
    $this->email->getMail()->msgHTML($content);
    $this->email->getMail()->AltBody = $content;
    return $this->email->sed();
  }

  public function generarCorreoBoleteriaNewUnico($reserva, $qrsGenerados)
  {

    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $this->_view->evento = $eventoModel->getById($reserva->reserva_id_evento ?? 1);
    $this->_view->invitados = $invitadosModel->getList("reserva_id_reserva = $reserva->id ", "");
    $this->_view->boletas = $qrsGenerados;
    $this->_view->reserva = $reserva;
    $informacionModel = new Page_Model_DbTable_Informacion();
    $informacion = $informacionModel->getList("", "orden ASC")[0];
    $correo = $informacion->info_pagina_correos_contacto;
    $asunto = $informacion->info_pagina_correos_contacto ?? "Envío Boleteria Club El Nogal";
    $this->email->getMail()->addBCC("desarrollo5@omegawebsystems.com", $asunto);

    $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/generarcorreonew.php');
    $this->email->getMail()->Subject = $asunto;
    $this->email->getMail()->msgHTML($content);
    $this->email->getMail()->AltBody = $content;
    return $this->email->sed();
  }
}
