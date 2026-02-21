<?php
// app/Services/Finanzas/ReporteService.php

namespace App\Services\Finanzas;

use App\Models\Category;

class ReporteService
{
    /**
     * Servicio para cálculos estadísticos
     *
     * @var EstadisticaService
     */
    protected $estadisticaService;

    /**
     * Servicio para importación CSV
     *
     * @var CSVImportService
     */
    protected $csvImportService;

    /**
     * Constructor del servicio
     * Inyecta las dependencias necesarias para generación de reportes
     *
     * @param EstadisticaService $estadisticaService
     * @param CSVImportService $csvImportService
     */
    public function __construct(
        EstadisticaService $estadisticaService,
        CSVImportService $csvImportService
    ) {
        $this->estadisticaService = $estadisticaService;
        $this->csvImportService = $csvImportService;
    }

    /**
     * GENERAR ANÁLISIS DE FRECUENCIAS
     * 
     * Crea tablas de frecuencia y métricas estadísticas
     * para visualizar la distribución de gastos
     *
     * @param array|null $sessionData Datos de análisis de la sesión
     * @return array|null Datos de frecuencia o null si no hay datos
     */
    public function generarAnalisisFrecuencias($sessionData)
    {
        if (!$sessionData) {
            return null;
        }

        // 📊 1. TABLAS EXISTENTES
        $tablaFrecuencia = $this->estadisticaService->generarTablaFrecuenciaCompleta(
            $sessionData['gastosDinamicos'] ?? []
        );

        $tablaCategorias = $this->generarFrecuenciaCategorias($sessionData);
        $histogramaData = $this->estadisticaService->prepararDatosHistograma(
            $sessionData['gastosDinamicos'] ?? []
        );

        // 📈 2. CALCULAR MÉTRICAS
        $metricasCalculadas = $this->estadisticaService->calcularMetricasFrecuencias(
            $sessionData['gastosDinamicos'] ?? [],
            $tablaFrecuencia
        );

        // 📊 3. CALCULAR MEDIDAS ESTADÍSTICAS
        $gastosIndividuales = $this->csvImportService->extraerMontosIndividuales(
            $sessionData['gastos_detallados'] ?? []
        );

        $medidasEstadisticas = $this->estadisticaService->calcularMedidas($gastosIndividuales);

        // 📋 4. PREPARAR DATOS PARA JAVASCRIPT
        $categoriasData = $this->prepararDatosCategoriasJS($tablaCategorias);

        return [
            'tablaFrecuencia'     => $tablaFrecuencia,
            'tablaCategorias'     => $tablaCategorias,
            'histogramaData'      => $histogramaData,
            'datosAnalisis'       => $sessionData,
            'categoriasData'      => $categoriasData,
            'rangoVariacion'      => $metricasCalculadas['rangoVariacion'] ?? ['diferencia' => 0],
            'porcentajeExtremos'  => $metricasCalculadas['porcentajeExtremos'] ?? 0,
            'montoIdeal'          => $metricasCalculadas['montoIdeal'] ?? 0,
            'medidasEstadisticas' => $medidasEstadisticas
        ];
    }

    /**
     * GENERAR FRECUENCIA POR CATEGORÍAS
     * 
     * Determina qué método usar según el tipo de datos disponibles:
     * - PRIORIDAD 1: Gastos detallados (CSV detallado)
     * - PRIORIDAD 2: Análisis de categorías (modo detallado manual)
     * - PRIORIDAD 3: Gastos por categoría
     * - PRIORIDAD 4: Modo básico (mensaje informativo)
     *
     * @param array $datosAnalisis Datos de análisis
     * @return array Tabla de frecuencia o mensaje para modo básico
     */
    public function generarFrecuenciaCategorias($datosAnalisis)
    {
        // 🥇 PRIORIDAD 1: Gastos detallados (modo detallado CSV)
        if (isset($datosAnalisis['gastos_detallados']) &&
            !empty($datosAnalisis['gastos_detallados'])) {
            return $this->generarFrecuenciaDesdeGastosDetallados(
                $datosAnalisis['gastos_detallados']
            );
        }

        // 🥈 PRIORIDAD 2: Análisis de categorías (modo detallado manual)
        if (isset($datosAnalisis['analisis_categorias']) &&
            !empty($datosAnalisis['analisis_categorias'])) {
            return $this->generarFrecuenciaDeAnalisisCategorias(
                $datosAnalisis['analisis_categorias']
            );
        }

        // 🥉 PRIORIDAD 3: Gastos por categoría
        if (isset($datosAnalisis['gastos_por_categoria']) &&
            !empty($datosAnalisis['gastos_por_categoria'])) {
            return $this->generarFrecuenciaDeGastosCategoria(
                $datosAnalisis['gastos_por_categoria']
            );
        }

        // ℹ️ PRIORIDAD 4: Modo básico - mensaje informativo
        return [
            'tipo'    => 'modo_basico',
            'mensaje' => 'Para ver frecuencia por categorías, usa el Modo Detallado',
            'datos'   => [] // Array vacío
        ];
    }

