

<div class="mb-5">
    <h5 class="text-primary fw-bold mb-4">
        <i class="bi bi-calculator-fill me-2"></i>📌 Medidas de Tendencia Central
    </h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle shadow-sm">
            <thead class="table-light">
                <tr>
                    <th class="fw-bold">Concepto</th>
                    <th class="fw-bold text-end">Valor</th>
                    <th class="fw-bold">Descripción</th>
                    <th class="fw-bold text-center">Fórmula</th>
                </tr>
            </thead>
            <tbody>
                {{-- MEDIANA INGRESOS (igual para ambos modos) --}}
                <tr>
                    <td class="fw-bold">
                        <i class="bi bi-filter-circle text-primary me-2"></i>Mediana Ingresos
                    </td>
                    <td class="text-end fw-bold text-primary">${{ number_format($medianaIngresos, 2) }}</td>
                    <td>
                        <strong>¿Qué significa?</strong><br>
                        El 50% de tus meses tuvieron ingresos <strong>menores a ${{ number_format($medianaIngresos, 2) }}</strong>
                        <br>
                        <small class="text-muted">Útil para ver tu "nivel base" sin meses excepcionales</small>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#formulaMediana">
                            <i class="bi bi-info-circle"></i> Ver Fórmula
                        </button>
                    </td>
                </tr>

                {{-- CONDICIONAL SEGÚN MODO --}}
                
@if(isset($modo_analisis) && $modo_analisis == 'detallado')
    {{-- =========== MODO DETALLADO =========== --}}
    <td class="fw-bold">
        <i class="bi bi-filter-circle text-success me-2"></i>
        @if(isset($modo_analisis) && $modo_analisis == 'detallado')
            Gasto Típico Individual  {{-- ✅ NUEVO NOMBRE --}}
        @else
            Mediana Gastos Totales   {{-- Nombre original para otros modos --}}
        @endif
    </td>
    <td class="text-end fw-bold text-success">
        @if(isset($mediana_gastos_totales) && $mediana_gastos_totales > 0)
            ${{ number_format($mediana_gastos_totales, 2) }}
        @elseif(isset($medianaGastos) && $medianaGastos > 0)
            ${{ number_format($medianaGastos, 2) }}
        @else
            ${{ number_format($total_gastos ?? 0, 2) }}
        @endif
    </td>
    <td>
        <strong>¿Qué significa?</strong><br>
        @if(isset($modo_analisis) && $modo_analisis == 'detallado')
            Tu <strong>gasto individual típico</strong> es  {{-- ✅ NUEVA DESCRIPCIÓN --}}
            <strong>
                @if(isset($mediana_gastos_totales) && $mediana_gastos_totales > 0)
                    ${{ number_format($mediana_gastos_totales, 2) }}
                @elseif(isset($medianaGastos) && $medianaGastos > 0)
                    ${{ number_format($medianaGastos, 2) }}
                @else
                    ${{ number_format($total_gastos ?? 0, 2) }}
                @endif
            </strong>
            <br>
            <small class="text-muted">
                La mediana de todos tus gastos individuales (no el total mensual)
            </small>
        @else
            En el mes "típico", tus <strong>gastos totales</strong> fueron 
            <strong>
                @if(isset($mediana_gastos_totales) && $mediana_gastos_totales > 0)
                    ${{ number_format($mediana_gastos_totales, 2) }}
                @elseif(isset($medianaGastos) && $medianaGastos > 0)
                    ${{ number_format($medianaGastos, 2) }}
                @else
                    ${{ number_format($total_gastos ?? 0, 2) }}
                @endif
            </strong>
            <br>
            <small class="text-muted">Tu gasto mensual habitual (todas las categorías combinadas)</small>
        @endif
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#formulaMediana">
            <i class="bi bi-info-circle"></i> Ver Fórmula
        </button>
    </td>
    
    {{-- CATEGORÍA PRINCIPAL (si existe) --}}
    @if(isset($analisis_categorias) && !empty($analisis_categorias) && isset($analisis_categorias[0]))
        @php
            $categoriaPrincipal = $analisis_categorias[0]['categoria']->name ?? 'General';
            // Buscar mediana en varios lugares posibles
            $medianaCategoria = $analisis_categorias[0]['mediana'] ?? 
                               $analisis_categorias[0]['mediana_gastos'] ?? 
                               $analisis_categorias[0]['total'] ?? 0;
        @endphp
        <tr>
            <td class="fw-bold">
                <i class="bi bi-tag-fill text-info me-2"></i>Mediana {{ $categoriaPrincipal }}
            </td>
            <td class="text-end fw-bold text-info">
                ${{ number_format($medianaCategoria, 2) }}
            </td>
            <td>
                <strong>¿Qué significa?</strong><br>
                En <strong>{{ $categoriaPrincipal }}</strong>, tu gasto mensual típico es 
                <strong>${{ number_format($medianaCategoria, 2) }}</strong>
                <br>
                <small class="text-muted">
                    @if($medianaCategoria > 0)
                        Mediana de tu categoría principal de gasto
                    @else
                        No hay suficientes datos para calcular la mediana
                    @endif
                </small>
            </td>
            <td class="text-center">
                --
            </td>
        </tr>
    @endif
                @else
                    {{-- =========== MODO RÁPIDO =========== --}}
                    <tr>
                        <td class="fw-bold">
                            <i class="bi bi-filter-circle text-warning me-2"></i>Mediana Gastos Fijos
                        </td>
                        <td class="text-end fw-bold text-warning">${{ number_format($medianaFijos, 2) }}</td>
                        <td>
                            <strong>¿Qué significa?</strong><br>
                            En el mes "típico", tus <strong>gastos fijos</strong> fueron 
                            <strong>${{ number_format($medianaFijos, 2) }}</strong>
                            <br>
                            <small class="text-muted">Tu compromiso financiero regular (renta, deudas, servicios)</small>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#formulaMediana">
                                <i class="bi bi-info-circle"></i> Ver Fórmula
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">
                            <i class="bi bi-filter-circle text-info me-2"></i>Mediana Gastos Dinámicos
                        </td>
                        <td class="text-end fw-bold text-info">${{ number_format($medianaDinamicos, 2) }}</td>
                        <td>
                            <strong>¿Qué significa?</strong><br>
                            Tu <strong>gasto variable "normal"</strong> es 
                            <strong>${{ number_format($medianaDinamicos, 2) }}</strong>
                            <br>
                            <small class="text-muted">Lo que normalmente gastas en comida, transporte, ocio (sin extras)</small>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#formulaMediana">
                                <i class="bi bi-info-circle"></i> Ver Fórmula
                            </button>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
        
        {{-- NOTA EXPLICATIVA PARA MODO DETALLADO --}}
        @if(isset($modo_analisis) && $modo_analisis == 'detallado')
            <div class="alert alert-info mt-3 alert-permanent">
                <div class="d-flex">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Nota:</strong> En modo detallado, no se muestran "Gastos Fijos" y "Gastos Dinámicos" 
                        porque el sistema analiza cada gasto por categoría. La clasificación fijo/dinámico se realiza 
                        automáticamente basada en patrones de comportamiento.
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>