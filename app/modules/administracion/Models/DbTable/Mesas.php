<?php

/**
 * clase que genera la insercion y edicion  de mesa en la base de datos
 */
class Administracion_Model_DbTable_Mesas extends Db_Table
{
	/**
	 * [ nombre de la tabla actual]
	 * @var string
	 */
	protected $_name = 'mesas';

	/**
	 * [ identificador de la tabla actual en la base de datos]
	 * @var string
	 */
	protected $_id = 'mesa_id';

	/**
	 * insert recibe la informacion de un mesa y la inserta en la base de datos
	 * @param  array Array array con la informacion con la cual se va a realizar la insercion en la base de datos
	 * @return integer      identificador del  registro que se inserto
	 */
	public function insert($data)
	{
		$mesa_ambiente = $data['mesa_ambiente'];
		$mesa_codigo = $data['mesa_codigo'];
		$mesa_nombre = $data['mesa_nombre'];
		$mesa_capacidad = $data['mesa_capacidad'];
		$mesa_forma = $data['mesa_forma'];
		$mesa_estado = $data['mesa_estado'] ?? '0';
		$mesa_activa = $data['mesa_activa'];
		$orden = $data['orden'] ?? 0;
		$mesa_imagen_disponible = $data['mesa_imagen_disponible'];
		$mesa_imagen_pendiente = $data['mesa_imagen_pendiente'];
		$mesa_imagen_ocupada = $data['mesa_imagen_ocupada'];
		$mesa_imagen_ubicacion_en_ambiente = $data['mesa_imagen_ubicacion_en_ambiente'];
		$mesa_imagen_ubicacion_en_piso = $data['mesa_imagen_ubicacion_en_piso'];
		$mesa_pos_x = $data['mesa_pos_x'];
		$mesa_pos_y = $data['mesa_pos_y'];
		$mesa_tipo = $data['mesa_tipo'];
		$mesa_ancho = $data['mesa_ancho'];
		$mesa_alto = $data['mesa_alto'];
		$mesa_rotacion = $data['mesa_rotacion'];
		$mesa_provision = $data['mesa_provision'];
		// mesa_precio: solo aplica a sillas. Se castea a float y se emite literal NULL si viene vacío.
		$mesa_precio_sql = (isset($data['mesa_precio']) && $data['mesa_precio'] !== '' && $data['mesa_precio'] !== null)
			? "'" . (float) $data['mesa_precio'] . "'"
			: "NULL";
		$query = "INSERT INTO mesas( mesa_ambiente, mesa_codigo, mesa_nombre, mesa_capacidad, mesa_precio, mesa_forma, mesa_estado, mesa_activa, orden, mesa_imagen_disponible, mesa_imagen_pendiente, mesa_imagen_ocupada, mesa_imagen_ubicacion_en_ambiente, mesa_imagen_ubicacion_en_piso, mesa_pos_x, mesa_pos_y, mesa_tipo, mesa_ancho, mesa_alto, mesa_rotacion, mesa_provision) VALUES ( '$mesa_ambiente', '$mesa_codigo', '$mesa_nombre', '$mesa_capacidad', $mesa_precio_sql, '$mesa_forma', '$mesa_estado', '$mesa_activa', '$orden', '$mesa_imagen_disponible', '$mesa_imagen_pendiente', '$mesa_imagen_ocupada', '$mesa_imagen_ubicacion_en_ambiente', '$mesa_imagen_ubicacion_en_piso', '$mesa_pos_x', '$mesa_pos_y', '$mesa_tipo', '$mesa_ancho', '$mesa_alto', '$mesa_rotacion', '$mesa_provision')";
		$res = $this->_conn->query($query);
		return mysqli_insert_id($this->_conn->getConnection());
	}

