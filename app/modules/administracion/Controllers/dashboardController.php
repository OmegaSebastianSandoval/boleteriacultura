<?php

/**
 * Controlador de Información que permite la  creacion, edicion  y eliminacion de la información del Sistema
 */
class Administracion_dashboardController extends Administracion_mainController
{
	public $botonpanel = 9;

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
		$this->namefilter = "parametersfilterdashboard";
		$this->route = "/administracion/dashboard";
		$this->namepages = "pages_dashboard";
		$this->namepageactual = "page_actual_dashboard";
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
		$title = "Dashboard";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;

		// Obtener datos para el reporte
		$this->_view->reservasData = $this->getReservasData();
		$this->_view->metodospagoData = $this->getMetodosPagoData();
		$this->_view->pisosData = $this->getPisosData();
		$this->_view->ambientesData = $this->getAmbientesData();
		$this->_view->mesasData = $this->getMesasData();
		$this->_view->estadisticasGenerales = $this->getEstadisticasGenerales();
		$this->_view->invitadosPorEdades = $this->getInvitadosPorEdadesData();
	}

	/**
	 * Obtiene los datos de reservas aceptadas (estados 2, 3 y 11)
	 * @return array
	 */
	private function getReservasData()
	{
		$reservasModel = new Administracion_Model_DbTable_Reservas();
		$filters = "reserva_estado IN (2, 3, 11)";
		$order = "reserva_fecha DESC";
		return $reservasModel->getList($filters, $order);
	}

	/**
	 * Obtiene los datos agrupados por método de pago
	 * @return array
	 */
	private function getMetodosPagoData()
	{
		// Simulamos los datos ya que no podemos acceder directamente a _conn
		// En una implementación real, esto debería ser un método en el modelo
		$reservasModel = new Administracion_Model_DbTable_Reservas();
		$reservas = $reservasModel->getList("reserva_estado IN (2, 3, 11)", "reserva_estado");

		$metodosData = array();
		$cargoData = array('metodo_pago' => 'Cargo a la Acción', 'cantidad_reservas' => 0, 'total_monto' => 0, 'reserva_estado' => 2);
		$lineaData = array('metodo_pago' => 'Pago en Línea', 'cantidad_reservas' => 0, 'total_monto' => 0, 'reserva_estado' => 3);
		$datafonoData = array('metodo_pago' => 'Datafono', 'cantidad_reservas' => 0, 'total_monto' => 0, 'reserva_estado' => 11);

		foreach ($reservas as $reserva) {
			if ($reserva->reserva_estado == '2') {
				$cargoData['cantidad_reservas']++;
				$cargoData['total_monto'] += (float) ($reserva->reserva_total_pagar ?? 0);
			} elseif ($reserva->reserva_estado == '3') {
				$lineaData['cantidad_reservas']++;
				$lineaData['total_monto'] += (float) ($reserva->reserva_total_pagar ?? 0);
			} elseif ($reserva->reserva_estado == '11') {
				$datafonoData['cantidad_reservas']++;
				$datafonoData['total_monto'] += (float) ($reserva->reserva_total_pagar ?? 0);
			}
		}

		if ($cargoData['cantidad_reservas'] > 0) {
			$metodosData[] = (object) $cargoData;
		}
		if ($lineaData['cantidad_reservas'] > 0) {
			$metodosData[] = (object) $lineaData;
		}
		if ($datafonoData['cantidad_reservas'] > 0) {
			$metodosData[] = (object) $datafonoData;
		}

		return $metodosData;
	}

	/**
	 * Agrupa una lista de elementos (mesas o sillas) en totales de ocupación/capacidad.
	 * Reutilizado por getPisosData()/getAmbientesData() para no duplicar la lógica de
	 * conteo entre mesas y sillas.
	 * @return object con las propiedades: total, ocupados, disponibles, capacidad_total, capacidad_ocupada
	 */
	private function agregarStatsElementos($elementos)
	{
		$total = count($elementos);
		$ocupados = 0;
		$capacidadTotal = 0;
		$capacidadOcupada = 0;

		foreach ($elementos as $el) {
			$capacidadTotal += (int) $el->mesa_capacidad;
			$estaOcupado = $el->mesa_estado == '1' || (($el->mesa_estado !== null && $el->mesa_estado !== '0') && $el->mesa_provision);
			if ($estaOcupado) {
				$ocupados++;
				$capacidadOcupada += (int) $el->mesa_capacidad;
			}
		}

		return (object) array(
			'total' => $total,
			'ocupados' => $ocupados,
			'disponibles' => $total - $ocupados,
			'capacidad_total' => $capacidadTotal,
			'capacidad_ocupada' => $capacidadOcupada,
		);
	}

	/**
	 * Obtiene los datos de capacidad por pisos (mesas y sillas por separado)
	 * @return array
	 */
	private function getPisosData()
	{
		$pisosModel = new Administracion_Model_DbTable_Pisos();
		$mesasModel = new Administracion_Model_DbTable_Mesas();

		// $pisos = $pisosModel->getList("", "piso_nombre");
		$pisos = $pisosModel->getList("piso_estado = 1", "piso_nombre");

		$pisosData = array();

		foreach ($pisos as $piso) {
			$filtroBase = "mesa_ambiente IN (
        SELECT ambiente_id FROM ambientes
        WHERE ambiente_piso = {$piso->piso_id}
        AND ambiente_estado = 1
    )
    AND mesa_activa = 1
    AND (mesa_provision <> 2 OR mesa_provision IS NULL)";

			$mesasDelPiso = $mesasModel->getList("$filtroBase AND mesa_tipo = 'mesa'", "");
			$sillasDelPiso = $mesasModel->getList("$filtroBase AND mesa_tipo = 'silla'", "");

			$statsMesas = $this->agregarStatsElementos($mesasDelPiso);
			$statsSillas = $this->agregarStatsElementos($sillasDelPiso);

			$pisosData[] = (object) array(
				'piso_nombre' => $piso->piso_nombre,
				'total_mesas' => $statsMesas->total,
				'mesas_ocupadas' => $statsMesas->ocupados,
				'mesas_disponibles' => $statsMesas->disponibles,
				'capacidad_total' => $statsMesas->capacidad_total,
				'capacidad_ocupada' => $statsMesas->capacidad_ocupada,
				'total_sillas' => $statsSillas->total,
				'sillas_ocupadas' => $statsSillas->ocupados,
				'sillas_disponibles' => $statsSillas->disponibles,
				'capacidad_sillas_total' => $statsSillas->capacidad_total,
				'capacidad_sillas_ocupada' => $statsSillas->capacidad_ocupada,
			);
		}
		$getPisoNum = static function (?string $name): int {
			if (!$name)
				return -1; // Sin nombre = al final
			if (preg_match('/\d+/', $name, $m))
				return (int) $m[0];
			return -1; // Si no hay número, al final
		};

		usort($pisosData, static function ($a, $b) use ($getPisoNum) {
			$na = $getPisoNum($a->piso_nombre);
			$nb = $getPisoNum($b->piso_nombre);
			if ($na !== $nb)
				return $nb <=> $na; // DESC por número
			return strcasecmp($a->piso_nombre, $b->piso_nombre);
		});
		return $pisosData;
	}

	/**
	 * Obtiene los datos de capacidad por ambientes
	 * @return array
	 */
	private function getAmbientesData()
	{
		$ambientesModel = new Administracion_Model_DbTable_Ambientes();
		$mesasModel = new Administracion_Model_DbTable_Mesas();
		$pisosModel = new Administracion_Model_DbTable_Pisos();
		$categoriasModel = new Administracion_Model_DbTable_Categorias();
		$reservasModel = new Administracion_Model_DbTable_Reservas();
		$invitadosReservasModel = new Administracion_Model_DbTable_Invitadosreservas();

		// Pre-cargar reservas e invitados para lookup eficiente
		$todasReservas = $reservasModel->getList("reserva_estado IN (2, 3, 11)", "");
		$todosInvitados = $invitadosReservasModel->getList("invitado_tipo IN ('1', '2')", "");

		// Agrupar invitados por reserva
		$invPorReserva = [];
		foreach ($todosInvitados as $inv) {
			$rid = $inv->reserva_id_reserva;
			if (!isset($invPorReserva[$rid])) $invPorReserva[$rid] = ['socios' => 0, 'otros' => 0];
			if ($inv->invitado_tipo == '1') $invPorReserva[$rid]['socios']++;
			else $invPorReserva[$rid]['otros']++;
		}

		// Mapa mesa_id -> reserva_id
		$mesaAReserva = [];
		foreach ($todasReservas as $reserva) {
			foreach (array_map('trim', explode(',', $reserva->reserva_mesa_id ?? '')) as $mid) {
				if ($mid !== '') $mesaAReserva[$mid] = $reserva->id;
			}
		}

		// $ambientes = $ambientesModel->getList("", "ambiente_piso ASC");
		$ambientes = $ambientesModel->getList(
			"ambiente_estado = 1 AND ambiente_piso IN (
        SELECT piso_id FROM pisos WHERE piso_estado = 1
    )",
			"ambiente_piso ASC"
		);
		$ambientesData = array();

		foreach ($ambientes as $ambiente) {
			// Obtener información del piso
			$piso = $pisosModel->getById($ambiente->ambiente_piso);
			$pisoNombre = $piso ? $piso->piso_nombre : 'Sin piso';

			// Obtener información de la categoría
			$categoria = $categoriasModel->getById($ambiente->ambiente_categoria);
			$categoriaNombre = $categoria ? $categoria->categoria_nombre : 'Sin categoría';

			// Obtener mesas y sillas del ambiente por separado
			$mesasDelAmbiente = $mesasModel->getList("mesa_ambiente = {$ambiente->ambiente_id} AND mesa_tipo = 'mesa' AND mesa_activa = 1", "");
			$sillasDelAmbiente = $mesasModel->getList("mesa_ambiente = {$ambiente->ambiente_id} AND mesa_tipo = 'silla' AND mesa_activa = 1", "");

			$totalMesas = count($mesasDelAmbiente);
			$totalSillas = count($sillasDelAmbiente);
			$mesasOcupadas = 0;
			$sillasOcupadas = 0;
			$capacidadTotal = 0;
			$capacidadOcupada = 0;
			$capacidadSillasTotal = 0;
			$capacidadSillasOcupada = 0;
			$mesasLibres = array();
			$sillasLibres = array();
			$totalSocios = 0;
			$totalOtros = 0;

			foreach ($mesasDelAmbiente as $mesa) {
				$capacidadTotal += (int) $mesa->mesa_capacidad;
				if ($mesa->mesa_estado == '1' || (($mesa->mesa_estado !== null && $mesa->mesa_estado !== '0') && $mesa->mesa_provision)) {
					$mesasOcupadas++;
					$capacidadOcupada += (int) $mesa->mesa_capacidad;
					$rid = $mesaAReserva[(string) $mesa->mesa_id] ?? null;
					if ($rid && isset($invPorReserva[$rid])) {
						$totalSocios += $invPorReserva[$rid]['socios'];
						$totalOtros  += $invPorReserva[$rid]['otros'];
					}
				} else {
					// Consideramos libre si mesa_estado es null o '0'
					$mesasLibres[] = array(
						'mesa_nombre' => $mesa->mesa_nombre,
						'mesa_capacidad' => $mesa->mesa_capacidad
					);
				}
			}

			foreach ($sillasDelAmbiente as $silla) {
				$capacidadSillasTotal += (int) $silla->mesa_capacidad;
				if ($silla->mesa_estado == '1' || (($silla->mesa_estado !== null && $silla->mesa_estado !== '0') && $silla->mesa_provision)) {
					$sillasOcupadas++;
					$capacidadSillasOcupada += (int) $silla->mesa_capacidad;
					$rid = $mesaAReserva[(string) $silla->mesa_id] ?? null;
					if ($rid && isset($invPorReserva[$rid])) {
						$totalSocios += $invPorReserva[$rid]['socios'];
						$totalOtros  += $invPorReserva[$rid]['otros'];
					}
				} else {
					$sillasLibres[] = array(
						'mesa_nombre' => $silla->mesa_nombre,
						'mesa_precio' => $silla->mesa_precio,
					);
				}
			}

			// Antes solo se incluían ambientes con mesas; un ambiente 100% de sillas
			// (o híbrido) quedaba fuera del dashboard por completo.
			if ($totalMesas > 0 || $totalSillas > 0) {
				$ambientesData[] = (object) array(
					'ambiente_id' => $ambiente->ambiente_id,
					'ambiente_nombre' => $ambiente->ambiente_nombre,
					'piso_nombre' => $pisoNombre,
					'categoria_nombre' => $categoriaNombre,
					'total_mesas' => $totalMesas,
					'mesas_ocupadas' => $mesasOcupadas,
					'mesas_disponibles' => $totalMesas - $mesasOcupadas,
					'capacidad_total' => $capacidadTotal,
					'capacidad_ocupada' => $capacidadOcupada,
					'mesas_libres' => $mesasLibres,
					'total_sillas' => $totalSillas,
					'sillas_ocupadas' => $sillasOcupadas,
					'sillas_disponibles' => $totalSillas - $sillasOcupadas,
					'capacidad_sillas_total' => $capacidadSillasTotal,
					'capacidad_sillas_ocupada' => $capacidadSillasOcupada,
					'sillas_libres' => $sillasLibres,
					'total_socios' => $totalSocios,
					'total_otros' => $totalOtros,
				);
			}
		}
		$getPisoNum = static function (?string $name): int {
			if (!$name)
				return -1;                    // "Sin piso" al final
			if (preg_match('/\d+/', $name, $m))
				return (int) $m[0];
			return -1;                                // si no hay número, al final
		};

		usort($ambientesData, static function ($a, $b) use ($getPisoNum) {
			$na = $getPisoNum($a->piso_nombre);
			$nb = $getPisoNum($b->piso_nombre);
			if ($na !== $nb)
				return $nb <=> $na;     // DESC por número
			// desempate: nombre de ambiente (opcional)
			return strcasecmp($a->ambiente_nombre, $b->ambiente_nombre);
		});


		return $ambientesData;
	}

	/**
	 * Obtiene estadísticas generales de mesas
	 * @return object
	 */
	private function getMesasData()
	{
		$mesasModel = new Administracion_Model_DbTable_Mesas();
		$mesas = $mesasModel->getList("mesa_tipo = 'mesa' AND mesa_activa = 1", "");

		$totalMesas = count($mesas);
		$mesasOcupadas = 0;
		$capacidadTotal = 0;
		$capacidadOcupada = 0;

		foreach ($mesas as $mesa) {
			$capacidadTotal += (int) $mesa->mesa_capacidad;
			if ($mesa->mesa_estado == '1' || ($mesa->mesa_estado !== null && $mesa->mesa_estado != '0')) {
				$mesasOcupadas++;
				$capacidadOcupada += (int) $mesa->mesa_capacidad;
			}
		}


		return (object) array(
			'total_mesas' => $totalMesas,
			'mesas_ocupadas' => $mesasOcupadas,
			'mesas_disponibles' => $totalMesas - $mesasOcupadas,
			'capacidad_total' => $capacidadTotal,
			'capacidad_ocupada' => $capacidadOcupada
		);
	}

	/**
	 * Obtiene estadísticas generales del sistema
	 * @return object
	 */
	private function getEstadisticasGenerales()
	{
		$reservasModel = new Administracion_Model_DbTable_Reservas();
		$invitadosReservasModel = new Administracion_Model_DbTable_Invitadosreservas();
		$mesasModel = new Administracion_Model_DbTable_Mesas();

		$reservas = $reservasModel->getList("reserva_estado IN (2, 3, 11)", "");

		$totalReservas = count($reservas);
		$totalIngresos = 0;
		$totalPersonas = 0;
		$totalPersonasSocios = 0;
		$totalPersonasInvitados = 0;
		$pagosLinea = 0;
		$pagosAccion = 0;
		$pagosDatafono = 0;
		$fechaPrimera = null;
		$fechaUltima = null;
		$mesasVendidas = 0;
		$sillasVendidas = 0;
		$totalMesas = 0;
		$totalCapacidad = 0;
		$totalSillas = 0;
		$capacidadSillas = 0;

		$filtroAmbientesActivos = "mesa_activa = 1 AND mesa_ambiente IN (
        SELECT ambiente_id FROM ambientes
        WHERE ambiente_estado = 1
        AND ambiente_piso IN (
            SELECT piso_id FROM pisos WHERE piso_estado = 1
        )
    )";

		$mesas = $mesasModel->getList("mesa_tipo = 'mesa' AND $filtroAmbientesActivos", "");
		$sillas = $mesasModel->getList("mesa_tipo = 'silla' AND $filtroAmbientesActivos", "");
		$totalMesas = count($mesas);
		$totalSillas = count($sillas);
		foreach ($mesas as $mesa) {
			$totalCapacidad += (int) $mesa->mesa_capacidad;
		}
		foreach ($sillas as $silla) {
			$capacidadSillas += (int) $silla->mesa_capacidad;
		}

		// Mapa mesa_id -> tipo, para clasificar cada id de reserva_mesa_id sin hacer
		// una consulta por id. Antes esto no existía y "mesas_vendidas" contaba TODOS
		// los ids (mesas y sillas mezclados) mientras "total_mesas" solo contaba mesas,
		// lo que inflaba/rompía el % de ocupación de la tarjeta en cuanto se vendía
		// alguna silla.
		$tipoPorMesaId = [];
		foreach ($mesas as $mesa) {
			$tipoPorMesaId[(string) $mesa->mesa_id] = 'mesa';
		}
		foreach ($sillas as $silla) {
			$tipoPorMesaId[(string) $silla->mesa_id] = 'silla';
		}

		foreach ($reservas as $reserva) {
			$totalIngresos += (float) ($reserva->reserva_total_pagar ?? 0);
			$totalPersonas += (int) ($reserva->reserva_total_personas ?? 0);
			$idsReserva = array_filter(array_map('trim', explode(',', $reserva->reserva_mesa_id ?? '')));
			foreach ($idsReserva as $mid) {
				if (($tipoPorMesaId[$mid] ?? 'mesa') === 'silla') {
					$sillasVendidas++;
				} else {
					$mesasVendidas++;
				}
			}

			$invitadosSocios = $invitadosReservasModel->getList("reserva_id_reserva = {$reserva->id} AND invitado_tipo = '1'", "");
			$invitadosInvitados = $invitadosReservasModel->getList("reserva_id_reserva = {$reserva->id} AND invitado_tipo = '2'", "");

			if ($reserva->reserva_metodo_pago == 'linea') {
				$pagosLinea++;
			}
			if ($reserva->reserva_metodo_pago == 'cargo') {
				$pagosAccion++;
			}
			if ($reserva->reserva_metodo_pago == 'datafono') {
				$pagosDatafono++;
			}

			$totalPersonasSocios += count($invitadosSocios);
			$totalPersonasInvitados += count($invitadosInvitados);

			if ($fechaPrimera === null || $reserva->reserva_fecha < $fechaPrimera) {
				$fechaPrimera = $reserva->reserva_fecha;
			}
			if ($fechaUltima === null || $reserva->reserva_fecha > $fechaUltima) {
				$fechaUltima = $reserva->reserva_fecha;
			}
		}

		return (object) array(
			'total_reservas_aceptadas' => $totalReservas,
			'total_ingresos' => $totalIngresos,
			'promedio_por_reserva' => $totalReservas > 0 ? $totalIngresos / $totalReservas : 0,
			'total_personas' => $totalPersonas,
			'total_personas_socios' => $totalPersonasSocios,
			'total_personas_invitados' => $totalPersonasInvitados,
			'fecha_primera_reserva' => $fechaPrimera,
			'fecha_ultima_reserva' => $fechaUltima,
			'mesas_vendidas' => $mesasVendidas,
			'total_mesas' => $totalMesas,
			'sillas_vendidas' => $sillasVendidas,
			'total_sillas' => $totalSillas,
			'capacidad_sillas' => $capacidadSillas,
			'pagos_linea' => $pagosLinea,
			'pagos_accion' => $pagosAccion,
			'pagos_datafono' => $pagosDatafono,
			// Capacidad combinada de mesas + sillas: la tarjeta "Total personas / Total
			// capacidad" debe reflejar el aforo real del evento, no solo el de mesas
			// (antes quedaba en 0 en eventos que solo venden sillas).
			'capacidad_total' => $totalCapacidad + $capacidadSillas,
			'capacidad_total_mesas' => $totalCapacidad,
		);
	}
	public function getInvitadosPorEdadesData()
	{
		$reservasModel = new Administracion_Model_DbTable_Reservas();
		$invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
		$reservasAceptadas = $reservasModel->getList("reserva_estado IN (2, 3, 11)", "");
		foreach ($reservasAceptadas as $reserva) {
			$reserva->invitadosSocios = $invitadosModel->getList("reserva_id_reserva = {$reserva->id} AND invitado_tipo = '1' AND invitadoReserva_estado_invitado IN ('A', 'S') AND (invitadoReserva_fecha_nacimiento != '' AND invitadoReserva_fecha_nacimiento != '1900-01-01')", "");
		}

		$entre18_35 = 0;
		$entre35_55 = 0;
		$mayores55 = 0;

		$edadesData = array(
			'entre18_35' => 0,
			'entre35_55' => 0,
			'mayores55' => 0,
			'sin_dato' => 0
		);

		$hoy = new DateTime();
		foreach ($reservasAceptadas as $reserva) {
			foreach ($reserva->invitadosSocios as $invitado) {
				if ($invitado->invitadoReserva_fecha_nacimiento) {
					$fechaNacimiento = DateTime::createFromFormat('Y-m-d', $invitado->invitadoReserva_fecha_nacimiento);
					if ($fechaNacimiento) {
						$edad = $hoy->diff($fechaNacimiento)->y;
						if ($edad >= 18 && $edad <= 35) {
							$edadesData['entre18_35']++;
						} elseif ($edad > 35 && $edad <= 55) {
							$edadesData['entre35_55']++;
						} elseif ($edad > 55) {
							$edadesData['mayores55']++;
						}
					} else {

						$edadesData['sin_dato']++;
					}
				} else {
					$edadesData['sin_dato']++;
				}
			}
		}




		return (object) $edadesData;
	}
	/**
	 * Exporta el listado de reservas a Excel
	 * @return void
	 */
	public function exportarAction()
	{
		$reservasModel = new Administracion_Model_DbTable_Reservas();
		$eventosModel = new Administracion_Model_DbTable_Eventos();

		$filters = "reserva_estado IN (2, 3, 11)";
		$order = "reserva_fecha DESC";
		$reservas = $reservasModel->getList($filters, $order);

		// Obtener eventos para el mapeo
		$eventos = array();
		$eventosData = $eventosModel->getList("", "evento_id ASC");
		foreach ($eventosData as $evento) {
			$eventos[$evento->evento_id] = $evento;
		}

		$this->_view->reservas = $reservas;
		$this->_view->eventos = $eventos;
		$this->setLayout('blanco');

		$hoy = date("YmdHis");
		$excel = $this->_getSanitizedParam("excel");

		if ($excel == 1) {
			header("Content-Type: application/vnd.ms-excel; charset=utf-8");
			header("Content-type: application/x-msexcel; charset=utf-8");
			header("Content-Disposition: attachment; filename=reporte_reservas_" . $hoy . ".xls");
		}
	}
	/**
	 * Acción para mostrar la página de reset de boletería
	 *
	 * @return void.
	 */
	public function resetAction()
	{
		$title = "Reiniciar Boletería";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;
	}
	/**
	 * Acción para procesar el reinicio de boletería
	 *
	 * @return void.
	 */
	public function acceptresetAction()
	{
		$this->setLayout('blanco');

		try {
			// Crear carpeta de backups si no existe
			$backupDir = dirname(__DIR__, 4) . '/backups/';
			if (!is_dir($backupDir)) {
				mkdir($backupDir, 0755, true);
			}

			// Crear subcarpeta con fecha y hora
			$timestamp = date('Y-m-d_H-i-s');
			$backupFolder = $backupDir . 'backup_' . $timestamp . '/';
			if (!is_dir($backupFolder)) {
				mkdir($backupFolder, 0755, true);
			}

			// 1. Guardar backup de la base de datos
			$this->saveBackupDB($backupFolder, $timestamp);

			// 2. Guardar backup de boletería (PDFs y QRs)
			$this->saveBackupBoleteria($backupFolder, $timestamp);

			// 3. Proceder con el reset
			$reservasModel = new Administracion_Model_DbTable_Reservas();
			$securityToken = hash('sha256', Administracion_Model_DbTable_Reservas::TRUNCATE_SECRET);

			$reservasModel->truncateTables($securityToken);
			$pisosModel = new Administracion_Model_DbTable_Pisos();
			$ambientesModel = new Administracion_Model_DbTable_Ambientes();
			$mesasModel = new Administracion_Model_DbTable_Mesas();
			$pisosAll = $pisosModel->getList("", "");
			$ambientesAll = $ambientesModel->getList("", "");
			$mesasAll = $mesasModel->getList("", "");

			foreach ($pisosAll as $piso) {
				$pisosModel->editField($piso->piso_id, 'piso_estado', 0);
			}

			foreach ($ambientesAll as $ambiente) {
				$ambientesModel->editField($ambiente->ambiente_id, 'ambiente_estado', 0);
			}

			foreach ($mesasAll as $mesa) {
				$mesasModel->editField($mesa->mesa_id, 'mesa_estado', 0);
				$mesasModel->editField($mesa->mesa_id, 'mesa_provision', 0);
				$mesasModel->editField($mesa->mesa_id, 'mesa_activa', 1);
			}

			// Eliminar qrs y pdfs
			$rootPath = dirname(__DIR__, 4) . '/public_html/';
			$foldersToEmpty = [
				'images_sales/qrs_news',
				'pdfs_news'
			];

			// Función para vaciar una carpeta recursivamente
			$emptyDir = function ($dir) use (&$emptyDir) {
				if (is_dir($dir)) {
					$files = scandir($dir);
					foreach ($files as $file) {
						if ($file != "." && $file != "..") {
							$filePath = $dir . DIRECTORY_SEPARATOR . $file;
							if (is_dir($filePath)) {
								$emptyDir($filePath);
								rmdir($filePath);
							} else {
								unlink($filePath);
							}
						}
					}
				}
			};

			// Vaciar las carpetas especificadas
			foreach ($foldersToEmpty as $folder) {
				$fullPath = $rootPath . $folder;
				$emptyDir($fullPath);
			}

			Session::getInstance()->set('reset_result', [
				'success' => true,
				'message' => "Reinicio completado exitosamente. Todas las tablas han sido truncadas y las carpetas de QRs y PDFs han sido vaciadas. Se ha guardado un backup en: " . $backupFolder
			]);
		} catch (Exception $e) {
			Session::getInstance()->set('reset_result', [
				'success' => false,
				'message' => "Error durante el reinicio: " . $e->getMessage()
			]);
		}

		header('Location: /administracion/dashboard/result');
		exit;
	}
	/**
	 * Acción para mostrar el resultado del reinicio
	 *
	 * @return void.
	 */
	public function resultAction()
	{
		$result = Session::getInstance()->get('reset_result');
		if ($result) {
			$this->_view->success = $result['success'];
			$this->_view->message = $result['message'];
			Session::getInstance()->set('reset_result', null); // Limpiar la sesión
		} else {
			$this->_view->success = false;
			$this->_view->message = "No se encontró información del reinicio.";
		}
		$this->_view->titlesection = "Resultado del Reinicio";
	}
	/**
	 * Acción para descargar backup de la base de datos
	 *
	 * @return void.
	 */
	public function backupdbAction()
	{
		// Sin layout ni vista
		$this->setLayout('blanco');

		// Verificar que APPLICATION_ENV esté definido
		if (!defined('APPLICATION_ENV')) {
			echo "-- Error: APPLICATION_ENV no definido\n";
			exit;
		}

		$conn = App::getDbConnection()->getConnection(); // Obtener la conexión mysqli

		// Verificar conexión
		if (!$conn) {
			echo "-- Error: No se pudo conectar a la base de datos\n";
			exit;
		}

		// Limpiar cualquier output previo
		if (ob_get_level()) ob_end_clean();

		// Headers para descarga
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="backup_' . APPLICATION_ENV . '_' . date('Y-m-d_H-i-s') . '.sql"');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');

		// Iniciar output buffering
		ob_start();

		echo "-- Backup de la base de datos generado el " . date('Y-m-d H:i:s') . "\n";
		echo "-- Entorno: " . APPLICATION_ENV . "\n\n";
		echo "SET FOREIGN_KEY_CHECKS = 0;\n\n";

		// Obtener todas las tablas
		$result = $conn->query("SHOW TABLES");
		if (!$result) {
			echo "-- Error al obtener tablas: " . $conn->error . "\n";
			ob_end_flush();
			exit;
		}
		$tables = [];
		while ($row = $result->fetch_array()) {
			$tables[] = $row[0];
		}
		$result->free();

		foreach ($tables as $table) {
			// Obtener CREATE TABLE
			$createResult = $conn->query("SHOW CREATE TABLE `$table`");
			if (!$createResult) {
				echo "-- Error en CREATE TABLE para $table: " . $conn->error . "\n";
				continue;
			}
			$createRow = $createResult->fetch_assoc();
			echo "-- Estructura de la tabla `$table`\n";
			echo $createRow['Create Table'] . ";\n\n";
			$createResult->free();

			// Obtener datos
			$dataResult = $conn->query("SELECT * FROM `$table`");
			if (!$dataResult) {
				echo "-- Error en SELECT para $table: " . $conn->error . "\n";
				continue;
			}
			if ($dataResult->num_rows > 0) {
				echo "-- Datos de la tabla `$table`\n";
				while ($row = $dataResult->fetch_assoc()) {
					$columns = array_keys($row);
					$values = array_map(function ($v) use ($conn) {
						return "'" . $conn->real_escape_string($v) . "'";
					}, $row);
					echo "INSERT INTO `$table` (`" . implode('`,`', $columns) . "`) VALUES (" . implode(',', $values) . ");\n";
				}
			}
			$dataResult->free();
			echo "\n";

			// Flush para enviar datos en chunks
			ob_flush();
			flush();
		}

		echo "SET FOREIGN_KEY_CHECKS = 1;\n";

		ob_end_flush();
		exit;
	}

	/**
	 * Guarda el backup de la base de datos en un archivo
	 *
	 * @param string $backupFolder Carpeta donde guardar el backup
	 * @param string $timestamp Timestamp para el nombre del archivo
	 * @return bool
	 */
	private function saveBackupDB($backupFolder, $timestamp)
	{
		if (!defined('APPLICATION_ENV')) {
			throw new Exception('APPLICATION_ENV no definido');
		}

		$conn = App::getDbConnection()->getConnection();

		if (!$conn) {
			throw new Exception('No se pudo conectar a la base de datos');
		}

		$filename = $backupFolder . 'backup_db_' . APPLICATION_ENV . '_' . $timestamp . '.sql';
		$file = fopen($filename, 'w');

		if (!$file) {
			throw new Exception('No se pudo crear el archivo de backup de BD');
		}

		fwrite($file, "-- Backup de la base de datos generado el " . date('Y-m-d H:i:s') . "\n");
		fwrite($file, "-- Entorno: " . APPLICATION_ENV . "\n\n");
		fwrite($file, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

		// Obtener todas las tablas
		$result = $conn->query("SHOW TABLES");
		if (!$result) {
			fclose($file);
			throw new Exception('Error al obtener tablas: ' . $conn->error);
		}

		$tables = [];
		while ($row = $result->fetch_array()) {
			$tables[] = $row[0];
		}
		$result->free();

		foreach ($tables as $table) {
			// Obtener CREATE TABLE
			$createResult = $conn->query("SHOW CREATE TABLE `$table`");
			if (!$createResult) {
				fwrite($file, "-- Error en CREATE TABLE para $table: " . $conn->error . "\n");
				continue;
			}
			$createRow = $createResult->fetch_assoc();
			fwrite($file, "-- Estructura de la tabla `$table`\n");
			fwrite($file, $createRow['Create Table'] . ";\n\n");
			$createResult->free();

			// Obtener datos
			$dataResult = $conn->query("SELECT * FROM `$table`");
			if (!$dataResult) {
				fwrite($file, "-- Error en SELECT para $table: " . $conn->error . "\n");
				continue;
			}

			if ($dataResult->num_rows > 0) {
				fwrite($file, "-- Datos de la tabla `$table`\n");
				while ($row = $dataResult->fetch_assoc()) {
					$columns = array_keys($row);
					$values = array_map(function ($v) use ($conn) {
						return is_null($v) ? 'NULL' : "'" . $conn->real_escape_string($v) . "'";
					}, $row);
					fwrite($file, "INSERT INTO `$table` (`" . implode('`,`', $columns) . "`) VALUES (" . implode(',', $values) . ");\n");
				}
			}
			$dataResult->free();
			fwrite($file, "\n");
		}

		fwrite($file, "SET FOREIGN_KEY_CHECKS = 1;\n");
		fclose($file);

		return true;
	}

	/**
	 * Guarda el backup de la boletería (PDFs y QRs) en un archivo ZIP
	 *
	 * @param string $backupFolder Carpeta donde guardar el backup
	 * @param string $timestamp Timestamp para el nombre del archivo
	 * @return bool
	 */
	private function saveBackupBoleteria($backupFolder, $timestamp)
	{
		$rootPath = dirname(__DIR__, 4) . '/public_html/';
		$folders = ['pdfs_news', 'images_sales/qrs_news'];

		$zipFile = $backupFolder . 'backup_boleteria_' . $timestamp . '.zip';
		$zip = new ZipArchive();

		if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
			throw new Exception('No se pudo crear el archivo ZIP de boletería');
		}

		// Función para agregar directorio recursivamente
		$addDirToZip = function ($dir, $zip, $basePath = '') use (&$addDirToZip) {
			if (!is_dir($dir)) {
				return;
			}

			$files = scandir($dir);
			foreach ($files as $file) {
				if ($file === '.' || $file === '..') continue;

				$filePath = $dir . '/' . $file;
				$zipPath = $basePath . $file;

				if (is_dir($filePath)) {
					$zip->addEmptyDir($zipPath);
					$addDirToZip($filePath, $zip, $zipPath . '/');
				} else {
					$zip->addFile($filePath, $zipPath);
				}
			}
		};

		// Agregar carpetas al ZIP
		foreach ($folders as $folder) {
			$folderPath = $rootPath . $folder;
			if (is_dir($folderPath)) {
				$zip->addEmptyDir($folder);
				$addDirToZip($folderPath, $zip, $folder . '/');
			}
		}

		$zip->close();

		return true;
	}

	/**
	 * Acción para descargar boletería
	 *
	 * @return void.
	 */
	public function downloadboleteriaAction()
	{
		$this->setLayout('blanco');

		// Rutas de las carpetas a comprimir
		$rootPath = dirname(__DIR__, 4) . '/public_html/';
		$folders = ['pdfs_news', 'images_sales/qrs_news'];

		// Verificar que las carpetas existan
		$missingFolders = [];
		foreach ($folders as $folder) {
			if (!is_dir($rootPath . $folder)) {
				$missingFolders[] = $folder;
			}
		}

		if (!empty($missingFolders)) {
			die('Error: Las siguientes carpetas no existen: ' . implode(', ', $missingFolders));
		}

		// Crear archivo ZIP temporal
		$zipFile = tempnam(sys_get_temp_dir(), 'boleteria_') . '.zip';
		$zip = new ZipArchive();

		if ($zip->open($zipFile, ZipArchive::CREATE) !== TRUE) {
			die('Error: No se pudo crear el archivo ZIP.');
		}

		// Función para agregar directorio recursivamente
		$addDirToZip = function ($dir, $zip, $basePath = '') use (&$addDirToZip) {
			$files = scandir($dir);
			foreach ($files as $file) {
				if ($file === '.' || $file === '..') continue;

				$filePath = $dir . '/' . $file;
				$zipPath = $basePath . $file;

				if (is_dir($filePath)) {
					$zip->addEmptyDir($zipPath);
					$addDirToZip($filePath, $zip, $zipPath . '/');
				} else {
					$zip->addFile($filePath, $zipPath);
				}
			}
		};

		// Agregar carpetas al ZIP
		foreach ($folders as $folder) {
			$folderPath = $rootPath . $folder;
			$zip->addEmptyDir($folder);
			$addDirToZip($folderPath, $zip, $folder . '/');
		}

		$zip->close();

		// Limpiar cualquier salida previa
		if (ob_get_level()) ob_end_clean();

		// Headers para descarga
		header('Content-Description: File Transfer');
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="boleteria_' . date('Y-m-d_H-i-s') . '.zip"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($zipFile));

		// Enviar el archivo
		readfile($zipFile);

		// Eliminar archivo temporal
		unlink($zipFile);
		exit;
	}
}
