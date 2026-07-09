<?php

/**
 *
 */

class Page_testController extends Page_mainController
{
  public $idEvento = 1; // Asumiendo que siempre es el ID 1 según tu contexto

  public function init()
  {


    parent::init();
    // Aquí puedes inicializar cualquier cosa específica para este controlador
  }
  public function indexAction()
  {
    // phpinfo();
    // error_reporting(E_ALL);
    $this->setLayout('blanco');

    $idReserva = $this->_getSanitizedParam("id");
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reservasInvitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
    $boletasModel = new Administracion_Model_DbTable_Boletasinfo();


    $reserva = $reservasModel->getById($idReserva);
    $invitadosReserva = $reservasInvitadosModel->getList("reserva_id_reserva = '$idReserva'", "orden ASC");

    $cantidadTotalReserva = $reserva->reserva_total_personas;
    $cantidadTotalInvitados = count($invitadosReserva);
    if ($cantidadTotalReserva != $cantidadTotalInvitados) {
      $this->_view->error = "La cantidad de invitados no coincide con la reserva.";
      exit;
    }
    $qrsGenerados = [];
    foreach ($invitadosReserva as $i => $invitado) {
      $i++; 
      $dataBoleta = [
        "boleta_reserva_id" => $idReserva,
        "boleta_numero_ticket" => $i,
        "boleta_estado" => 1,
        "boleta_fecha_creacion" => date("Y-m-d H:i:s"),
      ];
      $nextId = $boletasModel->getNextBoletaId();
      $id = $boletasModel->insert($dataBoleta);
      $token = base_convert($id, 10, 36);
      $yearMonth = date("Ym", strtotime($reserva->reserva_fecha));
      $customUid = "B-{$yearMonth}-" . str_pad($nextId, 7, "0", STR_PAD_LEFT);
      $baseString = "{$idReserva}-{$reserva->reserva_correo}-{$yearMonth}-{$nextId}";
      $token = substr(base_convert(hash('sha256', $baseString), 16, 36), 0, 12);
      $boletasModel->updateGeneratedQR($id, $customUid, $token, $this->idEvento, $invitado->id_invitado, $reserva->reserva_mesa_id ?? null);
      // $boletasModel->editField($id, "boleta_uid ", $customUid);
      // $boletasModel->editField($id, "boleta_token", $token);
      // $boletasModel->editField($id, "boleta_evento_id", $this->idEvento);
      $boleta = $boletasModel->getById($id);

      $qrsGenerados[] = [
        "boleta_id" => $id,
        "boleta_uid" => $customUid,
        "boleta_token" => $token,
        "boleta_numero_ticket" => $i,
        "rutaQR" => $this->generarQR($customUid, $token),
        "email" => $reserva->reserva_correo,
        "nombre" => $reserva->reserva_nombre_cliente . " " . $reserva->reserva_apellido_cliente,
        "telefono" => $reserva->reserva_telefono,
        "estado" => $boleta->boleta_estado,
      ];
      // echo "<pre>";
      // print_r($qrsGenerados);
      // echo "</pre>";
      $this->generarpdfs($reserva, $boleta);

    }

    $logModel = new Administracion_Model_DbTable_Log();
    $dataLog = array();
    $dataLog['log_log'] = print_r($qrsGenerados, true);
    $dataLog['log_tipo'] = 'QR GENERADOS';
    $logModel->insert($dataLog);
    $email = new Core_Model_Sendingemail($this->_view);
    // $email->generarCorreoBoleteria($infoVenta, $qrsGenerados);



    // echo '<pre>';
    // print_r($reserva);
    // print_r($invitadosReserva);
    // echo '</pre>';

    // echo "Hello, this is the testController index action!";



  }

  public function generarQR($uid, $token)
  {
    if (APPLICATION_ENV == 'development') {
      $textoQR = "http://192.168.150.58:8043/validacion?uid={$uid}&token={$token}";
    } else {

      $textoQR = "https://www.galeriacafelibro.com.co/validacion?uid={$uid}&token={$token}";
    }
    $rutaQR = "images_sales/qrs/{$uid}.png";
    QRcode::png($textoQR, $rutaQR, "Q", 5, 3);

    return $rutaQR;
  }
  public function generarpdfs($reserva, $boleta)
  {
    $this->setLayout('blanco');
    $this->_view->reserva = $reserva;
    $this->_view->boleta = $boleta;
    $eventoModel = new Administracion_Model_DbTable_Eventos();
    $evento = $eventoModel->getById($this->idEvento);
    $this->_view->evento = $evento;

    $pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetMargins(10, 10, 10);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(0);
    $pdf->SetAutoPageBreak(false, 0);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 12);
    $content = $this->_view->getRoutPHP('modules/page/Views/template/generarpdf.php');
    $pdf->writeHTML($content, true, false, true, false, '');
    // Si es un bono, agregamos la página de términos


    $pdf->AddPage(); // 👈 Agrega nueva página
    $pdf->SetFont('helvetica', '', 10); // Opcionalmente puedes poner otra fuente o tamaño


    ob_clean();
    $name = PDFS_PATH . "boleta_{$boleta->boleta_uid}.pdf";
    $pdf->SetTitle("Boleta {$boleta->boleta_uid}");

    $pdf->Output($name, 'F');
  }
}