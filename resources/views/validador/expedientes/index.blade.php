{{-- resources/views/validador/expedientes/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Expedientes en validación
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensajes --}}
            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-2xl p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
                    <div>
                        <h3 class="text-2xl font-bold text-[#691C32]">Bandeja de revisión</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Aquí se listan los expedientes con estatus <span class="font-semibold">En validación</span>.
                        </p>
                    </div>

                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                        Total: {{ $expedientes->total() }}
                    </span>
                </div>

                <div class="overflow-x-auto border border-gray-200 rounded-xl">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 border-b text-center">Asignación</th>
                                <th class="px-4 py-3 border-b text-left">Folio</th>
                                <th class="px-4 py-3 border-b text-left">Proyecto</th>
                                <th class="px-4 py-3 border-b text-left">Dependencia</th>
                                <th class="px-4 py-3 border-b text-left">Año</th>
                                <th class="px-4 py-3 border-b text-left">Capturista</th>
                                <th class="px-4 py-3 border-b text-left">Enviado</th>
                                <th class="px-4 py-3 border-b text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($expedientes as $exp)
                                @php
                                    $estatus = $exp->estatus ?? 'en_validacion';
                                    $badge = match($estatus) {
                                        'en_validacion' => ['bg-yellow-100 text-yellow-800', 'En validación'],
                                        'aprobado'      => ['bg-green-100 text-green-800', 'Aprobado'],
                                        'rechazado'     => ['bg-red-100 text-red-800', 'Rechazado'],
                                        'borrador'      => ['bg-gray-100 text-gray-800', 'Borrador'],
                                        default         => ['bg-gray-100 text-gray-800', ucfirst($estatus)],
                                    };
                                @endphp

                                <tr class="hover:bg-gray-50 {{ !empty($exp->fuera_asignacion) ? 'bg-red-50' : '' }}">
                                    <td class="px-4 py-3 text-center">
                                        @if(!empty($exp->fuera_asignacion))
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-red-100 text-red-800">
                                                ⚠️ Fuera
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-green-100 text-green-800">
                                                ✓ OK
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3">
                                        <span class="font-semibold text-gray-900">{{ $exp->folio }}</span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $exp->nombre_proyecto }}</div>

                                        <div class="mt-1">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $badge[0] }}">
                                                {{ $badge[1] }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3">{{ $exp->dependencia }}</td>

                                    <td class="px-4 py-3">{{ $exp->anio_ejercicio }}</td>

                                    <td class="px-4 py-3">
                                        {{ $exp->usuario->nombres ?? $exp->usuario->name ?? 'N/A' }}
                                    </td>

                                    <td class="px-4 py-3 text-xs text-gray-600">
                                        {{ optional($exp->updated_at)->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <a href="{{ route('validador.expedientes.show', $exp) }}"
                                           class="inline-flex items-center px-3 py-2 bg-[#691C32] text-white text-xs font-semibold rounded-md hover:bg-[#4e1324] transition">
                                            Revisar →
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        No hay expedientes en validación por el momento.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $expedientes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>