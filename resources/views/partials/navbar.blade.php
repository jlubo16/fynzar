<nav class="navbar navbar-expand-lg navbar-minimal fixed-top" id="mainNavbar">
    <div class="container">
        
        <!-- Logo con imagen -->
        <a class="navbar-brand logo-minimal" href="{{ route('analisis.limpiar') }}">
            <img src="{{ asset('images/branding/fynzar/imagotipo/imagotipo_multicolor.png') }}" 
                 alt="Fynzar" 
                 class="logo-img">
        </a>

        <!-- Toggler minimal -->
        <button class="navbar-toggler minimal-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <div class="toggler-icon"></div>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <!-- Home/Inicio -->
                <li class="nav-item">
                    <a class="nav-link nav-link-minimal" href="{{ route('welcome') }}">
                        <span class="nav-dot"></span>
                        <span class="nav-label">Inicio</span>
                    </a>
                </li>

                <!-- Nuevo Análisis -->
                <li class="nav-item">
                    <a class="nav-link nav-link-minimal" href="{{ route('analisis.limpiar') }}">
                        <span class="nav-dot"></span>
                        <span class="nav-label">Nuevo</span>
                    </a>
                </li>

                <!-- Ingresar Datos -->
                <li class="nav-item">
                    <a class="nav-link nav-link-minimal" href="#formulario-section">
                        <span class="nav-dot"></span>
                        <span class="nav-label">Datos</span>
                    </a>
                </li>

                <!-- CSV -->
                <li class="nav-item">
                    <a class="nav-link nav-link-minimal" href="#csv-section">
                        <span class="nav-dot"></span>
                        <span class="nav-label">CSV</span>
                    </a>
                </li>

                <!-- Frecuencias -->
                <li class="nav-item">
                    <a class="nav-link nav-link-minimal" href="{{ route('analisis.frecuencias') }}">
                        <span class="nav-dot"></span>
                        <span class="nav-label">Estadísticas</span>
                    </a>
                </li>

                <!-- Separador sutil -->
                <li class="nav-item nav-spacer"></li>

                <!-- CTA minimalista -->
                <li class="nav-item">
                    <a href="{{ route('analisis.limpiar') }}" class="btn-minimal-cta">
                        <span class="btn-text">Analizar</span>
                        <span class="btn-arrow">→</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Línea sutil inferior -->
    <div class="nav-line"></div>
</nav>

<!-- Espacio para navbar fija -->
<div class="nav-offset"></div>