<?php

class Page_leerpdfController extends Page_mainController
{
  public function indexAction()
  {
    $this->setLayout('blanco');
    if (isset($_GET['token'])) {
      $token = $this->_getSanitizedParam("token");
      $ruta = $this->decryptString($token);
      if ($ruta && file_exists($ruta)) {
        header("Content-Type: application/pdf");
        readfile($ruta);
        exit;
      } else {
        echo "❌ Archivo no encontrado o token inválido.";
        exit;
      }
    } else {
      echo "❌ Token no proporcionado.";
      exit;
    }
  }
  function decryptString($encryptedString, $key = 'omeganogal2025')
  {
    $data = base64_decode($encryptedString);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
  }
}