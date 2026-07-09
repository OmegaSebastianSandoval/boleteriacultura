<?php

/**
 *
 */

class Administracion_mainController extends Controllers_Abstract
{
  protected $namepages;



  public function init()
  {
    $this->_view->botonpanel = $this->botonpanel;
    $this->setLayout('administracion_panel');
    $botoneralateral = $this->_view->getRoutPHP('modules/administracion/Views/partials/botoneralateral.php');
    $this->getLayout()->setData("panel_botones", $botoneralateral);
    $botonerasuperior = $this->_view->getRoutPHP('modules/administracion/Views/partials/botonerasuperior.php');
    $this->getLayout()->setData("panel_header", $botonerasuperior);
    // Excepción para modo validación - permitir acceso sin sesión

    $modoValidacion = $this->_getSanitizedParam("modo") === "validacion";
    $displayOnly = $this->_getSanitizedParam("display") === "1";

    if (!($modoValidacion && $displayOnly)) {
      // Solo verificar sesión si NO es modo validación
      if ((Session::getInstance()->get("kt_login_id") < 0 || Session::getInstance()->get("kt_login_id", "") == '')) {
        header('Location: /administracion/');
      }
      $inactivo = 9000000;
      if (Session::getInstance()->get('tiempo') != '') {
        $vida_session = time() - Session::getInstance()->get('tiempo');
        if ($vida_session > $inactivo) {
          session_destroy();
          header('Location: /administracion/?inactividad==1');
        }
      }
      Session::getInstance()->set("tiempo", time());
    }
  }

  public function changepageAction()
  {
    Session::getInstance()->set($this->namepages, $this->_getSanitizedParam("pages"));
  }

  public function orderAction()
  {
    $this->setLayout('blanco');
    $csrf = $this->_getSanitizedParam("csrf");
    if (Session::getInstance()->get('csrf')[$this->_csrf_section] == $csrf) {
      $id1 =  $this->_getSanitizedParam("id1");
      $id2 =  $this->_getSanitizedParam("id2");
      if (isset($id1) && $id1 > 0 && isset($id2) && $id2 > 0) {
        $content1 = $this->mainModel->getById($id1);
        $content2 = $this->mainModel->getById($id2);
        if (isset($content1) && isset($content2)) {
          $order1 = $content1->orden;
          $order2 = $content2->orden;
          $this->mainModel->changeOrder($order2, $id1);
          $this->mainModel->changeOrder($order1, $id2);
        }
      }
    }
  }

