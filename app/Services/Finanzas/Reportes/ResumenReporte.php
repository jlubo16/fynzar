<?php
// app/Services/Finanzas/Reportes/ResumenReporte.php

namespace App\Services\Finanzas\Reportes;

class ResumenReporte extends BaseReporte
{
    /**
     * GENERAR RESUMEN EJECUTIVO
     */
    public function generar($datosAnalisis): array
    {
        $promIngreso = $datosAnalisis['promIngreso'] ?? 0;
        $saldo = $datosAnalisis['saldo'] ?? 0;
        $porcFijos = $datosAnalisis['porcFijos'] ?? 0;
        $cvIngresos = $datosAnalisis['cvIngresos'] ?? 0;

        $resumen = [
            'titulo' => 'Resumen Ejecutivo Financiero',
            'puntos' => []
        ];

        // Salud financiera general
        $resumen['puntos'][] = $this->generarPuntoSalud($saldo);

        // Estabilidad de ingresos
        $resumen['puntos'][] = $this->generarPuntoEstabilidad($cvIngresos);

        // Composición de gastos
        $resumen['puntos'][] = $this->generarPuntoGastos($porcFijos);

        // Proyección anual
        $puntoAnual = $this->generarPuntoAnual($datosAnalisis);
        if ($puntoAnual) {
            $resumen['puntos'][] = $puntoAnual;
        }

        // Recomendación principal
        $resumen['recomendacion_principal'] = $this->generarRecomendacionPrincipal(
            $saldo, $porcFijos
        );

        return $resumen;
    }

    /**
     * Generar punto de salud financiera
     */
    private function generarPuntoSalud($saldo): array
    {
        if ($saldo > 0) {
            return [
                'icono' => '✅',
                'texto' => "Salud financiera positiva con saldo mensual disponible de $" . number_format($saldo, 2)
            ];
        }
        
        return [
            'icono' => '⚠️',
            'texto' => "Atención: Gastos superan ingresos por $" . number_format(abs($saldo), 2) . " mensuales"
        ];
    }

    /**
     * Generar punto de estabilidad
     */
    private function generarPuntoEstabilidad($cvIngresos): array
    {
        if ($cvIngresos < 15) {
            return [
                'icono' => '📊',
                'texto' => "Ingresos estables (variación del " . number_format($cvIngresos, 1) . "%)"
            ];
        }
        
        return [
            'icono' => '📈',
            'texto' => "Ingresos variables (variación del " . number_format($cvIngresos, 1) . "%) - considera diversificar fuentes"
        ];
    }

    /**
     * Generar punto de gastos
     */
    private function generarPuntoGastos($porcFijos): array
    {
        if ($porcFijos < 40) {
            return [
                'icono' => '💰',
                'texto' => "Estructura de gastos saludable (" . number_format($porcFijos, 1) . "% en gastos fijos)"
            ];
        }
        
        return [
            'icono' => '📋',
            'texto' => "Gastos fijos altos (" . number_format($porcFijos, 1) . "%) - oportunidad de optimización"
        ];
    }

    /**
     * Generar punto anual
     */
    private function generarPuntoAnual($datosAnalisis): ?array
    {
        $saldoAnual = $datosAnalisis['saldoAnual'] ?? 0;
        
        if ($saldoAnual <= 0) return null;
        
        return [
            'icono' => '🎯',
            'texto' => "Proyección anual positiva: potencial de ahorro de $" . number_format($saldoAnual, 2)
        ];
    }

    /**
     * Generar recomendación principal
     */
    private function generarRecomendacionPrincipal($saldo, $porcFijos): string
    {
        if ($saldo > 0 && $porcFijos < 40) {
            return "Excelente gestión financiera. Continúa con el mismo plan y considera invertir el excedente.";
        }
        
        if ($saldo < 0) {
            return "Prioridad: Reducir gastos, especialmente variables. Revisa categorías con mayor porcentaje.";
        }
        
        return "Situación estable. Enfócate en optimizar gastos fijos y crear fondo de emergencia.";
    }

    /**
     * GENERAR DATOS SIMULADOS PARA MODO BÁSICO
     */
    public function generarDatosSimuladosCategorias(): array
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
        return $this->procesarSimulados($categorias, $total);
    }

    /**
     * Procesar datos simulados
     */
    private function procesarSimulados($categorias, $total): array
    {
        if ($total == 0) return [];

        $tabla = [];
        $frecuenciaAcumulada = 0;
        $porcentajeAcumulado = 0;

        arsort($categorias);

        foreach ($categorias as $categoria => $frecuencia) {
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
}