	/**
	 * update Recibe la informacion de un mesa  y actualiza la informacion en la base de datos
	 * @param  array Array Array con la informacion con la cual se va a realizar la actualizacion en la base de datos
	 * @param  integer    identificador al cual se le va a realizar la actualizacion
	 * @return void
	 */
	public function update($data, $id)
	{
		$mesa_ambiente = $data['mesa_ambiente'];
		$mesa_codigo = $data['mesa_codigo'];
		$mesa_nombre = $data['mesa_nombre'];
		$mesa_capacidad = $data['mesa_capacidad'];
		$mesa_forma = $data['mesa_forma'];
		$mesa_estado = $data['mesa_estado'] ?? '0';
		$mesa_activa = $data['mesa_activa'];
		$orden = $data['orden'] ?? 0;
		$mesa_imagen_disponible = $data['mesa_imagen_disponible'];
		$mesa_imagen_pendiente = $data['mesa_imagen_pendiente'];
		$mesa_imagen_ocupada = $data['mesa_imagen_ocupada'];
		$mesa_imagen_ubicacion_en_ambiente = $data['mesa_imagen_ubicacion_en_ambiente'];
		$mesa_imagen_ubicacion_en_piso = $data['mesa_imagen_ubicacion_en_piso'];
		$mesa_pos_x = $data['mesa_pos_x'];
		$mesa_pos_y = $data['mesa_pos_y'];
		$mesa_tipo = $data['mesa_tipo'];
		$mesa_ancho = $data['mesa_ancho'];
		$mesa_alto = $data['mesa_alto'];
		$mesa_rotacion = $data['mesa_rotacion'];
		$mesa_provision = $data['mesa_provision'];
		// mesa_precio: solo se toca si la clave viene en $data (así un guardado de reposición
		// por drag, que no la envía, no borra el precio existente de una silla).
		$mesa_precio_set = "";
		if (array_key_exists('mesa_precio', $data)) {
			$mesa_precio_set = ($data['mesa_precio'] !== '' && $data['mesa_precio'] !== null)
				? ", mesa_precio = '" . (float) $data['mesa_precio'] . "'"
				: ", mesa_precio = NULL";
		}
		$query = "UPDATE mesas SET  mesa_ambiente = '$mesa_ambiente', mesa_codigo = '$mesa_codigo', mesa_nombre = '$mesa_nombre', mesa_capacidad = '$mesa_capacidad'$mesa_precio_set, mesa_forma = '$mesa_forma', mesa_estado = '$mesa_estado', mesa_activa = '$mesa_activa', orden = '$orden', mesa_imagen_disponible = '$mesa_imagen_disponible', mesa_imagen_pendiente = '$mesa_imagen_pendiente', mesa_imagen_ocupada = '$mesa_imagen_ocupada', mesa_imagen_ubicacion_en_ambiente = '$mesa_imagen_ubicacion_en_ambiente', mesa_imagen_ubicacion_en_piso = '$mesa_imagen_ubicacion_en_piso', mesa_pos_x = '$mesa_pos_x', mesa_pos_y = '$mesa_pos_y', mesa_tipo = '$mesa_tipo', mesa_ancho = '$mesa_ancho', mesa_alto = '$mesa_alto', mesa_rotacion = '$mesa_rotacion', mesa_provision = '$mesa_provision' WHERE mesa_id = '" . $id . "'";
		$res = $this->_conn->query($query);
	}

	/**
	 * getMesasConDetalles - Obtiene mesas con información completa de piso, ambiente y categoría
	 * @param string $filtros - Condiciones WHERE adicionales
	 * @param string $orden - Ordenamiento de los resultados
	 * @return array - Array con mesas y sus detalles relacionados
	 */
	public function getMesasConDetalles($filtros = "1 = 1", $orden = "p.piso_nombre ASC, a.ambiente_nombre ASC, m.mesa_nombre ASC")
	{
		$query = "
			SELECT 
				m.*,
				p.piso_id,
				p.piso_nombre,
				p.piso_color,
				p.piso_estado as piso_estado,
				a.ambiente_id,
				a.ambiente_nombre,
				a.ambiente_categoria,
				a.ambiente_descuento,
				a.ambiente_precio_silla,
				a.ambiente_estado as ambiente_estado,
				c.categoria_id,
				c.categoria_nombre,
				c.categoria_descripcion
			FROM mesas m
			LEFT JOIN ambientes a ON m.mesa_ambiente = a.ambiente_id
			LEFT JOIN pisos p ON a.ambiente_piso = p.piso_id
			LEFT JOIN categorias c ON a.ambiente_categoria = c.categoria_id
			WHERE $filtros
			ORDER BY $orden
		";

		return $this->_conn->query($query)->fetchAsObject();
	}

