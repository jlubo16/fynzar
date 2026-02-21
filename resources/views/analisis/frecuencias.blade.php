@extends('layouts.app')

@section('title', 'Tablas de Frecuencia - FinanzAnalyzer')

@section('content')
<div class="container py-4">
    
    
    <!-- Header específico para frecuencias -->
    @include('analisis.partials.frecuencias.header')
    
    <!-- Resumen rápido (4 cards) -->
    @include('analisis.partials.frecuencias.resumen-rapido')
    
    <!-- Tabla de frecuencia por intervalos -->
    @include('analisis.partials.frecuencias.tabla-intervalos')
    
    <!-- Gráficos de frecuencia -->
    @include('analisis.partials.frecuencias.graficos-frecuencias')
    
    <!-- Tabla de frecuencia por categorías -->
    @include('analisis.partials.frecuencias.tabla-categorias')
    
    <!-- Medidas estadísticas -->
    @include('analisis.partials.frecuencias.medidas-estadisticas')
    
    <!-- Navegación entre módulos -->
    @include('analisis.partials.frecuencias.navegacion')
</div>

{{-- CARGAR Chart.js CON URL ABSOLUTA Y PROTOCOLO HTTPS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

{{-- Pasar datos a JavaScript --}}
@if(!empty($histogramaData))
<script>
    window.histogramaData = @json($histogramaData);
    console.log('histogramaData cargado:', window.histogramaData);
</script>
@endif

@if(!empty($categoriasData))
<script>
    window.tablaCategoriasData = @json($categoriasData);
    console.log('tablaCategoriasData cargado:', window.tablaCategoriasData);
</script>
@endif

{{-- Nuestro script --}}
<script src="{{ asset('js/frecuencias.js') }}"></script>
@endsection