    /**
     * GENERAR FRECUENCIA DESDE GASTOS DETALLADOS (CSV DETALLADO)
     * 
     * Procesa gastos individuales del CSV para crear tabla de frecuencia
     * por categorías con frecuencias absolutas, acumuladas y porcentajes
     *
     * @param array $gastosDetallados Lista de gastos individuales
     * @return array Tabla de frecuencia por categorías
     */
    private function generarFrecuenciaDesdeGastosDetallados($gastosDetallados)
    {
        $frecuencias = [];

        // 📝 CONTAR FRECUENCIAS POR CATEGORÍA
        foreach ($gastosDetallados as $gasto) {
            $categoria = $gasto['categoria'] ?? 'Sin categoría';

            if (empty($categoria)) {
                $categoria = 'Sin categoría';
            }

            if (!isset($frecuencias[$categoria])) {
                $frecuencias[$categoria] = 0;
            }

            $frecuencias[$categoria]++;
        }

        // Ordenar de mayor a menor frecuencia
        arsort($frecuencias);

        $totalGastos = count($gastosDetallados);
        $tabla = [];
        $acumulado = 0;
        $porcentajeAcumulado = 0;

        // 📊 CONSTRUIR TABLA CON MÉTRICAS
        foreach ($frecuencias as $categoria => $frecuencia) {
            $porcentaje = ($frecuencia / $totalGastos) * 100;
            $acumulado += $frecuencia;
            $porcentajeAcumulado += $porcentaje;

            // Calcular frecuencia mensual (asumiendo 3 meses)
            $frecuenciaMensual = $totalGastos > 0 ? ($frecuencia / 3) : 0; // 3 meses en tu CSV

            $tabla[] = [
                'categoria'              => $categoria,
                'frecuencia'             => $frecuencia,
                'frecuencia_acumulada'   => $acumulado,
                'porcentaje'              => number_format($porcentaje, 1) . '%',
                'porcentaje_acumulado'    => number_format($porcentajeAcumulado, 1) . '%',
                'frecuencia_mensual'      => number_format($frecuenciaMensual, 1)
            ];
        }

        return $tabla;
    }

    /**
     * GENERAR FRECUENCIA DESDE ANÁLISIS DE CATEGORÍAS (MODO DETALLADO)
     * 
     * Convierte análisis de categorías existente en tabla de frecuencia
     *
     * @param array $analisisCategorias Análisis de categorías
     * @return array Tabla de frecuencia por categorías
     */
    private function generarFrecuenciaDeAnalisisCategorias($analisisCategorias)
    {
        $total = 0;
        $frecuencias = [];

        // 📝 EXTRAER FRECUENCIAS APROXIMADAS
        foreach ($analisisCategorias as $analisis) {
            if (isset($analisis['categoria'])) {
                $nombre = $analisis['categoria']->name ?? $analisis['categoria'];
                $totalGasto = $analisis['total'] ?? 0;

                // Convertir total a frecuencia (aproximado basado en tamaño)
                $frecuencia = max(1, intval($totalGasto / 100)); // Ajustar según necesidad
                $frecuencias[$nombre] = ($frecuencias[$nombre] ?? 0) + $frecuencia;
                $total += $frecuencia;
            }
        }

        return $this->procesarTablaCategorias($frecuencias, $total);
    }

