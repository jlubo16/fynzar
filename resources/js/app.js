// app/js/app.js

// ===== 1. DEFINICIONES GLOBALES (ACCESIBLES DE INMEDIATO) =====
window.showLoading = function(message = 'Procesando...') {
    const loading = document.getElementById('global-loading');
    if (loading) {
        const textElement = loading.querySelector('p');
        if (textElement) textElement.textContent = message;
        loading.style.display = 'flex';
    } else {
        console.warn('⚠️ Elemento #global-loading no encontrado en el DOM.');
    }
};

window.hideLoading = function() {
    const loading = document.getElementById('global-loading');
    if (loading) {
        loading.style.display = 'none';
    }
};

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

// ===== 2. APLICACIÓN PRINCIPAL (ESPERA AL DOM) =====
document.addEventListener('DOMContentLoaded', function() {
    console.log("🔄 App.js cargado. Bootstrap disponible:", typeof bootstrap !== 'undefined');
    
    // Inicializar componentes de Bootstrap
    initTooltips();
    initPopovers();
    
    // Manejar lógica de UI
    initForms();
    setupGlobalEvents();
    initAnimations();
});

// ===== 3. FUNCIONES DE INICIALIZACIÓN =====
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl, {
            trigger: 'hover focus',
            placement: 'top'
        });
    });
}

function initPopovers() {
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl, {
            trigger: 'focus',
            html: true
        });
    });
}

function initForms() {
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

    // Feedback visual en tiempo real
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
}

function setupGlobalEvents() {
    // Manejar envío de formularios para mostrar loading
    document.addEventListener('submit', function(e) {
        const form = e.target;
        // Solo mostrar loading en formularios POST que no tengan la clase 'no-loading'
        if (form.method === 'post' && !form.classList.contains('no-loading')) {
            window.showLoading('Enviando datos...');
        }
    });

    // Auto-ocultar alertas después de 5 segundos
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            try {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            } catch (err) {
                alert.remove(); // Fallback si bootstrap no está listo
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
}

function initAnimations() {
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

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
}

// ===== 4. FUNCIONES UTILITARIAS DE WINDOW =====
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
        window.showToast('Copiado al portapapeles', 'success');
    } catch (err) {
        console.error('Error al copiar:', err);
        window.showToast('Error al copiar', 'error');
    }
};

window.showToast = function(message, type = 'info', duration = 3000) {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
    }

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
    const toastEl = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastEl, { delay: duration });
    toast.show();

    toastEl.addEventListener('hidden.bs.toast', function () {
        toastEl.remove();
    });
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

// ===== 5. MANEJO DE ERRORES GLOBALES =====
window.addEventListener('error', function(e) {
    console.error('🔴 Error global capturado:', e.error);
    // Solo mostramos toast si la función ya existe
    if (typeof window.showToast === 'function') {
        window.showToast('Ha ocurrido un error inesperado', 'error');
    }
});

window.addEventListener('unhandledrejection', function(e) {
    console.error('🟠 Promise rechazada:', e.reason);
    if (typeof window.showToast === 'function') {
        window.showToast('Error en la operación asíncrona', 'error');
    }
});

// ===== 6. EXPORTAR OBJETO APP =====
window.App = {
    showLoading: window.showLoading,
    hideLoading: window.hideLoading,
    showToast: window.showToast,
    formatCurrency: window.formatCurrency,
    formatPercentage: window.formatPercentage,
    downloadCSV: window.downloadCSV,
    copyToClipboard: window.copyToClipboard
};