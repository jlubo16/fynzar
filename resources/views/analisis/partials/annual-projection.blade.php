
<!-- Proyección Anual -->
@php
    // ✅ AGREGAR ESTO AL INICIO: Definir valores por defecto
    $ingresoAnual = $ingresoAnual ?? 0;
    $gastosFijosAnual = $gastosFijosAnual ?? 0;
    $gastosDinamicosAnual = $gastosDinamicosAnual ?? 0;
    $saldoAnual = $saldoAnual ?? 0;
    
    // ✅ DETERMINAR SI ES MODO DETALLADO
    $esModoDetallado = isset($modo_analisis) && $modo_analisis == 'detallado';
@endphp

<div class="card shadow-sm border-0 mb-5">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <button class="btn btn-link text-decoration-none fw-bold w-100 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#proyeccionAnual" aria-expanded="false" aria-controls="proyeccionAnual">
                <i class="bi bi-calendar4-week me-2"></i>📆 Adicionalmente puedes ver tu proyección Anual, dando clic aquí!
                <i class="bi bi-chevron-down float-end"></i>
            </button>
        </h5>
    </div>
    <div id="proyeccionAnual" class="collapse">
        <div class="card-body">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th class="fw-bold">Concepto</th>
                        <th class="fw-bold text-end">Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Ingresos Anuales</td>
                        <td class="text-end fw-bold">${{ number_format($ingresoAnual, 2) }}</td>
                    </tr>
                    
                    @if(!$esModoDetallado)
                        {{-- =========== MODO RÁPIDO =========== --}}
                        <tr>
                            <td>Gastos Fijos Anuales</td>
                            <td class="text-end fw-bold text-warning">${{ number_format($gastosFijosAnual, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Gastos Dinámicos Anuales</td>
                            <td class="text-end fw-bold text-info">${{ number_format($gastosDinamicosAnual, 2) }}</td>
                        </tr>
                    @else
                        {{-- =========== MODO DETALLADO =========== --}}
                        <tr>
                            <td>Gastos Totales Anuales</td>  {{-- ✅ NUEVO NOMBRE --}}
                            <td class="text-end fw-bold text-info">${{ number_format($gastosDinamicosAnual, 2) }}</td>
                        </tr>
                        @if($gastosFijosAnual > 0)
                            {{-- Mostrar nota si hay gastos fijos (poco probable en detallado) --}}
                            <tr>
                                <td class="small text-muted">
                                    <i class="bi bi-info-circle me-1"></i>Incluye gastos de todas las categorías
                                </td>
                                <td class="text-end small text-muted">--</td>
                            </tr>
                        @endif
                    @endif
                    
                    <tr class="table-success">
                        <td><strong>Saldo Anual</strong></td>
                        <td class="text-end fw-bold"><strong>${{ number_format($saldoAnual, 2) }}</strong></td>
                    </tr>
                </tbody>
            </table>
            
            {{-- NOTA EXPLICATIVA PARA MODO DETALLADO --}}
            @if($esModoDetallado)
                <div class="alert alert-info mt-3 mb-0 alert-permanent">
                    <div class="d-flex">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        <div class="small">
                            <strong>Nota:</strong> En modo detallado, "Gastos Totales Anuales" incluye 
                            <strong>todos tus gastos combinados</strong> sin separación entre fijos y variables.
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>