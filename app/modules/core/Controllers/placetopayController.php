<?php

/**
 *
 */

class Core_placetopayController extends Controllers_Abstract
{

  private function logStep(&$logs, $tipo, $mensaje)
  {
    $logs[] = ['log_tipo' => $tipo, 'log_log' => $mensaje];
  }

  /**
   * Aplica a la reserva el resultado de una respuesta EXITOSA de PlaceToPay
   * (aprobado/pendiente/fallido/rechazado), actualizando estado, liberando
   * mesas e inactivando beneficiarios/mesas bloqueadas cuando corresponda.
   * Usado por responseAction (redirect del navegador) y notificacionAction
   * (webhook servidor a servidor) para no duplicar la lógica de negocio.
   * @return string Estado resultante: APROBADO|PENDIENTE|FALLIDO|RECHAZADO
   */
  private function procesarEstadoPago($id, $reserva, $response, &$logs, $prefix)
  {
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $mesasModel = new Administracion_Model_DbTable_Mesas();

    $estadoAnterior = $reserva->reserva_estado;
    $mesaReserva = $reserva->reserva_mesa_id ?? null;

    $payment = $response->payment()[0] ?? null;
    $this->logStep($logs, "$prefix - PAYMENT INFO", "Información del pago: " . print_r($payment, true));

    $authorization = $payment ? ($payment->authorization() ?? '') : '';
    $franquicia = '';

    if ($payment) {
      $paymentMethod = $payment->paymentMethodName() ?? '';
      $issuer = $payment->issuerName() ?? '';
      $franquicia = trim($paymentMethod . ' ' . $issuer);
      $this->logStep($logs, "$prefix - METODO PAGO", "Método de pago: $paymentMethod, Emisor: $issuer, Franquicia: $franquicia");
    }

    $this->logStep($logs, "$prefix - RESPUESTA", "Respuesta PlaceToPay - Estado: " . $response->status()->status() .
      ", Autorización: $authorization, Franquicia: $franquicia");

    // Procesar según el estado
    if ($response->status()->isApproved()) {
      // PAGO APROBADO - Estado 3
      if ($estadoAnterior != '3') {
        $this->logStep($logs, "$prefix - PRE UPDATE APROBADO", "Preparando actualización a APROBADO. ID: $id, Auth: $authorization, Franquicia: $franquicia");

        $reservasModel->updatePagoAprobado($id, $authorization, $franquicia);
        //$this->notificaRegistro($id);

        $this->logStep($logs, "$prefix - PAGO APROBADO", "Pago APROBADO - Estado cambiado de $estadoAnterior a 3. Auth: $authorization");
        $documento = $reserva->reserva_documento;

        $this->logStep($logs, "$prefix - DOCUMENTO", "Documento de la reserva: " . print_r($documento, true));
        if ($documento) {
          // Inactivar beneficiarios bloqueados
          //$this->inactivarBeneficiariosBloqueados($documento, $logs);
        }
      } else {
        $this->logStep($logs, "$prefix - SIN CAMBIO", "Pago ya estaba APROBADO - No se actualiza (estado: $estadoAnterior)");
      }
      return 'APROBADO';
    } elseif ($response->status()->status() == 'PENDING' || ($payment && $payment->status()->status() == 'PENDING')) {
      // PAGO PENDIENTE - Estado 4
      if ($estadoAnterior != '4') {
        $this->logStep($logs, "$prefix - PRE UPDATE PENDIENTE", "Preparando actualización a PENDIENTE. ID: $id, Auth: $authorization, Franquicia: $franquicia");

        $reservasModel->updatePagoPendiente($id, $authorization, $franquicia);
        $this->logStep($logs, "$prefix - PAGO PENDIENTE", "Pago PENDIENTE - Estado cambiado de $estadoAnterior a 4. Auth: $authorization");
      } else {
        $this->logStep($logs, "$prefix - SIN CAMBIO", "Pago ya estaba PENDIENTE - No se actualiza (estado: $estadoAnterior)");
      }
      return 'PENDIENTE';
    } elseif ($payment && $payment->status()->status() == 'FAILED') {
      // PAGO FALLIDO - Estado 5
      if ($estadoAnterior != '5') {
        $this->logStep($logs, "$prefix - PRE UPDATE FALLIDO", "Preparando actualización a FALLIDO. ID: $id, Auth: $authorization, Franquicia: $franquicia, Mesa: " . print_r($mesaReserva, true));

        $reservasModel->updatePagoFallido($id, $authorization, $franquicia);

        if ($mesaReserva) {
          $mesaIds = explode(',', $mesaReserva); // Convertir en array

          $this->logStep($logs, "$prefix - LIBERAR MESAS", "Mesas a liberar: " . print_r($mesaIds, true));

          foreach ($mesaIds as $idMesa) {
            $idMesa = trim($idMesa); // Eliminar espacios por si acaso
            if ($idMesa) {
              $mesasModel->editField($idMesa, 'mesa_estado', 0);
              $this->logStep($logs, "$prefix - MESA LIBERADA", "Mesa liberada ID: $idMesa");
            }
          }
        }
        $reservasModel->editField($id, 'reserva_mesa_id', 0);
        $this->logStep($logs, "$prefix - PAGO FALLIDO", "Pago FALLIDO - Estado cambiado de $estadoAnterior a 5. Auth: $authorization");
        $documento = $reserva->reserva_documento;

        $this->logStep($logs, "$prefix - DOCUMENTO FALLIDO", "Documento para inactivar beneficiarios: " . print_r($documento, true));
        if ($documento) {
          // Inactivar beneficiarios bloqueados
          $this->inactivarBeneficiariosBloqueados($documento, $logs);
          $this->inactivarMesasBloqueadas($documento, $logs);
        }
      } else {
        $this->logStep($logs, "$prefix - SIN CAMBIO", "Pago ya estaba FALLIDO - No se actualiza (estado: $estadoAnterior)");
      }
      return 'FALLIDO';
    } else {
      // PAGO RECHAZADO - Estado 6
      if ($estadoAnterior != '6') {
        $this->logStep($logs, "$prefix - PRE UPDATE RECHAZADO", "Preparando actualización a RECHAZADO. ID: $id, Auth: $authorization, Franquicia: $franquicia, Mesa: " . print_r($mesaReserva, true));

        $reservasModel->updatePagoRechazado($id, $authorization, $franquicia);
        if ($mesaReserva) {
          $mesaIds = explode(',', $mesaReserva); // Convertir en array

          $this->logStep($logs, "$prefix - LIBERAR MESAS RECHAZO", "Mesas a liberar (rechazo): " . print_r($mesaIds, true));

          foreach ($mesaIds as $idMesa) {
            $idMesa = trim($idMesa); // Eliminar espacios por si acaso
            if ($idMesa) {
              $mesasModel->editField($idMesa, 'mesa_estado', 0);
              $this->logStep($logs, "$prefix - MESA LIBERADA RECHAZO", "Mesa liberada (rechazo) ID: $idMesa");
            }
          }
        }
        $reservasModel->editField($id, 'reserva_mesa_id', 0);
        $this->logStep($logs, "$prefix - PAGO RECHAZADO", "Pago RECHAZADO - Estado cambiado de $estadoAnterior a 6. Auth: $authorization");
        $documento = $reserva->reserva_documento;

        $this->logStep($logs, "$prefix - DOCUMENTO RECHAZADO", "Documento para inactivar beneficiarios (rechazo): " . print_r($documento, true));
        if ($documento) {
          // Inactivar beneficiarios bloqueados
          $this->inactivarBeneficiariosBloqueados($documento, $logs);
          $this->inactivarMesasBloqueadas($documento, $logs);
        }
      } else {
        $this->logStep($logs, "$prefix - SIN CAMBIO", "Pago ya estaba RECHAZADO - No se actualiza (estado: $estadoAnterior)");
      }
      return 'RECHAZADO';
    }
  }