    /**
     * GENERAR FRECUENCIA DESDE GASTOS POR CATEGORÍA
     * 
     * Convierte gastos agrupados por categoría en tabla de frecuencia
     *
     * @param array $gastosPorCategoria Gastos agrupados por categoría
     * @return array Tabla de frecuencia por categorías
     */
    private function generarFrecuenciaDeGastosCategoria($gastosPorCategoria)
    {
        $frecuencias = [];
        $total = 0;

        // 📝 EXTRAER FRECUENCIAS APROXIMADAS
        foreach ($gastosPorCategoria as $categoriaId => $datos) {
            if (isset($datos['categoria'])) {
                $nombre = $datos['categoria']->name ?? "Categoría $categoriaId";
                $totalGasto = $datos['total'] ?? 0;

                $frecuencia = max(1, intval($totalGasto / 50)); // Ajustar divisor según datos
                $frecuencias[$nombre] = $frecuencia;
                $total += $frecuencia;
            }
        }

        return $this->procesarTablaCategorias($frecuencias, $total);
    }

    /**
     * PROCESAR TABLA DE CATEGORÍAS COMÚN
     * 
     * Formatea los datos de frecuencia en una tabla estandarizada
     * con frecuencias absolutas, acumuladas y porcentajes
     *
     * @param array $frecuencias Frecuencias por categoría
     * @param int $total Total de frecuencias
     * @return array Tabla formateada
     */
    private function procesarTablaCategorias($frecuencias, $total)
    {
        if ($total == 0) {
            return [];
        }

        $tabla = [];
        $frecuenciaAcumulada = 0;
        $porcentajeAcumulado = 0;

        // Ordenar por frecuencia descendente
        arsort($frecuencias);

        // 📊 CONSTRUIR TABLA
        foreach ($frecuencias as $categoria => $frecuencia) {
            $porcentaje = ($frecuencia / $total) * 100;
            $frecuenciaAcumulada += $frecuencia;
            $porcentajeAcumulado += $porcentaje;

            $tabla[] = [
                'categoria'              => $categoria,
                'frecuencia'             => $frecuencia,
                'frecuencia_acumulada'   => $frecuenciaAcumulada,
                'porcentaje'              => number_format($porcentaje, 1) . '%',
                'porcentaje_acumulado'    => number_format($porcentajeAcumulado, 1) . '%'
            ];
        }

        return $tabla;
    }

    /**
     * PREPARAR DATOS DE CATEGORÍAS PARA JAVASCRIPT
     * 
     * Convierte tabla de categorías en formato compatible con Chart.js
     *
     * @param array $tablaCategorias Tabla de categorías
     * @return array Datos formateados para gráficos
     */
    public function prepararDatosCategoriasJS($tablaCategorias)
    {
        if (empty($tablaCategorias) || isset($tablaCategorias['tipo'])) {
            return [
                'labels'      => [],
                'porcentajes' => []
            ];
        }

        $labels = [];
        $porcentajes = [];

        foreach ($tablaCategorias as $fila) {
            $labels[] = $fila['categoria'];
            $porcentajes[] = floatval(str_replace('%', '', $fila['porcentaje']));
        }

        return [
            'labels'      => $labels,
            'porcentajes' => $porcentajes
        ];
    }

    /**
     * PREPARAR DATOS PARA ANÁLISIS COMPARATIVO (INTELIGENTE)
     * 
     * Detecta automáticamente el modo de análisis y utiliza
     * el método adecuado para preparar comparativos
     *
     * @param array $datosAnalisis Datos de análisis
     * @return array Datos comparativos
     */
    public function prepararDatosComparativos($datosAnalisis)
    {
        $modo = $datosAnalisis['modo_analisis'] ?? 'basico';
        
        if ($modo === 'detallado') {
            return $this->prepararComparativoDetallado($datosAnalisis);
        } else {
            return $this->prepararComparativoBasico($datosAnalisis);
        }
    }

