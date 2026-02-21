<?php
// app/Services/Finanzas/EstadisticaService.php

namespace App\Services\Finanzas;

use App\Models\Category;
use App\Models\Subcategory;

class EstadisticaService
{
    /**
     * CALCULAR MEDIANA DE UN ARRAY
     * 
     * La mediana es el valor central cuando los datos están ordenados
     * - Si el número de elementos es impar: valor del medio
     * - Si es par: promedio de los dos valores centrales
     *
     * @param array $array Array de números
     * @return float Mediana calculada (0 si el array está vacío)
     */
    public function calcularMediana($array)
    {
        // ⚠️ Validación: array vacío
        if (empty($array)) {
            return 0;
        }

        $sorted = $array;
        sort($sorted);
        $count = count($sorted);

        // 📊 Caso par: promedio de los dos valores centrales
        if ($count % 2 == 0) {
            return ($sorted[$count/2 - 1] + $sorted[$count/2]) / 2;
        } 
        // 📊 Caso impar: valor central
        else {
            return $sorted[floor($count/2)];
        }
    }

    /**
     * CALCULAR MODA DE UN ARRAY
     * 
     * La moda es el valor que más se repite en el conjunto
     * Puede haber múltiples modas si varios valores tienen la misma frecuencia máxima
     *
     * @param array $array Array de números
     * @return string|float Moda (valor único) o descripción de múltiples modas
     */
    public function calcularModa($array)
    {
        // ⚠️ Validación: array vacío
        if (empty($array)) {
            return 'N/A';
        }

        // Redondear a 2 decimales para agrupar valores similares
        $frecuencias = array_count_values(array_map(function($val) {
            return number_format($val, 2);
        }, $array));

        // Ordenar por frecuencia descendente
        arsort($frecuencias);
        $maxFrecuencia = reset($frecuencias);
        
        // Obtener todos los valores con la frecuencia máxima
        $modas = array_keys(array_filter($frecuencias, function($freq) use ($maxFrecuencia) {
            return $freq === $maxFrecuencia;
        }));

        // 📊 Si hay una sola moda, devolver el valor
        if (count($modas) === 1) {
            return floatval($modas[0]);
        } 
        // 📊 Si hay múltiples modas, mostrar las primeras 3
        else {
            return 'Múltiples: ' . implode(', ', array_slice($modas, 0, 3));
        }
    }

    /**
     * CALCULAR DESVIACIÓN ESTÁNDAR
     * 
     * Mide la dispersión de los datos respecto a la media
     * Opción para muestra (n-1) o población (n)
     *
     * @param array $array Array de números
     * @param bool $esMuestra True = fórmula de muestra (n-1), False = fórmula de población (n)
     * @return float Desviación estándar (0 si hay menos de 2 elementos)
     */
    public function calcularDesviacionEstandar($array, $esMuestra = true)
    {
        // ⚠️ Validación: necesitamos al menos 2 elementos
        if (empty($array) || count($array) < 2) {
            return 0;
        }

        $n = count($array);
        $media = array_sum($array) / $n;
        $sumaCuadrados = 0;

        // Calcular suma de cuadrados de diferencias
        foreach ($array as $valor) {
            $sumaCuadrados += pow($valor - $media, 2);
        }

        // 📊 Aplicar corrección de Bessel si es muestra
        $denominador = $esMuestra ? ($n - 1) : $n;
        return $denominador > 0 ? sqrt($sumaCuadrados / $denominador) : 0;
    }

    /**
     * CALCULAR MEDIDAS ESTADÍSTICAS COMPLETAS
     * 
     * Genera un conjunto completo de estadísticas descriptivas:
     * - Media, mediana, moda
     * - Desviación estándar, coeficiente de variación
     * - Rango
     *
     * @param array $datos Array de números
     * @return array Array con todas las medidas calculadas
     */
    public function calcularMedidas($datos)
    {
        // ⚠️ Validación: array vacío
        if (empty($datos)) {
            return [
                'media'                 => 0,
                'mediana'               => 0,
                'moda'                  => 'N/A',
                'desviacion'            => 0,
                'coeficiente_variacion' => 0,
                'rango'                 => 0
            ];
        }

        // 📊 PASO 1: ORDENAR DATOS
        sort($datos);
        $n = count($datos);

        // 📊 PASO 2: CALCULAR MEDIA
        $media = array_sum($datos) / $n;

        // 📊 PASO 3: CALCULAR MEDIANA
        $mediana = ($n % 2 == 0)
            ? ($datos[$n/2 - 1] + $datos[$n/2]) / 2
            : $datos[floor($n/2)];

        // 📊 PASO 4: CALCULAR MODA
        $conteos = array_count_values(array_map('strval', $datos));
        arsort($conteos);
        $maxConteo = reset($conteos);
        $modas = array_keys(array_filter($conteos, function($c) use ($maxConteo) {
            return $c === $maxConteo;
        }));

        $moda = count($modas) === 1
            ? floatval($modas[0])
            : 'Múltiples: ' . implode(', ', array_slice($modas, 0, 3));

        // 📊 PASO 5: CALCULAR DESVIACIÓN ESTÁNDAR
        $sumaCuadrados = 0;
        foreach ($datos as $valor) {
            $sumaCuadrados += pow($valor - $media, 2);
        }
        $desviacion = sqrt($sumaCuadrados / ($n > 1 ? $n - 1 : 1));

        // 📊 PASO 6: CALCULAR COEFICIENTE DE VARIACIÓN
        $coeficiente_variacion = $media > 0 ? ($desviacion / $media) * 100 : 0;

        // 📊 PASO 7: CALCULAR RANGO
        $rango = max($datos) - min($datos);

        return [
            'media'                 => $media,
            'mediana'               => $mediana,
            'moda'                  => $moda,
            'desviacion'            => $desviacion,
            'coeficiente_variacion' => $coeficiente_variacion,
            'rango'                 => $rango
        ];
    }

