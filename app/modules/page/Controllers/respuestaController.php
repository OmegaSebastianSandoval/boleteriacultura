<?php

/**
 *
 */

class Page_respuestaController extends Page_mainController
{

    public function indexAction()
    {
        //print_r($this->socio);
        $reservaCreada = Session::getInstance()->get('reservacreada');
        if ($reservaCreada == "oklogout") {
            // header('Location: /page/login/logout');
            // return;
        }

        //se crea esta variable de sesion para que el socio si se devuelva, haga un cierre se sesion para evitar conflictos
        Session::getInstance()->set('reservacreada', "oklogout");

        $id = $this->_getDecryptedParam("id");
        $error = $this->_getSanitizedParam("error");
        $this->_view->id = $id; // id descifrado disponible para la vista

        if ($error) {
            $this->_view->error = $error;
            return;
        }

        if (!$id) {
            $this->_view->error = 'no_reservation_id';
            return;
        }

        // Obtener datos de la reserva
        $reservasModel = new Administracion_Model_DbTable_Reservas();
        $eventosModel = new Administracion_Model_DbTable_Eventos();
        $invitadosModel = new Administracion_Model_DbTable_Invitadosreservas();
        $mesasModel = new Administracion_Model_DbTable_Mesas();

        $this->_view->reserva = $reserva = $reservasModel->getById($id);

        if (!$reserva) {
            $this->_view->error = 'reservation_not_found';
            return;
        }

        // Extender la sesión para dar tiempo a registrar invitados, anclada a la fecha
        // de pago (no a "ahora"): así, si el socio recarga o vuelve a entrar a esta
        // página, el conteo no se reinicia cada vez, sino que sigue corriendo hacia el
        // mismo límite (fecha de pago + 60 minutos).
        $sesionModel = new Administracion_Model_DbTable_Accionsesiones();
        $sesionId = $this->sesionInfo->accion_sesion_id;
        if ($sesionId) {
            $anclaFecha = $reserva->reserva_fecha_pago ?: date('Y-m-d H:i:s');
            $fechaLimiteSesion = date('Y-m-d H:i:s', strtotime($anclaFecha . ' +60 minutes'));
            $sesionModel->editField($sesionId, 'accion_sesion_fecha_fin', $fechaLimiteSesion);
            $sesionUpdated = $sesionModel->getById($sesionId);
            Session::getInstance()->set('sesion', $sesionUpdated);
        }

        // Verificación de propiedad: si hay un socio en sesión, la reserva debe ser suya.
        // (Se es tolerante si no hubiera sesión para no romper el aterrizaje del pago.)
        $socioSesion = $this->consultarSocioSession();
        if ($socioSesion && $socioSesion->SBE_CODI && $reserva->reserva_documento != $socioSesion->SBE_CODI) {
            $this->_view->reserva = null;
            $this->_view->error = 'reservation_not_found';
            return;
        }

        // Obtener información del evento (siempre será ID 1 según mencionas)
        $this->_view->evento = $evento = $eventosModel->getById(1);

        // Obtener información del socio
        $socio = $this->consultarSocioSession();

        // Obtener todos los invitados de la reserva
        $invitados = $invitadosModel->getList("reserva_id_reserva = '$id'", "");

        // Obtener información de la mesa si existe
        $mesaInfo = [];
        $categoria = null;

        if ($reserva->reserva_mesa_id) {
            $idsMesa = explode(',', $reserva->reserva_mesa_id);

            // Modelos para ambiente, piso y categoría (solo una vez fuera del loop)
            $ambientesModel = new Administracion_Model_DbTable_Ambientes();
            $pisosModel = new Administracion_Model_DbTable_Pisos();
            $categoriasModel = new Administracion_Model_DbTable_Categorias();

            foreach ($idsMesa as $valorMesa) {
                $mesa = $mesasModel->getById(trim($valorMesa));
                if ($mesa) {
                    $ambiente = $ambientesModel->getById($mesa->mesa_ambiente);
                    if ($ambiente) {
                        $mesa->ambiente_nombre = $ambiente->ambiente_nombre;
                        $mesa->ambiente_descuento = $ambiente->ambiente_descuento;
                        $mesa->ambiente_precio_silla = $ambiente->ambiente_precio_silla ?? null;
                        $piso = $pisosModel->getById($ambiente->ambiente_piso);
                        if ($piso) {
                            $mesa->piso_nombre = $piso->piso_nombre;
                        }

                        // Solo obtenemos una vez la categoría (asumiendo que es la misma para todas)
                        if (!$categoria) {
                            $categoria = $categoriasModel->getById($ambiente->ambiente_categoria);
                        }
                    }

                    $mesaInfo[] = $mesa; // Guardar en array
                }
            }
        }

        $this->_view->mesaInfo = $mesaInfo;
        $this->_view->categoria = $categoria;

        // Si la reserva es de sillas individuales, el precio usa las tarifas de silla
        // de la categoría (categoria_precio_silla_*), no las de mesa. Ver el mismo
        // esquema en eventoController::resumenAction().
        $esModoSilla = !empty($mesaInfo) && ($mesaInfo[0]->mesa_tipo === 'silla');
        $this->_view->esModoSilla = $esModoSilla;

        // Calcular precios para cada invitado
        $totalGeneral = 0;
        $idxSilla = 0;

        foreach ($invitados as $invitado) {
            // Verificar si es el socio principal
            if ($socio && $invitado->documento_invitado === $socio->SBE_CODI) {
                $invitado->es_socio_principal = true;
            } else {
                $invitado->es_socio_principal = false;
            }

            $esBeneficiarioHijoMenor25 =
                (isset($invitado->menor25) && $invitado->menor25) ||
                ((isset($invitado->invitadoReserva_beneficiario_menor25) && $invitado->invitadoReserva_beneficiario_menor25) &&
                    (isset($invitado->invitadoReserva_beneficiario_hijo) && $invitado->invitadoReserva_beneficiario_hijo));

            // Determinar tipo de participante para mostrar (independiente de que haya categoría/mesa)
            if ($invitado->invitado_tipo == '1') { // Asociado/Beneficiario
                if ($invitado->es_socio_principal) {
                    $invitado->tipo_participante = 'Titular de la reserva';
                } elseif ($esBeneficiarioHijoMenor25) {
                    $invitado->tipo_participante = $invitado->invitadoReserva_estado_invitado == 'S'
                        ? 'Cosocio Hijo (< 25 años)'
                        : 'Beneficiario Hijo (< 25 años)';
                } else {
                    $invitado->tipo_participante = $invitado->invitadoReserva_estado_invitado == 'S'
                        ? 'Cosocio'
                        : 'Beneficiario';
                }
            } else { // No asociado (invitado)
                $invitado->tipo_participante = 'Invitado';
            }

            // Determinar el precio según el tipo y categoría (solo si hay mesa/categoría asignada)
            $precio = 0;

            if ($esModoSilla) {
                $silla = isset($mesaInfo[$idxSilla]) ? $mesaInfo[$idxSilla] : null;
                $idxSilla++;

                if ($silla && $silla->mesa_precio !== null && $silla->mesa_precio !== '') {
                    // Override manual: esta silla concreta tiene un precio fijo asignado.
                    $precio = (float) $silla->mesa_precio;
                } elseif ($categoria) {
                    if ($invitado->invitado_tipo == '1') {
                        if ($invitado->es_socio_principal) {
                            $precio = $categoria->categoria_precio_silla_socio;
                        } elseif ($esBeneficiarioHijoMenor25) {
                            $precio = $categoria->categoria_precio_silla_socio_hijo;
                        } else {
                            $precio = $categoria->categoria_precio_silla_socio;
                        }
                    } else {
                        $precio = $categoria->categoria_precio_silla_invitado;
                    }
                    // La categoría no tiene tarifas de silla configuradas: respaldo al
                    // precio general del ambiente.
                    if ($precio === null || $precio === '') {
                        $precio = ($silla && $silla->ambiente_precio_silla !== null && $silla->ambiente_precio_silla !== '')
                            ? (float) $silla->ambiente_precio_silla
                            : 0;
                    }
                } elseif ($silla && $silla->ambiente_precio_silla !== null && $silla->ambiente_precio_silla !== '') {
                    $precio = (float) $silla->ambiente_precio_silla;
                } else {
                    $precio = 0;
                }
            } elseif ($categoria) {
                if ($invitado->invitado_tipo == '1') {
                    if ($invitado->es_socio_principal) {
                        $precio = $categoria->categoria_precio_socio;
                    } elseif ($esBeneficiarioHijoMenor25) {
                        $precio = $categoria->categoria_precio_socio_hijo;
                    } else {
                        $precio = $categoria->categoria_precio_socio;
                    }
                } else {
                    $precio = $categoria->categoria_precio_invitado;
                }
            }

            $invitado->precio_boleta = $precio;
            $totalGeneral += $precio;
        }
        $totalPagar = $totalGeneral;

        $descuento = $mesaInfo[0]->ambiente_descuento ?? 0;
        if ($descuento > 0) {
            $totalConDescuento = $totalGeneral - ($totalGeneral * ($descuento / 100));
            $totalPagar = $totalConDescuento;
        }

        $this->_view->invitados = $invitados;
        $this->_view->totalGeneral = $totalGeneral;
        $this->_view->totalConDescuento = $totalConDescuento;
        $this->_view->totalPagar = $totalPagar;
        $this->_view->descuento = $descuento;


        $this->_view->totalPersonas = count($invitados);

        // Determinar el estado y mensaje según el código
        $estadoInfo = $this->getEstadoInfo($reserva);
        $this->_view->estadoInfo = $estadoInfo;
    }