	/**
	 * getPisosDisponibles - Obtiene pisos que tienen mesas disponibles con la capacidad especificada
	 * @param int $capacidad - Capacidad requerida
	 * @return array - Array con pisos disponibles
	 */
	public function getPisosDisponibles($capacidad)
	{
		$query = "
			SELECT DISTINCT 
				p.piso_id,
				p.piso_nombre,
				p.piso_color,
				COUNT(m.mesa_id) as total_mesas
			FROM pisos p
			INNER JOIN ambientes a ON p.piso_id = a.ambiente_piso
			INNER JOIN mesas m ON a.ambiente_id = m.mesa_ambiente
			WHERE ((m.mesa_estado = '' OR m.mesa_estado = '0' OR m.mesa_estado IS NULL) AND m.mesa_activa = '1')
			AND (m.mesa_provision IS NULL OR m.mesa_provision = '')
			AND m.mesa_capacidad = '$capacidad'
			AND (p.piso_estado = '1' OR p.piso_estado = 1)
			AND (a.ambiente_estado = '1' OR a.ambiente_estado = 1)
			GROUP BY p.piso_id, p.piso_nombre, p.piso_color
			ORDER BY p.piso_nombre ASC
		";

		return $this->_conn->query($query)->fetchAsObject();
	}

	/**
	 * getAmbientesPorPiso - Obtiene ambientes de un piso específico que tienen mesas disponibles
	 * @param int $pisoId - ID del piso
	 * @param int $capacidad - Capacidad requerida
	 * @return array - Array con ambientes disponibles
	 */
	public function getAmbientesPorPiso($pisoId, $capacidad)
	{
		$socio = $_SESSION['socio'];
		$documento = $socio->SBE_CODI;
		$query = "
		SELECT
		    a.ambiente_id,
		    a.ambiente_nombre,
		    a.ambiente_categoria,
		    a.ambiente_fecha_partido,
		    c.categoria_nombre,
		    c.categoria_descripcion,
		    COALESCE(md.total_mesas, 0) AS total_mesas
		FROM ambientes a
		LEFT JOIN categorias c ON a.ambiente_categoria = c.categoria_id
		LEFT JOIN (
		    SELECT
		        m.mesa_ambiente,
		        COUNT(DISTINCT m.mesa_id) AS total_mesas
		    FROM mesas m
		    LEFT JOIN reservas r ON FIND_IN_SET(m.mesa_id, r.reserva_mesa_id)
		    WHERE ((m.mesa_estado = '' OR m.mesa_estado = '0' OR m.mesa_estado IS NULL)
		           AND (m.mesa_provision IS NULL OR m.mesa_provision = '')
		           OR (r.reserva_documento = '$documento' AND r.reserva_estado IN (1, 4, 7)))
		      AND m.mesa_capacidad = '$capacidad'
		      AND m.mesa_activa = '1'
		      AND m.mesa_tipo = 'mesa'
		    GROUP BY m.mesa_ambiente
		) AS md ON a.ambiente_id = md.mesa_ambiente
		WHERE a.ambiente_piso = '$pisoId'
		  AND a.ambiente_estado = '1'
		ORDER BY c.categoria_nombre ASC, a.ambiente_nombre ASC
		";

		return $this->_conn->query($query)->fetchAsObject();
	}

