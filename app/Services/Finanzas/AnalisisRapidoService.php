<?php
// app/Services/Finanzas/AnalisisRapidoService.php

namespace App\Services\Finanzas;

use App\Services\Finanzas\EstadisticaService;

class AnalisisRapidoService
{
    /**
     * Servicio para cálculos estadísticos
     * Proporciona métodos para desviación estándar, mediana, moda, etc.
     *
     * @var EstadisticaService
     */
    protected $estadisticaService;

    /**
     * Constructor del servicio
     * Inyecta el servicio de estadísticas necesario para los cálculos
     *
     * @param EstadisticaService $estadisticaService
     */
    public function __construct(EstadisticaService $estadisticaService)
    {
        $this->estadisticaService = $estadisticaService;
    }

    /**
     * PROCESAR DATOS DEL MODO BÁSICO
     * 
     * Método principal que procesa los arrays de ingresos y gastos
     * y calcula todas las métricas financieras:
     * - Promedios, medianas, desviaciones
     * - Porcentajes, coeficientes de variación
     * - Proyecciones anuales y conclusiones
     *
     * @param array $ingresos Array de ingresos mensuales
     * @param array $gastosFijos Array de gastos fijos mensuales
     * @param array $gastosDinamicos Array de gastos dinámicos mensuales
     * @return array Array con todas las métricas calculadas
     * @throws \Exception Si no hay datos suficientes
     */
    public function procesarDatosBasicos($ingresos, $gastosFijos, $gastosDinamicos)
    {
        // ⚠️ VALIDACIÓN INICIAL
        // Verificar que hay datos (puede haber ceros, pero arrays no vacíos)
        if (empty($ingresos) || empty($gastosFijos) || empty($gastosDinamicos)) {
            throw new \Exception('No hay datos suficientes para procesar');
        }

        // 🔍 FILTRAR CEROS PARA CÁLCULOS ESTADÍSTICOS
        // Algunos cálculos requieren excluir ceros para no sesgar resultados
        $ingresosSinCeros = $this->filtrarValoresPositivos($ingresos);
        $gastosFijosSinCeros = $this->filtrarValoresPositivos($gastosFijos);
        $gastosDinamicosSinCeros = $this->filtrarValoresPositivos($gastosDinamicos);

        // 📊 CÁLCULO DE PROMEDIOS
        // Usar arrays originales que pueden incluir ceros para representar meses sin actividad
        $promedios = $this->calcularPromedios($ingresos, $gastosFijos, $gastosDinamicos);

        // 📈 CÁLCULOS ESTADÍSTICOS AVANZADOS
        $estadisticas = $this->calcularEstadisticasAvanzadas(
            $ingresosSinCeros,
            $gastosFijosSinCeros,
            $gastosDinamicosSinCeros,
            $promedios['promIngreso'],
            $promedios['promDinamicos']
        );

        // 📏 CÁLCULO DE RANGOS
        $rangos = $this->calcularRangos(
            $ingresosSinCeros,
            $gastosFijosSinCeros,
            $gastosDinamicosSinCeros
        );

        // 💰 INDICADORES FINANCIEROS Y PROYECCIONES
        $indicadores = $this->calcularIndicadoresFinancieros(
            $promedios['promIngreso'],
            $promedios['promFijos'],
            $promedios['promDinamicos']
        );

        // 🎯 CÁLCULO DE MODA
        $moda = !empty($gastosDinamicosSinCeros) ?
            $this->estadisticaService->calcularModa($gastosDinamicosSinCeros) : 'N/A';

        // 💡 GENERACIÓN DE CONCLUSIONES
        $conclusion = $this->generarConclusion(
            $indicadores['saldo'],
            $indicadores['porcFijos'],
            $estadisticas['desviacion'],
            $estadisticas['cvIngresos'],
            $estadisticas['cvGastosDinamicos']
        );

        // 📦 CONSTRUIR ARRAY DE RESULTADOS
        return $this->construirResultados(
            $promedios,
            $estadisticas,
            $rangos,
            $indicadores,
            $moda,
            $conclusion,
            $ingresos,
            $gastosFijos,
            $gastosDinamicos
        );
    }

