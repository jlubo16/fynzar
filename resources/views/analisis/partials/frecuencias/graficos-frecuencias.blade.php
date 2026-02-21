<!-- resources/views/analisis/partials/frecuencias/graficos-frecuencias.blade.php -->
@if(!empty($histogramaData['data']))
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-bar-chart-line"></i> Histograma de Frecuencias</h5>
            </div>
            <div class="card-body">
                <canvas id="histogramaChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Polígono de Frecuencias</h5>
            </div>
            <div class="card-body">
                <canvas id="poligonoChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>
@endif