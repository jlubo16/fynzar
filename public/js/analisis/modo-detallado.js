// MÓDULO: MODO DETALLADO
// ============================================

function addIngresoDetallado() {
    const container = document.getElementById('ingresos-detallados-container');
    const newIngreso = document.createElement('div');
    newIngreso.className = 'ingreso-mes mb-3 p-3 border rounded';
    newIngreso.innerHTML = `
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Mes</label>
                <input type="month" name="ingresos_detallados[${ingresoCounter}][mes]" class="form-control" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Descripción</label>
                <input type="text" name="ingresos_detallados[${ingresoCounter}][descripcion]"
                       class="form-control" placeholder="Ej: Salario principal" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Monto</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="ingresos_detallados[${ingresoCounter}][monto]"
                           class="form-control" step="0.01" min="0" required>
                </div>
            </div>
        </div>
    `;
    container.appendChild(newIngreso);
    ingresoCounter++;
}

function addGastoDetallado() {
    const container = document.getElementById('gastos-detallados-container');
    const newGasto = document.createElement('div');
    newGasto.className = 'gasto-item mb-3 p-3 border rounded';
    
    if (!window.categorias) {
        console.error('Error: window.categorias no está definido.');
        
        newGasto.innerHTML = `
            <div class="alert alert-warning">
                Error: No se pudieron cargar las categorías. Recarga la página.
            </div>
        `;
        container.appendChild(newGasto);
        return;
    }
    
    let categoriasOptions = '<option value="">Seleccionar...</option>';
    window.categorias.forEach(categoria => {
        categoriasOptions += `
            <option value="${categoria.id}" data-color="${categoria.color || '#6c757d'}">
                ${categoria.name}
            </option>
        `;
    });
    
    newGasto.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="gastos_detallados[${gastoCounter}][fecha]" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Categoría</label>
                <select name="gastos_detallados[${gastoCounter}][categoria_id]"
                        class="form-select categoria-select" onchange="actualizarSubcategorias(this)" required>
                    ${categoriasOptions}
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Subcategoría</label>
                <select name="gastos_detallados[${gastoCounter}][subcategoria_id]"
                        class="form-select subcategoria-select" required>
                    <option value="">Primero selecciona categoría</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Monto</label>
                <div class="input-group">
                    <span class="input-group-text">$</span>
                    <input type="number" name="gastos_detallados[${gastoCounter}][monto]"
                           class="form-control" step="0.01" min="0" required>
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
                <label class="form-label">Descripción (opcional)</label>
                <input type="text" name="gastos_detallados[${gastoCounter}][descripcion]"
                       class="form-control" placeholder="Ej: Gasolina para el carro">
            </div>
        </div>
    `;
    
    container.appendChild(newGasto);
    gastoCounter++;
}

function removeGastoItem(button) {
    if (document.querySelectorAll('.gasto-item').length > 1) {
        button.closest('.gasto-item').remove();
    } else {
        alert('Debe haber al menos un gasto.');
    }
}

async function actualizarSubcategorias(select) {
    const categoriaId = select.value;
    const subcategoriaSelect = select.closest('.row').querySelector('.subcategoria-select');
    
    if (!categoriaId) {
        subcategoriaSelect.innerHTML = '<option value="">Primero selecciona categoría</option>';
        return;
    }
    
    try {
        const response = await fetch(`/api/categorias/${categoriaId}/subcategorias`);
        const subcategorias = await response.json();
        
        let options = '<option value="">Seleccionar subcategoría</option>';
        subcategorias.forEach(sub => {
            options += `<option value="${sub.id}">${sub.name}</option>`;
        });
        
        subcategoriaSelect.innerHTML = options;
    } catch (error) {
        console.error('Error cargando subcategorías:', error);
        subcategoriaSelect.innerHTML = '<option value="">Error cargando subcategorías</option>';
    }
}

function sugerirCategorias() {
    alert('Función de sugerencia en desarrollo. ¡Pronto estará disponible!');
}

// Exportar funciones globalmente
window.addIngresoDetallado = addIngresoDetallado;
window.addGastoDetallado = addGastoDetallado;
window.removeGastoItem = removeGastoItem;
window.actualizarSubcategorias = actualizarSubcategorias;
window.sugerirCategorias = sugerirCategorias;