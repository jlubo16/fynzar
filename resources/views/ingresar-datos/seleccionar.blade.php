@extends('layouts.app')

@section('title', 'Seleccionar Método - FinanzAnalyzer')

@section('content')
    <!-- Header -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-gradient mb-3">
            <i class="bi bi-list-check me-2"></i>Selecciona tu Método de Análisis
        </h1>
        <p class="lead text-muted">Elige cómo prefieres ingresar tus datos financieros</p>
        
        <!-- Indicador de pasos -->
        <div class="steps mb-4">
            <div class="step completed">
                <span class="step-number">1</span>
                <span class="step-label">Bienvenida</span>
            </div>
            <div class="step active">
                <span class="step-number">2</span>
                <span class="step-label">Seleccionar</span>
            </div>
            <div class="step">
                <span class="step-number">3</span>
                <span class="step-label">Ingresar Datos</span>
            </div>
            <div class="step">
                <span class="step-number">4</span>
                <span class="step-label">Resultados</span>
            </div>
        </div>
    </div>

    <!-- Opciones -->
    <div class="row g-4">
        <!-- Modo Rápido -->
        <div class="col-md-4">
            <div class="card h-100 border-primary shadow-sm">
                <div class="card-body text-center">
                    <div class="icon-large mb-3">
                        <i class="bi bi-lightning-charge text-primary"></i>
                    </div>
                    <h3 class="card-title">Modo Rápido</h3>
                    <p class="card-text text-muted">
                        Ideal para análisis básicos. Solo ingresa totales mensuales.
                    </p>
                    <ul class="text-start text-muted small">
                        <li>Ingresos totales por mes</li>
                        <li>Gastos fijos totales</li>
                        <li>Gastos variables totales</li>
                    </ul>
                    <a href="{{ route('formularios.rapido') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-play me-1"></i> Elegir Rápido
                    </a>
                </div>
                <div class="card-footer bg-transparent">
                    <small><i class="bi bi-clock"></i> ~3 minutos</small>
                </div>
            </div>
        </div>

        <!-- Modo Detallado -->
        <div class="col-md-4">
            <div class="card h-100 border-success shadow-sm">
                <div class="card-body text-center">
                    <div class="icon-large mb-3">
                        <i class="bi bi-bar-chart text-success"></i>
                    </div>
                    <h3 class="card-title">Modo Detallado</h3>
                    <p class="card-text text-muted">
                        Análisis completo con categorización de cada gasto.
                    </p>
                    <ul class="text-start text-muted small">
                        <li>Categorías y subcategorías</li>
                        <li>Análisis por tipo de gasto</li>
                        <li>Recomendaciones específicas</li>
                    </ul>
                    <a href="{{ route('formularios.detallado') }}" class="btn btn-success mt-3">
                        <i class="bi bi-list-check me-1"></i> Elegir Detallado
                    </a>
                </div>
                <div class="card-footer bg-transparent">
                    <small><i class="bi bi-clock"></i> ~10 minutos</small>
                </div>
            </div>
        </div>

        <!-- Importar CSV -->
        <div class="col-md-4">
            <div class="card h-100 border-info shadow-sm">
                <div class="card-body text-center">
                    <div class="icon-large mb-3">
                        <i class="bi bi-file-earmark-excel text-info"></i>
                    </div>
                    <h3 class="card-title">Importar CSV</h3>
                    <p class="card-text text-muted">
                        Sube datos desde Excel/Google Sheets. Perfecto si ya tienes registros.
                    </p>
                    <ul class="text-start text-muted small">
                        <li>Plantilla descargable</li>
                        <li>Importación masiva</li>
                        <li>Datos históricos</li>
                    </ul>
                    <a href="{{ route('formularios.csv') }}" class="btn btn-info mt-3">
                        <i class="bi bi-upload me-1"></i> Elegir CSV
                    </a>
                </div>
                <div class="card-footer bg-transparent">
                    <small><i class="bi bi-clock"></i> ~2 minutos</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Volver -->
    <div class="text-center mt-4">
        <a href="{{ route('bienvenida') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver a Bienvenida
        </a>
    </div>
@endsection

@push('styles')
<style>
.steps {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 30px;
}
.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
}
.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    background: #e9ecef;
    color: #6c757d;
}
.step.active .step-number {
    background: #0d6efd;
    color: white;
}
.step.completed .step-number {
    background: #198754;
    color: white;
}
.step-label {
    font-size: 0.9rem;
    color: #6c757d;
}
.step.active .step-label {
    font-weight: bold;
    color: #0d6efd;
}
.icon-large {
    font-size: 3rem;
}
.card {
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-5px);
}
</style>
@endpush