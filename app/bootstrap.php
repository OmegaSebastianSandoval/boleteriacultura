<?php
session_start();
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)) . DS);
define('FRAMEWORK_PATH', ROOT . '../framework' . DS);
define('APP_PATH', ROOT . '../app' . DS);
define('VIEWS_PATH', APP_PATH . 'View' . DS);
define('LAYOUTS_PATH', APP_PATH . 'layout' . DS);
define('IMAGE_PATH', APP_PATH . "../public_html/images/");
define('FILE_PATH', APP_PATH . "../public_html/files/");
define('PUBLIC_PATH', APP_PATH . "../public_html/");
define('PDFS_PATH', APP_PATH . "../public_html/pdfs/");
define('PDFS_PATH_NEWS', APP_PATH . "../public_html/pdfs_news/");


// $_SESSION['test'] = 123;
// echo $_SESSION['test']  ?? 'no hay test';
//phpinfo();

date_default_timezone_set('America/Bogota');
//phpinfo();
require_once FRAMEWORK_PATH . 'Config/Config.php';
set_include_path(
  implode(
    PATH_SEPARATOR,
    array(
      get_include_path(),
      FRAMEWORK_PATH
    )
  )
);

function framework_autoload($classname)
{
  $ruta = explode('_', $classname);
  if (substr(end($ruta), -10) == 'Controller') {
    $file = strtolower($ruta[0]) . '/Controllers/' . $ruta[1] . '.php';
    if (file_exists(APP_PATH . 'modules/' . $file)) {
      require_once(APP_PATH . 'modules/' . $file);
    }
  } else if (isset($ruta[1]) && $ruta[1] == 'Model') {
    $file = strtolower($ruta[0]) . "/Models/";
    unset($ruta[0]);
    unset($ruta[1]);
    $file = $file . implode("/", $ruta) . '.php';
    if (file_exists(APP_PATH . 'modules/' . $file)) {
      require_once(APP_PATH . 'modules/' . $file);
    }
  } else {
    $file = implode("/", $ruta) . '.php';
    if (file_exists(APP_PATH . '../framework/' . $file)) {
      require_once($file);
    }
  }
}
spl_autoload_register('framework_autoload');
include(APP_PATH . '/../vendor/autoload.php');
require_once APP_PATH . 'helpers/idcipher.php';
$env = "development";
if (strpos($_SERVER['HTTP_HOST'], "cenadegala.clubelnogal.com") !== false or strpos($_SERVER['HTTP_HOST'], "boleteriavirtual.wsnogal.com") !== false) {
  //$env = "production";
  $env = "staging";
} else if (strpos($_SERVER['HTTP_HOST'], "wsnogal.us.to") !== false) {
  $env = "production";
}

// echo $env;
define('APPLICATION_ENV', getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : $env);

if (APPLICATION_ENV == 'development') {
  define('RUTA', "http://localhost:8043");
  define('URL_BASE', "https://ev.clubelnogal.com/ConsultaAsociadosPruebas");
  // define('URL_BASE', "https://ev.clubelnogal.com/ConsultaAsociadosGala");
  define('URL_BASE_LOGIN', "https://ev.clubelnogal.com/iniciosesionPruebas/querys/loginPassword.php");
} else if (APPLICATION_ENV == 'staging') {
  define('RUTA', "https://cenadegala.clubelnogal.com");
  define('URL_BASE', "https://ev.clubelnogal.com/ConsultaAsociadosPruebas");
  define('URL_BASE_LOGIN', "https://ev.clubelnogal.com/iniciosesionPruebas/querys/loginPassword.php");
} else if (APPLICATION_ENV == 'production') {
  define('RUTA', "https://boleteriavirtual.clubelnogal.com");
  define('URL_BASE', "https://ev.clubelnogal.com/ConsultaAsociadosGala");
  define('URL_BASE_LOGIN', "https://ev.clubelnogal.com/iniciosesion/querys/loginPassword.php");
}

if (!headers_sent()) {
  $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
  $csp = [
    "default-src 'self'",
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://www.gstatic.com https://maps.googleapis.com https://s.ytimg.com https://unpkg.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://code.jquery.com https://www.googletagmanager.com",
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
    "img-src 'self' data: blob: https://elnogalwellnessnuts.com https://maps.gstatic.com https://maps.googleapis.com",
    "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
    "connect-src 'self' https://www.google.com https://www.gstatic.com https://maps.googleapis.com",
    "frame-src 'self' https://www.google.com https://www.youtube.com",
    "object-src 'none'",
    "base-uri 'self'",
    "form-action 'self' https://checkout.placetopay.com https://checkout-test.placetopay.com",
    "frame-ancestors 'self'"
  ];

  if ($isHttps) {
    $csp[] = 'upgrade-insecure-requests';
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
  }

  header('Content-Security-Policy: ' . implode('; ', $csp));
  header('X-Frame-Options: SAMEORIGIN');
  header('X-Content-Type-Options: nosniff');
  header('Referrer-Policy: strict-origin-when-cross-origin');
  header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
}

error_reporting(E_STRICT);
if ($_GET['debug'] == "1") {
  error_reporting(E_ALL);
}
ini_set("display_errors", 1);

if (!file_exists(IMAGE_PATH)) {
  mkdir(IMAGE_PATH, 0777, true);
}

if (!file_exists(FILE_PATH)) {
  mkdir(FILE_PATH, 0777, true);
}


//Imports para QR y PDF
require_once '../vendor/phpqrcode/qrlib.php';
require_once '../vendor/tecnick.com/tcpdf/tcpdf.php';

// Configuración CORS
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type, token');
