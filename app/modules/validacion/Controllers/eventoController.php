<?php

/**
 *
 */

class Validacion_eventoController extends Validacion_mainController
{
	protected $mainModel;
	protected $route;
	protected $_csrf_section = "evento";
	public $csrf;
	public function init()
	{
		if (!Session::getInstance()->get("user")) {
			header('Location: /validacion/');
			exit;
		}
		parent::init();
	}
	public function testAction()
	{
		// Implement your test logic here	
	}

	public function indexAction()
	{
		$user = Session::getInstance()->get("user");

		$this->_view->user = $user;


	}
	public function eventoAction()
	{
		$user = Session::getInstance()->get("user");
		$this->_view->user = $user;

	}



	public function consultardocumentoAction()
	{
		$this->setLayout('blanco');
		// error_reporting(E_ALL);
		header('Content-Type: application/json');
		$rawDocumento = $this->getRequest()->_getParam('documento', '');
		$check = $this->isSuspicious($rawDocumento);
		// error_log(sprintf("DOCUMENTO_RECEIVED: %s", print_r($check, true)));

		if ($check['suspicious']) {

			$attackId = $this->logAtaqueEnArchivo($rawDocumento, $check);

			// Log básico sin contenido peligroso en la base de datos
			$this->logAuditoriaBoleta('ENTRADA_SOSPECHOSA', 'fallido', [
				'documento_escaneado' => '[BLOQUEADO]',
				'metodo_escaneado' => $this->_getSanitizedParam('metodo_escaneado') ?: 'camera',
				'motivo_fallo' => 'Entrada inválida detectada',
				'observaciones' => sprintf('ATAQUE_ID: %s - Ver logs/security_attacks.txt', $attackId),
				'datos_adicionales' => json_encode([
					'attack_id' => $attackId,
					'patterns_count' => count($check['matches']),
					'severity' => count($check['matches']) > 2 ? 'HIGH' : 'MEDIUM'
				])
			]);



			$res = [
				'reload' => true,
				'error' => 'Entrada inválida detectada'
			];
			echo json_encode($res);
			return;
		}
		$documento = $this->_getSanitizedParam('documento');
		$metodoEscaneado = $this->_getSanitizedParam('metodo_escaneado') ?: 'camera';


		// Log intento de consulta
		$this->logAuditoriaBoleta('CONSULTA_DOCUMENTO', 'exitoso', [
			'documento_escaneado' => $documento,
			'metodo_escaneado' => $metodoEscaneado,
			'observaciones' => "Consulta de documento iniciada - Método: {$metodoEscaneado}"
		]);

		$boletasModel = new Administracion_Model_DbTable_Boletasinfo();
		$invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
		$reservasModel = new Administracion_Model_DbTable_Reservas();
		$boletaInfo = $boletasModel->getList("boleta_documento = '{$documento}' AND boleta_estado IN (1, 2)");

		if (!$boletaInfo) {
			// Log documento no encontrado
			$this->logAuditoriaBoleta('DOCUMENTO_NO_ENCONTRADO', 'fallido', [
				'documento_escaneado' => $documento,
				'metodo_escaneado' => $metodoEscaneado,
				'motivo_fallo' => 'Documento no existe en base de datos',
				'observaciones' => 'Búsqueda de documento sin resultados'
			]);


			$res = [
				'error' => 'Boleta no encontrada',
				'data' => [
					'documento' => $documento,
					'metodo_escaneado' => $metodoEscaneado
				]
			];
			echo json_encode($res);
			exit;
		}

		if (count($boletaInfo) > 1) {
			// Log documento duplicado
			$this->logAuditoriaBoleta('DOCUMENTO_DUPLICADO', 'error', [
				'documento_escaneado' => $documento,
				'metodo_escaneado' => $metodoEscaneado,
				'motivo_fallo' => 'Múltiples boletas con mismo documento',
				'observaciones' => 'Error de integridad - documentos duplicados',
				'datos_boleta_antes' => $boletaInfo
			]);

			$res = [
				'error' => 'Boleta duplicada, contactar a administración'
			];
			echo json_encode($res);
			exit;
		}

		$boletaInfo = $boletaInfo[0];

		$invitadoInfo = $invitadosModel->getById($boletaInfo->boleta_asignacion);
		if (!$invitadoInfo) {
			// Log invitado no encontrado
			$this->logAuditoriaBoleta('INVITADO_NO_ENCONTRADO', 'fallido', [
				'documento_escaneado' => $documento,
				'metodo_escaneado' => $metodoEscaneado,
				'boleta_id' => $boletaInfo->boleta_id,
				'boleta_reserva_id' => $boletaInfo->boleta_reserva_id,
				'datos_boleta_antes' => $boletaInfo,
				'motivo_fallo' => 'Invitado asociado no existe',
				'observaciones' => 'Error de integridad - invitado faltante'
			]);

			$res = [
				'error' => 'Invitado no encontrado, contactar a administración'
			];
			echo json_encode($res);
			exit;
		}
		//echo "Debug: Invitado encontrado - ID: {$invitadoInfo->id_invitado}, Documento: {$invitadoInfo->documento_invitado}\n, Documento: {$documento}\n"; // Línea de depuración

		if (trim($invitadoInfo->documento_invitado) != trim($documento)) {
			// Log discrepancia de documento
			$this->logAuditoriaBoleta('DOCUMENTO_NO_COINCIDE', 'fallido', [
				'documento_escaneado' => $documento,
				'metodo_escaneado' => $metodoEscaneado,
				'boleta_id' => $boletaInfo->boleta_id,
				'boleta_reserva_id' => $boletaInfo->boleta_reserva_id,
				'datos_boleta_antes' => $boletaInfo,
				'motivo_fallo' => "Documento escaneado: {$documento}, Documento invitado: {$invitadoInfo->documento_invitado}",
				'observaciones' => 'Discrepancia entre documentos'
			]);

			$res = [
				'error' => 'El documento no coincide con la boleta'
			];
			echo json_encode($res);
			exit;
		}

		$reservaInfo = $reservasModel->getById($invitadoInfo->reserva_id_reserva);

		if (!$reservaInfo || !in_array($reservaInfo->reserva_estado, [2, 3, 11])) {
			// Log reserva no válida
			$this->logAuditoriaBoleta('RESERVA_INVALIDA', 'fallido', [
				'documento_escaneado' => $documento,
				'metodo_escaneado' => $metodoEscaneado,
				'boleta_id' => $boletaInfo->boleta_id,
				'boleta_reserva_id' => $boletaInfo->boleta_reserva_id,
				'datos_boleta_antes' => $boletaInfo,
				'datos_reserva' => $reservaInfo,
				'motivo_fallo' => 'Reserva no existe o estado inválido',
				'observaciones' => $reservaInfo ? "Estado reserva: {$reservaInfo->reserva_estado}" : 'Reserva no encontrada'
			]);

			$res = [
				'error' => 'Reserva no válida, contactar a administración'
			];
			echo json_encode($res);
			exit;
		}

		// Log consulta exitosa
		$informacion = array_merge((array) $boletaInfo, (array) $invitadoInfo, (array) $reservaInfo);

		$this->logAuditoriaBoleta('CONSULTA_EXITOSA', 'exitoso', [
			'documento_escaneado' => $documento,
			'metodo_escaneado' => $metodoEscaneado,
			'boleta_id' => $boletaInfo->boleta_id,
			'boleta_uid' => $boletaInfo->boleta_uid,
			'boleta_token' => $boletaInfo->boleta_token,
			'boleta_reserva_id' => $boletaInfo->boleta_reserva_id,
			'boleta_evento_id' => $boletaInfo->boleta_evento_id,
			'boleta_mesa' => $boletaInfo->boleta_mesa,
			'datos_boleta_antes' => $boletaInfo,
			'datos_reserva' => $reservaInfo,
			'numero_carnet' => $boletaInfo->reserva_numero_carnet,
			'observaciones' => "Documento consultado exitosamente - Método: {$metodoEscaneado}"
		]);

		$res = [
			'success' => true,
			'boletaInfo' => $informacion
		];

		echo json_encode($res);
		exit;
	}