    /**
     * Filtrar valores positivos de un array
     * Elimina ceros y valores negativos para cálculos estadísticos
     *
     * @param array $datos Array de valores
     * @return array Array solo con valores > 0
     */
    private function filtrarValoresPositivos($datos)
    {
        return array_filter($datos, function($valor) {
            return $valor > 0;
        });
    }

    /**
     * Calcular promedios de ingresos y gastos
     *
     * @param array $ingresos
     * @param array $gastosFijos
     * @param array $gastosDinamicos
     * @return array Promedios calculados
     */
    private function calcularPromedios($ingresos, $gastosFijos, $gastosDinamicos)
    {
        return [
            'promIngreso'    => !empty($ingresos) ? array_sum($ingresos) / count($ingresos) : 0,
            'promFijos'      => !empty($gastosFijos) ? array_sum($gastosFijos) / count($gastosFijos) : 0,
            'promDinamicos'  => !empty($gastosDinamicos) ? array_sum($gastosDinamicos) / count($gastosDinamicos) : 0
        ];
    }

    /**
     * Calcular estadísticas avanzadas
     * Desviaciones estándar, medianas y coeficientes de variación
     *
     * @param array $ingresosSinCeros
     * @param array $gastosFijosSinCeros
     * @param array $gastosDinamicosSinCeros
     * @param float $promIngreso
     * @param float $promDinamicos
     * @return array Estadísticas calculadas
     */
    private function calcularEstadisticasAvanzadas(
        $ingresosSinCeros,
        $gastosFijosSinCeros,
        $gastosDinamicosSinCeros,
        $promIngreso,
        $promDinamicos
    ) {
        // Desviaciones estándar
        $desviacion = !empty($gastosDinamicosSinCeros) ?
            $this->estadisticaService->calcularDesviacionEstandar($gastosDinamicosSinCeros) : 0;

        $desviacionIngresos = !empty($ingresosSinCeros) ?
            $this->estadisticaService->calcularDesviacionEstandar($ingresosSinCeros) : 0;

        // Medianas
        $medianaIngresos = !empty($ingresosSinCeros) ?
            $this->estadisticaService->calcularMediana($ingresosSinCeros) : 0;

        $medianaFijos = !empty($gastosFijosSinCeros) ?
            $this->estadisticaService->calcularMediana($gastosFijosSinCeros) : 0;

        $medianaDinamicos = !empty($gastosDinamicosSinCeros) ?
            $this->estadisticaService->calcularMediana($gastosDinamicosSinCeros) : 0;

        // Coeficientes de variación
        $cvIngresos = $promIngreso > 0 ? ($desviacionIngresos / $promIngreso) * 100 : 0;
        $cvGastosDinamicos = $promDinamicos > 0 ? ($desviacion / $promDinamicos) * 100 : 0;

        return [
            'desviacion'           => $desviacion,
            'desviacionIngresos'   => $desviacionIngresos,
            'medianaIngresos'      => $medianaIngresos,
            'medianaFijos'         => $medianaFijos,
            'medianaDinamicos'     => $medianaDinamicos,
            'cvIngresos'           => $cvIngresos,
            'cvGastosDinamicos'    => $cvGastosDinamicos
        ];
    }

    /**
     * Calcular rangos (máximo - mínimo)
     *
     * @param array $ingresosSinCeros
     * @param array $gastosFijosSinCeros
     * @param array $gastosDinamicosSinCeros
     * @return array Rangos calculados
     */
    private function calcularRangos($ingresosSinCeros, $gastosFijosSinCeros, $gastosDinamicosSinCeros)
    {
        return [
            'rangoIngresos'   => !empty($ingresosSinCeros) ? max($ingresosSinCeros) - min($ingresosSinCeros) : 0,
            'rangoFijos'      => !empty($gastosFijosSinCeros) ? max($gastosFijosSinCeros) - min($gastosFijosSinCeros) : 0,
            'rangoDinamicos'  => !empty($gastosDinamicosSinCeros) ? max($gastosDinamicosSinCeros) - min($gastosDinamicosSinCeros) : 0
        ];
    }

