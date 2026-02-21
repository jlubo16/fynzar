@extends('layouts.app')

@section('title', 'FinanzAnalyzer - Bienvenida')

@section('content')
    <!-- Loading Spinner Global (heredado del layout) -->

    <!-- Hero Section con gradiente y efecto glassmorphism -->
    <div class="hero-section text-center mb-5 fade-in">
        <div class="container py-5">
            <div class="hero-badge mb-3">
                <span class="badge bg-white text-primary border-0 py-2 px-3 shadow-sm">
                    <i class="bi bi-stars me-1"></i>Inteligencia Financiera Personal
                </span>
            </div>
            <h1 class="display-4 fw-bold text-white mb-3">
                <i class="bi bi-graph-up me-2"></i>Análisis Automático de Finanzas Familiares
            </h1>
            <p class="lead text-white-80 fs-4 mb-4">Toma el control de tus finanzas con nuestro análisis inteligente</p>
            <div class="mt-4">
                <span class="badge badge-glass me-2"><i class="bi bi-shield-check"></i> Seguro</span>
                <span class="badge badge-glass me-2"><i class="bi bi-lightning"></i> Rápido</span>
                <span class="badge badge-glass"><i class="bi bi-graph-up-arrow"></i> Preciso</span>
            </div>
        </div>
    </div>

    <!-- INCLUIR EL PARTIAL EXISTENTE how-to-calculate -->
    @include('analisis.partials.how-to-calculate')

    <!-- Modos de Análisis - Tarjetas con diseño neumorfismo sutil -->
    <section class="modes-section mb-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <div class="icon-wrapper mb-3">
                    <div class="icon-circle">
                        <i class="bi bi-sliders text-primary"></i>
                    </div>
                </div>
                <h2 class="h1 fw-bold mb-3">
                    Tres Modos de Análisis Disponibles
                </h2>
                <p class="lead text-muted">Elige el que mejor se adapte a tus necesidades</p>
                <div class="d-flex justify-content-center mt-3">
                    <div class="timeline-dots">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Modo Rápido -->
                <div class="col-lg-4">
                    <div class="mode-card card-1 h-100">
                        <div class="card-icon">
                            <div class="icon-bg">
                                <i class="bi bi-lightning-charge-fill"></i>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-badge">
                                <span class="badge bg-success">5 min</span>
                            </div>
                            <h3 class="card-title">⚡ Modo Rápido</h3>
                            <p class="card-description">Perfecto para comenzar. Solo necesitas 3 números por mes:</p>
                            <ul class="card-list">
                                <li><i class="bi bi-check-circle-fill"></i> Ingresos totales</li>
                                <li><i class="bi bi-check-circle-fill"></i> Gastos fijos</li>
                                <li><i class="bi bi-check-circle-fill"></i> Gastos variables</li>
                            </ul>
                            <div class="card-footer">
                                <small class="text-muted">Recomendado para empezar</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modo Detallado -->
                <div class="col-lg-4">
                    <div class="mode-card card-2 h-100">
                        <div class="card-icon">
                            <div class="icon-bg">
                                <i class="bi bi-bar-chart-fill"></i>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-badge">
                                <span class="badge bg-warning">15-20 min</span>
                            </div>
                            <h3 class="card-title">📊 Modo Detallado</h3>
                            <p class="card-description">Para control preciso. Categoriza cada gasto:</p>
                            <ul class="card-list">
                                <li><i class="bi bi-check-circle-fill"></i> Análisis por categorías</li>
                                <li><i class="bi bi-check-circle-fill"></i> Subcategorías específicas</li>
                                <li><i class="bi bi-check-circle-fill"></i> Visualizaciones avanzadas</li>
                            </ul>
                            <div class="card-footer">
                                <small class="text-muted">Para control total</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Importar CSV -->
                <div class="col-lg-4">
                    <div class="mode-card card-3 h-100">
                        <div class="card-icon">
                            <div class="icon-bg">
                                <i class="bi bi-file-earmark-spreadsheet-fill"></i>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-badge">
                                <span class="badge bg-info">2 min</span>
                            </div>
                            <h3 class="card-title">📁 Importar CSV</h3>
                            <p class="card-description">Para usuarios avanzados con datos existentes:</p>
                            <ul class="card-list">
                                <li><i class="bi bi-check-circle-fill"></i> Importa desde Excel/Google Sheets</li>
                                <li><i class="bi bi-check-circle-fill"></i> Plantilla predefinida</li>
                                <li><i class="bi bi-check-circle-fill"></i> Análisis automático completo</li>
                            </ul>
                            <div class="card-footer">
                                <small class="text-muted">Para usuarios avanzados</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Beneficios - Grid minimalista -->
    <section class="benefits-section mb-5">
        <div class="container">
            <div class="section-header text-center mb-5">
                <h3 class="h2 fw-bold mb-3">
                    <i class="bi bi-award-fill text-gradient me-2"></i>
                    Beneficios de Usar FinanzAnalyzer
                </h3>
                <p class="text-muted">Descubre todo lo que ganarás al tomar el control</p>
            </div>

            <div class="row g-4">
                <div class="col-md-3 col-sm-6">
                    <div class="benefit-card">
                        <div class="benefit-icon icon-1">
                            <i class="bi bi-eye-fill"></i>
                        </div>
                        <h4 class="benefit-title">Claridad Inmediata</h4>
                        <p class="benefit-text">Entiende tu situación financiera al instante</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="benefit-card">
                        <div class="benefit-icon icon-2">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <h4 class="benefit-title">Detección Temprana</h4>
                        <p class="benefit-text">Identifica problemas de gasto antes de que crezcan</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="benefit-card">
                        <div class="benefit-icon icon-3">
                            <i class="bi bi-calendar-week-fill"></i>
                        </div>
                        <h4 class="benefit-title">Planificación Efectiva</h4>
                        <p class="benefit-text">Crea presupuestos basados en datos reales</p>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="benefit-card">
                        <div class="benefit-icon icon-4">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                        <h4 class="benefit-title">Educación Financiera</h4>
                        <p class="benefit-text">Aprende mientras analizas tus finanzas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final con efectos -->
    <section class="cta-section mt-5 pt-5">
        <div class="container">
            <div class="cta-card p-5 rounded-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3 class="h2 fw-bold text-white mb-3">¿Listo para transformar tus finanzas?</h3>
                        <p class="text-white-80 mb-0">
                            <i class="bi bi-check-circle-fill me-1"></i> Completamente gratis
                            <span class="mx-2">•</span>
                            <i class="bi bi-check-circle-fill me-1"></i> Sin registro requerido
                            <span class="mx-2">•</span>
                            <i class="bi bi-check-circle-fill me-1"></i> Tus datos nunca salen de tu navegador
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('analisis.index') }}" class="btn btn-light btn-lg px-5 py-3 shadow-lg btn-hover">
                            <i class="bi bi-arrow-right-circle me-2"></i>
                            Comenzar Análisis
                        </a>
                        <div class="mt-3">
                            <a href="#como-calcular-section" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-arrow-up-circle me-1"></i>
                                Ver Guía Primero
                            </a>
                            <a href="{{ route('analisis.index') }}#data-form-section" class="btn btn-link text-white ms-2 text-decoration-none">
                                <i class="bi bi-skip-forward-fill me-1"></i>
                                Saltar al Formulario
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection