@php
    // ✅ DETECTAR MODO
    $esModoDetallado = ($datosAnalisis['modo_analisis'] ?? 'basico') == 'detallado';
@endphp


@extends('layouts.app')

@section('title', 'Análisis Comparativo - FinanzAnalyzer')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('analisis.index') }}">
                <i class="bi bi-arrow-left"></i> Volver al Análisis Principal
            </a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('analisis.tendencias') }}" class="nav-link">
                    <i class="bi bi-graph-up"></i> Tendencias
                </a>
                <a href="{{ route('analisis.frecuencias') }}" class="nav-link">
                    <i class="bi bi-table"></i> Frecuencias
                </a>
            </div>
        </div>
    </nav>

    <div class="text-center mb-4">
        <h1 class="display-5 text-primary">
            <i class="bi bi-bar-chart"></i> Análisis Comparativo
        </h1>
        <p class="lead">Comparativa mes a mes de tus finanzas</p>
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

    <!-- Tabla Comparativa -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="bi bi-table"></i> Comparativa Mes a Mes</h4>
            <button class="btn btn-sm btn-light" onclick="exportarTabla('tabla-comparativa')">
                <i class="bi bi-download"></i> Exportar
            </button>
        </div>
        <div class="card-body">
            @if(!empty($comparativos))
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover" id="tabla-comparativa">
                  <thead class="table-light">
    <tr>
        <th>Mes</th>
        <th>Ingresos</th>
        @if(!$esModoDetallado)
            <th>Gastos Fijos</th>
        @endif
        <th>
            @if($esModoDetallado)
                Gastos Totales
            @else
                Gastos Dinámicos
            @endif
        </th>
        <th>Total Gastos</th>
        <th>Saldo</th>
        @if(!$esModoDetallado)
            <th>% Fijos</th>
        @endif
        <th>
            @if($esModoDetallado)
                % Gastos
            @else
                % Dinámicos
            @endif
        </th>
        <th>Tendencia</th>
        <th>Análisis</th>
    </tr>
</thead>
                    <tbody>
                     @foreach($comparativos as $comparativo)
<tr class="@if($comparativo['saldo'] > 0) table-success @elseif($comparativo['saldo'] < 0) table-danger @else table-warning @endif">
    <td><strong>{{ $comparativo['mes'] }}</strong></td>
    <td>${{ number_format($comparativo['ingreso'], 2) }}</td>
    
    @if(!$esModoDetallado)
        <td>${{ number_format($comparativo['gasto_fijo'], 2) }}</td>
    @endif
    
    <td>${{ number_format($comparativo['gasto_dinamico'], 2) }}</td>
    <td><strong>${{ number_format($comparativo['gasto_total'], 2) }}</strong></td>
    <td><strong>${{ number_format($comparativo['saldo'], 2) }}</strong></td>
    
    @if(!$esModoDetallado)
        <td>{{ number_format($comparativo['porc_fijo'], 1) }}%</td>
    @endif
    
    <td>{{ number_format($comparativo['porc_dinamico'], 1) }}%</td>
    
    <td>
        @if($comparativo['tendencia'] == 'mejora')
        <span class="badge bg-success"><i class="bi bi-arrow-up"></i> Mejora</span>
        @elseif($comparativo['tendencia'] == 'deterioro')
        <span class="badge bg-danger"><i class="bi bi-arrow-down"></i> Deterioro</span>
        @else
        <span class="badge bg-secondary"><i class="bi bi-dash"></i> Estable</span>
        @endif
    </td>
    <td>
        @if($comparativo['saldo'] > 0)
        <span class="text-success"><i class="bi bi-check-circle"></i> Superávit</span>
        @elseif($comparativo['saldo'] < 0)
        <span class="text-danger"><i class="bi bi-exclamation-triangle"></i> Déficit</span>
        @else
        <span class="text-warning"><i class="bi bi-dash-circle"></i> Equilibrio</span>
        @endif
    </td>
</tr>
@endforeach
                    </tbody>
                  {{--   <tfoot class="table-secondary">
                        <tr>
                            <td><strong>Promedio</strong></td>
                            <td><strong>${{ number_format(collect($comparativos)->avg('ingreso'), 2) }}</strong></td>
                            <td><strong>${{ number_format(collect($comparativos)->avg('gasto_fijo'), 2) }}</strong></td>
                            <td><strong>${{ number_format(collect($comparativos)->avg('gasto_dinamico'), 2) }}</strong></td>
                            <td><strong>${{ number_format(collect($comparativos)->avg('gasto_total'), 2) }}</strong></td>
                            <td><strong>${{ number_format(collect($comparativos)->avg('saldo'), 2) }}</strong></td>
                            <td><strong>{{ number_format(collect($comparativos)->avg('porc_fijo'), 1) }}%</strong></td>
                            <td><strong>{{ number_format(collect($comparativos)->avg('porc_dinamico'), 1) }}%</strong></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot> --}}
                    <tfoot class="table-secondary">
    <tr>
        <td><strong>Promedio</strong></td>
        <td><strong>${{ number_format(collect($comparativos)->avg('ingreso'), 2) }}</strong></td>
        
        @if(!$esModoDetallado)
            <td><strong>${{ number_format(collect($comparativos)->avg('gasto_fijo'), 2) }}</strong></td>
        @endif
        
        <td><strong>${{ number_format(collect($comparativos)->avg('gasto_dinamico'), 2) }}</strong></td>
        <td><strong>${{ number_format(collect($comparativos)->avg('gasto_total'), 2) }}</strong></td>
        <td><strong>${{ number_format(collect($comparativos)->avg('saldo'), 2) }}</strong></td>
        
        @if(!$esModoDetallado)
            <td><strong>{{ number_format(collect($comparativos)->avg('porc_fijo'), 1) }}%</strong></td>
        @endif
        
        <td><strong>{{ number_format(collect($comparativos)->avg('porc_dinamico'), 1) }}%</strong></td>
        <td colspan="{{ $esModoDetallado ? 2 : 3 }}"></td>
    </tr>
