<!-- Conclusiones automáticas -->
@php
    // ✅ Asegurar que $conclusion siempre sea un array
    $conclusiones = $conclusion ?? [];
@endphp

@if(count($conclusiones) > 0)
<div class="mb-5">
    <div class="card border-primary shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-lightbulb me-2"></i>💡 Conclusiones Automáticas
            </h5>
        </div>
        <div class="card-body">
            @foreach($conclusiones as $mensaje)
                <div class="alert alert-light border-start border-primary border-3 mb-3 alert-permanent">
                    <div class="d-flex">
                        <i class="bi bi-info-circle text-primary me-3 mt-1"></i>
                        <div>{!! $mensaje !!}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif