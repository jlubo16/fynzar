<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TraerDatosController;
use App\Http\Controllers\Analisis\AnalisisBaseController;
use App\Http\Controllers\Analisis\RapidoController;
use App\Http\Controllers\Analisis\DetalladoController;
use App\Http\Controllers\Analisis\FrecuenciasController;
use App\Http\Controllers\Analisis\TendenciasController;
use App\Http\Controllers\Analisis\ComparativoController;
use App\Http\Controllers\Analisis\ExportController;

use App\Models\Subcategory;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');


// ✅ RUTA PRINCIPAL - Usa AnalisisBaseController
Route::get('/analisis', [AnalisisBaseController::class, 'index'])->name('analisis.index');

// ✅ RUTA PARA MODO RÁPIDO - Usa RapidoController
Route::post('/analisis/calcular', [RapidoController::class, 'analizar'])->name('analisis.calcular');

// ✅ RUTA PARA MODO DETALLADO - Usa DetalladoController
// NOTA: Esta ruta se usa en tu formulario: action="{{ route('analisis.calcular.detallado') }}"
Route::post('/analisis/calcular-detallado', [DetalladoController::class, 'analizar'])->name('analisis.calcular.detallado');

// ✅ RUTA PARA IMPORTAR CSV - Usa DetalladoController
Route::post('/analisis/importar-csv', [DetalladoController::class, 'importarCSV'])->name('analisis.importar.csv');

// ✅ API para subcategorías 
Route::get('/api/categorias/{categoria}/subcategorias', function($categoria) {
    $subcategorias = Subcategory::where('category_id', $categoria)->get();
    return response()->json($subcategorias);
})->name('api.subcategorias');

// ✅ Rutas para estadística avanzada - Usan los nuevos controladores
Route::get('/analisis/frecuencias', [FrecuenciasController::class, 'index'])->name('analisis.frecuencias');
Route::get('/analisis/tendencias', [TendenciasController::class, 'index'])->name('analisis.tendencias');
Route::get('/analisis/comparativo', [ComparativoController::class, 'index'])->name('analisis.comparativo');

// ✅ Ruta para limpiar datos - Usa AnalisisBaseController
Route::get('/analisis/limpiar', [AnalisisBaseController::class, 'limpiar'])->name('analisis.limpiar');

// ✅ RUTAS OPCIONALES (si las necesitas):
// Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
// Route::post('/datos', [TraerDatosController::class, 'traerDatos'])->name('traerDatos');
// Route::get('/analisis/exportar', [AnalisisController::class, 'exportar'])->name('analisis.exportar');