    /**
     * COMPARATIVO PARA MODO DETALLADO (sin separación fijo/dinámico)
     * 
     * Utiliza datos reales de ingresos_por_mes y gastos_por_mes
     * del CSV o entrada manual detallada
     *
     * @param array $datosAnalisis Datos de análisis detallado
     * @return array Datos comparativos por mes
     */
    private function prepararComparativoDetallado($datosAnalisis)
    {
        // En modo detallado, usamos los datos REALES del CSV
        $ingresosPorMes = $datosAnalisis['ingresos_por_mes'] ?? [];
        $gastosPorMes = $datosAnalisis['gastos_por_mes'] ?? [];
        
        if (empty($ingresosPorMes)) {
            return [];
        }

        $comparativos = [];
        $meses = array_keys($ingresosPorMes);
        
        foreach ($meses as $index => $mes) {
            $ingreso = $ingresosPorMes[$mes] ?? 0;
            $gastoTotal = $gastosPorMes[$mes] ?? 0;
            $saldo = $ingreso - $gastoTotal;
            
            // Porcentaje de gastos
            $porcTotalGastos = $ingreso > 0 ? ($gastoTotal / $ingreso) * 100 : 0;

            $comparativos[] = [
                'mes'                => $this->formatearMes($mes), // Ej: "Ene 2024"
                'mes_original'       => $mes, // "2024-01"
                'ingreso'            => $ingreso,
                'gasto_fijo'         => 0, // En modo detallado no hay separación
                'gasto_dinamico'     => $gastoTotal, // Todos los gastos van aquí
                'gasto_total'        => $gastoTotal,
                'saldo'              => $saldo,
                'porc_fijo'          => 0,
                'porc_dinamico'      => $porcTotalGastos,
                'porc_total_gastos'  => $porcTotalGastos,
                'tendencia'          => $this->determinarTendenciaDetallado($index, $ingresosPorMes, $gastosPorMes),
            ];
        }

        return $comparativos;
    }

    /**
     * COMPARATIVO PARA MODO BÁSICO (con separación fijo/dinámico)
     * 
     * Utiliza arrays indexados de ingresos, gastos fijos y dinámicos
     *
     * @param array $datosAnalisis Datos de análisis básico
     * @return array Datos comparativos por mes
     */
    private function prepararComparativoBasico($datosAnalisis)
    {
        $ingresos = $datosAnalisis['ingresos'] ?? [];
        $gastosFijos = $datosAnalisis['gastosFijos'] ?? [];
        $gastosDinamicos = $datosAnalisis['gastosDinamicos'] ?? [];

        if (empty($ingresos)) {
            return [];
        }

        $comparativos = [];
        $totalMeses = count($ingresos);

        for ($i = 0; $i < $totalMeses; $i++) {
            $ingreso = $ingresos[$i] ?? 0;
            $gastoFijo = $gastosFijos[$i] ?? 0;
            $gastoDinamico = $gastosDinamicos[$i] ?? 0;
            $gastoTotal = $gastoFijo + $gastoDinamico;
            $saldo = $ingreso - $gastoTotal;

            // Porcentajes
            $porcFijo = $ingreso > 0 ? ($gastoFijo / $ingreso) * 100 : 0;
            $porcDinamico = $ingreso > 0 ? ($gastoDinamico / $ingreso) * 100 : 0;
            $porcTotalGastos = $ingreso > 0 ? ($gastoTotal / $ingreso) * 100 : 0;

            $comparativos[] = [
                'mes'               => $i + 1,
                'ingreso'           => $ingreso,
                'gasto_fijo'        => $gastoFijo,
                'gasto_dinamico'    => $gastoDinamico,
                'gasto_total'       => $gastoTotal,
                'saldo'             => $saldo,
                'porc_fijo'         => $porcFijo,
                'porc_dinamico'     => $porcDinamico,
                'porc_total_gastos' => $porcTotalGastos,
                'tendencia'         => $this->determinarTendencia($i, $ingresos, $gastoTotal),
            ];
        }

        return $comparativos;
    }

