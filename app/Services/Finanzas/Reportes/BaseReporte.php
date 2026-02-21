<?php
// app/Services/Finanzas/Reportes/BaseReporte.php

namespace App\Services\Finanzas\Reportes;

use App\Services\Finanzas\EstadisticaService;
use App\Services\Finanzas\CSVImportService;

abstract class BaseReporte
{
    protected $estadisticaService;
    protected $csvImportService;

    public function __construct(
        EstadisticaService $estadisticaService,
        CSVImportService $csvImportService
    ) {
        $this->estadisticaService = $estadisticaService;
        $this->csvImportService = $csvImportService;
    }

    /**
     * Formatear mes para mostrar (Ej: "2024-01" → "Ene 2024")
     */
    protected function formatearMes($mesString): string
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
     * Validar que hay datos de análisis
     */
    protected function validarDatos($datosAnalisis): bool
    {
        return $datosAnalisis !== null && !empty($datosAnalisis);
    }
}