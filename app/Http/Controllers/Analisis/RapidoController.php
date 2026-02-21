<?php
// app/Http/Controllers/Analisis/RapidoController.php

namespace App\Http\Controllers\Analisis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Analisis\RapidoRequest;
use App\Services\Finanzas\AnalisisRapidoService;
use App\Models\Category;

class RapidoController extends Controller
{
    /**
     * Servicio para realizar análisis financiero rápido
     * Maneja la lógica de negocio para procesar arrays simples de ingresos y gastos
     *
     * @var AnalisisRapidoService
     */
    protected $analisisService;

    /**
     * Constructor del controlador
     * Inyecta el servicio de análisis rápido necesario para procesar los datos
     *
     * @param AnalisisRapidoService $analisisService
     */
    public function __construct(AnalisisRapidoService $analisisService)
    {
        $this->analisisService = $analisisService;
    }

    /**
     * PROCESAR ANÁLISIS RÁPIDO
     * 
     * Este método procesa los datos del formulario de análisis rápido,
     * realiza validaciones, normaliza los datos y genera estadísticas
     * básicas de ingresos y gastos.
     *
     * @param RapidoRequest $request Request validado específico para análisis rápido
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function analizar(RapidoRequest $request)
    {
        // ⚖️ PASO 1: VALIDAR BALANCE FINANCIERO
        // Verifica que los datos tengan sentido financiero
        $balance = $this->analisisService->validarBalance(
            $request->ingresos,
            $request->gastos_fijos,
            $request->gastos_dinamicos
        );

        // Si hay error en el balance, retornar con mensaje
        if ($balance['error']) {
            return back()->with('error', $balance['mensaje']);
        }

        // 🔄 PASO 2: NORMALIZAR ARRAYS
        // Asegura que todos los arrays tengan la misma longitud y estructura
        list($ingresos, $gastosFijos, $gastosDinamicos) = $this->analisisService->normalizarArrays(
            $request->ingresos,
            $request->gastos_fijos,
            $request->gastos_dinamicos
        );

        // 📅 PASO 3: FILTRAR MESES CON DATOS VÁLIDOS
        // Identifica qué meses tienen al menos un dato válido
        $mesesConDatos = $this->analisisService->filtrarMesesConDatos(
            $ingresos, 
            $gastosFijos, 
            $gastosDinamicos
        );

        // Validar que haya al menos un mes con datos
        if (empty($mesesConDatos)) {
            return back()->with('error', 'Debes ingresar al menos un mes con datos válidos');
        }

        // 🎯 PASO 4: APLICAR FILTRO DE MESES
        // Elimina los meses sin datos para mantener solo información relevante
        list($ingresos, $gastosFijos, $gastosDinamicos) = $this->analisisService->aplicarFiltroMeses(
            $ingresos,
            $gastosFijos,
            $gastosDinamicos,
            $mesesConDatos
        );

        // 📊 PASO 5: PROCESAR DATOS BÁSICOS
        // Calcula estadísticas como promedios, medianas, desviaciones, etc.
        $datos = $this->analisisService->procesarDatosBasicos(
            $ingresos, 
            $gastosFijos, 
            $gastosDinamicos
        );

        // 🏷️ PASO 6: OBTENER CATEGORÍAS
        // Carga las categorías de gastos para mostrar en la vista
        $categorias = Category::with('subcategories')->forExpenses()->get();

        // 💾 PASO 7: GUARDAR EN SESIÓN
        // Almacena los datos procesados para uso en otras vistas (comparativo, frecuencias)
        session(['analisis_data' => $datos]);

        // 🚀 PASO 8: RENDERIZAR VISTA CON RESULTADOS
        // Combina los datos procesados con categorías y modo de análisis
        return view('analisis.index', array_merge($datos, [
            'categorias'     => $categorias,
            'modo_analisis'  => 'basico',
        ]))->with('success', 'Se analizaron ' . count($mesesConDatos) . ' meses con datos válidos.');
    }
}