</tfoot>
                </table>
            </div>
            @else
            <div class="alert alert-warning text-center">
                <i class="bi bi-exclamation-triangle"></i> No hay datos para análisis comparativo
            </div>
            @endif
        </div>
    </div>

    <!-- Gráfico Comparativo -->
    @if(!empty($comparativos))
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="bi bi-graph-up"></i> Evolución Mensual</h4>
        </div>
        <div class="card-body">
            <canvas id="comparativoChart" height="300"></canvas>
        </div>
    </div>
    @endif

    <!-- Navegación -->
    <div class="text-center mt-4">
        <div class="btn-group" role="group">
            <a href="{{ route('analisis.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-speedometer2"></i> Análisis Principal
            </a>
            <a href="{{ route('analisis.tendencias') }}" class="btn btn-outline-success">
                <i class="bi bi-graph-up"></i> Análisis de Tendencias
            </a>
            <a href="{{ route('analisis.frecuencias') }}" class="btn btn-outline-info">
                <i class="bi bi-table"></i> Tablas de Frecuencia
            </a>
        </div>
    </div>
</div>
<!-- CARGAR Chart.js ANTES de usar Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<!-- Script para gráficos -->
@if(!empty($comparativos))
<script>
    // Esperar a que Chart.js esté disponible
    function esperarChartJs(callback) {
        if (typeof Chart !== 'undefined') {
            callback();
        } else {
            setTimeout(() => esperarChartJs(callback), 100);
        }
    }
    
    // Inicializar gráfico cuando todo esté listo
    function inicializarGrafico() {
        const meses = @json(array_column($comparativos, 'mes'));
        const ingresos = @json(array_column($comparativos, 'ingreso'));
        const gastosFijos = @json(array_column($comparativos, 'gasto_fijo'));
        const gastosDinamicos = @json(array_column($comparativos, 'gasto_dinamico'));
        const saldos = @json(array_column($comparativos, 'saldo'));

        const ctx = document.getElementById('comparativoChart');
        if (!ctx) {
            console.error('No se encontró el canvas comparativoChart');
            return;
        }

        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: meses.map(m => 'Mes ' + m),
               /*  datasets: [
                    {
                        label: 'Ingresos',
                        data: ingresos,
                        borderColor: 'rgba(40, 167, 69, 1)',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Gastos Fijos',
                        data: gastosFijos,
                        borderColor: 'rgba(255, 193, 7, 1)',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Gastos Dinámicos',
                        data: gastosDinamicos,
                        borderColor: 'rgba(23, 162, 184, 1)',
                        backgroundColor: 'rgba(23, 162, 184, 0.1)',
                        borderWidth: 2,
                        tension: 0.3
                    },
                    {
                        label: 'Saldo',
                        data: saldos,
                        borderColor: 'rgba(108, 117, 125, 1)',
                        backgroundColor: 'rgba(108, 117, 125, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        borderDash: [5, 5]
                    }
                ] */
               // En la sección del gráfico, cambiar el dataset según modo
datasets: [
    {
        label: 'Ingresos',
        data: ingresos,
        borderColor: 'rgba(40, 167, 69, 1)',
        backgroundColor: 'rgba(40, 167, 69, 0.1)',
        borderWidth: 2,
        tension: 0.3
    },
    @if(!$esModoDetallado)
    {
        label: 'Gastos Fijos',
        data: gastosFijos,
        borderColor: 'rgba(255, 193, 7, 1)',
        backgroundColor: 'rgba(255, 193, 7, 0.1)',
        borderWidth: 2,
        tension: 0.3
    },
    @endif
    {
        label: '{{ $esModoDetallado ? "Gastos Totales" : "Gastos Dinámicos" }}',
        data: gastosDinamicos,
        borderColor: 'rgba(23, 162, 184, 1)',
        backgroundColor: 'rgba(23, 162, 184, 0.1)',
        borderWidth: 2,
        tension: 0.3
    },
    {
        label: 'Saldo',
        data: saldos,
        borderColor: 'rgba(108, 117, 125, 1)',
        backgroundColor: 'rgba(108, 117, 125, 0.1)',
        borderWidth: 2,
        tension: 0.3,
        borderDash: [5, 5]
    }
]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'Monto ($)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Meses'
                        }
                    }
                }
            }
        });
        
        console.log('Gráfico comparativo inicializado');
    }

    // Esperar a que el DOM esté listo y Chart.js disponible
    document.addEventListener('DOMContentLoaded', function() {
        esperarChartJs(inicializarGrafico);
    });
</script>
@endif

<script>
    // Función para exportar tabla (separada del gráfico)
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
        a.download = `analisis-comparativo-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        alert('Tabla exportada como CSV');
    }

    window.exportarTabla = exportarTabla;
</script>
@endsection