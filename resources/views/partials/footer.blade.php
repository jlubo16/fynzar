<footer class="footer-minimal">
    <div class="container">
        
        <!-- Contenido principal -->
        <div class="footer-main">            
            <!-- Enlaces rápidos -->
            <div class="footer-links">
                <h6 class="footer-heading">Navegación</h6>
                <ul class="link-list">
                    <li><a href="{{ route('welcome') }}" class="footer-link">Inicio</a></li>
                    <li><a href="{{ route('analisis.limpiar') }}" class="footer-link">Nuevo Análisis</a></li>
                    <li><a href="#formulario-section" class="footer-link">Ingresar Datos</a></li>
                    <li><a href="#csv-section" class="footer-link">Importar CSV</a></li>
                    <li><a href="{{ route('analisis.frecuencias') }}" class="footer-link">Estadísticas</a></li>
                </ul>
            </div>

            <!-- Contacto -->
            <div class="footer-contact">
                <h6 class="footer-heading">Contacto</h6>
                <div class="contact-info">
                    <a href="mailto:info@finanzanalyzer.com" class="contact-item">
                        <span class="contact-icon">✉</span>
                        <span>info@finanzanalyzer.com</span>
                    </a>
                    <div class="contact-item">
                        <span class="contact-icon">☎</span>
                        <span>+1 (555) 123-4567</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Línea divisoria sutil -->
        <div class="footer-divider"></div>

        <!-- Redes sociales y copyright -->
        <div class="footer-bottom">
            <div class="social-links">
                <a href="#" class="social-link" aria-label="Twitter">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 4.01c-1 .49-1.98.689-3 .99-1.121-1.265-2.783-1.335-4.38-.737S11.977 6.323 12 8v1c-3.245.083-6.135-1.395-8-4 0 0-4 9 5 13-2.147 2.029-4.87 3.078-7.96 3C4.95 21.83 8.357 23 12 23c6.927 0 11-5.373 11-12 0-.278-.028-.556-.08-.83C21.94 6.054 22.94 5.05 22 4.01z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <a href="#" class="social-link" aria-label="LinkedIn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="4" cy="4" r="2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                <a href="#" class="social-link" aria-label="GitHub">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>

            <div class="copyright">
                <span>&copy; {{ date('Y') }} FinanzAnalyzer</span>
                <span class="copyright-sep">•</span>
                <span>Todos los derechos reservados</span>
            </div>

            <div class="footer-cta">
                <a href="{{ route('analisis.limpiar') }}" class="footer-btn">
                    <span>Comenzar análisis</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>