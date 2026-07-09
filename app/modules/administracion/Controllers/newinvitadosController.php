<?php

/**
 * Controlador de Newinvitados que permite la  creacion, edicion  y eliminacion de los newinvitados del Sistema
 */
class Administracion_newinvitadosController extends Administracion_mainController
{
	public $botonpanel = 15;
	/**
	 * $mainModel  instancia del modelo de  base de datos newinvitados
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
	protected $_csrf_section = "administracion_newinvitados";

	/**
	 * $namepages nombre de la pvariable en la cual se va a guardar  el numero de seccion en la paginacion del controlador
	 * @var string
	 */
	protected $namepages;

	/**
	 * $namepageactual nombre de la variable en la cual se va a guardar la página actual
	 * @var string
	 */
	protected $namepageactual;



	/**
	 * Inicializa las variables principales del controlador newinvitados .
	 *
	 * @return void.
	 */
	public function init()
	{
		$this->mainModel = new Administracion_Model_DbTable_Newinvitados();
		$this->namefilter = "parametersfilternewinvitados";
		$this->route = "/administracion/newinvitados";
		$this->namepages = "pages_newinvitados";
		$this->namepageactual = "page_actual_newinvitados";
		$this->_view->route = $this->route;
		if (Session::getInstance()->get($this->namepages)) {
			$this->pages = Session::getInstance()->get($this->namepages);
		} else {
			$this->pages = 20;
		}
		parent::init();
	}


	/**
	 * Recibe la informacion y  muestra un listado de  newinvitados con sus respectivos filtros.
	 *
	 * @return void.
	 */
	public function indexAction()
	{
		$title = "Administración de invitados - nuevo";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;
		$this->filters();
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$filters = (object) Session::getInstance()->get($this->namefilter);
		$this->_view->filters = $filters;
		$filters = $this->getFilter();
		$order = "id_invitado DESC";
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
		$this->_view->list_reserva_id_reserva = $this->getReservaidreserva();
		$this->_view->list_invitado_tipo = $this->getInvitadotipo();
		$this->_view->list_invitadoReserva_estado_invitado = $this->getInvitadoReservaestadoinvitado();
	}

