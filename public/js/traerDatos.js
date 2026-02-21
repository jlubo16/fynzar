document.addEventListener("DOMContentLoaded", function() {
    
    function filtrarDatos(datos) {
        return datos
            .toLowerCase() // Convertir a minúsculas
            .normalize("NFD") // Normalizar caracteres con tildes
            .replace(/[\u0300-\u036f]/g, ""); // Eliminar marcas diacríticas (tildes)
    }

    $("#procesar").on("click", function(event) {
        event.preventDefault();
        const datos = $("#datos").val();
        const arrayDatos = datos.split(",").map(item => filtrarDatos(item.trim()));
     
    });
    // Implementar el token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    });

    $("#procesar").on("click", function(event) {
        event.preventDefault();
        const datos = $("#datos").val();
        const datosFiltrados = filtrarDatos(datos);
        $.ajax({
            url: 'http://127.0.0.1:8000/datos',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ datos: datosFiltrados }),
            success: function(data) {
              
                mostrarResultados(data);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    });

    // Mostrar resultados
    function mostrarResultados(data) {
        const resultadosDiv = $("#tabla-frecuencias");
        resultadosDiv.empty();

        let totalFrecuencia = 0;
        let totalRelativa = 0;
        let totalPorcentaje = 0;

        // Arrays para Chart.js
        let labels = [];
        let frecuencias = [];
        let porcentajes = [];

        Object.entries(data).forEach(([key, item]) => {
            resultadosDiv.append(`
                <tr>
                    <td>${key}</td>
                    <td>${item["Frecuencia Absoluta"]}</td>
                    <td>${item["Frecuencia Relativa"]}</td>
                    <td>${item["Frecuencia Acumulada"]}</td>
                    <td>${item["Frecuencia Relativa Acumulada"]}</td>
                    <td>${item["Porcentaje"]}</td>
                </tr>
            `);

            // Acumulamos los totales
            totalFrecuencia += Number(item["Frecuencia Absoluta"]);
            totalRelativa += Number(item["Frecuencia Relativa"]);
            totalPorcentaje += Number(item["Porcentaje"]);

            // Guardamos datos para Chart.js
            labels.push(key);
            frecuencias.push(item["Frecuencia Absoluta"]);
            porcentajes.push(item["Porcentaje"]);
        });

        // Agregar la fila de totales
        resultadosDiv.append(`
            <tr style="font-weight:bold; background:#f0f0f0;">
                <td>Total</td>
                <td>${totalFrecuencia}</td>
                <td>${totalRelativa.toFixed(2)}</td>
                <td>-</td>
                <td>-</td>
                <td>${totalPorcentaje.toFixed(2)}%</td>
            </tr>
        `);

        // Llamamos a la función que genera los gráficos
        generarGraficos(labels, frecuencias, porcentajes);
    }

    // Función para generar gráficos con Chart.js
    function generarGraficos(labels, frecuencias, porcentajes) {
      
        
        // Gráfico de barras
        new Chart(document.getElementById("barChart"), {
            type: "bar",
            data: {
                labels: labels,
                datasets: [{
                    label: "Frecuencia Absoluta",
                    data: frecuencias,
                    backgroundColor: "rgba(54, 162, 235, 0.6)",
                    borderColor: "rgba(54, 162, 235, 1)",
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

    // Gráfico anillo (doughnut) con porcentajes dentro
new Chart(document.getElementById("pieChart"), {
    type: "doughnut", // <- Cambié pie por doughnut
    data: {
        labels: labels,
        datasets: [{
            label: "Porcentaje",
            data: porcentajes,
            backgroundColor: [
                '#4285F4', // Azul vivo
                '#FB8C00', // Naranja brillante
                '#E53935', // Rojo fuerte
                '#43A047', // Verde vivo
                '#00ACC1', // Cian
                '#FDD835', // Amarillo intenso
                '#8E24AA', // Morado fuerte
                '#F4511E', // Naranja rojizo
                '#6D4C41', // Marrón
                '#BDBDBD'  // Gris claro
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: "bottom"
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ": " + context.raw + "%";
                    }
                }
            },
            datalabels: {
                color: "#fff",
                font: {
                    weight: "bold",
                    size: 14
                },
                formatter: (value) => value + "%" // Muestra 20% en cada porción
            }
        }
    },
    plugins: [ChartDataLabels] // <- Activo el plugin
});
}

});