    /**
     * Obtiene la información del estado de la reserva
     */
    private function getEstadoInfo($reserva)
    {
        $estados = [
            '2' => [
                'codigo' => '2',
                'titulo' => 'Pago por Cargo a la Acción',
                'mensaje' => 'Tu reserva ha sido registrada exitosamente',
                'icono' => 'fas fa-credit-card',
                'color' => 'info',
                'descripcion' => 'El pago será cargado a tu acción del club en ' . ($reserva->reserva_numero_cuotas ?? '1') . ' cuota' . (($reserva->reserva_numero_cuotas ?? 1) > 1 ? 's' : ''),
                'mostrar_cuotas' => true
            ],
            '3' => [
                'codigo' => '3',
                'titulo' => 'Pago Aprobado',
                'mensaje' => 'Tu reserva ha sido confirmada exitosamente',
                'icono' => 'fas fa-check-circle',
                'color' => 'success',
                'descripcion' => 'El pago ha sido procesado correctamente'
            ],
            '4' => [
                'codigo' => '4',
                'titulo' => 'Pago Pendiente de Confirmación',
                'mensaje' => 'Tu pago está siendo procesado',
                'icono' => 'fas fa-clock',
                'color' => 'warning',
                'descripcion' => 'Estamos esperando la confirmación del pago'
            ],
            '5' => [
                'codigo' => '5',
                'titulo' => 'Pago Fallido',
                'mensaje' => 'Hubo un problema con tu pago',
                'icono' => 'fas fa-times-circle',
                'color' => 'danger',
                'descripcion' => 'La transacción fue cancelada o falló'
            ],
            '6' => [
                'codigo' => '6',
                'titulo' => 'Pago Rechazado',
                'mensaje' => 'Tu pago fue rechazado',
                'icono' => 'fas fa-ban',
                'color' => 'danger',
                'descripcion' => 'El pago fue rechazado por la entidad financiera'
            ],
            '11' => [
                'codigo' => '11',
                'titulo' => 'Pago por Débito Automático',
                'mensaje' => 'Tu reserva ha sido registrada exitosamente',
                'icono' => 'fas fa-credit-card',
                'color' => 'info',
                'descripcion' => 'El pago será debitado automáticamente de tu cuenta en ' . ($reserva->reserva_numero_cuotas ?? '1') . ' cuota' . (($reserva->reserva_numero_cuotas ?? 1) > 1 ? 's' : ''),
                'mostrar_cuotas' => true
            ]
        ];

        return $estados[$reserva->reserva_estado] ?? [
            'codigo' => 'unknown',
            'titulo' => 'Estado Desconocido',
            'mensaje' => 'No se pudo determinar el estado de la reserva',
            'icono' => 'fas fa-question-circle',
            'color' => 'secondary',
            'descripcion' => 'Contacta con el servicio al cliente'
        ];
    }
}