	public function getAmbientesPorPisoold($pisoId, $capacidad)
	{
		$socio = $_SESSION['socio'];
		$documento = $socio->SBE_CODI;
		$query = "
			SELECT DISTINCT 
				a.ambiente_id,
				a.ambiente_nombre,
				a.ambiente_categoria,
				c.categoria_nombre,
				c.categoria_descripcion,
				COUNT(m.mesa_id) as total_mesas
			FROM ambientes a
			INNER JOIN mesas m ON a.ambiente_id = m.mesa_ambiente
			LEFT JOIN categorias c ON a.ambiente_categoria = c.categoria_id
			LEFT JOIN reservas r ON
    (FIND_IN_SET(m.mesa_id, r.reserva_mesa_id) OR (r.reserva_mesa_id IS NULL))
			WHERE a.ambiente_piso = '$pisoId'
			AND (m.mesa_estado = '' OR m.mesa_estado = '0' OR m.mesa_estado IS NULL OR r.reserva_documento = '$documento')
			AND m.mesa_capacidad = '$capacidad'
			AND (a.ambiente_estado = '1' OR a.ambiente_estado = 1)
			GROUP BY a.ambiente_id, a.ambiente_nombre, a.ambiente_categoria, c.categoria_nombre, c.categoria_descripcion
			ORDER BY c.categoria_nombre ASC, a.ambiente_nombre ASC
		";

		return $this->_conn->query($query)->fetchAsObject();
	}

	/**
	 * getMesasPorAmbiente - Obtiene mesas disponibles de un ambiente específico
	 * @param int $ambienteId - ID del ambiente
	 * @param int $capacidad - Capacidad requerida
	 * @return array - Array con mesas disponibles
	 */
	public function getMesasPorAmbiente($ambienteId, $capacidad)
	{
		// Se devuelven TODAS las mesas activas del ambiente/capacidad (libres u ocupadas,
		// sin importar qué socio las reservó), para que el mapa pueda pintar en rojo las
		// mesas ya tomadas por cualquier otro socio y no solo las de la sesión actual.
		$query = "
			SELECT
				m.*,
				CASE WHEN (m.mesa_provision IS NOT NULL AND m.mesa_provision != '') THEN 1 ELSE m.mesa_estado END AS mesa_estado,
				a.ambiente_nombre,
				p.piso_nombre
			FROM mesas m
			INNER JOIN ambientes a ON m.mesa_ambiente = a.ambiente_id
			INNER JOIN pisos p ON a.ambiente_piso = p.piso_id
			WHERE m.mesa_ambiente = '$ambienteId'
			AND m.mesa_capacidad = '$capacidad'
			AND m.mesa_activa = '1'
			ORDER BY m.mesa_nombre ASC
		";

		return $this->_conn->query($query)->fetchAsObject();
	}
	public function getMesasPorAmbiente_OLD($ambienteId, $capacidad)
	{
		$socio = $_SESSION['socio'];
		$documento = $socio->SBE_CODI;
		$query = "
			SELECT 
				m.*,
				a.ambiente_nombre,
				p.piso_nombre
			FROM mesas m
			INNER JOIN ambientes a ON m.mesa_ambiente = a.ambiente_id
			INNER JOIN pisos p ON a.ambiente_piso = p.piso_id
			LEFT JOIN reservas r ON
    (FIND_IN_SET(m.mesa_id, r.reserva_mesa_id) OR (r.reserva_mesa_id IS NULL))
			WHERE m.mesa_ambiente = '$ambienteId'
			AND (m.mesa_estado = '' OR m.mesa_estado = '0' OR m.mesa_estado IS NULL OR r.reserva_documento = '$documento')
			AND m.mesa_capacidad = '$capacidad'
			ORDER BY m.mesa_nombre ASC
		";

		return $this->_conn->query($query)->fetchAsObject();
	}

	/**
	 * updateEstado - Actualiza solo el estado de una mesa
	 * @param string $estado - Nuevo estado de la mesa
	 * @param int $id - ID de la mesa
	 * @return void
	 */
	public function updateEstado($estado, $id)
	{
		$query = "UPDATE mesas SET mesa_estado = '$estado' WHERE mesa_id = '$id'";
		$this->_conn->query($query);
	}

