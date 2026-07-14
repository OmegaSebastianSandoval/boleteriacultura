<?php

/**
 * Controlador de Eventos que permite la  creacion, edicion  y eliminacion de los eventos del Sistema
 */
class Administracion_eventosController extends Administracion_mainController
{
  public $botonpanel = 10;

  /**
   * $mainModel  instancia del modelo de  base de datos eventos
   * @var modeloContenidos
   */
  public $mainModel;

  /**
   * $route  url del controlador base
   * @var string
   */
  protected $route;

  /**
   * $pages cantidad de registros a mostrar por pagina]
   * @var integer
   */
  protected $pages;

  /**
   * $namefilter nombre de la variable a la fual se le van a guardar los filtros
   * @var string
   */
  protected $namefilter;

  /**
   * $_csrf_section  nombre de la variable general csrf  que se va a almacenar en la session
   * @var string
   */
  protected $_csrf_section = "administracion_eventos";

  /**
   * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
   * @var string
   */
  protected $namepages;

  protected $namepageactual = "";



  /**
   * Inicializa las variables principales del controlador eventos .
   *
   * @return void.
   */
  public function init()
  {
    $this->mainModel = new Administracion_Model_DbTable_Eventos();
    $this->namefilter = "parametersfiltereventos";
    $this->route = "/administracion/eventos";
    $this->namepages = "pages_eventos";
    $this->namepageactual = "page_actual_eventos";
    $this->_view->route = $this->route;
    if (Session::getInstance()->get($this->namepages)) {
      $this->pages = Session::getInstance()->get($this->namepages);
    } else {
      $this->pages = 20;
    }
    parent::init();
  }


  /**
   * Recibe la informacion y  muestra un listado de  eventos con sus respectivos filtros.
   *
   * @return void.
   */
  public function indexAction()
  {
    $title = "Administración de eventos";
    $this->getLayout()->setTitle($title);
    $this->_view->titlesection = $title;
    $this->filters();
    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
    $filters = (object) Session::getInstance()->get($this->namefilter);
    $this->_view->filters = $filters;
    $filters = $this->getFilter();
    $order = "orden ASC";
    $list = $this->mainModel->getList($filters, $order);
    $amount = $this->pages;
    $page = $this->_getSanitizedParam("page");
    if (!$page && Session::getInstance()->get($this->namepageactual)) {
      $page = Session::getInstance()->get($this->namepageactual);
      $start = ($page - 1) * $amount;
    } else if (!$page) {
      $start = 0;
      $page = 1;
      Session::getInstance()->set($this->namepageactual, $page);
    } else {
      Session::getInstance()->set($this->namepageactual, $page);
      $start = ($page - 1) * $amount;
    }
    $this->_view->register_number = count($list);
    $this->_view->pages = $this->pages;
    $this->_view->totalpages = ceil(count($list) / $amount);
    $this->_view->page = $page;
    $this->_view->lists = $this->mainModel->getListPages($filters, $order, $start, $amount);
    $this->_view->csrf_section = $this->_csrf_section;
  }

  /**
   * Genera la Informacion necesaria para editar o crear un  eventos  y muestra su formulario
   *
   * @return void.
   */
  public function manageAction()
  {
    $this->_view->route = $this->route;
    $this->_csrf_section = "manage_eventos_" . date("YmdHis");
    $this->_csrf->generateCode($this->_csrf_section);
    $this->_view->csrf_section = $this->_csrf_section;
    $this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];

