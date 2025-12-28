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
                    Bienvenido(a), {{ Auth::user()->nombres ?? Auth::user()->name }}
                </h3>

                <p class="text-gray-700 leading-relaxed">
                    Desde este panel podrás <span class="font-semibold">capturar, consultar y dar seguimiento</span> a los expedientes técnicos.
                </p>

                {{-- Aviso si no hay asignaciones --}}
                @php $tieneAsignaciones = ($asignacionesCount ?? 0) > 0; @endphp

                @if(! $tieneAsignaciones)
                    <div class="mt-6 px-4 py-3 rounded-lg bg-yellow-50 text-yellow-800 border border-yellow-100 text-sm">
                        ⚠️ Por el momento tu institución no tiene subprogramas FASP asignados.
                        Solicita al validador que realice la asignación para poder capturar expedientes.
                    </div>
                @endif

                {{-- ACCIONES RÁPIDAS --}}
                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Mis asignaciones FASP --}}
                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow hover:shadow-md hover:bg-[#9F2241]/20 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h4 class="font-semibold text-[#9F2241] mb-1 text-lg">Mis asignaciones FASP</h4>
                                <p class="text-gray-600 text-sm mb-3">
                                    Subprogramas asignados a tu institución para realizar expedientes.
                                </p>
                            </div>

                            <span class="inline-flex items-center px-6 py-6 rounded-full text-xs font-semibold
                                {{ $tieneAsignaciones ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ $asignacionesCount ?? 0 }}
                            </span>
                        </div>
                    </div>

                    {{-- Crear expediente --}}
                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow transition
                        {{ $tieneAsignaciones ? 'hover:shadow-md hover:bg-[#9F2241]/20' : 'opacity-60' }}">
                        <h4 class="font-semibold text-[#9F2241] mb-1 text-lg">Crear nuevo expediente</h4>
                        <p class="text-gray-600 text-sm mb-3">
                            Inicia la captura de un nuevo expediente técnico con base en lo asignado a tu institución.
                        </p>

                        @if($tieneAsignaciones)
                            <a href="{{ route('expedientes.create') }}"
                               class="inline-flex items-center px-3 py-2 bg-[#691C32] text-white text-sm font-medium rounded-md hover:bg-[#4e1324] transition">
                                Ir a captura de expediente <span class="ml-1">→</span>
                            </a>
                        @else
                            <span class="inline-flex items-center px-3 py-2 bg-gray-200 text-gray-600 text-sm font-medium rounded-md cursor-not-allowed">
                                Requiere asignación
                            </span>
                        @endif
                    </div>

                    {{-- Mis expedientes --}}
                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow hover:shadow-md hover:bg-[#9F2241]/20 transition">
                        <h4 class="font-semibold text-[#9F2241] mb-1 text-lg">Mis expedientes</h4>
                        <p class="text-gray-600 text-sm mb-3">
                            Consulta tus expedientes, revisa estatus (Borrador, En validación, Aprobado, Rechazado) y continúa su edición.
                        </p>
                        <a href="{{ route('expedientes.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-[#691C32] text-white text-sm font-medium rounded-md hover:bg-[#4e1324] transition">
                            Ver lista de expedientes <span class="ml-1">→</span>
                        </a>
                    </div>

                </div>

                {{-- Guía rápida --}}
                <div class="mt-8 bg-gray-50 border border-gray-200 rounded-2xl p-6">
                    <h4 class="font-semibold text-gray-700 mb-2 text-lg">Guía rápida</h4>
                    <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                        <li>Captura primero datos generales, después descripción técnica, metas/indicadores, presupuesto y documentación.</li>
                        <li>Los expedientes se crean en estatus <span class="font-semibold">Borrador</span>.</li>
                        <li>Si un expediente es <span class="font-semibold">Rechazado</span>, corrige y reenvía a validación.</li>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>