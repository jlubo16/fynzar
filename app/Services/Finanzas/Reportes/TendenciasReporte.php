<?php
// app/Services/Finanzas/Reportes/TendenciasReporte.php

namespace App\Services\Finanzas\Reportes;

class TendenciasReporte extends BaseReporte
{
    /**
     * PREPARAR DATOS PARA ANÁLISIS DE TENDENCIAS
     */
    public function preparar($datosAnalisis): array
    {
        $ingresos = $datosAnalisis['ingresos'] ?? [];
        $gastosFijos = $datosAnalisis['gastosFijos'] ?? [];
        $gastosDinamicos = $datosAnalisis['gastosDinamicos'] ?? [];

        if (empty($ingresos)) return [];

        $totalMeses = count($ingresos);
        $gastosTotales = $this->calcularGastosTotales($gastosFijos, $gastosDinamicos, $totalMeses);
        $saldos = $this->calcularSaldos($ingresos, $gastosTotales, $totalMeses);

        $tendencias = $this->procesarMeses($ingresos, $gastosTotales, $saldos, $totalMeses);
        $tendencias['analisis_general'] = $this->calcularTendenciasGenerales($ingresos, $gastosTotales, $saldos);

        return $tendencias;
    }

    /**
     * Calcular gastos totales por mes
     */
    private function calcularGastosTotales($gastosFijos, $gastosDinamicos, $totalMeses): array
    {
        $gastosTotales = [];
        for ($i = 0; $i < $totalMeses; $i++) {
            $gastosTotales[$i] = ($gastosFijos[$i] ?? 0) + ($gastosDinamicos[$i] ?? 0);
        }
        return $gastosTotales;
    }

    /**
     * Calcular saldos por mes
     */
    private function calcularSaldos($ingresos, $gastosTotales, $totalMeses): array
    {
        $saldos = [];
        for ($i = 0; $i < $totalMeses; $i++) {
            $saldos[$i] = $ingresos[$i] - $gastosTotales[$i];
        }
        return $saldos;
    }

    /**
     * Procesar cada mes
     */
    private function procesarMeses($ingresos, $gastosTotales, $saldos, $totalMeses): array
    {
        $tendencias = [];

        for ($i = 0; $i < $totalMeses; $i++) {
            $tendenciaIngreso = $this->calcularTendenciaMes($i, $ingresos);
            $tendenciaGasto = $this->calcularTendenciaMes($i, $gastosTotales);

            $tendencias[] = [
                'mes'                 => $i + 1,
                'ingreso'             => $ingresos[$i] ?? 0,
                'gasto_total'         => $gastosTotales[$i] ?? 0,
                'saldo'               => $saldos[$i] ?? 0,
                'tendencia_ingreso'   => $tendenciaIngreso,
                'tendencia_gasto'     => $tendenciaGasto,
                'estado_financiero'   => $this->determinarEstadoFinanciero($saldos[$i] ?? 0, $ingresos[$i] ?? 0),
                'recomendacion'       => $this->generarRecomendacion($tendenciaIngreso, $tendenciaGasto, $saldos[$i] ?? 0)
            ];
        }

        return $tendencias;
    }

    /**
     * Calcular tendencia por mes
     */
    private function calcularTendenciaMes($indice, $datos): string
    {
        if ($indice == 0 || count($datos) < 2) return 'estable';

        $actual = $datos[$indice] ?? 0;
        $anterior = $datos[$indice - 1] ?? 0;
        
        if ($anterior == 0) return 'estable';
        
        $porcentajeCambio = (($actual - $anterior) / $anterior) * 100;

        if ($porcentajeCambio > 10) return 'creciente_fuerte';
        if ($porcentajeCambio > 3) return 'creciente';
        if ($porcentajeCambio < -10) return 'decreciente_fuerte';
        if ($porcentajeCambio < -3) return 'decreciente';
        return 'estable';
    }

    /**
     * Calcular tendencias generales
     */
    private function calcularTendenciasGenerales($ingresos, $gastosTotales, $saldos): array
    {
        return [
            'tendencia_ingresos' => $this->calcularTendenciaGeneral($ingresos),
            'tendencia_gastos'   => $this->calcularTendenciaGeneral($gastosTotales),
            'tendencia_saldos'   => $this->calcularTendenciaGeneral($saldos)
        ];
    }

    /**
     * Calcular tendencia general
     */
    private function calcularTendenciaGeneral($datos): string
    {
        if (count($datos) < 2) return 'insuficientes_datos';

        $inicio = $datos[0];
        $fin = $datos[count($datos) - 1];
        
        if ($inicio == 0) return 'estable';
        
        $porcentajeCambioTotal = (($fin - $inicio) / $inicio) * 100;
        $porcentajePorMes = $porcentajeCambioTotal / (count($datos) - 1);

        if ($porcentajePorMes > 10) return 'crecimiento_fuerte';
        if ($porcentajePorMes > 5) return 'crecimiento_moderado';
        if ($porcentajePorMes > 2) return 'crecimiento_leve';
        if ($porcentajePorMes < -10) return 'decrecimiento_fuerte';
        if ($porcentajePorMes < -5) return 'decrecimiento_moderado';
        if ($porcentajePorMes < -2) return 'decrecimiento_leve';
        return 'estable';
    }

    /**
     * Determinar estado financiero
     */
    private function determinarEstadoFinanciero($saldo, $ingreso): string
    {
        if ($ingreso == 0) return 'sin_datos';

        $porcentajeAhorro = ($saldo / $ingreso) * 100;

        if ($porcentajeAhorro >= 20) return 'excelente';
        if ($porcentajeAhorro >= 10) return 'bueno';
        if ($porcentajeAhorro >= 0) return 'regular';
        if ($porcentajeAhorro >= -10) return 'preocupante';
        return 'critico';
    }

    /**
     * Generar recomendación
     */
    private function generarRecomendacion($tendenciaIngreso, $tendenciaGasto, $saldo): string
    {
        if ($tendenciaGasto === 'creciente_fuerte' && $tendenciaIngreso !== 'creciente_fuerte') {
            return '📈 Tus gastos crecen más rápido que tus ingresos. Revisa tus gastos variables.';
        }
        
        if ($tendenciaIngreso === 'decreciente_fuerte') {
            return '📉 Tus ingresos están disminuyendo fuertemente. Busca fuentes alternativas de ingreso.';
        }
        
        if ($tendenciaIngreso === 'decreciente' && $tendenciaGasto === 'creciente') {
            return '⚠️ Atención: Ingresos bajando y gastos subiendo. Revisa tu presupuesto.';
        }
        
        if ($tendenciaIngreso === 'decreciente') {
            return '📉 Tus ingresos están disminuyendo. Considera ajustar tus gastos.';
        }
        
        if ($tendenciaGasto === 'creciente') {
            return '📈 Tus gastos están aumentando. Mantén un control estricto.';
        }
        
        return '✅ Situación financiera estable. Mantén el control de tus gastos.';
    }
}