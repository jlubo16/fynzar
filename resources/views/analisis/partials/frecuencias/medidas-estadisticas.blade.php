{{-- <!-- resources/views/analisis/partials/frecuencias/medidas-estadisticas.blade.php -->
<div class="card">
    <div class="card-header bg-warning text-dark">
        <h4 class="mb-0"><i class="bi bi-calculator"></i> Medidas Estadísticas de la Distribución</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Medidas de Tendencia Central</h6>
                <table class="table table-sm table-bordered">
                    <tr>
                        <td>Media (Promedio)</td>
                        <td>${{ number_format($datosAnalisis['promDinamicos'] ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Mediana</td>
                        <td>${{ number_format($datosAnalisis['medianaDinamicos'] ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Moda</td>
                        <td>{{ $datosAnalisis['moda'] ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Medidas de Dispersión</h6>
                <table class="table table-sm table-bordered">
                    <tr>
                        <td>Desviación Estándar</td>
                        <td>${{ number_format($datosAnalisis['desviacion'] ?? 0, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Coeficiente de Variación</td>
                        <td>{{ number_format($datosAnalisis['cvGastosDinamicos'] ?? 0, 1) }}%</td>
                    </tr>
                    <tr>
                        <td>Rango</td>
                        <td>${{ number_format($datosAnalisis['rangoDinamicos'] ?? 0, 2) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div> --}}

<!-- resources/views/analisis/partials/frecuencias/medidas-estadisticas.blade.php -->
<div class="card border-warning">
    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Análisis de Tus Transacciones Individuales</h5>
            <small class="opacity-75">
                Estadísticas de cada uno de tus {{ count($datosAnalisis['gastos_detallados'] ?? []) }} gastos
            </small>
        </div>
        <span class="badge bg-dark">
            <i class="bi bi-lightbulb"></i> Nuevo análisis
        </span>
    </div>
    <div class="card-body">
        @php
            // Calcular métricas básicas aquí si no vienen del controlador
            $gastosIndividuales = [];
            if (isset($datosAnalisis['gastos_detallados']) && is_array($datosAnalisis['gastos_detallados'])) {
                foreach ($datosAnalisis['gastos_detallados'] as $gasto) {
                    if (isset($gasto['monto']) && $gasto['monto'] > 0) {
                        $gastosIndividuales[] = $gasto['monto'];
                    }
                }
            }
            
            // Si tenemos medidas del controlador, usarlas
            $medidas = $medidasEstadisticas ?? [
                'media' => $datosAnalisis['promDinamicos'] ?? 0,
                'mediana' => $datosAnalisis['medianaDinamicos'] ?? 0,
                'moda' => $datosAnalisis['moda'] ?? 'N/A',
                'desviacion' => $datosAnalisis['desviacion'] ?? 0,
                'coeficiente_variacion' => $datosAnalisis['cvGastosDinamicos'] ?? 0,
                'rango' => $datosAnalisis['rangoDinamicos'] ?? 0
            ];
            
            $totalTransacciones = count($gastosIndividuales);
        @endphp
        
        @if($totalTransacciones > 0)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-light alert-permanent">
                    <h6 class="mb-2"><i class="bi bi-info-circle text-primary"></i> ¿Qué estamos analizando?</h6>
                    <p class="mb-0">
                        En lugar de solo ver <strong>gastos mensuales</strong> ($1,284, $1,671, etc.), 
                        estamos analizando <strong>cada una de tus {{ $totalTransacciones }} transacciones individuales</strong> 
                        ($120, $450, $85, $60, etc.). Esto revela patrones reales de consumo.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary mb-3">
                    <i class="bi bi-bullseye"></i> Medidas de Tendencia Central
                </h6>
                <table class="table table-sm table-bordered table-hover">
                    <tr>
                        <td width="60%"><strong>Media (Promedio por gasto)</strong></td>
                        <td width="40%" class="text-end fw-bold text-primary">
                            ${{ number_format($medidas['media'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Mediana (Gasto "típico")</strong></td>
                        <td class="text-end fw-bold text-info">
                            ${{ number_format($medidas['mediana'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Moda (Lo más frecuente)</strong></td>
                        <td class="text-end fw-bold text-success">
                            @if(is_string($medidas['moda']))
                                {{ $medidas['moda'] }}
                            @else
                                ${{ number_format($medidas['moda'], 2) }}
                            @endif
                        </td>
                    </tr>
                </table>
                
                <div class="alert alert-info mt-2 p-2 small alert-permanent">
                    <i class="bi bi-lightbulb"></i> 
                    <strong>La mediana (${{ number_format($medidas['mediana'], 0) }})</strong> 
                    muestra tu gasto "normal", ignorando valores extremos.
                </div>
            </div>
            
            <div class="col-md-6">
                <h6 class="text-primary mb-3">
                    <i class="bi bi-bar-chart"></i> Medidas de Dispersión
                </h6>
                <table class="table table-sm table-bordered table-hover">
                    <tr>
                        <td width="60%"><strong>Desviación Estándar</strong></td>
                        <td width="40%" class="text-end fw-bold text-warning">
                            ${{ number_format($medidas['desviacion'], 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Coeficiente de Variación</strong></td>
                        <td class="text-end fw-bold 
                            @if($medidas['coeficiente_variacion'] > 100) text-danger
                            @elseif($medidas['coeficiente_variacion'] > 50) text-warning
                            @else text-success @endif">
                            {{ number_format($medidas['coeficiente_variacion'], 1) }}%
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Rango</strong></td>
                        <td class="text-end fw-bold text-secondary">
                            ${{ number_format($medidas['rango'], 2) }}
                        </td>
                    </tr>
                </table>
                
                <div class="alert alert-info mt-2 p-2 small alert-permanent">
                    <i class="bi bi-lightbulb"></i> 
                    <strong>Coeficiente de variación ({{ number_format($medidas['coeficiente_variacion'], 1) }}%)</strong> 
                    indica qué tan variables son tus gastos. 
                    @if($medidas['coeficiente_variacion'] > 100)
                        <span class="text-danger">Muy altos → Gastos muy irregulares</span>
                    @elseif($medidas['coeficiente_variacion'] > 50)
                        <span class="text-warning">Moderados → Alguna variación</span>
                    @else
                        <span class="text-success">Bajos → Gastos consistentes</span>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- RESUMEN INTERPRETATIVO -->
        <div class="alert alert-light border mt-4 alert-permanent">
            <h6 class="text-primary mb-2">
                <i class="bi bi-chat-left-text"></i> Interpretación para tus finanzas:
            </h6>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Tu gasto promedio:</strong> ${{ number_format($medidas['media'], 0) }}<br>
                        <small class="text-muted">Pero tu gasto típico es de ${{ number_format($medidas['mediana'], 0) }}</small>
                    </p>
                    <p class="mb-0">
                        <strong>Lo que más repites:</strong> 
                        @if(is_numeric($medidas['moda']))
                            Pagos de ${{ number_format($medidas['moda'], 0) }}
                        @else
                            Varios montos diferentes
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2">
                        <strong>Variación:</strong> 
                        @if($medidas['coeficiente_variacion'] > 100)
                            <span class="text-danger">Alta</span> - Tus gastos son muy diferentes entre sí
                        @else
                            <span class="text-success">Moderada</span> - Gastos relativamente consistentes
                        @endif
                    </p>
                    <p class="mb-0">
                        <strong>Rango:</strong> ${{ number_format($medidas['rango'], 0) }} entre tu gasto más pequeño y más grande
                    </p>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-warning text-center py-4 alert-permanent" >
            <i class="bi bi-exclamation-triangle display-4 mb-3"></i>
            <h5>No hay transacciones individuales para analizar</h5>
            <p class="mb-0">Usa el <strong>Modo Detallado</strong> para ver análisis de cada gasto.</p>
        </div>
        @endif
    </div>
</div>