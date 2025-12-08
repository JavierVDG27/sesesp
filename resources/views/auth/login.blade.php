<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen bg-gray-100 flex flex-col" x-data="{ showRecoverModal: false }">

    {{-- BARRA SUPERIOR --}}
    <header class="w-full bg-[#691C32] text-white">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex flex-col items-center text-center">
                <img src="{{ asset('images/tecnologias-logo.png') }}"
                    alt="Logotipo"
                    class="h-12 w-auto mb-2">

                <span class="text-xs uppercase tracking-wide opacity-80">
                    Secretariado Ejecutivo del Sistema Estatal de Seguridad Pública
                </span>
                <span class="text-sm md:text-base font-semibold">
                    SISTEMA DE GESTIÓN DE EXPEDIENTES
                </span>
            </div>
        </div>
    </header>


    {{-- CONTENEDOR PRINCIPAL --}}
    <main class="flex-1 flex items-center justify-center px-4 py-10">

        <div class="max-w-5xl w-full">
            {{-- Tarjeta grande --}}
            <div class="bg-[#691C32] rounded-3xl shadow-2xl overflow-hidden flex flex-col lg:flex-row">

                {{-- LADO IZQUIERDO - Bienvenida --}}
                <div class="w-full lg:w-1/2 px-10 py-10 text-white flex flex-col justify-between"
                    style="background: linear-gradient(135deg, #691C32, #9F2241);">

                    {{-- Contenido centrado --}}
                    <div class="flex flex-col items-center justify-center text-center mt-10">
                        <h2 class="text-3xl md:text-4xl font-bold mb-3">
                            Bienvenido/a
                        </h2>

                        <p class="text-sm md:text-base text-gray-100 max-w-sm">
                            Gestiona tus expedientes de manera eficiente y segura con nuestro sistema.
                        </p>
                    </div>

                    {{-- Texto inferior más pequeño --}}
                    <div class="text-center mt-10 mb-2 opacity-90">
                        <p class="text-[10px] uppercase tracking-wide text-gray-200">
                            Uso exclusivo institucional
                        </p>
                        <p class="text-[10px] text-gray-200 mt-1">
                            Para dudas de acceso, contacte al área de Tecnologías de su institución.
                        </p>
                    </div>

                </div>

                {{-- LADO DERECHO - Formulario de login --}}
                <div class="w-full lg:w-1/2 bg-white flex items-center justify-center px-8 py-10">
                    <div class="w-full max-w-sm">

                        {{-- Logo encima del título --}}
                        <div class="flex flex-col items-center mb-6">
                            <img src="{{ asset('images/logo.jpg') }}" alt="Logo SESESP"
                                 class="h-18 w-auto mb-3">
                            <h2 class="text-2xl font-bold text-[#691C32]">
                                Iniciar sesión
                            </h2>
                        </div>

                        {{-- MENSAJES DE ERROR --}}
                        @if ($errors->any())
                            <div class="mb-4 p-3 rounded-lg bg-red-100 border border-red-300 text-red-700 text-sm">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- FORMULARIO --}}
                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            {{-- Correo --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">
                                    Correo electrónico
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    class="mt-1 block w-full rounded-full border-gray-300 bg-white px-4 py-2.5
                                           shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]"
                                    placeholder="Ingresa tu correo"
                                >
                            </div>

                            {{-- Contraseña --}}
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    Contraseña
                                </label>
                                <input
                                    type="password"
                                    id="password"
                                    name="password"
                                    required
                                    class="mt-1 block w-full rounded-full border-gray-300 bg-white px-4 py-2.5
                                           shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]"
                                    placeholder="Ingresa tu contraseña"
                                >
                            </div>

                            {{-- Recordarme + Olvidaste --}}
                            <div class="flex items-center justify-between">
                                <label class="flex items-center text-sm text-gray-700">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        class="rounded border-gray-300 text-[#691C32] focus:ring-[#9F2241]">
                                    <span class="ml-2">Recuérdame</span>
                                </label>

                                <button
                                    type="button"
                                    class="text-sm text-[#9F2241] hover:text-[#691C32] underline"
                                    @click="showRecoverModal = true"
                                >
                                    ¿Olvidaste tu contraseña?
                                </button>
                            </div>

                            {{-- Botón --}}
                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full py-2.5 px-4 rounded-full bg-[#9F2241] hover:bg-[#691C32]
                                           text-white font-semibold shadow-md
                                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#691C32]">
                                    Entrar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

    </main>

    {{-- FOOTER --}}
    <footer class="w-full bg-[#691C32] text-gray-100 text-xs py-3">
        <div class="max-w-7xl mx-auto px-6 text-center">
            &copy; {{ date('Y') }} Sistema de Gestión de Expedientes. Todos los derechos reservados.
        </div>
    </footer>

    {{-- MODAL: Olvidaste tu contraseña --}}
    <div
        x-show="showRecoverModal"
        x-transition
        x-cloak
        class="fixed inset-0 flex items-center justify-center bg-black/40 z-50"
    >
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg text-center font-semibold text-[#691C32] mb-2">
                Recuperación de contraseña
            </h3>
            <p class="text-sm text-center text-gray-700 mb-4">
                Para restablecer tu contraseña, por favor comunícate con la institución
                o con el administrador del sistema.  
            </p>
            <p class="text-sm text-center text-gray-700 mb-4">
                El restablecimiento de contraseñas solo puede realizarse por personal autorizado.
            </p>

            <div class="flex justify-end">
                <button
                    type="button"
                    class="px-4 py-2 text-sm rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300"
                    @click="showRecoverModal = false"
                >
                    Cerrar
                </button>
            </div>
        </div>
    </div>

</body>
</html>
