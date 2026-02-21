// MÓDULO: INICIALIZACIÓN
// ============================================

function inicializarAnalisis() {
    // Limpiar valores de ejemplo
    limpiarValoresIniciales();
    
    // Balancear y actualizar indicadores
    balancearMeses();
    updateMonthIndicators();
    renumerarBotonesEliminar();
    
    // Manejo de cambio entre modos
    const rapidoContainer = document.getElementById('modo-rapido-container');
    const detalladoContainer = document.getElementById('modo-detallado-container');
    
    function actualizarRequired(modo) {
        if (modo === 'rapido') {
            document.querySelectorAll('#modo-rapido-container input[type="number"]').forEach(input => {
                input.required = true;
            });
            document.querySelectorAll('#modo-detallado-container [required]').forEach(field => {
                field.removeAttribute('required');
            });
        } else {
            document.querySelectorAll('#modo-rapido-container input[type="number"]').forEach(input => {
                input.removeAttribute('required');
            });
            document.querySelectorAll('#modo-detallado-container input[type="number"], #modo-detallado-container select').forEach(field => {
                field.required = true;
            });
        }
    }
    
    // Inicializar modo rápido
    actualizarRequired('rapido');
    
    // Event listeners para radios de modo
    document.querySelectorAll('input[name="modo_ingreso"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const modo = this.value;
            
            if (modo === 'rapido') {
                rapidoContainer.style.display = 'block';
                detalladoContainer.style.display = 'none';
            } else {
                rapidoContainer.style.display = 'none';
                detalladoContainer.style.display = 'block';
            }
            
            actualizarRequired(modo);
        });
    });
    
    // Validación del formulario principal
    const form = document.getElementById('mainForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const modo = document.querySelector('input[name="modo_ingreso"]:checked').value;
            let isValid = true;
            
            if (modo === 'rapido') {
                isValid = validarModoRapido();
            } else {
                isValid = validarModoDetallado();
            }
            
            if (!isValid) {
                e.preventDefault();
                return false;
            }
            
            // Mostrar loading spinner
            document.getElementById('loading-spinner').style.display = 'block';
            
            // Verificar datos antes de enviar
            verificarDatos();
        });
    }
    
    // Verificar datos al cargar
    setTimeout(verificarDatos, 500);
    
    console.log('✅ Análisis inicializado correctamente');
}

// Exportar la función de inicialización
window.inicializarAnalisis = inicializarAnalisis;