  public function deleteimageAction()
  {
    $this->setLayout('blanco');
    header('Content-Type:application/json');
    $campo = $this->_getSanitizedParam("campo");
    $id = $this->_getSanitizedParam("id");
    $csrf = $this->_getSanitizedParam("csrf");
    $elimino = 0;
    if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
      $id = $this->_getSanitizedParam("id");
      $content = $this->mainModel->getById($id);
      if ($content->$campo != '') {
        $modelUploadImage = new Core_Model_Upload_Image();
        $this->mainModel->editField($id, $campo, '');
        $modelUploadImage->delete($content->$campo);
        $elimino = 1;
      }
    }
    echo json_encode(array('elimino' => $elimino));
  }
  public function deletearchivoAction()
  {
    $this->setLayout('blanco');
    header('Content-Type:application/json');
    $campo = $this->_getSanitizedParam("campo");
    $id = $this->_getSanitizedParam("id");
    $csrf = $this->_getSanitizedParam("csrf");
    $elimino = 0;
    if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
      $id = $this->_getSanitizedParam("id");
      $content = $this->mainModel->getById($id);
      if ($content->$campo != '') {
        $modelUploadDocument = new Core_Model_Upload_Document();
        $this->mainModel->editField($id, $campo, '');
        $modelUploadDocument->delete($content->$campo);
        $elimino = 1;
      }
    }
    echo json_encode(array('elimino' => $elimino));
  }
  /**
   * obtenerCsrfEliminarReservaAction — devuelve un token csrf fresco para la sección por
   * defecto del controlador. Se usa justo antes de eliminarreservaAction porque ese token
   * rota en cualquier petición al controlador que no incluya "csrf" (ej. las llamadas AJAX
   * de cambiarEstadoMesa, mesasDisponiblesParaReasignar, etc.), así que uno capturado al
   * cargar la página queda obsoleto apenas el usuario interactúa con el mapa de mesas.
   */
  public function obtenerCsrfEliminarReservaAction()
  {
    $this->setLayout('blanco');
    header('Content-Type: application/json');
    echo json_encode(['csrf' => Session::getInstance()->get('csrf')[$this->_csrf_section] ?? '']);
    exit;
  }

  /**
   * eliminarreservaAction — endpoint reutilizable para eliminar una reserva por completo.
   * Cualquier controlador de administración lo hereda (ej: /administracion/reservas/eliminarreserva,
   * /administracion/infoboletas/eliminarreserva) porque vive en el controlador base.
   * Restringido al usuario con id 1 (kt_login_id).
   */
  public function eliminarreservaAction()
  {
    $this->setLayout('blanco');
    $id = $this->_getSanitizedParam("id");
    $csrf = $this->_getSanitizedParam("csrf");

    // Permite volver a la página donde se originó la acción (ej. manage.php de un
    // ambiente específico) en vez de siempre caer al listado ($this->route). Solo se
    // acepta una ruta interna del propio módulo de administración, nunca una externa.
    $redirect = $this->_getSanitizedParam("redirect");
    $volverA = (is_string($redirect) && strpos($redirect, '/administracion/') === 0) ? $redirect : $this->route;

    if (Session::getInstance()->get('kt_login_id') != '1') {
      Session::getInstance()->set('error_eliminar_reserva', 'No tienes permisos para eliminar reservas.');
      header('Location: ' . $volverA);
      exit;
    }

    if (Session::getInstance()->get('csrf')[$this->_csrf_section] != $csrf) {
      Session::getInstance()->set('error_eliminar_reserva', 'Token de seguridad inválido, intenta de nuevo.');
      header('Location: ' . $volverA);
      exit;
    }

    try {
      $resultado = $this->eliminarReservaCompleta($id);
    } catch (\Throwable $e) {
      $resultado = ['success' => false, 'message' => 'Error interno al eliminar la reserva.'];
    }

    Session::getInstance()->set(
      $resultado['success'] ? 'success_eliminar_reserva' : 'error_eliminar_reserva',
      $resultado['message']
    );

    header('Location: ' . $volverA);
    exit;
  }

  /**
   * eliminarReservaCompleta — borra una reserva y libera todo lo que quedó atado a ella:
   * mesas (mesa_estado), bloqueos de mesa, bloqueos de beneficiarios, cupos adicionales,
   * boletas generadas y los invitados de la reserva. Deja constancia en el log.
   * Reutilizable desde cualquier controlador que extienda Administracion_mainController.
   *
   * @param  int $reservaId
   * @return array ['success' => bool, 'message' => string]
   */
  protected function eliminarReservaCompleta($reservaId)
  {
    $reservaId = intval($reservaId);
    if ($reservaId <= 0) {
      return ['success' => false, 'message' => 'Reserva inválida'];
    }

    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reserva = $reservasModel->getById($reservaId);
    if (!$reserva) {
      return ['success' => false, 'message' => 'La reserva no existe'];
    }

    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $mesasBloqueoModel = new Administracion_Model_DbTable_Mesasbloqueo();
    $beneficiariosBloqueadosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();
    $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();
    $cuposModel = new Administracion_Model_DbTable_Reservacuposadicionales();
    $logModel = new Administracion_Model_DbTable_Log();

    // Mesas asociadas a la reserva (lista separada por comas, puede traer marcadores vacíos)
    $mesaIds = [];
    if (!empty($reserva->reserva_mesa_id)) {
      $mesaIds = array_values(array_filter(array_map('intval', explode(',', $reserva->reserva_mesa_id))));
    }

    // Invitados de la reserva (se necesitan antes de borrarlos para liberar sus bloqueos)
    $invitados = $invitadosModel->getList("reserva_id_reserva = '$reservaId'", "");
    $documentos = [];
    if (is_countable($invitados)) {
      foreach ($invitados as $invitado) {
        if (!empty($invitado->documento_invitado)) {
          $documentos[] = $invitado->documento_invitado;
        }
      }
    }

    // 1. Liberar las mesas de la reserva
    foreach ($mesaIds as $mesaId) {
      $mesasModel->editField($mesaId, 'mesa_estado', 0);
    }

    // 2. Liberar bloqueos de mesa asociados a la reserva o a sus mesas
    $mesasBloqueoModel->liberarPorReservaOMesas($reservaId, $mesaIds);

    // 3. Liberar bloqueos de beneficiarios asociados a los invitados de la reserva
    $beneficiariosBloqueadosModel->liberarPorDocumentos($documentos);

    // 4. Eliminar cupos adicionales de la reserva
    $cuposModel->deleteByReserva($reservaId);

    // 5. Eliminar boletas generadas de la reserva
    $boletasModel->deleteByReserva($reservaId);

    // 6. Eliminar los invitados de la reserva
    $invitadosModel->deleteInvitados($reservaId);

    // 7. Eliminar la reserva
    $reservasModel->deleteRegister($reservaId);

    // 8. Dejar constancia en el log
    $usuarioLog = Session::getInstance()->get('kt_login_user') ?: 'Sistema';
    $detalle = [
      'reserva_eliminada' => (array) $reserva,
      'mesas_liberadas' => $mesaIds,
      'documentos_con_bloqueos_liberados' => $documentos,
      'invitados_eliminados' => is_countable($invitados) ? count($invitados) : 0,
    ];
    $logModel->insert([
      'log_usuario' => $usuarioLog,
      'log_tipo' => 'ELIMINAR RESERVA',
      'log_fecha' => date('Y-m-d H:i:s'),
      'log_log' => "Reserva #$reservaId eliminada por el usuario ID " . Session::getInstance()->get('kt_login_id')
        . " ($usuarioLog).\n" . print_r($detalle, true),
    ]);

    return ['success' => true, 'message' => "Reserva #$reservaId eliminada correctamente. Mesas y bloqueos liberados."];
  }

  public function consultarSocio($ncar)
  {

    // $codi = Session::getInstance()->get('codi');
    // $ncar = Session::getInstance()->get('ncar');

    $loginServiceUrl = URL_BASE . '/querys/buscarUsuario.php';

    // Datos a enviar al servicio externo
    $postData = http_build_query([
      'token' => $this->generarToken(), //tken que recibe de la base de
      // 'codi' => $codi,
      'ncar' => $ncar
    ]);

    $ch = curl_init($loginServiceUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }
  public function generarToken()
  {
    $loginServiceUrl = 'https://ev.clubelnogal.com/tokens/querys/consultar_token.php';

    // Datos a enviar al servicio externo
    $postData = http_build_query([
      'inputUsername' => 'webnogal', //tken que recibe de la base de
      'inputPassword' => 'nogal2023*'
    ]);

    $ch = curl_init($loginServiceUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    // return $response;
    return $response->token;
  }

  public function ValidarIngresoSocio($mac_nume, $ing_iden, $ing_fing)
  {
    $url = 'https://ev.clubelnogal.com/NogalInvitados/querys/ValidarIngresoSocio.php';

    $postData = http_build_query([
      'token' => $this->generarToken(), //token que recibe de la base
      'MAC_NUME' => $mac_nume, // Numero de accion
      'ING_IDEN' => $ing_iden, // Identificacion del socio
      'ING_FING' => $ing_fing, // Fecha de ingreso del socio
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }
  public function ValidarPreingresoSocio($mac_nume, $ing_iden, $ing_fing)
  {
    $url = 'https://ev.clubelnogal.com/NogalInvitados/querys/ValidarPrengresoSocio.php';

    $postData = http_build_query([
      'token' => $this->generarToken(), //token que recibe de la base
      'MAC_NUME' => $mac_nume, // Numero de accion
      'ING_IDEN' => $ing_iden, // Identificacion del socio
      'ING_FING' => $ing_fing, // Fecha de ingreso del socio
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }
  public function ActualizarConsecutivo()
  {
    $url = 'https://ev.clubelnogal.com/NogalInvitados/querys/ActualizarConsecutivo.php';

    $postData = http_build_query([
      'token' => $this->generarToken(), //token que recibe de la base
      'CON_CODI' => '821', // Consecutivo interno de ingreso del socio
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }
  public function BuscarConsecutivo()
  {
    $url = 'https://ev.clubelnogal.com/NogalInvitados/querys/BuscarConsecutivo.php';

    $postData = http_build_query([
      'token' => $this->generarToken(), //token que recibe de la base
      'CON_CODI' => '821', // Consecutivo interno de ingreso del socio
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }

  public function InsertarSocio($data)
  {
    $url = 'https://ev.clubelnogal.com/NogalInvitados/querys/InsertSocios.php';

    $postData = http_build_query($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    // $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);
    return $response;
  }
  public function ConteoInvitados($mac_nume, $ing_iden, $ing_fing)
  {
    $url = 'https://ev.clubelnogal.com/NogalInvitados/querys/ValidarPrengresoSocio.php';

    $postData = http_build_query([
      'token' => $this->generarToken(), //token que recibe de la base
      'MAC_NUME' => $mac_nume, // Numero de accion
      'ING_IDEN' => $ing_iden, // Identificacion del socio
      'ING_FING' => $ing_fing, // Fecha de ingreso del socio
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }
  public function InsertarInvitado($data)
  {
    $url = 'https://ev.clubelnogal.com/NogalInvitados/querys/InsertInvitados.php';

    $postData = http_build_query($data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }

  public function InvitadosReservas($id)
  {
    $url = 'https://ev.clubelnogal.com/NogalInvitados/querys/ListarInvitadosReservas.php';

    $postData = http_build_query([
      'token' => $this->generarToken(), //token que recibe de la base
      'reserva' => $id, // Consecutivo interno de ingreso del socio
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }
  public function InvitadosEventos($id)
  {
    $url = 'https://ev.clubelnogal.com/NogalInvitados/querys/ListarInvitadosEventos.php';

    $postData = http_build_query([
      'token' => $this->generarToken(), //token que recibe de la base
      'evento' => $id, // Consecutivo interno de ingreso del socio
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }
  public function Acomodacion($acomodacion, $ing_cont, $inv_cont)
  {
    $url = 'https://ev.clubelnogal.com/NogalInvitados/querys/UpdateAccomodation.php';

    $postData = http_build_query([
      'token' => $this->generarToken(), //token que recibe de la base
      'ACOMODACION' => $acomodacion,
      'ING_CONT' => $ing_cont,
      'INV_CONT' => $inv_cont,
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }
  public function ListarInvitados($mac_nume, $soc_cont, $sbe_cont, $date)
  {
    $url = 'https://ev.clubelnogal.com/NogalInvitados/querys/ListarInvitados.php';

    $postData = http_build_query([
      'token' => $this->generarToken(), //token que recibe de la base
      'MAC_NUME' => $mac_nume,
      'SOC_CONT' => $soc_cont,
      'SBE_CONT' => $sbe_cont,
      'DATE' => $date,
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }
  public function consultarSocioInd($ncar)
  {

 
    if (!$ncar)
      return null;

    $loginServiceUrl = URL_BASE . '/querys/buscarUsuario.php';

    // Datos a enviar al servicio externo
    $postData = http_build_query([
      'token' => $this->generarToken(), //tken que recibe de la base de
      // 'codi' => $codi,
      'ncar' => $ncar
    ]);

    $ch = curl_init($loginServiceUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $response = json_decode($response);

    if (curl_errno($ch)) {
      echo 'Error cURL: ' . curl_error($ch);
      exit;
    }

    curl_close($ch);

    return $response;
  }
}
