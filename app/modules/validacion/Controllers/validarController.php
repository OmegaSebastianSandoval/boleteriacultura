<?php

/**
 *
 */

class Validacion_validarController extends Validacion_mainController
{
	protected $_csrf_section = "validacion";


	public function init()
	{
		if (!Session::getInstance()->get("user")) {
			header('Location: /validacion/');
			exit;
		}
		parent::init();
	}

	public function indexAction()
	{

		Session::getInstance()->set("error_validacion", "");

		$this->_csrf_section = "validacion_" . date("YmdHis");
		$this->_csrf->generateCode($this->_csrf_section);
		$this->_view->csrf_section = $this->_csrf_section;
		$this->_view->csrf = Session::getInstance()->get('csrf')[$this->_csrf_section];

		$boletaInfoJson = $_POST['boletaInfo'] ?? null;
		$metodoEscaneado = $_POST['metodo_escaneado'] ?? $this->_getSanitizedParam('metodo_escaneado') ?? 'camera';
		$documento_scan = $_POST['documento_scan'];

		if ($boletaInfoJson) {
			$boletaInformacion = json_decode($boletaInfoJson);
			if ($boletaInformacion === null) {
				// Log error de JSON
				$this->logAuditoriaBoleta('ERROR_JSON', 'error', [
					'metodo_escaneado' => $metodoEscaneado,
					'motivo_fallo' => 'Error al decodificar JSON: ' . json_last_error_msg(),
					'observaciones' => 'Datos JSON inválidos recibidos',
					'parametros_recibidos' => $boletaInfoJson
				]);
				echo "Error al decodificar JSON: " . json_last_error_msg();
			}

			$uid = $boletaInformacion->boleta_uid;
			$token = $boletaInformacion->boleta_token;
		} else {
			$uid = $this->_getSanitizedParam("uid");
			$token = $this->_getSanitizedParam("token");
		}
		$boletaInfo = $this->infoBoleto($uid, $token);
		// error_log(print_r($boletaInfo, true));

		// Log acceso a vista de validación
		$this->logAuditoriaBoleta('ACCESO_VALIDACION', 'exitoso', [
			'documento_escaneado' => $boletaInfo->boleta_documento ?? null,
			'boleta_uid' => $uid,
			'boleta_token' => $token,
			'metodo_escaneado' => $metodoEscaneado,
			'observaciones' => "Usuario accede a vista de validación - Método: {$metodoEscaneado}",

		]);

		$this->_view->uid = $uid;
		$this->_view->token = $token;
		$this->_view->metodo_escaneado = $metodoEscaneado;
		$this->_view->documento_scan = $documento_scan ?? null;



		$boletaInfo = $this->infoBoleto($uid, $token);
		if (!$boletaInfo) {
			// Log boleta no encontrada
			$this->logAuditoriaBoleta('BOLETA_NO_ENCONTRADA', 'fallido', [
				'documento_escaneado' => $documento_scan ?? null,
				'boleta_uid' => $uid,
				'boleta_token' => $token,
				'metodo_escaneado' => $metodoEscaneado,
				'motivo_fallo' => 'Boleta no existe en base de datos',
				'observaciones' => 'Intento de validar boleta inexistente'
			]);
			Session::getInstance()->set("error_validacion", "El boleto no existe.");
		}
		if ($boletaInfo && $boletaInfo->boleta_estado != 1) {
			// Log boleta ya validada
			$this->logAuditoriaBoleta('BOLETA_YA_VALIDADA', 'duplicado', [
				'documento_escaneado' => $boletaInfo->boleta_documento,
				'boleta_uid' => $uid,
				'boleta_token' => $token,
				'metodo_escaneado' => $metodoEscaneado,
				'boleta_id' => $boletaInfo->boleta_id,
				'boleta_reserva_id' => $boletaInfo->boleta_reserva_id,
				'boleta_evento_id' => $boletaInfo->boleta_evento_id,
				'boleta_mesa' => $boletaInfo->boleta_mesa,
				'datos_boleta_antes' => $boletaInfo,
				'motivo_fallo' => 'Intento de validar boleta ya procesada',
				'observaciones' => 'Boleta estado: ' . $boletaInfo->boleta_estado
			]);
			Session::getInstance()->set("error_validacion", "El boleto ya fue validado.");
		}

		$reserva = $this->validarReserva($uid, $token);
		// print_r($reserva);
		if (!$reserva) {
			// Log problema con reserva
			$this->logAuditoriaBoleta('RESERVA_NO_VALIDA', 'fallido', [
				'boleta_uid' => $uid,
				'boleta_token' => $token,
				'metodo_escaneado' => $metodoEscaneado,
				'boleta_id' => $boletaInfo ? $boletaInfo->boleta_id : null,
				'datos_boleta_antes' => $boletaInfo,
				'motivo_fallo' => 'Boleta no asociada a reserva válida',
				'observaciones' => 'Problema de integridad en datos'
			]);
			Session::getInstance()->set("error_validacion", "El boleto no se encuentra asociado a una reserva, contacte al administrador.");
		}

		if (Session::getInstance()->get("error_validacion")) {
			header("Location: /validacion/validar/error?uid={$uid}&token={$token}&metodo_escaneado={$metodoEscaneado}&documento_scan={$boletaInfo->boleta_documento}");
			exit;
		}

		// Log carga exitosa de datos para validación
		$this->logAuditoriaBoleta('DATOS_CARGADOS', 'exitoso', [
			'documento_escaneado' => $boletaInfo->boleta_documento ?? null,
			'boleta_uid' => $uid,
			'boleta_token' => $token,
			'metodo_escaneado' => $metodoEscaneado,
			'boleta_id' => $boletaInfo ? $boletaInfo->boleta_id : null,
			'boleta_reserva_id' => $boletaInfo ? $boletaInfo->boleta_reserva_id : null,
			'boleta_evento_id' => $boletaInfo ? $boletaInfo->boleta_evento_id : null,
			'boleta_mesa' => $boletaInfo ? $boletaInfo->boleta_mesa : null,
			'datos_boleta_antes' => $boletaInfo,
			'datos_reserva' => $reserva,
			'observaciones' => 'Datos cargados correctamente para validación'
		]);


		// $compra->programacion_fecha = '2025-04-10 23:30:00';



		$boletosModel = new Administracion_Model_DbTable_Boletasinfo();

		$boletosTodos = $boletosModel->getList("boleta_reserva_id = {$reserva->id} AND boleta_estado IN (1, 2)", "");

		$boletosSinValidar = [];
		$boletosValidados = [];
		
		$invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
		foreach ($boletosTodos as $boleto) {
		    $invitado = $invitadosModel->getById($boleto->boleta_asignacion);
			$boleto->invitado = $invitado;
			if ($boleto->boleta_estado == 1) {
				$boletosSinValidar[] = $boleto;
			} elseif ($boleto->boleta_estado == 2) {
				$boletosValidados[] = $boleto;
			}
		}

		$eventoModel = new Administracion_Model_DbTable_Eventos();
		$mesasModel = new Administracion_Model_DbTable_Mesas();

		$mesasIds = $reserva->reserva_mesa_id;
		$mesasIds = explode(',', $mesasIds);

		$mesasConsulta = [];
		foreach ($mesasIds as $mesaId) {
			$mesasConsulta[] = $mesasModel->getMesasConDetalles("mesa_id = '{$mesaId}'")[0];
		}

		// print_r($boletaInformacion);


		$mesaId = $boletaInformacion->boleta_mesa;
		$mesaInfo = $mesasModel->getMesasConDetalles("mesa_id = '{$mesaId}'")[0];
		$evento = $eventoModel->getById($boletaInfo->boleta_evento_id || 1);

		// Obtener información del ambiente para la vista interactiva
		$ambientesModel = new Administracion_Model_DbTable_Ambientes();
		$ambienteInfo = null;
		if ($mesaInfo && $mesaInfo->mesa_ambiente) {
			$ambienteInfo = $ambientesModel->getById($mesaInfo->mesa_ambiente);
		}


		$this->_view->mesasConsulta = $mesasConsulta;
		$this->_view->reserva = $reserva;
		$this->_view->mesaInfo = $mesaInfo;
		$this->_view->evento = $evento;
		$this->_view->boletaInformacion = $boletaInformacion;
		$this->_view->boletaInfo = $boletaInfo;
		$this->_view->boletosSinValidar = count($boletosSinValidar);
		$this->_view->boletosValidados = count($boletosValidados);
		$this->_view->boletosTodos = count($boletosTodos);
		$this->_view->boletosInvitados = ($boletosTodos);
		$this->_view->ambienteInfo = $ambienteInfo;
	}


