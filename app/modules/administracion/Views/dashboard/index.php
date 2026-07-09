<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .chart-container {
    position: relative;
    height: 400px;
    margin: 20px 0;
  }

  .metric-card {
    text-align: center;
    /* padding: 20px; */
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
  }

  .metric-number {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
  }

  .metric-label {
    font-size: 0.8rem;
    opacity: 0.8;
  }

  .print-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
  }
</style>
</head>

<h1 class="titulo-principal"><i class="fas fa-info-circle"></i> <?php echo $this->titlesection; ?></h1>

<div class="container-fluid">
  <?php
  $tp = (int)($this->estadisticasGenerales->total_personas ?? 0);
  $ct = (int)($this->estadisticasGenerales->capacidad_total ?? 0);
  $ocupacion = $ct > 0 ? round(($tp / $ct) * 100, 1) : 0;
  ?>
  
  <div class="d-flex flex-wrap gap-3 justify-content-center content-dashboard">
    <?php if ($this->estadisticasGenerales): ?>
      <div class="row w-100 g-1 justify-content-center">
        <!-- Grupo Personas -->
        <div class="col-md-3 shadow-sm">
          <div class="card stats-card h-100">
            <div class="card-body metric-card">
              <div class="metric-number">
                <?php echo number_format($this->estadisticasGenerales->total_personas); ?>/<?php echo number_format($this->estadisticasGenerales->capacidad_total); ?>
              </div>
              <div class="metric-label">Total personas / Total capacidad</div>
              <hr>
              <div class="row g-0 p-0 w-100">
                <div class="col-4">
                  <div class="metric-number" style="font-size:1.2rem;">
                    <?php echo number_format($this->estadisticasGenerales->total_personas_socios); ?>
                  </div>
                  <div class="metric-label">Socios</div>
                </div>
                <div class="col-4">
                  <div class="metric-number" style="font-size:1.2rem;">
                    <?php echo number_format($this->estadisticasGenerales->total_personas_invitados); ?>
                  </div>
                  <div class="metric-label">Invitados</div>
                </div>
                <div class="col-4">
                  <div class="metric-number" style="font-size:1.2rem;">
                    <?php echo number_format($ocupacion); ?>%
                  </div>
                  <div class="metric-label">Ocupacion</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Grupo Mesas -->
        <div class="col-md-3 shadow-sm">
          <div class="card stats-card h-100">
            <div class="card-body metric-card">
              <div class="metric-number"><?php echo number_format($this->estadisticasGenerales->total_mesas); ?></div>
              <div class="metric-label">Total mesas</div>
              <hr>
              <div class="row  g-0 p-0 w-100">
                <div class="col-4">
                  <div class="metric-number" style="font-size:1.2rem;">
                    <?php echo number_format($this->estadisticasGenerales->mesas_vendidas); ?>
                  </div>
                  <div class="metric-label">Vendidas</div>
                </div>
                <div class="col-4">
                  <!--<?php echo (int) $this->estadisticasGenerales->total_mesas ?>-->
                  <!--      <?php echo $this->estadisticasGenerales->mesas_vendidas ?>-->
                  <div class="metric-number" style="font-size:1.2rem;">
                    <?php echo number_format((int) $this->estadisticasGenerales->total_mesas - (int) $this->estadisticasGenerales->mesas_vendidas); ?>
                  </div>
                  <div class="metric-label">Disponibles</div>
                </div>
                <div class="col-4">
                  <div class="metric-number" style="font-size:1.2rem;">
                    <?php
                    $tmRef = (int)($this->estadisticasGenerales->total_mesas ?? 0);
                    $mvRef = (int)($this->estadisticasGenerales->mesas_vendidas ?? 0);
                    echo $tmRef > 0 ? round(($mvRef / $tmRef) * 100) : 0;
                  ?>%
                  </div>
                  <div class="metric-label">Ocupación</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Grupo Pagos -->
        <div class="col-md-3 shadow-sm">
          <div class="card stats-card h-100">
            <div class="card-body metric-card">
              <div class="metric-number">$<?php echo number_format($this->estadisticasGenerales->total_ingresos, 0); ?>
              </div>
              <div class="metric-label">Total ingresos</div>
              <hr>
              <div class="row  g-0 p-0 w-100">
                <div class="col-4">
                  <div class="metric-number" style="font-size:1.2rem;">
                    <?php echo number_format($this->estadisticasGenerales->pagos_accion); ?>
                  </div>
                  <div class="metric-label">Pagos acción</div>
                </div>
                <div class="col-4">
                  <div class="metric-number" style="font-size:1.2rem;">
                    <?php echo number_format($this->estadisticasGenerales->pagos_linea); ?>
                  </div>
                  <div class="metric-label">Pagos placetopay</div>
                </div>
                <div class="col-4">
                  <div class="metric-number" style="font-size:1.2rem;">
                    <?php echo number_format($this->estadisticasGenerales->pagos_datafono); ?>
                  </div>
                  <div class="metric-label">Pagos datafono</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Reservas aceptadas -->
        <div class="col-md-3 shadow-sm">
          <div class="card stats-card h-100">
            <div class="card-body metric-card">
              <div class="metric-number">
                <?php echo number_format($this->estadisticasGenerales->total_reservas_aceptadas); ?>
              </div>
              <div class="metric-label">Reservas aceptadas</div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
  
  <div class="content-dashboard">
    <!-- Gráficas de Métodos de Pago -->
    <div class="cfg-section-title">
      <span class="cfg-icon"><i class="fas fa-credit-card"></i></span>
      Análisis de métodos de pago
    </div>
    <div class="row mb-3">
      <div class="col-md-4">
        <div class="div-dashboard">
          <h2>
            <i class="fas fa-chart-pie"></i> Distribución por método de pago
          </h2>
          <div class="pading-dashboard">
            <div class="chart-container">
              <canvas id="metodosChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="div-dashboard">
          <h2>
            <i class="fas fa-chart-bar"></i> Ingresos por método de pago
          </h2>
          <div class="pading-dashboard">
            <div class="chart-container">
              <canvas id="ingresosChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="div-dashboard">
          <h2>
            <i class="fas fa-chart-bar"></i> Distribución por edades (Socios)
          </h2>
          <div class="pading-dashboard">
            <div class="chart-container">
              <canvas id="edadesChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla de detalles de métodos de pago -->
    <div class="row mb-5">
      <div class="col-md-12">
        <div class="div-dashboard">
          <h2>
            <i class="fas fa-table"></i> Detalle métodos de pago
          </h2>
          <div class="pading-dashboard">
            <div class="content-table mb-3">
              <table class=" table table-striped  table-hover table-administrator text-left">
                <thead>
                  <tr>
                    <th>Método de Pago</th>
                    <th>Cantidad de Reservas</th>
                    <th>Total Monto</th>
                    <th>Promedio por Reserva</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if ($this->metodospagoData):
                    foreach ($this->metodospagoData as $metodo): ?>
                      <tr>
                        <td><strong><?php echo $metodo->metodo_pago; ?></strong></td>
                        <td><?php echo number_format($metodo->cantidad_reservas); ?></td>
                        <td>$<?php echo number_format($metodo->total_monto, 0); ?></td>
                        <td>$<?php echo number_format($metodo->total_monto / $metodo->cantidad_reservas, 0); ?></td>
                      </tr>
                  <?php endforeach;
                  endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- Gráficas de Ocupación por Pisos -->
    <div class="cfg-section-title">
      <span class="cfg-icon"><i class="fas fa-building"></i></span>
      Análisis de ocupación por pisos
    </div>
    <div class="row mb-3">
      <div class="col-md-8">
        <div class="div-dashboard">
          <h2>
            <i class="fas fa-chart-bar"></i> Ocupación de mesas por piso
          </h2>
          <div class="pading-dashboard">
            <div class="chart-container">
              <canvas id="pisosChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="div-dashboard">
          <h2>
            <i class="fas fa-area-chart"></i> Capacidad total por piso
          </h2>
          <div class="pading-dashboard">
            <div class="chart-container">
              <canvas id="capacidadPisosChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla de detalles de pisos -->
    <div class="row mb-5">
      <div class="col-12">
        <div class="div-dashboard">
          <h2><i class="fas fa-table"></i> Detalle por pisos</h2>
          <div class="pading-dashboard">
            <div class="content-table mb-3">
              <table class=" table table-striped  table-hover table-administrator text-left">
                <thead>
                  <tr>
                    <th>Piso</th>
                    <th>Total Mesas</th>
                    <th>Mesas Ocupadas</th>
                    <th>Mesas Disponibles</th>
                    <th>% Ocupación</th>
                    <th>Capacidad Total</th>
                    <th>Capacidad Ocupada</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $totalmesas = 0;
                  $totalocupadas = 0;
                  $totaldisponibles = 0;
                  ?>
                  <?php if ($this->pisosData):
                    foreach ($this->pisosData as $piso): ?>
                      <tr>
                        <td><strong><?php echo $piso->piso_nombre; ?></strong></td>
                        <td>
                          <?php echo $piso->total_mesas; ?>
                        </td>
                        <?php $totalmesas += (int) $piso->total_mesas; ?>
                        <td><span class="badge bg-danger">
                            <?php echo $piso->mesas_ocupadas; ?>
                            <?php $totalocupadas += (int) $piso->mesas_ocupadas; ?>
                          </span></td>
                        <td><span class="badge bg-success">
                            <?php echo $piso->mesas_disponibles; ?>
                            <?php $totaldisponibles += (int) $piso->mesas_disponibles; ?>
                          </span></td>
                        <td>
                          <?php echo $piso->total_mesas > 0 ? round(($piso->mesas_ocupadas / $piso->total_mesas) * 100, 1) : 0; ?>%
                        </td>
                        <td><?php echo $piso->capacidad_total; ?></td>
                        <td><?php echo $piso->capacidad_ocupada; ?></td>
                      </tr>
                  <?php endforeach;
                  endif; ?>
                <tfoot class="d-none" style="font-weight: bold; background-color: #f8f9fa !important;">
                  <tr>
                    <td><strong>Total</strong></td>
                    <td><?php echo $totalmesas; ?></td>
                    <td><span class="badge bg-danger"><?php echo $totalocupadas; ?></span></td>
                    <td><span class="badge bg-success"><?php echo $totaldisponibles; ?></span></td>
                    <td>
                      <?php echo $totalmesas > 0 ? round(($totalocupadas / $totalmesas) * 100, 1) : 0; ?>%
                    </td>
                    <td><?php echo $totalcapacidad; ?></td>
                    <td><?php echo $totalcapacidadocupada; ?></td>
                  </tr>
                </tfoot>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    
    <!-- Gráficas de Ambientes -->
    <div class="cfg-section-title">
      <span class="cfg-icon"><i class="fas fa-door-open"></i></span>
      Análisis de ambientes
    </div>
    <div class="row mb-3">
      <div class="col-12 mb-3">
        <div class="div-dashboard">
          <h2><i class="fas fa-chart-bar"></i> Ocupación por ambientes</h2>
          <div class="pading-dashboard">
            <div class="chart-container">
              <canvas id="ambientesChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="div-dashboard">
          <h2><i class="fas fa-chair"></i> Mesas disponibles por capacidad</h2>
          <div class="pading-dashboard">
            <?php
            // Agrupar todas las mesas libres por capacidad
            $mesasDisponiblesPorCapacidad = [];
            if ($this->ambientesData) {
              foreach ($this->ambientesData as $ambiente) {
                if (!empty($ambiente->mesas_libres)) {
                  foreach ($ambiente->mesas_libres as $mesa) {
                    $capacidad = $mesa['mesa_capacidad'];
                    if (!isset($mesasDisponiblesPorCapacidad[$capacidad])) {
                      $mesasDisponiblesPorCapacidad[$capacidad] = [];
                    }
                    $mesasDisponiblesPorCapacidad[$capacidad][] = [
                      'mesa_nombre' => $mesa['mesa_nombre'],
                      'ambiente_nombre' => $ambiente->ambiente_nombre,
                      'piso_nombre' => $ambiente->piso_nombre,
                      'capacidad' => $mesa['mesa_capacidad']
                    ];
                  }
                }
              }
            }

            // Ordenar por capacidad
            ksort($mesasDisponiblesPorCapacidad);
            ?>

            <?php if (!empty($mesasDisponiblesPorCapacidad)): ?>
              <div class="accordion mb-3" id="accordionMesasDisponibles">
                <?php foreach ($mesasDisponiblesPorCapacidad as $capacidad => $mesas): ?>
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?php echo $capacidad; ?>">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapse<?php echo $capacidad; ?>" aria-expanded="false"
                        aria-controls="collapse<?php echo $capacidad; ?>">
                        <strong>Capacidad <?php echo $capacidad; ?> personas</strong>
                        <span class="badge bg-success ms-2"><?php echo count($mesas); ?> mesas disponibles</span>
                      </button>
                    </h2>
                    <div id="collapse<?php echo $capacidad; ?>" class="accordion-collapse collapse"
                      aria-labelledby="heading<?php echo $capacidad; ?>" data-bs-parent="#accordionMesasDisponibles">
                      <div class="accordion-body">
                        <div class="table-responsive">
                          <table class="table table-sm table-hover">
                            <thead class="table-light">
                              <tr>
                                <th>Mesa</th>
                                <th>Ambiente</th>
                                <th>Piso</th>
                                <th>Capacidad</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php foreach ($mesas as $mesa): ?>
                                <tr>
                                  <td><strong><?php echo $mesa['mesa_nombre']; ?></strong></td>
                                  <td><?php echo $mesa['ambiente_nombre']; ?></td>
                                  <td><span class="badge bg-info"><?php echo $mesa['piso_nombre']; ?></span></td>
                                  <td><?php echo $mesa['capacidad']; ?> personas</td>
                                </tr>
                              <?php endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php else: ?>
              <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No hay mesas disponibles en este momento.
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="div-dashboard">
          <h2><i class="fas fa-table"></i> Detalle por ambientes</h2>
          <div class="pading-dashboard">
            <div class="content-table mb-3">
              <table class=" table table-striped  table-hover table-administrator text-left">
                <thead>
                  <tr>
                    <th>Piso</th>
                    <th>Ambiente</th>
                    <!-- <th>Categoría</th> -->
                    <th>Total Mesas</th>
                    <th>Ocupadas</th>
                    <th>Disponibles</th>
                    <th>% Ocupación</th>
                    <th>Cap. Total</th>
                    <th>Cap. Ocupada</th>
                    <th>Socios</th>
                    <th>Otros</th>
                    <th>Mesas Libres</th>
                  </tr>
                </thead>
                <tbody class="align-middle">
                  <?php if ($this->ambientesData):
                    foreach ($this->ambientesData as $ambiente): ?>
                      <tr>
                        <td><?php echo $ambiente->piso_nombre; ?></td>
                        <td><strong><?php echo $ambiente->ambiente_nombre; ?></strong></td>
                        <!-- <td><?php echo $ambiente->categoria_nombre; ?></td> -->
                        <td><?php echo $ambiente->total_mesas; ?></td>
                        <td><span class="badge bg-danger"><?php echo $ambiente->mesas_ocupadas; ?></span></td>
                        <td><span class="badge bg-success"><?php echo $ambiente->mesas_disponibles; ?></span></td>
                        <td>
                          <?php echo $ambiente->total_mesas > 0 ? round(($ambiente->mesas_ocupadas / $ambiente->total_mesas) * 100, 1) : 0; ?>%
                        </td>
                        <td><?php echo $ambiente->capacidad_total; ?></td>
                        <td><?php echo $ambiente->capacidad_ocupada; ?></td>
                        <td><span class="badge bg-primary"><?php echo $ambiente->total_socios; ?></span></td>
                        <td><span class="badge bg-secondary"><?php echo $ambiente->total_otros; ?></span></td>
                        <td>
                            <a href="#" data-bs-toggle="modal"
                              data-bs-target="#modalMesasLibres<?php echo $ambiente->ambiente_id; ?>"
                              style="display:inline-flex;align-items:center;border-radius:6px;overflow:hidden;text-decoration:none;font-size:.78rem;font-weight:600;line-height:1;">
                              <span style="background:#15803d;color:#fff;padding:5px 8px;">
                                <i class="fas fa-eye"></i>
                              </span>
                              <span style="background:#dcfce7;color:#166534;padding:5px 9px;">
                                Ver (<?php echo count($ambiente->mesas_libres ?? []); ?>)
                              </span>
                            </a>
                        </td>
                      </tr>
                  <?php endforeach;
                  endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if ($this->ambientesData): foreach ($this->ambientesData as $ambiente): ?>
  <div class="modal fade" id="modalMesasLibres<?php echo $ambiente->ambiente_id; ?>" tabindex="-1"
    aria-labelledby="modalMesasLibresLabel<?php echo $ambiente->ambiente_id; ?>" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalMesasLibresLabel<?php echo $ambiente->ambiente_id; ?>">
            Mesas libres en <?php echo htmlspecialchars($ambiente->ambiente_nombre); ?>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body" style="max-height:50vh;overflow-y:auto;">
          <?php if (!empty($ambiente->mesas_libres)): ?>
            <ul class="list-group">
              <?php foreach ($ambiente->mesas_libres as $mesa): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <?php echo htmlspecialchars($mesa['mesa_nombre']); ?>
                  <span class="badge bg-primary">Cap: <?php echo $mesa['mesa_capacidad']; ?></span>
                  <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($ambiente->piso_nombre); ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-center text-muted py-3"><i class="fas fa-check-circle text-success me-1"></i> Todas las mesas están ocupadas.</p>
          <?php endif; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; endif; ?>

  <a href="<?php echo $this->route; ?>/reset" class="btn reset-btn me-2">
    <i class="fas fa-redo"></i> Reset
  </a>
  <div class="d-flex justify-content-end mb-3 print-btn">
    <!-- <button class="btn btn-success me-2" onclick="exportarExcel()">
        <i class="fas fa-file-excel"></i> Exportar Excel
      </button> -->
    <button class="btn btn-primary" onclick="imprimirReporte()">
      <i class="fas fa-print"></i> Imprimir
    </button>
  </div>