  public function responseAction()
  {
    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $logModel = new Administracion_Model_DbTable_Log();
    $placetopay = Payment_Placetopay::getInstance()->getPlacetopay();
    $reference = $this->_getSanitizedParam("reference");
    $id = substr($reference, strrpos($reference, '_') + 1);

    $logs = [];
    $this->logStep($logs, 'PLACETOPAY RESPONSE - INICIO', "Iniciando responseAction para reserva ID: $id");

    if (!$id) {
      $this->logStep($logs, 'PLACETOPAY RESPONSE - ERROR', "Error: ID de reserva no proporcionado");
      $logModel->insertBatch($logs);
      header("Location: /page/respuesta?error=invalid_reference");
      return;
    }

    $reserva = $reservasModel->getById($id);
    $this->logStep($logs, 'PLACETOPAY RESPONSE - RESERVA OBTENIDA', "Reserva obtenida: " . print_r($reserva, true));

    if (!$reserva || !$reserva->request_id) {
      $this->logStep($logs, 'PLACETOPAY RESPONSE - ERROR', "Error: Reserva no encontrada o sin request_id. Reserva: " . print_r($reserva, true));
      $logModel->insertBatch($logs);
      header("Location: /page/respuesta?error=reservation_not_found");
      return;
    }

    $this->logStep($logs, 'PLACETOPAY RESPONSE - ESTADO ANTERIOR', "Estado anterior de la reserva: {$reserva->reserva_estado}, Request ID: " . $reserva->request_id);

    $response = $placetopay->query($reserva->request_id);
    $this->logStep($logs, 'PLACETOPAY RESPONSE - RESPUESTA COMPLETA', "Respuesta completa de PlaceToPay: " . print_r($response, true));

    if ($response && $response->isSuccessful()) {
      $this->procesarEstadoPago($id, $reserva, $response, $logs, 'PLACETOPAY RESPONSE');
      header("Location: /page/respuesta?id=" . enc_id($id));
    } else {
      // Error de conexión con PlacetoPay
      $this->logStep($logs, 'PLACETOPAY RESPONSE - ERROR CONEXION', "Error de conexión con PlaceToPay. Response: " . print_r($response, true));
      header("Location: /page/respuesta?error=connection_failed");
    }

    $logModel->insertBatch($logs);
  }