    /**
     * FORMATO DE MES PARA MOSTRAR
     * 
     * Convierte formato YYYY-MM a texto legible (Ej: "Ene 2024")
     *
     * @param string $mesString Mes en formato YYYY-MM
     * @return string Mes formateado
     */
    private function formatearMes($mesString)
    {
        if (preg_match('/^(\d{4})-(\d{2})$/', $mesString, $matches)) {
            $mesNum = (int)$matches[2];
            $anio = $matches[1];
            
            $meses = [
                1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 
                5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago',
                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
            ];
            
            return ($meses[$mesNum] ?? 'Mes') . ' ' . $anio;
        }
        
        return $mesString;
    }

    /**
     * DETERMINAR TENDENCIA PARA MODO DETALLADO
     * 
     * Analiza la evolución de ingresos y gastos respecto al mes anterior
     * 
     * @param int $index Índice del mes
     * @param array $ingresosPorMes Ingresos por mes
     * @param array $gastosPorMes Gastos por mes
     * @return string Tendencia (mejora, deterioro, estable)
     */
    private function determinarTendenciaDetallado($index, $ingresosPorMes, $gastosPorMes)
    {
        $meses = array_keys($ingresosPorMes);
        
        if ($index === 0) {
            return 'estable'; // Primer mes
        }
        
        $mesActual = $meses[$index];
        $mesAnterior = $meses[$index - 1];
        
        $ingresoActual = $ingresosPorMes[$mesActual] ?? 0;
        $ingresoAnterior = $ingresosPorMes[$mesAnterior] ?? 0;
        
        $gastoActual = $gastosPorMes[$mesActual] ?? 0;
        $gastoAnterior = $gastosPorMes[$mesAnterior] ?? 0;
        
        // Calcular ratios
        $ratioIngreso = $ingresoAnterior > 0 ? ($ingresoActual / $ingresoAnterior) * 100 : 0;
        $ratioGasto = $gastoAnterior > 0 ? ($gastoActual / $gastoAnterior) * 100 : 0;
        
        // Determinar tendencia
        if ($ratioIngreso > 105 && $ratioGasto < 95) {
            return 'mejora'; // Ingresos ↑, Gastos ↓
        } elseif ($ratioIngreso < 95 && $ratioGasto > 105) {
            return 'deterioro'; // Ingresos ↓, Gastos ↑
        } else {
            return 'estable';
        }
    }

    /**
     * DETERMINAR TENDENCIA DEL MES (MODO BÁSICO)
     * 
     * Compara ingresos con mes anterior
     *
     * @param int $indice Índice del mes
     * @param array $ingresos Array de ingresos
     * @param float $gastoTotal Gasto total del mes (no usado actualmente)
     * @return string Tendencia (mejora, deterioro, estable)
     */
    private function determinarTendencia($indice, $ingresos, $gastoTotal)
    {
        if ($indice == 0) {
            return 'estable';
        }

        $ingresoActual = $ingresos[$indice] ?? 0;
        $ingresoAnterior = $ingresos[$indice - 1] ?? 0;

        if ($ingresoActual > $ingresoAnterior) {
            return 'mejora';
        } elseif ($ingresoActual < $ingresoAnterior) {
            return 'deterioro';
        } else {
            return 'estable';
        }
    }

