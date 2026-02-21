
<div class="card shadow-lg border-0 mb-5 fade-in" id="analisis-section">
    <div class="card-header bg-primary text-white py-4">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-speedometer2 me-2"></i>Resultados del Análisis
            </h4>
            <span class="badge bg-light text-primary fs-6">
                <i class="bi bi-calendar-check me-1"></i>{{ now()->format('d/m/Y') }}
            </span>
        </div>
    </div>
    <div class="card-body">
        @php
    // Al inicio del archivo, después del div de card-body
    if (!isset($porcTotalGastos)) {
        $porcTotalGastos = 0;
        if (isset($promIngreso) && $promIngreso > 0) {
            $porcTotalGastos = min((($promFijos + $promDinamicos) / $promIngreso) * 100, 100);
        }
    }
@endphp
      
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card text-white bg-primary h-100 border-0 shadow">
            <div class="card-header d-flex align-items-center">
                <i class="bi bi-cash-coin fs-4 me-2"></i>
                Ingreso Promedio
            </div>
            <div class="card-body text-center py-4">
                <h2 class="card-title fw-bold display-6 mb-3">${{ number_format($promIngreso, 2) }}</h2>
                <p class="card-text opacity-75">Promedio mensual de ingresos familiares</p>
                <div class="mt-3">
                    <small class="opacity-75">
                        <i class="bi bi-arrow-up-right-circle me-1"></i>
                        {{ $porcFijos + $porcDinamicos < 100 ? 'Saludable' : 'Revisar' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD CONDICIONAL SEGÚN MODO --}}
    @if(isset($modo_analisis) && $modo_analisis == 'detallado')
        {{-- =========== MODO DETALLADO =========== --}}
        <div class="col-md-4">
            <div class="card text-dark bg-info h-100 border-0 shadow">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-pie-chart-fill fs-4 me-2"></i>
                    Gastos Totales
                </div>
                <div class="card-body text-center py-4">
                 {{--    <h2 class="card-title fw-bold display-6 mb-3">
                        ${{ number_format(($promFijos + $promDinamicos), 2) }}
                    </h2> --}}
                    <h2 class="card-title fw-bold display-6 mb-3">
    @if(isset($promGastos))
        ${{ number_format($promGastos, 2) }}  {{-- ✅ Usar promGastos --}}
    @else
        ${{ number_format(($promFijos + $promDinamicos), 2) }}
    @endif
</h2>
                    <p class="card-text">Suma de todos los gastos mensuales</p>
                    <div class="progress mt-3" style="height: 8px;">
                       {{--  @php
                            $porcTotalGastos = min($porcFijos + $porcDinamicos, 100);
                            $claseBarra = $porcTotalGastos > 80 ? 'bg-danger' : 
                                         ($porcTotalGastos > 60 ? 'bg-warning' : 'bg-success');
                        @endphp --}}
                        @php
    // ✅ CORRECCIÓN: Usar promGastos si está disponible (modo detallado)
    $porcTotalGastos = 0;
    
    if (isset($promGastos) && $promIngreso > 0) {
        // Modo detallado: usar promGastos
        $porcTotalGastos = min(($promGastos / $promIngreso) * 100, 100);
    } elseif ($promIngreso > 0) {
        // Modo rápido: usar promFijos + promDinamicos
        $porcTotalGastos = min((($promFijos + $promDinamicos) / $promIngreso) * 100, 100);
    }
    
    $claseBarra = 'bg-secondary'; // Por defecto
    if ($promIngreso > 0) {
        $claseBarra = $porcTotalGastos > 80 ? 'bg-danger' : 
                     ($porcTotalGastos > 60 ? 'bg-warning' : 'bg-success');
    }
