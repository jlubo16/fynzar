<!-- resources/views/analisis/partials/frecuencias/resumen-rapido.blade.php -->
@if(isset($datosAnalisis))
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body text-center">
                <h6>Meses Analizados</h6>
              <h4>{{ $meses_analizados ?? count($datosAnalisis['ingresos'] ?? []) }}</h4>
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
                <h6>Saldo Mensual</h6>
                <h4>${{ number_format($datosAnalisis['saldo'] ?? 0, 0) }}</h4>
            </div>
        </div>
    </div>
</div>
@endif