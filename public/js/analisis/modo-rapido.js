// MÓDULO: MODO RÁPIDO
// ============================================

const nameMap = {
    'ingresos-container': { 
        name: 'ingresos', 
        placeholder: '2800',
        type: 'input' // ← nuevo campo
    },
    'fijos-container': { 
        name: 'gastos_fijos', 
        placeholder: '850',
        type: 'input' // ← nuevo campo
    },
    'dinamicos-container': { 
        name: 'gastos_dinamicos', 
        placeholder: '550',
        type: 'input' // ← nuevo campo
    },
};

// Contadores globales (se mantienen igual)
let ingresoCounter = 1;
let gastoCounter = 1;

// Todas las funciones del modo rápido (COPIADAS SIN MODIFICAR)
/* function addField(containerId, value = '') {
    const config = nameMap[containerId];
    if (!config) return;
    
    const container = document.getElementById(containerId);
    const currentCount = container.querySelectorAll('.input-group').length;
    
    const newField = document.createElement('div');
    newField.className = 'input-group mb-2';
    newField.innerHTML = `
        <span class="input-group-text">
            <small class="text-muted month-indicator">M${currentCount + 1}</small>
        </span>
        <input type="number" name="${config.name}[]" class="form-control" value="${value}"
               placeholder="Ej: ${config.placeholder}" step="0.01" min="0">
    `;
    
    container.appendChild(newField);
} */

function addField(containerId, value = '') {
    const config = nameMap[containerId];
    if (!config) return;
    
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const currentCount = container.querySelectorAll('.input-group').length;
    
    const newField = document.createElement('div');
    newField.className = 'input-group mb-2';
    newField.setAttribute('data-mes-index', currentCount); // Para identificar
    
    // Obtener el mes correspondiente
    const meses = ['Ene 2024', 'Feb 2024', 'Mar 2024', 'Abr 2024', 'May 2024', 'Jun 2024'];
    const mesIndex = currentCount < meses.length ? currentCount : 0;
    const mesLabel = meses[mesIndex];
    const mesValue = mesLabel.toLowerCase().replace(' ', '_');
    
    let inputHTML = '';
    
    if (containerId === 'ingresos-container') {
        // INGRESOS: con select de periodo
        inputHTML = `
            <select class="form-select periodo-select" name="periodos[]" 
                    style="width: 120px;" 
                    data-mes-index="${currentCount}"
                    onchange="syncPeriodoDisplay(this)">
                ${meses.map((mes, i) => `
                    <option value="${mes.toLowerCase().replace(' ', '_')}" 
                            ${i === currentCount ? 'selected' : ''}>
                        ${mes}
                    </option>
                `).join('')}
            </select>
            <input type="number" name="${config.name}[]" class="form-control" 
                   value="${value}" placeholder="Ej: ${config.placeholder}" 
                   step="0.01" min="0">
        `;
    } else {
        // GASTOS FIJOS y DINÁMICOS: solo muestra el periodo
        inputHTML = `
            <span class="input-group-text periodo-display" 
                  style="width: 120px;"
                  data-mes-index="${currentCount}">
                <small class="text-muted">${mesLabel}</small>
            </span>
            <input type="number" name="${config.name}[]" class="form-control" 
                   value="${value}" placeholder="Ej: ${config.placeholder}" 
                   step="0.01" min="0">
        `;
    }
    
    newField.innerHTML = inputHTML;
    container.appendChild(newField);
    
    // Sincronizar display si es el primer mes
    if (containerId === 'ingresos-container') {
        setTimeout(() => syncPeriodoDisplay(newField.querySelector('.periodo-select')), 10);
    }
}
function updateMonthIndicators() {
    const containers = ['ingresos-container', 'fijos-container', 'dinamicos-container'];
    
    containers.forEach(containerId => {
        const container = document.getElementById(containerId);
        const indicators = container.querySelectorAll('.month-indicator');
        
        indicators.forEach((indicator, index) => {
            indicator.textContent = `M${index + 1}`;
        });
    });
}

