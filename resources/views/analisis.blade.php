@extends('layouts.app')

@section('title', 'Análisis Financiero Familiar')

@push('chart-js')
    @if($promIngreso > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endif
@endpush

@section('content')
    <!-- Loading Spinner Local -->
    <div id="loading-spinner" class="loading-overlay" style="display: none;">
        <div class="spinner-container">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Procesando...</span>
            </div>
            <p class="mt-3 fs-5 text-primary">Analizando sus datos financieros...</p>
            <p class="text-muted">Esto puede tomar unos segundos</p>
        </div>
    </div>

    <!-- Header -->
    <div class="text-center mb-5 fade-in">
        <h1 class="display-4 fw-bold text-gradient mb-3">
            <i class="bi bi-graph-up me-2"></i>Análisis Automático de Finanzas Familiares
        </h1>
        <p class="lead text-muted fs-4">Toma el control de tus finanzas con nuestro análisis inteligente</p>
        <div class="mt-4">
            <span class="badge bg-primary me-2"><i class="bi bi-shield-check"></i> Seguro</span>
            <span class="badge bg-success me-2"><i class="bi bi-lightning"></i> Rápido</span>
            <span class="badge bg-info"><i class="bi bi-graph-up-arrow"></i> Preciso</span>
        </div>
    </div>

    <!-- Sección de Cómo Calcular -->
    @include('analisis.partials.how-to-calculate')

    <!-- Resultados del Análisis -->
    @if($promIngreso > 0)
        @include('analisis.partials.results')
        @include('analisis.partials.modals')
    @else
        <!-- Welcome Message -->
        @include('analisis.partials.welcome-message')
    @endif

    <!-- Mostrar errores si hay -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulario ÚNICO que maneja ambos modos -->
    @include('analisis.partials.data-form')

    <!-- Importar CSV -->
    @include('analisis.partials.csv-import')

    <!-- Análisis por Categorías (si aplica) -->
    @if(isset($modo_analisis) && $modo_analisis === 'detallado')
        @include('analisis.partials.category-analysis')
    @endif
@endsection

@push('scripts')
    @if($promIngreso > 0)
        <script>
            window.datosGraficos = {
                promIngreso:    {{ json_encode($promIngreso) }},
                promFijos:      {{ json_encode($promFijos) }},
                promDinamicos:  {{ json_encode($promDinamicos) }},
                porcFijos:      {{ json_encode($porcFijos) }},
                porcDinamicos:  {{ json_encode($porcDinamicos) }},
                saldo:          {{ json_encode($saldo) }}
            };
        </script>
        <script src="{{ asset('js/graficos.js') }}" defer></script>
    @endif
    <script src="{{ asset('js/analisis.js') }}" defer></script>
@endpush