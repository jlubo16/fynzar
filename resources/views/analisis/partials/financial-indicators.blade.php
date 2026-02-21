<!-- Indicadores financieros - SOLO PORCENTAJES -->
<div class="mb-5">
    <h5 class="text-primary fw-bold mb-4">
        <i class="bi bi-percent me-2"></i>📊 Estructura Porcentual
    </h5>
    
    @php
        // ✅ DETERMINAR SI ES MODO DETALLADO
        $esModoDetallado = isset($modo_analisis) && $modo_analisis == 'detallado';
        
        // ✅ CALCULAR PORCENTAJES CORRECTOS PARA CADA MODO
        if ($esModoDetallado) {
            // Modo detallado: usar porcTotalGastos
            $porcentajeGastos = $porcTotalGastos ?? ($porcDinamicos ?? 0);
            $porcentajeAhorro = 100 - $porcentajeGastos;
        } else {
            // Modo rápido: usar suma de porcFijos + porcDinamicos
            $porcentajeGastos = ($porcFijos ?? 0) + ($porcDinamicos ?? 0);
            $porcentajeAhorro = 100 - $porcentajeGastos;
        }
    @endphp
    
    {{-- ALERTA SI HAY DÉFICIT --}}
    @if($porcentajeGastos > 100)
    <div class="alert alert-danger mb-4 alert-permanent">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
            <div>
                <h6 class="alert-heading mb-1">🚨 <strong>DÉFICIT FINANCIERO</strong></h6>
                <p class="mb-0">Gastas <strong>{{ number_format($porcentajeGastos - 100, 1) }}% más</strong> 
                de lo que ganas. Necesitas financiamiento externo.</p>
            </div>
        </div>
    </div>
    @endif
    
    <div class="table-responsive">
        <table class="table table-hover align-middle shadow-sm">
            <thead class="table-light">
                <tr>
                    <th class="fw-bold">Componente</th>
                    <th class="fw-bold text-end">% del Ingreso</th>
                    <th class="fw-bold text-center">Estado</th>
                    <th class="fw-bold">Recomendación</th>
                </tr>
            </thead>
            <tbody>
                @if(!$esModoDetallado)
                    {{-- =========== MODO RÁPIDO =========== --}}
                    {{-- GASTOS FIJOS (solo modo rápido) --}}
                    <tr>
                        <td class="fw-bold">
                            <i class="bi bi-house-door-fill text-warning me-2"></i>Gastos Fijos
                        </td>
                        <td class="text-end fw-bold {{ $porcFijos > 50 ? 'text-danger' : ($porcFijos > 40 ? 'text-warning' : 'text-success') }}">
                            {{ number_format($porcFijos, 1) }}%
                        </td>
                        <td class="text-center">
                            @if($porcFijos > 50)
                                <span class="badge bg-danger rounded-pill">Alto</span>
                            @elseif($porcFijos > 40)
                                <span class="badge bg-warning rounded-pill">Moderado</span>
                            @else
                                <span class="badge bg-success rounded-pill">Saludable</span>
                            @endif
                        </td>
                        <td class="small">
                            @if($porcFijos > 50)
                                <span class="text-danger">⚠️ Reduce obligaciones fijas</span>
                            @elseif($porcFijos > 40)
                                <span class="text-warning">📊 En límite, monitorea</span>
                            @else
                                <span class="text-success">✅ Dentro de lo ideal (< 50%)</span>
                            @endif
                        </td>
                    </tr>
                    
                    {{-- GASTOS DINÁMICOS (solo modo rápido) --}}
                    <tr>
                        <td class="fw-bold">
                            <i class="bi bi-cart-fill text-info me-2"></i>Gastos Variables
                        </td>
                        <td class="text-end fw-bold {{ $porcDinamicos > 40 ? 'text-warning' : 'text-success' }}">
                            {{ number_format($porcDinamicos, 1) }}%
                        </td>
                        <td class="text-center">
                            @if($porcDinamicos > 40)
                                <span class="badge bg-warning rounded-pill">Variable</span>
                            @else
                                <span class="badge bg-success rounded-pill">Controlado</span>
                            @endif
                        </td>
                        <td class="small">
                            @if($porcDinamicos > 40)
                                <span class="text-warning">🎯 Establece presupuesto mensual</span>
                            @else
                                <span class="text-success">✅ Buen manejo de gastos flexibles</span>
                            @endif
                        </td>
                    </tr>
                @else
                    {{-- =========== MODO DETALLADO =========== --}}
                    {{-- GASTOS TOTALES (solo modo detallado) --}}
                    <tr>
                        <td class="fw-bold">
                            <i class="bi bi-pie-chart-fill text-primary me-2"></i>Gastos Totales
                        </td>
                        <td class="text-end fw-bold {{ $porcentajeGastos > 80 ? 'text-danger' : ($porcentajeGastos > 60 ? 'text-warning' : 'text-success') }}">
                            {{ number_format($porcentajeGastos, 1) }}%
                        </td>
                        <td class="text-center">
                            @if($porcentajeGastos > 80)
                                <span class="badge bg-danger rounded-pill">Crítico</span>
                            @elseif($porcentajeGastos > 60)
                                <span class="badge bg-warning rounded-pill">Moderado</span>
                            @else
                                <span class="badge bg-success rounded-pill">Saludable</span>
                            @endif
                        </td>
                        <td class="small">
                            @if($porcentajeGastos > 80)
                                <span class="text-danger">🚨 Necesita acción inmediata</span>
                            @elseif($porcentajeGastos > 60)
                                <span class="text-warning">📊 Considera reducir gastos</span>
                            @else
                                <span class="text-success">✅ Dentro de lo ideal (< 60%)</span>
                            @endif
                        </td>
                    </tr>
                @endif
                
                {{-- TOTAL GASTOS (para ambos modos, pero con lógica diferente) --}}
                <tr class="table-secondary">
                    <td class="fw-bold">
                        <i class="bi bi-calculator-fill me-2"></i>
                        @if($esModoDetallado)
                            Total Gastos Analizados
                        @else
                            Total Gastos
                        @endif
                    </td>
                    <td class="text-end fw-bold {{ $porcentajeGastos > 100 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($porcentajeGastos, 1) }}%
                    </td>
                    <td class="text-center">
                        @if($porcentajeGastos > 100)
                            <span class="badge bg-danger rounded-pill">Déficit</span>
                        @else
                            <span class="badge bg-success rounded-pill">En ingreso</span>
                        @endif
                    </td>
                   {{--  <td class="small">
                        @if($porcentajeGastos > 100)
                            <span class="text-danger">🚨 Urgente: Reduce gastos en 
                            {{ number_format($porcentajeGastos - 100, 1) }}%</span>
                        @else
                            <span class="text-success">✅ Gastas dentro de tus ingresos</span>
                        @endif
                    </td> --}}
                    <td class="small">
    @if($porcentajeGastos > 100)
        <span class="text-danger">🚨 Urgente: Reduce gastos en 
        {{ number_format($porcentajeGastos - 100, 1) }}%</span>
    @elseif($porcentajeGastos == 100)
        <span class="text-warning">⚠️ <strong>Límite máximo:</strong> Sin margen para imprevistos</span>
    @else
        <span class="text-success">✅ Gastas dentro de tus ingresos</span>
    @endif