/* function addMonth() {
    addField('ingresos-container');
    addField('fijos-container');
    addField('dinamicos-container');
    updateMonthIndicators();
    renumerarBotonesEliminar();
}
 */

function addMonth() {
   
    // Luego los demás campos
    addField('ingresos-container');
    addField('fijos-container');
    addField('dinamicos-container');
    
   /*  updateMonthIndicators(); */
    renumerarBotonesEliminar();
}
/* function removeMes(mesIndex) {
    const containers = ['ingresos-container', 'fijos-container', 'dinamicos-container'];
    const mesesActuales = document.querySelectorAll('#ingresos-container .input-group').length;
    
    if (mesesActuales <= 1) {
        alert('Debe haber al menos un mes completo.');
        return;
    }
    
    if (mesIndex >= mesesActuales || mesIndex < 0) {
        alert('Mes inválido.');
        return;
    }
    
    containers.forEach(containerId => {
        const container = document.getElementById(containerId);
        const inputs = container.querySelectorAll('.input-group');
        if (inputs[mesIndex]) {
            inputs[mesIndex].remove();
        }
    });
    
    updateMonthIndicators();
    renumerarBotonesEliminar();
} */
/* 
function removeMes(mesIndex) {
    const containers = [
      
        'ingresos-container', 
        'fijos-container', 
        'dinamicos-container'
    ];
    
    const mesesActuales = document.querySelectorAll('#ingresos-container .input-group').length;
    
    if (mesesActuales <= 1) {
        alert('Debe haber al menos un mes completo.');
        return;
    }
    
    if (mesIndex >= mesesActuales || mesIndex < 0) {
        alert('Mes inválido.');
        return;
    }
    
    containers.forEach(containerId => {
        const container = document.getElementById(containerId);
        const inputs = container.querySelectorAll('.input-group');
        if (inputs[mesIndex]) {
            inputs[mesIndex].remove();
        }
    });
    
  
    renumerarBotonesEliminar();
} */
/* 
function addField(containerId, value = '') {
    const config = nameMap[containerId];
    if (!config) return;

    const container = document.getElementById(containerId);
    if (!container) return;

    const currentCount = container.querySelectorAll('.input-group').length;

    const newField = document.createElement('div');
    newField.className = 'input-group mb-2';
    newField.setAttribute('data-mes-index', currentCount); // Para identificar

    // Obtener el mes correspondiente
    const meses = ['Ene 2024', 'Feb 2024', 'Mar 2024', 'Abr 2024', 'May 2024', 'Jun 2024'];
    const mesIndex = currentCount < meses.length ? currentCount : 0;
    const mesLabel = meses[mesIndex];
    const mesValue = mesLabel.toLowerCase().replace(' ', '_');

    let inputHTML = '';

    if (containerId === 'ingresos-container') {
        // INGRESOS: con select de periodo
        inputHTML = `
            <select class="form-select periodo-select" name="periodos[]"
                    style="width: 120px;"
                    data-mes-index="${currentCount}"
                    onchange="syncPeriodoDisplay(this)">
                ${meses.map((mes, i) => `
                    <option value="${mes.toLowerCase().replace(' ', '_')}"
                            ${i === currentCount ? 'selected' : ''}>
                        ${mes}
                    </option>
                `).join('')}
            </select>
            <input type="number" name="${config.name}[]" class="form-control"
                   value="${value}" placeholder="Ej: ${config.placeholder}"
                   step="0.01" min="0">
        `;
    } else {
        // GASTOS FIJOS y DINÁMICOS: solo muestra el periodo
        inputHTML = `
            <span class="input-group-text periodo-display"
                  style="width: 120px;"
                  data-mes-index="${currentCount}">
                <small class="text-muted">${mesLabel}</small>
            </span>
            <input type="number" name="${config.name}[]" class="form-control"
                   value="${value}" placeholder="Ej: ${config.placeholder}"
                   step="0.01" min="0">
        `;
    }

    // AGREGAR BOTÓN DE ELIMINAR SOLO PARA GASTOS DINÁMICOS
    if (containerId === 'dinamicos-container') {
        inputHTML += `
            <button type="button" class="btn btn-outline-danger btn-sm" 
                    onclick="removeMes(${currentCount})" 
                    style="border-radius: 0 .375rem .375rem 0; padding: 0.375rem 0.75rem;">
                <i class="bi bi-x"></i>
            </button>
        `;
    }

    newField.innerHTML = inputHTML;
    container.appendChild(newField);

    // Sincronizar display si es el primer mes
    if (containerId === 'ingresos-container') {
        setTimeout(() => syncPeriodoDisplay(newField.querySelector('.periodo-select')), 10);
    }
} */
function removeMes(mesIndex) {
    const containers = [
        'ingresos-container',
        'fijos-container',
        'dinamicos-container'
    ];

    const mesesActuales = document.querySelectorAll('#ingresos-container .input-group').length;

    if (mesesActuales <= 1) {
        alert('Debe haber al menos un mes completo.');
        return;
    }

    if (mesIndex >= mesesActuales || mesIndex < 0) {
        alert('Mes inválido.');
        return;
    }

    containers.forEach(containerId => {
        const container = document.getElementById(containerId);
        const inputs = container.querySelectorAll('.input-group');
        if (inputs[mesIndex]) {
            inputs[mesIndex].remove();
        }
    });

    // IMPORTANTE: Después de eliminar, renumerar los botones de eliminar
    const dinamicosContainer = document.getElementById('dinamicos-container');
    const botones = dinamicosContainer.querySelectorAll('.btn-outline-danger');
    
    botones.forEach((boton, index) => {
        boton.setAttribute('onclick', `removeMes(${index})`);
    });
}
function addField(containerId, value = '') {
    const config = nameMap[containerId];
    if (!config) return;

    const container = document.getElementById(containerId);
    if (!container) return;

    const currentCount = container.querySelectorAll('.input-group').length;

    const newField = document.createElement('div');
    newField.className = 'input-group mb-2';
    newField.setAttribute('data-mes-index', currentCount); // Para identificar

    // Obtener el mes correspondiente
    const meses = ['Ene 2024', 'Feb 2024', 'Mar 2024', 'Abr 2024', 'May 2024', 'Jun 2024'];
    const mesIndex = currentCount < meses.length ? currentCount : 0;
    const mesLabel = meses[mesIndex];
    const mesValue = mesLabel.toLowerCase().replace(' ', '_');

    let inputHTML = '';

    if (containerId === 'ingresos-container') {
        // INGRESOS: con select de periodo
        inputHTML = `
            <select class="form-select periodo-select" name="periodos[]"
                    style="width: 120px;"
                    data-mes-index="${currentCount}"
                    onchange="syncPeriodoDisplay(this)">
                ${meses.map((mes, i) => `
                    <option value="${mes.toLowerCase().replace(' ', '_')}"
                            ${i === currentCount ? 'selected' : ''}>
                        ${mes}
                    </option>
                `).join('')}
            </select>
            <input type="number" name="${config.name}[]" class="form-control"
                   value="${value}" placeholder="Ej: ${config.placeholder}"
                   step="0.01" min="0">
        `;
    } else {
        // GASTOS FIJOS y DINÁMICOS: solo muestra el periodo
        inputHTML = `
            <span class="input-group-text periodo-display"
                  style="width: 120px;"
                  data-mes-index="${currentCount}">
                <small class="text-muted">${mesLabel}</small>
            </span>
            <input type="number" name="${config.name}[]" class="form-control"
                   value="${value}" placeholder="Ej: ${config.placeholder}"
                   step="0.01" min="0">
        `;
    }

    // AGREGAR BOTÓN DE ELIMINAR SOLO PARA GASTOS DINÁMICOS
    if (containerId === 'dinamicos-container') {
        inputHTML += `
            <button type="button" class="btn btn-outline-danger btn-sm" 
                    onclick="removeMes(${currentCount})" 
                    style="border-radius: 0 .375rem .375rem 0; padding: 0.375rem 0.75rem;">
                <i class="bi bi-x"></i>
            </button>
        `;
    }

    newField.innerHTML = inputHTML;
    container.appendChild(newField);

    // Sincronizar display si es el primer mes
    if (containerId === 'ingresos-container') {
        setTimeout(() => syncPeriodoDisplay(newField.querySelector('.periodo-select')), 10);
    }
}