	public function updateEstadoAtomico($estado, $id)
	{
		$id = intval($id);
		$estado = intval($estado);
		if ($estado === 1) {
			$fecha = date('Y-m-d H:i:s');
			$query = "UPDATE mesas SET mesa_estado = '$estado', mesa_fecha_bloqueo = '$fecha' WHERE mesa_id = '$id' AND (mesa_estado = '0' OR mesa_estado = '' OR mesa_estado IS NULL) AND (mesa_provision IS NULL OR mesa_provision = '')";
		} else {
			$query = "UPDATE mesas SET mesa_estado = '$estado' WHERE mesa_id = '$id' AND (mesa_estado = '0' OR mesa_estado = '' OR mesa_estado IS NULL)";
		}
		$this->_conn->query($query);
	}

	/**
	 * Obtiene las mesas que llevan bloqueadas (mesa_estado = 1) desde antes de la fecha límite dada.
	 * Solo considera mesas que tienen mesa_fecha_bloqueo registrado (bloqueadas por el flujo de reserva).
	 * @param string $limiteFecha Fecha límite en formato 'Y-m-d H:i:s'
	 * @return array
	 */
	public function getMesasBloqueadasVencidas($limiteFecha)
	{
		$query = "SELECT * FROM mesas WHERE mesa_estado = '1' AND mesa_fecha_bloqueo IS NOT NULL AND mesa_fecha_bloqueo <= '$limiteFecha'";
		$result = $this->_conn->query($query);
		return $result ? $result->fetchAsObject() : [];
	}

	/**
	 * Libera una mesa vencida: la marca disponible y limpia su fecha de bloqueo.
	 * @param int $id
	 * @return void
	 */
	public function liberarMesaVencida($id)
	{
		$id = intval($id);
		$query = "UPDATE mesas SET mesa_estado = '0', mesa_fecha_bloqueo = NULL WHERE mesa_id = '$id'";
		$this->_conn->query($query);
	}

	/**
	 * Limpia solo la fecha de bloqueo (SQL NULL real) sin tocar el estado de la mesa.
	 * Usado para mesas de reservas pagadas: quedan ocupadas pero fuera del barrido de limpieza.
	 * @param int $id
	 * @return void
	 */
	public function limpiarFechaBloqueo($id)
	{
		$id = intval($id);
		$query = "UPDATE mesas SET mesa_fecha_bloqueo = NULL WHERE mesa_id = '$id'";
		$this->_conn->query($query);
	}

	public function getConnection()
	{
		return $this->_conn->getConnection();
	}

	public function getListMesasDisponibles()
	{
			$query = "
		SELECT
			m.mesa_capacidad,
			SUM(CASE WHEN (m.mesa_estado IS NULL OR m.mesa_estado != 1) AND (m.mesa_provision IS NULL OR m.mesa_provision = '') THEN 1 ELSE 0 END) AS cantidad_mesas,
			COUNT(*) AS total_mesas
		FROM mesas AS m
		JOIN ambientes AS a ON m.mesa_ambiente = a.ambiente_id
		JOIN pisos AS p ON a.ambiente_piso = p.piso_id
		WHERE
			m.mesa_activa = 1
			AND m.mesa_tipo = 'mesa'
			AND a.ambiente_estado = 1
			AND p.piso_estado = 1
			AND (a.ambiente_fecha_partido IS NULL OR a.ambiente_fecha_partido >= NOW())
		GROUP BY
			m.mesa_capacidad
		ORDER BY
			m.mesa_capacidad ASC;
		";

		return $this->_conn->query($query)->fetchAsObject();
	}
	public function getMesaDisponibleUnica($capacidad)
	{
		$query = "
		SELECT 
			m.mesa_id
		FROM pisos p
		INNER JOIN ambientes a ON p.piso_id = a.ambiente_piso
		INNER JOIN mesas m ON a.ambiente_id = m.mesa_ambiente
		WHERE (m.mesa_estado = '' OR m.mesa_estado = '0' OR m.mesa_estado IS NULL)
		AND (m.mesa_provision IS NULL OR m.mesa_provision = '')
		AND m.mesa_capacidad = '$capacidad'
		AND m.mesa_tipo = 'mesa'
		AND (p.piso_estado = '1' OR p.piso_estado = 1)
		AND (a.ambiente_estado = '1' OR a.ambiente_estado = 1)
		LIMIT 1
	";

		return $this->_conn->query($query)->fetchAsObject();
	}