    /**
     * PREPARAR DATOS PARA ANÁLISIS DE TENDENCIAS
     * 
     * Genera análisis detallado de tendencias por mes
     * incluyendo estados financieros y recomendaciones
     *
     * @param array $datosAnalisis Datos de análisis
     * @return array Datos de tendencias por mes y análisis general
     */
    public function prepararDatosTendencias($datosAnalisis)
    {
        $ingresos = $datosAnalisis['ingresos'] ?? [];
        $gastosFijos = $datosAnalisis['gastosFijos'] ?? [];
        $gastosDinamicos = $datosAnalisis['gastosDinamicos'] ?? [];

        if (empty($ingresos)) {
            return [];
        }

        $tendencias = [];
        $totalMeses = count($ingresos);

        // 📊 1. Pre-calcular gastos totales (UNA SOLA VEZ)
        $gastosTotales = [];
        for ($i = 0; $i < $totalMeses; $i++) {
            $gastosTotales[$i] = ($gastosFijos[$i] ?? 0) + ($gastosDinamicos[$i] ?? 0);
        }

        // 📊 2. Pre-calcular saldos (UNA SOLA VEZ)
        $saldos = [];
        for ($i = 0; $i < $totalMeses; $i++) {
            $saldos[$i] = $ingresos[$i] - $gastosTotales[$i];
        }

        // 📊 3. Procesar cada mes
        for ($i = 0; $i < $totalMeses; $i++) {
            $ingreso = $ingresos[$i] ?? 0;
            $gastoTotal = $gastosTotales[$i] ?? 0;
            $saldo = $saldos[$i] ?? 0;

            // Calcular tendencias (UNA SOLA VEZ cada una)
            $tendenciaIngreso = $this->calcularTendenciaMes($i, $ingresos);
            $tendenciaGastoTotal = $this->calcularTendenciaMes($i, $gastosTotales);

            $tendencias[] = [
                'mes'                 => $i + 1,
                'ingreso'             => $ingreso,
                'gasto_total'         => $gastoTotal,
                'saldo'               => $saldo,
                'tendencia_ingreso'   => $tendenciaIngreso,
                'tendencia_gasto'     => $tendenciaGastoTotal,
                'estado_financiero'   => $this->determinarEstadoFinanciero($saldo, $ingreso),
                'recomendacion'       => $this->generarRecomendacionTendencia(
                    $tendenciaIngreso, 
                    $tendenciaGastoTotal, 
                    $saldo
                )
            ];
        }

        // 📊 4. Calcular tendencias generales
        $tendencias['analisis_general'] = [
            'tendencia_ingresos' => $this->calcularTendenciaGeneral($ingresos),
            'tendencia_gastos'   => $this->calcularTendenciaGeneral($gastosTotales),
            'tendencia_saldos'   => $this->calcularTendenciaGeneral($saldos)
        ];

        return $tendencias;
    }

    /**
     * CALCULAR TENDENCIA POR MES
     * 
     * Determina si un valor aumentó o disminuyó respecto al mes anterior
     * usando umbrales porcentuales configurables
     *
     * @param int $indice Índice del mes
     * @param array $datos Array de datos
     * @return string Tendencia (creciente_fuerte, creciente, estable, decreciente, decreciente_fuerte)
     */
    private function calcularTendenciaMes($indice, $datos)
    {
        // Validación básica
        if ($indice == 0 || count($datos) < 2) {
            return 'estable';
        }

        $actual = $datos[$indice] ?? 0;
        $anterior = $datos[$indice - 1] ?? 0;
        
        if ($anterior == 0) {
            return 'estable';
        }
        
        $porcentajeCambio = (($actual - $anterior) / $anterior) * 100;

        // UMBRALES DEFINITIVOS
        if ($porcentajeCambio > 10) {
            return 'creciente_fuerte';
        } elseif ($porcentajeCambio > 3) {
            return 'creciente';
        } elseif ($porcentajeCambio < -10) {
            return 'decreciente_fuerte';
        } elseif ($porcentajeCambio < -3) {
            return 'decreciente';
        } else {
            return 'estable';
        }
    }

    /**
     * CALCULAR TENDENCIA GENERAL
     * 
     * Analiza la tendencia en todo el período usando
     * el cambio porcentual entre el primer y último valor
     *
     * @param array $datos Array de datos
     * @return string Tendencia general
     */
    private function calcularTendenciaGeneral($datos)
    {
        if (count($datos) < 2) {
            return 'insuficientes_datos';
        }

        // Calcular porcentaje total de cambio
        $inicio = $datos[0];
        $fin = $datos[count($datos) - 1];
        
        if ($inicio == 0) return 'estable';
        
        $porcentajeCambioTotal = (($fin - $inicio) / $inicio) * 100;

        // Ajustar por número de meses
        $porcentajePorMes = $porcentajeCambioTotal / (count($datos) - 1);

        if ($porcentajePorMes > 10) {
            return 'crecimiento_fuerte';
        } elseif ($porcentajePorMes > 5) {
            return 'crecimiento_moderado';
        } elseif ($porcentajePorMes > 2) {
            return 'crecimiento_leve';
        } elseif ($porcentajePorMes < -10) {
            return 'decrecimiento_fuerte';
        } elseif ($porcentajePorMes < -5) {
            return 'decrecimiento_moderado';
        } elseif ($porcentajePorMes < -2) {
            return 'decrecimiento_leve';
        } else {
            return 'estable';
        }
    }

