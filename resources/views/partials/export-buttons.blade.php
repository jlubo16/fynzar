{{-- resources/views/partials/export-buttons.blade.php --}}
@php
/**
 * DETECCIÓN UNIVERSAL DE DATOS PARA EXPORTACIÓN
 * Compatible con múltiples controladores y estructuras
 */

$module = $currentModule ?? 'analisis';

// ====================================================
// MÉTODO 1: Buscar datos en TODAS las posibles ubicaciones
// ====================================================

$tieneDatos = false;
$numRegistros = 0;
$datosParaExportar = [];

// 1. Buscar en sesión por nombre estándar
$sessionKeys = [
    'datos_analisis',
    'datos_financieros',
    'analisis_data',
    'resultados',
    'meses_analizados',
    'datos_' . $module,
    'analisis_' . $module
];

foreach ($sessionKeys as $key) {
    if (session()->has($key)) {
        $datosSesion = session($key);
        if (!empty($datosSesion)) {
            $tieneDatos = true;
            $datosParaExportar = $datosSesion;
            
            if (is_array($datosSesion)) {
                $numRegistros = count($datosSesion);
            } elseif (is_object($datosSesion)) {
                $numRegistros = 1;
            }
            break;
        }
    }
}

// 2. Buscar en variables de vista (pasadas desde controlador)
if (!$tieneDatos) {
    $viewVariables = get_defined_vars();
    
    // Variables comunes que indican datos
    $indicadores = ['meses', 'ingresos', 'gastos', 'saldo', 'resultados', 'datos'];
    
    foreach ($indicadores as $ind) {
        if (isset($$ind) && !empty($$ind)) {
            $tieneDatos = true;
            $datosParaExportar = $$ind;
            
            if (is_array($$ind)) {
                $numRegistros = count($$ind);
            }
            break;
        }
    }
}

// 3. Verificar variables específicas del módulo principal
if (!$tieneDatos && $module === 'analisis') {
    $indicadoresAnalisis = [
        'promIngreso', 'promFijos', 'promDinamicos',
        'porcFijos', 'porcDinamicos', 'saldo'
    ];
    
    foreach ($indicadoresAnalisis as $ind) {
        if (isset($$ind) && $$ind > 0) {
            $tieneDatos = true;
            $numRegistros = 1; // Hay al menos un análisis
            
            // Crear datos básicos para exportar
            $datosParaExportar = [
                'promedio_ingresos' => $promIngreso ?? 0,
                'promedio_gastos_fijos' => $promFijos ?? 0,
                'promedio_gastos_variables' => $promDinamicos ?? 0,
                'saldo_total' => $saldo ?? 0
            ];
            break;
        }
    }
}

// 4. Último recurso: verificar si hay datos POST en sesión flash
if (!$tieneDatos && session()->has('_old_input')) {
    $oldInput = session('_old_input');
    if (isset($oldInput['meses']) || isset($oldInput['ingresos'])) {
        $tieneDatos = true;
        $datosParaExportar = $oldInput;
    }
}

// Debug (solo mostrar en desarrollo local)
$isLocal = app()->environment('local');
if ($isLocal) {
    $debugInfo = [
        'module' => $module,
        'has_data' => $tieneDatos,
        'num_records' => $numRegistros,
        'session_keys_found' => array_filter($sessionKeys, fn($k) => session()->has($k)),
        'view_vars' => array_keys(array_filter(get_defined_vars(), fn($v) => !empty($v) && !is_string($v)))
    ];
    echo "<!-- DEBUG Export: " . htmlspecialchars(json_encode($debugInfo, JSON_PRETTY_PRINT)) . " -->";
}
@endphp

{{-- ==================================================== --}}
{{-- HTML - BOTÓN ADAPTATIVO --}}
{{-- ==================================================== --}}

