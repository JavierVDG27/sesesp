<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Expedientes - SESESP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #691C32, #9F2241);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">

    <!-- HEADER -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <img src="{{ asset('images/logo.jpg') }}" alt="SESESP" class="h-12">
                    <div>
                        <h1 class="text-xl font-bold text-[#691C32]">SESESP</h1>
                        <p class="text-sm text-gray-600">Sistema de Gestión de Expedientes</p>
                    </div>
                </div>
                <nav class="hidden md:flex space-x-6">
                    <a href="#features" class="text-gray-700 hover:text-[#9F2241]">Características</a>
                    <a href="#about" class="text-gray-700 hover:text-[#9F2241]">Acerca de</a>
                    <a href="{{ route('login') }}" class="text-[#691C32] hover:text-[#9F2241] font-semibold">Iniciar Sesión</a>
                </nav>
                <div class="md:hidden">
                    <!-- Menú móvil -->
                </div>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="hero-gradient text-white">
        <div class="container mx-auto px-4 py-20">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">
                    Gestión Eficiente de Expedientes
                </h1>
                <p class="text-xl mb-8 text-gray-100">
                    Sistema especializado para la administración y control de expedientes 
                    en la Secretaría de Seguridad Pública
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" 
                       class="bg-white text-[#691C32] px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition shadow-lg">
                        Crear Cuenta
                    </a>
                    <a href="{{ route('login') }}" 
                       class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-[#691C32] transition">
                        Acceso al Sistema
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="features" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-[#691C32] mb-12">
                Características Principales
            </h2>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-gray-50 p-6 rounded-lg shadow-md">
                    <div class="w-12 h-12 bg-[#9F2241] rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-[#691C32] mb-3">Gestión Centralizada</h3>
                    <p class="text-gray-600">
                        Administra todos los expedientes desde una plataforma unificada 
                        con acceso controlado y seguro.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-gray-50 p-6 rounded-lg shadow-md">
                    <div class="w-12 h-12 bg-[#9F2241] rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-[#691C32] mb-3">Seguridad Avanzada</h3>
                    <p class="text-gray-600">
                        Protocolos de seguridad implementados para proteger información 
                        sensible y confidencial.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-gray-50 p-6 rounded-lg shadow-md">
                    <div class="w-12 h-12 bg-[#9F2241] rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-[#691C32] mb-3">Reportes en Tiempo Real</h3>
                    <p class="text-gray-600">
                        Genera reportes automáticos y obtén métricas actualizadas 
                        del estado de los expedientes.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-3xl font-bold text-[#691C32] mb-6">
                    Sistema Oficial SESESP
                </h2>
                <p class="text-lg text-gray-700 mb-8">
                    Plataforma desarrollada para optimizar los procesos administrativos 
                    y mejorar la eficiencia en el manejo de expedientes dentro de la 
                    Secretaría de Seguridad Pública.
                </p>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold text-[#691C32] mb-4">
                        ¿Necesitas acceso al sistema?
                    </h3>
                    <p class="text-gray-600 mb-4">
                        Contacta al administrador del sistema para solicitar tu cuenta institucional.
                    </p>
                    <a href="{{ route('login') }}" 
                       class="inline-block bg-[#9F2241] text-white px-6 py-2 rounded-lg font-semibold hover:bg-[#691C32] transition">
                        Acceder al Sistema
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-[#691C32] text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-4 mb-4 md:mb-0">
                    <img src="{{ asset('images/logo.jpg') }}" alt="SESESP" class="h-10">
                    <div>
                        <p class="font-semibold">Secretaría de Seguridad Pública</p>
                        <p class="text-sm text-gray-300">Sistema de Gestión de Expedientes</p>
                    </div>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-gray-300">&copy; {{ date('Y') }} SESESP. Todos los derechos reservados.</p>
                    <p class="text-sm text-gray-400">Plataforma oficial para uso institucional</p>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>