	public function getTodosElementosPorAmbiente($ambienteId, $capacidad)
	{
		$query = "
        SELECT 
            m.*,
            a.ambiente_nombre,
            p.piso_nombre
        FROM mesas m
        INNER JOIN ambientes a ON m.mesa_ambiente = a.ambiente_id
        INNER JOIN pisos p ON a.ambiente_piso = p.piso_id
        WHERE m.mesa_ambiente = '$ambienteId' AND (m.mesa_capacidad != '$capacidad' OR m.mesa_tipo != 'mesa') AND m.mesa_activa = '1'
        ORDER BY m.mesa_nombre ASC
    ";

		return $this->_conn->query($query)->fetchAsObject();
	}

	// ============================================================================
	// SILLAS INDIVIDUALES (mesa_tipo = 'silla', capacidad = 1)
	// Espejo de los métodos de mesa, filtrando explícitamente por mesa_tipo='silla'
	// para no confundir una 'mesa' de capacidad 1 con inventario de sillas.
	// ============================================================================

	/**
	 * Conteo global de sillas libres (para el selector de cantidad del paso 1).
	 * Las sillas no se agrupan por capacidad: el comprador elige una cantidad.
	 * @return array un objeto con la propiedad cantidad_sillas.
	 */
	public function getSillasDisponiblesTotal()
	{
		$query = "
			SELECT
				SUM(CASE WHEN (m.mesa_estado IS NULL OR m.mesa_estado != 1) AND (m.mesa_provision IS NULL OR m.mesa_provision = '') THEN 1 ELSE 0 END) AS cantidad_sillas,
				COUNT(*) AS total_sillas
			FROM mesas AS m
			JOIN ambientes AS a ON m.mesa_ambiente = a.ambiente_id
			JOIN pisos AS p ON a.ambiente_piso = p.piso_id
			WHERE
				m.mesa_activa = 1
				AND m.mesa_tipo = 'silla'
				AND a.ambiente_estado = 1
				AND p.piso_estado = 1
				AND (a.ambiente_fecha_partido IS NULL OR a.ambiente_fecha_partido >= NOW())
		";

		return $this->_conn->query($query)->fetchAsObject();
	}

	/**
	 * Pisos que tienen sillas disponibles. Espejo de getPisosDisponibles() sin capacidad.
	 */
	public function getPisosDisponiblesSillas()
	{
		$query = "
			SELECT DISTINCT
				p.piso_id,
				p.piso_nombre,
				p.piso_color,
				COUNT(m.mesa_id) as total_mesas
			FROM pisos p
			INNER JOIN ambientes a ON p.piso_id = a.ambiente_piso
			INNER JOIN mesas m ON a.ambiente_id = m.mesa_ambiente
			WHERE ((m.mesa_estado = '' OR m.mesa_estado = '0' OR m.mesa_estado IS NULL) AND m.mesa_activa = '1')
			AND (m.mesa_provision IS NULL OR m.mesa_provision = '')
			AND m.mesa_tipo = 'silla'
			AND (p.piso_estado = '1' OR p.piso_estado = 1)
			AND (a.ambiente_estado = '1' OR a.ambiente_estado = 1)
			GROUP BY p.piso_id, p.piso_nombre, p.piso_color
			ORDER BY p.piso_nombre ASC
		";

		return $this->_conn->query($query)->fetchAsObject();
	}

