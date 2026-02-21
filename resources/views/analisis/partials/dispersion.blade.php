
@php
    // Variables nuevas (modo detallado)
    $desviacionGastos     = $datos['desviacion_gastos']     ?? 0;
    $rangoGastos          = $datos['rango_gastos']          ?? 0;
    $cvGastos             = $datos['cv_gastos']             ?? 0;

    // Variables antiguas (modo rápido - compatibilidad)
    $desviacion           = $datos['desviacion']           ?? 0;
    $rangoDinamicos       = $datos['rangoDinamicos']       ?? 0;
    $cvGastosDinamicos    = $datos['cvGastosDinamicos']    ?? 0;
    $rangoFijos           = $datos['rangoFijos']           ?? 0;

    $desviacionIngresos   = $datos['desviacion_ingresos']   ?? 0;
    $rangoIngresos        = $datos['rango_ingresos']        ?? 0;
    $cvIngresos           = $datos['cv_ingresos']           ?? 0;

    $esModoDetallado = isset($datos['modo_analisis']) && $datos['modo_analisis'] === 'detallado';

    // Condición corregida: chequea las variables reales según el modo
    $tieneDatosDispersion = 
        ($esModoDetallado && ($desviacionGastos > 0 || $rangoGastos > 0 || $cvGastos > 0)) ||
        (!$esModoDetallado && ($desviacion > 0 || $rangoDinamicos > 0 || $cvGastosDinamicos > 0 || $rangoFijos > 0)) ||
        $desviacionIngresos > 0 || $rangoIngresos > 0 || $cvIngresos > 0;
