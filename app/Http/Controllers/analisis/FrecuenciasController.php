<?php
// app/Http/Controllers/Analisis/FrecuenciasController.php

namespace App\Http\Controllers\Analisis;

use App\Http\Controllers\Controller;
use App\Services\Finanzas\ReporteService;
use Illuminate\Http\Request;

class FrecuenciasController extends Controller
{
    /**
     * Servicio para generar reportes y análisis de frecuencias
     *
     * @var ReporteService
     */
    protected $reporteService;

    /**
     * Constructor del controlador
     * Inyecta el servicio de reportes necesario para el análisis de frecuencias
     *
     * @param ReporteService $reporteService
     */
    public function __construct(ReporteService $reporteService)
    {
        $this->reporteService = $reporteService;
    }

    /**
     * MOSTRAR ANÁLISIS DE FRECUENCIAS
     * 
     * Este método genera tablas de frecuencia basadas en los datos
     * de análisis almacenados en sesión. Las frecuencias muestran
     * patrones de gastos recurrentes.
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // 🔍 OBTENER DATOS DE SESIÓN
        // Recupera los datos del último análisis realizado
        $sessionData = session('analisis_data');

        // ⚠️ VALIDACIÓN 1: DATOS EXISTENTES
        // Verificar si hay datos de análisis en sesión
        if (!$sessionData) {
            return redirect()->route('analisis.index')
                ->with('error', 'Primero realiza un análisis para ver las tablas de frecuencia');
        }

        // ⚠️ VALIDACIÓN 2: MODO DE ANÁLISIS
        // ✅ VERIFICAR QUE ES MODO DETALLADO
        // El análisis de frecuencias solo está disponible en modo detallado
        if (($sessionData['modo_analisis'] ?? 'basico') !== 'detallado') {
            return redirect()->route('analisis.index')
                ->with('info', 'El análisis de frecuencias está disponible solo en Modo Detallado');
        }

        // 📊 GENERAR ANÁLISIS DE FRECUENCIAS
        // Solo procesar si es modo detallado
        // El servicio de reportes genera tablas de frecuencia de gastos
        $datosFrecuencias = $this->reporteService->generarAnalisisFrecuencias($sessionData);

        // 🚀 RENDERIZAR VISTA DE FRECUENCIAS
        // Retorna la vista con los datos de frecuencia procesados
        return view('analisis.frecuencias', $datosFrecuencias);
    }
}