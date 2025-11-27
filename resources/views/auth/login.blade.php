<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex bg-gray-100">

    <!-- LADO IZQUIERDO (INSTITUCIONAL) -->
    <div class="hidden lg:flex w-1/2 flex-col justify-center items-center text-white px-10"
         style="background: linear-gradient(135deg, #691C32, #9F2241);">
         <img src="{{ asset('images/logo.jpg') }}" alt="Logo de la Institución" class="h-28 mx-auto mb-6">
        <div class="text-center">
            <h1 class="text-3xl font-bold mb-3">Bienvenid@ <span class="text-gray-100"></span></h1>
            <p class="text-base text-gray-200 max-w-md">
                Accede a tu cuenta para continuar con tus actividades administrativas.
            </p>
        </div>
    </div>

    <!-- LADO DERECHO (FORMULARIO) -->
    <div class="flex w-full lg:w-1/2 justify-center items-center bg-white">
        <div class="w-full max-w-md p-8">
            <div class="mb-6 text-center lg:hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 mx-auto mb-3">
                <h2 class="text-2xl font-bold text-[#691C32]">Iniciar sesión</h2>
            </div>

            <!-- FORMULARIO -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Correo -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                    <input type="email" id="email" name="email" required autofocus
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]">
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <input type="password" id="password" name="password" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]">
                </div>

                <!-- Recordarme -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#691C32] focus:ring-[#9F2241]">
                        <span class="ml-2 text-sm text-gray-600">Recuérdame</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-[#9F2241] hover:text-[#691C32]">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>

                <!-- Botón -->
                <div>
                    <button type="submit"
                        class="w-full py-2 px-4 bg-[#9F2241] hover:bg-[#691C32] text-white font-semibold rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#691C32] transition">
                        Iniciar sesión
                    </button>
                </div>
            </form>

            <!-- Footer -->
            <footer class="mt-10 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} SESESP. Todos los derechos reservados.
            </footer>
        </div>
    </div>

</body>
</html>