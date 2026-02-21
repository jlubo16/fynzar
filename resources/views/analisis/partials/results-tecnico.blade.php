{{-- resources/views/analisis/partials/results-tecnico.blade.php --}}
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