    /**
     * Calcular indicadores financieros y proyecciones anuales
     *
     * @param float $promIngreso
     * @param float $promFijos
     * @param float $promDinamicos
     * @return array Indicadores calculados
     */
    private function calcularIndicadoresFinancieros($promIngreso, $promFijos, $promDinamicos)
    {
        $totalGastos = $promFijos + $promDinamicos;
        $porcFijos = $promIngreso > 0 ? ($promFijos / $promIngreso) * 100 : 0;
        $porcDinamicos = $promIngreso > 0 ? ($promDinamicos / $promIngreso) * 100 : 0;
        $saldo = $promIngreso - $totalGastos;

        // Proyecciones anuales
        $ingresoAnual = $promIngreso * 12;
        $gastosFijosAnual = $promFijos * 12;
        $gastosDinamicosAnual = $promDinamicos * 12;
        $saldoAnual = $saldo * 12;

        return [
            'porcFijos'             => $porcFijos,
            'porcDinamicos'         => $porcDinamicos,
            'saldo'                 => $saldo,
            'ingresoAnual'          => $ingresoAnual,
            'gastosFijosAnual'      => $gastosFijosAnual,
            'gastosDinamicosAnual'  => $gastosDinamicosAnual,
            'saldoAnual'            => $saldoAnual
        ];
    }

    /**
     * Construir array final de resultados
     *
     * @param array $promedios
     * @param array $estadisticas
     * @param array $rangos
     * @param array $indicadores
     * @param mixed $moda
     * @param array $conclusion
     * @param array $ingresos
     * @param array $gastosFijos
     * @param array $gastosDinamicos
     * @return array Resultados completos
     */
    private function construirResultados(
        $promedios,
        $estadisticas,
        $rangos,
        $indicadores,
        $moda,
        $conclusion,
        $ingresos,
        $gastosFijos,
        $gastosDinamicos
    ) {
        return [
            // Promedios
            'promIngreso'           => $promedios['promIngreso'],
            'promFijos'             => $promedios['promFijos'],
            'promDinamicos'         => $promedios['promDinamicos'],

            // Estadísticas avanzadas
            'desviacion'            => $estadisticas['desviacion'],
            'desviacionIngresos'    => $estadisticas['desviacionIngresos'],
            'medianaIngresos'       => $estadisticas['medianaIngresos'],
            'medianaFijos'          => $estadisticas['medianaFijos'],
            'medianaDinamicos'      => $estadisticas['medianaDinamicos'],
            'cvIngresos'            => $estadisticas['cvIngresos'],
            'cvGastosDinamicos'     => $estadisticas['cvGastosDinamicos'],

            // Rangos
            'rangoIngresos'         => $rangos['rangoIngresos'],
            'rangoFijos'            => $rangos['rangoFijos'],
            'rangoDinamicos'        => $rangos['rangoDinamicos'],

            // Indicadores financieros
            'porcFijos'             => $indicadores['porcFijos'],
            'porcDinamicos'         => $indicadores['porcDinamicos'],
            'saldo'                 => $indicadores['saldo'],
            'ingresoAnual'          => $indicadores['ingresoAnual'],
            'gastosFijosAnual'      => $indicadores['gastosFijosAnual'],
            'gastosDinamicosAnual'  => $indicadores['gastosDinamicosAnual'],
            'saldoAnual'            => $indicadores['saldoAnual'],

            // Otros
            'moda'                  => $moda,
            'conclusion'            => $conclusion,

            // Datos originales
            'ingresos'              => $ingresos,
            'gastosFijos'           => $gastosFijos,
            'gastosDinamicos'       => $gastosDinamicos
        ];
    }