</td>
                </tr>
                
                {{-- SALDO/CAPACIDAD DE AHORRO (para ambos modos) --}}
                <tr class="{{ $saldo >= 0 ? 'table-success' : 'table-danger' }}">
                    <td class="fw-bold">
                        <i class="bi bi-wallet2 me-2"></i>
                        @if($saldo >= 0)
                            <span class="text-success">Capacidad de Ahorro</span>
                        @else
                            <span class="text-danger">Déficit a Cubrir</span>
                        @endif
                    </td>
                   {{--  <td class="text-end fw-bold {{ $saldo >= 0 ? 'text-success' : 'text-danger' }}">
                        @if($promIngreso > 0)
                            {{ number_format(($saldo / $promIngreso) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </td> --}}
                    <td class="text-end fw-bold {{ $saldo >= 0 ? 'text-success' : 'text-danger' }}">
    @if($promIngreso > 0)
        @if($saldo >= 0)
            {{ number_format(($saldo / $promIngreso) * 100, 1) }}%
        @else
            {{ number_format(abs($saldo) / $promIngreso * 100, 1) }}%
        @endif
    @else
        0%
    @endif
</td>
                    <td class="text-center">
                        @if($saldo > 0)
                            <span class="badge bg-success rounded-pill">Superávit</span>
                        @elseif($saldo < 0)
                            <span class="badge bg-danger rounded-pill">Déficit</span>
                        @else
                            <span class="badge bg-secondary rounded-pill">Equilibrio</span>
                        @endif
                    </td>
                    <td class="small fw-bold">
                        @if($saldo > 0)
                            <span class="text-success">💰 Puedes ahorrar/invertir este %</span>
                        @elseif($saldo < 0)
                            <span class="text-danger">💸 Necesitas cubrir este % mensual</span>
                        @else
                            <span class="text-secondary">⚖️ Sin margen para imprevistos</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    {{-- RESUMEN VISUAL (adaptado por modo) --}}
    <div class="card border-0 bg-light mt-3">
        <div class="card-body">
            <h6 class="card-title mb-3">
                <i class="bi bi-lightbulb text-warning me-2"></i>¿Cómo leer esta tabla?
            </h6>
            <div class="row">
                <div class="col-md-6">
                    <ul class="small mb-0">
                        @if($esModoDetallado)
                            <li><strong>Gastos Totales</strong>: % de tu ingreso en todos los gastos combinados</li>
                            <li><strong>Total Gastos Analizados</strong>: Incluye todas las categorías de gasto</li>
                        @else
                            <li><strong>Gastos Fijos</strong>: % de tu ingreso en obligaciones mensuales</li>
                            <li><strong>Gastos Variables</strong>: % en gastos que puedes ajustar</li>
                            <li><strong>Total Gastos</strong>: % total que gastas respecto a ingresos</li>
                        @endif
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="small mb-0">
                        <li><span class="badge bg-success rounded-pill">Verde</span>: Dentro de rangos saludables</li>
                        <li><span class="badge bg-warning rounded-pill">Ámbar</span>: Requiere atención</li>
                        <li><span class="badge bg-danger rounded-pill">Rojo</span>: Necesita acción inmediata</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>