<?php
// app/Services/Finanzas/ReporteService.php (NUEVO - Reemplaza al anterior)

namespace App\Services\Finanzas;

use App\Services\Finanzas\Reportes\FrecuenciaReporte;
use App\Services\Finanzas\Reportes\ComparativoReporte;
use App\Services\Finanzas\Reportes\TendenciasReporte;
use App\Services\Finanzas\Reportes\ResumenReporte;

class ReporteService
{
    protected $frecuenciaReporte;
    protected $comparativoReporte;
    protected $tendenciasReporte;
    protected $resumenReporte;

    public function __construct(
        FrecuenciaReporte $frecuenciaReporte,
        ComparativoReporte $comparativoReporte,
        TendenciasReporte $tendenciasReporte,
        ResumenReporte $resumenReporte
    ) {
        $this->frecuenciaReporte = $frecuenciaReporte;
        $this->comparativoReporte = $comparativoReporte;
        $this->tendenciasReporte = $tendenciasReporte;
        $this->resumenReporte = $resumenReporte;
    }

    /**
     * GENERAR ANÁLISIS DE FRECUENCIAS
     */
    public function generarAnalisisFrecuencias($sessionData): ?array
    {
        return $this->frecuenciaReporte->generar($sessionData);
    }

    /**
     * GENERAR FRECUENCIA POR CATEGORÍAS
     */
    public function generarFrecuenciaCategorias($datosAnalisis): array
    {
        return $this->frecuenciaReporte->generarFrecuenciaCategorias($datosAnalisis);
    }

    /**
     * PREPARAR DATOS COMPARATIVOS
     */
    public function prepararDatosComparativos($datosAnalisis): array
    {
        return $this->comparativoReporte->preparar($datosAnalisis);
    }

    /**
     * PREPARAR DATOS DE TENDENCIAS
     */
    public function prepararDatosTendencias($datosAnalisis): array
    {
        return $this->tendenciasReporte->preparar($datosAnalisis);
    }

    /**
     * GENERAR RESUMEN EJECUTIVO
     */
    public function generarResumenEjecutivo($datosAnalisis): array
    {
        return $this->resumenReporte->generar($datosAnalisis);
    }

    /**
     * GENERAR DATOS SIMULADOS
     */
    public function generarDatosSimuladosCategorias(): array
    {
        return $this->resumenReporte->generarDatosSimuladosCategorias();
    }

    /**
     * GENERAR REPORTE COMPLETO
     */
    public function generarReporteCompleto($datosAnalisis): array
    {
        return [
            'frecuencias' => $this->generarAnalisisFrecuencias($datosAnalisis),
            'comparativo' => $this->prepararDatosComparativos($datosAnalisis),
            'tendencias'  => $this->prepararDatosTendencias($datosAnalisis),
            'resumen'     => $this->generarResumenEjecutivo($datosAnalisis)
        ];
    }
}