<h1 class="titulo-principal"><i class="fas fa-list-alt"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
  <!-- Filtros -->
  <form action="<?php echo $this->route; ?>/logs" method="post">
    <div class="content-dashboard">
      <div class="row">
        <div class="col-3">
          <label>Fecha Desde</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text input-icono fondo-morado"><i class="fas fa-calendar"></i></span>
            </div>
            <input type="date" class="form-control" name="fecha_desde" value="<?php echo $this->fecha_desde; ?>">
          </label>
        </div>
        <div class="col-3">
          <label>Fecha Hasta</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text input-icono fondo-morado"><i class="fas fa-calendar"></i></span>
            </div>
            <input type="date" class="form-control" name="fecha_hasta" value="<?php echo $this->fecha_hasta; ?>">
          </label>
        </div>
        <div class="col-3">
          <label>Tipo de Acción</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text input-icono fondo-azul"><i class="fas fa-filter"></i></span>
            </div>
            <select class="form-control" name="tipo_accion">
              <option value="">Todas las acciones</option>
              <?php foreach ($this->tipos_accion as $key => $value): ?>
                <option value="<?= $key; ?>" <?php if ($this->tipo_accion == $key)
                                                echo "selected"; ?>><?= $value; ?>
                </option>
              <?php endforeach ?>
            </select>
          </label>
        </div>
        <div class="col-3">
          <label>ID Reserva</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text input-icono fondo-cafe"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" class="form-control" name="reserva_id" value="<?php echo $this->reserva_id; ?>"
              placeholder="ID de reserva">
          </label>
        </div>
      </div>
      <div class="row mt-3">
        <div class="col-6">
          <button type="submit" class="btn btn-block btn-azul"> <i class="fas fa-filter"></i> Filtrar Logs</button>
        </div>
        <div class="col-6">
          <a class="btn btn-block btn-azul-claro" href="<?php echo $this->route; ?>/logs"> <i class="fas fa-eraser"></i>
            Limpiar Filtros</a>
        </div>
      </div>
    </div>
  </form>

  <!-- Paginación -->
  <div align="center">
    <ul class="pagination justify-content-center">
      <?php
      $url = $this->route . '/logs';
      $params = array();
      if ($this->fecha_desde)
        $params[] = "fecha_desde=" . $this->fecha_desde;
      if ($this->fecha_hasta)
        $params[] = "fecha_hasta=" . $this->fecha_hasta;
      if ($this->tipo_accion)
        $params[] = "tipo_accion=" . $this->tipo_accion;
      if ($this->reserva_id)
        $params[] = "reserva_id=" . $this->reserva_id;
      $paramString = !empty($params) ? '&' . implode('&', $params) : '';

      if ($this->totalpages > 1) {
        if ($this->page != 1)
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page - 1) . $paramString . '"> &laquo; Anterior </a></li>';

        $min = max(1, $this->page - 5);
        $max = min($this->totalpages, $this->page + 5);

        for ($i = $min; $i <= $max; $i++) {
          if ($this->page == $i)
            echo '<li class="active page-item"><a class="page-link">' . $this->page . '</a></li>';
          else
            echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . $paramString . '">' . $i . '</a></li>';
        }

        if ($this->page != $this->totalpages)
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page + 1) . $paramString . '">Siguiente &raquo;</a></li>';
      }
      ?>
    </ul>
  </div>

  <!-- Resultados -->
  <div class="content-dashboard">
    <div class="franja-paginas">
      <div class="row g-0 justify-content-between">
        <div class="col-8">
          <div class="titulo-registro">Se encontraron <?php echo $this->register_number; ?> Logs</div>
        </div>
        <div class="col-4">
          <div class="text-end">
            <a class="btn btn-sm btn-info" href="<?php echo $this->route; ?>">
              <i class="fas fa-arrow-left"></i> Volver a Invitados
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="content-table">
      <table class="table table-striped table-hover table-administrator text-start">
        <thead>
          <tr>
            <th>Fecha/Hora</th>
            <th>Acción</th>
            <th>Usuario</th>
            <th>Invitado/Reserva</th>
            <th>Impacto Financiero</th>
            <th width="100">Detalles</th>
          </tr>
        </thead>
        <tbody>
          <?php
          // Función auxiliar para parsear logs en diferentes formatos
          function parseLogString($logString)
          {
            $data = array();

            // Si es un objeto stdClass convertido a string con print_r
            if (strpos($logString, 'stdClass Object') !== false) {
              // Extraer información básica usando regex
              if (preg_match('/\[reserva_id_reserva\] => (\d+)/', $logString, $matches)) {
                $data['reserva_id_reserva'] = $matches[1];
              }
              if (preg_match('/\[documento_invitado\] => ([^\n\r]*)/', $logString, $matches)) {
                $data['documento_invitado'] = trim($matches[1]);
              }
              if (preg_match('/\[invitadoReserva_nombre_invitado\] => ([^\n\r]*)/', $logString, $matches)) {
                $data['invitadoReserva_nombre_invitado'] = trim($matches[1]);
              }
              if (preg_match('/\[invitado_tipo\] => (\d+)/', $logString, $matches)) {
                $data['invitado_tipo'] = $matches[1];
              }
            }

            // Si es un Array de recálculo
            if (strpos($logString, 'Array') !== false && strpos($logString, 'valor_anterior') !== false) {
              if (preg_match('/\[valor_anterior\] => ([0-9.]+)/', $logString, $matches)) {
                $data['valor_anterior'] = $matches[1];
              }
              if (preg_match('/\[valor_nuevo\] => ([0-9.]+)/', $logString, $matches)) {
                $data['valor_nuevo'] = $matches[1];
              }
              if (preg_match('/\[reserva_id\] => (\d+)/', $logString, $matches)) {
                $data['reserva_id'] = $matches[1];
              }
            }

            return $data;
          }

          // Función para extraer datos combinados del log completo
          function extractLogData($log)
          {
            $combinedData = array();

            // 1. Agregar campos directos del objeto log primero
            foreach ($log as $field => $value) {
              if ($field != 'log_log' && $value !== null && $value !== '') {
                $combinedData[$field] = $value;
              }
            }

            // 2. Extraer del JSON en log_log
            if (($log->log_log) && is_string($log->log_log)) {
              // Limpiar el JSON de caracteres de control problemáticos
              $cleanJson = $log->log_log;

              // Escapar caracteres de control comunes en strings dentro del JSON
              $cleanJson = str_replace(["\n", "\r", "\t"], ['\\n', '\\r', '\\t'], $cleanJson);

              // Intentar decodificar el JSON limpio
              $jsonDecoded = json_decode($cleanJson, true);

              // Si aún falla, intentar con más limpieza
              if ($jsonDecoded === null) {
                // Remover caracteres de control no printables excepto espacios
                $cleanJson = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $log->log_log);
                $jsonDecoded = json_decode($cleanJson, true);
              }

              if ($jsonDecoded !== null) {
                // Primero agregar todos los campos del JSON principal (sin sobrescribir existentes)
                foreach ($jsonDecoded as $key => $value) {
                  if (!isset($combinedData[$key])) {
                    $combinedData[$key] = $value;
                  }
                }

                // Extraer datos específicos de recálculo
                if (($jsonDecoded['valor_recalculado'])) {
                  $combinedData['valor_nuevo'] = $jsonDecoded['valor_recalculado'];
                }

                // Extraer de cambios_que_provocaron_recalculo si existe
                if (($jsonDecoded['cambios_que_provocaron_recalculo'])) {
                  $cambios = $jsonDecoded['cambios_que_provocaron_recalculo'];

                  // Extraer información de recalculo_reserva
                  if (($cambios['recalculo_reserva']['nuevo'])) {
                    $recalculo = $cambios['recalculo_reserva']['nuevo'];
                    if (($recalculo['valor_actual'])) {
                      $combinedData['valor_anterior'] = $recalculo['valor_actual'];
                    }
                    if (($recalculo['valor_calculado'])) {
                      $combinedData['valor_nuevo'] = $recalculo['valor_calculado'];
                    }
                  }

                  // Extraer información del log_log anidado solo para completar datos faltantes
                  if (($cambios['log_log']['nuevo'])) {
                    $logAnidado = $cambios['log_log']['nuevo'];

                    // Si es un string, intentar parsearlo como array PHP
                    if (is_string($logAnidado) && strpos($logAnidado, 'Array') !== false) {
                      // Solo extraer si no tenemos estos datos ya
                      if (!isset($combinedData['reserva_id_reserva']) && preg_match('/\[reserva_id_reserva\] => (\d+)/', $logAnidado, $matches)) {
                        $combinedData['reserva_id_reserva'] = $matches[1];
                      }
                      if (!isset($combinedData['documento_invitado']) && preg_match('/\[documento_invitado\] => ([^\n\r]*)/', $logAnidado, $matches)) {
                        $combinedData['documento_invitado'] = trim($matches[1]);
                      }
                      if (!isset($combinedData['invitadoReserva_nombre_invitado']) && preg_match('/\[invitadoReserva_nombre_invitado\] => ([^\n\r]*)/', $logAnidado, $matches)) {
                        $combinedData['invitadoReserva_nombre_invitado'] = trim($matches[1]);
                      }
                      if (!isset($combinedData['invitado_tipo']) && preg_match('/\[invitado_tipo\] => (\d+)/', $logAnidado, $matches)) {
                        $combinedData['invitado_tipo'] = $matches[1];
                      }
                      if (!isset($combinedData['invitadoReserva_correo_invitado']) && preg_match('/\[invitadoReserva_correo_invitado\] => ([^\n\r]*)/', $logAnidado, $matches)) {
                        $combinedData['invitadoReserva_correo_invitado'] = trim($matches[1]);
                      }
                      if (!isset($combinedData['invitadoReserva_telefono']) && preg_match('/\[invitadoReserva_telefono\] => ([^\n\r]*)/', $logAnidado, $matches)) {
                        $combinedData['invitadoReserva_telefono'] = trim($matches[1]);
                      }
                      if (!isset($combinedData['id_invitado']) && preg_match('/\[id_invitado\] => (\d+)/', $logAnidado, $matches)) {
                        $combinedData['id_invitado'] = $matches[1];
                      }
                    } else if (is_array($logAnidado)) {
                      // Si ya es un array, mezclarlo directamente solo para campos faltantes
                      foreach ($logAnidado as $key => $value) {
                        if (!isset($combinedData[$key])) {
                          $combinedData[$key] = $value;
                        }
                      }
                    }
                  }
                }

                // Extraer datos específicos para diferentes tipos de logs (solo si no existen)
                if (($jsonDecoded['datos_invitado'])) {
                  foreach ($jsonDecoded['datos_invitado'] as $key => $value) {
                    if (!isset($combinedData[$key])) {
                      $combinedData[$key] = $value;
                    }
                  }
                }

                if (($jsonDecoded['datos_insertados'])) {
                  foreach ($jsonDecoded['datos_insertados'] as $key => $value) {
                    if (!isset($combinedData[$key])) {
                      $combinedData[$key] = $value;
                    }
                  }
                }

                if (($jsonDecoded['datos_nuevos'])) {
                  foreach ($jsonDecoded['datos_nuevos'] as $key => $value) {
                    if (!isset($combinedData[$key])) {
                      $combinedData[$key] = $value;
                    }
                  }
                }

                if (($jsonDecoded['datos_eliminados'])) {
                  foreach ($jsonDecoded['datos_eliminados'] as $key => $value) {
                    if (!isset($combinedData[$key])) {
                      $combinedData[$key] = $value;
                    }
                  }
                }

                // Si hay recálculo directo, extraer valores
                if (($jsonDecoded['recalculo'])) {
                  $combinedData['valor_anterior'] = $jsonDecoded['recalculo']['valor_anterior'] ?? null;
                  $combinedData['valor_nuevo'] = $jsonDecoded['recalculo']['valor_nuevo'] ?? null;
                  $combinedData['diferencia'] = $jsonDecoded['recalculo']['diferencia'] ?? null;
                }

                // Para logs de recálculo, extraer datos del recálculo
                if (($jsonDecoded['valor_anterior']) && ($jsonDecoded['valor_nuevo'])) {
                  $combinedData['valor_anterior'] = $jsonDecoded['valor_anterior'];
                  $combinedData['valor_nuevo'] = $jsonDecoded['valor_nuevo'];
                }
              } else {
                // Si no es JSON, usar parseLogString
                $parsedData = parseLogString($log->log_log);
                foreach ($parsedData as $key => $value) {
                  if (!isset($combinedData[$key])) {
                    $combinedData[$key] = $value;
                  }
                }
              }
            }

            return $combinedData;
          }

          foreach ($this->logs as $key => $log) {
            // Usar la nueva función para extraer todos los datos
            $logData = extractLogData($log);

            $accionClass = '';
            $accionIcon = '';

            switch ($log->log_tipo) {
              case 'CREAR_NEWINVITADOS':
                $accionClass = 'text-bg-success';
                $accionIcon = 'fas fa-plus';
                break;
              case 'ACTUALIZAR_NEWINVITADOS_DETALLADO':
              case 'EDITAR NEWINVITADOS':
                $accionClass = 'text-bg-warning';
                $accionIcon = 'fas fa-edit';
                break;
              case 'ELIMINAR_NEWINVITADOS':
              case 'BORRAR NEWINVITADOS':
                $accionClass = 'text-bg-danger';
                $accionIcon = 'fas fa-trash';
                break;
              case 'RECALCULO_RESERVA_POST_ELIMINACION':
              case 'RECALCULO_RESERVA_POST_ACTUALIZACION':
                $accionClass = 'text-bg-info';
                $accionIcon = 'fas fa-calculator';
                break;
              default:
                $accionClass = 'text-bg-secondary';
                $accionIcon = 'fas fa-info';
            }
          ?>
            <?php
            // Debug temporal para ver qué está extrayendo
            // if ($key == 0) {
            //   echo "<pre>";
            //   echo "=== LOGDATA EXTRAÍDO ===\n";
            //   print_r($logData);
            //   echo "\n=== LOG ORIGINAL ===\n";
            //   print_r($log);
            //   echo "\n=== JSON STRING ===\n";
            //   echo "JSON Length: " . strlen($log->log_log) . "\n";
            //   echo "First 200 chars: " . substr($log->log_log, 0, 200) . "\n";
            //   echo "Last 200 chars: " . substr($log->log_log, -200) . "\n";
            //   echo "\n=== JSON DECODE TEST ===\n";
            //   $jsonDecoded = json_decode($log->log_log, true);
            //   echo "JSON Error: " . json_last_error_msg() . "\n";
            //   echo "JSON Decoded is null: " . ($jsonDecoded === null ? 'YES' : 'NO') . "\n";
            //   if ($jsonDecoded !== null) {
            //     echo "JSON Keys: " . implode(', ', array_keys($jsonDecoded)) . "\n";
            //     echo "Has ip_address: " . (isset($jsonDecoded['ip_address']) ? 'YES - ' . $jsonDecoded['ip_address'] : 'NO') . "\n";
            //     print_r($jsonDecoded);
            //   }
            //   echo "</pre>";
            // }

            // Extraer IP address de diferentes ubicaciones posibles
            $ip_address = 'N/A';
            if (($logData['ip_address'])) {
              $ip_address = $logData['ip_address'];
            } elseif (($logData['datos_nuevos']['ip_address'])) {
              $ip_address = $logData['datos_nuevos']['ip_address'];
            } elseif (($logData['datos_insertados']['ip_address'])) {
              $ip_address = $logData['datos_insertados']['ip_address'];
            } elseif (($logData['datos_eliminados']['ip_address'])) {
              $ip_address = $logData['datos_eliminados']['ip_address'];
            }

            if ($key == 0) {
              // echo "<pre>";
              // print_r($logData);
              // print_r($log);
              // echo "</pre>";
            }
            ?>

            <tr>
              <td>
                <small>
                  <?= date('d/m/Y H:i:s', strtotime($log->log_fecha ?? date('Y-m-d H:i:s'))); ?>
                  <br>
                  <span class="text-muted">IP: <?= $ip_address ?? 'N/A'; ?></span>
                </small>
              </td>
              <td>
                <span class="badge <?= $accionClass; ?>">
                  <i class="<?= $accionIcon; ?>"></i>
                  <?= $this->tipos_accion[$log->log_tipo] ?? $log->log_tipo; ?>
                </span>
              </td>
              <td>
                <?php
                // Extraer información del usuario
                $usuario_nombre = '';
                $usuario_id = '';

                if (($logData['usuario_id'])) {
                  $usuario_id = $logData['usuario_id'];
                }
                if (($logData['usuario_nombre'])) {
                  $usuario_nombre = $logData['usuario_nombre'];
                } else {
                  $usuario_nombre = $log->log_usuario ?? 'Sistema';
                }
                ?>
                <strong><?= $usuario_nombre; ?></strong>
                <br>
                <small class="text-muted">ID: <?= $usuario_id ?: ($log->log_usuario ?? 'N/A'); ?></small>
              </td>
              <td>
                <?php
                // Extraer información del invitado de múltiples fuentes posibles
                $documento = '';
                $nombre = '';
                $reservaId = '';

                // Buscar en diferentes ubicaciones del JSON
                if (($logData['documento_invitado'])) {
                  $documento = $logData['documento_invitado'];
                } elseif (($logData['datos_nuevos']['documento_invitado'])) {
                  $documento = $logData['datos_nuevos']['documento_invitado'];
                } elseif (($logData['datos_insertados']['documento_invitado'])) {
                  $documento = $logData['datos_insertados']['documento_invitado'];
                } elseif (($logData['datos_eliminados']['documento_invitado'])) {
                  $documento = $logData['datos_eliminados']['documento_invitado'];
                } elseif (($logData['documento_eliminado'])) {
                  $documento = $logData['documento_eliminado'];
                }

                if (($logData['invitadoReserva_nombre_invitado'])) {
                  $nombre = $logData['invitadoReserva_nombre_invitado'];
                } elseif (($logData['datos_nuevos']['invitadoReserva_nombre_invitado'])) {
                  $nombre = $logData['datos_nuevos']['invitadoReserva_nombre_invitado'];
                } elseif (($logData['datos_insertados']['invitadoReserva_nombre_invitado'])) {
                  $nombre = $logData['datos_insertados']['invitadoReserva_nombre_invitado'];
                } elseif (($logData['datos_eliminados']['invitadoReserva_nombre_invitado'])) {
                  $nombre = $logData['datos_eliminados']['invitadoReserva_nombre_invitado'];
                } elseif (($logData['nombre_eliminado'])) {
                  $nombre = $logData['nombre_eliminado'];
                }

                if (($logData['reserva_id_reserva'])) {
                  $reservaId = $logData['reserva_id_reserva'];
                } elseif (($logData['datos_nuevos']['reserva_id_reserva'])) {
                  $reservaId = $logData['datos_nuevos']['reserva_id_reserva'];
                } elseif (($logData['datos_insertados']['reserva_id_reserva'])) {
                  $reservaId = $logData['datos_insertados']['reserva_id_reserva'];
                } elseif (($logData['datos_eliminados']['reserva_id_reserva'])) {
                  $reservaId = $logData['datos_eliminados']['reserva_id_reserva'];
                } elseif (($logData['reserva_id_afectada'])) {
                  $reservaId = $logData['reserva_id_afectada'];
                } elseif (($logData['reserva_id'])) {
                  $reservaId = $logData['reserva_id'];
                }
                ?>
                <?php if ($documento): ?>
                  <strong>Doc:</strong> <?= htmlspecialchars($documento); ?><br>
                <?php endif; ?>
                <?php if ($nombre): ?>
                  <strong>Nombre:</strong> <?= htmlspecialchars($nombre); ?><br>
                <?php endif; ?>
                <strong>Reserva:</strong> <?= htmlspecialchars($reservaId ?: 'N/A'); ?>
              </td>
              <td>
                <?php
                $impacto = $logData['impacto_financiero'] ?? array();
                $valorAnterior = null;
                $valorNuevo = null;
                $diferencia = null;

                // Buscar valores de impacto financiero en diferentes ubicaciones
                if (($logData['valor_anterior'])) {
                  $valorAnterior = is_numeric($logData['valor_anterior']) ? (float) $logData['valor_anterior'] : null;
                }

                if (($logData['valor_nuevo'])) {
                  $valorNuevo = is_numeric($logData['valor_nuevo']) ? (float) $logData['valor_nuevo'] : null;
                } elseif (($logData['valor_recalculado'])) {
                  $valorNuevo = is_numeric($logData['valor_recalculado']) ? (float) $logData['valor_recalculado'] : null;
                }

                if (($logData['diferencia'])) {
                  $diferencia = is_numeric($logData['diferencia']) ? (float) $logData['diferencia'] : null;
                } elseif ($valorAnterior !== null && $valorNuevo !== null) {
                  $diferencia = $valorNuevo - $valorAnterior;
                }

                // También buscar en impacto_financiero
                if ($valorAnterior === null && ($impacto['valor_anterior_reserva'])) {
                  $valorAnterior = is_numeric($impacto['valor_anterior_reserva']) ? (float) $impacto['valor_anterior_reserva'] : null;
                }
                if ($valorNuevo === null && ($impacto['valor_nuevo_reserva'])) {
                  $valorNuevo = is_numeric($impacto['valor_nuevo_reserva']) ? (float) $impacto['valor_nuevo_reserva'] : null;
                }

                if ($valorAnterior !== null && $valorNuevo !== null && is_numeric($valorAnterior) && is_numeric($valorNuevo)): ?>
                  <div class="alert alert-sm alert-info p-1">
                    <small>
                      <strong>Antes:</strong> $<?= number_format((float) $valorAnterior, 0, ',', '.'); ?><br>
                      <strong>Después:</strong> $<?= number_format((float) $valorNuevo, 0, ',', '.'); ?><br>
                      <strong>Diferencia:</strong>
                      <?php $diff = $diferencia ?? ((float) $valorNuevo - (float) $valorAnterior); ?>
                      <span class="<?= $diff >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <?= $diff >= 0 ? '+' : ''; ?>$<?= number_format($diff, 0, ',', '.'); ?>
                      </span>
                    </small>
                  </div>
                <?php elseif (($impacto['mensaje']) && $impacto['mensaje']): ?>
                  <small class="text-info"><?= htmlspecialchars($impacto['mensaje']); ?></small>
                <?php elseif ($log->log_tipo == 'RECALCULO_RESERVA_POST_ELIMINACION' || $log->log_tipo == 'RECALCULO_RESERVA_POST_ACTUALIZACION'): ?>
                  <small class="text-info">
                    <?php if (($logData['valor_recalculado']) && is_numeric($logData['valor_recalculado'])): ?>
                      Valor recalculado: $<?= number_format((float) $logData['valor_recalculado'], 0, ',', '.'); ?>
                    <?php else: ?>
                      Recálculo de reserva
                    <?php endif; ?>
                  </small>
                <?php else: ?>
                  <small class="text-muted">Sin impacto registrado</small>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#logModal<?= $log->log_id; ?>"
                  title="Ver detalles completos">
                  <i class="fas fa-eye"></i>
                </button>

                <!-- Modal de detalles -->
                <div class="modal fade" id="logModal<?= $log->log_id; ?>" tabindex="-1">
                  <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title">
                          <i class="<?= $accionIcon; ?>"></i>
                          Detalles del log - <?= $this->tipos_accion[$log->log_tipo] ?? $log->log_tipo; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-12">
                            <h6><i class="fas fa-info-circle"></i> Información general</h6>
                            <table class="table table-sm">
                              <tr>
                                <td><strong>Fecha:</strong></td>
                                <td><?= date('d/m/Y H:i:s', strtotime($log->log_fecha ?? date('Y-m-d H:i:s'))); ?></td>
                              </tr>
                              <tr>
                                <td><strong>Usuario:</strong></td>
                                <td><?= $usuario_nombre; ?>
                                  (ID: <?= $usuario_id ?: ($log->log_usuario ?? 'N/A'); ?>)</td>
                              </tr>
                              <tr>
                                <td><strong>IP:</strong></td>
                                <td><?= $ip_address; ?></td>
                              </tr>
                              <tr>
                                <td><strong>Navegador:</strong></td>
                                <td>
                                  <small><?= substr(($logData['user_agent'] ?? $logData['reserva_user_agent'] ?? 'N/A'), 0, 50) . '...'; ?></small>
                                </td>
                              </tr>
                            </table>
                          </div>
                          <div class="col-12">
                            <?php if (($logData['cambios_detectados']) && !empty($logData['cambios_detectados'])): ?>
                              <h6><i class="fas fa-exchange-alt"></i> Cambios realizados</h6>
                              <div class="table-responsive">
                                <table class="table table-sm">
                                  <?php foreach ($logData['cambios_detectados'] as $campo => $cambio): ?>
                                    <tr>
                                      <td colspan="2"><strong><?= htmlspecialchars($campo); ?>:</strong></td>
                                    </tr>
                                    <tr>
                                      <td style="width: 50%; vertical-align: top;">
                                        <small>
                                          <strong class="text-danger">Anterior:</strong><br>
                                          <?php
                                          $valorAnterior = $cambio['anterior'] ?? 'N/A';
                                          if (is_object($valorAnterior) || is_array($valorAnterior)) {
                                            // Para objetos/arrays, mostrar solo información relevante
                                            if (is_object($valorAnterior) && isset($valorAnterior->id)) {
                                              echo '<span class="badge bg-secondary">Objeto: ID ' . $valorAnterior->id . '</span>';
                                            } elseif (is_array($valorAnterior) && !empty($valorAnterior)) {
                                              echo '<span class="badge bg-secondary">Array con ' . count($valorAnterior) . ' elementos</span>';
                                            } else {
                                              echo '<span class="badge bg-secondary">Objeto/Array complejo</span>';
                                            }
                                          } else {
                                            echo htmlspecialchars($valorAnterior);
                                          }
                                          ?>
                                        </small>
                                      </td>
                                      <td style="width: 50%; vertical-align: top;">
                                        <small>
                                          <strong class="text-success">Nuevo:</strong><br>
                                          <?php
                                          $valorNuevo = $cambio['nuevo'] ?? 'N/A';
                                          if (is_object($valorNuevo) || is_array($valorNuevo)) {
                                            // Para objetos/arrays, mostrar información más detallada
                                            if (is_object($valorNuevo)) {
                                              if (isset($valorNuevo->id)) {
                                                echo '<span class="badge bg-info">Reserva ID: ' . $valorNuevo->id . '</span><br>';
                                              }
                                              if (isset($valorNuevo->reserva_nombre_cliente)) {
                                                echo '<small>Cliente: ' . htmlspecialchars($valorNuevo->reserva_nombre_cliente . ' ' . ($valorNuevo->reserva_apellido_cliente ?? '')) . '</small><br>';
                                              }
                                              if (isset($valorNuevo->reserva_total_pagar) && is_numeric($valorNuevo->reserva_total_pagar)) {
                                                echo '<small>Total: $' . number_format((float) $valorNuevo->reserva_total_pagar, 0, ',', '.') . '</small><br>';
                                              }
                                              if (isset($valorNuevo->reserva_estado_texto)) {
                                                echo '<small>Estado: ' . htmlspecialchars($valorNuevo->reserva_estado_texto) . '</small>';
                                              }
                                            } elseif (is_array($valorNuevo)) {
                                              if (isset($valorNuevo['mensaje'])) {
                                                echo '<span class="badge bg-info">' . htmlspecialchars($valorNuevo['mensaje']) . '</span><br>';
                                              }
                                              if (isset($valorNuevo['valor_actual']) && is_numeric($valorNuevo['valor_actual'])) {
                                                echo '<small>Valor: $' . number_format((float) $valorNuevo['valor_actual'], 0, ',', '.') . '</small><br>';
                                              }
                                              if (isset($valorNuevo['total_invitados'])) {
                                                echo '<small>Invitados: ' . $valorNuevo['total_invitados'] . '</small>';
                                              }
                                              if (empty(array_filter($valorNuevo, function ($v) {
                                                return isset($v);
                                              }))) {
                                                echo '<span class="badge bg-secondary">Array con ' . count($valorNuevo) . ' elementos</span>';
                                              }
                                            }
                                          ?>
                                            <br><button class="btn btn-xs btn-outline-info mt-1" type="button"
                                              data-bs-toggle="collapse" data-bs-target="#detalle_<?= md5($campo); ?>"
                                              aria-expanded="false">
                                              <i class="fas fa-eye"></i> Ver detalle completo
                                            </button>
                                            <div class="collapse mt-2" id="detalle_<?= md5($campo); ?>">
                                              <div class="card card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                                <pre
                                                  style="font-size: 10px; margin: 0;"><?= htmlspecialchars(print_r($valorNuevo, true)); ?></pre>
                                              </div>
                                            </div>
                                          <?php
                                          } else {
                                            echo htmlspecialchars($valorNuevo);
                                          }
                                          ?>
                                        </small>
                                      </td>
                                    </tr>
                                  <?php endforeach; ?>
                                </table>
                              </div>
                            <?php else: ?>
                              <h6><i class="fas fa-info-circle"></i> Datos del Registro</h6>
                              <table class="table table-sm">
                                <?php if (($logData['invitadoReserva_nombre_invitado']) || ($nombre)): ?>
                                  <tr>
                                    <td><strong>Nombre:</strong></td>
                                    <td><?= $logData['invitadoReserva_nombre_invitado'] ?? $nombre ?? 'N/A'; ?></td>
                                  </tr>
                                <?php endif; ?>
                                <?php if (($logData['documento_invitado']) || ($documento)): ?>
                                  <tr>
                                    <td><strong>Documento:</strong></td>
                                    <td><?= $logData['documento_invitado'] ?? $documento ?? 'N/A'; ?></td>
                                  </tr>
                                <?php endif; ?>
                                <?php if (($logData['reserva_id_reserva']) || ($reservaId)): ?>
                                  <tr>
                                    <td><strong>Reserva ID:</strong></td>
                                    <td><?= $logData['reserva_id_reserva'] ?? $reservaId ?? 'N/A'; ?></td>
                                  </tr>
                                <?php endif; ?>
                                <?php if (($logData['invitado_tipo'])): ?>
                                  <tr>
                                    <td><strong>Tipo:</strong></td>
                                    <td><?= $logData['invitado_tipo'] == '1' ? 'Socio' : 'Invitado'; ?></td>
                                  </tr>
                                <?php endif; ?>
                              </table>
                            <?php endif; ?>
                          </div>
                        </div>

                        <?php if ($valorAnterior !== null && $valorNuevo !== null && is_numeric($valorAnterior) && is_numeric($valorNuevo)): ?>
                          <hr>
                          <h6><i class="fas fa-calculator"></i> Impacto <frameset></frameset>inanciero</h6>
                          <div class="alert alert-info">
                            <div class="row">
                              <div class="col-4 text-center">
                                <strong>Valor anterior</strong><br>
                                <span
                                  class="h5 text-danger">$<?= number_format((float) $valorAnterior, 0, ',', '.'); ?></span>
                              </div>
                              <div class="col-4 text-center">
                                <strong>Valor nuevo</strong><br>
                                <span class="h5 text-success">$<?= number_format((float) $valorNuevo, 0, ',', '.'); ?></span>
                              </div>
                              <div class="col-4 text-center">
                                <strong>Diferencia</strong><br>
                                <?php $diff = (float) $valorNuevo - (float) $valorAnterior; ?>
                                <span class="h5 <?= $diff >= 0 ? 'text-success' : 'text-danger'; ?>">
                                  <?= $diff >= 0 ? '+' : ''; ?>$<?= number_format($diff, 0, ',', '.'); ?>
                                </span>
                              </div>
                            </div>
                          </div>
                        <?php endif; ?>

                        <?php if (($logData['detalle_recalculo'])): ?>
                          <hr>
                          <h6><i class="fas fa-calculator"></i> Detalle del recálculo</h6>
                          <div class="alert alert-info">
                            <pre
                              style="max-height: 200px; overflow-y: auto; text-align: start;"><?= print_r($logData['detalle_recalculo'], true); ?></pre>
                          </div>
                        <?php endif; ?>

                        <hr>
                        <h6><i class="fas fa-code"></i> Log completo</h6>
                        <div class="alert alert-secondary">
                          <pre style="max-height: 300px; overflow-y: auto; font-size: 11px; text-align: start;"><?=
                                                                                                                is_string($log->log_log) ? htmlentities($log->log_log) : htmlentities(print_r($log->log_log, true));
                                                                                                                ?></pre>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                      </div>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Paginación inferior -->
  <div align="center">
    <ul class="pagination justify-content-center">
      <?php
      if ($this->totalpages > 1) {
        if ($this->page != 1)
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page - 1) . $paramString . '"> &laquo; Anterior </a></li>';

        $min = max(1, $this->page - 5);
        $max = min($this->totalpages, $this->page + 5);

        for ($i = $min; $i <= $max; $i++) {
          if ($this->page == $i)
            echo '<li class="active page-item"><a class="page-link">' . $this->page . '</a></li>';
          else
            echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $i . $paramString . '">' . $i . '</a></li>';
        }

        if ($this->page != $this->totalpages)
          echo '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . ($this->page + 1) . $paramString . '">Siguiente &raquo;</a></li>';
      }
      ?>
    </ul>
  </div>
</div>

<style>
  .alert-sm {
    padding: 0.25rem 0.5rem;
    margin-bottom: 0.25rem;
    font-size: 0.75rem;
  }

  .badge {
    font-size: 0.75em;
    padding: 0.5em 0.75em;
  }

  pre {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    padding: 0.5rem;
    font-size: 0.875rem;
  }
</style>