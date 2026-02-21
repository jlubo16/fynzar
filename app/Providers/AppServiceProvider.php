<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Finanzas\EstadisticaService;
use App\Services\Finanzas\AnalisisRapidoService;
use App\Services\Finanzas\CSVImportService;
use App\Services\Finanzas\AnalisisDetalladoService;
use App\Services\Finanzas\Reportes\FrecuenciaReporte;
use App\Services\Finanzas\Reportes\ComparativoReporte;
use App\Services\Finanzas\Reportes\TendenciasReporte;
use App\Services\Finanzas\Reportes\ResumenReporte;
use App\Services\Finanzas\ReporteService;

use Illuminate\Support\Facades\URL; // <--- 1. AÑADE ESTA LÍNEA
// ... (tus otros imports se quedan igual)

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ... (todo tu código de singletons se queda exactamente igual)
        $this->app->singleton(EstadisticaService::class);
        $this->app->singleton(AnalisisRapidoService::class);
        $this->app->singleton(CSVImportService::class);
        $this->app->singleton(AnalisisDetalladoService::class);

        $this->app->singleton(FrecuenciaReporte::class, function ($app) {
            return new FrecuenciaReporte(
                $app->make(EstadisticaService::class),
                $app->make(CSVImportService::class)
            );
        });

        $this->app->singleton(ComparativoReporte::class, function ($app) {
            return new ComparativoReporte(
                $app->make(EstadisticaService::class),
                $app->make(CSVImportService::class)
            );
        });

        $this->app->singleton(TendenciasReporte::class, function ($app) {
            return new TendenciasReporte(
                $app->make(EstadisticaService::class),
                $app->make(CSVImportService::class)
            );
        });

        $this->app->singleton(ResumenReporte::class, function ($app) {
            return new ResumenReporte(
                $app->make(EstadisticaService::class),
                $app->make(CSVImportService::class)
            );
        });

        $this->app->singleton(ReporteService::class, function ($app) {
            return new ReporteService(
                $app->make(FrecuenciaReporte::class),
                $app->make(ComparativoReporte::class),
                $app->make(TendenciasReporte::class),
                $app->make(ResumenReporte::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 2. FORZAR HTTPS EN PRODUCCIÓN
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}