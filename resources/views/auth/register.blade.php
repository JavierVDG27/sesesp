<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Mi Institución</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex bg-gray-100">

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="flex w-full justify-center items-center bg-white">
        <div class="w-full max-w-md p-8">

            <!-- LOGO E INTRO -->
            <div class="mb-6 text-center">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo de la Institución" class="h-20 mx-auto mb-3">
                </a>
                <h2 class="text-2xl font-bold text-[#691C32]">Crear cuenta</h2>
                <p class="text-sm text-gray-600 mt-1">Regístrate para acceder al sistema institucional</p>
            </div>

            <!-- FORMULARIO -->
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre completo</label>
                    <input type="text" id="name" name="name" :value="old('name')" required autofocus
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]">
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Correo -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                    <input type="email" id="email" name="email" :value="old('email')" required
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]">
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]">
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirmar contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Botón -->
                <div>
                    <button type="submit"
                        class="w-full py-2 px-4 bg-[#9F2241] hover:bg-[#691C32] text-white font-semibold rounded-lg shadow focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#691C32] transition">
                        Registrarse
                    </button>
                </div>
            </form>

            <!-- Enlace para volver al login -->
            <p class="mt-6 text-center text-sm text-gray-600">
                ¿Ya tienes cuenta?
                <a href="{{ route('login') }}" class="font-semibold text-[#9F2241] hover:text-[#691C32] transition">
                    Inicia sesión aquí
                </a>
            </p>

            <!-- Footer -->
            <footer class="mt-10 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} SESESP. Todos los derechos reservados.
            </footer>
        </div>
    </div>

</body>
</html>
