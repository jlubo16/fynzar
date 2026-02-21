<!-- resources/views/analisis/partials/frecuencias/navegacion.blade.php -->
<div class="text-center mt-4">
    <div class="btn-group" role="group">
        <a href="{{ route('analisis.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-speedometer2"></i> Análisis Principal
        </a>
        <a href="{{ route('analisis.tendencias') }}" class="btn btn-outline-success">
            <i class="bi bi-graph-up"></i> Análisis de Tendencias
        </a>
        <a href="{{ route('analisis.comparativo') }}" class="btn btn-outline-info">
            <i class="bi bi-bar-chart"></i> Análisis Comparativo
        </a>
    </div>
</div>