@endphp
                        <div class="progress-bar {{ $claseBarra }}" style="width: {{ $porcTotalGastos }}%"></div>
                    </div>
                    <small class="mt-2 d-block">{{ number_format($porcTotalGastos, 1) }}% de tus ingresos</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-secondary h-100 border-0 shadow">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-tags-fill fs-4 me-2"></i>
                    Categoría Principal
                </div>
                <div class="card-body text-center py-4">
                    
                    @if(isset($analisis_categorias) && count($analisis_categorias) > 0)
                        @php
                            $categoriaPrincipal = $analisis_categorias[0]['categoria']->name ?? 'No definida';
                            $porcPrincipal = $analisis_categorias[0]['porcentaje'] ?? 0;
                        @endphp
                        <h4 class="card-title fw-bold mb-3">{{ $categoriaPrincipal }}</h4>
                        <h2 class="display-6 fw-bold mb-3">{{ number_format($porcPrincipal, 1) }}%</h2>
                        <p class="card-text opacity-75">Principal categoría de gasto</p>
                    @else
                        <h4 class="card-title fw-bold mb-3">Sin datos</h4>
                        <p class="card-text opacity-75">No hay categorías analizadas</p>
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- =========== MODO RÁPIDO (basico/simple) =========== --}}
        <div class="col-md-4">
            <div class="card text-dark bg-warning h-100 border-0 shadow">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-house-door-fill fs-4 me-2"></i>
                    Promedio Gastos Fijos
                </div>
                <div class="card-body text-center py-4">
                    <h2 class="card-title fw-bold display-6 mb-3">${{ number_format($promFijos, 2) }}</h2>
                    <p class="card-text">Promedio de gastos obligatorios mensuales</p>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-dark" style="width: {{ min($porcFijos, 100) }}%"></div>
                    </div>
                    <small class="mt-2 d-block">{{ number_format($porcFijos, 1) }}% de tus ingresos</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-dark bg-warning h-100 border-0 shadow">
                <div class="card-header d-flex align-items-center">
                    <i class="bi bi-currency-exchange fs-4 me-2"></i>
                    Promedio Gastos Dinámicos
                </div>
                <div class="card-body text-center py-4">
                    <h2 class="card-title fw-bold display-6 mb-3">${{ number_format($promDinamicos, 2) }}</h2>
                    <p class="card-text">Promedio de gastos variables mensuales</p>
                    <div class="progress mt-3" style="height: 8px;">
                        <div class="progress-bar bg-dark" style="width: {{ min($porcDinamicos, 100) }}%"></div>
                    </div>
                    <small class="mt-2 d-block">{{ number_format($porcDinamicos, 1) }}% de tus ingresos</small>
                </div>
            </div>
        </div>
    @endif

    {{-- CARD DE SALDO (igual para ambos modos) --}}
    <div class="col-md-4">
        <div class="card text-white bg-success h-100 border-0 shadow">
            <div class="card-header d-flex align-items-center">
                <i class="bi bi-piggy-bank-fill fs-4 me-2"></i>
                Saldo Disponible
            </div>
            <div class="card-body text-center py-4">
                <h2 class="card-title fw-bold display-6 mb-3">${{ number_format($saldo, 2) }}</h2>
                <p class="card-text opacity-75">Dinero para ahorrar o invertir cada mes</p>
                <div class="mt-3">
                    @if($saldo > 0)
                        <span class="badge bg-light text-success fs-6">
                            <i class="bi bi-check-circle me-1"></i>Positivo
                        </span>
                    @else
                        <span class="badge bg-light text-danger fs-6">
                            <i class="bi bi-exclamation-triangle me-1"></i>Negativo
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
        <!-- Medidas de tendencia central -->
        @include('analisis.partials.central-tendency')

        <!-- Medidas de dispersión -->
        @include('analisis.partials.dispersion')

        <!-- Indicadores financieros -->
        @include('analisis.partials.financial-indicators')

        <!-- Gráficos -->
        @include('analisis.partials.charts')

        <!-- Conclusiones automáticas -->
        @include('analisis.partials.conclusions')

        <!-- Proyección Anual -->
        @include('analisis.partials.annual-projection')
    </div>
</div>