    $id = $this->_getSanitizedParam("id");
    if ($id > 0) {
      $content = $this->mainModel->getById($id);

      $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
      $reservasModel = new Administracion_Model_DbTable_Reservas();
      $socios = $invitadosModel->getList("invitado_evento = '$id' AND invitado_tipo = '1'", "");
      $cantidadSocios = 0;
      foreach ($socios as $s) {
        $r = $reservasModel->getById($s->reserva_id_reserva);
        if ($r && $r->reserva_estado != 'C') {
          $cantidadSocios++;
        }
      }
      $this->_view->cantidadSocios = $cantidadSocios;


      if ($content->evento_id) {
        $this->_view->content = $content;
        $this->_view->routeform = $this->route . "/update";
        $title = "Actualizar configuración del evento";
        $this->getLayout()->setTitle($title);
        $this->_view->titlesection = $title;
      } else {
        $this->_view->routeform = $this->route . "/insert";
        $title = "Crear eventos";
        $this->getLayout()->setTitle($title);
        $this->_view->titlesection = $title;
      }
    } else {
      $this->_view->routeform = $this->route . "/insert";
      $title = "Crear eventos";
      $this->getLayout()->setTitle($title);
      $this->_view->titlesection = $title;
    }
  }

  /**
   * Inserta la informacion de un eventos  y redirecciona al listado de eventos.
   *
   * @return void.
   */
  public function insertAction()
  {
    $this->setLayout('blanco');
    $csrf = $this->_getSanitizedParam("csrf");
    if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
      $data = $this->getData();
      $uploadImage = new Core_Model_Upload_Image();
      if ($_FILES['evento_imagen_evento']['name'] != '') {
        $data['evento_imagen_evento'] = $uploadImage->upload("evento_imagen_evento");
      }
      if ($_FILES['evento_imagenfondo']['name'] != '') {
        $data['evento_imagenfondo'] = $uploadImage->upload("evento_imagenfondo");
      }

      if ($_FILES['evento_imagenfondo_home']['name'] != '') {
        $data['evento_imagenfondo_home'] = $uploadImage->upload("evento_imagenfondo_home");
      }

      if ($_FILES['evento_imagenfondo_login']['name'] != '') {
        $data['evento_imagenfondo_login'] = $uploadImage->upload("evento_imagenfondo_login");
      }

      if ($_FILES['evento_imagenfondo_home_responsive']['name'] != '') {
        $data['evento_imagenfondo_home_responsive'] = $uploadImage->upload("evento_imagenfondo_home_responsive");
      }

      if ($_FILES['evento_imagenfondo_login_responsive']['name'] != '') {
        $data['evento_imagenfondo_login_responsive'] = $uploadImage->upload("evento_imagenfondo_login_responsive");
      }
      $id = $this->mainModel->insert($data);
      $this->mainModel->changeOrder($id, $id);
      $data['evento_id'] = $id;
      $data['log_log'] = print_r($data, true);
      $data['log_tipo'] = 'CREAR EVENTOS';
      $logModel = new Administracion_Model_DbTable_Log();
      $logModel->insert($data);
    }
    header('Location: ' . $this->route . '' . '');
  }

  /**
   * Recibe un identificador  y Actualiza la informacion de un eventos  y redirecciona al listado de eventos.
   *
   * @return void.
   */
  public function updateAction()
  {
    $this->setLayout('blanco');
    $csrf = $this->_getSanitizedParam("csrf");
    if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
      $id = $this->_getSanitizedParam("id");
      $content = $this->mainModel->getById($id);
      if ($content->evento_id) {
        $data = $this->getData();
        $uploadImage = new Core_Model_Upload_Image();
        if ($_FILES['evento_imagen_evento']['name'] != '') {
          if ($content->evento_imagen_evento) {
            $uploadImage->delete($content->evento_imagen_evento);
          }
          $data['evento_imagen_evento'] = $uploadImage->upload("evento_imagen_evento");
        } else {
          $data['evento_imagen_evento'] = $content->evento_imagen_evento;
        }
        if ($_FILES['evento_imagenfondo']['name'] != '') {
          if ($content->evento_imagenfondo) {
            $uploadImage->delete($content->evento_imagenfondo);
          }
          $data['evento_imagenfondo'] = $uploadImage->upload("evento_imagenfondo");
        } else {
          $data['evento_imagenfondo'] = $content->evento_imagenfondo;
        }

        if ($_FILES['evento_imagenfondo_home']['name'] != '') {
          if ($content->evento_imagenfondo_home) {
            $uploadImage->delete($content->evento_imagenfondo_home);
          }
          $data['evento_imagenfondo_home'] = $uploadImage->upload("evento_imagenfondo_home");
        } else {
          $data['evento_imagenfondo_home'] = $content->evento_imagenfondo_home;
        }

        if ($_FILES['evento_imagenfondo_login']['name'] != '') {
          if ($content->evento_imagenfondo_login) {
            $uploadImage->delete($content->evento_imagenfondo_login);
          }
          $data['evento_imagenfondo_login'] = $uploadImage->upload("evento_imagenfondo_login");
        } else {
          $data['evento_imagenfondo_login'] = $content->evento_imagenfondo_login;
        }

        if ($_FILES['evento_imagenfondo_home_responsive']['name'] != '') {
          if ($content->evento_imagenfondo_home_responsive) {
            $uploadImage->delete($content->evento_imagenfondo_home_responsive);
          }
          $data['evento_imagenfondo_home_responsive'] = $uploadImage->upload("evento_imagenfondo_home_responsive");
        } else {
          $data['evento_imagenfondo_home_responsive'] = $content->evento_imagenfondo_home_responsive;
        }

        if ($_FILES['evento_imagenfondo_login_responsive']['name'] != '') {
          if ($content->evento_imagenfondo_login_responsive) {
            $uploadImage->delete($content->evento_imagenfondo_login_responsive);
          }
          $data['evento_imagenfondo_login_responsive'] = $uploadImage->upload("evento_imagenfondo_login_responsive");
        } else {
          $data['evento_imagenfondo_login_responsive'] = $content->evento_imagenfondo_login_responsive;
        }
        $this->mainModel->update($data, $id);
      }
      $data['evento_id'] = $id;
      $data['log_log'] = print_r($data, true);
      $data['log_tipo'] = 'EDITAR EVENTOS';
      $logModel = new Administracion_Model_DbTable_Log();
      $logModel->insert($data);
    }
    header('Location: ' . $this->route . '/manage?id=' . $id);
  }

  /**
   * Recibe un identificador  y elimina un eventos  y redirecciona al listado de eventos.
   *
   * @return void.
   */
  public function deleteAction()
  {
    $this->setLayout('blanco');
    $csrf = $this->_getSanitizedParam("csrf");
    if (Session::getInstance()->get('csrf')[$this->_csrf_section] == $csrf) {
      $id = $this->_getSanitizedParam("id");
      if (isset($id) && $id > 0) {
        $content = $this->mainModel->getById($id);
        if (isset($content)) {
          $uploadImage = new Core_Model_Upload_Image();
          if (isset($content->evento_imagen_evento) && $content->evento_imagen_evento != '') {
            $uploadImage->delete($content->evento_imagen_evento);
          }
          if (isset($content->evento_imagenfondo) && $content->evento_imagenfondo != '') {
            $uploadImage->delete($content->evento_imagenfondo);
          }
          if (isset($content->evento_imagenfondo_home) && $content->evento_imagenfondo_home != '') {
            $uploadImage->delete($content->evento_imagenfondo_home);
          }
          if (isset($content->evento_imagenfondo_login) && $content->evento_imagenfondo_login != '') {
            $uploadImage->delete($content->evento_imagenfondo_login);
          }

          if (isset($content->evento_imagenfondo_home_responsive) && $content->evento_imagenfondo_home_responsive != '') {
            $uploadImage->delete($content->evento_imagenfondo_home_responsive);
          }

          if (isset($content->evento_imagenfondo_login_responsive) && $content->evento_imagenfondo_login_responsive != '') {
            $uploadImage->delete($content->evento_imagenfondo_login_responsive);
          }
          //$this->mainModel->deleteRegister($id);
          $data = (array) $content;
          $data['log_log'] = print_r($data, true);
          $data['log_tipo'] = 'BORRAR EVENTOS';
          $logModel = new Administracion_Model_DbTable_Log();
          $logModel->insert($data);
        }
      }
    }
    header('Location: ' . $this->route . '' . '');
  }




  /**
   * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Eventos.
   *
   * @return array con toda la informacion recibida del formulario.
   */
  private function getData()
  {
    $data = array();
    $data['evento_titulo'] = $this->_getSanitizedParam("evento_titulo");
    $data['evento_fecha_auditoria'] = $this->_getSanitizedParam("evento_fecha_auditoria");
    $data['evento_fecha_apertura_reserva'] = $this->_getSanitizedParam("evento_fecha_apertura_reserva");
    $data['evento_fecha_cierre_reserva'] = $this->_getSanitizedParam("evento_fecha_cierre_reserva");
    $data['evento_descripcion'] = $this->_getSanitizedParamHtml("evento_descripcion");
    $data['evento_asignacion_lugar'] = $this->_getSanitizedParam("evento_asignacion_lugar");
    $data['evento_lugar'] = $this->_getSanitizedParam("evento_lugar");
    $data['evento_politica_reserva'] = $this->_getSanitizedParamHtml("evento_politica_reserva");
    $data['evento_acomodacion'] = $this->_getSanitizedParam("evento_acomodacion");
    $data['orden'] = $this->_getSanitizedParam("orden");
    $data['evento_fecha_creacion'] = $this->_getSanitizedParam("evento_fecha_creacion");
    $data['evento_fecha_actualizacion'] = $this->_getSanitizedParam("evento_fecha_actualizacion");
    $data['evento_usuario_creacion'] = $this->_getSanitizedParam("evento_usuario_creacion");
    $data['evento_usuario_actualizacion'] = $this->_getSanitizedParam("evento_usuario_actualizacion");
    if ($this->_getSanitizedParam("evento_cupo_maximo") == '') {
      $data['evento_cupo_maximo'] = '0';
    } else {
      $data['evento_cupo_maximo'] = $this->_getSanitizedParam("evento_cupo_maximo");
    }
    if ($this->_getSanitizedParam("evento_invitados_socio") == '') {
      $data['evento_invitados_socio'] = '0';
    } else {
      $data['evento_invitados_socio'] = $this->_getSanitizedParam("evento_invitados_socio");
    }
    $data['evento_imagen_evento'] = "";
    $data['evento_imagenfondo'] = "";
    $data['evento_fecha_inicio'] = $this->_getSanitizedParam("evento_fecha_inicio");
    $data['evento_fecha_fin'] = $this->_getSanitizedParam("evento_fecha_fin");
    $fechaInicio = new DateTime($data['evento_fecha_inicio']);
    $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $data['evento_dia_semana'] = $diasSemana[(int)$fechaInicio->format('w')];
    $eventoFecha = $this->_getSanitizedParam("evento_fecha");
    $data['evento_fecha'] = !empty($eventoFecha) ? $eventoFecha : '0000-00-00';
    $data['evento_colorfondo'] = $this->_getSanitizedParam("evento_colorfondo");

    if ($this->_getSanitizedParam("evento_datafono") == '') {
      $data['evento_datafono'] = '0';
    } else {
      $data['evento_datafono'] = $this->_getSanitizedParam("evento_datafono");
    }

    if ($this->_getSanitizedParam("evento_cuotas") == '') {
      $data['evento_cuotas'] = '0';
    } else {
      $data['evento_cuotas'] = $this->_getSanitizedParam("evento_cuotas");
    }
    $data['evento_max_cuotas'] = $this->_getSanitizedParam("evento_max_cuotas");
    $data['evento_imagenfondo_home'] = "";
    $data['evento_imagenfondo_login'] = "";

    $data['evento_imagenfondo_home_responsive'] = "";
    $data['evento_imagenfondo_login_responsive'] = "";

    if ($this->_getSanitizedParam("evento_menu_habilitado") == '') {
      $data['evento_menu_habilitado'] = '0';
    } else {
      $data['evento_menu_habilitado'] = $this->_getSanitizedParam("evento_menu_habilitado");
    }

    if ($this->_getSanitizedParam("evento_invitados_permitidos") == '') {
      $data['evento_invitados_permitidos'] = '0';
    } else {
      $data['evento_invitados_permitidos'] = $this->_getSanitizedParam("evento_invitados_permitidos");
    }

    return $data;
  }






  /**
   * Genera la consulta con los filtros de este controlador.
   *
   * @return array cadena con los filtros que se van a asignar a la base de datos
   */
  protected function getFilter()
  {
    $filtros = " 1 = 1 ";
    if (Session::getInstance()->get($this->namefilter) != "") {
      $filters = (object) Session::getInstance()->get($this->namefilter);
      if ($filters->evento_titulo != '') {
        $filtros = $filtros . " AND evento_titulo LIKE '%" . $filters->evento_titulo . "%'";
      }
    }
    return $filtros;
  }

  /**
   * Recibe y asigna los filtros de este controlador
   *
   * @return void
   */
  protected function filters()
  {
    if ($this->getRequest()->isPost() == true) {
      Session::getInstance()->set($this->namepageactual, 1);
      $parramsfilter = array();
      $parramsfilter['evento_titulo'] = $this->_getSanitizedParam("evento_titulo");
      Session::getInstance()->set($this->namefilter, $parramsfilter);
    }
    if ($this->_getSanitizedParam("cleanfilter") == 1) {
      Session::getInstance()->set($this->namefilter, '');
      Session::getInstance()->set($this->namepageactual, 1);
    }
  }
}
