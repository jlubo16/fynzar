<?php
// app/Services/Finanzas/Reportes/ComparativoReporte.php

namespace App\Services\Finanzas\Reportes;

class ComparativoReporte extends BaseReporte
{
    /**
     * PREPARAR DATOS PARA ANÁLISIS COMPARATIVO
     */
    public function preparar($datosAnalisis): array
    {
        $modo = $datosAnalisis['modo_analisis'] ?? 'basico';
        
        if ($modo === 'detallado') {
            return $this->prepararDetallado($datosAnalisis);
        }
        return $this->prepararBasico($datosAnalisis);
    }

    /**
     * Comparativo para modo detallado
     */
    private function prepararDetallado($datosAnalisis): array
    {
        $ingresosPorMes = $datosAnalisis['ingresos_por_mes'] ?? [];
        $gastosPorMes = $datosAnalisis['gastos_por_mes'] ?? [];
        
        if (empty($ingresosPorMes)) return [];

        $comparativos = [];
        $meses = array_keys($ingresosPorMes);
        
        foreach ($meses as $index => $mes) {
            $ingreso = $ingresosPorMes[$mes] ?? 0;
            $gastoTotal = $gastosPorMes[$mes] ?? 0;
            $saldo = $ingreso - $gastoTotal;
            $porcTotalGastos = $ingreso > 0 ? ($gastoTotal / $ingreso) * 100 : 0;

            $comparativos[] = [
                'mes'                => $this->formatearMes($mes),
                'mes_original'       => $mes,
                'ingreso'            => $ingreso,
                'gasto_fijo'         => 0,
                'gasto_dinamico'     => $gastoTotal,
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
     * Comparativo para modo básico
     */
    private function prepararBasico($datosAnalisis): array
    {
        $ingresos = $datosAnalisis['ingresos'] ?? [];
        $gastosFijos = $datosAnalisis['gastosFijos'] ?? [];
        $gastosDinamicos = $datosAnalisis['gastosDinamicos'] ?? [];

        if (empty($ingresos)) return [];

        $comparativos = [];
        $totalMeses = count($ingresos);

        for ($i = 0; $i < $totalMeses; $i++) {
            $ingreso = $ingresos[$i] ?? 0;
            $gastoFijo = $gastosFijos[$i] ?? 0;
            $gastoDinamico = $gastosDinamicos[$i] ?? 0;
            $gastoTotal = $gastoFijo + $gastoDinamico;
            $saldo = $ingreso - $gastoTotal;

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
                'tendencia'         => $this->determinarTendencia($i, $ingresos),
            ];
        }

        return $comparativos;
    }

    /**
     * Determinar tendencia para modo detallado
     */
    private function determinarTendenciaDetallado($index, $ingresosPorMes, $gastosPorMes): string
    {
        $meses = array_keys($ingresosPorMes);
        if ($index === 0) return 'estable';
        
        $mesActual = $meses[$index];
        $mesAnterior = $meses[$index - 1];
        
        $ingresoActual = $ingresosPorMes[$mesActual] ?? 0;
        $ingresoAnterior = $ingresosPorMes[$mesAnterior] ?? 0;
        $gastoActual = $gastosPorMes[$mesActual] ?? 0;
        $gastoAnterior = $gastosPorMes[$mesAnterior] ?? 0;
        
        $ratioIngreso = $ingresoAnterior > 0 ? ($ingresoActual / $ingresoAnterior) * 100 : 0;
        $ratioGasto = $gastoAnterior > 0 ? ($gastoActual / $gastoAnterior) * 100 : 0;
        
        if ($ratioIngreso > 105 && $ratioGasto < 95) return 'mejora';
        if ($ratioIngreso < 95 && $ratioGasto > 105) return 'deterioro';
        return 'estable';
    }

    /**
     * Determinar tendencia para modo básico
     */
    private function determinarTendencia($indice, $ingresos): string
    {
        if ($indice == 0) return 'estable';

        $ingresoActual = $ingresos[$indice] ?? 0;
        $ingresoAnterior = $ingresos[$indice - 1] ?? 0;

        if ($ingresoActual > $ingresoAnterior) return 'mejora';
        if ($ingresoActual < $ingresoAnterior) return 'deterioro';
        return 'estable';
    }
}