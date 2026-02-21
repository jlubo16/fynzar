<?php
// app/Services/Finanzas/AnalisisDetalladoService.php

namespace App\Services\Finanzas;

use App\Models\Category;
use App\Models\Subcategory;

class AnalisisDetalladoService
{
    /**
     * Servicio para análisis rápido (reutilizado para procesamiento básico)
     *
     * @var AnalisisRapidoService
     */
    protected $analisisRapidoService;

    /**
     * Servicio para cálculos estadísticos avanzados
     *
     * @var EstadisticaService
     */
    protected $estadisticaService;

    /**
     * Constructor del servicio
     * Inyecta las dependencias necesarias para análisis detallado
     *
     * @param AnalisisRapidoService $analisisRapidoService
     * @param EstadisticaService $estadisticaService
     */
    public function __construct(
        AnalisisRapidoService $analisisRapidoService,
        EstadisticaService $estadisticaService
    ) {
        $this->analisisRapidoService = $analisisRapidoService;
        $this->estadisticaService = $estadisticaService;
    }

    /**
     * PROCESAR DATOS DETALLADOS
     * 
     * Método principal que procesa ingresos y gastos detallados,
     * identifica patrones, clasifica gastos fijos vs dinámicos,
     * y genera análisis completos por categorías y meses.
     *
     * @param array $ingresosDetallados Array con ingresos por mes (formato: mes, monto)
     * @param array $gastosDetallados Array con gastos individuales (fecha, categoria_id, monto)
     * @return array Resultados del análisis detallado
     * @throws \Exception Si no hay datos válidos
     */
    public function procesarDatosDetallados($ingresosDetallados, $gastosDetallados)
    {
        // 📊 PASO 1: PROCESAR INGRESOS POR MES
        // Agrupar ingresos por mes y sumar montos
        $ingresosPorMes = $this->procesarIngresosPorMes($ingresosDetallados);

        // 🏷️ PASO 2: PROCESAR GASTOS POR MES Y CLASIFICAR
        // Identificar patrones y clasificar gastos como fijos o dinámicos
        $gastosProcesados = $this->procesarGastosDetallados($gastosDetallados);
        
        $gastosPorMes = $gastosProcesados['gastosPorMes'];
        $gastosFijosPorMes = $gastosProcesados['gastosFijosPorMes'];
        $gastosDinamicosPorMes = $gastosProcesados['gastosDinamicosPorMes'];
        $gastosPorCategoriaDetalle = $gastosProcesados['gastosPorCategoriaDetalle'];

        // 🔄 PASO 3: SINCRONIZAR MESES
        // Obtener todos los meses únicos entre ingresos y gastos
        $todosMeses = $this->obtenerTodosLosMeses($ingresosPorMes, $gastosPorMes);
        
        if (empty($todosMeses)) {
            throw new \Exception('No hay datos válidos para procesar.');
        }

        // 📅 PASO 4: CREAR ARRAYS PARA ANÁLISIS BÁSICO
        // Convertir datos agrupados a arrays indexados por mes
        $arraysAnalisis = $this->crearArraysParaAnalisisBasico(
            $todosMeses, 
            $ingresosPorMes, 
            $gastosFijosPorMes, 
            $gastosDinamicosPorMes
        );

        // 📈 PASO 5: OBTENER ANÁLISIS BÁSICO
        // Reutilizar servicio de análisis rápido para estadísticas generales
        $resultados = $this->analisisRapidoService->procesarDatosBasicos(
            $arraysAnalisis['ingresosArray'], 
            $arraysAnalisis['gastosFijosArray'], 
            $arraysAnalisis['gastosDinamicosArray']
        );

        // 🔧 PASO 6: AGREGAR DATOS ESPECÍFICOS DEL MODO DETALLADO
        // Enriquecer resultados con datos detallados
        $resultados = $this->enriquecerResultadosDetallados(
            $resultados,
            $ingresosDetallados,
            $gastosDetallados,
            $ingresosPorMes,
            $gastosPorMes,
            $gastosFijosPorMes,
            $gastosDinamicosPorMes,
            $todosMeses
        );

        // 🏷️ PASO 7: ANÁLISIS POR CATEGORÍAS
        // Generar análisis detallado por categorías de gastos
        $resultados['analisis_categorias'] = $this->analizarGastosPorCategoria($gastosDetallados);

        return $resultados;
    }