    /**
     * CALCULAR TENDENCIA LINEAL
     * 
     * Calcula la pendiente de la regresión lineal simple
     * Útil para detectar tendencias crecientes o decrecientes
     *
     * @param array $datos Array de valores (Y) con índices como X
     * @return float Pendiente de la tendencia (0 si hay menos de 2 datos)
     */
    public function calcularTendenciaLineal($datos)
    {
        $n = count($datos);
        
        // ⚠️ Validación: necesitamos al menos 2 puntos
        if ($n < 2) {
            return 0;
        }

        $sumX = 0;
        $sumY = 0;
        $sumXY = 0;
        $sumX2 = 0;

        // 📊 Calcular sumatorias para regresión lineal
        for ($i = 0; $i < $n; $i++) {
            $sumX += $i;
            $sumY += $datos[$i];
            $sumXY += $i * $datos[$i];
            $sumX2 += $i * $i;
        }

        // 📊 Fórmula de pendiente: (nΣxy - ΣxΣy) / (nΣx² - (Σx)²)
        $pendiente = ($n * $sumXY - $sumX * $sumY) / ($n * $sumX2 - $sumX * $sumX);
        return $pendiente;
    }

    /**
     * CALCULAR MÉTRICAS PARA FRECUENCIAS
     * 
     * Calcula métricas específicas para análisis de frecuencias:
     * - Rango de variación (min, max, diferencia)
     * - Porcentaje de meses en extremos
     * - Monto ideal (valor más frecuente)
     *
     * @param array $gastosDinamicos Array de gastos dinámicos
     * @param array $tablaFrecuencia Tabla de frecuencia generada
     * @return array Métricas calculadas
     */
    public function calcularMetricasFrecuencias($gastosDinamicos, $tablaFrecuencia)
    {
        // ⚠️ Validación: array vacío
        if (empty($gastosDinamicos)) {
            return [
                'rangoVariacion'    => ['diferencia' => 0],
                'porcentajeExtremos' => 0,
                'montoIdeal'         => 0
            ];
        }

        // 📊 1. RANGO DE VARIACIÓN
        $rangoVariacion = [
            'minimo'     => min($gastosDinamicos),
            'maximo'     => max($gastosDinamicos),
            'diferencia' => max($gastosDinamicos) - min($gastosDinamicos)
        ];

        // 📊 2. PORCENTAJE DE MESES EXTREMOS
        $porcentajeExtremos = 0;
        if (!empty($tablaFrecuencia) && count($tablaFrecuencia) >= 2) {
            $totalMeses = array_sum(array_column($tablaFrecuencia, 'frecuencia'));
            $extremos = 0;

            // Primer intervalo con frecuencia > 0
            foreach ($tablaFrecuencia as $fila) {
                if ($fila['frecuencia'] > 0) {
                    $extremos += $fila['frecuencia'];
                    break;
                }
            }

            // Último intervalo con frecuencia > 0
            for ($i = count($tablaFrecuencia) - 1; $i >= 0; $i--) {
                if ($tablaFrecuencia[$i]['frecuencia'] > 0) {
                    $extremos += $tablaFrecuencia[$i]['frecuencia'];
                    break;
                }
            }

            if ($totalMeses > 0) {
                $porcentajeExtremos = ($extremos / $totalMeses) * 100;
            }
        }

        // 📊 3. MONTO IDEAL (más frecuente)
        $montoIdeal = 0;
        $maxFrecuencia = 0;

        foreach ($tablaFrecuencia as $fila) {
            if ($fila['frecuencia'] > $maxFrecuencia) {
                $maxFrecuencia = $fila['frecuencia'];
                // Extraer número de la marca de clase (ej: "$1,322.70" → 1322.70)
                $montoIdeal = floatval(str_replace(['$', ','], '', $fila['marca_clase']));
            }
        }

        // Si no hay frecuencia clara, usar promedio
        if ($montoIdeal == 0 && !empty($gastosDinamicos)) {
            $montoIdeal = array_sum($gastosDinamicos) / count($gastosDinamicos);
        }

        return [
            'rangoVariacion'     => $rangoVariacion,
            'porcentajeExtremos' => $porcentajeExtremos,
            'montoIdeal'         => $montoIdeal
        ];
    }

