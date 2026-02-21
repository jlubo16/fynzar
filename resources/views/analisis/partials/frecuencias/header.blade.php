<!-- resources/views/analisis/partials/frecuencias/header.blade.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('analisis.index') }}">
            <i class="bi bi-arrow-left"></i> Volver al Análisis Principal
        </a>
        <div class="navbar-nav ms-auto">
            <a href="{{ route('analisis.tendencias') }}" class="nav-link">
                <i class="bi bi-graph-up"></i> Tendencias
            </a>
            <a href="{{ route('analisis.comparativo') }}" class="nav-link">
                <i class="bi bi-bar-chart"></i> Comparativo
            </a>
        </div>
    </div>
</nav>

<div class="text-center mb-4">
    <h1 class="display-5 text-primary">
        <i class="bi bi-table"></i> Análisis de Frecuencias
    </h1>
    <p class="lead">Distribución estadística de tus datos financieros</p>
</div>