	/**
	 * Detecta el método de escaneado para consulta de documento
	 * Este método ahora es backup, se prefiere usar el parámetro enviado
	 * @return string Método detectado
	 */
	private function detectarMetodoEscaneadoDocumento()
	{
		// Priorizar el parámetro enviado desde el frontend
		$metodoEnviado = $this->_getSanitizedParam('metodo_escaneado');
		if ($metodoEnviado) {
			return $metodoEnviado;
		}

		// Fallback a detección automática
		$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		$referer = $_SERVER['HTTP_REFERER'] ?? '';

		// Si viene de modal manual
		if (strpos($referer, 'manual') !== false) {
			return 'manual';
		}

		// Por defecto cámara
		return 'camera';
	}
	function isSuspicious(string $input, array $opts = []): array
	{
		// Opciones por defecto
		$opts = array_merge([
			'max_length' => 50,   // Aumentar para permitir UIDs más largos
			'min_length_for_checks' => 1,
			'decode_url' => true,
			'decode_html_entities' => true,
			'reduce_whitespace' => true,
			'return_first_match' => false,
			'allow_url_params' => true, // Nueva opción
		], $opts);

		$original = $input;

		// Si parece ser un UID válido (B-YYYYMM-NNNNNNN), permitirlo
		if (preg_match('/^B-\d{6}-\d{7}$/', $input)) {
			return [
				'suspicious' => false,
				'matches' => [],
				'reason' => 'valid_uid_format',
				'normalized' => $input,
			];
		}

		// Si es una URL con parámetros válidos, extraer el UID
		if ($opts['allow_url_params'] && strpos($input, 'uid=') !== false) {
			parse_str(parse_url($input, PHP_URL_QUERY), $params);
			if (isset($params['uid']) && preg_match('/^B-\d{6}-\d{7}$/', $params['uid'])) {
				return [
					'suspicious' => false,
					'matches' => [],
					'reason' => 'valid_url_with_uid',
					'normalized' => $params['uid'],
				];
			}
		}

		// 1) Normalización básica
		if ($opts['decode_url']) {
			$decoded = @urldecode($input);
			if ($decoded !== false)
				$input = $decoded;
		}
		if ($opts['decode_html_entities']) {
			$input = html_entity_decode($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
		}
		if (function_exists('transliterator_transliterate')) {
			$x = @transliterator_transliterate('Any-Latin; Latin-ASCII', $input);
			if ($x !== null)
				$input = $x;
		}
		if ($opts['reduce_whitespace']) {
			$input = preg_replace('/\s+/u', ' ', $input);
		}
		$input = trim($input);
		$lower = mb_strtolower($input, 'UTF-8');

		$result = [
			'suspicious' => false,
			'matches' => [],
			'reason' => '',
			'normalized' => $input,
		];

		// 2) Reglas rápidas
		if ($input === '') {
			$result['reason'] = 'empty';
			return $result;
		}

		if (mb_strlen($original, 'UTF-8') > $opts['max_length']) {
			$result['suspicious'] = true;
			$result['matches'][] = 'too_long';
			$result['reason'] = 'input longer than allowed';
			return $result;
		}

		// 3) Lista de patrones SQL peligrosos
		$patterns = [
			'/\bunion\b/i',
			'/\binformation_schema\b/i',
			'/\bselect\b\s+.*\bfrom\b/i',
			'/\binsert\b\s+into\b/i',
			'/\bupdate\b\s+.*\bset\b/i',
			'/\bdelete\b\s+from\b/i',
			'/\bdrop\b\s+(table|database)\b/i',
			'/\balter\b\s+table\b/i',
			'/\bsleep\s*\(/i',
			'/\bbenchmark\s*\(/i',
			'/\bpg_sleep\s*\(/i',
			'/\bdbms_lock\.sleep\s*\(/i',
			'/\binto\s+outfile\b/i',
			'/\binto\s+dumpfile\b/i',
			'/\bload_file\s*\(/i',
			'/\boutfile\b/i',
			'/\bconcat\s*\(/i',
			'/\bchar\s*\(/i',
			'/\b0x[0-9a-f]{2,}\b/i',
			'/\bxp_cmdshell\b/i',
			'/\bexec\s+xp_cmdshell\b/i',
			'/\bexec\s+sp_executesql\b/i',
			'/--\s*$/',
			'/--\s+/i',
			'/#\s*/',
			'/\/\*/',
			'/;\s*/',
			'/or\s+1\s*=\s*1\b/i',
			'/\'\s*or\s*\'1\'\s*=\s*\'1\b/i',
			'/"\s*or\s*"1"\s*=\s*"1\b/i',
			'/database\(\)/i',
			'/version\(\)/i',
			'/current_user\(\)/i',
			'/schema_name\(/i',
		];

		foreach ($patterns as $pat) {
			if (preg_match($pat, $lower)) {
				$result['suspicious'] = true;
				$result['matches'][] = $pat;
				if ($opts['return_first_match']) {
					$result['reason'] = 'matched ' . $pat;
					return $result;
				}
			}
		}

		// 4) Heurísticas más específicas - solo para caracteres realmente peligrosos
		// Remover & ya que es normal en URLs
		$specials = preg_match_all('/[\'"`;]/', $lower, $m);
		if ($specials > 4) { // Umbral más alto
			$result['suspicious'] = true;
			$result['matches'][] = 'many_special_chars';
		}

		// múltiples palabras clave SQL juntas
		if (preg_match('/\bunion\b.*\bselect\b/i', $lower) || preg_match('/\bselect\b.*\bunion\b/i', $lower)) {
			$result['suspicious'] = true;
			$result['matches'][] = 'union_select_combo';
		}

		// 5) devolver resultado
		if ($result['suspicious']) {
			$result['reason'] = 'patterns: ' . implode(', ', array_slice($result['matches'], 0, 5));
		} else {
			$result['reason'] = 'clean';
		}

		return $result;
	}

	private function logAtaqueEnArchivo($inputPeligroso, $checkResult)
	{
		$attackId = $this->generarIdAtaque();

		// Crear directorio logs si no existe
		$logDir = $_SERVER['DOCUMENT_ROOT'] . '/../logs';
		if (!is_dir($logDir)) {
			mkdir($logDir, 0755, true);
		}

		$logFile = $logDir . '/security_attacks.txt';

		$logEntry = [
			'ATTACK_ID' => $attackId,
			'TIMESTAMP' => date('Y-m-d H:i:s'),
			'IP_ADDRESS' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
			'USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
			'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? 'direct',
			'REQUEST_METHOD' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
			'USER_ID' => Session::getInstance()->get("user")->user_id ?? 'anonymous',
			'INPUT_LENGTH' => mb_strlen($inputPeligroso),
			'INPUT_HASH' => hash('sha256', $inputPeligroso),
			'INPUT_CONTENT' => base64_encode($inputPeligroso), // Codificado para seguridad
			'PATTERNS_DETECTED' => implode(', ', $checkResult['matches']),
			'DETECTION_REASON' => $checkResult['reason'],
			'SEVERITY' => $this->calcularSeveridad($checkResult),
			'METODO_ESCANEADO' => $this->_getSanitizedParam('metodo_escaneado') ?: 'camera'
		];

		// Formatear entrada del log
		$logText = str_repeat('=', 80) . "\n";
		$logText .= "SECURITY ALERT - ATTACK DETECTED\n";
		$logText .= str_repeat('=', 80) . "\n";

		foreach ($logEntry as $key => $value) {
			$logText .= sprintf("%-20s: %s\n", $key, $value);
		}

		$logText .= str_repeat('-', 80) . "\n\n";

		// Escribir al archivo con bloqueo
		file_put_contents($logFile, $logText, FILE_APPEND | LOCK_EX);

		// También crear un archivo específico por día para búsquedas más rápidas
		$dailyLogFile = $logDir . '/attacks_' . date('Y-m-d') . '.txt';
		file_put_contents($dailyLogFile, $logText, FILE_APPEND | LOCK_EX);

		return $attackId;
	}

	/**
	 * Genera un ID único para el ataque detectado
	 * @return string ID único del ataque
	 */
	private function generarIdAtaque()
	{
		return 'ATK_' . date('YmdHis') . '_' . substr(uniqid(), -6) . '_' . mt_rand(100, 999);
	}

	/**
	 * Calcula el nivel de severidad del ataque detectado
	 * @param array $checkResult Resultado del análisis
	 * @return string Nivel de severidad
	 */
	private function calcularSeveridad($checkResult)
	{
		$patronesCriticos = [
			'/\bunion\b/i',
			'/\bselect\b\s+.*\bfrom\b/i',
			'/\bdrop\b\s+(table|database)\b/i',
			'/\bdelete\b\s+from\b/i',
			'/\bxp_cmdshell\b/i'
		];

		foreach ($checkResult['matches'] as $match) {
			foreach ($patronesCriticos as $critico) {
				if ($match === $critico) {
					return 'CRITICAL';
				}
			}
		}

		if (count($checkResult['matches']) > 3) {
			return 'HIGH';
		} elseif (count($checkResult['matches']) > 1) {
			return 'MEDIUM';
		}

		return 'LOW';
	}

	/**
	 * Busca ataques por ID en los logs
	 * @param string $attackId ID del ataque a buscar
	 * @return array|null Información del ataque encontrado
	 */
	public function buscarAtaquePorId($attackId)
	{
		$logFile = $_SERVER['DOCUMENT_ROOT'] . '/../logs/security_attacks.txt';

		if (!file_exists($logFile)) {
			return null;
		}

		$content = file_get_contents($logFile);
		$attacks = explode(str_repeat('=', 80), $content);

		foreach ($attacks as $attack) {
			if (strpos($attack, "ATTACK_ID        : $attackId") !== false) {
				$lines = explode("\n", trim($attack));
				$attackData = [];

				foreach ($lines as $line) {
					if (strpos($line, ':') !== false) {
						list($key, $value) = explode(':', $line, 2);
						$attackData[trim($key)] = trim($value);
					}
				}

				return $attackData;
			}
		}

		return null;
	}
}
