<?php

/**
 * Modelo del modulo Core que se encarga de inicializar  la clase de envio de correos
 */
class Core_Model_Mail
{
  /**
   * classe de  phpmailer
   * @var class
   */
  private $mail;

  /**
   * asigna los valores a la clase e instancia el phpMailer
   */
  public function __construct()
  {
    // Configuración principal: Office365

    $informacionModel = new Administracion_Model_DbTable_Informacion();
    $informacion = $informacionModel->getById(1);

    $host = $informacion->info_pagina_host;
    $port = $informacion->info_pagina_port;
    $username = $informacion->info_pagina_username;
    $password = $informacion->info_pagina_password;
    $correo_remitente = $informacion->info_pagina_correo_remitente;
    $nombre_remitente = $informacion->info_pagina_nombre_remitente;
    $debug = $informacion->info_pagina_correo_oculto;
    $debug = $informacion->info_pagina_debug;
    $smtp = $informacion->info_pagina_smtp;
    $this->mail = new PHPMailer;
    $this->mail->CharSet = 'UTF-8';
    $this->mail->isSMTP();
    $this->mail->SMTPDebug = $debug;
    $this->mail->SMTPSecure = $smtp;
    $this->mail->Host = $host;
    $this->mail->Port = $port;
    $this->mail->SMTPAuth = true;
    $this->mail->Username = $username;
    $this->mail->Password = $password;
    $this->mail->setFrom($correo_remitente, $nombre_remitente);
  }

  /**
   * retorna la  instancia de email
   * @return class email
   */
  public function getMail()
  {
    return $this->mail;
  }

  /**
   * envia el correo
   * @return bool envia el estado del correo
   */
  public function sed()
  {
    if ($this->mail->send()) {
      return 1;
    } else {
      // Guardar destinatarios, asunto y cuerpo
      $to = $this->mail->getToAddresses();
      $cc = $this->mail->getCcAddresses();
      $bcc = $this->mail->getBccAddresses();
      $subject = $this->mail->Subject;
      $body = $this->mail->Body;
      $altBody = $this->mail->AltBody;
      $isHTML = $this->mail->isHTML();

      // Reconfigurar con Gmail
      $this->mail = new PHPMailer;
      $this->mail->CharSet = 'UTF-8';
      $this->mail->isSMTP();
      $this->mail->SMTPDebug = 0;
      $this->mail->SMTPSecure = "tls";
      $this->mail->Host = "smtp.gmail.com";
      $this->mail->Port = 587;
      $this->mail->SMTPAuth = true;
      $this->mail->Username = "deliveryclubelnogal@gmail.com";
      $this->mail->Password = "igijajtcfiayccjs";
      $this->mail->setFrom("deliveryclubelnogal@gmail.com", "Nogal en casa");

      // Restaurar destinatarios, asunto y cuerpo
      foreach ($to as $addr) {
        $this->mail->addAddress($addr[0], $addr[1] ?? '');
      }
      foreach ($cc as $addr) {
        $this->mail->addCC($addr[0], $addr[1] ?? '');
      }
      foreach ($bcc as $addr) {
        $this->mail->addBCC($addr[0], $addr[1] ?? '');
      }
      $this->mail->Subject = $subject;
      $this->mail->Body = $body;
      $this->mail->AltBody = $altBody;
      if ($isHTML) {
        $this->mail->isHTML(true);
      }


      if ($this->mail->send()) {
        return 1;
      } else {

        $logData = array(
          'log_log' => "Error al enviar correo: " . $this->mail->ErrorInfo,
          'log_tipo' => 'ERROR ENVIO CORREO'
        );

        $logModel = new Administracion_Model_DbTable_Log();
        $logModel->insert($logData);
        return 2;
      }
    }
    return 2;
  }
}