  public function sondaAction()
  {
    $this->setLayout('blanco');

    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $logModel = new Administracion_Model_DbTable_Log();
    $mesasModel = new Administracion_Model_DbTable_Mesas();
    $placetopay = Payment_Placetopay::getInstance()->getPlacetopay();

    $logs = [];
    $this->logStep($logs, 'PLACETOPAY SONDA - INICIO', "Iniciando sondaAction - Revisión automática de pagos pendientes");

    // Obtener solo las reservas en estado PENDIENTE (4) con request_id
    $reservasPendientes = $reservasModel->getList(
      "(reserva_estado = '4' OR reserva_estado = '7') AND (request_id IS NOT NULL AND request_id != '')",
      "id ASC"
    );

    $totalReservas = count($reservasPendientes);
    $procesadas = 0;
    $actualizadas = 0;
    $errores = 0;

    $this->logStep($logs, 'PLACETOPAY SONDA - TOTAL ENCONTRADAS', "Se encontraron $totalReservas reservas pendientes para revisar");
    $logModel->insertBatch($logs);
    $logs = [];

    echo "<h2>Revisión Automática de Pagos Pendientes</h2>";
    echo "<p>Reservas encontradas: <strong>$totalReservas</strong></p>";
    echo "<hr>";

    foreach ($reservasPendientes as $reserva) {
      $id = $reserva->id;
      $estadoAnterior = $reserva->reserva_estado;
      $documento = $reserva->reserva_documento;
      $mesaReserva = $reserva->reserva_mesa_id ?? null;

      $this->logStep($logs, 'PLACETOPAY SONDA - DATOS RESERVA', "Datos completos de reserva: " . print_r($reserva, true));
      $this->logStep($logs, 'PLACETOPAY SONDA - CONSULTANDO', "Consultando estado de reserva ID: $id, Request ID: " . $reserva->request_id);

      echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
      echo "<strong>Reserva ID: $id</strong> - Estado anterior: $estadoAnterior<br>";

      try {
        $response = $placetopay->query($reserva->request_id);
        $this->logStep($logs, 'PLACETOPAY SONDA - RESPUESTA COMPLETA', "Respuesta PlaceToPay completa: " . print_r($response, true));

        if ($response && $response->isSuccessful()) {
          $procesadas++;

          // Obtener información del pago
          $payment = $response->payment()[0] ?? null;
          $this->logStep($logs, 'PLACETOPAY SONDA - PAYMENT INFO', "Información del pago (sonda): " . print_r($payment, true));

          $authorization = $payment ? ($payment->authorization() ?? '') : '';
          $franquicia = '';

          if ($payment) {
            $paymentMethod = $payment->paymentMethodName() ?? '';
            $issuer = $payment->issuerName() ?? '';
            $franquicia = trim($paymentMethod . ' ' . $issuer);
          }

          $estadoPlacetopay = $response->status()->status();
          $this->logStep($logs, 'PLACETOPAY SONDA - RESPUESTA', "Respuesta PlaceToPay para reserva $id - Estado: $estadoPlacetopay, Auth: $authorization, Franquicia: $franquicia");

          echo "Estado PlaceToPay: <strong>$estadoPlacetopay</strong><br>";
          echo "Autorización: $authorization<br>";
          echo "Franquicia: $franquicia<br>";

          // Procesar según el estado (CON VALIDACIONES)
          if ($response->status()->isApproved()) {
            // PAGO APROBADO - Estado 3
            if ($estadoAnterior != '3') {
              $this->logStep($logs, 'PLACETOPAY SONDA - PRE UPDATE APROBADO', "SONDA: Preparando actualización a APROBADO. Datos: ID=$id, Auth=$authorization, Franquicia=$franquicia");

              $reservasModel->updatePagoAprobado($id, $authorization, $franquicia);
              //$this->notificaRegistro($id);
              $actualizadas++;

              $this->logStep($logs, 'PLACETOPAY SONDA - PAGO APROBADO', "SONDA: Pago APROBADO - Estado cambiado de $estadoAnterior a 3. Auth: $authorization");

              echo "<span style='color: green;'>ACTUALIZADO A APROBADO</span><br>";
              //$this->enviar($id);
            } else {
              echo "<span style='color: blue;'>Ya estaba aprobado</span><br>";
            }
          } elseif ($response->status()->status() == 'PENDING' || ($payment && $payment->status()->status() == 'PENDING')) {
            // PAGO PENDIENTE - Mantener estado 4
            echo "<span style='color: orange;'>Sigue pendiente</span><br>";

            $this->logStep($logs, 'PLACETOPAY SONDA - SIGUE PENDIENTE', "SONDA: Pago sigue PENDIENTE - No se actualiza (estado: $estadoAnterior)");
          } elseif ($payment && $payment->status()->status() == 'FAILED') {
            // PAGO FALLIDO - Estado 5
            if ($estadoAnterior != '5') {
              $this->logStep($logs, 'PLACETOPAY SONDA - PRE UPDATE FALLIDO', "SONDA: Preparando actualización a FALLIDO. Datos: ID=$id, Auth=$authorization, Mesa=" . print_r($mesaReserva, true));

              $reservasModel->updatePagoFallido($id, $authorization, $franquicia);
              $actualizadas++;

              if ($mesaReserva) {
                $mesaIds = explode(',', $mesaReserva); // Convertir en array

                $this->logStep($logs, 'PLACETOPAY SONDA - LIBERAR MESAS FALLIDO', "SONDA: Mesas a liberar (fallido): " . print_r($mesaIds, true));

                foreach ($mesaIds as $idMesa) {
                  $idMesa = trim($idMesa); // Eliminar espacios por si acaso
                  if ($idMesa) {
                    $mesasModel->editField($idMesa, 'mesa_estado', 0);
                    $this->logStep($logs, 'PLACETOPAY SONDA - MESA LIBERADA FALLIDO', "SONDA: Mesa liberada (fallido) ID: $idMesa");
                  }
                }
              }
              $reservasModel->editField($id, 'reserva_mesa_id', 0);
              $this->logStep($logs, 'PLACETOPAY SONDA - PAGO FALLIDO', "SONDA: Pago FALLIDO - Estado cambiado de $estadoAnterior a 5. Auth: $authorization");

              echo "<span style='color: red;'>ACTUALIZADO A FALLIDO</span><br>";

              $this->logStep($logs, 'PLACETOPAY SONDA - DOCUMENTO FALLIDO', "SONDA: Documento para inactivar (fallido): " . print_r($documento, true));

              $this->inactivarBeneficiariosBloqueados($documento, $logs);
              $this->inactivarMesasBloqueadas($documento, $logs);
            } else {
              echo "<span style='color: blue;'>Ya estaba fallido</span><br>";
            }
          } else {
            // PAGO RECHAZADO - Estado 6
            if ($estadoAnterior != '6') {
              $this->logStep($logs, 'PLACETOPAY SONDA - PRE UPDATE RECHAZADO', "SONDA: Preparando actualización a RECHAZADO. Datos: ID=$id, Auth=$authorization, Mesa=" . print_r($mesaReserva, true));

              $reservasModel->updatePagoRechazado($id, $authorization, $franquicia);
              $actualizadas++;
              if ($mesaReserva) {
                $mesaIds = explode(',', $mesaReserva); // Convertir en array

                $this->logStep($logs, 'PLACETOPAY SONDA - LIBERAR MESAS RECHAZO', "SONDA: Mesas a liberar (rechazo): " . print_r($mesaIds, true));

                foreach ($mesaIds as $idMesa) {
                  $idMesa = trim($idMesa); // Eliminar espacios por si acaso
                  if ($idMesa) {
                    $mesasModel->editField($idMesa, 'mesa_estado', 0);
                    $this->logStep($logs, 'PLACETOPAY SONDA - MESA LIBERADA RECHAZO', "SONDA: Mesa liberada (rechazo) ID: $idMesa");
                  }
                }
              }
              $reservasModel->editField($id, 'reserva_mesa_id', 0);

              $this->logStep($logs, 'PLACETOPAY SONDA - DOCUMENTO RECHAZADO', "SONDA: Documento para inactivar (rechazo): " . print_r($documento, true));

              $this->inactivarBeneficiariosBloqueados($documento, $logs);
              $this->inactivarMesasBloqueadas($documento, $logs);
              $this->logStep($logs, 'PLACETOPAY SONDA - PAGO RECHAZADO', "SONDA: Pago RECHAZADO - Estado cambiado de $estadoAnterior a 6. Auth: $authorization");

              echo "<span style='color: red;'>ACTUALIZADO A RECHAZADO</span><br>";
            } else {
              echo "<span style='color: blue;'>Ya estaba rechazado</span><br>";
            }
          }
        } else {
          $errores++;

          $this->logStep($logs, 'PLACETOPAY SONDA - ERROR CONSULTA', "Error consultando PlaceToPay para reserva $id: " . ($response ? print_r($response->status(), true) : 'Sin respuesta'));

          echo "<span style='color: red;'>Error consultando PlaceToPay</span><br>";
        }
      } catch (Exception $e) {
        $errores++;

        $this->logStep($logs, 'PLACETOPAY SONDA - EXCEPCION', "Excepción consultando reserva $id: " . $e->getMessage());

        echo "<span style='color: red;'>Error: " . $e->getMessage() . "</span><br>";
      }

      echo "</div>";

      // Un solo INSERT por reserva procesada (agrupa todos los pasos logueados en esta iteración)
      $logModel->insertBatch($logs);
      $logs = [];

      // Evitar sobrecargar PlaceToPay
      usleep(500000); // 0.5 segundos entre consultas
    }

    // Log final del resumen
    $this->logStep($logs, 'PLACETOPAY SONDA - RESUMEN FINAL', "Proceso completado - Total: $totalReservas, Procesadas: $procesadas, Actualizadas: $actualizadas, Errores: $errores");
    $logModel->insertBatch($logs);

    echo "<hr>";
    echo "<h3>Resumen Final:</h3>";
    echo "<ul>";
    echo "<li><strong>Total encontradas:</strong> $totalReservas</li>";
    echo "<li><strong>Procesadas exitosamente:</strong> $procesadas</li>";
    echo "<li><strong>Actualizadas:</strong> $actualizadas</li>";
    echo "<li><strong>Errores:</strong> $errores</li>";
    echo "</ul>";

    echo "<p><em>Proceso completado a las " . date('Y-m-d H:i:s') . "</em></p>";
  }

