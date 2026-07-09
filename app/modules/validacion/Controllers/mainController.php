<?php

/**
 *
 */

class Validacion_mainController extends Controllers_Abstract
{

	public $template;

	public function init()
	{
		$this->setLayout('page_page');
		$this->template = new Page_Model_Template_Template($this->_view);
		$infopageModel = new Page_Model_DbTable_Informacion();
		$this->_view->infopage = $informacion = $infopageModel->getById(1);


		$this->getLayout()->setData("meta_description", "$informacion->info_pagina_descripcion");
		$this->getLayout()->setData("meta_keywords", "$informacion->info_pagina_tags");
		$this->getLayout()->setData("scripts", "$informacion->info_pagina_scripts");
		$this->getLayout()->setData("metricas", "$informacion->info_pagina_metricas");




		$header = $this->_view->getRoutPHP('modules/validacion/Views/partials/header.php');
		$this->getLayout()->setData("header", $header);
		$footer = $this->_view->getRoutPHP('modules/validacion/Views/partials/footer.php');
		$this->getLayout()->setData("footer", $footer);
		$flotantes = $this->_view->getRoutPHP('modules/validacion/Views/partials/flotantes.php');
		$this->getLayout()->setData("flotantes", $flotantes);
		$this->usuario();
	}


	public function usuario()
	{
		$userModel = new Core_Model_DbTable_User();
		$user = $userModel->getById(Session::getInstance()->get("kt_login_id"));
		if ($user->user_id == 1) {
			// $this->editarpage = 1;
		}
	}
	private function sanitizarDatosPostSeguro($postData)
	{
		try {
			// Si está vacío, devolver string vacío
			if (empty($postData)) {
				return '{}';
			}

			// Crear versión completamente segura
			$safe = [];
			foreach ($postData as $key => $value) {
				$safeKey = preg_replace('/[^\w_]/', '_', $key);

				if (is_string($value)) {
					// Si es muy largo o contiene patrones sospechosos, resumir
					if (mb_strlen($value) > 100 || $this->esContenidoSospechoso($value)) {
						$safe[$safeKey] = sprintf(
							'[VALOR_SOSPECHOSO] Longitud: %d, Hash: %s',
							mb_strlen($value),
							substr(hash('sha256', $value), 0, 12)
						);
					} else {
						$safe[$safeKey] = addslashes(mb_substr($value, 0, 100));
					}
				} else {
					$safe[$safeKey] = '[TIPO_' . gettype($value) . ']';
				}
			}

			return json_encode($safe, JSON_UNESCAPED_UNICODE);

		} catch (Exception $e) {
			return '{"error": "Error al procesar POST"}';
		}
	}
	/**
	 * Función principal para registrar eventos de auditoría en el sistema de validación
	 * @param string $accion Acción realizada (ej: 'LECTURA_QR', 'VALIDACION_EXITOSA', 'ERROR_VALIDACION')
	 * @param string $resultado Resultado de la acción ('exitoso', 'fallido', 'error', 'duplicado')
	 * @param array $datosAdicionales Array con datos adicionales específicos del evento
	 * @return bool True si se registró correctamente, false en caso de error
	 */
	public function logAuditoriaBoleta($accion, $resultado, $datosAdicionales = [])
	{
		try {
			$auditoriaModel = new Administracion_Model_DbTable_Auditoriaboleta();

			// Obtener datos del usuario actual
			$user = Session::getInstance()->get("user");

			// Detectar método de escaneado
			$metodoEscaneado = $this->detectarMetodoEscaneado($datosAdicionales);

			// Gestionar session_id para trazabilidad
			$sessionId = $this->gestionarSessionId($accion, $datosAdicionales);
			// Sanitizar POST de forma segura antes de usarlo
			$postSanitizado = $this->sanitizarDatosPostSeguro($_POST);

			// Datos base del log de auditoría
			$logData = [
				// ID de sesión para trazabilidad
				'auditoriaboleta_session_id' => $sessionId,

				'auditoriaboleta_boleta_uid' => $datosAdicionales['boleta_uid'] ?? null,
				'auditoriaboleta_boleta_token' => $datosAdicionales['boleta_token'] ?? null,
				'auditoriaboleta_documento_escaneado' => $datosAdicionales['documento_escaneado'] ?? null,

				// Información de la boleta si está disponible
				'auditoriaboleta_boleta_id' => $datosAdicionales['boleta_id'] ?? null,
				'auditoriaboleta_boleta_reserva_id' => $datosAdicionales['boleta_reserva_id'] ?? null,
				'auditoriaboleta_boleta_evento_id' => $datosAdicionales['boleta_evento_id'] ?? null,
				'auditoriaboleta_boleta_mesa' => $datosAdicionales['boleta_mesa'] ?? null,
				'auditoriaboleta_boleta_numero_ticket' => $datosAdicionales['boleta_numero_ticket'] ?? null,

				// Usuario y sesión
				'auditoriaboleta_usuario_validador_id' => $user ? $user->user_id : null,
				'auditoriaboleta_usuario_validador_nombre' => $user ? $user->user_user : 'N/A',
				'auditoriaboleta_numero_carnet' => $datosAdicionales['numero_carnet'] ?? null,

				// Acción y resultado
				'auditoriaboleta_accion' => $accion,
				'auditoriaboleta_resultado' => $resultado,
				'auditoriaboleta_motivo_fallo' => $datosAdicionales['motivo_fallo'] ?? null,

				// Información técnica
				'auditoriaboleta_metodo_escaneado' => $metodoEscaneado,
				'auditoriaboleta_ip_address' => $this->obtenerIpCliente(),
				'auditoriaboleta_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
				'auditoriaboleta_dispositivo_info' => json_encode([
					'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
					'accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? null,
					'server_name' => $_SERVER['SERVER_NAME'] ?? null
				]),

				// Ubicación y contexto
				'auditoriaboleta_url_completa' => $_SERVER['REQUEST_URI'] ?? null,
				'auditoriaboleta_referer' => $_SERVER['HTTP_REFERER'] ?? null,
				'auditoriaboleta_parametros_get' => json_encode($_GET),
				'auditoriaboleta_parametros_post' => $postSanitizado,

				// Timestamps
				'auditoriaboleta_fecha_hora' => date('Y-m-d H:i:s'),
				'auditoriaboleta_timestamp_unix' => time(),

				// Datos adicionales en JSON
				'auditoriaboleta_datos_boleta_antes' => ($datosAdicionales['datos_boleta_antes']) ? json_encode($datosAdicionales['datos_boleta_antes']) : null,
				'auditoriaboleta_datos_boleta_despues' => ($datosAdicionales['datos_boleta_despues']) ? json_encode($datosAdicionales['datos_boleta_despues']) : null,
				'auditoriaboleta_datos_reserva' => ($datosAdicionales['datos_reserva']) ? json_encode($datosAdicionales['datos_reserva']) : null,
				'auditoriaboleta_datos_sesion' => ($datosAdicionales['datos_sesion']) ? json_encode($datosAdicionales['datos_sesion']) : null,

				// Observaciones
				'auditoriaboleta_observaciones' => $datosAdicionales['observaciones'] ?? null
			];

			// Insertar en la base de datos
			return $auditoriaModel->insert($logData);

		} catch (Exception $e) {
			// Log del error pero no interrumpir el flujo
			try {
				$logDataMinimo = [
					'auditoriaboleta_session_id' => $sessionId ?? $this->generarSessionId(),
					'auditoriaboleta_accion' => $accion,
					'auditoriaboleta_resultado' => $resultado,
					'auditoriaboleta_motivo_fallo' => 'Error en logAuditoriaBoleta: ' . $this->sanitizarMensajeError($e->getMessage()),
					'auditoriaboleta_fecha_hora' => date('Y-m-d H:i:s'),
					'auditoriaboleta_parametros_post' => '[ERROR_AL_SANITIZAR]',
					'auditoriaboleta_observaciones' => 'Log de emergencia por error en sanitización',
					'auditoriaboleta_metodo_escaneado' => $metodoEscaneado,
					// Timestamps
					'auditoriaboleta_timestamp_unix' => time(),

				];
				return $auditoriaModel->insert($logDataMinimo);
			} catch (Exception $e2) {
				error_log("Error crítico en log de auditoría: " . $e2->getMessage());
				return false;
			}
		}
	}
	private function sanitizarMensajeError($errorMessage)
	{
		// Truncar mensaje muy largo
		if (mb_strlen($errorMessage) > 200) {
			$errorMessage = mb_substr($errorMessage, 0, 200) . '...';
		}

		// Reemplazar caracteres problemáticos
		$errorMessage = str_replace(['"', "'", "\\", "\n", "\r", "\t"], ['&quot;', '&#39;', '&#92;', ' ', ' ', ' '], $errorMessage);

		// Si sigue siendo problemático, usar hash
		if ($this->esContenidoSospechoso($errorMessage)) {
			return 'Error SQL detectado - Hash: ' . substr(hash('sha256', $errorMessage), 0, 16);
		}

		return $errorMessage;
	}
	/**
	 * Detecta el método de escaneado basado en los datos disponibles
	 * @param array $datosAdicionales Datos adicionales del evento
	 * @return string Método detectado ('camera', 'usb_scanner', 'manual')
	 */
	private function detectarMetodoEscaneado($datosAdicionales = [])
	{
		// Si se especifica explícitamente en los datos adicionales
		if (($datosAdicionales['metodo_escaneado'])) {
			return $datosAdicionales['metodo_escaneado'];
		}

		// Si viene como parámetro POST
		if (($_POST['metodo_escaneado']) && !empty($_POST['metodo_escaneado'])) {
			return $_POST['metodo_escaneado'];
		}

		// Detección automática (fallback)
		$referer = $_SERVER['HTTP_REFERER'] ?? '';
		$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

		// Si viene de modal manual o se indica manual
		if (strpos($referer, 'manual') !== false || ($_POST['manual_entry'])) {
			return 'manual';
		}

		// Si viene con datos JSON (típico de cámara)
		if (($_POST['boletaInfo']) && !empty($_POST['boletaInfo'])) {
			return 'camera';
		}

		// Si es muy rápido (menos de 2 segundos desde carga), probablemente USB
		$sessionStartTime = Session::getInstance()->get('page_load_time');
		if ($sessionStartTime && (time() - $sessionStartTime) < 2) {
			return 'usb_scanner';
		}

		return 'camera'; // Por defecto
	}