    /**
     * VALIDAR BALANCE DE ARRAYS
     * 
     * Verifica que los tres arrays tengan la misma longitud
     * Esto es necesario para el análisis mensual comparativo
     *
     * @param array $ingresos
     * @param array $gastosFijos
     * @param array $gastosDinamicos
     * @return array ['error' => bool, 'mensaje' => string]
     */
    public function validarBalance($ingresos, $gastosFijos, $gastosDinamicos)
    {
        if (count($ingresos) !== count($gastosFijos) || count($ingresos) !== count($gastosDinamicos)) {
            return [
                'error' => true,
                'mensaje' => 'Error: Los datos están desbalanceados. ' .
                    'Ingresos: ' . count($ingresos) . ' meses, ' .
                    'Fijos: ' . count($gastosFijos) . ' meses, ' .
                    'Dinámicos: ' . count($gastosDinamicos) . ' meses. ' .
                    'Por favor, usa el botón "Agregar Otro Mes" para mantener el balance.'
            ];
        }

        return ['error' => false];
    }

    /**
     * NORMALIZAR ARRAYS DE DATOS
     * 
     * Asegura que todos los arrays tengan la misma longitud
     * Si algún array está vacío, lo rellena con ceros
     *
     * @param array $ingresos
     * @param array $gastosFijos
     * @param array $gastosDinamicos
     * @return array [$ingresos, $gastosFijos, $gastosDinamicos]
     */
    public function normalizarArrays($ingresos, $gastosFijos, $gastosDinamicos)
    {
        $maxMeses = max(
            count($ingresos),
            count($gastosFijos),
            count($gastosDinamicos)
        );

        // Si no hay gastos, crear arrays con ceros
        if (empty($gastosFijos)) {
            $gastosFijos = array_fill(0, $maxMeses, 0);
        }

        if (empty($gastosDinamicos)) {
            $gastosDinamicos = array_fill(0, $maxMeses, 0);
        }

        // Normalizar los arrays para que tengan la misma longitud
        $ingresos = array_pad($ingresos, $maxMeses, 0);
        $gastosFijos = array_pad($gastosFijos, $maxMeses, 0);
        $gastosDinamicos = array_pad($gastosDinamicos, $maxMeses, 0);

        return [$ingresos, $gastosFijos, $gastosDinamicos];
    }

    /**
     * FILTRAR MESES CON DATOS VÁLIDOS
     * 
     * Identifica qué índices (meses) tienen al menos un dato positivo
     * Útil para eliminar meses completamente vacíos
     *
     * @param array $ingresos
     * @param array $gastosFijos
     * @param array $gastosDinamicos
     * @return array Índices de meses con datos
     */
    public function filtrarMesesConDatos($ingresos, $gastosFijos, $gastosDinamicos)
    {
        $maxMeses = count($ingresos);
        $mesesConDatos = [];

        for ($i = 0; $i < $maxMeses; $i++) {
            if ($ingresos[$i] > 0 || $gastosFijos[$i] > 0 || $gastosDinamicos[$i] > 0) {
                $mesesConDatos[] = $i;
            }
        }

        return $mesesConDatos;
    }

    /**
     * APLICAR FILTRO DE MESES CON DATOS
     * 
     * Filtra los arrays para mantener solo los meses con datos válidos
     *
     * @param array $ingresos
     * @param array $gastosFijos
     * @param array $gastosDinamicos
     * @param array $mesesConDatos Índices a mantener
     * @return array [$ingresos, $gastosFijos, $gastosDinamicos] filtrados
     */
    public function aplicarFiltroMeses($ingresos, $gastosFijos, $gastosDinamicos, $mesesConDatos)
    {
        if (count($mesesConDatos) < count($ingresos)) {
            $ingresosFiltrados = [];
            $gastosFijosFiltrados = [];
            $gastosDinamicosFiltrados = [];

            foreach ($mesesConDatos as $index) {
                $ingresosFiltrados[] = $ingresos[$index];
                $gastosFijosFiltrados[] = $gastosFijos[$index];
                $gastosDinamicosFiltrados[] = $gastosDinamicos[$index];
            }

            $ingresos = $ingresosFiltrados;
            $gastosFijos = $gastosFijosFiltrados;
            $gastosDinamicos = $gastosDinamicosFiltrados;
        }

        return [$ingresos, $gastosFijos, $gastosDinamicos];
    }