    /**
     * Procesar ingresos agrupándolos por mes
     *
     * @param array $ingresosDetallados
     * @return array Ingresos agrupados por mes
     */
    private function procesarIngresosPorMes($ingresosDetallados)
    {
        $ingresosPorMes = [];
        
        foreach ($ingresosDetallados as $ingreso) {
            $mes = $ingreso['mes']; // Formato: YYYY-MM
            $monto = (float) $ingreso['monto'];

            if (!isset($ingresosPorMes[$mes])) {
                $ingresosPorMes[$mes] = 0;
            }
            $ingresosPorMes[$mes] += $monto;
        }
        
        return $ingresosPorMes;
    }

    /**
     * Procesar gastos detallados: clasificar y agrupar
     *
     * @param array $gastosDetallados
     * @return array Gastos procesados y clasificados
     */
    private function procesarGastosDetallados($gastosDetallados)
    {
        // Inicializar arrays
        $gastosPorMes = [];
        $gastosFijosPorMes = [];
        $gastosDinamicosPorMes = [];
        $gastosPorCategoriaDetalle = [];

        // Identificar patrones para determinar qué gastos son fijos
        $patronesGastos = $this->identificarPatronesGastos($gastosDetallados);

        foreach ($gastosDetallados as $gasto) {
            $mes = date('Y-m', strtotime($gasto['fecha']));
            $monto = (float) $gasto['monto'];
            $categoriaId = $gasto['categoria_id'];

            // Inicializar arrays por mes si no existen
            if (!isset($gastosPorMes[$mes])) {
                $gastosPorMes[$mes] = 0;
                $gastosFijosPorMes[$mes] = 0;
                $gastosDinamicosPorMes[$mes] = 0;
            }

            // Determinar si es gasto fijo o dinámico basado en patrones
            $esFijo = $this->esGastoFijo($gasto, $patronesGastos);

            // Clasificar gasto
            if ($esFijo) {
                $gastosFijosPorMes[$mes] += $monto;
            } else {
                $gastosDinamicosPorMes[$mes] += $monto;
            }

            $gastosPorMes[$mes] += $monto;

            // Análisis por categoría
            $this->acumularGastosPorCategoria(
                $gastosPorCategoriaDetalle, 
                $categoriaId, 
                $monto, 
                $esFijo
            );
        }

        return [
            'gastosPorMes' => $gastosPorMes,
            'gastosFijosPorMes' => $gastosFijosPorMes,
            'gastosDinamicosPorMes' => $gastosDinamicosPorMes,
            'gastosPorCategoriaDetalle' => $gastosPorCategoriaDetalle
        ];
    }

    /**
     * Acumular gastos por categoría
     *
     * @param array &$gastosPorCategoriaDetalle Array de gastos por categoría (por referencia)
     * @param int $categoriaId ID de la categoría
     * @param float $monto Monto del gasto
     * @param bool $esFijo Si es gasto fijo o dinámico
     */
    private function acumularGastosPorCategoria(&$gastosPorCategoriaDetalle, $categoriaId, $monto, $esFijo)
    {
        if (!isset($gastosPorCategoriaDetalle[$categoriaId])) {
            $gastosPorCategoriaDetalle[$categoriaId] = [
                'total' => 0,
                'fijo' => 0,
                'dinamico' => 0,
                'categoria' => Category::find($categoriaId)
            ];
        }

        $gastosPorCategoriaDetalle[$categoriaId]['total'] += $monto;
        
        if ($esFijo) {
            $gastosPorCategoriaDetalle[$categoriaId]['fijo'] += $monto;
        } else {
            $gastosPorCategoriaDetalle[$categoriaId]['dinamico'] += $monto;
        }
    }

