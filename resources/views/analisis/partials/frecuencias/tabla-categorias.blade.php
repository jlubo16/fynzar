{{-- <!-- resources/views/analisis/partials/frecuencias/tabla-categorias.blade.php -->
<div class="card mb-4">
    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-pie-chart"></i> Frecuencia por Categorías de Gastos</h4>
        <button class="btn btn-sm btn-light" onclick="exportarTabla('tabla-categorias')">
            <i class="bi bi-download"></i> Exportar
        </button>
    </div>
    <div class="card-body">
        @if(!empty($tablaCategorias))
        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover" id="tabla-categorias">
                <thead class="table-light">
                    <tr>
                        <th>Categoría</th>
                        <th>Frecuencia (f)</th>
                        <th>Frec. Acumulada</th>
                        <th>Porcentaje</th>
                        <th>% Acumulado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tablaCategorias as $fila)
                    <tr>
                        <td>{{ $fila['categoria'] }}</td>
                        <td>{{ $fila['frecuencia'] }}</td>
                        <td>{{ $fila['frecuencia_acumulada'] }}</td>
                        <td>{{ $fila['porcentaje'] }}</td>
                        <td>{{ $fila['porcentaje_acumulado'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-8">
                <canvas id="categoriasChart" height="250"></canvas>
            </div>
            <div class="col-md-4">
                <div class="alert alert-secondary">
                    <h6><i class="bi bi-info-circle"></i> Análisis Categorías:</h6>
                    <small>
                        @php
                            $categoriaPrincipal = $tablaCategorias[0]['categoria'] ?? '';
                            $porcentajePrincipal = $tablaCategorias[0]['porcentaje'] ?? '0%';
                        @endphp
                        Tu mayor gasto es en <strong>{{ $categoriaPrincipal }}</strong> ({{ $porcentajePrincipal }})
                    </small>
                </div>
            </div>
        </div>
        @else
        <div class="alert alert-warning text-center">
            <i class="bi bi-exclamation-triangle"></i> No hay datos de categorías disponibles
        </div>
        @endif
    </div>
</div> --}}

@if(isset($tablaCategorias['tipo']) && $tablaCategorias['tipo'] === 'insuficiente')
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        {{ $tablaCategorias['mensaje'] }}
    </div>
@elseif(!empty($tablaCategorias))
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Categoría</th>
                <th class="text-center">Veces que gastaste</th>
                <th class="text-center">% del total</th>
                <th class="text-center">Frecuencia mensual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tablaCategorias as $fila)
            <tr>
                <td><strong>{{ $fila['categoria'] }}</strong></td>
                <td class="text-center">
                    <span class="badge bg-primary rounded-pill">
                        {{ $fila['frecuencia'] }} vez{{ $fila['frecuencia'] > 1 ? 'es' : '' }}
                    </span>
                </td>
                <td class="text-center">
                    <strong class="text-info">{{ $fila['porcentaje'] }}</strong>
                </td>
                <td class="text-center">
                    @php
                        $frecuenciaMensual = $fila['frecuencia'] / 3; // 3 meses
                    @endphp
                    {{ number_format($frecuenciaMensual, 1) }} vez{{ $frecuenciaMensual > 1 ? 'es' : '' }}/mes
                </td>
            </tr>
            @endforeach
            
        </tbody>
    </table>
@endif