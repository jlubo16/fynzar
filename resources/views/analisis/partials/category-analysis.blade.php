<!-- NUEVA SECCIÓN PARA ANÁLISIS DETALLADO -->
@if(isset($modo_analisis) && $modo_analisis === 'detallado')
<div class="card mb-4 shadow-lg border-0">
    <div class="card-header bg-info text-white py-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-bar-chart me-2"></i> Análisis por Categorías
        </h4>
    </div>
    <div class="card-body">
        @if(isset($analisis_categorias) && count($analisis_categorias) > 0)
        <div class="row g-4">
            <div class="col-md-6">
                <h5 class="fw-bold text-primary mb-3">📊 Distribución por Categoría</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover shadow-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-bold">Categoría</th>
                                <th class="fw-bold text-end">Total</th>
                                <th class="fw-bold text-end">Porcentaje</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analisis_categorias as $analisis)
                            <tr>
                                <td class="fw-bold">{{ $analisis['categoria']->name }}</td>
                                <td class="text-end fw-bold">${{ number_format($analisis['total'], 2) }}</td>
                                <td class="text-end fw-bold text-info">{{ number_format($analisis['porcentaje'], 1) }}%</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="col-md-6">
                <h5 class="fw-bold text-primary mb-3">📈 Tendencias Mensuales</h5>
                @if(isset($tendencias_mensuales))
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="bi bi-graph-up-arrow me-2"></i>Ingresos:
                        </h6>
                        <p class="mb-0">{{ $tendencias_mensuales['analisis']['ingresos']['interpretacion'] ?? 'No disponible' }}</p>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="fw-bold text-warning mb-3">
                            <i class="bi bi-graph-down-arrow me-2"></i>Gastos:
                        </h6>
                        <p class="mb-0">{{ $tendencias_mensuales['analisis']['gastos']['interpretacion'] ?? 'No disponible' }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @else
        <div class="text-center py-4">
            <i class="bi bi-bar-chart display-4 text-muted mb-3"></i>
            <p class="text-muted fs-5">No hay datos de categorías para mostrar.</p>
        </div>
        @endif
    </div>
</div>
@endif