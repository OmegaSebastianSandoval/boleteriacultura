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
      $this->email->getMail()->addBCC("desarrollo8@omegawebsystems.com");
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
      $nombreCliente = trim(($data->reserva_nombre_cliente ?? '') . ' ' . ($data->reserva_apellido_cliente ?? ''));

      // Estados de reserva segun el resultado del pago (ver placetopayController::procesarEstadoPago
      // y Page_eventoController::pagarAction): 2/3/11 = Aprobado (cargo/PlaceToPay/datafono),
      // 4 = Pendiente, 5 = Fallido, 6 = Rechazado. Misma plantilla para los 4 casos, solo cambia
      // color/icono/texto. Unicamente el correo de Aprobado lleva boton de accion (ctaTexto);
      // los demas son solo informativos, por eso su ctaTexto queda vacio.
      // Pendiente NO libera la mesa (se mantiene reservada mientras se confirma el pago);
      // solo Fallido/Rechazado la liberan (ver placetopayController::procesarEstadoPago).
      $notaMesaPendiente = 'Tu mesa se mantiene reservada mientras se confirma el pago. En caso de que el pago no sea confirmado, la mesa será liberada.';
      $notaMesaLiberada = 'Tu mesa quedó liberada por falta de confirmación del pago. Puedes intentar tu reserva nuevamente.';
      $notaBoleteriaPendiente = 'El resto de tu boletería (con el código QR de cada invitado) te llegará por correo tan pronto completes los datos de tus invitados.';
      $mensajeAprobado = 'Te confirmamos que tu pago se realizó exitosamente. Ahora, por favor no olvides completar la información requerida para autenticar a tus invitados: esto es muy importante para garantizar el acceso adecuado al evento.';
      $estadosCorreo = [
        '2' => ['color' => '#77bc23', 'icono' => '&#10003;', 'titulo' => '¡Tu pago fue confirmado!', 'mensaje' => $mensajeAprobado, 'nota' => $notaBoleteriaPendiente, 'notaBg' => '#eef8e5', 'notaTexto' => '#3a5a1f', 'asunto' => 'Confirmación de pago exitoso', 'ctaTexto' => 'Completar información de invitados', 'ctaLink' => $enlace_completo],
        '3' => ['color' => '#77bc23', 'icono' => '&#10003;', 'titulo' => '¡Tu pago fue confirmado!', 'mensaje' => $mensajeAprobado, 'nota' => $notaBoleteriaPendiente, 'notaBg' => '#eef8e5', 'notaTexto' => '#3a5a1f', 'asunto' => 'Confirmación de pago exitoso', 'ctaTexto' => 'Completar información de invitados', 'ctaLink' => $enlace_completo],
        '11' => ['color' => '#77bc23', 'icono' => '&#10003;', 'titulo' => '¡Tu pago fue confirmado!', 'mensaje' => $mensajeAprobado, 'nota' => $notaBoleteriaPendiente, 'notaBg' => '#eef8e5', 'notaTexto' => '#3a5a1f', 'asunto' => 'Confirmación de pago exitoso', 'ctaTexto' => 'Completar información de invitados', 'ctaLink' => $enlace_completo],
        '4' => ['color' => '#f0ad4e', 'icono' => '&#8987;', 'titulo' => 'Tu pago está pendiente de confirmación', 'mensaje' => 'Estamos a la espera de que la entidad de pago confirme tu transacción. Te enviaremos otro correo en cuanto se confirme.', 'nota' => $notaMesaPendiente, 'notaBg' => '#fdf4e9', 'notaTexto' => '#5a4b30', 'asunto' => 'Pago pendiente de confirmación', 'ctaTexto' => '', 'ctaLink' => ''],
        '5' => ['color' => '#dc3545', 'icono' => '&#10007;', 'titulo' => 'No pudimos procesar tu pago', 'mensaje' => 'Hubo un problema al procesar tu pago y no pudo completarse.', 'nota' => $notaMesaLiberada, 'notaBg' => '#fbeaea', 'notaTexto' => '#7a2530', 'asunto' => 'No se pudo procesar tu pago', 'ctaTexto' => '', 'ctaLink' => ''],
        '6' => ['color' => '#dc3545', 'icono' => '&#10007;', 'titulo' => 'Tu pago fue rechazado', 'mensaje' => 'La entidad de pago rechazó tu transacción.', 'nota' => $notaMesaLiberada, 'notaBg' => '#fbeaea', 'notaTexto' => '#7a2530', 'asunto' => 'Pago rechazado', 'ctaTexto' => '', 'ctaLink' => ''],
      ];
      $estadoInfo = $estadosCorreo[(string) $data->reserva_estado] ?? $estadosCorreo['3'];

      $asunto = ' ' . $estadoInfo['asunto'] . ' -  El Nogal';
      $this->_view->contenido = $data;
      $this->_view->enlace_completo = $estadoInfo['ctaLink'];
      $this->_view->estadoColor = $estadoInfo['color'];
      $this->_view->estadoIcono = $estadoInfo['icono'];
      $this->_view->estadoTitulo = $estadoInfo['titulo'];
      $this->_view->estadoMensaje = $estadoInfo['mensaje'];
      $this->_view->estadoNota = $estadoInfo['nota'];
      $this->_view->estadoNotaBg = $estadoInfo['notaBg'];
      $this->_view->estadoNotaTexto = $estadoInfo['notaTexto'];
      $this->_view->ctaTexto = $estadoInfo['ctaTexto'];
      // Mismo logo que ya usa generarcorreo.php/generarcorreonew.php para la boletería.
      $this->_view->logoUrl = 'https://nogalencasa.com/corte/logonegro.png';

      // Detalle de la compra para el cuerpo del correo
      $eventoModel = new Administracion_Model_DbTable_Eventos();
      $this->_view->evento = $eventoModel->getById($data->reserva_id_evento ?? 1);

      $nombreAmbiente = '';
      if ($data->reserva_mesa_id) {
        $mesasModel = new Administracion_Model_DbTable_Mesas();
        $ambientesModel = new Administracion_Model_DbTable_Ambientes();
        $mesaIds = array_map('trim', explode(',', $data->reserva_mesa_id));
        $mesa = $mesasModel->getById($mesaIds[0]);
        if ($mesa) {
          $ambiente = $ambientesModel->getById($mesa->mesa_ambiente);
          if ($ambiente) {
            $nombreAmbiente = $ambiente->ambiente_nombre;
          }
        }
      }
      $this->_view->nombreAmbiente = $nombreAmbiente;

      $informacionModel = new Page_Model_DbTable_Informacion();
      $informacion = $informacionModel->getList("", "orden ASC")[0];
      $correosOcultos = $informacion->info_pagina_correo_oculto;


      if (APPLICATION_ENV == 'production') {

        $this->email->getMail()->addAddress($correo, $nombreCliente);

        $correoArray = explode(",", $correosOcultos);
        foreach ($correoArray as $correoOculto) {
          $this->email->getMail()->addBCC(trim($correoOculto));
        }
      } else {
        $asunto = "[PRUEBAS] " . $asunto;
      }

      $this->email->getMail()->addBCC("desarrollo5@omegawebsystems.com");
      $this->email->getMail()->addBCC("soporteomega@omegawebsystems.com");
      $this->email->getMail()->addBCC("desarrollo8@omegawebsystems.com");
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
    $this->email->getMail()->addBCC("desarrollo8@omegawebsystems.com");
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
    $this->email->getMail()->addBCC("desarrollo8@omegawebsystems.com");

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
    $this->email->getMail()->addBCC("desarrollo8@omegawebsystems.com");

    $content = $this->_view->getRoutPHP('/../app/modules/core/Views/templatesemail/generarcorreonew.php');
    $this->email->getMail()->Subject = $asunto;
    $this->email->getMail()->msgHTML($content);
    $this->email->getMail()->AltBody = $content;
    return $this->email->sed();
  }
}
