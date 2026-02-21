<!-- Importar CSV -->
<div class="border-top pt-5" id="csv-section">
    <div class="alert alert-secondary border-0 shadow-sm alert-permanent">
        <h5 class="fw-bold">
            <i class="bi bi-file-earmark-spreadsheet me-2"></i> ¿Prefieres usar Excel/CSV?
        </h5>
        <p class="mb-2">Tenemos dos formatos disponibles:</p>
    </div>
    
    <div class="row g-4">
        <div class="col-md-6">
            <form action="{{ route('analisis.importar.csv') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group shadow-sm">
                    <input type="file" name="archivo_csv" class="form-control" accept=".csv,.txt" required>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i> Importar CSV
                    </button>
                </div>
                <small class="form-text text-muted mt-2">
                    Formatos aceptados: Simple (3 columnas) o Detallado (6 columnas)
                </small>
            </form>
        </div>
        
        <div class="col-md-6">
            <!-- Pestañas para mostrar ambos formatos -->
            <ul class="nav nav-tabs" id="csvFormatTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="simple-tab" data-bs-toggle="tab" data-bs-target="#simple" type="button" role="tab">
                        <i class="bi bi-list-check me-1"></i> Formato Simple
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="detallado-tab" data-bs-toggle="tab" data-bs-target="#detallado" type="button" role="tab">
                        <i class="bi bi-card-checklist me-1"></i> Formato Detallado
                    </button>
                </li>
            </ul>
            
            <div class="tab-content card shadow-sm border-0 rounded-top-0" id="csvFormatTabContent">
                <!-- TAB 1: Formato Simple -->
                <div class="tab-pane fade show active p-3" id="simple" role="tabpanel">
                    <h6 class="fw-bold text-primary">📋 Formato Simple (3 columnas):</h6>
                    <p class="small text-muted">Para análisis rápido, sin categorización.</p>
                    <code class="small d-block p-3 bg-light rounded mt-2">
                        ingresos,gastos_fijos,gastos_dinamicos<br>
                        5000,2000,1500<br>
                        5200,2100,1800<br>
                        5900,2050,2200
                    </code>
                    <button type="button" class="btn btn-outline-success btn-sm mt-3" onclick="descargarPlantillaSimple()">
                        <i class="bi bi-download me-1"></i> Descargar Plantilla Simple
                    </button>
                </div>
                
                <!-- TAB 2: Formato Detallado -->
                <div class="tab-pane fade p-3" id="detallado" role="tabpanel">
                    <h6 class="fw-bold text-primary">📋 Formato Detallado (6 columnas):</h6>
                    <p class="small text-muted">Para análisis completo con categorización.</p>
                    <code class="small d-block p-3 bg-light rounded mt-2">
                        Tipo,Mes,Categoria,Subcategoria,Monto,Descripcion<br>
                        Ingreso,2024-01,,,5000,Salario principal<br>
                        Gasto,2024-01,Vivienda,Renta,1200,Pago renta<br>
                        Gasto,2024-01,Alimentacion,Supermercado,400,Compra<br>
                        Gasto,2024-02,Vivienda,Renta,1200,Pago renta
                    </code>
                    <div class="alert alert-info small mt-3">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Nota:</strong> Los ingresos llevan vacías las columnas de categoría/subcategoría.
                    </div>
                    <button type="button" class="btn btn-outline-info btn-sm mt-2" onclick="descargarPlantillaDetallada()">
                        <i class="bi bi-download me-1"></i> Descargar Plantilla Detallada
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>