@endphp
<!-- Medidas de dispersión -->
<div class="mb-5">
    <h5 class="text-primary fw-bold mb-4">
        <i class="bi bi-bar-chart-fill me-2"></i>📌 Medidas de Dispersión
    </h5>

    @if(!$tieneDatosDispersion)
        <div class="card border-info mb-4 shadow-sm">
            <div class="card-header bg-info bg-opacity-10 border-info">
                <div class="d-flex align-items-center">
                    <i class="bi bi-info-circle-fill text-info fs-4 me-3"></i>
                    <div>
                        <h6 class="card-title mb-0 text-info">📊 Datos insuficientes para análisis de dispersión</h6>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p class="card-text mb-2">
                    Las medidas de dispersión requieren datos de al menos 2 meses con valores diferentes.
                </p>
                <p class="card-text mb-0">
                    <small class="text-muted">
                        💡 Agrega más meses o valores que varíen para ver este análisis completo.
                    </small>
                </p>
            </div>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle shadow-sm">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold">Concepto</th>
                        <th class="fw-bold text-end">Valor</th>
                        <th class="fw-bold">Interpretación</th>
                        <th class="fw-bold text-center">Fórmula</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Desviación Estándar Gastos -->
                    <tr>
                        <td class="fw-bold">
                            <i class="bi bi-graph-up text-primary me-2"></i>
                            @if($esModoDetallado)
                                Desviación Estándar Gastos
                            @else
                                Desviación Estándar Gastos Dinámicos
                            @endif
                        </td>
                        <td class="text-end fw-bold text-primary">
                            ${{ number_format($esModoDetallado ? $desviacionGastos : $desviacion, 2) }}
                        </td>
                        <td>
                            <strong>¿Qué significa?</strong> 
                            @if($esModoDetallado)
                                Mide cuánto varían tus gastos mes a mes
                            @else
                                Mide cuánto varían tus gastos variables mes a mes
                            @endif
                            <br>
                            <small class="text-muted">
                                Un valor de ${{ number_format($esModoDetallado ? $desviacionGastos : $desviacion, 2) }} indica que tus gastos típicamente se alejan esta cantidad del promedio mensual.
                            </small>
                        </td>
                        <td class="text-center">
                            @if(($esModoDetallado ? $desviacionGastos : $desviacion) > 0)
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#formulaDesviacion">
                                    <i class="bi bi-info-circle"></i> Ver Fórmula
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Rango Gastos -->
                    <tr>
                        <td class="fw-bold">
                            <i class="bi bi-arrow-left-right text-info me-2"></i>
                            @if($esModoDetallado)
                                Rango Gastos
                            @else
                                Rango Gastos Dinámicos
                            @endif
                        </td>
                        <td class="text-end fw-bold text-info">
                            ${{ number_format($esModoDetallado ? $rangoGastos : $rangoDinamicos, 2) }}
                        </td>
                        <td>
                            <strong>¿Qué significa?</strong> Amplitud de fluctuación 
                            @if($esModoDetallado)
                                en todos tus gastos
                            @else
                                en gastos variables
                            @endif
                            <br>
                            <small class="text-muted">
                                Tus gastos varían hasta ${{ number_format($esModoDetallado ? $rangoGastos : $rangoDinamicos, 2) }} entre meses.
                            </small>
                        </td>
                        <td></td>
                    </tr>

                    <!-- Coeficiente Variación Gastos -->
                    <tr>
                        <td class="fw-bold">
                            <i class="bi bi-percent text-warning me-2"></i>
                            @if($esModoDetallado)
                                Coeficiente Variación Gastos
                            @else
                                Coeficiente Variación Gastos Dinámicos
                            @endif
                        </td>
                        <td class="text-end fw-bold text-warning">
                            {{ number_format($esModoDetallado ? $cvGastos : $cvGastosDinamicos, 2) }}%
                        </td>
                        <td>
                            <strong>¿Qué significa?</strong> Qué tan controlados están tus gastos
                            @if($esModoDetallado)
                                en todos tus gastos
                            @else
                                variables
                            @endif
                            <br>
                            <small class="text-muted">
                                {{ number_format($esModoDetallado ? $cvGastos : $cvGastosDinamicos, 2) }}% de variación.
                                @if(($esModoDetallado ? $cvGastos : $cvGastosDinamicos) < 15)
                                    <span class="text-success d-block mt-1">✅ Bien controlados</span>
                                @elseif(($esModoDetallado ? $cvGastos : $cvGastosDinamicos) > 30)
                                    <span class="text-warning d-block mt-1">⚠️ Requiere más control</span>
                                @endif
                            </small>
                        </td>
                        <td></td>
                    </tr>

                    <!-- Rango Gastos Fijos (solo modo rápido) -->
                    @if(!$esModoDetallado && $rangoFijos >= 0)
                    <tr>
                        <td class="fw-bold">
                            <i class="bi bi-arrow-left-right text-warning me-2"></i>Rango Gastos Fijos
                        </td>
                        <td class="text-end fw-bold text-warning">${{ number_format($rangoFijos, 2) }}</td>
                        <td>
                            <strong>¿Qué significa?</strong> Consistencia de obligaciones mensuales<br>
                            <small class="text-muted">
                                Los gastos fijos deberían ser constantes. Un rango de ${{ number_format($rangoFijos, 2) }}
                                @if($rangoFijos > 0)
                                    sugiere que algunos "gastos fijos" no son realmente fijos.
                                @else
                                    indica perfecta consistencia.
                                @endif
                            </small>
                        </td>
                        <td></td>
                    </tr>
                    @endif

                    <!-- Desviación Estándar Ingresos -->
                    @if($desviacionIngresos > 0)
                    <tr>
                        <td class="fw-bold">
                            <i class="bi bi-cash text-success me-2"></i>Desviación Estándar Ingresos
                        </td>
                        <td class="text-end fw-bold text-success">${{ number_format($desviacionIngresos, 2) }}</td>
                        <td>
                            <strong>¿Qué significa?</strong> Mide la estabilidad de tus ingresos<br>
                            <small class="text-muted">
                                Una desviación de ${{ number_format($desviacionIngresos, 2) }}
                                sugiere que tus ingresos varían significativamente entre meses.
                            </small>
                        </td>
                        <td></td>
                    </tr>
                    @endif

                    <!-- Rango Ingresos -->
                    @if($rangoIngresos > 0)
                    <tr>
                        <td class="fw-bold">
                            <i class="bi bi-arrow-left-right text-success me-2"></i>Rango Ingresos
                        </td>
                        <td class="text-end fw-bold text-success">${{ number_format($rangoIngresos, 2) }}</td>
                        <td>
                            <strong>¿Qué significa?</strong> Diferencia entre tu mejor y peor mes<br>
                            <small class="text-muted">
                                Hay ${{ number_format($rangoIngresos, 2) }} de diferencia entre tu mes con mayores y menores ingresos.
                            </small>
                        </td>
                        <td></td>
                    </tr>
                    @endif

                    <!-- Coeficiente Variación Ingresos -->
                    @if($cvIngresos > 0)
                    <tr>
                        <td class="fw-bold">
                            <i class="bi bi-percent text-info me-2"></i>Coeficiente Variación Ingresos
                        </td>
                        <td class="text-end fw-bold text-info">{{ number_format($cvIngresos, 2) }}%</td>
                        <td>
                            <strong>¿Qué significa?</strong> Variación porcentual respecto al promedio<br>
                            <small class="text-muted">
                                {{ number_format($cvIngresos, 2) }}% significa que tus ingresos varían aproximadamente esta proporción respecto a su valor promedio. 
                                @if($cvIngresos > 30)
                                    <span class="text-warning d-block mt-1">⚠️ Alta variación</span>
                                @elseif($cvIngresos < 15)
                                    <span class="text-success d-block mt-1">✅ Buena estabilidad</span>
                                @endif
                            </small>
                        </td>
                        <td></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    @endif
</div>