    /**
     * DETERMINAR ESTADO FINANCIERO
     * 
     * Clasifica la salud financiera basada en el porcentaje de ahorro
     *
     * @param float $saldo Saldo del mes
     * @param float $ingreso Ingreso del mes
     * @return string Estado financiero
     */
    private function determinarEstadoFinanciero($saldo, $ingreso)
    {
        if ($ingreso == 0) return 'sin_datos';

        $porcentajeAhorro = ($saldo / $ingreso) * 100;

        if ($porcentajeAhorro >= 20) {
            return 'excelente';
        } elseif ($porcentajeAhorro >= 10) {
            return 'bueno';
        } elseif ($porcentajeAhorro >= 0) {
            return 'regular';
        } elseif ($porcentajeAhorro >= -10) {
            return 'preocupante';
        } else {
            return 'critico';
        }
    }

    /**
     * GENERAR RECOMENDACIÓN POR TENDENCIA
     * 
     * Crea recomendaciones personalizadas basadas en las tendencias
     * de ingresos y gastos
     *
     * @param string $tendenciaIngreso Tendencia de ingresos
     * @param string $tendenciaGasto Tendencia de gastos
     * @param float $saldo Saldo del mes
     * @return string Recomendación
     */
    private function generarRecomendacionTendencia($tendenciaIngreso, $tendenciaGasto, $saldo)
    {
        // Caso crítico: gastos creciendo fuerte mientras ingresos no
        if ($tendenciaGasto === 'creciente_fuerte' && $tendenciaIngreso !== 'creciente_fuerte') {
            return '📈 Tus gastos crecen más rápido que tus ingresos. Revisa tus gastos variables.';
        }
        
        // Caso preocupante: ingresos decreciendo fuerte
        if ($tendenciaIngreso === 'decreciente_fuerte') {
            return '📉 Tus ingresos están disminuyendo fuertemente. Busca fuentes alternativas de ingreso.';
        }
        
        // Caso atención: ingresos bajan, gastos suben
        if ($tendenciaIngreso === 'decreciente' && $tendenciaGasto === 'creciente') {
            return '⚠️ Atención: Ingresos bajando y gastos subiendo. Revisa tu presupuesto.';
        }
        
        // Caso ingresos decrecientes
        if ($tendenciaIngreso === 'decreciente') {
            return '📉 Tus ingresos están disminuyendo. Considera ajustar tus gastos.';
        }
        
        // Caso gastos crecientes
        if ($tendenciaGasto === 'creciente') {
            return '📈 Tus gastos están aumentando. Mantén un control estricto.';
        }
        
        // Default: todo estable
        return '✅ Situación financiera estable. Mantén el control de tus gastos.';
    }

    /**
     * GENERAR DATOS SIMULADOS PARA MODO BÁSICO
     * 
     * Crea datos de ejemplo para categorías cuando no hay datos reales
     *
     * @return array Datos simulados de categorías
     */
    public function generarDatosSimuladosCategorias()
    {
        $categorias = [
            'Alimentación'    => rand(10, 25),
            'Transporte'      => rand(8, 20),
            'Entretenimiento' => rand(5, 15),
            'Salud'           => rand(3, 10),
            'Educación'       => rand(2, 8),
            'Vivienda'        => rand(15, 30),
            'Otros'           => rand(5, 12)
        ];

        $total = array_sum($categorias);
        return $this->procesarTablaCategorias($categorias, $total);
    }

