<?php
// app/Services/Finanzas/CSVImportService.php

namespace App\Services\Finanzas;

use App\Models\Category;
use App\Models\Subcategory;
use Exception;

class CSVImportService
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
     * Inyecta las dependencias necesarias para importación CSV
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
     * IMPORTAR ARCHIVO CSV
     * 
     * Método principal que detecta el formato del CSV y redirige
     * al procesador adecuado:
     * - Formato detallado: Transacciones individuales con categorías
     * - Formato simple: Arrays mensuales de ingresos/gastos
     *
     * @param array $contenido Líneas del archivo CSV
     * @return array Datos procesados y mensaje de resultado
     */
    public function importarCSV($contenido)
    {
        // 🔍 PASO 1: VERIFICAR FORMATO DEL ARCHIVO
        $primeraLinea = trim($contenido[0]);
        
        // ✅ VERSIÓN CORREGIDA - Insensible a mayúsculas/acentos
        $primeraLineaLower = mb_strtolower($this->quitarAcentos($primeraLinea), 'UTF-8');
        
        // Verificar si tiene las columnas clave del formato detallado
        $tieneTipo = str_contains($primeraLineaLower, 'tipo');
        $tieneCategoria = str_contains($primeraLineaLower, 'categoria');
        $tieneSubcategoria = str_contains($primeraLineaLower, 'subcategoria');
        
        $esFormatoDetallado = $tieneTipo && $tieneCategoria && $tieneSubcategoria;

        // 🎯 PASO 2: REDIRIGIR AL PROCESADOR ADECUADO
        if ($esFormatoDetallado) {
            // FORMATO DETALLADO: Transacciones individuales
            return $this->importarCSVDetallado($contenido);
        } else {
            // FORMATO SIMPLE: Arrays mensuales (3 o 4 columnas)
            return $this->importarCSVSimple($contenido);
        }
    }

    /**
     * QUITAR ACENTOS DE UN STRING
     * 
     * Utilizado para normalizar texto y hacer comparaciones
     * insensibles a acentos y caracteres especiales
     *
     * @param string $string Texto a normalizar
     * @return string Texto sin acentos
     */
    private function quitarAcentos($string)
    {
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        $string = preg_replace('/&([a-zA-Z])(acute|cedil|circ|grave|ring|tilde|uml);/', '$1', $string);
        return html_entity_decode($string);
    }

    /**
     * IMPORTAR CSV EN FORMATO DETALLADO
     * 
     * Procesa archivos CSV con transacciones individuales:
     * Tipo, Mes, Categoría, Subcategoría, Monto, Descripción
     * Genera análisis por categorías y tendencias mensuales
     *
     * @param array $contenido Líneas del archivo CSV
     * @return array Datos procesados y mensaje de éxito
     * @throws Exception Si no hay datos suficientes
     */
    private function importarCSVDetallado($contenido)
    {
        // 📊 PASO 1: INICIALIZAR ESTRUCTURAS DE DATOS
        $ingresosPorMes = [];
        $gastosPorMes = [];
        $gastosDetallados = [];

        // 📝 PASO 2: PROCESAR CADA LÍNEA DEL CSV
        foreach ($contenido as $numeroLinea => $linea) {
            if ($numeroLinea === 0) continue; // Saltar encabezado

            $datos = str_getcsv(trim($linea), ',');

            if (count($datos) < 6) continue;

            $tipo = trim($datos[0]);
            $mes = trim($datos[1]);
            $categoria = trim($datos[2]);
            $subcategoria = trim($datos[3]);
            $monto = floatval(trim($datos[4]));
            $descripcion = trim($datos[5]);

            // 🔄 PROCESAR INGRESOS
            if ($tipo === 'Ingreso') {
                if (!isset($ingresosPorMes[$mes])) {
                    $ingresosPorMes[$mes] = 0;
                }
                $ingresosPorMes[$mes] += $monto;
            } 
            // 🔄 PROCESAR GASTOS
            elseif ($tipo === 'Gasto') {
                if (!isset($gastosPorMes[$mes])) {
                    $gastosPorMes[$mes] = 0;
                }
                $gastosPorMes[$mes] += $monto;

                // Guardar detalle para análisis por categorías
                $gastosDetallados[] = [
                    'fecha' => $mes . '-01',
                    'categoria' => $categoria,
                    'subcategoria' => $subcategoria,
                    'monto' => $monto,
                    'descripcion' => $descripcion
                ];
            }
        }

        // 📅 PASO 3: ORDENAR MESES CRONOLÓGICAMENTE
        ksort($ingresosPorMes);
        ksort($gastosPorMes);
        
        // ✅ FIX CRÍTICO: Manejar caso sin ingresos
        if (empty($ingresosPorMes) && !empty($gastosPorMes)) {
            // Si no hay ingresos pero sí hay gastos, usar meses de gastos
            $mesesGastos = array_keys($gastosPorMes);
            foreach ($mesesGastos as $mes) {
                $ingresosPorMes[$mes] = 0; // Inicializar con 0
            }
            // Re-ordenar
            ksort($ingresosPorMes);
        }
        
        // ✅ FIX CRÍTICO: Manejar caso sin gastos  
        if (empty($gastosPorMes) && !empty($ingresosPorMes)) {
            // Si no hay gastos pero sí hay ingresos, usar meses de ingresos
            $mesesIngresos = array_keys($ingresosPorMes);
            foreach ($mesesIngresos as $mes) {
                $gastosPorMes[$mes] = 0; // Inicializar con 0
            }
            // Re-ordenar
            ksort($gastosPorMes);
        }

        // 📊 PASO 4: PREPARAR ARRAYS PARA PROCESAMIENTO
        $ingresosArray = array_values($ingresosPorMes);
        $gastosArray = array_values($gastosPorMes);

        // ✅ FIX: Validar que hay datos
        if (empty($ingresosArray) && empty($gastosArray)) {
            throw new Exception('No hay datos suficientes para procesar. El CSV debe contener al menos ingresos O gastos.');
        }

        $promedioGastos = count($gastosArray) > 0 ? array_sum($gastosArray) / count($gastosArray) : 0;
    $promedioIngresos = count($ingresosArray) > 0 ? array_sum($ingresosArray) / count($ingresosArray) : 0;

    $desviacionGastos = $this->estadisticaService->calcularDesviacionEstandar($gastosArray);
    $desviacionIngresos = $this->estadisticaService->calcularDesviacionEstandar($ingresosArray);

    $rangoGastos = !empty($gastosArray) ? max($gastosArray) - min($gastosArray) : 0;
    $rangoIngresos = !empty($ingresosArray) ? max($ingresosArray) - min($ingresosArray) : 0;

    $cvGastos = $promedioGastos > 0 ? ($desviacionGastos / $promedioGastos) * 100 : 0;
    $cvIngresos = $promedioIngresos > 0 ? ($desviacionIngresos / $promedioIngresos) * 100 : 0;

        // 📈 PASO 5: ESTIMAR GASTOS FIJOS VS DINÁMICOS
        // Para compatibilidad con análisis rápido, estimamos una distribución 40/60
        $gastosFijosArray = [];
        $gastosDinamicosArray = [];
        foreach ($gastosArray as $gastoTotal) {
            $gastosFijosArray[] = $gastoTotal * 0.4;
            $gastosDinamicosArray[] = $gastoTotal * 0.6;
        }

        // 🏷️ PASO 6: OBTENER CATEGORÍAS
        $categorias = Category::with('subcategories')->forExpenses()->get();

        // 📊 PASO 7: PROCESAR DATOS BÁSICOS
        $datos = $this->analisisRapidoService->procesarDatosBasicos(
            $ingresosArray, 
            $gastosFijosArray, 
            $gastosDinamicosArray
        );

        // 🔥 PASO 8: GENERAR ANÁLISIS ADICIONALES
        $analisisCategorias = $this->analizarCategoriasDesdeDetallados($gastosDetallados);
        $tendenciasMensuales = $this->generarTendenciasMensuales($ingresosPorMes, $gastosPorMes);

        // 🔧 PASO 9: ENRIQUECER DATOS CON INFORMACIÓN DETALLADA
        $datos['modo_analisis'] = 'detallado';
        $datos['categorias'] = $categorias;
        $datos['gastos_detallados'] = $gastosDetallados;
        $datos['ingresos_por_mes'] = $ingresosPorMes;
        $datos['gastos_por_mes'] = $gastosPorMes;
        $datos['analisis_categorias'] = $analisisCategorias;
        $datos['tendencias_mensuales'] = $tendenciasMensuales;

        // 📊 PASO 10: CALCULAR MÉTRICAS ESPECÍFICAS
        $promedioGastos = count($gastosArray) > 0 ? array_sum($gastosArray) / count($gastosArray) : 0;
        $promedioIngresos = count($ingresosArray) > 0 ? array_sum($ingresosArray) / count($ingresosArray) : 0;
        
        $datos['totalGastos'] = array_sum($gastosArray);
        $datos['promGastos'] = $promedioGastos;
        $datos['promIngreso'] = $promedioIngresos;
        $datos['promFijos'] = 0;  // En modo detallado no hay separación
        $datos['promDinamicos'] = $promedioGastos;
        $datos['porcTotalGastos'] = $promedioIngresos > 0 ? 
            min(($promedioGastos / $promedioIngresos) * 100, 100) : 0;
        
        // 📆 PROYECCIONES ANUALES
        $datos['ingresoAnual'] = $promedioIngresos * 12;
        $datos['gastosFijosAnual'] = 0;
        $datos['gastosDinamicosAnual'] = $promedioGastos * 12;
        $datos['saldoAnual'] = ($promedioIngresos - $promedioGastos) * 12;
        
        $mesesIngresos = array_keys($ingresosPorMes);
        $mesesGastos = array_keys($gastosPorMes);
        $totalMesesUnicos = count(array_unique(array_merge($mesesIngresos, $mesesGastos)));

        // 💡 CONCLUSIONES PERSONALIZADAS (CORREGIDAS)
        $datos['conclusion'] = [
            '✅ Se procesaron ' . count($gastosDetallados) . ' gastos en ' . $totalMesesUnicos . ' meses.',
            '📊 Gastas en promedio $' . number_format($promedioGastos, 2) . ' mensuales.',
            '💰 ' . (($promedioIngresos - $promedioGastos) > 0 ? 
                'Superávit mensual: $' . number_format(($promedioIngresos - $promedioGastos), 2) : 
                'Déficit mensual: $' . number_format(abs($promedioIngresos - $promedioGastos), 2))
        ];
        
        // 📊 PASO 11: CALCULAR MEDIANAS
        $montosGastos = array_column($gastosDetallados, 'monto');
        sort($montosGastos);
        $medianaGastosTotales = $this->estadisticaService->calcularMediana($montosGastos);
        $datos['mediana_gastos_totales'] = $medianaGastosTotales;
        $datos['medianaGastos'] = $medianaGastosTotales;

        $datos['desviacion_gastos'] = round($desviacionGastos, 2);
        $datos['rango_gastos'] = round($rangoGastos, 2);
        $datos['cv_gastos'] = round($cvGastos, 2);

        $datos['desviacion_ingresos'] = round($desviacionIngresos, 2);
        $datos['rango_ingresos'] = round($rangoIngresos, 2);
        $datos['cv_ingresos'] = round($cvIngresos, 2);

/* dd([
    'Gastos mensuales (array)'          => $gastosArray,
    'Promedio Gastos'                   => round($promedioGastos, 2),
    'Desviación Estándar Gastos'        => round($desviacionGastos, 2),
    'Rango Gastos'                      => round($rangoGastos, 2),
    'CV Gastos (%)'                     => round($cvGastos, 2),

    '---'                               => '---',

    'Ingresos mensuales (array)'        => $ingresosArray,
    'Promedio Ingresos'                 => round($promedioIngresos, 2),
    'Desviación Estándar Ingresos'      => round($desviacionIngresos, 2),
    'Rango Ingresos'                    => round($rangoIngresos, 2),
    'CV Ingresos (%)'                   => round($cvIngresos, 2),
]); */
        
/* dd(
    'Modo actual en detallado:', $datos['modo_analisis'] ?? 'NO DEFINIDO',
    'desviacion_gastos:', $datos['desviacion_gastos'] ?? 'NO EXISTE',
    'rango_gastos:', $datos['rango_gastos'] ?? 'NO EXISTE',
    'cv_gastos:', $datos['cv_gastos'] ?? 'NO EXISTE'
); */
        // 🏷️ PASO 12: ANALIZAR CATEGORÍA PRINCIPAL
        $this->enriquecerConCategoriaPrincipal($datos, $analisisCategorias, $gastosDetallados);

        // ✅ PASO 13: CREAR MENSAJE DE ÉXITO
        $mensaje = 'Archivo CSV detallado procesado. ' .
            count($ingresosArray) . ' meses de ingresos y ' .
            count($gastosDetallados) . ' gastos analizados.';

            
        return [
            'datos' => $datos,
            'mensaje' => $mensaje,
            'tipo' => 'detallado'
        ];
    }

    /**
     * Enriquecer datos con información de categoría principal
     *
     * @param array &$datos Datos a enriquecer (por referencia)
     * @param array $analisisCategorias Análisis de categorías
     * @param array $gastosDetallados Gastos detallados
     */
    private function enriquecerConCategoriaPrincipal(&$datos, $analisisCategorias, $gastosDetallados)
    {
        if (!empty($analisisCategorias) && isset($analisisCategorias[0])) {
            $categoriaPrincipal = $analisisCategorias[0]['categoria']->name ?? '';
            $gastosCategoriaPrincipal = array_filter($gastosDetallados, function($gasto) use ($categoriaPrincipal) {
                return $gasto['categoria'] === $categoriaPrincipal;
            });
            $montosCategoria = array_column($gastosCategoriaPrincipal, 'monto');
            $medianaCategoria = $this->estadisticaService->calcularMediana($montosCategoria);
            $analisisCategorias[0]['mediana'] = $medianaCategoria;
            $analisisCategorias[0]['mediana_gastos'] = $medianaCategoria;
            $datos['analisis_categorias'] = $analisisCategorias;
        }
    }

    /**
     * PROCESAR DATOS DETALLADOS
     * 
     * Calcula métricas financieras a partir de datos agrupados por mes
     *
     * @param array $ingresosPorMes Ingresos agrupados por mes
     * @param array $gastosPorMes Gastos agrupados por mes
     * @param array $gastosDetallados Gastos individuales
     * @return array Métricas calculadas
     */
    private function procesarDatosDetallados($ingresosPorMes, $gastosPorMes, $gastosDetallados)
    {
        // Calcular promedios
        $promedioIngresos = count($ingresosPorMes) > 0 ? 
            array_sum($ingresosPorMes) / count($ingresosPorMes) : 0;
        
        $promedioGastos = count($gastosPorMes) > 0 ? 
            array_sum($gastosPorMes) / count($gastosPorMes) : 0;
        
        $saldo = $promedioIngresos - $promedioGastos;
        
        // Extraer montos individuales para estadísticas
        $montosGastos = [];
        foreach ($gastosDetallados as $gasto) {
            $montosGastos[] = $gasto['monto'];
        }
        
        // Usar el EstadisticaService para cálculos
        $medianaGastos = $this->estadisticaService->calcularMediana($montosGastos);
        $desviacionGastos = $this->estadisticaService->calcularDesviacionEstandar($montosGastos);
        
        // Preparar array de ingresos para estadísticas
        $ingresosArray = array_values($ingresosPorMes);
        $medianaIngresos = $this->estadisticaService->calcularMediana($ingresosArray);
        $desviacionIngresos = $this->estadisticaService->calcularDesviacionEstandar($ingresosArray);
        
        // ✅ AGREGAR: Calcular coeficientes de variación
        $cvIngresos = $promedioIngresos > 0 ? 
            ($desviacionIngresos / $promedioIngresos) * 100 : 0;
        
        $cvGastos = $promedioGastos > 0 ? 
            ($desviacionGastos / $promedioGastos) * 100 : 0;

        return [
            'promIngreso' => $promedioIngresos,
            'promFijos' => 0,  // En modo detallado no hay separación
            'promDinamicos' => $promedioGastos,  // Todos los gastos se consideran aquí
            'desviacion' => $desviacionGastos,
            'porcFijos' => 0,
            'porcDinamicos' => $promedioIngresos > 0 ? 
                min(($promedioGastos / $promedioIngresos) * 100, 100) : 0,
            'saldo' => $saldo,
            'medianaIngresos' => $medianaIngresos,
            'medianaDinamicos' => $medianaGastos,
            'desviacionIngresos' => $desviacionIngresos,

            // ✅ NUEVO: Agregar coeficientes de variación
            'cvIngresos' => $cvIngresos,
            'cvGastosDinamicos' => $cvGastos,  // Para mantener compatibilidad
            
            // ✅ AGREGAR también rangos (necesarios para la vista)
            'rangoIngresos' => !empty($ingresosArray) ? 
                max($ingresosArray) - min($ingresosArray) : 0,
            'rangoDinamicos' => !empty($montosGastos) ? 
                max($montosGastos) - min($montosGastos) : 0,
            'rangoFijos' => 0,  // En modo detallado no hay gastos fijos separados
        ];
    }

    /**
     * IMPORTAR CSV EN FORMATO SIMPLE
     * 
     * Procesa archivos CSV con formato de 3 o 4 columnas:
     * - 3 columnas: Ingresos, Gastos Fijos, Gastos Dinámicos
     * - 4 columnas: Mes, Ingresos, Gastos Fijos, Gastos Dinámicos
     *
     * @param array $contenido Líneas del archivo CSV
     * @return array Datos procesados y mensaje de éxito
     * @throws Exception Si no se pueden leer los datos
     */
    private function importarCSVSimple($contenido)
    {
        // 📊 PASO 1: INICIALIZAR ARRAYS
        $ingresos = [];
        $gastosFijos = [];
        $gastosDinamicos = [];
        $meses = []; // Para almacenar los meses en formato 4 columnas
        
        // 📝 PASO 2: PROCESAR CADA LÍNEA DEL CSV
        foreach ($contenido as $numeroLinea => $linea) {
            if (trim($linea) === '') continue;
            
            // Detectar separador (; o ,)
            if (strpos($linea, ';') !== false) {
                $datos = str_getcsv($linea, ';');
            } else {
                $datos = str_getcsv($linea, ',');
            }
            
            // Saltar encabezados si existen
            if ($numeroLinea === 0 && !is_numeric(trim($datos[0] ?? '')) && !is_numeric(trim($datos[1] ?? ''))) {
                continue;
            }
            
            // Verificar si es formato de 4 columnas (nuevo formato con meses)
            if (count($datos) >= 4 && 
                is_numeric(trim($datos[1] ?? '')) &&  // Columna 1 es número (ingresos)
                is_numeric(trim($datos[2] ?? '')) &&  // Columna 2 es número (gastos fijos)
                is_numeric(trim($datos[3] ?? ''))) {  // Columna 3 es número (gastos dinámicos)
                
                // 👈 NUEVO FORMATO: Mes, Ingresos, Gastos Fijos, Gastos Dinámicos
                $meses[] = trim($datos[0]); // Guardar el mes (puede ser texto o número)
                $ingresos[] = floatval(trim($datos[1]));
                $gastosFijos[] = floatval(trim($datos[2]));
                $gastosDinamicos[] = floatval(trim($datos[3]));
                
            } 
            // Verificar si es formato de 3 columnas (formato antiguo)
            elseif (count($datos) >= 3 &&
                    is_numeric(trim($datos[0] ?? '')) &&
                    is_numeric(trim($datos[1] ?? '')) &&
                    is_numeric(trim($datos[2] ?? ''))) {
                
                // 👈 FORMATO ANTIGUO: Ingresos, Gastos Fijos, Gastos Dinámicos
                $meses[] = 'Mes ' . (count($meses) + 1); // Asignar mes genérico
                $ingresos[] = floatval(trim($datos[0]));
                $gastosFijos[] = floatval(trim($datos[1]));
                $gastosDinamicos[] = floatval(trim($datos[2]));
            }
        }

        // ⚠️ PASO 3: VALIDAR DATOS
        if (empty($ingresos)) {
            throw new Exception('No se pudieron leer los datos. Verifica que el archivo CSV tenga el formato correcto (3 o 4 columnas).');
        }

        // 🏷️ PASO 4: OBTENER CATEGORÍAS
        $categorias = Category::with('subcategories')->forExpenses()->get();
        
        // 📊 PASO 5: PROCESAR DATOS BÁSICOS
        $datos = $this->analisisRapidoService->procesarDatosBasicos(
            $ingresos, 
            $gastosFijos, 
            $gastosDinamicos
        );
        
        // 🔧 PASO 6: ENRIQUECER DATOS
        $datos['categorias'] = $categorias;
        $datos['modo_analisis'] = 'basico';
        $datos['meses'] = $meses; // Agregar meses al resultado
        
        // ✅ PASO 7: CREAR MENSAJE DE ÉXITO
        return [
            'datos' => $datos,
            'mensaje' => 'Archivo CSV procesado correctamente. Se analizaron ' . count($ingresos) . ' registros.',
            'tipo' => 'simple'
        ];
    }

    /**
     * ANALIZAR CATEGORÍAS DESDE GASTOS DETALLADOS
     * 
     * Genera un análisis de gastos agrupados por categoría
     * a partir de los gastos individuales del CSV detallado
     *
     * @param array $gastosDetallados Lista de gastos individuales
     * @return array Análisis por categoría con totales y porcentajes
     */
    public function analizarCategoriasDesdeDetallados($gastosDetallados)
    {
        if (empty($gastosDetallados)) {
            return [];
        }

        $totalesPorCategoria = [];

        foreach ($gastosDetallados as $gasto) {
            $categoria = $gasto['categoria'];
            $monto = $gasto['monto'];

            if (!isset($totalesPorCategoria[$categoria])) {
                $totalesPorCategoria[$categoria] = 0;
            }

            $totalesPorCategoria[$categoria] += $monto;
        }

        // Calcular total general
        $totalGeneral = array_sum($totalesPorCategoria);

        // Crear array de análisis
        $analisis = [];

        foreach ($totalesPorCategoria as $categoria => $total) {
            $porcentaje = $totalGeneral > 0 ? ($total / $totalGeneral) * 100 : 0;

            // Buscar o crear objeto categoría simulado
            $categoriaObj = (object) [
                'name' => $categoria,
                'id' => null,
                'icon' => 'bi-tag',
                'color' => '#6c757d'
            ];

            $analisis[] = [
                'categoria' => $categoriaObj,
                'total' => $total,
                'porcentaje' => $porcentaje
            ];
        }

        // Ordenar por total descendente
        usort($analisis, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        return $analisis;
    }

    /**
     * GENERAR TENDENCIAS MENSUALES
     * 
     * Crea un array con tendencias mensuales de ingresos, gastos y saldos
     * Incluye análisis de variación porcentual en el período
     *
     * @param array $ingresosPorMes Ingresos agrupados por mes
     * @param array $gastosPorMes Gastos agrupados por mes
     * @return array|null Datos de tendencias o null si no hay ingresos
     */
    public function generarTendenciasMensuales($ingresosPorMes, $gastosPorMes)
    {
        if (empty($ingresosPorMes)) {
            return null;
        }

        // Ordenar meses
        ksort($ingresosPorMes);
        ksort($gastosPorMes);

        $tendencias = [
            'meses' => [],
            'ingresos' => [],
            'gastos' => [],
            'saldos' => [],
            'analisis' => []
        ];

        foreach ($ingresosPorMes as $mes => $ingreso) {
            $gasto = $gastosPorMes[$mes] ?? 0;
            $saldo = $ingreso - $gasto;

            $tendencias['meses'][] = date('M Y', strtotime($mes . '-01'));
            $tendencias['ingresos'][] = $ingreso;
            $tendencias['gastos'][] = $gasto;
            $tendencias['saldos'][] = $saldo;
        }

        // Análisis simple de tendencias
        if (count($tendencias['ingresos']) >= 2) {
            $primero = $tendencias['ingresos'][0];
            $ultimo = $tendencias['ingresos'][count($tendencias['ingresos']) - 1];
            $variacion = (($ultimo - $primero) / $primero) * 100;

            $tendencias['analisis']['ingresos'] = [
                'tendencia' => $variacion > 0 ? 'creciente' : ($variacion < 0 ? 'decreciente' : 'estable'),
                'interpretacion' => $variacion > 0 ?
                    "📈 Tus ingresos aumentaron un " . number_format($variacion, 1) . "% en el período" :
                    ($variacion < 0 ?
                        "📉 Tus ingresos disminuyeron un " . number_format(abs($variacion), 1) . "% en el período" :
                        "📊 Tus ingresos se mantuvieron estables")
            ];
        }

        return $tendencias;
    }

    /**
     * OBTENER ID DE CATEGORÍA (CREAR SI NO EXISTE)
     * 
     * Busca una categoría por nombre o la crea si no existe
     *
     * @param string $nombreCategoria Nombre de la categoría
     * @return int|null ID de la categoría o null si está vacío
     */
    public function obtenerIdCategoria($nombreCategoria)
    {
        // Si la categoría está vacía, retornar null
        if (empty($nombreCategoria)) {
            return null;
        }

        // Buscar o crear categoría
        $categoria = Category::where('name', $nombreCategoria)->first();

        if (!$categoria) {
            $categoria = Category::create([
                'name' => $nombreCategoria,
                'type' => 'expense'
            ]);
        }

        return $categoria->id;
    }

    /**
     * OBTENER ID DE SUBCATEGORÍA
     * 
     * Busca una subcategoría por nombre
     *
     * @param string $nombreSubcategoria Nombre de la subcategoría
     * @return int|null ID de la subcategoría o null si no existe/está vacío
     */
    public function obtenerIdSubcategoria($nombreSubcategoria)
    {
        if (empty($nombreSubcategoria)) return null;

        $subcategoria = Subcategory::where('name', $nombreSubcategoria)->first();
        return $subcategoria ? $subcategoria->id : null;
    }

    /**
     * EXTRAER MONTOS INDIVIDUALES DE GASTOS DETALLADOS
     * 
     * Extrae solo los montos positivos de los gastos para cálculos estadísticos
     *
     * @param array $gastosDetallados Lista de gastos individuales
     * @return array Array de montos positivos
     */
    public function extraerMontosIndividuales($gastosDetallados)
    {
        if (empty($gastosDetallados)) {
            return [];
        }

        $montos = [];

        foreach ($gastosDetallados as $gasto) {
            if (isset($gasto['monto']) && is_numeric($gasto['monto']) && $gasto['monto'] > 0) {
                $montos[] = (float) $gasto['monto'];
            }
        }

        return $montos;
    }
}