  public function inactivarBeneficiariosBloqueados($documento, &$logs)
  {
    if (!$documento) {
      $this->logStep($logs, 'INACTIVAR BENEFICIARIOS - SIN DOCUMENTO', "No se puede inactivar beneficiarios - Documento vacío");
      return;
    }

    $this->logStep($logs, 'INACTIVAR BENEFICIARIOS - INICIO', "Inactivando beneficiarios bloqueados para documento: " . print_r($documento, true));

    $beneficiariosBloqueadosModel = new Administracion_Model_DbTable_Beneficiariosbloqueos();
    $beneficiariosBloqueadosModel->inactivarBeneficiariosBloqueados($documento);

    $this->logStep($logs, 'INACTIVAR BENEFICIARIOS - COMPLETADO', "Beneficiarios bloqueados inactivados para documento: $documento");
  }

  public function inactivarMesasBloqueadas($documento, &$logs)
  {
    if (!$documento) {
      $this->logStep($logs, 'INACTIVAR MESAS - SIN DOCUMENTO', "No se puede inactivar mesas - Documento vacío");
      return;
    }

    $this->logStep($logs, 'INACTIVAR MESAS - INICIO', "Buscando mesas bloqueadas para documento: " . print_r($documento, true));

    $mesasBloqueoModel = new Administracion_Model_DbTable_Mesasbloqueo();
    $mesasBloqueadas = $mesasBloqueoModel->getList("mesa_bloqueo_documento = '{$documento}' AND mesa_bloqueo_estado = 1", "");

    $this->logStep($logs, 'INACTIVAR MESAS - MESAS ENCONTRADAS', "Mesas bloqueadas encontradas: " . print_r($mesasBloqueadas, true));
    if (!$mesasBloqueadas || !is_countable($mesasBloqueadas) || count($mesasBloqueadas) < 1) {
      $this->logStep($logs, 'INACTIVAR MESAS - SIN MESAS', "No se encontraron mesas bloqueadas para inactivar - Documento: $documento");
      return;
    }
    foreach ($mesasBloqueadas as $mesaBloqueada) {
      $this->logStep($logs, 'INACTIVAR MESAS - ELIMINAR MESA', "Eliminando mesa bloqueada: " . print_r($mesaBloqueada, true));
      $mesasBloqueoModel->deleteRegister($mesaBloqueada->mesa_bloqueo_id);
    }

    $this->logStep($logs, 'INACTIVAR MESAS - COMPLETADO', "Mesas bloqueadas eliminadas para documento: $documento. Total: " . count($mesasBloqueadas));
  }