<div class="export-module" data-module="{{ $module }}">
    @if($tieneDatos)
    {{-- BOTÓN ACTIVO CON DATOS --}}
    <div class="dropdown">
        <button class="btn btn-success btn-export-active dropdown-toggle" 
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                id="exportBtnActive"
                data-bs-title="Exportar {{ $numRegistros }} registros">
            <i class="bi bi-download me-2"></i>
            <span class="export-text">Exportar</span>
            <span class="badge bg-white text-dark ms-2">{{ $numRegistros }}</span>
        </button>
        
        <ul class="dropdown-menu dropdown-menu-end shadow">
            <li class="dropdown-header text-primary mb-1">
                <i class="bi bi-file-arrow-down me-1"></i> Formato de exportación
            </li>
            
            <li>
                <form action="{{ route('exportar') }}" method="POST" class="p-2">
                    @csrf
                    <input type="hidden" name="module" value="{{ $module }}">
                    
                    <div class="d-grid gap-2">
                        <button type="submit" name="format" value="csv" 
                                class="btn btn-outline-success btn-export-format">
                            <i class="bi bi-filetype-csv me-2"></i> CSV/Excel
                            <small class="d-block text-muted mt-1">Para hojas de cálculo</small>
                        </button>
                        
                        <button type="submit" name="format" value="pdf" 
                                class="btn btn-outline-danger btn-export-format">
                            <i class="bi bi-filetype-pdf me-2"></i> PDF
                            <small class="d-block text-muted mt-1">Para imprimir</small>
                        </button>
                        
                        <button type="submit" name="format" value="json" 
                                class="btn btn-outline-info btn-export-format">
                            <i class="bi bi-filetype-json me-2"></i> JSON
                            <small class="d-block text-muted mt-1">Datos estructurados</small>
                        </button>
                    </div>
                </form>
            </li>
            
            <li class="dropdown-divider"></li>
            
            <li class="px-3 py-2 small text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Datos del módulo: <strong>{{ ucfirst($module) }}</strong>
            </li>
        </ul>
    </div>
    @else
    {{-- BOTÓN INACTIVO (SIN DATOS) --}}
    <div class="dropdown">
        <button class="btn btn-outline-secondary btn-export-inactive dropdown-toggle" 
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
                id="exportBtnInactive"
                data-bs-title="Primero ingresa datos financieros">
            <i class="bi bi-download me-2"></i>
            <span class="export-text">Exportar</span>
        </button>
        
        <ul class="dropdown-menu dropdown-menu-end">
            <li class="dropdown-item-text px-3 py-3 text-center">
                <div class="mb-3">
                    <i class="bi bi-clipboard-x text-warning fs-1"></i>
                </div>
                <p class="mb-2 fw-semibold">Sin datos para exportar</p>
                <p class="small text-muted mb-3">
                    Completa el análisis financiero primero
                </p>
                
                {{-- Botones de acción --}}
                <div class="d-grid gap-2">
                    <a href="#formulario" class="btn btn-primary btn-sm">
                        <i class="bi bi-calculator me-1"></i> Ir al formulario
                    </a>
                    
                    @if($module !== 'analisis')
                    <a href="{{ route('analisis.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Volver al análisis
                    </a>
                    @endif
                </div>
            </li>
        </ul>
    </div>
    @endif
</div>

{{-- ==================================================== --}}
{{-- ESTILOS --}}
{{-- ==================================================== --}}
<style>
.btn-export-active {
    min-width: 120px;
    transition: all 0.3s;
}

.btn-export-active:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.2);
}

.btn-export-inactive {
    min-width: 120px;
    opacity: 0.7;
}

.btn-export-format {
    text-align: left;
    padding: 12px 15px;
    border-radius: 8px;
    transition: all 0.2s;
}

.btn-export-format:hover {
    transform: translateX(5px);
    background-color: var(--bs-btn-hover-bg);
}

.export-module .dropdown-menu {
    min-width: 250px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

.export-module .dropdown-header {
    background-color: #f8f9fa;
    font-weight: 600;
}
</style>

{{-- ==================================================== --}}
{{-- JAVASCRIPT --}}
{{-- ==================================================== --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap
    const tooltipTriggerList = document.querySelectorAll('[data-bs-title]');
    const tooltipList = [...tooltipTriggerList].map(
        tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl)
    );
    
    // Manejar clic en botones de exportación
    document.querySelectorAll('.btn-export-format').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Mostrar spinner de carga
            const spinner = document.getElementById('loading-spinner');
            if (spinner) {
                spinner.style.display = 'flex';
            } else {
                // Crear spinner temporal
                const tempSpinner = document.createElement('div');
                tempSpinner.className = 'position-fixed top-0 start-0 w-100 h-100 bg-white d-flex justify-content-center align-items-center';
                tempSpinner.style.zIndex = '9999';
                tempSpinner.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                        <p class="mt-3 fw-bold">Preparando archivo para descarga...</p>
                        <p class="text-muted small">Por favor espera</p>
                    </div>
                `;
                document.body.appendChild(tempSpinner);
                
                // Remover después de 5 segundos (por si acaso)
                setTimeout(() => {
                    if (document.body.contains(tempSpinner)) {
                        document.body.removeChild(tempSpinner);
                    }
                }, 5000);
            }
            
            // Ocultar dropdown
            const dropdown = this.closest('.dropdown');
            if (dropdown) {
                const dropdownInstance = bootstrap.Dropdown.getInstance(
                    dropdown.querySelector('.dropdown-toggle')
                );
                if (dropdownInstance) {
                    dropdownInstance.hide();
                }
            }
        });
    });
    
    // Log para debugging
    console.log('Export button module: {{ $module }}');
    console.log('Has data: {{ $tieneDatos ? "Yes" : "No" }}');
});
</script>