	/**
	 * Ambientes de un piso con sillas disponibles. Espejo de getAmbientesPorPiso() sin capacidad.
	 */
	public function getAmbientesPorPisoSillas($pisoId)
	{
		$socio = $_SESSION['socio'];
		$documento = $socio->SBE_CODI;
		$query = "
		SELECT
		    a.ambiente_id,
		    a.ambiente_nombre,
		    a.ambiente_categoria,
		    a.ambiente_fecha_partido,
		    c.categoria_nombre,
		    c.categoria_descripcion,
		    COALESCE(md.total_mesas, 0) AS total_mesas
		FROM ambientes a
		LEFT JOIN categorias c ON a.ambiente_categoria = c.categoria_id
		LEFT JOIN (
		    SELECT
		        m.mesa_ambiente,
		        COUNT(DISTINCT m.mesa_id) AS total_mesas
		    FROM mesas m
		    LEFT JOIN reservas r ON FIND_IN_SET(m.mesa_id, r.reserva_mesa_id)
		    WHERE ((m.mesa_estado = '' OR m.mesa_estado = '0' OR m.mesa_estado IS NULL)
		           AND (m.mesa_provision IS NULL OR m.mesa_provision = '')
		           OR (r.reserva_documento = '$documento' AND r.reserva_estado IN (1, 4, 7)))
		      AND m.mesa_activa = '1'
		      AND m.mesa_tipo = 'silla'
		    GROUP BY m.mesa_ambiente
		) AS md ON a.ambiente_id = md.mesa_ambiente
		WHERE a.ambiente_piso = '$pisoId'
		  AND a.ambiente_estado = '1'
		ORDER BY c.categoria_nombre ASC, a.ambiente_nombre ASC
		";

		return $this->_conn->query($query)->fetchAsObject();
	}

	/**
	 * Sillas disponibles de un ambiente. Espejo de getMesasPorAmbiente() sin capacidad.
	 */
	public function getSillasPorAmbiente($ambienteId)
	{
		// Mismo criterio que getMesasPorAmbiente(): se devuelven todas las sillas activas
		// del ambiente (libres u ocupadas, de cualquier socio) para que el mapa las pinte
		// en rojo correctamente sin depender del socio de la sesión actual.
		$query = "
			SELECT
				m.*,
				CASE WHEN (m.mesa_provision IS NOT NULL AND m.mesa_provision != '') THEN 1 ELSE m.mesa_estado END AS mesa_estado,
				a.ambiente_nombre,
				p.piso_nombre
			FROM mesas m
			INNER JOIN ambientes a ON m.mesa_ambiente = a.ambiente_id
			INNER JOIN pisos p ON a.ambiente_piso = p.piso_id
			WHERE m.mesa_ambiente = '$ambienteId'
			AND m.mesa_tipo = 'silla'
			AND m.mesa_activa = '1'
			ORDER BY m.mesa_nombre ASC
		";

		return $this->_conn->query($query)->fetchAsObject();
	}

	/**
	 * Elementos de fondo (no seleccionables) al elegir sillas: todo lo que NO es silla
	 * (mesas reales, decoraciones) se dibuja gris. Espejo de getTodosElementosPorAmbiente().
	 */
	public function getTodosElementosPorAmbienteSillas($ambienteId)
	{
		$query = "
        SELECT
            m.*,
            a.ambiente_nombre,
            p.piso_nombre
        FROM mesas m
        INNER JOIN ambientes a ON m.mesa_ambiente = a.ambiente_id
        INNER JOIN pisos p ON a.ambiente_piso = p.piso_id
        WHERE m.mesa_ambiente = '$ambienteId' AND m.mesa_tipo != 'silla' AND m.mesa_activa = '1'
        ORDER BY m.mesa_nombre ASC
    ";

		return $this->_conn->query($query)->fetchAsObject();
	}

	/**
	 * Obtiene TODOS los elementos del ambiente (mesas, paredes, decoraciones)
	 * para mostrar el plano completo en la administración
	 * Este método NO filtra por capacidad ni tipo
	 */
	public function getTodosElementosDelAmbiente($ambienteId)
	{
		$query = "
        SELECT 
            m.*,
            a.ambiente_nombre,
            p.piso_nombre
        FROM mesas m
        INNER JOIN ambientes a ON m.mesa_ambiente = a.ambiente_id
        INNER JOIN pisos p ON a.ambiente_piso = p.piso_id
        WHERE m.mesa_ambiente = '$ambienteId'
        AND m.mesa_activa = '1'
    ";

		return $this->_conn->query($query)->fetchAsObject();
	}