  public function notificaRegistro($id)
  {
    $logModel = new Administracion_Model_DbTable_Log();
    $logs = [];
    $this->logStep($logs, 'NOTIFICA REGISTRO - INICIO', "Iniciando notificación de registro para ID: $id");

    $reservasModel = new Administracion_Model_DbTable_Reservas();

    $reserva = $reservasModel->getById($id);
    $this->logStep($logs, 'NOTIFICA REGISTRO - RESERVA', "Reserva obtenida para notificar: " . print_r($reserva, true));

    $resultado = null;
    if ($reserva->reserva_estado == 2 || $reserva->reserva_estado == 3 || $reserva->reserva_estado == 11) {
      $sendingemail = new Core_Model_Sendingemail($this->_view);
      if ($sendingemail->notificaPago($reserva)) {
        $this->logStep($logs, 'NOTIFICA REGISTRO - EXITO', "Notificación enviada exitosamente para ID: $id");
        $resultado = true;
      } else {
        $this->logStep($logs, 'NOTIFICA REGISTRO - ERROR', "Error al enviar notificación para ID: $id");
        $resultado = false;
      }
    } else {
      $this->logStep($logs, 'NOTIFICA REGISTRO - ESTADO INVALIDO', "Estado de reserva no permite notificación. Estado: " . $reserva->reserva_estado);
    }

    $logModel->insertBatch($logs);
    return $resultado;
  }

