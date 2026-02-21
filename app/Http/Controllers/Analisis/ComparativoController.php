<?php
// app/Http/Controllers/Analisis/ComparativoController.php

namespace App\Http\Controllers\Analisis;

use App\Http\Controllers\Controller;
use App\Services\Finanzas\ReporteService;
use Illuminate\Http\Request;

class ComparativoController extends Controller
{
    /**
     * Servicio para generar reportes y análisis comparativos
     *
     * @var ReporteService
     */
    protected $reporteService;

    /**
     * Constructor del controlador
     * Inyecta el servicio de reportes necesario para el análisis comparativo
     *
     * @param ReporteService $reporteService
     */
    public function __construct(ReporteService $reporteService)
    {
        $this->reporteService = $reporteService;
    }

    /**
     * MOSTRAR ANÁLISIS COMPARATIVO
     * 
     * Este método genera una vista comparativa basada en los datos
     * de análisis almacenados previamente en sesión.
     * Compara diferentes períodos y métricas financieras.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // 🔍 OBTENER DATOS DE SESIÓN
        // Recupera los datos del último análisis realizado
        $sessionData = session('analisis_data');

        // VALIDACIÓN DE DATOS EXISTENTES
        // Si no hay datos en sesión, redirigir al análisis principal
        if (!$sessionData) {
            return redirect()->route('analisis.index')
                ->with('error', 'Primero realiza un análisis para ver el análisis comparativo');
        }

        // PREPARAR DATOS COMPARATIVOS
        // Utiliza el servicio de reportes para transformar los datos
        // en un formato adecuado para comparaciones
        $datosComparativos = $this->reporteService->prepararDatosComparativos($sessionData);

        // CONTAR MESES ANALIZADOS
        // Calcula la cantidad de meses con datos de ingresos
        $mesesAnalizados = count($sessionData['ingresos'] ?? []);

        // RENDERIZAR VISTA COMPARATIVA
        // Retorna la vista con todos los datos necesarios
        return view('analisis.comparativo', [
            'datosAnalisis'    => $sessionData,      // Datos originales del análisis
            'comparativos'     => $datosComparativos, // Datos procesados para comparación
            'mesesAnalizados'  => $mesesAnalizados,   // Total de meses analizados
        ]);
    }
}