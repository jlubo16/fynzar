{{-- <!-- resources/views/analisis/partials/frecuencias/tabla-intervalos.blade.php -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-bar-chart"></i> Distribución de Gastos Dinámicos</h4>
        <button class="btn btn-sm btn-light" onclick="exportarTabla('tabla-intervalos')">
            <i class="bi bi-download"></i> Exportar
        </button>
    </div>
    <div class="card-body">
        @if(!empty($tablaFrecuencia))
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="tabla-intervalos">
                <thead class="table-light">
                    <tr>
                        <th>Intervalo de Gastos</th>
                        <th>Marca de Clase</th>
                        <th>Frecuencia (f)</th>
                        <th>Frec. Relativa</th>
                        <th>Frec. Acumulada</th>
                        <th>Frec. Rel. Acum.</th>
                        <th>Porcentaje</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tablaFrecuencia as $fila)
                    <tr>
                        <td>{{ $fila['intervalo'] }}</td>
                        <td>${{ $fila['marca_clase'] }}</td>
                        <td>{{ $fila['frecuencia'] }}</td>
                        <td>{{ $fila['frecuencia_relativa'] }}</td>
                        <td>{{ $fila['frecuencia_acumulada'] }}</td>
                        <td>{{ $fila['frecuencia_relativa_acumulada'] }}</td>
                        <td>{{ $fila['porcentaje'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-secondary">
                    <tr>
                        <td><strong>Total</strong></td>
                        <td>-</td>
                        <td><strong>{{ array_sum(array_column($tablaFrecuencia, 'frecuencia')) }}</strong></td>
                        <td><strong>1.000</strong></td>
                        <td>-</td>
                        <td><strong>1.000</strong></td>
                        <td><strong>100%</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div class="alert alert-info mt-3">
            <h6><i class="bi bi-lightbulb"></i> Interpretación:</h6>
            <ul class="mb-0">
                <li>La tabla muestra cómo se distribuyen tus gastos variables en diferentes rangos de valor</li>
                <li><strong>Frecuencia:</strong> Número de meses que caen en cada intervalo</li>
                <li><strong>Frecuencia Acumulada:</strong> Total acumulado de meses hasta ese intervalo</li>
                <li><strong>Porcentaje:</strong> Proporción de meses en cada rango de gastos</li>
            </ul>
        </div>
        @else
        <div class="alert alert-warning text-center">
            <i class="bi bi-exclamation-triangle"></i> No hay datos suficientes para generar la tabla de frecuencia
        </div>
        @endif
    </div>
</div> --}}

<!-- resources/views/analisis/partials/frecuencias/tabla-intervalos.blade.php -->
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0"><i class="bi bi-pie-chart"></i> Análisis de Consistencia en Tus Gastos</h4>
            <small class="opacity-75">¿Son predecibles tus gastos mensuales?</small>
        </div>
        <button class="btn btn-sm btn-light" onclick="exportarTabla('tabla-consistencia')">
            <i class="bi bi-download"></i> Descargar
        </button>
    </div>
    <div class="card-body">
        @if(!empty($tablaFrecuencia) && count($tablaFrecuencia) > 0)
        
        <!-- RESUMEN EJECUTIVO -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <h2 class="text-primary fw-bold">${{ number_format($rangoVariacion['diferencia'] ?? 0, 0) }}</h2>
                        <p class="text-muted mb-1">Variación máxima</p>
                        <small class="text-info">
                            <i class="bi bi-info-circle"></i> 
                            Diferencia entre tu mejor y peor mes
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <h2 class="text-warning fw-bold">{{ $porcentajeExtremos ?? 0 }}%</h2>
                        <p class="text-muted mb-1">Meses extremos</p>
                        <small class="text-info">
                            <i class="bi bi-info-circle"></i> 
                            Porcentaje de meses con gastos muy altos o muy bajos
                        </small>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center">
                        <h2 class="text-success fw-bold">${{ number_format($montoIdeal ?? 0, 0) }}</h2>
                        <p class="text-muted mb-1">Gasto mensual ideal</p>
                        <small class="text-info">
                            <i class="bi bi-info-circle"></i> 
                            Objetivo para mayor estabilidad financiera
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- TABLA SIMPLIFICADA -->
        <div class="table-responsive mb-4">
            <table class="table table-hover" id="tabla-consistencia">
                <thead class="table-light">
                    <tr>
                        <th width="25%">Nivel de Gasto</th>
                        <th width="15%" class="text-center">Meses</th>
                        <th width="20%" class="text-center">Porcentaje</th>
                        <th width="40%">¿Qué significa para ti?</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $niveles = [
                            'bajo' => ['icon' => 'bi-arrow-down', 'color' => 'success', 'text' => 'Tus meses más económicos'],
                            'moderado' => ['icon' => 'bi-dash', 'color' => 'info', 'text' => 'Gasto promedio saludable'],
                            'alto' => ['icon' => 'bi-arrow-up', 'color' => 'warning', 'text' => 'Meses con gastos elevados']
                        ];
                    @endphp
                    
                    @foreach($tablaFrecuencia as $index => $fila)
                        @php
                            $nivel = '';
                            $explicacion = '';
                            
                            if($index == 0) {
                                $nivel = 'bajo';
                                $explicacion = 'Tu mes más económico. Intenta mantenerte cerca de este nivel.';
                            } elseif($index == count($tablaFrecuencia) - 1 && $fila['frecuencia'] > 0) {
                                $nivel = 'alto';
                                $explicacion = 'Mes con mayores gastos. Revisa qué hizo que gastaras más.';
                            } elseif($fila['frecuencia'] > 0) {
                                $nivel = 'moderado';
                                $explicacion = 'Gasto dentro del rango esperado. Buen control financiero.';
                            }
                        @endphp
                        
                        @if($fila['frecuencia'] > 0 && isset($niveles[$nivel]))
                        <tr>
                            <td>
                                <i class="bi {{ $niveles[$nivel]['icon'] }} text-{{ $niveles[$nivel]['color'] }} me-2"></i>
                                <strong>{{ ucfirst($nivel) }}:</strong> {{ $fila['intervalo'] }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $niveles[$nivel]['color'] }} rounded-pill fs-6">
                                    {{ $fila['frecuencia'] }} mes{{ $fila['frecuencia'] > 1 ? 'es' : '' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <strong class="text-{{ $niveles[$nivel]['color'] }}">
                                    {{ $fila['porcentaje'] }}
                                </strong>
                            </td>
                            <td class="text-muted">
                                {{ $explicacion }}
                                <br>
                                <small>
                                    <i class="bi {{ $niveles[$nivel]['icon'] }}"></i> 
                                    {{ $niveles[$nivel]['text'] }}
                                </small>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        

        
        <!-- RECOMENDACIONES PERSONALIZADAS -->
        <div class="alert alert-light border">
            <h5 class="text-primary mb-3">
                <i class="bi bi-lightbulb"></i> Recomendaciones basadas en tu análisis:
            </h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-cash-coin fs-4 text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>Objetivo de Control</h6>
                            <p class="mb-0">Intenta mantener tus gastos cerca de <strong>${{ number_format($montoIdeal ?? 0, 0) }}</strong> mensuales.</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-calendar-check fs-4 text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6>Consistencia</h6>
                            <p class="mb-0">{{ $porcentajeExtremos ?? 0 }}% de tus meses son extremos. Busca mayor estabilidad.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            @if(($porcentajeExtremos ?? 0) > 50)
            <div class="alert alert-warning mt-2">
                <i class="bi bi-exclamation-triangle"></i>
                <strong>Atención:</strong> Más de la mitad de tus meses tienen gastos extremos. 
                Considera crear un presupuesto más estricto.
            </div>
            @endif
        </div>
        
        <!-- SECCIÓN TÉCNICA (Colapsable) -->
        <details class="mt-4">
            <summary class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-gear"></i> Ver análisis estadístico detallado
            </summary>
            <div class="mt-3 p-3 bg-light rounded">
                <h6 class="text-muted mb-3">
                    <i class="bi bi-bar-chart"></i> Tabla Técnica de Frecuencias
                </h6>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Intervalo</th>
                                <th>Marca Clase</th>
                                <th>Frecuencia</th>
                                <th>%</th>
                                <th>Frec. Acum.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tablaFrecuencia as $fila)
                            <tr>
                                <td>{{ $fila['intervalo'] }}</td>
                                <td>${{ $fila['marca_clase'] }}</td>
                                <td>{{ $fila['frecuencia'] }}</td>
                                <td>{{ $fila['porcentaje'] }}</td>
                                <td>{{ $fila['frecuencia_acumulada'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-muted mt-2 small">
                    <i class="bi bi-info-circle"></i> 
                    Esta tabla usa estadística descriptiva (Regla de Sturges) para analizar 
                    la distribución de tus gastos. Los datos técnicos ayudan a identificar 
                    patrones que luego se traducen en recomendaciones prácticas.
                </p>
            </div>
        </details>
        
        @else
        <div class="alert alert-warning text-center py-5">
            <i class="bi bi-exclamation-triangle display-4 mb-3"></i>
            <h5>No hay datos suficientes</h5>
            <p class="mb-0">Necesitas al menos 2 meses de datos con gastos variables para ver este análisis.</p>
        </div>
        @endif
    </div>
</div>