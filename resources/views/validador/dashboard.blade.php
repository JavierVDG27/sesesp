<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Panel del validador
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-2xl p-8">
                <h3 class="text-2xl font-bold text-[#691C32] mb-2">
                    Bienvenido(a), {{ Auth::user()->nombres ?? Auth::user()->name }}
                </h3>

                <p class="text-gray-700 leading-relaxed">
                    Desde aquí podrás revisar y dictaminar los expedientes enviados a validación, y administrar las asignaciones FASP.
                </p>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Bandeja de revisión --}}
                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow hover:shadow-md hover:bg-[#9F2241]/20 transition">
                        <h4 class="font-semibold text-[#9F2241] mb-1 text-lg">Bandeja de revisión</h4>
                        <p class="text-gray-600 text-sm mb-3">
                            Consulta expedientes en estatus <b>En validación</b> y emite tu decisión.
                        </p>
                        <a href="{{ route('revision.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-[#691C32] text-white text-sm font-medium rounded-md hover:bg-[#4e1324] transition">
                            Ir a expedientes en validación →
                        </a>
                    </div>

                    {{-- Asignaciones FASP --}}
                    <div class="bg-[#9F2241]/10 p-6 rounded-xl shadow hover:shadow-md hover:bg-[#9F2241]/20 transition">
                        <h4 class="font-semibold text-[#9F2241] mb-1 text-lg">Asignaciones FASP</h4>
                        <p class="text-gray-600 text-sm mb-3">
                            Asigna <b>Eje/Programa/Subprograma</b> a capturistas por año para controlar qué pueden capturar.
                        </p>
                        <a href="{{ route('validador.fasp_asignaciones_institucion.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-[#691C32] text-white text-sm font-medium rounded-md hover:bg-[#4e1324] transition">
                            Ir a asignaciones FASP →
                        </a>
                    </div>

                    {{-- Notas --}}
                    <div class="bg-gray-100 p-6 rounded-xl">
                        <h4 class="font-semibold text-gray-700 mb-1 text-lg">Notas</h4>
                        <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                            <li>Si rechazas, las observaciones son obligatorias.</li>
                            <li>Al aprobar, el expediente queda bloqueado para edición.</li>
                            <li>Si un capturista no ve opciones, revisa sus asignaciones FASP.</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