  /**
   * Valida la firma de una notificación de PlaceToPay según
   * https://docs.placetopay.dev/checkout/notification
   * Fórmula: HASH(requestId + status.status + status.date + secretKey)
   * - Si la firma recibida trae el prefijo "sha256:" se usa SHA-256 (recomendado).
   * - Si no trae prefijo, se usa SHA-1 (algoritmo legado, aún soportado).
   */
  private function validarFirmaNotificacion($data, $tranKey)
  {
    if (!isset($data['signature'], $data['requestId'], $data['status']['status'], $data['status']['date'])) {
      return false;
    }

    $base = $data['requestId'] . $data['status']['status'] . $data['status']['date'] . $tranKey;
    $signatureRecibida = $data['signature'];

    if (strpos($signatureRecibida, 'sha256:') === 0) {
      $signatureRecibida = substr($signatureRecibida, strlen('sha256:'));
      $signatureCalculada = hash('sha256', $base);
    } else {
      $signatureCalculada = sha1($base);
    }

    return hash_equals($signatureCalculada, $signatureRecibida);
  }

  public function notificacionAction()
  {
    // Webhook servidor a servidor de PlaceToPay: la notificación llega como JSON en el
    // body (no como parámetros de formulario). Ver https://docs.placetopay.dev/checkout/notification
    $logModel = new Administracion_Model_DbTable_Log();
    $logs = [];

    $rawBody = file_get_contents('php://input');
    $this->logStep($logs, 'PLACETOPAY NOTIFICACION - BODY', "Body recibido: $rawBody");

    $data = json_decode($rawBody, true);

    if (!is_array($data) || !isset($data['status']['status'], $data['status']['date'], $data['requestId'], $data['reference'], $data['signature'])) {
      $this->logStep($logs, 'PLACETOPAY NOTIFICACION - ERROR', "Body inválido o incompleto: $rawBody");
      $logModel->insertBatch($logs);
      http_response_code(400);
      return;
    }

    $tranKey = Payment_Placetopay::getInstance()->getData()['tranKey'];

    // La firma valida que la notificación realmente viene de PlaceToPay
    if (!$this->validarFirmaNotificacion($data, $tranKey)) {
      $this->logStep($logs, 'PLACETOPAY NOTIFICACION - FIRMA INVALIDA', "Firma inválida. requestId: {$data['requestId']}, reference: {$data['reference']}, signature recibida: {$data['signature']}");
      $logModel->insertBatch($logs);
      http_response_code(401);
      return;
    }

    $reference = $data['reference'];
    // if (str_starts_with($reference, 'CULBOL_')) {
    //   try {
    //     $url = 'https://junior-typological-formlessly.ngrok-free.dev/core/placetopay/notificacion';
    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $rawBody);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, [
    //       'Content-Type: application/json',
    //       'ngrok-skip-browser-warning: true',
    //     ]);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    //     curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    //     curl_exec($ch);
    //     curl_close($ch);
    //     $this->logStep($logs, 'PLACETOPAY NOTIFICACION - FORWARD DEBUG', "Referencia sin prefijo CULBOL_, reenviada a endpoint debug: $reference");
    //   } catch (\Throwable $e) {
    //     $this->logStep($logs, 'PLACETOPAY NOTIFICACION - FORWARD ERROR', "Error reenviando a endpoint debug: " . $e->getMessage());
    //   }

    //   $logModel->insertBatch($logs);
    //   return;
    // }

    if (!str_starts_with($reference, 'BOL_') && !str_starts_with($reference, 'CULBOL_')) {
      // No es un error: es una notificación válida de otra plataforma que comparte las
      // mismas credenciales de PlaceToPay. Se responde 200 para que no reintenten.
      $this->logStep($logs, 'PLACETOPAY NOTIFICACION - IGNORADA', "Referencia de otra plataforma, se ignora: $reference");
      $logModel->insertBatch($logs);
      return;
    }
    $id = substr($reference, strrpos($reference, '_') + 1);
    $this->logStep($logs, 'PLACETOPAY NOTIFICACION - INICIO', "Notificación válida recibida para reserva ID: $id, requestId: {$data['requestId']}, estado notificado: {$data['status']['status']}");

    if (!$id) {
      $this->logStep($logs, 'PLACETOPAY NOTIFICACION - ERROR', "No se pudo extraer el ID de reserva de la referencia: $reference");
      $logModel->insertBatch($logs);
      return;
    }

    $reservasModel = new Administracion_Model_DbTable_Reservas();
    $reserva = $reservasModel->getById($id);

    if (!$reserva || !$reserva->request_id) {
      // 500 para que PlaceToPay reintente el webhook: puede ser una condición de carrera
      // (la reserva aún no existe/no tiene request_id en el instante en que llega la notificación)
      $this->logStep($logs, 'PLACETOPAY NOTIFICACION - ERROR', "Reserva no encontrada o sin request_id. ID: $id");
      $logModel->insertBatch($logs);
      http_response_code(500);
      return;
    }

    $placetopay = Payment_Placetopay::getInstance()->getPlacetopay();

    // La notificación no trae el detalle del pago (autorización, franquicia), por eso
    // se consulta el estado completo igual que en responseAction/sondaAction.
    $response = $placetopay->query($reserva->request_id);
    $this->logStep($logs, 'PLACETOPAY NOTIFICACION - RESPUESTA COMPLETA', "Respuesta completa de PlaceToPay: " . print_r($response, true));

    if ($response && $response->isSuccessful()) {
      $this->procesarEstadoPago($id, $reserva, $response, $logs, 'PLACETOPAY NOTIFICACION');
    } else {
      // 500 para que PlaceToPay reintente: la falla fue nuestra al consultar, no de la notificación
      $this->logStep($logs, 'PLACETOPAY NOTIFICACION - ERROR CONEXION', "Error de conexión con PlaceToPay. Response: " . print_r($response, true));
      $logModel->insertBatch($logs);
      http_response_code(500);
      return;
    }

    $logModel->insertBatch($logs);
  }

