<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FinanzAnalyzer - Análisis Financiero')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Font Awesome (opcional pero recomendado) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Chart.js -->
    @stack('chart-js')
    
    <!-- Estilos personalizados -->
  {{--   <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
       {{--  <link href="{{ asset('css/main.css') }}" rel="stylesheet"> --}}
       @vite(['resources/scss/app.scss', 'resources/js/app.js'])


    @stack('styles')
</head>
<body class="bg-light">
    <!-- Navigation -->
    @include('partials.navbar')
    
    <!-- Main Content -->
    <main class="container py-4">
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('partials.footer')
    
    <!-- Loading Spinner Global -->
    <div id="global-loading" class="loading-overlay" style="display: none;">
        <div class="spinner-container">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3 text-primary">Procesando...</p>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

     <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script>
document.addEventListener('DOMContentLoaded', function() {

    // 👇 AGREGAR ESTO
    const rootStyles = getComputedStyle(document.documentElement);

    // Configuración global de SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Mostrar alertas según el tipo
    @if(Session::has('success'))
        Toast.fire({
            icon: 'success',
            title: "{{ Session::get('success') }}",
            background: rootStyles.getPropertyValue('--success-color'),
            color: 'white'
        });
    @endif

    @if(Session::has('error'))
        Toast.fire({
            icon: 'error',
            title: "{{ Session::get('error') }}",
            background: rootStyles.getPropertyValue('--danger-color'),
            color: 'white',
            timer: 3000
        });
    @endif

    @if(Session::has('info'))
        Toast.fire({
            icon: 'info',
            title: "{{ Session::get('info') }}",
            background: rootStyles.getPropertyValue('--info-color'),
            color: 'white'
        });
    @endif

    @if(Session::has('warning'))
        Toast.fire({
            icon: 'warning',
            title: "{{ Session::get('warning') }}",
            background: rootStyles.getPropertyValue('--warning-color'),
            color: '#212529'
        });
    @endif

});
</script>

    <!-- Scripts personalizados -->
  <script src="{{ asset('js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>