function renumerarBotonesEliminar() {
    const botonesContainer = document.getElementById('botones-eliminar-container');
    if (!botonesContainer) return;
    
    const mesesRestantes = document.querySelectorAll('#ingresos-container .input-group').length;
    botonesContainer.innerHTML = '';
    
    for (let i = 0; i < mesesRestantes; i++) {
        const newButtonDiv = document.createElement('div');
        newButtonDiv.className = 'text-center mt-2';
        
        newButtonDiv.innerHTML = `
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeMes(${i})">
                <i class="bi bi-trash"></i> Eliminar Mes ${i + 1}
            </button>
        `;
        
        botonesContainer.appendChild(newButtonDiv);
    }
    
    console.log(`Botones regenerados para ${mesesRestantes} meses`);
}

/* function balancearMeses() {
    const containers = ['ingresos-container', 'fijos-container', 'dinamicos-container'];
    const counts = containers.map(id =>
        document.querySelectorAll(`#${id} .input-group`).length);
    const maxCount = Math.max(...counts);
    
    containers.forEach(containerId => {
        const currentCount = document.querySelectorAll(`#${containerId} .input-group`).length;
        for (let i = 0; i < maxCount - currentCount; i++) {
            addField(containerId);
        }
    });
    
    updateMonthIndicators();
    renumerarBotonesEliminar();
}
 */