	public function registrarAction()
	{
		// Establece un layout vacío
		//error_reporting(E_ALL);
		$this->setLayout('blanco');
		// Recibe los datos enviados en formato JSON
		$user = Session::getInstance()->get("user");
		// Sanitiza y obtiene los datos necesarios
		$uid = $this->_getSanitizedParam("uid");
		$token = $this->_getSanitizedParam("token");
		$csrf = $this->_getSanitizedParam("csrf");
		$metodoEscaneado = $this->_getSanitizedParam("metodo_escaneado") ?? 'camera';

		// Log intento de validación
		$this->logAuditoriaBoleta('INTENTO_VALIDACION', 'exitoso', [

			'boleta_uid' => $uid,
			'boleta_token' => $token,
			'metodo_escaneado' => $metodoEscaneado,
			'observaciones' => "Inicio proceso de validación de boleta - Método: {$metodoEscaneado}"
		]);

		if (Session::getInstance()->get('csrf')[$this->_getSanitizedParam("csrf_section")] !== $csrf) {
			// Log error de CSRF
			$this->logAuditoriaBoleta('ERROR_CSRF', 'error', [
				'boleta_uid' => $uid,
				'boleta_token' => $token,
				'metodo_escaneado' => $metodoEscaneado,
				'motivo_fallo' => 'Token CSRF inválido',
				'observaciones' => 'Posible intento de ataque CSRF'
			]);

			$response = [
				'status' => 'error',
				'message' => 'Token CSRF inválido.',
			];
			echo json_encode($response);
			return;
		}

		$this->_view->uid = $uid;
		$this->_view->token = $token;

		$boletosModel = new Administracion_Model_DbTable_Boletasinfo();
		$boletaInfo = $boletosModel->getList("boleta_uid = '$uid' AND boleta_token = '$token'")[0];

		if (!$boletaInfo) {
			// Log boleta no encontrada en validación
			$this->logAuditoriaBoleta('VALIDACION_BOLETA_NO_ENCONTRADA', 'fallido', [
				'boleta_uid' => $uid,
				'boleta_token' => $token,
				'metodo_escaneado' => $metodoEscaneado,
				'motivo_fallo' => 'Boleta no existe al momento de validar',
				'observaciones' => 'Boleta desapareció entre vista y validación'
			]);

			$response = [
				'status' => 'error',
				'message' => 'El boleto no existe.',
			];
			echo json_encode($response);
			return;
		}

		// Guardar estado antes de la validación
		$estadoAntes = clone $boletaInfo;

		if ($boletaInfo->boleta_estado != 1) {
			// Log intento de validar boleta ya validada
			$this->logAuditoriaBoleta('VALIDACION_DUPLICADA', 'duplicado', [
				'documento_escaneado' => $boletaInfo->boleta_documento,
				'boleta_uid' => $uid,
				'boleta_token' => $token,
				'metodo_escaneado' => $metodoEscaneado,
				'boleta_id' => $boletaInfo->boleta_id,
				'boleta_reserva_id' => $boletaInfo->boleta_reserva_id,
				'boleta_evento_id' => $boletaInfo->boleta_evento_id,
				'boleta_mesa' => $boletaInfo->boleta_mesa,
				'datos_boleta_antes' => $estadoAntes,
				'motivo_fallo' => 'Intento de validar boleta ya procesada',
				'observaciones' => 'Validación duplicada detectada'
			]);

			$response = [
				'status' => 'error',
				'message' => 'El boleto ya fue validado.',
				'redirect' => "/validacion/validar/error?uid={$uid}&token={$token}&metodo_escaneado={$metodoEscaneado}"
			];
			echo json_encode($response);
			return;
		}

		$id = $boletaInfo->boleta_id;

		// Realizar la validación
		$boletosModel->editField($id, "boleta_estado", 2);
		$boletosModel->editField($id, "boleta_fecha_validacion", date("Y-m-d H:i:s"));
		$boletosModel->editField($id, "boleta_metodo_validacion", "validacion manual");
		$boletosModel->editField($id, "boleta_dispositivo_validacion", $_SERVER['HTTP_USER_AGENT']);
		$boletosModel->editField($id, "boleta_ip_validacion", $_SERVER['REMOTE_ADDR']);
		$boletosModel->editField($id, "boleta_usuario_validador", $user->user_id);

		// Obtener estado después de la validación
		$boletaActualizada = $boletosModel->getById($id);

		$boletosTotal = $boletosModel->getList("boleta_reserva_id = {$boletaInfo->boleta_reserva_id}");
		$boletosValidadosPorCompra = $boletosModel->getList("boleta_reserva_id = {$boletaInfo->boleta_reserva_id} AND boleta_estado = 2");

		if (
			count($boletosTotal) >= 1 &&
			(count($boletosTotal) == count($boletosValidadosPorCompra))
		) {
			$reservaModel = new Administracion_Model_DbTable_Reservas();
			$reservaModel->editField($boletaInfo->boleta_reserva_id, "reserva_boletas_validadas", 1);

			// Log reserva completamente validada
			$this->logAuditoriaBoleta('RESERVA_COMPLETADA', 'exitoso', [
				'boleta_uid' => $uid,
				'boleta_reserva_id' => $boletaInfo->boleta_reserva_id,
				'metodo_escaneado' => $metodoEscaneado,
				'observaciones' => 'Todas las boletas de la reserva han sido validadas'
			]);
		}

		$boletoActualizado = $boletosModel->getById($id);
		if ($boletoActualizado->boleta_estado == 2) {
			// Log validación exitosa
			$this->logAuditoriaBoleta('VALIDACION_EXITOSA', 'exitoso', [
				'documento_escaneado' => $boletaInfo->boleta_documento,
				'boleta_uid' => $uid,
				'boleta_token' => $token,
				'metodo_escaneado' => $metodoEscaneado,
				'boleta_id' => $boletaInfo->boleta_id,
				'boleta_reserva_id' => $boletaInfo->boleta_reserva_id,
				'boleta_evento_id' => $boletaInfo->boleta_evento_id,
				'boleta_mesa' => $boletaInfo->boleta_mesa,
				'datos_boleta_antes' => $estadoAntes,
				'datos_boleta_despues' => $boletaActualizada,
				'observaciones' => "Boleta validada correctamente - Método: {$metodoEscaneado}",
				'boleta_numero_ticket' => $boletaActualizada->boleta_numero_ticket
			]);

			$response = [
				'status' => 'success',
				'message' => 'Boleto validado correctamente.',
				'redirect' => "/validacion/evento",
			];
			echo json_encode($response);
			return;
		} else {
			// Log error en validación
			$this->logAuditoriaBoleta('ERROR_VALIDACION', 'error', [
				'documento_escaneado' => $boletaInfo->boleta_documento,
				'boleta_uid' => $uid,
				'boleta_token' => $token,
				'metodo_escaneado' => $metodoEscaneado,
				'boleta_id' => $boletaInfo->boleta_id,
				'boleta_reserva_id' => $boletaInfo->boleta_reserva_id,
				'boleta_evento_id' => $boletaInfo->boleta_evento_id,
				'datos_boleta_antes' => $estadoAntes,
				'datos_boleta_despues' => $boletaActualizada,
				'motivo_fallo' => 'Error al actualizar estado de boleta',
				'observaciones' => 'Fallo en actualización de base de datos'
			]);

			$response = [
				'status' => 'error',
				'message' => 'Error al validar el boleto.',
			];
			echo json_encode($response);
			return;
		}

		/* if ($ticketInfo->ticket_estado == 1) {
																					$data['ticket_estado'] = 2;
																					$ticketsModel->update($data, "ticket_id = {$ticketInfo->ticket_id}");
																					header("Location: /validacion/validar/?uid={$uid}&token={$token}");
																					exit;
																				}
																				header("Location: /validacion/validar/error?uid={$uid}&token={$token}");
																				exit; */
	}
	public function errorAction()
	{
		$uid = $this->_getSanitizedParam("uid");
		$token = $this->_getSanitizedParam("token");
		$metodoEscaneado = $this->_getSanitizedParam("metodo_escaneado") ?? 'camera';

		$this->_view->uid = $uid;
		$this->_view->token = $token;
		$this->_view->metodo_escaneado = $metodoEscaneado;


		$boletaInfo = $this->infoBoleto($uid, $token);
		// print_r($boletaInfo);
		if (!$boletaInfo) {
			$this->_view->error = "El boleto no existe.";
			$this->_view->tipoError = 1;
		}
		if ($boletaInfo->boleta_estado != 1) {
			$this->_view->error = "El boleto ya fue validado.";
			$this->_view->tipoError = 2;
		}

		$reserva = $this->validarReserva($uid, $token);
		if (!$reserva) {
			$this->_view->error = "El boleto no se encuentra asociado a una reserva, contacte al administrador.";
			$this->_view->tipoError = 3;
		}

		$eventoModel = new Administracion_Model_DbTable_Eventos();
		$mesasModel = new Administracion_Model_DbTable_Mesas();
		$usuarioValidador = $boletaInfo->boleta_usuario_validador;
		if ($usuarioValidador) {
			$usuariosModel = new Administracion_Model_DbTable_Usuario();
			$usuario = $usuariosModel->getById($usuarioValidador);
			$this->_view->usuarioValidador = $usuario;
		}
		$mesaId = $boletaInfo->boleta_mesa;
		if ($mesaId) {
			$mesaInfo = $mesasModel->getMesasConDetalles("mesa_id = '$mesaId'")[0];
			$this->_view->mesaInfo = $mesaInfo;

		}
		$evento = $eventoModel->getById($boletaInfo->boleta_evento_id || 1);
		$invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
		$invitado = $invitadosModel->getList("documento_invitado = '{$boletaInfo->boleta_documento}' AND reserva_id_reserva = {$boletaInfo->boleta_reserva_id} AND id_invitado = {$boletaInfo->boleta_asignacion}")[0];
		$this->_view->invitado = $invitado;
		$this->_view->evento = $evento;
		$this->_view->reserva = $reserva;
		$this->_view->boletaInfo = $boletaInfo;

		$this->_view->estadoBoleto = $this->getEstadosBoletos()[$boletaInfo->boleta_estado];
	}
	public function infoBoleto($uid, $token)
	{
		$boletosModel = new Administracion_Model_DbTable_Boletasinfo();
		$boletaInfo = $boletosModel->getList("boleta_uid = '$uid' AND boleta_token = '$token'")[0];
		return $boletaInfo;
	}

	public function validarReserva($uid, $token)
	{
		$reservaModel = new Administracion_Model_DbTable_Reservas();
		$boletosModel = new Administracion_Model_DbTable_Boletasinfo();


		$boleto = $boletosModel->getList("boleta_uid = '$uid' AND boleta_token = '$token'")[0];


		$reserva = $reservaModel->getById($boleto->boleta_reserva_id);
		return $reserva;
	}

	public function validarToken($reserva, $boleto)
	{

		// Regenerar el token usando los mismos datos con los que fue creado
		$yearMonth = date("Ym", strtotime($reserva->programacion_fecha));
		$baseString = "{$reserva->boleta_compra_id}-{$reserva->boleta_compra_email}-{$yearMonth}-{$boleto->boleta_id}";
		$generatedToken = substr(base_convert(hash('sha256', $baseString), 16, 36), 0, 12);

		return $generatedToken;
	}



	public function getEstadosBoletos()
	{
		$data = [];
		$data[1] = 'Sin validar';
		$data[2] = 'Validado';
		return $data;
	}
}
