<?php
// app/Http/Controllers/Analisis/TendenciasController.php

namespace App\Http\Controllers\Analisis;

use App\Http\Controllers\Controller;
use App\Services\Finanzas\ReporteService;
use Illuminate\Http\Request;

class TendenciasController extends Controller
{
    /**
     * Servicio para generar reportes y análisis de tendencias
     * Permite visualizar patrones y comportamientos a lo largo del tiempo
     *
     * @var ReporteService
     */
    protected $reporteService;

    /**
     * Constructor del controlador
     * Inyecta el servicio de reportes necesario para el análisis de tendencias
     *
     * @param ReporteService $reporteService
     */
    public function __construct(ReporteService $reporteService)
    {
        $this->reporteService = $reporteService;
    }

    /**
     * MOSTRAR ANÁLISIS DE TENDENCIAS
     * 
     * Este método genera visualizaciones de tendencias basadas en los datos
     * de análisis almacenados en sesión. Las tendencias muestran la evolución
     * de ingresos y gastos a lo largo del tiempo.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // 🔍 PASO 1: OBTENER DATOS DE SESIÓN
        // Recupera los datos del último análisis realizado
        $sessionData = session('analisis_data');

        // ⚠️ PASO 2: VALIDAR DATOS EXISTENTES
        // Verificar si hay datos de análisis en sesión
        if (!$sessionData) {
            return redirect()->route('analisis.index')
                ->with('error', 'Primero realiza un análisis para ver las tendencias');
        }

        // 📈 PASO 3: PREPARAR DATOS DE TENDENCIAS
        // Utiliza el servicio de reportes para transformar los datos
        // en un formato adecuado para análisis de tendencias
        $datosTendencias = $this->reporteService->prepararDatosTendencias($sessionData);

        // 📅 PASO 4: CONTAR MESES ANALIZADOS
        // Calcula la cantidad de meses con datos de ingresos
        $mesesAnalizados = count($sessionData['ingresos'] ?? []);

        // 🚀 PASO 5: RENDERIZAR VISTA DE TENDENCIAS
        // Retorna la vista con todos los datos necesarios
        return view('analisis.tendencias', [
            'datosAnalisis'    => $sessionData,      // Datos originales del análisis
            'tendencias'       => $datosTendencias,  // Datos procesados para tendencias
            'mesesAnalizados'  => $mesesAnalizados,  // Total de meses analizados
        ]);
    }
}