    /**
     * Obtener todos los meses únicos de ingresos y gastos
     *
     * @param array $ingresosPorMes
     * @param array $gastosPorMes
     * @return array Array ordenado de meses únicos
     */
    private function obtenerTodosLosMeses($ingresosPorMes, $gastosPorMes)
    {
        $todosMeses = array_unique(array_merge(
            array_keys($ingresosPorMes),
            array_keys($gastosPorMes)
        ));
        sort($todosMeses);
        
        return $todosMeses;
    }

    /**
     * Crear arrays indexados para análisis básico
     *
     * @param array $todosMeses Lista de meses
     * @param array $ingresosPorMes Ingresos agrupados
     * @param array $gastosFijosPorMes Gastos fijos agrupados
     * @param array $gastosDinamicosPorMes Gastos dinámicos agrupados
     * @return array Arrays listos para análisis
     */
    private function crearArraysParaAnalisisBasico($todosMeses, $ingresosPorMes, $gastosFijosPorMes, $gastosDinamicosPorMes)
    {
        $ingresosArray = [];
        $gastosFijosArray = [];
        $gastosDinamicosArray = [];

        foreach ($todosMeses as $mes) {
            $ingresosArray[] = $ingresosPorMes[$mes] ?? 0;
            $gastosFijosArray[] = $gastosFijosPorMes[$mes] ?? 0;
            $gastosDinamicosArray[] = $gastosDinamicosPorMes[$mes] ?? 0;
        }

        return [
            'ingresosArray' => $ingresosArray,
            'gastosFijosArray' => $gastosFijosArray,
            'gastosDinamicosArray' => $gastosDinamicosArray
        ];
    }

    /**
     * Enriquecer resultados con datos específicos del modo detallado
     *
     * @param array $resultados Resultados base
     * @param array $ingresosDetallados Ingresos originales
     * @param array $gastosDetallados Gastos originales
     * @param array $ingresosPorMes Ingresos por mes
     * @param array $gastosPorMes Gastos por mes
     * @param array $gastosFijosPorMes Gastos fijos por mes
     * @param array $gastosDinamicosPorMes Gastos dinámicos por mes
     * @param array $todosMeses Todos los meses
     * @return array Resultados enriquecidos
     */
    private function enriquecerResultadosDetallados(
        $resultados, 
        $ingresosDetallados, 
        $gastosDetallados, 
        $ingresosPorMes, 
        $gastosPorMes, 
        $gastosFijosPorMes, 
        $gastosDinamicosPorMes, 
        $todosMeses
    ) {
        $resultados['modo_analisis'] = 'detallado';
        $resultados['categorias'] = Category::with('subcategories')->forExpenses()->get();
        $resultados['ingresos_detallados'] = $ingresosDetallados;
        $resultados['gastos_detallados'] = $gastosDetallados;
        $resultados['gastos_por_mes'] = $gastosPorMes;
        $resultados['ingresos_por_mes'] = $ingresosPorMes;
        $resultados['gastos_fijos_por_mes'] = $gastosFijosPorMes;
        $resultados['gastos_dinamicos_por_mes'] = $gastosDinamicosPorMes;
        $resultados['meses_analizados'] = $todosMeses;

        return $resultados;
    }

    /**
     * IDENTIFICAR PATRONES DE GASTOS
     * 
     * Analiza los gastos para encontrar patrones que ayuden
     * a determinar qué gastos son fijos (recurrentes)
     *
     * @param array $gastosDetallados Lista de gastos
     * @return array Patrones identificados
     */
    private function identificarPatronesGastos($gastosDetallados)
    {
        $patrones = [];

        foreach ($gastosDetallados as $gasto) {
            $categoriaId = $gasto['categoria_id'];
            $descripcion = strtolower($gasto['descripcion'] ?? '');
            $monto = (float) $gasto['monto'];

            // Clave única para este tipo de gasto
            $clave = $categoriaId . '_' . round($monto, -2); // Redondear a centenas para agrupar

            if (!isset($patrones[$clave])) {
                $patrones[$clave] = [
                    'count' => 0,
                    'total_monto' => 0,
                    'meses' => [],
                    'gasto' => $gasto
                ];
            }

            $mes = date('Y-m', strtotime($gasto['fecha']));
            $patrones[$clave]['count']++;
            $patrones[$clave]['total_monto'] += $monto;
            $patrones[$clave]['meses'][] = $mes;
        }

        return $patrones;
    }

