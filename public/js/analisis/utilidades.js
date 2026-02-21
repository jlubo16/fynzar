// MÓDULO: UTILIDADES
// ============================================

function descargarPlantilla() {
    const contenido = `ingresos,gastos_fijos,gastos_dinamicos\n2800,850,550\n3000,900,600\n2750,800,500`;
    const blob = new Blob([contenido], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    
    a.href = url;
    a.download = 'plantilla-finanzas-familiares.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    
    alert('Plantilla descargada. Ábrela con Excel y completa con tus datos.');
}

function limpiarValoresIniciales() {
    const containers = ['ingresos-container', 'fijos-container', 'dinamicos-container'];
    containers.forEach(containerId => {
        const container = document.getElementById(containerId);
        const inputs = container.querySelectorAll('input[type="number"]');
        inputs.forEach(input => input.value = '');
        
        while (container.querySelectorAll('.input-group').length > 1) {
            container.querySelector('.input-group:last-child').remove();
        }
    });
}

function verificarDatos() {
    const ingresos = document.querySelectorAll('#ingresos-container input').length;
    const fijos = document.querySelectorAll('#fijos-container input').length;
    const dinamicos = document.querySelectorAll('#dinamicos-container input').length;
    
    if (ingresos !== fijos || fijos !== dinamicos) {
        console.warn('Desbalance detectado. Balanceando automáticamente...');
        balancearMeses();
    }
}

// Exportar funciones globalmente
window.descargarPlantilla = descargarPlantilla;
window.limpiarValoresIniciales = limpiarValoresIniciales;
window.verificarDatos = verificarDatos;