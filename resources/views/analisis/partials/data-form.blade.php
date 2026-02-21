

<!-- Formulario ÚNICO que maneja ambos modos -->
<div class="card shadow-lg border-0" id="formulario-section">
    <div class="card-header bg-success text-white py-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-cloud-upload me-2"></i> Ingresar Datos Financieros
        </h4>
    </div>
    <div class="card-body">
        <form action="{{ route('analisis.calcular.detallado') }}" method="POST" id="mainForm">
            @csrf
            
            <div class="alert alert-info border-0 shadow-sm alert-permanent">
                <h5><i class="bi bi-lightbulb me-2"></i> Nuevo: Categorización Inteligente</h5>
                <p class="mb-1">Ahora puedes categorizar tus gastos para obtener análisis más detallados.</p>
            </div>

            <!-- SELECTOR DE MODO -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="modo_ingreso" id="modo_rapido" value="rapido" checked>
                        <label class="form-check-label fw-bold" for="modo_rapido">
                            <i class="bi bi-lightning me-1"></i> Modo Rápido
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="modo_ingreso" id="modo_detallado" value="detallado">
                        <label class="form-check-label fw-bold" for="modo_detallado">
                            <i class="bi bi-bar-chart me-1"></i> Modo Detallado
                        </label>
                    </div>
                </div>
            </div>

         
            <!-- MODO RÁPIDO -->
 <div id="modo-rapido-container">
    <div class="alert alert-info border-0 shadow-sm alert-permanent">
        <h5><i class="bi bi-info-circle me-2"></i> ¿Cómo funciona?</h5>
        <p class="mb-1">Ingresa tus datos financieros de los últimos meses. Cuantos más meses ingreses, más preciso será el análisis.</p>
    </div>

    <div class="row">
        <!-- Ingresos -->
        <div class="col-md-4">
            <div class="h-100 border-success shadow-sm">
                <div class="card-header bg-success text-white d-flex align-items-center">
                    <i class="bi bi-cash-coin me-2"></i>
                    <h6 class="mb-0 fw-bold">💰 Ingresos Mensuales</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        <strong>Ejemplos:</strong><br>
                        • Salario: $2,500<br>
                        • Ingresos extra: $300<br>
                        • <strong>Total mensual: $2,800</strong>
                        <br>
                        <br>
                    </p>
                   <div id="ingresos-container">
    <div class="input-group mb-2" data-mes-index="0">
        <!-- AGREGAR onchange AQUÍ -->
        <select class="form-select periodo-select" name="periodos[]" 
                style="width: 10px;" 
                data-mes-index="0"
                onchange="syncPeriodoDisplay(this)">  <!-- ← AGREGAR ESTO -->
            <option value="enero_2024">Ene 2024</option>
            <option value="febrero_2024">Feb 2024</option>
            <option value="marzo_2024">Mar 2024</option>
            <option value="abril_2024">Abr 2024</option>
            <option value="mayo_2024">May 2024</option>
            <option value="junio_2024">Jun 2024</option>
            <option value="julio_2024">Jul 2024</option>
            <option value="agosto_2024">Ago 2024</option>
            <option value="septiembre_2024">Sep 2024</option>
            <option value="octubre_2024">Oct 2024</option>
            <option value="noviembre_2024">Nov 2024</option>
            <option value="diciembre_2024">Dic 2024</option>
        </select>
        <input type="number" name="ingresos[]" class="form-control" placeholder="Ej: 2800" step="0.01" min="0">
    </div>