    /**
     * DETERMINAR SI UN GASTO ES FIJO
     * 
     * Evalúa si un gasto debe clasificarse como fijo basado en:
     * - Patrones de recurrencia (meses consecutivos)
     * - Categorías típicamente fijas
     *
     * @param array $gasto Gasto a evaluar
     * @param array $patronesGastos Patrones identificados
     * @return bool True si es gasto fijo, False si es dinámico
     */
    private function esGastoFijo($gasto, $patronesGastos)
    {
        $categoriaId = $gasto['categoria_id'];
        $monto = (float) $gasto['monto'];
        $clave = $categoriaId . '_' . round($monto, -2);

        // Si aparece en múltiples meses, probablemente es fijo
        if (isset($patronesGastos[$clave]) && $patronesGastos[$clave]['count'] >= 2) {
            // Verificar si aparece en meses consecutivos
            $meses = $patronesGastos[$clave]['meses'];
            sort($meses);

            // Si hay al menos 2 meses consecutivos, considerar fijo
            $consecutivos = 0;
            for ($i = 1; $i < count($meses); $i++) {
                $mesActual = strtotime($meses[$i]);
                $mesAnterior = strtotime($meses[$i-1]);

                // Calcular diferencia en meses
                $diferencia = (date('Y', $mesActual) - date('Y', $mesAnterior)) * 12 +
                    (date('m', $mesActual) - date('m', $mesAnterior));

                if ($diferencia == 1) {
                    $consecutivos++;
                }
            }

            return $consecutivos >= 1;
        }

        // Categorías que típicamente son gastos fijos
        $categoriasFijas = ['Vivienda', 'Servicios', 'Préstamos', 'Seguros', 'Educación (matrículas)'];
        $categoria = Category::find($categoriaId);

        if ($categoria && in_array($categoria->name, $categoriasFijas)) {
            return true;
        }

        return false;
    }

    /**
     * ANALIZAR GASTOS POR CATEGORÍA
     * 
     * Genera un análisis detallado de gastos agrupados por categoría,
     * incluyendo totales, porcentajes y gastos individuales.
     *
     * @param array $gastosDetallados Lista de gastos
     * @return array Análisis por categoría (ordenado por porcentaje)
     */
    private function analizarGastosPorCategoria($gastosDetallados)
    {
        $gastosPorCategoria = [];

        foreach ($gastosDetallados as $gasto) {
            $categoriaId = $gasto['categoria_id'];
            $monto = (float) $gasto['monto'];

            if (!isset($gastosPorCategoria[$categoriaId])) {
                $gastosPorCategoria[$categoriaId] = [
                    'total' => 0,
                    'categoria' => Category::find($categoriaId),
                    'gastos' => []
                ];
            }

            $gastosPorCategoria[$categoriaId]['total'] += $monto;
            $gastosPorCategoria[$categoriaId]['gastos'][] = [
                'monto' => $monto,
                'fecha' => $gasto['fecha'],
                'descripcion' => $gasto['descripcion'] ?? ''
            ];
        }

        // Calcular porcentajes sobre el total
        $totalGastos = array_sum(array_column($gastosPorCategoria, 'total'));

        foreach ($gastosPorCategoria as &$categoriaData) {
            $categoriaData['porcentaje'] = $totalGastos > 0 ? 
                ($categoriaData['total'] / $totalGastos) * 100 : 0;
        }

        // Ordenar por porcentaje descendente
        uasort($gastosPorCategoria, function($a, $b) {
            return $b['porcentaje'] <=> $a['porcentaje'];
        });

        return $gastosPorCategoria;
    }