	/**
	 * Obtiene pisos disponibles para administración (sin depender de sesión)
	 */
	public function getPisosDisponiblesAdmin($capacidad)
	{
		$query = "
			SELECT DISTINCT 
				p.piso_id,
				p.piso_nombre,
				p.piso_color,
				COUNT(DISTINCT m.mesa_id) as total_mesas
			FROM pisos p
			INNER JOIN ambientes a ON p.piso_id = a.ambiente_piso
			INNER JOIN mesas m ON a.ambiente_id = m.mesa_ambiente
			WHERE (m.mesa_estado = '' OR m.mesa_estado = '0' OR m.mesa_estado IS NULL)
			AND (m.mesa_provision IS NULL OR m.mesa_provision = '')
			AND m.mesa_activa = '1'
			AND m.mesa_capacidad = '$capacidad'
			AND m.mesa_tipo = 'mesa'
			AND (p.piso_estado = '1' OR p.piso_estado = 1)
			AND (a.ambiente_estado = '1' OR a.ambiente_estado = 1)
			GROUP BY p.piso_id, p.piso_nombre, p.piso_color
			HAVING COUNT(DISTINCT m.mesa_id) > 0
			ORDER BY p.piso_nombre ASC
		";

		return $this->_conn->query($query)->fetchAsObject();
	}

	/**
	 * Obtiene ambientes de un piso para administración (sin depender de sesión)
	 */
	public function getAmbientesPorPisoAdmin($pisoId, $capacidad)
	{
		$query = "
			SELECT
				a.ambiente_id,
				a.ambiente_nombre,
				a.ambiente_categoria,
				a.ambiente_filas,
				a.ambiente_columnas,
				a.ambiente_descuento,
				c.categoria_id,
				c.categoria_nombre,
				c.categoria_descripcion,
				c.categoria_precio_socio,
				c.categoria_precio_socio_hijo,
				c.categoria_precio_invitado,
				COUNT(DISTINCT m.mesa_id) AS total_mesas
			FROM ambientes a
			INNER JOIN mesas m ON a.ambiente_id = m.mesa_ambiente
			LEFT JOIN categorias c ON a.ambiente_categoria = c.categoria_id
			WHERE a.ambiente_piso = '$pisoId'
			AND (m.mesa_estado = '' OR m.mesa_estado = '0' OR m.mesa_estado IS NULL)
			AND (m.mesa_provision IS NULL OR m.mesa_provision = '')
			AND m.mesa_capacidad = '$capacidad'
			AND m.mesa_tipo = 'mesa'
			AND m.mesa_activa = '1'
			AND a.ambiente_estado = '1'
			GROUP BY 
				a.ambiente_id,
				a.ambiente_nombre,
				a.ambiente_categoria,
				a.ambiente_filas,
				a.ambiente_columnas,
				a.ambiente_descuento,
				c.categoria_id,
				c.categoria_nombre,
				c.categoria_descripcion,
				c.categoria_precio_socio,
				c.categoria_precio_socio_hijo,
				c.categoria_precio_invitado
			HAVING COUNT(DISTINCT m.mesa_id) > 0
			ORDER BY c.categoria_nombre ASC, a.ambiente_nombre ASC
		";

		return $this->_conn->query($query)->fetchAsObject();
	}

	/**
	 * Obtiene mesas disponibles de un ambiente para administración (sin depender de sesión)
	 */
	public function getMesasPorAmbienteAdmin($ambienteId, $capacidad)
	{
		$query = "
			SELECT 
				m.*,
				a.ambiente_nombre,
				p.piso_nombre
			FROM mesas m
			INNER JOIN ambientes a ON m.mesa_ambiente = a.ambiente_id
			INNER JOIN pisos p ON a.ambiente_piso = p.piso_id
			WHERE m.mesa_ambiente = '$ambienteId'
			AND (m.mesa_estado = '' OR m.mesa_estado = '0' OR m.mesa_estado IS NULL)
			AND (m.mesa_provision IS NULL OR m.mesa_provision = '')
			AND m.mesa_capacidad = '$capacidad'
			AND m.mesa_tipo = 'mesa'
			AND m.mesa_activa = '1'
			GROUP BY m.mesa_id
			ORDER BY m.mesa_nombre ASC
		";

		return $this->_conn->query($query)->fetchAsObject();
	}
}