    /**
     * GENERAR TABLA DE FRECUENCIA COMPLETA CON INTERVALOS
     * 
     * Crea una tabla de frecuencia con intervalos de clase usando la regla de Sturges:
     * - Número de intervalos = 1 + 3.322 * log10(n)
     * - Calcula frecuencia absoluta, relativa, acumulada y porcentajes
     *
     * @param array $gastosDinamicos Array de gastos dinámicos
     * @return array Tabla de frecuencia con intervalos
     */
public function generarTablaFrecuenciaCompleta($gastosDinamicos)
{
    // Filtrar solo valores positivos y válidos
    $datos = array_filter($gastosDinamicos, fn($v) => is_numeric($v) && $v > 0);
    $datos = array_values($datos);

    if (empty($datos)) {
        return [];
    }

    $min = min($datos);
    $max = max($datos);
    $n = count($datos);

    // Regla de Sturges
    $numIntervalos = min(8, max(3, ceil(1 + 3.322 * log10($n))));

    $rango = $max - $min;
    $amplitud = $rango / $numIntervalos;

    $intervalos = [];
    $frecuenciaAcumulada = 0;

    for ($i = 0; $i < $numIntervalos; $i++) {
        $limiteInferior = $min + ($i * $amplitud);
        $limiteSuperior = $limiteInferior + $amplitud;
        $marcaClase = ($limiteInferior + $limiteSuperior) / 2;

        // CORRECCIÓN: siempre inclusivo con margen flotante
        $frecuencia = 0;
        foreach ($datos as $dato) {
            if ($dato >= $limiteInferior && $dato <= $limiteSuperior + 0.0001) {
                $frecuencia++;
            }
        }

        $frecuenciaAcumulada += $frecuencia;

        $intervalos[] = [
            'intervalo'                         => '$' . number_format($limiteInferior, 0) . ' - $' . number_format($limiteSuperior, 0),
            'marca_clase'                       => number_format($marcaClase, 2),
            'frecuencia'                        => $frecuencia,
            'frecuencia_relativa'               => $n > 0 ? number_format($frecuencia / $n, 3) : 0,
            'frecuencia_acumulada'              => $frecuenciaAcumulada,
            'frecuencia_relativa_acumulada'     => $n > 0 ? number_format($frecuenciaAcumulada / $n, 3) : 0,
            'porcentaje'                        => $n > 0 ? number_format(($frecuencia / $n) * 100, 1) . '%' : '0%'
        ];
    }

    return $intervalos;
}

    /**
     * PREPARAR DATOS PARA HISTOGRAMA
     * 
     * Prepara los datos para visualización en gráfico de histograma
     * Retorna labels (marcas de clase) y frecuencias para Chart.js
     *
     * @param array $gastosDinamicos Array de gastos dinámicos
     * @return array Datos formateados para histograma
     */
    public function prepararDatosHistograma($gastosDinamicos)
    {
        // ⚠️ Validación: array vacío
        if (empty($gastosDinamicos)) return [];

        // Filtrar solo valores positivos
        $datos = array_filter($gastosDinamicos, function($valor) {
            return $valor > 0;
        });

        if (empty($datos)) return [];

        $min = min($datos);
        $max = max($datos);
        $n = count($datos);

        // 📊 Determinar número de intervalos (regla de Sturges)
        $numIntervalos = max(5, min(8, ceil(1 + 3.322 * log10($n))));
        $rango = $max - $min;
        $amplitud = $rango / $numIntervalos;

        $labels = [];
        $data = [];

        // 📊 Generar intervalos para el histograma
        for ($i = 0; $i < $numIntervalos; $i++) {
            $limiteInferior = $min + ($i * $amplitud);
            $limiteSuperior = $limiteInferior + $amplitud;
            $marcaClase = ($limiteInferior + $limiteSuperior) / 2;

            // Contar frecuencia en este intervalo
            $frecuencia = 0;
            foreach ($datos as $dato) {
                if ($dato >= $limiteInferior && $dato < $limiteSuperior) {
                    $frecuencia++;
                }
            }

            $labels[] = '$' . number_format($marcaClase, 0);
            $data[] = $frecuencia;
        }

        return [
            'labels' => $labels,
            'data'   => $data
        ];
    }

    /**
     * CALCULAR EL RANGO DE UN CONJUNTO DE DATOS
     * 
     * Rango = valor máximo - valor mínimo
     * Mide la dispersión total de los datos
     *
     * @param array $datos Array de números
     * @return float Rango calculado (0 si el array está vacío)
     */
    public function calcularRango(array $datos): float
    {
        if (empty($datos)) {
            return 0;
        }
        
        return max($datos) - min($datos);
    }
}