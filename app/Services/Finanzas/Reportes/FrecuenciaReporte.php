<?php
// app/Services/Finanzas/Reportes/FrecuenciaReporte.php

namespace App\Services\Finanzas\Reportes;

class FrecuenciaReporte extends BaseReporte
{
    /**
     * GENERAR ANÁLISIS DE FRECUENCIAS
     */
    public function generar($sessionData): ?array
    {
        if (!$this->validarDatos($sessionData)) {
            return null;
        }

        // 🔥 ÚNICO CAMBIO: Determinar qué gastos usar según el modo
        $gastosParaFrecuencia = ($sessionData['modo_analisis'] ?? 'basico') === 'detallado'
            ? array_values($sessionData['gastos_por_mes'] ?? [])  // Modo detallado: gastos reales por mes
            : ($sessionData['gastosDinamicos'] ?? []);            // Modo básico: gastosDinamicos

        // 1. Tablas existentes (AHORA USA LOS GASTOS CORRECTOS)
        $tablaFrecuencia = $this->estadisticaService->generarTablaFrecuenciaCompleta($gastosParaFrecuencia);

        $tablaCategorias = $this->generarFrecuenciaCategorias($sessionData);
        $histogramaData = $this->estadisticaService->prepararDatosHistograma($gastosParaFrecuencia);

        // 2. Calcular métricas (AHORA USA LOS GASTOS CORRECTOS)
        $metricasCalculadas = $this->estadisticaService->calcularMetricasFrecuencias(
            $gastosParaFrecuencia,
            $tablaFrecuencia
        );

        // 3. Calcular medidas estadísticas (esto ya está bien, usa gastos_detallados)
        $gastosIndividuales = $this->csvImportService->extraerMontosIndividuales(
            $sessionData['gastos_detallados'] ?? []
        );

        $medidasEstadisticas = $this->estadisticaService->calcularMedidas($gastosIndividuales);

        // 4. Preparar datos para JavaScript
        $categoriasData = $this->prepararDatosCategoriasJS($tablaCategorias);

        $mesesIngresos = array_keys($sessionData['ingresos_por_mes'] ?? []);
        $mesesGastos = array_keys($sessionData['gastos_por_mes'] ?? []);
        $todosMeses = array_unique(array_merge($mesesIngresos, $mesesGastos));
        $totalMesesReales = count($todosMeses);

        return [
            'meses_analizados' => $totalMesesReales,
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
     */
    public function generarFrecuenciaCategorias($datosAnalisis): array
    {
        // 🥇 PRIORIDAD 1: Gastos detallados (modo detallado CSV)
        if (isset($datosAnalisis['gastos_detallados']) && !empty($datosAnalisis['gastos_detallados'])) {
            return $this->generarDesdeGastosDetallados($datosAnalisis['gastos_detallados']);
        }

        // 🥈 PRIORIDAD 2: Análisis de categorías (modo detallado manual)
        if (isset($datosAnalisis['analisis_categorias']) && !empty($datosAnalisis['analisis_categorias'])) {
            return $this->generarDesdeAnalisisCategorias($datosAnalisis['analisis_categorias']);
        }

        // 🥉 PRIORIDAD 3: Gastos por categoría
        if (isset($datosAnalisis['gastos_por_categoria']) && !empty($datosAnalisis['gastos_por_categoria'])) {
            return $this->generarDesdeGastosCategoria($datosAnalisis['gastos_por_categoria']);
        }

        // ℹ️ PRIORIDAD 4: Modo básico - mensaje informativo
        return [
            'tipo'    => 'modo_basico',
            'mensaje' => 'Para ver frecuencia por categorías, usa el Modo Detallado',
            'datos'   => []
        ];
    }

    /**
     * Generar frecuencia desde gastos detallados
     */
    private function generarDesdeGastosDetallados($gastosDetallados): array
    {
        $frecuencias = [];
        foreach ($gastosDetallados as $gasto) {
            $categoria = $gasto['categoria'] ?? 'Sin categoría';
            if (empty($categoria)) $categoria = 'Sin categoría';
            
            if (!isset($frecuencias[$categoria])) {
                $frecuencias[$categoria] = 0;
            }
            $frecuencias[$categoria]++;
        }

        arsort($frecuencias);
        return $this->procesarTablaCategorias($frecuencias, count($gastosDetallados));
    }

    /**
     * Generar frecuencia desde análisis de categorías
     */
    private function generarDesdeAnalisisCategorias($analisisCategorias): array
    {
        $total = 0;
        $frecuencias = [];

        foreach ($analisisCategorias as $analisis) {
            if (isset($analisis['categoria'])) {
                $nombre = $analisis['categoria']->name ?? $analisis['categoria'];
                $totalGasto = $analisis['total'] ?? 0;
                $frecuencia = max(1, intval($totalGasto / 100));
                $frecuencias[$nombre] = ($frecuencias[$nombre] ?? 0) + $frecuencia;
                $total += $frecuencia;
            }
        }

        return $this->procesarTablaCategorias($frecuencias, $total);
    }

    /**
     * Generar frecuencia desde gastos por categoría
     */
    private function generarDesdeGastosCategoria($gastosPorCategoria): array
    {
        $frecuencias = [];
        $total = 0;

        foreach ($gastosPorCategoria as $categoriaId => $datos) {
            if (isset($datos['categoria'])) {
                $nombre = $datos['categoria']->name ?? "Categoría $categoriaId";
                $totalGasto = $datos['total'] ?? 0;
                $frecuencia = max(1, intval($totalGasto / 50));
                $frecuencias[$nombre] = $frecuencia;
                $total += $frecuencia;
            }
        }

        return $this->procesarTablaCategorias($frecuencias, $total);
    }

    /**
     * Procesar tabla de categorías común
     */
    private function procesarTablaCategorias($frecuencias, $total): array
    {
        if ($total == 0) return [];

        $tabla = [];
        $frecuenciaAcumulada = 0;
        $porcentajeAcumulado = 0;

        arsort($frecuencias);

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
     * Preparar datos de categorías para JavaScript
     */
    public function prepararDatosCategoriasJS($tablaCategorias): array
    {
        if (empty($tablaCategorias) || isset($tablaCategorias['tipo'])) {
            return ['labels' => [], 'porcentajes' => []];
        }

        $labels = [];
        $porcentajes = [];

        foreach ($tablaCategorias as $fila) {
            $labels[] = $fila['categoria'];
            $porcentajes[] = floatval(str_replace('%', '', $fila['porcentaje']));
        }

        return ['labels' => $labels, 'porcentajes' => $porcentajes];
    }
}