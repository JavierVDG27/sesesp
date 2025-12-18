<x-app-layout>

    {{-- Encabezado --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Panel del Administrador
        </h2>
    </x-slot>

    <div class="flex bg-gray-100 min-h-screen">

        {{-- SIDE MENU --}}
        <aside class="w-64 bg-[#691C32] text-white min-h-screen shadow-xl">
            <div class="p-6 text-center text-xl font-bold tracking-wide border-b border-[#9F2241]">
                Administrador
            </div>

            <nav class="mt-6 space-y-1">

                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}"
                   class="block px-6 py-3 hover:bg-[#9F2241]/40 transition">
                    📊 Dashboard
                </a>

                {{-- Usuarios --}}
                <a href="{{ route('admin.users.index') }}"
                   class="block px-6 py-3 hover:bg-[#9F2241]/40 transition">
                    👤 Gestión de Usuarios
                </a>

                {{-- Dependencias --}}
                <a href="{{ route('admin.dependencias.index') }}"
                class="block px-6 py-3 hover:bg-[#9F2241]/40 transition">
                    🏢 Dependencias
                </a>

                {{-- Reportes --}}
                <a href="#"
                   class="block px-6 py-3 hover:bg-[#9F2241]/40 transition">
                    📑 Reportes
                </a>

                {{-- Configuración --}}
                <a href="#"
                   class="block px-6 py-3 hover:bg-[#9F2241]/40 transition">
                    ⚙️ Configuración
                </a>

                <hr class="border-[#9F2241] my-4">

                {{-- Perfil --}}
                <a href="{{ route('profile.edit') }}"
                   class="block px-6 py-3 hover:bg-[#9F2241]/40 transition">
                    🙍 Mi Perfil
                </a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-6 py-3 hover:bg-[#9F2241]/40 transition">
                        🚪 Cerrar Sesión
                    </button>
                </form>

            </nav>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 p-10">

            <div class="bg-white shadow-lg rounded-2xl p-8">

                <h1 class="text-3xl font-bold text-[#691C32] mb-4">
                    Bienvenido, {{ Auth::user()->name }}
                </h1>

                <p class="text-gray-700 mb-8">
                    Desde este panel puedes administrar usuarios, dependencias y configuraciones del sistema.
                </p>

                {{-- Tarjetas del Dashboard --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Usuarios --}}
                    <a href="{{ route('admin.users.index') }}"
                       class="block bg-[#9F2241]/10 hover:bg-[#9F2241]/20 p-6 rounded-xl shadow transition">
                        <h4 class="font-semibold text-[#9F2241] text-lg mb-2">Usuarios</h4>
                        <p class="text-gray-600 text-sm">Gestiona todos los usuarios registrados.</p>
                    </a>

                    {{-- Dependencias --}}
                    <div class="bg-[#9F2241]/10 hover:bg-[#9F2241]/20 p-6 rounded-xl shadow transition">
                        <h4 class="font-semibold text-[#9F2241] text-lg mb-2">Dependencias</h4>
                        <p class="text-gray-600 text-sm">Administra dependencias y asignaciones.</p>
                    </div>

                    {{-- Reportes --}}
                    <div class="bg-[#9F2241]/10 hover:bg-[#9F2241]/20 p-6 rounded-xl shadow transition">
                        <h4 class="font-semibold text-[#9F2241] text-lg mb-2">Reportes</h4>
                        <p class="text-gray-600 text-sm">Accede a estadísticas y reportes generales.</p>
                    </div>

                </div>

            </div>

        </main>

    </div>

</x-app-layout>