	/**
	 * Genera la Informacion necesaria para editar o crear un  newinvitados  y muestra su formulario
	 *
	 * @return void.
	 */
	public function manageAction()
	{
		$this->_view->route = $this->route;
		$this->_csrf_section = "manage_newinvitados_" . date("YmdHis");
		$this->_csrf->generateCode($this->_csrf_section);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];
		$this->_view->list_reserva_id_reserva = $this->getReservaidreserva();
		$this->_view->list_invitadoReserva_estado_invitado = $this->getInvitadoReservaestadoinvitado();
		$this->_view->list_invitado_tipo = $this->getInvitadotipo();
		$this->_view->list_invitado_evento = $this->getInvitadoevento();
		$id = $this->_getSanitizedParam("id");
		if ($id > 0) {
			$content = $this->mainModel->getById($id);
			if ($content->id_invitado) {
				$this->_view->content = $content;
				$this->_view->routeform = $this->route . "/update";
				$title = "Actualizar newinvitados";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			} else {
				$this->_view->routeform = $this->route . "/insert";
				$title = "Crear newinvitados";
				$this->getLayout()->setTitle($title);
				$this->_view->titlesection = $title;
			}
		} else {
			$this->_view->routeform = $this->route . "/insert";
			$title = "Crear newinvitados";
			$this->getLayout()->setTitle($title);
			$this->_view->titlesection = $title;
		}
	}

	/**
	 * Inserta la informacion de un newinvitados  y redirecciona al listado de newinvitados.
	 *
	 * @return void.
	 */
	public function insertAction()
	{
		$this->setLayout('blanco');
		$csrf = $this->_getSanitizedParam("csrf");
		if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] == $csrf) {
			$data = $this->getData();
			$id = $this->mainModel->insert($data);

			// Obtener valor anterior de la reserva antes del recálculo
			$valorAnterior = 0;
			if ($data['reserva_id_reserva'] > 0) {
				$reservaModel = new Administracion_Model_DbTable_Reservas();
				$reservaAntes = $reservaModel->getById($data['reserva_id_reserva']);
				$valorAnterior = $reservaAntes ? $reservaAntes->reserva_total_pagar : 0;
			}

			// Log detallado para creación
			$logDataDetallado = array(
				'id_invitado' => $id,
				'accion' => 'CREAR_NEWINVITADOS',
				'usuario_id' => Session::getInstance()->get('usuarioData')['id'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
				'usuario_nombre' => Session::getInstance()->get('usuarioData')['nombre'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
				'fecha_accion' => date('Y-m-d H:i:s'),
				'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
				'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
				'datos_insertados' => $data,
				'reserva_id_afectada' => $data['reserva_id_reserva'],
				'tipo_invitado' => $data['invitado_tipo'],
				'documento_invitado' => $data['documento_invitado'],
				'invitadoReserva_nombre_invitado' => $data['invitadoReserva_nombre_invitado'],
				'invitadoReserva_apellido_invitado' => $data['invitadoReserva_apellido	_invitado']
			);

			// Recalcular automáticamente el valor de la reserva si es necesario
			if ($data['reserva_id_reserva'] > 0) {
				$valorNuevo = $this->recalcularValorReserva($data['reserva_id_reserva'], $logDataDetallado);

				// Agregar información del recálculo al log
				$logDataDetallado['valor_anterior'] = $valorAnterior;
				$logDataDetallado['valor_nuevo'] = $valorNuevo;
				$logDataDetallado['diferencia'] = $valorNuevo - $valorAnterior;
				$logDataDetallado['impacto_financiero'] = array(
					'mensaje' => 'Nuevo invitado creado - reserva recalculada automaticamente',
					'valor_anterior_reserva' => $valorAnterior,
					'valor_nuevo_reserva' => $valorNuevo,
					'diferencia' => $valorNuevo - $valorAnterior,
					'requiere_recalculo' => true,
					'recalculo_realizado' => true
				);

				// Preparar datos para insertar en el log
				$logParaInsertar = array(
					'log_usuario' => Session::getInstance()->get('usuarioData')['id'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
					'log_tipo' => 'CREAR_NEWINVITADOS',
					'log_fecha' => date('Y-m-d H:i:s'),
					'log_log' => json_encode(array(
						'datos_invitado' => $data,
						'recalculo' => array(
							'valor_anterior' => $valorAnterior,
							'valor_nuevo' => $valorNuevo,
							'diferencia' => $valorNuevo - $valorAnterior
						),
						'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A'
					))
				);
				// $actualizarValorReserva = $this->_getSanitizedParam("actualziarvalorreseva");
				$actualizarValorReserva = 1;
				if ($actualizarValorReserva == 1) {
					$totalPersonas = count($this->mainModel->getList("reserva_id_reserva = '{$data['reserva_id_reserva']}'"));
					$reservaModel = new Administracion_Model_DbTable_Reservas();
					$reservaModel->editField($data['reserva_id_reserva'], "reserva_total_personas", $totalPersonas);

					$reservaModel->editField($data['reserva_id_reserva'], "reserva_fecha_actualizacion", date('Y-m-d H:i:s'));
				}
			} else {
				$logDataDetallado['impacto_financiero'] = array(
					'mensaje' => 'Nuevo invitado creado sin reserva asociada',
					'requiere_recalculo' => false
				);
				$logParaInsertar = array(
					'log_usuario' => Session::getInstance()->get('usuarioData')['id'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
					'log_tipo' => 'CREAR_NEWINVITADOS',
					'log_fecha' => date('Y-m-d H:i:s'),
					'log_log' => json_encode($logDataDetallado, JSON_UNESCAPED_UNICODE)
				);
			}

			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($logParaInsertar);
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y Actualiza la informacion de un newinvitados  y redirecciona al listado de newinvitados.
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
			if ($content->id_invitado) {
				$data = $this->getData();
				$this->mainModel->update($data, $id);
			}
			// $actualizarValorReserva = $this->_getSanitizedParam("actualziarvalorreseva");
			$actualizarValorReserva = 1;
			if ($actualizarValorReserva == 1) {
				$reservaModel = new Administracion_Model_DbTable_Reservas();
				$reserva = $reservaModel->getById($content->reserva_id_reserva);

				if ($reserva) {
					$data["reservaInfo"] = print_r($reserva, true);
					$invitados = $this->mainModel->getList("reserva_id_reserva = '{$content->reserva_id_reserva}'");
					$categoriasModel = new Administracion_Model_DbTable_Categorias();
					$categoria = $categoriasModel->getById(4);

					if (is_countable($invitados) && count($invitados) >= 1 && $categoria) {
						$valorAnterior = $reserva->reserva_total_pagar;
						$totalGeneral = 0;
						$detalleCalculos = array();

						foreach ($invitados as $invitado) {
							$precio = 0;
							$tipoCalculoAplicado = '';

							if ($invitado->invitado_tipo == '1') { // Beneficiario/Socio
								$esMenor25 = ($invitado->invitadoReserva_beneficiario_menor25) && $invitado->invitadoReserva_beneficiario_menor25 == '1';
								$esHijo = ($invitado->invitadoReserva_beneficiario_hijo) && $invitado->invitadoReserva_beneficiario_hijo == '1';

								if ($esMenor25 && $esHijo) {
									$precio = $categoria->categoria_precio_socio_hijo;
									$tipoCalculoAplicado = 'Socio Hijo Menor 25';
								} else {
									$precio = $categoria->categoria_precio_socio;
									$tipoCalculoAplicado = 'Socio Regular';
								}

								// Aplicar descuento si es co-socio
								if (($invitado->invitadoReserva_estado_invitado) && $invitado->invitadoReserva_estado_invitado == 'S') {

									$tipoCalculoAplicado .= ' (Co-socio)';
								}
							} else { // Invitado
								$precio = $categoria->categoria_precio_invitado;
								$tipoCalculoAplicado = 'Invitado';
							}

							$totalGeneral += $precio;

							// Guardar detalle del cálculo para logging
							$detalleCalculos[] = array(
								'id_invitado' => $invitado->id_invitado ?? 'N/A',
								'documento' => $invitado->documento_invitado ?? 'N/A',
								'nombre' => $invitado->invitadoReserva_nombre_invitado ?? 'N/A',
								'apellido' => $invitado->invitadoReserva_apellido_invitado ?? 'N/A',
								'tipo' => $invitado->invitado_tipo,
								'estado' => $invitado->invitadoReserva_estado_invitado ?? 'N/A',
								'es_menor_25' => $esMenor25 ?? false,
								'es_hijo' => $esHijo ?? false,
								'precio_aplicado' => $precio,
								'tipo_calculo' => $tipoCalculoAplicado
							);
						}

						// Solo actualizar si hay cambio en el total
						if ($totalGeneral > 0 && $totalGeneral != $valorAnterior) {
							$reservaModel->editField($content->reserva_id_reserva, "reserva_total_pagar", $totalGeneral);

							// Log detallado del recálculo
							$logRecalculo = array(
								'reserva_id' => $content->reserva_id_reserva,
								'valor_anterior' => $valorAnterior,
								'valor_nuevo' => $totalGeneral,
								'diferencia' => $totalGeneral - $valorAnterior,
								'categoria_id' => 4,
								'categoria_precio_socio' => $categoria->categoria_precio_socio ?? 'N/A',
								'categoria_precio_socio_hijo' => $categoria->categoria_precio_socio_hijo ?? 'N/A',
								'categoria_precio_invitado' => $categoria->categoria_precio_invitado ?? 'N/A',
								'total_invitados' => count($invitados),
								'detalle_invitados' => $detalleCalculos,
								'usuario_actualizacion' => Session::getInstance()->get('usuarioData')['id'] ?? 'Sistema',
								'fecha_recalculo' => date('Y-m-d H:i:s')
							);

							$data['recalculo_reserva'] = $logRecalculo;
						} else {
							$data['recalculo_reserva'] = array(
								'mensaje' => 'No se requirio actualizacion',
								'valor_actual' => $valorAnterior,
								'valor_calculado' => $totalGeneral,
								'total_invitados' => count($invitados)
							);
						}
					} else {
						$data['recalculo_reserva'] = array(
							'error' => 'No se pudieron obtener invitados o categoría',
							'invitados_count' => is_countable($invitados) ? count($invitados) : 0,
							'categoria_existe' => ($categoria) && $categoria ? true : false
						);
					}
				} else {
					$data['recalculo_reserva'] = array(
						'error' => 'Reserva no encontrada',
						'reserva_id_buscada' => $content->reserva_id_reserva
					);
				}
			}

			$data['id_invitado'] = $id;
			$data['log_log'] = print_r($data, true);
			$data['log_tipo'] = 'EDITAR NEWINVITADOS';
			$logModel = new Administracion_Model_DbTable_Log();
			$logModel->insert($data);

			// Log específico mejorado para actualización
			$logDataDetallado = array(
				'id_invitado' => $id,
				'accion' => 'ACTUALIZAR_NEWINVITADOS',
				'usuario_id' => Session::getInstance()->get('usuarioData')['id'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
				'usuario_nombre' => Session::getInstance()->get('usuarioData')['nombre'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
				'fecha_accion' => date('Y-m-d H:i:s'),
				'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
				'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
				'datos_anteriores' => (array) $content,
				'datos_nuevos' => $data,
				'reserva_id_afectada' => $content->reserva_id_reserva,
				'actualizar_valor_reserva' => $actualizarValorReserva == 1,
				'detalle_recalculo' => ($data['recalculo_reserva']) ? $data['recalculo_reserva'] : null,
				'cambios_detectados' => $this->detectarCambios((array) $content, $data),
				'impacto_financiero' => array(
					'reserva_recalculada' => $actualizarValorReserva == 1,
					'mensaje' => $actualizarValorReserva == 1 ? 'Reserva recalculada automaticamente' : 'Sin recalculo de reserva'
				)
			);

			// Preparar datos para el modelo Log (solo los 4 campos que acepta)
			$logParaInsertar = array(
				'log_usuario' => Session::getInstance()->get('usuarioData')['id'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
				'log_tipo' => 'ACTUALIZAR_NEWINVITADOS_DETALLADO',
				'log_fecha' => date('Y-m-d H:i:s'),
				'log_log' => json_encode($logDataDetallado, JSON_UNESCAPED_UNICODE)
			);

			$logModel->insert($logParaInsertar);

			// Registro liviano y consultable del cambio de tipo (Socio/Cosocio/Invitado),
			// usado por el historial de cambios en el listado de facturación.
			$cambiosDetectados = $logDataDetallado['cambios_detectados'];
			if (isset($cambiosDetectados['invitado_tipo']) || isset($cambiosDetectados['invitadoReserva_estado_invitado'])) {
				$this->registrarCambioTipoInvitado($id, $content, $data, $cambiosDetectados);
			}

			// Recálculo automático post-actualización (siempre ejecutar para mantener consistencia)
			if ($content->reserva_id_reserva > 0) {
				$valorNuevoPostActualizacion = $this->recalcularValorReserva($content->reserva_id_reserva, $logDataDetallado);

				// Log específico del recálculo automático post-actualización
				$logRecalculoAutoDetallado = array(
					'accion' => 'RECALCULO_POST_ACTUALIZACION',
					'reserva_id' => $content->reserva_id_reserva,
					'invitado_actualizado_id' => $id,
					'valor_recalculado' => $valorNuevoPostActualizacion,
					'fecha_recalculo' => date('Y-m-d H:i:s'),
					'usuario_id' => Session::getInstance()->get('usuarioData')['id'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
					'motivo' => 'Recálculo automático después de actualizar invitado',
					'cambios_que_provocaron_recalculo' => $this->detectarCambios((array) $content, $data),
					'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A'

				);

				// Preparar datos para el modelo Log
				$logRecalculoParaInsertar = array(
					'log_usuario' => Session::getInstance()->get('usuarioData')['id'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
					'log_tipo' => 'RECALCULO_RESERVA_POST_ACTUALIZACION',
					'log_fecha' => date('Y-m-d H:i:s'),
					'log_log' => json_encode($logRecalculoAutoDetallado, JSON_UNESCAPED_UNICODE)
				);

				$logModel->insert($logRecalculoParaInsertar);
			}
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe un identificador  y elimina un newinvitados  y redirecciona al listado de newinvitados.
	 *
	 * @return void.
	 */
	public function deleteAction()
	{
		$this->setLayout('blanco');
		$csrf = $this->_getSanitizedParam("csrf");
		if (Session::getInstance()->get('csrf')[$this->_csrf_section] == $csrf) {
			$id = $this->_getSanitizedParam("id");
			if (($id) && $id > 0) {
				$content = $this->mainModel->getById($id);
				if (($content)) {
					// Obtener información de la reserva antes de eliminar
					$reservaId = $content->reserva_id_reserva;
					$reservasModel = new Administracion_Model_DbTable_Reservas();
					$reservaAntes = $reservasModel->getById($reservaId);
					$valorAnterior = $reservaAntes ? $reservaAntes->reserva_total_pagar : 0;

					// Eliminar el registro
					$this->mainModel->deleteRegister($id);

					// Log detallado para eliminación
					$logDataDetallado = array(
						'id_invitado' => $id,
						'accion' => 'ELIMINAR_NEWINVITADOS',
						'usuario_id' => Session::getInstance()->get('usuarioData')['id'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
						'usuario_nombre' => Session::getInstance()->get('usuarioData')['nombre'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
						'fecha_accion' => date('Y-m-d H:i:s'),
						'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A',
						'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'N/A',
						'datos_eliminados' => (array) $content,
						'reserva_id_afectada' => $reservaId,
						'valor_reserva_antes' => $valorAnterior,
						'documento_eliminado' => $content->documento_invitado,
						'nombre_eliminado' => $content->invitadoReserva_nombre_invitado,
						'apellido_eliminado' => $content->invitadoReserva_apellido_invitado,
						'tipo_invitado_eliminado' => $content->invitado_tipo,
						'impacto_financiero' => array(
							'mensaje' => 'Invitado eliminado - requiere recalculo automatico de reserva',
							'valor_anterior_reserva' => $valorAnterior,
							'requiere_recalculo' => true
						),
					);

					// Preparar datos para el modelo Log
					$logParaInsertar = array(
						'log_usuario' => Session::getInstance()->get('usuarioData')['id'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
						'log_tipo' => 'ELIMINAR_NEWINVITADOS',
						'log_fecha' => date('Y-m-d H:i:s'),
						'log_log' => json_encode($logDataDetallado, JSON_UNESCAPED_UNICODE)
					);

					$logModel = new Administracion_Model_DbTable_Log();
					$logModel->insert($logParaInsertar);

					// Recalcular automáticamente el valor de la reserva después de eliminar
					if ($reservaId > 0) {
						$valorNuevo = $this->recalcularValorReservaDespuesEliminar($reservaId, $logDataDetallado);

						// Log adicional del recálculo
						$logRecalculoDetallado = array(
							'accion' => 'RECALCULO_POST_ELIMINACION',
							'reserva_id' => $reservaId,
							'valor_anterior' => $valorAnterior,
							'valor_nuevo' => $valorNuevo,
							'diferencia' => $valorNuevo - $valorAnterior,
							'invitado_eliminado_id' => $id,
							'fecha_recalculo' => date('Y-m-d H:i:s'),
							'usuario_id' => Session::getInstance()->get('usuarioData')['id'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
							'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'N/A'

						);

						// Preparar datos del recálculo para el modelo Log
						$logRecalculoParaInsertar = array(
							'log_usuario' => Session::getInstance()->get('usuarioData')['id'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
							'log_tipo' => 'RECALCULO_RESERVA_POST_ELIMINACION',
							'log_fecha' => date('Y-m-d H:i:s'),
							'log_log' => json_encode($logRecalculoDetallado, JSON_UNESCAPED_UNICODE)
						);

						$reservasModel = new Administracion_Model_DbTable_Reservas();
						$totalPersonasReserva = count($this->mainModel->getList("reserva_id_reserva = '{$reservaId}'"));

						$reservasModel->editField($reservaId, "reserva_total_personas", $totalPersonasReserva);
						$reservasModel->editField($reservaId, "reserva_total_pagar", $valorNuevo);
						$logModel->insert($logRecalculoParaInsertar);
					}
				}
			}
		}
		header('Location: ' . $this->route . '' . '');
	}

	/**
	 * Recibe la informacion del formulario y la retorna en forma de array para la edicion y creacion de Newinvitados.
	 *
	 * @return array con toda la informacion recibida del formulario.
	 */
	private function getData()
	{
		$data = array();
		if ($this->_getSanitizedParam("reserva_id_reserva") == '') {
			$data['reserva_id_reserva'] = '0';
		} else {
			$data['reserva_id_reserva'] = $this->_getSanitizedParam("reserva_id_reserva");
		}
		$data['documento_invitado'] = $this->_getSanitizedParam("documento_invitado");
		$data['invitadoReserva_nombre_invitado'] = $this->_getSanitizedParam("invitadoReserva_nombre_invitado");
		$data['invitadoReserva_apellido_invitado'] = $this->_getSanitizedParam("invitadoReserva_apellido_invitado");
		$data['invitadoReserva_correo_invitado'] = $this->_getSanitizedParam("invitadoReserva_correo_invitado");
		$data['invitadoReserva_estado_invitado'] = $this->_getSanitizedParam("invitadoReserva_estado_invitado");
		$data['invitadoReserva_fecha_nacimiento'] = $this->_getSanitizedParam("invitadoReserva_fecha_nacimiento");
		$data['invitadoReserva_telefono'] = $this->_getSanitizedParam("invitadoReserva_telefono");
		$data['invitadosReserva_fecha_creacion'] = $this->_getSanitizedParam("invitadosReserva_fecha_creacion");
		$data['invitadosReserva_fecha_actualizacion'] = $this->_getSanitizedParam("invitadosReserva_fecha_actualizacion");
		$data['invitadosReserva_usuario_creacion'] = $this->_getSanitizedParam("invitadosReserva_usuario_creacion");
		$data['invitadosReserva_actualizacion'] = $this->_getSanitizedParam("invitadosReserva_actualizacion");
		$data['invitado_tipo'] = $this->_getSanitizedParam("invitado_tipo");
		if ($this->_getSanitizedParam("invitado_evento") == '') {
			$data['invitado_evento'] = '0';
		} else {
			$data['invitado_evento'] = $this->_getSanitizedParam("invitado_evento");
		}
		if ($this->_getSanitizedParam("invitadoReserva_beneficiario_menor25") == '') {
			$data['invitadoReserva_beneficiario_menor25'] = '0';
		} else {
			$data['invitadoReserva_beneficiario_menor25'] = $this->_getSanitizedParam("invitadoReserva_beneficiario_menor25");
		}
		if ($this->_getSanitizedParam("invitadoReserva_beneficiario_hijo") == '') {
			$data['invitadoReserva_beneficiario_hijo'] = '0';
		} else {
			$data['invitadoReserva_beneficiario_hijo'] = $this->_getSanitizedParam("invitadoReserva_beneficiario_hijo");
		}
		$data['invitadoReserva_beneficiario_cupo'] = $this->_getSanitizedParam("invitadoReserva_beneficiario_cupo");
		$data['invitadoReserva_beneficiario_principal'] = $this->_getSanitizedParam("invitadoReserva_beneficiario_principal");
		$data['invitadoReserva_numero_carnet'] = $this->_getSanitizedParam("invitadoReserva_numero_carnet");
		return $data;
	}

	/**
	 * Genera los valores del campo reserva_id_reserva.
	 *
	 * @return array cadena con los valores del campo reserva_id_reserva.
	 */
	private function getReservaidreserva()
	{
		$modelData = new Administracion_Model_DbTable_Dependreservas();
		$data = $modelData->getList();
		$array = array();
		foreach ($data as $key => $value) {
			$array[$value->id] = $value->id;
		}
		return $array;
	}


	/**
	 * Genera los valores del campo invitadoReserva_estado_invitado.
	 *
	 * @return array cadena con los valores del campo invitadoReserva_estado_invitado.
	 */
	private function getInvitadoReservaestadoinvitado()
	{
		$array = array();
		$array['A'] = 'Socio';
		$array['S'] = 'Cosocio';
		$array['P'] = 'Invitado';
		return $array;
	}


	/**
	 * Genera los valores del campo invitado_tipo.
	 *
	 * @return array cadena con los valores del campo invitado_tipo.
	 */
	private function getInvitadotipo()
	{
		$array = array();
		$array['1'] = 'Socio';
		$array['2'] = 'Invitado';
		return $array;
	}


	/**
	 * Genera los valores del campo invitado_evento.
	 *
	 * @return array cadena con los valores del campo invitado_evento.
	 */
	private function getInvitadoevento()
	{
		$modelData = new Administracion_Model_DbTable_Dependeventos();
		$data = $modelData->getList();
		$array = array();
		foreach ($data as $key => $value) {
			$array[$value->evento_id] = $value->evento_titulo;
		}
		return $array;
	}

	/**
	 * Genera la consulta con los filtros de este controlador.
	 *
	 * @return array cadena con los filtros que se van a asignar a la base de datos
	 */
	protected function getFilter()
	{
		$filtros = " 1 = 1";
		if (Session::getInstance()->get($this->namefilter) != "") {

			$filters = (object) Session::getInstance()->get($this->namefilter);
			if ($filters->reserva_id_reserva != '') {
				$filtros = $filtros . " AND reserva_id_reserva = '" . $filters->reserva_id_reserva . "'";
			} else {
				$filtros = $filtros . "  AND reserva_id_reserva IN (SELECT id
                FROM reservas
                WHERE reserva_estado IN (2, 3, 11)) ";
			}
			if ($filters->documento_invitado != '') {
				$filtros = $filtros . " AND documento_invitado LIKE '%" . $filters->documento_invitado . "%'";
			}
			if ($filters->invitado_tipo != '') {
				$filtros = $filtros . " AND invitado_tipo ='" . $filters->invitado_tipo . "'";
			}
			if ($filters->invitadoReserva_numero_carnet != '') {
				$filtros = $filtros . " AND invitadoReserva_numero_carnet LIKE '%" . $filters->invitadoReserva_numero_carnet . "%'";
			}
			if ($filters->reserva_invitados == 1) {
				// Reservas donde TODOS los invitados (tipo 2) NO tienen documento
				$subquery = "
        SELECT reserva_id_reserva
        FROM invitadosreserva
        WHERE reserva_id_reserva > 0 
          AND invitado_tipo = 2
        GROUP BY reserva_id_reserva
        HAVING COUNT(CASE WHEN TRIM(COALESCE(documento_invitado, '')) <> '' THEN 1 END) = 0
    ";

				$filtros .= " AND reserva_id_reserva IN ($subquery)";
			}

			if ($filters->reserva_invitados == 2) {
				// Reservas donde al menos un invitado (tipo 2) TIENE documento
				$subquery = "
        SELECT reserva_id_reserva
        FROM invitadosreserva
        WHERE reserva_id_reserva > 0 
          AND invitado_tipo = 2
        GROUP BY reserva_id_reserva
        HAVING COUNT(CASE WHEN TRIM(COALESCE(documento_invitado, '')) <> '' THEN 1 END) > 0
    ";

				$filtros .= " AND reserva_id_reserva IN ($subquery)";
			}
		} else {
			$filtros = $filtros . "  AND reserva_id_reserva IN (SELECT id
							FROM reservas
							WHERE reserva_estado IN (2, 3, 11)) ";
		}
		// echo $filtros;
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
			$parramsfilter['reserva_id_reserva'] = $this->_getSanitizedParam("reserva_id_reserva");
			$parramsfilter['documento_invitado'] = $this->_getSanitizedParam("documento_invitado");
			$parramsfilter['invitado_tipo'] = $this->_getSanitizedParam("invitado_tipo");
			$parramsfilter['reserva_invitados'] = $this->_getSanitizedParam("reserva_invitados");
			$parramsfilter['invitadoReserva_numero_carnet'] = $this->_getSanitizedParam("invitadoReserva_numero_carnet");
			Session::getInstance()->set($this->namefilter, $parramsfilter);
		}
		if ($this->_getSanitizedParam("cleanfilter") == 1) {
			Session::getInstance()->set($this->namefilter, '');
			Session::getInstance()->set($this->namepageactual, 1);
		}
	}

	/**
	 * Detecta los cambios entre los datos anteriores y nuevos
	 *
	 * @param array $datosAnteriores
	 * @param array $datosNuevos
	 * @return array
	 */
	private function detectarCambios($datosAnteriores, $datosNuevos)
	{
		$cambios = array();

		foreach ($datosNuevos as $campo => $valorNuevo) {
			$valorAnterior = ($datosAnteriores[$campo]) ? $datosAnteriores[$campo] : null;
			if ($valorAnterior != $valorNuevo) {
				$cambios[$campo] = array(
					'anterior' => $valorAnterior,
					'nuevo' => $valorNuevo
				);
			}
		}

		return $cambios;
	}

	/**
	 * Registra en reservas_auditoria (tabla liviana e indexada por reserva_id)
	 * un cambio de tipo/estado del invitado (Socio/Cosocio/Invitado), para que
	 * el historial en el listado de facturación no dependa de recorrer la tabla
	 * `log` completa buscando dentro de los JSON.
	 *
	 * @param int $id
	 * @param object $content datos del invitado antes de la actualización
	 * @param array $data datos del invitado después de la actualización
	 * @param array $cambiosDetectados resultado de detectarCambios()
	 * @return void
	 */
	private function registrarCambioTipoInvitado($id, $content, $data, $cambiosDetectados)
	{
		if (isset($cambiosDetectados['invitadoReserva_estado_invitado'])) {
			$estados = $this->getInvitadoReservaestadoinvitado();
			$anteriorCod = $cambiosDetectados['invitadoReserva_estado_invitado']['anterior'];
			$nuevoCod = $cambiosDetectados['invitadoReserva_estado_invitado']['nuevo'];
			$anterior = $estados[$anteriorCod] ?? ($anteriorCod ?: '—');
			$nuevo = $estados[$nuevoCod] ?? ($nuevoCod ?: '—');
		} else {
			$tipos = $this->getInvitadotipo();
			$anteriorCod = $cambiosDetectados['invitado_tipo']['anterior'];
			$nuevoCod = $cambiosDetectados['invitado_tipo']['nuevo'];
			$anterior = $tipos[$anteriorCod] ?? ($anteriorCod ?: '—');
			$nuevo = $tipos[$nuevoCod] ?? ($nuevoCod ?: '—');
		}

		if ($anterior === $nuevo) {
			return;
		}

		$nombre = trim(($data['invitadoReserva_nombre_invitado'] ?? '') . ' ' . ($data['invitadoReserva_apellido_invitado'] ?? ''));

		$auditoriaModel = new Administracion_Model_DbTable_Reservasauditoria();
		$auditoriaModel->insert(array(
			'reserva_id' => $content->reserva_id_reserva,
			'numero_carnet' => $data['invitadoReserva_numero_carnet'] ?? '',
			'documento_socio' => $data['documento_invitado'] ?? '',
			'accion' => 'CAMBIO_TIPO_INVITADO',
			'controlador' => 'newinvitados',
			'metodo' => 'updateAction',
			'estado_anterior' => $anterior,
			'estado_nuevo' => $nuevo,
			'mesa_id_anterior' => '',
			'mesa_id_nuevo' => '',
			'invitados_antes' => '',
			'invitados_despues' => '',
			'datos_json' => json_encode(array('id_invitado' => $id, 'invitado_nombre' => $nombre), JSON_UNESCAPED_UNICODE),
			'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
			'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
			'session_data' => '',
			'url_completa' => $_SERVER['REQUEST_URI'] ?? '',
			'parametros_get' => '',
			'parametros_post' => '',
			'observaciones' => "Invitado {$nombre} cambió de {$anterior} a {$nuevo}",
			'fecha_creacion' => date('Y-m-d H:i:s'),
			'usuario_sistema' => Session::getInstance()->get('usuarioData')['id'] ?? Session::getInstance()->get('kt_login_user') ?? 'Sistema',
		));
	}

	/**
	 * Recalcula el valor de una reserva específica
	 *
	 * @param int $reservaId
	 * @param array $logContext
	 * @return float
	 */
	private function recalcularValorReserva($reservaId, $logContext = array())
	{
		$reservaModel = new Administracion_Model_DbTable_Reservas();
		$reserva = $reservaModel->getById($reservaId);

		if (!$reserva) {
			return 0;
		}

		$invitados = $this->mainModel->getList("reserva_id_reserva = '{$reservaId}'");
		$categoriasModel = new Administracion_Model_DbTable_Categorias();
		$categoria = $categoriasModel->getById(4);

		if (is_countable($invitados) && count($invitados) >= 1 && $categoria) {
			$valorAnterior = $reserva->reserva_total_pagar;
			$totalGeneral = 0;

			foreach ($invitados as $invitado) {
				$precio = 0;

				if ($invitado->invitado_tipo == '1') { // Beneficiario/Socio
					$esMenor25 = ($invitado->invitadoReserva_beneficiario_menor25) && $invitado->invitadoReserva_beneficiario_menor25 == '1';
					$esHijo = ($invitado->invitadoReserva_beneficiario_hijo) && $invitado->invitadoReserva_beneficiario_hijo == '1';

					if ($esMenor25 && $esHijo) {
						$precio = $categoria->categoria_precio_socio_hijo;
					} else {
						$precio = $categoria->categoria_precio_socio;
					}
				} else { // Invitado
					$precio = $categoria->categoria_precio_invitado;
				}

				$totalGeneral += $precio;
			}

			// Solo actualizar si hay cambio en el total
			if ($totalGeneral > 0 && $totalGeneral != $valorAnterior) {
				$reservaModel->editField($reservaId, "reserva_total_pagar", $totalGeneral);
				return $totalGeneral;
			}
		}

		return $reserva->reserva_total_pagar;
	}

	/**
	 * Recalcula el valor de una reserva después de eliminar un invitado
	 *
	 * @param int $reservaId
	 * @param array $logContext
	 * @return float
	 */
	private function recalcularValorReservaDespuesEliminar($reservaId, $logContext = array())
	{
		return $this->recalcularValorReserva($reservaId, $logContext);
	}

	/**
	 * Muestra la página de logs específicos de newinvitados
	 *
	 * @return void
	 */
	public function logsAction()
	{
		$title = "Logs de Invitados - Actividad Detallada";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;
		$this->_view->route = $this->route;

		$logModel = new Administracion_Model_DbTable_Log();

		// Filtros específicos para logs de newinvitados
		$filtros = " (log_tipo LIKE '%NEWINVITADOS%' OR log_tipo LIKE '%RECALCULO_RESERVA%') ";

		// Agregar filtros adicionales si se envían
		$fechaDesde = $this->_getSanitizedParam("fecha_desde");
		$fechaHasta = $this->_getSanitizedParam("fecha_hasta");
		$tipoAccion = $this->_getSanitizedParam("tipo_accion");
		$reservaId = $this->_getSanitizedParam("reserva_id");

		if ($fechaDesde) {
			$filtros .= " AND DATE(log_fecha) >= '{$fechaDesde}' ";
		}
		if ($fechaHasta) {
			$filtros .= " AND DATE(log_fecha) <= '{$fechaHasta}' ";
		}
		if ($tipoAccion) {
			$filtros .= " AND log_tipo = '{$tipoAccion}' ";
		}
		if ($reservaId) {
			$filtros .= " AND log_log LIKE '%reserva_id_reserva.*{$reservaId}%' ";
		}

		$order = "log_fecha DESC";
		$page = $this->_getSanitizedParam("page") ?: 1;
		$amount = 200;
		$start = ($page - 1) * $amount;

		$allLogs = $logModel->getList($filtros, $order);
		$logs = $logModel->getListPages($filtros, $order, $start, $amount);

		$this->_view->logs = $logs;
		$this->_view->register_number = count($allLogs);
		$this->_view->totalpages = ceil(count($allLogs) / $amount);
		$this->_view->page = $page;
		$this->_view->pages = $amount;
		$this->_view->fecha_desde = $fechaDesde;
		$this->_view->fecha_hasta = $fechaHasta;
		$this->_view->tipo_accion = $tipoAccion;
		$this->_view->reserva_id = $reservaId;

		// Tipos de acción disponibles
		$this->_view->tipos_accion = array(
			'CREAR_NEWINVITADOS' => 'Crear Invitado',
			'ACTUALIZAR_NEWINVITADOS_DETALLADO' => 'Actualizar Invitado',
			'ELIMINAR_NEWINVITADOS' => 'Eliminar Invitado',
			'BORRAR NEWINVITADOS' => 'Eliminar Invitado (Legacy)',
			'EDITAR NEWINVITADOS' => 'Editar Invitado (Legacy)',
			'RECALCULO_RESERVA_POST_ELIMINACION' => 'Recálculo Post-Eliminación',
			'RECALCULO_RESERVA_POST_ACTUALIZACION' => 'Recálculo Post-Actualización'
		);
	}

	/**
	 * Muestra la página de logs resumidos para usuarios no técnicos
	 *
	 * @return void
	 */
	public function actividadAction()
	{
		$title = "Historial de Actividad - Vista Resumida";
		$this->getLayout()->setTitle($title);
		$this->_view->titlesection = $title;
		$this->_view->route = $this->route;

		$logModel = new Administracion_Model_DbTable_Log();

		// Filtros específicos para logs de newinvitados
		$filtros = " (log_tipo LIKE '%NEWINVITADOS%' OR log_tipo LIKE '%RECALCULO_RESERVA%') ";

		// Agregar filtros adicionales si se envían
		$fechaDesde = $this->_getSanitizedParam("fecha_desde");
		$fechaHasta = $this->_getSanitizedParam("fecha_hasta");
		$tipoAccion = $this->_getSanitizedParam("tipo_accion");
		$reservaId = $this->_getSanitizedParam("reserva_id");

		if ($fechaDesde) {
			$filtros .= " AND DATE(log_fecha) >= '{$fechaDesde}' ";
		}
		if ($fechaHasta) {
			$filtros .= " AND DATE(log_fecha) <= '{$fechaHasta}' ";
		}
		if ($tipoAccion) {
			$filtros .= " AND log_tipo = '{$tipoAccion}' ";
		}
		if ($reservaId) {
			$filtros .= " AND log_log LIKE '%reserva_id_reserva.*{$reservaId}%' ";
		}

		$order = "log_fecha DESC";
		$page = $this->_getSanitizedParam("page") ?: 1;
		$amount = 15;
		$start = ($page - 1) * $amount;

		// Obtener logs y agrupar por operación para mostrar solo 1 por operación
		$allLogsRaw = $logModel->getList($filtros, $order);
		$logsGrouped = $this->_groupLogsByOperation($allLogsRaw);

		// Paginar los logs agrupados
		$totalGrouped = count($logsGrouped);
		$logsForPage = array_slice($logsGrouped, $start, $amount);

		$this->_view->logs = $logsForPage;
		$this->_view->register_number = $totalGrouped;
		$this->_view->totalpages = ceil($totalGrouped / $amount);
		$this->_view->page = $page;
		$this->_view->pages = $amount;
		$this->_view->fecha_desde = $fechaDesde;
		$this->_view->fecha_hasta = $fechaHasta;
		$this->_view->tipo_accion = $tipoAccion;
		$this->_view->reserva_id = $reservaId;

		// Tipos de acción disponibles para filtros
		$this->_view->tipos_accion = array(
			'CREAR_NEWINVITADOS' => 'Invitado Creado',
			'ACTUALIZAR_NEWINVITADOS_DETALLADO' => 'Invitado Actualizado',
			'ELIMINAR_NEWINVITADOS' => 'Invitado Eliminado',
			'RECALCULO_RESERVA_POST_ELIMINACION' => 'Reserva Recalculada',
			'RECALCULO_RESERVA_POST_ACTUALIZACION' => 'Reserva Recalculada'
		);
	}

	/**
	 * Agrupa los logs por operación para mostrar solo uno por cada acción realizada
	 *
	 * @param array $logs
	 * @return array
	 */
	private function _groupLogsByOperation($logs)
	{
		$grouped = array();
		$operationsMap = array();

		foreach ($logs as $log) {
			$logData = $this->_extractBasicLogData($log);

			// Primero verificar si es un log de bajo valor que debemos ignorar completamente
			if ($this->_isLowValueLog($log)) {
				// Ignorar logs de bajo valor completamente sin procesarlos
				continue;
			}

			// Crear identificador único con ventana de tiempo más específica para UPDATE
			$timestamp = strtotime($log->log_fecha);
			$normalizedType = $this->_normalizeLogType($log->log_tipo);

			// Para operaciones UPDATE usar ventana de 60 segundos, para otras 2 minutos
			if ($normalizedType === 'UPDATE') {
				$timeWindow = floor($timestamp / 60) * 60; // 1 minuto para UPDATE
			} else {
				$timeWindow = floor($timestamp / 120) * 120; // 2 minutos para otros
			}

			// Crear clave de operación más específica
			$operationId = sprintf(
				'%s_%s_%s_%d',
				$normalizedType,
				$logData['documento_invitado'] ?? 'NODOC',
				$logData['reserva_id'] ?? 'NORES',
				$timeWindow
			);

			// Determinar si este log debe ser agrupado con otros
			$shouldGroup = $this->_shouldGroupLog($log->log_tipo);

			if ($shouldGroup) {
				// Para operaciones que pueden ser agrupadas
				if (isset($operationsMap[$operationId])) {
					// Ya existe una operación, comparar cuál es mejor
					$existingIndex = $operationsMap[$operationId];
					$existingLog = $grouped[$existingIndex];

					if ($this->_hasMoreInformation($log, $existingLog)) {
						// Reemplazar con el log más informativo
						$grouped[$existingIndex] = $log;
					}
					// Si el nuevo log no es mejor, simplemente lo ignoramos
				} else {
					// Nueva operación - agregarlo
					$grouped[] = $log;
					$operationsMap[$operationId] = count($grouped) - 1;
				}
			} else {
				// Para operaciones que no se agrupan (CREAR, ELIMINAR, etc.)
				$grouped[] = $log;
			}
		}

		return $grouped;
	}

	/**
	 * Normaliza los tipos de log para mejor agrupación
	 *
	 * @param string $logType
	 * @return string
	 */
	private function _normalizeLogType($logType)
	{
		// Normalizar tipos similares para agruparlos mejor
		$normalizations = array(
			'ACTUALIZAR_NEWINVITADOS_DETALLADO' => 'UPDATE',
			'EDITAR NEWINVITADOS' => 'UPDATE',
			'RECALCULO_RESERVA_POST_ACTUALIZACION' => 'UPDATE',
			'RECALCULO_RESERVA_POST_ELIMINACION' => 'RECALC',
			'CREAR_NEWINVITADOS' => 'CREATE',
			'ELIMINAR_NEWINVITADOS' => 'DELETE',
			'BORRAR NEWINVITADOS' => 'DELETE'
		);

		return $normalizations[$logType] ?? $logType;
	}

	/**
	 * Determina si un tipo de log debe ser agrupado
	 *
	 * @param string $logType
	 * @return bool
	 */
	private function _shouldGroupLog($logType)
	{
		$groupableTypes = array(
			'ACTUALIZAR_NEWINVITADOS_DETALLADO',
			'EDITAR NEWINVITADOS',
			'RECALCULO_RESERVA_POST_ACTUALIZACION'
		);

		return in_array($logType, $groupableTypes);
	}

	/**
	 * Determina si un log tiene más información que otro
	 *
	 * @param object $log1
	 * @param object $log2
	 * @return bool
	 */
	private function _hasMoreInformation($log1, $log2)
	{
		// Primero verificar si alguno de los logs es claramente inútil
		if ($this->_isLowValueLog($log1) && !$this->_isLowValueLog($log2)) {
			return false; // log2 es mejor
		}
		if (!$this->_isLowValueLog($log1) && $this->_isLowValueLog($log2)) {
			return true; // log1 es mejor
		}

		// Prioridad por tipo de log (ACTUALIZAR_NEWINVITADOS_DETALLADO es el más completo)
		$priorities = array(
			'ACTUALIZAR_NEWINVITADOS_DETALLADO' => 10,
			'RECALCULO_RESERVA_POST_ACTUALIZACION' => 5,
			'EDITAR NEWINVITADOS' => 2
		);

		$priority1 = isset($priorities[$log1->log_tipo]) ? $priorities[$log1->log_tipo] : 0;
		$priority2 = isset($priorities[$log2->log_tipo]) ? $priorities[$log2->log_tipo] : 0;

		// Evaluar contenido para ajustar prioridades
		$content1 = json_decode($log1->log_log, true);
		$content2 = json_decode($log2->log_log, true);

		$score1 = $this->_scoreLogContent($content1);
		$score2 = $this->_scoreLogContent($content2);

		// Combinar prioridad base con puntuación de contenido
		$finalScore1 = $priority1 + $score1;
		$finalScore2 = $priority2 + $score2;

		// Si log1 es significativamente mejor, elegirlo
		if ($finalScore1 > $finalScore2 * 1.2) {
			return true;
		}

		// Si log2 es significativamente mejor, elegir log2
		if ($finalScore2 > $finalScore1 * 1.2) {
			return false;
		}

		// Si están muy cerca, preferir el más reciente
		$time1 = strtotime($log1->log_fecha);
		$time2 = strtotime($log2->log_fecha);

		return $time1 > $time2;
	}

	/**
	 * Detecta si un log tiene bajo valor informativo
	 *
	 * @param object $log
	 * @return bool
	 */
	private function _isLowValueLog($log)
	{
		// Los logs "EDITAR NEWINVITADOS" simples suelen tener poca información
		if ($log->log_tipo === 'EDITAR NEWINVITADOS') {
			$content = json_decode($log->log_log, true);

			// Si no se puede decodificar como JSON, es de bajo valor
			if (!is_array($content)) {
				return true;
			}

			// Si no tiene cambios detectados ni información de recálculo, es de bajo valor
			if (
				!isset($content['cambios_detectados']) &&
				!isset($content['detalle_recalculo']) &&
				!isset($content['valor_anterior']) &&
				!isset($content['valor_nuevo']) &&
				!isset($content['impacto_financiero']) &&
				!isset($content['datos_nuevos']) &&
				!isset($content['datos_anteriores'])
			) {
				return true;
			}

			// Si el contenido es muy básico (solo metadatos), es de bajo valor
			$informativeKeys = array(
				'cambios_detectados',
				'datos_nuevos',
				'datos_anteriores',
				'detalle_recalculo',
				'impacto_financiero',
				'recalculo_reserva',
				'valor_anterior',
				'valor_nuevo',
				'diferencia'
			);
			$hasInformativeContent = false;

			foreach ($informativeKeys as $key) {
				if (isset($content[$key]) && !empty($content[$key])) {
					$hasInformativeContent = true;
					break;
				}
			}

			if (!$hasInformativeContent) {
				return true;
			}
		}

		// También verificar logs que muestran datos N/A o vacíos
		$logDataBasic = $this->_extractBasicLogData($log);

		// Si el documento y la reserva son N/A o vacíos, probablemente sea un log de bajo valor
		if (
			(!isset($logDataBasic['documento_invitado']) || $logDataBasic['documento_invitado'] === 'N/A' || empty($logDataBasic['documento_invitado'])) &&
			(!isset($logDataBasic['reserva_id']) || $logDataBasic['reserva_id'] === 'N/A' || empty($logDataBasic['reserva_id']) || $logDataBasic['reserva_id'] === '0')
		) {

			// Si además no tiene cambios detectados o información financiera, es definitivamente de bajo valor
			$content = json_decode($log->log_log, true);
			if (is_array($content)) {
				$hasValuableInfo = isset($content['cambios_detectados']) && !empty($content['cambios_detectados']) ||
					isset($content['valor_anterior']) && isset($content['valor_nuevo']) ||
					isset($content['impacto_financiero']) && !empty($content['impacto_financiero']);

				if (!$hasValuableInfo) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Asigna un puntaje a un log basado en la cantidad de información útil
	 *
	 * @param array $content
	 * @return int
	 */
	private function _scoreLogContent($content)
	{
		if (!is_array($content)) {
			return 0;
		}

		$score = 0;

		// Máxima puntuación para información realmente valiosa
		if (isset($content['cambios_detectados']) && is_array($content['cambios_detectados']) && !empty($content['cambios_detectados'])) {
			$score += 50; // Muy importante
			$score += count($content['cambios_detectados']) * 5; // Bonus por cantidad de cambios
		}

		if (isset($content['impacto_financiero']) && is_array($content['impacto_financiero']) && !empty($content['impacto_financiero'])) {
			$score += 40; // Información financiera es muy valiosa
		}

		if (isset($content['valor_anterior']) && isset($content['valor_nuevo']) && $content['valor_anterior'] != $content['valor_nuevo']) {
			$score += 35; // Cambios de valor son importantes
		}

		if (isset($content['detalle_recalculo']) && !empty($content['detalle_recalculo'])) {
			$score += 30;
		}

		if (isset($content['datos_anteriores']) && isset($content['datos_nuevos'])) {
			$score += 25; // Comparación antes/después es valiosa
		}

		// Puntuación media para datos estructurados
		if (isset($content['datos_invitado']) && is_array($content['datos_invitado'])) {
			$score += 15;
		}

		if (isset($content['reserva_id_afectada']) && $content['reserva_id_afectada'] > 0) {
			$score += 10; // Información de contexto
		}

		if (isset($content['usuario_nombre']) && !empty($content['usuario_nombre'])) {
			$score += 5; // Metadatos útiles
		}

		// Penalizar logs que son solo metadatos básicos
		$basicKeys = array('log_usuario', 'log_tipo', 'log_fecha', 'ip_address', 'user_agent');
		$informativeKeys = array(
			'cambios_detectados',
			'datos_nuevos',
			'datos_anteriores',
			'detalle_recalculo',
			'impacto_financiero',
			'valor_anterior',
			'valor_nuevo'
		);

		$hasOnlyBasicInfo = true;
		foreach ($informativeKeys as $key) {
			if (isset($content[$key]) && !empty($content[$key])) {
				$hasOnlyBasicInfo = false;
				break;
			}
		}

		if ($hasOnlyBasicInfo) {
			$score = max(0, $score - 20); // Penalizar logs con solo metadatos
		}

		// Verificar si el contenido es un array simple sin estructura
		if (count($content) <= 3 && $hasOnlyBasicInfo) {
			$score = max(0, $score - 10);
		}

		return $score;
	}
	/**
	 * Extrae datos básicos del log para su procesamiento
	 *
	 * @param object $log
	 * @return array
	 */
	private function _extractBasicLogData($log)
	{
		$data = array();

		if (isset($log->log_log) && is_string($log->log_log)) {
			// Limpiar y decodificar JSON
			$cleanJson = str_replace(["\n", "\r", "\t"], ['\\n', '\\r', '\\t'], $log->log_log);
			$jsonDecoded = json_decode($cleanJson, true);

			if ($jsonDecoded !== null) {
				// 1. Primero intentar extraer desde datos_invitado (estructura de INSERT)
				if (isset($jsonDecoded['datos_invitado'])) {
					$datosInvitado = $jsonDecoded['datos_invitado'];
					$data['documento_invitado'] = $datosInvitado['documento_invitado'] ?? null;
					$data['reserva_id'] = $datosInvitado['reserva_id_reserva'] ?? null;
					$data['nombre_invitado'] = $datosInvitado['invitadoReserva_nombre_invitado'] ?? null;
					$data['apellido_invitado'] = $datosInvitado['invitadoReserva_apellido_invitado'] ?? null;
				}

				// 2. Si no encontramos datos, buscar en otras estructuras
				if (!$data['documento_invitado']) {
					$data['documento_invitado'] = $jsonDecoded['documento_invitado'] ??
						$jsonDecoded['datos_nuevos']['documento_invitado'] ??
						$jsonDecoded['datos_insertados']['documento_invitado'] ??
						$jsonDecoded['datos_eliminados']['documento_invitado'] ?? null;
				}

				if (!$data['reserva_id']) {
					$data['reserva_id'] = $jsonDecoded['reserva_id_reserva'] ??
						$jsonDecoded['datos_nuevos']['reserva_id_reserva'] ??
						$jsonDecoded['datos_insertados']['reserva_id_reserva'] ??
						$jsonDecoded['datos_eliminados']['reserva_id_reserva'] ??
						$jsonDecoded['reserva_id'] ?? null;
				}

				if (!$data['nombre_invitado']) {
					$data['nombre_invitado'] = $jsonDecoded['invitadoReserva_nombre_invitado'] ??
						$jsonDecoded['datos_nuevos']['invitadoReserva_nombre_invitado'] ??
						$jsonDecoded['datos_insertados']['invitadoReserva_nombre_invitado'] ??
						$jsonDecoded['datos_eliminados']['invitadoReserva_nombre_invitado'] ?? null;
				}
				if (!$data['apellido_invitado']) {
					$data['apellido_invitado'] = $jsonDecoded['invitadoReserva_apellido_invitado'] ??
						$jsonDecoded['datos_nuevos']['invitadoReserva_apellido_invitado'] ??
						$jsonDecoded['datos_insertados']['invitadoReserva_apellido_invitado'] ??
						$jsonDecoded['datos_eliminados']['invitadoReserva_apellido_invitado'] ?? null;
				}
			}
		}

		return $data;
	}

	/**
	 * Crea una clave única para agrupar operaciones similares
	 *
	 * @param object $log
	 * @param array $logData
	 * @return string
	 */
	private function _createOperationKey($log, $logData)
	{
		$tipo = $log->log_tipo ?? '';
		$documento = $logData['documento_invitado'] ?? '';
		$reserva = $logData['reserva_id'] ?? '';

		// Para actualizaciones, usar ventana de tiempo de 5 minutos
		if (in_array($tipo, ['ACTUALIZAR_NEWINVITADOS_DETALLADO', 'EDITAR NEWINVITADOS', 'RECALCULO_RESERVA_POST_ACTUALIZACION'])) {
			$fecha = date('Y-m-d H:i', strtotime($log->log_fecha ?? '') - (strtotime($log->log_fecha ?? '') % 300)); // Agrupa por bloques de 5 minutos
			return 'UPDATE_' . $documento . '_' . $reserva . '_' . $fecha;
		}

		// Para otras operaciones, usar el tiempo normal
		$fecha = date('Y-m-d H:i', strtotime($log->log_fecha ?? ''));
		return $tipo . '_' . $documento . '_' . $reserva . '_' . $fecha;
	}

	public function exportarinvitados()
	{
		$this->setLayout('blanco');
		$reservasModel = new Administracion_Model_DbTable_Reservas();
		$invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();

		// $reservasAceptadas = 
	}
}
