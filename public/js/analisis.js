// Cargador de módulos - REEMPLAZA el archivo actual
// ============================================

// Cargar todos los módulos necesarios
function cargarModulosAnalisis() {
    const basePath = window.assetsPath || '/js/'; // Ajusta según tu configuración

const modulos = [
    `${basePath}analisis/modo-rapido.js`,
    `${basePath}analisis/modo-detallado.js`,
    `${basePath}analisis/validaciones.js`,
    `${basePath}analisis/utilidades.js`,
    `${basePath}analisis/inicializacion.js`
];

    // Función para cargar un script dinámicamente
    function cargarScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    // Cargar todos los módulos en paralelo
    Promise.all(modulos.map(cargarScript))
        .then(() => {
            console.log('✅ Módulos de análisis cargados correctamente');
            // Inicializar la aplicación después de cargar todo
            if (window.inicializarAnalisis) {
                window.inicializarAnalisis();
            }
        })
        .catch(error => {
            console.error('❌ Error cargando módulos:', error);
        });
}

// Cargar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', cargarModulosAnalisis);


// En tu archivo JavaScript
document.getElementById('mainForm').addEventListener('submit', function(e) {
    const modo = document.querySelector('input[name="modo_ingreso"]:checked').value;
    
    if (modo === 'rapido') {
        // Deshabilitar campos del modo detallado
        document.querySelectorAll('[name^="ingresos_detallados"]').forEach(input => {
            input.disabled = true;
        });
        document.querySelectorAll('[name^="gastos_detallados"]').forEach(input => {
            input.disabled = true;
        });
    } else {
        // Deshabilitar campos del modo rápido
        document.querySelectorAll('[name="ingresos[]"]').forEach(input => {
            input.disabled = true;
        });
        document.querySelectorAll('[name="gastos_fijos[]"]').forEach(input => {
            input.disabled = true;
        });
        document.querySelectorAll('[name="gastos_dinamicos[]"]').forEach(input => {
            input.disabled = true;
        });
        document.querySelectorAll('[name="periodos[]"]').forEach(input => {
            input.disabled = true;
        });
    }
});

// 1. Función para plantilla SIMPLE (Modo Rápido)
function descargarPlantillaSimple() {
    // Contenido CSV para modo rápido
    const csvContent = "Mes,Ingresos,Gastos Fijos,Gastos Dinamicos\n" +
                      "enero_2024,5000,2000,1500\n" +
                      "febrero_2024,5200,2100,1400\n" +
                      "marzo_2024,4800,1900,1600\n" +
                      "abril_2024,5100,2050,1550\n" +
                      "mayo_2024,5300,2200,1350";
    
    // Crear blob y descargar
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", "plantilla_modo_rapido.csv");
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

// 2. Función para plantilla DETALLADA (Modo Detallado)
function descargarPlantillaDetallada() {
    // Contenido CSV para modo detallado
    const csvContent = "Tipo,Mes,Categoria,Subcategoria,Monto,Descripcion\n" +
                      "Ingreso,2024-01,,,5000,Salario principal\n" +
                      "Gasto,2024-01,Vivienda,Arriendo,1200,Pago mensual arriendo\n" +
                      "Gasto,2024-01,Transporte,Gasolina,300,Gasolina para el carro\n" +
                      "Gasto,2024-01,Alimentacion,Supermercado,400,Compra del mes\n" +
                      "Ingreso,2024-02,,,5200,Salario principal\n" +
                      "Gasto,2024-02,Vivienda,Arriendo,1200,Pago mensual arriendo";
    
    // Crear blob y descargar
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute("href", url);
        link.setAttribute("download", "plantilla_modo_detallado.csv");
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}
// 3. Hacerlas disponibles globalmente
window.descargarPlantillaSimple = descargarPlantillaSimple;
window.descargarPlantillaDetallada = descargarPlantillaDetallada;