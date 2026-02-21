<?php
// app/Http/Controllers/Analisis/DetalladoController.php

namespace App\Http\Controllers\Analisis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Analisis\RapidoRequest;
use App\Http\Requests\Analisis\CSVImportRequest;
use App\Services\Finanzas\AnalisisDetalladoService;
use App\Services\Finanzas\CSVImportService;
use App\Services\Finanzas\AnalisisRapidoService;
use Illuminate\Support\Facades\Log;
use App\Models\Category;

class DetalladoController extends Controller
{
    /**
     * Servicios necesarios para el análisis financiero
     */
    protected $analisisDetalladoService;
    protected $csvImportService;
    protected $analisisRapidoService;

    /**
     * Constructor del controlador
     * Inyecta las dependencias necesarias
     */
    public function __construct(
        AnalisisDetalladoService $analisisDetalladoService,
        CSVImportService $csvImportService,
        AnalisisRapidoService $analisisRapidoService
    ) {
        $this->analisisDetalladoService = $analisisDetalladoService;
        $this->csvImportService = $csvImportService;
        $this->analisisRapidoService = $analisisRapidoService;
    }

    /**
     * PROCESAR ANÁLISIS FINANCIERO
     * Método principal que determina qué tipo de análisis realizar
     * 
     * @param Request $request Datos del formulario
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function analizar(Request $request)
    {
       /*  dd([
    'mensaje'                     => 'Llegó a DetalladoController@analizar',
    'tiene_ingresos_simples'      => $request->has('ingresos'),
    'ingresos_es_array'           => is_array($request->input('ingresos')),
    'tiene_ingresos_detallados'   => $request->has('ingresos_detallados'),
    'todos_los_inputs'            => $request->all(),
    'url_actual'                  => $request->fullUrl(),
]); */
        // 🎯 PRIORIDAD 1: Verificar si es análisis detallado (estructura con objetos por mes)
        if ($request->has('ingresos_detallados') && is_array($request->ingresos_detallados)) {
            return $this->procesarModoDetallado($request);
        }
        
        // 🎯 PRIORIDAD 2: Verificar si es análisis rápido (arrays simples)
        if ($request->has('ingresos') && is_array($request->ingresos)) {
            return $this->procesarModoRapido($request);
        }
        
