@extends('layouts.app')

@section('title', 'Análisis de Tendencias - FinanzAnalyzer')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('analisis.index') }}">
                <i class="bi bi-arrow-left"></i> Volver al Análisis Principal
            </a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('analisis.comparativo') }}" class="nav-link">
                    <i class="bi bi-bar-chart"></i> Comparativo
                </a>
                <a href="{{ route('analisis.frecuencias') }}" class="nav-link">
                    <i class="bi bi-table"></i> Frecuencias
                </a>
            </div>
        </div>
    </nav>

    <div class="text-center mb-4">
        <h1 class="display-5 text-primary">
            <i class="bi bi-graph-up"></i> Análisis de Tendencias
        </h1>
        <p class="lead">Evolución y tendencias de tus finanzas a lo largo del tiempo</p>
    </div>

    <!-- Resumen -->
    @if(isset($datosAnalisis))
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body text-center">
                    <h6>Meses Analizados</h6>
                    <h4>{{ $mesesAnalizados ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body text-center">
                    <h6>Prom. Ingresos</h6>
                    <h4>${{ number_format($datosAnalisis['promIngreso'] ?? 0, 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body text-center">
                    <h6>Prom. Gastos</h6>
                    <h4>${{ number_format(($datosAnalisis['promFijos'] ?? 0) + ($datosAnalisis['promDinamicos'] ?? 0), 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body text-center">
                    <h6>Saldo Promedio</h6>
                    <h4>${{ number_format($datosAnalisis['saldo'] ?? 0, 0) }}</h4>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Análisis General de Tendencias -->
    @if(isset($tendencias['analisis_general']))
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="bi bi-speedometer2"></i> Tendencias Generales</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Tendencia Ingresos</h6>
                            @php
                                $tendencia = $tendencias['analisis_general']['tendencia_ingresos'] ?? 'estable';
                                $color = str_contains($tendencia, 'crecimiento') ? 'success' : 
                                        (str_contains($tendencia, 'decrecimiento') ? 'danger' : 'warning');
                                $icono = str_contains($tendencia, 'crecimiento') ? 'arrow-up' : 
                                        (str_contains($tendencia, 'decrecimiento') ? 'arrow-down' : 'dash');
                            @endphp
                            <h4 class="text-{{ $color }}">
                                <i class="bi bi-{{ $icono }}"></i>
                                {{ ucfirst(str_replace('_', ' ', $tendencia)) }}
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Tendencia Gastos</h6>
                            @php
                                $tendencia = $tendencias['analisis_general']['tendencia_gastos'] ?? 'estable';
                                $color = str_contains($tendencia, 'crecimiento') ? 'danger' : 
                                        (str_contains($tendencia, 'decrecimiento') ? 'success' : 'warning');
                                $icono = str_contains($tendencia, 'crecimiento') ? 'arrow-up' : 
                                        (str_contains($tendencia, 'decrecimiento') ? 'arrow-down' : 'dash');
                            @endphp
                            <h4 class="text-{{ $color }}">
                                <i class="bi bi-{{ $icono }}"></i>
                                {{ ucfirst(str_replace('_', ' ', $tendencia)) }}
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Tendencia Saldos</h6>
                            @php
                                $tendencia = $tendencias['analisis_general']['tendencia_saldos'] ?? 'estable';
                                $color = str_contains($tendencia, 'crecimiento') ? 'success' : 
                                        (str_contains($tendencia, 'decrecimiento') ? 'danger' : 'warning');
                                $icono = str_contains($tendencia, 'crecimiento') ? 'arrow-up' : 
                                        (str_contains($tendencia, 'decrecimiento') ? 'arrow-down' : 'dash');
                            @endphp
                            <h4 class="text-{{ $color }}">
                                <i class="bi bi-{{ $icono }}"></i>
                                {{ ucfirst(str_replace('_', ' ', $tendencia)) }}
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Gráfico de Tendencias -->
    @if(!empty($tendencias) && count($tendencias) > 1)
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="bi bi-graph-up"></i> Evolución Financiera</h4>
        </div>
        <div class="card-body">
            <canvas id="tendenciasChart" height="300"></canvas>
        </div>
    </div>
    @endif

    <!-- Tabla Detallada de Tendencias -->
    @if(!empty($tendencias) && count($tendencias) > 1)
    <div class="card mb-4">
        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-table"></i> Tendencias Mensuales Detalladas</h4>
            <button class="btn btn-sm btn-light" onclick="exportarTabla('tabla-tendencias')">
                <i class="bi bi-download"></i> Exportar
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-tendencias">
                    <thead class="table-light">
                        <tr>
                            <th>Mes</th>
                            <th>Ingresos</th>
                            <th>Gastos Totales</th>
                            <th>Saldo</th>
                            <th>Tendencia Ingresos</th>
                            <th>Tendencia Gastos</th>
                            <th>Estado Financiero</th>
                            <th>Recomendación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tendencias as $key => $tendencia)
                            @if($key !== 'analisis_general')
                            <tr class="@if($tendencia['estado_financiero'] == 'excelente') table-success 
                                      @elseif($tendencia['estado_financiero'] == 'critico') table-danger 
                                      @elseif($tendencia['estado_financiero'] == 'preocupante') table-warning 
                                      @endif">
                                <td><strong>Mes {{ $tendencia['mes'] }}</strong></td>
                                <td>${{ number_format($tendencia['ingreso'], 2) }}</td>
                                <td>${{ number_format($tendencia['gasto_total'], 2) }}</td>
                                <td><strong>${{ number_format($tendencia['saldo'], 2) }}</strong></td>
                                <td>
                                    @php
                                        $color = str_contains($tendencia['tendencia_ingreso'], 'creciente') ? 'success' : 
                                                (str_contains($tendencia['tendencia_ingreso'], 'decreciente') ? 'danger' : 'secondary');
                                        $icon = str_contains($tendencia['tendencia_ingreso'], 'creciente') ? 'arrow-up' : 
                                               (str_contains($tendencia['tendencia_ingreso'], 'decreciente') ? 'arrow-down' : 'dash');
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        <i class="bi bi-{{ $icon }}"></i>
                                        {{ ucfirst(str_replace('_', ' ', $tendencia['tendencia_ingreso'])) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $color = str_contains($tendencia['tendencia_gasto'], 'creciente') ? 'danger' : 
                                                (str_contains($tendencia['tendencia_gasto'], 'decreciente') ? 'success' : 'secondary');
                                        $icon = str_contains($tendencia['tendencia_gasto'], 'creciente') ? 'arrow-up' : 
                                               (str_contains($tendencia['tendencia_gasto'], 'decreciente') ? 'arrow-down' : 'dash');
                                    @endphp
                                    <span class="badge bg-{{ $color }}">
                                        <i class="bi bi-{{ $icon }}"></i>
                                        {{ ucfirst(str_replace('_', ' ', $tendencia['tendencia_gasto'])) }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $estadoColor = [
                                            'excelente' => 'success',
                                            'bueno' => 'info', 
                                            'regular' => 'warning',
                                            'preocupante' => 'danger',
                                            'critico' => 'dark',
                                            'sin_datos' => 'secondary'
                                        ][$tendencia['estado_financiero']] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $estadoColor }}">
                                        {{ ucfirst($tendencia['estado_financiero']) }}
                                    </span>
                                </td>
                                <td class="small">{{ $tendencia['recomendacion'] }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Navegación -->
    <div class="text-center mt-4">
        <div class="btn-group" role="group">
            <a href="{{ route('analisis.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-speedometer2"></i> Análisis Principal
            </a>
            <a href="{{ route('analisis.comparativo') }}" class="btn btn-outline-success">
                <i class="bi bi-bar-chart"></i> Análisis Comparativo
            </a>
            <a href="{{ route('analisis.frecuencias') }}" class="btn btn-outline-info">
                <i class="bi bi-table"></i> Tablas de Frecuencia
            </a>
        </div>
    </div>
</div>
<!-- CARGAR Chart.js ANTES de usarlo -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<!-- Script para gráficos -->
@if(!empty($tendencias) && count($tendencias) > 1)
<script>
    // Función para verificar y esperar Chart.js
    function inicializarGraficoTendencias() {
        // Verificar que Chart.js esté disponible
        if (typeof Chart === 'undefined') {
            console.error('Chart.js no está disponible. Recargando...');
            // Intentar cargar dinámicamente
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
            script.onload = crearGraficoTendencias;
            document.head.appendChild(script);
            return;
        }
        
        crearGraficoTendencias();
    }
    
    function crearGraficoTendencias() {
        console.log('Chart.js disponible, creando gráfico...');
        
        // Preparar datos para el gráfico
        const meses = [];
        const ingresos = [];
        const gastos = [];
        const saldos = [];

        @foreach($tendencias as $key => $tendencia)
            @if($key !== 'analisis_general')
            meses.push('Mes {{ $tendencia["mes"] }}');
            ingresos.push({{ $tendencia['ingreso'] }});
            gastos.push({{ $tendencia['gasto_total'] }});
            saldos.push({{ $tendencia['saldo'] }});
            @endif
        @endforeach

        const ctx = document.getElementById('tendenciasChart');
        if (!ctx) {
            console.error('No se encontró el canvas tendenciasChart');
            return;
        }

        try {
            new Chart(ctx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: meses,
                    datasets: [
                        {
                            label: 'Ingresos',
                            data: ingresos,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 3,
                            tension: 0.4
                        },
                        {
                            label: 'Gastos Totales',
                            data: gastos,
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            borderWidth: 2,
                            tension: 0.4
                        },
                        {
                            label: 'Saldos',
                            data: saldos,
                            borderColor: '#17a2b8',
                            backgroundColor: 'rgba(23, 162, 184, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            borderDash: [5, 5]
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });
            console.log('✅ Gráfico de tendencias creado exitosamente');
        } catch (error) {
            console.error('❌ Error creando gráfico:', error);
        }
    }
    
    // Inicializar cuando el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        inicializarGraficoTendencias();
    });
</script>
@endif

<script>
    // Función para exportar tabla (independiente de Chart.js)
    function exportarTabla(tableId) {
        const tabla = document.getElementById(tableId);
        if (!tabla) {
            alert('No se encontró la tabla');
            return;
        }
        
        let csv = [];
        
        // Obtener headers
        const headers = [];
        for (let i = 0; i < tabla.rows[0].cells.length; i++) {
            headers.push(tabla.rows[0].cells[i].innerText);
        }
        csv.push(headers.join(','));
        
        // Obtener datos
        for (let i = 1; i < tabla.rows.length; i++) {
            const row = [];
            for (let j = 0; j < tabla.rows[i].cells.length; j++) {
                row.push(tabla.rows[i].cells[j].innerText);
            }
            csv.push(row.join(','));
        }
        
        // Descargar
        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        
        a.href = url;
        a.download = `tendencias-financieras-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        alert('Tabla exportada como CSV');
    }

    window.exportarTabla = exportarTabla;
</script>
@endsection