</div>

<script>
  // Datos de métodos de pago
  const metodosData = <?php echo json_encode($this->metodospagoData ?: []); ?>;
  const pisosData = <?php echo json_encode($this->pisosData ?: []); ?>;
  const ambientesData = <?php echo json_encode($this->ambientesData ?: []); ?>;
  const edadesData = <?php echo json_encode($this->invitadosPorEdades ?: (object) ['entre18_35' => 0, 'entre35_55' => 0, 'mayores55' => 0, 'sin_dato' => 0]); ?>;
  console.log(edadesData)
  // Gráfica de métodos de pago (pie chart)
  const metodosCtx = document.getElementById('metodosChart').getContext('2d');
  new Chart(metodosCtx, {
    type: 'pie',
    data: {
      labels: metodosData.map(m => m.metodo_pago),
      datasets: [{
        data: metodosData.map(m => m.cantidad_reservas),
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'],
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const total = metodosData.reduce((sum, m) => sum + parseInt(m.cantidad_reservas), 0);
              const percentage = ((context.raw / total) * 100).toFixed(1);
              return context.label + ': ' + context.raw + ' (' + percentage + '%)';
            }
          }
        }
      }
    }
  });

  // Gráfica de ingresos por método de pago
  const ingresosCtx = document.getElementById('ingresosChart').getContext('2d');
  new Chart(ingresosCtx, {
    type: 'bar',
    data: {
      labels: metodosData.map(m => m.metodo_pago),
      datasets: [{
        label: 'Total Ingresos',
        data: metodosData.map(m => m.total_monto),
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return '$' + value.toLocaleString();
            }
          }
        }
      },
      plugins: {
        tooltip: {
          callbacks: {
            label: function(context) {
              return 'Total: $' + context.raw.toLocaleString();
            }
          }
        }
      }
    }
  });

  // Gráfica de ocupación por pisos
  const pisosCtx = document.getElementById('pisosChart').getContext('2d');
  new Chart(pisosCtx, {
    type: 'bar',
    data: {
      labels: pisosData.map(p => p.piso_nombre),
      datasets: [{
          label: 'Mesas Ocupadas',
          data: pisosData.map(p => p.mesas_ocupadas),
          backgroundColor: '#FF6384'
        },
        {
          label: 'Mesas Disponibles',
          data: pisosData.map(p => p.mesas_disponibles),
          backgroundColor: '#36A2EB'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          stacked: true
        },
        y: {
          stacked: true,
          beginAtZero: true
        }
      }
    }
  });

  // Gráfica de capacidad por pisos
  const capacidadPisosCtx = document.getElementById('capacidadPisosChart').getContext('2d');
  new Chart(capacidadPisosCtx, {
    type: 'doughnut',
    data: {
      labels: pisosData.map(p => p.piso_nombre),
      datasets: [{
        data: pisosData.map(p => p.capacidad_total),
        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom'
        }
      }
    }
  });

  // Gráfica de ambientes
  const ambientesCtx = document.getElementById('ambientesChart').getContext('2d');
  new Chart(ambientesCtx, {
    type: 'bar',
    data: {
      labels: ambientesData.map(a => a.ambiente_nombre + ' (' + a.piso_nombre + ')'),
      datasets: [{
          label: 'Mesas Ocupadas',
          data: ambientesData.map(a => a.mesas_ocupadas),
          backgroundColor: '#FF6384'
        },
        {
          label: 'Mesas Disponibles',
          data: ambientesData.map(a => a.mesas_disponibles),
          backgroundColor: '#36A2EB'
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      indexAxis: 'y',
      scales: {
        x: {
          stacked: true,
          beginAtZero: true
        },
        y: {
          stacked: true
        }
      }
    }
  });


  // Gráfica de distribución por edades
  const edadesCtx = document.getElementById('edadesChart').getContext('2d');
  new Chart(edadesCtx, {
    type: 'bar',
    data: {
      labels: ['18-35 años', '36-55 años', 'Más de 55'],
      datasets: [{
        label: 'Cantidad de Socios',
        data: [
          edadesData.entre18_35,
          edadesData.entre35_55,
          edadesData.mayores55,
          edadesData.sin_dato
        ],
        backgroundColor: [
          '#4BC0C0', // Verde agua para jóvenes
          '#36A2EB', // Azul para adultos
          '#FF9F40', // Naranja para mayores
          '#C9CBCF' // Gris para sin dato
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      },
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const total = edadesData.entre18_35 + edadesData.entre35_55 + edadesData.mayores55 + edadesData.sin_dato;
              const percentage = total > 0 ? ((context.raw / total) * 100).toFixed(1) : '0';
              return context.label + ': ' + context.raw + ' (' + percentage + '%)';
            }
          }
        }
      }
    }
  });


  // Función para exportar a Excel
  function exportarExcel() {
    window.location.href = '<?php echo $this->route; ?>/exportar?excel=1';
  }

  // Función para imprimir el reporte
  function imprimirReporte() {
    window.print();
  }

  // Estilos para impresión
  const style = document.createElement('style');
  style.innerHTML = `
      @media print {
        .print-btn { display: none !important; }
        .chart-container { height: 300px !important; }
        .card { break-inside: avoid; }
        .section-header { break-after: avoid; }
        #panel-botones { display: none !important; }
        header { display: none !important; }
        .panel-titulo { display: none !important; }
        #contenido_panel{width: 100% !important;}
        .row { display: flex !important; flex-wrap: wrap !important; }
        .col-md-3 { flex: 0 0 50% !important; max-width: 50% !important; }
      }
    `;
  document.head.appendChild(style);
</script>