    /**
     * GENERAR CONCLUSIONES (MEJORADO)
     * 
     * Genera conclusiones personalizadas basadas en los indicadores financieros:
     * - Saldo disponible (positivo/negativo)
     * - Porcentaje de gastos fijos
     * - Variabilidad de ingresos y gastos (coeficiente de variación)
     * - Combinaciones de indicadores para situación óptima
     *
     * @param float $saldo Saldo promedio mensual
     * @param float $porcFijos Porcentaje de gastos fijos
     * @param float $desviacion Desviación estándar de gastos dinámicos
     * @param float $cvIngresos Coeficiente de variación de ingresos
     * @param float $cvGastosDinamicos Coeficiente de variación de gastos dinámicos
     * @return array Array de conclusiones
     */
    private function generarConclusion($saldo, $porcFijos, $desviacion, $cvIngresos, $cvGastosDinamicos)
    {
        $conclusiones = [];

        // ✅ CONCLUSIONES SOBRE SALDO DISPONIBLE
        if ($saldo > 0) {
            $conclusiones[] = "✅ <strong>Tienes capacidad de ahorro/inversión</strong>. Saldo disponible: $" . number_format($saldo, 2);
        } else if ($saldo < 0) {
            $conclusiones[] = "❌ <strong>Gastas más de lo que ganas</strong>. Recomendamos revisar tus gastos.";
        } else {
            $conclusiones[] = "⚠️ <strong>Estás en equilibrio</strong>. No hay saldo disponible para ahorrar.";
        }

        // 📊 CONCLUSIONES SOBRE GASTOS FIJOS
        if ($porcFijos > 50) {
            $conclusiones[] = "📊 <strong>Tus gastos fijos son altos</strong> (" . number_format($porcFijos, 1) . "%). Considera reducirlos.";
        } else if ($porcFijos < 30) {
            $conclusiones[] = "💰 <strong>Tus gastos fijos son bajos</strong> (" . number_format($porcFijos, 1) . "%). Buena gestión.";
        }

        // 📈 CONCLUSIONES SOBRE VARIABILIDAD
        if ($cvIngresos > 20) {
            $conclusiones[] = "📈 <strong>Tus ingresos son variables</strong> (CV: " . number_format($cvIngresos, 1) . "%). Considera fuentes de ingreso estables.";
        } else if ($cvIngresos < 10) {
            $conclusiones[] = "💪 <strong>Tus ingresos son estables</strong> (CV: " . number_format($cvIngresos, 1) . "%). Excelente predictibilidad.";
        }

        if ($cvGastosDinamicos > 25) {
            $conclusiones[] = "🎯 <strong>Tus gastos dinámicos son muy variables</strong> (CV: " . number_format($cvGastosDinamicos, 1) . "%). Intenta estabilizarlos.";
        } else if ($cvGastosDinamicos < 15) {
            $conclusiones[] = "📋 <strong>Buen control de gastos variables</strong> (CV: " . number_format($cvGastosDinamicos, 1) . "%).";
        }

        // 🏆 CONCLUSIONES COMBINADAS
        if ($cvIngresos < 10 && $cvGastosDinamicos < 15) {
            $conclusiones[] = "🏆 <strong>Excelente estabilidad financiera</strong>. Tus ingresos y gastos son predecibles.";
        }

        if ($saldo > 0 && $porcFijos < 40 && $cvIngresos < 15) {
            $conclusiones[] = "🌟 <strong>Situación financiera óptima</strong>. Tienes control total sobre tus finanzas.";
        }

        return $conclusiones;
    }
}