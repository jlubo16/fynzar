// public/js/frecuencias.js - VERSIÓN CON DEPURACIÓN

console.log('frecuencias.js cargado');

// Verificar que Chart.js esté disponible
if (typeof Chart === 'undefined') {
    console.error('ERROR: Chart.js no está cargado');
} else {
    console.log('Chart.js disponible:', Chart.version);
}

// Funciones para exportar tablas a CSV
function exportarTabla(tableId) {
    console.log('exportarTabla llamado para:', tableId);
    try {
        const tabla = document.getElementById(tableId);
        if (!tabla) {
            alert('No se encontró la tabla: ' + tableId);
            return;
        }
        
        let csv = [];
        
        // Obtener headers
        const headers = [];
        for (let i = 0; i < tabla.rows[0].cells.length; i++) {
            headers.push(tabla.rows[0].cells[i].innerText);
        }
        csv.push(headers.join(','));
        
        // Obtener datos
        for (let i = 1; i < tabla.rows.length; i++) {
            const row = [];
            for (let j = 0; j < tabla.rows[i].cells.length; j++) {
                row.push(tabla.rows[i].cells[j].innerText);
            }
            csv.push(row.join(','));
        }
        
        // Descargar
        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        
        a.href = url;
        a.download = `tabla-frecuencia-${tableId}-${new Date().toISOString().split('T')[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        alert('Tabla exportada como CSV');
    } catch (error) {
        console.error('Error en exportarTabla:', error);
        alert('Error al exportar la tabla: ' + error.message);
    }
}

// Función para imprimir reporte
function imprimirReporte() {
    console.log('imprimirReporte llamado');
    window.print();
}

// Función para verificar que los elementos del DOM existan
function elementoExiste(id) {
    const elemento = document.getElementById(id);
    const existe = elemento !== null;
    console.log(`Elemento ${id} existe:`, existe);
    return existe;
}

// Inicializar histograma con validaciones
function inicializarHistograma() {
    console.log('Intentando inicializar histograma');
    
    if (!elementoExiste('histogramaChart')) {
        console.error('No se encontró canvas histogramaChart');
        return false;
    }
    
    if (!window.histogramaData || !window.histogramaData.data) {
        console.error('No hay datos para histograma:', window.histogramaData);
        return false;
    }
    
    try {
        const ctx1 = document.getElementById('histogramaChart').getContext('2d');
        console.log('Contexto obtenido, datos:', window.histogramaData);
        
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: window.histogramaData.labels,
                datasets: [{
                    label: 'Frecuencia de Gastos',
                    data: window.histogramaData.data,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Frecuencia'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Rango de Gastos ($)'
                        }
                    }
                }
            }
        });
        
        console.log('Histograma inicializado correctamente');
        return true;
    } catch (error) {
        console.error('Error inicializando histograma:', error);
        return false;
    }
}

// Inicializar polígono de frecuencias
function inicializarPoligono() {
    console.log('Intentando inicializar polígono');
    
    if (!elementoExiste('poligonoChart')) {
        console.error('No se encontró canvas poligonoChart');
        return false;
    }
    
    if (!window.histogramaData || !window.histogramaData.data) {
        console.error('No hay datos para polígono');
        return false;
    }
    
    try {
        const ctx2 = document.getElementById('poligonoChart').getContext('2d');
        
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: window.histogramaData.labels,
                datasets: [{
                    label: 'Polígono de Frecuencias',
                    data: window.histogramaData.data,
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        console.log('Polígono inicializado correctamente');
        return true;
    } catch (error) {
        console.error('Error inicializando polígono:', error);
        return false;
    }
}

// Inicializar gráfico de categorías
function inicializarGraficoCategorias() {
    console.log('Intentando inicializar gráfico de categorías');
    
    if (!elementoExiste('categoriasChart')) {
        console.error('No se encontró canvas categoriasChart');
        return false;
    }
    
    if (!window.tablaCategoriasData) {
        console.error('No hay datos para categorías:', window.tablaCategoriasData);
        return false;
    }
    
    try {
        const ctx3 = document.getElementById('categoriasChart').getContext('2d');
        
        new Chart(ctx3, {
            type: 'doughnut',
            data: {
                labels: window.tablaCategoriasData.labels,
                datasets: [{
                    data: window.tablaCategoriasData.porcentajes,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
                        '#9966FF', '#FF9F40', '#FF6384', '#C9CBCF'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
        
        console.log('Gráfico de categorías inicializado correctamente');
        return true;
    } catch (error) {
        console.error('Error inicializando gráfico de categorías:', error);
        return false;
    }
}

// Inicialización principal
function inicializarTodosLosGraficos() {
    console.log('=== INICIALIZANDO GRÁFICOS ===');
    console.log('Chart disponible:', typeof Chart);
    console.log('Datos histograma:', window.histogramaData);
    console.log('Datos categorías:', window.tablaCategoriasData);
    
    let graficosInicializados = 0;
    
    if (window.histogramaData && window.histogramaData.data) {
        if (inicializarHistograma()) graficosInicializados++;
        if (inicializarPoligono()) graficosInicializados++;
    }
    
    if (window.tablaCategoriasData) {
        if (inicializarGraficoCategorias()) graficosInicializados++;
    }
    
    console.log(`Total gráficos inicializados: ${graficosInicializados}`);
}

// Esperar a que todo esté listo
function inicializarApp() {
    console.log('=== INICIANDO APLICACIÓN FRECUENCIAS ===');
    
    // Esperar a que Chart.js esté disponible
    function esperarChartJs(callback) {
        if (typeof Chart !== 'undefined') {
            console.log('Chart.js ya disponible');
            callback();
        } else {
            console.log('Esperando Chart.js...');
            setTimeout(() => esperarChartJs(callback), 100);
        }
    }
    
    // Esperar a que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM completamente cargado');
            esperarChartJs(inicializarTodosLosGraficos);
        });
    } else {
        console.log('DOM ya cargado');
        esperarChartJs(inicializarTodosLosGraficos);
    }
}

// Iniciar la aplicación
inicializarApp();

// Hacer funciones disponibles globalmente
window.exportarTabla = exportarTabla;
window.imprimirReporte = imprimirReporte;

console.log('frecuencias.js finalizado');