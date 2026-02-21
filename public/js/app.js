// ===== APLICACIÓN PRINCIPAL - VERSIÓN CORREGIDA =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 App.js cargado. Bootstrap disponible:', typeof bootstrap !== 'undefined');
    
    // Solo inicializar si Bootstrap está disponible
    if (typeof bootstrap === 'undefined') {
        console.error('❌ Bootstrap NO está disponible. Revisa el orden de carga.');
        return;
    }
    
    // Inicializar tooltips
    initTooltips();
    
    // Inicializar popovers
    initPopovers();
    
    // Manejar formularios
    initForms();
    
    // Configurar eventos globales
    setupGlobalEvents();
    
    // Inicializar animaciones (si existen elementos)
    initAnimations();
});

// ===== FUNCIONES DE INICIALIZACIÓN =====
function initTooltips() {
    try {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltips = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                trigger: 'hover focus',
                placement: 'top'
            });
        });
        console.log('✅ Tooltips inicializados:', tooltips.length);
    } catch (error) {
        console.error('❌ Error inicializando tooltips:', error);
    }
}

function initPopovers() {
    try {
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        const popovers = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl, {
                trigger: 'focus',
                html: true
            });
        });
        console.log('✅ Popovers inicializados:', popovers.length);
    } catch (error) {
        console.error('❌ Error inicializando popovers:', error);
    }
}

function initForms() {
    try {
        // Validación de formularios
        const forms = document.querySelectorAll('.needs-validation');
        forms.forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

        // Limpiar validación al cambiar
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    if (input.checkValidity()) {
                        input.classList.remove('is-invalid');
                        input.classList.add('is-valid');
                    } else {
                        input.classList.remove('is-valid');
                        input.classList.add('is-invalid');
                    }
                });
            });
        });
        
        console.log('✅ Formularios inicializados:', forms.length);
    } catch (error) {
        console.error('❌ Error inicializando formularios:', error);
    }
}

function setupGlobalEvents() {
    try {
        // Mostrar/ocultar loading global
        window.showLoading = function(message = 'Procesando...') {
            const loading = document.getElementById('global-loading');
            if (loading) {
                const messageEl = loading.querySelector('p');
                if (messageEl) messageEl.textContent = message;
                loading.style.display = 'flex';
            }
        };

        window.hideLoading = function() {
            const loading = document.getElementById('global-loading');
            if (loading) {
                loading.style.display = 'none';
            }
        };

        // Manejar envío de formularios
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.method === 'post' && !form.classList.contains('no-loading')) {
                showLoading('Enviando datos...');
            }
        });

        // Auto-ocultar alertas después de 5 segundos
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            setTimeout(() => {
                try {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                } catch (e) {
                    // Silenciar error si no se puede cerrar
                }
            }, 5000);
        });

        // Smooth scroll para enlaces internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
        
        console.log('✅ Eventos globales configurados');
    } catch (error) {
        console.error('❌ Error configurando eventos globales:', error);
    }
}

function initAnimations() {
    try {
        // Solo si hay elementos para animar
        const animateElements = document.querySelectorAll('.animate-on-scroll');
        if (animateElements.length === 0) return;
        
        // Intersection Observer para animaciones al hacer scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observar elementos con clase 'animate-on-scroll'
        animateElements.forEach(el => {
            observer.observe(el);
        });
        
        console.log('✅ Animaciones inicializadas:', animateElements.length);
    } catch (error) {
        console.error('❌ Error inicializando animaciones:', error);
    }
}

// ===== FUNCIONES UTILITARIAS =====
window.formatCurrency = function(amount) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 2
    }).format(amount);
};

window.formatPercentage = function(value) {
    return new Intl.NumberFormat('es-ES', {
        style: 'percent',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value / 100);
};

window.downloadCSV = function(data, filename) {
    const blob = new Blob([data], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.setAttribute('href', url);
    a.setAttribute('download', filename);
    a.click();
    window.URL.revokeObjectURL(url);
};

window.copyToClipboard = async function(text) {
    try {
        await navigator.clipboard.writeText(text);
        showToast('Copiado al portapapeles', 'success');
    } catch (err) {
        console.error('Error al copiar:', err);
        showToast('Error al copiar', 'error');
    }
};

// ===== NOTIFICACIONES Y TOASTS =====
window.showToast = function(message, type = 'info', duration = 3000) {
    try {
        // Crear contenedor si no existe
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            document.body.appendChild(container);
        }

        // Crear toast
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-${type} text-white">
                    <strong class="me-auto">
                        <i class="bi ${getToastIcon(type)} me-2"></i>
                        ${getToastTitle(type)}
                    </strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', toastHtml);
        
        // Mostrar toast
        const toastEl = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastEl, { delay: duration });
        toast.show();

        // Eliminar después de mostrar
        toastEl.addEventListener('hidden.bs.toast', function () {
            toastEl.remove();
        });
    } catch (error) {
        console.error('❌ Error mostrando toast:', error);
    }
};

function getToastIcon(type) {
    const icons = {
        'success': 'bi-check-circle-fill',
        'error': 'bi-exclamation-triangle-fill',
        'warning': 'bi-exclamation-circle-fill',
        'info': 'bi-info-circle-fill'
    };
    return icons[type] || 'bi-info-circle-fill';
}

function getToastTitle(type) {
    const titles = {
        'success': 'Éxito',
        'error': 'Error',
        'warning': 'Advertencia',
        'info': 'Información'
    };
    return titles[type] || 'Información';
}

// ===== MANEJO DE ERRORES GLOBALES =====
window.addEventListener('error', function(e) {
    console.error('Error global capturado:', e.error);
    // No mostrar toast para evitar recursión
});

window.addEventListener('unhandledrejection', function(e) {
    console.error('Promise rechazada capturada:', e.reason);
    // No mostrar toast para evitar recursión
});

// ===== EXPORTAR FUNCIONES GLOBALES =====
window.App = {
    showLoading: window.showLoading,
    hideLoading: window.hideLoading,
    showToast: window.showToast,
    formatCurrency: window.formatCurrency,
    formatPercentage: window.formatPercentage,
    downloadCSV: window.downloadCSV,
    copyToClipboard: window.copyToClipboard
};

console.log('🚀 App.js cargado completamente');