</div>
                </div>
            </div>
        </div>

        <!-- Gastos Fijos -->
        <div class="col-md-4">
            <div class="h-100 border-warning shadow-sm">
                <div class="card-header bg-warning text-white d-flex align-items-center">
                    <i class="bi bi-house-door me-2"></i>
                    <h6 class="mb-0 fw-bold">🏠 Gastos Fijos</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        <strong>Gastos obligatorios:</strong><br>
                        • Arriendo: $500<br>
                        • Servicios: $200<br>
                        • Transporte: $150<br>
                        • <strong>Total fijos: $850</strong>
                    </p>
                    <!-- Gastos Fijos -->
                    <div id="fijos-container">
                        <div class="input-group mb-2"data-mes-index="0">
                            <!-- CAMBIO AQUÍ: M1 → Muestra solo el mes (no select) -->
                            <span class="input-group-text periodo-display" style="width: 120px;" data-mes-index="0">
                                <small class="text-muted">Ene 2024</small>
                            </span>
                            <input type="number" name="gastos_fijos[]" class="form-control" placeholder="Ej: 850" step="0.01" min="0">
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{-- 
        <!-- Gastos Dinámicos -->
        <div class="col-md-4">
            <div class="h-100 border-info shadow-sm">
                <div class="card-header bg-info text-white d-flex align-items-center">
                    <i class="bi bi-graph-up-arrow me-2"></i>
                    <h6 class="mb-0 fw-bold">🎯 Gastos Dinámicos</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">
                        <i class="bi bi-lightbulb me-1"></i>
                        <strong>Gastos variables:</strong><br>
                        • Mercado: $400<br>
                        • Entretenimiento: $100<br>
                        • Ropa: $50<br>
                        • <strong>Total variables: $550</strong>
                    </p>
                    <div id="dinamicos-container">
                        <div class="input-group mb-2" data-mes-index="0">
                            <!-- CAMBIO AQUÍ: M1 → Muestra solo el mes (no select) -->
                            <span class="input-group-text periodo-display" style="width: 120px;" data-mes-index="0">
                                <small class="text-muted">Ene 2024</small>
                            </span>
                            <input type="number" name="gastos_dinamicos[]" class="form-control" placeholder="Ej: 550" step="0.01" min="0">
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        <!-- Gastos Dinámicos -->
<div class="col-md-4">
    <div class="h-100 border-info shadow-sm">
        <div class="card-header bg-info text-white d-flex align-items-center">
            <i class="bi bi-graph-up-arrow me-2"></i>
            <h6 class="mb-0 fw-bold">🎯 Gastos Dinámicos</h6>
        </div>
        <div class="card-body">
            <p class="small text-muted">
                <i class="bi bi-lightbulb me-1"></i>
                <strong>Gastos variables:</strong><br>
                • Mercado: $400<br>
                • Entretenimiento: $100<br>
                • Ropa: $50<br>
                • <strong>Total variables: $550</strong>
            </p>
            <div id="dinamicos-container">
                <div class="input-group mb-2" data-mes-index="0">
                    <span class="input-group-text periodo-display" style="width: 120px;" data-mes-index="0">
                        <small class="text-muted">Ene 2024</small>
                    </span>
                    <input type="number" name="gastos_dinamicos[]" class="form-control" placeholder="Ej: 550" step="0.01" min="0">
                    <!-- BOTÓN ELIMINAR PARA EL PRIMER MES -->
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeMes(0)" 
                            style="border-radius: 0 .375rem .375rem 0; padding: 0.375rem 0.75rem;">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>

    <button type="button" class="btn btn-outline-primary mt-3" onclick="addMonth()">
        <i class="bi bi-plus-circle me-1"></i> Agregar Otro Mes
    </button>

    {{-- <div id="botones-eliminar-container" class="mt-3">
        <div class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeMes(0)">
                <i class="bi bi-trash me-1"></i> Eliminar Mes 1
            </button>
        </div>
    </div> --}}
</div> 

                        <!-- MODO DETALLADO -->
            <div id="modo-detallado-container" style="display: none;">
                <!-- INGRESOS -->
                <div class="card border-success mb-4 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-cash-coin me-2"></i>💰 Ingresos Mensuales
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="ingresos-detallados-container">
                            <div class="ingreso-mes mb-3 p-3 border rounded shadow-sm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Mes</label>
                                        <input type="month" name="ingresos_detallados[0][mes]" class="form-control">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-bold">Descripción</label>
                                        <input type="text" name="ingresos_detallados[0][descripcion]" class="form-control" placeholder="Ej: Salario principal">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Monto</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="ingresos_detallados[0][monto]" class="form-control" step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="addIngresoDetallado()">
                            <i class="bi bi-plus-circle me-1"></i> Agregar Otro Ingreso
                        </button>
                    </div>
                </div>


                                <!-- GASTOS DETALLADOS -->
                <div class="card border-warning shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-graph-up-arrow me-2"></i>🎯 Gastos Detallados por Categoría
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="gastos-detallados-container">
                            <div class="gasto-item mb-3 p-3 border rounded shadow-sm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Fecha</label>
                                        <input type="date" name="gastos_detallados[0][fecha]" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Categoría</label>
                                        <select name="gastos_detallados[0][categoria_id]" class="form-select categoria-select" onchange="actualizarSubcategorias(this)">
                                            <option value="">Seleccionar...</option>
                                            @foreach($categorias as $categoria)
                                                <option value="{{ $categoria->id }}" data-color="{{ $categoria->color }}">
                                                    {{ $categoria->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-bold">Subcategoría</label>
                                        <select name="gastos_detallados[0][subcategoria_id]" class="form-select subcategoria-select">
                                            <option value="">Primero selecciona categoría</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label fw-bold">Monto</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="gastos_detallados[0][monto]" class="form-control" step="0.01" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger" onclick="removeGastoItem(this)">
                                            ×
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Descripción (opcional)</label>
                                        <input type="text" name="gastos_detallados[0][descripcion]" class="form-control" placeholder="Ej: Gasolina para el carro">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="addGastoDetallado()">
                            <i class="bi bi-plus-circle me-1"></i> Agregar Otro Gasto
                        </button>

                        <!-- BOTÓN DETECCIÓN AUTOMÁTICA -->
                        <button type="button" class="btn btn-outline-info btn-sm ms-2" onclick="sugerirCategorias()">
                            <i class="bi bi-magic me-1"></i> Sugerir Categorías
                        </button>
                    </div>
                </div>
            </div>


                        <div class="mt-4 text-center">
                <button type="submit" class="btn btn-success btn-lg px-5 py-3 shadow">
                    <i class="bi bi-calculator me-2"></i> Calcular Análisis
                </button>
            </div>
        </form>
    </div>
</div>

