<?php

/**
 * Controlador de Información que permite la  creacion, edicion  y eliminacion de la información del Sistema
 */
class Administracion_infodocumentoController extends Administracion_mainController
{
	public $botonpanel = 13;

	/**
	 * $mainModel  instancia del modelo de  base de datos información
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
	protected $_csrf_section = "administracion_dashboard";

	/**
	 * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
	 * @var string
	 */
	protected $namepages;

	protected $namepageactual;



	/**
	 * Inicializa las variables principales del controlador accionsesiones .
	 *
	 * @return void.
	 */
	public function init()
	{
		$this->namefilter = "parametersfilterinfodocumento";
		$this->route = "/administracion/infodocumento";
		$this->namepages = "pages_infodocumento";
		$this->namepageactual = "page_actual_infodocumento";
		$this->_view->route = $this->route;
		if (Session::getInstance()->get($this->namepages)) {
			$this->pages = Session::getInstance()->get($this->namepages);
		} else {
			$this->pages = 20;
		}
		parent::init();
	}


	/**
	 * Recibe la informacion y  muestra un listado de  accionsesion con sus respectivos filtros.
	 *
	 * @return void.
	 */
	public function indexAction()
	{
		$title = "Reporte de Reservas";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;


		// Procesar búsqueda por documento
		$resultados = [];
		$tipo = null;
		$documento = $this->_getSanitizedParam("documento_busqueda");
		$reservaId = null;

		if ($documento) {
			$reservasModel = new Administracion_Model_DbTable_Reservas();
			$mesasModel = new Administracion_Model_DbTable_Mesas();
			$ambientesModel = new Administracion_Model_DbTable_Ambientes();
			$pisosModel = new Administracion_Model_DbTable_Pisos();
			$invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();

			// Buscar todas las reservas por documento
			$filtroPrincipal = "reserva_documento = '" . addslashes($documento) . "' AND reserva_estado IN (2,3,11)";
			$reservasPorDocumento = $reservasModel->getList($filtroPrincipal, "id DESC");

			// Si no encuentra por documento, buscar por número de carnet
			if (empty($reservasPorDocumento)) {
				$filtroCarnet = "reserva_numero_carnet = '" . addslashes($documento) . "' AND reserva_estado IN (2,3,11)";
				$reservasPorDocumento = $reservasModel->getList($filtroCarnet, "id DESC");
			}			// Si no encuentra buscar por id de la reserva (única)
			if (empty($reservasPorDocumento)) {
				$filtroId = "id = '" . intval($documento) . "'";
				$reservasPorDocumento = $reservasModel->getList($filtroId, "id DESC");
			}

			if (!empty($reservasPorDocumento)) {
				$tipo = "principal";
				foreach ($reservasPorDocumento as $reserva) {
					$mesasIds = explode(",", $reserva->reserva_mesa_id ?? '');
					$mesasInfo = [];
					foreach ($mesasIds as $mesasId) {
						$mesaInd = $mesasModel->getById($mesasId);
						if ($mesaInd) {
							$ambiente = null;
							$piso = null;
							if (!empty($mesaInd->mesa_ambiente)) {
								$ambiente = $ambientesModel->getById($mesaInd->mesa_ambiente);
								if ($ambiente && !empty($ambiente->ambiente_piso)) {
									$piso = $pisosModel->getById($ambiente->ambiente_piso);
								}
							}
							$mesaInd->ambiente_nombre = $ambiente->ambiente_nombre ?? '';
							$mesaInd->piso_nombre = $piso->piso_nombre ?? '';
							$mesasInfo[] = $mesaInd;
						}
					}
					$invitados = $invitadosModel->getList("reserva_id_reserva = '" . intval($reserva->id) . "'", "id_invitado ASC");
					$resultados[] = [
						'reserva' => $reserva,
						'invitados' => $invitados,
						'mesas' => $mesasInfo
					];
				}
			} else {
				// Buscar como invitado (puede haber varios)
				$filtroInvitado = "documento_invitado = '" . addslashes($documento) . "'";
				$invitados = $invitadosModel->getList($filtroInvitado, "id_invitado DESC");
				if (!empty($invitados)) {
					$tipo = "invitado";
					foreach ($invitados as $invitado) {
						$reservaId = $invitado->reserva_id_reserva;
						$reservaPrincipal = $reservasModel->getList("id = '" . intval($reservaId) . "' AND reserva_estado IN (2,3,11)", "id DESC");
						$reserva = !empty($reservaPrincipal) ? $reservaPrincipal[0] : null;
						$mesasIds = explode(",", $reserva->reserva_mesa_id ?? '');
						$mesasInfo = [];
						foreach ($mesasIds as $mesasId) {
							$mesaInd = $mesasModel->getById($mesasId);
							if ($mesaInd) {
								$ambiente = null;
								$piso = null;
								if (!empty($mesaInd->mesa_ambiente)) {
									$ambiente = $ambientesModel->getById($mesaInd->mesa_ambiente);
									if ($ambiente && !empty($ambiente->ambiente_piso)) {
										$piso = $pisosModel->getById($ambiente->ambiente_piso);
									}
								}
								$mesaInd->ambiente_nombre = $ambiente->ambiente_nombre ?? '';
								$mesaInd->piso_nombre = $piso->piso_nombre ?? '';
								$mesasInfo[] = $mesaInd;
							}
						}
						$invitadosReserva = $invitadosModel->getList("reserva_id_reserva = '" . intval($reservaId) . "'", "id_invitado ASC");
						$resultados[] = [
							'invitado' => $invitado,
							'reserva' => $reserva,
							'invitados' => $invitadosReserva,
							'mesas' => $mesasInfo
						];
					}
				} else if (!empty($filtroCarnet)) {
					// Si se buscó por carnet y no hay reserva ni invitado, consultarSocioInd
					$socioData = $this->consultarSocioInd($documento);
					if ($socioData && ($socioData->SBE_CODI)) {
						$invitadoCarnet = $invitadosModel->getList("documento_invitado = '" . ($socioData->SBE_CODI) . "'", "id_invitado DESC");
						if (!empty($invitadoCarnet)) {
							$tipo = "invitado";
							foreach ($invitadoCarnet as $invitado) {
								$reservaId = $invitado->reserva_id_reserva;
								$reservaPrincipal = $reservasModel->getList("id = '" . intval($reservaId) . "' AND reserva_estado IN (2,3,11)", "id DESC");
								$reserva = !empty($reservaPrincipal) ? $reservaPrincipal[0] : null;
								$mesasIds = explode(",", $reserva->reserva_mesa_id ?? '');
								$mesasInfo = [];
								foreach ($mesasIds as $mesasId) {
									$mesaInd = $mesasModel->getById($mesasId);
									if ($mesaInd) {
										$ambiente = null;
										$piso = null;
										if (!empty($mesaInd->mesa_ambiente)) {
											$ambiente = $ambientesModel->getById($mesaInd->mesa_ambiente);
											if ($ambiente && !empty($ambiente->ambiente_piso)) {
												$piso = $pisosModel->getById($ambiente->ambiente_piso);
											}
										}
										$mesaInd->ambiente_nombre = $ambiente->ambiente_nombre ?? '';
										$mesaInd->piso_nombre = $piso->piso_nombre ?? '';
										$mesasInfo[] = $mesaInd;
									}
								}
								$invitadosReserva = $invitadosModel->getList("reserva_id_reserva = '" . intval($reservaId) . "'", "id_invitado ASC");
								$resultados[] = [
									'invitado' => $invitado,
									'reserva' => $reserva,
									'invitados' => $invitadosReserva,
									'mesas' => $mesasInfo
								];
							}
						}
					}
				}
			}
		}
		$this->_view->busqueda_tipo = $tipo;
		$this->_view->busqueda_resultados = $resultados;
		$this->_view->busqueda_documento = $documento;
		$this->_view->reservaId = $reservaId;
	}
}
