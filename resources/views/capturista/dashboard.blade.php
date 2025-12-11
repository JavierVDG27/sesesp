<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Panel de capturista') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-2xl p-8">
                <h3 class="text-2xl font-bold text-[#691C32] mb-2">
                    Bienvenido(a), {{ Auth::user()->name }}
                </h3>
                <p class="text-gray-700 leading-relaxed">
                    Desde este panel podrás <span class="font-semibold">capturar, consultar y dar seguimiento a los expedientes técnicos</span>
                    que forman parte del Sistema de Gestión de Expedientes del SESESP.
                </p>

                {{-- ACCIONES RÁPIDAS --}}
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Crear expediente --}}
                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow hover:shadow-md hover:bg-[#9F2241]/20 transition">
                        <h4 class="font-semibold text-[#9F2241] mb-1 text-lg">Crear nuevo expediente</h4>
                        <p class="text-gray-600 text-sm mb-3">
                            Inicia la captura de un nuevo expediente técnico, registrando los datos generales y el presupuesto del proyecto.
                        </p>
                        <a href="{{ route('expedientes.create') }}"
                           class="inline-flex items-center px-3 py-2 bg-[#691C32] text-white text-sm font-medium rounded-md hover:bg-[#4e1324] transition">
                            Ir a captura de expediente
                            <span class="ml-1">→</span>
                        </a>
                    </div>

                    {{-- Mis expedientes --}}
                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow hover:shadow-md hover:bg-[#9F2241]/20 transition">
                        <h4 class="font-semibold text-[#9F2241] mb-1 text-lg">Mis expedientes</h4>
                        <p class="text-gray-600 text-sm mb-3">
                            Consulta los expedientes que has capturado, revisa su estatus (Borrador, En revisión, Aprobado, Rechazado)
                            y continúa su edición.
                        </p>
                        <a href="{{ route('expedientes.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-[#691C32] text-white text-sm font-medium rounded-md hover:bg-[#4e1324] transition">
                            Ver lista de expedientes
                            <span class="ml-1">→</span>
                        </a>
                    </div>

                    {{-- Guía rápida / ayuda --}}
                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow hover:shadow-md hover:bg-[#9F2241]/20 transition">
                        <h4 class="font-semibold text-[#9F2241] mb-1 text-lg">Guía rápida de captura</h4>
                        <p class="text-gray-600 text-sm mb-3">
                            Recuerda capturar primero los datos generales, después la descripción técnica, metas e indicadores,
                            presupuesto y finalmente adjuntar la documentación correspondiente.
                        </p>
                        <ul class="text-xs text-gray-600 list-disc list-inside space-y-1">
                            <li>Los expedientes se crean en estatus <span class="font-semibold">Borrador</span>.</li>
                            <li>Antes de enviar a revisión, verifica los campos obligatorios.</li>
                            <li>Puedes regresar y editar un expediente en borrador en cualquier momento.</li>
                        </ul>
                    </div>
                </div>

                {{-- (Opcional) sección para futuro: resumen / indicadores --}}
                {{-- 
                <div class="mt-10 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="p-4 bg-gray-100 rounded-xl text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Expedientes en borrador</p>
                        <p class="text-2xl font-bold text-[#691C32]">{{ $borradores ?? 0 }}</p>
                    </div>
                    <div class="p-4 bg-gray-100 rounded-xl text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">En revisión</p>
                        <p class="text-2xl font-bold text-[#691C32]">{{ $revision ?? 0 }}</p>
                    </div>
                    <div class="p-4 bg-gray-100 rounded-xl text-center">
                        <p class="text-xs text-gray-500 uppercase tracking-wide">Aprobados</p>
                        <p class="text-2xl font-bold text-[#691C32]">{{ $aprobados ?? 0 }}</p>
                    </div>
                </div>
                --}}
            </div>
        </div>
    </div>
</x-app-layout>