    /**
     * ANALIZAR POR CATEGORÍAS (VERSIÓN SIMPLIFICADA)
     * 
     * Versión simplificada del análisis por categorías que genera
     * un array indexado con categorías, totales, porcentajes e iconos.
     *
     * @param array $gastosPorCategoria Gastos agrupados por categoría
     * @return array Análisis simplificado por categorías
     */
    public function analizarPorCategorias($gastosPorCategoria)
    {
        $totalGastos = array_sum($gastosPorCategoria);
        $categorias = Category::whereIn('id', array_keys($gastosPorCategoria))->get();

        $analisis = [];

        foreach ($categorias as $categoria) {
            $totalCategoria = $gastosPorCategoria[$categoria->id] ?? 0;
            $porcentaje = $totalGastos > 0 ? ($totalCategoria / $totalGastos) * 100 : 0;

            $analisis[] = [
                'categoria' => $categoria,
                'total' => $totalCategoria,
                'porcentaje' => round($porcentaje, 2),
                'icono' => $categoria->icon ?? 'bi-tag',
                'color' => $categoria->color ?? '#6c757d'
            ];
        }

        // Ordenar por porcentaje descendente
        usort($analisis, function($a, $b) {
            return $b['porcentaje'] <=> $a['porcentaje'];
        });

        return $analisis;
    }

    /**
     * ANALIZAR CATEGORÍAS (VERSIÓN COMPLETA)
     * 
     * Versión completa del análisis por categorías que incluye
     * subcategorías y recomendaciones personalizadas.
     *
     * @param array $gastosPorCategoria Gastos agrupados por categoría
     * @return array Análisis completo con recomendaciones
     */
    public function analizarCategorias($gastosPorCategoria)
    {
        $totalGastos = array_sum(array_column($gastosPorCategoria, 'total'));
        $analisis = [];

        foreach ($gastosPorCategoria as $categoriaId => $datos) {
            $porcentaje = $totalGastos > 0 ? ($datos['total'] / $totalGastos) * 100 : 0;

            $analisis[] = [
                'categoria' => $datos['categoria'],
                'total' => $datos['total'],
                'porcentaje' => $porcentaje,
                'subcategorias' => $datos['subcategorias'] ?? [],
                'recomendacion' => $this->generarRecomendacionCategoria(
                    $datos['categoria']->name, 
                    $porcentaje, 
                    $datos['total']
                )
            ];
        }

        // Ordenar por porcentaje descendente
        usort($analisis, function($a, $b) {
            return $b['porcentaje'] <=> $a['porcentaje'];
        });

        return $analisis;
    }

    /**
     * GENERAR RECOMENDACIÓN POR CATEGORÍA
     * 
     * Genera recomendaciones personalizadas basadas en el porcentaje
     * de gasto en cada categoría comparado con umbrales saludables.
     *
     * @param string $categoria Nombre de la categoría
     * @param float $porcentaje Porcentaje del gasto total
     * @param float $monto Monto total gastado
     * @return string Recomendación personalizada
     */
    private function generarRecomendacionCategoria($categoria, $porcentaje, $monto)
    {
        $umbrales = [
            'Vivienda' => 30,
            'Transporte' => 15,
            'Alimentación' => 15,
            'Entretenimiento' => 10,
            'Salud' => 10,
            'Educación' => 10
        ];

        $categoriaBase = explode(' ', $categoria)[0]; // Tomar primera palabra

        if (isset($umbrales[$categoriaBase]) && $porcentaje > $umbrales[$categoriaBase]) {
            return "⚠️ Gastas más del {$umbrales[$categoriaBase]}% recomendado en {$categoria}. Considera reducir estos gastos.";
        }

        return "✅ Gastos en {$categoria} dentro de rangos saludables.";
    }