	/**
	 * Obtiene la IP del cliente de forma segura
	 * @return string IP del cliente
	 */
	private function obtenerIpCliente()
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
		}
	}

	/**
	 * Sanitiza los datos POST para no guardar información sensible
	 * @param array $postData Datos POST a sanitizar
	 * @return string JSON con datos sanitizados
	 */
	private function sanitizarDatosPost($postData)
	{
		$sanitized = [];

		// Remover campos sensibles
		$sensitiveFields = ['password', 'csrf', 'token'];

		foreach ($postData as $key => $value) {
			// Si es un campo sensible, redactar
			if (in_array($key, $sensitiveFields)) {
				$sanitized[$key] = '[REDACTADO]';
				continue;
			}

			// Si el valor es sospechoso, manejarlo de forma segura
			if (is_string($value) && $this->esContenidoSospechoso($value)) {
				$sanitized[$key] = $this->sanitizarContenidoPeligroso($value);
			} else {
				// Para contenido normal, sanitizar caracteres especiales
				$sanitized[$key] = is_string($value) ? addslashes($value) : $value;
			}
		}

		return json_encode($sanitized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
	}

	/**
	 * Verifica si el contenido es sospechoso usando los mismos criterios
	 * @param string $content Contenido a verificar
	 * @return bool True si es sospechoso
	 */
	private function esContenidoSospechoso($content)
	{
		// Usar la misma lógica que isSuspicious pero solo el check básico
		$patterns = [
			'/\bunion\b.*\bselect\b/i',
			'/\bselect\b.*\bfrom\b/i',
			'/\binsert\b.*\binto\b/i',
			'/\bdelete\b.*\bfrom\b/i',
			'/\bdrop\b.*\btable\b/i',
			'/\balter\b.*\btable\b/i',
			'/\bexec\b.*\(/i',
			'/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/i',
			'/javascript:/i'
		];

		foreach ($patterns as $pattern) {
			if (preg_match($pattern, $content)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Sanitiza contenido peligroso para almacenamiento seguro
	 * @param string $content Contenido peligroso
	 * @return string Contenido sanitizado para log
	 */
	private function sanitizarContenidoPeligroso($content)
	{
		return sprintf(
			'[CONTENIDO_PELIGROSO] Longitud: %d, Hash: %s, Preview: %s',
			mb_strlen($content),
			substr(hash('sha256', $content), 0, 16),
			addslashes(mb_substr($content, 0, 20)) . '...'
		);
	}

	/**
	 * Gestiona el session_id para la trazabilidad de operaciones
	 * @param string $accion Acción que se está realizando
	 * @param array $datosAdicionales Datos adicionales del evento
	 * @return string Session ID único para esta operación
	 */
	private function gestionarSessionId($accion, $datosAdicionales = [])
	{
		// Acciones que inician una nueva sesión (lectura de documento)
		$accionesInicioSesion = [
			'CONSULTA_DOCUMENTO',
			'LECTURA_QR',
			'LECTURA_MANUAL',        // Agregar acciones de seguridad que deben iniciar nueva sesión
			'ENTRADA_SOSPECHOSA',
			// 'DOCUMENTO_NO_ENCONTRADO',
			// 'DOCUMENTO_DUPLICADO',
			// 'INVITADO_NO_ENCONTRADO',
			// 'DOCUMENTO_NO_COINCIDE',
			// 'RESERVA_INVALIDA'
		];

		// Si es una acción de inicio de sesión, generar nuevo session_id
		if (in_array($accion, $accionesInicioSesion)) {
			$sessionId = $this->generarSessionId();
			Session::getInstance()->set('audit_session_id', $sessionId);
			return $sessionId;
		}

		// Para otras acciones, usar el session_id existente o generar uno nuevo si no existe
		$sessionId = Session::getInstance()->get('audit_session_id');
		if (!$sessionId) {
			$sessionId = $this->generarSessionId();
			Session::getInstance()->set('audit_session_id', $sessionId);
		}

		return $sessionId;
	}

	/**
	 * Genera un session_id único
	 * @return string Session ID único basado en timestamp, usuario y valores aleatorios
	 */
	private function generarSessionId()
	{
		$user = Session::getInstance()->get("user");
		$userId = $user ? $user->user_id : 'anon';

		// Formato: {usuario}_{timestamp}_{hash}
		// Ejemplo: "5_20240911123045_a1b2c3d4"
		return $userId . '_' . date('YmdHis') . '_' . substr(md5(uniqid(mt_rand(), true)), 0, 8);
	}

	/**
	 * Reinicia el session_id (útil para limpiar sesión al cambiar de documento)
	 * @return string Nuevo session ID generado
	 */
	public function reiniciarSessionId()
	{
		$sessionId = $this->generarSessionId();
		Session::getInstance()->set('audit_session_id', $sessionId);
		return $sessionId;
	}

	/**
	 * Obtiene el session_id actual
	 * @return string|null Session ID actual o null si no existe
	 */
	public function getSessionIdActual()
	{
		return Session::getInstance()->get('audit_session_id');
	}
}