        // ⚠️ Error: No se recibieron datos válidos
        return back()->with('error', 'No se recibieron datos válidos para análisis.');
    }

    /**
     * PROCESAR MODO RÁPIDO
     * Maneja el análisis con arrays simples de ingresos y gastos
     * 
     * @param Request $request Datos del formulario
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    private function procesarModoRapido(Request $request)
    {
        // 📝 Validar datos básicos del modo rápido
        $validated = $request->validate([
            'ingresos'           => 'required|array|min:1',
            'ingresos.*'         => 'required|numeric|min:0',
            'gastos_fijos'       => 'sometimes|array|min:1',
            'gastos_fijos.*'     => 'sometimes|numeric|min:0',
            'gastos_dinamicos'   => 'sometimes|array|min:1',
            'gastos_dinamicos.*' => 'sometimes|numeric|min:0'
        ]);

        // ⚖️ Validar balance financiero
        $balance = $this->analisisRapidoService->validarBalance(
            $request->ingresos,
            $request->gastos_fijos,
            $request->gastos_dinamicos
        );

        // Verificar si hay error en el balance
        if ($balance['error']) {
            return back()->with('error', $balance['mensaje']);
        }

        // 🔄 Normalizar arrays para asegurar estructura correcta
        list($ingresos, $gastosFijos, $gastosDinamicos) = $this->analisisRapidoService->normalizarArrays(
            $request->ingresos,
            $request->gastos_fijos,
            $request->gastos_dinamicos
        );

        // 📅 Filtrar solo meses con datos válidos
        $mesesConDatos = $this->analisisRapidoService->filtrarMesesConDatos(
            $ingresos, 
            $gastosFijos, 
            $gastosDinamicos
        );

        // Verificar si hay meses con datos
        if (empty($mesesConDatos)) {
            return back()->with('error', 'Debes ingresar al menos un mes con datos válidos');
        }

        // 🎯 Aplicar filtro para mantener solo meses con datos
        list($ingresos, $gastosFijos, $gastosDinamicos) = $this->analisisRapidoService->aplicarFiltroMeses(
            $ingresos,
            $gastosFijos,
            $gastosDinamicos,
            $mesesConDatos
        );

        // 📊 Procesar datos básicos para obtener estadísticas
        $datos = $this->analisisRapidoService->procesarDatosBasicos(
            $ingresos, 
            $gastosFijos, 
            $gastosDinamicos
        );

        // 🏷️ Obtener categorías de gastos para mostrar en vista
       $categorias = Category::with('subcategories')->forExpenses()->get();

// Guardar en sesión (ya lo tienes)
session(['analisis_data' => $datos]);
// Desempaquetar variables sueltas para la vista y parciales
extract($datos);

// Return: pasar EL ARRAY $datos + las variables sueltas + modo explícito
return view('analisis.index', array_merge($datos, [
    'datos'         => $datos,               // ← clave: pasa el array completo como $datos
    'categorias'    => $categorias,
    'modo_analisis' => 'basico',
]))->with('success', 'Se analizaron ' . count($mesesConDatos) . ' meses con datos válidos.');




    }

    /**
     * PROCESAR MODO DETALLADO
     * Maneja el análisis con estructura detallada (ingresos por mes y gastos individuales)
     * 
     * @param Request $request Datos del formulario
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    private function procesarModoDetallado(Request $request)
    {
        // 📝 Validar estructura compleja del modo detallado
        $validated = $request->validate([
            // Validación de ingresos detallados (por mes)
            'ingresos_detallados'                   => 'required|array|min:1',
            'ingresos_detallados.*.mes'              => 'required|date_format:Y-m',
            'ingresos_detallados.*.monto'             => 'required|numeric|min:0',
            
            // Validación de gastos detallados (transacciones individuales)
            'gastos_detallados'                      => 'required|array|min:1',
            'gastos_detallados.*.fecha'               => 'required|date',
            'gastos_detallados.*.categoria_id'        => 'required|exists:categories,id',
            'gastos_detallados.*.subcategoria_id'     => 'nullable|exists:subcategories,id',
            'gastos_detallados.*.monto'                => 'required|numeric|min:0',
        ]);

try {
    // Procesar
    $resultados = $this->analisisDetalladoService->procesarDatosDetallados(
        $request->ingresos_detallados,
        $request->gastos_detallados
    );

    $categorias = Category::with('subcategories')->forExpenses()->get();

    // Renombrar a $datos para consistencia
    $datos = $resultados;

    // Agrega modo
    $datos['modo_analisis'] = 'detallado';


    // GUARDAR EN SESIÓN (ya lo tienes)
    session([
        'analisis_data' => $datos,
        'modo_analisis' => 'detallado'
    ]);

    // CONVERTIR ARRAY ASOCIATIVO A INDEXADO (ya lo tienes)
    $analisisCategorias = $datos['analisis_categorias'] ?? [];
    if (!empty($analisisCategorias) && array_keys($analisisCategorias) !== range(0, count($analisisCategorias) - 1)) {
        $analisisCategorias = array_values($analisisCategorias);
    }

    // DESENPAQUETA TODAS LAS CLAVES COMO VARIABLES SUELTAS
    extract($datos);

    // Return con array_merge para mantener $datos como array también
    return view('analisis.index', array_merge($datos, [
        'categorias'        => $categorias,
        'analisis_categorias' => $analisisCategorias,
        'modo_analisis'     => 'detallado',
    ]))->with('success', 'Análisis detallado procesado correctamente.');

} catch (\Exception $e) {
    return back()->with('error', 'Error al procesar datos detallados: ' . $e->getMessage())
                 ->withInput();
}
    }

    /**
     * IMPORTAR ARCHIVO CSV
     * Procesa la importación de datos desde un archivo CSV
     * 
     * @param CSVImportRequest $request Request validado específico para CSV
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
   public function importarCSV(CSVImportRequest $request)
{
    try {
        $file = $request->file('archivo_csv');
        $contenido = file($file->getPathname());

        $resultado = $this->csvImportService->importarCSV($contenido);

        // Guardar en sesión si lo necesitas
        session(['analisis_data' => $resultado['datos']]);

        // RETORNO CORREGIDO Y CLARO
        return view('analisis.index', $resultado['datos'])
            ->with('mensaje', $resultado['mensaje'])
            ->with('tipo', $resultado['tipo'])
            ->with('datos', $resultado['datos']);  // ← ← ← AGREGAR ESTA LÍNEA PARA QUE $datos exista en la vista principal
    } catch (\Exception $e) {
        return back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
    }
}
}