    /**
     * GENERAR REPORTE COMPLETO DE ANÁLISIS
     * 
     * Combina todos los análisis en un solo array:
     * - Frecuencias
     * - Comparativo
     * - Tendencias
     * - Resumen ejecutivo
     *
     * @param array $datosAnalisis Datos de análisis
     * @return array Reporte completo
     */
    public function generarReporteCompleto($datosAnalisis)
    {
        return [
            'frecuencias' => $this->generarAnalisisFrecuencias($datosAnalisis),
            'comparativo' => $this->prepararDatosComparativos($datosAnalisis),
            'tendencias'  => $this->prepararDatosTendencias($datosAnalisis),
            'resumen'     => $this->generarResumenEjecutivo($datosAnalisis)
        ];
    }

    /**
     * GENERAR RESUMEN EJECUTIVO
     * 
     * Crea un resumen ejecutivo con puntos clave y recomendaciones
     * basado en los indicadores financieros principales
     *
     * @param array $datosAnalisis Datos de análisis
     * @return array Resumen ejecutivo con puntos y recomendación principal
     */
    private function generarResumenEjecutivo($datosAnalisis)
    {
        $promIngreso = $datosAnalisis['promIngreso'] ?? 0;
        $promFijos = $datosAnalisis['promFijos'] ?? 0;
        $promDinamicos = $datosAnalisis['promDinamicos'] ?? 0;
        $saldo = $datosAnalisis['saldo'] ?? 0;
        $porcFijos = $datosAnalisis['porcFijos'] ?? 0;
        $cvIngresos = $datosAnalisis['cvIngresos'] ?? 0;

        $resumen = [
            'titulo' => 'Resumen Ejecutivo Financiero',
            'puntos' => []
        ];

        // ✅ SALUD FINANCIERA GENERAL
        if ($saldo > 0) {
            $resumen['puntos'][] = [
                'icono' => '✅',
                'texto' => "Salud financiera positiva con saldo mensual disponible de $" . number_format($saldo, 2)
            ];
        } else {
            $resumen['puntos'][] = [
                'icono' => '⚠️',
                'texto' => "Atención: Gastos superan ingresos por $" . number_format(abs($saldo), 2) . " mensuales"
            ];
        }

        // 📊 ESTABILIDAD DE INGRESOS
        if ($cvIngresos < 15) {
            $resumen['puntos'][] = [
                'icono' => '📊',
                'texto' => "Ingresos estables (variación del " . number_format($cvIngresos, 1) . "%)"
            ];
        } else {
            $resumen['puntos'][] = [
                'icono' => '📈',
                'texto' => "Ingresos variables (variación del " . number_format($cvIngresos, 1) . "%) - considera diversificar fuentes"
            ];
        }

        // 📋 COMPOSICIÓN DE GASTOS
        if ($porcFijos < 40) {
            $resumen['puntos'][] = [
                'icono' => '💰',
                'texto' => "Estructura de gastos saludable (" . number_format($porcFijos, 1) . "% en gastos fijos)"
            ];
        } else {
            $resumen['puntos'][] = [
                'icono' => '📋',
                'texto' => "Gastos fijos altos (" . number_format($porcFijos, 1) . "%) - oportunidad de optimización"
            ];
        }

        // 🎯 PROYECCIÓN ANUAL
        $saldoAnual = $datosAnalisis['saldoAnual'] ?? 0;
        if ($saldoAnual > 0) {
            $resumen['puntos'][] = [
                'icono' => '🎯',
                'texto' => "Proyección anual positiva: potencial de ahorro de $" . number_format($saldoAnual, 2)
            ];
        }

        // 💡 RECOMENDACIÓN PRINCIPAL
        if ($saldo > 0 && $porcFijos < 40) {
            $resumen['recomendacion_principal'] = "Excelente gestión financiera. Continúa con el mismo plan y considera invertir el excedente.";
        } elseif ($saldo < 0) {
            $resumen['recomendacion_principal'] = "Prioridad: Reducir gastos, especialmente variables. Revisa categorías con mayor porcentaje.";
        } else {
            $resumen['recomendacion_principal'] = "Situación estable. Enfócate en optimizar gastos fijos y crear fondo de emergencia.";
        }

        return $resumen;
    }
}