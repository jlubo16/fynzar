<?php
// app/Http/Controllers/Analisis/AnalisisBaseController.php

namespace App\Http\Controllers\Analisis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class AnalisisBaseController extends Controller
{
    /**
     * MOSTRAR PÁGINA PRINCIPAL DE ANÁLISIS
     * 
     * Este método controla la visualización de la página principal de análisis.
     * Puede mostrar datos existentes en sesión o una vista vacía para comenzar.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // LIMPIAR SESIÓN SI SE SOLICITA
        // Verifica si hay parámetro de limpiar o bandera de nuevo análisis
        if ($request->has('limpiar') || $request->session()->get('nuevo_analisis')) {
            session()->forget('analisis_data');
            session()->forget('nuevo_analisis');
        }

        // VERIFICAR DATOS EXISTENTES EN SESIÓN
        // Después de importar CSV o realizar análisis, pueden haber datos guardados
        if (session()->has('analisis_data')) {
            return $this->mostrarVistaConDatos();
        }

        // MOSTRAR VISTA VACÍA (SIN DATOS)
        // Si no hay datos en sesión, mostrar formulario vacío
        return $this->mostrarVistaVacia();
    }

    /**
     * MOSTRAR VISTA CON DATOS DE SESIÓN
     * 
     * Prepara y retorna la vista con los datos de análisis existentes
     * en la sesión, asegurando que tenga todos los elementos necesarios.
     *
     * @return \Illuminate\View\View
     */
    private function mostrarVistaConDatos()
    {
        // OBTENER DATOS DE SESIÓN
        $datosSesion = session('analisis_data');

        // 🏷️ OBTENER CATEGORÍAS DE GASTOS
        // Asegurar que tenemos las categorías disponibles para la vista
        $categorias = Category::with('subcategories')->forExpenses()->get();
        $datosSesion['categorias'] = $categorias;

        // ASEGURAR MODO DE ANÁLISIS
        // Si no está definido, establecer modo básico por defecto
        if (!isset($datosSesion['modo_analisis'])) {
            $datosSesion['modo_analisis'] = 'basico';
        }

        // RENDERIZAR VISTA CON DATOS EXISTENTES
        return view('analisis.index', $datosSesion);
    }

    /**
     * MOSTRAR VISTA VACÍA (SIN DATOS PREVIOS)
     * 
     * Prepara y retorna la vista con valores por defecto
     * cuando no hay datos de análisis en sesión.
     *
     * @return \Illuminate\View\View
     */
    private function mostrarVistaVacia()
    {
        // 🏷️ OBTENER CATEGORÍAS DE GASTOS
        $categorias = Category::with('subcategories')->forExpenses()->get();

        // 📋 DATOS POR DEFECTO PARA VISTA VACÍA
        // Array completo con todos los valores inicializados en 0 o vacíos
        return view('analisis.index', [
            // 📊 MÉTRICAS PROMEDIO
            'promIngreso'       => 0,
            'promFijos'         => 0,
            'promDinamicos'     => 0,
            
            // 📈 MÉTRICAS ESTADÍSTICAS
            'desviacion'        => 0,
            'porcFijos'         => 0,
            'porcDinamicos'     => 0,
            'saldo'             => 0,
            'moda'              => 'N/A',
            
            // 📅 DATOS MENSUALES
            'ingresos'          => [],
            'gastosFijos'       => [],
            'gastosDinamicos'   => [],
            
            // 💡 CONCLUSIONES
            'conclusion'        => [],
            
            // 📊 MÉTRICAS AVANZADAS
            'desviacionIngresos'    => 0,
            'medianaIngresos'       => 0,
            'medianaFijos'          => 0,
            'medianaDinamicos'      => 0,
            'cvIngresos'            => 0,
            'cvGastosDinamicos'     => 0,
            'rangoIngresos'         => 0,
            'rangoFijos'            => 0,
            'rangoDinamicos'        => 0,
            
            // 📆 PROYECCIONES ANUALES
            'ingresoAnual'          => 0,
            'gastosFijosAnual'      => 0,
            'gastosDinamicosAnual'  => 0,
            'saldoAnual'            => 0,
            
            // 🏷️ CATEGORÍAS Y ANÁLISIS
            'categorias'            => $categorias,
            'analisis_categorias'   => [],
            'tendencias_mensuales'  => [],
            
            // 🔧 CONFIGURACIÓN
            'modo_analisis'         => 'basico'
        ]);
    }

    /**
     * LIMPIAR TODOS LOS DATOS DE ANÁLISIS
     * 
     * Elimina todos los datos de análisis almacenados en sesión
     * y redirige a la página principal para comenzar un nuevo análisis.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function limpiar()
    {
        // 🧹 LIMPIAR DATOS DE SESIÓN
        // Eliminar todos los tipos de datos de análisis posibles
        session()->forget([
            'analisis_data',           // Datos de análisis rápido/detallado
            'analisis_detallado_data',  // Datos de análisis detallado específico
            'csv_import_data',          // Datos importados de CSV
            'frecuencias_data',         // Datos de análisis de frecuencias
            'comparativo_data'          // Datos de análisis comparativo
        ]);

        // 🔄 REDIRIGIR A PÁGINA PRINCIPAL
        return redirect()->route('analisis.index')
            ->with('info', 'Datos anteriores eliminados. Puedes comenzar un nuevo análisis.');
    }
}