function balancearMeses() {
    const containers = [
      
        'ingresos-container', 
        'fijos-container', 
        'dinamicos-container'
    ];
    
    const counts = containers.map(id =>
        document.querySelectorAll(`#${id} .input-group`).length);
    const maxCount = Math.max(...counts);
    
    containers.forEach(containerId => {
        const currentCount = document.querySelectorAll(`#${containerId} .input-group`).length;
        for (let i = 0; i < maxCount - currentCount; i++) {
            addField(containerId);
        }
    });
    
  /*   updateMonthIndicators(); */
    renumerarBotonesEliminar();
}

// Función para sincronizar los displays de periodo
function syncPeriodoDisplay(selectElement) {
    if (!selectElement) return;
    
    const mesIndex = selectElement.getAttribute('data-mes-index');
    const selectedText = selectElement.options[selectElement.selectedIndex].text;
    
    // Actualizar todos los displays con el mismo índice
    const displays = document.querySelectorAll(`.periodo-display[data-mes-index="${mesIndex}"]`);
    displays.forEach(display => {
        display.innerHTML = `<small class="text-muted">${selectedText}</small>`;
    });
}
// Inicializar periodos cuando se cargue la página
document.addEventListener('DOMContentLoaded', function() {
    // Sincronizar periodos iniciales (si hay)
    const periodosSelects = document.querySelectorAll('.periodo-select');
    periodosSelects.forEach(select => {
        syncPeriodoDisplay(select);
    });
});

// Exportar la nueva función también
window.syncPeriodoDisplay = syncPeriodoDisplay;

// Exportar funciones globalmente
window.addMonth = addMonth;
window.removeMes = removeMes;
window.balancearMeses = balancearMeses;
window.updateMonthIndicators = updateMonthIndicators;
window.renumerarBotonesEliminar = renumerarBotonesEliminar;

