document.addEventListener('DOMContentLoaded', () => {
    if (!window.datosGraficos || window.datosGraficos.promIngreso <= 0) return;

    const { promIngreso, promFijos, promDinamicos } = window.datosGraficos;

    // Gráfico de pastel
    const ctxPie = document.getElementById('gastosChart')?.getContext('2d');
    if (ctxPie) {
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['Gastos Fijos', 'Gastos Dinámicos'],
                datasets: [{
                    data: [promFijos, promDinamicos],
                    backgroundColor: ['#007bff', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    // Gráfico de barras
    const ctxBar = document.getElementById('ingresosVsGastos')?.getContext('2d');
    if (ctxBar) {
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Ingresos', 'Gastos Fijos', 'Gastos Dinámicos'],
                datasets: [{
                    label: 'Monto ($)',
                    data: [promIngreso, promFijos, promDinamicos],
                    backgroundColor: ['#28a745', '#007bff', '#ffc107']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true } }
            }
        });
    }
});