  /**
   * Acción de diagnóstico manual: hace un POST de prueba al endpoint de
   * debug (ngrok) para confirmar que el servidor de pruebas puede hacer
   * salidas curl correctamente (extensión disponible, sin bloqueo de
   * firewall, SSL ok, etc). No toca lógica de negocio ni base de datos.
   */
  // public function testcurlAction()
  // {
  //   $this->setLayout('blanco');

  //   $url = 'https://junior-typological-formlessly.ngrok-free.dev/core/placetopay/notificacion';
  //   $payload = json_encode(['test' => true, 'origen' => 'testcurlAction', 'timestamp' => date('Y-m-d H:i:s')]);

  //   echo "<h2>Test de conexión CURL</h2>";
  //   echo "<p><strong>URL:</strong> $url</p>";
  //   echo "<p><strong>Payload enviado:</strong> " . htmlspecialchars($payload) . "</p>";

  //   if (!function_exists('curl_init')) {
  //     echo "<p style='color: red;'><strong>ERROR:</strong> La extensión curl no está disponible en este servidor.</p>";
  //     return;
  //   }

  //   $ch = curl_init($url);
  //   curl_setopt($ch, CURLOPT_POST, true);
  //   curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
  //   curl_setopt($ch, CURLOPT_HTTPHEADER, [
  //     'Content-Type: application/json',
  //     'ngrok-skip-browser-warning: true',
  //   ]);
  //   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  //   curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
  //   curl_setopt($ch, CURLOPT_TIMEOUT, 10);

  //   $response = curl_exec($ch);
  //   $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  //   $curlErrno = curl_errno($ch);
  //   $curlError = curl_error($ch);
  //   curl_close($ch);

  //   if ($curlErrno) {
  //     echo "<p style='color: red;'><strong>ERROR CURL ($curlErrno):</strong> " . htmlspecialchars($curlError) . "</p>";
  //   } else {
  //     echo "<p style='color: green;'><strong>CURL ejecutado correctamente.</strong></p>";
  //   }

  //   echo "<p><strong>Código HTTP:</strong> $httpCode</p>";
  //   echo "<p><strong>Respuesta recibida:</strong></p>";
  //   echo "<pre>" . htmlspecialchars($response !== false ? $response : '(sin respuesta)') . "</pre>";
  // }
}
