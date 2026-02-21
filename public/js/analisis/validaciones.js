// MÓDULO: VALIDACIONES
// ============================================

function validarModoRapido() {
    const errors = [];
    
    const ingresosCampos = document.querySelectorAll('#ingresos-container input[type="number"]');
    const fijosCampos = document.querySelectorAll('#fijos-container input[type="number"]');
    const dinamicosCampos = document.querySelectorAll('#dinamicos-container input[type="number"]');
    
    const numIngresos = ingresosCampos.length;
    const numFijos = fijosCampos.length;
    const numDinamicos = dinamicosCampos.length;
    
    if (numIngresos === 0 || numFijos === 0 || numDinamicos === 0) {
        errors.push('Debe haber al menos un mes completo (los 3 campos)');
    }
    
    // Verificar valores negativos
    ingresosCampos.forEach((input, index) => {
        const valor = parseFloat(input.value) || 0;
        if (valor < 0) errors.push(`Valor negativo no permitido en ingresos, mes ${index + 1}`);
    });
    
    fijosCampos.forEach((input, index) => {
        const valor = parseFloat(input.value) || 0;
        if (valor < 0) errors.push(`Valor negativo no permitido en gastos fijos, mes ${index + 1}`);
    });
    
    dinamicosCampos.forEach((input, index) => {
        const valor = parseFloat(input.value) || 0;
        if (valor < 0) errors.push(`Valor negativo no permitido en gastos dinámicos, mes ${index + 1}`);
    });
    
    // Buscar al menos un mes con datos
    let mesCompletoEncontrado = false;
    for (let i = 0; i < Math.max(numIngresos, numFijos, numDinamicos); i++) {
        const ingreso = parseFloat(ingresosCampos[i]?.value) || 0;
        const fijo = parseFloat(fijosCampos[i]?.value) || 0;
        const dinamico = parseFloat(dinamicosCampos[i]?.value) || 0;
        
        if (ingreso > 0 || fijo > 0 || dinamico > 0) {
            mesCompletoEncontrado = true;
            break;
        }
    }
    
    if (!mesCompletoEncontrado) {
        errors.push('Debes ingresar al menos un mes con datos (puede tener ceros en algunos campos)');
    }
    
    if (errors.length > 0) {
        alert('Corrige estos errores:\n\n' + errors.join('\n'));
        return false;
    }
    
    return true;
}

function validarModoDetallado() {
    const errors = [];
    
    const ingresos = document.querySelectorAll('#modo-detallado-container input[name^="ingresos_detallados"][name$="[monto]"]');
    const gastos = document.querySelectorAll('#modo-detallado-container input[name^="gastos_detallados"][name$="[monto]"]');
    const categorias = document.querySelectorAll('#modo-detallado-container select[name^="gastos_detallados"][name$="[categoria_id]"]');
    
    if (Array.from(ingresos).every(input => input.value.trim() === '')) {
        errors.push('Debes ingresar al menos un ingreso');
    }
    
    if (Array.from(gastos).every(input => input.value.trim() === '')) {
        errors.push('Debes ingresar al menos un gasto');
    }
    
    if (Array.from(categorias).every(select => select.value === '')) {
        errors.push('Debes seleccionar categorías para los gastos');
    }
    
    if (errors.length > 0) {
        alert('Por favor corrige los siguientes errores:\n\n' + errors.join('\n'));
        return false;
    }
    
    return true;
}

// Exportar funciones globalmente
window.validarModoRapido = validarModoRapido;
window.validarModoDetallado = validarModoDetallado;