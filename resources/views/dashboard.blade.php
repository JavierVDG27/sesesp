<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Panel de control') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-2xl p-8">
                <h3 class="text-2xl font-bold text-[#691C32] mb-6">
                    Bienvenido(a), {{ Auth::user()->name }}
                </h3>

                <p class="text-gray-700 leading-relaxed">
                    Este es tu panel principal del sistema institucional.  
                    Desde aquí podrás acceder a los diferentes módulos, revisar tus actividades y gestionar tu información.
                </p>

                <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow hover:shadow-md transition">
                        <h4 class="font-semibold text-[#9F2241] mb-2">Usuarios</h4>
                        <p class="text-gray-600 text-sm">Administra la información de los usuarios registrados.</p>
                        <a href="#" class="inline-block mt-3 text-[#9F2241] hover:text-[#691C32] font-medium text-sm">Ir al módulo →</a>
                    </div>

                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow hover:shadow-md transition">
                        <h4 class="font-semibold text-[#9F2241] mb-2">Reportes</h4>
                        <p class="text-gray-600 text-sm">Genera reportes e indicadores del sistema.</p>
                        <a href="#" class="inline-block mt-3 text-[#9F2241] hover:text-[#691C32] font-medium text-sm">Ver reportes →</a>
                    </div>

                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow hover:shadow-md transition">
                        <h4 class="font-semibold text-[#9F2241] mb-2">Configuración</h4>
                        <p class="text-gray-600 text-sm">Personaliza los parámetros del sistema y tus preferencias.</p>
                        <a href="#" class="inline-block mt-3 text-[#9F2241] hover:text-[#691C32] font-medium text-sm">Configurar →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