    /**
     * ANALIZAR TENDENCIAS MENSUALES
     * 
     * Genera un análisis de tendencias mensuales comparando
     * ingresos, gastos y saldos a lo largo del tiempo.
     *
     * @param array $ingresosPorMes Ingresos agrupados por mes
     * @param array $gastosPorMes Gastos agrupados por mes
     * @return array Tendencias mensuales con análisis
     */
    public function analizarTendenciasMensuales($ingresosPorMes, $gastosPorMes)
    {
        $tendencias = [
            'meses' => [],
            'ingresos' => [],
            'gastos' => [],
            'saldos' => [],
            'analisis' => []
        ];

        // Ordenar los meses cronológicamente
        ksort($ingresosPorMes);
        ksort($gastosPorMes);

        foreach ($ingresosPorMes as $mes => $ingreso) {
            $gasto = $gastosPorMes[$mes] ?? 0;
            $saldo = $ingreso - $gasto;

            $tendencias['meses'][] = $this->formatearMes($mes);
            $tendencias['ingresos'][] = $ingreso;
            $tendencias['gastos'][] = $gasto;
            $tendencias['saldos'][] = $saldo;
        }

        // Análisis de tendencias
        $tendencias['analisis'] = $this->calcularTendencias(
            $tendencias['ingresos'], 
            $tendencias['gastos'], 
            $tendencias['saldos']
        );

        return $tendencias;
    }

    /**
     * Formatear mes para visualización
     *
     * @param string $mes Mes en formato YYYY-MM
     * @return string Mes formateado (Ej: "Ene 2024")
     */
    private function formatearMes($mes)
    {
        return date('M Y', strtotime($mes . '-01'));
    }

    /**
     * CALCULAR TENDENCIAS
     * 
     * Calcula tendencias lineales para ingresos, gastos y saldos,
     * y genera interpretaciones de cada tendencia.
     *
     * @param array $ingresos Array de ingresos
     * @param array $gastos Array de gastos
     * @param array $saldos Array de saldos
     * @return array Análisis de tendencias con interpretaciones
     */
    private function calcularTendencias($ingresos, $gastos, $saldos)
    {
        $analisis = [];

        // Tendencia de ingresos
        $tendenciaIngresos = $this->estadisticaService->calcularTendenciaLineal($ingresos);
        $analisis['ingresos'] = [
            'tendencia' => $tendenciaIngresos,
            'interpretacion' => $this->interpretarTendencia($tendenciaIngresos, 'ingresos')
        ];

        // Tendencia de gastos
        $tendenciaGastos = $this->estadisticaService->calcularTendenciaLineal($gastos);
        $analisis['gastos'] = [
            'tendencia' => $tendenciaGastos,
            'interpretacion' => $this->interpretarTendencia($tendenciaGastos, 'gastos')
        ];

        // Tendencia de saldos
        $tendenciaSaldos = $this->estadisticaService->calcularTendenciaLineal($saldos);
        $analisis['saldos'] = [
            'tendencia' => $tendenciaSaldos,
            'interpretacion' => $this->interpretarTendencia($tendenciaSaldos, 'saldos')
        ];

        return $analisis;
    }

    /**
     * INTERPRETAR TENDENCIA
     * 
     * Genera un mensaje interpretativo basado en la pendiente
     * de la tendencia y el tipo de métrica.
     *
     * @param float $pendiente Pendiente de la regresión lineal
     * @param string $tipo Tipo de métrica (ingresos, gastos, saldos)
     * @return string Mensaje interpretativo
     */
    private function interpretarTendencia($pendiente, $tipo)
    {
        $umbral = 50; // Umbral para considerar tendencia estable

        if (abs($pendiente) < $umbral) {
            $estado = 'estable';
        } elseif ($pendiente > 0) {
            $estado = 'creciente';
        } else {
            $estado = 'decreciente';
        }

        $interpretaciones = [
            'ingresos' => [
                'creciente'  => '📈 Tus ingresos muestran una tendencia positiva',
                'decreciente' => '📉 Tus ingresos muestran una tendencia a la baja',
                'estable'     => '📊 Tus ingresos se mantienen estables'
            ],
            'gastos' => [
                'creciente'  => '⚠️ Tus gastos están aumentando con el tiempo',
                'decreciente' => '✅ Tus gastos muestran una tendencia a la baja',
                'estable'     => '📊 Tus gastos se mantienen estables'
            ],
            'saldos' => [
                'creciente'  => '💰 Tu capacidad de ahorro está mejorando',
                'decreciente' => '🔻 Tu saldo disponible está disminuyendo',
                'estable'     => '⚖️ Tu saldo se mantiene constante'
            ]
        ];

        return $interpretaciones[$tipo][$estado] . " (tendencia: " . number_format($pendiente, 2) . ")";
    }
}