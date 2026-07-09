<h1 class="titulo-principal"><i class="fas fa-history"></i> <?php echo $this->titlesection; ?></h1>
<div class="container-fluid">
  <!-- Filtros -->
  <form action="<?php echo $this->route; ?>/actividad" method="post">
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
          <label>Tipo de Actividad</label>
          <label class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text input-icono fondo-azul"><i class="fas fa-filter"></i></span>
            </div>
            <select class="form-control" name="tipo_accion">
              <option value="">Todas las actividades</option>
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
          <button type="submit" class="btn btn-block btn-azul"> <i class="fas fa-filter"></i> Filtrar Actividad</button>
        </div>
        <div class="col-6">
          <a class="btn btn-block btn-azul-claro" href="<?php echo $this->route; ?>/actividad"> <i
              class="fas fa-eraser"></i>
            Limpiar Filtros</a>
        </div>
      </div>
    </div>
  </form>

  <!-- Navegación entre vistas -->
  <div class="content-dashboard mt-3">
    <div class="row">
      <div class="col-12">
        <div class="nav nav-pills justify-content-center">
          <a class="nav-link active" href="<?php echo $this->route; ?>/actividad">
            <i class="fas fa-history"></i> Vista Resumida
          </a>
          <a class="nav-link" href="<?php echo $this->route; ?>/logs">
            <i class="fas fa-list-alt"></i> Logs Técnicos
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Paginación -->
  <div align="center">
    <ul class="pagination justify-content-center">
      <?php
      $url = $this->route . '/actividad';
      $params = array();
      if ($this->fecha_desde)
        $params[] = "fecha_desde=" . $this->fecha_desde;
      if ($this->fecha_hasta)
        $params[] = "fecha_hasta=" . $this->fecha_hasta;
      if ($this->tipo_accion)
        $params[] = "tipo_accion=" . $this->tipo_accion;
      if ($this->reserva_id)
        $params[] = "reserva_id=" . $this->reserva_id;
      $paramString = ($params) ? '&' . implode('&', $params) : '';

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
          <div class="titulo-registro">Se encontraron <?php echo $this->register_number; ?> actividades</div>
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

    <!-- Timeline de actividades -->
    <div class="activity-timeline">
      <?php
      // Función auxiliar para extraer datos resumidos del log
      function extractSummaryData($log)
      {
        $data = array();

        if (($log->log_log) && is_string($log->log_log)) {
          $cleanJson = str_replace(["\n", "\r", "\t"], ['\\n', '\\r', '\\t'], $log->log_log);
          $jsonDecoded = json_decode($cleanJson, true);
          // echo "---";
// echo "<pre>";
// print_r($jsonDecoded);
// echo "</pre>";
      
          if ($jsonDecoded !== null) {

            // 0. Estructura específica para RECALCULO_RESERVA_POST_ACTUALIZACION
            if ($log->log_tipo === 'RECALCULO_RESERVA_POST_ACTUALIZACION' || $log->log_tipo === 'RECALCULO_POST_ACTUALIZACION') {
              // Extraer reserva_id del nivel superior
              if (($jsonDecoded['reserva_id'])) {
                $data['reserva_id'] = $jsonDecoded['reserva_id'];
              }

              // Extraer datos del invitado de cambios_que_provocaron_recalculo
              if (($jsonDecoded['cambios_que_provocaron_recalculo'])) {
                $cambios = $jsonDecoded['cambios_que_provocaron_recalculo'];

                // Extraer documento del invitado desde los cambios directos
                if (($cambios['documento_invitado']['nuevo'])) {
                  $data['documento_invitado'] = $cambios['documento_invitado']['nuevo'];
                }

                // Extraer información del log_log anidado
                if (($cambios['log_log']['nuevo']) && is_array($cambios['log_log']['nuevo'])) {
                  $logNuevo = $cambios['log_log']['nuevo'];
                  $data['nombre_invitado'] = $logNuevo['invitadoReserva_nombre_invitado'] ?? $data['nombre_invitado'];
                  $data['apellido_invitado'] = $logNuevo['invitadoReserva_apellido_invitado'] ?? $data['apellido_invitado'];
                  $data['documento_invitado'] = $logNuevo['documento_invitado'] ?? $data['documento_invitado'];
                  $data['reserva_id'] = $logNuevo['reserva_id_reserva'] ?? $data['reserva_id'];
                  $data['correo_invitado'] = $logNuevo['invitadoReserva_correo_invitado'] ?? null;
                  $data['telefono'] = $logNuevo['invitadoReserva_telefono'] ?? null;
                  $data['invitado_tipo'] = $logNuevo['invitado_tipo'] ?? null;
                }

                // Extraer información financiera del recálculo
                if (($cambios['recalculo_reserva']['nuevo'])) {
                  $recalculo = $cambios['recalculo_reserva']['nuevo'];
                  $data['valor_anterior'] = $recalculo['valor_anterior'] ?? null;
                  $data['valor_nuevo'] = $recalculo['valor_nuevo'] ?? null;
                  $data['diferencia'] = $recalculo['diferencia'] ?? null;
                }

                // Extraer cambios realizados para mostrar en el detalle
                $data['cambios_realizados'] = array();
                $camposInteres = [
                  'documento_invitado',
                  'invitadoReserva_nombre_invitado',
                  'invitadoReserva_apellido_invitado',
                  'invitadoReserva_correo_invitado',
                  'invitadoReserva_telefono',
                  'invitadoReserva_fecha_nacimiento',
                  'invitadoReserva_estado_invitado',
                  'invitado_tipo',
                  'invitadoReserva_beneficiario_menor25',
                  'invitadoReserva_beneficiario_hijo',
                  'invitadoReserva_beneficiario_principal',
                  'invitadoReserva_beneficiario_cupo',
                  'invitadoReserva_numero_carnet'
                ];

                foreach ($camposInteres as $campo) {
                  if (($cambios[$campo]) && is_array($cambios[$campo])) {
                    $data['cambios_realizados'][$campo] = $cambios[$campo];
                  }
                }
              }

              // Si no encontramos datos del invitado en cambios_que_provocaron_recalculo,
              // intentar extraerlos del invitado_actualizado_id y el detalle del recálculo
              if (!$data['nombre_invitado'] || !$data['documento_invitado']) {
                $invitadoId = $jsonDecoded['invitado_actualizado_id'] ?? null;

                // Buscar en el detalle de invitados del recálculo
                if ($invitadoId && ($jsonDecoded['cambios_que_provocaron_recalculo']['recalculo_reserva']['nuevo']['detalle_invitados'])) {
                  $detalleInvitados = $jsonDecoded['cambios_que_provocaron_recalculo']['recalculo_reserva']['nuevo']['detalle_invitados'];

                  foreach ($detalleInvitados as $invitado) {
                    if (($invitado['id_invitado']) && $invitado['id_invitado'] == $invitadoId) {
                      $data['documento_invitado'] = $invitado['documento'] ?? $data['documento_invitado'];
                      $data['nombre_invitado'] = $invitado['nombre'] ?? $data['nombre_invitado'];
                      break;
                    }
                  }
                }

                // También intentar desde log_log.nuevo
                if (
                  (!$data['nombre_invitado'] || !$data['documento_invitado']) &&
                  ($jsonDecoded['cambios_que_provocaron_recalculo']['log_log']['nuevo'])
                ) {
                  $logData = $jsonDecoded['cambios_que_provocaron_recalculo']['log_log']['nuevo'];

                  if (is_array($logData)) {
                    $data['documento_invitado'] = $logData['documento_invitado'] ?? $data['documento_invitado'];
                    $data['nombre_invitado'] = $logData['invitadoReserva_nombre_invitado'] ?? $data['nombre_invitado'];
                    $data['apellido_invitado'] = $logData['invitadoReserva_apellido_invitado'] ?? $data['apellido_invitado'];
                    $data['reserva_id'] = $logData['reserva_id_reserva'] ?? $data['reserva_id'];
                  }
                }
              }
            }

            // 1. Estructura de INSERT: datos_invitado
            if (($jsonDecoded['datos_invitado'])) {
              $datosInvitado = $jsonDecoded['datos_invitado'];

              $data['documento_invitado'] = $datosInvitado['documento_invitado'] ?? $data['documento_invitado'];
              $data['nombre_invitado'] = $datosInvitado['invitadoReserva_nombre_invitado'] ?? $data['nombre_invitado'];
              $data['apellido_invitado'] = $datosInvitado['invitadoReserva_apellido_invitado'] ?? $data['apellido_invitado'];
              $data['reserva_id'] = $datosInvitado['reserva_id_reserva'] ?? $data['reserva_id'];
              $data['correo_invitado'] = $datosInvitado['invitadoReserva_correo_invitado'] ?? $data['correo_invitado'];
              $data['telefono'] = $datosInvitado['invitadoReserva_telefono'] ?? $data['telefono'];
              $data['fecha_nacimiento'] = $datosInvitado['invitadoReserva_fecha_nacimiento'] ?? $data['fecha_nacimiento'];
              $data['invitado_tipo'] = $datosInvitado['invitado_tipo'] ?? $data['invitado_tipo'];
            }

            // 2. Estructura de UPDATE DETALLADO (la más completa)
            if (($jsonDecoded['datos_nuevos'])) {
              $datosNuevos = $jsonDecoded['datos_nuevos'];

              $data['documento_invitado'] = $datosNuevos['documento_invitado'] ?? $data['documento_invitado'];
              $data['nombre_invitado'] = $datosNuevos['invitadoReserva_nombre_invitado'] ?? $data['nombre_invitado'];
              $data['apellido_invitado'] = $datosNuevos['invitadoReserva_apellido_invitado'] ?? $data['apellido_invitado'];
              $data['reserva_id'] = $datosNuevos['reserva_id_reserva'] ?? $data['reserva_id'];
              $data['correo_invitado'] = $datosNuevos['invitadoReserva_correo_invitado'] ?? $data['correo_invitado'];
              $data['telefono'] = $datosNuevos['invitadoReserva_telefono'] ?? $data['telefono'];
              $data['fecha_nacimiento'] = $datosNuevos['invitadoReserva_fecha_nacimiento'] ?? $data['fecha_nacimiento'];
              $data['invitado_tipo'] = $datosNuevos['invitado_tipo'] ?? $data['invitado_tipo'];
            }

            // 3. Extraer cambios detectados (la información más valiosa)
            if (($jsonDecoded['cambios_detectados']) && empty($data['cambios_realizados'])) {
              $data['cambios_realizados'] = array();
              foreach ($jsonDecoded['cambios_detectados'] as $campo => $cambio) {
                if (is_array($cambio) && ($cambio['anterior']) && ($cambio['nuevo'])) {
                  // Filtrar campos que nos interesan mostrar
                  $camposInteres = [
                    'documento_invitado',
                    'invitadoReserva_nombre_invitado',
                    'invitadoReserva_apellido_invitado',
                    'invitadoReserva_correo_invitado',
                    'invitadoReserva_telefono',
                    'invitadoReserva_estado_invitado',
                    'invitado_tipo',
                    'invitadoReserva_fecha_nacimiento',
                    'invitadoReserva_beneficiario_menor25',
                    'invitadoReserva_beneficiario_hijo',
                    'invitadoReserva_beneficiario_principal'
                  ];

                  if (in_array($campo, $camposInteres)) {
                    $data['cambios_realizados'][$campo] = $cambio;
                  }
                }
              }
            }

            // 4. Extraer información financiera detallada
            if (($jsonDecoded['detalle_recalculo'])) {
              $recalculo = $jsonDecoded['detalle_recalculo'];
              $data['valor_anterior'] = $recalculo['valor_anterior'] ?? null;
              $data['valor_nuevo'] = $recalculo['valor_nuevo'] ?? null;
              $data['diferencia'] = $recalculo['diferencia'] ?? null;
            } elseif (($jsonDecoded['recalculo_reserva'])) {
              $recalculo = $jsonDecoded['recalculo_reserva'];
              $data['valor_anterior'] = $recalculo['valor_anterior'] ?? null;
              $data['valor_nuevo'] = $recalculo['valor_nuevo'] ?? null;
              $data['diferencia'] = $recalculo['diferencia'] ?? null;
            } elseif (($jsonDecoded['recalculo'])) {
              $data['valor_anterior'] = $jsonDecoded['recalculo']['valor_anterior'] ?? null;
              $data['valor_nuevo'] = $jsonDecoded['recalculo']['valor_nuevo'] ?? null;
              $data['diferencia'] = $jsonDecoded['recalculo']['diferencia'] ?? null;
            }

            // 5. Estructura de UPDATE: cambios_que_provocaron_recalculo (fallback)
            if (($jsonDecoded['cambios_que_provocaron_recalculo']) && empty($data['cambios_realizados'])) {
              $cambios = $jsonDecoded['cambios_que_provocaron_recalculo'];

              // Extraer datos del log anidado
              if (($cambios['log_log']['nuevo'])) {
                $logNuevo = $cambios['log_log']['nuevo'];

                if (is_array($logNuevo)) {
                  $data['documento_invitado'] = $logNuevo['documento_invitado'] ?? $data['documento_invitado'];
                  $data['nombre_invitado'] = $logNuevo['invitadoReserva_nombre_invitado'] ?? $data['nombre_invitado'];
                  $data['apellido_invitado'] = $logNuevo['invitadoReserva_apellido_invitado'] ?? $data['apellido_invitado'];
                  $data['reserva_id'] = $logNuevo['reserva_id_reserva'] ?? $data['reserva_id'];
                  $data['correo_invitado'] = $logNuevo['invitadoReserva_correo_invitado'] ?? $data['correo_invitado'];
                  $data['telefono'] = $logNuevo['invitadoReserva_telefono'] ?? $data['telefono'];
                  $data['fecha_nacimiento'] = $logNuevo['invitadoReserva_fecha_nacimiento'] ?? $data['fecha_nacimiento'];
                  $data['invitado_tipo'] = $logNuevo['invitado_tipo'] ?? $data['invitado_tipo'];
                }
              }

              // Extraer información de recálculo de reserva
              if (($cambios['recalculo_reserva']['nuevo']) && !$data['valor_anterior']) {
                $recalculo = $cambios['recalculo_reserva']['nuevo'];
                $data['valor_anterior'] = $recalculo['valor_actual'] ?? null;
                $data['valor_nuevo'] = $recalculo['valor_calculado'] ?? null;
              }

              // Extraer cambios específicos realizados
              if (empty($data['cambios_realizados'])) {
                $data['cambios_realizados'] = array();
                foreach ($cambios as $campo => $cambio) {
                  if (is_array($cambio) && ($cambio['anterior']) && ($cambio['nuevo'])) {
                    $data['cambios_realizados'][$campo] = $cambio;
                  }
                }
              }
            }

            // 6. Fallback a estructura directa en nivel raíz
            if (!$data['documento_invitado']) {
              $data['documento_invitado'] = $jsonDecoded['documento_invitado'] ??
                $jsonDecoded['datos_insertados']['documento_invitado'] ??
                $jsonDecoded['datos_eliminados']['documento_invitado'] ?? null;
            }

            if (!$data['nombre_invitado']) {
              $data['nombre_invitado'] = $jsonDecoded['invitadoReserva_nombre_invitado'] ??
                $jsonDecoded['datos_insertados']['invitadoReserva_nombre_invitado'] ??
                $jsonDecoded['datos_eliminados']['invitadoReserva_nombre_invitado'] ?? null;
            }

            if (!$data['apellido_invitado']) {
              $data['apellido_invitado'] = $jsonDecoded['invitadoReserva_apellido_invitado'] ??
                $jsonDecoded['datos_insertados']['invitadoReserva_apellido_invitado'] ??
                $jsonDecoded['datos_eliminados']['invitadoReserva_apellido_invitado'] ?? null;
            }

            if (!$data['reserva_id']) {
              $data['reserva_id'] = $jsonDecoded['reserva_id_reserva'] ??
                $jsonDecoded['reserva_id_afectada'] ??
                $jsonDecoded['datos_insertados']['reserva_id_reserva'] ??
                $jsonDecoded['datos_eliminados']['reserva_id_reserva'] ??
                $jsonDecoded['reserva_id'] ?? null;
            }

            // Información del usuario que realizó la acción
            $data['usuario_nombre'] = $jsonDecoded['usuario_nombre'] ?? $log->log_usuario ?? 'Sistema';

            // Guardar IP address
            $data['ip_address'] = $jsonDecoded['ip_address'] ?? null;
          }
        }

        return $data;
      }

      // Función para generar descripción amigable de la actividad
      function getActivityDescription($log, $data)
      {
        $usuario = $data['usuario_nombre'] ?? 'Sistema';
        $invitado = $data['nombre_invitado'] ?? 'Invitado';
        $documento = $data['documento_invitado'] ?? 'N/A';
        $reserva = $data['reserva_id'] ?? 'N/A';

        switch ($log->log_tipo) {
          case 'CREAR_NEWINVITADOS':
            return "$usuario agregó un nuevo invitado: $invitado ($documento) a la reserva #$reserva";

          case 'ACTUALIZAR_NEWINVITADOS_DETALLADO':
          case 'EDITAR NEWINVITADOS':
          case 'RECALCULO_RESERVA_POST_ACTUALIZACION':
            // Solo omitir si no hay información del invitado Y tampoco hay cambios realizados
            if (
              (!$invitado || $invitado === 'Invitado') &&
              (!$documento || $documento === 'N/A')
            ) {
              return null;
            }

            $descripcion = "$usuario actualizó la información del invitado: $invitado ($documento) de la reserva #$reserva";

            // Agregar detalles de los cambios si están disponibles
            if (($data['cambios_realizados']) && ($data['cambios_realizados'])) {
              $cambios = array();
              foreach ($data['cambios_realizados'] as $campo => $cambio) {
                $nombreCampo = getNombreCampoAmigable($campo);
                if ($nombreCampo && ($cambio['anterior']) && ($cambio['nuevo'])) {
                  $valorAnterior = getValorAmigable($campo, $cambio['anterior']);
                  $valorNuevo = getValorAmigable($campo, $cambio['nuevo']);
                  $cambios[] = "$nombreCampo: '$valorAnterior' → '$valorNuevo'";
                }
              }

              if (($cambios)) {
                $descripcion .= ". Cambios: " . implode(', ', $cambios);
              }
            }

            return $descripcion;

          case 'ELIMINAR_NEWINVITADOS':
          case 'BORRAR NEWINVITADOS':
            return "$usuario eliminó al invitado: $invitado ($documento) de la reserva #$reserva";

          case 'RECALCULO_RESERVA_POST_ELIMINACION':
            return "Se recalculó automáticamente el valor de la reserva #$reserva después de eliminar un invitado";

          default:
            return "$usuario realizó una acción en la reserva #$reserva";
        }
      }

      // Función para convertir nombres de campos técnicos a nombres amigables
      function getNombreCampoAmigable($campo)
      {
        $nombres = array(
          'invitadoReserva_nombre_invitado' => 'Nombre',
          'invitadoReserva_apellido_invitado' => 'Apellido',
          'documento_invitado' => 'Documento',
          'invitadoReserva_correo_invitado' => 'Email',
          'invitadoReserva_telefono' => 'Teléfono',
          'invitadoReserva_fecha_nacimiento' => 'Fecha Nacimiento',
          'invitado_tipo' => 'Tipo de Invitado',
          'invitadoReserva_estado_invitado' => 'Estado',
          'invitadoReserva_beneficiario_menor25' => 'Beneficiario Menor 25',
          'invitadoReserva_beneficiario_hijo' => 'Beneficiario Hijo',
          'invitadoReserva_beneficiario_principal' => 'Beneficiario Principal',
          'invitadoReserva_beneficiario_cupo' => 'Beneficiario Cupo',
          'invitadoReserva_numero_carnet' => 'Número Carnet',
        );

        return $nombres[$campo] ?? null;
      }

      // Función para convertir valores técnicos a valores amigables
      function getValorAmigable($campo, $valor)
      {
        switch ($campo) {
          case 'invitado_tipo':
            $tipos = [
              '1' => 'Socio',
              '2' => 'Invitado',
            ];
            return $tipos[$valor] ?? "Tipo $valor";

          case 'invitadoReserva_estado_invitado':
            $estados = [
              'P' => 'Invitado',
              'S' => 'Cosocio',
              'A' => 'Socio',

            ];
            return $estados[$valor] ?? $valor;

          case 'invitadoReserva_beneficiario_menor25':
          case 'invitadoReserva_beneficiario_hijo':
          case 'invitadoReserva_beneficiario_principal':
          case 'invitadoReserva_beneficiario_cupo':
          case 'invitadoReserva_numero_carnet':
            return $valor == '1' ? 'Sí' : 'No';

          default:
            return $valor;
        }
      }

      foreach ($this->logs as $key => $log) {
        $logData = extractSummaryData($log);
        // print_r($logData);
        $description = getActivityDescription($log, $logData);

        // Si la descripción es null, omitir este item
        if ($description === null) {
          continue;
        }

        // Determinar el color y icono según el tipo de acción
        $activityClass = '';
        $activityIcon = '';
        $activityColor = '';

        switch ($log->log_tipo) {
          case 'CREAR_NEWINVITADOS':
            $activityClass = 'timeline-success';
            $activityIcon = 'fas fa-user-plus';
            $activityColor = '#28a745';
            break;
          case 'ACTUALIZAR_NEWINVITADOS_DETALLADO':
          case 'EDITAR NEWINVITADOS':
            $activityClass = 'timeline-warning';
            $activityIcon = 'fas fa-user-edit';
            $activityColor = '#ffc107';
            break;
          case 'ELIMINAR_NEWINVITADOS':
          case 'BORRAR NEWINVITADOS':
            $activityClass = 'timeline-danger';
            $activityIcon = 'fas fa-user-minus';
            $activityColor = '#dc3545';
            break;
          case 'RECALCULO_RESERVA_POST_ELIMINACION':
          case 'RECALCULO_RESERVA_POST_ACTUALIZACION':
            $activityClass = 'timeline-info';
            $activityIcon = 'fas fa-calculator';
            $activityColor = '#17a2b8';
            break;
          default:
            $activityClass = 'timeline-secondary';
            $activityIcon = 'fas fa-info-circle';
            $activityColor = '#6c757d';
        }
        ?>

        <div class="timeline-item <?= $activityClass; ?>">
          <div class="timeline-marker" style="background-color: <?= $activityColor; ?>">
            <i class="<?= $activityIcon; ?>"></i>
          </div>
          <div class="timeline-content">
            <div class="timeline-header">
              <h6 class="timeline-title"><?= $this->tipos_accion[$log->log_tipo] ?? $log->log_tipo; ?></h6>
              <small class="timeline-date">
                <i class="fas fa-clock"></i>
                <?= date('d/m/Y H:i', strtotime($log->log_fecha ?? date('Y-m-d H:i:s'))); ?>
                <?php if ($logData['ip_address']): ?>
                  <br><i class="fas fa-map-marker-alt"></i> IP: <?= htmlspecialchars($logData['ip_address']); ?>
                <?php endif; ?>
              </small>
            </div>
            <div class="timeline-body">
              <p class="timeline-description"><?= htmlspecialchars($description); ?></p>

              <?php if ($logData['documento_invitado'] || $logData['correo_invitado'] || $logData['telefono'] || $logData['fecha_nacimiento'] || $logData['invitado_tipo']): ?>
                <div class="timeline-details">
                  <div class="row">
                    <?php if ($logData['documento_invitado']): ?>
                      <div class="col-md-3">
                        <small class="text-muted">
                          <strong>Documento:</strong> <?= htmlspecialchars($logData['documento_invitado']); ?>
                        </small>
                      </div>
                    <?php endif; ?>
                    <?php if ($logData['correo_invitado']): ?>
                      <div class="col-md-3">
                        <small class="text-muted">
                          <strong>Email:</strong> <?= htmlspecialchars($logData['correo_invitado']); ?>
                        </small>
                      </div>
                    <?php endif; ?>
                    <?php if ($logData['telefono']): ?>
                      <div class="col-md-3">
                        <small class="text-muted">
                          <strong>Teléfono:</strong> <?= htmlspecialchars($logData['telefono']); ?>
                        </small>
                      </div>
                    <?php endif; ?>
                    <?php if ($logData['fecha_nacimiento']): ?>
                      <div class="col-md-3">
                        <small class="text-muted">
                          <strong>F. Nacimiento:</strong> <?= date('d/m/Y', strtotime($logData['fecha_nacimiento'])); ?>
                        </small>
                      </div>
                    <?php endif; ?>
                  </div>
                  <?php if ($logData['invitado_tipo']): ?>
                    <div class="row mt-2">
                      <div class="col-12">
                        <small class="text-muted">
                          <strong>Tipo de Invitado:</strong>
                          <?php
                          $tipos_invitado = [
                            1 => 'Adulto',
                            2 => 'Menor',
                            3 => 'Bebé',
                            4 => 'Senior'
                          ];
                          echo $tipos_invitado[$logData['invitado_tipo']] ?? 'Tipo ' . $logData['invitado_tipo'];
                          ?>
                        </small>
                      </div>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <?php
              // Mostrar cambios específicos para actualizaciones
              if (($logData['cambios_realizados']) && ($logData['cambios_realizados'])): ?>
                <div class="timeline-changes">
                  <h6 class="changes-title"><i class="fas fa-exchange-alt"></i> Cambios Realizados:</h6>
                  <!-- <?php
                  // print_r($logData['cambios_realizados']);
                  ?> -->
                  <div class="changes-list">
                    <?php foreach ($logData['cambios_realizados'] as $campo => $cambio): ?>

                      <?php
                      $nombreCampo = getNombreCampoAmigable($campo);
                      //   echo $nombreCampo;
                      //   echo "<br>";
                      //   echo $cambio['anterior'];
                      //   echo "<br>";
                      //   echo $cambio['nuevo'];
                      //   echo "<br>";
                
                      if (
                        $nombreCampo &&
                        ($cambio['anterior'] ||
                          $cambio['nuevo'])
                      ): ?>
                        <div class="change-item">
                          <strong><?= $nombreCampo; ?>:</strong>
                          <span class="change-from"><?= ($cambio['anterior']); ?></span>
                          <i class="fas fa-arrow-right"></i>
                          <span class="change-to"><?= ($cambio['nuevo']); ?></span>
                        </div>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endif; ?>

              <?php
              // Mostrar impacto financiero si hay valores
              $valorAnterior = is_numeric($logData['valor_anterior']) ? (float) $logData['valor_anterior'] : null;
              $valorNuevo = is_numeric($logData['valor_nuevo']) ? (float) $logData['valor_nuevo'] : null;

              if ($valorAnterior !== null && $valorNuevo !== null):
                $diferencia = $valorNuevo - $valorAnterior;
                ?>
                <div class="timeline-financial">
                  <div class="alert alert-info alert-sm">
                    <i class="fas fa-dollar-sign"></i>
                    <strong>Impacto Financiero:</strong>
                    Reserva cambió de $<?= number_format($valorAnterior, 0, ',', '.'); ?>
                    a $<?= number_format($valorNuevo, 0, ',', '.'); ?>
                    <span class="<?= $diferencia >= 0 ? 'text-success' : 'text-danger'; ?>">
                      (<?= $diferencia >= 0 ? '+' : ''; ?>$<?= number_format($diferencia, 0, ',', '.'); ?>)
                    </span>
                  </div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      <?php } ?>

      <?php if (empty($this->logs)): ?>
        <div class="text-center py-5">
          <i class="fas fa-history fa-3x text-muted mb-3"></i>
          <h5 class="text-muted">No se encontraron actividades</h5>
          <p class="text-muted">Intenta ajustar los filtros para ver más resultados.</p>
        </div>
      <?php endif; ?>
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
  /* Estilos para el timeline de actividades */
  .activity-timeline {
    position: relative;
    padding: 20px 0;
  }

  .activity-timeline::before {
    content: '';
    position: absolute;
    top: 0;
    left: 30px;
    height: 100%;
    width: 2px;
    background: #e9ecef;
  }

  .timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 70px;
  }

  .timeline-marker {
    position: absolute;
    left: 20px;
    top: 5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 10px;
    z-index: 1;
  }

  .timeline-content {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    border-bottom: 1px solid #f8f9fa;
    padding-bottom: 10px;
  }

  .timeline-title {
    margin: 0;
    color: #495057;
    font-weight: 600;
  }

  .timeline-date {
    color: #6c757d;
    font-size: 0.85em;
  }

  .timeline-description {
    margin-bottom: 15px;
    color: #212529;
    line-height: 1.5;
  }

  .timeline-details {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 15px;
  }

  .timeline-financial .alert-sm {
    padding: 10px 15px;
    margin-bottom: 0;
    font-size: 0.9em;
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .activity-timeline::before {
      left: 15px;
    }

    .timeline-item {
      padding-left: 50px;
    }

    .timeline-marker {
      left: 5px;
    }

    .timeline-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .timeline-date {
      margin-top: 5px;
    }
  }

  /* Navigation pills */
  .nav-pills .nav-link {
    border-radius: 25px;
    margin: 0 10px;
    font-weight: 500;
  }

  .nav-pills .nav-link.active {
    background-color: #007bff;
  }

  .nav-pills .nav-link:not(.active) {
    color: #007bff;
    background-color: #f8f9fa;
  }

  .nav-pills .nav-link:not(.active):hover {
    background-color: #e9ecef;
  }

  /* Estilos para los cambios realizados */
  .timeline-changes {
    background: #fff8dc;
    border: 1px solid #ffeaa7;
    border-radius: 6px;
    padding: 15px;
    margin-top: 15px;
  }

  .changes-title {
    color: #6c5ce7;
    margin-bottom: 10px;
    font-size: 0.9em;
    font-weight: 600;
  }

  .changes-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .change-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85em;
    padding: 5px 0;
    border-bottom: 1px solid #f0f0f0;
  }

  .change-item:last-child {
    border-bottom: none;
  }

  .change-from {
    background: #ffe4e1;
    color: #dc3545;
    padding: 2px 6px;
    border-radius: 3px;
    font-weight: 500;
  }

  .change-to {
    background: #d4edda;
    color: #28a745;
    padding: 2px 6px;
    border-radius: 3px;
    font-weight: 500;
  }

  .change-item .fas.fa-arrow-right {
    color: #6c757d;
    font-size: 0.8em;
  }
</style>